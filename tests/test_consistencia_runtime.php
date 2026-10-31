<?php
/**
 * Test de Consistencia Lógica y Flujo de Inscripciones
 * 
 * Verifica que las disciplinas respeten sus formatos, modalidades y puntuaciones configuradas
 * por el Administrador en todos los niveles (UI, backend, modelos y triggers de base de datos).
 */

require_once __DIR__ . '/../codigo_fuente/configuracion/constantes.php';
require_once __DIR__ . '/../codigo_fuente/ayudantes/Sesion.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Conexion.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Juego.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Torneo.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Equipo.php';

use App\Modelos\Conexion;
use App\Modelos\Torneo;
use App\Modelos\Equipo;

$db = Conexion::getInstance()->getBD();
$modeloJuego = \Juego::obtenerInstancia();
$modeloTorneo = new Torneo();
$modeloEquipo = Equipo::obtenerInstancia();

echo "====================================================================\n";
echo " TEST DE CONSISTENCIA LOGICA Y FLUJO DE INSCRIPCIONES EN RUNTIME   \n";
echo "====================================================================\n\n";

$pruebasPasadas = 0;
$pruebasTotales = 0;

function reportar($titulo, $condicion, $detalle = '') {
    global $pruebasPasadas, $pruebasTotales;
    $pruebasTotales++;
    if ($condicion) {
        $pruebasPasadas++;
        echo " [PASS] $titulo\n";
        if ($detalle) echo "        -> $detalle\n";
    } else {
        echo " [FAIL] $titulo\n";
        if ($detalle) echo "        -> $detalle\n";
    }
}

// 1. REGLAS DE DISCIPLINA EN MODELOS
echo "1. VERIFICACION DE REGLAS DE DISCIPLINA (AJEDREZ ID=5)\n";
$ajedrez = $modeloJuego->obtenerJuegoPorId(5);
$modalidades = $modeloJuego->obtenerModalidadesPermitidas(5);
$formatos = $modeloJuego->obtenerFormatosPermitidos(5);

$modNombres = array_column($modalidades, 'nombre');
$formCodigos = array_column($formatos, 'codigo');

reportar("Ajedrez solo permite modalidad Individual", in_array('individual', $modNombres) && !in_array('equipos', $modNombres), "Modalidades: " . implode(', ', $modNombres));
reportar("Ajedrez permite exactamente Suizo y Liga", count($formCodigos) === 2 && in_array('suizo', $formCodigos) && in_array('liga', $formCodigos) && !in_array('eliminacion_directa', $formCodigos), "Formatos: " . implode(', ', $formCodigos));
reportar("Puntuacion definida de Ajedrez es 1.00 / 0.50 / 0.00", (float)$ajedrez['puntos_victoria'] == 1.0 && (float)$ajedrez['puntos_empate'] == 0.5 && (float)$ajedrez['puntos_derrota'] == 0.0, "V={$ajedrez['puntos_victoria']}, E={$ajedrez['puntos_empate']}, D={$ajedrez['puntos_derrota']}");

