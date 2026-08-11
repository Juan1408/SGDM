<?php
/*
ARCHIVO DESHABILITADO TEMPORALMENTE PARA EVITAR ERRORES EN EL EDITOR
*/

namespace App\Controladores;
use App\Modelos\Usuario;
use App\Ayudantes\Sesion;

require_once __DIR__ . '/../modelos/Usuario.php';
require_once __DIR__ . '/../ayudantes/Sesion.php';

class UsuarioControlador {
    private $modeloUsuario;

    public function __construct(){
        //Validamos que solo el administrador ingrese
        Sesion::validarAdmin();
        $this->modeloUsuario = new Usuario();
    }

    //Metodo para mostrar el menu
    public function index() {
        //Obtenemos todos los usuarios con sus roles
        $listaUsuarios = $this->modeloUsuario->obtenerTodosUsuariosConRoles();
        
        //Empaquetamos los datos
        $datos = [
            'usuarios' => $listaUsuarios
        ];

        //Cargamos la vista index.php del directorio jugadores
        $vistaInyectada = 'usuarios_index.php';
        require_once __DIR__ . '/../vistas/admin/dashboard.php';
        
    }
}