<?php
namespace Tests\Unitarias;

use Tests\TestCase;
use App\Modelos\PoliticaContrasena;
use App\Controladores\AuthControlador;
use App\Ayudantes\Sesion;

require_once __DIR__ . '/../../codigo_fuente/modelos/PoliticaContrasena.php';
require_once __DIR__ . '/../../codigo_fuente/controladores/AuthControlador.php';
require_once __DIR__ . '/../../codigo_fuente/ayudantes/Sesion.php';

/**
 * Pruebas de la coherencia entre la política de contraseñas vigente y lo que
 * muestra el formulario público de registro.
 *
 * Cubre el defecto detectado: el formulario anunciaba siempre "Mínimo 8"
 * (y no tenía minlength) aunque el Administrador hubiera configurado otra
 * longitud, y al re-renderizar por un error se perdía la política cargada.
 */
class PoliticaContrasenaRegistroTest extends TestCase {

    private PoliticaContrasena $modeloPolitica;

    protected function setUp(): void {
        parent::setUp();
        $this->modeloPolitica = new PoliticaContrasena();
        $this->fijarPolitica(8);
        $_POST = [];
        $_GET = [];
        $_SESSION = [];
        $_SERVER['REQUEST_METHOD'] = 'POST';
    }

    protected function tearDown(): void {
        $_POST = [];
        $_GET = [];
        $_SESSION = [];
        parent::tearDown();
    }

    // --- Ayudas ---------------------------------------------------------

    private function fijarPolitica(int $longitud, int $mayuscula = 1, int $minuscula = 1, int $numero = 1, int $especial = 1): void {
        // La suite base no toca politicas_contrasenas; cada prueba fija su
        // propia fila id = 1 para no depender del orden de ejecución.
        $this->bd->query("TRUNCATE TABLE politicas_contrasenas");
        $stmt = $this->bd->prepare(
            "INSERT INTO politicas_contrasenas
                (id, longitud_minima, requiere_mayuscula, requiere_minuscula, requiere_numero, requiere_caracter_especial, expiracion_dias, historial_cantidad)
             VALUES (1, :longitud, :mayuscula, :minuscula, :numero, :especial, 90, 5)"
        );
        $stmt->execute([
            ':longitud' => $longitud,
            ':mayuscula' => $mayuscula,
            ':minuscula' => $minuscula,
            ':numero' => $numero,
            ':especial' => $especial,
        ]);
    }

