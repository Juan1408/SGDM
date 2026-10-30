<?php
namespace Tests\Integracion;

use Tests\TestCase;
use App\Modelos\Torneo;

/**
 * Comportamiento real del sistema de comentarios de torneos sobre ascend_test.
 *
 * Cubre publicación, visibilidad según aprobado, autor vigente vs. autor
 * guardado, orden, paginación, ventana anti-inundación, borrado en cascada y
 * rechazo de comentarios huérfanos.
 */
class ComentariosTorneoTest extends TestCase {

    private Torneo $modeloTorneo;

    protected function setUp(): void {
        parent::setUp();
        $this->modeloTorneo = new Torneo();
        $this->sembrarTorneo();
    }

    /** Crea el mínimo relacional: juego habilitado, usuario y torneo publicable. */
    private function sembrarTorneo(): void {
        $this->bd->exec("INSERT INTO juegos (id, nombre, categoria) VALUES (1, 'League of Legends', 'esport-moba')");
        $this->bd->exec("INSERT INTO juego_modalidades_permitidas (juego_id, modalidad_id) VALUES (1, 2)");
        $this->bd->exec("INSERT INTO juego_formatos_permitidos (juego_id, formato_codigo) VALUES (1, 'liga')");
        $this->bd->exec(
            "INSERT INTO usuarios (id, nombre_completo, email, contrasena_hash, rol_id, esta_activo)
             VALUES (1, 'Jugador Uno', 'jugador1@test.com', 'hash-de-prueba', 3, 1)"
        );
        $this->bd->exec(
            "INSERT INTO torneos (id, nombre, juego_id, organizador_id, estado, formato, modalidad_id, fecha_inicio, cupo_max_equipos)
             VALUES (1, 'Copa Comentarios', 1, 1, 'inscripciones_abiertas', 'liga', 2, '2026-12-01', 8)"
        );
    }

    private function insertarComentarioDirecto(string $texto, string $fechaHora, int $aprobado = 1, ?int $usuarioId = 1): void {
        $usuario = $usuarioId === null ? 'NULL' : (string) $usuarioId;
        $this->bd->exec(
            "INSERT INTO torneo_comentarios (torneo_id, usuario_id, nombre_autor, email_autor, comentario, aprobado, fecha_hora)
             VALUES (1, {$usuario}, 'Jugador Uno', 'jugador1@test.com', '{$texto}', {$aprobado}, '{$fechaHora}')"
        );
    }

    private function contarFilas(): int {
        return (int) $this->bd->query("SELECT COUNT(*) FROM torneo_comentarios")->fetchColumn();
    }

    public function test_publicar_y_listar_un_comentario(): void {
        $this->assertTrue($this->modeloTorneo->crearComentario(1, 1, 'Jugador Uno', 'jugador1@test.com', 'Excelente organización de la fecha.'));

        $comentarios = $this->modeloTorneo->obtenerComentariosPublicos(1, 10);

        $this->assertCount(1, $comentarios);
        $this->assertSame('Excelente organización de la fecha.', $comentarios[0]['comentario']);
        $this->assertSame('Jugador Uno', $comentarios[0]['nombre_autor']);
        $this->assertSame('Jugador Uno', $comentarios[0]['nombre_usuario']);
        $this->assertSame(1, (int) $comentarios[0]['usuario_id']);
        $this->assertNotEmpty($comentarios[0]['fecha_hora']);
        $this->assertSame(1, $this->modeloTorneo->contarComentariosPublicos(1));
    }

    public function test_el_comentario_no_aprobado_no_se_lista(): void {
        $this->assertTrue($this->modeloTorneo->crearComentario(1, 1, 'Jugador Uno', 'jugador1@test.com', 'Comentario retenido.', false));

        $this->assertSame(1, $this->contarFilas(), 'El registro debe guardarse igual.');
        $this->assertSame(0, $this->modeloTorneo->contarComentariosPublicos(1));
        $this->assertSame([], $this->modeloTorneo->obtenerComentariosPublicos(1, 10));
    }

