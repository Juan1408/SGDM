<?php
/**
 * Clase ConexionBD
 * 
 * Implementa el patrón Singleton para la conexión a la base de datos MySQL
 * utilizando la extensión PDO (PHP Data Objects).
 */
class ConexionBD {
    /**
     * @var PDO|null $instancia Instancia única de la conexión PDO.
     */
    private static ?PDO $instancia = null;

    /**
     * Constructor privado para evitar la creación de múltiples instancias.
     */
    private function __construct() {}

    /**
     * Clonador privado para evitar la duplicación de la instancia única.
     */
    private function __clone() {}

    /**
     * Obtiene la instancia única de la conexión PDO.
     *
     * @return PDO Retorna la conexión PDO activa.
     */
    public static function obtenerInstancia(): PDO {
        if (self::$instancia === null) {
            /**
             * @var array $configuracion Contiene las credenciales cargadas desde el archivo de configuración.
             */
            $configuracion = require __DIR__ . '/../configuracion/base_de_datos.php';

            /**
             * @var string $host Dirección del servidor de base de datos.
             * @var string $usuario Usuario de la base de datos.
             * @var string $clave Contraseña de la base de datos.
             * @var string $bd Nombre de la base de datos.
             */
            $host = $configuracion['host'];
            $usuario = $configuracion['user'];
            $clave = $configuracion['password'];
            $bd = $configuracion['database'];

            try {
                /**
                 * @var string $dsn Cadena de conexión (Data Source Name).
                 */
                $dsn = "mysql:host=$host;dbname=$bd;charset=utf8mb4";
                self::$instancia = new PDO($dsn, $usuario, $clave);
                self::$instancia->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instancia->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $error) {
                die("Error crítico al conectar con la base de datos: " . $error->getMessage());
            }
        }
        return self::$instancia;
    }
}
