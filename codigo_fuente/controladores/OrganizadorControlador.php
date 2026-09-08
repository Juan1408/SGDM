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
            'organizadores' => $listaOrganizador,
            'mensaje' => $_SESSION['mensaje'] ?? null
        ];
        unset($_SESSION['mensaje']);

        $vistaInyectada = 'organizadores/organizador_index.php';
        require_once __DIR__ . '/../vistas/admin/dashboard.php';
    }

    public function cambiarVerificacion() {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id > 0) {
            $exito = $this->modeloUsuario->cambiarVerificacionOrganizador($id);
            $_SESSION['mensaje'] = $exito 
                ? "Estado de aprobación/verificación del organizador actualizado correctamente."
                : "No se pudo actualizar el estado de aprobación del organizador.";
        }
        header("Location: " . URL_BASE . "index.php?c=organizador&a=index");
        exit;
    }
}