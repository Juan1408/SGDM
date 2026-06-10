<?php
require_once __DIR__ . '/../nucleo/ControladorBase.php';
require_once __DIR__ . '/../nucleo/AdministradorSesion.php';
require_once __DIR__ . '/../repositorios/RepositorioEquipo.php';
require_once __DIR__ . '/../repositorios/RepositorioUsuario.php';
require_once __DIR__ . '/../modelos/Equipo.php';

/**
 * Clase ControladorEquipo
 * 
 * Controlador que procesa las peticiones de creación de equipos, gestión de la plantilla,
 * invitaciones mediante código y administración de admisiones.
 */
class ControladorEquipo extends ControladorBase {
    /**
     * @var RepositorioEquipo $repositorio Instancia del repositorio de equipos.
     */
    private RepositorioEquipo $repositorio;

    /**
     * Constructor del controlador.
     */
    public function __construct() {
        $this->repositorio = new RepositorioEquipo();
    }

    /**
     * Lista todos los equipos activos registrados en el sistema.
     * 
     * @return void
     */
    public function listar(): void {
        AdministradorSesion::iniciar();
        if (!AdministradorSesion::estaAutenticado()) {
            $this->redireccionar('/login');
        }

        /**
         * @var Equipo[] $equipos Colección de todos los equipos.
         */
        $equipos = $this->repositorio->obtenerTodos();
        $tokenCsrf = AdministradorSesion::generarTokenCSRF();

        $this->renderizarVista('equipos/listar', [
            'equipos' => $equipos,
            'token_csrf' => $tokenCsrf,
            'titulo' => 'Equipos - SGDM'
        ]);
    }

