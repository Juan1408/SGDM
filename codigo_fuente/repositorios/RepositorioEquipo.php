<?php
require_once __DIR__ . '/../nucleo/ConexionBD.php';
require_once __DIR__ . '/../modelos/Equipo.php';
require_once __DIR__ . '/../modelos/SolicitudEquipo.php';

/**
 * Clase RepositorioEquipo
 * 
 * Gestiona el acceso a datos para Equipos, Miembros, Capitanes y Solicitudes de unión.
 */
class RepositorioEquipo {
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
     * Obtiene una lista de todos los equipos activos registrados en el sistema.
     * 
     * @return Equipo[] Listado de equipos activos.
     */
    public function obtenerTodos(): array {
        /**
         * @var string $sql Consulta SQL para listar equipos.
         */
        $sql = "SELECT id, nombre_equipo, escudo_url, descripcion, fecha_creacion, creado_por, activo, codigo_invitacion 
                FROM equipos 
                WHERE activo = 1 
                ORDER BY nombre_equipo ASC";

        $sentencia = $this->conexion->query($sql);
        /**
         * @var array $filas Datos recuperados de la base de datos.
         */
        $filas = $sentencia->fetchAll();
        /**
         * @var Equipo[] $resultados Colección de objetos Equipo.
         */
        $resultados = [];

        foreach ($filas as $fila) {
            $resultados[] = new Equipo(
                (int)$fila['id'],
                $fila['nombre_equipo'],
                $fila['escudo_url'],
                $fila['descripcion'],
                $fila['fecha_creacion'],
                (int)$fila['creado_por'],
                (bool)$fila['activo'],
                $fila['codigo_invitacion']
            );
        }

        return $resultados;
    }

    /**
     * Busca y obtiene un equipo específico por su identificador único.
     * 
     * @param int $id Identificador del equipo.
     * @return Equipo|null Retorna el objeto Equipo o null si no se encuentra.
     */
    public function obtenerPorId(int $id): ?Equipo {
        /**
         * @var string $sql Sentencia SQL de búsqueda por ID.
         */
        $sql = "SELECT id, nombre_equipo, escudo_url, descripcion, fecha_creacion, creado_por, activo, codigo_invitacion 
                FROM equipos 
                WHERE id = :id 
                LIMIT 1";

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute(['id' => $id]);
        
        /**
         * @var array|false $fila Registro recuperado.
         */
        $fila = $sentencia->fetch();

        if (!$fila) {
            return null;
        }

        return new Equipo(
            (int)$fila['id'],
            $fila['nombre_equipo'],
            $fila['escudo_url'],
            $fila['descripcion'],
            $fila['fecha_creacion'],
            (int)$fila['creado_por'],
            (bool)$fila['activo'],
            $fila['codigo_invitacion']
        );
    }

    /**
     * Busca un equipo mediante su código único de invitación alfanumérico.
     * 
     * @param string $codigo Código de invitación a buscar.
     * @return Equipo|null El objeto Equipo o null si no coincide el código.
     */
    public function obtenerPorCodigoInvitacion(string $codigo): ?Equipo {
        /**
         * @var string $sql Sentencia de búsqueda por código de invitación.
         */
        $sql = "SELECT id, nombre_equipo, escudo_url, descripcion, fecha_creacion, creado_por, activo, codigo_invitacion 
                FROM equipos 
                WHERE codigo_invitacion = :codigo 
                  AND activo = 1 
                LIMIT 1";

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute(['codigo' => $codigo]);
        
        /**
         * @var array|false $fila Fila obtenida.
         */
        $fila = $sentencia->fetch();

        if (!$fila) {
            return null;
        }

        return new Equipo(
            (int)$fila['id'],
            $fila['nombre_equipo'],
            $fila['escudo_url'],
            $fila['descripcion'],
            $fila['fecha_creacion'],
            (int)$fila['creado_por'],
            (bool)$fila['activo'],
            $fila['codigo_invitacion']
        );
    }

