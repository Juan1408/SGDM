<?php
/**
 * Test Automatizado de Fase C: Seguridad, Baneo en Caliente y Eliminación en Cascada de Torneos
 */

require_once __DIR__ . '/../codigo_fuente/configuracion/constantes.php';
require_once __DIR__ . '/../codigo_fuente/ayudantes/Sesion.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Conexion.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Torneo.php';

use App\Ayudantes\Sesion;
use App\Modelos\Conexion;
use App\Modelos\Torneo;

$bd = Conexion::getInstance()->getBD();
$modeloTorneo = new Torneo();

echo "====================================================================\n";
echo "   TEST DE FASE C: BANEO EN CALIENTE Y ELIMINACION EN CASCADA       \n";
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

$bd->beginTransaction();
try {
    // ----------------------------------------------------------------------
    // 1. TEST: Baneo en Caliente (Invalidación Inmediata de Sesión)
    // ----------------------------------------------------------------------
    echo "--- 1. Baneo en Caliente de Usuarios Suspendidos ---\n";

    $usuarioTestId = 401;
    // Creamos usuario activo
    $bd->exec("INSERT INTO usuarios (id, nombre_completo, email, contrasena_hash, rol_id, esta_activo)
               VALUES ($usuarioTestId, 'Usuario Baneo Test', 'baneo_test@sgdm.com', 'dummyhash', 3, 1)
               ON DUPLICATE KEY UPDATE esta_activo = 1");

    // Simulamos sesión activa del usuario
    Sesion::iniciarSesion();
    $_SESSION['usuario_id'] = $usuarioTestId;
    $_SESSION['email'] = 'baneo_test@sgdm.com';
    $_SESSION['rol_id'] = 3;
    $_SESSION['usuario_nombre'] = 'Usuario Baneo Test';
    $_SESSION['esta_activo'] = 1;
    $_SESSION['expiracion_sesion'] = time() + 1800;

    // 1.1 Usuario activo es aceptado
    $activoPre = Sesion::verificarUsuarioActivo();
    $logueadoPre = Sesion::estaLogueado();

    verificar("1.1 Usuario con esta_activo = 1 es validado correctamente",
        $activoPre === true && $logueadoPre === true,
        "verificarUsuarioActivo: true, estaLogueado: true"
    );

    // 1.2 El administrador suspende al usuario en la base de datos
    $bd->exec("UPDATE usuarios SET esta_activo = 0 WHERE id = $usuarioTestId");

    // 1.3 La siguiente comprobación detecta el baneo en caliente
    $activoPost = Sesion::verificarUsuarioActivo();
    $logueadoPost = Sesion::estaLogueado();
    $sesionDestruida = empty($_SESSION['usuario_id']);

    verificar("1.2 Al suspender la cuenta en la BD, estaLogueado() detecta el cambio en caliente",
        $activoPost === false && $logueadoPost === false && $sesionDestruida === true,
        "verificarUsuarioActivo: false, estaLogueado: false, Sesión destruida: Sí"
    );

    // ----------------------------------------------------------------------
    // 2. TEST: Eliminación en Cascada Segura de Torneos por Administrador
    // ----------------------------------------------------------------------
    echo "\n--- 2. Eliminación en Cascada Segura de Torneos por Admin ---\n";

    $organizadorId = 2;

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

    // Creamos un torneo dummy
    $bd->exec("INSERT INTO torneos (organizador_id, juego_id, modalidad_id, nombre, formato, sistema_puntuacion_id, estado, cupo_max_equipos)
               VALUES ($organizadorId, $juegoId, 1, '__TORNEO_CASCADA_TEST__', 'liga', 1, 'inscripciones_abiertas', 8)");
    $tId = (int) $bd->lastInsertId();

    // Creamos registros en todas las tablas dependientes
    // 1. participantes_torneo
    $bd->exec("INSERT INTO participantes_torneo (torneo_id, tipo, referencia_id, nombre, estado)
               VALUES ($tId, 'usuario', 402, 'Jugador Cascada 1', 'confirmado'),
                      ($tId, 'usuario', 403, 'Jugador Cascada 2', 'confirmado')");
    $p1 = (int) $bd->query("SELECT id FROM participantes_torneo WHERE torneo_id = $tId LIMIT 1")->fetchColumn();
    $p2 = (int) $bd->query("SELECT id FROM participantes_torneo WHERE torneo_id = $tId ORDER BY id DESC LIMIT 1")->fetchColumn();

    // 2. torneo_encuentros
    $bd->exec("INSERT INTO torneo_encuentros (torneo_id, ronda, participante_local_id, participante_visitante_id, estado)
               VALUES ($tId, 1, $p1, $p2, 'programado')");

    // 3. torneo_posiciones
    $bd->exec("INSERT INTO torneo_posiciones (torneo_id, participante_id, puntos)
               VALUES ($tId, $p1, 3), ($tId, $p2, 0)");

    // 4. torneo_suizo_parejas
    $bd->exec("INSERT INTO torneo_suizo_parejas (torneo_id, participante_a_id, participante_b_id, ronda)
               VALUES ($tId, $p1, $p2, 1)");

    // 5. torneo_actividades
    $bd->exec("INSERT INTO torneo_actividades (torneo_id, titulo, descripcion, tipo, fecha, hora)
               VALUES ($tId, 'Apertura', 'Actividad de prueba', 'reunion', '2026-10-24', '10:00:00')");

    // 6. torneo_comentarios
    $bd->exec("INSERT INTO torneo_comentarios (torneo_id, usuario_id, comentario, aprobado)
               VALUES ($tId, $organizadorId, 'Comentario de prueba', 1)");

    // 7. torneo_cambio_estado
    $bd->exec("INSERT INTO torneo_cambio_estado (torneo_id, estado_anterior, estado_nuevo, motivo, usuario_id)
               VALUES ($tId, 'borrador', 'inscripciones_abiertas', 'Inicio de torneo', $organizadorId)");

    // 8. torneo_config
    $bd->exec("INSERT INTO torneo_config (torneo_id, clave, valor)
               VALUES ($tId, 'permitir_empates', '1')");

    // 9. guardados (favoritos)
    $bd->exec("INSERT INTO guardados (usuario_id, tipo, referencia_id)
               VALUES ($organizadorId, 'torneo', $tId)");

    // Verificamos que las dependencias existan antes del borrado
    $depsPre = [
        'encuentros' => (int) $bd->query("SELECT COUNT(*) FROM torneo_encuentros WHERE torneo_id = $tId")->fetchColumn(),
        'posiciones' => (int) $bd->query("SELECT COUNT(*) FROM torneo_posiciones WHERE torneo_id = $tId")->fetchColumn(),
        'suizo' => (int) $bd->query("SELECT COUNT(*) FROM torneo_suizo_parejas WHERE torneo_id = $tId")->fetchColumn(),
        'actividades' => (int) $bd->query("SELECT COUNT(*) FROM torneo_actividades WHERE torneo_id = $tId")->fetchColumn(),
        'comentarios' => (int) $bd->query("SELECT COUNT(*) FROM torneo_comentarios WHERE torneo_id = $tId")->fetchColumn(),
        'cambio_estado' => (int) $bd->query("SELECT COUNT(*) FROM torneo_cambio_estado WHERE torneo_id = $tId")->fetchColumn(),
        'config' => (int) $bd->query("SELECT COUNT(*) FROM torneo_config WHERE torneo_id = $tId")->fetchColumn(),
        'participantes' => (int) $bd->query("SELECT COUNT(*) FROM participantes_torneo WHERE torneo_id = $tId")->fetchColumn(),
        'guardados' => (int) $bd->query("SELECT COUNT(*) FROM guardados WHERE tipo = 'torneo' AND referencia_id = $tId")->fetchColumn(),
    ];

    $totalDepsPre = array_sum($depsPre);
    verificar("2.1 Torneo creado con dependencias completas en 9 tablas relacionales",
        $totalDepsPre >= 9,
        "Total registros vinculados creados: $totalDepsPre"
    );

    // 2.2 Ejecutamos la eliminación administrativa
    $resEliminar = $modeloTorneo->eliminarPorAdmin($tId);

    // Verificamos que el torneo principal haya sido eliminado
    $torneoExiste = (int) $bd->query("SELECT COUNT(*) FROM torneos WHERE id = $tId")->fetchColumn();

    // Verificamos que todas las tablas dependientes hayan quedado limpias
    $depsPost = [
        'encuentros' => (int) $bd->query("SELECT COUNT(*) FROM torneo_encuentros WHERE torneo_id = $tId")->fetchColumn(),
        'posiciones' => (int) $bd->query("SELECT COUNT(*) FROM torneo_posiciones WHERE torneo_id = $tId")->fetchColumn(),
        'suizo' => (int) $bd->query("SELECT COUNT(*) FROM torneo_suizo_parejas WHERE torneo_id = $tId")->fetchColumn(),
        'actividades' => (int) $bd->query("SELECT COUNT(*) FROM torneo_actividades WHERE torneo_id = $tId")->fetchColumn(),
        'comentarios' => (int) $bd->query("SELECT COUNT(*) FROM torneo_comentarios WHERE torneo_id = $tId")->fetchColumn(),
        'cambio_estado' => (int) $bd->query("SELECT COUNT(*) FROM torneo_cambio_estado WHERE torneo_id = $tId")->fetchColumn(),
        'config' => (int) $bd->query("SELECT COUNT(*) FROM torneo_config WHERE torneo_id = $tId")->fetchColumn(),
        'participantes' => (int) $bd->query("SELECT COUNT(*) FROM participantes_torneo WHERE torneo_id = $tId")->fetchColumn(),
        'guardados' => (int) $bd->query("SELECT COUNT(*) FROM guardados WHERE tipo = 'torneo' AND referencia_id = $tId")->fetchColumn(),
    ];

    $totalDepsPost = array_sum($depsPost);

    verificar("2.2 eliminarPorAdmin elimina en cascada atómica todas las dependencias y la fila principal",
        $resEliminar === true && $torneoExiste === 0 && $totalDepsPost === 0,
        "Torneo residual: $torneoExiste, Dependencias residuales: $totalDepsPost"
    );

    // 2.3 Intentar eliminar un torneo inexistente debe retornar false limpiamente
    $resInexistente = $modeloTorneo->eliminarPorAdmin(99999999);
    verificar("2.3 eliminarPorAdmin con ID inexistente retorna false de forma segura",
        $resInexistente === false,
        "Retorno: false"
    );

    $bd->rollBack();
} catch (\Throwable $e) {
    $bd->rollBack();
    echo "ERROR durante las pruebas de Fase C: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}

echo "\n====================================================================\n";
echo " RESUMEN FASE C: $exitos / $total PRUEBAS EXITOSAS (" . round(($exitos / $total) * 100) . "%)\n";
echo "====================================================================\n";
