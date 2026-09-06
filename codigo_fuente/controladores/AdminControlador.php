<?php
/**
 * ============================================================================
 * CLASE CONTROLADOR: AdminControlador.php
 * ============================================================================
 * Propósito: Gestiona las pantallas principales del Administrador General,
 *            incluyendo el Dashboard de métricas, estadísticas y resumen del sistema.
 * Ubicación: codigo_fuente/controladores/AdminControlador.php
 * ============================================================================
 */

namespace App\Controladores;

//Importamos el ayudante de seguridad
use App\Ayudantes\Sesion;
use App\Modelos\Usuario;
use App\Modelos\Torneo;
use App\Modelos\Equipo;

//Importamos las clases necesarias
require_once __DIR__ . '/../ayudantes/Sesion.php';
require_once __DIR__ . '/../modelos/Usuario.php';
require_once __DIR__ . '/../modelos/Torneo.php';
require_once __DIR__ . '/../modelos/Equipo.php';

class AdminControlador {

    public function __construct(){
        //En el futuro aca vamos a bloquear el paso a los que no sean administradores
        //Por ahora solo verificamos que la persona esté logueada en el sistema
        Sesion::validarAdmin();
    }

    public function dashboard() {

        //Pasamos el total de registros de cada tabla a la vista
        $datos = [
            'totalOrganizadores' => $this->obtenerOrganizadores(),
            'totalTorneos' => $this->obtenerTorneos(),
            'totalJugadores' => $this->obtenerJugadores(),
            'totalEquipos' => $this->obtenerEquipos(),

            //Pasamos el listado de los ultimos registros de cada modelo
            'ultimosTorneos' => (new Torneo())->obtenerUltimosTorneosCreados(5),
            'ultimosEquipos' => $this->ultimosEquipos(5),
            'ultimosOrganizadores' => $this->obtenerUltimosOrganizadores(5),
            'ultimosJugadores' => $this->obtenerUltimosJugadores(5),
            
        ];
        //Cargamos la vista del administrador
        $vistaInyectada = 'inicio.php';
        require_once __DIR__ . '/../vistas/admin/dashboard.php';
    }

    private function obtenerTorneos(){
        //Instanciamos el modelo para poder usarlo
        $modeloTorneo = new Torneo();

        //Le pedimos que cuente todos los torneos
        $totalTorneos = $modeloTorneo->contarTorneosActivos();

        return $totalTorneos;
    }

    private function obtenerJugadores(){
        //Instanciamos el modelo para poder usarlo
        $modeloUsuario = new Usuario();
        
        //Le pedimos que cuente todos los usuarios
        $totalJugadores = $modeloUsuario->contarTotalJugadoresRegistrados();

        return $totalJugadores;
    }

    private function obtenerOrganizadores(){
        $modeloUsuarios = new Usuario();
        $totalOrganizadores = $modeloUsuarios->contarTotalOrganizadoresRegistrados();

        return $totalOrganizadores;
    }

    private function obtenerEquipos(){
        //Instanciamos el modelo
        
        $modeloEquipo = Equipo::obtenerInstancia();

        //Le pedimos que cuente todos los equipos
        $totalEquipos = $modeloEquipo->contarTotalEquipos();

        return $totalEquipos;
    }

    

    private function ultimosEquipos(int $limite = 5){
        //Instanciamos el modelo
        
        $modeloEquipo = Equipo::obtenerInstancia();

        //Le pedimos que nos devuelva los ultimos equipos
        $ultimosEquipos = $modeloEquipo->obtenerUltimosEquiposCreados($limite);

        return $ultimosEquipos;
    }

    public function obtenerUltimosJugadores(int $limite){
        $modeloUsuario = new Usuario();
        $ultimosJugadores = $modeloUsuario->obtenerUltimosJugadoresRegistrados($limite);
        return $ultimosJugadores;
    }

    public function obtenerUltimosOrganizadores(int $limite){
        $modeloUsuario = new Usuario();
        $ultimosOrganizadores = $modeloUsuario->obtenerUltimosOrganizadoresRegistrados($limite);
        return $ultimosOrganizadores;
    }
}