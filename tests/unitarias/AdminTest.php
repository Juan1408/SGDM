<?php
namespace Tests\Unitarias;

use Tests\TestCase;
use App\Modelos\Usuario;
use App\Modelos\Juego;
use App\Modelos\Torneo;
use App\Modelos\ErrorSistema;
use App\Modelos\PoliticaContrasena;
use App\Modelos\Alerta;

require_once __DIR__ . '/../../codigo_fuente/modelos/Usuario.php';
require_once __DIR__ . '/../../codigo_fuente/modelos/Juego.php';
require_once __DIR__ . '/../../codigo_fuente/modelos/Torneo.php';
require_once __DIR__ . '/../../codigo_fuente/modelos/ErrorSistema.php';
require_once __DIR__ . '/../../codigo_fuente/modelos/PoliticaContrasena.php';
require_once __DIR__ . '/../../codigo_fuente/modelos/Alerta.php';
require_once __DIR__ . '/../../codigo_fuente/ayudantes/Auditorias.php';

class AdminTest extends TestCase {

    private Usuario $modeloUsuario;
    private \Juego $modeloJuego;
    private Torneo $modeloTorneo;
    private ErrorSistema $modeloError;
    private PoliticaContrasena $modeloPolitica;
    private Alerta $modeloAlerta;

    protected function setUp(): void {
        parent::setUp();
        $this->modeloUsuario = new Usuario();
        $this->modeloJuego = new \Juego();
        $this->modeloTorneo = new Torneo();
        $this->modeloError = new ErrorSistema();
        $this->modeloPolitica = new PoliticaContrasena();
        $this->modeloAlerta = new Alerta();

        // Restaurar la política de contraseñas por defecto
        $this->bd->query("TRUNCATE TABLE politicas_contrasenas");
        $this->bd->query("INSERT INTO politicas_contrasenas (id, longitud_minima, requiere_mayuscula, requiere_minuscula, requiere_numero, requiere_caracter_especial, expiracion_dias, historial_cantidad) VALUES (1, 8, 1, 1, 1, 1, 90, 5)");
    }

    /**
     * 1. GESTIÓN DE USUARIOS
     * El administrador puede bloquear a un usuario (cambiarEstadoUsuario).
     */
    public function test_admin_puede_bloquear_usuario() {
        $jugadorId = $this->crearUsuarioFalso(3);
        $resultado = $this->modeloUsuario->cambiarEstadoUsuario($jugadorId);
        $this->assertTrue($resultado, 'Debería devolver true al ejecutarse correctamente.');
        $usuarioBd = $this->modeloUsuario->obtenerUsuarioPorId($jugadorId);
        $this->assertEquals(0, $usuarioBd['esta_activo'], 'El usuario debería estar inactivo en la BD.');
    }

    /**
     * 2. GESTIÓN DE CATÁLOGO DE JUEGOS
     * El administrador puede crear un nuevo juego y desactivarlo.
     */
    public function test_admin_puede_gestionar_catalogo_juegos() {
        $exito = $this->modeloJuego->crearJuego('Valorant', 'Shooter', 3, 1, 0);
        $this->assertTrue($exito, 'El admin debería poder crear un juego.');

        $juegos = $this->modeloJuego->obtenerTodosLosJuegos();
        $this->assertCount(1, $juegos, 'Debería haber 1 juego en el catálogo.');
        $juegoId = $juegos[0]['id'];
        
        $this->modeloJuego->cambiarEstado($juegoId);
        $juegoBd = $this->modeloJuego->obtenerJuegoPorId($juegoId);
        $this->assertEquals(0, $juegoBd['activo'], 'El juego debería estar desactivado.');
    }

    /**
     * 3. MONITOREO DE ERRORES DEL SISTEMA
     * El administrador puede registrar y cambiar el estado de errores.
     */
    public function test_admin_puede_gestionar_errores_sistema() {
        // Registrar error simulado
        $stmt = $this->bd->prepare("INSERT INTO errores_sistema (hash_error, tipo_error, mensaje, archivo, linea, stack_trace, estado) VALUES ('hash123', 'Excepcion', 'Falla de BD', 'archivo.php', 10, 'trace', 'nuevo')");
        $stmt->execute();
        $errorId = $this->bd->lastInsertId();

        // Obtener errores
        $errores = $this->modeloError->obtenerErroresAgrupados();
        $this->assertGreaterThan(0, count($errores));

        // Cambiar estado a resuelto
        $this->modeloError->cambiarEstado($errorId, 'resuelto', 'Se reinició el servidor de BD');

        // Verificamos en DB
        $errorMod = $this->modeloError->obtenerDetalleError($errorId);
        $this->assertEquals('resuelto', $errorMod['estado'], 'El error debería estar resuelto.');
    }

