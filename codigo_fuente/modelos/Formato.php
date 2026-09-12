<?php

namespace App\Modelos;

use App\Modelos\Conexion;
use \PDO;
require_once __DIR__ . '/Conexion.php';

class Formato {
    private $bd;

    public function __construct() {
        $this->bd = Conexion::getInstance()->getBD();
    }

    public static function obtenerInstancia() {
        return new self();
    }

    public function obtenerTodas() {
        $sql = "SELECT * FROM formatos_torneo ORDER BY codigo ASC";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerActivas() {
        $sql = "SELECT * FROM formatos_torneo WHERE activo = 1 ORDER BY codigo ASC";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cambiarEstado(string $codigo, int $activo) {
        $sql = "UPDATE formatos_torneo SET activo = :activo WHERE codigo = :codigo";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([
            ':activo' => $activo,
            ':codigo' => $codigo
        ]);
    }
}
