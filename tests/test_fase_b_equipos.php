<?php
/**
 * Test Automatizado de Fase B: Blindaje de Equipos y Plantillas en Competencia
 */

require_once __DIR__ . '/../codigo_fuente/configuracion/constantes.php';
require_once __DIR__ . '/../codigo_fuente/ayudantes/Sesion.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Conexion.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Equipo.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Juego.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Torneo.php';

use App\Modelos\Conexion;
use App\Modelos\Equipo;
use App\Modelos\Torneo;

$bd = Conexion::getInstance()->getBD();
$modeloEquipo = Equipo::obtenerInstancia();
$modeloTorneo = new Torneo();

echo "====================================================================\n";
echo "   TEST DE FASE B: BLINDAJE DE EQUIPOS Y ROSTER EN COMPETENCIA      \n";
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

// 1. Obtenemos disciplinas válidas colectivas e individuales
$juegoColectivo1 = (int) $bd->query(
    "SELECT j.id FROM juegos j
     INNER JOIN juego_modalidades_permitidas jmp ON jmp.juego_id = j.id AND jmp.modalidad_id = 2
     WHERE j.activo = 1 ORDER BY j.id ASC LIMIT 1"
)->fetchColumn();

$juegoColectivo2 = (int) $bd->query(
    "SELECT j.id FROM juegos j
     INNER JOIN juego_modalidades_permitidas jmp ON jmp.juego_id = j.id AND jmp.modalidad_id = 2
     WHERE j.activo = 1 AND j.id != $juegoColectivo1 ORDER BY j.id ASC LIMIT 1"
)->fetchColumn();

$juegoIndividual = (int) $bd->query(
    "SELECT j.id FROM juegos j
     WHERE j.activo = 1 AND j.id NOT IN (
         SELECT juego_id FROM juego_modalidades_permitidas WHERE modalidad_id = 2
     ) LIMIT 1"
)->fetchColumn();

