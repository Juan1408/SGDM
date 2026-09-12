<?php
/**
 * Modelo para las Modalidades (Individual/Equipos)
 */

namespace App\Modelos;

use \PDO;
require_once __DIR__ . '/Conexion.php';

class Modalidad {
    private $bd;

    public function __construct() {
        $this->bd = Conexion::getInstance()->getBD();
    }

    public function obtenerTodas() {
        $sql = "SELECT * FROM modalidades ORDER BY id ASC";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
