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

class Sesion {

    /**
     * Inicia la sesión si no está activa
     */
    private static function iniciarSesion(){
        if (session_status() === PHP_SESSION_NONE) {
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
    }

    //Verifica si el usuario está logueado
    public static function estaLogueado(){
        self::iniciarSesion();
        return isset($_SESSION['usuario_id']);
    }

    //Si no esta logueado redirige al login
    public static function requerirLogin() {
        if (!self::estaLogueado()) {
            //Redirigimos al login
            header("Location: " . URL_BASE);
            //Detenemos la ejecución del script
            exit;
        }
    }

    //Verifica que el usuario sea administrador
    public static function validarAdmin(){
        //primero verifica que este logueado
        self::requerirLogin();

        //Consultamos que el rol sea de administrador
        if ($_SESSION['rol_id'] !== 1) {
            //si no es el administrador lo mandamos al inicio
            header("Location: " . URL_BASE . "index.php?c=auth&a=requerirLogin&error=acceso_denegado");
            exit;
        }
    }

    //cerrar sesión
    public static function destruir(){
        self::iniciarSesion();
        $_SESSION = []; //Vaciamos el array
        session_destroy(); //Elimina completamente la sesión

        //Ciberseguridad OWASP - Eliminación de cookies de sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), "", time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
    }
}

