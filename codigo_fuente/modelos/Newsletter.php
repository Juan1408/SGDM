<?php
/**
 * ============================================================================
 * MODELO: Newsletter
 * ============================================================================
 * Gestiona la tabla existente `newsletter_subscriptores` (boletin publico).
 * ============================================================================
 */

namespace App\Modelos;

use PDO;

require_once __DIR__ . '/Conexion.php';

class Newsletter {
    private $bd;

    public function __construct() {
        $this->bd = Conexion::getInstance()->getBD();
    }

    /**
     * Suscribe un correo al boletin.
     * Devuelve: 'nuevo' (primera vez), 'reactivado' (se habia dado de baja)
     *           o 'ya_suscripto' (ya estaba activo). Lanza excepcion si falla.
     */
    public function suscribir(string $email): string {
        $stmt = $this->bd->prepare("SELECT id, activo FROM newsletter_subscriptores WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $existente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existente) {
            if ((int) $existente['activo'] === 1) {
                return 'ya_suscripto';
            }
            $upd = $this->bd->prepare("UPDATE newsletter_subscriptores SET activo = 1 WHERE id = :id");
            $upd->execute([':id' => $existente['id']]);
            return 'reactivado';
        }

        $ins = $this->bd->prepare("INSERT INTO newsletter_subscriptores (email, activo) VALUES (:email, 1)");
        $ins->execute([':email' => $email]);
        return 'nuevo';
    }

    /**
     * Cantidad de correos activos. Se usa en el panel de administracion.
     */
    public function contarActivos(): int {
        $stmt = $this->bd->query("SELECT COUNT(*) FROM newsletter_subscriptores WHERE activo = 1");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Ultimos correos activos, para la vista de solo lectura del administrador.
     */
    public function listarActivos(int $limite = 20): array {
        $limite = max(1, min(100, $limite));
        $stmt = $this->bd->prepare(
            "SELECT id, email, fecha_suscripcion
             FROM newsletter_subscriptores
             WHERE activo = 1
             ORDER BY fecha_suscripcion DESC, id DESC
             LIMIT :limite"
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
