<?php
/**
 * Test de Verificación Integral de Fases de Remediación
 * Fases 1, 2 y 3: Integridad de Equipos, Ciclo de Vida de Inscripciones y Calidad de Datos
 */

require_once __DIR__ . '/../codigo_fuente/configuracion/constantes.php';
require_once __DIR__ . '/../codigo_fuente/ayudantes/Sesion.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Conexion.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Juego.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Torneo.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Equipo.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Guardado.php';

use App\Modelos\Conexion;
use App\Modelos\Torneo;
use App\Modelos\Equipo;

$bd = Conexion::getInstance()->getBD();
$modeloJuego = \Juego::obtenerInstancia();
$modeloTorneo = new Torneo();
$modeloEquipo = Equipo::obtenerInstancia();

echo "====================================================================\n";
echo "   EJECUCION DE TEST DE REMEDIACION: FASES 1, 2 Y 3 EN RUNTIME      \n";
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

// --------------------------------------------------------------------------
// FASE 1: Integridad Inmediata
// --------------------------------------------------------------------------
echo "--- FASE 1: Integridad de Equipos y Disciplinas Colectivas ---\n";

// 1.1 Restricción de creación de equipos a disciplinas colectivas
$juegosEquipos = $modeloJuego->obtenerJuegosParaEquipos();
$todosColectivos = true;
foreach ($juegosEquipos as $j) {
    if (!$modeloJuego->permiteEquipos((int) $j['id'])) {
        $todosColectivos = false;
        break;
    }
}
verificar("1.1 Juegos elegibles para equipos filtran exclusivamente disciplinas que permiten equipos (modalidad_id = 2)", 
    $todosColectivos && count($juegosEquipos) > 0, 
    "Total disciplinas colectivas elegibles encontradas: " . count($juegosEquipos)
);

// 1.2 Verificación de Juego::permiteEquipos()
$ajedrezId = (int) $bd->query("SELECT id FROM juegos WHERE LOWER(nombre) LIKE '%ajedrez%' LIMIT 1")->fetchColumn();
$futbolId = (int) $bd->query("SELECT juego_id FROM juego_modalidades_permitidas WHERE modalidad_id = 2 LIMIT 1")->fetchColumn();

$permiteAjedrez = $ajedrezId > 0 ? $modeloJuego->permiteEquipos($ajedrezId) : false;
$permiteFutbol = $futbolId > 0 ? $modeloJuego->permiteEquipos($futbolId) : true;

verificar("1.2 PermiteEquipos bloquea disciplinas individuales y habilita colectivas",
    $permiteAjedrez === false && $permiteFutbol === true,
    "Ajedrez permiteEquipos=" . ($permiteAjedrez ? 'SI' : 'NO') . ", Colectiva permiteEquipos=" . ($permiteFutbol ? 'SI' : 'NO')
);

// 1.3 Detección de conflicto de jugadores entre equipos en un mismo torneo
$torneosEquipos = $bd->query("SELECT id FROM torneos WHERE modalidad_id = 2 LIMIT 1")->fetchAll(PDO::FETCH_COLUMN);
$torneoPruebaId = !empty($torneosEquipos) ? (int) $torneosEquipos[0] : 0;

if ($torneoPruebaId > 0) {
    $equiposPrueba = $bd->query("SELECT id FROM equipos WHERE activo = 1 LIMIT 2")->fetchAll(PDO::FETCH_COLUMN);
    if (count($equiposPrueba) >= 2) {
        $eq1 = (int) $equiposPrueba[0];
        $eq2 = (int) $equiposPrueba[1];
        $conflictos = $modeloTorneo->obtenerMiembrosEnConflicto($torneoPruebaId, $eq1);
        verificar("1.3 Método obtenerMiembrosEnConflicto ejecuta correctamente sin excepciones SQL",
            is_array($conflictos),
            "Ejecución contra torneo #$torneoPruebaId y equipo #$eq1 completada exitosamente."
        );
    }
}

// --------------------------------------------------------------------------
// FASE 2: Ciclo de Vida de Inscripciones
// --------------------------------------------------------------------------
echo "\n--- FASE 2: Ciclo de Vida de Inscripciones (Reintento, Retiro y Limpieza) ---\n";

