<?php
namespace Test;

use App\Modelos\Usuario;

require_once __DIR__ . '/CasoBase.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Usuario.php';

/**
 * Funcionalidad 1: alta de una cuenta de jugador.
 * Método: Usuario::registrarUsuario()
 *
 * Reglas comprobadas:
 *  - El alta válida crea el usuario, su perfil y la constancia legal.
 *  - El correo se normaliza a minúsculas y la contraseña nunca se guarda en claro.
 *  - No se admiten dos cuentas con el mismo correo.
 *  - Sin aceptación de Términos y Privacidad no se crea ninguna cuenta.
 */
class RegistroUsuarioTest extends CasoBase {

    private Usuario $modeloUsuario;

    protected function setUp(): void {
        parent::setUp();
        $this->modeloUsuario = new Usuario();
    }

    private function datosRegistro(string $email = 'nuevo@ascend.test'): array {
        return [
            'nombre_completo' => 'Jugador Nuevo',
            'email' => $email,
            'telefono' => '+59899123456',
            'contrasena' => 'ClaveSegura1!',
            'rol_id' => 3,
            'aceptaciones' => [
                'terminos' => '1.0',
                'privacidad' => '1.0',
            ],
            'aceptacion_ip' => '127.0.0.1',
            'aceptacion_user_agent' => 'PHPUnit',
        ];
    }

    public function test_registro_valido_crea_usuario_perfil_y_constancia_legal(): void {
        $resultado = $this->modeloUsuario->registrarUsuario($this->datosRegistro('Nuevo.Usuario@Ascend.Test'));

        $this->assertTrue($resultado['exito'], 'El alta debería completarse.');
        $this->assertGreaterThan(0, $resultado['usuario_id']);

        $stmt = $this->bd->prepare('SELECT * FROM usuarios WHERE id = :id');
        $stmt->execute([':id' => $resultado['usuario_id']]);
        $usuario = $stmt->fetch();

        $this->assertSame('nuevo.usuario@ascend.test', $usuario['email'], 'El correo debe normalizarse a minúsculas.');
        $this->assertSame(3, (int) $usuario['rol_id']);
        $this->assertSame(1, (int) $usuario['esta_activo']);
        $this->assertSame(0, (int) $usuario['email_verificado'], 'La cuenta nace sin correo verificado.');
        $this->assertNotSame('ClaveSegura1!', $usuario['contrasena_hash'], 'La contraseña no debe guardarse en claro.');
        $this->assertTrue(password_verify('ClaveSegura1!', $usuario['contrasena_hash']));

        $stmt = $this->bd->prepare('SELECT COUNT(*) FROM perfiles_jugadores WHERE usuario_id = :id');
        $stmt->execute([':id' => $resultado['usuario_id']]);
        $this->assertSame(1, (int) $stmt->fetchColumn(), 'Debe crearse el perfil de jugador.');

        $stmt = $this->bd->prepare('SELECT COUNT(*) FROM aceptaciones_legales WHERE usuario_id = :id');
        $stmt->execute([':id' => $resultado['usuario_id']]);
        $this->assertSame(2, (int) $stmt->fetchColumn(), 'Deben registrarse Términos y Privacidad.');
    }

    public function test_registro_rechaza_un_correo_ya_registrado(): void {
        $this->assertTrue($this->modeloUsuario->registrarUsuario($this->datosRegistro('repetido@ascend.test'))['exito']);

        $segundo = $this->modeloUsuario->registrarUsuario($this->datosRegistro('REPETIDO@ascend.test'));

        $this->assertFalse($segundo['exito']);
        $this->assertStringContainsString('ya se encuentra registrado', $segundo['mensaje']);
        $this->assertSame(1, (int) $this->bd->query('SELECT COUNT(*) FROM usuarios')->fetchColumn());
    }

    public function test_registro_sin_aceptaciones_no_crea_ninguna_cuenta(): void {
        $datos = $this->datosRegistro('sin.aceptacion@ascend.test');
        unset($datos['aceptaciones']);

        $resultado = $this->modeloUsuario->registrarUsuario($datos);

        $this->assertFalse($resultado['exito']);
        $this->assertStringContainsString('Debés aceptar', $resultado['mensaje']);
        $this->assertSame(0, (int) $this->bd->query('SELECT COUNT(*) FROM usuarios')->fetchColumn());
        $this->assertSame(0, (int) $this->bd->query('SELECT COUNT(*) FROM aceptaciones_legales')->fetchColumn());
    }
}
