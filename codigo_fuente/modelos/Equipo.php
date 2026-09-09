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
    public function crearEquipo(string $nombreEquipo, int $creadoPor, ?string $descripcion = null, ?string $ubicacion = null, ?int $anioFundacion = null, ?string $escudoUrl = null, ?string $bannerUrl = null, ?string $codigoInvitacion = null){
        //Preparamos la consulta SQL con las columnas que realmente existen en 'equipos'
        $sql = "INSERT INTO equipos (nombre_equipo, creado_por, descripcion, ubicacion, anio_fundacion, escudo_url, banner_url, codigo_invitacion)
                VALUES (:nombreEquipo, :creadoPor, :descripcion, :ubicacion, :anioFundacion, :escudoUrl, :bannerUrl, :codigoInvitacion)";
        //Preparamos la consulta para evitar inyección SQL
        $stmt = $this->bd->prepare($sql);
        //Ejecutamos la consulta pasandole los datos
        $stmt->execute([
            ':nombreEquipo' => $nombreEquipo,
            ':creadoPor' => $creadoPor,
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
    public function crearEquipoCompleto(string $nombreEquipo, int $usuarioId, ?string $descripcion = null, ?string $ubicacion = null, ?int $anioFundacion = null): array {        
        try {
            $this->bd->beginTransaction();

            $codigoInvitacion = $this->generarCodigoInvitacion();

            //1. Creamos el equipo (escudo y banner quedan null por ahora, se suben en un paso aparte)
            $equipoId = $this->crearEquipo($nombreEquipo, $usuarioId, $descripcion, $ubicacion, $anioFundacion, null, null, $codigoInvitacion);
            //2. El creador queda como miembro del equipo
            $sqlMiembro = "INSERT INTO equipo_miembros (equipo_id, usuario_id) VALUES (:equipoId, :usuarioId)";
            $stmtMiembro = $this->bd->prepare($sqlMiembro);
            $stmtMiembro->execute([':equipoId' => $equipoId, ':usuarioId' => $usuarioId]);

            //3. El creador queda como capitan principal (asignado_por: se asigna a si mismo, no hay nadie mas todavia)
            $sqlCapitan = "INSERT INTO equipo_capitanes (equipo_id, usuario_id, asignado_por, es_capitan_principal) VALUES (:equipoId, :usuarioIdCapitan, :usuarioIdAsigna, 1)";
            $stmtCapitan = $this->bd->prepare($sqlCapitan);
            $stmtCapitan->execute([
                ':equipoId' => $equipoId,
                ':usuarioIdCapitan' => $usuarioId,
                ':usuarioIdAsigna' => $usuarioId
            ]);

            $this->bd->commit();

            return ['exito' => true, 'equipoId' => $equipoId];
        } catch (\Exception $e) {
            //Si algo fallo, deshacemos todo (no queremos un equipo sin capitan, o un capitan sin equipo)
            $this->bd->rollBack();
            return ['exito' => false, 'error' => $e->getMessage()];
        }
    }

        //Metodo para eliminar un equipo (Soft Delete)
    //Faltan ajustes de restricciones de integridad referencial
    public function eliminarEquipo(int $id){
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
        $sql = "SELECT * FROM equipos WHERE id = :id AND activo = 1 LIMIT 1";
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
        $sql = "INSERT INTO solicitudes_equipo (equipo_id, usuario_id, iniciado_por, mensaje, estado)
                VALUES (:equipoId, :usuarioId, :iniciadoPor, :mensaje, 'pendiente')";
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
        try {
            $this->bd->beginTransaction();

            //Verificamos que la solicitud sea de este usuario y siga pendiente (evita que alguien responda invitaciones ajenas)
            $sqlVerificar = "SELECT equipo_id FROM solicitudes_equipo WHERE id = :id AND usuario_id = :usuarioId AND estado = 'pendiente' LIMIT 1 FOR UPDATE";
            $stmtVerificar = $this->bd->prepare($sqlVerificar);
            $stmtVerificar->execute([':id' => $solicitudId, ':usuarioId' => $usuarioId]);
            $solicitud = $stmtVerificar->fetch(PDO::FETCH_ASSOC);

            if (!$solicitud) {
                $this->bd->rollBack();
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

            //Si acepto, lo sumamos de verdad como miembro del equipo
            if ($aceptar) {
                $sqlMiembro = "INSERT INTO equipo_miembros (equipo_id, usuario_id) VALUES (:equipoId, :usuarioId)";
                $stmtMiembro = $this->bd->prepare($sqlMiembro);
                $stmtMiembro->execute([':equipoId' => $solicitud['equipo_id'], ':usuarioId' => $usuarioId]);
            }

            $this->bd->commit();
            return ['exito' => true];
        } catch (\Exception $e) {
            $this->bd->rollBack();
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
    public function expulsarMiembro(int $equipoId, int $usuarioId): bool {
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
        try {
            $this->bd->beginTransaction();

            //Verificamos que la solicitud sea de ESTE equipo y siga pendiente
            $sqlVerificar = "SELECT usuario_id FROM solicitudes_equipo WHERE id = :id AND equipo_id = :equipoId AND estado = 'pendiente' LIMIT 1 FOR UPDATE";
            $stmtVerificar = $this->bd->prepare($sqlVerificar);
            $stmtVerificar->execute([':id' => $solicitudId, ':equipoId' => $equipoId]);
            $solicitud = $stmtVerificar->fetch(PDO::FETCH_ASSOC);

            if (!$solicitud) {
                $this->bd->rollBack();
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

            //Si se acepto, sumamos al jugador que pidio entrar como miembro real
            if ($aceptar) {
                $sqlMiembro = "INSERT INTO equipo_miembros (equipo_id, usuario_id) VALUES (:equipoId, :usuarioId)";
                $stmtMiembro = $this->bd->prepare($sqlMiembro);
                $stmtMiembro->execute([':equipoId' => $equipoId, ':usuarioId' => $solicitud['usuario_id']]);
            }

            $this->bd->commit();
            return ['exito' => true];
        } catch (\Exception $e) {
            $this->bd->rollBack();
            return ['exito' => false, 'error' => $e->getMessage()];
        }
    }

        //Metodo para designar a un miembro como capitan adicional (sub-capitan)
    public function hacerCapitan(int $equipoId, int $usuarioId, int $asignadoPor): bool {
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
    public function dejarEquipo(int $equipoId, int $usuarioId): array {
        try {
            $this->bd->beginTransaction();

            //Buscamos si hay OTRO capitan en este equipo (distinto al que se quiere ir)
            $sqlOtroCapitan = "SELECT usuario_id FROM equipo_capitanes WHERE equipo_id = :equipoId AND usuario_id != :usuarioId LIMIT 1 FOR UPDATE";
            $stmtOtroCapitan = $this->bd->prepare($sqlOtroCapitan);
            $stmtOtroCapitan->execute([':equipoId' => $equipoId, ':usuarioId' => $usuarioId]);
            $otroCapitan = $stmtOtroCapitan->fetch(PDO::FETCH_ASSOC);

            if (!$otroCapitan) {
                $this->bd->rollBack();
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

            $this->bd->commit();
            return ['exito' => true];
        } catch (\Exception $e) {
            $this->bd->rollBack();
            return ['exito' => false, 'error' => $e->getMessage()];
        }
    }

}