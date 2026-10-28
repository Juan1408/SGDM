<?php


namespace App\Controladores;
use App\Modelos\Usuario;
use App\Ayudantes\Sesion;
use App\Ayudantes\Auditoria;

require_once __DIR__ . '/../modelos/Usuario.php';
require_once __DIR__ . '/../ayudantes/Sesion.php';
require_once __DIR__ . '/../ayudantes/Auditorias.php';

class UsuarioControlador {
    /** Origenes permitidos para volver tras editar o cancelar. */
    private const ORIGENES_VALIDOS = ['usuarios', 'jugadores', 'organizadores', 'ficha'];

    private Usuario $modeloUsuario;

    public function __construct(){
        // Nota: La validacion de admin se movio a los metodos individuales
        // para permitir que restaurarSesion funcione estando con rol de Jugador/Organizador.
        $this->modeloUsuario = new Usuario();
    }

    /**
     * Normaliza el origen recibido por GET/POST.
     * Solo acepta valores conocidos para evitar redirecciones abiertas.
     */
    private function normalizarOrigen($origen): string {
        $valor = is_string($origen) ? strtolower(trim($origen)) : '';
        return in_array($valor, self::ORIGENES_VALIDOS, true) ? $valor : '';
    }

    /** Listado general correspondiente a un rol. */
    private function urlListadoPorRol(int $rolId): string {
        if ($rolId === 3) {
            return URL_BASE . 'index.php?c=jugador&a=index';
        }
        if ($rolId === 2) {
            return URL_BASE . 'index.php?c=organizador&a=index';
        }
        return URL_BASE . 'index.php?c=usuario&a=index';
    }

    /**
     * URL de retorno según el origen real del administrador.
     * Sin origen válido se conserva el comportamiento previo: listado del rol objetivo.
     */
    private function urlRetorno(string $origen, int $usuarioId, int $rolId): string {
        switch ($origen) {
            case 'usuarios':
                return URL_BASE . 'index.php?c=usuario&a=index';
            case 'jugadores':
                return URL_BASE . 'index.php?c=jugador&a=index';
            case 'organizadores':
                return URL_BASE . 'index.php?c=organizador&a=index';
            case 'ficha':
                return $usuarioId > 0
                    ? URL_BASE . 'index.php?c=usuario&a=ver&id=' . $usuarioId
                    : URL_BASE . 'index.php?c=usuario&a=index';
            default:
                return $this->urlListadoPorRol($rolId);
        }
    }

    /** URL del formulario de edición conservando el origen. */
    private function urlEditar(int $usuarioId, string $origen): string {
        $url = URL_BASE . 'index.php?c=usuario&a=editarUsuario&id=' . $usuarioId;
        return $origen !== '' ? $url . '&origen=' . rawurlencode($origen) : $url;
    }

    /** Panel principal correspondiente a un rol (para retornos seguros). */
    private function urlPanelPorRol(int $rolId): string {
        if ($rolId === 3) {
            return URL_BASE . 'index.php?c=panelJugador&a=dashboard';
        }
        if ($rolId === 2) {
            return URL_BASE . 'index.php?c=panelOrganizador&a=dashboard';
        }
        if ($rolId === 1) {
            return URL_BASE . 'index.php?c=admin&a=dashboard';
        }
        return URL_BASE . 'index.php';
    }

    //Metodo para mostrar el menu
    public function index() {
        Sesion::validarAdmin();
        //Obtenemos todos los usuarios con sus roles
        $listaUsuarios = $this->modeloUsuario->obtenerTodosUsuariosConRoles(1);
        
        //Empaquetamos los datos
        $datos = [
            'usuarios' => $listaUsuarios
        ];

        //Cargamos la vista index.php del directorio jugadores
        $vistaInyectada = '/usuarios/usuarios_index.php';
        require_once __DIR__ . '/../vistas/admin/dashboard.php';
        
    }

