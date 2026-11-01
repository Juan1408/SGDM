<?php
namespace Tests\Unitarias;

use Tests\TestCase;
use App\Modelos\Equipo;

require_once __DIR__ . '/../../codigo_fuente/modelos/Equipo.php';

/**
 * Datos de las pestañas Torneos y Ranking del perfil de equipo (Defecto 5).
 *
 * Verifica que el perfil muestre los torneos reales del equipo, calcule su
 * puesto con el mismo orden que la tabla publica (puntos, diferencia, favor,
 * nombre) y no mezcle informacion de otros equipos.
 */
class EquipoPerfilTorneosTest extends TestCase {

    private Equipo $modeloEquipo;

    private int $organizadorId;

    protected function setUp(): void {
        parent::setUp();

        //Tablas del dominio de equipos y torneos que el setUp general no vacia
        $this->bd->query("SET FOREIGN_KEY_CHECKS = 0;");
        $this->bd->query("TRUNCATE TABLE torneo_posiciones");
        $this->bd->query("TRUNCATE TABLE torneo_encuentros");
        $this->bd->query("TRUNCATE TABLE participantes_torneo");
        $this->bd->query("TRUNCATE TABLE solicitudes_equipo");
        $this->bd->query("TRUNCATE TABLE equipo_capitanes");
        $this->bd->query("TRUNCATE TABLE equipo_miembros");
        $this->bd->query("TRUNCATE TABLE equipos");
        $this->bd->query("SET FOREIGN_KEY_CHECKS = 1;");

        $this->sembrarCatalogos();
        $this->organizadorId = $this->crearUsuario(2);
        $this->modeloEquipo = Equipo::obtenerInstancia();
    }

    protected function tearDown(): void {
        //No dejar equipos apuntando a usuarios de esta suite: otras clases
        //reutilizan IDs desde 1 y el FK RESTRICT impediria su limpieza.
        $this->bd->query("SET FOREIGN_KEY_CHECKS = 0;");
        $this->bd->query("TRUNCATE TABLE torneo_posiciones");
        $this->bd->query("TRUNCATE TABLE torneo_encuentros");
        $this->bd->query("TRUNCATE TABLE participantes_torneo");
        $this->bd->query("TRUNCATE TABLE solicitudes_equipo");
        $this->bd->query("TRUNCATE TABLE equipo_capitanes");
        $this->bd->query("TRUNCATE TABLE equipo_miembros");
        $this->bd->query("TRUNCATE TABLE equipos");
        $this->bd->query("SET FOREIGN_KEY_CHECKS = 1;");

        parent::tearDown();
    }

    /** Juego habilitado con una modalidad y un formato compatibles con los triggers. */
    private function sembrarCatalogos(): void {
        $this->bd->exec("INSERT INTO juegos (id, nombre, categoria) VALUES (1, 'League of Legends', 'esport-moba')");
        $this->bd->exec("INSERT INTO juego_modalidades_permitidas (juego_id, modalidad_id) VALUES (1, 2)");
        $this->bd->exec("INSERT INTO juego_formatos_permitidos (juego_id, formato_codigo) VALUES (1, 'liga')");
    }

    // ------------------------------------------------------------------
    // Datos de apoyo
    // ------------------------------------------------------------------

    private function crearUsuario(int $rolId = 3): int {
        $stmt = $this->bd->prepare(
            "INSERT INTO usuarios (email, contrasena_hash, nombre_completo, telefono, rol_id, esta_activo, email_verificado)
             VALUES (:email, :hash, :nombre, '+59899000000', :rol, 1, 1)"
        );
        $stmt->execute([
            ':email' => 'perfil_' . uniqid() . '@ascend.test',
            ':hash' => password_hash('ClaveDePrueba123!', PASSWORD_BCRYPT),
            ':nombre' => 'Usuario Perfil ' . uniqid(),
            ':rol' => $rolId,
        ]);

        return (int) $this->bd->lastInsertId();
    }

    private function crearEquipo(int $creadorId, string $nombre): int {
        $stmt = $this->bd->prepare(
            "INSERT INTO equipos (nombre_equipo, creado_por, codigo_invitacion, activo)
             VALUES (:nombre, :creador, :codigo, 1)"
        );
        $stmt->execute([
            ':nombre' => $nombre,
            ':creador' => $creadorId,
            ':codigo' => strtoupper(substr(bin2hex(random_bytes(4)), 0, 8)),
        ]);

        return (int) $this->bd->lastInsertId();
    }

    private function crearTorneo(string $nombre, ?string $fechaInicio, ?string $fechaFin = null): int {
        $stmt = $this->bd->prepare(
            "INSERT INTO torneos (nombre, juego_id, organizador_id, estado, formato, modalidad_id, fecha_inicio, fecha_fin, cupo_max_equipos)
             VALUES (:nombre, 1, :organizador, 'inscripciones_abiertas', 'liga', 2, :inicio, :fin, 8)"
        );
        $stmt->execute([
            ':nombre' => $nombre,
            ':organizador' => $this->organizadorId,
            ':inicio' => $fechaInicio,
            ':fin' => $fechaFin,
        ]);

        return (int) $this->bd->lastInsertId();
    }

