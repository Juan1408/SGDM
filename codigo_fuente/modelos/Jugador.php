<?php
/**
 * ============================================================================
 * CLASE MODELO: Jugador.php
 * ============================================================================
 * Propósito: Gestiona el acceso a datos específicos del rol Jugador,
 *            combinando la tabla general 'usuarios' con la especialización
 *            'perfiles_jugadores' (relación 1 a 1).
 * Ubicación: codigo_fuente/modelos/Jugador.php
 * ============================================================================
 */

namespace App\Modelos;
use PDO;
require_once __DIR__ . '/Conexion.php';

class Jugador {
    private PDO $bd;

    public function __construct() {
        $this->bd = Conexion::getInstance()->getBD();
    }

    //Metodo para obtener el perfil completo de un jugador (datos de cuenta + datos deportivos)
    //Usamos LEFT JOIN porque puede que el usuario todavia no tenga fila en perfiles_jugadores
    //(por ejemplo si el registro no crea la fila automaticamente todavia)
    public function obtenerPerfil(int $usuarioId) {
        $sql = "SELECT
            u.id,
            u.nombre_completo,
            u.email,
            u.telefono,
            u.foto_perfil_url,
            p.apodo_gamertag,
            p.bio,
            p.banner_url,
            p.nivel,
            p.experiencia_puntos,
            p.pais,
            p.ciudad,
            p.fecha_nacimiento,
            p.discord_tag,
            p.instagram_url,
            p.twitter_url,
            p.campos_publicos
        FROM usuarios u
        LEFT JOIN perfiles_jugadores p ON p.usuario_id = u.id
        WHERE u.id = :usuarioId
        LIMIT 1";

        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':usuarioId' => $usuarioId]);

        $perfil = $stmt->fetch(PDO::FETCH_ASSOC);

        //Si no encuentra el usuario devuelve false, si lo encuentra devuelve el perfil
        return $perfil ?: false;
    }

    //Metodo para verificar si un email ya esta en uso por otro usuario
    //(evita que dos cuentas terminen con el mismo email al editar perfil)
    public function emailEnUso(string $email, int $usuarioIdActual): bool {
        $sql = "SELECT id FROM usuarios WHERE email = :email AND id != :usuarioId LIMIT 1";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':email' => $email, ':usuarioId' => $usuarioIdActual]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    //Campos OPCIONALES que cada jugador puede mostrar u ocultar en su perfil publico.
    //Siempre visibles: apodo, foto, banner, nivel y equipos. Nunca visibles: email y telefono.
    public const CAMPOS_OPCIONALES = ['bio', 'ubicacion', 'edad', 'discord', 'instagram', 'twitter', 'torneos'];

    //Convierte el texto guardado en BD ("bio,edad") en un arreglo, descartando valores desconocidos
    public static function listaCamposPublicos(?string $texto): array {
        $lista = array_filter(array_map('trim', explode(',', (string) $texto)));
        return array_values(array_intersect($lista, self::CAMPOS_OPCIONALES));
    }

    //Perfil PUBLICO de un jugador: solo los datos que el jugador eligio mostrar.
    //NO incluye email ni telefono (nunca son publicos).
    //Solo devuelve usuarios activos con rol Jugador (3).
    //El resultado trae 'visibles' (array con los campos opcionales que el jugador dejo publicos).
    public function obtenerPerfilPublico(int $usuarioId) {
        $sql = "SELECT
            u.id,
            u.nombre_completo,
            u.foto_perfil_url,
            p.apodo_gamertag,
            p.bio,
            p.banner_url,
            p.nivel,
            p.experiencia_puntos,
            p.pais,
            p.ciudad,
            p.fecha_nacimiento,
            p.discord_tag,
            p.instagram_url,
            p.twitter_url,
            p.campos_publicos
        FROM usuarios u
        LEFT JOIN perfiles_jugadores p ON p.usuario_id = u.id
        WHERE u.id = :usuarioId AND u.rol_id = 3 AND u.esta_activo = 1
        LIMIT 1";

        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':usuarioId' => $usuarioId]);
        $perfil = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$perfil) {
            return false;
        }

        $visibles = self::listaCamposPublicos($perfil['campos_publicos']);

        //Lo que no esta marcado como publico se borra aca, antes de llegar a la vista
        if (!in_array('bio', $visibles, true)) { $perfil['bio'] = null; }
        if (!in_array('ubicacion', $visibles, true)) { $perfil['ciudad'] = null; $perfil['pais'] = null; }
        if (!in_array('edad', $visibles, true)) { $perfil['fecha_nacimiento'] = null; }
        if (!in_array('discord', $visibles, true)) { $perfil['discord_tag'] = null; }
        if (!in_array('instagram', $visibles, true)) { $perfil['instagram_url'] = null; }
        if (!in_array('twitter', $visibles, true)) { $perfil['twitter_url'] = null; }

        unset($perfil['campos_publicos']);
        $perfil['visibles'] = $visibles;
        return $perfil;
    }

        //Logros del jugador (catalogo + su estado y progreso). Primero los desbloqueados, despues en curso, al final bloqueados
    public function obtenerLogros(int $usuarioId): array {
        $sql = "SELECT l.nombre, l.descripcion, l.icono,
                       ul.estado, ul.progreso_actual, ul.progreso_objetivo
                FROM usuario_logros ul
                INNER JOIN logros l ON l.id = ul.logro_id
                WHERE ul.usuario_id = :usuarioId
                ORDER BY FIELD(ul.estado, 'desbloqueado', 'en_curso', 'bloqueado'), l.id";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':usuarioId' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Metodo para actualizar el perfil del jugador (datos de cuenta + datos deportivos)
    //$datosUsuario: nombre_completo, email, foto_perfil_url (null = no cambia)
    //$datosPerfil: bio, ciudad, pais, fecha_nacimiento, discord_tag, instagram_url, twitter_url,
    //              banner_url (null = no cambia), campos_publicos (texto "bio,edad,...")
    public function actualizarPerfil(int $usuarioId, array $datosUsuario, array $datosPerfil): bool {
        //1. Actualizamos los datos generales en 'usuarios'
        $sqlUsuario = "UPDATE usuarios
            SET nombre_completo = :nombre_completo,
                email = :email,
                foto_perfil_url = COALESCE(:foto, foto_perfil_url)
            WHERE id = :id";
        $stmtUsuario = $this->bd->prepare($sqlUsuario);
        $okUsuario = $stmtUsuario->execute([
            ':nombre_completo' => $datosUsuario['nombre_completo'],
            ':email' => $datosUsuario['email'],
            ':foto' => $datosUsuario['foto_perfil_url'] ?? null,
            ':id' => $usuarioId
        ]);

        //2. Insertamos o actualizamos los datos deportivos en 'perfiles_jugadores'
        //Usamos ON DUPLICATE KEY UPDATE porque el jugador puede no tener fila todavia
        //(usuario_id es PRIMARY KEY de esta tabla, por eso funciona como upsert)
        $sqlPerfil = "INSERT INTO perfiles_jugadores
                (usuario_id, bio, ciudad, pais, fecha_nacimiento, discord_tag, instagram_url, twitter_url, banner_url, campos_publicos)
            VALUES
                (:usuario_id, :bio, :ciudad, :pais, :fecha_nacimiento, :discord, :instagram, :twitter, :banner, :campos)
            ON DUPLICATE KEY UPDATE
                bio = VALUES(bio),
                ciudad = VALUES(ciudad),
                pais = VALUES(pais),
                fecha_nacimiento = VALUES(fecha_nacimiento),
                discord_tag = VALUES(discord_tag),
                instagram_url = VALUES(instagram_url),
                twitter_url = VALUES(twitter_url),
                banner_url = COALESCE(VALUES(banner_url), banner_url),
                campos_publicos = VALUES(campos_publicos)";
        $stmtPerfil = $this->bd->prepare($sqlPerfil);
        $okPerfil = $stmtPerfil->execute([
            ':usuario_id' => $usuarioId,
            ':bio' => $datosPerfil['bio'],
            ':ciudad' => $datosPerfil['ciudad'],
            ':pais' => $datosPerfil['pais'],
            ':fecha_nacimiento' => $datosPerfil['fecha_nacimiento'] ?: null,
            ':discord' => $datosPerfil['discord_tag'] ?: null,
            ':instagram' => $datosPerfil['instagram_url'] ?: null,
            ':twitter' => $datosPerfil['twitter_url'] ?: null,
            ':banner' => $datosPerfil['banner_url'] ?? null,
            ':campos' => $datosPerfil['campos_publicos'] ?? ''
        ]);

        return $okUsuario && $okPerfil;
    }

}
