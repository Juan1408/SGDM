<?php
/**
 * ============================================================================
 * MODELO: Alerta
 * ============================================================================
 * Propósito: Gestiona la inserción masiva de notificaciones globales en la 
 *            tabla `notificaciones` para enviar comunicados a los usuarios.
 * ============================================================================
 */

namespace App\Modelos;

use PDO;
use PDOException;

require_once __DIR__ . '/Conexion.php';
require_once __DIR__ . '/Usuario.php';

class Alerta {
    private $db;

    public function __construct() {
        $this->db = Conexion::getInstance()->getBD();
    }

    /**
     * Envía una notificación masiva a una audiencia específica
     * 
     * @param string $titulo
     * @param string $mensaje
     * @param string $tipo (info, exito, advertencia, error)
     * @param string $audiencia ('todos', 'jugador', 'organizador')
     * @return array ['exito' => bool, 'mensaje' => string, 'enviados' => int]
     */
    public function enviarAlertaGlobal($titulo, $mensaje, $tipo, $audiencia) {
        try {
            // 1. Obtener los IDs de los usuarios objetivo usando los nombres correctos de columna
            $sqlUsuarios = "SELECT id FROM usuarios WHERE esta_activo = 1";
            $params = [];
            
            if ($audiencia === 'jugador') {
                $sqlUsuarios .= " AND rol_id = 3"; // Jugador
            } elseif ($audiencia === 'organizador') {
                $sqlUsuarios .= " AND rol_id = 2"; // Organizador
            }

            $stmt = $this->db->prepare($sqlUsuarios);
            $stmt->execute($params);
            $usuarios = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (empty($usuarios)) {
                return [
                    'exito' => false, 
                    'mensaje' => 'No se encontraron usuarios activos en la audiencia seleccionada.',
                    'enviados' => 0
                ];
            }

            // 2. Preparar la inserción masiva
            // Para no saturar el servidor si hay miles de usuarios, usamos sentencias preparadas en transacción
            $this->db->beginTransaction();

            $sqlInsert = "INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje) VALUES (?, ?, ?, ?)";
            $stmtInsert = $this->db->prepare($sqlInsert);

            $contador = 0;
            foreach ($usuarios as $usuarioId) {
                $stmtInsert->execute([$usuarioId, $tipo, $titulo, $mensaje]);
                $contador++;
            }

            $this->db->commit();

            return [
                'exito' => true,
                'mensaje' => "Alerta enviada correctamente a $contador usuarios.",
                'enviados' => $contador
            ];

        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Error en Alerta::enviarAlertaGlobal - " . $e->getMessage());
            return [
                'exito' => false,
                'mensaje' => 'Ocurrió un error en la base de datos al enviar la alerta.',
                'enviados' => 0
            ];
        }
    }
}
