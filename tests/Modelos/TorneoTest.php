<?php
namespace Tests\Modelos;

use Tests\TestCase;
use App\Modelos\Torneo;
use PDO;

class TorneoTest extends TestCase {

    public function test_obtener_directorio_admin_trae_los_datos_correctamente() {
        //1 PREPARACION (Insertamos datos falsos en la base de datos de pruebas)
        $this->bd->query("INSERT INTO juegos (id, nombre, categoria) VALUES (1, 'League of Legends', 'esport-moba')");
        $this->bd->query("INSERT INTO usuarios (id, nombre_completo, email, contrasena_hash, rol_id, esta_activo) VALUES (1, 'Admin Prueba',
        'admin@test.com', 'hash-de-prueba', 1, 1)");
        $this->bd->query("INSERT INTO torneos (id, nombre, juego_id, organizador_id, estado, formato, fecha_inicio, cupo_max_equipos) 
        VALUES (1, 'Torneo Prueba', 1, 1, 'inscripciones_abiertas', 'liga', '2026-12-01', 8)"); 
        
        //2 EJECUCION
        $modeloTorneo = new Torneo();
        $resultado = $modeloTorneo->obtenerDirectorioAdmin(1, 'todos', 'nuevo');

        //3 ASUNCIONES (Hacemos las afirmaciones)
        //Afirmamos que el resultado es un array
        $this->assertIsArray($resultado);
        //Afirmamos que el array no está vacío
        $this->assertNotEmpty($resultado);
        //Afirmamos que el array contiene al menos un elemento
        $this->assertCount(1, $resultado);
        //Afirmamos que el primer elemento es un array
        $this->assertIsArray($resultado[0]);
        //Afirmamos que el primer elemento tiene la clave 'nombre' con valor 'Torneo Prueba'
        $this->assertEquals('Torneo Prueba', $resultado[0]['nombre']);
        //Afirmamos que el primer elemento tiene la clave 'nombre_juego' con valor 'League of Legends'
        $this->assertEquals('League of Legends', $resultado[0]['nombre_juego']);
        //Afirmamos que el primer elemento tiene la clave 'estado' con valor 'inscripciones_abiertas'
        $this->assertEquals('inscripciones_abiertas', $resultado[0]['estado']);
        //Afirmamos que el primer elemento tiene la clave 'formato' con valor 'liga'
        $this->assertEquals('liga', $resultado[0]['formato']);
        //Afirmamos que el primer elemento tiene la clave 'fecha_inicio' con valor '2026-12-01'
        $this->assertEquals('2026-12-01', $resultado[0]['fecha_inicio']);
    }
}
