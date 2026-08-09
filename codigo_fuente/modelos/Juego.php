<?php
/**
 * ============================================================================
 * CLASE MODELO: Juego.php
 * ============================================================================
 * Propósito: Gestiona el catálogo de deportes, disciplinas y juegos electrónicos.
 * Ubicación: codigo_fuente/modelos/Juego.php
 * ============================================================================
 */

require_once __DIR__ . '/Conexion.php';

class Juego {
    private PDO $db;

    public function __construct() {
        $this->db = Conexion::obtenerConexion();
    }

    /**
     * Obtiene todas las disciplinas registradas.
     */
    public function obtenerTodos(): array {
        $sql = "SELECT j.*, m.nombre AS modalidad_nombre, s.nombre AS sistema_puntuacion_nombre
                FROM juegos j
                LEFT JOIN modalidades m ON j.modalidad_id = m.id
                LEFT JOIN sistemas_puntuacion s ON j.sistema_puntuacion_id = s.id
                ORDER BY j.nombre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
