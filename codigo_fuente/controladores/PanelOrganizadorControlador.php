<?php
namespace App\Controladores;

require_once __DIR__ . '/../ayudantes/Sesion.php';
require_once __DIR__ . '/../ayudantes/SubidaImagen.php';
require_once __DIR__ . '/../modelos/Torneo.php';
require_once __DIR__ . '/../modelos/Juego.php';
require_once __DIR__ . '/../modelos/Organizador.php';

use App\Ayudantes\Sesion;
use App\Ayudantes\SubidaImagen;
use App\Modelos\Torneo;
use App\Modelos\Juego;
use App\Modelos\Organizador;

class PanelOrganizadorControlador {
    private Torneo $modeloTorneo;
    private \Juego $modeloJuego;
    private Organizador $modeloOrganizador;

    public function __construct() {
        Sesion::validarOrganizador();
        $this->modeloTorneo = new Torneo();
        $this->modeloJuego = \Juego::obtenerInstancia();
        $this->modeloOrganizador = new Organizador();
    }

    public function dashboard() {
        $organizadorId = (int) $_SESSION['usuario_id'];
        $datos = [
            'nombre' => $_SESSION['usuario_nombre'] ?? 'Organizador',
            'estadisticas' => $this->modeloTorneo->obtenerEstadisticasOrganizador($organizadorId),
            'torneos' => $this->modeloTorneo->obtenerTorneosPorOrganizador($organizadorId),
        ];

        require_once __DIR__ . '/../vistas/organizador/dashboard.php';
    }

    public function crear() {
        Sesion::validarOrganizadorVerificado();
        $juegos = $this->modeloJuego->obtenerJuegosActivos();

        $reglasPorJuego = [];
        foreach ($juegos as $j) {
            $jId = (int) $j['id'];
            $modalidadesJuego = $this->modeloJuego->obtenerModalidadesPermitidas($jId);
            $formatosJuego = $this->modeloJuego->obtenerFormatosPermitidos($jId);
            $reglasPorJuego[$jId] = [
                'id' => $jId,
                'nombre' => $j['nombre'],
                'puntos_victoria' => (float) ($j['puntos_victoria'] ?? 3.0),
                'puntos_empate' => (float) ($j['puntos_empate'] ?? 1.0),
                'puntos_derrota' => (float) ($j['puntos_derrota'] ?? 0.0),
                'modalidades' => array_values(array_map(static fn ($m) => [
                    'id' => (int) $m['id'],
                    'nombre' => (string) $m['nombre'],
                ], $modalidadesJuego)),
                'formatos' => array_values(array_map(static fn ($f) => [
                    'codigo' => (string) $f['codigo'],
                    'nombre' => (string) $f['nombre'],
                ], $formatosJuego)),
            ];
        }

        $datos = [
            'etapa' => max(1, min(4, (int) ($_GET['etapa'] ?? 1))),
            'torneo' => null,
            'juegos' => $juegos,
            'modalidades' => $this->modeloTorneo->obtenerModalidades(),
            'sistemasPuntuacion' => $this->modeloTorneo->obtenerSistemasPuntuacion(),
            'reglasPorJuego' => $reglasPorJuego,
            'actividades' => [],
            'mensaje' => $_SESSION['mensaje'] ?? null,
        ];
        unset($_SESSION['mensaje']);

        // Datos que el usuario había escrito antes de un error (solo si es de esta misma etapa)
        $previo = $_SESSION['formulario_previo'] ?? null;
        unset($_SESSION['formulario_previo']);
        $datos['previo'] = (is_array($previo) && ($previo['etapa'] ?? 0) === $datos['etapa'])
            ? $previo['datos']
            : [];

        if (isset($_GET['id'])) {
            $datos['torneo'] = $this->modeloTorneo->obtenerBorrador(
                (int) $_GET['id'],
                (int) $_SESSION['usuario_id']
            );
        }

                if ($datos['torneo'] && $datos['etapa'] >= 3) {
            $datos['actividades'] = $this->modeloTorneo->obtenerActividades(
                (int) $datos['torneo']['id'],
                (int) $_SESSION['usuario_id']
            );
        }

        require_once __DIR__ . '/../vistas/organizador/crear-torneo.php';
    }

    public function misTorneos() {
        $estado = $_GET['estado'] ?? null;
        $estadosPermitidos = ['borrador', 'inscripciones_abiertas', 'en_curso', 'finalizado', 'cancelado'];
        $torneos = $this->modeloTorneo->obtenerTodosPorOrganizador((int) $_SESSION['usuario_id']);
        if (in_array($estado, $estadosPermitidos, true)) {
            $torneos = array_values(array_filter($torneos, static fn ($torneo) => $torneo['estado'] === $estado));
        }

        $datos = [
            'torneos' => $torneos,
            'estado' => $estado,
            'mensaje' => $_SESSION['mensaje'] ?? null,
        ];
        unset($_SESSION['mensaje']);
        require_once __DIR__ . '/../vistas/organizador/mis-torneos.php';
    }

    public function resultados() {
        $datos = [
            'resultados' => $this->modeloTorneo->obtenerResultadosOrganizador((int) $_SESSION['usuario_id']),
        ];
        require_once __DIR__ . '/../vistas/organizador/resultados.php';
    }

    public function calendario() {
        $datos = [
            'calendario' => $this->modeloTorneo->obtenerPartidosCalendarioOrganizador((int) $_SESSION['usuario_id']),
        ];
        require_once __DIR__ . '/../vistas/organizador/calendario.php';
    }

