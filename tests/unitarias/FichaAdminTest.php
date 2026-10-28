<?php
namespace Tests\Unitarias;

use Tests\TestCase;
use App\Modelos\Usuario;
use App\Modelos\Equipo;

require_once __DIR__ . '/../../codigo_fuente/modelos/Usuario.php';
require_once __DIR__ . '/../../codigo_fuente/modelos/Equipo.php';

/**
 * Pruebas de las fichas administrativas y del renombre sincronizado de equipos.
 *
 * Cubre:
 *  - Renombre de equipo con sincronización de participantes_torneo y podios JSON.
 *  - Rechazo de nombres duplicados, vacíos, demasiado cortos o largos.
 *  - Idempotencia del renombre y equipo inexistente.
 *  - Ficha de equipo y ficha de usuario sin exposición de secretos.
 */
class FichaAdminTest extends TestCase {

    private Usuario $modeloUsuario;
    private Equipo $modeloEquipo;

    /** @var int[] */
    private array $usuariosCreados = [];
    /** @var int[] */
    private array $equiposCreados = [];
    /** @var int[] */
    private array $torneosCreados = [];
    /** @var int[] */
    private array $juegosCreados = [];

    protected function setUp(): void {
        parent::setUp();
        $this->modeloUsuario = new Usuario();
        $this->modeloEquipo = Equipo::obtenerInstancia();
    }

    protected function tearDown(): void {
        // Limpieza explícita: el TestCase base no trunca estas tablas y
        // los AUTO_INCREMENT reiniciados podrían chocar con filas huérfanas.
        foreach ($this->torneosCreados as $torneoId) {
            $this->bd->exec("DELETE FROM torneo_config WHERE torneo_id = " . (int) $torneoId);
            $this->bd->exec("DELETE FROM participantes_torneo WHERE torneo_id = " . (int) $torneoId);
            $this->bd->exec("DELETE FROM torneos WHERE id = " . (int) $torneoId);
        }

        foreach ($this->equiposCreados as $equipoId) {
            $this->bd->exec("DELETE FROM equipo_capitanes WHERE equipo_id = " . (int) $equipoId);
            $this->bd->exec("DELETE FROM equipo_miembros WHERE equipo_id = " . (int) $equipoId);
            $this->bd->exec("DELETE FROM equipos WHERE id = " . (int) $equipoId);
        }

        foreach ($this->usuariosCreados as $usuarioId) {
            $this->bd->exec("DELETE FROM logs_acceso WHERE usuario_id = " . (int) $usuarioId);
            $this->bd->exec("DELETE FROM sesiones_activas WHERE usuario_id = " . (int) $usuarioId);
            $this->bd->exec("DELETE FROM logs_actividad WHERE usuario_id = " . (int) $usuarioId);
            $this->bd->exec("DELETE FROM perfiles_jugadores WHERE usuario_id = " . (int) $usuarioId);
            $this->bd->exec("DELETE FROM perfiles_organizadores WHERE usuario_id = " . (int) $usuarioId);
            $this->bd->exec("DELETE FROM usuarios WHERE id = " . (int) $usuarioId);
        }

        foreach ($this->juegosCreados as $juegoId) {
            $this->bd->exec("DELETE FROM juegos WHERE id = " . (int) $juegoId);
        }

        parent::tearDown();
    }

    // ------------------------------------------------------------------
    // Fixtures
    // ------------------------------------------------------------------

    private function crearUsuario(int $rolId): int {
        $email = 'ficha_' . uniqid() . '@ascend.test';
        $hash = password_hash('ClaveDePrueba123!', PASSWORD_BCRYPT);

        $stmt = $this->bd->prepare(
            "INSERT INTO usuarios (email, contrasena_hash, nombre_completo, telefono, rol_id, esta_activo, email_verificado)
             VALUES (:email, :hash, :nombre, :telefono, :rol, 1, 1)"
        );
        $stmt->execute([
            ':email' => $email,
            ':hash' => $hash,
            ':nombre' => 'Usuario Ficha ' . $rolId,
            ':telefono' => '+59899000000',
            ':rol' => $rolId,
        ]);

        $id = (int) $this->bd->lastInsertId();
        $this->usuariosCreados[] = $id;
        return $id;
    }

