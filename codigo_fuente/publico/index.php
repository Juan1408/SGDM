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

require_once __DIR__ . '/../configuracion/constantes.php';
require_once __DIR__ . '/../configuracion/base_de_datos.php';

require_once __DIR__ . '/../ayudantes/Sesion.php';
use App\Ayudantes\Sesion;

//Definimos el controlador y la accion por defecto si no se especifican
$controlador = $_GET['c'] ?? 'auth';
$accion = $_GET['a'] ?? 'mostrarLogin';

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
        echo "<h1>Error 404</h1><p>La accion no existe en este controlador</p>";
    }
} else {
    echo "<h1>Error 404</h1><p>El controlador no existe</p>";
}
