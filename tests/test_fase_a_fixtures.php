<?php
/**
 * Test Automatizado de Fase A: Integridad de Fixture y Carga de Marcadores
 */

require_once __DIR__ . '/../codigo_fuente/configuracion/constantes.php';
require_once __DIR__ . '/../codigo_fuente/ayudantes/Sesion.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Conexion.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Juego.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Torneo.php';

use App\Modelos\Conexion;
use App\Modelos\Torneo;

$bd = Conexion::getInstance()->getBD();
$modeloTorneo = new Torneo();

echo "====================================================================\n";
echo "   TEST DE FASE A: INTEGRIDAD DE FIXTURE Y RECTIFICACION DE MARCADOR \n";
echo "====================================================================\n\n";

$exitos = 0;
$total = 0;

function verificar(string $nombre, bool $condicion, string $detalle = '') {
    global $exitos, $total;
    $total++;
    if ($condicion) {
        $exitos++;
        echo " [PASS] $nombre\n";
        if ($detalle) echo "        -> $detalle\n";
    } else {
        echo " [FAIL] $nombre\n";
        if ($detalle) echo "        -> ERROR: $detalle\n";
    }
}

// Obtenemos disciplinas válidas para las pruebas según sus modalidades y formatos permitidos
$juegoLigaId = (int) $bd->query(
    "SELECT j.id FROM juegos j
     INNER JOIN juego_modalidades_permitidas jmp ON jmp.juego_id = j.id AND jmp.modalidad_id = 1
     INNER JOIN juego_formatos_permitidos jfp ON jfp.juego_id = j.id AND jfp.formato_codigo = 'liga'
     WHERE j.activo = 1 LIMIT 1"
)->fetchColumn();

$juegoElimId = (int) $bd->query(
    "SELECT j.id FROM juegos j
     INNER JOIN juego_modalidades_permitidas jmp ON jmp.juego_id = j.id AND jmp.modalidad_id = 1
     INNER JOIN juego_formatos_permitidos jfp ON jfp.juego_id = j.id AND jfp.formato_codigo = 'eliminacion_directa'
     WHERE j.activo = 1 LIMIT 1"
)->fetchColumn();

$organizadorId = 2;