    private function crearJuego(): int {
        $nombre = 'Juego Ficha ' . uniqid();
        $stmt = $this->bd->prepare("INSERT INTO juegos (nombre, categoria) VALUES (:nombre, 'Pruebas')");
        $stmt->execute([':nombre' => $nombre]);

        $id = (int) $this->bd->lastInsertId();
        $this->juegosCreados[] = $id;

        // El trigger de torneos exige que la modalidad y el formato esten
        // habilitados para la disciplina; la base de test se trunca por prueba.
        $stmtRel = $this->bd->prepare(
            "INSERT INTO juego_modalidades_permitidas (juego_id, modalidad_id) VALUES (:juego, 1)"
        );
        $stmtRel->execute([':juego' => $id]);

        $stmtFmt = $this->bd->prepare(
            "INSERT INTO juego_formatos_permitidos (juego_id, formato_codigo) VALUES (:juego, 'liga')"
        );
        $stmtFmt->execute([':juego' => $id]);

        return $id;
    }

    private function crearTorneo(int $organizadorId, int $juegoId, string $nombre): int {
        $stmt = $this->bd->prepare(
            "INSERT INTO torneos (organizador_id, juego_id, modalidad_id, nombre, formato, estado, cupo_max_equipos)
             VALUES (:organizador, :juego, 1, :nombre, 'liga', 'en_curso', 8)"
        );
        $stmt->execute([
            ':organizador' => $organizadorId,
            ':juego' => $juegoId,
            ':nombre' => $nombre,
        ]);

        $id = (int) $this->bd->lastInsertId();
        $this->torneosCreados[] = $id;
        return $id;
    }

    private function crearEquipo(int $creadoPor, int $juegoId, string $nombre): int {
        $stmt = $this->bd->prepare(
            "INSERT INTO equipos (nombre_equipo, creado_por, juego_id, activo)
             VALUES (:nombre, :creador, :juego, 1)"
        );
        $stmt->execute([
            ':nombre' => $nombre,
            ':creador' => $creadoPor,
            ':juego' => $juegoId,
        ]);

        $id = (int) $this->bd->lastInsertId();
        $this->equiposCreados[] = $id;
        return $id;
    }

    private function agregarMiembro(int $equipoId, int $usuarioId, bool $capitan = false): void {
        $stmt = $this->bd->prepare(
            "INSERT INTO equipo_miembros (equipo_id, usuario_id, posicion, numero_camiseta, es_activo)
             VALUES (:equipo, :usuario, 'Titular', 10, 1)"
        );
        $stmt->execute([':equipo' => $equipoId, ':usuario' => $usuarioId]);

        if ($capitan) {
            $stmt = $this->bd->prepare(
                "INSERT INTO equipo_capitanes (equipo_id, usuario_id, asignado_por, es_capitan_principal)
                 VALUES (:equipo, :usuario, :asignado, 1)"
            );
            $stmt->execute([
                ':equipo' => $equipoId,
                ':usuario' => $usuarioId,
                ':asignado' => $usuarioId,
            ]);
        }
    }

    private function inscribirEquipo(int $torneoId, int $equipoId, string $nombre): int {
        $stmt = $this->bd->prepare(
            "INSERT INTO participantes_torneo (torneo_id, tipo, referencia_id, nombre, estado)
             VALUES (:torneo, 'equipo', :equipo, :nombre, 'confirmado')"
        );
        $stmt->execute([
            ':torneo' => $torneoId,
            ':equipo' => $equipoId,
            ':nombre' => $nombre,
        ]);
        return (int) $this->bd->lastInsertId();
    }

