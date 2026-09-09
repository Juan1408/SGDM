<?php
/**
 * CLASE MODELO: Guardado.php
 *
 * Propósito: gestiona lo que el jugador guarda para ver después (torneos y equipos).
 * Ubicación: codigo_fuente/modelos/Guardado.php
 */

namespace App\Modelos;
use PDO;
require_once __DIR__ . '/Conexion.php';

class Guardado {
    private PDO $bd;

    public function __construct() {
        $this->bd = Conexion::getInstance()->getBD();
    }

    //Metodo para saber si algo puntual ya esta guardado (para mostrar el boton correcto: "Guardar" o "Quitar")
    public function estaGuardado(int $usuarioId, string $tipo, int $referenciaId): bool {
        $sql = "SELECT 1 FROM guardados WHERE usuario_id = :usuarioId AND tipo = :tipo AND referencia_id = :referenciaId LIMIT 1";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':usuarioId' => $usuarioId, ':tipo' => $tipo, ':referenciaId' => $referenciaId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    //Metodo para guardar algo (torneo o equipo)
    public function guardar(int $usuarioId, string $tipo, int $referenciaId): bool {
        //Si ya esta guardado no hacemos nada (evita el error de clave duplicada)
        if ($this->estaGuardado($usuarioId, $tipo, $referenciaId)) {
            return true;
        }

        $sql = "INSERT INTO guardados (usuario_id, tipo, referencia_id) VALUES (:usuarioId, :tipo, :referenciaId)";
        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([':usuarioId' => $usuarioId, ':tipo' => $tipo, ':referenciaId' => $referenciaId]);
    }

    //Metodo para quitar algo de guardados
    public function quitar(int $usuarioId, string $tipo, int $referenciaId): bool {
        $sql = "DELETE FROM guardados WHERE usuario_id = :usuarioId AND tipo = :tipo AND referencia_id = :referenciaId";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':usuarioId' => $usuarioId, ':tipo' => $tipo, ':referenciaId' => $referenciaId]);
        return $stmt->rowCount() > 0;
    }

    //Metodo para traer los equipos guardados por el jugador, con sus datos reales
    public function obtenerEquiposGuardados(int $usuarioId): array {
        $sql = "SELECT e.id, e.nombre_equipo, e.escudo_url, g.fecha_guardado
                FROM guardados g
                INNER JOIN equipos e ON e.id = g.referencia_id
                WHERE g.usuario_id = :usuarioId AND g.tipo = 'equipo' AND e.activo = 1
                ORDER BY g.fecha_guardado DESC";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':usuarioId' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Metodo para traer los torneos guardados por el jugador, con sus datos reales
    //(hoy va a devolver vacio si todavia no hay torneos cargados, es esperado)
    public function obtenerTorneosGuardados(int $usuarioId): array {
        $sql = "SELECT t.id, t.nombre, t.banner_url, t.estado, t.fecha_inicio, g.fecha_guardado
                FROM guardados g
                INNER JOIN torneos t ON t.id = g.referencia_id
                WHERE g.usuario_id = :usuarioId AND g.tipo = 'torneo'
                ORDER BY g.fecha_guardado DESC";
        $stmt = $this->bd->prepare($sql);
        $stmt->execute([':usuarioId' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