    private function inscribir(int $torneoId, int $equipoId, string $nombreEquipo, string $estado = 'confirmado'): int {
        $stmt = $this->bd->prepare(
            "INSERT INTO participantes_torneo (torneo_id, tipo, referencia_id, nombre, estado)
             VALUES (:torneo, 'equipo', :equipo, :nombre, :estado)"
        );
        $stmt->execute([
            ':torneo' => $torneoId,
            ':equipo' => $equipoId,
            ':nombre' => $nombreEquipo,
            ':estado' => $estado,
        ]);

        return (int) $this->bd->lastInsertId();
    }

    private function registrarPosicion(int $torneoId, int $participanteId, float $puntos, float $favor, float $contra, int $jugados = 3): void {
        $stmt = $this->bd->prepare(
            "INSERT INTO torneo_posiciones (torneo_id, participante_id, partidos_jugados, partidos_ganados, partidos_empatados, partidos_perdidos, puntos_favor, puntos_contra, puntos)
             VALUES (:torneo, :participante, :jugados, 1, 0, 2, :favor, :contra, :puntos)"
        );
        $stmt->execute([
            ':torneo' => $torneoId,
            ':participante' => $participanteId,
            ':jugados' => $jugados,
            ':favor' => $favor,
            ':contra' => $contra,
            ':puntos' => $puntos,
        ]);
    }

    private function nombresDeTorneos(array $torneos): array {
        return array_column($torneos, 'torneo_nombre');
    }

    // ------------------------------------------------------------------
    // Pestaña Torneos
    // ------------------------------------------------------------------

    public function test_los_torneos_del_equipo_se_ordenan_del_mas_reciente_al_mas_viejo(): void {
        $jugadorId = $this->crearUsuario(3);
        $equipoId = $this->crearEquipo($jugadorId, 'Fire Wolves');

        $recienteId = $this->crearTorneo('Copa Marzo', '2026-03-01');
        $antiguoId = $this->crearTorneo('Copa Enero', '2026-01-01');
        $sinFechaId = $this->crearTorneo('Copa Sin Fecha', null);

        $this->inscribir($recienteId, $equipoId, 'Fire Wolves');
        $this->inscribir($antiguoId, $equipoId, 'Fire Wolves');
        $this->inscribir($sinFechaId, $equipoId, 'Fire Wolves');

        $torneos = $this->modeloEquipo->obtenerTorneosDelEquipo($equipoId);

        $this->assertSame(
            ['Copa Marzo', 'Copa Enero', 'Copa Sin Fecha'],
            $this->nombresDeTorneos($torneos)
        );
        $this->assertSame('liga', $torneos[0]['formato']);
        $this->assertSame('Fire Wolves', $torneos[0]['nombre_participacion']);
        $this->assertSame('confirmado', $torneos[0]['estado_inscripcion']);
    }

    public function test_la_pestana_torneos_no_mezcla_equipos_ni_roles_individuales(): void {
        $jugadorId = $this->crearUsuario(3);
        $equipoPropioId = $this->crearEquipo($jugadorId, 'Fire Wolves');
        $equipoAjenoId = $this->crearEquipo($jugadorId, 'Ice Bears');

        $torneoId = $this->crearTorneo('Copa Mixta', '2026-02-01');
        $propia = $this->inscribir($torneoId, $equipoPropioId, 'Fire Wolves');
        $this->inscribir($torneoId, $equipoAjenoId, 'Ice Bears', 'pendiente');

        //Una inscripcion individual no debe aparecer en el perfil del equipo
        $stmt = $this->bd->prepare(
            "INSERT INTO participantes_torneo (torneo_id, tipo, referencia_id, nombre, estado)
             VALUES (:torneo, 'usuario', :usuario, 'Jugador Suelto', 'confirmado')"
        );
        $stmt->execute([':torneo' => $torneoId, ':usuario' => $jugadorId]);

        $torneos = $this->modeloEquipo->obtenerTorneosDelEquipo($equipoPropioId);

        $this->assertCount(1, $torneos);
        $this->assertSame($propia, (int) $torneos[0]['participacion_id']);
        $this->assertSame('Fire Wolves', $torneos[0]['nombre_participacion']);
    }

    // ------------------------------------------------------------------
    // Pestaña Ranking
    // ------------------------------------------------------------------

