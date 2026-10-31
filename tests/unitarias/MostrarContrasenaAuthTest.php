<?php
namespace Tests\Unitarias;

use PHPUnit\Framework\TestCase;

/**
 * Contrato del botón "Mostrar contraseña" en Iniciar Sesión y Crear Cuenta.
 *
 * Verifica que:
 *  - Cada campo de contraseña (.password-group) tenga un único botón
 *    .btn-mostrar-contrasena con un solo ícono FontAwesome.
 *  - La hoja de estilos login.css oculte el ícono nativo en color negro que
 *    inyecta el navegador (::-ms-reveal y ::-ms-clear en Edge/Windows) y
 *    mantenga el ícono propio en color blanco (#ffffff).
 *  - El script login.js alterne el tipo del input (password <-> text) y la
 *    clase del ícono existente sin duplicar nodos en el DOM.
 */
class MostrarContrasenaAuthTest extends TestCase {

    private const RUTA_LOGIN_VISTA    = __DIR__ . '/../../codigo_fuente/vistas/auth/login.php';
    private const RUTA_REGISTRO_VISTA = __DIR__ . '/../../codigo_fuente/vistas/auth/registro.php';
    private const RUTA_LOGIN_CSS      = __DIR__ . '/../../codigo_fuente/publico/css/publico/login.css';
    private const RUTA_LOGIN_JS       = __DIR__ . '/../../codigo_fuente/publico/js/login.js';

    private function leer(string $ruta): string {
        $this->assertFileExists($ruta, 'No se encontró el archivo requerido: ' . $ruta);
        return (string) file_get_contents($ruta);
    }

    public function test_login_tiene_un_unico_boton_e_icono_de_mostrar_contrasena(): void {
        $html = $this->leer(self::RUTA_LOGIN_VISTA);

        $this->assertSame(1, substr_count($html, 'password-group'), 'Login debe tener un único grupo de contraseña.');
        $this->assertSame(1, substr_count($html, 'btn-mostrar-contrasena'), 'Login debe tener un único botón para mostrar contraseña.');
        $this->assertSame(1, substr_count($html, 'fa-eye-slash'), 'Login no debe duplicar el ícono del ojo en el HTML.');
    }

    public function test_registro_tiene_un_unico_boton_por_cada_campo_de_contrasena(): void {
        $html = $this->leer(self::RUTA_REGISTRO_VISTA);

        $this->assertSame(2, substr_count($html, 'password-group'), 'Registro debe tener exactamente dos grupos de contraseña (clave y confirmación).');
        $this->assertSame(2, substr_count($html, 'btn-mostrar-contrasena'), 'Registro debe tener un único botón por cada campo de contraseña.');
        $this->assertSame(2, substr_count($html, 'fa-eye-slash'), 'Registro no debe duplicar íconos de ojo dentro de un mismo campo.');
    }

    public function test_css_oculta_el_icono_negro_nativo_del_navegador_y_mantiene_el_blanco(): void {
        $css = $this->leer(self::RUTA_LOGIN_CSS);

        $this->assertStringContainsString('::-ms-reveal', $css, 'login.css debe ocultar ::-ms-reveal para evitar el ojo negro nativo del navegador.');
        $this->assertStringContainsString('::-ms-clear', $css, 'login.css debe ocultar ::-ms-clear en los campos de contraseña.');

        $this->assertMatchesRegularExpression(
            '/::-ms-reveal[^{]*\{[^}]*display\s*:\s*none/s',
            $css,
            'La regla ::-ms-reveal debe declarar display: none.'
        );

        $this->assertMatchesRegularExpression(
            '/\.password-group\s+button\s*\{[^}]*color\s*:\s*#fff(?:fff)?\b/is',
            $css,
            'El botón de mostrar contraseña debe mantenerse en color blanco (#ffffff).'
        );
    }

    public function test_js_alterna_visibilidad_sobre_el_mismo_icono_sin_crear_nodos(): void {
        $js = $this->leer(self::RUTA_LOGIN_JS);

        $this->assertStringContainsString("querySelectorAll('.btn-mostrar-contrasena')", $js);
        $this->assertStringContainsString("closest('.password-group')", $js);
        $this->assertStringContainsString("classList.toggle('fa-eye'", $js);
        $this->assertStringContainsString("classList.toggle('fa-eye-slash'", $js);
        $this->assertStringNotContainsString('createElement', $js, 'login.js no debe inyectar íconos adicionales en el DOM.');
        $this->assertStringNotContainsString('insertAdjacentHTML', $js, 'login.js no debe duplicar HTML dentro del botón.');
    }
}
