<?php

// Retornamos un arreglo asociativo con las credenciales de XAMPP/MYSQL
return [
    'host' => 'localhost', //El servidor donde esta la base de datos, actualmente nuestra pc
    'bdnombre' => 'ascend', //El nombre de la base de datos que creamos en xampp, es el nombre exacto que usamos
    'usuario' => 'root', //El usuario de la base de datos, por defecto root en xampp
    'contrasena' => '', //La contraseña de la base de datos, por defecto vacia en xampp
    'charset' => 'utf8mb4' //El charset de la base de datos, utf8mb4 es el charset que usamos
];