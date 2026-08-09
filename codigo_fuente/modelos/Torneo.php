<?php
/**
 * ============================================================================
 * CLASE MODELO: Torneo.php
 * ============================================================================
 * Propósito: Gestiona el acceso a datos para la tabla 'torneos'.
 * Ubicación: codigo_fuente/modelos/Torneo.php
 * ============================================================================
 */

require_once __DIR__ . '/Conexion.php';

class Torneo {
    private PDO $db;

    public function __construct() {
        $this->db = Conexion::obtenerConexion();
    }

    /**
     * Obtiene el listado general de torneos para la vista administrativa.
     */
    public function obtenerTodos(): array {
        $sql = "SELECT t.*, j.nombre AS juego_nombre, u.nombre_completo AS organizador_nombre
                FROM torneos t
                INNER JOIN juegos j ON t.juego_id = j.id
                INNER JOIN usuarios u ON t.organizador_id = u.id
                ORDER BY t.id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Cambia el estado de un torneo (borrador, inscripciones_abiertas, en_curso, finalizado, cancelado).
     */
    public function cambiarEstado(int $torneoId, string $nuevoEstado, int $usuarioId): bool {
        $sql = "UPDATE torneos SET estado = :estado WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':estado' => $nuevoEstado, ':id' => $torneoId]);
    }
}
