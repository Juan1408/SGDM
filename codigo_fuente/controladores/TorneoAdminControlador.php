<?php
/**
 * ============================================================================
 * CLASE CONTROLADOR: TorneoAdminControlador.php
 * ============================================================================
 * Propósito: Gestiona la supervisión general de torneos desde el panel de administración.
 * Ubicación: codigo_fuente/controladores/TorneoAdminControlador.php
 * ============================================================================
 */

require_once __DIR__ . '/../ayudantes/Sesion.php';
require_once __DIR__ . '/../modelos/Torneo.php';

class TorneoAdminControlador {
    private Torneo $torneoModelo;

    public function __construct() {
        Sesion::requerirAdmin();
        $this->torneoModelo = new Torneo();
    }

    /**
     * Muestra el listado de todos los torneos en el sistema.
     * Ruta: index.php?c=torneoAdmin&a=index
     */
    public function index(): void {
        $torneos = $this->torneoModelo->obtenerTodos();
        require_once __DIR__ . '/../vistas/admin/torneos/index.html';
    }
}
