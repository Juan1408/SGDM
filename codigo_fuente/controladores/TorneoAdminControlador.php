<?php

namespace App\Controladores;

use App\Modelos\Torneo;
use App\Ayudantes\Sesion;
use App\Ayudantes\Auditoria;
use Juego;

require_once __DIR__ . '/../ayudantes/Sesion.php';
require_once __DIR__ . '/../modelos/Torneo.php';
require_once __DIR__ . '/../ayudantes/Auditorias.php';
require_once __DIR__ . '/../modelos/Juego.php';

class TorneoAdminControlador {
    private Torneo $modeloTorneo;
    private \Juego $modeloJuego;

    public function __construct() {
        // Validamos que el usuario sea administrador
        Sesion::validarAdmin();

        $this->modeloTorneo = new Torneo();
        $this->modeloJuego = new \Juego();
    }

    public function index() {
        // Capturar los parámetros de filtro de la URL
        $juegoId = isset($_GET['juego_id']) && is_numeric($_GET['juego_id']) ? (int)$_GET['juego_id'] : null;
        $estado = $_GET['estado'] ?? 'todos';
        $orden = $_GET['orden'] ?? 'nuevos';

        // Obtener el catálogo de juegos para el select del filtro
        $juegos = $this->modeloJuego->obtenerTodosLosJuegos();

        // Obtener la lista de torneos filtrada y ordenada
        $listaTorneos = $this->modeloTorneo->obtenerDirectorioAdmin($juegoId, $estado, $orden);

        // Empaquetando los datos en un array
        $datos = [
            'torneos' => $listaTorneos,
            'juegos' => $juegos,
            'filtro_juego' => $juegoId,
            'filtro_estado' => $estado,
            'filtro_orden' => $orden
        ];

        // Lo inyectamos en la vista
        $vistaInyectada = 'torneos/torneos_index.php';

        require_once __DIR__ . '/../vistas/admin/dashboard.php';
    }

    public function editar() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: " . URL_BASE . "index.php?c=torneoAdmin&a=index");
            exit;
        }

        // Usamos un metodo generico (o lo creamos) para obtener los datos del torneo.
        // Dado que somos admin, no necesitamos validar el organizador_id.
        // Simulamos obtenerlo usando una consulta directa o un metodo si estuviera disponible.
        // Como no existe "obtenerPorIdParaAdmin", vamos a obtener todos y filtrar (solo para no reescribir mucho, o preferiblemente crear un metodo rápido si tuvieramos).
        // En este caso, ya que no tengo "obtenerPorIdParaAdmin", buscaré el torneo en obtenerTorneos.
        $torneos = $this->modeloTorneo->obtenerTorneos();
        $torneoEditar = null;
        foreach($torneos as $t) {
            if ($t['id'] == $id) {
                $torneoEditar = $t;
                break;
            }
        }

        if (!$torneoEditar) {
            $_SESSION['mensaje'] = "Torneo no encontrado.";
            header("Location: " . URL_BASE . "index.php?c=torneoAdmin&a=index");
            exit;
        }

        $datos = ['torneo' => $torneoEditar];
        $vistaInyectada = 'torneos/torneo_editar.php';
        require_once __DIR__ . '/../vistas/admin/dashboard.php';
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            \App\Ayudantes\Sesion::requerirTokensCSRF();
            $id = $_POST['id'] ?? null;
            $nombre = $_POST['nombre'] ?? '';
            $estado = $_POST['estado'] ?? '';

            if ($id && $nombre && $estado) {
                $datos = [
                    'nombre' => trim($nombre),
                    'estado' => trim($estado)
                ];

                if ($this->modeloTorneo->actualizarPorAdmin((int)$id, $datos)) {
                    $_SESSION['mensaje'] = "Torneo actualizado correctamente.";
                    Auditoria::registrar('ACTUALIZACION_TORNEO', "El administrador ha actualizado el torneo ID: $id (Nombre: $nombre, Estado: $estado)");
                } else {
                    $_SESSION['mensaje'] = "Hubo un error al actualizar el torneo.";
                }
            } else {
                $_SESSION['mensaje'] = "Faltan datos obligatorios.";
            }
        }
        header("Location: " . URL_BASE . "index.php?c=torneoAdmin&a=index");
        exit;
    }

    public function eliminar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['mensaje'] = "Método no permitido. La eliminación requiere una solicitud POST válida.";
            header("Location: " . URL_BASE . "index.php?c=torneoAdmin&a=index");
            exit;
        }

        Sesion::requerirTokensCSRF();

        $id = $_POST['id'] ?? null;
        
        if ($id) {
            // Buscamos el nombre del torneo antes de eliminarlo para loguearlo
            $torneos = $this->modeloTorneo->obtenerTorneos();
            $nombreTorneo = "Desconocido";
            foreach($torneos as $t) {
                if ($t['id'] == $id) {
                    $nombreTorneo = $t['nombre'];
                    break;
                }
            }

            if ($this->modeloTorneo->eliminarPorAdmin((int)$id)) {
                $_SESSION['mensaje'] = "El torneo ha sido eliminado permanentemente.";
                // Registro de la auditoría para la eliminación
                Auditoria::registrar('ELIMINACION_TORNEO', "El administrador ha eliminado irreversiblemente el torneo ID: $id (Nombre: $nombreTorneo)");
            } else {
                $_SESSION['mensaje'] = "Hubo un error al eliminar el torneo.";
            }
        }

        header("Location: " . URL_BASE . "index.php?c=torneoAdmin&a=index");
        exit;
    }
}
