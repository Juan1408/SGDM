<?php
/**
 * ============================================================================
 * CLASE MODELO: Usuario.php
 * ============================================================================
 * Propósito: Gestiona el acceso a datos y operaciones CRUD para la tabla 'usuarios'
 *            y la especialización de perfiles (perfiles_jugadores, perfiles_organizadores).
 * Ubicación: codigo_fuente/modelos/Usuario.php
 * ============================================================================
 */

namespace App\Modelos;
use PDO; //importamos pdo para no tener que poner PDO en toda la clase
require_once __DIR__ . '/../modelos/Conexion.php';

//La clase Usuario maneja todas las interacciones con la base de datos relacionadas con usuarios.

class Usuario {
    //Propiedades: nos aseguramos de que la conexion sea privada para que solo se pueda acceder desde aqui
    private $bd;
    
    //Constructor: inicializamos la conexion a la base de datos al crear un objeto de esta clase
    public function __construct() {
        //Optenemos la instancia única de la base de datos
        $this->bd = Conexion::getInstance()->getBD();
    }

    //Metodo para buscar a un usuario por su email
    public function buscarPorEmail(string $email) {
        //Escribimos la consulta SQL usando un marcador por seguridad (:email)
        $sql = "SELECT * FROM usuarios WHERE email = :email AND esta_activo = 1 LIMIT 1"; 

        //Preparamos la consulta para evitar inyección SQL
        $stmt = $this->bd->prepare($sql);

        //Ejecutamos la consulta pasandole el email
        $stmt->execute([':email' => $email]);

        //Obtenemos el resultado como array asociativo
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        //Devolvemos el resultado si lo encuentra o null si no
        return $usuario;        
    }

    //Metodo para contar el total de usuarios registrador
    public function contarTotalJugadoresRegistrados() {
        //La instruccion SQL COUNT(*) cuenta las filas de la tabla
        $sql = "SELECT COUNT(*) as total FROM usuarios WHERE rol_id = 3";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute();

        //fetch devuelve la fila, asi sacamos la columna total
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado['total'] ?? 0;
    }

    //Metodo para contar el total de organizadores registrados
    public function contarTotalOrganizadoresRegistrados(){
     $sql = "SELECT COUNT(*) as total FROM usuarios WHERE rol_id = 2";
     $stmt = $this->bd->prepare($sql);
     $stmt->execute();
     $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
     return $resultado['total'] ?? 0;    
    }    

    //Metodo para obtener los ultimos jugadores registrados
    public function obtenerUltimosJugadoresRegistrados(int $limite){
        $sql = "SELECT id, nombre_completo, email, fecha_registro
        FROM usuarios
        WHERE rol_id = 3
        ORDER BY fecha_registro DESC
        LIMIT :limite";
        
        $stmt = $this->bd->prepare($sql);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Metodo para obtener los ultimos organizadores registrados
    public function obtenerUltimosOrganizadoresRegistrados(int $limite) {
        $sql = "SELECT id, nombre_completo, email, fecha_registro
        FROM usuarios
        WHERE rol_id = 2
        ORDER BY fecha_registro DESC
        LIMIT :limit";

        $stmt = $this->bd->prepare($sql);
        $stmt->bindParam(':limit', $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Metodo para obtener todos los usuarios registrados junto con el rol
    public function obtenerTodosUsuariosConRoles(){
        $sql = "SELECT 
        u.id,
        u.nombre_completo,
        u.email,
        u.esta_activo,
        u.fecha_registro,
        r.nombre_rol
        FROM usuarios u
        INNER JOIN roles r ON u.rol_id = r.id
        ORDER BY u.id DESC";

        $stmt = $this->bd->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
