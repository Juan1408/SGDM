<?php
require_once __DIR__ . '/../nucleo/ConexionBD.php';
require_once __DIR__ . '/../modelos/Enfrentamiento.php';

class RepositorioEnfrentamiento {
    private PDO $conexion;

    public function __construct() {
        $this->conexion = ConexionBD::obtenerInstancia();
    }

    public function obtenerPorTorneo(int $torneoId): array {
        $sql = 'SELECT id, torneo_id, ronda, participante_local_id, participante_visitante_id,
                       fecha_hora_programada, cancha, resultado_local, resultado_visitante,
                       estado, participante_ganador_id
                FROM torneo_encuentros
                WHERE torneo_id = :torneo_id
                ORDER BY ronda ASC, id ASC';

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute(['torneo_id' => $torneoId]);

        $resultados = [];
        foreach ($sentencia->fetchAll() as $fila) {
            $resultados[] = new Enfrentamiento(
                (int)$fila['id'],
                (int)$fila['torneo_id'],
                (int)$fila['ronda'],
                $fila['participante_local_id'] ? (int)$fila['participante_local_id'] : null,
                $fila['participante_visitante_id'] ? (int)$fila['participante_visitante_id'] : null,
                $fila['fecha_hora_programada'],
                $fila['cancha'],
                $fila['resultado_local'] !== null ? (float)$fila['resultado_local'] : null,
                $fila['resultado_visitante'] !== null ? (float)$fila['resultado_visitante'] : null,
                $fila['estado'] ?? 'programado',
                $fila['participante_ganador_id'] ? (int)$fila['participante_ganador_id'] : null
            );
        }

        return $resultados;
    }

    public function obtenerPorId(int $encuentroId): ?Enfrentamiento {
        $sql = 'SELECT id, torneo_id, ronda, participante_local_id, participante_visitante_id,
                       fecha_hora_programada, cancha, resultado_local, resultado_visitante,
                       estado, participante_ganador_id
                FROM torneo_encuentros
                WHERE id = :id';

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute(['id' => $encuentroId]);
        $fila = $sentencia->fetch();

        if (!$fila) {
            return null;
        }

        return new Enfrentamiento(
            (int)$fila['id'],
            (int)$fila['torneo_id'],
            (int)$fila['ronda'],
            $fila['participante_local_id'] ? (int)$fila['participante_local_id'] : null,
            $fila['participante_visitante_id'] ? (int)$fila['participante_visitante_id'] : null,
            $fila['fecha_hora_programada'],
            $fila['cancha'],
            $fila['resultado_local'] !== null ? (float)$fila['resultado_local'] : null,
            $fila['resultado_visitante'] !== null ? (float)$fila['resultado_visitante'] : null,
            $fila['estado'] ?? 'programado',
            $fila['participante_ganador_id'] ? (int)$fila['participante_ganador_id'] : null
        );
    }

    public function generarFixtureBasico(int $torneoId, array $participantes): int {
        if (count($participantes) < 2) {
            return 0;
        }

        $sql = 'INSERT INTO torneo_encuentros (
                    torneo_id, ronda, participante_local_id, participante_visitante_id,
                    fecha_hora_programada, cancha, estado, creado_en, ultima_modificacion
                ) VALUES (:torneo_id, :ronda, :local, :visitante, NOW(), :cancha, :estado, NOW(), NOW())';
        $sentencia = $this->conexion->prepare($sql);

        $insertados = 0;
        for ($indice = 0; $indice < count($participantes); $indice += 2) {
            if (!isset($participantes[$indice + 1])) {
                break;
            }

            $local = $participantes[$indice];
            $visitante = $participantes[$indice + 1];

            $resultado = $sentencia->execute([
                'torneo_id' => $torneoId,
                'ronda' => 1,
                'local' => $local->id,
                'visitante' => $visitante->id,
                'cancha' => 'Cancha principal',
                'estado' => 'programado'
            ]);

            if ($resultado) {
                $insertados++;
            }
        }

        return $insertados;
    }

    public function actualizarResultado(int $encuentroId, float $resultadoLocal, float $resultadoVisitante, ?int $ganadorId, string $estado = 'finalizado'): bool {
        $sql = 'UPDATE torneo_encuentros
                SET resultado_local = :resultado_local,
                    resultado_visitante = :resultado_visitante,
                    participante_ganador_id = :ganador_id,
                    estado = :estado,
                    ultima_modificacion = CURRENT_TIMESTAMP
                WHERE id = :id';

        $sentencia = $this->conexion->prepare($sql);
        return $sentencia->execute([
            'resultado_local' => $resultadoLocal,
            'resultado_visitante' => $resultadoVisitante,
            'ganador_id' => $ganadorId,
            'estado' => $estado,
            'id' => $encuentroId,
        ]);
    }
}