    public function test_el_nombre_actual_del_usuario_prevalece_sobre_el_guardado(): void {
        $this->modeloTorneo->crearComentario(1, 1, 'Jugador Uno', 'jugador1@test.com', 'Comentario con nombre viejo.');
        $this->bd->exec("UPDATE usuarios SET nombre_completo = 'Jugador Renombrado' WHERE id = 1");

        $comentarios = $this->modeloTorneo->obtenerComentariosPublicos(1, 10);

        $this->assertSame('Jugador Uno', $comentarios[0]['nombre_autor'], 'Se conserva la copia histórica.');
        $this->assertSame('Jugador Renombrado', $comentarios[0]['nombre_usuario'], 'La vista usa el nombre vigente.');
    }

    public function test_si_la_cuenta_ya_no_existe_se_conserva_el_comentario(): void {
        $this->assertTrue($this->modeloTorneo->crearComentario(1, null, 'Autor Histórico', 'historico@test.com', 'Comentario de una cuenta eliminada.'));

        $comentarios = $this->modeloTorneo->obtenerComentariosPublicos(1, 10);

        $this->assertCount(1, $comentarios);
        $this->assertNull($comentarios[0]['usuario_id']);
        $this->assertNull($comentarios[0]['nombre_usuario']);
        $this->assertSame('Autor Histórico', $comentarios[0]['nombre_autor']);
    }

    public function test_el_listado_ordena_del_mas_reciente_al_mas_viejo(): void {
        $this->insertarComentarioDirecto('Primero', '2026-10-01 10:00:00');
        $this->insertarComentarioDirecto('Segundo', '2026-10-02 10:00:00');
        $this->insertarComentarioDirecto('Tercero', '2026-10-03 10:00:00');

        $comentarios = $this->modeloTorneo->obtenerComentariosPublicos(1, 10);

        $this->assertSame(['Tercero', 'Segundo', 'Primero'], array_column($comentarios, 'comentario'));
    }

    public function test_la_paginacion_respeta_el_limite_y_el_total(): void {
        for ($i = 1; $i <= 12; $i++) {
            $this->insertarComentarioDirecto('Comentario ' . $i, '2026-10-01 10:00:00');
        }

        $this->assertCount(10, $this->modeloTorneo->obtenerComentariosPublicos(1, 10));
        $this->assertCount(12, $this->modeloTorneo->obtenerComentariosPublicos(1, 20));
        $this->assertSame(12, $this->modeloTorneo->contarComentariosPublicos(1));
    }

    public function test_el_limite_anti_inundacion_solo_cuenta_la_ventana_reciente(): void {
        $this->insertarComentarioDirecto('Reciente', date('Y-m-d H:i:s'));
        $this->insertarComentarioDirecto('Antiguo', date('Y-m-d H:i:s', time() - 3600));

        $this->assertSame(1, $this->modeloTorneo->contarComentariosRecientes(1, 1, 30));
        $this->assertSame(2, $this->modeloTorneo->contarComentariosRecientes(1, 1, 7200));
    }

    public function test_el_limite_anti_inundacion_es_por_torneo(): void {
        $this->bd->exec(
            "INSERT INTO torneos (id, nombre, juego_id, organizador_id, estado, formato, modalidad_id, fecha_inicio, cupo_max_equipos)
             VALUES (2, 'Otra Copa', 1, 1, 'inscripciones_abiertas', 'liga', 2, '2026-12-01', 8)"
        );
        $this->insertarComentarioDirecto('En el torneo 1', date('Y-m-d H:i:s'));

        $this->assertSame(1, $this->modeloTorneo->contarComentariosRecientes(1, 1, 30));
        $this->assertSame(0, $this->modeloTorneo->contarComentariosRecientes(1, 2, 30));
    }

    public function test_el_borrado_administrativo_del_torneo_elimina_los_comentarios(): void {
        $this->modeloTorneo->crearComentario(1, 1, 'Jugador Uno', 'jugador1@test.com', 'Se va con el torneo.');
        $this->assertSame(1, $this->contarFilas());

        $this->assertTrue($this->modeloTorneo->eliminarPorAdmin(1));

        $this->assertSame(0, $this->contarFilas());
    }

    public function test_no_se_guarda_un_comentario_de_un_torneo_inexistente(): void {
        $resultado = $this->modeloTorneo->crearComentario(9999, 1, 'Jugador Uno', 'jugador1@test.com', 'Torneo fantasma.');

        $this->assertFalse($resultado);
        $this->assertSame(0, $this->contarFilas());
    }
}
