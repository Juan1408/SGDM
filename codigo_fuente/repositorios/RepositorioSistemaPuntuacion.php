<?php
require_once __DIR__ . '/../nucleo/ConexionBD.php';

/**
 * Clase RepositorioSistemaPuntuacion
 *
 * Gestiona la recuperación de los sistemas de puntuación predefinidos del catálogo.
 */
class RepositorioSistemaPuntuacion {
    /** @var PDO $conexion Conexión activa a la base de datos. */
    private PDO $conexion;

    public function __construct() {
        $this->conexion = ConexionBD::obtenerInstancia();
    }

    /**
     * Obtiene todos los sistemas de puntuación disponibles.
     *
     * @return array[] Colección con id, nombre, puntos y descripción.
     */
    public function obtenerTodos(): array {
        /** @var string $sql Consulta del catálogo. */
        $sql = "SELECT id, nombre, puntos_victoria, puntos_empate, puntos_derrota, descripcion
                FROM sistemas_puntuacion
                ORDER BY id ASC";
        return $this->conexion->query($sql)->fetchAll();
    }
}
