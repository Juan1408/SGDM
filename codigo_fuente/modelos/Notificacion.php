<?php
/**
 * ============================================================================
 * MODELO: Notificacion
 * ============================================================================
 * Bandeja de entrada personal. Lee y actualiza la tabla `notificaciones`, que
 * ya alimenta el envio masivo del administrador (modelo Alerta).
 *
 * Toda consulta filtra por usuario_id: una notificacion solo puede leerse o
 * marcarse desde la cuenta a la que pertenece.
 * ============================================================================
 */

namespace App\Modelos;

use PDO;
use PDOException;

require_once __DIR__ . '/Conexion.php';

class Notificacion {
    private $db;

    public function __construct() {
        $this->db = Conexion::getInstance()->getBD();
    }

    /**
     * Ultimas notificaciones del usuario, de la mas reciente a la mas antigua.
     */
    public function obtenerRecientes(int $usuarioId, int $limite = 8): array {
        if ($usuarioId <= 0) {
            return [];
        }

        $limite = max(1, min(50, $limite));

        $sql = "SELECT id, tipo, titulo, mensaje, leido, creado_en
                FROM notificaciones
                WHERE usuario_id = :usuario
                ORDER BY creado_en DESC, id DESC
                LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':usuario', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cantidad de notificaciones sin leer del usuario.
     */
    public function contarNoLeidas(int $usuarioId): int {
        if ($usuarioId <= 0) {
            return 0;
        }

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM notificaciones WHERE usuario_id = :usuario AND leido = 0"
        );
        $stmt->execute([':usuario' => $usuarioId]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Marca una notificacion como leida solo si pertenece al usuario.
     * Devuelve false cuando el id no existe o es de otra cuenta.
     */
    public function marcarLeida(int $id, int $usuarioId): bool {
        if ($id <= 0 || $usuarioId <= 0) {
            return false;
        }

        try {
            $stmt = $this->db->prepare(
                "UPDATE notificaciones
                 SET leido = 1, fecha_lectura = COALESCE(fecha_lectura, CURRENT_TIMESTAMP)
                 WHERE id = :id AND usuario_id = :usuario"
            );
            $stmt->execute([':id' => $id, ':usuario' => $usuarioId]);

            if ($stmt->rowCount() > 0) {
                return true;
            }

            //Si ya estaba leida, MySQL informa 0 filas afectadas: verificamos pertenencia.
            $stmtCheck = $this->db->prepare(
                "SELECT 1 FROM notificaciones WHERE id = :id AND usuario_id = :usuario LIMIT 1"
            );
            $stmtCheck->execute([':id' => $id, ':usuario' => $usuarioId]);

            return $stmtCheck->fetchColumn() !== false;
        } catch (PDOException $e) {
            error_log('Error en Notificacion::marcarLeida - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Marca todas las notificaciones pendientes del usuario.
     * Devuelve cuantas se actualizaron.
     */
    public function marcarTodasLeidas(int $usuarioId): int {
        if ($usuarioId <= 0) {
            return 0;
        }

        try {
            $stmt = $this->db->prepare(
                "UPDATE notificaciones
                 SET leido = 1, fecha_lectura = COALESCE(fecha_lectura, CURRENT_TIMESTAMP)
                 WHERE usuario_id = :usuario AND leido = 0"
            );
            $stmt->execute([':usuario' => $usuarioId]);

            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log('Error en Notificacion::marcarTodasLeidas - ' . $e->getMessage());
            return 0;
        }
    }
}
