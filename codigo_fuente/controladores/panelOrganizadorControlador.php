<?php
namespace App\Controladores;

require_once __DIR__ . '/../ayudantes/Sesion.php';
use App\Ayudantes\Sesion;

class PanelOrganizadorControlador {

    public function __construct() {
        // En un futuro cercano, deberías validar que el rol sea 2 (Organizador)
        Sesion::requerirLogin();
    }

    public function dashboard() {
        //aqui cargaremos la vista del panel del jugador
        require_once __DIR__ . '/../vistas/organizador/dashboard.php';
    }
}