<?php
namespace App\Controladores;

require_once __DIR__ . '/../ayudantes/Sesion.php';
require_once __DIR__ . '/../modelos/Torneo.php';
require_once __DIR__ . '/../modelos/Juego.php';
require_once __DIR__ . '/../modelos/Usuario.php';
use App\Ayudantes\Sesion;
use App\Modelos\Torneo;
use App\Modelos\Juego;
use App\Modelos\Usuario;

class PanelOrganizadorControlador {
    private Torneo $modeloTorneo;
    private \Juego $modeloJuego;
    private Usuario $modeloUsuario;

    public function __construct() {
        Sesion::validarOrganizador();
        $this->modeloTorneo = new Torneo();
        $this->modeloJuego = \Juego::obtenerInstancia();
        $this->modeloUsuario = new Usuario();
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
        $datos = [
            'etapa' => max(1, min(3, (int) ($_GET['etapa'] ?? 1))),
            'torneo' => null,
            'juegos' => $this->modeloJuego->obtenerJuegosActivos(),
            'modalidades' => $this->modeloTorneo->obtenerModalidades(),
            'sistemasPuntuacion' => $this->modeloTorneo->obtenerSistemasPuntuacion(),
            'mensaje' => $_SESSION['mensaje'] ?? null,
        ];
        unset($_SESSION['mensaje']);

        if (isset($_GET['id'])) {
            $datos['torneo'] = $this->modeloTorneo->obtenerBorrador(
                (int) $_GET['id'],
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
            'calendario' => $this->modeloTorneo->obtenerCalendarioOrganizador((int) $_SESSION['usuario_id']),
        ];
        require_once __DIR__ . '/../vistas/organizador/calendario.php';
    }

    public function historial() {
        $datos = [
            'historial' => $this->modeloTorneo->obtenerHistorialOrganizador((int) $_SESSION['usuario_id']),
        ];
        require_once __DIR__ . '/../vistas/organizador/historial.php';
    }

    public function perfil() {
        $datos = [
            'perfil' => $this->modeloUsuario->obtenerPerfilOrganizador((int) $_SESSION['usuario_id']),
            'mensaje' => $_SESSION['mensaje'] ?? null,
        ];
        unset($_SESSION['mensaje']);
        require_once __DIR__ . '/../vistas/organizador/perfil.php';
    }

    public function guardarPerfil() {
        $datos = [
            'nombre_completo' => trim($_POST['nombre_completo'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'nombre_organizacion' => trim($_POST['nombre_organizacion'] ?? ''),
            'bio_organizacion' => trim($_POST['bio_organizacion'] ?? ''),
            'localidad' => trim($_POST['localidad'] ?? ''),
            'telefono_contacto' => trim($_POST['telefono_contacto'] ?? ''),
            'sitio_web' => trim($_POST['sitio_web'] ?? ''),
        ];
        if ($datos['nombre_completo'] === '' || $datos['nombre_organizacion'] === '') {
            $_SESSION['mensaje'] = 'El nombre y la organización son obligatorios.';
        } elseif ($this->modeloUsuario->actualizarPerfilOrganizador((int) $_SESSION['usuario_id'], $datos)) {
            $_SESSION['usuario_nombre'] = $datos['nombre_completo'];
            $_SESSION['mensaje'] = 'Perfil actualizado correctamente.';
        } else {
            $_SESSION['mensaje'] = 'No se pudo actualizar el perfil.';
        }
        header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=perfil');
        exit;
    }

    public function gestionar() {
        $torneo = isset($_GET['id'])
            ? $this->modeloTorneo->obtenerBorrador((int) $_GET['id'], (int) $_SESSION['usuario_id'])
            : null;

        if (!$torneo) {
            $_SESSION['mensaje'] = 'Torneo no encontrado.';
            header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=dashboard');
            exit;
        }

        $datos = [
            'torneo' => $torneo,
            'inscripciones' => $this->modeloTorneo->obtenerInscripciones((int) $torneo['id'], (int) $_SESSION['usuario_id']),
            'cuposOcupados' => $this->modeloTorneo->contarConfirmados((int) $torneo['id']),
            'fixture' => $this->modeloTorneo->obtenerFixture((int) $torneo['id'], (int) $_SESSION['usuario_id']),
            'posiciones' => $this->modeloTorneo->obtenerPosiciones((int) $torneo['id'], (int) $_SESSION['usuario_id']),
            'podio' => $this->modeloTorneo->obtenerPodio((int) $torneo['id']),
            'mensaje' => $_SESSION['mensaje'] ?? null,
        ];
        unset($_SESSION['mensaje']);
        require_once __DIR__ . '/../vistas/organizador/gestionar-torneo.php';
    }

    public function guardarGestion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=dashboard');
            exit;
        }
        $torneoId = (int) ($_POST['torneo_id'] ?? 0);
        $organizadorId = (int) $_SESSION['usuario_id'];
        $datos = [
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'reglas' => trim($_POST['reglas'] ?? ''),
            'comunidad' => trim($_POST['comunidad'] ?? ''),
            'premios' => trim($_POST['premios'] ?? ''),
            'banner_url' => trim($_POST['banner_url'] ?? ''),
            'stream_url' => trim($_POST['stream_url'] ?? ''),
            'fecha_inicio_inscripcion' => $_POST['fecha_inicio_inscripcion'] ?? null,
            'fecha_limite_inscripcion' => $_POST['fecha_limite_inscripcion'] ?? null,
            'fecha_inicio' => $_POST['fecha_inicio'] ?? null,
            'fecha_fin' => $_POST['fecha_fin'] ?? null,
        ];
        $fechasValidas = $datos['fecha_inicio_inscripcion'] <= $datos['fecha_limite_inscripcion']
            && $datos['fecha_limite_inscripcion'] <= $datos['fecha_inicio']
            && $datos['fecha_inicio'] <= $datos['fecha_fin'];
        if (!$fechasValidas || !$this->modeloTorneo->actualizarGestion($torneoId, $organizadorId, $datos)) {
            $_SESSION['mensaje'] = 'No se pudieron guardar los cambios. Revisa las fechas o el estado del torneo.';
        } else {
            $_SESSION['mensaje'] = 'Configuración actualizada correctamente.';
        }
        header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=gestionar&id=' . $torneoId);
        exit;
    }

    public function cambiarEstado() {
        $torneoId = (int) ($_POST['torneo_id'] ?? $_GET['id'] ?? 0);
        $estado = $_POST['estado'] ?? $_GET['estado'] ?? '';
        $correcto = $this->modeloTorneo->cambiarEstado($torneoId, (int) $_SESSION['usuario_id'], $estado);
        $_SESSION['mensaje'] = $correcto ? 'Estado actualizado correctamente.' : 'No se puede realizar esa transición de estado.';
        header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=gestionar&id=' . $torneoId);
        exit;
    }

    public function responderInscripcion() {
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
        header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=gestionar&id=' . $torneoId);
        exit;
    }

    public function generarFixture() {
        $torneoId = (int) ($_POST['torneo_id'] ?? 0);
        $resultado = $this->modeloTorneo->generarFixture($torneoId, (int) $_SESSION['usuario_id']);
        $_SESSION['mensaje'] = $resultado['mensaje'];
        header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=gestionar&id=' . $torneoId);
        exit;
    }

    public function guardarResultado() {
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
        header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=gestionar&id=' . $torneoId);
        exit;
    }

    public function guardarEtapa() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=crear');
            exit;
        }

