<?php
/**
 * ============================================================================
 * CLASE CONTROLADOR: RolControlador.php
 * ============================================================================
 * Propósito: Gestiona el catálogo de roles y la asignación de permisos (RBAC).
 * Ubicación: codigo_fuente/controladores/RolControlador.php
 * ============================================================================
 */

require_once __DIR__ . '/../ayudantes/Sesion.php';
require_once __DIR__ . '/../modelos/Rol.php';

class RolControlador {
    private Rol $rolModelo;

    public function __construct() {
        Sesion::requerirAdmin();
        $this->rolModelo = new Rol();
    }

    /**
     * Muestra el listado de roles y sus permisos asignados.
     * Ruta: index.php?c=rol&a=index
     */
    public function index(): void {
        $roles = $this->rolModelo->obtenerTodos();
        require_once __DIR__ . '/../vistas/admin/roles/index.html';
    }
}
