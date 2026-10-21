<?php

namespace App\Core;

use App\Modelos\Conexion;
use PDO;
use Throwable;

class ManejadorErrores {

    public static function capturarExcepcion(Throwable $e) {
        $tipoError = ($e instanceof \PDOException || strpos(strtolower($e->getMessage()), 'sql') !== false) ? 'base_de_datos' : 'codigo';
        self::registrarError($e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString(), $tipoError);
        self::responderError500();
    }

    /**
     * Devuelve una respuesta 500 controlada en lugar de dejar un 200 vacío.
     * El detalle técnico queda en errores_sistema; al usuario solo se le
     * muestra un mensaje genérico.
     */
    private static function responderError500(): void {
        if (PHP_SAPI === 'cli') {
            return;
        }

        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: text/html; charset=utf-8');
        }

        // Descarta cualquier salida parcial que haya quedado en el buffer.
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $nombreApp = defined('APP_NOMBRE') ? APP_NOMBRE : 'SGDM';
        $nombreApp = htmlspecialchars($nombreApp, ENT_QUOTES, 'UTF-8');

        echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error interno</title>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, sans-serif; background: #f5f6f8; color: #1f2933; margin: 0; }
        main { max-width: 560px; margin: 12vh auto; background: #fff; padding: 2rem; border-radius: 12px; box-shadow: 0 8px 30px rgba(0,0,0,.08); }
        h1 { font-size: 1.4rem; margin-top: 0; }
        p { line-height: 1.5; }
        a { color: #1d4ed8; }
    </style>
</head>
<body>
    <main>
        <h1>Ocurrió un error interno</h1>
        <p>La aplicación no pudo completar la operación. El incidente quedó registrado para su revisión.</p>
        <p><a href="' . (defined('URL_BASE') ? htmlspecialchars(URL_BASE, ENT_QUOTES, 'UTF-8') : '/') . '">Volver al inicio</a></p>
        <p><small>' . $nombreApp . '</small></p>
    </main>
</body>
</html>';
        exit;
    }

    public static function capturarErrorPhp($nivel, $mensaje, $archivo, $linea) {
        if (!(error_reporting() & $nivel)) {
            return false;
        }
        $e = new \Exception();
        $stackTrace = $e->getTraceAsString();
        self::registrarError($mensaje, $archivo, $linea, $stackTrace, 'codigo');
        return false;
    }

    private static function registrarError($mensaje, $archivo, $linea, $stackTrace, $tipoError = 'codigo') {
        try {
            $bd = Conexion::getInstance()->getBD();
            $hashError = md5($archivo . $linea . $mensaje);

            $contexto = [
                'POST' => self::censurarDatosSensibles($_POST),
                'GET' => $_GET,
                'SERVER' => [
                    'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? '',
                    'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? '',
                    'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                    'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'] ?? ''
                ],
                'SESSION' => isset($_SESSION) ? self::censurarDatosSensibles($_SESSION) : []
            ];

            $contextoJson = json_encode($contexto);
            $usuarioId = $_SESSION['usuario_id'] ?? null;
            $rolUsuario = $_SESSION['rol'] ?? null;

            $sql = "INSERT INTO errores_sistema 
                        (hash_error, tipo_error, mensaje, archivo, linea, stack_trace, contexto_json, usuario_id, rol_usuario, cantidad_ocurrencias, estado) 
                    VALUES 
                        (:hash, :tipo, :mensaje, :archivo, :linea, :stack, :contexto, :usuario, :rol, 1, 'nuevo')
                    ON DUPLICATE KEY UPDATE 
                        cantidad_ocurrencias = cantidad_ocurrencias + 1,
                        ultima_vez = CURRENT_TIMESTAMP,
                        estado = 'nuevo'";

            $stmt = $bd->prepare($sql);
            $stmt->execute([
                ':hash' => $hashError,
                ':tipo' => $tipoError,
                ':mensaje' => $mensaje,
                ':archivo' => $archivo,
                ':linea' => $linea,
                ':stack' => $stackTrace,
                ':contexto' => $contextoJson,
                ':usuario' => $usuarioId,
                ':rol' => $rolUsuario
            ]);

        } catch (\Throwable $th) {
            // Si el manejador de errores falla (ej. base de datos caída), loguear al archivo nativo de PHP
            error_log("Error crítico en ManejadorErrores: " . $th->getMessage());
            error_log("Error original: " . $mensaje);
        }
    }

    /**
     * Reemplaza las contraseñas por asteriscos en los arrays de estado.
     */
    private static function censurarDatosSensibles(array $datos): array {
        $clavesSensibles = ['password', 'contrasena', 'clave', 'token'];
        foreach ($datos as $key => $value) {
            if (is_array($value)) {
                $datos[$key] = self::censurarDatosSensibles($value);
            } else {
                foreach ($clavesSensibles as $sensible) {
                    if (stripos($key, $sensible) !== false) {
                        $datos[$key] = '******** (Censurado por Seguridad)';
                        break;
                    }
                }
            }
        }
        return $datos;
    }
}