        $organizadorId = (int) $_SESSION['usuario_id'];
        $etapa = (int) ($_POST['etapa'] ?? 1);
        $torneoId = (int) ($_POST['torneo_id'] ?? 0);

        if ($etapa === 1) {
            $nombre = trim($_POST['nombre'] ?? '');
            $juegoId = (int) ($_POST['juego_id'] ?? 0);
            $formato = $_POST['formato'] ?? '';
            $cupo = (int) ($_POST['cupo_max_equipos'] ?? 0);
            $formatos = ['liga', 'suizo', 'eliminacion_directa'];

            $cupoValido = $formato !== 'eliminacion_directa'
                || in_array($cupo, [2, 4, 8, 16, 32, 64], true);
            $modalidadId = (int) ($_POST['modalidad_id'] ?? 0);
            $sistemaPuntuacionId = (int) ($_POST['sistema_puntuacion_id'] ?? 0);
            $modalidadValida = array_filter($this->modeloTorneo->obtenerModalidades(), static fn ($item) => (int) $item['id'] === $modalidadId) !== [];
            $sistemaValido = $sistemaPuntuacionId === 0 || array_filter($this->modeloTorneo->obtenerSistemasPuntuacion(), static fn ($item) => (int) $item['id'] === $sistemaPuntuacionId) !== [];
            if ($nombre === '' || !in_array($formato, $formatos, true) || $juegoId < 1
                || !$this->modeloJuego->estaActivo($juegoId) || !$modalidadValida || !$sistemaValido
                || $cupo < 2 || !$cupoValido) {
                $_SESSION['mensaje'] = 'Completa correctamente los datos básicos del torneo.';
                header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=crear');
                exit;
            }

            $torneoId = $this->modeloTorneo->crearBorrador([
                'nombre' => $nombre,
                'juego_id' => $juegoId,
                'formato' => $formato,
                'cupo' => $cupo,
                'modalidad_id' => $modalidadId,
                'sistema_puntuacion_id' => $sistemaPuntuacionId ?: null,
                'tipo_resultado' => 'goles',
                'mejor_de' => 1,
            ], $organizadorId);
            $siguienteEtapa = 2;
        } elseif ($etapa === 2 && $torneoId > 0) {
            $datos = [
                'fecha_inicio_inscripcion' => $_POST['fecha_inicio_inscripcion'] ?? null,
                'fecha_limite_inscripcion' => $_POST['fecha_limite_inscripcion'] ?? null,
                'fecha_inicio' => $_POST['fecha_inicio'] ?? null,
                'fecha_fin' => $_POST['fecha_fin'] ?? null,
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'reglas' => trim($_POST['reglas'] ?? ''),
                'ubicacion' => trim($_POST['ubicacion'] ?? ''),
                'localidad' => trim($_POST['localidad'] ?? ''),
                'comunidad' => trim($_POST['comunidad'] ?? ''),
                'premios' => trim($_POST['premios'] ?? ''),
                'banner_url' => trim($_POST['banner_url'] ?? ''),
                'stream_url' => trim($_POST['stream_url'] ?? ''),
                'tipo_resultado' => $_POST['tipo_resultado'] ?? 'goles',
                'mejor_de' => max(1, (int) ($_POST['mejor_de'] ?? 1)),
                'modalidad_id' => (int) ($_POST['modalidad_id'] ?? 1),
                'sistema_puntuacion_id' => (int) ($_POST['sistema_puntuacion_id'] ?? 0) ?: null,
                'actividades' => [],
            ];
            $tiposActividad = ['administrativo', 'reunion', 'competencia', 'premiacion'];
            foreach ($_POST['actividad_titulo'] ?? [] as $indice => $titulo) {
                $datos['actividades'][] = [
                    'titulo' => trim($titulo),
                    'tipo' => in_array($_POST['actividad_tipo'][$indice] ?? '', $tiposActividad, true)
                        ? $_POST['actividad_tipo'][$indice] : 'competencia',
                    'fecha' => $_POST['actividad_fecha'][$indice] ?? '',
                    'hora' => $_POST['actividad_hora'][$indice] ?? '',
                ];
            }
            $tiposResultado = ['goles', 'puntos', 'rondas', 'booleano'];
            if (!in_array($datos['tipo_resultado'], $tiposResultado, true) || $datos['mejor_de'] > 99) {
                $_SESSION['mensaje'] = 'La configuración de resultados no es válida.';
                header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=crear&etapa=2&id=' . $torneoId);
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
                header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=crear&etapa=2&id=' . $torneoId);
                exit;
            }
            if (!$this->modeloTorneo->actualizarEtapaDatos($torneoId, $organizadorId, $datos)) {
                $_SESSION['mensaje'] = 'No se pudo guardar la configuración del torneo.';
            }
            $siguienteEtapa = 3;
        } elseif ($etapa === 3 && $torneoId > 0) {
            if (!$this->modeloTorneo->publicarBorrador($torneoId, $organizadorId)) {
                $_SESSION['mensaje'] = 'Completa las fechas antes de publicar el torneo.';
                $siguienteEtapa = 3;
            } else {
                $_SESSION['mensaje'] = 'Torneo creado y publicado correctamente.';
                header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=dashboard');
                exit;
            }
        } else {
            $_SESSION['mensaje'] = 'La etapa del torneo no es válida.';
            header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=crear');
            exit;
        }

        header('Location: ' . URL_BASE . 'index.php?c=panelOrganizador&a=crear&etapa=' . $siguienteEtapa . '&id=' . $torneoId);
        exit;
    }
}