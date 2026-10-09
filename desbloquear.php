<?php
require_once __DIR__ . '/codigo_fuente/modelos/Conexion.php';
$bd = App\Modelos\Conexion::getInstance()->getBD();
$bd->query("TRUNCATE TABLE intentos_login");
echo "✅ Tabla de intentos_login limpiada. Ya puedes volver a intentar loguearte.";
