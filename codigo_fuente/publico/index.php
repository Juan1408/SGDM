<?php
/**
 * ============================================================================
 * FRONT CONTROLLER: index.php
 * ============================================================================
 * Propósito: Punto único de entrada a la aplicación web. Inicializa la sesión,
 *            carga la configuración y despacha la petición al controlador correspondiente.
 * Ubicación: codigo_fuente/publico/index.php
 * ============================================================================
 */

ob_start();

require_once __DIR__ . '/../configuracion/constantes.php';
require_once __DIR__ . '/../configuracion/base_de_datos.php';

require_once __DIR__ . '/../ayudantes/Sesion.php';
require_once __DIR__ . '/../modelos/Conexion.php'; // Requerido por el manejador
require_once __DIR__ . '/../core/ManejadorErrores.php';

use App\Ayudantes\Sesion;
use App\Core\ManejadorErrores;

// Registrar el Manejador de Errores Global
set_exception_handler([ManejadorErrores::class, 'capturarExcepcion']);
set_error_handler([ManejadorErrores::class, 'capturarErrorPhp']);

// ============================================================================
// POLÍTICAS HTTP DE SEGURIDAD (Security Headers)
// ============================================================================
require_once __DIR__ . '/../core/CabecerasSeguridad.php';
\App\Core\CabecerasSeguridad::aplicar();

//Definimos el controlador y la accion por defecto si no se especifican
$controlador = $_GET['c'] ?? 'auth';
$accion = $_GET['a'] ?? 'mostrarLogin';

$rutasPermitidas = [
    'admin' => ['dashboard', 'editarPerfil'],
    'alerta' => ['index', 'enviar'],
    'auth' => ['mostrarLogin', 'procesarLogin', 'mostrarRegistro', 'procesarRegistro', 'verificarEmail', 'logout'],
    'equipoAdmin' => ['index', 'ver', 'actualizarNombre'],
    'equipo' => ['crear', 'index', 'procesarCreacion', 'verPerfil', 'solicitarUnion', 'solicitudes', 'responderInvitacion', 'gestionar', 'responderSolicitudRecibida', 'invitarJugador', 'actualizarEquipo', 'expulsarMiembro', 'buscarJugadoresAjax', 'hacerCapitan', 'dejarEquipo'],
    'auditoria' => ['index'],
    'errorTracking' => ['dashboard', 'verDetalle', 'accionError'],
    'formato' => ['index', 'guardarEstados', 'obtenerPermitidasJSON'],
    'guardados' => ['index', 'guardar', 'quitar'],
    'juego' => ['index', 'crear', 'guardar', 'cambiarEstado', 'editar', 'actualizar'],
    'jugador' => ['index'],
    'perfil' => ['jugador', 'organizador'],
    'organizador' => ['index', 'responderSolicitud'],
    'panelJugador' => ['dashboard', 'perfil', 'actualizarPerfil', 'editarPerfil'],
    'panelOrganizador' => ['dashboard', 'crear', 'misTorneos', 'resultados', 'calendario', 'historial', 'perfil', 'editarPerfil', 'guardarPerfil', 'administrar', 'cambiarEstado', 'responderInscripcion', 'quitarParticipante', 'generarFixture', 'programarEncuentro', 'agregarActividad', 'editarActividad', 'eliminarActividad', 'editarTorneo', 'guardarResultado', 'corregirResultado', 'guardarEtapa'],
    'inicio' => ['index', 'enConstruccion', 'contacto', 'juegos', 'nosotros', 'rankings'],
    'torneo' => ['catalogoPublico', 'detalle', 'inscribirse', 'cancelarInscripcion'],
    'politicaContrasena' => ['index', 'guardar'],
    'resultadoGlobal' => ['index'],
    'torneoAdmin' => ['index', 'editar', 'actualizar', 'eliminar'],
    'torneoHistorial' => ['index'],
    'usuario' => ['index', 'ver', 'cambiarEstadoUsuario', 'editarUsuario', 'verComo', 'restaurarSesion', 'actualizarUsuario', 'eliminarUsuario']
];

if (!isset($rutasPermitidas[$controlador]) ||
    !in_array($accion, $rutasPermitidas[$controlador], true)) {
    http_response_code(404);
    exit('Ruta no disponible');
}

