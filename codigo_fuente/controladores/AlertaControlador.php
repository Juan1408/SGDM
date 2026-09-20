<?php
/**
 * ============================================================================
 * CONTROLADOR: AlertaControlador
 * ============================================================================
 * Propósito: Gestiona la vista y el procesamiento de alertas/notificaciones
 *            masivas a los usuarios del sistema.
 * ============================================================================
 */

namespace App\Controladores;

use App\Modelos\Alerta;
use App\Modelos\ErrorSistema;
use App\Ayudantes\Sesion;

require_once __DIR__ . '/../modelos/Alerta.php';
require_once __DIR__ . '/../modelos/ErrorSistema.php'; // Para registrar errores si ocurren
require_once __DIR__ . '/../ayudantes/Sesion.php';

class AlertaControlador {
    private $modeloAlerta;

    public function __construct() {
        // Verificar que el usuario tenga sesión y sea admin general usando el Helper oficial
        Sesion::validarAdmin();

        $this->modeloAlerta = new Alerta();
    }

    /**
     * Muestra la vista con el formulario para enviar una alerta global
     */
    public function index() {
        $vistaInyectada = 'alertas/enviar_alerta.php';
        require_once __DIR__ . '/../vistas/admin/dashboard.php';
    }

    /**
     * Procesa el formulario de envío masivo
     */
    public function enviar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?c=alerta&a=index');
            exit;
        }

        // Seguridad: Validar Token CSRF
        if (!Sesion::validarTokenCSRF($_POST['csrf_token'] ?? '')) {
            $_SESSION['mensaje'] = "Error de seguridad (CSRF). Petición denegada.";
            $_SESSION['mensaje_tipo'] = "error";
            header('Location: index.php?c=alerta&a=index');
            exit;
        }

        $titulo = trim($_POST['titulo'] ?? '');
        $mensaje = trim($_POST['mensaje'] ?? '');
        $tipo = $_POST['tipo'] ?? 'info';
        $audiencia = $_POST['audiencia'] ?? 'todos';

        // Validaciones básicas
        if (empty($titulo) || empty($mensaje)) {
            $_SESSION['mensaje'] = "El título y el mensaje son obligatorios.";
            $_SESSION['mensaje_tipo'] = "error";
            header('Location: index.php?c=alerta&a=index');
            exit;
        }

        $resultado = $this->modeloAlerta->enviarAlertaGlobal($titulo, $mensaje, $tipo, $audiencia);

        if ($resultado['exito']) {
            $_SESSION['mensaje'] = "¡Éxito! " . $resultado['mensaje'];
            $_SESSION['mensaje_tipo'] = "exito";
        } else {
            $_SESSION['mensaje'] = "Error: " . $resultado['mensaje'];
            $_SESSION['mensaje_tipo'] = "error";
        }

        header('Location: index.php?c=alerta&a=index');
        exit;
    }
}