    private function guardarPodio(int $torneoId, array $podio): void {
        $stmt = $this->bd->prepare(
            "INSERT INTO torneo_config (torneo_id, clave, valor) VALUES (:torneo, 'podio', :valor)"
        );
        $stmt->execute([
            ':torneo' => $torneoId,
            ':valor' => json_encode($podio, JSON_UNESCAPED_UNICODE),
        ]);
    }

    private function obtenerNombreEquipo(int $equipoId): string {
        $stmt = $this->bd->prepare("SELECT nombre_equipo FROM equipos WHERE id = :id");
        $stmt->execute([':id' => $equipoId]);
        return (string) $stmt->fetchColumn();
    }

    private function obtenerNombreParticipante(int $torneoId, int $equipoId): string {
        $stmt = $this->bd->prepare(
            "SELECT nombre FROM participantes_torneo
             WHERE torneo_id = :torneo AND tipo = 'equipo' AND referencia_id = :equipo"
        );
        $stmt->execute([':torneo' => $torneoId, ':equipo' => $equipoId]);
        return (string) $stmt->fetchColumn();
    }

    private function obtenerPodio(int $torneoId): array {
        $stmt = $this->bd->prepare(
            "SELECT valor FROM torneo_config WHERE torneo_id = :torneo AND clave = 'podio'"
        );
        $stmt->execute([':torneo' => $torneoId]);
        $valor = $stmt->fetchColumn();
        return $valor ? (array) json_decode($valor, true) : [];
    }

    /**
     * Recorre recursivamente un array y devuelve todas las claves encontradas.
     */
    private function recolectarClaves(array $datos): array {
        $claves = [];
        foreach ($datos as $clave => $valor) {
            $claves[] = (string) $clave;
            if (is_array($valor)) {
                $claves = array_merge($claves, $this->recolectarClaves($valor));
            }
        }
        return $claves;
    }

    // ------------------------------------------------------------------
    // Renombre sincronizado
    // ------------------------------------------------------------------

    public function test_renombrar_sincroniza_equipo_inscripciones_y_podio(): void {
        $adminId = $this->crearUsuario(1);
        $juegoId = $this->crearJuego();
        $torneoId = $this->crearTorneo($adminId, $juegoId, 'Torneo Ficha ' . uniqid());

        $nombreViejo = 'Equipo Viejo ' . uniqid();
        $nombreNuevo = 'Equipo Nuevo ' . uniqid();
        $equipoId = $this->crearEquipo($adminId, $juegoId, $nombreViejo);
        $this->agregarMiembro($equipoId, $adminId, true);
        $this->inscribirEquipo($torneoId, $equipoId, $nombreViejo);
        $this->guardarPodio($torneoId, [
            'primero' => $nombreViejo,
            'segundo' => 'Rival Estable',
            'tercero' => null,
        ]);

        $resultado = $this->modeloEquipo->renombrarEquipoAdmin($equipoId, $nombreNuevo);

        $this->assertTrue($resultado['exito'], 'El renombre debería completarse.');
        $this->assertSame($nombreViejo, $resultado['anterior']);
        $this->assertSame($nombreNuevo, $resultado['nuevo']);

        $this->assertSame($nombreNuevo, $this->obtenerNombreEquipo($equipoId));
        $this->assertSame($nombreNuevo, $this->obtenerNombreParticipante($torneoId, $equipoId));

        $podio = $this->obtenerPodio($torneoId);
        $this->assertSame($nombreNuevo, $podio['primero'], 'El podio debe actualizar el nombre ganador.');
        $this->assertSame('Rival Estable', $podio['segundo'], 'Los demás puestos no deben cambiar.');
        $this->assertNull($podio['tercero']);
    }