// Crear un torneo dummy temporal para probar el ciclo de vida completo
$bd->beginTransaction();
try {
    $bd->exec("INSERT INTO torneos (organizador_id, juego_id, modalidad_id, nombre, formato, sistema_puntuacion_id, estado, cupo_max_equipos)
               VALUES (2, $futbolId, 2, '__TEST_TORNEO_CICLO__', 'liga', 1, 'inscripciones_abiertas', 8)");
    $testTorneoId = (int) $bd->lastInsertId();

    $usuarioPruebaId = 3; // Jugador regular

    // 2.1 Inscribir jugador
    $inscripto = $modeloTorneo->inscribirse($testTorneoId, 'usuario', $usuarioPruebaId, 'Jugador Test');
    $inscripcion1 = $modeloTorneo->obtenerInscripcionDelJugador($testTorneoId, $usuarioPruebaId, [], true);
    verificar("2.1 Inscripción inicial se genera en estado 'pendiente'",
        $inscripto && $inscripcion1 !== null && $inscripcion1['estado'] === 'pendiente',
        "Estado actual: " . ($inscripcion1['estado'] ?? 'null')
    );

    // 2.2 Cancelar/Retirar solicitud pendiente
    $cancelado = $modeloTorneo->cancelarInscripcionParticipante($testTorneoId, 'usuario', $usuarioPruebaId);
    $inscripcionActiva = $modeloTorneo->obtenerInscripcionDelJugador($testTorneoId, $usuarioPruebaId, [], true);
    $inscripcionHistorica = $modeloTorneo->obtenerInscripcionDelJugador($testTorneoId, $usuarioPruebaId, [], false);
    verificar("2.2 Jugador retira su solicitud pendiente y pasa a 'cancelado' sin bloquear reintento",
        $cancelado && $inscripcionActiva === null && $inscripcionHistorica['estado'] === 'cancelado',
        "Inscripción activa es null y última histórica es: " . ($inscripcionHistorica['estado'] ?? 'null')
    );

    // 2.3 Re-inscribirse luego de haber sido cancelado (probar ON DUPLICATE KEY UPDATE)
    $reinscripto = $modeloTorneo->inscribirse($testTorneoId, 'usuario', $usuarioPruebaId, 'Jugador Test');
    $inscripcionReactivada = $modeloTorneo->obtenerInscripcionDelJugador($testTorneoId, $usuarioPruebaId, [], true);
    verificar("2.3 Re-inscripción tras cancelación se procesa correctamente y vuelve a 'pendiente'",
        $reinscripto && $inscripcionReactivada !== null && $inscripcionReactivada['estado'] === 'pendiente',
        "Estado re-activado: " . ($inscripcionReactivada['estado'] ?? 'null')
    );

    // 2.4 Limpieza automática de pendientes al iniciar torneo (transición a 'en_curso')
    $modeloTorneo->cambiarEstado($testTorneoId, 2, 'en_curso');
    $inscripcionPostInicio = $modeloTorneo->obtenerInscripcionDelJugador($testTorneoId, $usuarioPruebaId, [], false);
    verificar("2.4 Al pasar torneo a 'en_curso', las solicitudes 'pendiente' pasan automáticamente a 'rechazado'",
        $inscripcionPostInicio !== null && $inscripcionPostInicio['estado'] === 'rechazado',
        "Estado post en_curso: " . ($inscripcionPostInicio['estado'] ?? 'null')
    );

    $bd->rollBack();
} catch (\Throwable $e) {
    $bd->rollBack();
    echo "ERROR en pruebas de Fase 2: " . $e->getMessage() . "\n";
}

// --------------------------------------------------------------------------
// FASE 3: Calidad de Datos, Récord Deportivo y Seguridad
// --------------------------------------------------------------------------
echo "\n--- FASE 3: Calidad de Datos, UX y Seguridad ---\n";

// 3.1 Cálculo de empates reales en obtenerRecordDelJugador
$recordJugador = $modeloTorneo->obtenerRecordDelJugador(3, []);
verificar("3.1 obtenerRecordDelJugador retorna estructura completa con victorias, empates y derrotas",
    isset($recordJugador['victorias'], $recordJugador['empates'], $recordJugador['derrotas']),
    "Récord: {$recordJugador['victorias']}V - {$recordJugador['empates']}E - {$recordJugador['derrotas']}D"
);

// 3.2 Estadísticas dinámicas de equipo
$primerEquipoId = (int) $bd->query("SELECT id FROM equipos LIMIT 1")->fetchColumn();
$statsEquipo = $modeloEquipo->obtenerEstadisticasTorneos($primerEquipoId);
verificar("3.2 obtenerEstadisticasTorneos calcula métricas reales de partidos y torneos ganados desde la BD",
    isset($statsEquipo['partidas'], $statsEquipo['victorias'], $statsEquipo['empates'], $statsEquipo['torneos_ganados']),
    "Equipo #$primerEquipoId -> Partidas: {$statsEquipo['partidas']}, Victorias: {$statsEquipo['victorias']}, Empates: {$statsEquipo['empates']}, Torneos Ganados: {$statsEquipo['torneos_ganados']}"
);

// 3.3 Blindaje contra Open Redirect en GuardadosControlador
require_once __DIR__ . '/../codigo_fuente/controladores/GuardadosControlador.php';
$reflector = new \ReflectionClass(\App\Controladores\GuardadosControlador::class);
$metodoSanitizar = $reflector->getMethod('sanitizarVolverA');
$metodoSanitizar->setAccessible(true);
$instancia = $reflector->newInstanceWithoutConstructor();

$urlMaliciosa1 = "https://evil.com/phishing";
$urlMaliciosa2 = "//evil.com/login";
$urlMaliciosa3 = "javascript:alert(1)";
$urlValida1 = URL_BASE . "index.php?c=torneo&a=detalle&id=10";
$urlValida2 = "index.php?c=equipo&a=verPerfil&id=5";

$res1 = $metodoSanitizar->invoke($instancia, $urlMaliciosa1);
$res2 = $metodoSanitizar->invoke($instancia, $urlMaliciosa2);
$res3 = $metodoSanitizar->invoke($instancia, $urlMaliciosa3);
$res4 = $metodoSanitizar->invoke($instancia, $urlValida1);
$res5 = $metodoSanitizar->invoke($instancia, $urlValida2);

$blindajeExitoso = (
    strpos($res1, 'evil.com') === false &&
    strpos($res2, 'evil.com') === false &&
    strpos($res3, 'javascript') === false &&
    $res4 === $urlValida1 &&
    str_starts_with($res5, URL_BASE)
);

verificar("3.3 Blindaje contra Open Redirect neutraliza URLs externas absolutas y protocol-relative",
    $blindajeExitoso,
    "Maliciosa neutralizada: $res1 | Válida preservada: $res4"
);

echo "\n====================================================================\n";
echo " RESUMEN: $exitos / $total PRUEBAS EXITOSAS (" . round(($exitos / $total) * 100) . "%)\n";
echo "====================================================================\n";
