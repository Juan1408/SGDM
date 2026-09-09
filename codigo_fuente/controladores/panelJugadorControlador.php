<?php
namespace App\Controladores;

use App\Ayudantes\Sesion;
use App\Modelos\Jugador;
use App\Modelos\Equipo;
use App\Modelos\Torneo;
require_once __DIR__ . '/../ayudantes/Sesion.php';
require_once __DIR__ . '/../modelos/Jugador.php';
require_once __DIR__ . '/../modelos/Equipo.php';
require_once __DIR__ . '/../modelos/Torneo.php';

class PanelJugadorControlador {

    private Jugador $modeloJugador;
    private Equipo $modeloEquipo;
    private Torneo $modeloTorneo;

    public function __construct(){
        //Aseguramos que el usuario este logueado Y que sea rol Jugador (3)
        Sesion::validarJugador();
        $this->modeloJugador = new Jugador();
        $this->modeloEquipo = Equipo::obtenerInstancia();
        $this->modeloTorneo = new Torneo();
    }

    //Funcion para mostrar el dashboard
    public function dashboard(){
        $usuarioId = $_SESSION['usuario_id'];

        $datos = $this->modeloJugador->obtenerPerfil($usuarioId);
        $equipos = $this->modeloEquipo->obtenerEquiposDelJugador($usuarioId);
        $equipoIds = array_column($equipos, 'id');

        $torneosActivos = $this->modeloTorneo->contarTorneosActivosDelJugador($usuarioId, $equipoIds);
        $proximosPartidos = $this->modeloTorneo->obtenerProximosPartidos($usuarioId, $equipoIds, 3);
        $record = $this->modeloTorneo->obtenerRecordDelJugador($usuarioId, $equipoIds);
        $solicitudesPendientes = $this->modeloEquipo->obtenerInvitacionesRecibidas($usuarioId);

        require_once __DIR__ . '/../vistas/jugador/dashboard.php';
    }

    //Accion para mostrar el perfil propio del jugador logueado
    public function perfil(){
        $usuarioId = $_SESSION['usuario_id'];
        $datos = $this->modeloJugador->obtenerPerfil($usuarioId);
        $equipos = $this->modeloEquipo->obtenerEquiposDelJugador($usuarioId);


        require_once __DIR__ . '/../vistas/jugador/perfil-jugador.php';
    }

    //Accion para procesar el guardado del formulario de edicion de perfil
    public function actualizarPerfil(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header("Location: " . URL_BASE . "index.php?c=panelJugador&a=perfil");
            exit;
        }

        $usuarioId = $_SESSION['usuario_id'];

        $nombreCompleto = trim($_POST['nombre_completo'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $ciudad = trim($_POST['ciudad'] ?? '');
        $pais = trim($_POST['pais'] ?? '');
        $fechaNacimiento = trim($_POST['fecha_nacimiento'] ?? '');

        //Validaciones minimas
        if (empty($nombreCompleto) || empty($email)) {
            $error = "El nombre y el email son obligatorios.";
            $datos = $this->modeloJugador->obtenerPerfil($usuarioId);
            require_once __DIR__ . '/../vistas/jugador/perfil-jugador.php';
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "El email ingresado no es valido.";
            $datos = $this->modeloJugador->obtenerPerfil($usuarioId);
            require_once __DIR__ . '/../vistas/jugador/perfil-jugador.php';
            return;
        }

        if ($this->modeloJugador->emailEnUso($email, $usuarioId)) {
            $error = "Ese email ya esta en uso por otra cuenta.";
            $datos = $this->modeloJugador->obtenerPerfil($usuarioId);
            require_once __DIR__ . '/../vistas/jugador/perfil-jugador.php';
            return;
        }

        $datosUsuario = ['nombre_completo' => $nombreCompleto, 'email' => $email];
        $datosPerfil = ['bio' => $bio, 'ciudad' => $ciudad, 'pais' => $pais, 'fecha_nacimiento' => $fechaNacimiento];

        $this->modeloJugador->actualizarPerfil($usuarioId, $datosUsuario, $datosPerfil);

        //Actualizamos el nombre/email en sesion para que se reflejen sin tener que volver a loguearse
        $_SESSION['usuario_nombre'] = $nombreCompleto;
        $_SESSION['email'] = $email;

        header("Location: " . URL_BASE . "index.php?c=panelJugador&a=perfil&exito=1");
        exit;
    }
}