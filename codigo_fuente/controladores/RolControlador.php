<?php
require_once __DIR__ . '/../nucleo/ControladorBase.php';
require_once __DIR__ . '/../nucleo/AdministradorSesion.php';
require_once __DIR__ . '/../repositorios/RepositorioRol.php';

class RolControlador extends ControladorBase {
    private RepositorioRol $repositorio;

    public function __construct() {
        $this->repositorio = new RepositorioRol();
    }

    public function listar(): void {
        AdministradorSesion::iniciar();

        $roles = $this->repositorio->obtenerTodos();
        $rolActual = $_SESSION['rol_id'] ?? null;

        $this->renderizarVista('roles/listado', [
            'roles' => $roles,
            'rol_actual' => $rolActual,
            'titulo' => 'Roles y permisos - SGDM'
        ]);
    }

    public function panel(): void {
        $this->listar();
    }
}