    /**
     * Muestra el formulario de creación de equipos o procesa su creación.
     * 
     * @return void
     */
    public function crear(): void {
        AdministradorSesion::iniciar();
        if (!AdministradorSesion::estaAutenticado()) {
            $this->redireccionar('/login');
        }

        /**
         * @var string $metodo Método HTTP de la petición.
         */
        $metodo = $_SERVER['REQUEST_METHOD'];

        if ($metodo === 'POST') {
            /**
             * @var string|null $tokenCsrfRecibido Token CSRF enviado por formulario.
             */
            $tokenCsrfRecibido = $_POST['token_csrf'] ?? null;
            if (!AdministradorSesion::validarTokenCSRF($tokenCsrfRecibido)) {
                $this->redireccionar('/equipos?error=csrf');
            }

            /**
             * @var string $nombre Nombre del equipo.
             * @var string $descripcion Breve reseña.
             */
            $nombre = trim($_POST['nombre_equipo'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            /**
             * @var int $creadoPor ID del usuario en sesión.
             */
            $creadoPor = AdministradorSesion::obtenerUsuarioId();

            /**
             * @var string[] $errores Mensajes de error acumulados.
             */
            $errores = [];

            if (empty($nombre)) {
                $errores[] = "El nombre del equipo es obligatorio.";
            }

            if (empty($errores)) {
                // Generar código de invitación único alfanumérico de 8 caracteres en mayúsculas
                /**
                 * @var string $codigoInvitacion Código de invitación único.
                 */
                $codigoInvitacion = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

                $nuevoEquipo = new Equipo(
                    null,
                    $nombre,
                    null, // escudo_url
                    $descripcion,
                    '',   // fecha_creacion
                    $creadoPor,
                    true,
                    $codigoInvitacion
                );

                if ($this->repositorio->guardar($nuevoEquipo)) {
                    // El fundador se une al equipo automáticamente como jugador activo
                    $this->repositorio->agregarMiembro($nuevoEquipo->id, $creadoPor, 10, 'Capitán');
                    
                    // Se le designa como Capitán Principal
                    $this->repositorio->agregarCapitan($nuevoEquipo->id, $creadoPor, $creadoPor, true);

                    $this->redireccionar('/equipos/ver?id=' . $nuevoEquipo->id);
                } else {
                    $errores[] = "No se pudo registrar el equipo. Es posible que el nombre ya esté en uso.";
                }
            }

            $this->renderizarVista('equipos/crear', [
                'errores' => $errores,
                'token_csrf' => AdministradorSesion::generarTokenCSRF(),
                'titulo' => 'Crear Equipo - SGDM',
                'datos' => $_POST
            ]);
        } else {
            // Cargar formulario GET
            $this->renderizarVista('equipos/crear', [
                'token_csrf' => AdministradorSesion::generarTokenCSRF(),
                'titulo' => 'Crear Equipo - SGDM'
            ]);
        }
    }

    /**
     * Muestra la ficha detallada de un equipo dado su ID.
     * 
     * @return void
     */
    public function ver(): void {
        AdministradorSesion::iniciar();
        if (!AdministradorSesion::estaAutenticado()) {
            $this->redireccionar('/login');
        }

        /**
         * @var int $id ID del equipo a visualizar.
         */
        $id = (int)($_GET['id'] ?? 0);
        /**
         * @var Equipo|null $equipo Objeto del equipo.
         */
        $equipo = $this->repositorio->obtenerPorId($id);

        if (!$equipo) {
            $this->redireccionar('/equipos?error=no_existe');
        }

        /**
         * @var int $usuarioId ID del usuario conectado.
         */
        $usuarioId = AdministradorSesion::obtenerUsuarioId();

        /**
         * @var array[] $miembros Miembros del equipo.
         */
        $miembros = $this->repositorio->obtenerMiembros($id);

        /**
         * @var bool $esCapitán Bandera que indica si el usuario actual es líder del equipo.
         */
        $esCapitan = $this->repositorio->esCapitan($id, $usuarioId);

        /**
         * @var array[] $solicitudes Pendientes para los líderes.
         */
        $solicitudes = [];
        if ($esCapitan) {
            $solicitudes = $this->repositorio->obtenerSolicitudesPendientes($id);
        }

        $tokenCsrf = AdministradorSesion::generarTokenCSRF();

        $this->renderizarVista('equipos/detalle', [
            'equipo' => $equipo,
            'miembros' => $miembros,
            'es_capitan' => $esCapitan,
            'solicitudes' => $solicitudes,
            'token_csrf' => $tokenCsrf,
            'titulo' => $equipo->nombreEquipo . ' - SGDM'
        ]);
    }

    /**
     * Permite a un usuario enviar una solicitud de admisión a un equipo mediante código de invitación.
     * 
     * @return void
     */
    public function unirse(): void {
        AdministradorSesion::iniciar();
        if (!AdministradorSesion::estaAutenticado()) {
            $this->redireccionar('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tokenCsrfRecibido = $_POST['token_csrf'] ?? null;
            if (!AdministradorSesion::validarTokenCSRF($tokenCsrfRecibido)) {
                $this->redireccionar('/equipos?error=csrf');
            }

            /**
             * @var string $codigo Código de invitación ingresado.
             */
            $codigo = trim($_POST['codigo_invitacion'] ?? '');
            /**
             * @var string $mensaje Mensaje del postulante.
             */
            $mensaje = trim($_POST['mensaje'] ?? 'Solicito unirme al equipo.');
            /**
             * @var int $usuarioId ID del usuario postulante.
             */
            $usuarioId = AdministradorSesion::obtenerUsuarioId();

            /**
             * @var Equipo|null $equipo Equipo coincidente con el código.
             */
            $equipo = $this->repositorio->obtenerPorCodigoInvitacion($codigo);

            if (!$equipo) {
                $this->redireccionar('/equipos?error=codigo_invalido');
            }

            // Verificar si el usuario ya es miembro activo del equipo
            /**
             * @var array[] $miembros Plantilla del equipo.
             */
            $miembros = $this->repositorio->obtenerMiembros($equipo->id);
            foreach ($miembros as $miembro) {
                if ((int)$miembro['usuario_id'] === $usuarioId) {
                    $this->redireccionar('/equipos/ver?id=' . $equipo->id . '&error=ya_miembro');
                }
            }

            // Crear solicitud de ingreso
            if ($this->repositorio->crearSolicitud($equipo->id, $usuarioId, $mensaje)) {
                $this->redireccionar('/equipos?mensaje_exito=solicitud_enviada');
            } else {
                $this->redireccionar('/equipos?error=solicitud_fallida');
            }
        } else {
            $this->redireccionar('/equipos');
        }
    }

    /**
     * Permite a los capitanes procesar (aprobar o rechazar) solicitudes de ingreso de postulantes.
     * 
     * @return void
     */
    public function procesarSolicitud(): void {
        AdministradorSesion::iniciar();
        if (!AdministradorSesion::estaAutenticado()) {
            $this->redireccionar('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tokenCsrfRecibido = $_POST['token_csrf'] ?? null;
            if (!AdministradorSesion::validarTokenCSRF($tokenCsrfRecibido)) {
                $this->redireccionar('/equipos?error=csrf');
            }

            /**
             * @var int $solicitudId ID de la solicitud a procesar.
             * @var string $decision Decisión de admisión ('aprobar' o 'rechazar').
             */
            $solicitudId = (int)($_POST['solicitud_id'] ?? 0);
            $decision = $_POST['decision'] ?? '';
            /**
             * @var int $usuarioId ID del capitán que toma la decisión.
             */
            $usuarioId = AdministradorSesion::obtenerUsuarioId();

            /**
             * @var array|null $solicitud Registro de la solicitud.
             */
            $solicitud = $this->repositorio->obtenerSolicitudPorId($solicitudId);

            if (!$solicitud || $solicitud['estado'] !== 'pendiente') {
                $this->redireccionar('/equipos?error=solicitud_invalida');
            }

            /**
             * @var int $equipoId ID del equipo al que postula.
             */
            $equipoId = (int)$solicitud['equipo_id'];

            // Verificar que el usuario que decide sea realmente capitán
            if (!$this->repositorio->esCapitan($equipoId, $usuarioId)) {
                $this->redireccionar('/equipos/ver?id=' . $equipoId . '&error=no_autorizado');
            }

            if ($decision === 'aprobar') {
                // Cambiar estado de solicitud a aprobado
                $this->repositorio->procesarSolicitud($solicitudId, 'aprobado', $usuarioId);
                // Añadir miembro a la plantilla
                $this->repositorio->agregarMiembro($equipoId, (int)$solicitud['usuario_id'], null, 'Jugador');
                $this->redireccionar('/equipos/ver?id=' . $equipoId . '&mensaje_exito=miembro_aprobado');
            } elseif ($decision === 'rechazar') {
                $this->repositorio->procesarSolicitud($solicitudId, 'rechazado', $usuarioId);
                $this->redireccionar('/equipos/ver?id=' . $equipoId . '&mensaje_exito=miembro_rechazado');
            } else {
                $this->redireccionar('/equipos/ver?id=' . $equipoId . '&error=decision_invalida');
            }
        } else {
            $this->redireccionar('/equipos');
        }
    }
}
