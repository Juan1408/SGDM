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
require_once __DIR__ . '/../ayudantes/Sesion.php';


class AdminControlador {

    public function __construct(){
        //En el futuro aca vamos a bloquear el paso a los que no sean administradores
        //Por ahora solo verificamos que la persona esté logueada en el sistema
        Sesion::requerirLogin();
    }

    public function dashboard() {
        //Cargamos la vista del administrador
        require_once __DIR__ . '/../vistas/admin/dashboard.php';
    }
}