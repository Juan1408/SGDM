<?php
/**
 * ============================================================================
 * CLASE MODELO: Newsletter.php
 * ============================================================================
 * Propósito: Gestiona la tabla 'newsletter_subscriptores' (boletín público).
 * Ubicación: codigo_fuente/modelos/Newsletter.php
 * ============================================================================
 */

namespace App\Modelos;
use PDO;
require_once __DIR__ . '/Conexion.php';

class Newsletter {
    private $bd;

    public function __construct(){
        //Patron Singleton: una unica conexion a la base de datos
        $this->bd = Conexion::getInstance()->getBD();
    }

    /**
     * Suscribe un correo al boletin.
     * Devuelve: 'nuevo' (primera vez), 'reactivado' (se habia dado de baja)
     *           o 'ya_suscripto' (ya estaba activo). Lanza excepcion si falla la BD.
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
}
