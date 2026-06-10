<?php
require_once __DIR__ . '/../nucleo/ControladorBase.php';
require_once __DIR__ . '/../nucleo/AdministradorSesion.php';
require_once __DIR__ . '/../repositorios/RepositorioTorneo.php';
require_once __DIR__ . '/../repositorios/RepositorioModalidad.php';
require_once __DIR__ . '/../repositorios/RepositorioSistemaPuntuacion.php';
require_once __DIR__ . '/../modelos/Torneo.php';

class TorneoControlador extends ControladorBase {
    private RepositorioTorneo $repositorio;
    private RepositorioModalidad $repositorioModalidad;
    private RepositorioSistemaPuntuacion $repositorioSistemaPuntuacion;

    private function puedeCrearTorneo(): bool {
        $rolId = (int)($_SESSION['rol_id'] ?? 0);
        return in_array($rolId, [1, 2], true);
    }

    public function __construct() {
        $this->repositorio = new RepositorioTorneo();
        $this->repositorioModalidad = new RepositorioModalidad();
        $this->repositorioSistemaPuntuacion = new RepositorioSistemaPuntuacion();
    }

    public function listar(): void {
        AdministradorSesion::iniciar();
        if (!AdministradorSesion::estaAutenticado()) {
            $this->redireccionar('/login');
        }

        $torneos = $this->repositorio->obtenerTodos();
        $this->renderizarVista('torneos/listado', [
            'torneos' => $torneos,
            'puede_crear_torneo' => $this->puedeCrearTorneo(),
            'titulo' => 'Torneos - SGDM'
        ]);
    }

    public function crear(): void {
        AdministradorSesion::iniciar();
        if (!AdministradorSesion::estaAutenticado()) {
            $this->redireccionar('/login');
        }

        if (!$this->puedeCrearTorneo()) {
            $this->redireccionar('/torneos?error=sin_permiso_torneo');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token_csrf'] ?? null;
            if (!AdministradorSesion::validarTokenCSRF($token)) {
                $this->redireccionar('/torneos/crear?error=csrf');
            }

            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $formato = $_POST['formato'] ?? 'liga';
            $estado = $_POST['estado'] ?? 'inscripciones_abiertas';
            $modalidadId = (int)($_POST['modalidad_id'] ?? 1);
            $sistemaPuntuacionId = !empty($_POST['sistema_puntuacion_id']) ? (int)$_POST['sistema_puntuacion_id'] : null;
            $cupoMax = (int)($_POST['cupo_max_equipos'] ?? 8);
            $cupoMin = (int)($_POST['cupo_min_equipos'] ?? 2);
            $fechaInicio = $_POST['fecha_inicio'] ?? null;
            $fechaFin = $_POST['fecha_fin'] ?? null;
            $fechaLimite = $_POST['fecha_limite_inscripcion'] ?? null;
            $tipoResultado = $_POST['tipo_resultado'] ?? 'goles';
            $mejorDe = (int)($_POST['mejor_de'] ?? 1);
            $puntosVictoria = (float)($_POST['puntos_victoria'] ?? 3);
            $puntosEmpate = (float)($_POST['puntos_empate'] ?? 1);
            $puntosDerrota = (float)($_POST['puntos_derrota'] ?? 0);

            if ($nombre === '') {
                $this->redireccionar('/torneos/crear?error=nombre_obligatorio');
            }

            $torneo = new Torneo(
                null,
                $nombre,
                $descripcion,
                $formato,
                $estado,
                $cupoMax,
                $cupoMin,
                $fechaInicio,
                $fechaFin,
                $fechaLimite,
                null,
                null,
                null,
                (int)($_SESSION['usuario_id'] ?? 0),
                '',
                '',
                $modalidadId,
                $sistemaPuntuacionId,
                $puntosVictoria,
                $puntosEmpate,
                $puntosDerrota,
                $tipoResultado,
                $mejorDe
            );

            if ($this->repositorio->guardar($torneo)) {
                $this->redireccionar('/torneos?mensaje_exito=torneo_creado');
            }

            $this->redireccionar('/torneos/crear?error=guardar_fallido');
        }

        $modalidades = $this->repositorioModalidad->obtenerTodas();
        $sistemas = $this->repositorioSistemaPuntuacion->obtenerTodos();
        $this->renderizarVista('torneos/crear', [
            'modalidades' => $modalidades,
            'sistemas_puntuacion' => $sistemas,
            'token_csrf' => AdministradorSesion::generarTokenCSRF(),
            'titulo' => 'Crear torneo - SGDM'
        ]);
    }

    public function detalle(): void {
        AdministradorSesion::iniciar();
        if (!AdministradorSesion::estaAutenticado()) {
            $this->redireccionar('/login');
        }

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->redireccionar('/torneos?error=torneo_invalido');
        }

        $torneo = $this->repositorio->obtenerPorId($id);
        if (!$torneo) {
            $this->redireccionar('/torneos?error=torneo_no_encontrado');
        }

        $this->renderizarVista('torneos/detalle', [
            'torneo' => $torneo,
            'titulo' => $torneo->nombre . ' - SGDM'
        ]);
    }

    public function ver(): void {
        $this->detalle();
    }
}
