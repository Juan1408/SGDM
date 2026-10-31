<?php
/**
 * ============================================================================
 * CLASE MODELO: MensajeContacto.php
 * ============================================================================
 * Propósito: Entrega los mensajes del formulario de contacto público a los
 *            administradores usando la tabla existente 'notificaciones'
 *            (no requiere tablas nuevas). Cada administrador activo recibe una
 *            notificación de tipo 'info' con el nombre, el correo y el mensaje.
 * Ubicación: codigo_fuente/modelos/MensajeContacto.php
 * ============================================================================
 */

namespace App\Modelos;
use PDO;
require_once __DIR__ . '/Conexion.php';

class MensajeContacto {
    private $bd;

    public function __construct(){
        //Patron Singleton: una unica conexion a la base de datos
        $this->bd = Conexion::getInstance()->getBD();
    }

    /**
     * Registra el mensaje como notificacion para cada administrador activo (rol 1).
     * Devuelve a cuantos administradores se les entrego. Lanza excepcion si falla la BD.
     */
    public function enviarAAdministradores(string $nombre, string $email, string $mensaje): int {
        $stmt = $this->bd->prepare("SELECT id FROM usuarios WHERE rol_id = 1 AND esta_activo = 1");
        $stmt->execute();
        $admins = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($admins)) {
            throw new \RuntimeException('No hay administradores activos para recibir el mensaje de contacto.');
        }

        $titulo = 'Mensaje de contacto de ' . $nombre;
        if (function_exists('mb_substr')) {
            $titulo = mb_substr($titulo, 0, 255, 'UTF-8');
        }
        $cuerpo = 'De: ' . $nombre . ' <' . $email . '>' . "\n\n" . $mensaje;

        $this->bd->beginTransaction();
        try {
            $ins = $this->bd->prepare(
                "INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje) VALUES (?, 'info', ?, ?)"
            );
            foreach ($admins as $adminId) {
                $ins->execute([$adminId, $titulo, $cuerpo]);
            }
            $this->bd->commit();
        } catch (\Throwable $th) {
            if ($this->bd->inTransaction()) {
                $this->bd->rollBack();
            }
            throw $th;
        }

        return count($admins);
    }
}
