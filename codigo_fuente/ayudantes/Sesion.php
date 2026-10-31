<?php
/**
 * ============================================================================
 * CLASE AYUDANTE: Sesion.php
 * ============================================================================
 * Propósito: Gestiona el ciclo de vida de la sesión HTTP y el control de acceso
 *            basado en roles (RBAC - Role-Based Access Control).
 * Ubicación: codigo_fuente/ayudantes/Sesion.php
 * ============================================================================
 */

namespace App\Ayudantes;

use App\Ayudantes\Auditoria;
use App\Modelos\Conexion;

require_once __DIR__ . '/../configuracion/constantes.php';
require_once __DIR__ . '/../ayudantes/Auditorias.php';

class Sesion {

    /**
     * Inicia la sesión si no está activa
     */
    public static function iniciarSesion(){
        if (session_status() === PHP_SESSION_NONE) {
            // Seguridad: Configurar parámetros de cookie antes de iniciar la sesión (si no se enviaron cabeceras)
            if (!headers_sent()) {
                session_set_cookie_params([
                    'lifetime' => 1800,
                    'path'     => '/',
                    'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on', // Solo en HTTPS si está activo
                    'httponly' => true,      // Inaccesible desde JavaScript (mitiga XSS)
                    'samesite' => 'Lax'      // Protección básica anti-CSRF
                ]);
            }
            @session_start();
        }
    }

    public static function iniciarLogin($usuarioBD) {
        self::iniciarSesion();

        //Ciberseguridad OWASP - Regeneración de ID de sesión
        session_regenerate_id(true);   

        //Guardamos los datos vitales del usuario en variables de sesión
        $_SESSION['usuario_id'] = $usuarioBD['id'];
        $_SESSION['email'] = $usuarioBD['email'];
        $_SESSION['rol_id'] = $usuarioBD['rol_id'];
        $_SESSION['usuario_nombre'] = $usuarioBD['nombre_completo'];
        $_SESSION['esta_activo'] = $usuarioBD['esta_activo'] ?? $usuarioBD['es_activo'] ?? 1;

        //Control de tiempo de expiración (Timeboxing)
        $_SESSION['expiracion_sesion'] = time() + 1800; //30 minutos

        //Persistimos ultimo_acceso, el intento exitoso y la sesion activa (panel admin)
        self::registrarAccesoExitoso($usuarioBD);

        //Registramos el login en la auditoria sin bloquear el acceso si falla el registro
        if (class_exists('App\Ayudantes\Auditoria')) {
            try {
                Auditoria::registrar('LOGIN_EXITOSO', "El usuario " . $_SESSION['email'] . " realizó inicio de sesión");
            } catch (\Throwable $e) {
                error_log('No se pudo registrar LOGIN_EXITOSO en logs_actividad: ' . $e->getMessage());
            }
        }
    }

    /**
     * Verifica en tiempo real contra la base de datos si el usuario de la sesión sigue activo
     * y sincroniza su rol actual (Baneo en caliente de usuarios suspendidos y actualización de privilegios).
     * Sigue la política Fail-Closed: si la BD falla o no responde, no permite accesos no autorizados.
     */
    public static function verificarUsuarioActivo(): bool {
        if (!isset($_SESSION['usuario_id'])) {
            return false;
        }

        require_once __DIR__ . '/../modelos/Conexion.php';
        try {
            $bd = \App\Modelos\Conexion::getInstance()->getBD();
            $stmt = $bd->prepare("SELECT esta_activo, rol_id FROM usuarios WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => (int) $_SESSION['usuario_id']]);
            $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$usuario) {
                return false;
            }

            if ((int) $usuario['esta_activo'] !== 1) {
                return false;
            }

            // Sincronización en caliente de rol_id ante promociones o degradaciones administrativas
            if (isset($usuario['rol_id'])) {
                $_SESSION['rol_id'] = (int) $usuario['rol_id'];
            }

            return true;
        } catch (\Throwable $e) {
            // Fail-closed por seguridad: ante caída o bloqueo de BD, denegar acceso y loguear
            if (php_sapi_name() === 'cli' && empty($_SESSION['usuario_id'])) {
                return true;
            }
            error_log("Error de seguridad en verificarUsuarioActivo: " . $e->getMessage());
            return false;
        }
    }

