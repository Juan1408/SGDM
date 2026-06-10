<?php
/**
 * Clase AdministradorSesion
 * 
 * Gestiona el ciclo de vida de la sesión del usuario de forma segura.
 * Incluye protección contra fijación de sesión y generación/validación de tokens CSRF.
 */
class AdministradorSesion {
    /**
     * Inicia la sesión de PHP de forma segura si no está ya iniciada.
     * 
     * @return void
     */
    public static function iniciar(): void {
        if (session_status() === PHP_SESSION_NONE) {
            // Configurar cookies de sesión para mayor seguridad
            session_start([
                'cookie_lifetime' => 0,
                'cookie_path' => '/',
                'cookie_secure' => false, // Cambiar a true si se usa HTTPS en producción
                'cookie_httponly' => true,
                'cookie_samesite' => 'Lax'
            ]);
        }
    }

    /**
     * Regenera el identificador de sesión para prevenir ataques de fijación de sesión.
     * 
     * @return void
     */
    public static function regenerar(): void {
        self::iniciar();
        session_regenerate_id(true);
    }

    /**
     * Genera un token CSRF único y lo almacena en la sesión del usuario.
     * 
     * @return string Token CSRF generado.
     */
    public static function generarTokenCSRF(): string {
        self::iniciar();
        if (empty($_SESSION['token_csrf'])) {
            $_SESSION['token_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['token_csrf'];
    }

    /**
     * Valida si un token CSRF provisto coincide con el guardado en la sesión.
     * 
     * @param string|null $token Token recibido del formulario.
     * @return bool Retorna verdadero si el token es válido, falso en caso contrario.
     */
    public static function validarTokenCSRF(?string $token): bool {
        self::iniciar();
        if (empty($_SESSION['token_csrf']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['token_csrf'], $token);
    }

    /**
     * Destruye la sesión actual del usuario de manera segura.
     * 
     * @return void
     */
    public static function destruir(): void {
        self::iniciar();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $parametros = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $parametros["path"],
                $parametros["domain"],
                $parametros["secure"],
                $parametros["httponly"]
            );
        }
        session_destroy();
    }

    /**
     * Establece los datos básicos del usuario autenticado en la sesión.
     * 
     * @param int $usuarioId Identificador único del usuario.
     * @param string $nombreCompleto Nombre completo del usuario.
     * @param int $rolId Identificador del rol.
     * @return void
     */
    public static function login(int $usuarioId, string $nombreCompleto, int $rolId): void {
        self::regenerar();
        $_SESSION['usuario_id'] = $usuarioId;
        $_SESSION['nombre_completo'] = $nombreCompleto;
        $_SESSION['rol_id'] = $rolId;
        $_SESSION['esta_autenticado'] = true;
    }

    /**
     * Comprueba si el usuario actual está autenticado.
     * 
     * @return bool Verdadero si está autenticado, falso de lo contrario.
     */
    public static function estaAutenticado(): bool {
        self::iniciar();
        return isset($_SESSION['esta_autenticado']) && $_SESSION['esta_autenticado'] === true;
    }

    /**
     * Obtiene el identificador del usuario autenticado de la sesión.
     * 
     * @return int|null Retorna el identificador del usuario o null si no está autenticado.
     */
    public static function obtenerUsuarioId(): ?int {
        self::iniciar();
        return $_SESSION['usuario_id'] ?? null;
    }
}
