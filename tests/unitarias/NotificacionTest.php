<?php
namespace Tests\Unitarias;

use Tests\TestCase;
use App\Modelos\Notificacion;
use App\Modelos\Alerta;

require_once __DIR__ . '/../../codigo_fuente/modelos/Notificacion.php';
require_once __DIR__ . '/../../codigo_fuente/modelos/Alerta.php';

/**
 * Bandeja de notificaciones del jugador y del organizador (Defecto 3).
 *
 * Cubre que el envio masivo del administrador llegue a la campana, que cada
 * cuenta solo vea y modifique sus propias filas y que las marcas de lectura se
 * persistan. Trabaja contra ascend_test con datos creados en cada caso.
 */
class NotificacionTest extends TestCase {

    private Notificacion $modeloNotificacion;

    protected function setUp(): void {
        parent::setUp();

        $this->modeloNotificacion = new Notificacion();
    }

    // ------------------------------------------------------------------
    // Datos de apoyo
    // ------------------------------------------------------------------

    private function crearUsuario(int $rolId = 3, int $activo = 1): int {
        $stmt = $this->bd->prepare(
            "INSERT INTO usuarios (email, contrasena_hash, nombre_completo, telefono, rol_id, esta_activo, email_verificado)
             VALUES (:email, :hash, :nombre, '+59899000000', :rol, :activo, 1)"
        );
        $stmt->execute([
            ':email' => 'notif_' . uniqid() . '@ascend.test',
            ':hash' => password_hash('ClaveDePrueba123!', PASSWORD_BCRYPT),
            ':nombre' => 'Usuario Notificacion ' . uniqid(),
            ':rol' => $rolId,
            ':activo' => $activo,
        ]);

        return (int) $this->bd->lastInsertId();
    }

    private function crearNotificacion(int $usuarioId, string $titulo, string $creadoEn, int $leido = 0): int {
        $stmt = $this->bd->prepare(
            "INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, leido, creado_en)
             VALUES (:usuario, 'info', :titulo, 'Mensaje de prueba', :leido, :creadoEn)"
        );
        $stmt->execute([
            ':usuario' => $usuarioId,
            ':titulo' => $titulo,
            ':leido' => $leido,
            ':creadoEn' => $creadoEn,
        ]);

        return (int) $this->bd->lastInsertId();
    }

