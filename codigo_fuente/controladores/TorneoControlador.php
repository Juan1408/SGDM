<?php
namespace App\Controladores;

use App\Ayudantes\Sesion;
use App\Modelos\Torneo;
use App\Modelos\Equipo;
require_once __DIR__ . '/../ayudantes/Sesion.php';
require_once __DIR__ . '/../modelos/Torneo.php';
require_once __DIR__ . '/../modelos/Equipo.php';

/**
 * Controlador publico de Torneos (catalogo + detalle + inscripcion).
 * catalogoPublico() y detalle() son publicas (sin sesion). inscribirse() exige
 * sesion de Jugador.
 */
class TorneoControlador {

    private Torneo $modeloTorneo;
    private Equipo $modeloEquipo;

    public function __construct(){
        $this->modeloTorneo = new Torneo();
        $this->modeloEquipo = Equipo::obtenerInstancia();
    }

    //Accion para el catalogo publico de torneos
    public function catalogoPublico(){
        $torneos = $this->modeloTorneo->obtenerTorneosPublicados();
        require_once __DIR__ . '/../vistas/publico/torneos.php';
    }

    //Accion para el detalle de un torneo puntual
    public function detalle(){
        $torneoId = (int) ($_GET['id'] ?? 0);
        $torneo = $torneoId > 0 ? $this->modeloTorneo->obtenerTorneoPublico($torneoId) : null;

        if ($torneo === null) {
            //No existe, esta en borrador, o cancelado: no hay nada que mostrar
            header('Location: ' . URL_BASE . 'index.php?c=torneo&a=catalogoPublico');
            exit;
        }

        //IMPORTANTE: esta pagina es publica (nadie llama a Sesion::validarJugador() aca),
        //por eso hace falta invocar explicitamente Sesion::estaLogueado() primero -- es la
        //que internamente llama a session_start(). Sin esto, $_SESSION aparece vacio aunque
        //la persona SI este logueada, porque la sesion nunca se "reanuda".
        $esJugadorLogueado = Sesion::estaLogueado() && (int) ($_SESSION['rol_id'] ?? 0) === 3;

        $miInscripcion = null;
        $misEquiposCapitan = [];

        if ($esJugadorLogueado) {
            $usuarioId = (int) $_SESSION['usuario_id'];
            $misEquipos = $this->modeloEquipo->obtenerEquiposDelJugador($usuarioId);
            $misEquiposCapitan = array_values(array_filter($misEquipos, static fn ($equipo) => !empty($equipo['es_capitan'])));
            $equipoIds = array_map(static fn ($equipo) => (int) $equipo['id'], $misEquipos);
            $miInscripcion = $this->modeloTorneo->obtenerInscripcionDelJugador($torneoId, $usuarioId, $equipoIds);
        }
        $agenda = $this->modeloTorneo->obtenerAgendaPublica($torneoId);
        $participantes = $this->modeloTorneo->obtenerParticipantesPublicos($torneoId);
        $encuentros = $this->modeloTorneo->obtenerFixturePublico($torneoId);
        $posiciones = in_array($torneo['formato'], ['liga', 'suizo'], true)
            ? $this->modeloTorneo->obtenerPosicionesPublicas($torneoId)
            : [];
        
        require_once __DIR__ . '/../vistas/publico/detalle-torneo.php';
    }

    //Accion para que un Jugador (o uno de sus equipos, si es capitan) se inscriba
    public function inscribirse(){
        Sesion::validarJugador();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header('Location: ' . URL_BASE . 'index.php?c=torneo&a=catalogoPublico');
            exit;
        }
        \App\Ayudantes\Sesion::requerirTokensCSRF();

        $torneoId = (int) ($_POST['torneo_id'] ?? 0);
        $tipo = $_POST['tipo'] ?? '';
        $usuarioId = (int) $_SESSION['usuario_id'];

        $torneo = $this->modeloTorneo->obtenerTorneoPublico($torneoId);
        $inscripcionesAbiertas = $torneo
            && $torneo['estado'] === 'inscripciones_abiertas'
            && (empty($torneo['fecha_limite_inscripcion']) || $torneo['fecha_limite_inscripcion'] >= date('Y-m-d'));
        if (!$inscripcionesAbiertas) {
            header('Location: ' . URL_BASE . 'index.php?c=torneo&a=detalle&id=' . $torneoId);
            exit;
        }

        if ($tipo === 'usuario') {
        $this->modeloTorneo->inscribirse($torneoId, 'usuario', $usuarioId, $_SESSION['usuario_nombre'] ?? 'Jugador');
        } elseif ($tipo === 'equipo') {
            $equipoId = (int) ($_POST['equipo_id'] ?? 0);
            //Verificamos que quien inscribe sea de verdad capitan de ese equipo
            if ($equipoId > 0 && $this->modeloEquipo->esCapitan($equipoId, $usuarioId)) {
                $equipo = $this->modeloEquipo->obtenerPorId($equipoId);
                if ($equipo) {
                    $this->modeloTorneo->inscribirse($torneoId, 'equipo', $equipoId, $equipo['nombre_equipo']);
                }
            }
        }

        header('Location: ' . URL_BASE . 'index.php?c=torneo&a=detalle&id=' . $torneoId);
        exit;
    }
}