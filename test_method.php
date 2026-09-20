<?php
require_once 'c:/xampp/htdocs/SGDM/codigo_fuente/controladores/UsuarioControlador.php';
$c = new App\Controladores\UsuarioControlador();
var_dump(method_exists($c, 'restaurarSesion'));
var_dump(get_class_methods($c));
