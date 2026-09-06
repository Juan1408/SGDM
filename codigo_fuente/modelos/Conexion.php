<?php

namespace App\Modelos; // Esto le dice a PHP que esta clase pertenece al espacio de nombres App\Modelos

use PDO; // PDO es la tecnologia de PHP para comunicarse con bases de datos
use PDOException; // PDOException es la tecnologia de PHP para manejar errores de PDO

class Conexion {
   //La instancia de esta clase sera guardada en esta variable
    private static $instancia = null;  
    
    //La conexionreal a MySQL
    private $pdo;

    //El constructor es privado para impedir la creacion de instancias directamente.
    private function __construct(){
        //Leemos el archivo de configuracion
        $config = require __DIR__ . '/../configuracion/base_de_datos.php';

        //Preparamos los parametros de la conexion
        $dns = 'mysql:host=' . $config['host'] . ";dbname=" . $config['bdnombre'] . ";charset=" . $config['charset'];
        
        //Intentamos la conexion aplicando try catch
        try {
            //creamos el objeto PDO y lo guardamos
            $this->pdo = new PDO($dns, $config['usuario'], $config['contrasena']);

            //COnfiguramos PDO para que nos avise si hay errores y no use emuladores por seguridad
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            //Si la base de datos esta caida, detenemos todo y mostramos el mensaje de error
            die("Error al conectar a la base de datos" . $e->getMessage());
        }
    }
               
    //El metodo publico y estatico que vamos usar en el proyecto para pedir la conexion
    public static function getInstance(){
        
        if (self::$instancia === null){
            self::$instancia = new self();
        }

        return self::$instancia;
    }

    //Un metodo simple para obtener el objeto PDO y hacer consultas
    public function getBD(){
        return $this->pdo;
    }
}