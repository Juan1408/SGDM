<?php
namespace Tests\Unitarias;

use PDO;
use PDOException;
use Tests\TestCase;
use App\Modelos\Usuario;

require_once __DIR__ . '/../../codigo_fuente/modelos/Usuario.php';

/**
 * Pruebas de la constancia de aceptación de Términos y Condiciones y de la
 * Política de Privacidad en el alta de cuenta.
 *
 * Cubre:
 *  - Registro con aceptación de ambos documentos y datos de evidencia.
 *  - Rechazo sin aceptación o con versión vacía, sin crear usuario.
 *  - Unicidad por usuario/documento/versión.
 *  - Rollback completo si falla la inserción de la aceptación.
 *  - Exposición de la constancia en la ficha administrativa.
 *  - Cuentas anteriores a la versión 1.0 sin registros.
 */
class AceptacionLegalTest extends TestCase {

    private Usuario $modeloUsuario;

    protected function setUp(): void {
        parent::setUp();
        $this->modeloUsuario = new Usuario();
    }

    private function datosRegistro(array $sobrescribir = []): array {
        return array_merge([
            'nombre_completo' => 'Usuario Aceptación',
            'email' => 'aceptacion_' . uniqid() . '@ascend.test',
            'telefono' => '+59899000123',
            'contrasena' => 'ClaveDePrueba123!',
            'rol_id' => 3,
            'aceptaciones' => [
                'terminos' => '1.0',
                'privacidad' => '1.0',
            ],
            'aceptacion_ip' => '127.0.0.1',
            'aceptacion_user_agent' => 'PHPUnit/9.6',
        ], $sobrescribir);
    }

