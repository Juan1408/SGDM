<?php
namespace Tests\Unitarias;

use Tests\TestCase;
use App\Modelos\Jugador;

require_once __DIR__ . '/../../codigo_fuente/modelos/Jugador.php';

/**
 * El perfil del jugador solo puede cambiar datos personales y deportivos.
 * El email queda en solo lectura para preservar la verificación de correo.
 */
class PerfilJugadorTest extends TestCase {

    private Jugador $modeloJugador;

    protected function setUp(): void {
        parent::setUp();
        $this->modeloJugador = new Jugador();
    }

    private function crearJugador(): int {
        $email = 'perfil_' . uniqid() . '@ascend.test';
        $stmt = $this->bd->prepare(
            "INSERT INTO usuarios (email, contrasena_hash, nombre_completo, telefono, rol_id, esta_activo, email_verificado)
             VALUES (:email, :hash, 'Jugador Perfil', '+59899000001', 3, 1, 1)"
        );
        $stmt->execute([
            ':email' => $email,
            ':hash' => password_hash('ClaveDePrueba123!', PASSWORD_BCRYPT),
        ]);

        $usuarioId = (int) $this->bd->lastInsertId();

        $stmt = $this->bd->prepare(
            "INSERT INTO perfiles_jugadores (usuario_id, apodo_gamertag) VALUES (:id, 'ApodoOriginal')"
        );
        $stmt->execute([':id' => $usuarioId]);

        return $usuarioId;
    }

    private function datosPerfil(): array {
        return [
            'bio' => 'Biografía de prueba',
            'ciudad' => 'Montevideo',
            'pais' => 'Uruguay',
            'fecha_nacimiento' => '1990-01-01',
            'discord_tag' => null,
            'instagram_url' => null,
            'twitter_url' => null,
            'banner_url' => null,
            'campos_publicos' => '',
        ];
    }

    public function test_actualizar_perfil_cambia_nombre_pero_nunca_el_email(): void {
        $usuarioId = $this->crearJugador();

        $stmt = $this->bd->prepare("SELECT email FROM usuarios WHERE id = :id");
        $stmt->execute([':id' => $usuarioId]);
        $emailOriginal = $stmt->fetchColumn();

        $resultado = $this->modeloJugador->actualizarPerfil(
            $usuarioId,
            [
                'nombre_completo' => 'Nombre Actualizado',
                // Aunque un POST fabricado envíe otro correo, el modelo debe ignorarlo.
                'email' => 'intruso@ascend.test',
                'foto_perfil_url' => null,
            ],
            $this->datosPerfil()
        );

        $this->assertTrue($resultado);

        $stmt = $this->bd->prepare(
            "SELECT nombre_completo, email, email_verificado FROM usuarios WHERE id = :id"
        );
        $stmt->execute([':id' => $usuarioId]);
        $fila = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertSame('Nombre Actualizado', $fila['nombre_completo']);
        $this->assertSame($emailOriginal, $fila['email'], 'El email no debe cambiar desde el perfil del jugador.');
        $this->assertSame(1, (int) $fila['email_verificado'], 'La verificación debe conservarse.');

        $stmt = $this->bd->prepare("SELECT bio, ciudad FROM perfiles_jugadores WHERE usuario_id = :id");
        $stmt->execute([':id' => $usuarioId]);
        $perfil = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertSame('Biografía de prueba', $perfil['bio']);
        $this->assertSame('Montevideo', $perfil['ciudad']);
    }
}