    /**
     * Ficha administrativa del usuario (solo lectura).
     * Muestra identidad, contacto, perfiles, equipos y datos de auditoría de acceso.
     * Nunca expone contraseñas, hashes ni tokens de sesión.
     */
    public function ver() {
        Sesion::validarAdmin();

        $usuarioId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int) $_GET['id'] : 0;
        $origen = $this->normalizarOrigen($_GET['origen'] ?? '');
        $ficha = $usuarioId > 0 ? $this->modeloUsuario->obtenerFichaAdmin($usuarioId) : false;

        if (!$ficha) {
            $_SESSION['mensaje'] = "El usuario solicitado no existe.";
            $_SESSION['mensaje_tipo'] = "error";
            header("Location: " . $this->urlRetorno($origen, 0, 0));
            exit();
        }

        // Trazabilidad: toda consulta de datos personales desde el panel queda auditada.
        try {
            Auditoria::registrar(
                'ADMIN_CONSULTA_USUARIO',
                "El administrador consultó la ficha del usuario ID {$usuarioId} ({$ficha['usuario']['email']})"
            );
        } catch (\Throwable $e) {
            error_log('No se pudo auditar la consulta de ficha de usuario: ' . $e->getMessage());
        }

        $datos = $ficha;
        $datos['es_mi_cuenta'] = ((int) ($_SESSION['usuario_id'] ?? 0) === $usuarioId);

        // Para el enlace "Volver" solo tienen sentido los orígenes de listado.
        $origenListado = in_array($origen, ['usuarios', 'jugadores', 'organizadores'], true) ? $origen : '';
        $datos['volver_url'] = $this->urlRetorno($origenListado, $usuarioId, (int) $ficha['usuario']['rol_id']);

        $vistaInyectada = 'usuarios/usuario_detalle.php';
        $tituloPantalla = 'Ficha administrativa del usuario';
        $estilosExtra = ['css/admin/ficha.css'];

