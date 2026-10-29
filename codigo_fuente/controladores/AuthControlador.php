<?php

namespace App\Controladores;

//Importamos las clases que vamos a usar
use App\Modelos\Usuario; //Para buscar en la base de datos
use App\Ayudantes\Sesion; //Para manejar sesiones

require_once __DIR__ . '/../modelos/Usuario.php';
require_once __DIR__ . '/../ayudantes/Sesion.php';
require_once __DIR__ . '/../configuracion/constantes.php';

class AuthControlador {

    private Usuario $modeloUsuario;

    //Cuando se llame al controlador preparamos el modelo Usuario
    public function __construct() {
        $this->modeloUsuario = new Usuario();
    }

    //Accion para mostrar la pantalla del formulario del login
    public function mostrarLogin() {
        //Si el usuario ya esta logueado lo mandamos directo al Home para que no vea el login otra vez
        if (Sesion::estaLogueado()) {
            $this->redirigirPorRol($_SESSION['rol_id']);
        }

        $error = '';
        if (isset($_GET['error'])) {
            if ($_GET['error'] === 'cuenta_suspendida') {
                $error = "Tu cuenta ha sido suspendida o desactivada por la administración.";
            } elseif ($_GET['error'] === 'acceso_denegado') {
                $error = "No tienes permisos para acceder a esa sección.";
            } elseif ($_GET['error'] === 'rol_invalido') {
                $error = "Rol de usuario no válido.";
            }
        }

        //si no esta logueado cargamos el archivo login.php
        require __DIR__ . '/../vistas/auth/login.php';
    }

    //Accion para procesar el envio del login (POST en el boton Enviar)
    public function procesarLogin() {
        //Verificamos si realmente se envio el formulario mediante el metodo POST
        if ($_SERVER['REQUEST_METHOD'] ==='POST'){
            // Seguridad: Validar Token CSRF
            if (!Sesion::validarTokenCSRF($_POST['csrf_token'] ?? '')) {
                $error = "Petición no válida (CSRF Token incorrecto). Intente nuevamente.";
                require_once __DIR__ . '/../vistas/auth/login.php';
                return;
            }

            // Seguridad: Rate Limiting Profesional (Bloqueo IP)
            require_once __DIR__ . '/../modelos/SeguridadAuth.php';
            $seguridadAuth = new \App\Modelos\SeguridadAuth();
            $ipUsuario = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

            if ($seguridadAuth->estaIpBloqueada($ipUsuario)) {
                $error = "Por seguridad, su dirección IP ha sido bloqueada temporalmente debido a múltiples intentos fallidos. Intente nuevamente en 5 minutos.";
                require_once __DIR__ . '/../vistas/auth/login.php';
                return;
            }

            //A los datos ingresados les sacamos los espacios en blanco que puedan ser ingresados accidentalmente
            $email = trim($_POST['email'] ?? '');
            $contrasena = $_POST['contrasena'] ?? '';

            //Validacion de datos minimos
            if (empty($email) || empty($contrasena)){
                $error = "Por favor, complete todos los campos.";
                require_once __DIR__ . '/../vistas/auth/login.php';
                return;
            }
        

        //Le pedimos al modelo que busque si existe ese email en la base de datos
        $usuarioEncontrado = $this->modeloUsuario->buscarPorEmail($email);

        //Si encontro al usuario y la contraseña son coincide con el texto encriptado (hash)
        if ($usuarioEncontrado && password_verify($contrasena, $usuarioEncontrado['contrasena_hash'])){
            //Si la cuenta esta inactiva no lo dejamos pasar
            if ($usuarioEncontrado['esta_activo'] == 0){
                Sesion::registrarAccesoFallido($email, (int) $usuarioEncontrado['id'], 'Cuenta inactiva');
                $error = "Tu cuenta esta inactiva. Por favor, contacta al administrador.";
                require_once __DIR__ . '/../vistas/auth/login.php';
                return;
            }

            //La verificación de correo es obligatoria: sin ella no se habilita el acceso.
            if ((int) ($usuarioEncontrado['email_verificado'] ?? 0) !== 1) {
                Sesion::registrarAccesoFallido($email, (int) $usuarioEncontrado['id'], 'Email no verificado');
                $error = "Tu correo electrónico aún no está verificado. Revisá el enlace de verificación que se mostró al registrarte.";
                require_once __DIR__ . '/../vistas/auth/login.php';
                return;
            }

            //Iniciamos sesion con el usuario y redirigimos al dashboard
            Sesion::iniciarLogin($usuarioEncontrado);
            
            // Login exitoso, reseteamos los intentos fallidos de esta IP
            $seguridadAuth->limpiarIntentos($ipUsuario);

            //Lo enviamos a la pantalla correspondiente
            $this->redirigirPorRol($usuarioEncontrado['rol_id']);
        } else {
            // Si algo falla recargamos la vista de login con un mensaje de error
            $seguridadAuth->registrarIntentoFallido($ipUsuario);
            Sesion::registrarAccesoFallido(
                $email,
                $usuarioEncontrado ? (int) $usuarioEncontrado['id'] : null,
                'Credenciales incorrectas'
            );
            $error = "Credenciales incorrectas. Verifique su email y contraseña.";
            require_once __DIR__ . '/../vistas/auth/login.php';
            return; 
        }
    }   
}