    public function test_renombrar_rechaza_nombre_duplicado_sin_modificar_nada(): void {
        $adminId = $this->crearUsuario(1);
        $juegoId = $this->crearJuego();

        $nombreA = 'Equipo A ' . uniqid();
        $nombreB = 'Equipo B ' . uniqid();
        $equipoA = $this->crearEquipo($adminId, $juegoId, $nombreA);
        $this->crearEquipo($adminId, $juegoId, $nombreB);

        $resultado = $this->modeloEquipo->renombrarEquipoAdmin($equipoA, $nombreB);

        $this->assertFalse($resultado['exito']);
        $this->assertStringContainsString('Ya existe', $resultado['error']);
        $this->assertSame($nombreA, $this->obtenerNombreEquipo($equipoA), 'El nombre original no debe cambiar.');
    }

    public function test_renombrar_valida_nombre_vacio_corto_y_largo(): void {
        $adminId = $this->crearUsuario(1);
        $juegoId = $this->crearJuego();
        $equipoId = $this->crearEquipo($adminId, $juegoId, 'Equipo Validación ' . uniqid());
        $original = $this->obtenerNombreEquipo($equipoId);

        $vacio = $this->modeloEquipo->renombrarEquipoAdmin($equipoId, '   ');
        $this->assertFalse($vacio['exito']);

        $corto = $this->modeloEquipo->renombrarEquipoAdmin($equipoId, 'A');
        $this->assertFalse($corto['exito']);

        $largo = $this->modeloEquipo->renombrarEquipoAdmin($equipoId, str_repeat('X', 101));
        $this->assertFalse($largo['exito']);

        $this->assertSame($original, $this->obtenerNombreEquipo($equipoId), 'Ninguna validación debe modificar el equipo.');
    }

    public function test_renombrar_mismo_nombre_es_idempotente(): void {
        $adminId = $this->crearUsuario(1);
        $juegoId = $this->crearJuego();
        $nombre = 'Equipo Idempotente ' . uniqid();
        $equipoId = $this->crearEquipo($adminId, $juegoId, $nombre);

        $resultado = $this->modeloEquipo->renombrarEquipoAdmin($equipoId, $nombre);

        $this->assertTrue($resultado['exito']);
        $this->assertSame($nombre, $resultado['anterior']);
        $this->assertSame($nombre, $resultado['nuevo']);
        $this->assertSame($nombre, $this->obtenerNombreEquipo($equipoId));
    }

    public function test_renombrar_equipo_inexistente_devuelve_error(): void {
        $resultado = $this->modeloEquipo->renombrarEquipoAdmin(999999999, 'Nombre Válido');

        $this->assertFalse($resultado['exito']);
        $this->assertArrayHasKey('error', $resultado);
    }

    // ------------------------------------------------------------------
    // Ficha de equipo
    // ------------------------------------------------------------------

    public function test_ficha_equipo_incluye_plantilla_participaciones_y_resumen(): void {
        $adminId = $this->crearUsuario(1);
        $jugador1 = $this->crearUsuario(3);
        $jugador2 = $this->crearUsuario(3);
        $juegoId = $this->crearJuego();
        $torneoId = $this->crearTorneo($adminId, $juegoId, 'Torneo Ficha Equipo ' . uniqid());

        $nombre = 'Equipo Ficha ' . uniqid();
        $equipoId = $this->crearEquipo($adminId, $juegoId, $nombre);
        $this->agregarMiembro($equipoId, $jugador1, true);
        $this->agregarMiembro($equipoId, $jugador2, false);
        $this->inscribirEquipo($torneoId, $equipoId, $nombre);

        $ficha = $this->modeloEquipo->obtenerFichaAdmin($equipoId);

        $this->assertIsArray($ficha);
        foreach (['equipo', 'capitanes', 'miembros', 'participaciones', 'partidos', 'resumen', 'solicitudes_pendientes'] as $clave) {
            $this->assertArrayHasKey($clave, $ficha, "La ficha debe incluir la clave '$clave'.");
        }

        $this->assertSame($nombre, $ficha['equipo']['nombre_equipo']);
        $this->assertCount(2, $ficha['miembros']);
        $this->assertCount(1, $ficha['capitanes']);
        $this->assertSame($jugador1, (int) $ficha['capitanes'][0]['id']);
        $this->assertCount(1, $ficha['participaciones']);
        $this->assertSame(0, (int) $ficha['resumen']['partidas']);
        $this->assertArrayNotHasKey('contrasena_hash', $ficha['equipo']);
    }

