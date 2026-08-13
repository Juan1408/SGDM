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
            header("Location: " . URL_BASE . "index.php?c=admin&a=dashboard");
            exit;
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
            header("Location: " . URL_BASE . "index.php?c=admin&a=dashboard");
            exit;
        } else {
            // Si algo falla recargamos la vista de login con un mensaje de error
            $error = "Credenciales incorrectas. Verifique su email y contraseña.";
            require_once __DIR__ . '/../vistas/auth/login.php';
            return; 
        }
    }   
}

public function logout(){
    Sesion::destruir();
    header("Location: " . URL_BASE . "index.php?c=auth&a=mostrarLogin");
    exit;
}
}