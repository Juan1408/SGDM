<?php
namespace Tests\Unitarias;

use Tests\TestCase;
use App\Modelos\Equipo;

require_once __DIR__ . '/../../codigo_fuente/modelos/Equipo.php';

/**
 * Ingreso a un equipo mediante el codigo de invitacion (CU-05 / RF responsabilidad
 * del perfil del jugador): quien conoce el codigo queda como miembro al instante,
 * sin pasar por la bandeja de solicitudes.
 *
 * Las pruebas trabajan contra ascend_test con datos propios creados en cada caso.
 */
class UnionEquipoCodigoTest extends TestCase {

    private Equipo $modeloEquipo;

    protected function setUp(): void {
        parent::setUp();

        //Tablas del dominio de equipos: no estan en el setUp general
        $this->bd->query("SET FOREIGN_KEY_CHECKS = 0;");
        $this->bd->query("TRUNCATE TABLE solicitudes_equipo");
        $this->bd->query("TRUNCATE TABLE equipo_capitanes");
        $this->bd->query("TRUNCATE TABLE equipo_miembros");
        $this->bd->query("TRUNCATE TABLE equipos");
        $this->bd->query("SET FOREIGN_KEY_CHECKS = 1;");

        $this->modeloEquipo = Equipo::obtenerInstancia();
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
            ':email' => 'union_' . uniqid() . '@ascend.test',
            ':hash' => password_hash('ClaveDePrueba123!', PASSWORD_BCRYPT),
            ':nombre' => 'Jugador Union ' . uniqid(),
            ':rol' => $rolId,
            ':activo' => $activo,
        ]);

        return (int) $this->bd->lastInsertId();
    }

    private function crearEquipo(int $creadorId, string $codigo, int $activo = 1): int {
        $stmt = $this->bd->prepare(
            "INSERT INTO equipos (nombre_equipo, creado_por, codigo_invitacion, activo)
             VALUES (:nombre, :creador, :codigo, :activo)"
        );
        $stmt->execute([
            ':nombre' => 'Equipo ' . uniqid(),
            ':creador' => $creadorId,
            ':codigo' => $codigo,
            ':activo' => $activo,
        ]);

        return (int) $this->bd->lastInsertId();
    }

    private function contarMembresias(int $equipoId, int $usuarioId): int {
        $stmt = $this->bd->prepare(
            "SELECT COUNT(*) FROM equipo_miembros WHERE equipo_id = :equipoId AND usuario_id = :usuarioId"
        );
        $stmt->execute([':equipoId' => $equipoId, ':usuarioId' => $usuarioId]);

        return (int) $stmt->fetchColumn();
    }

    private function contarLogs(string $accion, int $usuarioId): int {
        $stmt = $this->bd->prepare(
            "SELECT COUNT(*) FROM logs_actividad WHERE accion = :accion AND usuario_id = :usuarioId"
        );
        $stmt->execute([':accion' => $accion, ':usuarioId' => $usuarioId]);

        return (int) $stmt->fetchColumn();
    }

    private function crearSolicitudPendiente(int $equipoId, int $usuarioId, int $iniciadoPor): int {
        $stmt = $this->bd->prepare(
            "INSERT INTO solicitudes_equipo (equipo_id, usuario_id, iniciado_por, mensaje, estado)
             VALUES (:equipoId, :usuarioId, :iniciadoPor, 'Quiero sumarme', 'pendiente')"
        );
        $stmt->execute([
            ':equipoId' => $equipoId,
            ':usuarioId' => $usuarioId,
            ':iniciadoPor' => $iniciadoPor,
        ]);

        return (int) $this->bd->lastInsertId();
    }

    // ------------------------------------------------------------------
    // Ingreso con codigo
    // ------------------------------------------------------------------

    public function test_codigo_valido_incorpora_al_jugador_como_miembro(): void {
        $capitanId = $this->crearUsuario(3);
        $jugadorId = $this->crearUsuario(3);
        $equipoId = $this->crearEquipo($capitanId, 'A1B2C3D4');

        $resultado = $this->modeloEquipo->unirsePorCodigo('A1B2C3D4', $jugadorId);

        $this->assertTrue($resultado['exito']);
        $this->assertFalse($resultado['ya_era_miembro']);
        $this->assertSame($equipoId, $resultado['equipo_id']);
        $this->assertSame(1, $this->contarMembresias($equipoId, $jugadorId));

        $stmt = $this->bd->prepare(
            "SELECT es_activo, fecha_union FROM equipo_miembros
             WHERE equipo_id = :equipoId AND usuario_id = :usuarioId"
        );
        $stmt->execute([':equipoId' => $equipoId, ':usuarioId' => $jugadorId]);
        $membresia = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertSame(1, (int) $membresia['es_activo'], 'La fila debe quedar activa.');
        $this->assertNotNull($membresia['fecha_union'], 'Debe registrarse la fecha de union.');

        $this->assertSame(1, $this->contarLogs('UNION_CODIGO_EXITOSA', $jugadorId));
    }

    public function test_el_codigo_se_acepta_en_minusculas_y_con_espacios(): void {
        $capitanId = $this->crearUsuario(3);
        $jugadorId = $this->crearUsuario(3);
        $equipoId = $this->crearEquipo($capitanId, '0F1E2D3C');

        //El jugador copia el codigo con espacios o en minusculas: el modelo normaliza
        $resultado = $this->modeloEquipo->unirsePorCodigo('  0f1e2d3c ', $jugadorId);

        $this->assertTrue($resultado['exito']);
        $this->assertSame($equipoId, $resultado['equipo_id']);
        $this->assertSame(1, $this->contarMembresias($equipoId, $jugadorId));
    }

    public function test_codigo_inexistente_no_crea_membresia_y_queda_auditado(): void {
        $capitanId = $this->crearUsuario(3);
        $jugadorId = $this->crearUsuario(3);
        $equipoId = $this->crearEquipo($capitanId, 'A1B2C3D4');

        $resultado = $this->modeloEquipo->unirsePorCodigo('FFFFFFFF', $jugadorId);

        $this->assertFalse($resultado['exito']);
        $this->assertSame('codigo_invalido', $resultado['error']);
        $this->assertSame(0, $this->contarMembresias($equipoId, $jugadorId));
        $this->assertSame(1, $this->contarLogs('UNION_CODIGO_FALLIDA', $jugadorId));

        //El codigo ingresado nunca se guarda en la auditoria
        $stmt = $this->bd->prepare(
            "SELECT descripcion FROM logs_actividad
             WHERE accion = 'UNION_CODIGO_FALLIDA' AND usuario_id = :usuarioId LIMIT 1"
        );
        $stmt->execute([':usuarioId' => $jugadorId]);
        $this->assertStringNotContainsString('FFFFFFFF', (string) $stmt->fetchColumn());
    }

    public function test_equipo_inactivo_no_admite_ingresos_por_codigo(): void {
        $capitanId = $this->crearUsuario(3);
        $jugadorId = $this->crearUsuario(3);
        $equipoId = $this->crearEquipo($capitanId, 'ABCDEF12', 0);

        $resultado = $this->modeloEquipo->unirsePorCodigo('ABCDEF12', $jugadorId);

        $this->assertFalse($resultado['exito']);
        $this->assertSame('codigo_invalido', $resultado['error']);
        $this->assertSame(0, $this->contarMembresias($equipoId, $jugadorId));
    }

    public function test_un_segundo_ingreso_no_duplica_la_membresia(): void {
        $capitanId = $this->crearUsuario(3);
        $jugadorId = $this->crearUsuario(3);
        $equipoId = $this->crearEquipo($capitanId, 'A1B2C3D4');

        $primero = $this->modeloEquipo->unirsePorCodigo('A1B2C3D4', $jugadorId);
        $segundo = $this->modeloEquipo->unirsePorCodigo('A1B2C3D4', $jugadorId);

        $this->assertTrue($primero['exito']);
        $this->assertTrue($segundo['exito']);
        $this->assertTrue($segundo['ya_era_miembro']);
        $this->assertSame(1, $this->contarMembresias($equipoId, $jugadorId), 'No debe duplicar la fila.');
    }

    public function test_una_cuenta_que_no_es_jugador_no_puede_ingresar(): void {
        $capitanId = $this->crearUsuario(3);
        $organizadorId = $this->crearUsuario(2);
        $equipoId = $this->crearEquipo($capitanId, 'A1B2C3D4');

        $resultado = $this->modeloEquipo->unirsePorCodigo('A1B2C3D4', $organizadorId);

        $this->assertFalse($resultado['exito']);
        $this->assertSame('no_jugador', $resultado['error']);
        $this->assertSame(0, $this->contarMembresias($equipoId, $organizadorId));
    }

    public function test_un_codigo_con_formato_invalido_se_rechaza(): void {
        $capitanId = $this->crearUsuario(3);
        $jugadorId = $this->crearUsuario(3);
        $equipoId = $this->crearEquipo($capitanId, 'A1B2C3D4');

        foreach (['ABC', 'A1B2C3D', 'A1B2C3D45', 'ZZZZZZZZ', 'A1B2-C3D'] as $codigoMalFormado) {
            $resultado = $this->modeloEquipo->unirsePorCodigo($codigoMalFormado, $jugadorId);

            $this->assertFalse($resultado['exito'], "El codigo '$codigoMalFormado' no debe aceptarse.");
            $this->assertSame('codigo_invalido', $resultado['error']);
        }

        $this->assertSame(0, $this->contarMembresias($equipoId, $jugadorId));
    }

    public function test_cinco_intentos_fallidos_bloquean_temporalmente_el_ingreso(): void {
        $capitanId = $this->crearUsuario(3);
        $jugadorId = $this->crearUsuario(3);
        $equipoId = $this->crearEquipo($capitanId, 'A1B2C3D4');

        //Cinco codigos con formato valido que no existen: agotan el margen permitido
        for ($i = 0; $i < 5; $i++) {
            $fallido = $this->modeloEquipo->unirsePorCodigo('DEADBEE' . $i, $jugadorId);
            $this->assertFalse($fallido['exito']);
            $this->assertSame('codigo_invalido', $fallido['error']);
        }

        //Con el codigo correcto el ingreso sigue bloqueado hasta que expire la ventana
        $bloqueado = $this->modeloEquipo->unirsePorCodigo('A1B2C3D4', $jugadorId);

        $this->assertFalse($bloqueado['exito']);
        $this->assertSame('demasiados_intentos', $bloqueado['error']);
        $this->assertSame(0, $this->contarMembresias($equipoId, $jugadorId));
    }

    public function test_el_ingreso_por_codigo_cierra_la_solicitud_pendiente(): void {
        $capitanId = $this->crearUsuario(3);
        $jugadorId = $this->crearUsuario(3);
        $equipoId = $this->crearEquipo($capitanId, 'A1B2C3D4');

        //El jugador habia pedido unirse y esperaba respuesta del capitan
        $solicitudId = $this->crearSolicitudPendiente($equipoId, $jugadorId, $jugadorId);

        $resultado = $this->modeloEquipo->unirsePorCodigo('A1B2C3D4', $jugadorId);
        $this->assertTrue($resultado['exito']);

        $stmt = $this->bd->prepare(
            "SELECT estado, respondido_por, fecha_respuesta FROM solicitudes_equipo WHERE id = :id"
        );
        $stmt->execute([':id' => $solicitudId]);
        $solicitud = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertSame('aprobado', $solicitud['estado'], 'La solicitud pendiente debe quedar resuelta.');
        $this->assertSame($jugadorId, (int) $solicitud['respondido_por']);
        $this->assertNotNull($solicitud['fecha_respuesta']);
    }

    // ------------------------------------------------------------------
    // Flujo de invitacion relacionado (RF-011): el capitan invita y el
    // jugador ve la invitacion en su bandeja y puede aceptarla.
    // ------------------------------------------------------------------

    public function test_la_invitacion_del_capitan_llega_a_la_bandeja_y_al_aceptarla_ingresa(): void {
        $capitanId = $this->crearUsuario(3);
        $jugadorId = $this->crearUsuario(3);
        $equipoId = $this->crearEquipo($capitanId, 'A1B2C3D4');

        $creada = $this->modeloEquipo->crearSolicitud($equipoId, $jugadorId, $capitanId);
        $this->assertTrue($creada);

        $invitaciones = $this->modeloEquipo->obtenerInvitacionesRecibidas($jugadorId);
        $this->assertCount(1, $invitaciones, 'La invitacion del capitan debe aparecer en la bandeja.');
        $this->assertSame($equipoId, (int) $invitaciones[0]['equipo_id']);

        $respuesta = $this->modeloEquipo->responderInvitacion(
            (int) $invitaciones[0]['id'],
            $jugadorId,
            true
        );

        $this->assertTrue($respuesta['exito']);
        $this->assertSame(1, $this->contarMembresias($equipoId, $jugadorId));
        $this->assertCount(0, $this->modeloEquipo->obtenerInvitacionesRecibidas($jugadorId));
    }

    // ------------------------------------------------------------------
    // Cableado de la interfaz: el formulario debe enviar el token CSRF.
    // Son comprobaciones estaticas del codigo entregado, no de navegador.
    // ------------------------------------------------------------------

    public function test_el_buscador_de_invitaciones_envia_el_token_csrf(): void {
        $js = file_get_contents(__DIR__ . '/../../codigo_fuente/publico/js/buscar-jugadores-vivo.js');
        $vista = file_get_contents(__DIR__ . '/../../codigo_fuente/vistas/jugador/gestionar-equipo.php');

        $this->assertStringContainsString('name="csrf_token"', $js, 'El formulario armado en JS debe incluir el token.');
        $this->assertStringContainsString('meta[name="csrf-token"]', $js);
        $this->assertStringContainsString('name="csrf-token"', $vista, 'La vista debe publicar el token para el JS.');
    }

    public function test_la_pantalla_de_solicitudes_ofrece_el_ingreso_por_codigo(): void {
        $vista = file_get_contents(__DIR__ . '/../../codigo_fuente/vistas/jugador/solicitudes.php');
        $rutas = file_get_contents(__DIR__ . '/../../codigo_fuente/publico/index.php');

        $this->assertStringContainsString('a=unirsePorCodigo', $vista);
        $this->assertStringContainsString('name="codigo"', $vista);
        $this->assertStringContainsString('name="csrf_token"', $vista);
        $this->assertStringContainsString("'unirsePorCodigo'", $rutas, 'La accion debe estar registrada en la tabla de rutas.');
    }
}
