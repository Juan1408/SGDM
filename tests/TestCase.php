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
        // Limpiar las tablas puente: al truncar juegos con las FK desactivadas
        // quedan filas huérfanas que se "reenganchan" al reiniciarse el AUTO_INCREMENT
        // y hacen fallar los triggers de la migración 007.
        $this->bd->query("TRUNCATE TABLE juego_modalidades_permitidas");
        $this->bd->query("TRUNCATE TABLE juego_formatos_permitidos");
        // Tablas hijas por usuario: sin esto, al reiniciarse el AUTO_INCREMENT de
        // usuarios una fila huérfana podría "reengancharse" al siguiente usuario.
        $this->bd->query("TRUNCATE TABLE perfiles_jugadores");
        $this->bd->query("TRUNCATE TABLE perfiles_organizadores");
        $this->bd->query("TRUNCATE TABLE tokens_verificacion_email");
        $this->bd->query("TRUNCATE TABLE aceptaciones_legales");
        $this->bd->query("TRUNCATE TABLE logs_acceso");
        $this->bd->query("TRUNCATE TABLE sesiones_activas");
        $this->bd->query("TRUNCATE TABLE logs_actividad");
        $this->bd->query("TRUNCATE TABLE usuarios");
        $this->bd->query("TRUNCATE TABLE errores_sistema");
        $this->bd->query("TRUNCATE TABLE notificaciones");
        $this->bd->query("TRUNCATE TABLE auditoria_cambios");
        $this->bd->query("SET FOREIGN_KEY_CHECKS = 1;");
    }
}