    /**
     * 4. POLÍTICAS DE CONTRASEÑAS CORPORATIVAS
     * El administrador puede actualizar y validar políticas de contraseña.
     */
    public function test_admin_puede_actualizar_politicas_de_contrasena() {
        // Obtenemos la política por defecto
        $politicaOriginal = $this->modeloPolitica->obtenerPoliticaVigente();
        $this->assertEquals(8, $politicaOriginal['longitud_minima']);

        // Admin cambia la política a 12 caracteres mínimo sin caracteres especiales
        $nuevaPolitica = [
            'longitud_minima' => 12,
            'requiere_mayuscula' => 1,
            'requiere_minuscula' => 1,
            'requiere_numero' => 1,
            'requiere_caracter_especial' => 0,
            'expiracion_dias' => 60,
            'historial_cantidad' => 3
        ];
        
        $adminId = $this->crearUsuarioFalso(1);
        $this->modeloPolitica->guardarPolitica($nuevaPolitica, $adminId);

        // Verificamos cambios
        $politicaActual = $this->modeloPolitica->obtenerPoliticaVigente();
        $this->assertEquals(12, $politicaActual['longitud_minima'], 'La política debería actualizar la longitud a 12.');
        $this->assertEquals(0, $politicaActual['requiere_caracter_especial']);

        // Verificamos validación de contraseñas bajo nueva política
        $validacionFalla = $this->modeloPolitica->validarPassword('Pass123'); // muy corta, 7 chars
        $this->assertFalse($validacionFalla['valido'], 'Debería fallar porque la contraseña tiene menos de 12 caracteres.');

        $validacionPasa = $this->modeloPolitica->validarPassword('PasswordSuperLargo123'); // cumple todo
        $this->assertTrue($validacionPasa['valido'], 'Debería pasar porque cumple longitud, min/may y números.');
    }

    /**
     * 5. ESTADÍSTICAS GLOBALES DEL DASHBOARD
     * El dashboard del admin cuenta correctamente los torneos activos.
     */
    public function test_dashboard_admin_cuenta_torneos_activos() {
        $organizadorId = $this->crearUsuarioFalso(2);
        // El modelo escribe las tablas puente en la misma transacción; sin ellas
        // los triggers de la migración 007 rechazan la creación de torneos.
        $this->modeloJuego->crearJuego('LoL', 'MOBA', 3, 1, 0, [2], ['liga']);
        $juegos = $this->modeloJuego->obtenerTodosLosJuegos();
        $juegoId = $juegos[0]['id'];

        $sql = "INSERT INTO torneos (nombre, organizador_id, juego_id, estado, modalidad_id, tipo_resultado, formato, cupo_max_equipos) VALUES 
                ('Torneo 1', ?, ?, 'en_curso', 2, 'goles', 'liga', 4),
                ('Torneo 2', ?, ?, 'inscripciones_abiertas', 2, 'goles', 'liga', 4),
                ('Torneo 3', ?, ?, 'finalizado', 2, 'goles', 'liga', 4)";
                
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([$organizadorId, $juegoId, $organizadorId, $juegoId, $organizadorId, $juegoId]);

        $totalActivos = $this->modeloTorneo->contarTorneosActivos();
        $this->assertEquals(2, $totalActivos, 'Debería contar solo 2 torneos activos (en curso y abiertas).');
    }

    /**
     * 6. COMUNICACIONES Y ALERTAS GLOBALES
     * El administrador puede enviar notificaciones masivas.
     */
    public function test_admin_puede_enviar_alerta_global() {
        // Insertamos varios usuarios activos
        $jugador1 = $this->crearUsuarioFalso(3);
        $jugador2 = $this->crearUsuarioFalso(3);
        $org1 = $this->crearUsuarioFalso(2);

        // Enviamos alerta solo a jugadores
        $resultado = $this->modeloAlerta->enviarAlertaGlobal('Mantenimiento', 'Servidores abajo', 'advertencia', 'jugador');
        
        $this->assertTrue($resultado['exito']);
        $this->assertEquals(2, $resultado['enviados'], 'Debería enviar exactamente a los 2 jugadores creados.');

        // Comprobar que en BD existen 2 notificaciones
        $stmt = $this->bd->query("SELECT COUNT(*) FROM notificaciones");
        $this->assertEquals(2, $stmt->fetchColumn(), 'Debe haber 2 notificaciones en total.');
    }

    // --- Métodos de Ayuda ---

    private function crearUsuarioFalso(int $rolId): int {
        $email = "test_" . uniqid() . "@test.com";
        $hash = password_hash('123456', PASSWORD_DEFAULT);
        
        $stmt = $this->bd->prepare("INSERT INTO usuarios (email, contrasena_hash, nombre_completo, rol_id, esta_activo, email_verificado) VALUES (?, ?, 'Usuario Test', ?, 1, 1)");
        $stmt->execute([$email, $hash, $rolId]);
        
        return (int) $this->bd->lastInsertId();
    }
}
