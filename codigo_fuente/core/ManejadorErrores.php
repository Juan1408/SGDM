<?php

namespace App\Core;

use App\Modelos\Conexion;
use PDO;
use Throwable;

class ManejadorErrores {

    public static function capturarExcepcion(Throwable $e) {
        $tipoError = ($e instanceof \PDOException || strpos(strtolower($e->getMessage()), 'sql') !== false) ? 'base_de_datos' : 'codigo';
        self::registrarError($e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString(), $tipoError);
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
