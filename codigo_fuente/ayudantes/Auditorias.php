<?php
namespace App\Ayudantes;

use App\Modelos\Conexion;
use PDO;

require_once __DIR__ . '/../modelos/Conexion.php';

class Auditoria {

    //Con este metodo estatico vamos a insertar un registro en la tabla logs_actividad
    //Se utiliza un patron singleton para obtener la instancia de la base de datos
    public static function registrar(string $accion, string $descripcion){
        //Obtenemos la instancia de la base de datos
        $bd = Conexion::getInstance()->getBD();

        //Obtenemos el ID del usuario si está logueado de lo contrario será null
        $usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;

        //Obtenemos la IP del usuario
        $ip_origen = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';

        //Obtenemos el agente de usuario del usuario
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';

        //Sentencia SQL para insertar un registro en la tabla logs_actividad
        $sql = "INSERT INTO logs_actividad (usuario_id, accion, descripcion, ip_origen, user_agent) VALUES (:usuario_id, :accion, :descripcion, :ip_origen, :user_agent)";
        $stmt = $bd->prepare($sql);
        //Parametros
        $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':accion' => $accion,
            ':descripcion' => $descripcion,
            ':ip_origen' => $ip_origen,
            ':user_agent' => $user_agent
        ]);
    }

    //Metodo estatico para traer todos los logs y mostrarlos en la tabla
    public static function obtenerTodos(){
        $bd = Conexion::getInstance()->getBD();
        
        //Hacemos un JOIN con usuarios para saber quien realizo la accion
        $sql = "SELECT l.*, u.email AS usuario_email 
        FROM logs_actividad l
        LEFT JOIN usuarios u ON l.usuario_id = u.id
        ORDER BY l.fecha_hora DESC";
        $stmt = $bd->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}