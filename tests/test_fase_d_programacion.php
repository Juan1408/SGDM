<?php
/**
 * Test Automatizado de Fase D: Detección y Advertencia de Conflictos de Horario y Canchas
 */

require_once __DIR__ . '/../codigo_fuente/configuracion/constantes.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Conexion.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Torneo.php';

use App\Modelos\Conexion;
use App\Modelos\Torneo;

$bd = Conexion::getInstance()->getBD();
$modeloTorneo = new Torneo();

echo "====================================================================\n";
echo "   TEST DE FASE D: CONFLICTOS DE HORARIO Y CANCHAS EN PROGRAMACION  \n";
echo "====================================================================\n\n";

$exitos = 0;
$total = 0;

function verificar(string $nombre, bool $condicion, string $detalle = '') {
    global $exitos, $total;
    $total++;
    if ($condicion) {
        $exitos++;
        echo " [PASS] $nombre\n";
        if ($detalle) {
            echo "        -> $detalle\n";
        }
    } else {
        echo " [FAIL] $nombre\n";
        if ($detalle) {
            echo "        -> DETALLE: $detalle\n";
        }
    }
}

try {
    $organizadorId = 2;

    // Seleccionamos una disciplina que permita modalidad individual (1) y formato liga
    $juegoId = (int) $bd->query("
        SELECT j.id 
        FROM juegos j
        INNER JOIN juego_modalidades_permitidas jmp ON jmp.juego_id = j.id AND jmp.modalidad_id = 1
        INNER JOIN juego_formatos_permitidos jfp ON jfp.juego_id = j.id AND jfp.formato_codigo = 'liga'
        WHERE j.activo = 1 
        LIMIT 1
    ")->fetchColumn();
    if (!$juegoId) {
        $juegoId = (int) $bd->query("SELECT id FROM juegos WHERE activo = 1 LIMIT 1")->fetchColumn();
    }

    // 1. Crear torneo dummy para pruebas de programación
    $bd->exec("INSERT INTO torneos (organizador_id, juego_id, modalidad_id, nombre, formato, sistema_puntuacion_id, estado, cupo_max_equipos, fecha_inicio, fecha_fin, ubicacion)
               VALUES ($organizadorId, $juegoId, 1, '__TORNEO_FASE_D_CONFLICTOS__', 'liga', 1, 'en_curso', 8, '2026-11-01', '2026-11-30', 'Polideportivo Central')");
    $tId = (int) $bd->lastInsertId();

    // 2. Crear participantes de prueba
    $bd->exec("INSERT INTO participantes_torneo (torneo_id, tipo, referencia_id, nombre, estado)
               VALUES ($tId, 'usuario', 501, 'Jugador Alpha', 'confirmado'),
                      ($tId, 'usuario', 502, 'Jugador Beta', 'confirmado'),
                      ($tId, 'usuario', 503, 'Jugador Gamma', 'confirmado'),
                      ($tId, 'usuario', 504, 'Jugador Delta', 'confirmado')");

    $pAlpha = (int) $bd->query("SELECT id FROM participantes_torneo WHERE torneo_id = $tId AND referencia_id = 501")->fetchColumn();
    $pBeta  = (int) $bd->query("SELECT id FROM participantes_torneo WHERE torneo_id = $tId AND referencia_id = 502")->fetchColumn();
    $pGamma = (int) $bd->query("SELECT id FROM participantes_torneo WHERE torneo_id = $tId AND referencia_id = 503")->fetchColumn();
    $pDelta = (int) $bd->query("SELECT id FROM participantes_torneo WHERE torneo_id = $tId AND referencia_id = 504")->fetchColumn();

    // 3. Crear encuentros de prueba
    // Encuentro 1: Alpha vs Beta (Ronda 1)
    $bd->exec("INSERT INTO torneo_encuentros (torneo_id, ronda, participante_local_id, participante_visitante_id, estado)
               VALUES ($tId, 1, $pAlpha, $pBeta, 'programado')");
    $encuentro1 = (int) $bd->lastInsertId();

    // Encuentro 2: Gamma vs Delta (Ronda 1)
    $bd->exec("INSERT INTO torneo_encuentros (torneo_id, ronda, participante_local_id, participante_visitante_id, estado)
               VALUES ($tId, 1, $pGamma, $pDelta, 'programado')");
    $encuentro2 = (int) $bd->lastInsertId();

    // Encuentro 3: Alpha vs Gamma (Ronda 2)
    $bd->exec("INSERT INTO torneo_encuentros (torneo_id, ronda, participante_local_id, participante_visitante_id, estado)
               VALUES ($tId, 2, $pAlpha, $pGamma, 'programado')");
    $encuentro3 = (int) $bd->lastInsertId();

    echo "--- 1. Programación Inicial sin Conflictos ---\n";
    $res1 = $modeloTorneo->programarEncuentro($encuentro1, $organizadorId, '2026-11-15 15:00:00', 'Cancha Principal');
    verificar("1.1 Programar encuentro 1 sin conflictos",
        $res1['ok'] === true && $res1['mensaje'] === 'Partido programado correctamente.',
        "Retorno: " . json_encode($res1)
    );

    echo "\n--- 2. Detección y Bloqueo de Conflictos de Cancha/Sede ---\n";
    // 2.1 Intentar programar encuentro 2 en la misma cancha y mismo horario
    $resCanchaMismoHorario = $modeloTorneo->programarEncuentro($encuentro2, $organizadorId, '2026-11-15 15:00:00', 'Cancha Principal');
    verificar("2.1 Bloqueo por cancha ocupada en el mismo horario exacto",
        $resCanchaMismoHorario['ok'] === false && strpos($resCanchaMismoHorario['mensaje'], 'Conflicto de sede') !== false,
        "Mensaje: " . ($resCanchaMismoHorario['mensaje'] ?? '')
    );

    // 2.2 Intentar con variación de mayúsculas/minúsculas y espacios en la cancha
    $resCanchaCase = $modeloTorneo->programarEncuentro($encuentro2, $organizadorId, '2026-11-15 15:00:00', '  cancha principal  ');
    verificar("2.2 Bloqueo por cancha ocupada normalizando mayúsculas y espacios",
        $resCanchaCase['ok'] === false && strpos($resCanchaCase['mensaje'], 'Conflicto de sede') !== false,
        "Mensaje: " . ($resCanchaCase['mensaje'] ?? '')
    );

    // 2.3 Intentar con solapamiento menor a 30 minutos (ej. 15:15)
    $resCanchaSolape = $modeloTorneo->programarEncuentro($encuentro2, $organizadorId, '2026-11-15 15:15:00', 'Cancha Principal');
    verificar("2.3 Bloqueo por solapamiento temporal (< 30 min) en la misma cancha",
        $resCanchaSolape['ok'] === false && strpos($resCanchaSolape['mensaje'], 'Conflicto de sede') !== false,
        "Mensaje: " . ($resCanchaSolape['mensaje'] ?? '')
    );

    echo "\n--- 3. Detección y Bloqueo de Conflictos de Participantes (Superposición) ---\n";
    // 3.1 Intentar programar encuentro 3 (Alpha vs Gamma) en cancha distinta ('Cancha 2') pero a la misma hora en que juega Alpha
    $resPartSuperpuesto = $modeloTorneo->programarEncuentro($encuentro3, $organizadorId, '2026-11-15 15:00:00', 'Cancha 2');
    verificar("3.1 Bloqueo por participante ya comprometido a la misma hora en otro partido",
        $resPartSuperpuesto['ok'] === false && strpos($resPartSuperpuesto['mensaje'], 'Conflicto de horario') !== false && strpos($resPartSuperpuesto['mensaje'], 'Jugador Alpha') !== false,
        "Mensaje: " . ($resPartSuperpuesto['mensaje'] ?? '')
    );

    // 3.2 Intentar solapamiento menor a 30 min del participante
    $resPartSolape = $modeloTorneo->programarEncuentro($encuentro3, $organizadorId, '2026-11-15 15:20:00', 'Cancha 2');
    verificar("3.2 Bloqueo por participante con solapamiento temporal (< 30 min)",
        $resPartSolape['ok'] === false && strpos($resPartSolape['mensaje'], 'Conflicto de horario') !== false,
        "Mensaje: " . ($resPartSolape['mensaje'] ?? '')
    );

    echo "\n--- 4. Reprogramación sin Falsos Positivos y Horarios Válidos ---\n";
    // 4.1 Reprogramar el propio encuentro 1 sobre su misma cancha y horario (no debe colisionar consigo mismo)
    $resAuto = $modeloTorneo->programarEncuentro($encuentro1, $organizadorId, '2026-11-15 15:00:00', 'Cancha Principal');
    verificar("4.1 Reprogramar el mismo encuentro no genera falsos positivos contra sí mismo",
        $resAuto['ok'] === true,
        "Mensaje: " . ($resAuto['mensaje'] ?? '')
    );

    // 4.2 Programar encuentro 2 en horario posterior libre (16:00:00) en la misma Cancha Principal
    $resLibre = $modeloTorneo->programarEncuentro($encuentro2, $organizadorId, '2026-11-15 16:00:00', 'Cancha Principal');
    verificar("4.2 Programación válida en horario separado (>= 30 min)",
        $resLibre['ok'] === true && $resLibre['mensaje'] === 'Partido programado correctamente.',
        "Mensaje: " . ($resLibre['mensaje'] ?? '')
    );

    // Limpieza del torneo de prueba usando eliminación en cascada de Fase C
    $modeloTorneo->eliminarPorAdmin($tId);

} catch (\Throwable $e) {
    echo "ERROR durante las pruebas de Fase D: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    if (isset($tId) && $tId > 0) {
        $modeloTorneo->eliminarPorAdmin($tId);
    }
}

echo "\n====================================================================\n";
echo " RESUMEN FASE D: $exitos / $total PRUEBAS EXITOSAS (" . ($total > 0 ? round(($exitos / $total) * 100) : 0) . "%)\n";
echo "====================================================================\n\n";

if ($exitos === $total && $total > 0) {
    exit(0);
}
exit(1);
