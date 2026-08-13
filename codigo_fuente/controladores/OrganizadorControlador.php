<?php

namespace App\Controladores;

use App\Modelos\Usuario;
use App\Ayudantes\Sesion;

require_once __DIR__ . '/../modelos/Usuario.php';
require_once __DIR__ . '/../ayudantes/Sesion.php';

class OrganizadorControlador {
    private Usuario $modeloUsuario;

    public function __construct(){
        //Validamos que solo el administrador y el organizador puedan acceder
        Sesion::validarAdmin();
        $this->modeloUsuario = new Usuario();
    }

    public function index(){
        $listaOrganizador = $this->modeloUsuario->obtenerTodosUsuariosConRoles(2);
        
        $datos = [
            'organizadores' => $listaOrganizador
        ];

        $vistaInyectada = 'organizador/organizador_index.php';
        require_once __DIR__ . '/../vistas/admin/dashboard.php';
    }

}