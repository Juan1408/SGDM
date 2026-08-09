<?php
/**
 * ============================================================================
 * CLASE AYUDANTE: Validador.php
 * ============================================================================
 * Propósito: Centraliza las validaciones de entrada y sanitización de datos.
 * Ubicación: codigo_fuente/ayudantes/Validador.php
 * ============================================================================
 */

class Validador {

    /**
     * Sanitiza una cadena eliminando espacios sobrantes y convirtiendo caracteres
     * especiales a entidades HTML para prevenir ataques XSS (Cross-Site Scripting).
     *
     * @param string $dato Cadena sin procesar
     * @return string Cadena sanitizada
     */
    public static function sanitizar(string $dato): string {
        return htmlspecialchars(trim($dato), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Valida que un texto tenga una longitud mínima y máxima requerida.
     *
     * @param string $texto Texto a evaluar
     * @param int $min Longitud mínima permitida
     * @param int $max Longitud máxima permitida
     * @return bool True si cumple con el rango, False en caso contrario
     */
    public static function validarTexto(string $texto, int $min = 2, int $max = 100): bool {
        $longitud = mb_strlen(trim($texto));
        return ($longitud >= $min && $longitud <= $max);
    }

    /**
     * Valida el formato sintáctico de una dirección de correo electrónico.
     *
     * @param string $email Correo a validar
     * @return bool True si el formato es válido
     */
    public static function validarEmail(string $email): bool {
        return (bool) filter_var(trim($email), FILTER_VALIDATE_EMAIL);
    }

    /**
     * Valida números de teléfono (Locales de Uruguay ej: 099123456 o Internacionales ej: +59899123456).
     *
     * @param string $telefono Cadena telefónica
     * @return bool True si contiene entre 8 y 15 dígitos con '+' opcional al inicio
     */
    public static function validarTelefono(string $telefono): bool {
        $telefono = trim($telefono);
        $patron = '/^\+?[0-9]{8,15}$/';
        return (bool) preg_match($patron, $telefono);
    }

    /**
     * Valida que una contraseña cumpla con las políticas mínimas de seguridad:
     * - Al menos 8 caracteres
     * - Al menos una mayúscula (A-Z)
     * - Al menos una minúscula (a-z)
     * - Al menos un dígito numérico (0-9)
     *
     * @param string $clave Contraseña en texto plano
     * @return bool True si cumple con las directivas
     */
    public static function validarContrasenaFuerte(string $clave): bool {
        if (strlen($clave) < 8) return false;
        if (!preg_match('/[A-Z]/', $clave)) return false;
        if (!preg_match('/[a-z]/', $clave)) return false;
        if (!preg_match('/[0-9]/', $clave)) return false;
        return true;
    }

    /**
     * Valida que un número entero se encuentre dentro de un rango específico.
     *
     * @param int $valor Número a evaluar
     * @param int $min Límite inferior
     * @param int $max Límite superior
     * @return bool True si está dentro del rango
     */
    public static function validarEnteroRango(int $valor, int $min, int $max): bool {
        return ($valor >= $min && $valor <= $max);
    }

    /**
     * Valida que una fecha cumpla con el formato especificado y sea una fecha existente en el calendario.
     *
     * @param string $fecha Cadena de fecha (ej: '2026-08-15')
     * @param string $formato Formato esperado ('Y-m-d')
     * @return bool True si la fecha es real y válida
     */
    public static function validarFecha(string $fecha, string $formato = 'Y-m-d'): bool {
        $d = DateTime::createFromFormat($formato, $fecha);
        return $d && $d->format($formato) === $fecha;
    }

    /**
     * Valida que un archivo subido sea una imagen real comprobando su tipo MIME y tamaño.
     *
     * @param array $archivo Arreglo $_FILES['campo']
     * @param int $tamanoMaximoBytes Límite de peso en bytes (2MB por defecto = 2097152)
     * @return bool True si es una imagen válida permitida
     */
    public static function validarImagen(array $archivo, int $tamanoMaximoBytes = 2097152): bool {
        if (!isset($archivo['tmp_name']) || empty($archivo['tmp_name'])) {
            return false;
        }

        if ($archivo['size'] > $tamanoMaximoBytes) {
            return false;
        }

        $mimesPermitidos = ['image/jpeg', 'image/png', 'image/webp'];
        $tipoMime = mime_content_type($archivo['tmp_name']);

        return in_array($tipoMime, $mimesPermitidos, true);
    }
}
