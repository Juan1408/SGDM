<?php
/**
 * ============================================================================
 * CLASE CONTROLADOR: JuegoControlador.php
 * ============================================================================
 * Propósito: Gestiona el catálogo de deportes, disciplinas y juegos electrónicos.
 * Ubicación: codigo_fuente/controladores/JuegoControlador.php
 * ============================================================================
 */

require_once __DIR__ . '/../ayudantes/Sesion.php';
require_once __DIR__ . '/../modelos/Juego.php';

class JuegoControlador {
    private Juego $juegoModelo;

    public function __construct() {
        Sesion::requerirAdmin();
        $this->juegoModelo = new Juego();
    }

    /**
     * Muestra el catálogo de disciplinas disponibles.
     * Ruta: index.php?c=juego&a=index
     */
    public function index(): void {
        $juegos = $this->juegoModelo->obtenerTodos();
        require_once __DIR__ . '/../vistas/admin/juegos/index.html';
    }
}