$bd->beginTransaction();
try {
    // ----------------------------------------------------------------------
    // 1. TEST: Invalidación de Fixture al Quitar Participante
    // ----------------------------------------------------------------------
    echo "--- 1. Protección de Fixture ante Participantes Cancelados ---\n";

    // Creamos un torneo dummy en inscripciones_abiertas
    $bd->exec("INSERT INTO torneos (organizador_id, juego_id, modalidad_id, nombre, formato, sistema_puntuacion_id, estado, cupo_max_equipos)
               VALUES ($organizadorId, $juegoLigaId, 1, '__TEST_TORNEO_FASE_A__', 'liga', 1, 'inscripciones_abiertas', 4)");
    $tId = (int) $bd->lastInsertId();

    // Confirmamos 4 participantes
    $pIds = [];
    for ($i = 100; $i <= 103; $i++) {
        $bd->exec("INSERT INTO participantes_torneo (torneo_id, tipo, referencia_id, nombre, estado, confirmado_por, fecha_confirmacion)
                   VALUES ($tId, 'usuario', $i, 'Jugador $i', 'confirmado', $organizadorId, NOW())");
        $pIds[] = (int) $bd->lastInsertId();
    }

    // Generamos el fixture
    $resFixture = $modeloTorneo->generarFixture($tId, $organizadorId);
    $encuentrosPre = (int) $bd->query("SELECT COUNT(*) FROM torneo_encuentros WHERE torneo_id = $tId")->fetchColumn();

    verificar("1.1 Fixture inicial generado correctamente con 4 participantes",
        $resFixture['ok'] === true && $encuentrosPre > 0,
        "Total encuentros creados: $encuentrosPre"
    );

    // Quitamos al primer participante confirmado
    $partAEliminar = $pIds[0];
    $resQuitar = $modeloTorneo->quitarParticipante($partAEliminar, $organizadorId);
    $encuentrosPost = (int) $bd->query("SELECT COUNT(*) FROM torneo_encuentros WHERE torneo_id = $tId")->fetchColumn();
    $estadoPart = $bd->query("SELECT estado FROM participantes_torneo WHERE id = $partAEliminar")->fetchColumn();

    verificar("1.2 quitarParticipante cancela al participante e invalida el fixture obsoleto",
        $resQuitar['ok'] === true && $resQuitar['fixture_invalidado'] === true && $encuentrosPost === 0 && $estadoPart === 'cancelado',
        "Fixture residual: $encuentrosPost encuentros. Mensaje: {$resQuitar['mensaje']}"
    );

    // ----------------------------------------------------------------------
    // 2. TEST: Rectificación de Resultados en Liga
    // ----------------------------------------------------------------------
    echo "\n--- 2. Rectificación de Resultados en Formato Liga ---\n";

    // Volvemos a confirmar al participante para regenerar fixture limpio
    $bd->exec("UPDATE participantes_torneo SET estado = 'confirmado' WHERE id = $partAEliminar");
    $modeloTorneo->generarFixture($tId, $organizadorId);
    // Iniciamos el torneo
    $bd->exec("UPDATE torneos SET estado = 'en_curso' WHERE id = $tId");

    // Obtenemos el primer encuentro
    $encuentro = $bd->query("SELECT id, participante_local_id, participante_visitante_id FROM torneo_encuentros WHERE torneo_id = $tId LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $encId = (int) $encuentro['id'];
    $locId = (int) $encuentro['participante_local_id'];
    $visId = (int) $encuentro['participante_visitante_id'];

    // 2.1 Carga inicial de resultado: gana local 2 - 1
    $resGuardar = $modeloTorneo->guardarResultado($encId, $organizadorId, 2.0, 1.0);
    $ptsLocPre = (float) $bd->query("SELECT puntos FROM torneo_posiciones WHERE torneo_id = $tId AND participante_id = $locId")->fetchColumn();

    verificar("2.1 Resultado inicial cargado correctamente (Gana local 2-1)",
        $resGuardar['ok'] === true && $ptsLocPre > 0,
        "Puntos local tras victoria: $ptsLocPre"
    );

    // 2.2 Corrección de resultado: era un error tipográfico, en realidad empataron 1 - 1
    $resCorregir = $modeloTorneo->corregirResultado($encId, $organizadorId, 1.0, 1.0);
    $ptsLocPost = (float) $bd->query("SELECT puntos FROM torneo_posiciones WHERE torneo_id = $tId AND participante_id = $locId")->fetchColumn();
    $ganadorPost = $bd->query("SELECT participante_ganador_id FROM torneo_encuentros WHERE id = $encId")->fetchColumn();

    verificar("2.2 corregirResultado rectifica marcador y recalcula posiciones en tiempo real",
        $resCorregir['ok'] === true && $ganadorPost === null && $ptsLocPost < $ptsLocPre,
        "Puntos local tras corrección a empate: $ptsLocPost (previo: $ptsLocPre)"
    );

    // ----------------------------------------------------------------------
    // 3. TEST: Rectificación de Resultados en Eliminación Directa
    // ----------------------------------------------------------------------
    echo "\n--- 3. Rectificación de Resultados en Eliminación Directa ---\n";

    $bd->exec("INSERT INTO torneos (organizador_id, juego_id, modalidad_id, nombre, formato, sistema_puntuacion_id, estado, cupo_max_equipos)
               VALUES ($organizadorId, $juegoElimId, 1, '__TEST_TORNEO_ELIMINACION__', 'eliminacion_directa', 1, 'inscripciones_abiertas', 4)");
    $tElimId = (int) $bd->lastInsertId();

    $pElimIds = [];
    for ($i = 200; $i <= 203; $i++) {
        $bd->exec("INSERT INTO participantes_torneo (torneo_id, tipo, referencia_id, nombre, estado, confirmado_por, fecha_confirmacion)
                   VALUES ($tElimId, 'usuario', $i, 'Jugador $i', 'confirmado', $organizadorId, NOW())");
        $pElimIds[] = (int) $bd->lastInsertId();
    }

    $modeloTorneo->generarFixture($tElimId, $organizadorId);
    $bd->exec("UPDATE torneos SET estado = 'en_curso' WHERE id = $tElimId");

    $partidosRonda1 = $bd->query("SELECT id, participante_local_id, participante_visitante_id FROM torneo_encuentros WHERE torneo_id = $tElimId AND ronda = 1 ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

    // Finalizamos ambos partidos de ronda 1
    $p1 = $partidosRonda1[0];
    $p2 = $partidosRonda1[1];
    $modeloTorneo->guardarResultado((int)$p1['id'], $organizadorId, 3.0, 0.0); // Gana local de p1
    $modeloTorneo->guardarResultado((int)$p2['id'], $organizadorId, 2.0, 0.0); // Gana local de p2

    // Se debió generar ronda 2 (la final) con los dos ganadores
    $finalAntes = $bd->query("SELECT id, participante_local_id, participante_visitante_id FROM torneo_encuentros WHERE torneo_id = $tElimId AND ronda = 2")->fetch(PDO::FETCH_ASSOC);

    verificar("3.1 Ronda 2 (final) generada automáticamente tras finalizar semifinales",
        !empty($finalAntes) && (int)$finalAntes['participante_local_id'] === (int)$p1['participante_local_id'],
        "Participante en final: " . $finalAntes['participante_local_id']
    );

    // 3.2 Corregimos semifinal 1: en realidad ganó el visitante de p1
    $resCorrElim = $modeloTorneo->corregirResultado((int)$p1['id'], $organizadorId, 0.0, 3.0);
    $finalDespues = $bd->query("SELECT id, participante_local_id, participante_visitante_id FROM torneo_encuentros WHERE torneo_id = $tElimId AND ronda = 2")->fetch(PDO::FETCH_ASSOC);

    verificar("3.2 Al cambiar el ganador de semifinal (no jugada la final), se propaga el nuevo ganador a la final",
        $resCorrElim['ok'] === true && (int)$finalDespues['participante_local_id'] === (int)$p1['participante_visitante_id'],
        "Nuevo participante en final: " . $finalDespues['participante_local_id'] . " (esperado: " . $p1['participante_visitante_id'] . ")"
    );

    // 3.3 Si la final ya fue jugada, bloquear cualquier intento de cambiar la semifinal anterior
    $modeloTorneo->guardarResultado((int)$finalDespues['id'], $organizadorId, 2.0, 1.0); // Finalizamos la final
    $resIntentoIlegal = $modeloTorneo->corregirResultado((int)$p1['id'], $organizadorId, 4.0, 0.0);

    verificar("3.3 Se bloquea la corrección de semifinal si la final ya fue disputada",
        $resIntentoIlegal['ok'] === false,
        "Mensaje de bloqueo: " . $resIntentoIlegal['mensaje']
    );

    $bd->rollBack();
} catch (\Throwable $e) {
    $bd->rollBack();
    echo "ERROR durante las pruebas: " . $e->getMessage() . "\n";
}

echo "\n====================================================================\n";
echo " RESUMEN FASE A: $exitos / $total PRUEBAS EXITOSAS (" . round(($exitos / $total) * 100) . "%)\n";
echo "====================================================================\n";
