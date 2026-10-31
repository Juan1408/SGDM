<?php
namespace Test;

use App\Modelos\Usuario;

require_once __DIR__ . '/CasoBase.php';
require_once __DIR__ . '/../codigo_fuente/modelos/Usuario.php';

/**
 * Funcionalidad 7: actualización administrativa de un usuario y cambio de rol.
 * Método: Usuario::actualizarUsuario()
 *
 * Reglas comprobadas:
 *  - Se actualiza el nombre y el rol en una sola operación.
 *  - Al pasar a organizador se crea su perfil sin borrar datos previos.
 *  - Al pasar a jugador se crea el perfil de jugador y se conserva el de organizador.
 */
class ActualizarUsuarioTest extends CasoBase {

    private Usuario $modeloUsuario;

    protected function setUp(): void {
        parent::setUp();
        $this->modeloUsuario = new Usuario();
    }

    public function test_cambio_a_organizador_actualiza_el_rol_y_crea_su_perfil(): void {
        $usuarioId = $this->crearUsuario(3, 'Nombre Anterior');

        $resultado = $this->modeloUsuario->actualizarUsuario($usuarioId, [
            'nombre_completo' => 'Nombre Actualizado',
            'rol_id' => 2,
        ]);

        $this->assertTrue($resultado);

        $stmt = $this->bd->prepare('SELECT nombre_completo, rol_id FROM usuarios WHERE id = :id');
        $stmt->execute([':id' => $usuarioId]);
        $usuario = $stmt->fetch();

        $this->assertSame('Nombre Actualizado', $usuario['nombre_completo']);
        $this->assertSame(2, (int) $usuario['rol_id']);

        $stmt = $this->bd->prepare(
            'SELECT nombre_organizacion, verificado_oficial FROM perfiles_organizadores WHERE usuario_id = :id'
        );
        $stmt->execute([':id' => $usuarioId]);
        $perfil = $stmt->fetch();

        $this->assertNotFalse($perfil, 'Debe crearse el perfil de organizador.');
        $this->assertSame('Organizador Independiente', $perfil['nombre_organizacion']);
        $this->assertSame(0, (int) $perfil['verificado_oficial']);
    }

    public function test_cambio_a_jugador_crea_su_perfil_y_conserva_el_de_organizador(): void {
        $usuarioId = $this->crearUsuario(2, 'Organizador Existente');

        $stmt = $this->bd->prepare(
            "INSERT INTO perfiles_organizadores (usuario_id, nombre_organizacion, verificado_oficial)
             VALUES (:id, 'Liga Barrial', 0)"
        );
        $stmt->execute([':id' => $usuarioId]);

        $stmt = $this->bd->prepare('SELECT email FROM usuarios WHERE id = :id');
        $stmt->execute([':id' => $usuarioId]);
        $email = $stmt->fetchColumn();

        $resultado = $this->modeloUsuario->actualizarUsuario($usuarioId, [
            'nombre_completo' => 'Ahora Jugador',
            'rol_id' => 3,
        ]);

        $this->assertTrue($resultado);

        $stmt = $this->bd->prepare('SELECT rol_id FROM usuarios WHERE id = :id');
        $stmt->execute([':id' => $usuarioId]);
        $this->assertSame(3, (int) $stmt->fetchColumn());

        $stmt = $this->bd->prepare('SELECT apodo_gamertag FROM perfiles_jugadores WHERE usuario_id = :id');
        $stmt->execute([':id' => $usuarioId]);
        $apodo = $stmt->fetchColumn();
        $this->assertNotFalse($apodo, 'Debe crearse el perfil de jugador.');
        $this->assertSame(explode('@', $email)[0], $apodo, 'El apodo inicial sale del correo.');

        $stmt = $this->bd->prepare(
            'SELECT nombre_organizacion FROM perfiles_organizadores WHERE usuario_id = :id'
        );
        $stmt->execute([':id' => $usuarioId]);
        $this->assertSame('Liga Barrial', $stmt->fetchColumn(), 'El perfil de organizador no debe pisarse.');
    }
}
