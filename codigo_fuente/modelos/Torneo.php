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

        //Metodo interno: encuentra los IDs de participacion de un jugador (directa o via sus equipos)
    private function obtenerParticipacionesDelJugador(int $usuarioId, array $equipoIds): array {
        $condiciones = ["(tipo = 'usuario' AND referencia_id = :usuarioId)"];
        $parametros = [':usuarioId' => $usuarioId];

        if (!empty($equipoIds)) {
            $marcadores = [];
            foreach ($equipoIds as $indice => $equipoId) {
                $clave = ":equipoId{$indice}";
                $marcadores[] = $clave;
                $parametros[$clave] = (int) $equipoId;
            }
            $condiciones[] = "(tipo = 'equipo' AND referencia_id IN (" . implode(',', $marcadores) . "))";
        }

        $sql = "SELECT id FROM participantes_torneo WHERE (" . implode(' OR ', $condiciones) . ") AND estado = 'confirmado'";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute($parametros);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id');
    }

    //Cuenta los torneos activos (en curso o con inscripciones abiertas) donde participa el jugador
    public function contarTorneosActivosDelJugador(int $usuarioId, array $equipoIds): int {
        $participacionIds = $this->obtenerParticipacionesDelJugador($usuarioId, $equipoIds);
        if (empty($participacionIds)) return 0;

        $idsTexto = implode(',', array_map('intval', $participacionIds));
        $sql = "SELECT COUNT(DISTINCT pt.torneo_id) as total
                FROM participantes_torneo pt
                INNER JOIN torneos t ON t.id = pt.torneo_id
                WHERE pt.id IN ($idsTexto)
                AND t.estado IN ('en_curso', 'inscripciones_abiertas')";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($resultado['total'] ?? 0);
    }

    //Trae los proximos partidos programados del jugador (directos o via equipo)
    public function obtenerProximosPartidos(int $usuarioId, array $equipoIds, int $limite = 3): array {
        $participacionIds = $this->obtenerParticipacionesDelJugador($usuarioId, $equipoIds);
        if (empty($participacionIds)) return [];

        $idsTexto = implode(',', array_map('intval', $participacionIds));
        $sql = "SELECT e.id, e.fecha_hora_programada, e.cancha, t.nombre AS torneo_nombre,
                    pl.nombre AS nombre_local, pv.nombre AS nombre_visitante
                FROM torneo_encuentros e
                INNER JOIN torneos t ON t.id = e.torneo_id
                LEFT JOIN participantes_torneo pl ON pl.id = e.participante_local_id
                LEFT JOIN participantes_torneo pv ON pv.id = e.participante_visitante_id
                WHERE (e.participante_local_id IN ($idsTexto) OR e.participante_visitante_id IN ($idsTexto))
                AND e.estado = 'programado'
                ORDER BY e.fecha_hora_programada ASC
                LIMIT :limite";
        $stmt = $this->bd->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Trae el record de victorias/derrotas del jugador en partidos ya finalizados
    public function obtenerRecordDelJugador(int $usuarioId, array $equipoIds): array {
        $participacionIds = $this->obtenerParticipacionesDelJugador($usuarioId, $equipoIds);
        if (empty($participacionIds)) return ['victorias' => 0, 'derrotas' => 0];

        $idsTexto = implode(',', array_map('intval', $participacionIds));
        $sql = "SELECT
                    SUM(CASE WHEN participante_ganador_id IN ($idsTexto) THEN 1 ELSE 0 END) AS victorias,
                    SUM(CASE
                        WHEN estado = 'finalizado' AND participante_ganador_id IS NOT NULL
                        AND participante_ganador_id NOT IN ($idsTexto)
                        AND (participante_local_id IN ($idsTexto) OR participante_visitante_id IN ($idsTexto))
                        THEN 1 ELSE 0 END) AS derrotas
                FROM torneo_encuentros
                WHERE estado = 'finalizado'
                AND (participante_local_id IN ($idsTexto) OR participante_visitante_id IN ($idsTexto))";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return [
            'victorias' => (int) ($resultado['victorias'] ?? 0),
            'derrotas' => (int) ($resultado['derrotas'] ?? 0)
        ];
    }
    
}