<?php

namespace App\Modelos;

use PDO;

class ErrorSistema {
    private $bd;

    public function __construct() {
        $this->bd = Conexion::getInstance()->getBD();
    }

    /**
     * Obtiene el listado agrupado de errores con filtros avanzados
     */
    public function obtenerErroresAgrupados($estado = 'todos', $rol = 'todos', $tipo = 'todos') {
        $sql = "SELECT e.id, e.hash_error, e.tipo_error, e.mensaje, e.archivo, e.linea, 
                       e.cantidad_ocurrencias, e.primera_vez, e.ultima_vez, e.estado,
                       u.email as usuario_email, e.rol_usuario
                FROM errores_sistema e
                LEFT JOIN usuarios u ON e.usuario_id = u.id
                WHERE 1=1";
        
        $params = [];

        if ($estado !== 'todos') {
            $sql .= " AND e.estado = :estado";
            $params[':estado'] = $estado;
        }
        
        if ($rol !== 'todos') {
            $sql .= " AND e.rol_usuario = :rol";
            $params[':rol'] = $rol;
        }

        if ($tipo !== 'todos') {
            $sql .= " AND e.tipo_error = :tipo";
            $params[':tipo'] = $tipo;
        }

        // Ordenamos: primero los no resueltos, luego por última vez
        $sql .= " ORDER BY FIELD(e.estado, 'nuevo', 'revision', 'resuelto'), e.ultima_vez DESC";

        $stmt = $this->bd->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todos los detalles de un error específico
     */
    public function obtenerDetalleError($id) {
        $sql = "SELECT e.*, u.email as usuario_email 
                FROM errores_sistema e
                LEFT JOIN usuarios u ON e.usuario_id = u.id
                WHERE e.id = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cambia el estado del error y guarda los detalles de resolución
     */
    public function cambiarEstado($id, $estado, $detalles = null) {
        $sql = "UPDATE errores_sistema SET estado = :estado, detalles_resolucion = :detalles WHERE id = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([
            ':estado' => $estado,
            ':detalles' => $detalles,
            ':id' => $id
        ]);
    }

    /**
     * Elimina un registro de error permanentemente
     */
    public function eliminarError($id) {
        $sql = "DELETE FROM errores_sistema WHERE id = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
