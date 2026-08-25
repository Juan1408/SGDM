<?php
/**
 * ============================================================================
 * CLASE MODELO: Torneo.php
 * ============================================================================
 * Propósito: Gestiona el acceso a datos para la tabla 'torneos'.
 * Ubicación: codigo_fuente/modelos/Torneo.php
 * ============================================================================
 */

namespace App\Modelos;
use PDO;
require_once __DIR__ . '/Conexion.php';

class Torneo {
    private $bd;

    public function __construct(){
    //Usamos el patron Singleton para obtener la instancia única de la base de datos
        $this->bd = Conexion::getInstance()->getBD();   
    }

    //Metodo para obtener todos los torneos en curso o abiertos
    public function contarTorneosActivos(){
        //Sentencia SQL para obtener todos los torneos activos
        $sql = "SELECT COUNT(*) as total FROM torneos WHERE estado = 'inscripciones_abiertas' OR estado = 'en_curso'";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'] ?? 0;
    }   

    public function obtenerUltimosTorneosCreados(int $limite = 5){
        $sql = "SELECT id, nombre, estado, fecha_inicio FROM torneos ORDER BY id DESC LIMIT :limite";
        $stmt = $this->bd->prepare($sql);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT); //usamos bindParam para mayor seguridad
        $stmt->execute();
        $torneos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $torneos;
    }
    
    //Metodo para obtener todos los torneos
    public function obtenerTorneos(){
        $sql = "SELECT t.*, j.nombre AS nombre_juego
                FROM torneos t
                INNER JOIN juegos j ON t.juego_id = j.id
                ORDER BY t.fecha_inicio DESC";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute();
        $torneos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $torneos;
    }   
}