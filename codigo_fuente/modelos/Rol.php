<?php
/**
 * ============================================================================
 * CLASE MODELO: Rol.php
 * ============================================================================
 * Propósito: Gestiona el acceso a datos para las tablas 'roles' y 'rol_permisos'.
 * Ubicación: codigo_fuente/modelos/Rol.php
 * ============================================================================
 */

require_once __DIR__ . '/Conexion.php';

class Rol {
    private PDO $db;

    public function __construct() {
        $this->db = Conexion::obtenerConexion();
    }

    /**
     * Obtiene el listado de todos los roles del sistema.
     */
    public function obtenerTodos(): array {
        $sql = "SELECT * FROM roles ORDER BY id ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene los permisos asociados a un rol específico.
     */
    public function obtenerPermisosPorRol(int $rolId): array {
        $sql = "SELECT p.* 
                FROM permisos p
                INNER JOIN rol_permisos rp ON p.id = rp.permiso_id
                WHERE rp.rol_id = :rol_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':rol_id' => $rolId]);
        return $stmt->fetchAll();
    }
}
