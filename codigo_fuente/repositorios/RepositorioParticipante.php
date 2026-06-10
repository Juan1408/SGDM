<?php
require_once __DIR__ . '/../nucleo/ConexionBD.php';
require_once __DIR__ . '/../modelos/ParticipanteTorneo.php';

/**
 * Clase RepositorioParticipante
 * 
 * Gestiona el acceso a datos para las inscripciones de Participantes (individuales o equipos) en los Torneos.
 */
class RepositorioParticipante {
    /**
     * @var PDO $conexion Conexión activa a la base de datos MySQL.
     */
    private PDO $conexion;

    /**
     * Constructor del repositorio.
     */
    public function __construct() {
        $this->conexion = ConexionBD::obtenerInstancia();
    }

    /**
     * Verifica si un competidor (equipo o usuario) ya se encuentra inscrito en un torneo específico.
     * 
     * @param int $torneoId Identificador del torneo.
     * @param string $tipo Tipo de competidor ('equipo' o 'usuario').
     * @param int $referenciaId ID de referencia del equipo o usuario.
     * @return bool Retorna verdadero si ya cuenta con registro en participantes_torneo.
     */
    public function estaInscrito(int $torneoId, string $tipo, int $referenciaId): bool {
        /**
         * @var string $sql Búsqueda de inscripciones previas activas.
         */
        $sql = "SELECT COUNT(*) FROM participantes_torneo 
                WHERE torneo_id = :torneo_id 
                  AND tipo = :tipo 
                  AND referencia_id = :referencia_id 
                  AND estado != 'cancelado'";

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute([
            'torneo_id' => $torneoId,
            'tipo' => $tipo,
            'referencia_id' => $referenciaId
        ]);
        return (int)$sentencia->fetchColumn() > 0;
    }

    /**
     * Registra de manera formal una inscripción de competidor en la base de datos.
     * 
     * @param ParticipanteTorneo $participante Objeto con las propiedades de la inscripción.
     * @return bool Retorna verdadero si se guardó la inscripción.
     */
    public function inscribir(ParticipanteTorneo $participante): bool {
        /**
         * @var string $sql Inserción de participante en torneo.
         */
        $sql = "INSERT INTO participantes_torneo (torneo_id, tipo, referencia_id, nombre, estado) 
                VALUES (:torneo_id, :tipo, :referencia_id, :nombre, :estado)";

        $sentencia = $this->conexion->prepare($sql);
        
        /**
         * @var bool $resultado Bandera de resultado de la inserción.
         */
        $resultado = $sentencia->execute([
            'torneo_id' => $participante->torneoId,
            'tipo' => $participante->tipo,
            'referencia_id' => $participante->referenciaId,
            'nombre' => $participante->nombre,
            'estado' => $participante->estado
        ]);

        if ($resultado) {
            $participante->id = (int)$this->conexion->lastInsertId();
        }

        return $resultado;
    }

    /**
     * Obtiene el listado de participantes inscritos en un torneo específico.
     * 
     * @param int $torneoId ID del torneo.
     * @return ParticipanteTorneo[] Colección de objetos ParticipanteTorneo.
     */
    public function obtenerParticipantesPorTorneo(int $torneoId): array {
        /**
         * @var string $sql Consulta selectiva de participantes por torneo.
         */
        $sql = "SELECT id, torneo_id, tipo, referencia_id, nombre, estado, fecha_inscripcion, confirmado_por, fecha_confirmacion 
                FROM participantes_torneo 
                WHERE torneo_id = :torneo_id 
                ORDER BY fecha_inscripcion ASC";

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute(['torneo_id' => $torneoId]);
        
        /**
         * @var array $filas Datos recuperados de la BD.
         */
        $filas = $sentencia->fetchAll();
        /**
         * @var ParticipanteTorneo[] $resultados Array de retorno.
         */
        $resultados = [];

        foreach ($filas as $fila) {
            $resultados[] = new ParticipanteTorneo(
                (int)$fila['id'],
                (int)$fila['torneo_id'],
                $fila['tipo'],
                (int)$fila['referencia_id'],
                $fila['nombre'],
                $fila['estado'],
                $fila['fecha_inscripcion'],
                $fila['confirmado_por'] ? (int)$fila['confirmado_por'] : null,
                $fila['fecha_confirmacion']
            );
        }

        return $resultados;
    }

    /**
     * Actualiza el estado de aprobación de una inscripción en particular.
     * 
     * @param int $participanteId ID del participante inscrito.
     * @param string $estado Nuevo estado ('confirmado', 'rechazado', 'cancelado').
     * @param int|null $confirmadoPor ID del organizador que realiza la validación (opcional).
     * @return bool Verdadero si la operación se aplicó correctamente.
     */
    public function actualizarEstado(int $participanteId, string $estado, ?int $confirmadoPor = null): bool {
        /**
         * @var string $sql Sentencia de actualización de estado e info de confirmación.
         */
        $sql = "UPDATE participantes_torneo 
                SET estado = :estado, 
                    confirmado_por = :confirmado_por, 
                    fecha_confirmacion = CASE WHEN :estado = 'confirmado' THEN CURRENT_TIMESTAMP ELSE NULL END 
                WHERE id = :id";
        
        $sentencia = $this->conexion->prepare($sql);
        return $sentencia->execute([
            'estado' => $estado,
            'confirmado_por' => $confirmadoPor,
            'id' => $participanteId
        ]);
    }
}