    //Verifica si el usuario está logueado y si su cuenta sigue activa
    public static function estaLogueado(){
        self::iniciarSesion();
        
        // Validar expiración real de la sesión (Timeboxing)
        if (isset($_SESSION['expiracion_sesion'])) {
            if (time() > $_SESSION['expiracion_sesion']) {
                // Sesión expirada por inactividad
                self::destruir();
                return false;
            } else {
                // Renovar la ventana de tiempo de inactividad
                $_SESSION['expiracion_sesion'] = time() + 1800; // +30 minutos
            }
        }
        
        if (!isset($_SESSION['usuario_id'])) {
            return false;
        }

        // Baneo en caliente: verificar vigencia de la cuenta en tiempo real
        if (!self::verificarUsuarioActivo()) {
            self::destruir();
            if (!headers_sent()) {
                header("Location: " . (\defined('URL_BASE') ? \URL_BASE : '/') . "index.php?c=auth&a=mostrarLogin&error=cuenta_suspendida");
                exit;
            }
            return false;
        }

        //Mantenemos fresca la sesion activa para la ficha de auditoria (cada 5 minutos)
        self::tocarSesionActiva();

        return true;
    }

    //Si no esta logueado redirige al login
    public static function requerirLogin() {
        if (!self::estaLogueado()) {
            if (!headers_sent()) {
                $url = (\defined('URL_BASE') ? \URL_BASE : '/') . "index.php?c=auth&a=mostrarLogin";
                if (isset($_GET['error'])) {
                    $url .= "&error=" . urlencode($_GET['error']);
                }
                header("Location: " . $url);
                exit;
            }
            return;
        }
    }

    //Verifica que el usuario sea administrador
    public static function validarAdmin(){
        //primero verifica que este logueado
        self::requerirLogin();

        //Consultamos que el rol sea de administrador
        if ((int)($_SESSION['rol_id'] ?? 0) !== 1) {
            //si no es el administrador lo mandamos al inicio
            header("Location: " . (\defined('URL_BASE') ? \URL_BASE : '/') . "index.php?c=auth&a=mostrarLogin&error=acceso_denegado");
            exit;
        }
    }

    // Verifica que el usuario tenga el rol de organizador.
    public static function validarOrganizador(){
        self::requerirLogin();

        if ((int) ($_SESSION['rol_id'] ?? 0) !== 2) {
            header("Location: " . (\defined('URL_BASE') ? \URL_BASE : '/') . "index.php?c=auth&a=mostrarLogin&error=acceso_denegado");
        exit;
        }
    }

