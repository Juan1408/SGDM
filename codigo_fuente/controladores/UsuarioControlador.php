<?php
/*
ARCHIVO DESHABILITADO TEMPORALMENTE PARA EVITAR ERRORES EN EL EDITOR
*/

namespace App\Controladores;
use App\Modelos\Usuario;
use App\Ayudantes\Sesion;

require_once __DIR__ . '/../modelos/Usuario.php';
require_once __DIR__ . '/../ayudantes/Sesion.php';

class UsuarioControlador {
    private Usuario $modeloUsuario;

    public function __construct(){
        //Validamos que solo el administrador ingrese
        Sesion::validarAdmin();
        $this->modeloUsuario = new Usuario();
    }

    //Metodo para mostrar el menu
    public function index() {
        //Obtenemos todos los usuarios con sus roles
        $listaUsuarios = $this->modeloUsuario->obtenerTodosUsuariosConRoles(1);
        
        //Empaquetamos los datos
        $datos = [
            'usuarios' => $listaUsuarios
        ];

        //Cargamos la vista index.php del directorio jugadores
        $vistaInyectada = '/usuarios/usuarios_index.php';
        require_once __DIR__ . '/../vistas/admin/dashboard.php';
        
    }

    //Metodo para cambiar el estado (Activo/Bloqueado) de un usuario
    public function cambiarEstadoUsuario() {
        //Validamos que venga un ID en la URL y que sea un numero
        if (isset($_GET['id']) && is_numeric($_GET['id'])){
            //Obtenemos el ID del usuario
            $usuarioId = (int)$_GET['id'];

            //Aca evitamos que el administrador se autobloquee 
            if ($usuarioId !== $_SESSION['usuario_id']) {
                //Llamamos al metodo para cambiar el estado
                $this->modeloUsuario->cambiarEstadoUsuario($usuarioId);
            }
        }

        //Recargamos la tabla de usuarios redirigiendo a la lista
        header("Location: " . URL_BASE . "index.php?c=usuario&a=index");
        exit();
    }

    //Metodo para cargar la pantalla de edicion de un usuario
    public function editarUsuario(){
        //Validamos que venga un ID en la URL y que sea un numero
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $usuarioId = (int)$_GET['id'];
            
            //Aca evitamos que el administrador se autobloquee 
            if ($usuarioId !== $_SESSION['usuario_id']) {
                //Llamamos al metodo para obtener los datos del usuario
                $usuario = $this->modeloUsuario->obtenerUsuarioPorId($usuarioId);

                //Si se encuentra el usuario
                if ($usuario) {
                    $datos = [
                        'usuario' => $usuario,
                        'roles' => $this->modeloUsuario->obtenerTodosLosRoles(), //Obtenemos los roles del sistema
                        'es_admin' => (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1) //Verificamos si el usuario logueado es admin
                    ];

                    $vistaInyectada = 'usuarios/usuario_editar.php';
                    require_once __DIR__ . '/../vistas/admin/dashboard.php';
                    return;
                }
            }
        }

        //Si no se encuentra el usuario, redirigimos a la lista
        header("Location: " . URL_BASE . "index.php?c=usuario&a=index");
        exit();
    }

    //Metodo para obtener el id de un jugador y usarlo en la accion de suplantacion de identidad
    public function verComo (){
        //Capturamos el id del jugador
        $usuarioId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        //Traemos todos los datos del jugador desde el modelo con el metodo ya creado
        $usuario = $this->modeloUsuario->obtenerUsuarioPorId($usuarioId);

        //Si el jugador existe y esta activo
        if ($usuario && $usuario['esta_activo'] == 1) {
            //Guardamos nuestra sesion antes de entrar como usuario
            $_SESSION['admin_antes_suplantar'] = $_SESSION['usuario_id'];

            //Sobreescribimos la sesion con los datos del usuario
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre_completo'];
            $_SESSION['rol_id'] = $usuario['rol_id'];

            //Mensaje de feedback
            $_SESSION['mensaje'] = "Estás viendo el perfil como: " . $usuario['nombre_completo'];

            //Redirigimos a la vista del usuario
            header("Location: " . URL_BASE . "index.php?c=inicio&a=index");
            exit;
            
        } else {
            //Si el usuario no existe o esta bloqueado lo indicamos con un mensaje de error
            $_SESSION['mensaje'] = "No puedes suplantar a un usuario bloqueado o inexistente.";

            //Si es jugador redirigimos a la lista de jugadores
            if ($usuario['rol_id'] == 3){
                header("Location: " . URL_BASE . "index.php?c=jugador&a=index");
                exit();
            } else if ($usuario['rol_id'] == 2){
                header("Location: " . URL_BASE . "index.php?c=organizador&a=index");
                exit();
            } else {
                header("Location: " . URL_BASE . "index.php?c=usuario&a=index");
                exit();
            }
        }

    }

    //Metodo para recibir los datos del formulario POST y guardarlos en la base de datos
    public function actualizarUsuario(){
        //Comprobar si los datos se envian por el metodo POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuarioId = (int)$_POST['id'];
            
            $datosActualizar = [
                'nombre_completo' => trim($_POST['nombre_completo']),
                'email' => trim($_POST['email']),
                'rol_id' => (int)$_POST['rol_id']
            ];

            //Validaciones minimas de seguridad
            if (empty($datosActualizar['nombre_completo']) || empty($datosActualizar['email']) || $datosActualizar['rol_id'] <= 0) {
                $_SESSION['mensaje'] = "Todos los campos son obligatorios.";
                header("Location: " . URL_BASE . "index.php?c=usuario&a=editarUsuario&id=" . $usuarioId);
                exit();
            }

            $resultado = $this->modeloUsuario->actualizarUsuario($usuarioId, $datosActualizar);

            if ($resultado) {
                $_SESSION['mensaje'] = "Usuario actualizado correctamente";
            } else {
                $_SESSION['mensaje'] = "Error al actualizar el usuario";
            }

            //Recargamos la tabla de usuarios redirigiendo a la lista
            if ($datosActualizar['rol_id'] == 3){
                header("Location: " . URL_BASE . "index.php?c=jugador&a=index");
                exit();
            } else if ($datosActualizar['rol_id'] == 2){
                header("Location: " . URL_BASE . "index.php?c=organizador&a=index");
                exit();
            } else {
                header("Location: " . URL_BASE . "index.php?c=usuario&a=index");
                exit();
            }
        }
    }

    //Metodo para eliminar el usuario (baja fisica)
    public function eliminarUsuario() {
        //Validamos que el usuario este logueado y sea admin
        Sesion::validarAdmin();
        //Recibimos el ID
        $usuarioId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        //Capturamos el rol para redirigir despues de eliminar
        $rolId = isset($_GET['rol_id']) ? (int)$_GET['rol_id'] : 0;
        //Ejecutamos la funcion eliminarUsuario del modelo
        $this->modeloUsuario->eliminarUsuario($usuarioId);
        //Mandamos un mensaje de feedback
        $_SESSION['mensaje'] = "Usuario eliminado correctamente";
        //Redirigimos al usuario
        if ($rolId == 3){
            header("Location: " . URL_BASE . "index.php?c=jugador&a=index");
            exit();
        } else if ($rolId == 2){
            header("Location: " . URL_BASE . "index.php?c=organizador&a=index");
            exit();
        } else {
            header("Location: " . URL_BASE . "index.php?c=usuario&a=index");
            exit();
        }
    }
}