    public function test_ficha_equipo_inexistente_devuelve_false(): void {
        $this->assertFalse($this->modeloEquipo->obtenerFichaAdmin(999999999));
        $this->assertFalse($this->modeloEquipo->obtenerFichaAdmin(0));
    }

    // ------------------------------------------------------------------
    // Ficha de usuario
    // ------------------------------------------------------------------

    public function test_ficha_usuario_expone_identidad_y_auditoria_sin_secretos(): void {
        $usuarioId = $this->crearUsuario(3);
        $adminId = $this->crearUsuario(1);
        $juegoId = $this->crearJuego();

        // Perfil de jugador
        $this->bd->exec(
            "INSERT INTO perfiles_jugadores (usuario_id, apodo_gamertag, pais, ciudad)
             VALUES ($usuarioId, 'ApodoTest', 'Uruguay', 'Montevideo')"
        );

        // Equipo con membresía y capitanía
        $equipoId = $this->crearEquipo($adminId, $juegoId, 'Equipo Usuario ' . uniqid());
        $this->agregarMiembro($equipoId, $usuarioId, true);

        // Accesos: 1 exitoso y 1 fallido
        $this->bd->exec(
            "INSERT INTO logs_acceso (email_intentado, usuario_id, exito, ip_origen, user_agent, mensaje_error)
             VALUES ('ficha@test.com', $usuarioId, 1, '203.0.113.10', 'Navegador Test', NULL)"
        );
        $this->bd->exec(
            "INSERT INTO logs_acceso (email_intentado, usuario_id, exito, ip_origen, user_agent, mensaje_error)
             VALUES ('ficha@test.com', $usuarioId, 0, '203.0.113.11', 'Navegador Test', 'Credenciales incorrectas')"
        );

        // Sesión activa con token (el token NO debe salir en la ficha)
        $this->bd->exec(
            "INSERT INTO sesiones_activas (usuario_id, token_sesion, ip_origen, user_agent, expira_en, activa)
             VALUES ($usuarioId, 'token-secreto-de-prueba', '203.0.113.10', 'Navegador Test', DATE_ADD(NOW(), INTERVAL 7 DAY), 1)"
        );

        $this->bd->exec(
            "INSERT INTO logs_actividad (usuario_id, accion, descripcion, ip_origen)
             VALUES ($usuarioId, 'PRUEBA_FICHA', 'Actividad de prueba', '203.0.113.10')"
        );

        $ficha = $this->modeloUsuario->obtenerFichaAdmin($usuarioId);

        $this->assertIsArray($ficha);
        foreach (['usuario', 'perfil_jugador', 'perfil_organizador', 'equipos', 'participaciones', 'accesos', 'resumen_accesos', 'sesiones', 'actividad', 'metricas'] as $clave) {
            $this->assertArrayHasKey($clave, $ficha, "La ficha debe incluir la clave '$clave'.");
        }

        // Identidad y contacto disponibles para auditoría
        $this->assertSame($usuarioId, (int) $ficha['usuario']['id']);
        $this->assertSame('+59899000000', $ficha['usuario']['telefono']);
        $this->assertNotNull($ficha['usuario']['fecha_registro']);
        $this->assertSame('ApodoTest', $ficha['perfil_jugador']['apodo_gamertag']);

        // Equipo y capitanía
        $this->assertCount(1, $ficha['equipos']);
        $this->assertSame(1, (int) $ficha['equipos'][0]['es_capitan']);
        $this->assertSame(1, (int) $ficha['metricas']['equipos_activos']);

        // Auditoría de accesos
        $this->assertSame(2, (int) $ficha['resumen_accesos']['total']);
        $this->assertSame(1, (int) $ficha['resumen_accesos']['exitosos']);
        $this->assertSame(1, (int) $ficha['resumen_accesos']['fallidos']);
        $this->assertNotNull($ficha['resumen_accesos']['ultimo_fallido']);
        $this->assertCount(2, $ficha['accesos']);
        $this->assertCount(1, $ficha['sesiones']);
        $this->assertCount(1, $ficha['actividad']);

        // Nunca se exponen secretos
        $claves = $this->recolectarClaves($ficha);
        $this->assertNotContains('contrasena_hash', $claves);
        $this->assertNotContains('token_sesion', $claves);
        $this->assertNotContains('token', $claves);
        $this->assertStringNotContainsString('token-secreto-de-prueba', json_encode($ficha));
        $this->assertStringNotContainsString('$2y$', json_encode($ficha));
    }

