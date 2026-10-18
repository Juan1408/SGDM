<?php
namespace App\Controladores;

/**
 * Controlador publico general (Inicio, Contacto, Juegos, Nosotros, Rankings,
 * Perfil Publico Organizador, En Construccion). Paso 1: solo pasa las vistas
 * por el router, sin traer datos reales todavia.
 */
class InicioControlador {

    public function __construct(){
        //Sin restriccion de sesion: estas paginas son publicas
    }

     //Pagina de inicio
    public function index(){
        require_once __DIR__ . '/../vistas/publico/index.php';
    }

    //Pantalla generica para secciones que todavia no estan programadas
    public function enConstruccion(){
        require_once __DIR__ . '/../vistas/publico/en-construccion.php';
    }

     //Pagina de contacto
    public function contacto(){
        require_once __DIR__ . '/../vistas/publico/contacto.php';
    }

    //Catalogo de juegos
    public function juegos(){
        require_once __DIR__ . '/../vistas/publico/juegos.php';
    }

    //Pagina institucional "Nosotros"
    public function nosotros(){
        require_once __DIR__ . '/../vistas/publico/nosotros.php';
    }

    //Ranking general
    public function rankings(){
        require_once __DIR__ . '/../vistas/publico/rankings.php';
    }
}
