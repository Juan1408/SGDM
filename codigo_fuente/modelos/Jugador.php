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
            p.twitter_url
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

    //Metodo para actualizar el perfil del jugador (datos de cuenta + datos deportivos)
    //Nota: avatar y banner no se manejan aca todavia (requieren subida de archivos, es un paso aparte)
    public function actualizarPerfil(int $usuarioId, array $datosUsuario, array $datosPerfil): bool {
        //1. Actualizamos los datos generales en 'usuarios'
        $sqlUsuario = "UPDATE usuarios
            SET nombre_completo = :nombre_completo,
                email = :email
            WHERE id = :id";
        $stmtUsuario = $this->bd->prepare($sqlUsuario);
        $okUsuario = $stmtUsuario->execute([
            ':nombre_completo' => $datosUsuario['nombre_completo'],
            ':email' => $datosUsuario['email'],
            ':id' => $usuarioId
        ]);

        //2. Insertamos o actualizamos los datos deportivos en 'perfiles_jugadores'
        //Usamos ON DUPLICATE KEY UPDATE porque el jugador puede no tener fila todavia
        //(usuario_id es PRIMARY KEY de esta tabla, por eso funciona como upsert)
        $sqlPerfil = "INSERT INTO perfiles_jugadores (usuario_id, bio, ciudad, pais, fecha_nacimiento)
            VALUES (:usuario_id, :bio, :ciudad, :pais, :fecha_nacimiento)
            ON DUPLICATE KEY UPDATE
                bio = VALUES(bio),
                ciudad = VALUES(ciudad),
                pais = VALUES(pais),
                fecha_nacimiento = VALUES(fecha_nacimiento)";
        $stmtPerfil = $this->bd->prepare($sqlPerfil);
        $okPerfil = $stmtPerfil->execute([
            ':usuario_id' => $usuarioId,
            ':bio' => $datosPerfil['bio'],
            ':ciudad' => $datosPerfil['ciudad'],
            ':pais' => $datosPerfil['pais'],
            ':fecha_nacimiento' => $datosPerfil['fecha_nacimiento'] ?: null
        ]);

        return $okUsuario && $okPerfil;
    }

}
