<?php
namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use App\Modelos\Conexion;

abstract class TestCase extends BaseTestCase {
    protected $bd;

    //Este metodo se ejecuta automaticamente antes de cada test
    protected function setUp(): void {
        parent::setUp();

        //Obtenemos la conexion a la base de datos test (ascend_test)
        $this->bd = Conexion::getInstance()->getBD();

        //Vaciamos las tablas clave para tener un entorno limpio en cada test
        $this->bd->query("SET FOREIGN_KEY_CHECKS = 0;");
        $this->bd->query("TRUNCATE TABLE torneos");
        $this->bd->query("TRUNCATE TABLE juegos");
        $this->bd->query("TRUNCATE TABLE usuarios");
        $this->bd->query("SET FOREIGN_KEY_CHECKS = 1;");
    }
}

