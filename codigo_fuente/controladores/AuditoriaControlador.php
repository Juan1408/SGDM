<?php

namespace App\Controladores;

use App\Ayudantes\Sesion;
use App\Ayudantes\Auditoria;

require_once __DIR__ . '/../ayudantes/Auditorias.php';
require_once __DIR__ . '/../ayudantes/Sesion.php';

class AuditoriaControlador {

    public function __construct() {
        // Solo los administradores ven los logs
        Sesion::validarAdmin();
    }
    
    public function index() {
        $logs = Auditoria::obtenerTodos();
        $datos = ['auditoria' => $logs];

        // Cargamos la vista de auditoría
        $vistaInyectada = 'auditoria/auditoria.php';

        // Lo inyectamos en el dashboard del administrador
        require_once __DIR__ . '/../vistas/admin/dashboard.php';
    }
}