    /**
     * Crea o registra un nuevo equipo en el sistema.
     * 
     * @param Equipo $equipo Objeto conteniendo las propiedades del equipo.
     * @return bool Retorna verdadero si se guardó correctamente.
     */
    public function guardar(Equipo $equipo): bool {
        /**
         * @var string $sql Sentencia SQL de inserción de equipo.
         */
        $sql = "INSERT INTO equipos (nombre_equipo, escudo_url, descripcion, creado_por, activo, codigo_invitacion) 
                VALUES (:nombre_equipo, :escudo_url, :descripcion, :creado_por, :activo, :codigo_invitacion)";

        $sentencia = $this->conexion->prepare($sql);
        
        /**
         * @var bool $resultado Resultado del proceso de guardado.
         */
        $resultado = $sentencia->execute([
            'nombre_equipo' => $equipo->nombreEquipo,
            'escudo_url' => $equipo->escudoUrl,
            'descripcion' => $equipo->descripcion,
            'creado_por' => $equipo->creadoPor,
            'activo' => $equipo->activo ? 1 : 0,
            'codigo_invitacion' => $equipo->codigoInvitacion
        ]);

        if ($resultado) {
            $equipo->id = (int)$this->conexion->lastInsertId();
        }

        return $resultado;
    }

    /**
     * Agrega un usuario a la plantilla de un equipo.
     * 
     * @param int $equipoId ID del equipo.
     * @param int $usuarioId ID del usuario a añadir.
     * @param int|null $numeroCamiseta Dorsal asignado (opcional).
     * @param string|null $posicion Posición del jugador en cancha/campo (opcional).
     * @return bool Verdadero si se añadió con éxito.
     */
    public function agregarMiembro(int $equipoId, int $usuarioId, ?int $numeroCamiseta = null, ?string $posicion = null): bool {
        /**
         * @var string $sql Sentencia de inserción en equipo_miembros.
         */
        $sql = "INSERT INTO equipo_miembros (equipo_id, usuario_id, numero_camiseta, posicion, es_activo) 
                VALUES (:equipo_id, :usuario_id, :numero_camiseta, :posicion, 1)
                ON DUPLICATE KEY UPDATE numero_camiseta = VALUES(numero_camiseta), posicion = VALUES(posicion), es_activo = 1";
        
        $sentencia = $this->conexion->prepare($sql);
        return $sentencia->execute([
            'equipo_id' => $equipoId,
            'usuario_id' => $usuarioId,
            'numero_camiseta' => $numeroCamiseta,
            'posicion' => $posicion
        ]);
    }

    /**
     * Obtiene una lista detallada de miembros asignados a un equipo.
     * 
     * @param int $equipoId Identificador del equipo.
     * @return array[] Colección de arrays asociativos conteniendo datos de miembro e información del perfil de usuario.
     */
    public function obtenerMiembros(int $equipoId): array {
        /**
         * @var string $sql Consulta SQL con JOIN a usuarios.
         */
        $sql = "SELECT em.usuario_id, em.fecha_union, em.numero_camiseta, em.posicion, em.es_activo,
                       u.nombre_completo, u.email, u.foto_perfil_url 
                FROM equipo_miembros em 
                JOIN usuarios u ON em.usuario_id = u.id 
                WHERE em.equipo_id = :equipo_id AND em.es_activo = 1";

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute(['equipo_id' => $equipoId]);
        return $sentencia->fetchAll();
    }

    /**
     * Designa a un miembro del equipo con el rol de Capitán.
     * 
     * @param int $equipoId ID del equipo.
     * @param int $usuarioId ID del usuario capitán.
     * @param int $asignadoPor ID del capitán/organizador que realiza el nombramiento.
     * @param bool $esPrincipal Verdadero si es el capitán primario.
     * @return bool Estatus de éxito de la designación.
     */
    public function agregarCapitan(int $equipoId, int $usuarioId, int $asignadoPor, bool $esPrincipal = false): bool {
        /**
         * @var string $sql Inserción en tabla de capitanes.
         */
        $sql = "INSERT INTO equipo_capitanes (equipo_id, usuario_id, asignado_por, es_capitan_principal) 
                VALUES (:equipo_id, :usuario_id, :asignado_por, :es_capitan_principal)
                ON DUPLICATE KEY UPDATE es_capitan_principal = VALUES(es_capitan_principal)";
        
        $sentencia = $this->conexion->prepare($sql);
        return $sentencia->execute([
            'equipo_id' => $equipoId,
            'usuario_id' => $usuarioId,
            'asignado_por' => $asignadoPor,
            'es_capitan_principal' => $esPrincipal ? 1 : 0
        ]);
    }

