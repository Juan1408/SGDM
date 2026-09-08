<?php
/**
 * ============================================================================
 * CLASE CONTROLADOR: PoliticaContrasenaControlador.php
 * ============================================================================
 * Propósito: Administra las vistas y la persistencia de las reglas globales
 *            de complejidad y caducidad de contraseñas (Tarea ADM-07).
 * Ubicación: codigo_fuente/controladores/PoliticaContrasenaControlador.php
 * ============================================================================
 */

namespace App\Controladores;

use App\Ayudantes\Sesion;
use App\Ayudantes\Auditoria;
use App\Modelos\PoliticaContrasena;

require_once __DIR__ . '/../ayudantes/Sesion.php';
require_once __DIR__ . '/../ayudantes/Auditorias.php';
require_once __DIR__ . '/../modelos/PoliticaContrasena.php';

class PoliticaContrasenaControlador {

    private PoliticaContrasena $modeloPolitica;

    public function __construct() {
        // Exigir rol de Administrador General
        Sesion::validarAdmin();
        $this->modeloPolitica = new PoliticaContrasena();
    }

    /**
     * Muestra la pantalla de configuración de políticas de contraseña.
     */
    public function index() {
        $politica = $this->modeloPolitica->obtenerPoliticaVigente();

        $datos = [
            'politica' => $politica
        ];

        // Se inyecta en el layout del dashboard de admin
        $vistaInyectada = 'configuracion/politicas_contrasenas.php';
        require_once __DIR__ . '/../vistas/admin/dashboard.php';
    }

    /**
     * Procesa el formulario POST para actualizar las políticas de contraseña.
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL_BASE . 'index.php?c=politicaContrasena&a=index');
            exit;
        }

        // Sanitización y armado del payload
        $longitudMinima = (int) ($_POST['longitud_minima'] ?? 8);
        $expiracionDias = (int) ($_POST['expiracion_dias'] ?? 90);
        $historialCantidad = (int) ($_POST['historial_cantidad'] ?? 5);

        // Validaciones básicas de rangos de seguridad
        if ($longitudMinima < 4 || $longitudMinima > 64) {
            $longitudMinima = 8;
        }
        if ($expiracionDias < 0 || $expiracionDias > 365) {
            $expiracionDias = 90;
        }
        if ($historialCantidad < 0 || $historialCantidad > 20) {
            $historialCantidad = 5;
        }

        $datosFormulario = [
            'longitud_minima' => $longitudMinima,
            'requiere_mayuscula' => isset($_POST['requiere_mayuscula']) ? 1 : 0,
            'requiere_minuscula' => isset($_POST['requiere_minuscula']) ? 1 : 0,
            'requiere_numero' => isset($_POST['requiere_numero']) ? 1 : 0,
            'requiere_caracter_especial' => isset($_POST['requiere_caracter_especial']) ? 1 : 0,
            'expiracion_dias' => $expiracionDias,
            'historial_cantidad' => $historialCantidad
        ];

        $usuarioId = $_SESSION['usuario_id'] ?? null;
        $exito = $this->modeloPolitica->guardarPolitica($datosFormulario, $usuarioId);

        if ($exito) {
            // Registrar en auditoría
            Auditoria::registrar(
                'Modificación de Políticas de Contraseña',
                "Se actualizaron los parámetros: Min {$longitudMinima} caracteres, Expiración {$expiracionDias} días."
            );

            $_SESSION['mensaje'] = "Las políticas de contraseña se han actualizado con éxito.";
        } else {
            $_SESSION['mensaje'] = "Ocurrió un error al guardar los cambios en las políticas.";
        }

        header('Location: ' . URL_BASE . 'index.php?c=politicaContrasena&a=index');
        exit;
    }
}
