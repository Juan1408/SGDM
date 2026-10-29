<?php
namespace Test;

use App\Modelos\Equipo;

require_once __DIR__ . '/CasoBase.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Equipo.php';

/**
 * Funcionalidad 4: renombre administrativo de un equipo.
 * Método: Equipo::renombrarEquipoAdmin()
 *
 * Reglas comprobadas:
 *  - Se recortan y colapsan los espacios del nombre antes de guardarlo.
 *  - No se permite un nombre que ya usa otro equipo.
 *  - Un nombre con menos de 2 caracteres se rechaza.
 */
class RenombrarEquipoTest extends CasoBase {

    private Equipo $modeloEquipo;

    protected function setUp(): void {
        parent::setUp();
        $this->modeloEquipo = Equipo::obtenerInstancia();
    }

    private function crearEquipo(int $creadorId, string $nombre): int {
        $stmt = $this->bd->prepare(
            "INSERT INTO equipos (nombre_equipo, creado_por, activo) VALUES (:nombre, :creador, 1)"
        );
        $stmt->execute([':nombre' => $nombre, ':creador' => $creadorId]);

        return (int) $this->bd->lastInsertId();
    }

    public function test_renombre_normaliza_espacios_y_devuelve_el_nombre_anterior(): void {
        $creadorId = $this->crearUsuario(3, 'Capitán QA');
        $equipoId = $this->crearEquipo($creadorId, 'Equipo Original');

        $resultado = $this->modeloEquipo->renombrarEquipoAdmin($equipoId, '  Equipo   Renombrado  ');

        $this->assertTrue($resultado['exito']);
        $this->assertSame('Equipo Original', $resultado['anterior']);
        $this->assertSame('Equipo Renombrado', $resultado['nuevo']);

        $stmt = $this->bd->prepare('SELECT nombre_equipo FROM equipos WHERE id = :id');
        $stmt->execute([':id' => $equipoId]);
        $this->assertSame('Equipo Renombrado', $stmt->fetchColumn());
    }

    public function test_renombre_rechaza_un_nombre_ya_usado_por_otro_equipo(): void {
        $creadorId = $this->crearUsuario(3, 'Capitán QA');
        $equipoA = $this->crearEquipo($creadorId, 'Equipo Alfa');
        $this->crearEquipo($creadorId, 'Equipo Beta');

        $resultado = $this->modeloEquipo->renombrarEquipoAdmin($equipoA, 'Equipo Beta');

        $this->assertFalse($resultado['exito']);
        $this->assertStringContainsString('Ya existe otro equipo', $resultado['error']);

        $stmt = $this->bd->prepare('SELECT nombre_equipo FROM equipos WHERE id = :id');
        $stmt->execute([':id' => $equipoA]);
        $this->assertSame('Equipo Alfa', $stmt->fetchColumn(), 'El nombre original no debe cambiar.');
    }

    public function test_renombre_rechaza_un_nombre_demasiado_corto(): void {
        $creadorId = $this->crearUsuario(3, 'Capitán QA');
        $equipoId = $this->crearEquipo($creadorId, 'Equipo Válido');

        $resultado = $this->modeloEquipo->renombrarEquipoAdmin($equipoId, ' A ');

        $this->assertFalse($resultado['exito']);
        $this->assertStringContainsString('al menos 2 caracteres', $resultado['error']);
    }
}