    public function test_ficha_usuario_organizador_incluye_su_perfil(): void {
        $organizadorId = $this->crearUsuario(2);

        $this->bd->exec(
            "INSERT INTO perfiles_organizadores (usuario_id, nombre_organizacion, verificado_oficial)
             VALUES ($organizadorId, 'Liga de Prueba', 1)"
        );

        $ficha = $this->modeloUsuario->obtenerFichaAdmin($organizadorId);

        $this->assertIsArray($ficha);
        $this->assertSame('Liga de Prueba', $ficha['perfil_organizador']['nombre_organizacion']);
        $this->assertSame(1, (int) $ficha['perfil_organizador']['verificado_oficial']);
        $this->assertNull($ficha['perfil_jugador']);
    }

    public function test_ficha_usuario_inexistente_devuelve_false(): void {
        $this->assertFalse($this->modeloUsuario->obtenerFichaAdmin(999999999));
        $this->assertFalse($this->modeloUsuario->obtenerFichaAdmin(0));
    }

    /**
     * El panel administrativo puede cambiar nombre y rol, pero nunca el email:
     * un cambio de correo invalidaría la verificación que pertenece al usuario.
     */
    public function test_actualizar_usuario_cambia_nombre_y_rol_pero_nunca_el_email(): void {
        $usuarioId = $this->crearUsuario(3);

        $stmt = $this->bd->prepare("SELECT email FROM usuarios WHERE id = :id");
        $stmt->execute([':id' => $usuarioId]);
        $emailOriginal = $stmt->fetchColumn();

        $resultado = $this->modeloUsuario->actualizarUsuario($usuarioId, [
            'nombre_completo' => 'Nombre Administrado',
            'email' => 'intruso@ascend.test',
            'rol_id' => 2,
        ]);

        $this->assertTrue($resultado);

        $stmt = $this->bd->prepare(
            "SELECT nombre_completo, email, rol_id, email_verificado FROM usuarios WHERE id = :id"
        );
        $stmt->execute([':id' => $usuarioId]);
        $fila = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertSame('Nombre Administrado', $fila['nombre_completo']);
        $this->assertSame($emailOriginal, $fila['email'], 'El email no debe cambiar desde el panel administrativo.');
        $this->assertSame(2, (int) $fila['rol_id']);
        $this->assertSame(1, (int) $fila['email_verificado'], 'La verificación del email debe conservarse.');
    }

