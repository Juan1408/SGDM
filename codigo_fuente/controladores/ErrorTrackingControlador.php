<?php

namespace App\Controladores;

use App\Ayudantes\Sesion;
use App\Modelos\ErrorSistema;

require_once __DIR__ . '/../ayudantes/Sesion.php';
require_once __DIR__ . '/../modelos/ErrorSistema.php';

class ErrorTrackingControlador {

    private $modeloErrores;

    public function __construct() {
        // Solo administradores pueden acceder a este controlador
        Sesion::validarAdmin();

        $this->modeloErrores = new ErrorSistema();
    }

    /**
     * Muestra la tabla principal (Dashboard) de errores agrupados
     */
    public function dashboard() {
        $filtroEstado = $_GET['estado'] ?? 'todos';
        $filtroRol = $_GET['rol'] ?? 'todos';
        $filtroTipo = $_GET['tipo'] ?? 'todos';
        
        $errores = $this->modeloErrores->obtenerErroresAgrupados($filtroEstado, $filtroRol, $filtroTipo);
        
        $vistaInyectada = 'monitoreo_errores.php';
        require_once __DIR__ . '/../vistas/admin/dashboard.php';
    }

    /**
     * Muestra el detalle de un error (la Foto del Stack Trace y el Contexto JSON)
     */
    public function verDetalle() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?c=errorTracking&a=dashboard');
            exit;
        }

        $error = $this->modeloErrores->obtenerDetalleError($id);
        
        if (!$error) {
            die("Error no encontrado");
        }

        require_once __DIR__ . '/../vistas/admin/detalle_error.php';
    }

    /**
     * Acción central para procesar operaciones sobre los errores (POST)
     */
    public function accionError() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $accion = $_POST['accion'] ?? null;

            if ($id && $accion) {
                if ($accion === 'eliminar') {
                    $this->modeloErrores->eliminarError($id);
                } elseif ($accion === 'cambiar_estado') {
                    $nuevoEstado = $_POST['estado'] ?? 'nuevo';
                    $detalles = $_POST['detalles_resolucion'] ?? null;
                    $this->modeloErrores->cambiarEstado($id, $nuevoEstado, $detalles);
                }
            }
        }
        
        // Redirigir conservando los filtros si es posible, por simplicidad volvemos al dashboard
        header('Location: index.php?c=errorTracking&a=dashboard');
        exit;
    }
}