$bd->beginTransaction();
try {
    echo "--- 1. Preparación de Equipo y Torneo de Competencia ---\n";

    // Creamos 3 usuarios dummy de prueba
    $capitanId = 301;
    $subCapitanId = 302;
    $miembroId = 303;
    $organizadorId = 2;

    foreach ([$capitanId, $subCapitanId, $miembroId] as $uId) {
        $bd->exec("INSERT INTO usuarios (id, nombre_completo, email, contrasena_hash, rol_id, esta_activo)
                   VALUES ($uId, 'Jugador Test $uId', 'test$uId@sgdm.com', 'dummyhash', 3, 1)
                   ON DUPLICATE KEY UPDATE esta_activo = 1");
    }

    // Creamos equipo dummy
    $resCrear = $modeloEquipo->crearEquipoCompleto('__EQUIPO_FASE_B__', $capitanId, 'Equipo de test Fase B', 'Buenos Aires', 2024, $juegoColectivo1);
    $equipoId = (int) $resCrear['equipoId'];

    // Sumamos al subcapitán y al miembro
    $bd->exec("INSERT INTO equipo_miembros (equipo_id, usuario_id, es_activo) VALUES ($equipoId, $subCapitanId, 1)");
    $bd->exec("INSERT INTO equipo_miembros (equipo_id, usuario_id, es_activo) VALUES ($equipoId, $miembroId, 1)");
    // Nombramos al subcapitán como capitán secundario
    $modeloEquipo->hacerCapitan($equipoId, $subCapitanId, $capitanId);

    verificar("1.1 Equipo creado con 3 miembros y 2 capitanes",
        $resCrear['exito'] === true && $equipoId > 0,
        "Equipo ID: $equipoId, Disciplina ID: $juegoColectivo1"
    );

    // Creamos torneo colectivo en curso
    $bd->exec("INSERT INTO torneos (organizador_id, juego_id, modalidad_id, nombre, formato, sistema_puntuacion_id, estado, cupo_max_equipos)
               VALUES ($organizadorId, $juegoColectivo1, 2, '__TORNEO_FASE_B__', 'liga', 1, 'en_curso', 4)");
    $torneoId = (int) $bd->lastInsertId();

    // Inscribimos y confirmamos al equipo en el torneo
    $bd->exec("INSERT INTO participantes_torneo (torneo_id, tipo, referencia_id, nombre, estado, confirmado_por, fecha_confirmacion)
               VALUES ($torneoId, 'equipo', $equipoId, '__EQUIPO_FASE_B__', 'confirmado', $organizadorId, NOW())");

    verificar("1.2 Equipo confirmado en torneo activo 'en_curso'",
        $modeloEquipo->tieneTorneosEnCurso($equipoId) === true,
        "Torneo ID: $torneoId (en_curso)"
    );

    // ----------------------------------------------------------------------
    // 2. TEST: Bloqueo de Roster en Competencia Activa
    // ----------------------------------------------------------------------
    echo "\n--- 2. Bloqueo de Alteración de Roster en Torneos en Curso ---\n";

    // 2.1 Intentar expulsar a un miembro mientras el equipo está en_curso
    $resExpulsarBloqueado = $modeloEquipo->expulsarMiembro($equipoId, $miembroId);
    $miembroActivo = (int) $bd->query("SELECT es_activo FROM equipo_miembros WHERE equipo_id = $equipoId AND usuario_id = $miembroId")->fetchColumn();

    verificar("2.1 expulsarMiembro bloqueado mientras el torneo está en curso",
        $resExpulsarBloqueado === false && $miembroActivo === 1,
        "Retorno: false, es_activo sigue en 1"
    );

    // 2.2 Intentar dejar el equipo siendo capitán mientras el equipo está en_curso
    $resDejarBloqueado = $modeloEquipo->dejarEquipo($equipoId, $capitanId);
    $capitanActivo = (int) $bd->query("SELECT es_activo FROM equipo_miembros WHERE equipo_id = $equipoId AND usuario_id = $capitanId")->fetchColumn();

    verificar("2.2 dejarEquipo bloqueado mientras el torneo está en curso",
        $resDejarBloqueado['exito'] === false && $capitanActivo === 1,
        "Mensaje: {$resDejarBloqueado['error']}"
    );

    // ----------------------------------------------------------------------
    // 3. TEST: Bloqueo de Cambio de Disciplina y Eliminación del Equipo
    // ----------------------------------------------------------------------
    echo "\n--- 3. Bloqueo de Cambio de Disciplina y Disolución en Torneos Activos ---\n";

    // 3.1 Intentar cambiar disciplina mientras tiene torneos activos
    $resCambioBloqueado = $modeloEquipo->cambiarDisciplina($equipoId, $juegoColectivo2);
    $disciplinaActual = (int) $bd->query("SELECT juego_id FROM equipos WHERE id = $equipoId")->fetchColumn();

    verificar("3.1 cambiarDisciplina bloqueado mientras el equipo compite en torneos activos",
        $resCambioBloqueado['exito'] === false && $disciplinaActual === $juegoColectivo1,
        "Mensaje: {$resCambioBloqueado['error']}"
    );

    // 3.2 Intentar eliminar el equipo mientras tiene torneos activos
    $resEliminarBloqueado = $modeloEquipo->eliminarEquipo($equipoId);
    $equipoSigueActivo = (int) $bd->query("SELECT activo FROM equipos WHERE id = $equipoId")->fetchColumn();

    verificar("3.2 eliminarEquipo bloqueado mientras el equipo compite en torneos activos",
        $resEliminarBloqueado === false && $equipoSigueActivo === 1,
        "Retorno: false, el equipo permanece con activo = 1"
    );

    // ----------------------------------------------------------------------
    // 4. TEST: Habilitación Tras Finalización del Torneo
    // ----------------------------------------------------------------------
    echo "\n--- 4. Desbloqueo Operativo Tras Finalización de la Competencia ---\n";

    // Finalizamos el torneo
    $bd->exec("UPDATE torneos SET estado = 'finalizado' WHERE id = $torneoId");

    verificar("4.1 Torneo transicionado a 'finalizado' (sin torneos en curso)",
        $modeloEquipo->tieneTorneosEnCurso($equipoId) === false,
        "tieneTorneosEnCurso retorno: false"
    );

    // 4.2 Ahora expulsar al miembro debe permitirse
    $resExpulsarPermitido = $modeloEquipo->expulsarMiembro($equipoId, $miembroId);
    $miembroDesactivado = (int) $bd->query("SELECT es_activo FROM equipo_miembros WHERE equipo_id = $equipoId AND usuario_id = $miembroId")->fetchColumn();

    verificar("4.2 expulsarMiembro habilitado tras finalizar la competencia",
        $resExpulsarPermitido === true && $miembroDesactivado === 0,
        "Retorno: true, miembro pasa a es_activo = 0"
    );

    // 4.3 Intentar cambiar disciplina a una disciplina individual incompatible
    $resCambioIndividual = $modeloEquipo->cambiarDisciplina($equipoId, $juegoIndividual);

    verificar("4.3 cambiarDisciplina rechaza disciplinas individuales (no colectivas)",
        $resCambioIndividual['exito'] === false,
        "Mensaje: {$resCambioIndividual['error']}"
    );

    // 4.4 Cambiar disciplina a una colectiva válida (debe permitirlo ahora que el torneo finalizó)
    $resCambioPermitido = $modeloEquipo->cambiarDisciplina($equipoId, $juegoColectivo2);
    $nuevaDisciplina = (int) $bd->query("SELECT juego_id FROM equipos WHERE id = $equipoId")->fetchColumn();

    verificar("4.4 cambiarDisciplina permite cambio a otra disciplina colectiva permitida",
        $resCambioPermitido['exito'] === true && $nuevaDisciplina === $juegoColectivo2,
        "Nueva disciplina: $nuevaDisciplina (esperada: $juegoColectivo2)"
    );

    // 4.5 Dejar el equipo ahora se permite (habiendo otro capitán)
    $resDejarPermitido = $modeloEquipo->dejarEquipo($equipoId, $capitanId);
    $nuevoLider = (int) $bd->query("SELECT usuario_id FROM equipo_capitanes WHERE equipo_id = $equipoId AND es_capitan_principal = 1")->fetchColumn();
    $capitanDesactivado = (int) $bd->query("SELECT es_activo FROM equipo_miembros WHERE equipo_id = $equipoId AND usuario_id = $capitanId")->fetchColumn();

    verificar("4.5 dejarEquipo permitido tras finalización del torneo, transfiriendo capitanía principal",
        $resDejarPermitido['exito'] === true && $nuevoLider === $subCapitanId && $capitanDesactivado === 0,
        "Nuevo capitán principal: $nuevoLider, ex-capitán es_activo: $capitanDesactivado"
    );

    // 4.6 Eliminar equipo ahora permitido
    $resEliminarPermitido = $modeloEquipo->eliminarEquipo($equipoId);
    $equipoDesactivado = (int) $bd->query("SELECT activo FROM equipos WHERE id = $equipoId")->fetchColumn();

    verificar("4.6 eliminarEquipo permitido al no tener torneos vigentes",
        $resEliminarPermitido === true && $equipoDesactivado === 0,
        "Retorno: true, equipo pasa a activo = 0"
    );

    $bd->rollBack();
} catch (\Throwable $e) {
    $bd->rollBack();
    echo "ERROR durante las pruebas de Fase B: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}

echo "\n====================================================================\n";
echo " RESUMEN FASE B: $exitos / $total PRUEBAS EXITOSAS (" . round(($exitos / $total) * 100) . "%)\n";
echo "====================================================================\n";
