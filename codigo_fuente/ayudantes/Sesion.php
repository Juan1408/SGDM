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
    private static function iniciarSesion(){
        if (session_status() === PHP_SESSION_NONE) {
            // Seguridad: Configurar parámetros de cookie antes de iniciar la sesión
            session_set_cookie_params([
                'lifetime' => 1800,
                'path'     => '/',
                'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on', // Solo en HTTPS si está activo
                'httponly' => true,      // Inaccesible desde JavaScript (mitiga XSS)
                'samesite' => 'Lax'      // Protección básica anti-CSRF
            ]);
            session_start();
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
        $_SESSION['es_activo'] = $usuarioBD['es_activo'];

        //Control de tiempo de expiración (Timeboxing)
        $_SESSION['expiracion_sesion'] = time() + 1800; //30 minutos

        //Registramos el login en la auditoria
        if (class_exists('App\Ayudantes\Auditoria')) {
            Auditoria::registrar('LOGIN_EXITOSO', "El usuario " . $_SESSION['email'] . " realizó inicio de sesión");
        }
    }

    //Verifica si el usuario está logueado
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
        
        return isset($_SESSION['usuario_id']);
    }

    //Si no esta logueado redirige al login
    public static function requerirLogin() {
        if (!self::estaLogueado()) {
            //Redirigimos al login
            header("Location: " . (\defined('URL_BASE') ? \URL_BASE : '/'));
            //Detenemos la ejecución del script
            exit;
        }
    }

    //Verifica que el usuario sea administrador
    public static function validarAdmin(){
        //primero verifica que este logueado
        self::requerirLogin();

        //Consultamos que el rol sea de administrador
        if ((int)($_SESSION['rol_id'] ?? 0) !== 1) {
            //si no es el administrador lo mandamos al inicio
            header("Location: " . (\defined('URL_BASE') ? \URL_BASE : '/') . "index.php?c=auth&a=requerirLogin&error=acceso_denegado");
            exit;
        }
    }

    // Verifica que el usuario tenga el rol de organizador.
    public static function validarOrganizador(){
        self::requerirLogin();

        if ((int) ($_SESSION['rol_id'] ?? 0) !== 2) {
            header("Location: " . (\defined('URL_BASE') ? \URL_BASE : '/') . "index.php?c=auth&a=requerirLogin&error=acceso_denegado");
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
        if ($_SESSION['rol_id'] !== 3) {
            //si no es jugador lo mandamos al inicio
            header("Location: " . URL_BASE . "index.php?c=auth&a=requerirLogin&error=acceso_denegado");
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
        session_destroy(); //Elimina completamente la sesión

        //Ciberseguridad OWASP - Eliminación de cookies de sesión
        if (ini_get("session.use_cookies")) {
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
}
