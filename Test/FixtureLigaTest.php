<?php
namespace Test;

use App\Modelos\Torneo;

require_once __DIR__ . '/CasoBase.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Torneo.php';

/**
 * Funcionalidad 5: generación del fixture de un torneo de liga.
 * Método: Torneo::generarFixture()
 *
 * Reglas comprobadas:
 *  - Todos contra todos: cada par se enfrenta una sola vez.
 *  - Con cantidad par, todos juegan en cada fecha.
 *  - Con cantidad impar, se agrega un descanso por fecha y nadie repite rival.
 *  - La cantidad de fechas es una menos que la cantidad de participantes
 *    (con impar, una más).
 */
class FixtureLigaTest extends CasoBase {

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
     * @return array<int, array{id:int, ronda:int, local:int, visitante:int}>
     */
    private function encuentros(int $torneoId): array {
        $stmt = $this->bd->prepare(
            'SELECT id, ronda, participante_local_id, participante_visitante_id
             FROM torneo_encuentros WHERE torneo_id = :id ORDER BY ronda, id'
        );
        $stmt->execute([':id' => $torneoId]);

        return array_map(static function (array $fila): array {
            return [
                'id' => (int) $fila['id'],
                'ronda' => (int) $fila['ronda'],
                'local' => (int) $fila['participante_local_id'],
                'visitante' => (int) $fila['participante_visitante_id'],
            ];
        }, $stmt->fetchAll());
    }

    /**
     * Devuelve los pares sin importar el orden local/visitante.
     *
     * @param array<int, array{local:int, visitante:int}> $encuentros
     * @return string[]
     */
    private function pares(array $encuentros): array {
        return array_map(static function (array $encuentro): string {
            $a = min($encuentro['local'], $encuentro['visitante']);
            $b = max($encuentro['local'], $encuentro['visitante']);
            return $a . '-' . $b;
        }, $encuentros);
    }

    public function test_liga_con_cuatro_participantes_genera_todos_los_cruces_sin_repetir(): void {
        $torneoId = $this->crearTorneo($this->organizadorId, $this->juegoId, 'liga');
        $participantes = $this->confirmarParticipantes($torneoId, 4, $this->organizadorId);

        $resultado = $this->modeloTorneo->generarFixture($torneoId, $this->organizadorId);

        $this->assertTrue($resultado['ok'], $resultado['mensaje'] ?? '');

        $encuentros = $this->encuentros($torneoId);
        $this->assertCount(6, $encuentros, 'Cuatro participantes generan seis partidos.');

        $pares = $this->pares($encuentros);
        $this->assertCount(6, array_unique($pares), 'Ningún cruce puede repetirse.');

        $rondas = array_unique(array_column($encuentros, 'ronda'));
        $this->assertCount(3, $rondas, 'Deben generarse tres fechas.');

        // Cada participante juega exactamente una vez por fecha.
        foreach ($rondas as $ronda) {
            $jugadores = [];
            foreach ($encuentros as $encuentro) {
                if ($encuentro['ronda'] === $ronda) {
                    $jugadores[] = $encuentro['local'];
                    $jugadores[] = $encuentro['visitante'];
                }
            }
            sort($jugadores);
            $esperados = $participantes;
            sort($esperados);
            $this->assertSame($esperados, $jugadores, 'En la fecha ' . $ronda . ' deben jugar los cuatro participantes.');
        }
    }

    public function test_liga_con_cinco_participantes_agrega_un_descanso_por_fecha(): void {
        $torneoId = $this->crearTorneo($this->organizadorId, $this->juegoId, 'liga');
        $participantes = $this->confirmarParticipantes($torneoId, 5, $this->organizadorId);

        $resultado = $this->modeloTorneo->generarFixture($torneoId, $this->organizadorId);

        $this->assertTrue($resultado['ok'], $resultado['mensaje'] ?? '');

        $encuentros = $this->encuentros($torneoId);
        $this->assertCount(10, $encuentros, 'Cinco participantes generan diez partidos.');

        $pares = $this->pares($encuentros);
        $this->assertCount(10, array_unique($pares), 'Ningún cruce puede repetirse.');

        $rondas = array_unique(array_column($encuentros, 'ronda'));
        $this->assertCount(5, $rondas, 'Con cantidad impar se juega una fecha más.');

        // Cada fecha tiene dos partidos (cuatro jugadores) y un descanso.
        $apariciones = array_fill_keys($participantes, 0);
        foreach ($rondas as $ronda) {
            $jugadores = [];
            foreach ($encuentros as $encuentro) {
                if ($encuentro['ronda'] === $ronda) {
                    $jugadores[] = $encuentro['local'];
                    $jugadores[] = $encuentro['visitante'];
                }
            }
            $this->assertCount(4, $jugadores, 'En cada fecha descansa exactamente un participante.');
            $this->assertCount(4, array_unique($jugadores), 'Un participante no puede jugar dos veces en la misma fecha.');

            foreach ($jugadores as $jugador) {
                $this->assertArrayHasKey($jugador, $apariciones, 'El jugador debe pertenecer al torneo.');
                $apariciones[$jugador]++;
            }
        }

        foreach ($apariciones as $participanteId => $cantidad) {
            $this->assertSame(4, $cantidad, 'El participante ' . $participanteId . ' debe jugar cuatro de las cinco fechas.');
        }
    }

    public function test_fixture_rechaza_un_torneo_con_un_solo_participante(): void {
        $torneoId = $this->crearTorneo($this->organizadorId, $this->juegoId, 'liga');
        $this->confirmarParticipantes($torneoId, 1, $this->organizadorId);

        $resultado = $this->modeloTorneo->generarFixture($torneoId, $this->organizadorId);

        $this->assertFalse($resultado['ok']);
        $this->assertStringContainsString('al menos dos participantes', $resultado['mensaje']);
        $this->assertCount(0, $this->encuentros($torneoId));
    }
}