    /**
     * La baja administrativa es lógica: conserva la fila, el perfil y el historial.
     */
    public function test_baja_logica_conserva_fila_perfil_e_historial(): void {
        $usuarioId = $this->crearUsuario(3);
        $this->bd->exec(
            "INSERT INTO perfiles_jugadores (usuario_id, apodo_gamertag)
             VALUES ($usuarioId, 'ApodoBaja')"
        );

        $resultado = $this->modeloUsuario->eliminarUsuario($usuarioId);
        $this->assertTrue($resultado['exito']);

        $stmt = $this->bd->prepare("SELECT esta_activo FROM usuarios WHERE id = :id");
        $stmt->execute([':id' => $usuarioId]);
        $fila = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertNotFalse($fila, 'El usuario no debe desaparecer de la tabla.');
        $this->assertSame(0, (int) $fila['esta_activo']);

        $stmt = $this->bd->prepare("SELECT COUNT(*) FROM perfiles_jugadores WHERE usuario_id = :id");
        $stmt->execute([':id' => $usuarioId]);
        $this->assertSame(1, (int) $stmt->fetchColumn(), 'El perfil debe conservarse.');

        $segunda = $this->modeloUsuario->eliminarUsuario($usuarioId);
        $this->assertTrue($segunda['exito']);
        $this->assertStringContainsString('ya estaba', $segunda['mensaje']);

        $inexistente = $this->modeloUsuario->eliminarUsuario(999999999);
        $this->assertFalse($inexistente['exito']);
    }

    /**
     * Al cambiar el rol se crea el perfil del rol destino y no se pisan los datos existentes.
     */
    public function test_cambio_de_rol_sincroniza_el_perfil_del_destino(): void {
        $usuarioId = $this->crearUsuario(3);

        $stmt = $this->bd->prepare("SELECT email FROM usuarios WHERE id = :id");
        $stmt->execute([':id' => $usuarioId]);
        $email = (string) $stmt->fetchColumn();

        $this->assertTrue($this->modeloUsuario->actualizarUsuario($usuarioId, [
            'nombre_completo' => 'Rol Organizador',
            'rol_id' => 2,
        ]));

        $stmt = $this->bd->prepare(
            "SELECT nombre_organizacion, verificado_oficial FROM perfiles_organizadores WHERE usuario_id = :id"
        );
        $stmt->execute([':id' => $usuarioId]);
        $organizador = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertNotFalse($organizador, 'Debe crearse el perfil de organizador.');
        $this->assertSame(0, (int) $organizador['verificado_oficial']);

        // Una verificación posterior no debe perderse al volver a guardar el mismo rol.
        $this->bd->exec("UPDATE perfiles_organizadores SET verificado_oficial = 1 WHERE usuario_id = $usuarioId");
        $this->assertTrue($this->modeloUsuario->actualizarUsuario($usuarioId, [
            'nombre_completo' => 'Rol Organizador 2',
            'rol_id' => 2,
        ]));

        $stmt = $this->bd->prepare("SELECT verificado_oficial FROM perfiles_organizadores WHERE usuario_id = :id");
        $stmt->execute([':id' => $usuarioId]);
        $this->assertSame(1, (int) $stmt->fetchColumn());

        // Volver a jugador crea el perfil faltante con el apodo derivado del email.
        $this->assertTrue($this->modeloUsuario->actualizarUsuario($usuarioId, [
            'nombre_completo' => 'Rol Jugador',
            'rol_id' => 3,
        ]));

        $stmt = $this->bd->prepare("SELECT apodo_gamertag FROM perfiles_jugadores WHERE usuario_id = :id");
        $stmt->execute([':id' => $usuarioId]);
        $apodo = $stmt->fetchColumn();

        $this->assertSame(explode('@', $email)[0], $apodo);
    }

    /**
     * El impacto informado antes de cambiar el rol cuenta las dependencias reales.
     */
    public function test_impacto_de_rol_cuenta_torneos_equipos_y_capitanias(): void {
        $adminId = $this->crearUsuario(1);
        $juegoId = $this->crearJuego();
        $this->crearTorneo($adminId, $juegoId, 'Torneo Impacto ' . uniqid());
        $equipoId = $this->crearEquipo($adminId, $juegoId, 'Equipo Impacto ' . uniqid());
        $this->agregarMiembro($equipoId, $adminId, true);

        $impacto = $this->modeloUsuario->obtenerImpactoRol($adminId);

        $this->assertSame(1, $impacto['torneos']);
        $this->assertSame(1, $impacto['equipos']);
        $this->assertSame(1, $impacto['capitanias']);
    }
}
