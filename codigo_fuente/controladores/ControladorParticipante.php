<?php
require_once __DIR__ . '/../nucleo/ControladorBase.php';
require_once __DIR__ . '/../nucleo/AdministradorSesion.php';
require_once __DIR__ . '/../repositorios/RepositorioParticipante.php';
require_once __DIR__ . '/../repositorios/RepositorioEquipo.php';
require_once __DIR__ . '/../modelos/ParticipanteTorneo.php';

/**
 * Clase ControladorParticipante
 * 
 * Controlador encargado de gestionar las inscripciones de competidores
 * (individuales o en equipo) a los torneos del sistema.
 */
class ControladorParticipante extends ControladorBase {
    /**
     * @var RepositorioParticipante $repositorio Instancia del repositorio de participantes.
     */
    private RepositorioParticipante $repositorio;
    
    /**
     * @var RepositorioEquipo $repositorioEquipo Repositorio de equipos.
     */
    private RepositorioEquipo $repositorioEquipo;

    /**
     * Constructor del controlador.
     */
    public function __construct() {
        $this->repositorio = new RepositorioParticipante();
        $this->repositorioEquipo = new RepositorioEquipo();
    }

    /**
     * Muestra el formulario de inscripción para un torneo o procesa el envío de la misma.
     * 
     * @return void
     */
    public function inscribir(): void {
        AdministradorSesion::iniciar();
        if (!AdministradorSesion::estaAutenticado()) {
            $this->redireccionar('/login');
        }

        /**
         * @var int $usuarioId ID del usuario activo.
         */
        $usuarioId = AdministradorSesion::obtenerUsuarioId();
        /**
         * @var int $torneoId ID del torneo (proporcionado en la URL).
         */
        $torneoId = (int)($_GET['torneo_id'] ?? 1); // Por defecto torneo 1 para pruebas en esta fase

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tokenCsrfRecibido = $_POST['token_csrf'] ?? null;
            if (!AdministradorSesion::validarTokenCSRF($tokenCsrfRecibido)) {
                $this->redireccionar('/torneos?error=csrf');
            }

            /**
             * @var string $tipo Tipo de inscripción ('equipo' o 'usuario').
             */
            $tipo = $_POST['tipo'] ?? 'usuario';
            /**
             * @var int $referenciaId ID del equipo o del usuario.
             */
            $referenciaId = (int)($_POST['referencia_id'] ?? 0);
            
            /**
             * @var string $nombre Nombre legible del participante.
             */
            $nombre = '';

            if ($tipo === 'usuario') {
                $referenciaId = $usuarioId;
                $nombre = $_SESSION['nombre_completo'];
            } elseif ($tipo === 'equipo') {
                // Verificar que el equipo exista
                /**
                 * @var Equipo|null $equipo Objeto del equipo.
                 */
                $equipo = $this->repositorioEquipo->obtenerPorId($referenciaId);
                if (!$equipo) {
                    $this->redireccionar('/torneos?error=equipo_invalido');
                }
                
                // Verificar que quien inscribe sea capitán del equipo
                if (!$this->repositorioEquipo->esCapitan($referenciaId, $usuarioId)) {
                    $this->redireccionar('/torneos?error=no_es_capitan');
                }
                
                $nombre = $equipo->nombreEquipo;
            } else {
                $this->redireccionar('/torneos?error=tipo_invalido');
            }

            // Validar duplicado
            if ($this->repositorio->estaInscrito($torneoId, $tipo, $referenciaId)) {
                $this->redireccionar('/torneos?error=ya_inscrito');
            }

            /**
             * @var ParticipanteTorneo $participante Nueva inscripción a guardar.
             */
            $participante = new ParticipanteTorneo(
                null,
                $torneoId,
                $tipo,
                $referenciaId,
                $nombre,
                'pendiente' // Queda pendiente de aprobación del organizador por defecto
            );

            if ($this->repositorio->inscribir($participante)) {
                $this->redireccionar('/torneos?mensaje_exito=inscripcion_completada');
            } else {
                $this->redireccionar('/torneos?error=inscripcion_fallida');
            }
        } else {
            // Cargar datos para el formulario de inscripción GET
            /**
             * @var Equipo[] $misEquipos Equipos donde el usuario es capitán para permitir inscribirlos.
             */
            $misEquipos = [];
            
            // Buscar equipos del usuario y filtrar los que capitanea
            /**
             * @var Equipo[] $todosEquipos Todos los equipos del sistema.
             */
            $todosEquipos = $this->repositorioEquipo->obtenerTodos();
            foreach ($todosEquipos as $equipo) {
                if ($this->repositorioEquipo->esCapitan($equipo->id, $usuarioId)) {
                    $misEquipos[] = $equipo;
                }
            }

            $tokenCsrf = AdministradorSesion::generarTokenCSRF();
            $this->renderizarVista('participantes/inscribir', [
                'torneo_id' => $torneoId,
                'mis_equipos' => $misEquipos,
                'token_csrf' => $tokenCsrf,
                'titulo' => 'Inscripción a Torneo - SGDM'
            ]);
        }
    }
}
