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
        $_SESSION = []; //Vaciamos el array
        session_destroy(); //Elimina completamente la sesión

        //Ciberseguridad OWASP - Eliminación de cookies de sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), "", time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
    }
}
