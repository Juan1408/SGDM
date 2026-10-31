<?php
namespace Test;

use App\Modelos\Usuario;

require_once __DIR__ . '/CasoBase.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Usuario.php';

/**
 * Funcionalidad 3: baja lógica de un usuario.
 * Método: Usuario::eliminarUsuario()
 *
 * Reglas comprobadas:
 *  - La baja conserva la fila y solo cambia esta_activo a 0.
 *  - Un identificador inexistente devuelve error.
 *  - Una segunda baja informa que la cuenta ya estaba dada de baja.
 */
class BajaUsuarioTest extends CasoBase {

    private Usuario $modeloUsuario;

    protected function setUp(): void {
        parent::setUp();
        $this->modeloUsuario = new Usuario();
    }

    public function test_baja_logica_conserva_la_fila_y_desactiva_la_cuenta(): void {
        $usuarioId = $this->crearUsuario(3, 'Jugador Baja');

        $resultado = $this->modeloUsuario->eliminarUsuario($usuarioId);

        $this->assertTrue($resultado['exito']);

        $stmt = $this->bd->prepare('SELECT esta_activo, nombre_completo FROM usuarios WHERE id = :id');
        $stmt->execute([':id' => $usuarioId]);
        $fila = $stmt->fetch();

        $this->assertNotFalse($fila, 'La fila debe conservarse para no perder el historial.');
        $this->assertSame(0, (int) $fila['esta_activo']);
        $this->assertSame('Jugador Baja', $fila['nombre_completo']);
    }

    public function test_baja_de_un_usuario_inexistente_devuelve_error(): void {
        $resultado = $this->modeloUsuario->eliminarUsuario(999999);

        $this->assertFalse($resultado['exito']);
        $this->assertStringContainsString('no existe', $resultado['mensaje']);
    }

    public function test_segunda_baja_informa_que_la_cuenta_ya_estaba_inactiva(): void {
        $usuarioId = $this->crearUsuario(3, 'Jugador Repetido');
        $this->modeloUsuario->eliminarUsuario($usuarioId);

        $resultado = $this->modeloUsuario->eliminarUsuario($usuarioId);

        $this->assertTrue($resultado['exito']);
        $this->assertStringContainsString('ya estaba dado de baja', $resultado['mensaje']);
    }
}
