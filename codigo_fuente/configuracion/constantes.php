<?php
/**
 * ============================================================================
 * ARCHIVO DE CONSTANTES GLOBALES: constantes.php
 * ============================================================================
 * Propósito: Define rutas del servidor, URLs base y constantes del sistema.
 * Ubicación: codigo_fuente/configuracion/constantes.php
 * ============================================================================
 */

// Nombre oficial de la aplicación
if (!defined('APP_NOMBRE')) {
    define('APP_NOMBRE', 'SGDM - ASCEND');
}

// URL Base del proyecto (Ajustar según la ruta local en XAMPP/WAMP o Docker)
if (!defined('URL_BASE')) {
    define('URL_BASE', 'http://localhost/SGDM/codigo_fuente/publico/');
}

// Rutas absolutas en el sistema de archivos del servidor
if (!defined('RUTA_RAIZ')) {
    define('RUTA_RAIZ', dirname(__DIR__) . '/');
}
if (!defined('RUTA_MODELOS')) {
    define('RUTA_MODELOS', RUTA_RAIZ . 'modelos/');
}
if (!defined('RUTA_CONTROLADORES')) {
    define('RUTA_CONTROLADORES', RUTA_RAIZ . 'controladores/');
}
if (!defined('RUTA_VISTAS')) {
    define('RUTA_VISTAS', RUTA_RAIZ . 'vistas/');
}
if (!defined('RUTA_AYUDANTES')) {
    define('RUTA_AYUDANTES', RUTA_RAIZ . 'ayudantes/');
}
if (!defined('RUTA_PUBLICO')) {
    define('RUTA_PUBLICO', RUTA_RAIZ . 'publico/');
}
if (!defined('RUTA_SUBIDAS')) {
    define('RUTA_SUBIDAS', RUTA_PUBLICO . 'img/subidas/');
}

// Zona horaria estándar para registros y auditoría
date_default_timezone_set('America/Montevideo');
