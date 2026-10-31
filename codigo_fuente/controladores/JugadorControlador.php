<?php
namespace App\Controladores;

use App\Modelos\Usuario;
use App\Ayudantes\Sesion;

require_once __DIR__ . '/../modelos/Usuario.php';
require_once __DIR__ . '/../ayudantes/Sesion.php';

class JugadorControlador {
    private Usuario $modeloUsuario;

    public function __construct() {
        //Validamos que solo el administrador ingrese
        Sesion::validarAdmin();
        $this->modeloUsuario = new Usuario();
    }

    public function index(){
        //Obtenemos todos los usuarios con sus roles
        $listaJugadores = $this->modeloUsuario->obtenerTodosUsuariosConRoles(3);

        $datos = [
            'jugadores' => $listaJugadores
        ];

        //Cargamos la vista index.php del directorio jugadores
        $vistaInyectada = 'jugadores/jugadores_index.php';
        require_once __DIR__ . '/../vistas/admin/dashboard.php';
    }

    
}