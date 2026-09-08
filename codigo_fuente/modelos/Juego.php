<?php

use App\Modelos\Conexion;
require_once __DIR__ . '/Conexion.php';

class Juego {
    private $bd;

    //Constructor para usar la instancia singleton de la base de datos
    public function __construct() {
        $this->bd = Conexion::getInstance()->getBD();
    }

    //Instanciacion estatica para que no de error al intentar usarla en el futuro
    public static function obtenerInstancia() {
        return new self();
    }

    //1. Obtenemos todos los juegos para mostrarlos en las tablas 
    public function obtenerTodosLosJuegos(){
        $sql = "SELECT * FROM juegos ORDER BY nombre ASC";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerJuegosActivos(): array {
        $sql = "SELECT id, nombre, categoria, formato_equipo_defecto
                FROM juegos
                WHERE activo = 1
                ORDER BY nombre ASC";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function estaActivo(int $juegoId): bool {
        $stmt = $this->bd->prepare('SELECT COUNT(*) FROM juegos WHERE id = :id AND activo = 1');
        $stmt->execute([':id' => $juegoId]);
        return (int) $stmt->fetchColumn() === 1;
    }
    
    //2. Crear un nuevo juego
    public function crearJuego(string $nombre, string $categoria, string $formato_equipo){
        $sql = "INSERT INTO juegos(nombre, categoria, formato_equipo_defecto, puntos_victoria,
        puntos_empate, puntos_derrota, activo) 
        VALUES (:nombre, :categoria, :formato_equipo, 3.00, 1.00, 0.00, 1)";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([
           ':nombre' => $nombre,
           ':categoria' => $categoria,
           ':formato_equipo' => $formato_equipo 
        ]);
    }

    //Metodo para cambiar el estado de un juego
    public function cambiarEstado(int $juegoId){
        //Verificamos el estado actual
        $sql = "SELECT activo FROM juegos WHERE id = :id LIMIT 1";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $juegoId]);
        $estadoActual = $stmt->fetch(PDO::FETCH_COLUMN);

        if ($estadoActual === false){
            return false;
        }

        //Con un switch pasamos 1 o 0
        $nuevoEstado = $estadoActual == 1 ? 0 : 1;

        //Actualizamos el estado en la base de datos
        $sqlUpdate = "UPDATE juegos SET activo = :activo WHERE id = :id";
        $stmtUpdate = $this->bd->prepare($sqlUpdate);
        return $stmtUpdate->execute([
            ':activo' => $nuevoEstado,
            ':id' => $juegoId
        ]);
    }

    //Obtener los datos de un juego especifico para llenar el formulario y poder editarlo
    public function obtenerJuegoPorId(int $juegoId){
        $sql = "SELECT * FROM juegos WHERE id = :id LIMIT 1";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $juegoId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //Actualizar los datos de un juego
    public function actualizarJuego(int $juegoId, string $nombre, string $categoria, string $formato_equipo){
        $sql = "UPDATE juegos SET nombre = :nombre, categoria = :categoria, formato_equipo_defecto = :formato_equipo 
        WHERE id = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([
           ':nombre' => $nombre,
           ':categoria' => $categoria,
           ':formato_equipo' => $formato_equipo,
           ':id' => $juegoId
        ]);
    }
}
