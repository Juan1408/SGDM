<?php
namespace App\Modelos;

use PDO;
require_once __DIR__ . '/Conexion.php';

class ResultadoGlobal {
    private PDO $bd;
    
    public function __construct() {
        $this->bd = Conexion::getInstance()->getBD();
    }

    /**
     * Obtiene la lista de juegos que tienen al menos un torneo finalizado o en curso
     */
    public function obtenerJuegosConResultados() {
        $sql = "SELECT j.id, j.nombre, j.categoria
                FROM juegos j
                INNER JOIN torneos t ON t.juego_id = j.id
                WHERE t.estado IN ('finalizado', 'en_curso')
                GROUP BY j.id, j.nombre, j.categoria
                ORDER BY j.nombre ASC";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el Top 10 de mejores participantes históricos de un juego específico
     */
    public function obtenerTop10PorJuego(int $juegoId) {
        $sql = "SELECT pt.nombre AS participante_nombre, pt.tipo AS participante_tipo,
                       COUNT(DISTINCT tp.torneo_id) AS torneos_jugados,
                       SUM(tp.partidos_jugados) AS partidos_jugados,
                       SUM(tp.partidos_ganados) AS partidos_ganados,
                       SUM(tp.puntos) AS puntos_totales
                FROM torneo_posiciones tp
                INNER JOIN participantes_torneo pt ON tp.participante_id = pt.id
                INNER JOIN torneos t ON tp.torneo_id = t.id
                WHERE t.estado IN ('finalizado', 'en_curso') AND t.juego_id = :juego_id
                GROUP BY pt.referencia_id, pt.tipo, pt.nombre
                ORDER BY puntos_totales DESC, partidos_ganados DESC
                LIMIT 10";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':juego_id' => $juegoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    /**
     * Ranking publico de una disciplina, de un solo tipo de participante.
     * $tipo: 'usuario' (jugadores individuales) o 'equipo'.
     * Los puntos solo son comparables dentro de un mismo juego, por eso se filtra por juego.
     * Para jugadores se muestra el apodo si lo tienen (no el nombre completo).
     */
    public function obtenerRankingPorJuego(int $juegoId, string $tipo, int $limite = 20): array {
        $sql = "SELECT pt.referencia_id AS id,
                       COALESCE(NULLIF(MAX(pj.apodo_gamertag), ''), pt.nombre) AS nombre,
                       COUNT(DISTINCT tp.torneo_id) AS torneos_jugados,
                       SUM(tp.partidos_jugados) AS partidos_jugados,
                       SUM(tp.partidos_ganados) AS partidos_ganados,
                       SUM(tp.puntos) AS puntos_totales
                FROM torneo_posiciones tp
                INNER JOIN participantes_torneo pt ON tp.participante_id = pt.id
                INNER JOIN torneos t ON tp.torneo_id = t.id
                LEFT JOIN perfiles_jugadores pj ON pt.tipo = 'usuario' AND pj.usuario_id = pt.referencia_id
                WHERE t.estado IN ('finalizado', 'en_curso')
                  AND t.juego_id = :juego_id
                  AND pt.tipo = :tipo
                GROUP BY pt.referencia_id, pt.nombre
                ORDER BY puntos_totales DESC, partidos_ganados DESC, pt.nombre ASC
                LIMIT " . (int) $limite;
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':juego_id' => $juegoId, ':tipo' => $tipo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    /**
     * Juegos que tienen posiciones cargadas (lo que alimenta el ranking publico)
     */
    public function obtenerJuegosConRanking(): array {
        $sql = "SELECT j.id, j.nombre, j.categoria
                FROM juegos j
                INNER JOIN torneos t ON t.juego_id = j.id
                INNER JOIN torneo_posiciones tp ON tp.torneo_id = t.id
                WHERE t.estado IN ('finalizado', 'en_curso')
                GROUP BY j.id, j.nombre, j.categoria
                ORDER BY j.nombre ASC";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Calcula KPIs globales del sistema (total de puntos, victorias, torneos)
     */
    public function obtenerKPIsGlobales() {
        $sql = "SELECT 
                    SUM(tp.puntos) AS total_puntos_repartidos,
                    SUM(tp.partidos_ganados) AS total_victorias_registradas,
                    SUM(tp.partidos_jugados) AS total_partidos_jugados
                FROM torneo_posiciones tp
                INNER JOIN torneos t ON tp.torneo_id = t.id
                WHERE t.estado IN ('finalizado', 'en_curso')";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total_puntos_repartidos' => 0, 'total_victorias_registradas' => 0, 'total_partidos_jugados' => 0];
    }
}