    private function contarPorEmail(string $email): int {
        $stmt = $this->bd->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => strtolower($email)]);
        return (int) $stmt->fetchColumn();
    }

    public function test_registro_guarda_aceptacion_de_ambos_documentos(): void {
        $datos = $this->datosRegistro();
        $resultado = $this->modeloUsuario->registrarUsuario($datos);

        $this->assertTrue($resultado['exito'], $resultado['mensaje'] ?? '');
        $usuarioId = (int) $resultado['usuario_id'];

        $stmt = $this->bd->prepare(
            "SELECT tipo_documento, version, aceptado_en, ip_origen, user_agent
             FROM aceptaciones_legales
             WHERE usuario_id = :id"
        );
        $stmt->execute([':id' => $usuarioId]);
        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->assertCount(2, $filas);
        $porTipo = [];
        foreach ($filas as $fila) {
            $porTipo[$fila['tipo_documento']] = $fila;
        }

        $this->assertArrayHasKey('terminos', $porTipo);
        $this->assertArrayHasKey('privacidad', $porTipo);
        $this->assertSame('1.0', $porTipo['terminos']['version']);
        $this->assertSame('1.0', $porTipo['privacidad']['version']);
        $this->assertSame('127.0.0.1', $porTipo['terminos']['ip_origen']);
        $this->assertSame('PHPUnit/9.6', $porTipo['terminos']['user_agent']);
        $this->assertNotEmpty($porTipo['terminos']['aceptado_en']);
    }

    public function test_registro_sin_aceptacion_rechaza_y_no_crea_usuario(): void {
        $datos = $this->datosRegistro();
        unset($datos['aceptaciones']);

        $resultado = $this->modeloUsuario->registrarUsuario($datos);

        $this->assertFalse($resultado['exito']);
        $this->assertStringContainsString('Debés aceptar', $resultado['mensaje']);
        $this->assertSame(0, $this->contarPorEmail($datos['email']));
        $this->assertSame(0, (int) $this->bd->query("SELECT COUNT(*) FROM aceptaciones_legales")->fetchColumn());
    }

    public function test_registro_con_version_vacia_rechaza_y_no_crea_usuario(): void {
        $datos = $this->datosRegistro([
            'aceptaciones' => ['terminos' => '', 'privacidad' => '1.0'],
        ]);

        $resultado = $this->modeloUsuario->registrarUsuario($datos);

        $this->assertFalse($resultado['exito']);
        $this->assertSame(0, $this->contarPorEmail($datos['email']));
        $this->assertSame(0, (int) $this->bd->query("SELECT COUNT(*) FROM aceptaciones_legales")->fetchColumn());
    }

    /**
     * @dataProvider correosInvalidos
     */
    public function test_registro_con_email_invalido_rechaza_y_no_crea_usuario(string $email): void {
        $datos = $this->datosRegistro(['email' => $email]);

        $resultado = $this->modeloUsuario->registrarUsuario($datos);

        $this->assertFalse($resultado['exito']);
        $this->assertStringContainsString('correo electrónico válido', $resultado['mensaje']);
        $this->assertSame(0, $this->contarPorEmail($email));
        $this->assertSame(0, (int) $this->bd->query("SELECT COUNT(*) FROM aceptaciones_legales")->fetchColumn());
    }

    public static function correosInvalidos(): array {
        return [
            'sin arroba' => ['correo-sin-arroba'],
            'sin dominio' => ['usuario@dominio'],
            'doble arroba' => ['usuario@@dominio.com'],
            'punto doble' => ['usuario@dominio..com'],
            'espacio' => ['usuario dominio.com'],
            'demasiado largo' => [str_repeat('a', 250) . '@ascend.test'],
        ];
    }

    public function test_aceptacion_duplicada_no_se_permite(): void {
        $datos = $this->datosRegistro();
        $resultado = $this->modeloUsuario->registrarUsuario($datos);
        $this->assertTrue($resultado['exito'], $resultado['mensaje'] ?? '');

        $stmt = $this->bd->prepare(
            "INSERT INTO aceptaciones_legales (usuario_id, tipo_documento, version)
             VALUES (:id, 'terminos', '1.0')"
        );

        $this->expectException(PDOException::class);
        $stmt->execute([':id' => (int) $resultado['usuario_id']]);
    }

    public function test_registro_duplicado_no_duplica_aceptaciones(): void {
        $datos = $this->datosRegistro();
        $primero = $this->modeloUsuario->registrarUsuario($datos);
        $this->assertTrue($primero['exito'], $primero['mensaje'] ?? '');

        $segundo = $this->modeloUsuario->registrarUsuario($datos);

        $this->assertFalse($segundo['exito']);
        $this->assertStringContainsString('ya se encuentra registrado', $segundo['mensaje']);
        $this->assertSame(1, $this->contarPorEmail($datos['email']));
        $this->assertSame(2, (int) $this->bd->query("SELECT COUNT(*) FROM aceptaciones_legales")->fetchColumn());
    }

    public function test_ficha_admin_incluye_aceptaciones_sin_exponer_el_hash(): void {
        $datos = $this->datosRegistro();
        $resultado = $this->modeloUsuario->registrarUsuario($datos);
        $this->assertTrue($resultado['exito'], $resultado['mensaje'] ?? '');
        $usuarioId = (int) $resultado['usuario_id'];

        $ficha = $this->modeloUsuario->obtenerFichaAdmin($usuarioId);

        $this->assertIsArray($ficha);
        $this->assertArrayHasKey('aceptaciones', $ficha);
        $this->assertCount(2, $ficha['aceptaciones']);
        $this->assertArrayNotHasKey('contrasena_hash', $ficha['usuario']);
        $this->assertArrayNotHasKey('token', $ficha['usuario']);
    }

    public function test_usuario_sin_aceptacion_devuelve_lista_vacia(): void {
        $stmt = $this->bd->prepare(
            "INSERT INTO usuarios (email, contrasena_hash, nombre_completo, rol_id, esta_activo, email_verificado)
             VALUES (:email, :hash, 'Cuenta Anterior', 3, 1, 1)"
        );
        $stmt->execute([
            ':email' => 'anterior_' . uniqid() . '@ascend.test',
            ':hash' => password_hash('ClaveDePrueba123!', PASSWORD_BCRYPT),
        ]);
        $usuarioId = (int) $this->bd->lastInsertId();

        $ficha = $this->modeloUsuario->obtenerFichaAdmin($usuarioId);

        $this->assertIsArray($ficha);
        $this->assertSame([], $ficha['aceptaciones']);
        $this->assertSame([], $this->modeloUsuario->obtenerAceptacionesLegales(0));
    }

    public function test_falla_al_insertar_aceptacion_revierte_toda_la_cuenta(): void {
        $this->bd->exec("DROP TRIGGER IF EXISTS trg_test_aceptacion_falla");
        try {
            $this->bd->exec(
                "CREATE TRIGGER trg_test_aceptacion_falla
                 BEFORE INSERT ON aceptaciones_legales
                 FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'falla simulada'"
            );
        } catch (PDOException $e) {
            $this->markTestSkipped('El usuario de pruebas no puede crear triggers: ' . $e->getMessage());
        }

        $datos = $this->datosRegistro();
        try {
            $resultado = $this->modeloUsuario->registrarUsuario($datos);

            $this->assertFalse($resultado['exito']);
            $this->assertSame(0, $this->contarPorEmail($datos['email']));
            $this->assertSame(0, (int) $this->bd->query("SELECT COUNT(*) FROM perfiles_jugadores")->fetchColumn());
            $this->assertSame(0, (int) $this->bd->query("SELECT COUNT(*) FROM tokens_verificacion_email")->fetchColumn());
            $this->assertSame(0, (int) $this->bd->query("SELECT COUNT(*) FROM aceptaciones_legales")->fetchColumn());
        } finally {
            $this->bd->exec("DROP TRIGGER IF EXISTS trg_test_aceptacion_falla");
        }
    }

    public function test_user_agent_se_trunca_e_ip_invalida_se_descarta(): void {
        $datos = $this->datosRegistro([
            'aceptacion_ip' => '999.999.999.999',
            'aceptacion_user_agent' => str_repeat('A', 300),
        ]);

        $resultado = $this->modeloUsuario->registrarUsuario($datos);
        $this->assertTrue($resultado['exito'], $resultado['mensaje'] ?? '');

        $stmt = $this->bd->prepare(
            "SELECT ip_origen, user_agent FROM aceptaciones_legales WHERE usuario_id = :id LIMIT 1"
        );
        $stmt->execute([':id' => (int) $resultado['usuario_id']]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertNull($fila['ip_origen']);
        $this->assertSame(255, mb_strlen($fila['user_agent'], 'UTF-8'));
    }

    public function test_registro_organizador_tambien_registra_aceptacion(): void {
        $datos = $this->datosRegistro([
            'rol_id' => 2,
            'aceptaciones' => ['terminos' => '1.0', 'privacidad' => '1.0'],
        ]);

        $resultado = $this->modeloUsuario->registrarUsuario($datos);

        $this->assertTrue($resultado['exito'], $resultado['mensaje'] ?? '');
        $this->assertSame(2, (int) $this->bd->query("SELECT COUNT(*) FROM aceptaciones_legales")->fetchColumn());
    }
}
