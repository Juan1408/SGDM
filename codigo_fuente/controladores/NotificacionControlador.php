<?php
/**
 * ============================================================================
 * CONTROLADOR: NotificacionControlador
 * ============================================================================
 * Acciones POST para marcar notificaciones como leidas desde la campana.
 * No acepta usuario_id desde el formulario: siempre usa el de la sesion.
 * ============================================================================
 */

namespace App\Controladores;

use App\Ayudantes\Sesion;
use App\Modelos\Notificacion;

require_once __DIR__ . '/../ayudantes/Sesion.php';
require_once __DIR__ . '/../modelos/Notificacion.php';

class NotificacionControlador {

    private Notificacion $modeloNotificacion;

    public function __construct() {
        $this->modeloNotificacion = new Notificacion();
    }

    /**
     * Marca una notificacion puntual. Solo POST, con CSRF y pertenencia.
     */
    public function marcarLeida(): void {
        $usuarioId = $this->exigirPostConSesion();
        Sesion::requerirTokensCSRF();

        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        if ($id !== false && (int) $id > 0) {
            $this->modeloNotificacion->marcarLeida((int) $id, $usuarioId);
        }

        $this->volver();
    }

    /**
     * Marca todas las notificaciones del usuario como leidas.
     */
    public function marcarTodas(): void {
        $usuarioId = $this->exigirPostConSesion();
        Sesion::requerirTokensCSRF();

        $this->modeloNotificacion->marcarTodasLeidas($usuarioId);

        $this->volver();
    }

    private function exigirPostConSesion(): int {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $this->base() . 'index.php');
            exit;
        }

        if (!Sesion::estaLogueado()) {
            header('Location: ' . $this->base() . 'index.php?c=auth&a=mostrarLogin');
            exit;
        }

        return (int) ($_SESSION['usuario_id'] ?? 0);
    }

    private function base(): string {
        return \defined('URL_BASE') ? URL_BASE : '/';
    }

    private function volver(): void {
        header('Location: ' . $this->destinoSeguro($_SERVER['HTTP_REFERER'] ?? ''));
        exit;
    }

    /**
     * Vuelve a la pagina de origen solo si pertenece al mismo sitio.
     * Si no, al panel del rol que hizo la peticion.
     */
    private function destinoSeguro(string $referer): string {
        $porDefecto = $this->base() . 'index.php';
        $rol = (int) ($_SESSION['rol_id'] ?? 0);
        if ($rol === 1) {
            $porDefecto = $this->base() . 'index.php?c=admin&a=dashboard';
        } elseif ($rol === 2) {
            $porDefecto = $this->base() . 'index.php?c=panelOrganizador&a=dashboard';
        } elseif ($rol === 3) {
            $porDefecto = $this->base() . 'index.php?c=panelJugador&a=dashboard';
        }

        if ($referer === '') {
            return $porDefecto;
        }

        $base = parse_url($this->base());
        $destino = parse_url($referer);

        if (!$base || !$destino || empty($base['host']) || empty($destino['host'])) {
            return $porDefecto;
        }

        $mismoOrigen = strcasecmp($base['scheme'] ?? '', $destino['scheme'] ?? '') === 0
            && strcasecmp($base['host'], $destino['host']) === 0
            && ($base['port'] ?? null) === ($destino['port'] ?? null);

        if (!$mismoOrigen) {
            return $porDefecto;
        }

        $rutaBase = $base['path'] ?? '/';
        $rutaDestino = $destino['path'] ?? '/';
        if (strpos($rutaDestino, $rutaBase) !== 0) {
            return $porDefecto;
        }

        return $referer;
    }
}
