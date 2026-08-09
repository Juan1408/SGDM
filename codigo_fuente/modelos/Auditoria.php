<?php
/**
 * ============================================================================
 * CLASE MODELO: Auditoria.php
 * ============================================================================
 * Propósito: Gestiona el registro y consulta inmutable de logs de seguridad
 *            y trazabilidad (tablas 'logs_acceso' y 'auditoria_cambios').
 * Ubicación: codigo_fuente/modelos/Auditoria.php
 * ============================================================================
 */

require_once __DIR__ . '/Conexion.php';

class Auditoria {
    private PDO $db;

    public function __construct() {
        $this->db = Conexion::obtenerConexion();
    }

    /**
     * Obtiene los últimos logs de acceso y autenticación en el sistema.
     */
    public function obtenerLogsAcceso(int $limite = 50): array {
        $sql = "SELECT l.*, u.email, u.nombre_completo 
                FROM logs_acceso l
                LEFT JOIN usuarios u ON l.usuario_id = u.id
                ORDER BY l.id DESC LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtiene los registros de auditoría de cambios sobre datos sensibles.
     */
    public function obtenerAuditoriaCambios(int $limite = 50): array {
        $sql = "SELECT a.*, u.nombre_completo AS usuario_nombre 
                FROM auditoria_cambios a
                LEFT JOIN usuarios u ON a.usuario_id = u.id
                ORDER BY a.id DESC LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