// ============================================================================
// BLOQUEO DE ESCRITURA EN MODO SUPLANTACIÓN (READ-ONLY MODE)
// ============================================================================
Sesion::iniciarSesion();
if (isset($_SESSION['admin_antes_suplantar']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Permitimos unicamente la accion de restaurar la sesion
    if (!($controlador === 'usuario' && $accion === 'restaurarSesion')) {
        $_SESSION['mensaje'] = "Acción bloqueada: Modo Suplantación es de Solo Lectura.";
        $_SESSION['mensaje_tipo'] = "error";

        //Destino por defecto: el panel del rol suplantado.
        $destinoBloqueo = URL_BASE . 'index.php';
        $rolSuplantado = (int) ($_SESSION['rol_id'] ?? 0);
        if ($rolSuplantado === 1) {
            $destinoBloqueo = URL_BASE . 'index.php?c=admin&a=dashboard';
        } elseif ($rolSuplantado === 2) {
            $destinoBloqueo = URL_BASE . 'index.php?c=panelOrganizador&a=dashboard';
        } elseif ($rolSuplantado === 3) {
            $destinoBloqueo = URL_BASE . 'index.php?c=panelJugador&a=dashboard';
        }

        //Solo aceptamos volver a la misma página si el Referer pertenece al mismo origen.
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if ($referer !== '') {
            $esquemaBase = parse_url(URL_BASE, PHP_URL_SCHEME);
            $hostBase = parse_url(URL_BASE, PHP_URL_HOST);
            $puertoBase = parse_url(URL_BASE, PHP_URL_PORT);

            $esquemaRef = parse_url($referer, PHP_URL_SCHEME);
            $hostRef = parse_url($referer, PHP_URL_HOST);
            $puertoRef = parse_url($referer, PHP_URL_PORT);

            if ($hostBase && $hostRef
                && $esquemaBase && $esquemaRef
                && strcasecmp($esquemaBase, $esquemaRef) === 0
                && strcasecmp($hostBase, $hostRef) === 0
                && $puertoBase === $puertoRef) {
                $destinoBloqueo = $referer;
            }
        }

        header("Location: " . $destinoBloqueo);
        exit;
    }
}

//Ponemos en mayuscula la primera letra para que coincida con el nombre del archivo controlador
$nombreClaseControlador = ucfirst($controlador) . 'Controlador'; //ucfirst pone la primera letra en mayuscula

//Ruta del controlador
$rutaArchivo = __DIR__ . '/../controladores/' . $nombreClaseControlador . '.php';

//Verificamos si el archivo del controlador existe antes de cargarlo
if (file_exists($rutaArchivo)) {
    //Si existe cargamos el archivo
    require_once $rutaArchivo;

    //Como usamos namespace App\Controladores la clase real se llama completa 
    $claseCompleta = "App\Controladores\\" . $nombreClaseControlador;

    //Creamos un objeto del controlador $instancia = new App\Controladores\AuthControlador())
    $instancia = new $claseCompleta();

    //Verificamos que la accion exista
    if (method_exists($instancia, $accion)){
        $instancia->$accion();
    } else {
        http_response_code(404);
        echo "<h1>Error 404</h1><p>La acción no existe en este controlador.</p>";
        exit;
    }
} else {
    http_response_code(404);
    echo "<h1>Error 404</h1><p>El controlador no existe.</p>";
    exit;
}

// ============================================================================
// INYECCIÓN GLOBAL: Banner de Suplantación (Modo "Ver Como")
// ============================================================================
if (isset($_SESSION['admin_antes_suplantar'])) {
    $urlRestaurar = (\defined('URL_BASE') ? \URL_BASE : '/') . "index.php?c=usuario&a=restaurarSesion";
    $rutaScript = (\defined('URL_BASE') ? \URL_BASE : '/') . "js/admin/suplantacion.js";
    $tokenRestaurar = Sesion::generarTokenCSRF();

    // Inyectamos la URL, el token CSRF y el enlace al archivo estático JS
    echo "<script>window.URL_RESTAURAR_SESION = " . json_encode($urlRestaurar) . ";"
        . "window.CSRF_TOKEN_SUPLANTACION = " . json_encode($tokenRestaurar) . ";</script>";
    echo "<script src=\"" . $rutaScript . "\"></script>";
}
