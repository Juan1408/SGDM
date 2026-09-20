<?php
namespace App\Controladores;

use App\Ayudantes\Sesion;
use App\Modelos\ResultadoGlobal;

require_once __DIR__ . '/../ayudantes/Sesion.php';
require_once __DIR__ . '/../modelos/ResultadoGlobal.php';

class ResultadoGlobalControlador {
    private ResultadoGlobal $modeloResultado;

    public function __construct() {
        // Solo los administradores generales tienen acceso a este dashboard global analítico
        Sesion::validarAdmin();
        $this->modeloResultado = new ResultadoGlobal();
    }

    /**
     * Muestra la vista del Salón de la Fama agrupado por disciplina
     */
    public function index() {
        // Obtenemos los juegos que tienen torneos computables
        $juegos = $this->modeloResultado->obtenerJuegosConResultados();
        
        $salonesDeLaFama = [];
        
        // Iteramos cada disciplina para obtener su propio Top 10 real
        foreach ($juegos as $juego) {
            $top10 = $this->modeloResultado->obtenerTop10PorJuego($juego['id']);
            if (!empty($top10)) {
                $salonesDeLaFama[] = [
                    'juego_id' => $juego['id'],
                    'juego_nombre' => $juego['nombre'],
                    'juego_categoria' => $juego['categoria'],
                    'ranking' => $top10
                ];
            }
        }
        
        $vistaInyectada = 'resultados/globales.php';
        require_once __DIR__ . '/../vistas/admin/dashboard.php';
    }
}
