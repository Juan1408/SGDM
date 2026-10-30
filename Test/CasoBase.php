<?php
namespace Test;

use PHPUnit\Framework\TestCase as BaseTestCase;
use App\Modelos\Conexion;

/**
 * Base común de las pruebas unitarias de la entrega de Ingeniería de Software.
 *
 * Cada caso se ejecuta contra la base de datos de prueba (ascend_test). El
 * setUp deja las tablas de negocio vacías para que un caso no dependa del
 * resultado de otro y fija la política de contraseñas en sus valores por
 * defecto, de modo que las reglas evaluadas sean siempre las mismas.
 */
abstract class CasoBase extends BaseTestCase {

    /** @var \PDO */
    protected $bd;

    protected function setUp(): void {
        parent::setUp();

        $this->bd = Conexion::getInstance()->getBD();

        $tablas = [
            // Torneos y competencia
            'torneo_encuentros',
            'torneo_posiciones',
            'torneo_suizo_parejas',
            'torneo_config',
            'torneo_comentarios',
            'participantes_torneo',
            'torneos',
            // Equipos
            'equipo_capitanes',
            'equipo_miembros',
            'equipos',
            // Usuarios y registros asociados
            'perfiles_jugadores',
            'perfiles_organizadores',
            'tokens_verificacion_email',
            'aceptaciones_legales',
            'sesiones_activas',
            'logs_acceso',
            'logs_actividad',
            'notificaciones',
            'auditoria_cambios',
            'errores_sistema',
            'usuarios',
            // Catálogo de juegos (se recrea en cada caso que lo necesita)
            'juego_modalidades_permitidas',
            'juego_formatos_permitidos',
            'juegos',
        ];

        $this->bd->exec('SET FOREIGN_KEY_CHECKS = 0');
        foreach ($tablas as $tabla) {
            $this->bd->exec('TRUNCATE TABLE ' . $tabla);
        }
        $this->bd->exec('SET FOREIGN_KEY_CHECKS = 1');

        // Política de contraseñas determinista: 8 caracteres, mayúscula,
        // minúscula, número y carácter especial obligatorios.
        $this->bd->exec('DELETE FROM politicas_contrasenas');
        $this->bd->exec(
            "INSERT INTO politicas_contrasenas
                (id, longitud_minima, requiere_mayuscula, requiere_minuscula, requiere_numero,
                 requiere_caracter_especial, expiracion_dias, historial_cantidad)
             VALUES (1, 8, 1, 1, 1, 1, 90, 5)"
        );
    }

    /**
     * Crea un usuario directamente en la tabla, sin pasar por el alta.
     * Se usa como dato de apoyo (organizador, capitán, creador de equipo).
     */
    protected function crearUsuario(int $rolId = 3, string $nombre = 'Usuario de Prueba'): int {
        $email = 'qa_' . uniqid('', true) . '@ascend.test';
        $stmt = $this->bd->prepare(
            "INSERT INTO usuarios (nombre_completo, email, contrasena_hash, telefono, rol_id, esta_activo, email_verificado)
             VALUES (:nombre, :email, :hash, '+59899000000', :rol, 1, 1)"
        );
        $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':hash' => password_hash('ClaveDePrueba123!', PASSWORD_BCRYPT),
            ':rol' => $rolId,
        ]);

        return (int) $this->bd->lastInsertId();
    }

    /**
     * Crea un juego activo con modalidad individual y los tres formatos
     * habilitados, para satisfacer los triggers de integridad de torneos.
     */
    protected function crearJuego(): int {
        $stmt = $this->bd->prepare(
            "INSERT INTO juegos (nombre, categoria, puntos_victoria, puntos_empate, puntos_derrota, activo)
             VALUES (:nombre, 'esport-shooter', 3.00, 1.00, 0.00, 1)"
        );
        $stmt->execute([':nombre' => 'Juego QA ' . uniqid()]);
        $juegoId = (int) $this->bd->lastInsertId();

        $this->bd->prepare("INSERT INTO juego_modalidades_permitidas (juego_id, modalidad_id) VALUES (:j, 1)")
            ->execute([':j' => $juegoId]);

        $formato = $this->bd->prepare(
            "INSERT INTO juego_formatos_permitidos (juego_id, formato_codigo) VALUES (:j, :f)"
        );
        foreach (['liga', 'eliminacion_directa', 'suizo'] as $codigo) {
            $formato->execute([':j' => $juegoId, ':f' => $codigo]);
        }

        return $juegoId;
    }

    /**
     * Crea un torneo con puntuación 3/1/0 y el estado indicado.
     */
    protected function crearTorneo(int $organizadorId, int $juegoId, string $formato, string $estado = 'inscripciones_abiertas'): int {
        $stmt = $this->bd->prepare(
            "INSERT INTO torneos
                (organizador_id, juego_id, modalidad_id, nombre, formato, sistema_puntuacion_id,
                 puntos_victoria, puntos_empate, puntos_derrota, estado, cupo_max_equipos)
             VALUES (:org, :juego, 1, :nombre, :formato, NULL, 3.00, 1.00, 0.00, :estado, 64)"
        );
        $stmt->execute([
            ':org' => $organizadorId,
            ':juego' => $juegoId,
            ':nombre' => 'Torneo QA ' . uniqid(),
            ':formato' => $formato,
            ':estado' => $estado,
        ]);

        return (int) $this->bd->lastInsertId();
    }

    /**
     * Confirma una cantidad de participantes y devuelve sus IDs.
     *
     * @return int[]
     */
    protected function confirmarParticipantes(int $torneoId, int $cantidad, int $organizadorId): array {
        $stmt = $this->bd->prepare(
            "INSERT INTO participantes_torneo
                (torneo_id, tipo, referencia_id, nombre, estado, confirmado_por, fecha_confirmacion)
             VALUES (:torneo, 'usuario', :referencia, :nombre, 'confirmado', :org, NOW())"
        );

        $ids = [];
        for ($indice = 1; $indice <= $cantidad; $indice++) {
            $stmt->execute([
                ':torneo' => $torneoId,
                ':referencia' => 1000 + $indice,
                ':nombre' => 'Participante ' . $indice,
                ':org' => $organizadorId,
            ]);
            $ids[] = (int) $this->bd->lastInsertId();
        }

        return $ids;
    }
}