        require_once __DIR__ . '/../vistas/admin/dashboard.php';
    }

    //Metodo para cambiar el estado (Activo/Bloqueado) de un usuario
    public function cambiarEstadoUsuario() {
        Sesion::validarAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Metodo no permitido');
        }
        Sesion::requerirTokensCSRF();

        $usuarioId = isset($_POST['id']) && is_numeric($_POST['id']) ? (int) $_POST['id'] : 0;
        $usuario = $usuarioId > 0 ? $this->modeloUsuario->obtenerUsuarioPorId($usuarioId) : false;

        if (!$usuario) {
            $_SESSION['mensaje'] = "El usuario indicado no existe.";
            $_SESSION['mensaje_tipo'] = "error";
            header("Location: " . URL_BASE . "index.php?c=usuario&a=index");
            exit();
        }

        //El administrador no puede bloquear su propia cuenta.
        if ($usuarioId === (int) $_SESSION['usuario_id']) {
            $_SESSION['mensaje'] = "No podés bloquear tu propia cuenta.";
            $_SESSION['mensaje_tipo'] = "error";
            header("Location: " . $this->urlListadoPorRol((int) $usuario['rol_id']));
            exit();
        }

        $estabaActivo = (int) $usuario['esta_activo'] === 1;
        $resultado = $this->modeloUsuario->cambiarEstadoUsuario($usuarioId);

        if ($resultado) {
            // Trazabilidad: bloquear o reactivar una cuenta es una acción sensible.
            try {
                $accionAuditada = $estabaActivo ? 'bloqueó' : 'activó';
                Auditoria::registrar(
                    'ADMIN_CAMBIA_ESTADO_USUARIO',
                    "El administrador {$accionAuditada} al usuario ID {$usuarioId} ({$usuario['email']})"
                );
            } catch (\Throwable $e) {
                error_log('No se pudo auditar el cambio de estado del usuario: ' . $e->getMessage());
            }

            $_SESSION['mensaje'] = $estabaActivo
                ? 'Usuario bloqueado correctamente.'
                : 'Usuario activado correctamente.';
        } else {
            $_SESSION['mensaje'] = "No se pudo cambiar el estado del usuario.";
            $_SESSION['mensaje_tipo'] = "error";
        }

        //Recargamos la lista correspondiente al rol real del usuario afectado
        header("Location: " . $this->urlListadoPorRol((int) $usuario['rol_id']));
        exit();
    }

    //Metodo para cargar la pantalla de edicion de un usuario
    public function editarUsuario(){
        Sesion::validarAdmin();

        $origen = $this->normalizarOrigen($_GET['origen'] ?? '');
        $usuarioId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int) $_GET['id'] : 0;

        //Evitamos que el administrador edite su propia cuenta desde este formulario
        if ($usuarioId <= 0 || $usuarioId === (int) $_SESSION['usuario_id']) {
            header("Location: " . $this->urlRetorno($origen, $usuarioId, 1));
            exit();
        }

        $usuario = $this->modeloUsuario->obtenerUsuarioPorId($usuarioId);
        if (!$usuario) {
            header("Location: " . $this->urlRetorno($origen, 0, 0));
            exit();
        }

        $datos = [
            'usuario' => $usuario,
            'roles' => $this->modeloUsuario->obtenerTodosLosRoles(), //Obtenemos los roles del sistema
            'es_admin' => (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1), //Verificamos si el usuario logueado es admin
            'origen' => $origen,
            'volver_url' => $this->urlRetorno($origen, $usuarioId, (int) $usuario['rol_id']),
            'impacto_rol' => $this->modeloUsuario->obtenerImpactoRol($usuarioId)
        ];

        $vistaInyectada = 'usuarios/usuario_editar.php';
        require_once __DIR__ . '/../vistas/admin/dashboard.php';
    }

    //Metodo para obtener el id de un jugador y usarlo en la accion de suplantacion de identidad
    public function verComo (){
        Sesion::validarAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Metodo no permitido');
        }
        Sesion::requerirTokensCSRF();

        $adminId = (int) $_SESSION['usuario_id'];
        $usuarioId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        //No se suplanta la propia cuenta.
        if ($usuarioId <= 0 || $usuarioId === $adminId) {
            $_SESSION['mensaje'] = "No podés suplantar tu propia cuenta.";
            $_SESSION['mensaje_tipo'] = "error";
            header("Location: " . URL_BASE . "index.php?c=usuario&a=index");
            exit;
        }

        //Traemos todos los datos del usuario desde el modelo
        $usuario = $this->modeloUsuario->obtenerUsuarioPorId($usuarioId);

        //La suplantación está pensada para revisar la experiencia de jugadores y organizadores.
        //Permitir suplantar a otro administrador multiplicaría el riesgo de escalada.
        if ($usuario && (int) $usuario['rol_id'] === 1) {
            $_SESSION['mensaje'] = "No se puede suplantar a otro administrador.";
            $_SESSION['mensaje_tipo'] = "error";
            header("Location: " . URL_BASE . "index.php?c=usuario&a=index");
            exit;
        }

        //Si el usuario existe y esta activo
        if ($usuario && (int) $usuario['esta_activo'] === 1) {
            //Auditamos antes de cambiar la identidad: el autor real es el administrador actual.
            try {
                Auditoria::registrar(
                    'ADMIN_SUPLANTA_INICIO',
                    "El administrador inició suplantación del usuario ID {$usuarioId} ({$usuario['email']})"
                );
            } catch (\Throwable $e) {
                error_log('No se pudo auditar el inicio de suplantación: ' . $e->getMessage());
            }

            //Guardamos el administrador original y sobreescribimos la sesión.
            $_SESSION['admin_antes_suplantar'] = $_SESSION['admin_antes_suplantar'] ?? $adminId;
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre_completo'];
            $_SESSION['rol_id'] = $usuario['rol_id'];

            //OWASP: el cambio de identidad debe renovar el identificador de sesión.
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_regenerate_id(true);
            }

            $_SESSION['mensaje'] = "Estás viendo el perfil como: " . $usuario['nombre_completo'];

            header("Location: " . $this->urlPanelPorRol((int) $usuario['rol_id']));
            exit;
        }

        //Si el usuario no existe o esta bloqueado lo indicamos con un mensaje de error
        $_SESSION['mensaje'] = "No puedes suplantar a un usuario bloqueado o inexistente.";
        $_SESSION['mensaje_tipo'] = "error";

        $rolDestino = (int) ($usuario['rol_id'] ?? 0);
        header("Location: " . ($rolDestino > 0 ? $this->urlListadoPorRol($rolDestino) : URL_BASE . 'index.php?c=usuario&a=index'));
        exit;
    }

    // Metodo para deshacer la suplantacion (Ver Como) y volver al usuario Administrador original
    public function restaurarSesion() {
        // Necesitamos iniciar la sesion para poder leer $_SESSION
        Sesion::requerirLogin();

        //La restauración cambia de identidad: exige POST y token CSRF.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Metodo no permitido');
        }
        if (!Sesion::validarTokenCSRF($_POST['csrf_token'] ?? '')) {
            $_SESSION['mensaje'] = "Error de seguridad (CSRF). Petición denegada.";
            $_SESSION['mensaje_tipo'] = "error";
            header("Location: " . $this->urlPanelPorRol((int) ($_SESSION['rol_id'] ?? 0)));
            exit;
        }

        // Verificamos si existe la variable de sesion original
        if (!isset($_SESSION['admin_antes_suplantar'])) {
            // No estaba suplantando: al inicio por seguridad
            header("Location: " . URL_BASE . "index.php");
            exit;
        }

        // Compatibilidad con sesiones creadas antes de guardar solo el id (formato array)
        $respaldoAdmin = $_SESSION['admin_antes_suplantar'];
        $adminId = is_array($respaldoAdmin)
            ? (int) ($respaldoAdmin['usuario_id'] ?? 0)
            : (int) $respaldoAdmin;

        $admin = $adminId > 0 ? $this->modeloUsuario->obtenerUsuarioPorId($adminId) : false;

        //El administrador original debe seguir siendo admin y estar activo.
        if ($admin && (int) $admin['rol_id'] === 1 && (int) $admin['esta_activo'] === 1) {
            $usuarioSuplantado = (int) ($_SESSION['usuario_id'] ?? 0);

            //Auditamos antes de restaurar: el autor real sigue siendo el administrador.
            try {
                Auditoria::registrar(
                    'ADMIN_SUPLANTA_FIN',
                    "El administrador finalizó la suplantación del usuario ID {$usuarioSuplantado}"
                );
            } catch (\Throwable $e) {
                error_log('No se pudo auditar el fin de suplantación: ' . $e->getMessage());
            }

            $_SESSION['usuario_id'] = $admin['id'];
            $_SESSION['usuario_nombre'] = $admin['nombre_completo'];
            $_SESSION['rol_id'] = $admin['rol_id'];
            unset($_SESSION['admin_antes_suplantar']);

            //OWASP: renovamos el identificador al volver a la identidad original.
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_regenerate_id(true);
            }

            $_SESSION['mensaje'] = "Has vuelto a tu sesión de Administrador original.";
            header("Location: " . URL_BASE . "index.php?c=admin&a=dashboard");
            exit;
        }

        //Fail-closed: si el administrador original ya no es válido, se cierra la sesión.
        unset($_SESSION['admin_antes_suplantar']);
        Sesion::destruir();
        header("Location: " . URL_BASE . "index.php?c=auth&a=mostrarLogin&error=acceso_denegado");
        exit;
    }

    //Metodo para recibir los datos del formulario POST y guardarlos en la base de datos
    public function actualizarUsuario(){
        Sesion::validarAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Metodo no permitido');
        }

        $usuarioId = isset($_POST['id']) && is_numeric($_POST['id']) ? (int) $_POST['id'] : 0;
        $origen = $this->normalizarOrigen($_POST['origen'] ?? '');

        // Estado actual: necesario para validar el destino y auditar el cambio.
        $usuarioActual = $usuarioId > 0 ? $this->modeloUsuario->obtenerUsuarioPorId($usuarioId) : false;
        $rolActual = $usuarioActual ? (int) $usuarioActual['rol_id'] : 0;

        // Seguridad: Validar Token CSRF
        if (!Sesion::validarTokenCSRF($_POST['csrf_token'] ?? '')) {
            $_SESSION['mensaje'] = "Error de seguridad (CSRF). Petición denegada.";
            $_SESSION['mensaje_tipo'] = "error";
            header("Location: " . $this->urlRetorno($origen, $usuarioId, $rolActual));
            exit();
        }

        //No se edita un usuario inexistente ni la propia cuenta del administrador.
        if (!$usuarioActual || $usuarioId === (int) $_SESSION['usuario_id']) {
            $_SESSION['mensaje'] = "El usuario indicado no se puede editar.";
            $_SESSION['mensaje_tipo'] = "error";
            header("Location: " . $this->urlRetorno($origen, 0, 0));
            exit();
        }

        // El email no se recibe del formulario: la verificación de correo es
        // competencia del usuario y un cambio administrativo la invalidaría.
        $datosActualizar = [
            'nombre_completo' => isset($_POST['nombre_completo']) && is_string($_POST['nombre_completo'])
                ? trim($_POST['nombre_completo'])
                : '',
            'rol_id' => isset($_POST['rol_id']) && is_numeric($_POST['rol_id']) ? (int) $_POST['rol_id'] : 0
        ];

        //Validaciones minimas de seguridad
        if ($datosActualizar['nombre_completo'] === '') {
            $_SESSION['mensaje'] = "El nombre completo es obligatorio.";
            $_SESSION['mensaje_tipo'] = "error";
            header("Location: " . $this->urlEditar($usuarioId, $origen));
            exit();
        }
        if (mb_strlen($datosActualizar['nombre_completo'], 'UTF-8') < 2) {
            $_SESSION['mensaje'] = "El nombre completo debe tener al menos 2 caracteres.";
            $_SESSION['mensaje_tipo'] = "error";
            header("Location: " . $this->urlEditar($usuarioId, $origen));
            exit();
        }
        if (mb_strlen($datosActualizar['nombre_completo'], 'UTF-8') > 255) {
            $_SESSION['mensaje'] = "El nombre completo no puede superar los 255 caracteres.";
            $_SESSION['mensaje_tipo'] = "error";
            header("Location: " . $this->urlEditar($usuarioId, $origen));
            exit();
        }

        $rolesSistema = $this->modeloUsuario->obtenerTodosLosRoles();
        $rolesValidos = array_map(static fn ($rol) => (int) $rol['id'], $rolesSistema);
        if (!in_array($datosActualizar['rol_id'], $rolesValidos, true)) {
            $_SESSION['mensaje'] = "El rol seleccionado no es válido.";
            $_SESSION['mensaje_tipo'] = "error";
            header("Location: " . $this->urlEditar($usuarioId, $origen));
            exit();
        }

        //Promover a Administrador es una acción sensible: exige confirmación explícita.
        if ($datosActualizar['rol_id'] === 1 && $rolActual !== 1
            && ($_POST['confirmar_admin'] ?? '') !== '1') {
            $_SESSION['mensaje'] = "Para promover a un usuario a Administrador, confirmá explícitamente la acción.";
            $_SESSION['mensaje_tipo'] = "error";
            header("Location: " . $this->urlEditar($usuarioId, $origen));
            exit();
        }

        if ($usuarioActual['nombre_completo'] === $datosActualizar['nombre_completo']
            && $rolActual === $datosActualizar['rol_id']) {
            $_SESSION['mensaje'] = "El usuario no tenía cambios para guardar.";
            header("Location: " . $this->urlRetorno($origen, $usuarioId, $rolActual));
            exit();
        }

        try {
            $resultado = $this->modeloUsuario->actualizarUsuario($usuarioId, $datosActualizar);
        } catch (\Throwable $e) {
            error_log('Error al actualizar usuario ID ' . $usuarioId . ': ' . $e->getMessage());
            $_SESSION['mensaje'] = "No se pudo actualizar el usuario. Intentá nuevamente.";
            $_SESSION['mensaje_tipo'] = "error";
            header("Location: " . $this->urlEditar($usuarioId, $origen));
            exit();
        }

        if (!$resultado) {
            $_SESSION['mensaje'] = "No se pudo actualizar el usuario. Intentá nuevamente.";
            $_SESSION['mensaje_tipo'] = "error";
            header("Location: " . $this->urlEditar($usuarioId, $origen));
            exit();
        }

        // Trazabilidad del cambio administrativo sobre datos personales.
        try {
            $nombresRoles = [];
            foreach ($rolesSistema as $rol) {
                $nombresRoles[(int) $rol['id']] = $rol['nombre_rol'];
            }
            $rolAnterior = $nombresRoles[$rolActual] ?? (string) $rolActual;
            $rolNuevo = $nombresRoles[$datosActualizar['rol_id']] ?? (string) $datosActualizar['rol_id'];

            Auditoria::registrar(
                'ADMIN_ACTUALIZA_USUARIO',
                "El administrador actualizó el usuario ID {$usuarioId}: nombre \"{$usuarioActual['nombre_completo']}\" → \"{$datosActualizar['nombre_completo']}\"; rol {$rolAnterior} → {$rolNuevo}"
            );
        } catch (\Throwable $e) {
            error_log('No se pudo auditar la actualización de usuario: ' . $e->getMessage());
        }

        $_SESSION['mensaje'] = "Usuario actualizado correctamente";
        header("Location: " . $this->urlRetorno($origen, $usuarioId, $datosActualizar['rol_id']));
        exit();
    }

    //Metodo para dar de baja lógica a un usuario (conserva todo su historial)
    public function eliminarUsuario() {
        //Validamos que el usuario este logueado y sea admin
        Sesion::validarAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Metodo no permitido');
        }

        Sesion::requerirTokensCSRF();

        $usuarioId = isset($_POST['id']) && is_numeric($_POST['id']) ? (int) $_POST['id'] : 0;
        $usuario = $usuarioId > 0 ? $this->modeloUsuario->obtenerUsuarioPorId($usuarioId) : false;

        if (!$usuario) {
            $_SESSION['mensaje'] = "El usuario indicado no existe.";
            $_SESSION['mensaje_tipo'] = "error";
            header("Location: " . URL_BASE . "index.php?c=usuario&a=index");
            exit();
        }

        //El administrador no puede darse de baja a sí mismo.
        if ($usuarioId === (int) $_SESSION['usuario_id']) {
            $_SESSION['mensaje'] = "No podés dar de baja tu propia cuenta.";
            $_SESSION['mensaje_tipo'] = "error";
            header("Location: " . $this->urlListadoPorRol((int) $usuario['rol_id']));
            exit();
        }

        $resultado = $this->modeloUsuario->eliminarUsuario($usuarioId);
        $_SESSION['mensaje'] = $resultado['mensaje'];
        if (empty($resultado['exito'])) {
            $_SESSION['mensaje_tipo'] = "error";
        }

        if (!empty($resultado['exito'])) {
            // Trazabilidad de la baja administrativa.
            try {
                Auditoria::registrar(
                    'ADMIN_BAJA_USUARIO',
                    "El administrador dio de baja al usuario ID {$usuarioId} ({$usuario['email']})"
                );
            } catch (\Throwable $e) {
                error_log('No se pudo auditar la baja del usuario: ' . $e->getMessage());
            }
        }

        header("Location: " . $this->urlListadoPorRol((int) $usuario['rol_id']));
        exit();
    }
}
