<?php
/**
 * Punto de entrada principal y enrutador de la aplicación (index.php)
 * 
 * Recibe todas las peticiones HTTP y las delega al controlador y método
 * correspondiente en base a la ruta solicitada.
 */

// Carga automática mínima de archivos del núcleo, modelos y controladores
require_once __DIR__ . '/nucleo/AdministradorSesion.php';
require_once __DIR__ . '/nucleo/ControladorBase.php';
// Módulo 1: Autenticación
require_once __DIR__ . '/controladores/ControladorUsuario.php';
// Módulo 2: Equipos y Participantes
require_once __DIR__ . '/controladores/ControladorEquipo.php';
require_once __DIR__ . '/controladores/ControladorParticipante.php';
// Módulo 3: Torneos configurables
require_once __DIR__ . '/controladores/TorneoControlador.php';
// Módulo 4: Resultados y posiciones
require_once __DIR__ . '/controladores/EnfrentamientoControlador.php';
// Módulo 5: Roles y permisos
require_once __DIR__ . '/controladores/RolControlador.php';

// Iniciar sesión de forma segura
AdministradorSesion::iniciar();

/**
 * @var string $ruta URI solicitada procesada sin parámetros query.
 */
$ruta = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

/**
 * @var string $metodo Método HTTP de la petición (GET o POST).
 */
$metodo = $_SERVER['REQUEST_METHOD'];

// =============================================================================
// ENRUTAMIENTO DE PETICIONES
// =============================================================================

if ($ruta === '/' || $ruta === '/login') {
    $controlador = new ControladorUsuario();
    if ($metodo === 'POST') {
        $controlador->login();
    } else {
        $controlador->mostrarLogin();
    }
} elseif ($ruta === '/registro') {
    $controlador = new ControladorUsuario();
    if ($metodo === 'POST') {
        $controlador->registro();
    } else {
        $controlador->mostrarRegistro();
    }
} elseif ($ruta === '/logout') {
    $controlador = new ControladorUsuario();
    $controlador->logout();

// =============================================================================
// RUTAS DEL MÓDULO 2: EQUIPOS Y PARTICIPANTES
// =============================================================================
} elseif ($ruta === '/equipos') {
    (new ControladorEquipo())->listar();
} elseif ($ruta === '/equipos/crear') {
    (new ControladorEquipo())->crear();
} elseif ($ruta === '/equipos/ver') {
    (new ControladorEquipo())->ver();
} elseif ($ruta === '/equipos/unirse') {
    (new ControladorEquipo())->unirse();
} elseif ($ruta === '/equipos/solicitudes/procesar') {
    (new ControladorEquipo())->procesarSolicitud();
} elseif ($ruta === '/participantes/inscribir') {
    (new ControladorParticipante())->inscribir();

// =============================================================================
// RUTAS DEL MÓDULO 1: PANEL PRINCIPAL
// =============================================================================
} elseif ($ruta === '/torneos') {
    (new TorneoControlador())->listar();
} elseif ($ruta === '/torneos/crear') {
    (new TorneoControlador())->crear();
} elseif ($ruta === '/torneos/ver') {
    (new TorneoControlador())->ver();
} elseif ($ruta === '/torneos/detalle') {
    (new TorneoControlador())->detalle();
} elseif ($ruta === '/torneos/resultados') {
    (new EnfrentamientoControlador())->resultados();
} elseif ($ruta === '/torneos/resultados/guardar') {
    (new EnfrentamientoControlador())->guardarResultado();
} elseif ($ruta === '/roles') {
    (new RolControlador())->listar();
} elseif ($ruta === '/roles/panel') {
    (new RolControlador())->panel();
} else {
    // Página no encontrada (404)
    http_response_code(404);
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>404 - No Encontrado</title>
        <link rel="stylesheet" href="/publico/css/style.css">
    </head>
    <body>
        <div class="contenedor-pantalla" style="text-align: center;">
            <div class="tarjeta-autenticacion">
                <h1 style="font-size: 3rem; color: #ef4444; font-family: var(--fuente-titulo);">404</h1>
                <p style="margin-bottom: 20px; color: var(--color-texto-secundario);">La página que buscas no existe en el sistema.</p>
                <a href="/" class="boton-primario" style="display: inline-block; text-decoration: none;">Ir al Inicio</a>
            </div>
        </div>
    </body>
    </html>
    <?php
}
