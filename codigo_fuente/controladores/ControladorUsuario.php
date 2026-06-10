<?php
require_once __DIR__ . '/../nucleo/ControladorBase.php';
require_once __DIR__ . '/../nucleo/AdministradorSesion.php';
require_once __DIR__ . '/../repositorios/RepositorioUsuario.php';
require_once __DIR__ . '/../modelos/Usuario.php';

/**
 * Clase ControladorUsuario
 * 
 * Controlador encargado de gestionar las peticiones de autenticación,
 * registro y deslogueo de usuarios.
 */
class ControladorUsuario extends ControladorBase {
    /**
     * @var RepositorioUsuario $repositorio Instancia del repositorio de usuarios.
     */
    private RepositorioUsuario $repositorio;

    /**
     * Constructor del controlador.
     */
    public function __construct() {
        $this->repositorio = new RepositorioUsuario();
    }

    /**
     * Muestra el formulario de inicio de sesión.
     * 
     * @return void
     */
    public function mostrarLogin(): void {
        AdministradorSesion::iniciar();
        if (AdministradorSesion::estaAutenticado()) {
            $this->redireccionar('/torneos');
        }

        /**
         * @var string $tokenCsrf Token de seguridad generado para el formulario.
         */
        $tokenCsrf = AdministradorSesion::generarTokenCSRF();
        $this->renderizarVista('login', [
            'token_csrf' => $tokenCsrf,
            'titulo' => 'Iniciar Sesión - SGDM'
        ]);
    }

    /**
     * Procesa el intento de inicio de sesión de un usuario.
     * 
     * @return void
     */
    public function login(): void {
        AdministradorSesion::iniciar();
        
        /**
         * @var string|null $tokenCsrfRecibido Token enviado por el formulario.
         */
        $tokenCsrfRecibido = $_POST['token_csrf'] ?? null;
        if (!AdministradorSesion::validarTokenCSRF($tokenCsrfRecibido)) {
            $this->redireccionar('/login?error=csrf');
        }

        /**
         * @var string $email Correo electrónico recibido.
         * @var string $contrasena Contraseña recibida en texto plano.
         */
        $email = trim($_POST['email'] ?? '');
        $contrasena = $_POST['contrasena'] ?? '';
        
        /**
         * @var string $ip Dirección IP de origen de la petición.
         * @var string $userAgent Navegador o dispositivo de origen.
         */
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';

        /**
         * @var Usuario|null $usuario Objeto de usuario si se encuentra registrado.
         */
        $usuario = $this->repositorio->obtenerPorEmail($email);

        if (!$usuario) {
            // Registrar log de acceso fallido por usuario no existente
            $this->repositorio->registrarLogAcceso($email, null, false, $ip, $userAgent, 'El correo electrónico no está registrado.');
            $this->renderizarVista('login', [
                'error' => 'El correo electrónico o la contraseña son incorrectos.',
                'token_csrf' => AdministradorSesion::generarTokenCSRF(),
                'titulo' => 'Iniciar Sesión - SGDM'
            ]);
            return;
        }

        if (!$usuario->estaActivo) {
            $this->repositorio->registrarLogAcceso($email, $usuario->id, false, $ip, $userAgent, 'La cuenta de usuario está desactivada.');
            $this->renderizarVista('login', [
                'error' => 'Tu cuenta ha sido desactivada por el administrador.',
                'token_csrf' => AdministradorSesion::generarTokenCSRF(),
                'titulo' => 'Iniciar Sesión - SGDM'
            ]);
            return;
        }

        // Verificación criptográfica de la contraseña
        if (password_verify($contrasena, $usuario->contrasenaHash)) {
            // Registro exitoso en logs
            $this->repositorio->registrarLogAcceso($email, $usuario->id, true, $ip, $userAgent, null);
            $this->repositorio->actualizarUltimoAcceso($usuario->id);
            
            // Guardar credenciales en la sesión
            AdministradorSesion::login($usuario->id, $usuario->nombreCompleto, $usuario->rolId);
            $this->redireccionar('/torneos');
        } else {
            // Intento fallido por contraseña incorrecta
            $this->repositorio->registrarLogAcceso($email, $usuario->id, false, $ip, $userAgent, 'Contraseña incorrecta.');
            $this->renderizarVista('login', [
                'error' => 'El correo electrónico o la contraseña son incorrectos.',
                'token_csrf' => AdministradorSesion::generarTokenCSRF(),
                'titulo' => 'Iniciar Sesión - SGDM'
            ]);
        }
    }

    /**
     * Muestra el formulario de registro de nuevos usuarios.
     * 
     * @return void
     */
    public function mostrarRegistro(): void {
        AdministradorSesion::iniciar();
        if (AdministradorSesion::estaAutenticado()) {
            $this->redireccionar('/torneos');
        }

        $tokenCsrf = AdministradorSesion::generarTokenCSRF();
        $this->renderizarVista('registro', [
            'token_csrf' => $tokenCsrf,
            'titulo' => 'Registro de Cuenta - SGDM'
        ]);
    }

