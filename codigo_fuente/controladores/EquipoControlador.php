<?php
namespace App\Controladores;

use App\Ayudantes\Sesion;
use App\Modelos\Equipo;
use App\Modelos\Guardado;
require_once __DIR__ . '/../ayudantes/Sesion.php';
require_once __DIR__ . '/../modelos/Equipo.php';
require_once __DIR__ . '/../modelos/Guardado.php';

class EquipoControlador {

    private Equipo $modeloEquipo;
    private Guardado $modeloGuardado;

    public function __construct(){
        //Solo jugadores logueados pueden crear/gestionar equipos
        Sesion::validarJugador();
        $this->modeloEquipo = Equipo::obtenerInstancia();
        $this->modeloGuardado = new Guardado();
    }

    //Accion para mostrar el formulario de crear equipo
    public function crear(){
        require_once __DIR__ . '/../vistas/jugador/crear-equipo.php';
    }

        //Accion para listar los equipos del jugador logueado
    public function index(){
        $usuarioId = $_SESSION['usuario_id'];
        $equipos = $this->modeloEquipo->obtenerEquiposDelJugador($usuarioId);

        require_once __DIR__ . '/../vistas/jugador/mis-equipos.php';
    }

    //Accion para procesar el formulario de crear equipo
    public function procesarCreacion(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header("Location: " . URL_BASE . "index.php?c=equipo&a=crear");
            exit;
        }

        $usuarioId = $_SESSION['usuario_id'];

        $nombreEquipo = trim($_POST['nombre_equipo'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $ubicacion = trim($_POST['ubicacion'] ?? '');
        $anioFundacion = trim($_POST['anio_fundacion'] ?? '');

        //Validacion minima
        if (empty($nombreEquipo)) {
            $error = "El nombre del equipo es obligatorio.";
            require_once __DIR__ . '/../vistas/jugador/crear-equipo.php';
            return;
        }

        //Chequeamos que no exista ya un equipo con ese nombre (nombre_equipo es UNIQUE en la BD)
        $equipoExistente = $this->modeloEquipo->buscarPorNombre($nombreEquipo);
        if ($equipoExistente) {
            $error = "Ya existe un equipo con ese nombre.";
            require_once __DIR__ . '/../vistas/jugador/crear-equipo.php';
            return;
        }

        $resultado = $this->modeloEquipo->crearEquipoCompleto($nombreEquipo, $usuarioId, $descripcion ?: null, $ubicacion ?: null, $anioFundacion !== '' ? (int) $anioFundacion : null);
        if (!$resultado['exito']) {
            $error = "No se pudo crear el equipo. Intenta de nuevo.";
            require_once __DIR__ . '/../vistas/jugador/crear-equipo.php';
            return;
        }

        header("Location: " . URL_BASE . "index.php?c=equipo&a=crear&exito=1");
        exit;
    }

        //Accion para ver el perfil publico de un equipo (propio o ajeno)
    public function verPerfil(){
        $equipoId = (int) ($_GET['id'] ?? 0);

        if ($equipoId <= 0) {
            header("Location: " . URL_BASE . "index.php?c=equipo&a=index");
            exit;
        }

        $equipo = $this->modeloEquipo->obtenerPorId($equipoId);

        if (!$equipo) {
            header("Location: " . URL_BASE . "index.php?c=equipo&a=index&error=equipo_no_encontrado");
            exit;
        }

        $usuarioId = $_SESSION['usuario_id'];
        $capitanes = $this->modeloEquipo->obtenerCapitanes($equipoId);
        $miembros = $this->modeloEquipo->obtenerMiembros($equipoId);
        $yaEsMiembro = $this->modeloEquipo->esMiembro($equipoId, $usuarioId);
        $tieneSolicitudPendiente = $this->modeloEquipo->tieneSolicitudPendiente($equipoId, $usuarioId);
        $equipoGuardado = $this->modeloGuardado->estaGuardado($usuarioId, 'equipo', $equipoId);

        //Filtro simple para buscar entre los miembros que YA estan en el equipo (no para invitar gente nueva)
        $filtroMiembro = trim($_GET['filtro'] ?? '');
        if ($filtroMiembro !== '') {
            $miembros = array_filter($miembros, function($miembro) use ($filtroMiembro) {
                return stripos($miembro['nombre_completo'], $filtroMiembro) !== false;
            });
        }

        require_once __DIR__ . '/../vistas/jugador/perfil-equipo.php';
    }

        //Accion para que un jugador pida entrar a un equipo por su cuenta
    public function solicitarUnion(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header("Location: " . URL_BASE . "index.php?c=equipo&a=index");
            exit;
        }

        $usuarioId = $_SESSION['usuario_id'];
        $equipoId = (int) ($_POST['equipo_id'] ?? 0);

        if ($equipoId <= 0 || !$this->modeloEquipo->obtenerPorId($equipoId)) {
            header("Location: " . URL_BASE . "index.php?c=equipo&a=index");
            exit;
        }

        //No dejamos pedir de nuevo si ya sos miembro o si ya tenes una pendiente
        if ($this->modeloEquipo->esMiembro($equipoId, $usuarioId) || $this->modeloEquipo->tieneSolicitudPendiente($equipoId, $usuarioId)) {
            header("Location: " . URL_BASE . "index.php?c=equipo&a=verPerfil&id=" . $equipoId);
            exit;
        }

        //iniciado_por = el propio usuario, porque el pedido lo genera el jugador
        $this->modeloEquipo->crearSolicitud($equipoId, $usuarioId, $usuarioId);

        header("Location: " . URL_BASE . "index.php?c=equipo&a=verPerfil&id=" . $equipoId . "&solicitudEnviada=1");
        exit;
    }

