<?php

namespace App\Controladores;

//Importamos las clases que vamos a usar
use App\Modelos\Usuario; //Para buscar en la base de datos
use App\Ayudantes\Sesion; //Para manejar sesiones

require_once __DIR__ . '/../modelos/Usuario.php';
require_once __DIR__ . '/../ayudantes/Sesion.php';

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
        //si no esta logueado cargamos el archivo login.php
        require __DIR__ . '/../vistas/auth/login.php';
    }

    //Accion para procesar el envio del login (POST en el boton Enviar)
    public function procesarLogin() {
        //Verificamos si realmente se envio el formulario mediante el metodo POST
        if ($_SERVER['REQUEST_METHOD'] ==='POST'){
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
                $error = "Tu cuenta esta inactiva. Por favor, contacta al administrador.";
                require_once __DIR__ . '/../vistas/auth/login.php';
                return;
            }

            //Iniciamos sesion con el usuario y redirigimos al dashboard
            Sesion::iniciarLogin($usuarioEncontrado);

            //Lo enviamos a la pantalla correspondiente
            $this->redirigirPorRol($usuarioEncontrado['rol_id']);
        } else {
            // Si algo falla recargamos la vista de login con un mensaje de error
            $error = "Credenciales incorrectas. Verifique su email y contraseña.";
            require_once __DIR__ . '/../vistas/auth/login.php';
            return; 
        }
    }   
}

    //Accion para mostrar la pantalla del formulario de registro
    public function mostrarRegistro() {
        if (Sesion::estaLogueado()) {
            $this->redirigirPorRol($_SESSION['rol_id']);
        }
        require __DIR__ . '/../vistas/auth/registro.php';
    }

    //Accion para procesar el envio del formulario de registro (POST)
    public function procesarRegistro() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL_BASE . 'index.php?c=auth&a=mostrarRegistro');
            exit;
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

        if ($contrasena !== $contrasenaConfirmar) {
            $error = "Las contraseñas no coinciden.";
            require __DIR__ . '/../vistas/auth/registro.php';
            return;
        }

        if (strlen($contrasena) < 8) {
            $error = "La contraseña debe tener al menos 8 caracteres conforme a las políticas de seguridad.";
            require __DIR__ . '/../vistas/auth/registro.php';
            return;
        }

        $datos = [
            'nombre_completo' => $nombre,
            'email' => $email,
            'telefono' => $telefono,
            'contrasena' => $contrasena,
            'rol_id' => $rolId,
            'nombre_organizacion' => trim($_POST['nombre_organizacion'] ?? ($nombre . ' Org'))
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
        $lineaLog = "[" . date('Y-m-d H:i:s') . "] EMAIL SIMULADO -> Para: " . $email . " | Enlace: " . $urlVerificacion . PHP_EOL;
        @file_put_contents($dirLog . '/emails_simulados.log', $lineaLog, FILE_APPEND);

        // Notificación de éxito con simulación de enlace para desarrollo local
        $mensajeExito = "¡Cuenta creada exitosamente! <br>"
            . ($rolId === 2 ? "<b>Nota para Organizadores:</b> Tu cuenta ha sido registrada y está pendiente de aprobación por el Administrador.<br>" : "")
            . "<br><div style='margin-top:10px; padding:10px; background:#e0f2fe; color:#0369a1; border-radius:6px; font-size:0.88rem;'>"
            . "<i class='fa-solid fa-flask'></i> <b>Entorno de Desarrollo Local:</b> Simulación de envío de correo activada.<br>"
            . "<a href='" . $urlVerificacion . "' style='color:#0284c7; font-weight:bold; text-decoration:underline;'>Haz clic aquí para verificar tu correo electrónico ahora</a>"
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