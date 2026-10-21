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
        require_once __DIR__ . '/../modelos/Torneo.php';
        $modeloTorneo = new \App\Modelos\Torneo();
        $torneosDestacados = $modeloTorneo->obtenerTorneosDestacados(6);
        $proximosEnfrentamientos = $modeloTorneo->obtenerProximosEnfrentamientos(4);
        $estadisticas = $modeloTorneo->obtenerEstadisticasPublicas();
        $topJugadores = $modeloTorneo->obtenerTopJugadores(4);
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
        require_once __DIR__ . '/../modelos/Juego.php';
        $juegos = (new \Juego())->obtenerJuegosPublicos();
        require_once __DIR__ . '/../vistas/publico/juegos.php';
    }

    //Pagina institucional "Nosotros"
    public function nosotros(){
        require_once __DIR__ . '/../vistas/publico/nosotros.php';
    }

    //Ranking por disciplina (los puntos de cada juego no son comparables entre si)
    public function rankings(){
        require_once __DIR__ . '/../modelos/ResultadoGlobal.php';
        $modeloRanking = new \App\Modelos\ResultadoGlobal();

        $juegosRanking = $modeloRanking->obtenerJuegosConRanking();

        require_once __DIR__ . '/../modelos/Juego.php';
        $juegosDestacados = (new \Juego())->obtenerJuegosDestacados(6);

        //Disciplina elegida (?juego=ID); si no hay o no es valida, la primera con resultados
        $juegoId = (int) ($_GET['juego'] ?? 0);
        $juegoSeleccionado = null;
        foreach ($juegosRanking as $j) {
            if ((int) $j['id'] === $juegoId) { $juegoSeleccionado = $j; break; }
        }
        if ($juegoSeleccionado === null && !empty($juegosRanking)) {
            $juegoSeleccionado = $juegosRanking[0];
        }

        $rankingJugadores = [];
        $rankingEquipos = [];
        if ($juegoSeleccionado !== null) {
            $rankingJugadores = $modeloRanking->obtenerRankingPorJuego((int) $juegoSeleccionado['id'], 'usuario');
            $rankingEquipos = $modeloRanking->obtenerRankingPorJuego((int) $juegoSeleccionado['id'], 'equipo');
        }

        require_once __DIR__ . '/../vistas/publico/rankings.php';
    }
}