    private function renderMostrarRegistro(): string {
        ob_start();
        try {
            (new AuthControlador())->mostrarRegistro();
            return (string) ob_get_clean();
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
    }

    private function renderRegistroTrasPost(array $datos): string {
        // Token válido salvo que la prueba envíe uno propio (caso CSRF).
        $token = array_key_exists('csrf_token', $datos) ? $datos['csrf_token'] : Sesion::generarTokenCSRF();
        $_POST = array_merge([
            'nombre_completo' => 'Persona de Prueba',
            'email' => 'registro_' . uniqid() . '@ascend.test',
            'telefono' => '',
            'contrasena' => 'ClaveSegura123!',
            'contrasena_confirmar' => 'ClaveSegura123!',
            'rol' => 'jugador',
            'aceptar_terminos' => '1',
        ], $datos, ['csrf_token' => $token]);

        ob_start();
        try {
            (new AuthControlador())->procesarRegistro();
            return (string) ob_get_clean();
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
    }

    // --- Formulario de registro ----------------------------------------

    public function test_registro_refleja_la_longitud_configurada_por_el_admin(): void {
        $this->fijarPolitica(20);

        $html = $this->renderMostrarRegistro();

        $this->assertStringContainsString('Contraseña (Mínimo 20 caracteres)', $html);
        $this->assertStringContainsString('Al menos 20 caracteres.', $html);
        $this->assertStringNotContainsString('Mínimo 8 caracteres', $html);
        // Ambos campos de contraseña declaran la misma longitud mínima.
        $this->assertSame(2, substr_count($html, 'minlength="20"'), 'Ambos campos de contraseña deben declarar el mínimo vigente.');
    }

    public function test_registro_refleja_una_politica_reducida(): void {
        $this->fijarPolitica(6);

        $html = $this->renderMostrarRegistro();

        $this->assertStringContainsString('Contraseña (Mínimo 6 caracteres)', $html);
        $this->assertSame(2, substr_count($html, 'minlength="6"'));
        $this->assertStringNotContainsString('Mínimo 8 caracteres', $html);
    }

    public function test_registro_con_error_de_confirmacion_mantiene_la_longitud_vigente(): void {
        $this->fijarPolitica(20);

        $html = $this->renderRegistroTrasPost([
            'contrasena' => 'ClaveSegura123!',
            'contrasena_confirmar' => 'OtraClave123!',
        ]);

        $this->assertStringContainsString('Las contraseñas no coinciden.', $html);
        $this->assertStringContainsString('Contraseña (Mínimo 20 caracteres)', $html);
        $this->assertSame(2, substr_count($html, 'minlength="20"'), 'El re-render por error no debe volver al valor fijo de 8.');
        $this->assertStringNotContainsString('Mínimo 8 caracteres', $html);
    }

    public function test_registro_con_error_csrf_mantiene_la_longitud_vigente(): void {
        $this->fijarPolitica(20);

        $html = $this->renderRegistroTrasPost(['csrf_token' => 'token-invalido']);

        $this->assertStringContainsString('CSRF', $html);
        $this->assertStringContainsString('Contraseña (Mínimo 20 caracteres)', $html);
        $this->assertSame(2, substr_count($html, 'minlength="20"'));
    }

    public function test_lista_de_requisitos_oculta_los_que_estan_desactivados(): void {
        $this->fijarPolitica(12, 1, 1, 1, 0);

        $html = $this->renderMostrarRegistro();

        $this->assertStringContainsString('Al menos 12 caracteres.', $html);
        $this->assertStringContainsString('Al menos una letra mayúscula (A-Z).', $html);
        $this->assertStringContainsString('Al menos una letra minúscula (a-z).', $html);
        $this->assertStringContainsString('Al menos un número (0-9).', $html);
        $this->assertStringNotContainsString('carácter especial', $html);
    }

    // --- Descripción compartida y validación del servidor ---------------

    public function test_descripcion_de_requisitos_por_defecto(): void {
        $requisitos = PoliticaContrasena::describirRequisitos([
            'longitud_minima' => 8,
            'requiere_mayuscula' => 1,
            'requiere_minuscula' => 1,
            'requiere_numero' => 1,
            'requiere_caracter_especial' => 1,
        ]);

        $this->assertCount(5, $requisitos);
        $this->assertSame('Al menos 8 caracteres.', $requisitos[0]);
        $this->assertContains('Al menos un carácter especial (!@#$%^&*...).', $requisitos);
    }

    public function test_descripcion_sin_politica_usa_los_valores_por_defecto(): void {
        $this->assertSame(['Al menos 8 caracteres.'], PoliticaContrasena::describirRequisitos([]));
    }

    public function test_validacion_del_servidor_coincide_con_la_longitud_mostrada(): void {
        $this->fijarPolitica(20);

        $corta = $this->modeloPolitica->validarPassword('ClaveSegura123!'); // 15 caracteres
        $this->assertFalse($corta['valido']);
        $this->assertContains('La contraseña debe tener al menos 20 caracteres.', $corta['errores']);

        $valida = $this->modeloPolitica->validarPassword(str_repeat('Ab1!', 5)); // 20 caracteres
        $this->assertTrue($valida['valido'], implode(' ', $valida['errores']));
    }

    // --- Aclaración en el panel administrativo --------------------------

    public function test_panel_admin_aclara_que_no_revalida_claves_existentes(): void {
        $datos = ['politica' => $this->modeloPolitica->obtenerPoliticaVigente()];

        ob_start();
        require __DIR__ . '/../../codigo_fuente/vistas/admin/configuracion/politicas_contrasenas.php';
        $html = (string) ob_get_clean();

        $this->assertStringContainsString('contraseñas nuevas', $html);
        $this->assertStringContainsString('no las revalida', $html);
    }
}
