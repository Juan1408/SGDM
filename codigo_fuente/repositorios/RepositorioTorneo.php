<?php
require_once __DIR__ . '/../nucleo/ConexionBD.php';
require_once __DIR__ . '/../modelos/Torneo.php';
require_once __DIR__ . '/../modelos/TorneoCambioEstado.php';

/**
 * Clase RepositorioTorneo
 *
 * Gestiona todas las operaciones de base de datos relacionadas con Torneos:
 * CRUD, historial de estados y configuraciones avanzadas (clave-valor).
 */
class RepositorioTorneo {
    /** @var PDO $conexion Conexión activa PDO a MySQL. */
    private PDO $conexion;

    public function __construct() {
        $this->conexion = ConexionBD::obtenerInstancia();
    }

    // -----------------------------------------------------------------------
    // CRUD PRINCIPAL
    // -----------------------------------------------------------------------

    /**
     * Guarda un nuevo torneo en la base de datos.
     *
     * @param Torneo $torneo Objeto con los datos del torneo a persistir.
     * @return bool Verdadero si la inserción fue exitosa.
     */
    public function guardar(Torneo $torneo): bool {
        /** @var string $sql Sentencia SQL de inserción. */
        $sql = "INSERT INTO torneos 
                    (nombre, descripcion, formato, estado, cupo_max_equipos, cupo_min_equipos,
                     fecha_inicio, fecha_fin, fecha_limite_inscripcion, reglas, premios, ubicacion,
                     organizador_id, modalidad_id, sistema_puntuacion_id,
                     puntos_victoria, puntos_empate, puntos_derrota,
                     tipo_resultado, mejor_de)
                VALUES
                    (:nombre, :descripcion, :formato, :estado, :cupo_max, :cupo_min,
                     :fecha_inicio, :fecha_fin, :fecha_limite, :reglas, :premios, :ubicacion,
                     :organizador_id, :modalidad_id, :sistema_puntuacion_id,
                     :puntos_victoria, :puntos_empate, :puntos_derrota,
                     :tipo_resultado, :mejor_de)";

        $sentencia = $this->conexion->prepare($sql);
        $resultado = $sentencia->execute([
            'nombre'               => $torneo->nombre,
            'descripcion'          => $torneo->descripcion,
            'formato'              => $torneo->formato,
            'estado'               => $torneo->estado,
            'cupo_max'             => $torneo->cupoMaxEquipos,
            'cupo_min'             => $torneo->cupoMinEquipos,
            'fecha_inicio'         => $torneo->fechaInicio,
            'fecha_fin'            => $torneo->fechaFin,
            'fecha_limite'         => $torneo->fechaLimiteInscripcion,
            'reglas'               => $torneo->reglas,
            'premios'              => $torneo->premios,
            'ubicacion'            => $torneo->ubicacion,
            'organizador_id'       => $torneo->organizadorId,
            'modalidad_id'         => $torneo->modalidadId,
            'sistema_puntuacion_id'=> $torneo->sistemaPuntuacionId,
            'puntos_victoria'      => $torneo->puntosVictoria,
            'puntos_empate'        => $torneo->puntosEmpate,
            'puntos_derrota'       => $torneo->puntosDerrota,
            'tipo_resultado'       => $torneo->tipoResultado,
            'mejor_de'             => $torneo->mejorDe,
        ]);

        if ($resultado) {
            $torneo->id = (int)$this->conexion->lastInsertId();
        }
        return $resultado;
    }

    /**
     * Obtiene un torneo por su identificador único.
     *
     * @param int $id ID del torneo.
     * @return Torneo|null Objeto Torneo o null si no se encuentra.
     */
    public function obtenerPorId(int $id): ?Torneo {
        /** @var string $sql Consulta de búsqueda por ID. */
        $sql = "SELECT * FROM torneos WHERE id = :id LIMIT 1";
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute(['id' => $id]);

        /** @var array|false $fila Registro recuperado. */
        $fila = $sentencia->fetch();
        return $fila ? $this->mapearFila($fila) : null;
    }

    /**
     * Obtiene todos los torneos, ordenados por fecha de creación descendente.
     *
     * @return Torneo[] Colección de objetos Torneo.
     */
    public function obtenerTodos(): array {
        /** @var string $sql Consulta de listado completo. */
        $sql = "SELECT * FROM torneos ORDER BY creado_en DESC";
        $sentencia = $this->conexion->query($sql);

        /** @var Torneo[] $resultados Array de retorno. */
        $resultados = [];
        foreach ($sentencia->fetchAll() as $fila) {
            $resultados[] = $this->mapearFila($fila);
        }
        return $resultados;
    }

    /**
     * Obtiene todos los torneos creados por un organizador específico.
     *
     * @param int $organizadorId ID del usuario organizador.
     * @return Torneo[] Lista de torneos del organizador.
     */
    public function obtenerPorOrganizador(int $organizadorId): array {
        /** @var string $sql Filtro por organizador. */
        $sql = "SELECT * FROM torneos WHERE organizador_id = :org_id ORDER BY creado_en DESC";
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute(['org_id' => $organizadorId]);

        $resultados = [];
        foreach ($sentencia->fetchAll() as $fila) {
            $resultados[] = $this->mapearFila($fila);
        }
        return $resultados;
    }

    // -----------------------------------------------------------------------
    // GESTIÓN DE ESTADO
    // -----------------------------------------------------------------------