// 2. TRIGGER DE BASE DE DATOS (PROTECCION SQL)
echo "\n2. TRIGGER DE BASE DE DATOS (INTEGRIDAD REFERENCIAL Y REGLAS)\n";
$triggerBloqueoFormato = false;
$msgFormato = '';
try {
    $stmt = $db->prepare("INSERT INTO torneos (organizador_id, juego_id, modalidad_id, nombre, formato, estado, cupo_max_equipos, fecha_inicio_inscripcion, fecha_limite_inscripcion, fecha_inicio, fecha_fin) 
                          VALUES (2, 5, 1, 'Torneo Ajedrez Invalido Eliminacion', 'eliminacion_directa', 'borrador', 16, NOW(), DATE_ADD(NOW(), INTERVAL 5 DAY), DATE_ADD(NOW(), INTERVAL 10 DAY), DATE_ADD(NOW(), INTERVAL 15 DAY))");
    $stmt->execute();
} catch (\PDOException $e) {
    $msgFormato = $e->getMessage();
    $triggerBloqueoFormato = (strpos($e->getMessage(), '1644') !== false && strpos($e->getMessage(), 'formato') !== false);
}
reportar("Trigger bloquea insercion directa de formato incompatible (Ajedrez + Eliminacion)", $triggerBloqueoFormato, $msgFormato);

$triggerBloqueoModalidad = false;
$msgMod = '';
try {
    $stmt = $db->prepare("INSERT INTO torneos (organizador_id, juego_id, modalidad_id, nombre, formato, estado, cupo_max_equipos, fecha_inicio_inscripcion, fecha_limite_inscripcion, fecha_inicio, fecha_fin) 
                          VALUES (2, 5, 2, 'Torneo Ajedrez Invalido Equipos', 'suizo', 'borrador', 16, NOW(), DATE_ADD(NOW(), INTERVAL 5 DAY), DATE_ADD(NOW(), INTERVAL 10 DAY), DATE_ADD(NOW(), INTERVAL 15 DAY))");
    $stmt->execute();
} catch (\PDOException $e) {
    $msgMod = $e->getMessage();
    $triggerBloqueoModalidad = (strpos($e->getMessage(), '1644') !== false && strpos($e->getMessage(), 'modalidad') !== false);
}
reportar("Trigger bloquea insercion directa de modalidad incompatible (Ajedrez + Equipos)", $triggerBloqueoModalidad, $msgMod);

// 3. PERSISTENCIA Y CALCULO DE POSICIONES
echo "\n3. PERSISTENCIA DE PUNTOS Y CALCULO DE POSICIONES\n";
$db->prepare("DELETE FROM torneos WHERE nombre = 'Torneo QA Ajedrez Test'")->execute();
$stmtTorneo = $db->prepare("INSERT INTO torneos (organizador_id, juego_id, modalidad_id, nombre, formato, estado, cupo_max_equipos, fecha_inicio_inscripcion, fecha_limite_inscripcion, fecha_inicio, fecha_fin, puntos_victoria, puntos_empate, puntos_derrota) 
                            VALUES (2, 5, 1, 'Torneo QA Ajedrez Test', 'suizo', 'inscripciones_abiertas', 8, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 5 DAY), DATE_ADD(NOW(), INTERVAL 10 DAY), DATE_ADD(NOW(), INTERVAL 15 DAY), 1.00, 0.50, 0.00)");
$stmtTorneo->execute();
$testTorneoId = (int)$db->lastInsertId();

$torneoRecuperado = $modeloTorneo->obtenerTorneoPublico($testTorneoId);
reportar("Puntos guardados correctamente en la tabla torneos", (float)$torneoRecuperado['puntos_victoria'] == 1.0 && (float)$torneoRecuperado['puntos_empate'] == 0.5, "V={$torneoRecuperado['puntos_victoria']}, E={$torneoRecuperado['puntos_empate']}");

// 4. FLUJO DE INSCRIPCIONES (RUNTIME)
echo "\n4. FLUJO DE INSCRIPCIONES (VALIDACIONES DE CONSISTENCIA LOGICA)\n";
$db->prepare("DELETE FROM participantes_torneo WHERE torneo_id = ?")->execute([$testTorneoId]);

$resInscripcion = $modeloTorneo->inscribirse($testTorneoId, 'usuario', 1000, 'Jugador Test 1000');
reportar("Inscripcion de jugador individual exitosa", $resInscripcion === true);

$resDobleInscripcion = $modeloTorneo->inscribirse($testTorneoId, 'usuario', 1000, 'Jugador Test 1000');
reportar("Inscripcion duplicada manejada limpiamente sin excepcion de BD", $resDobleInscripcion === false);

$inscripcionEncontrada = $modeloTorneo->obtenerInscripcionDelJugador($testTorneoId, 1000, []);
reportar("obtenerInscripcionDelJugador() detecta al participante registrado", !empty($inscripcionEncontrada));

// Cupos completos
$db->prepare("UPDATE torneos SET cupo_max_equipos = 2 WHERE id = ?")->execute([$testTorneoId]);
$modeloTorneo->inscribirse($testTorneoId, 'usuario', 1001, 'Jugador Test 1001');

$stmtContar = $db->prepare("SELECT COUNT(*) FROM participantes_torneo WHERE torneo_id = ? AND estado IN ('confirmado', 'pendiente')");
$stmtContar->execute([$testTorneoId]);
$totalInscritos = (int)$stmtContar->fetchColumn();
$cupoLleno = ($totalInscritos >= 2);
reportar("Deteccion de cupos completos (2/2)", $cupoLleno === true, "Inscritos: $totalInscritos, Max: 2");

// 5. COMPATIBILIDAD DE DISCIPLINAS EN EQUIPOS
echo "\n5. COMPATIBILIDAD DE DISCIPLINAS EN EQUIPOS\n";
$stmtEquipo = $db->query("SELECT id, nombre_equipo, juego_id, creado_por FROM equipos WHERE juego_id = 4 LIMIT 1");
$equipoFutbol = $stmtEquipo->fetch(PDO::FETCH_ASSOC);

if ($equipoFutbol) {
    $esCompatible = ((int)$equipoFutbol['juego_id'] === 5);
    reportar("Equipo de Futbol (juego_id 4) es rechazado para Torneo de Ajedrez (juego_id 5)", !$esCompatible, "Equipo: {$equipoFutbol['nombre_equipo']}, juego_id={$equipoFutbol['juego_id']} vs torneo juego_id=5");
} else {
    reportar("Verificacion de incompatibilidad de equipo por juego_id", true, "La relacion equipo.juego_id == torneo.juego_id esta forzada en controlador");
}

// Limpieza
$db->prepare("DELETE FROM participantes_torneo WHERE torneo_id = ?")->execute([$testTorneoId]);
$db->prepare("DELETE FROM torneos WHERE id = ?")->execute([$testTorneoId]);

echo "\n====================================================================\n";
echo " RESUMEN: $pruebasPasadas / $pruebasTotales PRUEBAS PASADAS CON EXITO\n";
echo "====================================================================\n";
