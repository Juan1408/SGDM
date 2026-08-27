<?php


namespace App\Controladores;

use App\Modelos\Torneo;
use App\Ayudantes\Sesion;

require_once __DIR__ . '/../ayudantes/Sesion.php';
require_once __DIR__ . '/../modelos/Torneo.php';

class TorneoAdminControlador {
    private Torneo $modeloTorneo;

    public function __construct(){
        //validamos que el usuario sea administrador
        Sesion::validarAdmin();

        $this->modeloTorneo = new Torneo();
    }

    public function index(){
     
        $listaTorneos = $this->modeloTorneo->obtenerTorneos();

        //Empaquetando los datos en un array
        $datos = [ 'torneos' => $listaTorneos];

        //Lo inyectamos en la vista
        $vistaInyectada = 'torneos/torneos_index.php';

        require_once __DIR__ . '/../vistas/admin/dashboard.php';
    }

    
}