    /**
     * Actualiza el estado de un torneo y registra el cambio en el historial.
     *
     * @param int $torneoId ID del torneo.
     * @param string $estadoActual Estado actual del torneo (para el historial).
     * @param string $estadoNuevo Nuevo estado a aplicar.
     * @param int $usuarioId ID del usuario que realiza el cambio.
     * @param string|null $motivo Justificación del cambio (opcional).
     * @return bool Verdadero si ambas operaciones tuvieron éxito.
     */
    public function cambiarEstado(int $torneoId, string $estadoActual, string $estadoNuevo, int $usuarioId, ?string $motivo = null): bool {
        // Actualizar estado en la tabla principal
        /** @var string $sqlActualizar Actualización del estado. */
        $sqlActualizar = "UPDATE torneos SET estado = :estado, actualizado_en = CURRENT_TIMESTAMP WHERE id = :id";
        $sentencia = $this->conexion->prepare($sqlActualizar);
        $actualizado = $sentencia->execute(['estado' => $estadoNuevo, 'id' => $torneoId]);

        if (!$actualizado) {
            return false;
        }

        // Registrar en el historial
        /** @var string $sqlHistorial Inserción en historial de cambios. */
        $sqlHistorial = "INSERT INTO torneo_cambio_estado (torneo_id, estado_anterior, estado_nuevo, motivo, usuario_id)
                         VALUES (:torneo_id, :estado_anterior, :estado_nuevo, :motivo, :usuario_id)";
        $sentenciaHistorial = $this->conexion->prepare($sqlHistorial);
        return $sentenciaHistorial->execute([
            'torneo_id'      => $torneoId,
            'estado_anterior'=> $estadoActual,
            'estado_nuevo'   => $estadoNuevo,
            'motivo'         => $motivo,
            'usuario_id'     => $usuarioId,
        ]);
    }

    /**
     * Obtiene el historial completo de cambios de estado de un torneo.
     *
     * @param int $torneoId ID del torneo.
     * @return array[] Colección de registros de historial con nombre de usuario.
     */
    public function obtenerHistorialEstados(int $torneoId): array {
        /** @var string $sql Consulta con JOIN a usuarios para obtener nombre del modificador. */
        $sql = "SELECT tce.estado_anterior, tce.estado_nuevo, tce.motivo, tce.fecha_cambio, u.nombre_completo
                FROM torneo_cambio_estado tce
                JOIN usuarios u ON tce.usuario_id = u.id
                WHERE tce.torneo_id = :torneo_id
                ORDER BY tce.fecha_cambio ASC";
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute(['torneo_id' => $torneoId]);
        return $sentencia->fetchAll();
    }

    // -----------------------------------------------------------------------
    // CONFIGURACIÓN CLAVE-VALOR
    // -----------------------------------------------------------------------

    /**
     * Guarda o actualiza una configuración avanzada del torneo en la tabla torneo_config.
     *
     * @param int $torneoId ID del torneo.
     * @param string $clave Nombre de la propiedad (ej: 'desempate_suizo', 'mapas_permitidos').
     * @param string $valor Valor de la propiedad.
     * @return bool Verdadero si se aplicó correctamente.
     */
    public function guardarConfiguracion(int $torneoId, string $clave, string $valor): bool {
        /** @var string $sql INSERT ... ON DUPLICATE KEY UPDATE para upsert seguro. */
        $sql = "INSERT INTO torneo_config (torneo_id, clave, valor)
                VALUES (:torneo_id, :clave, :valor)
                ON DUPLICATE KEY UPDATE valor = VALUES(valor)";
        $sentencia = $this->conexion->prepare($sql);
        return $sentencia->execute([
            'torneo_id' => $torneoId,
            'clave'     => $clave,
            'valor'     => $valor,
        ]);
    }

    /**
     * Obtiene todas las configuraciones avanzadas de un torneo.
     *
     * @param int $torneoId ID del torneo.
     * @return array<string,string> Array asociativo clave => valor.
     */
    public function obtenerConfiguraciones(int $torneoId): array {
        /** @var string $sql Consulta de configuraciones por torneo. */
        $sql = "SELECT clave, valor FROM torneo_config WHERE torneo_id = :torneo_id";
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute(['torneo_id' => $torneoId]);

        /** @var array<string,string> $configuraciones Mapa clave-valor. */
        $configuraciones = [];
        foreach ($sentencia->fetchAll() as $fila) {
            $configuraciones[$fila['clave']] = $fila['valor'];
        }
        return $configuraciones;
    }

    // -----------------------------------------------------------------------
    // MÉTODO PRIVADO DE MAPEO
    // -----------------------------------------------------------------------

    /**
     * Convierte una fila de base de datos en un objeto Torneo.
     *
     * @param array $fila Array asociativo con los campos del registro.
     * @return Torneo Objeto Torneo construido desde la fila.
     */
    private function mapearFila(array $fila): Torneo {
        return new Torneo(
            (int)$fila['id'],
            $fila['nombre'],
            $fila['descripcion'],
            $fila['formato'],
            $fila['estado'],
            (int)$fila['cupo_max_equipos'],
            (int)$fila['cupo_min_equipos'],
            $fila['fecha_inicio'],
            $fila['fecha_fin'],
            $fila['fecha_limite_inscripcion'],
            $fila['reglas'],
            $fila['premios'],
            $fila['ubicacion'],
            (int)$fila['organizador_id'],
            $fila['creado_en'],
            $fila['actualizado_en'],
            (int)$fila['modalidad_id'],
            $fila['sistema_puntuacion_id'] ? (int)$fila['sistema_puntuacion_id'] : null,
            (float)$fila['puntos_victoria'],
            (float)$fila['puntos_empate'],
            (float)$fila['puntos_derrota'],
            $fila['tipo_resultado'],
            (int)$fila['mejor_de']
        );
    }
}
