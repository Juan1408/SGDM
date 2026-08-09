<?php
/**
 * ============================================================================
 * CLASE CONTROLADOR: AuditoriaControlador.php
 * ============================================================================
 * Propósito: Gestiona la visualización de registros de seguridad, logs de acceso
 *            y pistas de auditoría exigidas en Ciberseguridad y Sistemas Operativos.
 * Ubicación: codigo_fuente/controladores/AuditoriaControlador.php
 * ============================================================================
 */

require_once __DIR__ . '/../ayudantes/Sesion.php';
require_once __DIR__ . '/../modelos/Auditoria.php';

class AuditoriaControlador {
    private Auditoria $auditoriaModelo;

    public function __construct() {
        Sesion::requerirAdmin();
        $this->auditoriaModelo = new Auditoria();
    }

    /**
     * Muestra la tabla con los logs de acceso de usuarios.
     * Ruta: index.php?c=auditoria&a=accesos
     */
    public function accesos(): void {
        $logs = $this->auditoriaModelo->obtenerLogsAcceso();
        require_once __DIR__ . '/../vistas/admin/reportes/index.html';
    }

    /**
     * Muestra la tabla de auditoría de cambios sobre datos del sistema.
     * Ruta: index.php?c=auditoria&a=cambios
     */
    public function cambios(): void {
        $cambios = $this->auditoriaModelo->obtenerAuditoriaCambios();
        require_once __DIR__ . '/../vistas/admin/reportes/index.html';
    }
}
