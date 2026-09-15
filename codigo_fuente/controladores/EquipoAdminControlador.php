<?php

namespace App\Controladores;

use App\Modelos\Equipo;
use App\Ayudantes\Sesion;
use Juego;

require_once __DIR__ . '/../ayudantes/Sesion.php';
require_once __DIR__ . '/../modelos/Equipo.php';
require_once __DIR__ . '/../modelos/Juego.php';

class EquipoAdminControlador {
    private Equipo $modeloEquipo;
    private \Juego $modeloJuego;

    public function __construct() {
        // Validamos que el usuario sea administrador
        Sesion::validarAdmin();

        $this->modeloEquipo = Equipo::obtenerInstancia();
        $this->modeloJuego = new Juego();
    }

    public function index() {
        // Capturar los parámetros de filtro de la URL
        $juegoId = isset($_GET['juego_id']) && is_numeric($_GET['juego_id']) ? (int)$_GET['juego_id'] : null;
        $orden = $_GET['orden'] ?? 'nuevos';

        // Obtener el catálogo de juegos para el select del filtro
        $juegos = $this->modeloJuego->obtenerTodosLosJuegos();

        // Obtener la lista de equipos filtrada y ordenada
        $listaEquipos = $this->modeloEquipo->obtenerDirectorioAdmin($juegoId, $orden);

        // Empaquetando los datos en un array
        $datos = [
            'equipos' => $listaEquipos,
            'juegos' => $juegos,
            'filtro_juego' => $juegoId,
            'filtro_orden' => $orden
        ];

        // Lo inyectamos en la vista
        $vistaInyectada = 'equipos/equipos_index.php';

        require_once __DIR__ . '/../vistas/admin/dashboard.php';
    }
}
