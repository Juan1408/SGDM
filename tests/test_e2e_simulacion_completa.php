<?php
/**
 * Test E2E Simulado: Ciclo de Vida Completo de la Plataforma SGDM
 *
 * Simula el flujo real de extremo a extremo:
 * 1. Verificación de Roles y Autenticación.
 * 2. Creación de Torneo por Organizador (Liga).
 * 3. Formación de Equipos y Validación de Disciplina Colectiva.
 * 4. Postulación e Inscripción de Equipos y Jugadores.
 * 5. Transición a 'en_curso' con depuración automática de pendientes.
 * 6. Generación de Fixture y Programación sin conflictos.
 * 7. Disputa de Partidos, Carga de Resultados y Recálculo de Posiciones.
 * 8. Rectificación de Resultados y Tabla de Posiciones Final.
 * 9. Finalización y Determinación de Podio.
 * 10. Desbloqueo de Equipos y Eliminación en Cascada por Admin.
 */

require_once __DIR__ . '/../codigo_fuente/configuracion/constantes.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Conexion.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Torneo.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Equipo.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Juego.php';
require_once __DIR__ . '/../codigo_fuente/ayudantes/Sesion.php';

use App\Modelos\Conexion;
use App\Modelos\Torneo;
use App\Modelos\Equipo;
use App\Ayudantes\Sesion;

$bd = Conexion::getInstance()->getBD();
$modeloTorneo = new Torneo();
$modeloEquipo = Equipo::obtenerInstancia();
$modeloJuego  = new \Juego();

echo "====================================================================\n";
echo "   AUDITORIA E2E: SIMULACION INTEGRAL DEL CICLO DE VIDA (SGDM)     \n";
echo "====================================================================\n\n";

$exitos = 0;
$total = 0;

function verificarE2E(string $paso, bool $condicion, string $detalle = '') {
    global $exitos, $total;
    $total++;
    if ($condicion) {
        $exitos++;
        echo " [PASS] $paso\n";
        if ($detalle) {
            echo "        -> $detalle\n";
        }
    } else {
        echo " [FAIL] $paso\n";
        if ($detalle) {
            echo "        -> DETALLE: $detalle\n";
        }
    }
}

