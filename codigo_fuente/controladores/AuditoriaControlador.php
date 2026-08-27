<?php

namespace App\Controladores;

use App\Ayudantes\Sesion;

require_once __DIR__ . '/../ayudantes/Auditorias.php';
use App\Ayudantes\Auditoria;
use SessionHandler;

class AuditoriaControlador{

    public function __construct(){
        // Solo los administradores ven los logs
        Sesion::validarAdmin();
    }
    
    public function index(){
        
        $logs = Auditoria::obtenerTodos();
        $datos = ['auditoria' => $logs];

        //Cargamos la vista
        $vistaInyectada = 'auditoria/auditoria.php';

        //Lo inyectamos en el dashboard
        require_once __DIR__ . '/../vistas/admin/dashboard.php';
    }
}
