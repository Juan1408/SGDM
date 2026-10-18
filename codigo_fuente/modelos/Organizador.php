<?php
/**
 * ============================================================================
 * CLASE MODELO: Organizador.php
 * ============================================================================
 * Proposito: Acceso a datos del perfil del rol Organizador, combinando la tabla
 *            general 'usuarios' con 'perfiles_organizadores' (relacion 1 a 1).
 * Ubicacion: codigo_fuente/modelos/Organizador.php
 * ============================================================================
 */

namespace App\Modelos;
use PDO;
require_once __DIR__ . '/Conexion.php';

class Organizador {
    private PDO $bd;

    //Campos OPCIONALES que cada organizador puede mostrar u ocultar en su perfil publico.
    //Siempre visibles: nombre de la organizacion, foto, banner, verificado y torneos que organiza.
    //Nunca visibles: email y telefono personal.
    public const CAMPOS_OPCIONALES = ['bio', 'localidad', 'sitio_web', 'telefono_contacto'];

    public function __construct() {
        $this->bd = Conexion::getInstance()->getBD();
    }

    //Convierte el texto guardado en BD ("bio,localidad") en un arreglo, descartando valores desconocidos
    public static function listaCamposPublicos(?string $texto): array {
        $lista = array_filter(array_map('trim', explode(',', (string) $texto)));
        return array_values(array_intersect($lista, self::CAMPOS_OPCIONALES));
    }

    //Perfil completo para la pagina de edicion (incluye datos privados)
    public function obtenerPerfil(int $usuarioId) {
        $sql = "SELECT
            u.id,
            u.nombre_completo,
            u.email,
            u.telefono,
            u.foto_perfil_url,
            p.nombre_organizacion,
            p.bio_organizacion,
            p.localidad,
            p.telefono_contacto,
            p.sitio_web,
            p.verificado_oficial,
            p.banner_url,
            p.campos_publicos
        FROM usuarios u
        LEFT JOIN perfiles_organizadores p ON p.usuario_id = u.id
        WHERE u.id = :usuarioId AND u.rol_id = 2
        LIMIT 1";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':usuarioId' => $usuarioId]);
        $perfil = $stmt->fetch(PDO::FETCH_ASSOC);
        return $perfil ?: false;
    }

    //Perfil PUBLICO: solo lo que el organizador eligio mostrar. NO trae email ni telefono personal.
    //Trae 'visibles' (campos opcionales publicos). Solo usuarios activos con rol Organizador (2).
    public function obtenerPerfilPublico(int $usuarioId) {
        $sql = "SELECT
            u.id,
            u.nombre_completo,
            u.foto_perfil_url,
            p.nombre_organizacion,
            p.bio_organizacion,
            p.localidad,
            p.telefono_contacto,
            p.sitio_web,
            p.verificado_oficial,
            p.banner_url,
            p.campos_publicos
        FROM usuarios u
        LEFT JOIN perfiles_organizadores p ON p.usuario_id = u.id
        WHERE u.id = :usuarioId AND u.rol_id = 2 AND u.esta_activo = 1
        LIMIT 1";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':usuarioId' => $usuarioId]);
        $perfil = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$perfil) {
            return false;
        }

        $visibles = self::listaCamposPublicos($perfil['campos_publicos']);

        //Lo que no esta marcado como publico se borra aca, antes de llegar a la vista
        if (!in_array('bio', $visibles, true)) { $perfil['bio_organizacion'] = null; }
        if (!in_array('localidad', $visibles, true)) { $perfil['localidad'] = null; }
        if (!in_array('sitio_web', $visibles, true)) { $perfil['sitio_web'] = null; }
        if (!in_array('telefono_contacto', $visibles, true)) { $perfil['telefono_contacto'] = null; }

        //Sin nombre de organizacion cargado, se muestra el nombre de la persona
        if (empty($perfil['nombre_organizacion'])) {
            $perfil['nombre_organizacion'] = $perfil['nombre_completo'];
        }

        unset($perfil['campos_publicos']);
        $perfil['visibles'] = $visibles;
        return $perfil;
    }

    //Torneos publicados del organizador (sin borradores ni cancelados) para su perfil publico
    public function obtenerTorneosPublicos(int $usuarioId, int $limite = 24): array {
        $sql = "SELECT t.id, t.nombre, t.estado, t.banner_url, t.fecha_inicio, j.nombre AS nombre_juego
                FROM torneos t
                INNER JOIN juegos j ON j.id = t.juego_id
                WHERE t.organizador_id = :usuarioId AND t.estado NOT IN ('borrador', 'cancelado')
                ORDER BY t.id DESC
                LIMIT :limite";
        $stmt = $this->bd->prepare($sql);
        $stmt->bindValue(':usuarioId', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Actualiza el perfil del organizador.
    //$datosUsuario: nombre_completo, telefono, foto_perfil_url (null = no cambia)
    //$datosPerfil: nombre_organizacion, bio_organizacion, localidad, telefono_contacto, sitio_web,
    //              banner_url (null = no cambia), campos_publicos (texto "bio,localidad,...")
    public function actualizarPerfil(int $usuarioId, array $datosUsuario, array $datosPerfil): bool {
        $stmtUsuario = $this->bd->prepare(
            "UPDATE usuarios
             SET nombre_completo = :nombre_completo,
                 telefono = :telefono,
                 foto_perfil_url = COALESCE(:foto, foto_perfil_url)
             WHERE id = :id"
        );
        $okUsuario = $stmtUsuario->execute([
            ':nombre_completo' => $datosUsuario['nombre_completo'],
            ':telefono' => $datosUsuario['telefono'] ?: null,
            ':foto' => $datosUsuario['foto_perfil_url'] ?? null,
            ':id' => $usuarioId
        ]);

        //usuario_id es PRIMARY KEY de perfiles_organizadores: sirve como upsert
        $stmtPerfil = $this->bd->prepare(
            "INSERT INTO perfiles_organizadores
                (usuario_id, nombre_organizacion, bio_organizacion, localidad, telefono_contacto, sitio_web, banner_url, campos_publicos)
             VALUES
                (:usuario_id, :organizacion, :bio, :localidad, :tel_contacto, :sitio, :banner, :campos)
             ON DUPLICATE KEY UPDATE
                nombre_organizacion = VALUES(nombre_organizacion),
                bio_organizacion = VALUES(bio_organizacion),
                localidad = VALUES(localidad),
                telefono_contacto = VALUES(telefono_contacto),
                sitio_web = VALUES(sitio_web),
                banner_url = COALESCE(VALUES(banner_url), banner_url),
                campos_publicos = VALUES(campos_publicos)"
        );
        $okPerfil = $stmtPerfil->execute([
            ':usuario_id' => $usuarioId,
            ':organizacion' => $datosPerfil['nombre_organizacion'],
            ':bio' => $datosPerfil['bio_organizacion'] ?: null,
            ':localidad' => $datosPerfil['localidad'] ?: null,
            ':tel_contacto' => $datosPerfil['telefono_contacto'] ?: null,
            ':sitio' => $datosPerfil['sitio_web'] ?: null,
            ':banner' => $datosPerfil['banner_url'] ?? null,
            ':campos' => $datosPerfil['campos_publicos'] ?? ''
        ]);

        return $okUsuario && $okPerfil;
    }
}
