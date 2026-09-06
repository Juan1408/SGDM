<?php

require_once '../modelos/Conexion.php';

//Como usamos el 'use' estamos acortando el camino para no escribir la ruta completa cada vez que llamamos a la clase
use App\Modelos\Conexion;

echo "<h1>Prueba de conexion a la Base de Datos</h1>";

try {
    $conexion = Conexion::getInstance();

    $bd = $conexion->getBD(); //Llamamos al metodo que creamos en Conexion

    echo "<p style='color:green; font-weight:bold;'>Conexion Exitosa</p>";
} catch (Exception $e){
    echo "<p style='color:red; font-weight:bold;'> Error: " . $e->getMessage() . "</p>";
}