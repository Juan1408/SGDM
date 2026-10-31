<?php
/**
 * ============================================================================
 * CLASE MODELO: SeguridadAuth.php
 * ============================================================================
 * Propósito: Gestiona el Rate Limiting (Protección contra fuerza bruta) para
 *            los intentos de login, bloqueando IPs temporalmente.
 * Ubicación: codigo_fuente/modelos/SeguridadAuth.php
 * ============================================================================
 */

namespace App\Modelos;

require_once __DIR__ . '/Conexion.php';

class SeguridadAuth {

    private \PDO $bd;
    private int $maxIntentos = 5;
    private int $tiempoBloqueoMinutos = 5;

    public function __construct() {
        $this->bd = Conexion::getInstance()->getBD();
    }

    /**
     * Verifica si una IP está actualmente bloqueada
     */
    public function estaIpBloqueada(string $ip): bool {
        $stmt = $this->bd->prepare("SELECT bloqueado_hasta FROM intentos_login WHERE ip_origen = :ip");
        $stmt->execute([':ip' => $ip]);
        $registro = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($registro && $registro['bloqueado_hasta']) {
            $bloqueadoHasta = strtotime($registro['bloqueado_hasta']);
            if (time() < $bloqueadoHasta) {
                return true; // Aún está bloqueado
            } else {
                // El bloqueo expiró, limpiamos el registro
                $this->limpiarIntentos($ip);
            }
        }
        return false;
    }

    /**
     * Registra un intento fallido para una IP
     * Si supera el límite, bloquea la IP.
     */
    public function registrarIntentoFallido(string $ip): void {
        // Verificar si existe el registro
        $stmt = $this->bd->prepare("SELECT intentos FROM intentos_login WHERE ip_origen = :ip");
        $stmt->execute([':ip' => $ip]);
        $registro = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($registro) {
            $nuevosIntentos = $registro['intentos'] + 1;
            $bloqueadoHasta = null;

            // Si supera el máximo, calculamos la fecha de bloqueo
            if ($nuevosIntentos >= $this->maxIntentos) {
                $bloqueadoHasta = date('Y-m-d H:i:s', time() + ($this->tiempoBloqueoMinutos * 60));
            }

            $updateStmt = $this->bd->prepare("UPDATE intentos_login SET intentos = :intentos, ultimo_intento = CURRENT_TIMESTAMP, bloqueado_hasta = :bloqueado WHERE ip_origen = :ip");
            $updateStmt->execute([
                ':intentos' => $nuevosIntentos,
                ':bloqueado' => $bloqueadoHasta,
                ':ip' => $ip
            ]);
        } else {
            // Primer intento fallido
            $insertStmt = $this->bd->prepare("INSERT INTO intentos_login (ip_origen, intentos, ultimo_intento) VALUES (:ip, 1, CURRENT_TIMESTAMP)");
            $insertStmt->execute([':ip' => $ip]);
        }
    }

    /**
     * Limpia los intentos fallidos de una IP tras un login exitoso
     * o cuando expiró el tiempo de bloqueo.
     */
    public function limpiarIntentos(string $ip): void {
        $stmt = $this->bd->prepare("DELETE FROM intentos_login WHERE ip_origen = :ip");
        $stmt->execute([':ip' => $ip]);
    }
}
