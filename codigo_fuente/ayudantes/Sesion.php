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

        //Registramos el login en la auditoria
        if (class_exists('App\Ayudantes\Auditoria')) {
            Auditoria::registrar('LOGIN_EXITOSO', "El usuario " . $_SESSION['email'] . " realizó inicio de sesión");
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
        
        // Registrar en auditoría antes de vaciar la sesión
        if (isset($_SESSION['email']) && class_exists('App\Ayudantes\Auditoria')) {
            Auditoria::registrar('LOGOUT_EXITOSO', "El usuario " . $_SESSION['email'] . " cerró su sesión");
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
