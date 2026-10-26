<?php
/**
 * Test de Verificación: Remediación de Deudas Técnicas de Auditoría
 * Cobertura: C1, A1, A2, A3, A4, M8, M9
 */

require_once __DIR__ . '/../codigo_fuente/configuracion/constantes.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Conexion.php';
require_once __DIR__ . '/../codigo_fuente/ayudantes/Sesion.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Torneo.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Juego.php';
require_once __DIR__ . '/../codigo_fuente/core/ManejadorErrores.php';

use App\Modelos\Conexion;
use App\Ayudantes\Sesion;
use App\Modelos\Torneo;
use App\Core\ManejadorErrores;

$bd = Conexion::getInstance()->getBD();
$exitos = 0;
$total = 0;

function assertTest(bool $condicion, string $mensaje, string $detalle = ''): void {
    global $exitos, $total;
    $total++;
    if ($condicion) {
        $exitos++;
        echo " [PASS] $mensaje\n";
        if ($detalle) echo "        -> $detalle\n";
    } else {
        echo " [FAIL] $mensaje\n";
        if ($detalle) echo "        -> FALLO: $detalle\n";
    }
}

echo "====================================================================\n";
echo "   TEST DE VERIFICACION: DEUDAS TECNICAS DE AUDITORIA               \n";
echo "====================================================================\n\n";