    /**
     * Verifica si un usuario particular cuenta con privilegios de capitán para un equipo dado.
     * 
     * @param int $equipoId ID del equipo.
     * @param int $usuarioId ID del usuario.
     * @return bool Verdadero si está asignado como capitán, falso en caso contrario.
     */
    public function esCapitan(int $equipoId, int $usuarioId): bool {
        /**
         * @var string $sql Búsqueda de coincidencia en capitanes.
         */
        $sql = "SELECT COUNT(*) FROM equipo_capitanes WHERE equipo_id = :equipo_id AND usuario_id = :usuario_id";
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute([
            'equipo_id' => $equipoId,
            'usuario_id' => $usuarioId
        ]);
        return (int)$sentencia->fetchColumn() > 0;
    }

    /**
     * Genera una solicitud de unión de un usuario hacia un equipo específico.
     * 
     * @param int $equipoId ID del equipo destino.
     * @param int $usuarioId ID del usuario remitente.
     * @param string|null $mensaje Mensaje de presentación opcional.
     * @return bool Retorna verdadero si se insertó la solicitud.
     */
    public function crearSolicitud(int $equipoId, int $usuarioId, ?string $mensaje = null): bool {
        /**
         * @var string $sql Inserción de solicitud.
         */
        $sql = "INSERT INTO solicitudes_equipo (equipo_id, usuario_id, mensaje, estado) 
                VALUES (:equipo_id, :usuario_id, :mensaje, 'pendiente')
                ON DUPLICATE KEY UPDATE mensaje = VALUES(mensaje), estado = 'pendiente', fecha_solicitud = CURRENT_TIMESTAMP";

        $sentencia = $this->conexion->prepare($sql);
        return $sentencia->execute([
            'equipo_id' => $equipoId,
            'usuario_id' => $usuarioId,
            'mensaje' => $mensaje
        ]);
    }

    /**
     * Recupera una solicitud específica por su ID.
     * 
     * @param int $solicitudId ID de la solicitud.
     * @return array|null Datos de la solicitud o null.
     */
    public function obtenerSolicitudPorId(int $solicitudId): ?array {
        /**
         * @var string $sql Consulta SQL.
         */
        $sql = "SELECT id, equipo_id, usuario_id, mensaje, estado FROM solicitudes_equipo WHERE id = :id LIMIT 1";
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute(['id' => $solicitudId]);
        $resultado = $sentencia->fetch();
        return $resultado ? $resultado : null;
    }

    /**
     * Obtiene el listado de solicitudes con estado 'pendiente' para un equipo.
     * 
     * @param int $equipoId ID del equipo receptor.
     * @return array[] Colección de registros de solicitudes con detalles de perfil de usuario.
     */
    public function obtenerSolicitudesPendientes(int $equipoId): array {
        /**
         * @var string $sql Consulta SQL con JOIN a usuarios.
         */
        $sql = "SELECT se.id, se.usuario_id, se.mensaje, se.fecha_solicitud, u.nombre_completo, u.email 
                FROM solicitudes_equipo se 
                JOIN usuarios u ON se.usuario_id = u.id 
                WHERE se.equipo_id = :equipo_id AND se.estado = 'pendiente'";

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute(['equipo_id' => $equipoId]);
        return $sentencia->fetchAll();
    }

    /**
     * Procesa (aprueba o rechaza) una solicitud de admisión dada.
     * 
     * @param int $solicitudId ID de la solicitud.
     * @param string $estado Nuevo estado ('aprobado' o 'rechazado').
     * @param int $respondidoPor ID del capitán que resuelve la solicitud.
     * @return bool Retorna verdadero si se actualizó correctamente.
     */
    public function procesarSolicitud(int $solicitudId, string $estado, int $respondidoPor): bool {
        /**
         * @var string $sql Sentencia SQL de actualización de estado de solicitud.
         */
        $sql = "UPDATE solicitudes_equipo 
                SET estado = :estado, fecha_respuesta = CURRENT_TIMESTAMP, respondido_por = :respondido_por 
                WHERE id = :id";
        
        $sentencia = $this->conexion->prepare($sql);
        return $sentencia->execute([
            'estado' => $estado,
            'respondido_por' => $respondidoPor,
            'id' => $solicitudId
        ]);
    }
}
