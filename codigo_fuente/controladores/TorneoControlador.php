<?php
namespace App\Controladores;

use App\Ayudantes\Sesion;
use App\Modelos\Torneo;
use App\Modelos\Equipo;
use App\Modelos\Guardado;
require_once __DIR__ . '/../ayudantes/Sesion.php';
require_once __DIR__ . '/../modelos/Torneo.php';
require_once __DIR__ . '/../modelos/Equipo.php';
require_once __DIR__ . '/../modelos/Guardado.php';

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
        $torneoGuardado = false;

        if ($esJugadorLogueado) {
            $usuarioId = (int) $_SESSION['usuario_id'];
            $misEquipos = $this->modeloEquipo->obtenerEquiposDelJugador($usuarioId);
            $misEquiposCapitan = array_values(array_filter($misEquipos, static function ($equipo) use ($torneo) {
                if (empty($equipo['es_capitan'])) {
                    return false;
                }
                // Si el equipo tiene una disciplina definida, debe coincidir con la del torneo
                if (!empty($equipo['juego_id']) && (int) $equipo['juego_id'] !== (int) $torneo['juego_id']) {
                    return false;
                }
                return true;
            }));
            $equipoIds = array_map(static fn ($equipo) => (int) $equipo['id'], $misEquipos);
            $miInscripcion = $this->modeloTorneo->obtenerInscripcionDelJugador($torneoId, $usuarioId, $equipoIds, true);
            $ultimaInscripcion = $this->modeloTorneo->obtenerInscripcionDelJugador($torneoId, $usuarioId, $equipoIds, false);
            $torneoGuardado = (new Guardado())->estaGuardado($usuarioId, 'torneo', $torneoId);
        }
        $agenda = $this->modeloTorneo->obtenerAgendaPublica($torneoId);
        $participantes = $this->modeloTorneo->obtenerParticipantesPublicos($torneoId);
        $encuentros = $this->modeloTorneo->obtenerFixturePublico($torneoId);
        $posiciones = in_array($torneo['formato'], ['liga', 'suizo'], true)
            ? $this->modeloTorneo->obtenerPosicionesPublicas($torneoId)
            : [];

        //Comentarios: el listado es acumulativo (pagina 2 muestra los 20 primeros)
        $paginaComentarios = max(1, (int) ($_GET['pagina'] ?? 1));
        $comentariosPorPagina = 10;
        $totalComentarios = $this->modeloTorneo->contarComentariosPublicos($torneoId);
        $comentarios = $this->modeloTorneo->obtenerComentariosPublicos(
            $torneoId,
            $comentariosPorPagina * $paginaComentarios
        );
        $hayMasComentarios = $totalComentarios > count($comentarios);

        require_once __DIR__ . '/../vistas/publico/detalle-torneo.php';
    }

    //Accion para publicar un comentario en un torneo. Exige sesion iniciada.
    public function comentar(){
        $torneoId = (int) ($_POST['torneo_id'] ?? 0);
        $destino = $torneoId > 0
            ? URL_BASE . 'index.php?c=torneo&a=detalle&id=' . $torneoId . '#comentarios'
            : URL_BASE . 'index.php?c=torneo&a=catalogoPublico';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $destino);
            exit;
        }

        if (!Sesion::estaLogueado()) {
            $_SESSION['mensaje_error'] = 'Necesitás iniciar sesión para comentar.';
            header('Location: ' . URL_BASE . 'index.php?c=auth&a=mostrarLogin');
            exit;
        }

        //En "Ver como" la sesion es la del usuario suplantado: no se publica en su nombre
        if (!empty($_SESSION['admin_antes_suplantar'])) {
            $_SESSION['mensaje_error'] = 'No podés comentar mientras usás "Ver como".';
            header('Location: ' . $destino);
            exit;
        }

        Sesion::requerirTokensCSRF();

        $torneo = $torneoId > 0 ? $this->modeloTorneo->obtenerTorneoPublico($torneoId) : null;
        if ($torneo === null) {
            $_SESSION['mensaje_error'] = 'El torneo ya no está disponible para comentarios.';
            header('Location: ' . URL_BASE . 'index.php?c=torneo&a=catalogoPublico');
            exit;
        }

        $texto = trim((string) ($_POST['comentario'] ?? ''));
        $largo = function_exists('mb_strlen') ? mb_strlen($texto, 'UTF-8') : strlen($texto);

        if ($largo < 5) {
            $_SESSION['mensaje_error'] = 'Escribí un comentario de al menos 5 caracteres.';
            header('Location: ' . $destino);
            exit;
        }

        if ($largo > 800) {
            $_SESSION['mensaje_error'] = 'El comentario no puede superar los 800 caracteres.';
            header('Location: ' . $destino);
            exit;
        }

        $usuarioId = (int) $_SESSION['usuario_id'];
        if ($this->modeloTorneo->contarComentariosRecientes($usuarioId, $torneoId, 30) > 0) {
            $_SESSION['mensaje_error'] = 'Esperá unos segundos antes de volver a comentar en este torneo.';
            header('Location: ' . $destino);
            exit;
        }

        $nombre = trim((string) ($_SESSION['usuario_nombre'] ?? ''));
        $email = trim((string) ($_SESSION['email'] ?? ''));

        if ($this->modeloTorneo->crearComentario($torneoId, $usuarioId, $nombre !== '' ? $nombre : 'Usuario', $email, $texto, true)) {
            $_SESSION['mensaje_exito'] = 'Tu comentario se publicó.';
        } else {
            $_SESSION['mensaje_error'] = 'No se pudo publicar el comentario. Intentá nuevamente.';
        }

        header('Location: ' . $destino);
        exit;
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
        if (!$torneo) {
            header('Location: ' . URL_BASE . 'index.php?c=torneo&a=catalogoPublico');
            exit;
        }

        $hoy = date('Y-m-d');
        $inscripcionesAbiertas = $torneo['estado'] === 'inscripciones_abiertas'
            && (empty($torneo['fecha_inicio_inscripcion']) || $torneo['fecha_inicio_inscripcion'] <= $hoy)
            && (empty($torneo['fecha_limite_inscripcion']) || $torneo['fecha_limite_inscripcion'] >= $hoy);

        if (!$inscripcionesAbiertas) {
            $_SESSION['mensaje_error'] = 'Las inscripciones para este torneo no se encuentran abiertas en este momento.';
            header('Location: ' . URL_BASE . 'index.php?c=torneo&a=detalle&id=' . $torneoId);
            exit;
        }

        // 1. Validar que el cupo del torneo no esté lleno
        $cuposOcupados = $this->modeloTorneo->contarConfirmados($torneoId);
        if ($cuposOcupados >= (int) ($torneo['cupo_max_equipos'] ?? 0)) {
            $_SESSION['mensaje_error'] = 'El cupo de participantes para este torneo está agotado.';
            header('Location: ' . URL_BASE . 'index.php?c=torneo&a=detalle&id=' . $torneoId);
            exit;
        }

        $esIndividual = strtolower(trim($torneo['nombre_modalidad'])) === 'individual';

        // 2. Validar correspondencia entre tipo enviado y modalidad de la competencia
        if ($esIndividual) {
            if ($tipo !== 'usuario') {
                $_SESSION['mensaje_error'] = 'Este torneo es individual. La inscripción debe ser personal.';
                header('Location: ' . URL_BASE . 'index.php?c=torneo&a=detalle&id=' . $torneoId);
                exit;
            }

            // Validar que no esté ya inscripto activamente
            $yaInscripto = $this->modeloTorneo->obtenerInscripcionDelJugador($torneoId, $usuarioId, [], true);
            if ($yaInscripto) {
                $_SESSION['mensaje_error'] = 'Ya cuentas con una inscripción activa en este torneo.';
                header('Location: ' . URL_BASE . 'index.php?c=torneo&a=detalle&id=' . $torneoId);
                exit;
            }

            $ok = $this->modeloTorneo->inscribirse($torneoId, 'usuario', $usuarioId, $_SESSION['usuario_nombre'] ?? 'Jugador');
            $_SESSION[$ok ? 'mensaje_exito' : 'mensaje_error'] = $ok
                ? '¡Inscripción solicitada con éxito! Está pendiente de confirmación por el organizador.'
                : 'No se pudo procesar la inscripción. Inténtalo de nuevo.';
        } else {
            if ($tipo !== 'equipo') {
                $_SESSION['mensaje_error'] = 'Este torneo es por equipos. Debes seleccionar e inscribir a tu escuadra.';
                header('Location: ' . URL_BASE . 'index.php?c=torneo&a=detalle&id=' . $torneoId);
                exit;
            }

            $equipoId = (int) ($_POST['equipo_id'] ?? 0);
            if ($equipoId <= 0 || !$this->modeloEquipo->esCapitan($equipoId, $usuarioId)) {
                $_SESSION['mensaje_error'] = 'Debes ser capitán del equipo seleccionado para poder inscribirlo.';
                header('Location: ' . URL_BASE . 'index.php?c=torneo&a=detalle&id=' . $torneoId);
                exit;
            }

            $equipo = $this->modeloEquipo->obtenerPorId($equipoId);
            if (!$equipo || empty($equipo['activo'])) {
                $_SESSION['mensaje_error'] = 'El equipo seleccionado no existe o no se encuentra activo.';
                header('Location: ' . URL_BASE . 'index.php?c=torneo&a=detalle&id=' . $torneoId);
                exit;
            }

            // Validar compatibilidad de disciplina
            if (!empty($equipo['juego_id']) && (int) $equipo['juego_id'] !== (int) $torneo['juego_id']) {
                $_SESSION['mensaje_error'] = 'El equipo está configurado para otra disciplina y no puede competir en este torneo.';
                header('Location: ' . URL_BASE . 'index.php?c=torneo&a=detalle&id=' . $torneoId);
                exit;
            }

            // Validar cantidad minima de miembros activos
            $miembrosEquipo = $this->modeloEquipo->obtenerMiembros($equipoId);
            if (count($miembrosEquipo) < 2) {
                $_SESSION['mensaje_error'] = 'El equipo debe contar con al menos 2 miembros activos para competir en un torneo colectivo.';
                header('Location: ' . URL_BASE . 'index.php?c=torneo&a=detalle&id=' . $torneoId);
                exit;
            }

            // Validar que ningun miembro este compitiendo en otro equipo en este mismo torneo
            $miembrosEnConflicto = $this->modeloTorneo->obtenerMiembrosEnConflicto($torneoId, $equipoId);
            if (!empty($miembrosEnConflicto)) {
                $detalles = array_map(static fn ($m) => $m['nombre_completo'] . ' (' . $m['nombre_equipo'] . ')', $miembrosEnConflicto);
                $_SESSION['mensaje_error'] = 'No se puede inscribir al equipo. Los siguientes miembros ya participan en otra escuadra en este torneo: ' . implode(', ', $detalles) . '.';
                header('Location: ' . URL_BASE . 'index.php?c=torneo&a=detalle&id=' . $torneoId);
                exit;
            }

            // Validar si el equipo ya tiene una inscripcion activa
            $misEquipos = $this->modeloEquipo->obtenerEquiposDelJugador($usuarioId);
            $equipoIds = array_map(static fn ($eq) => (int) $eq['id'], $misEquipos);
            $yaInscripto = $this->modeloTorneo->obtenerInscripcionDelJugador($torneoId, $usuarioId, $equipoIds, true);
            if ($yaInscripto) {
                $_SESSION['mensaje_error'] = 'Ya registraste una inscripción activa en este torneo (con este u otro de tus equipos).';
                header('Location: ' . URL_BASE . 'index.php?c=torneo&a=detalle&id=' . $torneoId);
                exit;
            }

            $ok = $this->modeloTorneo->inscribirse($torneoId, 'equipo', $equipoId, $equipo['nombre_equipo']);
            $_SESSION[$ok ? 'mensaje_exito' : 'mensaje_error'] = $ok
                ? '¡Inscripción del equipo solicitada con éxito! Está pendiente de confirmación por el organizador.'
                : 'No se pudo procesar la inscripción del equipo.';
        }

        header('Location: ' . URL_BASE . 'index.php?c=torneo&a=detalle&id=' . $torneoId);
        exit;
    }

    // Accion para que un jugador o capitan retire su inscripcion mientras sigan abiertas
    public function cancelarInscripcion() {
        Sesion::validarJugador();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL_BASE . 'index.php?c=torneo&a=catalogoPublico');
            exit;
        }
        \App\Ayudantes\Sesion::requerirTokensCSRF();

        $torneoId = (int) ($_POST['torneo_id'] ?? 0);
        $tipo = $_POST['tipo'] ?? '';
        $usuarioId = (int) $_SESSION['usuario_id'];

        $torneo = $this->modeloTorneo->obtenerTorneoPublico($torneoId);
        if (!$torneo || $torneo['estado'] !== 'inscripciones_abiertas') {
            $_SESSION['mensaje_error'] = 'Solo se pueden retirar inscripciones mientras el período de inscripción esté abierto.';
            header('Location: ' . URL_BASE . 'index.php?c=torneo&a=detalle&id=' . $torneoId);
            exit;
        }

        if ($tipo === 'usuario') {
            $ok = $this->modeloTorneo->cancelarInscripcionParticipante($torneoId, 'usuario', $usuarioId);
        } elseif ($tipo === 'equipo') {
            $equipoId = (int) ($_POST['equipo_id'] ?? 0);
            if ($equipoId <= 0 || !$this->modeloEquipo->esCapitan($equipoId, $usuarioId)) {
                $_SESSION['mensaje_error'] = 'Solo el capitán del equipo puede cancelar la inscripción.';
                header('Location: ' . URL_BASE . 'index.php?c=torneo&a=detalle&id=' . $torneoId);
                exit;
            }
            $ok = $this->modeloTorneo->cancelarInscripcionParticipante($torneoId, 'equipo', $equipoId);
        } else {
            $ok = false;
        }

        $_SESSION[$ok ? 'mensaje_exito' : 'mensaje_error'] = $ok
            ? 'Tu inscripción ha sido cancelada correctamente.'
            : 'No se pudo cancelar la inscripción.';

        header('Location: ' . URL_BASE . 'index.php?c=torneo&a=detalle&id=' . $torneoId);
        exit;
    }
}