        //Accion para mostrar la bandeja de invitaciones recibidas
    public function solicitudes(){
        $usuarioId = $_SESSION['usuario_id'];
        $invitaciones = $this->modeloEquipo->obtenerInvitacionesRecibidas($usuarioId);

        require_once __DIR__ . '/../vistas/jugador/solicitudes.php';
    }

    //Accion para aceptar o rechazar una invitacion recibida
    public function responderInvitacion(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header("Location: " . URL_BASE . "index.php?c=equipo&a=solicitudes");
            exit;
        }

        $usuarioId = $_SESSION['usuario_id'];
        $solicitudId = (int) ($_POST['solicitud_id'] ?? 0);
        $aceptar = ($_POST['respuesta'] ?? '') === 'aceptar';

        if ($solicitudId > 0) {
            $this->modeloEquipo->responderInvitacion($solicitudId, $usuarioId, $aceptar);
        }

        header("Location: " . URL_BASE . "index.php?c=equipo&a=solicitudes");
        exit;
    }

        //Accion para mostrar la pantalla de gestion (solo el capitan de ESTE equipo puede entrar)
    public function gestionar(){
        $equipoId = (int) ($_GET['id'] ?? 0);
        $usuarioId = $_SESSION['usuario_id'];

        $equipo = $equipoId > 0 ? $this->modeloEquipo->obtenerPorId($equipoId) : false;

            if (!$equipo || !$this->modeloEquipo->esCapitan($equipoId, $usuarioId)) {
            header("Location: " . URL_BASE . "index.php?c=equipo&a=index");
            exit;
        }

                $miembros = $this->modeloEquipo->obtenerMiembros($equipoId);
        $capitanes = $this->modeloEquipo->obtenerCapitanes($equipoId);
        $solicitudesPendientes = $this->modeloEquipo->obtenerSolicitudesPendientesDelEquipo($equipoId);

        require_once __DIR__ . '/../vistas/jugador/gestionar-equipo.php';
    }

    //Accion para que el capitan acepte o rechace una solicitud que un jugador mando a su equipo
    public function responderSolicitudRecibida(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header("Location: " . URL_BASE . "index.php?c=equipo&a=index");
            exit;
        }

        $usuarioId = $_SESSION['usuario_id'];
        $equipoId = (int) ($_POST['equipo_id'] ?? 0);
        $solicitudId = (int) ($_POST['solicitud_id'] ?? 0);
        $aceptar = ($_POST['respuesta'] ?? '') === 'aceptar';

        //Solo el capitan de ESE equipo puede responder
        if ($equipoId <= 0 || !$this->modeloEquipo->esCapitan($equipoId, $usuarioId)) {
            header("Location: " . URL_BASE . "index.php?c=equipo&a=index");
            exit;
        }

        if ($solicitudId > 0) {
            $this->modeloEquipo->responderSolicitudRecibida($solicitudId, $equipoId, $usuarioId, $aceptar);
        }

        header("Location: " . URL_BASE . "index.php?c=equipo&a=gestionar&id=" . $equipoId);
        exit;
    }


    //Accion para que el capitan invite a un jugador encontrado en la busqueda
    public function invitarJugador(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header("Location: " . URL_BASE . "index.php?c=equipo&a=index");
            exit;
        }

        $usuarioId = $_SESSION['usuario_id'];
        $equipoId = (int) ($_POST['equipo_id'] ?? 0);
        $jugadorId = (int) ($_POST['jugador_id'] ?? 0);

        //Solo el capitan de ESE equipo puede invitar
        if ($equipoId <= 0 || !$this->modeloEquipo->esCapitan($equipoId, $usuarioId)) {
            header("Location: " . URL_BASE . "index.php?c=equipo&a=index");
            exit;
        }

        //Evitamos invitar de nuevo si ya es miembro o ya tiene una solicitud pendiente
        if ($jugadorId > 0 && !$this->modeloEquipo->esMiembro($equipoId, $jugadorId) && !$this->modeloEquipo->tieneSolicitudPendiente($equipoId, $jugadorId)) {
            //iniciado_por = el capitan, porque aca es el quien invita
            $this->modeloEquipo->crearSolicitud($equipoId, $jugadorId, $usuarioId);
        }

        header("Location: " . URL_BASE . "index.php?c=equipo&a=gestionar&id=" . $equipoId . "&invitacionEnviada=1");
        exit;
    }

    //Accion para procesar la edicion de los datos del equipo

    //Accion para procesar la edicion de los datos del equipo
    public function actualizarEquipo(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header("Location: " . URL_BASE . "index.php?c=equipo&a=index");
            exit;
        }

        $usuarioId = $_SESSION['usuario_id'];
        $equipoId = (int) ($_POST['equipo_id'] ?? 0);
        $equipo = $equipoId > 0 ? $this->modeloEquipo->obtenerPorId($equipoId) : false;

        //Solo el capitan de ESE equipo puede editarlo
        if (!$equipo || !$this->modeloEquipo->esCapitan($equipoId, $usuarioId)) {
            header("Location: " . URL_BASE . "index.php?c=equipo&a=index");
            exit;
        }

        $nombreEquipo = trim($_POST['nombre_equipo'] ?? '');
        $ubicacion = trim($_POST['ubicacion'] ?? '');
        $anioFundacion = trim($_POST['anio_fundacion'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        $miembros = $this->modeloEquipo->obtenerMiembros($equipoId);
        $capitanes = $this->modeloEquipo->obtenerCapitanes($equipoId);

        if (empty($nombreEquipo)) {
            $error = "El nombre del equipo es obligatorio.";
            require_once __DIR__ . '/../vistas/jugador/gestionar-equipo.php';
            return;
        }

        if ($this->modeloEquipo->equipoNombreEnUso($nombreEquipo, $equipoId)) {
            $error = "Ya existe otro equipo con ese nombre.";
            require_once __DIR__ . '/../vistas/jugador/gestionar-equipo.php';
            return;
        }

        $this->modeloEquipo->actualizarDatos(
            $equipoId,
            $nombreEquipo,
            $ubicacion ?: null,
            $anioFundacion !== '' ? (int) $anioFundacion : null,
            $descripcion ?: null
        );

        header("Location: " . URL_BASE . "index.php?c=equipo&a=gestionar&id=" . $equipoId . "&exito=1");
        exit;
    }

        //Accion para que el capitan expulse a un miembro del equipo
    public function expulsarMiembro(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header("Location: " . URL_BASE . "index.php?c=equipo&a=index");
            exit;
        }

        $usuarioId = $_SESSION['usuario_id'];
        $equipoId = (int) ($_POST['equipo_id'] ?? 0);
        $miembroAExpulsar = (int) ($_POST['usuario_id'] ?? 0);

        //Solo el capitan de ESE equipo puede expulsar
        if ($equipoId <= 0 || !$this->modeloEquipo->esCapitan($equipoId, $usuarioId)) {
            header("Location: " . URL_BASE . "index.php?c=equipo&a=index");
            exit;
        }

        //No te podes expulsar a vos mismo desde aca (eso es "dejar el equipo", paso 3.6.5)
        //Y no se puede expulsar a otro capitan (primero hay que sacarle la capitania)
        if ($miembroAExpulsar !== $usuarioId && !$this->modeloEquipo->esCapitan($equipoId, $miembroAExpulsar)) {
            $this->modeloEquipo->expulsarMiembro($equipoId, $miembroAExpulsar);
        }

        header("Location: " . URL_BASE . "index.php?c=equipo&a=gestionar&id=" . $equipoId);
        exit;
    }

    //Endpoint AJAX: devuelve jugadores disponibles para invitar, en formato JSON
    //Se llama desde JS mientras el capitan escribe en el buscador (sin recargar la pagina)
    public function buscarJugadoresAjax(){
        header('Content-Type: application/json');

        $usuarioId = $_SESSION['usuario_id'];
        $equipoId = (int) ($_GET['id'] ?? 0);
        $busqueda = trim($_GET['buscar'] ?? '');

        //Mismas validaciones que en invitarJugador: solo el capitan de ese equipo puede buscar
        if ($equipoId <= 0 || !$this->modeloEquipo->esCapitan($equipoId, $usuarioId) || $busqueda === '') {
            echo json_encode([]);
            exit;
        }

        $resultados = $this->modeloEquipo->buscarJugadoresParaInvitar($busqueda, $equipoId);
        echo json_encode($resultados);
        exit;
    }

        //Accion para designar a un miembro como sub-capitan
    public function hacerCapitan(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header("Location: " . URL_BASE . "index.php?c=equipo&a=index");
            exit;
        }

        $usuarioId = $_SESSION['usuario_id'];
        $equipoId = (int) ($_POST['equipo_id'] ?? 0);
        $miembroId = (int) ($_POST['usuario_id'] ?? 0);

        //Solo el capitan de ESE equipo puede designar otro capitan
        if ($equipoId > 0 && $this->modeloEquipo->esCapitan($equipoId, $usuarioId) && $miembroId > 0) {
            $this->modeloEquipo->hacerCapitan($equipoId, $miembroId, $usuarioId);
        }

        header("Location: " . URL_BASE . "index.php?c=equipo&a=gestionar&id=" . $equipoId);
        exit;
    }

    //Accion para que el capitan deje el equipo
    public function dejarEquipo(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header("Location: " . URL_BASE . "index.php?c=equipo&a=index");
            exit;
        }

        $usuarioId = $_SESSION['usuario_id'];
        $equipoId = (int) ($_POST['equipo_id'] ?? 0);

        if ($equipoId <= 0 || !$this->modeloEquipo->esCapitan($equipoId, $usuarioId)) {
            header("Location: " . URL_BASE . "index.php?c=equipo&a=index");
            exit;
        }

        $resultado = $this->modeloEquipo->dejarEquipo($equipoId, $usuarioId);

        if (!$resultado['exito']) {
            header("Location: " . URL_BASE . "index.php?c=equipo&a=gestionar&id=" . $equipoId . "&errorDejar=" . urlencode($resultado['error']));
            exit;
        }

        //Ya no gestiona este equipo, lo mandamos a la lista de sus equipos
        header("Location: " . URL_BASE . "index.php?c=equipo&a=index");
        exit;
    }

}
