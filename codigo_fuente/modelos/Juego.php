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
        $sql = "SELECT id, nombre, categoria
                FROM juegos
                WHERE activo = 1
                ORDER BY nombre ASC";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

        //Juegos activos para el catalogo publico (con la modalidad permitida, si tiene)
    public function obtenerJuegosPublicos(): array {
        $sql = "SELECT j.id, j.nombre, j.categoria, j.imagen_url,
                       (SELECT m.nombre
                        FROM juego_modalidades_permitidas jmp
                        INNER JOIN modalidades m ON m.id = jmp.modalidad_id
                        WHERE jmp.juego_id = j.id
                        ORDER BY m.id
                        LIMIT 1) AS modalidad
                FROM juegos j
                WHERE j.activo = 1
                ORDER BY j.nombre ASC";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

        //Juegos con mas torneos creados (para "Juegos Destacados")
    public function obtenerJuegosDestacados(int $limite = 6): array {
        $sql = "SELECT j.id, j.nombre, j.categoria, j.imagen_url, COUNT(t.id) AS total_torneos
                FROM juegos j
                INNER JOIN torneos t ON t.juego_id = j.id
                WHERE j.activo = 1
                GROUP BY j.id, j.nombre, j.categoria, j.imagen_url
                ORDER BY total_torneos DESC, j.nombre ASC
                LIMIT :limite";
        $stmt = $this->bd->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function estaActivo(int $juegoId): bool {
        $stmt = $this->bd->prepare('SELECT COUNT(*) FROM juegos WHERE id = :id AND activo = 1');
        $stmt->execute([':id' => $juegoId]);
        return (int) $stmt->fetchColumn() === 1;
    }
    
    //2. Crear un nuevo juego
        public function crearJuego(string $nombre, string $categoria, float $pts_victoria, float $pts_empate, float $pts_derrota, array $modalidades = [], array $formatos = [], ?string $imagenUrl = null){
        try {
            $this->bd->beginTransaction();

            $sql = "INSERT INTO juegos(nombre, categoria, imagen_url, puntos_victoria, puntos_empate, puntos_derrota, activo) 
                    VALUES (:nombre, :categoria, :imagen, :p_vic, :p_emp, :p_der, 1)";
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([
               ':nombre' => $nombre,
               ':categoria' => $categoria,
               ':imagen' => $imagenUrl,
               ':p_vic' => $pts_victoria,
               ':p_emp' => $pts_empate,
               ':p_der' => $pts_derrota
            ]);

            $juegoId = $this->bd->lastInsertId();

            if (!empty($modalidades)) {
                $sqlMod = "INSERT INTO juego_modalidades_permitidas (juego_id, modalidad_id) VALUES (:j_id, :m_id)";
                $stmtMod = $this->bd->prepare($sqlMod);
                foreach ($modalidades as $mod_id) {
                    $stmtMod->execute([':j_id' => $juegoId, ':m_id' => $mod_id]);
                }
            }
            
            if (!empty($formatos)) {
                $sqlForm = "INSERT INTO juego_formatos_permitidos (juego_id, formato_codigo) VALUES (:j_id, :f_cod)";
                $stmtForm = $this->bd->prepare($sqlForm);
                foreach ($formatos as $f_cod) {
                    $stmtForm->execute([':j_id' => $juegoId, ':f_cod' => $f_cod]);
                }
            }

            $this->bd->commit();
            return true;
        } catch (\PDOException $e) {
            $this->bd->rollBack();
            error_log("Error creando juego: " . $e->getMessage());
            return false;
        }
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
        public function actualizarJuego(int $juegoId, string $nombre, string $categoria, float $pts_victoria, float $pts_empate, float $pts_derrota, array $modalidades = [], array $formatos = [], ?string $imagenUrl = null){
        try {
            $this->bd->beginTransaction();

            $sql = "UPDATE juegos SET nombre = :nombre, categoria = :categoria, 
                    imagen_url = COALESCE(:imagen, imagen_url),
                    puntos_victoria = :p_vic, puntos_empate = :p_emp, puntos_derrota = :p_der 
                    WHERE id = :id";
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([
               ':nombre' => $nombre,
               ':categoria' => $categoria,
               ':imagen' => $imagenUrl,
               ':p_vic' => $pts_victoria,
               ':p_emp' => $pts_empate,
               ':p_der' => $pts_derrota,
               ':id' => $juegoId
            ]);

            // Eliminar antiguas
            $stmtDelMod = $this->bd->prepare("DELETE FROM juego_modalidades_permitidas WHERE juego_id = :id");
            $stmtDelMod->execute([':id' => $juegoId]);
            
            $stmtDelForm = $this->bd->prepare("DELETE FROM juego_formatos_permitidos WHERE juego_id = :id");
            $stmtDelForm->execute([':id' => $juegoId]);

            // Insertar nuevas
            if (!empty($modalidades)) {
                $sqlMod = "INSERT INTO juego_modalidades_permitidas (juego_id, modalidad_id) VALUES (:j_id, :m_id)";
                $stmtMod = $this->bd->prepare($sqlMod);
                foreach ($modalidades as $mod_id) {
                    $stmtMod->execute([':j_id' => $juegoId, ':m_id' => $mod_id]);
                }
            }
            
            if (!empty($formatos)) {
                $sqlForm = "INSERT INTO juego_formatos_permitidos (juego_id, formato_codigo) VALUES (:j_id, :f_cod)";
                $stmtForm = $this->bd->prepare($sqlForm);
                foreach ($formatos as $f_cod) {
                    $stmtForm->execute([':j_id' => $juegoId, ':f_cod' => $f_cod]);
                }
            }

            $this->bd->commit();
            return true;
        } catch (\PDOException $e) {
            $this->bd->rollBack();
            error_log("Error actualizando juego: " . $e->getMessage());
            return false;
        }
    }

    // Obtener las modalidades permitidas para un juego
    public function obtenerModalidadesPermitidas(int $juegoId) {
        $sql = "SELECT m.id, m.nombre
                FROM modalidades m
                INNER JOIN juego_modalidades_permitidas jmp ON m.id = jmp.modalidad_id
                WHERE jmp.juego_id = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $juegoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener los formatos permitidos para un juego
    public function obtenerFormatosPermitidos(int $juegoId) {
        $sql = "SELECT f.codigo, f.nombre, f.activo 
                FROM formatos_torneo f
                INNER JOIN juego_formatos_permitidos jfp ON f.codigo = jfp.formato_codigo
                WHERE jfp.juego_id = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $juegoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