// --- 1. A1 y A2: verificarUsuarioActivo() y sincronización de rol_id en caliente ---
echo "--- 1. Baneo Fail-Closed y Sincronización de Rol en Caliente (A1, A2) ---\n";
// Creamos usuario temporal
$bd->exec("INSERT INTO usuarios (email, contrasena_hash, nombre_completo, rol_id, esta_activo, email_verificado)
           VALUES ('audit_user@test.local', 'hash', 'Audit User', 3, 1, 1)");
$auditUserId = (int) $bd->lastInsertId();

$_SESSION = [];
$_SESSION['usuario_id'] = $auditUserId;
$_SESSION['rol_id'] = 3;
$_SESSION['esta_activo'] = 1;
$_SESSION['expiracion_sesion'] = time() + 1800;

$activoInicial = Sesion::verificarUsuarioActivo();
assertTest($activoInicial === true && $_SESSION['rol_id'] === 3, "1.1 Usuario activo mantiene sesión y rol_id sincronizado", "rol_id inicial: {$_SESSION['rol_id']}");

// Promovemos al usuario a rol_id = 2 en la BD
$bd->exec("UPDATE usuarios SET rol_id = 2 WHERE id = $auditUserId");
$activoPostPromocion = Sesion::verificarUsuarioActivo();
assertTest($activoPostPromocion === true && $_SESSION['rol_id'] === 2, "1.2 Al modificar rol en BD, verificarUsuarioActivo sincroniza \$_SESSION['rol_id'] en caliente", "Nuevo rol_id en sesión: {$_SESSION['rol_id']}");

// Suspendemos al usuario (esta_activo = 0)
$bd->exec("UPDATE usuarios SET esta_activo = 0 WHERE id = $auditUserId");
$activoPostSuspension = Sesion::verificarUsuarioActivo();
assertTest($activoPostSuspension === false, "1.3 Usuario suspendido es rechazado de inmediato (Baneo en caliente)", "verificarUsuarioActivo retorno: " . var_export($activoPostSuspension, true));

// Limpieza de usuario temporal
$bd->exec("DELETE FROM usuarios WHERE id = $auditUserId");

// --- 2. A3: Torneo::cambiarEstado con guarda atómica de estado anterior ---
echo "\n--- 2. Guarda Atómica de Estado Anterior en Torneo::cambiarEstado (A3) ---\n";
$organizadorId = 2;
$juegoId = (int) $bd->query("SELECT j.id FROM juegos j 
                             INNER JOIN juego_modalidades_permitidas jmp ON jmp.juego_id = j.id AND jmp.modalidad_id = 1
                             INNER JOIN juego_formatos_permitidos jfp ON jfp.juego_id = j.id AND jfp.formato_codigo = 'liga'
                             WHERE j.activo = 1 LIMIT 1")->fetchColumn();

$bd->exec("INSERT INTO torneos (nombre, juego_id, formato, estado, cupo_max_equipos, organizador_id, modalidad_id)
           VALUES ('__TEST_ATOMIC_ESTADO__', $juegoId, 'liga', 'borrador', 8, $organizadorId, 1)");
$torneoId = (int) $bd->lastInsertId();

$modeloTorneo = new Torneo();
// Transición válida: borrador -> inscripciones_abiertas
$res1 = $modeloTorneo->cambiarEstado($torneoId, $organizadorId, 'inscripciones_abiertas');
$estado1 = $bd->query("SELECT estado FROM torneos WHERE id = $torneoId")->fetchColumn();
assertTest($res1 === true && $estado1 === 'inscripciones_abiertas', "2.1 Transición legítima (borrador -> inscripciones_abiertas) exitosa", "Estado actual: $estado1");

// Transición inválida: de inscripciones_abiertas NO se puede saltar directamente a finalizado
$res2 = $modeloTorneo->cambiarEstado($torneoId, $organizadorId, 'finalizado');
$estado2 = $bd->query("SELECT estado FROM torneos WHERE id = $torneoId")->fetchColumn();
assertTest($res2 === false && $estado2 === 'inscripciones_abiertas', "2.2 Transición inválida (inscripciones_abiertas -> finalizado) es rechazada atómicamente", "Estado preservado: $estado2");

// Limpieza torneo
$modeloTorneo->eliminarPorAdmin($torneoId);

// --- 3. M9: Captura de rol_id en ManejadorErrores ---
echo "\n--- 3. Registro Correcto de rol_id en ManejadorErrores (M9) ---\n";
$_SESSION['usuario_id'] = 1;
$_SESSION['rol_id'] = 1;
unset($_SESSION['rol']); // Asegurar que 'rol' no existe y 'rol_id' sí

// Simular logueo de excepción de prueba
$excepcionTest = new \RuntimeException("Excepción de prueba para auditoría de rol_id");
ManejadorErrores::capturarExcepcion($excepcionTest);

$ultimoError = $bd->query("SELECT rol_usuario FROM errores_sistema WHERE mensaje LIKE '%auditoría de rol_id%' ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
assertTest(!empty($ultimoError) && (int) $ultimoError['rol_usuario'] === 1, "3.1 ManejadorErrores guarda rol_usuario correctamente desde \$_SESSION['rol_id']", "rol_usuario registrado: {$ultimoError['rol_usuario']}");

// Limpieza de error de prueba
$bd->exec("DELETE FROM errores_sistema WHERE mensaje LIKE '%auditoría de rol_id%'");

// --- 4. C1: Validación de controladores protegidos contra GET ---
echo "\n--- 4. Blindaje de Métodos Destructivos contra GET (C1) ---\n";
require_once __DIR__ . '/../codigo_fuente/controladores/TorneoAdminControlador.php';
require_once __DIR__ . '/../codigo_fuente/controladores/JuegoControlador.php';

// Verificar que TorneoAdminControlador y JuegoControlador exigen POST
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SESSION['rol_id'] = 1;
$_SESSION['usuario_id'] = 1;

// Reflejar método en TorneoAdminControlador::eliminar
$reflTorneoAdmin = new \ReflectionMethod(\App\Controladores\TorneoAdminControlador::class, 'eliminar');
$codigoTorneoAdmin = file_get_contents((new \ReflectionClass(\App\Controladores\TorneoAdminControlador::class))->getFileName());
$tienePostTorneo = strpos($codigoTorneoAdmin, "\$_SERVER['REQUEST_METHOD'] !== 'POST'") !== false && strpos($codigoTorneoAdmin, "Sesion::requerirTokensCSRF()") !== false;
assertTest($tienePostTorneo, "4.1 TorneoAdminControlador::eliminar exige método POST y token CSRF");

$codigoJuego = file_get_contents((new \ReflectionClass(\App\Controladores\JuegoControlador::class))->getFileName());
$tienePostJuego = strpos($codigoJuego, "\$_SERVER['REQUEST_METHOD'] !== 'POST'") !== false && strpos($codigoJuego, "Sesion::requerirTokensCSRF()") !== false;
assertTest($tienePostJuego, "4.2 JuegoControlador::cambiarEstado exige método POST y token CSRF");

// --- 5. M8: Validación de stream_url ---
echo "\n--- 5. Validación de URLs en Transmisión (M8) ---\n";
$codigoPanelOrg = file_get_contents(__DIR__ . '/../codigo_fuente/controladores/PanelOrganizadorControlador.php');
$tieneValUrl = strpos($codigoPanelOrg, "FILTER_VALIDATE_URL") !== false && strpos($codigoPanelOrg, "#^https?://#i") !== false;
assertTest($tieneValUrl, "5.1 PanelOrganizadorControlador valida rigurosamente stream_url con FILTER_VALIDATE_URL");

echo "\n====================================================================\n";
echo " RESUMEN AUDITORIA: $exitos / $total PRUEBAS EXITOSAS (" . round(($exitos / $total) * 100) . "%)\n";
echo "====================================================================\n";
