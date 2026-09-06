<?php
namespace App\Controladores;

use App\Ayudantes\Sesion;
require_once __DIR__ . '/../ayudantes/Sesion.php';

class PanelJugadorControlador {
    public function __construct(){
        //Aseguramos que el usuario este logueado
        Sesion::requerirLogin();
    }

    //Funcion para mostrar el dashboard
    public function dashboard(){
        require_once __DIR__ . '/../vistas/jugador/dashboard.php';
    }
}