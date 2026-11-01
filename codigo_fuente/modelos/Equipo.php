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
    //Limite de codigos invalidos por jugador antes de bloquear temporalmente el ingreso
    private const MAX_INTENTOS_CODIGO = 5;
    private const VENTANA_INTENTOS_CODIGO_MIN = 10;

    private $bd;

    private function __construct(){
        //Usamos el patron Singleton para obtener la instancia única de la base de datos
        $this->bd = Conexion::getInstance()->getBD();
    }

    public static function obtenerInstancia(){
        return new self();
    }
    
      //Metodo para buscar un equipo por su nombre
    public function buscarPorNombre(string $nombre){
        //Escribimos la consulta SQL usando un marcador por seguridad (:nombre)
        $sql = "SELECT * FROM equipos WHERE nombre_equipo = :nombre AND activo = 1 LIMIT 1";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':nombre' => $nombre]);
        $equipo = $stmt->fetch(PDO::FETCH_ASSOC);
        return $equipo;
    }

     //Metodo para crear un equipo (columnas reales de la tabla 'equipos')
    public function crearEquipo(string $nombreEquipo, int $creadoPor, ?string $descripcion = null, ?string $ubicacion = null, ?int $anioFundacion = null, ?string $escudoUrl = null, ?string $bannerUrl = null, ?string $codigoInvitacion = null, ?int $juegoId = null){
        //Preparamos la consulta SQL con las columnas que realmente existen en 'equipos'
        $sql = "INSERT INTO equipos (nombre_equipo, creado_por, juego_id, descripcion, ubicacion, anio_fundacion, escudo_url, banner_url, codigo_invitacion)
                VALUES (:nombreEquipo, :creadoPor, :juegoId, :descripcion, :ubicacion, :anioFundacion, :escudoUrl, :bannerUrl, :codigoInvitacion)";
        //Preparamos la consulta para evitar inyección SQL
        $stmt = $this->bd->prepare($sql);
        //Ejecutamos la consulta pasandole los datos
        $stmt->execute([
            ':nombreEquipo' => $nombreEquipo,
            ':creadoPor' => $creadoPor,
            ':juegoId' => $juegoId,
            ':descripcion' => $descripcion,
            ':ubicacion' => $ubicacion,
            ':anioFundacion' => $anioFundacion,
            ':escudoUrl' => $escudoUrl,
            ':bannerUrl' => $bannerUrl,
            ':codigoInvitacion' => $codigoInvitacion
        ]);
        //Devuelve la ID del equipo recien creado
        return $this->bd->lastInsertId();
    }

        //Metodo para generar un codigo de invitacion unico (para que otros jugadores se sumen al equipo)
    private function generarCodigoInvitacion(): string {
        do {
            //Generamos un codigo random de 8 caracteres (letras mayusculas + numeros)
            $codigo = strtoupper(substr(bin2hex(random_bytes(5)), 0, 8));

            //Verificamos que no exista ya otro equipo con ese mismo codigo
            $sql = "SELECT id FROM equipos WHERE codigo_invitacion = :codigo LIMIT 1";
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([':codigo' => $codigo]);
            $existe = $stmt->fetch(PDO::FETCH_ASSOC);
        } while ($existe);

        return $codigo;
    }

    //Metodo para crear un equipo completo: el equipo + el creador como miembro + el creador como capitan principal
    //Usamos una transaccion porque son 3 inserciones relacionadas: si una falla, no queremos que las otras queden a medias
    public function crearEquipoCompleto(string $nombreEquipo, int $usuarioId, ?string $descripcion = null, ?string $ubicacion = null, ?int $anioFundacion = null, ?int $juegoId = null, ?string $escudoUrl = null, ?string $bannerUrl = null): array {
        $transaccionPropia = false;
        try {
            if (!$this->bd->inTransaction()) {
                $this->bd->beginTransaction();
                $transaccionPropia = true;
            }

            $codigoInvitacion = $this->generarCodigoInvitacion();

            //1. Creamos el equipo (escudo y banner opcionales: se suben junto con el formulario)
            $equipoId = $this->crearEquipo($nombreEquipo, $usuarioId, $descripcion, $ubicacion, $anioFundacion, $escudoUrl, $bannerUrl, $codigoInvitacion, $juegoId);
            //2. El creador queda como miembro del equipo
            $this->agregarMiembro((int) $equipoId, $usuarioId);

            //3. El creador queda como capitan principal (asignado_por: se asigna a si mismo, no hay nadie mas todavia)
            $sqlCapitan = "INSERT INTO equipo_capitanes (equipo_id, usuario_id, asignado_por, es_capitan_principal) VALUES (:equipoId, :usuarioIdCapitan, :usuarioIdAsigna, 1)";
            $stmtCapitan = $this->bd->prepare($sqlCapitan);
            $stmtCapitan->execute([
                ':equipoId' => $equipoId,
                ':usuarioIdCapitan' => $usuarioId,
                ':usuarioIdAsigna' => $usuarioId
            ]);

            if ($transaccionPropia && $this->bd->inTransaction()) {
                $this->bd->commit();
            }

            return ['exito' => true, 'equipoId' => $equipoId];
        } catch (\Exception $e) {
            //Si algo fallo, deshacemos todo (no queremos un equipo sin capitan, o un capitan sin equipo)
            if ($transaccionPropia && $this->bd->inTransaction()) {
                $this->bd->rollBack();
            }
            return ['exito' => false, 'error' => $e->getMessage()];
        }
    }

    //Metodo interno para sumar (o reactivar) a un usuario como miembro activo del equipo.
    //Usamos ON DUPLICATE KEY UPDATE porque la PK es (equipo_id, usuario_id): si el jugador
    //ya habia estado en el equipo (expulsado o se fue), hay que reactivar su fila en lugar de fallar.
    private function agregarMiembro(int $equipoId, int $usuarioId): void {
        $sql = "INSERT INTO equipo_miembros (equipo_id, usuario_id, es_activo, fecha_union)
                VALUES (:equipoId, :usuarioId, 1, CURRENT_TIMESTAMP)
                ON DUPLICATE KEY UPDATE es_activo = 1, fecha_union = CURRENT_TIMESTAMP";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':equipoId' => $equipoId, ':usuarioId' => $usuarioId]);
    }

        //Metodo para eliminar un equipo (Soft Delete)
    //Blindaje Fase B: Bloquear eliminacion si tiene inscripciones o torneos activos
    public function eliminarEquipo(int $id): bool {
        if ($this->tieneTorneosActivos($id)) {
            return false;
        }
        //Desactivamos el equipo en lugar de borrarlo completamente
        $sql = "UPDATE equipos SET activo = 0 WHERE id = :id";
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

      public function obtenerEquiposDelJugador(int $usuarioId): array {
        $sql = "SELECT
                    e.id,
                    e.nombre_equipo,
                    e.juego_id,
                    e.escudo_url,
                    e.descripcion,
                    EXISTS (
                        SELECT 1 FROM equipo_capitanes ec
                        WHERE ec.equipo_id = e.id AND ec.usuario_id = :usuarioIdCapitan
                    ) AS es_capitan
                FROM equipos e
                INNER JOIN equipo_miembros em ON em.equipo_id = e.id
                WHERE em.usuario_id = :usuarioIdMiembro
                AND em.es_activo = 1
                AND e.activo = 1
                ORDER BY e.nombre_equipo ASC";

        $stmt = $this->bd->prepare($sql);
        $stmt->execute([
            ':usuarioIdCapitan' => $usuarioId,
            ':usuarioIdMiembro' => $usuarioId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Metodo para traer los datos completos de un equipo por su ID
    public function obtenerPorId(int $id) {
        $sql = "SELECT e.*, j.nombre AS nombre_juego, j.imagen_url AS juego_icono
                FROM equipos e
                LEFT JOIN juegos j ON j.id = e.juego_id
                WHERE e.id = :id AND e.activo = 1 LIMIT 1";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':id' => $id]);
        $equipo = $stmt->fetch(PDO::FETCH_ASSOC);
        return $equipo ?: false;
    }

    //Metodo para traer los capitanes/lideres de un equipo (con nombre y foto)
    public function obtenerCapitanes(int $equipoId): array {
        $sql = "SELECT u.id, u.nombre_completo, u.foto_perfil_url, ec.es_capitan_principal
                FROM equipo_capitanes ec
                INNER JOIN usuarios u ON u.id = ec.usuario_id
                WHERE ec.equipo_id = :equipoId
                ORDER BY ec.es_capitan_principal DESC";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':equipoId' => $equipoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Metodo para traer los miembros activos de un equipo (con nombre y foto)
    public function obtenerMiembros(int $equipoId): array {
        $sql = "SELECT u.id, u.nombre_completo, u.foto_perfil_url, em.posicion, em.numero_camiseta
                FROM equipo_miembros em
                INNER JOIN usuarios u ON u.id = em.usuario_id
                WHERE em.equipo_id = :equipoId AND em.es_activo = 1
                ORDER BY u.nombre_completo ASC";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':equipoId' => $equipoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Metodo para saber si un usuario ya es miembro activo de un equipo puntual
    public function esMiembro(int $equipoId, int $usuarioId): bool {
        $sql = "SELECT 1 FROM equipo_miembros WHERE equipo_id = :equipoId AND usuario_id = :usuarioId AND es_activo = 1 LIMIT 1";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':equipoId' => $equipoId, ':usuarioId' => $usuarioId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

        //Metodo para saber si ya existe una solicitud pendiente de un usuario hacia un equipo
    public function tieneSolicitudPendiente(int $equipoId, int $usuarioId): bool {
        $sql = "SELECT 1 FROM solicitudes_equipo WHERE equipo_id = :equipoId AND usuario_id = :usuarioId AND estado = 'pendiente' LIMIT 1";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':equipoId' => $equipoId, ':usuarioId' => $usuarioId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    //Metodo para crear una solicitud de union a un equipo
    //$iniciadoPor: quien la origino (el propio jugador pidiendo entrar, o el capitan invitando)
    public function crearSolicitud(int $equipoId, int $usuarioId, int $iniciadoPor, ?string $mensaje = null): bool {
        //ON DUPLICATE KEY UPDATE permite volver a postularse despues de un rechazo:
        //la PK logica es (equipo_id, usuario_id), asi que reutilizamos esa fila y la dejamos pendiente otra vez.
        $sql = "INSERT INTO solicitudes_equipo (equipo_id, usuario_id, iniciado_por, mensaje, estado)
                VALUES (:equipoId, :usuarioId, :iniciadoPor, :mensaje, 'pendiente')
                ON DUPLICATE KEY UPDATE
                    iniciado_por = VALUES(iniciado_por),
                    mensaje = VALUES(mensaje),
                    estado = 'pendiente',
                    fecha_solicitud = CURRENT_TIMESTAMP,
                    fecha_respuesta = NULL,
                    respondido_por = NULL";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([
            ':equipoId' => $equipoId,
            ':usuarioId' => $usuarioId,
            ':iniciadoPor' => $iniciadoPor,
            ':mensaje' => $mensaje
        ]);
    }

        //Metodo para traer las invitaciones que un jugador recibio de capitanes (no sus propias solicitudes de "Solicitar Unirse")
    public function obtenerInvitacionesRecibidas(int $usuarioId): array {
        $sql = "SELECT s.id, s.mensaje, s.fecha_solicitud,
                    e.id AS equipo_id, e.nombre_equipo, e.escudo_url,
                    u.nombre_completo AS invitado_por_nombre
                FROM solicitudes_equipo s
                INNER JOIN equipos e ON e.id = s.equipo_id
                INNER JOIN usuarios u ON u.id = s.iniciado_por
                WHERE s.usuario_id = :usuarioId
                AND s.estado = 'pendiente'
                AND s.iniciado_por != s.usuario_id
                ORDER BY s.fecha_solicitud DESC";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':usuarioId' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Metodo para aceptar o rechazar una invitacion recibida
    //Si se acepta, ademas de cambiar el estado, se suma al jugador como miembro real del equipo
    public function responderInvitacion(int $solicitudId, int $usuarioId, bool $aceptar): array {
        $transaccionPropia = false;
        try {
            if (!$this->bd->inTransaction()) {
                $this->bd->beginTransaction();
                $transaccionPropia = true;
            }

            //Verificamos que la solicitud sea de este usuario y siga pendiente (evita que alguien responda invitaciones ajenas)
            $sqlVerificar = "SELECT equipo_id FROM solicitudes_equipo WHERE id = :id AND usuario_id = :usuarioId AND estado = 'pendiente' LIMIT 1 FOR UPDATE";
            $stmtVerificar = $this->bd->prepare($sqlVerificar);
            $stmtVerificar->execute([':id' => $solicitudId, ':usuarioId' => $usuarioId]);
            $solicitud = $stmtVerificar->fetch(PDO::FETCH_ASSOC);

            if (!$solicitud) {
                if ($transaccionPropia && $this->bd->inTransaction()) {
                    $this->bd->rollBack();
                }
                return ['exito' => false, 'error' => 'La solicitud no existe o ya fue respondida.'];
            }

            $nuevoEstado = $aceptar ? 'aprobado' : 'rechazado';

            $sqlActualizar = "UPDATE solicitudes_equipo
                    SET estado = :estado, fecha_respuesta = NOW(), respondido_por = :respondidoPor
                    WHERE id = :id";
            $stmtActualizar = $this->bd->prepare($sqlActualizar);
            $stmtActualizar->execute([
                ':estado' => $nuevoEstado,
                ':respondidoPor' => $usuarioId,
                ':id' => $solicitudId
            ]);

            //Si acepto, lo sumamos de verdad como miembro del equipo (reactivandolo si ya habia estado)
            if ($aceptar) {
                $this->agregarMiembro((int) $solicitud['equipo_id'], $usuarioId);
            }

            if ($transaccionPropia && $this->bd->inTransaction()) {
                $this->bd->commit();
            }
            return ['exito' => true];
        } catch (\Exception $e) {
            if ($transaccionPropia && $this->bd->inTransaction()) {
                $this->bd->rollBack();
            }
            return ['exito' => false, 'error' => $e->getMessage()];
        }
    }

    //Metodo para buscar jugadores para invitar a un equipo puntual
    //Busca por nombre o apodo_gamertag (no por email, por privacidad).
    //Excluye a quienes ya son miembros del equipo o ya tienen una solicitud pendiente hacia ese equipo.
        public function buscarJugadoresParaInvitar(string $busqueda, int $equipoId): array {
        $sql = "SELECT u.id, u.nombre_completo, u.email, u.foto_perfil_url, p.apodo_gamertag
                FROM usuarios u
                LEFT JOIN perfiles_jugadores p ON p.usuario_id = u.id
                WHERE u.rol_id = 3
                AND u.esta_activo = 1
                AND (u.nombre_completo LIKE :busquedaNombre OR p.apodo_gamertag LIKE :busquedaApodo)
                AND u.id NOT IN (
                    SELECT usuario_id FROM equipo_miembros WHERE equipo_id = :equipoId1 AND es_activo = 1
                )
                AND u.id NOT IN (
                    SELECT usuario_id FROM solicitudes_equipo WHERE equipo_id = :equipoId2 AND estado = 'pendiente'
                )
                LIMIT 20";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([
            ':busquedaNombre' => '%' . $busqueda . '%',
            ':busquedaApodo' => '%' . $busqueda . '%',
            ':equipoId1' => $equipoId,
            ':equipoId2' => $equipoId
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

        //Metodo para saber si un usuario es capitan de un equipo puntual
    public function esCapitan(int $equipoId, int $usuarioId): bool {
        $sql = "SELECT 1 FROM equipo_capitanes WHERE equipo_id = :equipoId AND usuario_id = :usuarioId LIMIT 1";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':equipoId' => $equipoId, ':usuarioId' => $usuarioId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    //Metodo para saber si ya existe OTRO equipo con ese nombre (para validar al editar, sin chocar con el propio)
    public function equipoNombreEnUso(string $nombre, int $excluirEquipoId): bool {
        $sql = "SELECT 1 FROM equipos WHERE nombre_equipo = :nombre AND id != :excluirEquipoId AND activo = 1 LIMIT 1";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':nombre' => $nombre, ':excluirEquipoId' => $excluirEquipoId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    /**
     * Verifica si el equipo se encuentra participando activamente en torneos en curso.
     * Retorna true si tiene participantes en estado 'confirmado' en torneos 'en_curso'.
     */
    public function tieneTorneosEnCurso(int $equipoId): bool {
        $sql = "SELECT COUNT(*) FROM participantes_torneo pt
                INNER JOIN torneos t ON t.id = pt.torneo_id
                WHERE pt.tipo = 'equipo'
                  AND pt.referencia_id = :equipo_id
                  AND pt.estado = 'confirmado'
                  AND t.estado = 'en_curso'";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':equipo_id' => $equipoId]);
        return ((int) $stmt->fetchColumn()) > 0;
    }

    /**
     * Verifica si el equipo tiene torneos activos (en_curso o inscripciones_abiertas confirmadas/pendientes)
     */
    public function tieneTorneosActivos(int $equipoId): bool {
        $sql = "SELECT COUNT(*) FROM participantes_torneo pt
                INNER JOIN torneos t ON t.id = pt.torneo_id
                WHERE pt.tipo = 'equipo'
                  AND pt.referencia_id = :equipo_id
                  AND pt.estado IN ('confirmado', 'pendiente')
                  AND t.estado IN ('en_curso', 'inscripciones_abiertas')";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':equipo_id' => $equipoId]);
        return ((int) $stmt->fetchColumn()) > 0;
    }

    /**
     * Permite cambiar la disciplina deportiva del equipo si no tiene torneos activos y la nueva disciplina admite equipos
     */
    public function cambiarDisciplina(int $equipoId, int $nuevoJuegoId): array {
        if ($this->tieneTorneosActivos($equipoId)) {
            return [
                'exito' => false,
                'error' => 'No podés cambiar la disciplina deportiva mientras el equipo tenga inscripciones activas o torneos en curso.'
            ];
        }

        $sqlCheck = "SELECT COUNT(*) FROM juego_modalidades_permitidas WHERE juego_id = :juego_id AND modalidad_id = 2";
        $stmtCheck = $this->bd->prepare($sqlCheck);
        $stmtCheck->execute([':juego_id' => $nuevoJuegoId]);
        if ((int) $stmtCheck->fetchColumn() === 0) {
            return [
                'exito' => false,
                'error' => 'La disciplina seleccionada no admite modalidad por equipos.'
            ];
        }

        $sql = "UPDATE equipos SET juego_id = :juego_id WHERE id = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':juego_id' => $nuevoJuegoId, ':id' => $equipoId]);
        return ['exito' => true];
    }

    //Metodo para actualizar los datos editables de un equipo (no toca creado_por, codigo_invitacion, ni activo)
    public function actualizarDatos(int $equipoId, string $nombreEquipo, ?string $ubicacion, ?int $anioFundacion, ?string $descripcion): bool {
        $sql = "UPDATE equipos
                SET nombre_equipo = :nombreEquipo, ubicacion = :ubicacion, anio_fundacion = :anioFundacion, descripcion = :descripcion
                WHERE id = :id";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([
            ':nombreEquipo' => $nombreEquipo,
            ':ubicacion' => $ubicacion,
            ':anioFundacion' => $anioFundacion,
            ':descripcion' => $descripcion,
            ':id' => $equipoId
        ]);
    }

    //Metodo para expulsar a un miembro del equipo (soft delete, igual que se hace con equipos)
    //Blindaje Fase B: No se puede expulsar miembros mientras el equipo esté disputando un torneo en curso
    public function expulsarMiembro(int $equipoId, int $usuarioId): bool {
        if ($this->tieneTorneosEnCurso($equipoId)) {
            return false;
        }

        $sql = "UPDATE equipo_miembros SET es_activo = 0 WHERE equipo_id = :equipoId AND usuario_id = :usuarioId";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':equipoId' => $equipoId, ':usuarioId' => $usuarioId]);
        return $stmt->rowCount() > 0;
    }
    
        //Metodo para traer las solicitudes que jugadores enviaron para entrar a MI equipo
    //(no las invitaciones que yo mande como capitan, esas van por otro lado)
    public function obtenerSolicitudesPendientesDelEquipo(int $equipoId): array {
        $sql = "SELECT s.id, s.mensaje, s.fecha_solicitud,
                    u.id AS usuario_id, u.nombre_completo, u.foto_perfil_url
                FROM solicitudes_equipo s
                INNER JOIN usuarios u ON u.id = s.usuario_id
                WHERE s.equipo_id = :equipoId
                AND s.estado = 'pendiente'
                AND s.iniciado_por = s.usuario_id
                ORDER BY s.fecha_solicitud ASC";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':equipoId' => $equipoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Metodo para que el capitan acepte o rechace una solicitud que un jugador mando a su equipo
    public function responderSolicitudRecibida(int $solicitudId, int $equipoId, int $respondidoPor, bool $aceptar): array {
        $transaccionPropia = false;
        try {
            if (!$this->bd->inTransaction()) {
                $this->bd->beginTransaction();
                $transaccionPropia = true;
            }

            //Solo un capitan del equipo puede aceptar/rechazar solicitudes (defensa en profundidad,
            //aunque el controlador ya lo valida)
            $sqlCapitan = "SELECT 1 FROM equipo_capitanes WHERE equipo_id = :equipoId AND usuario_id = :respondidoPor LIMIT 1";
            $stmtCapitan = $this->bd->prepare($sqlCapitan);
            $stmtCapitan->execute([':equipoId' => $equipoId, ':respondidoPor' => $respondidoPor]);
            if (!$stmtCapitan->fetch(PDO::FETCH_ASSOC)) {
                if ($transaccionPropia && $this->bd->inTransaction()) {
                    $this->bd->rollBack();
                }
                return ['exito' => false, 'error' => 'Solo un capitán del equipo puede responder esta solicitud.'];
            }

            //Verificamos que la solicitud sea de ESTE equipo y siga pendiente
            $sqlVerificar = "SELECT usuario_id FROM solicitudes_equipo WHERE id = :id AND equipo_id = :equipoId AND estado = 'pendiente' LIMIT 1 FOR UPDATE";
            $stmtVerificar = $this->bd->prepare($sqlVerificar);
            $stmtVerificar->execute([':id' => $solicitudId, ':equipoId' => $equipoId]);
            $solicitud = $stmtVerificar->fetch(PDO::FETCH_ASSOC);

            if (!$solicitud) {
                if ($transaccionPropia && $this->bd->inTransaction()) {
                    $this->bd->rollBack();
                }
                return ['exito' => false, 'error' => 'La solicitud no existe o ya fue respondida.'];
            }

            $nuevoEstado = $aceptar ? 'aprobado' : 'rechazado';

            $sqlActualizar = "UPDATE solicitudes_equipo
                    SET estado = :estado, fecha_respuesta = NOW(), respondido_por = :respondidoPor
                    WHERE id = :id";
            $stmtActualizar = $this->bd->prepare($sqlActualizar);
            $stmtActualizar->execute([
                ':estado' => $nuevoEstado,
                ':respondidoPor' => $respondidoPor,
                ':id' => $solicitudId
            ]);

            //Si se acepto, sumamos al jugador que pidio entrar como miembro real (reactivandolo si ya habia estado)
            if ($aceptar) {
                $this->agregarMiembro($equipoId, (int) $solicitud['usuario_id']);
            }

            if ($transaccionPropia && $this->bd->inTransaction()) {
                $this->bd->commit();
            }
            return ['exito' => true];
        } catch (\Exception $e) {
            if ($transaccionPropia && $this->bd->inTransaction()) {
                $this->bd->rollBack();
            }
            return ['exito' => false, 'error' => $e->getMessage()];
        }
    }

        //Metodo para designar a un miembro como capitan adicional (sub-capitan)
    public function hacerCapitan(int $equipoId, int $usuarioId, int $asignadoPor): bool {
        //Solo se puede nombrar capitan a alguien que sea miembro activo del equipo
        if (!$this->esMiembro($equipoId, $usuarioId)) {
            return false;
        }

        //Si ya es capitan no hacemos nada (evita el error de clave duplicada)
        if ($this->esCapitan($equipoId, $usuarioId)) {
            return false;
        }

        $sql = "INSERT INTO equipo_capitanes (equipo_id, usuario_id, asignado_por, es_capitan_principal)
                VALUES (:equipoId, :usuarioId, :asignadoPor, 0)";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([
            ':equipoId' => $equipoId,
            ':usuarioId' => $usuarioId,
            ':asignadoPor' => $asignadoPor
        ]);
    }

    //Metodo para que un capitan deje el equipo
    //Si hay otro capitan ya designado, ese pasa a ser el principal automaticamente.
    //Si es el unico capitan, no se le permite irse (tiene que asignar uno primero).
    //Blindaje Fase B: No se puede abandonar el equipo mientras esté disputando un torneo en curso.
    public function dejarEquipo(int $equipoId, int $usuarioId): array {
        if ($this->tieneTorneosEnCurso($equipoId)) {
            return [
                'exito' => false,
                'error' => 'No podés abandonar el equipo mientras esté disputando un torneo en curso.'
            ];
        }

        $transaccionPropia = false;
        try {
            if (!$this->bd->inTransaction()) {
                $this->bd->beginTransaction();
                $transaccionPropia = true;
            }

            //Buscamos si hay OTRO capitan en este equipo (distinto al que se quiere ir)
            $sqlOtroCapitan = "SELECT usuario_id FROM equipo_capitanes WHERE equipo_id = :equipoId AND usuario_id != :usuarioId LIMIT 1 FOR UPDATE";
            $stmtOtroCapitan = $this->bd->prepare($sqlOtroCapitan);
            $stmtOtroCapitan->execute([':equipoId' => $equipoId, ':usuarioId' => $usuarioId]);
            $otroCapitan = $stmtOtroCapitan->fetch(PDO::FETCH_ASSOC);

            if (!$otroCapitan) {
                if ($transaccionPropia && $this->bd->inTransaction()) {
                    $this->bd->rollBack();
                }
                return ['exito' => false, 'error' => 'Sos el único capitán del equipo. Asigná otro capitán antes de irte.'];
            }

            //Ese otro capitan pasa a ser el principal
            $sqlNuevoPrincipal = "UPDATE equipo_capitanes SET es_capitan_principal = 1 WHERE equipo_id = :equipoId AND usuario_id = :nuevoCapitanId";
            $stmtNuevoPrincipal = $this->bd->prepare($sqlNuevoPrincipal);
            $stmtNuevoPrincipal->execute([':equipoId' => $equipoId, ':nuevoCapitanId' => $otroCapitan['usuario_id']]);

            //Sacamos al que se va de la lista de capitanes
            $sqlSacarCapitan = "DELETE FROM equipo_capitanes WHERE equipo_id = :equipoId AND usuario_id = :usuarioId";
            $stmtSacarCapitan = $this->bd->prepare($sqlSacarCapitan);
            $stmtSacarCapitan->execute([':equipoId' => $equipoId, ':usuarioId' => $usuarioId]);

            //Y lo desactivamos como miembro (deja el equipo del todo, no solo la capitania)
            $sqlSacarMiembro = "UPDATE equipo_miembros SET es_activo = 0 WHERE equipo_id = :equipoId AND usuario_id = :usuarioId";
            $stmtSacarMiembro = $this->bd->prepare($sqlSacarMiembro);
            $stmtSacarMiembro->execute([':equipoId' => $equipoId, ':usuarioId' => $usuarioId]);

            if ($transaccionPropia && $this->bd->inTransaction()) {
                $this->bd->commit();
            }
            return ['exito' => true];
        } catch (\Exception $e) {
            if ($transaccionPropia && $this->bd->inTransaction()) {
                $this->bd->rollBack();
            }
            return ['exito' => false, 'error' => $e->getMessage()];
        }
    }

    // ============================================================================
    // INGRESO A UN EQUIPO MEDIANTE EL CODIGO DE INVITACION
    // ============================================================================

    /**
     * Incorpora a un jugador al equipo dueño del codigo de invitacion.
     *
     * El codigo es un secreto compartido por los referentes del equipo: quien lo
     * conoce puede ingresar sin pasar por la bandeja de solicitudes. Se valida el
     * formato, el rol del usuario, el estado del equipo y el bloqueo por intentos
     * fallidos. Si el jugador tenia una solicitud o invitacion pendiente, se cierra
     * como aprobada para no dejar una fila huerfana en la bandeja del capitan.
     *
     * @return array{exito:bool,error?:string,equipo_id?:int,nombre_equipo?:string,ya_era_miembro?:bool}
     */
    public function unirsePorCodigo(string $codigo, int $usuarioId): array {
        //Normalizacion: el codigo se guarda en mayusculas y sin espacios externos
        $codigo = strtoupper(trim($codigo));

        //Formato esperado: 8 caracteres hexadecimales generados por crearEquipoCompleto
        if (!preg_match('/^[A-F0-9]{8}$/', $codigo)) {
            return ['exito' => false, 'error' => 'codigo_invalido'];
        }

        if ($this->estaBloqueadoPorIntentosDeCodigo($usuarioId)) {
            return ['exito' => false, 'error' => 'demasiados_intentos'];
        }

        //Defensa en profundidad: solo un jugador activo puede integrarse a un equipo
        $stmtUsuario = $this->bd->prepare(
            "SELECT rol_id, esta_activo FROM usuarios WHERE id = :id LIMIT 1"
        );
        $stmtUsuario->execute([':id' => $usuarioId]);
        $usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

        if (!$usuario || (int) $usuario['rol_id'] !== 3 || (int) $usuario['esta_activo'] !== 1) {
            $this->registrarIntentoDeCodigo($usuarioId, 'UNION_CODIGO_FALLIDA', 'La cuenta no puede ingresar a un equipo.');
            return ['exito' => false, 'error' => 'no_jugador'];
        }

        //Buscamos el equipo por su codigo; solo equipos activos
        $stmtEquipo = $this->bd->prepare(
            "SELECT id, nombre_equipo FROM equipos WHERE codigo_invitacion = :codigo AND activo = 1 LIMIT 1"
        );
        $stmtEquipo->execute([':codigo' => $codigo]);
        $equipo = $stmtEquipo->fetch(PDO::FETCH_ASSOC);

        if (!$equipo) {
            //Mensaje generico: no distinguimos entre codigo inexistente y equipo dado de baja
            $this->registrarIntentoDeCodigo($usuarioId, 'UNION_CODIGO_FALLIDA', 'Codigo de invitacion invalido.');
            return ['exito' => false, 'error' => 'codigo_invalido'];
        }

        $equipoId = (int) $equipo['id'];

        //Si ya pertenece al equipo no se duplica la fila ni se altera la plantilla
        if ($this->esMiembro($equipoId, $usuarioId)) {
            return [
                'exito' => true,
                'equipo_id' => $equipoId,
                'nombre_equipo' => $equipo['nombre_equipo'],
                'ya_era_miembro' => true,
            ];
        }

        $transaccionPropia = false;
        try {
            if (!$this->bd->inTransaction()) {
                $this->bd->beginTransaction();
                $transaccionPropia = true;
            }

            //Alta (o reactivacion) del jugador como miembro activo
            $this->agregarMiembro($equipoId, $usuarioId);

            //Si habia una solicitud o invitacion pendiente, queda resuelta por este ingreso
            $stmtSolicitud = $this->bd->prepare(
                "UPDATE solicitudes_equipo
                 SET estado = 'aprobado', fecha_respuesta = NOW(), respondido_por = :respondidoPor
                 WHERE equipo_id = :equipoId AND usuario_id = :usuarioId AND estado = 'pendiente'"
            );
            $stmtSolicitud->execute([
                ':respondidoPor' => $usuarioId,
                ':equipoId' => $equipoId,
                ':usuarioId' => $usuarioId,
            ]);

            if ($transaccionPropia && $this->bd->inTransaction()) {
                $this->bd->commit();
            }
        } catch (\Exception $e) {
            if ($transaccionPropia && $this->bd->inTransaction()) {
                $this->bd->rollBack();
            }
            error_log('Error al unir por codigo al usuario ' . $usuarioId . ': ' . $e->getMessage());
            $this->registrarIntentoDeCodigo($usuarioId, 'UNION_CODIGO_FALLIDA', 'Error interno al ingresar al equipo.');
            return ['exito' => false, 'error' => 'error'];
        }

        $this->registrarIntentoDeCodigo(
            $usuarioId,
            'UNION_CODIGO_EXITOSA',
            'Ingreso al equipo "' . $equipo['nombre_equipo'] . '" (ID ' . $equipoId . ') con codigo de invitacion.'
        );

        return [
            'exito' => true,
            'equipo_id' => $equipoId,
            'nombre_equipo' => $equipo['nombre_equipo'],
            'ya_era_miembro' => false,
        ];
    }

    /**
     * Cuenta los intentos fallidos recientes del jugador y bloquea el ingreso
     * cuando supera el maximo. Se apoya en logs_actividad, que ya existe en el
     * esquema: no requiere columnas ni tablas nuevas.
     */
    private function estaBloqueadoPorIntentosDeCodigo(int $usuarioId): bool {
        $sql = "SELECT COUNT(*) FROM logs_actividad
                WHERE usuario_id = :usuarioId
                  AND accion = 'UNION_CODIGO_FALLIDA'
                  AND fecha_hora >= (NOW() - INTERVAL " . (int) self::VENTANA_INTENTOS_CODIGO_MIN . " MINUTE)";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':usuarioId' => $usuarioId]);
        return ((int) $stmt->fetchColumn()) >= self::MAX_INTENTOS_CODIGO;
    }

    //Registra en la auditoria el resultado de un intento de ingreso por codigo.
    //No guardamos el codigo ingresado: es un secreto del equipo.
    private function registrarIntentoDeCodigo(int $usuarioId, string $accion, string $descripcion): void {
        try {
            $sql = "INSERT INTO logs_actividad (usuario_id, accion, descripcion, ip_origen, user_agent)
                    VALUES (:usuarioId, :accion, :descripcion, :ip, :userAgent)";
            $stmt = $this->bd->prepare($sql);
            $stmt->execute([
                ':usuarioId' => $usuarioId,
                ':accion' => $accion,
                ':descripcion' => $descripcion,
                ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                ':userAgent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            ]);
        } catch (\Exception $e) {
            //La auditoria no debe impedir la operacion principal
            error_log('No se pudo registrar el intento de codigo: ' . $e->getMessage());
        }
    }

    // ============================================================================
    // METODOS DEL ADMINISTRADOR GENERAL
    // ============================================================================

    public function obtenerDirectorioAdmin(?int $juegoId, string $orden): array {
        $sql = "SELECT e.id, e.nombre_equipo, e.fecha_creacion, e.activo, e.escudo_url,
                       u.nombre_completo AS lider_nombre,
                       (SELECT COUNT(*) FROM equipo_miembros em WHERE em.equipo_id = e.id AND em.es_activo = 1) AS total_miembros
                FROM equipos e
                INNER JOIN usuarios u ON u.id = e.creado_por
                WHERE 1=1";

        $parametros = [];

        // Filtro por disciplina directo desde la tabla equipos
        if ($juegoId) {
            $sql .= " AND e.juego_id = :juegoId";
            $parametros[':juegoId'] = $juegoId;
        }

        // Ordenamiento dinámico
        switch ($orden) {
            case 'antiguos':
                $sql .= " ORDER BY e.fecha_creacion ASC";
                break;
            case 'az':
                $sql .= " ORDER BY e.nombre_equipo ASC";
                break;
            case 'za':
                $sql .= " ORDER BY e.nombre_equipo DESC";
                break;
            case 'nuevos':
            default:
                $sql .= " ORDER BY e.fecha_creacion DESC";
                break;
        }

        $stmt = $this->bd->prepare($sql);
        $stmt->execute($parametros);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Torneos en los que participa el equipo (cualquier estado de inscripcion), para la pestana "Torneos"
    public function obtenerTorneosDelEquipo(int $equipoId): array {
        $sql = "SELECT pt.id AS participacion_id, pt.nombre AS nombre_participacion,
                       pt.estado AS estado_inscripcion, pt.fecha_inscripcion,
                       t.id AS torneo_id, t.nombre AS torneo_nombre, t.formato,
                       t.estado AS torneo_estado, t.fecha_inicio, t.fecha_fin, t.banner_url
                FROM participantes_torneo pt
                INNER JOIN torneos t ON t.id = pt.torneo_id
                WHERE pt.tipo = 'equipo' AND pt.referencia_id = :equipoId
                ORDER BY t.fecha_inicio IS NULL, t.fecha_inicio DESC, t.id DESC";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':equipoId' => $equipoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Posicion del equipo en cada torneo con tabla de posiciones, usando el mismo orden
    // que Torneo::obtenerPosicionesPublicas(): puntos, diferencia, favor y nombre.
    public function obtenerPosicionesDelEquipo(int $equipoId): array {
        $sql = "SELECT pt.id AS participacion_id, pt.nombre AS nombre_participacion,
                       t.id AS torneo_id, t.nombre AS torneo_nombre, t.formato,
                       t.estado AS torneo_estado,
                       COALESCE(pos.puntos, 0) AS puntos,
                       COALESCE(pos.partidos_jugados, 0) AS partidos_jugados,
                       COALESCE(pos.partidos_ganados, 0) AS partidos_ganados,
                       COALESCE(pos.partidos_empatados, 0) AS partidos_empatados,
                       COALESCE(pos.partidos_perdidos, 0) AS partidos_perdidos,
                       COALESCE(pos.puntos_favor, 0) AS puntos_favor,
                       COALESCE(pos.puntos_contra, 0) AS puntos_contra,
                       COALESCE(pos.diferencia_goles, 0) AS diferencia_goles,
                       (
                           SELECT COUNT(*) + 1
                           FROM participantes_torneo rival
                           LEFT JOIN torneo_posiciones rpos
                               ON rpos.torneo_id = rival.torneo_id AND rpos.participante_id = rival.id
                           WHERE rival.torneo_id = pt.torneo_id
                             AND rival.estado = 'confirmado'
                             AND (
                                 COALESCE(rpos.puntos, 0) > COALESCE(pos.puntos, 0)
                                 OR (COALESCE(rpos.puntos, 0) = COALESCE(pos.puntos, 0)
                                     AND COALESCE(rpos.diferencia_goles, 0) > COALESCE(pos.diferencia_goles, 0))
                                 OR (COALESCE(rpos.puntos, 0) = COALESCE(pos.puntos, 0)
                                     AND COALESCE(rpos.diferencia_goles, 0) = COALESCE(pos.diferencia_goles, 0)
                                     AND COALESCE(rpos.puntos_favor, 0) > COALESCE(pos.puntos_favor, 0))
                                 OR (COALESCE(rpos.puntos, 0) = COALESCE(pos.puntos, 0)
                                     AND COALESCE(rpos.diferencia_goles, 0) = COALESCE(pos.diferencia_goles, 0)
                                     AND COALESCE(rpos.puntos_favor, 0) = COALESCE(pos.puntos_favor, 0)
                                     AND rival.nombre < pt.nombre)
                             )
                       ) AS posicion
                FROM participantes_torneo pt
                INNER JOIN torneos t ON t.id = pt.torneo_id
                INNER JOIN torneo_posiciones pos
                    ON pos.torneo_id = pt.torneo_id AND pos.participante_id = pt.id
                WHERE pt.tipo = 'equipo' AND pt.referencia_id = :equipoId
                  AND pt.estado = 'confirmado'
                ORDER BY t.fecha_inicio IS NULL, t.fecha_inicio DESC, t.id DESC";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':equipoId' => $equipoId]);

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as &$fila) {
            $fila['posicion'] = (int) ($fila['posicion'] ?? 0);
        }
        unset($fila);

        return $filas;
    }

    // Calcula estadísticas reales del equipo en torneos: partidas jugadas, victorias, empates y torneos ganados
    public function obtenerEstadisticasTorneos(int $equipoId): array {
        $stmtPt = $this->bd->prepare(
            "SELECT pt.id, pt.torneo_id, pt.nombre, t.estado AS torneo_estado, t.formato AS torneo_formato
             FROM participantes_torneo pt
             INNER JOIN torneos t ON t.id = pt.torneo_id
             WHERE pt.tipo = 'equipo' AND pt.referencia_id = :equipo_id"
        );
        $stmtPt->execute([':equipo_id' => $equipoId]);
        $participaciones = $stmtPt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($participaciones)) {
            return [
                'partidas' => 0,
                'victorias' => 0,
                'empates' => 0,
                'derrotas' => 0,
                'torneos_ganados' => 0,
            ];
        }

        $ptIds = array_column($participaciones, 'id');
        $ptIdsTexto = implode(',', array_map('intval', $ptIds));

        $sql = "SELECT
                    COUNT(*) AS partidas,
                    SUM(CASE WHEN participante_ganador_id IN ($ptIdsTexto) THEN 1 ELSE 0 END) AS victorias,
                    SUM(CASE WHEN participante_ganador_id IS NULL THEN 1 ELSE 0 END) AS empates,
                    SUM(CASE WHEN participante_ganador_id IS NOT NULL AND participante_ganador_id NOT IN ($ptIdsTexto) THEN 1 ELSE 0 END) AS derrotas
                FROM torneo_encuentros
                WHERE estado = 'finalizado'
                  AND (participante_local_id IN ($ptIdsTexto) OR participante_visitante_id IN ($ptIdsTexto))";
        $stmtEnc = $this->bd->prepare($sql);
        $stmtEnc->execute();
        $encStats = $stmtEnc->fetch(PDO::FETCH_ASSOC) ?: [];

        $torneosGanados = 0;
        foreach ($participaciones as $part) {
            $torneoId = (int) $part['torneo_id'];
            $ptId = (int) $part['id'];
            $nombreEquipo = $part['nombre'];

            // 1. Podio configurado
            $stmtPodio = $this->bd->prepare("SELECT valor FROM torneo_config WHERE torneo_id = :t_id AND clave = 'podio'");
            $stmtPodio->execute([':t_id' => $torneoId]);
            $valPodio = $stmtPodio->fetchColumn();
            if ($valPodio) {
                $podio = json_decode($valPodio, true);
                if (isset($podio['primero']) && $podio['primero'] === $nombreEquipo) {
                    $torneosGanados++;
                    continue;
                }
            }

            // 2. Si el torneo esta finalizado pero no tiene podio en json_config:
            if ($part['torneo_estado'] === 'finalizado') {
                if (in_array($part['torneo_formato'], ['liga', 'suizo'], true)) {
                    $stmtPos = $this->bd->prepare(
                        "SELECT participante_id FROM torneo_posiciones
                         WHERE torneo_id = :t_id
                         ORDER BY puntos DESC, diferencia_goles DESC, puntos_favor DESC LIMIT 1"
                    );
                    $stmtPos->execute([':t_id' => $torneoId]);
                    if ((int) $stmtPos->fetchColumn() === $ptId) {
                        $torneosGanados++;
                    }
                } else {
                    $stmtFinal = $this->bd->prepare(
                        "SELECT participante_ganador_id FROM torneo_encuentros
                         WHERE torneo_id = :t_id AND estado = 'finalizado'
                         ORDER BY ronda DESC, id DESC LIMIT 1"
                    );
                    $stmtFinal->execute([':t_id' => $torneoId]);
                    if ((int) $stmtFinal->fetchColumn() === $ptId) {
                        $torneosGanados++;
                    }
                }
            }
        }

        return [
            'partidas' => (int) ($encStats['partidas'] ?? 0),
            'victorias' => (int) ($encStats['victorias'] ?? 0),
            'empates' => (int) ($encStats['empates'] ?? 0),
            'derrotas' => (int) ($encStats['derrotas'] ?? 0),
            'torneos_ganados' => $torneosGanados,
        ];
    }

    // ============================================================================
    // FICHA DE EQUIPO PARA EL ADMINISTRADOR (solo lectura + renombre)
    // ============================================================================

    /**
     * Reúne todos los datos de la ficha administrativa de un equipo.
     * No filtra por activo: el administrador debe poder auditar equipos dados de baja.
     *
     * @return array|false
     */
    public function obtenerFichaAdmin(int $equipoId) {
        if ($equipoId <= 0) {
            return false;
        }

        $stmt = $this->bd->prepare(
            "SELECT e.*, j.nombre AS nombre_juego, j.imagen_url AS juego_icono,
                    u.nombre_completo AS creador_nombre, u.email AS creador_email
             FROM equipos e
             LEFT JOIN juegos j ON j.id = e.juego_id
             INNER JOIN usuarios u ON u.id = e.creado_por
             WHERE e.id = :id
             LIMIT 1"
        );
        $stmt->execute([':id' => $equipoId]);
        $equipo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$equipo) {
            return false;
        }

        $participaciones = $this->obtenerParticipacionesAdmin($equipoId);
        $partidos = $this->obtenerPartidosAdmin($equipoId, 15);

        $stmtSolicitudes = $this->bd->prepare(
            "SELECT COUNT(*) FROM solicitudes_equipo WHERE equipo_id = :id AND estado = 'pendiente'"
        );
        $stmtSolicitudes->execute([':id' => $equipoId]);
        $solicitudesPendientes = (int) $stmtSolicitudes->fetchColumn();

        return [
            'equipo' => $equipo,
            'capitanes' => $this->obtenerCapitanes($equipoId),
            'miembros' => $this->obtenerMiembros($equipoId),
            'participaciones' => $participaciones,
            'partidos' => $partidos,
            'resumen' => $this->obtenerEstadisticasTorneos($equipoId),
            'solicitudes_pendientes' => $solicitudesPendientes,
        ];
    }

    /**
     * Participaciones del equipo con su posición y estadísticas por torneo.
     */
    public function obtenerParticipacionesAdmin(int $equipoId): array {
        $sql = "SELECT pt.id, pt.torneo_id, pt.nombre AS nombre_participante,
                       pt.estado AS estado_inscripcion, pt.fecha_inscripcion,
                       t.nombre AS torneo_nombre, t.estado AS torneo_estado, t.formato,
                       t.fecha_inicio, t.fecha_fin,
                       pos.partidos_jugados, pos.partidos_ganados, pos.partidos_empatados,
                       pos.partidos_perdidos, pos.puntos, pos.puntos_favor, pos.puntos_contra,
                       pos.diferencia_goles,
                       CASE WHEN pos.participante_id IS NULL THEN NULL ELSE (
                           SELECT COUNT(*) + 1
                           FROM torneo_posiciones p2
                           WHERE p2.torneo_id = pt.torneo_id
                             AND (
                                 p2.puntos > pos.puntos
                                 OR (p2.puntos = pos.puntos AND p2.diferencia_goles > pos.diferencia_goles)
                                 OR (p2.puntos = pos.puntos AND p2.diferencia_goles = pos.diferencia_goles
                                     AND p2.puntos_favor > pos.puntos_favor)
                             )
                       ) END AS posicion
                FROM participantes_torneo pt
                INNER JOIN torneos t ON t.id = pt.torneo_id
                LEFT JOIN torneo_posiciones pos
                       ON pos.torneo_id = pt.torneo_id AND pos.participante_id = pt.id
                WHERE pt.tipo = 'equipo' AND pt.referencia_id = :equipo_id
                ORDER BY t.fecha_inicio DESC, t.id DESC";

        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':equipo_id' => $equipoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Últimos partidos del equipo con rival, condición y resultado (V/E/D/P).
     */
    public function obtenerPartidosAdmin(int $equipoId, int $limite = 15): array {
        $sql = "SELECT enc.id, enc.torneo_id, t.nombre AS torneo_nombre, enc.ronda,
                       enc.fecha_hora_programada, enc.estado,
                       enc.resultado_local, enc.resultado_visitante,
                       IF(enc.participante_local_id = pt.id, 'Local', 'Visitante') AS condicion,
                       COALESCE(rival.nombre, 'Sin rival') AS rival_nombre,
                       CASE
                           WHEN enc.estado = 'finalizado' AND enc.participante_ganador_id = pt.id THEN 'V'
                           WHEN enc.estado = 'finalizado' AND enc.participante_ganador_id IS NULL THEN 'E'
                           WHEN enc.estado = 'finalizado' THEN 'D'
                           ELSE 'P'
                       END AS resultado
                FROM torneo_encuentros enc
                INNER JOIN torneos t ON t.id = enc.torneo_id
                INNER JOIN participantes_torneo pt
                        ON pt.torneo_id = enc.torneo_id
                       AND pt.tipo = 'equipo'
                       AND pt.referencia_id = :equipo_id
                       AND (enc.participante_local_id = pt.id OR enc.participante_visitante_id = pt.id)
                LEFT JOIN participantes_torneo rival
                       ON rival.id = IF(enc.participante_local_id = pt.id,
                                        enc.participante_visitante_id,
                                        enc.participante_local_id)
                ORDER BY COALESCE(enc.fecha_hora_programada, enc.creado_en) DESC, enc.id DESC
                LIMIT :limite";

        $stmt = $this->bd->prepare($sql);
        $stmt->bindValue(':equipo_id', $equipoId, PDO::PARAM_INT);
        $stmt->bindValue(':limite', max(1, $limite), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Renombra un equipo y sincroniza las copias denormalizadas del nombre:
     * participantes_torneo.nombre y los podios guardados como JSON en torneo_config.
     *
     * Todo ocurre en una única transacción. Si algo falla, no se cambia nada.
     *
     * @return array{exito:bool,error?:string,anterior?:string,nuevo?:string}
     */
    public function renombrarEquipoAdmin(int $equipoId, string $nuevoNombre): array {
        if ($equipoId <= 0) {
            return ['exito' => false, 'error' => 'El equipo indicado no es válido.'];
        }

        $nuevoNombre = trim(preg_replace('/\s+/u', ' ', $nuevoNombre));
        if ($nuevoNombre === '' || mb_strlen($nuevoNombre, 'UTF-8') < 2) {
            return ['exito' => false, 'error' => 'El nombre del equipo debe tener al menos 2 caracteres.'];
        }
        if (mb_strlen($nuevoNombre, 'UTF-8') > 100) {
            return ['exito' => false, 'error' => 'El nombre del equipo no puede superar los 100 caracteres.'];
        }

        try {
            $this->bd->beginTransaction();

            $stmt = $this->bd->prepare("SELECT nombre_equipo FROM equipos WHERE id = :id FOR UPDATE");
            $stmt->execute([':id' => $equipoId]);
            $actual = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$actual) {
                $this->bd->rollBack();
                return ['exito' => false, 'error' => 'El equipo no existe.'];
            }

            $nombreAnterior = $actual['nombre_equipo'];
            if ($nombreAnterior === $nuevoNombre) {
                $this->bd->commit();
                return ['exito' => true, 'anterior' => $nombreAnterior, 'nuevo' => $nuevoNombre];
            }

            $stmt = $this->bd->prepare(
                "SELECT id FROM equipos WHERE nombre_equipo = :nombre AND id <> :id LIMIT 1"
            );
            $stmt->execute([':nombre' => $nuevoNombre, ':id' => $equipoId]);
            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->bd->rollBack();
                return ['exito' => false, 'error' => 'Ya existe otro equipo con ese nombre.'];
            }

            $stmt = $this->bd->prepare("UPDATE equipos SET nombre_equipo = :nombre WHERE id = :id");
            $stmt->execute([':nombre' => $nuevoNombre, ':id' => $equipoId]);

            $stmt = $this->bd->prepare(
                "UPDATE participantes_torneo
                 SET nombre = :nombre
                 WHERE tipo = 'equipo' AND referencia_id = :id"
            );
            $stmt->execute([':nombre' => $nuevoNombre, ':id' => $equipoId]);

            // Sincronizamos los podios (JSON) de los torneos donde participa el equipo.
            $stmt = $this->bd->prepare(
                "SELECT DISTINCT torneo_id FROM participantes_torneo
                 WHERE tipo = 'equipo' AND referencia_id = :id"
            );
            $stmt->execute([':id' => $equipoId]);
            $torneoIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $stmtPodio = $this->bd->prepare(
                "SELECT valor FROM torneo_config
                 WHERE torneo_id = :torneo_id AND clave = 'podio' FOR UPDATE"
            );
            $stmtGuardar = $this->bd->prepare(
                "UPDATE torneo_config SET valor = :valor
                 WHERE torneo_id = :torneo_id AND clave = 'podio'"
            );

            foreach ($torneoIds as $torneoId) {
                $stmtPodio->execute([':torneo_id' => (int) $torneoId]);
                $fila = $stmtPodio->fetch(PDO::FETCH_ASSOC);
                if (!$fila || empty($fila['valor'])) {
                    continue;
                }

                $podio = json_decode($fila['valor'], true);
                if (!is_array($podio)) {
                    continue;
                }

                $cambio = false;
                foreach (['primero', 'segundo', 'tercero'] as $puesto) {
                    if (isset($podio[$puesto]) && $podio[$puesto] === $nombreAnterior) {
                        $podio[$puesto] = $nuevoNombre;
                        $cambio = true;
                    }
                }

                if ($cambio) {
                    $stmtGuardar->execute([
                        ':valor' => json_encode($podio, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        ':torneo_id' => (int) $torneoId,
                    ]);
                }
            }

            $this->bd->commit();

            return ['exito' => true, 'anterior' => $nombreAnterior, 'nuevo' => $nuevoNombre];
        } catch (\PDOException $e) {
            if ($this->bd->inTransaction()) {
                $this->bd->rollBack();
            }
            if ($e->getCode() === '23000') {
                return ['exito' => false, 'error' => 'Ya existe otro equipo con ese nombre.'];
            }
            error_log('Error al renombrar equipo ID ' . $equipoId . ': ' . $e->getMessage());
            return ['exito' => false, 'error' => 'No se pudo renombrar el equipo. Intentá nuevamente.'];
        } catch (\Throwable $e) {
            if ($this->bd->inTransaction()) {
                $this->bd->rollBack();
            }
            error_log('Error inesperado al renombrar equipo ID ' . $equipoId . ': ' . $e->getMessage());
            return ['exito' => false, 'error' => 'No se pudo renombrar el equipo. Intentá nuevamente.'];
        }
    }

}