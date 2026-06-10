<?php
require_once __DIR__ . '/../nucleo/ConexionBD.php';

/**
 * Clase RepositorioModalidad
 *
 * Gestiona la recuperación de los catálogos de Modalidades (individual / equipos).
 */
class RepositorioModalidad {
    /** @var PDO $conexion Conexión activa a la base de datos. */
    private PDO $conexion;

    public function __construct() {
        $this->conexion = ConexionBD::obtenerInstancia();
    }

    /**
     * Obtiene todas las modalidades disponibles.
     *
     * @return array[] Colección de arrays asociativos con id y nombre.
     */
    public function obtenerTodas(): array {
        /** @var string $sql Consulta de modalidades. */
        $sql = "SELECT id, nombre, descripcion FROM modalidades ORDER BY id ASC";
        return $this->conexion->query($sql)->fetchAll();
    }
}