    /**
     * Procesa la solicitud de registro de un nuevo usuario.
     * 
     * @return void
     */
    public function registro(): void {
        AdministradorSesion::iniciar();

        $tokenCsrfRecibido = $_POST['token_csrf'] ?? null;
        if (!AdministradorSesion::validarTokenCSRF($tokenCsrfRecibido)) {
            $this->redireccionar('/registro?error=csrf');
        }

        /**
         * @var string $nombreCompleto Nombre y apellido.
         * @var string $email Correo electrónico.
         * @var string $contrasena Contraseña.
         * @var string|null $telefono Teléfono opcional.
         * @var string|null $fechaNacimiento Fecha de nacimiento opcional.
         */
        $nombreCompleto = trim($_POST['nombre_completo'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $contrasena = $_POST['contrasena'] ?? '';
        $telefono = trim($_POST['telefono'] ?? null);
        $fechaNacimiento = $_POST['fecha_nacimiento'] ?? null;

        /**
         * @var string[] $errores Colección de mensajes de validación fallida.
         */
        $errores = [];

        // Validaciones básicas de campos
        if (empty($nombreCompleto)) {
            $errores[] = "El nombre completo es obligatorio.";
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = "Debes ingresar un correo electrónico válido.";
        }
        if (empty($contrasena)) {
            $errores[] = "La contraseña es obligatoria.";
        }

        // Comprobación de existencia previa de correo
        if ($this->repositorio->obtenerPorEmail($email) !== null) {
            $errores[] = "Este correo electrónico ya está registrado.";
        }

        // Validación de políticas de contraseñas complejas
        /**
         * @var array|null $politica Políticas de complejidad activas.
         */
        $politica = $this->repositorio->obtenerPoliticaContrasenas();
        if ($politica && !empty($contrasena)) {
            $this->validarComplejidadContrasena($contrasena, $politica, $errores);
        }

        // Si existen errores, se retorna a la vista con la lista de fallos
        if (!empty($errores)) {
            $this->renderizarVista('registro', [
                'errores' => $errores,
                'token_csrf' => AdministradorSesion::generarTokenCSRF(),
                'titulo' => 'Registro de Cuenta - SGDM',
                'datos' => $_POST
            ]);
            return;
        }

        /**
         * @var string $hashContrasena Hash seguro de la contraseña.
         */
        $hashContrasena = password_hash($contrasena, PASSWORD_BCRYPT);

        // Instanciar y guardar la nueva entidad de usuario
        $nuevoUsuario = new Usuario(
            null,
            $email,
            $hashContrasena,
            $nombreCompleto,
            $telefono,
            $fechaNacimiento,
            null, // foto_perfil_url
            '',   // fecha_registro autogenerada por BD
            3,    // rol_id = 3 (Participante por defecto)
            true, // esta_activo
            false // email_verificado
        );

        if ($this->repositorio->guardar($nuevoUsuario)) {
            // Registrar contraseña inicial en el historial
            $this->repositorio->registrarHistorialContrasena($nuevoUsuario->id, $hashContrasena);
            $this->renderizarVista('login', [
                'mensaje_exito' => 'Tu cuenta ha sido creada exitosamente. Ya puedes iniciar sesión.',
                'token_csrf' => AdministradorSesion::generarTokenCSRF(),
                'titulo' => 'Iniciar Sesión - SGDM'
            ]);
        } else {
            $this->renderizarVista('registro', [
                'errores' => ['Ocurrió un error inesperado al guardar el usuario en el sistema.'],
                'token_csrf' => AdministradorSesion::generarTokenCSRF(),
                'titulo' => 'Registro de Cuenta - SGDM',
                'datos' => $_POST
            ]);
        }
    }

    /**
     * Cierra la sesión activa del usuario y lo redirige al formulario de acceso.
     * 
     * @return void
     */
    public function logout(): void {
        AdministradorSesion::destruir();
        $this->redireccionar('/login');
    }

    /**
     * Valida que la contraseña cumpla con los requisitos definidos en las políticas de seguridad.
     * 
     * @param string $contrasena Contraseña a validar.
     * @param array $politica Array con las políticas de complejidad activas.
     * @param array &$errores Colección de errores para insertar avisos en caso de incumplimiento.
     * @return void
     */
    private function validarComplejidadContrasena(string $contrasena, array $politica, array &$errores): void {
        /**
         * @var int $minima Longitud mínima requerida.
         */
        $minima = (int)$politica['longitud_minima'];
        if (strlen($contrasena) < $minima) {
            $errores[] = "La contraseña debe tener al menos $minima caracteres.";
        }
        if ((bool)$politica['requiere_mayuscula'] && !preg_match('/[A-Z]/', $contrasena)) {
            $errores[] = "La contraseña debe incluir al menos una letra mayúscula.";
        }
        if ((bool)$politica['requiere_minuscula'] && !preg_match('/[a-z]/', $contrasena)) {
            $errores[] = "La contraseña debe incluir al menos una letra minúscula.";
        }
        if ((bool)$politica['requiere_numero'] && !preg_match('/[0-9]/', $contrasena)) {
            $errores[] = "La contraseña debe incluir al menos un número.";
        }
        if ((bool)$politica['requiere_caracter_especial'] && !preg_match('/[^a-zA-Z0-9]/', $contrasena)) {
            $errores[] = "La contraseña debe incluir al menos un carácter especial (ej: @, $, !, %, *, #, ?, &).";
        }
    }
}
