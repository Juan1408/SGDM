<?php
namespace App\Controladores;

use App\Modelos\Jugador;
use App\Modelos\Equipo;
use App\Modelos\Torneo;
use App\Modelos\Organizador;
require_once __DIR__ . '/../modelos/Jugador.php';
require_once __DIR__ . '/../modelos/Equipo.php';
require_once __DIR__ . '/../modelos/Torneo.php';
require_once __DIR__ . '/../modelos/Organizador.php';

/**
 * Perfiles PUBLICOS (solo lectura). Accesibles para visitantes y para cualquier rol.
 * Rutas: index.php?c=perfil&a=jugador&id=X   |   index.php?c=perfil&a=organizador&id=X
 * Solo se muestra lo que la persona marco como publico (ver obtenerPerfilPublico de cada modelo).
 */
class PerfilControlador {

    private Jugador $modeloJugador;
    private Equipo $modeloEquipo;
    private Torneo $modeloTorneo;
    private Organizador $modeloOrganizador;

    public function __construct(){
        //Sin Sesion::validar...: el perfil publico lo puede ver cualquiera.
        //Pero se retoma la sesion (si existe) para elegir el navbar segun quien mira.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->modeloJugador = new Jugador();
        $this->modeloEquipo = Equipo::obtenerInstancia();
        $this->modeloTorneo = new Torneo();
        $this->modeloOrganizador = new Organizador();
    }

    public function jugador(){
        $id = (int) ($_GET['id'] ?? 0);
        $datos = $id > 0 ? $this->modeloJugador->obtenerPerfilPublico($id) : false;

        if (!$datos) {
            http_response_code(404);
            echo 'Jugador no encontrado.';
            return;
        }

        //Los equipos se muestran siempre
        $equipos = $this->modeloEquipo->obtenerEquiposDelJugador($id);

        //Los torneos solo si el jugador los dejo publicos; y solo las inscripciones confirmadas
        $torneos = [];
        if (in_array('torneos', $datos['visibles'], true)) {
            $equipoIds = array_column($equipos, 'id');
            $torneos = array_values(array_filter(
                $this->modeloTorneo->obtenerInscripcionesDelJugador($id, $equipoIds),
                fn($i) => $i['estado'] === 'confirmado'
            ));
        }

        $camposPublicos = $datos['visibles'];
        $esPublico = true;

        require_once __DIR__ . '/../vistas/jugador/perfil-jugador.php';
    }

    public function organizador(){
        $id = (int) ($_GET['id'] ?? 0);
        $datos = $id > 0 ? $this->modeloOrganizador->obtenerPerfilPublico($id) : false;

        if (!$datos) {
            http_response_code(404);
            echo 'Organizador no encontrado.';
            return;
        }

        //Torneos publicados (siempre visibles)
        $torneos = $this->modeloOrganizador->obtenerTorneosPublicos($id);

        //Para mostrar el aviso "Perfil actualizado" solo a la propia persona
        $esDuenio = (int) ($_SESSION['usuario_id'] ?? 0) === $id;

        require_once __DIR__ . '/../vistas/publico/perfil-publico-organizador.php';
    }
}
