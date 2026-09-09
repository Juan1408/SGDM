<?php
namespace App\Controladores;

use App\Ayudantes\Sesion;
use App\Modelos\Guardado;
require_once __DIR__ . '/../ayudantes/Sesion.php';
require_once __DIR__ . '/../modelos/Guardado.php';

class GuardadosControlador {

    private Guardado $modeloGuardado;

    public function __construct(){
        Sesion::validarJugador();
        $this->modeloGuardado = new Guardado();
    }

    //Accion para mostrar la pantalla de Guardados
    public function index(){
        $usuarioId = $_SESSION['usuario_id'];

        $torneosGuardados = $this->modeloGuardado->obtenerTorneosGuardados($usuarioId);
        $equiposGuardados = $this->modeloGuardado->obtenerEquiposGuardados($usuarioId);

        require_once __DIR__ . '/../vistas/jugador/guardados.php';
    }

        //Accion para guardar un torneo o equipo (se llama desde otras pantallas, ej. perfil-equipo.php)
    public function guardar(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header("Location: " . URL_BASE . "index.php?c=guardados&a=index");
            exit;
        }

        $usuarioId = $_SESSION['usuario_id'];
        $tipo = $_POST['tipo'] ?? '';
        $referenciaId = (int) ($_POST['referencia_id'] ?? 0);
        $volverA = $_POST['volver_a'] ?? (URL_BASE . 'index.php?c=guardados&a=index');

        if (in_array($tipo, ['torneo', 'equipo'], true) && $referenciaId > 0) {
            $this->modeloGuardado->guardar($usuarioId, $tipo, $referenciaId);
        }

        header("Location: " . $volverA);
        exit;
    }

    //Accion para quitar un torneo o equipo de guardados
    public function quitar(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header("Location: " . URL_BASE . "index.php?c=guardados&a=index");
            exit;
        }

        $usuarioId = $_SESSION['usuario_id'];
        $tipo = $_POST['tipo'] ?? '';
        $referenciaId = (int) ($_POST['referencia_id'] ?? 0);

        $volverA = $_POST['volver_a'] ?? (URL_BASE . 'index.php?c=guardados&a=index');

        if (in_array($tipo, ['torneo', 'equipo'], true) && $referenciaId > 0) {
            $this->modeloGuardado->quitar($usuarioId, $tipo, $referenciaId);
        }

        header("Location: " . $volverA);
        exit;
    
}

}