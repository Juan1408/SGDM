<?php
/**
 * ============================================================================
 * CLASE MODELO: PoliticaContrasena.php
 * ============================================================================
 * Propósito: Gestiona el acceso a datos y reglas de negocio para la tabla 
 *            'politicas_contrasenas', permitiendo al Administrador configurar
 *            los requisitos de complejidad y vencimiento de contraseñas.
 * Ubicación: codigo_fuente/modelos/PoliticaContrasena.php
 * ============================================================================
 */

namespace App\Modelos;

use PDO;

require_once __DIR__ . '/Conexion.php';

class PoliticaContrasena {

    private PDO $bd;

    public function __construct() {
        $this->bd = Conexion::getInstance()->getBD();
    }

    /**
     * Obtiene la política de contraseñas activa (fila id = 1).
     * Si no existe ninguna fila, inserta los valores por defecto del sistema.
     *
     * @return array Datos de la política activa
     */
    public function obtenerPoliticaVigente(): array {
        $sql = "SELECT p.*, u.nombre_completo AS actualizado_por_nombre, u.email AS actualizado_por_email 
                FROM politicas_contrasenas p
                LEFT JOIN usuarios u ON p.actualizado_por = u.id
                WHERE p.id = 1 
                LIMIT 1";
        
        $stmt = $this->bd->prepare($sql);
        $stmt->execute();
        $politica = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$politica) {
            // Insertar fila por defecto si estuviera vacía la tabla
            $sqlInsert = "INSERT INTO politicas_contrasenas 
                          (id, longitud_minima, requiere_mayuscula, requiere_minuscula, requiere_numero, requiere_caracter_especial, expiracion_dias, historial_cantidad) 
                          VALUES (1, 8, 1, 1, 1, 1, 90, 5)";
            $this->bd->exec($sqlInsert);

            // Volver a consultar
            $stmt->execute();
            $politica = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return $politica ? $politica : [
            'id' => 1,
            'longitud_minima' => 8,
            'requiere_mayuscula' => 1,
            'requiere_minuscula' => 1,
            'requiere_numero' => 1,
            'requiere_caracter_especial' => 1,
            'expiracion_dias' => 90,
            'historial_cantidad' => 5
        ];
    }

    /**
     * Actualiza los parámetros de la política de contraseñas (fila id = 1).
     *
     * @param array $datos Arreglo asociativo con los valores a guardar
     * @param int|null $usuarioId ID del administrador que realiza la actualización
     * @return bool Verdadero si la actualización fue exitosa
     */
    public function guardarPolitica(array $datos, ?int $usuarioId = null): bool {
        $sql = "UPDATE politicas_contrasenas SET 
                    longitud_minima = :longitud_minima,
                    requiere_mayuscula = :requiere_mayuscula,
                    requiere_minuscula = :requiere_minuscula,
                    requiere_numero = :requiere_numero,
                    requiere_caracter_especial = :requiere_caracter_especial,
                    expiracion_dias = :expiracion_dias,
                    historial_cantidad = :historial_cantidad,
                    actualizado_por = :actualizado_por
                WHERE id = 1";

        $stmt = $this->bd->prepare($sql);
        return $stmt->execute([
            ':longitud_minima' => (int) ($datos['longitud_minima'] ?? 8),
            ':requiere_mayuscula' => !empty($datos['requiere_mayuscula']) ? 1 : 0,
            ':requiere_minuscula' => !empty($datos['requiere_minuscula']) ? 1 : 0,
            ':requiere_numero' => !empty($datos['requiere_numero']) ? 1 : 0,
            ':requiere_caracter_especial' => !empty($datos['requiere_caracter_especial']) ? 1 : 0,
            ':expiracion_dias' => (int) ($datos['expiracion_dias'] ?? 90),
            ':historial_cantidad' => (int) ($datos['historial_cantidad'] ?? 5),
            ':actualizado_por' => $usuarioId
        ]);
    }

    /**
     * Valida una contraseña plana contra las políticas activas en la base de datos.
     *
     * @param string $password Contraseña recibida del formulario
     * @return array Resultado ['valido' => bool, 'errores' => array]
     */
    public function validarPassword(string $password): array {
        $politica = $this->obtenerPoliticaVigente();
        $errores = [];

        // 1. Longitud mínima
        if (mb_strlen($password) < (int) $politica['longitud_minima']) {
            $errores[] = "La contraseña debe tener al menos " . $politica['longitud_minima'] . " caracteres.";
        }

        // 2. Mayúscula
        if (!empty($politica['requiere_mayuscula']) && !preg_match('/[A-Z]/', $password)) {
            $errores[] = "La contraseña debe contener al menos una letra mayúscula (A-Z).";
        }

        // 3. Minúscula
        if (!empty($politica['requiere_minuscula']) && !preg_match('/[a-z]/', $password)) {
            $errores[] = "La contraseña debe contener al menos una letra minúscula (a-z).";
        }

        // 4. Número
        if (!empty($politica['requiere_numero']) && !preg_match('/[0-9]/', $password)) {
            $errores[] = "La contraseña debe contener al menos un número (0-9).";
        }

        // 5. Carácter especial
        if (!empty($politica['requiere_caracter_especial']) && !preg_match('/[!@#$%^&*()\-_=+\\\|\[\]{};:\'",.<>?\/]/', $password)) {
            $errores[] = "La contraseña debe contener al menos un carácter especial (!@#$%^&*...).";
        }

        return [
            'valido' => empty($errores),
            'errores' => $errores
        ];
    }
}