    private function obtenerFila(int $id): array {
        $stmt = $this->bd->prepare(
            "SELECT usuario_id, leido, fecha_lectura FROM notificaciones WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);

        return (array) $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // ------------------------------------------------------------------
    // Lectura de la bandeja
    // ------------------------------------------------------------------

    public function test_obtener_recientes_devuelve_solo_al_usuario_y_de_mas_nueva_a_mas_vieja(): void {
        $jugadorId = $this->crearUsuario(3);
        $otroJugadorId = $this->crearUsuario(3);

        $this->crearNotificacion($jugadorId, 'Primera', '2026-10-01 10:00:00');
        $this->crearNotificacion($jugadorId, 'Tercera', '2026-10-03 10:00:00');
        $this->crearNotificacion($jugadorId, 'Segunda', '2026-10-02 10:00:00');
        $this->crearNotificacion($otroJugadorId, 'Ajena', '2026-10-04 10:00:00');

        $recientes = $this->modeloNotificacion->obtenerRecientes($jugadorId);

        $this->assertCount(3, $recientes);
        $this->assertSame(
            ['Tercera', 'Segunda', 'Primera'],
            array_column($recientes, 'titulo'),
            'Debe respetar el orden descendente por fecha.'
        );
        $this->assertSame('Mensaje de prueba', $recientes[0]['mensaje']);
    }

    public function test_obtener_recientes_respeta_el_limite_pedido(): void {
        $jugadorId = $this->crearUsuario(3);
        for ($i = 1; $i <= 5; $i++) {
            $this->crearNotificacion($jugadorId, 'Aviso ' . $i, sprintf('2026-10-0%d 10:00:00', $i));
        }

        $this->assertCount(2, $this->modeloNotificacion->obtenerRecientes($jugadorId, 2));
    }

    public function test_contar_no_leidas_solo_cuenta_las_pendientes_propias(): void {
        $jugadorId = $this->crearUsuario(3);
        $organizadorId = $this->crearUsuario(2);

        $this->crearNotificacion($jugadorId, 'Pendiente 1', '2026-10-01 10:00:00');
        $this->crearNotificacion($jugadorId, 'Pendiente 2', '2026-10-02 10:00:00');
        $this->crearNotificacion($jugadorId, 'Leida', '2026-10-03 10:00:00', 1);
        $this->crearNotificacion($organizadorId, 'Ajena pendiente', '2026-10-04 10:00:00');

        $this->assertSame(2, $this->modeloNotificacion->contarNoLeidas($jugadorId));
        $this->assertSame(1, $this->modeloNotificacion->contarNoLeidas($organizadorId));
        $this->assertSame(0, $this->modeloNotificacion->contarNoLeidas(0));
    }

    // ------------------------------------------------------------------
    // Marcas de lectura
    // ------------------------------------------------------------------

    public function test_marcar_leida_persiste_y_es_idempotente(): void {
        $jugadorId = $this->crearUsuario(3);
        $notificacionId = $this->crearNotificacion($jugadorId, 'Aviso', '2026-10-01 10:00:00');

        $this->assertTrue($this->modeloNotificacion->marcarLeida($notificacionId, $jugadorId));

        $fila = $this->obtenerFila($notificacionId);
        $this->assertSame(1, (int) $fila['leido']);
        $this->assertNotNull($fila['fecha_lectura']);
        $this->assertSame(0, $this->modeloNotificacion->contarNoLeidas($jugadorId));

        //Repetir la marca sobre la misma fila no es un error
        $this->assertTrue($this->modeloNotificacion->marcarLeida($notificacionId, $jugadorId));
    }

    public function test_no_se_puede_marcar_la_notificacion_de_otra_cuenta(): void {
        $duenioId = $this->crearUsuario(3);
        $intrusoId = $this->crearUsuario(3);
        $notificacionId = $this->crearNotificacion($duenioId, 'Privada', '2026-10-01 10:00:00');

        $this->assertFalse($this->modeloNotificacion->marcarLeida($notificacionId, $intrusoId));

        $fila = $this->obtenerFila($notificacionId);
        $this->assertSame(0, (int) $fila['leido'], 'La fila ajena no debe cambiar.');
        $this->assertNull($fila['fecha_lectura']);
        $this->assertSame(1, $this->modeloNotificacion->contarNoLeidas($duenioId));
    }

    public function test_marcar_leida_rechaza_ids_invalidos(): void {
        $jugadorId = $this->crearUsuario(3);

        $this->assertFalse($this->modeloNotificacion->marcarLeida(0, $jugadorId));
        $this->assertFalse($this->modeloNotificacion->marcarLeida(999999, $jugadorId));
        $this->assertFalse($this->modeloNotificacion->marcarLeida(1, 0));
    }

    public function test_marcar_todas_leidas_alcanza_solo_al_usuario_e_informa_cuantas_cambio(): void {
        $jugadorId = $this->crearUsuario(3);
        $otroJugadorId = $this->crearUsuario(3);

        $primera = $this->crearNotificacion($jugadorId, 'Pendiente 1', '2026-10-01 10:00:00');
        $segunda = $this->crearNotificacion($jugadorId, 'Pendiente 2', '2026-10-02 10:00:00');
        $yaLeida = $this->crearNotificacion($jugadorId, 'Ya leida', '2026-10-03 10:00:00', 1);
        $ajena = $this->crearNotificacion($otroJugadorId, 'Ajena', '2026-10-04 10:00:00');

        $this->assertSame(2, $this->modeloNotificacion->marcarTodasLeidas($jugadorId));

        $this->assertSame(1, (int) $this->obtenerFila($primera)['leido']);
        $this->assertSame(1, (int) $this->obtenerFila($segunda)['leido']);
        $this->assertSame(1, (int) $this->obtenerFila($yaLeida)['leido']);
        $this->assertSame(0, (int) $this->obtenerFila($ajena)['leido'], 'La fila de otra cuenta debe quedar intacta.');

        //Una segunda pasada no tiene nada pendiente
        $this->assertSame(0, $this->modeloNotificacion->marcarTodasLeidas($jugadorId));
        $this->assertSame(1, $this->modeloNotificacion->contarNoLeidas($otroJugadorId));
    }

    // ------------------------------------------------------------------
    // Envio masivo del administrador (modelo Alerta)
    // ------------------------------------------------------------------

    public function test_la_audiencia_todos_incluye_jugadores_y_organizadores_pero_no_administradores(): void {
        $adminId = $this->crearUsuario(1);
        $jugadorActivoId = $this->crearUsuario(3);
        $organizadorActivoId = $this->crearUsuario(2);
        $jugadorInactivoId = $this->crearUsuario(3, 0);

        $alerta = new Alerta();
        $resultado = $alerta->enviarAlertaGlobal('Mantenimiento', 'El sitio estara en mantenimiento.', 'info', 'todos');

        $this->assertTrue($resultado['exito']);
        $this->assertSame(2, (int) $resultado['enviados'], 'Solo deben recibirla jugadores y organizadores activos.');

        $this->assertSame(1, $this->modeloNotificacion->contarNoLeidas($jugadorActivoId));
        $this->assertSame(1, $this->modeloNotificacion->contarNoLeidas($organizadorActivoId));
        $this->assertSame(0, $this->modeloNotificacion->contarNoLeidas($adminId));
        $this->assertSame(0, $this->modeloNotificacion->contarNoLeidas($jugadorInactivoId));

        $recibidas = $this->modeloNotificacion->obtenerRecientes($jugadorActivoId);
        $this->assertSame('Mantenimiento', $recibidas[0]['titulo']);
    }

    public function test_las_audiencias_especificas_solo_alcanzan_a_su_rol(): void {
        $this->crearUsuario(3);
        $this->crearUsuario(2);
        $this->crearUsuario(1);

        $alerta = new Alerta();
        $soloJugadores = $alerta->enviarAlertaGlobal('Para jugadores', 'Cuerpo', 'info', 'jugador');
        $soloOrganizadores = $alerta->enviarAlertaGlobal('Para organizadores', 'Cuerpo', 'info', 'organizador');

        $this->assertSame(1, (int) $soloJugadores['enviados']);
        $this->assertSame(1, (int) $soloOrganizadores['enviados']);

        $filas = $this->bd->query(
            "SELECT titulo, COUNT(*) AS total FROM notificaciones GROUP BY titulo ORDER BY titulo"
        )->fetchAll(\PDO::FETCH_ASSOC);

        $this->assertSame(
            [
                ['titulo' => 'Para jugadores', 'total' => 1],
                ['titulo' => 'Para organizadores', 'total' => 1],
            ],
            $filas
        );
    }

    public function test_sin_usuarios_activos_en_la_audiencia_la_alerta_no_inserta_nada(): void {
        $this->crearUsuario(3, 0);

        $alerta = new Alerta();
        $resultado = $alerta->enviarAlertaGlobal('Sin destinatarios', 'Cuerpo', 'info', 'jugador');

        $this->assertFalse($resultado['exito']);
        $this->assertSame(0, (int) $resultado['enviados']);
        $this->assertSame(0, (int) $this->bd->query("SELECT COUNT(*) FROM notificaciones")->fetchColumn());
    }
}
