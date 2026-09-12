<?php

namespace App\Controladores;

use App\Modelos\Formato;
use App\Modelos\Juego;
use App\Ayudantes\Sesion;

require_once __DIR__ . '/../modelos/Formato.php';
require_once __DIR__ . '/../modelos/Juego.php';
require_once __DIR__ . '/../ayudantes/Sesion.php';

class FormatoControlador {
    private $modeloFormato;

    public function __construct() {
        $this->modeloFormato = new Formato();
    }

    // Muestra la vista de gestión de módulos (mantenimiento)
    public function index() {
        Sesion::validarAdmin(); // Solo admin
        $formatos = $this->modeloFormato->obtenerTodas();
        $vistaInyectada = 'modalidades/index.php';
        require_once __DIR__ . '/../vistas/admin/dashboard.php';
    }

    // Acción para guardar el estado de todos los módulos
    public function guardarEstados() {
        Sesion::validarAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formatosActivos = $_POST['formatos'] ?? [];
            $todosLosFormatos = $this->modeloFormato->obtenerTodas();
            
            foreach ($todosLosFormatos as $f) {
                $activo = in_array($f['codigo'], $formatosActivos) ? 1 : 0;
                $this->modeloFormato->cambiarEstado($f['codigo'], $activo);
            }
            $_SESSION['mensaje_exito'] = "Estados de los módulos actualizados correctamente.";
        }
        header("Location: " . URL_BASE . "index.php?c=formato&a=index");
        exit;
    }

    // Endpoint API (JSON) para que el frontend del Organizador pida las modalidades permitidas de un juego
    public function obtenerPermitidasJSON() {
        // Puede acceder organizador y admin
        if (!Sesion::estaLogueado()) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $juegoId = $_GET['juego_id'] ?? null;
        if (!$juegoId) {
            http_response_code(400);
            echo json_encode(['error' => 'Falta juego_id']);
            exit;
        }

        $modeloJuego = new \Juego();
        $modalidades = $modeloJuego->obtenerModalidadesPermitidas((int)$juegoId);
        $formatos = $modeloJuego->obtenerFormatosPermitidos((int)$juegoId);

        // Devolver como JSON (Modalidades y Formatos)
        header('Content-Type: application/json');
        echo json_encode([
            'modalidades' => $modalidades,
            'formatos' => $formatos
        ]);
        exit;
    }
}
