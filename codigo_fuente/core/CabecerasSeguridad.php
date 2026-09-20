<?php
/**
 * ============================================================================
 * CLASE CORE: CabecerasSeguridad.php
 * ============================================================================
 * Propósito: Aplica políticas estrictas de seguridad HTTP (Security Headers)
 *            para mitigar XSS, Clickjacking y Sniffing en todo el sistema.
 * ============================================================================
 */

namespace App\Core;

class CabecerasSeguridad {

    /**
     * Aplica las cabeceras HTTP de seguridad a la respuesta.
     * Debe llamarse antes de enviar cualquier contenido (output) al cliente.
     */
    public static function aplicar() {
        // Previene Clickjacking evitando que el sitio se cargue en un iframe externo
        header("X-Frame-Options: SAMEORIGIN");

        // Previene ataques de suplantación de tipo MIME (MIME sniffing)
        header("X-Content-Type-Options: nosniff");

        // Protege la filtración de URLs en peticiones a otros dominios
        header("Referrer-Policy: strict-origin-when-cross-origin");

        // Bloquea el uso de hardware (cámara, microfono, ubicación) por si acaso
        header("Permissions-Policy: camera=(), microphone=(), geolocation=()");

        // Política de Seguridad de Contenido (CSP) Equilibrada:
        // Permite recursos internos, scripts inline (para la lógica visual y Toasts), 
        // imágenes en base64 (data:) y librerías desde CDNs confiables como Cloudflare/FontAwesome.
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com; " .
               "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; " .
               "font-src 'self' https://cdnjs.cloudflare.com; " .
               "img-src 'self' data: https:;";

        header("Content-Security-Policy: " . $csp);
    }
}