    // Punto único de acceso al modelo de políticas de contraseñas.
    // Todos los render de registro cargan la política desde acá para mostrar
    // siempre la misma regla que valida el servidor, incluso tras un error.
    private function modeloPoliticaContrasena(): \App\Modelos\PoliticaContrasena {
        require_once __DIR__ . '/../modelos/PoliticaContrasena.php';
        return new \App\Modelos\PoliticaContrasena();
    }

    //Accion para mostrar la pantalla del formulario de registro
    public function mostrarRegistro() {
        if (Sesion::estaLogueado()) {
            $this->redirigirPorRol($_SESSION['rol_id']);
        }

        // Obtener la política de contraseñas para enviarla a la vista
        $politica = $this->modeloPoliticaContrasena()->obtenerPoliticaVigente();

        require __DIR__ . '/../vistas/auth/registro.php';
    }

    //Accion para procesar el envio del formulario de registro (POST)
    public function procesarRegistro() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL_BASE . 'index.php?c=auth&a=mostrarRegistro');
            exit;
        }

        // La política se carga antes de cualquier render de error: la vista
        // debe mostrar siempre los requisitos vigentes y no un valor fijo.
        $politicaModelo = $this->modeloPoliticaContrasena();
        $politica = $politicaModelo->obtenerPoliticaVigente();

        // Seguridad: Validar Token CSRF
        if (!Sesion::validarTokenCSRF($_POST['csrf_token'] ?? '')) {
            $error = "Petición no válida (CSRF Token incorrecto). Intente nuevamente.";
            require __DIR__ . '/../vistas/auth/registro.php';
            return;
        }

        $nombre = trim($_POST['nombre_completo'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $contrasena = $_POST['contrasena'] ?? '';
        $contrasenaConfirmar = $_POST['contrasena_confirmar'] ?? '';
        $rolInput = $_POST['rol'] ?? 'jugador';
        $rolId = ($rolInput === 'organizador' || $rolInput === '2') ? 2 : 3;

        if (empty($nombre) || empty($email) || empty($contrasena) || empty($contrasenaConfirmar)) {
            $error = "Por favor, completa todos los campos requeridos.";
            require __DIR__ . '/../vistas/auth/registro.php';
            return;
        }

        // El formato del correo se valida en el servidor: el atributo type=email
        // del navegador se puede omitir o eludir y no debe decidir el alta.
        if (mb_strlen($email, 'UTF-8') > 255 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $error = "Ingresá un correo electrónico válido.";
            require __DIR__ . '/../vistas/auth/registro.php';
            return;
        }

        // La aceptación de los documentos legales es obligatoria. Se valida en
        // el servidor aunque el formulario use el atributo required.
        if (($_POST['aceptar_terminos'] ?? '') !== '1') {
            $error = "Debés aceptar los Términos y Condiciones y la Política de Privacidad para crear tu cuenta.";
            require __DIR__ . '/../vistas/auth/registro.php';
            return;
        }

        if ($contrasena !== $contrasenaConfirmar) {
            $error = "Las contraseñas no coinciden.";
            require __DIR__ . '/../vistas/auth/registro.php';
            return;
        }

        // Validación dinámica contra Políticas de Contraseñas (misma política ya cargada)
        $validacion = $politicaModelo->validarPassword($contrasena, $politica);
        
        if (!$validacion['valido']) {
            $error = implode('<br>', $validacion['errores']);
            require __DIR__ . '/../vistas/auth/registro.php';
            return;
        }

        $datos = [
            'nombre_completo' => $nombre,
            'email' => $email,
            'telefono' => $telefono,
            'contrasena' => $contrasena,
            'rol_id' => $rolId,
            'nombre_organizacion' => trim($_POST['nombre_organizacion'] ?? ($nombre . ' Org')),
            // Las versiones las decide el servidor; nunca se toman del formulario.
            'aceptaciones' => [
                'terminos' => VERSION_TERMINOS,
                'privacidad' => VERSION_PRIVACIDAD,
            ],
            'aceptacion_ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            'aceptacion_user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ];

        $resultado = $this->modeloUsuario->registrarUsuario($datos);

        if (!$resultado['exito']) {
            $error = $resultado['mensaje'];
            require __DIR__ . '/../vistas/auth/registro.php';
            return;
        }

        // Generar enlace de activación para entorno de desarrollo local (Opción A)
        $urlVerificacion = URL_BASE . "index.php?c=auth&a=verificarEmail&token=" . $resultado['token'];

        // Guardar registro simulado en archivo de log local
        $dirLog = __DIR__ . '/../logs';
        if (!is_dir($dirLog)) {
            @mkdir($dirLog, 0777, true);
        }
        $tokenVisible = substr($resultado['token'], 0, 8) . '...';
        $lineaLog = sprintf(
            "[%s] EMAIL SIMULADO -> Para: %s | Token: %s%s",
            date('Y-m-d H:i:s'),
            $email,
            $tokenVisible,
            PHP_EOL
        );
        @file_put_contents($dirLog . '/emails_simulados.log', $lineaLog, FILE_APPEND);

        // Notificación de éxito con simulación de enlace para desarrollo local
        $mensajeExito = "¡Cuenta creada exitosamente! <br>"
            . ($rolId === 2 ? "<b>Nota para Organizadores:</b> Tu cuenta ha sido registrada y está pendiente de aprobación por el Administrador.<br>" : "")
            . "<br><div class='alerta-simulacion'>"
            . "<i class='fa-solid fa-flask'></i> <b>Entorno de Desarrollo Local:</b> Simulación de envío de correo activada.<br>"
            . "<a href='" . $urlVerificacion . "'>Haz clic aquí para verificar tu correo electrónico ahora</a>"
            . "</div>";

        require __DIR__ . '/../vistas/auth/login.php';
    }

    //Accion para procesar la verificacion de email via token URL
    public function verificarEmail() {
        $token = trim($_GET['token'] ?? '');
        if (empty($token)) {
            $error = "Token de verificación no proporcionado.";
            require __DIR__ . '/../vistas/auth/login.php';
            return;
        }

        $resultado = $this->modeloUsuario->verificarTokenEmail($token);
        if ($resultado['exito']) {
            $mensajeExito = $resultado['mensaje'];
        } else {
            $error = $resultado['mensaje'];
        }

        require __DIR__ . '/../vistas/auth/login.php';
    }

    public function logout(){
        Sesion::destruir();
        header("Location: " . URL_BASE . "index.php?c=auth&a=mostrarLogin");
        exit;
    }

    //Metodo privado para redirigir segun el rol del usuario
    private function redirigirPorRol(int $rol_id){
        if ($rol_id == 1){
            header("Location: " . URL_BASE . "index.php?c=admin&a=dashboard");
            exit;
        } elseif ($rol_id == 2){
            header("Location: " . URL_BASE . "index.php?c=panelOrganizador&a=dashboard");
            exit;
        } elseif ($rol_id == 3){
            header("Location: " . URL_BASE . "index.php?c=panelJugador&a=dashboard");
            exit;
        } else {
            Sesion::destruir();
            header("Location: " . URL_BASE . "index.php?c=auth&a=mostrarLogin&error=rol_invalido");
        }
        exit;
    }
}