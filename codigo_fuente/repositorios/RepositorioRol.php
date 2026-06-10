<?php
require_once __DIR__ . '/../nucleo/ConexionBD.php';
require_once __DIR__ . '/../modelos/Rol.php';

/**
 * Clase RepositorioRol
 * 
 * Gestiona las operaciones de base de datos relacionadas con los Roles de usuario.
 */
class RepositorioRol {
    /**
     * @var PDO $conexion Conexión a la base de datos MySQL.
     */
    private PDO $conexion;

    /**
     * Constructor del repositorio. Inicializa la conexión PDO.
     */
    public function __construct() {
        $this->conexion = ConexionBD::obtenerInstancia();
    }

    /**
     * Obtiene un rol específico mediante su identificador único.
     * 
     * @param int $id Identificador del rol.
     * @return Rol|null Retorna el objeto Rol o null si no se encuentra.
     */
    public function obtenerPorId(int $id): ?Rol {
        /**
         * @var string $sql Consulta SQL preparada.
         */
        $sql = "SELECT id, nombre_rol, descripcion, nivel_permiso FROM roles WHERE id = :id LIMIT 1";
        
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute(['id' => $id]);
        
        /**
         * @var array|false $fila Fila recuperada de la base de datos.
         */
        $fila = $sentencia->fetch();

        if (!$fila) {
            return null;
        }

        return new Rol(
            (int)$fila['id'],
            $fila['nombre_rol'],
            $fila['descripcion'],
            (int)$fila['nivel_permiso']
        );
    }

    /**
     * Obtiene todos los roles registrados en el catálogo.
     * 
     * @return Rol[] Colección de objetos Rol.
     */
    public function obtenerTodos(): array {
        /**
         * @var string $sql Consulta SQL.
         */
        $sql = "SELECT id, nombre_rol, descripcion, nivel_permiso FROM roles ORDER BY nivel_permiso DESC";
        
        $sentencia = $this->conexion->query($sql);
        
        /**
         * @var array $filas Lista de registros recuperados.
         */
        $filas = $sentencia->fetchAll();
        
        /**
         * @var Rol[] $resultado Array de roles que será devuelto.
         */
        $resultado = [];

        foreach ($filas as $fila) {
            $resultado[] = new Rol(
                (int)$fila['id'],
                $fila['nombre_rol'],
                $fila['descripcion'],
                (int)$fila['nivel_permiso']
            );
        }

        return $resultado;
    }
}
