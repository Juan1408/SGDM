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
define('APP_NOMBRE', 'SGDM - ASCEND');

// URL Base del proyecto (Ajustar según la ruta local en XAMPP/WAMP o Docker)
define('URL_BASE', 'http://localhost/SGDM/codigo_fuente/publico/');

// Rutas absolutas en el sistema de archivos del servidor
define('RUTA_RAIZ', dirname(__DIR__) . '/');
define('RUTA_MODELOS', RUTA_RAIZ . 'modelos/');
define('RUTA_CONTROLADORES', RUTA_RAIZ . 'controladores/');
define('RUTA_VISTAS', RUTA_RAIZ . 'vistas/');
define('RUTA_AYUDANTES', RUTA_RAIZ . 'ayudantes/');
define('RUTA_PUBLICO', RUTA_RAIZ . 'publico/');
define('RUTA_SUBIDAS', RUTA_PUBLICO . 'img/subidas/');

// Zona horaria estándar para registros y auditoría
date_default_timezone_set('America/Montevideo');
