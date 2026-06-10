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
require_once __DIR__ . '/controladores/ControladorUsuario.php';

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
} elseif ($ruta === '/torneos') {
    // Redirigir al login si no está autenticado (Middleware básico)
    if (!AdministradorSesion::estaAutenticado()) {
        header('Location: /login');
        exit;
    }

    /**
     * @var string $nombreUsuario Nombre completo del usuario en sesión.
     */
    $nombreUsuario = $_SESSION['nombre_completo'] ?? 'Usuario';

    // Vista de prueba básica para confirmar que la autenticación funciona
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Panel Principal - SGDM</title>
        <link rel="stylesheet" href="/publico/css/style.css">
    </head>
    <body>
        <div class="contenedor-pantalla" style="max-width: 600px;">
            <div class="tarjeta-autenticacion" style="text-align: center;">
                <div class="cabecera-autenticacion">
                    <h1>¡Bienvenido, <?php echo htmlspecialchars($nombreUsuario); ?>!</h1>
                    <p>Has iniciado sesión correctamente en el sistema SGDM 2.0</p>
                </div>
                
                <div style="margin: 30px 0; background: rgba(255,255,255,0.05); padding: 20px; border-radius: 8px; font-size: 0.9rem; color: var(--color-texto-secundario);">
                    El Módulo 1 (Autenticación, Seguridad y Roles) está completamente funcional. Las variables en el código se encuentran documentadas y los formularios están protegidos contra ataques CSRF y políticas de complejidad de contraseñas.
                </div>

                <a href="/logout" class="boton-primario" style="display: inline-block; text-decoration: none; width: auto; padding: 12px 24px;">Cerrar Sesión</a>
            </div>
        </div>
    </body>
    </html>
    <?php
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
