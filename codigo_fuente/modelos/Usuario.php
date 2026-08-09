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
}
