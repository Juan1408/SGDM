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

    public function obtenerUltimosTorneos(int $limite = 5){
        return $this->obtenerUltimosTorneosCreados($limite);
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

    public function obtenerEstadisticasOrganizador(int $organizadorId): array {
        $sql = "SELECT
                    SUM(CASE WHEN estado IN ('inscripciones_abiertas', 'en_curso') THEN 1 ELSE 0 END) AS activos,
                    SUM(CASE WHEN estado = 'finalizado' THEN 1 ELSE 0 END) AS finalizados,
                    COALESCE((SELECT COUNT(*)
                        FROM participantes_torneo p
                        INNER JOIN torneos t2 ON t2.id = p.torneo_id
                        WHERE t2.organizador_id = :organizador_id_solicitudes
                        AND p.estado = 'pendiente'), 0) AS solicitudes
                FROM torneos
                WHERE organizador_id = :organizador_id_torneos";
        $stmt = $this->bd->prepare($sql);
        $stmt->bindValue(':organizador_id_solicitudes', $organizadorId, PDO::PARAM_INT);
        $stmt->bindValue(':organizador_id_torneos', $organizadorId, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        return [
            'activos' => (int) ($resultado['activos'] ?? 0),
            'solicitudes' => (int) ($resultado['solicitudes'] ?? 0),
            'finalizados' => (int) ($resultado['finalizados'] ?? 0),
        ];
    }

    public function obtenerTorneosPorOrganizador(int $organizadorId, int $limite = 5): array {
        $sql = "SELECT t.id, t.nombre, t.estado, t.formato, t.cupo_max_equipos,
                       t.fecha_inicio, j.nombre AS nombre_juego,
                       COUNT(CASE WHEN p.estado = 'confirmado' THEN 1 END) AS equipos_confirmados
                FROM torneos t
                INNER JOIN juegos j ON j.id = t.juego_id
                LEFT JOIN participantes_torneo p ON p.torneo_id = t.id
                WHERE t.organizador_id = :organizador_id
                GROUP BY t.id, t.nombre, t.estado, t.formato, t.cupo_max_equipos,
                         t.fecha_inicio, j.nombre
                ORDER BY t.fecha_inicio IS NULL, t.fecha_inicio ASC, t.id DESC
                LIMIT :limite";
        $stmt = $this->bd->prepare($sql);
        $stmt->bindValue(':organizador_id', $organizadorId, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTodosPorOrganizador(int $organizadorId): array {
        $sql = "SELECT t.id, t.nombre, t.estado, t.formato, t.cupo_max_equipos,
                    t.fecha_inicio, t.fecha_fin, j.nombre AS nombre_juego,
                    j.categoria, COUNT(CASE WHEN p.estado = 'confirmado' THEN 1 END) AS equipos_confirmados
                FROM torneos t
                INNER JOIN juegos j ON j.id = t.juego_id
                LEFT JOIN participantes_torneo p ON p.torneo_id = t.id
                WHERE t.organizador_id = :organizador_id
                GROUP BY t.id, t.nombre, t.estado, t.formato, t.cupo_max_equipos,
                        t.fecha_inicio, t.fecha_fin, j.nombre, j.categoria
                ORDER BY t.creado_en DESC, t.id DESC";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':organizador_id' => $organizadorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerModalidades(): array {
        $stmt = $this->bd->query('SELECT id, nombre, descripcion FROM modalidades ORDER BY nombre ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerSistemasPuntuacion(): array {
        $stmt = $this->bd->query('SELECT id, nombre, descripcion, puntos_victoria, puntos_empate, puntos_derrota FROM sistemas_puntuacion ORDER BY nombre ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearBorrador(array $datos, int $organizadorId): int {
        $sql = "INSERT INTO torneos
                    (nombre, juego_id, formato, estado, cupo_max_equipos, organizador_id,
                    modalidad_id, sistema_puntuacion_id, tipo_resultado, mejor_de)
                VALUES (:nombre, :juego_id, :formato, 'borrador', :cupo, :organizador_id,
                        :modalidad_id, :sistema_puntuacion_id, :tipo_resultado, :mejor_de)";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([
            ':nombre' => $datos['nombre'],
            ':juego_id' => $datos['juego_id'],
            ':formato' => $datos['formato'],
            ':cupo' => $datos['cupo'],
            ':organizador_id' => $organizadorId,
            ':modalidad_id' => $datos['modalidad_id'],
            ':sistema_puntuacion_id' => $datos['sistema_puntuacion_id'],
            ':tipo_resultado' => $datos['tipo_resultado'],
            ':mejor_de' => $datos['mejor_de'],
        ]);

        return (int) $this->bd->lastInsertId();
    }

    public function actualizarEtapaDatos(int $torneoId, int $organizadorId, array $datos): bool {
        $sql = "UPDATE torneos SET
                    fecha_inicio_inscripcion = :fecha_inicio_inscripcion,
                    fecha_limite_inscripcion = :fecha_limite_inscripcion,
                    fecha_inicio = :fecha_inicio,
                    fecha_fin = :fecha_fin,
                    descripcion = :descripcion,
                    reglas = :reglas,
                    ubicacion = :ubicacion,
                    localidad = :localidad,
                    comunidad = :comunidad,
                    premios = :premios,
                    banner_url = :banner_url,
                    stream_url = :stream_url,
                    tipo_resultado = :tipo_resultado,
                    mejor_de = :mejor_de,
                    modalidad_id = :modalidad_id,
                    sistema_puntuacion_id = :sistema_puntuacion_id
                WHERE id = :id AND organizador_id = :organizador_id AND estado = 'borrador'";
        try {
            $this->bd->beginTransaction();
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([
            ':fecha_inicio_inscripcion' => $datos['fecha_inicio_inscripcion'],
            ':fecha_limite_inscripcion' => $datos['fecha_limite_inscripcion'],
            ':fecha_inicio' => $datos['fecha_inicio'],
            ':fecha_fin' => $datos['fecha_fin'],
            ':descripcion' => $datos['descripcion'],
            ':reglas' => $datos['reglas'],
            ':ubicacion' => $datos['ubicacion'],
                ':localidad' => $datos['localidad'],
                ':comunidad' => $datos['comunidad'],
                ':premios' => $datos['premios'],
                ':banner_url' => $datos['banner_url'],
                ':stream_url' => $datos['stream_url'],
                ':tipo_resultado' => $datos['tipo_resultado'],
                ':mejor_de' => $datos['mejor_de'],
                ':modalidad_id' => $datos['modalidad_id'],
                ':sistema_puntuacion_id' => $datos['sistema_puntuacion_id'],
                ':id' => $torneoId,
                ':organizador_id' => $organizadorId,
            ]);

            $this->bd->prepare('DELETE FROM torneo_actividades WHERE torneo_id = :torneo_id')
                ->execute([':torneo_id' => $torneoId]);
            $actividad = $this->bd->prepare(
                'INSERT INTO torneo_actividades (torneo_id, titulo, tipo, fecha, hora)
                VALUES (:torneo_id, :titulo, :tipo, :fecha, :hora)'
            );
            foreach ($datos['actividades'] as $item) {
                if ($item['titulo'] === '' || $item['fecha'] === '') {
                    continue;
                }
                $actividad->execute([
                    ':torneo_id' => $torneoId,
                    ':titulo' => $item['titulo'],
                    ':tipo' => $item['tipo'],
                    ':fecha' => $item['fecha'],
                    ':hora' => $item['hora'] !== '' ? $item['hora'] : null,
                ]);
            }
            $this->bd->commit();
            return true;
        } catch (\Throwable $error) {
            if ($this->bd->inTransaction()) {
                $this->bd->rollBack();
            }
            return false;
        }
    }

    public function obtenerBorrador(int $torneoId, int $organizadorId): ?array {
        $sql = "SELECT t.*, j.nombre AS nombre_juego
                FROM torneos t
                INNER JOIN juegos j ON j.id = t.juego_id
                WHERE t.id = :id AND t.organizador_id = :organizador_id
                LIMIT 1";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $torneoId, ':organizador_id' => $organizadorId]);
        $torneo = $stmt->fetch(PDO::FETCH_ASSOC);
        return $torneo ?: null;
    }

    public function publicarBorrador(int $torneoId, int $organizadorId): bool {
        $sql = "UPDATE torneos
                SET estado = 'inscripciones_abiertas'
                WHERE id = :id AND organizador_id = :organizador_id AND estado = 'borrador'
                AND fecha_inicio_inscripcion IS NOT NULL
                AND fecha_limite_inscripcion IS NOT NULL
                AND fecha_inicio IS NOT NULL
                AND fecha_fin IS NOT NULL";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([':id' => $torneoId, ':organizador_id' => $organizadorId]);
    }

    public function actualizarGestion(int $torneoId, int $organizadorId, array $datos): bool {
        $torneo = $this->obtenerBorrador($torneoId, $organizadorId);
        if (!$torneo || in_array($torneo['estado'], ['en_curso', 'finalizado', 'cancelado'], true)) {
            return false;
        }

        $sql = "UPDATE torneos SET descripcion = :descripcion, reglas = :reglas,
                    comunidad = :comunidad, premios = :premios, banner_url = :banner_url,
                    stream_url = :stream_url, fecha_inicio_inscripcion = :fecha_inicio_inscripcion,
                    fecha_limite_inscripcion = :fecha_limite_inscripcion, fecha_inicio = :fecha_inicio,
                    fecha_fin = :fecha_fin
                WHERE id = :id AND organizador_id = :organizador_id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([
            ':descripcion' => $datos['descripcion'],
            ':reglas' => $datos['reglas'],
            ':comunidad' => $datos['comunidad'],
            ':premios' => $datos['premios'],
            ':banner_url' => $datos['banner_url'],
            ':stream_url' => $datos['stream_url'],
            ':fecha_inicio_inscripcion' => $datos['fecha_inicio_inscripcion'],
            ':fecha_limite_inscripcion' => $datos['fecha_limite_inscripcion'],
            ':fecha_inicio' => $datos['fecha_inicio'],
            ':fecha_fin' => $datos['fecha_fin'],
            ':id' => $torneoId,
            ':organizador_id' => $organizadorId,
        ]);
    }

    public function cambiarEstado(int $torneoId, int $organizadorId, string $estadoNuevo): bool {
        $torneo = $this->obtenerBorrador($torneoId, $organizadorId);
        $transiciones = [
            'borrador' => ['inscripciones_abiertas', 'cancelado'],
            'inscripciones_abiertas' => ['en_curso', 'cancelado'],
            'en_curso' => ['finalizado'],
            'finalizado' => [],
            'cancelado' => [],
        ];
        if (!$torneo || !in_array($estadoNuevo, $transiciones[$torneo['estado']] ?? [], true)) {
            return false;
        }

        if ($estadoNuevo === 'finalizado' && !$this->generarPodio($torneoId)) {
            return false;
        }

        try {
            $this->bd->beginTransaction();
            $stmt = $this->bd->prepare(
                "UPDATE torneos SET estado = :estado WHERE id = :id AND organizador_id = :organizador_id"
            );
            $stmt->execute([
                ':estado' => $estadoNuevo,
                ':id' => $torneoId,
                ':organizador_id' => $organizadorId,
            ]);
            $historial = $this->bd->prepare(
                'INSERT INTO torneo_cambio_estado (torneo_id, estado_anterior, estado_nuevo, usuario_id)
                 VALUES (:torneo_id, :estado_anterior, :estado_nuevo, :usuario_id)'
            );
            $historial->execute([
                ':torneo_id' => $torneoId,
                ':estado_anterior' => $torneo['estado'],
                ':estado_nuevo' => $estadoNuevo,
                ':usuario_id' => $organizadorId,
            ]);
            $this->bd->commit();
            return true;
        } catch (\Throwable $error) {
            if ($this->bd->inTransaction()) {
                $this->bd->rollBack();
            }
            return false;
        }
    }

    private function generarPodio(int $torneoId): bool {
        $pendientes = $this->bd->prepare(
            "SELECT COUNT(*) FROM torneo_encuentros WHERE torneo_id = :torneo_id AND estado <> 'finalizado'"
        );
        $pendientes->execute([':torneo_id' => $torneoId]);
        if ((int) $pendientes->fetchColumn() > 0) {
            return false;
        }

        $torneoStmt = $this->bd->prepare('SELECT formato FROM torneos WHERE id = :torneo_id');
        $torneoStmt->execute([':torneo_id' => $torneoId]);
        $formato = $torneoStmt->fetchColumn();
        $podio = ['primero' => null, 'segundo' => null, 'tercero' => null];
        if ($formato === 'liga' || $formato === 'suizo') {
            $stmt = $this->bd->prepare(
                "SELECT p.nombre FROM torneo_posiciones pos
                 INNER JOIN participantes_torneo p ON p.id = pos.participante_id
                 WHERE pos.torneo_id = :torneo_id
                 ORDER BY pos.puntos DESC, pos.diferencia_goles DESC, pos.puntos_favor DESC, p.id ASC
                 LIMIT 3"
            );
            $stmt->execute([':torneo_id' => $torneoId]);
            $puestos = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $podio['primero'] = $puestos[0] ?? null;
            $podio['segundo'] = $puestos[1] ?? null;
            $podio['tercero'] = $puestos[2] ?? null;
        } else {
            $stmt = $this->bd->prepare(
                "SELECT e.ronda, ganador.nombre AS ganador, perdedor.nombre AS perdedor
                 FROM torneo_encuentros e
                 INNER JOIN participantes_torneo ganador ON ganador.id = e.participante_ganador_id
                 INNER JOIN participantes_torneo local_p ON local_p.id = e.participante_local_id
                 INNER JOIN participantes_torneo visitante_p ON visitante_p.id = e.participante_visitante_id
                 INNER JOIN participantes_torneo perdedor ON perdedor.id = CASE
                    WHEN e.participante_ganador_id = e.participante_local_id THEN e.participante_visitante_id
                    ELSE e.participante_local_id END
                 WHERE e.torneo_id = :torneo_id
                 ORDER BY e.ronda DESC, e.id DESC LIMIT 1"
            );
            $stmt->execute([':torneo_id' => $torneoId]);
            $final = $stmt->fetch(PDO::FETCH_ASSOC);
            $podio['primero'] = $final['ganador'] ?? null;
            $podio['segundo'] = $final['perdedor'] ?? null;
            if ($final && (int) $final['ronda'] > 1) {
                $tercero = $this->bd->prepare(
                    "SELECT perdedor.nombre FROM torneo_encuentros e
                     INNER JOIN participantes_torneo perdedor ON perdedor.id = CASE
                        WHEN e.participante_ganador_id = e.participante_local_id THEN e.participante_visitante_id
                        ELSE e.participante_local_id END
                     WHERE e.torneo_id = :torneo_id AND e.ronda = :ronda
                     ORDER BY e.id ASC LIMIT 1"
                );
                $tercero->execute([':torneo_id' => $torneoId, ':ronda' => (int) $final['ronda'] - 1]);
                $podio['tercero'] = $tercero->fetchColumn() ?: null;
            }
        }
        if ($podio['primero'] === null) {
            return false;
        }

        $guardar = $this->bd->prepare(
            "INSERT INTO torneo_config (torneo_id, clave, valor) VALUES (:torneo_id, 'podio', :valor)
             ON DUPLICATE KEY UPDATE valor = VALUES(valor)"
        );
        $guardar->execute([':torneo_id' => $torneoId, ':valor' => json_encode($podio, JSON_UNESCAPED_UNICODE)]);
        return true;
    }

    public function obtenerPodio(int $torneoId): ?array {
        $stmt = $this->bd->prepare("SELECT valor FROM torneo_config WHERE torneo_id = :torneo_id AND clave = 'podio'");
        $stmt->execute([':torneo_id' => $torneoId]);
        $valor = $stmt->fetchColumn();
        if (!$valor) {
            return null;
        }
        $podio = json_decode($valor, true);
        return is_array($podio) ? $podio : null;
    }

    public function obtenerResultadosOrganizador(int $organizadorId): array {
        $stmt = $this->bd->prepare(
            "SELECT e.id, e.ronda, e.estado, e.resultado_local, e.resultado_visitante,
                    e.ultima_modificacion, t.id AS torneo_id, t.nombre AS torneo_nombre,
                    pl.nombre AS local_nombre, pv.nombre AS visitante_nombre
             FROM torneo_encuentros e
             INNER JOIN torneos t ON t.id = e.torneo_id
             LEFT JOIN participantes_torneo pl ON pl.id = e.participante_local_id
             LEFT JOIN participantes_torneo pv ON pv.id = e.participante_visitante_id
             WHERE t.organizador_id = :organizador_id
             ORDER BY e.ultima_modificacion DESC, e.id DESC"
        );
        $stmt->execute([':organizador_id' => $organizadorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerHistorialOrganizador(int $organizadorId): array {
        $stmt = $this->bd->prepare(
            "SELECT 'estado' AS tipo, t.nombre AS torneo_nombre,
                    CONCAT(COALESCE(c.estado_anterior, 'inicio'), ' -> ', c.estado_nuevo) AS detalle,
                    c.fecha_cambio AS fecha
             FROM torneo_cambio_estado c
             INNER JOIN torneos t ON t.id = c.torneo_id
             WHERE t.organizador_id = :organizador_id_estado
             UNION ALL
             SELECT 'resultado' AS tipo, t.nombre AS torneo_nombre,
                    CONCAT(COALESCE(pl.nombre, 'Pendiente'), ' ', e.resultado_local,
                           ' - ', e.resultado_visitante, ' ', COALESCE(pv.nombre, 'Pendiente')) AS detalle,
                    e.ultima_modificacion AS fecha
             FROM torneo_encuentros e
             INNER JOIN torneos t ON t.id = e.torneo_id
             LEFT JOIN participantes_torneo pl ON pl.id = e.participante_local_id
             LEFT JOIN participantes_torneo pv ON pv.id = e.participante_visitante_id
             WHERE t.organizador_id = :organizador_id_resultado AND e.estado = 'finalizado'
             ORDER BY fecha DESC"
        );
        $stmt->execute([
            ':organizador_id_estado' => $organizadorId,
            ':organizador_id_resultado' => $organizadorId,
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerCalendarioOrganizador(int $organizadorId): array {
        $stmt = $this->bd->prepare(
            "SELECT fecha, hora, tipo, titulo, torneo_nombre, clase FROM (
                SELECT a.fecha, a.hora, a.tipo, a.titulo, t.nombre AS torneo_nombre, 'actividad' AS clase
                FROM torneo_actividades a
                INNER JOIN torneos t ON t.id = a.torneo_id
                WHERE t.organizador_id = :organizador_id_actividad
                UNION ALL
                SELECT DATE(e.fecha_hora_programada) AS fecha, TIME(e.fecha_hora_programada) AS hora,
                       'partido' AS tipo,
                       CONCAT(COALESCE(pl.nombre, 'Pendiente'), ' vs ', COALESCE(pv.nombre, 'Pendiente')) AS titulo,
                       t.nombre AS torneo_nombre, 'partido' AS clase
                FROM torneo_encuentros e
                INNER JOIN torneos t ON t.id = e.torneo_id
                LEFT JOIN participantes_torneo pl ON pl.id = e.participante_local_id
                LEFT JOIN participantes_torneo pv ON pv.id = e.participante_visitante_id
                WHERE t.organizador_id = :organizador_id_partido
            ) calendario
            ORDER BY fecha IS NULL, fecha ASC, hora IS NULL, hora ASC"
        );
        $stmt->execute([
            ':organizador_id_actividad' => $organizadorId,
            ':organizador_id_partido' => $organizadorId,
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerInscripciones(int $torneoId, int $organizadorId): array {
        $sql = "SELECT p.id, p.tipo, p.referencia_id, p.nombre, p.estado,
                       p.fecha_inscripcion, t.cupo_max_equipos,
                       (SELECT COUNT(*) FROM participantes_torneo confirmados
                        WHERE confirmados.torneo_id = p.torneo_id
                          AND confirmados.estado = 'confirmado') AS confirmados
                FROM participantes_torneo p
                INNER JOIN torneos t ON t.id = p.torneo_id
                WHERE p.torneo_id = :torneo_id AND t.organizador_id = :organizador_id
                ORDER BY FIELD(p.estado, 'pendiente', 'confirmado', 'rechazado'), p.fecha_inscripcion ASC";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':torneo_id' => $torneoId, ':organizador_id' => $organizadorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerFixture(int $torneoId, int $organizadorId): array {
        $sql = "SELECT e.*, pl.nombre AS local_nombre, pv.nombre AS visitante_nombre
                FROM torneo_encuentros e
                INNER JOIN torneos t ON t.id = e.torneo_id
                LEFT JOIN participantes_torneo pl ON pl.id = e.participante_local_id
                LEFT JOIN participantes_torneo pv ON pv.id = e.participante_visitante_id
                WHERE e.torneo_id = :torneo_id AND t.organizador_id = :organizador_id
                ORDER BY e.ronda ASC, e.id ASC";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':torneo_id' => $torneoId, ':organizador_id' => $organizadorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function generarFixture(int $torneoId, int $organizadorId): array {
        try {
            $this->bd->beginTransaction();
            $torneoStmt = $this->bd->prepare(
                "SELECT formato, estado, cupo_max_equipos FROM torneos
                 WHERE id = :torneo_id AND organizador_id = :organizador_id FOR UPDATE"
            );
            $torneoStmt->execute([':torneo_id' => $torneoId, ':organizador_id' => $organizadorId]);
            $torneo = $torneoStmt->fetch(PDO::FETCH_ASSOC);
            if (!$torneo || !in_array($torneo['estado'], ['inscripciones_abiertas', 'en_curso'], true)) {
                throw new \RuntimeException('El torneo debe estar abierto o en curso.');
            }

            $participantesStmt = $this->bd->prepare(
                "SELECT id FROM participantes_torneo
                 WHERE torneo_id = :torneo_id AND estado = 'confirmado' ORDER BY id ASC"
            );
            $participantesStmt->execute([':torneo_id' => $torneoId]);
            $participantes = array_map('intval', $participantesStmt->fetchAll(PDO::FETCH_COLUMN));
            if (count($participantes) < 2) {
                throw new \RuntimeException('Se necesitan al menos dos participantes confirmados.');
            }

            $cantidad = count($participantes);
            if ($torneo['formato'] === 'eliminacion_directa' && ($cantidad & ($cantidad - 1)) !== 0) {
                throw new \RuntimeException('La eliminación directa necesita 2, 4, 8, 16, 32 o 64 participantes.');
            }

            $this->bd->prepare('DELETE FROM torneo_encuentros WHERE torneo_id = :torneo_id AND estado = \'programado\'')
                ->execute([':torneo_id' => $torneoId]);
            $insertar = $this->bd->prepare(
                "INSERT INTO torneo_encuentros
                 (torneo_id, ronda, participante_local_id, participante_visitante_id, estado)
                 VALUES (:torneo_id, :ronda, :local_id, :visitante_id, 'programado')"
            );

            if ($torneo['formato'] === 'liga') {
                for ($indice = 0; $indice < $cantidad; $indice++) {
                    for ($oponente = $indice + 1; $oponente < $cantidad; $oponente++) {
                        $insertar->execute([
                            ':torneo_id' => $torneoId,
                            ':ronda' => $indice + 1,
                            ':local_id' => $participantes[$indice],
                            ':visitante_id' => $participantes[$oponente],
                        ]);
                    }
                }
            } elseif ($torneo['formato'] === 'eliminacion_directa') {
                for ($indice = 0; $indice < $cantidad; $indice += 2) {
                    $insertar->execute([
                        ':torneo_id' => $torneoId,
                        ':ronda' => 1,
                        ':local_id' => $participantes[$indice],
                        ':visitante_id' => $participantes[$indice + 1],
                    ]);
                }
            } else {
                $this->crearRondaSuiza($torneoId, $participantes, 1);
            }
            $this->bd->commit();
            return ['ok' => true, 'mensaje' => 'Fixture generado correctamente.'];
        } catch (\Throwable $error) {
            if ($this->bd->inTransaction()) {
                $this->bd->rollBack();
            }
            return ['ok' => false, 'mensaje' => $error->getMessage()];
        }
    }

    public function guardarResultado(int $encuentroId, int $organizadorId, float $local, float $visitante): array {
        try {
            $this->bd->beginTransaction();
            $consulta = $this->bd->prepare(
                "SELECT e.id, e.torneo_id, e.participante_local_id, e.participante_visitante_id,
                        e.estado, t.formato, t.estado AS estado_torneo
                 FROM torneo_encuentros e
                 INNER JOIN torneos t ON t.id = e.torneo_id
                 WHERE e.id = :encuentro_id AND t.organizador_id = :organizador_id
                 FOR UPDATE"
            );
            $consulta->execute([':encuentro_id' => $encuentroId, ':organizador_id' => $organizadorId]);
            $encuentro = $consulta->fetch(PDO::FETCH_ASSOC);
            if (!$encuentro || $encuentro['estado'] === 'finalizado' || $encuentro['estado_torneo'] !== 'en_curso') {
                throw new \RuntimeException('El partido no está disponible para cargar resultados.');
            }
            if ($local < 0 || $visitante < 0 || ($encuentro['formato'] === 'eliminacion_directa' && $local === $visitante)) {
                throw new \RuntimeException('El resultado no es válido para este formato.');
            }

            $ganador = $local > $visitante
                ? $encuentro['participante_local_id']
                : ($visitante > $local ? $encuentro['participante_visitante_id'] : null);
            $actualizar = $this->bd->prepare(
                "UPDATE torneo_encuentros SET resultado_local = :local, resultado_visitante = :visitante,
                    participante_ganador_id = :ganador, estado = 'finalizado',
                    modificado_por_usuario_id = :usuario_id
                 WHERE id = :id AND estado <> 'finalizado'"
            );
            $actualizar->execute([
                ':local' => $local,
                ':visitante' => $visitante,
                ':ganador' => $ganador,
                ':usuario_id' => $organizadorId,
                ':id' => $encuentroId,
            ]);
            if ($encuentro['formato'] === 'liga' || $encuentro['formato'] === 'suizo') {
                $this->recalcularPosiciones((int) $encuentro['torneo_id']);
            }
            if ($encuentro['formato'] === 'eliminacion_directa') {
                $this->avanzarEliminacion((int) $encuentro['torneo_id']);
            } elseif ($encuentro['formato'] === 'suizo') {
                $this->avanzarSuizo((int) $encuentro['torneo_id']);
            }
            $this->bd->commit();
            return ['ok' => true, 'mensaje' => 'Resultado guardado correctamente.'];
        } catch (\Throwable $error) {
            if ($this->bd->inTransaction()) {
                $this->bd->rollBack();
            }
            return ['ok' => false, 'mensaje' => $error->getMessage()];
        }
    }

    private function avanzarEliminacion(int $torneoId): void {
        $rondaStmt = $this->bd->prepare(
            "SELECT MAX(ronda) FROM torneo_encuentros WHERE torneo_id = :torneo_id"
        );
        $rondaStmt->execute([':torneo_id' => $torneoId]);
        $rondaActual = (int) $rondaStmt->fetchColumn();

        $partidosStmt = $this->bd->prepare(
            "SELECT id, participante_ganador_id FROM torneo_encuentros
             WHERE torneo_id = :torneo_id AND ronda = :ronda ORDER BY id ASC"
        );
        $partidosStmt->execute([':torneo_id' => $torneoId, ':ronda' => $rondaActual]);
        $partidos = $partidosStmt->fetchAll(PDO::FETCH_ASSOC);
        if ($partidos === [] || count(array_filter($partidos, static fn ($partido) => $partido['participante_ganador_id'] !== null)) !== count($partidos)) {
            return;
        }

        $ganadores = array_map(static fn ($partido) => (int) $partido['participante_ganador_id'], $partidos);
        if (count($ganadores) === 1) {
            return;
        }

        $siguiente = $this->bd->prepare(
            "SELECT COUNT(*) FROM torneo_encuentros WHERE torneo_id = :torneo_id AND ronda = :ronda"
        );
        $siguiente->execute([':torneo_id' => $torneoId, ':ronda' => $rondaActual + 1]);
        if ((int) $siguiente->fetchColumn() > 0) {
            return;
        }

        $insertar = $this->bd->prepare(
            "INSERT INTO torneo_encuentros
             (torneo_id, ronda, participante_local_id, participante_visitante_id, estado)
             VALUES (:torneo_id, :ronda, :local_id, :visitante_id, 'programado')"
        );
        for ($indice = 0; $indice < count($ganadores); $indice += 2) {
            $insertar->execute([
                ':torneo_id' => $torneoId,
                ':ronda' => $rondaActual + 1,
                ':local_id' => $ganadores[$indice],
                ':visitante_id' => $ganadores[$indice + 1],
            ]);
        }
    }

    private function avanzarSuizo(int $torneoId): void {
        $rondaStmt = $this->bd->prepare('SELECT MAX(ronda) FROM torneo_encuentros WHERE torneo_id = :torneo_id');
        $rondaStmt->execute([':torneo_id' => $torneoId]);
        $rondaActual = (int) $rondaStmt->fetchColumn();
        $partidosStmt = $this->bd->prepare(
            "SELECT id FROM torneo_encuentros WHERE torneo_id = :torneo_id AND ronda = :ronda"
        );
        $partidosStmt->execute([':torneo_id' => $torneoId, ':ronda' => $rondaActual]);
        $partidos = $partidosStmt->fetchAll(PDO::FETCH_COLUMN);
        if ($partidos === []) {
            return;
        }
        $pendientes = $this->bd->prepare(
            "SELECT COUNT(*) FROM torneo_encuentros WHERE torneo_id = :torneo_id AND ronda = :ronda AND estado <> 'finalizado'"
        );
        $pendientes->execute([':torneo_id' => $torneoId, ':ronda' => $rondaActual]);
        if ((int) $pendientes->fetchColumn() > 0) {
            return;
        }

        $siguiente = $this->bd->prepare(
            'SELECT COUNT(*) FROM torneo_encuentros WHERE torneo_id = :torneo_id AND ronda = :ronda'
        );
        $siguiente->execute([':torneo_id' => $torneoId, ':ronda' => $rondaActual + 1]);
        if ((int) $siguiente->fetchColumn() > 0) {
            return;
        }

        $participantesStmt = $this->bd->prepare(
            "SELECT p.id, COALESCE(pos.puntos, 0) AS puntos
             FROM participantes_torneo p
             LEFT JOIN torneo_posiciones pos ON pos.participante_id = p.id AND pos.torneo_id = p.torneo_id
             WHERE p.torneo_id = :torneo_id AND p.estado = 'confirmado'
             ORDER BY puntos DESC, p.id ASC"
        );
        $participantesStmt->execute([':torneo_id' => $torneoId]);
        $participantes = $participantesStmt->fetchAll(PDO::FETCH_ASSOC);
        $ids = array_map(static fn ($item) => (int) $item['id'], $participantes);
        $this->crearRondaSuiza($torneoId, $ids, $rondaActual + 1);
    }

    private function crearRondaSuiza(int $torneoId, array $participantes, int $ronda): void {
        $parejas = $this->bd->prepare(
            "SELECT participante_a_id, participante_b_id FROM torneo_suizo_parejas WHERE torneo_id = :torneo_id"
        );
        $parejas->execute([':torneo_id' => $torneoId]);
        $enfrentados = [];
        foreach ($parejas->fetchAll(PDO::FETCH_ASSOC) as $pareja) {
            $a = (int) $pareja['participante_a_id'];
            $b = (int) $pareja['participante_b_id'];
            $enfrentados[$a . ':' . $b] = true;
            $enfrentados[$b . ':' . $a] = true;
        }
        $insertarPartido = $this->bd->prepare(
            "INSERT INTO torneo_encuentros (torneo_id, ronda, participante_local_id, participante_visitante_id, estado)
             VALUES (:torneo_id, :ronda, :local_id, :visitante_id, 'programado')"
        );
        $insertarPareja = $this->bd->prepare(
            "INSERT INTO torneo_suizo_parejas (torneo_id, participante_a_id, participante_b_id, ronda)
             VALUES (:torneo_id, :participante_a_id, :participante_b_id, :ronda)"
        );
        while (count($participantes) > 1) {
            $local = array_shift($participantes);
            $indiceOponente = null;
            foreach ($participantes as $indice => $oponente) {
                if (!isset($enfrentados[$local . ':' . $oponente])) {
                    $indiceOponente = $indice;
                    break;
                }
            }
            if ($indiceOponente === null) {
                $indiceOponente = 0;
            }
            $visitante = array_splice($participantes, $indiceOponente, 1)[0];
            $a = min($local, $visitante);
            $b = max($local, $visitante);
            $insertarPartido->execute([':torneo_id' => $torneoId, ':ronda' => $ronda, ':local_id' => $local, ':visitante_id' => $visitante]);
            $insertarPareja->execute([':torneo_id' => $torneoId, ':participante_a_id' => $a, ':participante_b_id' => $b, ':ronda' => $ronda]);
        }
    }

    private function recalcularPosiciones(int $torneoId): void {
        $this->bd->prepare('DELETE FROM torneo_posiciones WHERE torneo_id = :torneo_id')
            ->execute([':torneo_id' => $torneoId]);
        $participantes = $this->bd->prepare(
            "SELECT id FROM participantes_torneo WHERE torneo_id = :torneo_id AND estado = 'confirmado'"
        );
        $participantes->execute([':torneo_id' => $torneoId]);
        $insertar = $this->bd->prepare(
            'INSERT INTO torneo_posiciones (torneo_id, participante_id) VALUES (:torneo_id, :participante_id)'
        );
        foreach ($participantes->fetchAll(PDO::FETCH_COLUMN) as $participanteId) {
            $insertar->execute([':torneo_id' => $torneoId, ':participante_id' => $participanteId]);
        }

        $torneo = $this->bd->prepare(
            "SELECT t.puntos_victoria, t.puntos_empate, t.puntos_derrota,
                    s.puntos_victoria AS sistema_victoria, s.puntos_empate AS sistema_empate,
                    s.puntos_derrota AS sistema_derrota
             FROM torneos t LEFT JOIN sistemas_puntuacion s ON s.id = t.sistema_puntuacion_id
             WHERE t.id = :torneo_id"
        );
        $torneo->execute([':torneo_id' => $torneoId]);
        $reglas = $torneo->fetch(PDO::FETCH_ASSOC) ?: [];
        $victoria = $reglas['sistema_victoria'] ?? $reglas['puntos_victoria'] ?? 3;
        $empate = $reglas['sistema_empate'] ?? $reglas['puntos_empate'] ?? 1;
        $derrota = $reglas['sistema_derrota'] ?? $reglas['puntos_derrota'] ?? 0;

        $partidos = $this->bd->prepare(
            "SELECT participante_local_id, participante_visitante_id, resultado_local, resultado_visitante
             FROM torneo_encuentros WHERE torneo_id = :torneo_id AND estado = 'finalizado'"
        );
        $partidos->execute([':torneo_id' => $torneoId]);
        $tabla = [];
        foreach ($partidos->fetchAll(PDO::FETCH_ASSOC) as $partido) {
            foreach (['participante_local_id', 'participante_visitante_id'] as $lado) {
                $id = (int) $partido[$lado];
                $tabla[$id] ??= ['jugados' => 0, 'ganados' => 0, 'empatados' => 0, 'perdidos' => 0, 'favor' => 0, 'contra' => 0, 'puntos' => 0];
            }
            $localId = (int) $partido['participante_local_id'];
            $visitanteId = (int) $partido['participante_visitante_id'];
            $localResultado = (float) $partido['resultado_local'];
            $visitanteResultado = (float) $partido['resultado_visitante'];
            $tabla[$localId]['jugados']++;
            $tabla[$visitanteId]['jugados']++;
            $tabla[$localId]['favor'] += $localResultado;
            $tabla[$localId]['contra'] += $visitanteResultado;
            $tabla[$visitanteId]['favor'] += $visitanteResultado;
            $tabla[$visitanteId]['contra'] += $localResultado;
            if ($localResultado > $visitanteResultado) {
                $tabla[$localId]['ganados']++;
                $tabla[$localId]['puntos'] += $victoria;
                $tabla[$visitanteId]['perdidos']++;
                $tabla[$visitanteId]['puntos'] += $derrota;
            } elseif ($visitanteResultado > $localResultado) {
                $tabla[$visitanteId]['ganados']++;
                $tabla[$visitanteId]['puntos'] += $victoria;
                $tabla[$localId]['perdidos']++;
                $tabla[$localId]['puntos'] += $derrota;
            } else {
                $tabla[$localId]['empatados']++;
                $tabla[$visitanteId]['empatados']++;
                $tabla[$localId]['puntos'] += $empate;
                $tabla[$visitanteId]['puntos'] += $empate;
            }
        }
        $actualizar = $this->bd->prepare(
            "UPDATE torneo_posiciones SET partidos_jugados = :jugados, partidos_ganados = :ganados,
                partidos_empatados = :empatados, partidos_perdidos = :perdidos, puntos_favor = :favor,
                puntos_contra = :contra, puntos = :puntos
             WHERE torneo_id = :torneo_id AND participante_id = :participante_id"
        );
        foreach ($tabla as $participanteId => $fila) {
            $actualizar->execute([
                ':jugados' => $fila['jugados'], ':ganados' => $fila['ganados'], ':empatados' => $fila['empatados'],
                ':perdidos' => $fila['perdidos'], ':favor' => $fila['favor'], ':contra' => $fila['contra'],
                ':puntos' => $fila['puntos'], ':torneo_id' => $torneoId, ':participante_id' => $participanteId,
            ]);
        }
    }

    public function obtenerPosiciones(int $torneoId, int $organizadorId): array {
        $stmt = $this->bd->prepare(
            "SELECT p.nombre, pos.* FROM torneo_posiciones pos
             INNER JOIN participantes_torneo p ON p.id = pos.participante_id
             INNER JOIN torneos t ON t.id = pos.torneo_id
             WHERE pos.torneo_id = :torneo_id AND t.organizador_id = :organizador_id
             ORDER BY pos.puntos DESC, pos.diferencia_goles DESC, pos.puntos_favor DESC"
        );
        $stmt->execute([':torneo_id' => $torneoId, ':organizador_id' => $organizadorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarConfirmados(int $torneoId): int {
        $stmt = $this->bd->prepare(
            "SELECT COUNT(*) FROM participantes_torneo
             WHERE torneo_id = :torneo_id AND estado = 'confirmado'"
        );
        $stmt->execute([':torneo_id' => $torneoId]);
        return (int) $stmt->fetchColumn();
    }

    public function responderInscripcion(int $inscripcionId, int $organizadorId, string $respuesta): bool {
        if (!in_array($respuesta, ['confirmado', 'rechazado'], true)) {
            return false;
        }

        try {
            $this->bd->beginTransaction();
            $consulta = $this->bd->prepare(
                "SELECT p.id, p.torneo_id, p.estado, t.estado AS estado_torneo, t.cupo_max_equipos
                 FROM participantes_torneo p
                 INNER JOIN torneos t ON t.id = p.torneo_id
                 WHERE p.id = :id AND t.organizador_id = :organizador_id
                 FOR UPDATE"
            );
            $consulta->execute([':id' => $inscripcionId, ':organizador_id' => $organizadorId]);
            $inscripcion = $consulta->fetch(PDO::FETCH_ASSOC);
            if (!$inscripcion || $inscripcion['estado'] !== 'pendiente'
                || $inscripcion['estado_torneo'] !== 'inscripciones_abiertas') {
                $this->bd->rollBack();
                return false;
            }

            if ($respuesta === 'confirmado') {
                $conteo = $this->bd->prepare(
                    "SELECT COUNT(*) FROM participantes_torneo
                     WHERE torneo_id = :torneo_id AND estado = 'confirmado'"
                );
                $conteo->execute([':torneo_id' => $inscripcion['torneo_id']]);
                if ((int) $conteo->fetchColumn() >= (int) $inscripcion['cupo_max_equipos']) {
                    $this->bd->rollBack();
                    return false;
                }
            }

            $actualizar = $this->bd->prepare(
                "UPDATE participantes_torneo
                 SET estado = :estado, confirmado_por = :confirmado_por,
                     fecha_confirmacion = CURRENT_TIMESTAMP
                 WHERE id = :id AND estado = 'pendiente'"
            );
            $actualizar->execute([
                ':estado' => $respuesta,
                ':confirmado_por' => $organizadorId,
                ':id' => $inscripcionId,
            ]);
            $this->bd->commit();
            return true;
        } catch (\Throwable $error) {
            if ($this->bd->inTransaction()) {
                $this->bd->rollBack();
            }
            return false;
        }
    }
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