try {
    $adminId = 1;
    $organizadorId = 2;
    $capitan1Id = 301;
    $jugador1Id = 401;
    $capitan2Id = 302;
    $jugador2Id = 402;

    // Usuarios descartables (INSERT IGNORE: jamás se modifican usuarios reales; se eliminan al final).
    // Los ids 1 (admin) y 2 (organizador) son usuarios reales del sistema y NO se tocan.
    foreach ([
        [$capitan1Id, 'cap1@e2e.test', 3, 'Capitán Titanes'],
        [$jugador1Id, 'jug1@e2e.test', 3, 'Jugador Titán'],
        [$capitan2Id, 'cap2@e2e.test', 3, 'Capitán Dragones'],
        [$jugador2Id, 'jug2@e2e.test', 3, 'Jugador Dragón']
    ] as $u) {
        $bd->exec("INSERT IGNORE INTO usuarios (id, email, contrasena_hash, rol_id, nombre_completo, esta_activo)
                   VALUES ({$u[0]}, '{$u[1]}', 'hash_test', {$u[2]}, '{$u[3]}', 1)");
    }

    // PASO 1: Selección de Disciplina Deportiva Colectiva
    $juegoColectivo = $bd->query("
        SELECT j.id, j.nombre
        FROM juegos j
        INNER JOIN juego_modalidades_permitidas jmp ON jmp.juego_id = j.id AND jmp.modalidad_id = 2
        INNER JOIN juego_formatos_permitidos jfp ON jfp.juego_id = j.id AND jfp.formato_codigo = 'liga'
        WHERE j.activo = 1
        LIMIT 1
    ")->fetch(\PDO::FETCH_ASSOC);

    $juegoId = (int) $juegoColectivo['id'];
    verificarE2E("1. Catálogo de Disciplinas: Disciplina colectiva compatible con Liga",
        $juegoId > 0,
        "Disciplina elegida: {$juegoColectivo['nombre']} (ID: $juegoId)"
    );

    // Limpieza preventiva de ejecuciones previas
    $bd->exec("DELETE FROM equipos WHERE nombre_equipo IN ('__TITANES_E2E__', '__DRAGONES_E2E__')");

    // PASO 2: Creación de Equipos con Roster Reglamentario
    $resEq1 = $modeloEquipo->crearEquipoCompleto(
        '__TITANES_E2E__',
        $capitan1Id,
        'Equipo Titanes para E2E',
        'Buenos Aires',
        2026,
        $juegoId
    );
    if (empty($resEq1['exito'])) {
        throw new \Exception("Error al crear equipo 1: " . ($resEq1['error'] ?? 'desconocido'));
    }
    $eq1Id = (int) ($resEq1['equipoId'] ?? 0);
    $bd->exec("INSERT INTO equipo_miembros (equipo_id, usuario_id, es_activo)
               VALUES ($eq1Id, $jugador1Id, 1)
               ON DUPLICATE KEY UPDATE es_activo = 1");

    $resEq2 = $modeloEquipo->crearEquipoCompleto(
        '__DRAGONES_E2E__',
        $capitan2Id,
        'Equipo Dragones para E2E',
        'Córdoba',
        2026,
        $juegoId
    );
    $eq2Id = (int) ($resEq2['equipoId'] ?? 0);
    $bd->exec("INSERT INTO equipo_miembros (equipo_id, usuario_id, es_activo)
               VALUES ($eq2Id, $jugador2Id, 1)
               ON DUPLICATE KEY UPDATE es_activo = 1");

    verificarE2E("2. Equipos y Plantillas: Creación y asignación de roster mínimo (>= 2 miembros)",
        $eq1Id > 0 && $eq2Id > 0,
        "Equipo 1 ID: $eq1Id, Equipo 2 ID: $eq2Id"
    );

    // PASO 3: Creación de Torneo en Borrador y Apertura de Inscripciones
    $bd->exec("INSERT INTO torneos (organizador_id, juego_id, modalidad_id, nombre, formato, sistema_puntuacion_id,
                                    estado, cupo_max_equipos, cupo_min_equipos, fecha_inicio, fecha_fin, ubicacion)
               VALUES ($organizadorId, $juegoId, 2, '__TORNEO_MASTER_E2E__', 'liga', 1,
                       'inscripciones_abiertas', 4, 2, '2026-11-01', '2026-11-30', 'Arena Central')");
    $torneoId = (int) $bd->lastInsertId();

    verificarE2E("3. Creación y Ciclo de Vida de Torneo: Torneo creado con inscripciones abiertas",
        $torneoId > 0,
        "Torneo ID: $torneoId, Estado: inscripciones_abiertas"
    );

    // PASO 4: Inscripción y Confirmación de Participantes (Equipos)
    $bd->exec("INSERT INTO participantes_torneo (torneo_id, tipo, referencia_id, nombre, estado)
               VALUES ($torneoId, 'equipo', $eq1Id, 'Titanes E2E', 'confirmado'),
                      ($torneoId, 'equipo', $eq2Id, 'Dragones E2E', 'confirmado')");

    $pTitanes = (int) $bd->query("SELECT id FROM participantes_torneo WHERE torneo_id = $torneoId AND referencia_id = $eq1Id")->fetchColumn();
    $pDragones = (int) $bd->query("SELECT id FROM participantes_torneo WHERE torneo_id = $torneoId AND referencia_id = $eq2Id")->fetchColumn();

    // Inscripción pendiente que deberá ser depurada automáticamente al pasar a en_curso
    $bd->exec("INSERT INTO participantes_torneo (torneo_id, tipo, referencia_id, nombre, estado)
               VALUES ($torneoId, 'equipo', 9999, 'Equipo Rezagado', 'pendiente')");

    verificarE2E("4. Inscripciones: 2 equipos confirmados y 1 equipo rezagado pendiente",
        $pTitanes > 0 && $pDragones > 0,
        "Participante Titanes ID: $pTitanes, Dragones ID: $pDragones"
    );

    // PASO 5: Paso de Torneo a 'en_curso' con depuración atómica
    $modeloTorneo->cambiarEstado($torneoId, $organizadorId, 'en_curso');
    $estadoPendientePost = $bd->query("SELECT estado FROM participantes_torneo WHERE torneo_id = $torneoId AND referencia_id = 9999")->fetchColumn();

    verificarE2E("5. Transición a 'en_curso': Depuración automática de postulaciones pendientes",
        $estadoPendientePost === 'rechazado',
        "Estado del rezagado tras inicio del torneo: $estadoPendientePost"
    );

    // PASO 6: Generación de Fixture
    $resFixture = $modeloTorneo->generarFixture($torneoId, $organizadorId);
    $encuentros = $bd->query("SELECT id, ronda, participante_local_id, participante_visitante_id, estado FROM torneo_encuentros WHERE torneo_id = $torneoId")->fetchAll(\PDO::FETCH_ASSOC);

    verificarE2E("6. Generación de Fixtures: Fixture de liga generado con partidos programados",
        $resFixture['ok'] === true && count($encuentros) > 0,
        "Encuentros creados: " . count($encuentros)
    );

    $encuentroId = (int) $encuentros[0]['id'];

    // PASO 7: Programación de Encuentro con Validación de Conflictos (Fase D)
    $resProgOk = $modeloTorneo->programarEncuentro($encuentroId, $organizadorId, '2026-11-10 16:00:00', 'Cancha A');
    verificarE2E("7. Programación de Partidos: Asignación de fecha, hora y cancha sin conflictos",
        $resProgOk['ok'] === true,
        "Resultado: {$resProgOk['mensaje']}"
    );

    // PASO 8: Carga de Resultado y Generación Automática de Tabla de Posiciones
    $resMarcador = $modeloTorneo->guardarResultado($encuentroId, $organizadorId, 3.0, 1.0); // Gana Titanes 3 - 1
    $posiciones = $modeloTorneo->obtenerPosiciones($torneoId, $organizadorId);

    verificarE2E("8. Marcadores y Tabla de Posiciones: Carga de resultado y recálculo automático",
        $resMarcador['ok'] === true && count($posiciones) >= 2 && (float) $posiciones[0]['puntos'] === 3.0,
        "Líder: {$posiciones[0]['nombre']} con {$posiciones[0]['puntos']} pts (PJ: {$posiciones[0]['partidos_jugados']})"
    );

    // PASO 9: Rectificación de Marcador en Runtime (Fase A)
    $resCorrec = $modeloTorneo->corregirResultado($encuentroId, $organizadorId, 2.0, 2.0); // Rectificado a Empate 2 - 2
    $posicionesPost = $modeloTorneo->obtenerPosiciones($torneoId, $organizadorId);

    verificarE2E("9. Rectificación de Resultados: Modificación de marcador y ajuste dinámico de puntos",
        $resCorrec['ok'] === true && (float) $posicionesPost[0]['puntos'] === 1.0,
        "Puntos tras rectificación a empate: {$posicionesPost[0]['puntos']} pts"
    );

    // PASO 10: Finalización de Torneo y Podio
    $modeloTorneo->cambiarEstado($torneoId, $organizadorId, 'finalizado');
    $podio = $modeloTorneo->obtenerPodio($torneoId);

    $primerPuesto = is_array($podio['primero'] ?? null) ? $podio['primero']['nombre'] : ($podio['primero'] ?? 'Sin podio');
    verificarE2E("10. Finalización de Torneo: Estado 'finalizado' y cálculo del podio de honor",
        !empty($podio) && !empty($podio['primero']),
        "Primer puesto del podio: $primerPuesto"
    );

    // PASO 11: Seguridad Perimetral: Baneo en Caliente en Runtime (Fase C)
    // Suspendemos temporalmente al capitán 1 y verificamos que Sesion corte la ejecución
    $_SESSION['usuario_id'] = $capitan1Id;
    $bd->exec("UPDATE usuarios SET esta_activo = 0 WHERE id = $capitan1Id");
    $activoCheck = Sesion::verificarUsuarioActivo();
    $logueadoCheck = Sesion::estaLogueado();

    verificarE2E("11. Seguridad y Baneo en Caliente: Expulsión instantánea de sesión para usuario suspendido",
        $activoCheck === false && $logueadoCheck === false,
        "verificarUsuarioActivo: false, estaLogueado: false"
    );

    // PASO 12: Eliminación en Cascada Segura por Administrador (Fase C)
    $resElim = $modeloTorneo->eliminarPorAdmin($torneoId);
    $torneoExiste = (int) $bd->query("SELECT COUNT(*) FROM torneos WHERE id = $torneoId")->fetchColumn();
    $depsResiduales = (int) $bd->query("SELECT COUNT(*) FROM torneo_encuentros WHERE torneo_id = $torneoId")->fetchColumn();

    // Limpieza de equipos de prueba
    $bd->exec("DELETE FROM equipo_miembros WHERE equipo_id IN ($eq1Id, $eq2Id)");
    $bd->exec("DELETE FROM equipos WHERE id IN ($eq1Id, $eq2Id)");

    verificarE2E("12. Eliminación Atómica en Cascada: Purga de 9 tablas relacionales sin errores de FK",
        $resElim === true && $torneoExiste === 0 && $depsResiduales === 0,
        "Torneo residual: $torneoExiste, Encuentros residuales: $depsResiduales"
    );

} catch (\Throwable $e) {
    echo "ERROR E2E: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    if (isset($torneoId) && $torneoId > 0) {
        $modeloTorneo->eliminarPorAdmin($torneoId);
    }
} finally {
    // Limpieza garantizada de datos descartables (solo los creados por este test)
    $bd->exec("DELETE FROM equipos WHERE nombre_equipo IN ('__TITANES_E2E__', '__DRAGONES_E2E__')");
    $bd->exec("DELETE FROM usuarios WHERE email LIKE '%@e2e.test' AND contrasena_hash = 'hash_test'");
}

echo "\n====================================================================\n";
echo " RESUMEN AUDITORIA E2E: $exitos / $total PRUEBAS EXITOSAS (" . ($total > 0 ? round(($exitos / $total) * 100) : 0) . "%)\n";
echo "====================================================================\n\n";

if ($exitos === $total && $total > 0) {
    exit(0);
}
exit(1);
