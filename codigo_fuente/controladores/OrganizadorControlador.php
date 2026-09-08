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

    public function responderSolicitud() {
        $id = (int) ($_GET['id'] ?? 0);
        $decision = $_GET['decision'] ?? ''; // 'aprobar', 'rechazar', 'revocar'
        if ($id > 0 && in_array($decision, ['aprobar', 'rechazar', 'revocar'], true)) {
            $exito = $this->modeloUsuario->responderSolicitudOrganizador($id, $decision);
            if ($decision === 'aprobar') {
                $_SESSION['mensaje'] = "Solicitud aprobada: El organizador ha sido habilitado oficialmente para crear torneos.";
            } elseif ($decision === 'rechazar') {
                $_SESSION['mensaje'] = "Solicitud rechazada: El organizador permanece en estado no habilitado.";
            } else {
                $_SESSION['mensaje'] = "Habilitación oficial revocada correctamente.";
            }
        }
        header("Location: " . URL_BASE . "index.php?c=organizador&a=index");
        exit;
    }
}