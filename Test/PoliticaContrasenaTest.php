<?php
namespace Test;

use App\Modelos\PoliticaContrasena;

require_once __DIR__ . '/CasoBase.php';
require_once __DIR__ . '/../codigo_fuente/modelos/PoliticaContrasena.php';

/**
 * Funcionalidad 2: validación de una contraseña contra la política vigente.
 * Método: PoliticaContrasena::validarPassword()
 *
 * La política fijada en CasoBase exige 8 caracteres como mínimo y al menos
 * una mayúscula, una minúscula, un número y un carácter especial.
 */
class PoliticaContrasenaTest extends CasoBase {

    private PoliticaContrasena $modeloPolitica;

    protected function setUp(): void {
        parent::setUp();
        $this->modeloPolitica = new PoliticaContrasena();
    }

    public function test_contrasena_que_cumple_toda_la_politica_es_valida(): void {
        $resultado = $this->modeloPolitica->validarPassword('ClaveSegura1!');

        $this->assertTrue($resultado['valido']);
        $this->assertSame([], $resultado['errores']);
    }

    public function test_contrasena_corta_informa_el_minimo_de_caracteres(): void {
        $resultado = $this->modeloPolitica->validarPassword('Ab1!');

        $this->assertFalse($resultado['valido']);
        $this->assertContains(
            'La contraseña debe tener al menos 8 caracteres.',
            $resultado['errores']
        );
    }

    public function test_contrasena_sin_caracter_especial_es_rechazada(): void {
        $resultado = $this->modeloPolitica->validarPassword('ClaveSegura1');

        $this->assertFalse($resultado['valido']);
        $this->assertContains(
            'La contraseña debe contener al menos un carácter especial (!@#$%^&*...).',
            $resultado['errores']
        );
    }
}
