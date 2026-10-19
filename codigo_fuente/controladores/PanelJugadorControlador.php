<?php
namespace App\Controladores;

use App\Ayudantes\Sesion;
use App\Modelos\Jugador;
use App\Modelos\Equipo;
use App\Modelos\Torneo;
use App\Ayudantes\SubidaImagen;
require_once __DIR__ . '/../ayudantes/Sesion.php';
require_once __DIR__ . '/../ayudantes/SubidaImagen.php';
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
        $misInscripciones = $this->modeloTorneo->obtenerInscripcionesDelJugador($usuarioId, $equipoIds);
        $solicitudesPendientes = $this->modeloEquipo->obtenerInvitacionesRecibidas($usuarioId);

        require_once __DIR__ . '/../vistas/jugador/dashboard.php';
    }

    //Accion para mostrar el perfil propio del jugador logueado (solo lectura)
    public function perfil(){
        $usuarioId = $_SESSION['usuario_id'];
        $datos = $this->modeloJugador->obtenerPerfil($usuarioId);
        $equipos = $this->modeloEquipo->obtenerEquiposDelJugador($usuarioId);
        $logros = $this->modeloJugador->obtenerLogros($usuarioId);

        //Torneos reales: inscripciones del jugador (directas o por sus equipos), sin las rechazadas/canceladas
        $equipoIds = array_column($equipos, 'id');
        $torneos = array_values(array_filter(
            $this->modeloTorneo->obtenerInscripcionesDelJugador($usuarioId, $equipoIds),
            fn($i) => in_array($i['estado'], ['confirmado', 'pendiente'], true)
        ));

        $camposPublicos = Jugador::listaCamposPublicos($datos['campos_publicos'] ?? '');
        $esPublico = false;

        require_once __DIR__ . '/../vistas/jugador/perfil-jugador.php';
    }

    //Accion para mostrar la pagina de edicion del perfil (menu Jugador > Editar perfil)
    public function editarPerfil(){
        $this->mostrarEdicion();
    }

    //Carga los datos y muestra el formulario de edicion.
    //$error: mensaje a mostrar si hubo un problema al guardar
    //$valoresEnviados: lo que el usuario escribio, para no perderlo si hubo error
    private function mostrarEdicion(string $error = '', array $valoresEnviados = []){
        $usuarioId = $_SESSION['usuario_id'];
        $datos = $this->modeloJugador->obtenerPerfil($usuarioId);

        if (!empty($valoresEnviados)) {
            $datos = array_merge($datos ?: [], $valoresEnviados);
        }

        $camposPublicos = Jugador::listaCamposPublicos($datos['campos_publicos'] ?? '');

        require_once __DIR__ . '/../vistas/jugador/editar-perfil-jugador.php';
    }

    //Accion para procesar el guardado del formulario de edicion de perfil
    public function actualizarPerfil(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header("Location: " . URL_BASE . "index.php?c=panelJugador&a=perfil");
            exit;
        }
        \App\Ayudantes\Sesion::requerirTokensCSRF();

        $usuarioId = $_SESSION['usuario_id'];

        $nombreCompleto = trim($_POST['nombre_completo'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $ciudad = trim($_POST['ciudad'] ?? '');
        $pais = trim($_POST['pais'] ?? '');
        $fechaNacimiento = trim($_POST['fecha_nacimiento'] ?? '');
        $discord = trim($_POST['discord_tag'] ?? '');
        $instagram = trim($_POST['instagram_url'] ?? '');
        $twitter = trim($_POST['twitter_url'] ?? '');

        //Casillas de privacidad: solo aceptamos los nombres conocidos
        $publicosMarcados = array_intersect($_POST['publico'] ?? [], Jugador::CAMPOS_OPCIONALES);
        $camposPublicos = implode(',', $publicosMarcados);

        //Lo escrito, para volver a mostrarlo si hay un error
        $enviados = [
            'nombre_completo' => $nombreCompleto, 'email' => $email, 'bio' => $bio,
            'ciudad' => $ciudad, 'pais' => $pais, 'fecha_nacimiento' => $fechaNacimiento,
            'discord_tag' => $discord, 'instagram_url' => $instagram, 'twitter_url' => $twitter,
            'campos_publicos' => $camposPublicos
        ];

        //Validaciones
        $error = $this->validarPerfil($usuarioId, $nombreCompleto, $email, $fechaNacimiento, $discord, $instagram, $twitter, $bio);

        //Imagenes (opcionales): si no se sube nada, se conserva la que ya estaba
        $fotoUrl = null;
        $bannerUrl = null;
        if ($error === '') {
            if (SubidaImagen::fueEnviado($_FILES['avatar'] ?? null)) {
                $fotoUrl = SubidaImagen::guardar($_FILES['avatar'], 'perfiles/subidos');
                if ($fotoUrl === null) { $error = "La foto de perfil no es valida (JPG, PNG o WEBP, maximo 2 MB)."; }
            }
            if ($error === '' && SubidaImagen::fueEnviado($_FILES['banner'] ?? null)) {
                $bannerUrl = SubidaImagen::guardar($_FILES['banner'], 'perfiles/subidos');
                if ($bannerUrl === null) { $error = "El banner no es valido (JPG, PNG o WEBP, maximo 2 MB)."; }
            }
        }

        if ($error !== '') {
            $this->mostrarEdicion($error, $enviados);
            return;
        }

        $datosUsuario = ['nombre_completo' => $nombreCompleto, 'email' => $email, 'foto_perfil_url' => $fotoUrl];
        $datosPerfil = [
            'bio' => $bio, 'ciudad' => $ciudad, 'pais' => $pais, 'fecha_nacimiento' => $fechaNacimiento,
            'discord_tag' => $discord, 'instagram_url' => $instagram, 'twitter_url' => $twitter,
            'banner_url' => $bannerUrl, 'campos_publicos' => $camposPublicos
        ];

        $this->modeloJugador->actualizarPerfil($usuarioId, $datosUsuario, $datosPerfil);

        //Actualizamos el nombre/email en sesion para que se reflejen sin tener que volver a loguearse
        $_SESSION['usuario_nombre'] = $nombreCompleto;
        $_SESSION['email'] = $email;

        header("Location: " . URL_BASE . "index.php?c=panelJugador&a=perfil&exito=1");
        exit;
    }

    //Devuelve '' si todo esta bien, o el mensaje de error
    private function validarPerfil(int $usuarioId, string $nombre, string $email, string $fechaNacimiento,
                                  string $discord, string $instagram, string $twitter, string $bio): string {
        if ($nombre === '' || $email === '') {
            return "El nombre y el email son obligatorios.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "El email ingresado no es valido.";
        }
        if ($this->modeloJugador->emailEnUso($email, $usuarioId)) {
            return "Ese email ya esta en uso por otra cuenta.";
        }
        if (mb_strlen($bio) > 500) {
            return "La biografia no puede superar los 500 caracteres.";
        }
        if ($fechaNacimiento !== '') {
            $fecha = \DateTime::createFromFormat('Y-m-d', $fechaNacimiento);
            if (!$fecha || $fecha->format('Y-m-d') !== $fechaNacimiento || $fecha > new \DateTime('today')) {
                return "La fecha de nacimiento no es valida.";
            }
        }
        if (mb_strlen($discord) > 60) {
            return "El usuario de Discord es demasiado largo.";
        }
        foreach (['Instagram' => $instagram, 'X' => $twitter] as $red => $url) {
            if ($url === '') { continue; }
            $esUrl = filter_var($url, FILTER_VALIDATE_URL) && preg_match('#^https?://#i', $url);
            if (!$esUrl || mb_strlen($url) > 255) {
                return "El enlace de $red debe ser una URL valida (ej: https://instagram.com/usuario).";
            }
        }
        return '';
    }
}