    // Verifica que el usuario organizador esté aprobado/verificado oficialmente por el Administrador
    public static function validarOrganizadorVerificado(){
        self::validarOrganizador();
        $usuarioId = (int) ($_SESSION['usuario_id'] ?? 0);
        
        require_once __DIR__ . '/../modelos/Conexion.php';
        $bd = \App\Modelos\Conexion::getInstance()->getBD();
        $stmt = $bd->prepare("SELECT verificado_oficial FROM perfiles_organizadores WHERE usuario_id = :id LIMIT 1");
        $stmt->execute([':id' => $usuarioId]);
        $perfil = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$perfil || !(bool) $perfil['verificado_oficial']) {
            $_SESSION['mensaje'] = "Tu cuenta de Organizador aún no ha sido aprobada por el Administrador General. No puedes crear torneos hasta ser verificado.";
            header("Location: " . (\defined('URL_BASE') ? \URL_BASE : '/') . "index.php?c=panelOrganizador&a=dashboard");
            exit;
        }
    }

    //Verifica que el usuario sea jugador (rol_id 3)
    public static function validarJugador(){
        //primero verifica que este logueado
        self::requerirLogin();
 
        //Consultamos que el rol sea de jugador
        if ((int) ($_SESSION['rol_id'] ?? 0) !== 3) {
        //si no es jugador lo mandamos al inicio
            header("Location: " . URL_BASE . "index.php?c=auth&a=mostrarLogin&error=acceso_denegado");
        exit;
        }
    }

    //cerrar sesión
    public static function destruir(){
        self::iniciarSesion();
        
        //Cerramos la fila de sesion activa antes de vaciar la sesion
        self::marcarSesionInactiva();

        // Registrar en auditoría antes de vaciar la sesión
        if (isset($_SESSION['email']) && class_exists('App\Ayudantes\Auditoria')) {
            try {
                Auditoria::registrar('LOGOUT_EXITOSO', "El usuario " . $_SESSION['email'] . " cerró su sesión");
            } catch (\Throwable $e) {
                error_log('No se pudo registrar LOGOUT_EXITOSO en logs_actividad: ' . $e->getMessage());
            }
        }
        
        $_SESSION = []; //Vaciamos el array
        if (session_status() === PHP_SESSION_ACTIVE) {
            @session_destroy(); //Elimina completamente la sesión
        }

        //Ciberseguridad OWASP - Eliminación de cookies de sesión
        if (!headers_sent() && ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), "", time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
    }

    // ========================================================================
    // REGISTRO DE ACCESOS Y SESIONES ACTIVAS (auditoría para el panel admin)
    // Estas escrituras son de mejor esfuerzo: nunca deben impedir el login,
    // el logout ni la navegación normal de la plataforma.
    // ========================================================================

    private static function obtenerBdAuditoria(): ?\PDO {
        try {
            require_once __DIR__ . '/../modelos/Conexion.php';
            return Conexion::getInstance()->getBD();
        } catch (\Throwable $e) {
            error_log('No se pudo obtener la conexión para auditoría: ' . $e->getMessage());
            return null;
        }
    }

    //Huella de la sesión actual. Nunca se expone en pantalla ni se guarda en claro.
    private static function tokenSesionActual(): string {
        return hash('sha256', session_id() ?: 'sesion-sin-id');
    }

    private static function ipActual(): string {
        return (string) ($_SERVER['REMOTE_ADDR'] ?? 'Desconocida');
    }

    private static function userAgentActual(): string {
        return substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido'), 0, 1000);
    }

    /**
     * Actualiza ultimo_acceso, registra el intento exitoso y abre la sesión activa.
     */
    public static function registrarAccesoExitoso(array $usuarioBD): void {
        $bd = self::obtenerBdAuditoria();
        if ($bd === null) {
            return;
        }

        $usuarioId = (int) ($usuarioBD['id'] ?? 0);
        $email = (string) ($usuarioBD['email'] ?? '');
        $ip = self::ipActual();
        $userAgent = self::userAgentActual();

        try {
            $stmt = $bd->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = :id");
            $stmt->execute([':id' => $usuarioId]);

            $stmt = $bd->prepare(
                "INSERT INTO logs_acceso (email_intentado, usuario_id, exito, ip_origen, user_agent, mensaje_error)
                 VALUES (:email, :usuario_id, 1, :ip, :user_agent, NULL)"
            );
            $stmt->execute([
                ':email' => $email,
                ':usuario_id' => $usuarioId > 0 ? $usuarioId : null,
                ':ip' => $ip,
                ':user_agent' => $userAgent,
            ]);

            if (session_id() !== '') {
                $token = self::tokenSesionActual();
                $stmt = $bd->prepare(
                    "INSERT INTO sesiones_activas (usuario_id, token_sesion, ip_origen, user_agent, expira_en, activa)
                     VALUES (:usuario_id, :token, :ip, :user_agent, DATE_ADD(NOW(), INTERVAL 7 DAY), 1)
                     ON DUPLICATE KEY UPDATE
                        usuario_id = VALUES(usuario_id),
                        ip_origen = VALUES(ip_origen),
                        user_agent = VALUES(user_agent),
                        ultima_actividad = NOW(),
                        expira_en = VALUES(expira_en),
                        activa = 1"
                );
                $stmt->execute([
                    ':usuario_id' => $usuarioId,
                    ':token' => $token,
                    ':ip' => $ip,
                    ':user_agent' => $userAgent,
                ]);
                $_SESSION['sesion_touch'] = time();
            }
        } catch (\Throwable $e) {
            error_log('No se pudo registrar el acceso exitoso: ' . $e->getMessage());
        }
    }

    /**
     * Registra un intento de acceso fallido en logs_acceso (sin exponer la contraseña).
     */
    public static function registrarAccesoFallido(string $email, ?int $usuarioId, string $mensaje): void {
        $bd = self::obtenerBdAuditoria();
        if ($bd === null) {
            return;
        }

        try {
            $stmt = $bd->prepare(
                "INSERT INTO logs_acceso (email_intentado, usuario_id, exito, ip_origen, user_agent, mensaje_error)
                 VALUES (:email, :usuario_id, 0, :ip, :user_agent, :mensaje)"
            );
            $stmt->execute([
                ':email' => substr($email, 0, 255),
                ':usuario_id' => $usuarioId && $usuarioId > 0 ? $usuarioId : null,
                ':ip' => self::ipActual(),
                ':user_agent' => self::userAgentActual(),
                ':mensaje' => substr($mensaje, 0, 500),
            ]);
        } catch (\Throwable $e) {
            error_log('No se pudo registrar el acceso fallido: ' . $e->getMessage());
        }
    }

    /**
     * Marca como inactiva la fila de sesión asociada a la sesión HTTP actual.
     */
    public static function marcarSesionInactiva(): void {
        if (session_id() === '') {
            return;
        }

        $bd = self::obtenerBdAuditoria();
        if ($bd === null) {
            return;
        }

        try {
            $stmt = $bd->prepare("UPDATE sesiones_activas SET activa = 0 WHERE token_sesion = :token");
            $stmt->execute([':token' => self::tokenSesionActual()]);
        } catch (\Throwable $e) {
            error_log('No se pudo marcar la sesión como inactiva: ' . $e->getMessage());
        }
    }

    /**
     * Refresca ultima_actividad como máximo una vez cada cinco minutos.
     */
    public static function tocarSesionActiva(): void {
        if (empty($_SESSION['usuario_id']) || session_id() === '') {
            return;
        }

        $ahora = time();
        if (isset($_SESSION['sesion_touch']) && ($ahora - (int) $_SESSION['sesion_touch']) < 300) {
            return;
        }
        $_SESSION['sesion_touch'] = $ahora;

        $bd = self::obtenerBdAuditoria();
        if ($bd === null) {
            return;
        }

        try {
            $stmt = $bd->prepare(
                "UPDATE sesiones_activas
                 SET ultima_actividad = NOW(), activa = 1
                 WHERE token_sesion = :token AND usuario_id = :usuario_id"
            );
            $stmt->execute([
                ':token' => self::tokenSesionActual(),
                ':usuario_id' => (int) $_SESSION['usuario_id'],
            ]);
        } catch (\Throwable $e) {
            error_log('No se pudo actualizar la actividad de la sesión: ' . $e->getMessage());
        }
    }

    // ========================================================================
    // PREVENCIÓN CSRF (Cross-Site Request Forgery)
    // ========================================================================

    /**
     * Genera un token CSRF seguro y lo almacena en la sesión.
     */
    public static function generarTokenCSRF(): string {
        self::iniciarSesion();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Compara un token recibido (ej. por POST) con el almacenado en la sesión.
     */
    public static function validarTokenCSRF(?string $token): bool {
        self::iniciarSesion();
        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function requerirTokensCSRF (): void {
        $token = $_POST['csrf_token'] ?? '';

        if (!self::validarTokenCSRF($token)) { 
            http_response_code(419);
            exit('Peticion rechazada por proteccion CSRF');
        }
    }
}
