<?php
namespace Test;

use App\Modelos\Torneo;

require_once __DIR__ . '/CasoBase.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Torneo.php';

/**
 * Funcionalidad 6: registro de un resultado de liga y actualización de posiciones.
 * Método: Torneo::guardarResultado()
 *
 * Reglas comprobadas:
 *  - Una victoria suma 3 puntos al ganador y 0 al perdedor.
 *  - Un empate suma 1 punto a cada uno y no define ganador.
 *  - Un marcador negativo se rechaza y el partido sigue pendiente.
 *  - La tabla refleja partidos jugados, goles a favor, en contra y diferencia.
 */
class ResultadoLigaTest extends CasoBase {

    private Torneo $modeloTorneo;
    private int $organizadorId;
    private int $juegoId;

    protected function setUp(): void {
        parent::setUp();
        $this->modeloTorneo = new Torneo();
        $this->organizadorId = $this->crearUsuario(2, 'Organizador QA');
        $this->juegoId = $this->crearJuego();
    }

    /**
     * Deja un torneo de liga con cuatro participantes y fixture generado,
     * listo para cargar el primer resultado.
     *
     * @return array{torneo:int, participantes:int[], encuentro:int, local:int, visitante:int}
     */
    private function prepararTorneoEnCurso(): array {
        $torneoId = $this->crearTorneo($this->organizadorId, $this->juegoId, 'liga');
        $participantes = $this->confirmarParticipantes($torneoId, 4, $this->organizadorId);

        $fixture = $this->modeloTorneo->generarFixture($torneoId, $this->organizadorId);
        $this->assertTrue($fixture['ok'], $fixture['mensaje'] ?? '');

        $this->bd->prepare("UPDATE torneos SET estado = 'en_curso' WHERE id = :id")
            ->execute([':id' => $torneoId]);

        $stmt = $this->bd->prepare(
            "SELECT id, participante_local_id, participante_visitante_id
             FROM torneo_encuentros WHERE torneo_id = :id ORDER BY id LIMIT 1"
        );
        $stmt->execute([':id' => $torneoId]);
        $encuentro = $stmt->fetch();

        return [
            'torneo' => $torneoId,
            'participantes' => $participantes,
            'encuentro' => (int) $encuentro['id'],
            'local' => (int) $encuentro['participante_local_id'],
            'visitante' => (int) $encuentro['participante_visitante_id'],
        ];
    }

    private function posicion(int $torneoId, int $participanteId): array {
        $stmt = $this->bd->prepare(
            'SELECT * FROM torneo_posiciones WHERE torneo_id = :torneo AND participante_id = :participante'
        );
        $stmt->execute([':torneo' => $torneoId, ':participante' => $participanteId]);

        return $stmt->fetch();
    }

    private function estadoEncuentro(int $encuentroId): array {
        $stmt = $this->bd->prepare('SELECT * FROM torneo_encuentros WHERE id = :id');
        $stmt->execute([':id' => $encuentroId]);

        return $stmt->fetch();
    }

    public function test_victoria_suma_tres_puntos_y_actualiza_la_diferencia(): void {
        $datos = $this->prepararTorneoEnCurso();

        $resultado = $this->modeloTorneo->guardarResultado(
            $datos['encuentro'],
            $this->organizadorId,
            3,
            1
        );

        $this->assertTrue($resultado['ok'], $resultado['mensaje'] ?? '');

        $encuentro = $this->estadoEncuentro($datos['encuentro']);
        $this->assertSame('finalizado', $encuentro['estado']);
        $this->assertSame($datos['local'], (int) $encuentro['participante_ganador_id']);

        $ganador = $this->posicion($datos['torneo'], $datos['local']);
        $this->assertSame(1, (int) $ganador['partidos_jugados']);
        $this->assertSame(1, (int) $ganador['partidos_ganados']);
        $this->assertSame(3, (int) $ganador['puntos']);
        $this->assertSame(3.0, (float) $ganador['puntos_favor']);
        $this->assertSame(1.0, (float) $ganador['puntos_contra']);
        $this->assertSame(2.0, (float) $ganador['diferencia_goles']);

        $perdedor = $this->posicion($datos['torneo'], $datos['visitante']);
        $this->assertSame(1, (int) $perdedor['partidos_perdidos']);
        $this->assertSame(0, (int) $perdedor['puntos']);
        $this->assertSame(-2.0, (float) $perdedor['diferencia_goles']);
    }

    public function test_empate_reparte_un_punto_y_no_define_ganador(): void {
        $datos = $this->prepararTorneoEnCurso();

        $resultado = $this->modeloTorneo->guardarResultado(
            $datos['encuentro'],
            $this->organizadorId,
            2,
            2
        );

        $this->assertTrue($resultado['ok'], $resultado['mensaje'] ?? '');

        $encuentro = $this->estadoEncuentro($datos['encuentro']);
        $this->assertSame('finalizado', $encuentro['estado']);
        $this->assertNull($encuentro['participante_ganador_id'], 'Un empate no tiene ganador.');

        $local = $this->posicion($datos['torneo'], $datos['local']);
        $visitante = $this->posicion($datos['torneo'], $datos['visitante']);

        $this->assertSame(1, (int) $local['puntos']);
        $this->assertSame(1, (int) $visitante['puntos']);
        $this->assertSame(1, (int) $local['partidos_empatados']);
        $this->assertSame(1, (int) $visitante['partidos_empatados']);
        $this->assertSame(0.0, (float) $local['diferencia_goles']);
    }

    public function test_marcador_negativo_se_rechaza_y_el_partido_sigue_pendiente(): void {
        $datos = $this->prepararTorneoEnCurso();

        $resultado = $this->modeloTorneo->guardarResultado(
            $datos['encuentro'],
            $this->organizadorId,
            -1,
            0
        );

        $this->assertFalse($resultado['ok']);
        $this->assertStringContainsString('no es válido', $resultado['mensaje']);

        $encuentro = $this->estadoEncuentro($datos['encuentro']);
        $this->assertSame('programado', $encuentro['estado']);
        $this->assertNull($encuentro['resultado_local']);

        $stmt = $this->bd->prepare('SELECT COUNT(*) FROM torneo_posiciones WHERE torneo_id = :id');
        $stmt->execute([':id' => $datos['torneo']]);
        $this->assertSame(0, (int) $stmt->fetchColumn(), 'No deben recalcularse posiciones.');
    }
}