    public function test_el_puesto_respeta_el_orden_puntos_diferencia_favor_y_nombre(): void {
        $jugadorId = $this->crearUsuario(3);
        $equipoId = $this->crearEquipo($jugadorId, 'Zeta Squad');
        $liderId = $this->crearEquipo($jugadorId, 'Alfa Squad');

        $torneoId = $this->crearTorneo('Copa Ranking', '2026-04-01');
        $propio = $this->inscribir($torneoId, $equipoId, 'Zeta Squad');
        $lider = $this->inscribir($torneoId, $liderId, 'Alfa Squad');

        //Mismos puntos, misma diferencia y mismos puntos a favor: decide el nombre
        $this->registrarPosicion($torneoId, $propio, 6, 5, 2);
        $this->registrarPosicion($torneoId, $lider, 6, 5, 2);

        $posiciones = $this->modeloEquipo->obtenerPosicionesDelEquipo($equipoId);

        $this->assertCount(1, $posiciones);
        $this->assertSame('Copa Ranking', $posiciones[0]['torneo_nombre']);
        $this->assertSame(2, (int) $posiciones[0]['posicion'], 'Alfa va primero por nombre.');
        $this->assertSame(3, (int) $posiciones[0]['diferencia_goles']);
        $this->assertSame(6.0, (float) $posiciones[0]['puntos']);
    }

    public function test_mas_puntos_siempre_va_adelante_aunque_la_diferencia_sea_menor(): void {
        $jugadorId = $this->crearUsuario(3);
        $equipoId = $this->crearEquipo($jugadorId, 'Fire Wolves');
        $rivalId = $this->crearEquipo($jugadorId, 'Ice Bears');

        $torneoId = $this->crearTorneo('Copa Puntos', '2026-04-01');
        $propio = $this->inscribir($torneoId, $equipoId, 'Fire Wolves');
        $rival = $this->inscribir($torneoId, $rivalId, 'Ice Bears');

        $this->registrarPosicion($torneoId, $propio, 9, 2, 1);
        $this->registrarPosicion($torneoId, $rival, 6, 20, 1);

        $posiciones = $this->modeloEquipo->obtenerPosicionesDelEquipo($equipoId);

        $this->assertSame(1, (int) $posiciones[0]['posicion']);
        $this->assertSame(9.0, (float) $posiciones[0]['puntos']);
        $this->assertSame(1, (int) $posiciones[0]['partidos_ganados']);
        $this->assertSame(2, (int) $posiciones[0]['partidos_perdidos']);
    }

    public function test_un_rival_confirmado_sin_fila_de_posiciones_cuenta_como_cero(): void {
        $jugadorId = $this->crearUsuario(3);
        $equipoId = $this->crearEquipo($jugadorId, 'Fire Wolves');
        $rivalId = $this->crearEquipo($jugadorId, 'Alfa Bears');

        $torneoId = $this->crearTorneo('Copa Incompleta', '2026-04-01');
        $propio = $this->inscribir($torneoId, $equipoId, 'Fire Wolves');
        $this->inscribir($torneoId, $rivalId, 'Alfa Bears');

        //El equipo propio todavia no jugo: 0 puntos. El rival tampoco tiene fila.
        $this->registrarPosicion($torneoId, $propio, 0, 0, 0, 0);

        $posiciones = $this->modeloEquipo->obtenerPosicionesDelEquipo($equipoId);

        $this->assertSame(2, (int) $posiciones[0]['posicion'], 'Alfa Bears queda primero por nombre con los mismos ceros.');
    }

    public function test_sin_fila_de_posiciones_el_equipo_no_aparece_en_ranking(): void {
        $jugadorId = $this->crearUsuario(3);
        $equipoId = $this->crearEquipo($jugadorId, 'Fire Wolves');

        $torneoId = $this->crearTorneo('Copa Recien Inscripta', '2026-05-01');
        $this->inscribir($torneoId, $equipoId, 'Fire Wolves');

        $this->assertCount(1, $this->modeloEquipo->obtenerTorneosDelEquipo($equipoId));
        $this->assertSame([], $this->modeloEquipo->obtenerPosicionesDelEquipo($equipoId));
    }

    public function test_una_inscripcion_pendiente_aparece_en_torneos_pero_no_en_ranking(): void {
        $jugadorId = $this->crearUsuario(3);
        $equipoId = $this->crearEquipo($jugadorId, 'Fire Wolves');

        $torneoId = $this->crearTorneo('Copa Pendiente', '2026-05-01');
        $participacionId = $this->inscribir($torneoId, $equipoId, 'Fire Wolves', 'pendiente');
        //Aunque exista una fila de posiciones, pendiente no compite todavia
        $this->registrarPosicion($torneoId, $participacionId, 3, 1, 0, 1);

        $torneos = $this->modeloEquipo->obtenerTorneosDelEquipo($equipoId);
        $this->assertCount(1, $torneos);
        $this->assertSame('pendiente', $torneos[0]['estado_inscripcion']);

        $this->assertSame([], $this->modeloEquipo->obtenerPosicionesDelEquipo($equipoId));
    }

    public function test_un_equipo_sin_participaciones_devuelve_ambas_listas_vacias(): void {
        $jugadorId = $this->crearUsuario(3);
        $equipoId = $this->crearEquipo($jugadorId, 'Equipo Nuevo');

        $this->assertSame([], $this->modeloEquipo->obtenerTorneosDelEquipo($equipoId));
        $this->assertSame([], $this->modeloEquipo->obtenerPosicionesDelEquipo($equipoId));
    }
}
