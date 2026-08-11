<?php
/**
 * CLASE EQUIPO: Equipo.php
 * 
 * Propósito: Gestión de equipos
 * Ubicación: codigo_fuente/modelos/Equipo.php
 */

namespace App\Modelos;
use PDO;
require_once __DIR__ . '/Conexion.php';

class Equipo {
    private $bd;

    public function __construct(){
        //Usamos el patron Singleton para obtener la instancia única de la base de datos
        $this->bd = Conexion::getInstance()->getBD();
    }
    
    //Metodo para buscar un equipo por su nombre
    public function buscarPorNombre(string $nombre){
        //Escribimos la consulta SQL usando un marcador por seguridad (:nombre)
        $sql = "SELECT * FROM equipos WHERE nombre = :nombre AND esta_activo = 1 LIMIT 1";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':nombre' => $nombre]);
        $equipo = $stmt->fetch(PDO::FETCH_ASSOC);
        return $equipo;
    }
    //Metodo para crear un equipo
    public function crearEquipo(string $nombre, string $logo, int $organizacionId, int $deporteId){
        //Preparamos la consulta SQL
        $sql = "INSERT INTO equipos (nombre, logo, organizacion_id, deporte_id) VALUES (:nombre, :logo, :organizacionId, :deporteId)";
        //Preparamos la consulta para evitar inyección SQL
        $stmt = $this->bd->prepare($sql);
        //Ejecutamos la consulta pasandole los datos
        $stmt->execute([':nombre' => $nombre, ':logo' => $logo, ':organizacionId' => $organizacionId, ':deporteId' => $deporteId]);
        //Devuelve la ID del equipo recien creado
        return $this->bd->lastInsertId();
    }

    //Metodo para eliminar un equipo ( Soft Delete)
    //Faltan ajustes de restricciones de integridad referencial
    public function eliminarEquipo(int $id){
        //Desactivamos el equipo en lugar de borrarlo completamente
        $sql = "UPDATE equipos SET esta_activo = 0 WHERE id = :id";
        $stmt = $this->bd->prepare($sql);
        //Ejecutamos la consulta pasandole los datos
        $stmt->execute([':id' => $id]);
        //Devuelve true si la operacion fue exitosa
        return $stmt->rowCount() > 0;
    }

    public function contarTotalEquipos(){
        $sql = "SELECT COUNT(*) as total FROM equipos";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'] ?? 0;
    }

    public function obtenerUltimosEquiposCreados(int $limite = 5){
        $sql = "SELECT e.id,
        e.nombre_equipo,
        e.creado_por,
        COUNT(em.usuario_id) as total_miembros
        FROM equipos e
        LEFT JOIN equipo_miembros em ON e.id = em.equipo_id
        GROUP BY e.id, e.nombre_equipo, e.creado_por
        ORDER BY e.id DESC
        LIMIT :limite";
        $stmt = $this->bd->prepare($sql);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        $equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $equipos;
    }
}