    public function historial() {
        $datos = [
            'historial' => $this->modeloTorneo->obtenerHistorialOrganizador((int) $_SESSION['usuario_id']),
        ];
        require_once __DIR__ . '/../vistas/organizador/historial.php';
    }

    //Muestra la pagina para editar el perfil propio
    public function editarPerfil() {
        $this->mostrarEdicionPerfil();
    }

    public function perfil() {
        $this->mostrarEdicionPerfil();
    }

    private function mostrarEdicionPerfil(string $error = '', array $enviados = []) {
        $datos = $this->modeloOrganizador->obtenerPerfil((int) $_SESSION['usuario_id']);
        if (!$datos) {
            http_response_code(404);
            echo 'Perfil no encontrado.';
            return;
        }
        $camposPublicos = Organizador::listaCamposPublicos($datos['campos_publicos'] ?? '');

        //Si hubo un error, se muestran los valores que habia escrito
        if (!empty($enviados)) {
            $datos = array_merge($datos, $enviados['datos']);
            $camposPublicos = $enviados['publico'];
        }

        require_once __DIR__ . '/../vistas/organizador/editar-perfil-organizador.php';
        
    }

    //Recibe el formulario de editar perfil (solo POST)
    public function guardarPerfil() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=editarPerfil');
            exit;
        }

        Sesion::requerirTokensCSRF();

        $organizadorId = (int) $_SESSION['usuario_id'];

        $datos = [
            'nombre_completo' => trim($_POST['nombre_completo'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'nombre_organizacion' => trim($_POST['nombre_organizacion'] ?? ''),
            'bio_organizacion' => trim($_POST['bio_organizacion'] ?? ''),
            'localidad' => trim($_POST['localidad'] ?? ''),
            'telefono_contacto' => trim($_POST['telefono_contacto'] ?? ''),
            'sitio_web' => trim($_POST['sitio_web'] ?? ''),
        ];

        //Solo se aceptan los campos opcionales conocidos
        $publico = array_values(array_intersect(Organizador::CAMPOS_OPCIONALES, (array) ($_POST['publico'] ?? [])));
        $enviados = ['datos' => $datos, 'publico' => $publico];

        $error = '';
        if ($datos['nombre_completo'] === '' || $datos['nombre_organizacion'] === '') {
            $error = 'El nombre y la organización son obligatorios.';
        } elseif (mb_strlen($datos['nombre_completo']) > 120 || mb_strlen($datos['nombre_organizacion']) > 150) {
            $error = 'El nombre o la organización son demasiado largos.';
        } elseif ($datos['telefono'] !== '' && !preg_match('/^[0-9+()\-\s]{6,20}$/', $datos['telefono'])) {
            $error = 'El teléfono personal no es válido.';
        } elseif ($datos['telefono_contacto'] !== '' && !preg_match('/^[0-9+()\-\s]{6,50}$/', $datos['telefono_contacto'])) {
            $error = 'El teléfono de contacto no es válido.';
        } elseif (mb_strlen($datos['localidad']) > 100) {
            $error = 'La localidad es demasiado larga.';
        } elseif ($datos['sitio_web'] !== '' && (mb_strlen($datos['sitio_web']) > 255
                  || !filter_var($datos['sitio_web'], FILTER_VALIDATE_URL)
                  || !preg_match('#^https?://#i', $datos['sitio_web']))) {
            $error = 'El sitio web debe ser una dirección válida que empiece con http:// o https://';
        } elseif (mb_strlen($datos['bio_organizacion']) > 2000) {
            $error = 'La biografía no puede superar los 2000 caracteres.';
        }

        $fotoUrl = null;
        $bannerUrl = null;
        if ($error === '') {
            if (SubidaImagen::fueEnviado($_FILES['avatar'] ?? null)) {
                $fotoUrl = SubidaImagen::guardar($_FILES['avatar'], 'perfiles/subidos');
                if ($fotoUrl === null) { $error = 'La foto de perfil no es válida (JPG, PNG o WEBP, máximo 2 MB).'; }
            }
            if ($error === '' && SubidaImagen::fueEnviado($_FILES['banner'] ?? null)) {
                $bannerUrl = SubidaImagen::guardar($_FILES['banner'], 'perfiles/subidos');
                if ($bannerUrl === null) { $error = 'El banner no es válido (JPG, PNG o WEBP, máximo 2 MB).'; }
            }
        }

        if ($error !== '') {
            $this->mostrarEdicionPerfil($error, $enviados);
            return;
        }

        $ok = $this->modeloOrganizador->actualizarPerfil(
            $organizadorId,
            [
                'nombre_completo' => $datos['nombre_completo'],
                'telefono' => $datos['telefono'],
                'foto_perfil_url' => $fotoUrl,
            ],
            [
                'nombre_organizacion' => $datos['nombre_organizacion'],
                'bio_organizacion' => $datos['bio_organizacion'],
                'localidad' => $datos['localidad'],
                'telefono_contacto' => $datos['telefono_contacto'],
                'sitio_web' => $datos['sitio_web'],
                'banner_url' => $bannerUrl,
                'campos_publicos' => implode(',', $publico),
            ]
        );

        if (!$ok) {
            $this->mostrarEdicionPerfil('No se pudo guardar el perfil. Intentá de nuevo.', $enviados);
            return;
        }

        $_SESSION['usuario_nombre'] = $datos['nombre_completo'];
        header('Location: ' . URL_BASE . 'index.php?c=perfil&a=organizador&id=' . $organizadorId . '&exito=1');
        exit;
    }

        public function administrar() {
        $organizadorId = (int) $_SESSION['usuario_id'];
        $torneo = isset($_GET['id'])
            ? $this->modeloTorneo->obtenerBorrador((int) $_GET['id'], $organizadorId)
            : null;

        if (!$torneo) {
            $_SESSION['mensaje'] = 'Torneo no encontrado.';
            header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=dashboard');
            exit;
        }

        $secciones = ['informacion', 'participantes', 'solicitudes', 'fixture', 'resultados', 'posiciones', 'cronograma', 'editar'];
        $seccion = in_array($_GET['seccion'] ?? '', $secciones, true) ? $_GET['seccion'] : 'informacion';
        $modalidades = array_column($this->modeloTorneo->obtenerModalidades(), 'nombre', 'id');

        $datos = [
            'torneo' => $torneo,
            'seccion' => $seccion,
            'nombreModalidad' => ucfirst($modalidades[$torneo['modalidad_id']] ?? ''),
            'cuposOcupados' => $this->modeloTorneo->contarConfirmados((int) $torneo['id']),
            'inscripciones' => $this->modeloTorneo->obtenerInscripciones((int) $torneo['id'], $organizadorId),
            'actividades' => $seccion === 'cronograma'
                ? $this->modeloTorneo->obtenerActividades((int) $torneo['id'], $organizadorId)
                : [],
                'fixture' => in_array($seccion, ['fixture', 'resultados'], true)
                ? $this->modeloTorneo->obtenerFixture((int) $torneo['id'], $organizadorId)
                : [],
                            'posiciones' => $seccion === 'posiciones'
                ? $this->modeloTorneo->obtenerPosiciones((int) $torneo['id'], $organizadorId)
                : [],
            'podio' => $seccion === 'posiciones'
                ? $this->modeloTorneo->obtenerPodio((int) $torneo['id'])
                : null,
            'mensaje' => $_SESSION['mensaje'] ?? null,
            'error' => $_SESSION['error'] ?? null,
        ];
        unset($_SESSION['mensaje'], $_SESSION['error']);
        require_once __DIR__ . '/../vistas/organizador/administrar-torneo.php';
    }

    public function cambiarEstado() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Metodo no permitido');
        }
        Sesion::requerirTokensCSRF();
        $organizadorId = (int) $_SESSION['usuario_id'];
        $torneoId = (int) ($_POST['torneo_id'] ?? $_GET['id'] ?? 0);
        $estado = $_POST['estado'] ?? $_GET['estado'] ?? '';
        $secciones = ['informacion', 'participantes', 'solicitudes', 'fixture', 'resultados', 'posiciones', 'cronograma', 'editar'];
        $seccion = in_array($_POST['seccion'] ?? '', $secciones, true) ? $_POST['seccion'] : 'informacion';

        if ($estado === 'en_curso' && $this->modeloTorneo->obtenerFixture($torneoId, $organizadorId) === []) {
            $correcto = false;
            $error = 'Generá el fixture antes de iniciar el torneo.';
        } else {
            $correcto = $this->modeloTorneo->cambiarEstado($torneoId, $organizadorId, $estado);
            $error = $estado === 'finalizado'
                ? 'Para finalizar, todos los partidos deben tener su resultado cargado.'
                : 'No se puede realizar esa transición de estado.';
        }

        $_SESSION['mensaje'] = $correcto ? 'Estado actualizado correctamente.' : $error;
        header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=administrar&seccion=' . $seccion . '&id=' . $torneoId);
        exit;
    }

    public function responderInscripcion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Metodo no permitido');
        }

        Sesion::requerirTokensCSRF();

        $inscripcionId = (int) ($_POST['inscripcion_id'] ?? 0);
        $respuesta = $_POST['respuesta'] ?? '';
        $torneoId = (int) ($_POST['torneo_id'] ?? 0);
        $correcto = $this->modeloTorneo->responderInscripcion(
            $inscripcionId,
            (int) $_SESSION['usuario_id'],
            $respuesta
        );
        $_SESSION['mensaje'] = $correcto
            ? ($respuesta === 'confirmado' ? 'Equipo aprobado correctamente.' : 'Solicitud rechazada correctamente.')
            : 'No se pudo procesar la solicitud. Verifica el cupo disponible.';
        header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=administrar&seccion=solicitudes&id=' . $torneoId);
        exit;
    }

        public function quitarParticipante() {
        $torneoId = (int) ($_POST['torneo_id'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=dashboard');
            exit;
        }
        Sesion::requerirTokensCSRF();
        $resultado = $this->modeloTorneo->quitarParticipante(
            (int) ($_POST['inscripcion_id'] ?? 0),
            (int) $_SESSION['usuario_id']
        );
        $_SESSION['mensaje'] = $resultado['mensaje'];
        header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=administrar&seccion=participantes&id=' . $torneoId);
        exit;
    }

        public function generarFixture() {
        $torneoId = (int) ($_POST['torneo_id'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=dashboard');
            exit;
        }
        Sesion::requerirTokensCSRF();
        $resultado = $this->modeloTorneo->generarFixture($torneoId, (int) $_SESSION['usuario_id']);
        $_SESSION['mensaje'] = $resultado['mensaje'];
        header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=administrar&seccion=fixture&id=' . $torneoId);
        exit;
    }

    public function programarEncuentro() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=dashboard');
            exit;
        }
        Sesion::requerirTokensCSRF();

        $organizadorId = (int) $_SESSION['usuario_id'];
        $torneoId = (int) ($_POST['torneo_id'] ?? 0);
        $encuentroId = (int) ($_POST['encuentro_id'] ?? 0);
        $ronda = (int) ($_POST['ronda'] ?? 0);
        $fecha = trim($_POST['fecha'] ?? '');
        $hora = trim($_POST['hora'] ?? '');
        $sede = mb_substr(trim($_POST['sede'] ?? ''), 0, 100);
        $volver = URL_BASE . 'index.php?c=panelOrganizador&a=administrar&seccion=fixture&id=' . $torneoId . '&ronda=' . $ronda;

        $torneo = $this->modeloTorneo->obtenerBorrador($torneoId, $organizadorId);
        $fechaObjeto = \DateTime::createFromFormat('Y-m-d', $fecha);
        $fechaOk = $fechaObjeto !== false && $fechaObjeto->format('Y-m-d') === $fecha;
        $horaOk = (bool) preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $hora);

        if (!$torneo) {
            $_SESSION['error'] = 'Torneo no encontrado.';
            $_SESSION['mensaje'] = $_SESSION['error'];
        } elseif (!$fechaOk || !$horaOk) {
            $_SESSION['error'] = 'Ingresá una fecha y una hora válidas.';
            $_SESSION['mensaje'] = $_SESSION['error'];
        } elseif ($torneo['fecha_inicio'] && $torneo['fecha_fin']
            && ($fecha < $torneo['fecha_inicio'] || $fecha > $torneo['fecha_fin'])) {
            $_SESSION['error'] = 'La fecha del partido debe estar dentro de la duración del torneo ('
                . date('d/m/Y', strtotime($torneo['fecha_inicio'])) . ' - '
                . date('d/m/Y', strtotime($torneo['fecha_fin'])) . ').';
            $_SESSION['mensaje'] = $_SESSION['error'];
        } else {
            $resultado = $this->modeloTorneo->programarEncuentro($encuentroId, $organizadorId, $fecha . ' ' . $hora . ':00', $sede);
            if (!$resultado['ok']) {
                $_SESSION['error'] = $resultado['mensaje'];
                $_SESSION['mensaje'] = $resultado['mensaje'];
            } else {
                $_SESSION['mensaje'] = $resultado['mensaje'];
            }
        }
        header('Location: ' . $volver);
        exit;
    }

        public function agregarActividad() {
        $this->gestionarActividad('agregar');
    }

    public function editarActividad() {
        $this->gestionarActividad('editar');
    }

    public function eliminarActividad() {
        $this->gestionarActividad('eliminar');
    }

    private function gestionarActividad(string $accion): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=dashboard');
            exit;
        }
        Sesion::requerirTokensCSRF();

        $organizadorId = (int) $_SESSION['usuario_id'];
        $torneoId = (int) ($_POST['torneo_id'] ?? 0);
        $actividadId = (int) ($_POST['actividad_id'] ?? 0);
        $torneo = $this->modeloTorneo->obtenerBorrador($torneoId, $organizadorId);

        if (!$torneo) {
            $_SESSION['mensaje'] = 'Torneo no encontrado.';
        } elseif (in_array($torneo['estado'], ['finalizado', 'cancelado'], true)) {
            $_SESSION['mensaje'] = 'Las actividades no se pueden modificar en un torneo finalizado o cancelado.';
        } elseif ($accion === 'eliminar') {
            $_SESSION['mensaje'] = $this->modeloTorneo->eliminarActividad($actividadId, $organizadorId)
                ? 'Actividad eliminada correctamente.'
                : 'No se pudo eliminar la actividad.';
        } else {
            $titulo = mb_substr(trim($_POST['titulo'] ?? ''), 0, 150);
            $descripcion = mb_substr(trim($_POST['descripcion'] ?? ''), 0, 255);
            $tipo = $_POST['tipo'] ?? '';
            $fecha = trim($_POST['fecha'] ?? '');
            $hora = trim($_POST['hora'] ?? '');
            $fechaObjeto = \DateTime::createFromFormat('Y-m-d', $fecha);
            $fechaOk = $fechaObjeto !== false && $fechaObjeto->format('Y-m-d') === $fecha;

            if ($titulo === '' || !$fechaOk) {
                $_SESSION['mensaje'] = 'Cada actividad necesita un título y una fecha.';
            } elseif (!in_array($tipo, ['administrativo', 'reunion', 'competencia', 'premiacion'], true)) {
                $_SESSION['mensaje'] = 'Elegí un tipo de actividad válido.';
            } elseif ($hora !== '' && !preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $hora)) {
                $_SESSION['mensaje'] = 'Ingresá una hora válida.';
            } else {
                $actividad = ['titulo' => $titulo, 'descripcion' => $descripcion, 'tipo' => $tipo, 'fecha' => $fecha, 'hora' => $hora];                $correcto = $accion === 'agregar'
                    ? $this->modeloTorneo->agregarActividad($torneoId, $organizadorId, $actividad)
                    : $this->modeloTorneo->editarActividad($actividadId, $organizadorId, $actividad);
                $_SESSION['mensaje'] = $correcto
                    ? ($accion === 'agregar' ? 'Actividad agregada correctamente.' : 'Actividad actualizada correctamente.')
                    : 'No se pudo guardar la actividad.';
            }
        }

        header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=administrar&seccion=cronograma&id=' . $torneoId);
        exit;
    }

        public function editarTorneo() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=dashboard');
            exit;
        }
        Sesion::requerirTokensCSRF();

        $organizadorId = (int) $_SESSION['usuario_id'];
        $torneoId = (int) ($_POST['torneo_id'] ?? 0);
        $volver = URL_BASE . 'index.php?c=panelOrganizador&a=administrar&seccion=editar&id=' . $torneoId;
        $torneo = $this->modeloTorneo->obtenerBorrador($torneoId, $organizadorId);

        if (!$torneo) {
            $_SESSION['mensaje'] = 'Torneo no encontrado.';
            header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=dashboard');
            exit;
        }
        if (in_array($torneo['estado'], ['finalizado', 'cancelado'], true)) {
            $_SESSION['mensaje'] = 'Un torneo finalizado o cancelado no se puede editar.';
            header('Location: ' . $volver);
            exit;
        }

        // Fechas: las que no se pueden editar en este estado se conservan
        $campos = ['fecha_inicio_inscripcion', 'fecha_limite_inscripcion', 'fecha_inicio', 'fecha_fin'];
        $editables = $torneo['estado'] === 'en_curso' ? ['fecha_fin'] : $campos;
        $fechas = [];
        foreach ($campos as $campo) {
            $fechas[$campo] = in_array($campo, $editables, true) ? trim($_POST[$campo] ?? '') : (string) $torneo[$campo];
        }
        $fechasOk = true;
        foreach ($fechas as $valor) {
            $objeto = \DateTime::createFromFormat('Y-m-d', $valor);
            if ($objeto === false || $objeto->format('Y-m-d') !== $valor) {
                $fechasOk = false;
            }
        }

        $streamUrl = trim($_POST['stream_url'] ?? '');

        if (!$fechasOk) {
            $_SESSION['mensaje'] = 'Completá todas las fechas con valores válidos.';
        } elseif ($fechas['fecha_inicio_inscripcion'] > $fechas['fecha_limite_inscripcion']
            || $fechas['fecha_limite_inscripcion'] > $fechas['fecha_inicio']
            || $fechas['fecha_inicio'] > $fechas['fecha_fin']) {
            $_SESSION['mensaje'] = 'Las fechas deben respetar el orden: inicio de inscripción, cierre de inscripción, inicio del torneo y fin del torneo.';
        } elseif ($streamUrl !== '' && (!filter_var($streamUrl, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $streamUrl))) {
            $_SESSION['mensaje'] = 'El link de transmisión debe ser una URL válida que empiece con http:// o https://.';
        } else {
            // Banner nuevo (opcional): si no se sube uno, se conserva el actual
            $banner = null;
            $archivoBanner = $_FILES['banner'] ?? null;
            if ($archivoBanner && $archivoBanner['error'] !== UPLOAD_ERR_NO_FILE) {
                $banner = $this->guardarBanner($archivoBanner);
            }

            if ($archivoBanner && $archivoBanner['error'] !== UPLOAD_ERR_NO_FILE && $banner === null) {
                $_SESSION['mensaje'] = 'El banner debe ser una imagen JPG, PNG o WEBP de hasta 2 MB.';
            } else {
                $premios = [];
                foreach ([1, 2, 3] as $puesto) {
                    $premios[] = mb_substr(trim($_POST['premio_' . $puesto] ?? ''), 0, 60);
                }
                $datos = array_merge($fechas, [
                    'descripcion' => trim($_POST['descripcion'] ?? ''),
                    'reglas' => trim($_POST['reglas'] ?? ''),
                    'comunidad' => trim($_POST['comunidad'] ?? ''),
                    'premios' => implode('', $premios) === '' ? '' : json_encode($premios, JSON_UNESCAPED_UNICODE),
                    'banner_url' => $banner,
                    'stream_url' => $streamUrl,
                ]);
                $_SESSION['mensaje'] = $this->modeloTorneo->actualizarGestion($torneoId, $organizadorId, $datos)
                    ? 'Torneo actualizado correctamente.'
                    : 'No se pudieron guardar los cambios.';
            }
        }

        header('Location: ' . $volver);
        exit;
    }

    public function guardarResultado() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Metodo no permitido');
        }

        Sesion::requerirTokensCSRF();

        $encuentroId = (int) ($_POST['encuentro_id'] ?? 0);
        $torneoId = (int) ($_POST['torneo_id'] ?? 0);
        $local = filter_var($_POST['resultado_local'] ?? null, FILTER_VALIDATE_FLOAT);
        $visitante = filter_var($_POST['resultado_visitante'] ?? null, FILTER_VALIDATE_FLOAT);
        if ($local === false || $visitante === false) {
            $_SESSION['mensaje'] = 'Los resultados deben ser números válidos.';
        } else {
            $resultado = $this->modeloTorneo->guardarResultado($encuentroId, (int) $_SESSION['usuario_id'], $local, $visitante);
            $_SESSION['mensaje'] = $resultado['mensaje'];
        }
        $ronda = (int) ($_POST['ronda'] ?? 0);
        header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=administrar&seccion=resultados&id=' . $torneoId . ($ronda > 0 ? '&ronda=' . $ronda : ''));
        exit;
    }

    public function corregirResultado() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Metodo no permitido');
        }

        Sesion::requerirTokensCSRF();

        $encuentroId = (int) ($_POST['encuentro_id'] ?? 0);
        $torneoId = (int) ($_POST['torneo_id'] ?? 0);
        $local = filter_var($_POST['resultado_local'] ?? null, FILTER_VALIDATE_FLOAT);
        $visitante = filter_var($_POST['resultado_visitante'] ?? null, FILTER_VALIDATE_FLOAT);
        if ($local === false || $visitante === false) {
            $_SESSION['mensaje'] = 'Los resultados deben ser números válidos.';
        } else {
            $resultado = $this->modeloTorneo->corregirResultado($encuentroId, (int) $_SESSION['usuario_id'], $local, $visitante);
            $_SESSION['mensaje'] = $resultado['mensaje'];
        }
        $ronda = (int) ($_POST['ronda'] ?? 0);
        header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=administrar&seccion=resultados&id=' . $torneoId . ($ronda > 0 ? '&ronda=' . $ronda : ''));
        exit;
    }

        public function guardarEtapa() {
        Sesion::validarOrganizadorVerificado();
        $base = URL_BASE . 'index.php?c=panelOrganizador&a=crear';
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $base);
            exit;
        }

        Sesion::requerirTokensCSRF();

        $organizadorId = (int) $_SESSION['usuario_id'];
        $etapa = (int) ($_POST['etapa'] ?? 1);
        $torneoId = (int) ($_POST['torneo_id'] ?? 0);
 
        if ($etapa === 1) {
            // Si viene un torneo_id, se está editando un borrador existente (volvió con "Anterior")
            if ($torneoId > 0) {
                $borrador = $this->modeloTorneo->obtenerBorrador($torneoId, $organizadorId);
                if (!$borrador || $borrador['estado'] !== 'borrador') {
                    $_SESSION['mensaje'] = 'No se encontró el borrador solicitado.';
                    header('Location: ' . $base);
                    exit;
                }
            }
            $volverEtapa1 = $base . ($torneoId > 0 ? '&etapa=1&id=' . $torneoId : '');
 
            $nombre = trim($_POST['nombre'] ?? '');
            $juegoId = (int) ($_POST['juego_id'] ?? 0);
            $formato = $_POST['formato'] ?? '';
            $cupo = (int) ($_POST['cupo_max_equipos'] ?? 0);
            $descripcion = trim($_POST['descripcion'] ?? '');
            $reglas = trim($_POST['reglas'] ?? '');
            $modalidadId = (int) ($_POST['modalidad_id'] ?? 0);
            $sistemaPuntuacionId = (int) ($_POST['sistema_puntuacion_id'] ?? 0);

            // 1. Validar que la disciplina exista y esté activa
            $juego = $this->modeloJuego->obtenerJuegoPorId($juegoId);
            if (!$juego || empty($juego['activo'])) {
                $_SESSION['mensaje'] = 'La disciplina seleccionada no existe o no se encuentra activa.';
                $this->recordarFormulario(1, ['juego_id']);
                header('Location: ' . $volverEtapa1);
                exit;
            }

            // 2. Validar que la modalidad esté permitida específicamente para esta disciplina
            $modalidadesPermitidas = $this->modeloJuego->obtenerModalidadesPermitidas($juegoId);
            $idsModalidadesPermitidas = array_map(static fn ($m) => (int) $m['id'], $modalidadesPermitidas);
            if (!in_array($modalidadId, $idsModalidadesPermitidas, true)) {
                $_SESSION['mensaje'] = 'La modalidad seleccionada no está permitida para ' . $juego['nombre'] . '.';
                $this->recordarFormulario(1, ['modalidad_id']);
                header('Location: ' . $volverEtapa1);
                exit;
            }

            // 3. Validar que el formato de competencia esté permitido para esta disciplina
            $formatosPermitidos = $this->modeloJuego->obtenerFormatosPermitidos($juegoId);
            $codigosFormatosPermitidos = array_column($formatosPermitidos, 'codigo');
            if (!in_array($formato, $codigosFormatosPermitidos, true)) {
                $_SESSION['mensaje'] = 'El formato de competencia seleccionado no está permitido para ' . $juego['nombre'] . '.';
                $this->recordarFormulario(1, ['formato']);
                header('Location: ' . $volverEtapa1);
                exit;
            }

            // 4. Validar cupo según el formato
            $cupoValido = $this->cupoValidoParaFormato($formato, $cupo);
            if ($cupo < 2 || !$cupoValido) {
                $_SESSION['mensaje'] = 'El cupo elegido no es válido para el formato de competencia seleccionado.';
                $this->recordarFormulario(1, ['cupo_max_equipos']);
                header('Location: ' . $volverEtapa1);
                exit;
            }

            // 5. Determinar puntuación por victoria, empate y derrota
            if ($sistemaPuntuacionId > 0) {
                $sistemas = $this->modeloTorneo->obtenerSistemasPuntuacion();
                $sistemaSeleccionado = null;
                foreach ($sistemas as $s) {
                    if ((int) $s['id'] === $sistemaPuntuacionId) {
                        $sistemaSeleccionado = $s;
                        break;
                    }
                }
                if (!$sistemaSeleccionado) {
                    $_SESSION['mensaje'] = 'El sistema de puntuación seleccionado no es válido.';
                    $this->recordarFormulario(1, ['sistema_puntuacion_id']);
                    header('Location: ' . $volverEtapa1);
                    exit;
                }
                $puntosVictoria = (float) $sistemaSeleccionado['puntos_victoria'];
                $puntosEmpate = (float) $sistemaSeleccionado['puntos_empate'];
                $puntosDerrota = (float) $sistemaSeleccionado['puntos_derrota'];
            } else {
                $sistemaPuntuacionId = null;
                $puntosVictoria = (float) ($juego['puntos_victoria'] ?? 3.0);
                $puntosEmpate = (float) ($juego['puntos_empate'] ?? 1.0);
                $puntosDerrota = (float) ($juego['puntos_derrota'] ?? 0.0);
            }

            if ($nombre === '') {
                $_SESSION['mensaje'] = 'El nombre del torneo es obligatorio.';
                $this->recordarFormulario(1);
                header('Location: ' . $volverEtapa1);
                exit;
            }

            // Banner nuevo (opcional): si no se sube uno, se conserva el que ya tenía
            $banner = null;
            $archivoBanner = $_FILES['banner'] ?? null;
            if ($archivoBanner && $archivoBanner['error'] !== UPLOAD_ERR_NO_FILE) {
                $banner = $this->guardarBanner($archivoBanner);
                if ($banner === null) {
                    $_SESSION['mensaje'] = 'El banner debe ser una imagen JPG, PNG o WEBP de hasta 2 MB.';
                    $this->recordarFormulario(1);
                    header('Location: ' . $volverEtapa1);
                    exit;
                }
            }

            $datosGenerales = [
                'nombre' => $nombre,
                'juego_id' => $juegoId,
                'formato' => $formato,
                'cupo' => $cupo,
                'modalidad_id' => $modalidadId,
                'sistema_puntuacion_id' => $sistemaPuntuacionId,
                'puntos_victoria' => $puntosVictoria,
                'puntos_empate' => $puntosEmpate,
                'puntos_derrota' => $puntosDerrota,
                'descripcion' => $descripcion,
                'reglas' => $reglas,
                'banner_url' => $banner,
            ];

            if ($torneoId > 0) {
                if (!$this->modeloTorneo->actualizarEtapaGeneral($torneoId, $organizadorId, $datosGenerales)) {
                    $_SESSION['mensaje'] = 'No se pudieron guardar los cambios del torneo.';
                    $this->recordarFormulario(1);
                    header('Location: ' . $volverEtapa1);
                    exit;
                }
            } else {
                $torneoId = $this->modeloTorneo->crearBorrador(array_merge($datosGenerales, [
                    'banner_url' => $banner ?? '',
                    'tipo_resultado' => 'goles',
                    'mejor_de' => 1,
                ]), $organizadorId);
            }
            $siguienteEtapa = 2;
        } elseif ($etapa === 2 && $torneoId > 0) {
            $volverEtapa2 = $base . '&etapa=2&id=' . $torneoId;
 
            // Ubicación: presencial (dirección + localidad) u online (plataforma)
            if (($_POST['modo_ubicacion'] ?? 'presencial') === 'online') {
                $plataformas = array_values(array_intersect((array) ($_POST['plataforma'] ?? []), ['PC', 'Móvil']));
                if ($plataformas === []) {
                    $_SESSION['mensaje'] = 'Elegí al menos una plataforma para el torneo online (PC o Móvil).';
                    $this->recordarFormulario(2, ['plataforma']);
                    header('Location: ' . $volverEtapa2);
                    exit;
                }
                $ubicacion = 'Online (' . implode(' y ', $plataformas) . ')';
                $localidad = '';
            } else {
                $ubicacion = trim($_POST['ubicacion'] ?? '');
                $localidad = trim($_POST['localidad'] ?? '');
            }
 
            // Premios: primer, segundo y tercer lugar (todos opcionales)
            $premios = [];
            foreach ([1, 2, 3] as $puesto) {
                $premios[] = mb_substr(trim($_POST['premio_' . $puesto] ?? ''), 0, 60);
            }
            $premiosJson = implode('', $premios) === '' ? '' : json_encode($premios, JSON_UNESCAPED_UNICODE);
 
            $datos = [
                'fecha_inicio_inscripcion' => $_POST['fecha_inicio_inscripcion'] ?? null,
                'fecha_limite_inscripcion' => $_POST['fecha_limite_inscripcion'] ?? null,
                'fecha_inicio' => $_POST['fecha_inicio'] ?? null,
                'fecha_fin' => $_POST['fecha_fin'] ?? null,
                'ubicacion' => $ubicacion,
                'localidad' => $localidad,
                'comunidad' => trim($_POST['comunidad'] ?? ''),
                'premios' => $premiosJson,
                'stream_url' => trim($_POST['stream_url'] ?? ''),
                'tipo_resultado' => $_POST['tipo_resultado'] ?? 'goles',
                'mejor_de' => max(1, (int) ($_POST['mejor_de'] ?? 1)),
            ];
            $tiposResultado = ['goles', 'puntos', 'rondas', 'booleano'];
            if (!in_array($datos['tipo_resultado'], $tiposResultado, true) || $datos['mejor_de'] > 99) {
                $_SESSION['mensaje'] = 'La configuración de resultados no es válida.';
                $this->recordarFormulario(2, ['tipo_resultado', 'mejor_de']);
                header('Location: ' . $volverEtapa2);
                exit;
            }
            if ($datos['stream_url'] !== '' && (!filter_var($datos['stream_url'], FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $datos['stream_url']))) {
                $_SESSION['mensaje'] = 'El link de transmisión debe ser una URL válida (http:// o https://).';
                $this->recordarFormulario(2, ['stream_url']);
                header('Location: ' . $volverEtapa2);
                exit;
            }
            $fechasValidas = $datos['fecha_inicio_inscripcion'] !== null
                && $datos['fecha_limite_inscripcion'] !== null
                && $datos['fecha_inicio'] !== null
                && $datos['fecha_fin'] !== null
                && $datos['fecha_inicio_inscripcion'] <= $datos['fecha_limite_inscripcion']
                && $datos['fecha_limite_inscripcion'] <= $datos['fecha_inicio']
                && $datos['fecha_inicio'] <= $datos['fecha_fin'];
            if (!$fechasValidas) {
                $_SESSION['mensaje'] = 'Las fechas deben respetar el orden de cada etapa.';
                $this->recordarFormulario(2, ['fecha_inicio_inscripcion', 'fecha_limite_inscripcion', 'fecha_inicio', 'fecha_fin']);
                header('Location: ' . $volverEtapa2);
                exit;
            }
            if (!$this->modeloTorneo->actualizarEtapaDatos($torneoId, $organizadorId, $datos)) {
                $_SESSION['mensaje'] = 'No se pudo guardar la configuración del torneo.';
                $this->recordarFormulario(2);
                header('Location: ' . $volverEtapa2);
                exit;
            }
            $siguienteEtapa = 3;
        } elseif ($etapa === 3 && $torneoId > 0) {
            // Actividades (opcionales): se ignoran las filas totalmente vacías
            $tiposActividad = ['administrativo', 'reunion', 'competencia', 'premiacion'];
            $actividades = [];
            foreach ($_POST['actividad_titulo'] ?? [] as $indice => $titulo) {
                $titulo = trim($titulo);
                $fecha = trim($_POST['actividad_fecha'][$indice] ?? '');
                $hora = trim($_POST['actividad_hora'][$indice] ?? '');
                $descripcion = mb_substr(trim($_POST['actividad_descripcion'][$indice] ?? ''), 0, 255);
                if ($titulo === '' && $fecha === '') {
                    continue;
                }
                $fechaOk = \DateTime::createFromFormat('Y-m-d', $fecha) !== false;
                if ($titulo === '' || !$fechaOk) {
                    $_SESSION['mensaje'] = 'Cada actividad necesita un título y una fecha.';
                    $this->recordarFormulario(3);
                    header('Location: ' . $base . '&etapa=3&id=' . $torneoId);
                    exit;
                }
                $tipo = $_POST['actividad_tipo'][$indice] ?? '';
                $actividades[] = [
                    'titulo' => mb_substr($titulo, 0, 150),
                    'descripcion' => $descripcion,
                    'tipo' => in_array($tipo, $tiposActividad, true) ? $tipo : 'competencia',
                    'fecha' => $fecha,
                    'hora' => $hora,
                ];
            }
            if (!$this->modeloTorneo->guardarActividades($torneoId, $organizadorId, $actividades)) {
                $_SESSION['mensaje'] = 'No se pudieron guardar las actividades.';
                $this->recordarFormulario(3);
                header('Location: ' . $base . '&etapa=3&id=' . $torneoId);
                exit;
            }
            $siguienteEtapa = 4;
        } elseif ($etapa === 4 && $torneoId > 0) {
            if (!$this->modeloTorneo->publicarBorrador($torneoId, $organizadorId)) {
                $_SESSION['mensaje'] = 'Completa las fechas antes de publicar el torneo.';
                $siguienteEtapa = 4;
            } else {
                $_SESSION['mensaje'] = 'Torneo creado y publicado correctamente.';
                header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=dashboard');
                exit;
            }
        } else {
            $_SESSION['mensaje'] = 'La etapa del torneo no es válida.';
            header('Location: ' . $base);
            exit;
        }
 
        header('Location: ' . $base . '&etapa=' . $siguienteEtapa . '&id=' . $torneoId);
        exit;
    }

    // Cupos permitidos según el formato de competencia
    private function cupoValidoParaFormato(string $formato, int $cupo): bool {
        switch ($formato) {
            case 'liga':
                return $cupo >= 3 && $cupo <= 20;
            case 'suizo':
                return $cupo >= 4 && $cupo <= 64 && $cupo % 2 === 0;
            case 'eliminacion_directa':
                return in_array($cupo, [2, 4, 8, 16, 32, 64], true);
            default:
                return false;
        }
    }
 
    // Guarda lo que el usuario envió para volver a mostrarlo si hay un error.
    // Los campos de $excluir (los que fallaron) vuelven vacíos.
    private function recordarFormulario(int $etapa, array $excluir = []): void {
        $guardado = $_POST;
        unset($guardado['etapa'], $guardado['torneo_id']);
        foreach ($excluir as $campo) {
            $guardado[$campo] = in_array($campo, ['plataforma'], true) ? [] : '';
        }
        $_SESSION['formulario_previo'] = ['etapa' => $etapa, 'datos' => $guardado];
    }
 

        private function guardarBanner(array $archivo): ?string {
        $extensiones = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if ($archivo['error'] !== UPLOAD_ERR_OK || $archivo['size'] > 2 * 1024 * 1024) {
            return null;
        }

        $tipo = (new \finfo(FILEINFO_MIME_TYPE))->file($archivo['tmp_name']);
        if (!isset($extensiones[$tipo]) || @getimagesize($archivo['tmp_name']) === false) {
            return null;
        }

        $carpeta = __DIR__ . '/../publico/img/torneos/subidos/';
        if (!is_dir($carpeta) && !mkdir($carpeta, 0755, true)) {
            return null;
        }

        $nombre = bin2hex(random_bytes(8)) . '.' . $extensiones[$tipo];
        if (!move_uploaded_file($archivo['tmp_name'], $carpeta . $nombre)) {
            return null;
        }

        return 'img/torneos/subidos/' . $nombre;
    }
}
