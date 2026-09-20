<?php
namespace App\Controladores;

use App\Ayudantes\Sesion;
use App\Modelos\Torneo;

require_once __DIR__ . '/../ayudantes/Sesion.php';
require_once __DIR__ . '/../modelos/Torneo.php';

class TorneoHistorialControlador {
    private Torneo $modeloTorneo;

    public function __construct() {
        // Solo los administradores generales tienen acceso a este historial global
        Sesion::validarAdmin();
        $this->modeloTorneo = new Torneo();
    }

    /**
     * Muestra la lista del historial de torneos finalizados y cancelados
     */
    public function index() {
        $torneos = $this->modeloTorneo->obtenerHistorialTorneos();
        
        $vistaInyectada = 'torneos/historial.php';
        require_once __DIR__ . '/../vistas/admin/dashboard.php';
    }
}
