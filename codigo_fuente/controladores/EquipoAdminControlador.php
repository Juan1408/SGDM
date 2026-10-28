<?php

namespace App\Controladores;

use App\Modelos\Equipo;
use App\Ayudantes\Sesion;
use App\Ayudantes\Auditoria;
use Juego;

require_once __DIR__ . '/../ayudantes/Sesion.php';
require_once __DIR__ . '/../ayudantes/Auditorias.php';
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

    /**
     * Ficha administrativa de un equipo (solo lectura).
     * El administrador puede auditar equipos activos e inactivos.
     */
    public function ver() {
        $equipoId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int) $_GET['id'] : 0;

        $ficha = $equipoId > 0 ? $this->modeloEquipo->obtenerFichaAdmin($equipoId) : false;

        if (!$ficha) {
            $_SESSION['mensaje'] = "El equipo solicitado no existe o fue eliminado.";
            $_SESSION['mensaje_tipo'] = "error";
            header("Location: " . URL_BASE . "index.php?c=equipoAdmin&a=index");
            exit();
        }

        $datos = $ficha;
        $vistaInyectada = 'equipos/equipo_detalle.php';
        $tituloPantalla = 'Ficha administrativa del equipo';
        $estilosExtra = ['css/admin/ficha.css'];

        require_once __DIR__ . '/../vistas/admin/dashboard.php';
    }

    /**
     * Renombra un equipo y sincroniza las copias denormalizadas del nombre.
     * Solo acepta POST con token CSRF válido; luego redirige (PRG).
     */
    public function actualizarNombre() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Metodo no permitido');
        }

        Sesion::requerirTokensCSRF();

        $equipoId = isset($_POST['equipo_id']) && is_numeric($_POST['equipo_id']) ? (int) $_POST['equipo_id'] : 0;
        $nuevoNombre = isset($_POST['nombre_equipo']) && is_string($_POST['nombre_equipo'])
            ? $_POST['nombre_equipo']
            : '';

        if ($equipoId <= 0) {
            $_SESSION['mensaje'] = "El equipo indicado no es válido.";
            $_SESSION['mensaje_tipo'] = "error";
            header("Location: " . URL_BASE . "index.php?c=equipoAdmin&a=index");
            exit();
        }

        $resultado = $this->modeloEquipo->renombrarEquipoAdmin($equipoId, $nuevoNombre);

        if (!empty($resultado['exito'])) {
            if (($resultado['anterior'] ?? '') === ($resultado['nuevo'] ?? '')) {
                $_SESSION['mensaje'] = "El equipo ya se llamaba \"" . $resultado['nuevo'] . "\".";
            } else {
                $_SESSION['mensaje'] = "Equipo renombrado de \"" . $resultado['anterior'] . "\" a \"" . $resultado['nuevo'] . "\".";
                try {
                    Auditoria::registrar(
                        'ADMIN_RENOMBRA_EQUIPO',
                        "El administrador renombró el equipo ID {$equipoId}: \"{$resultado['anterior']}\" → \"{$resultado['nuevo']}\""
                    );
                } catch (\Throwable $e) {
                    error_log('No se pudo auditar el renombre de equipo: ' . $e->getMessage());
                }
            }
            $_SESSION['mensaje_tipo'] = "exito";
        } else {
            $_SESSION['mensaje'] = $resultado['error'] ?? "No se pudo renombrar el equipo.";
            $_SESSION['mensaje_tipo'] = "error";
        }

        header("Location: " . URL_BASE . "index.php?c=equipoAdmin&a=ver&id=" . $equipoId);
        exit();
    }
}
