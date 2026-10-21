<?php


namespace App\Controladores;
use App\Modelos\Usuario;
use App\Ayudantes\Sesion;

require_once __DIR__ . '/../modelos/Usuario.php';
require_once __DIR__ . '/../ayudantes/Sesion.php';

class UsuarioControlador {
    private Usuario $modeloUsuario;

    public function __construct(){
        // Nota: La validacion de admin se movio a los metodos individuales
        // para permitir que restaurarSesion funcione estando con rol de Jugador/Organizador.
        $this->modeloUsuario = new Usuario();
    }

    //Metodo para mostrar el menu
    public function index() {
        Sesion::validarAdmin();
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
        Sesion::validarAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Metodo no permitido');
        }
        Sesion::requerirTokensCSRF();

        //Validamos que venga un ID en la URL y que sea un numero
        if (isset($_POST['id']) && is_numeric($_POST['id'])){
            //Obtenemos el ID del usuario
            $usuarioId = (int)$_POST['id'];

            //Aca evitamos que el administrador se autobloquee 
            if ($usuarioId !== $_SESSION['usuario_id']) {
                //Llamamos al metodo para cambiar el estado
                $this->modeloUsuario->cambiarEstadoUsuario($usuarioId);
            }
        }

        //Recargamos la lista de origen según el rol del usuario afectado
        $rolId = isset($_POST['rol_id']) ? (int) $_POST['rol_id'] : 0;
        if ($rolId === 3) {
            header("Location: " . URL_BASE . "index.php?c=jugador&a=index");
        } elseif ($rolId === 2) {
            header("Location: " . URL_BASE . "index.php?c=organizador&a=index");
        } else {
            header("Location: " . URL_BASE . "index.php?c=usuario&a=index");
        }
        exit();
    }

    //Metodo para cargar la pantalla de edicion de un usuario
    public function editarUsuario(){
        Sesion::validarAdmin();
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
        Sesion::validarAdmin();
        //Capturamos el id del jugador
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Metodo no permitido');
        }
        Sesion::requerirTokensCSRF();

        $_SESSION['admin_antes_suplantar'] = $_SESSION['admin_antes_suplantar'] ?? $_SESSION['usuario_id'];
        $usuarioId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        //Traemos todos los datos del jugador desde el modelo con el metodo ya creado
        $usuario = $this->modeloUsuario->obtenerUsuarioPorId($usuarioId);

        //Si el jugador existe y esta activo
        if ($usuario && $usuario['esta_activo'] == 1) {
            //Sobreescribimos la sesion con los datos del usuario
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre_completo'];
            $_SESSION['rol_id'] = $usuario['rol_id'];

            // Mensaje de feedback
            $_SESSION['mensaje'] = "Estás viendo el perfil como: " . $usuario['nombre_completo'];

            // Redirigimos a la vista correcta segun el rol del usuario suplantado
            if ($usuario['rol_id'] == 2) { // Organizador
                header("Location: " . URL_BASE . "index.php?c=panelOrganizador&a=dashboard");
            } else if ($usuario['rol_id'] == 3) { // Jugador
                header("Location: " . URL_BASE . "index.php?c=panelJugador&a=dashboard");
            } else {
                // Por defecto (aunque un admin no deberia suplantar a otro admin)
                header("Location: " . URL_BASE . "index.php?c=admin&a=dashboard");
            }
            exit;
            
        } else {
            //Si el usuario no existe o esta bloqueado lo indicamos con un mensaje de error
            $_SESSION['mensaje'] = "No puedes suplantar a un usuario bloqueado o inexistente.";

            //Redirigimos segun el rol del usuario objetivo (si existe); si no, a la lista general
            $rolDestino = (int) ($usuario['rol_id'] ?? 0);
            if ($rolDestino === 3){
                header("Location: " . URL_BASE . "index.php?c=jugador&a=index");
                exit();
            } else if ($rolDestino === 2){
                header("Location: " . URL_BASE . "index.php?c=organizador&a=index");
                exit();
            } else {
                header("Location: " . URL_BASE . "index.php?c=usuario&a=index");
                exit();
            }
        }

    }

    // Metodo para deshacer la suplantacion (Ver Como) y volver al usuario Administrador original
    public function restaurarSesion() {
        // Necesitamos iniciar la sesion para poder leer $_SESSION
        Sesion::requerirLogin();
        
        // Verificamos si existe la variable de sesion original
        if (isset($_SESSION['admin_antes_suplantar'])) {
            // Compatibilidad con sesiones creadas antes de guardar solo el id (formato array)
            $respaldoAdmin = $_SESSION['admin_antes_suplantar'];
            $adminId = is_array($respaldoAdmin)
                ? (int) ($respaldoAdmin['usuario_id'] ?? 0)
                : (int) $respaldoAdmin;

            // Traemos los datos del administrador
            $admin = $this->modeloUsuario->obtenerUsuarioPorId($adminId);

            if ($admin && $admin['rol_id'] == 1) {
                // Restauramos la sesion
                $_SESSION['usuario_id'] = $admin['id'];
                $_SESSION['usuario_nombre'] = $admin['nombre_completo'];
                $_SESSION['rol_id'] = $admin['rol_id'];
                
                // Borramos la variable temporal
                unset($_SESSION['admin_antes_suplantar']);

                $_SESSION['mensaje'] = "Has vuelto a tu sesión de Administrador original.";
                header("Location: " . URL_BASE . "index.php?c=admin&a=dashboard");
                exit;
            }
        }

        // Si falla o no estaba suplantando, lo mandamos al inicio por seguridad
        header("Location: " . URL_BASE . "index.php");
        exit;
    }

    //Metodo para recibir los datos del formulario POST y guardarlos en la base de datos
    public function actualizarUsuario(){
        Sesion::validarAdmin();
        //Comprobar si los datos se envian por el metodo POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Seguridad: Validar Token CSRF
            if (!Sesion::validarTokenCSRF($_POST['csrf_token'] ?? '')) {
                $_SESSION['mensaje'] = "Error de seguridad (CSRF). Petición denegada.";
                header("Location: " . URL_BASE . "index.php?c=usuario&a=index");
                exit();
            }

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

            try {
                $resultado = $this->modeloUsuario->actualizarUsuario($usuarioId, $datosActualizar);
            } catch (\PDOException $e) {
                if ($e->getCode() === '23000') {
                    $_SESSION['mensaje'] = "El correo electrónico ya está registrado por otro usuario.";
                    header("Location: " . URL_BASE . "index.php?c=usuario&a=editarUsuario&id=" . $usuarioId);
                    exit();
                }
                throw $e;
            }

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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Metodo no permitido');
        }

        Sesion::requerirTokensCSRF();

        //Recibimos los datos del formulario POST
        $usuarioId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        //Capturamos el rol para redirigir despues de eliminar
        $rolId = isset($_POST['rol_id']) ? (int) $_POST['rol_id'] : 0;
        //Ejecutamos la funcion eliminarUsuario del modelo
        $resultado = $this->modeloUsuario->eliminarUsuario($usuarioId);
        //Mandamos un mensaje de feedback
        $_SESSION['mensaje'] = $resultado['mensaje'];
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
