<?php
/**
 * ============================================================================
 * CLASE CONTROLADOR: UsuarioControlador.php
 * ============================================================================
 * Propósito: Gestiona el flujo CRUD completo de usuarios en el módulo administrativo.
 * Ubicación: codigo_fuente/controladores/UsuarioControlador.php
 * ============================================================================
 */

require_once __DIR__ . '/../ayudantes/Sesion.php';
require_once __DIR__ . '/../ayudantes/Validador.php';
require_once __DIR__ . '/../modelos/Usuario.php';
require_once __DIR__ . '/../modelos/Rol.php';

class UsuarioControlador {
    private Usuario $usuarioModelo;
    private Rol $rolModelo;

    public function __construct() {
        // Exigir autenticación como Administrador
        Sesion::requerirAdmin();
        $this->usuarioModelo = new Usuario();
        $this->rolModelo     = new Rol();
    }

    /**
     * READ: Muestra el listado completo de usuarios.
     * Ruta: index.php?c=usuario&a=index
     */
    public function index(): void {
        $usuarios = $this->usuarioModelo->obtenerTodos();
        require_once __DIR__ . '/../vistas/admin/usuarios/index.html';
    }

    /**
     * CREATE (Paso 1): Muestra el formulario para crear un nuevo usuario.
     * Ruta: index.php?c=usuario&a=crear
     */
    public function crear(): void {
        $roles = $this->rolModelo->obtenerTodos();
        require_once __DIR__ . '/../vistas/admin/usuarios/formulario.html';
    }

    /**
     * CREATE (Paso 2): Procesa el envío del formulario POST y registra en la BD.
     * Ruta: index.php?c=usuario&a=guardar
     */
    public function guardar(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Sanitizar y capturar datos
            $nombre   = Validador::sanitizar($_POST['nombre'] ?? '');
            $email    = trim($_POST['correo'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $clave    = $_POST['contrasena'] ?? '';
            $rolId    = (int)($_POST['rol'] ?? 3);

            // 2. Validaciones con el helper Validador
            $errores = [];
            if (!Validador::validarTexto($nombre, 3, 100)) {
                $errores[] = "El nombre debe tener entre 3 y 100 caracteres.";
            }
            if (!Validador::validarEmail($email)) {
                $errores[] = "El correo electrónico no es válido.";
            }
            if (!Validador::validarContrasenaFuerte($clave)) {
                $errores[] = "La contraseña no cumple con las políticas de seguridad (mínimo 8 caracteres, mayúscula, minúscula y número).";
            }

            // 3. Si hay errores, redirigir informando
            if (!empty($errores)) {
                $_SESSION['errores'] = $errores;
                header('Location: index.php?c=usuario&a=crear');
                exit();
            }

            // 4. Guardar mediante el modelo
            $this->usuarioModelo->registrar([
                'nombre_completo' => $nombre,
                'email'           => $email,
                'telefono'        => $telefono,
                'contrasena'      => $clave,
                'rol_id'          => $rolId
            ]);

            $_SESSION['mensaje_exito'] = "Usuario creado exitosamente.";
            header('Location: index.php?c=usuario&a=index');
            exit();
        }
    }

    /**
     * UPDATE (Paso 1): Muestra el formulario con los datos cargados para editar.
     * Ruta: index.php?c=usuario&a=editar&id=5
     */
    public function editar(): void {
        $id = (int)($_GET['id'] ?? 0);
        $usuario = $this->usuarioModelo->obtenerPorId($id);
        $roles   = $this->rolModelo->obtenerTodos();

        if (!$usuario) {
            header('Location: index.php?c=usuario&a=index');
            exit();
        }

        require_once __DIR__ . '/../vistas/admin/usuarios/formulario.html';
    }

    /**
     * DELETE / DESACTIVAR: Suspende o activa un usuario.
     * Ruta: index.php?c=usuario&a=cambiarEstado&id=5&estado=0
     */
    public function cambiarEstado(): void {
        $id     = (int)($_GET['id'] ?? 0);
        $estado = (int)($_GET['estado'] ?? 0);
        $this->usuarioModelo->cambiarEstado($id, $estado);
        header('Location: index.php?c=usuario&a=index');
        exit();
    }
}
