<?php
namespace App\Controladores;
use App\Ayudantes\Sesion;
use App\Modelos\Juego;

require_once __DIR__ . '/../modelos/Juego.php';
require_once __DIR__ . '/../modelos/Formato.php';
require_once __DIR__ . '/../modelos/Modalidad.php';
require_once __DIR__ . '/../ayudantes/Sesion.php';

class JuegoControlador {
    private $modeloJuego;
    
    public function __construct(){
        
        Sesion::validarAdmin();
        $this->modeloJuego = \Juego::obtenerInstancia();
    }

    //Modelo de lista de todos los juegos
    public function index(){
        $juegos = $this->modeloJuego->obtenerTodosLosJuegos();
        
        //Preparamos la variable que inyecta los datos en el dashboard
        $vistaInyectada = 'juegos/juegos_index.php';
        require_once __DIR__ . '/../vistas/admin/dashboard.php';
    }

    //Metodo para mostrar el formulario de crear
    public function crear(){
        $modeloModalidad = new \App\Modelos\Modalidad();
        $modalidades = $modeloModalidad->obtenerTodas();
        
        $modeloFormato = new \App\Modelos\Formato();
        $formatos = $modeloFormato->obtenerTodas();
        
        $vistaInyectada = 'juegos/juegos_crear.php';
        require_once __DIR__ . '/../vistas/admin/dashboard.php';
    }

    //Procesamos el formulario de cuando se crea el nuevo juego
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            \App\Ayudantes\Sesion::requerirTokensCSRF();
            //Recoleccion de datos
            $nombre = trim($_POST['nombre'] ?? '');
            $categoria = trim($_POST['categoria'] ?? '');
            
            $pts_vic = isset($_POST['puntos_victoria']) ? (float)$_POST['puntos_victoria'] : 3.00;
            $pts_emp = isset($_POST['puntos_empate']) ? (float)$_POST['puntos_empate'] : 1.00;
            $pts_der = isset($_POST['puntos_derrota']) ? (float)$_POST['puntos_derrota'] : 0.00;
            
            $modalidades = $_POST['modalidades'] ?? [];
            $formatos = $_POST['formatos'] ?? [];

            //Validacion simple de datos, para la entrega final la seguimos mejorando
            if (!empty($nombre) && !empty($categoria)){
                //Llamamos al modelo
                if ($this->modeloJuego->crearJuego($nombre, $categoria, $pts_vic, $pts_emp, $pts_der, $modalidades, $formatos)){
                    $_SESSION['mensaje'] = "Juego creado exitosamente y añadido al catalogo";
                } else {
                    $_SESSION['mensaje'] = "Error al guardar el juego";
                }    
            }else{
                $_SESSION['mensaje'] = "Todos los campos son obligatorios";
            }

            //Redirigimos al usuario a la lista de juegos
            header("Location: " . URL_BASE . "index.php?c=juego&a=index");
            exit;
        }
    }

    //Metodo para cambiar el estado de un juego (Baja logica)
    public function cambiarEstado() {
        if (isset($_GET['id'])){
            $juegoId = (int)$_GET['id'];
            if ($this->modeloJuego->cambiarEstado($juegoId)){
                $_SESSION['mensaje'] = "Estado del juego actualizado";
            }else{
                $_SESSION['mensaje'] = "Error al actualizar el estado del juego";
            }
        }else{
            $_SESSION['mensaje'] = "ID del juego no proporcionado";
        }
        
        //Redirigimos al usuario a la lista de juegos
        header("Location: " . URL_BASE . "index.php?c=juego&a=index");
        exit;
    }

    //Metodo para mostrar el formulario de editar
    public function editar(){
        if (isset($_GET['id'])){
            $juegoId = (int)$_GET['id'];
            $juego = $this->modeloJuego->obtenerJuegoPorId($juegoId);
            if ($juego){
                $modeloModalidad = new \App\Modelos\Modalidad();
                $todas_modalidades = $modeloModalidad->obtenerTodas();
                $permitidas = $this->modeloJuego->obtenerModalidadesPermitidas($juegoId);
                $ids_permitidas = array_column($permitidas, 'id');
                
                $modeloFormato = new \App\Modelos\Formato();
                $todos_formatos = $modeloFormato->obtenerTodas();
                $formatos_permitidos = $this->modeloJuego->obtenerFormatosPermitidos($juegoId);
                $ids_formatos_permitidos = array_column($formatos_permitidos, 'codigo');

                $vistaInyectada = 'juegos/juegos_editar.php';
                require_once __DIR__ . '/../vistas/admin/dashboard.php';
            }else{
                $_SESSION['mensaje'] = "Juego no encontrado";
                header("Location: " . URL_BASE . "index.php?c=juego&a=index");
                exit;
            }
        }else{
            $_SESSION['mensaje'] = "ID del juego no proporcionado";
            header("Location: " . URL_BASE . "index.php?c=juego&a=index");
            exit;
        }
    }

    //Metodo para procesar el formulario de editar
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            \App\Ayudantes\Sesion::requerirTokensCSRF();
            //Recoleccion de datos
            $juegoId = (int)$_POST['id'];
            $nombre = trim($_POST['nombre'] ?? '');
            $categoria = trim($_POST['categoria'] ?? '');
            
            $pts_vic = isset($_POST['puntos_victoria']) ? (float)$_POST['puntos_victoria'] : 3.00;
            $pts_emp = isset($_POST['puntos_empate']) ? (float)$_POST['puntos_empate'] : 1.00;
            $pts_der = isset($_POST['puntos_derrota']) ? (float)$_POST['puntos_derrota'] : 0.00;
            
            $modalidades = $_POST['modalidades'] ?? [];
            $formatos = $_POST['formatos'] ?? [];

            //Validacion simple de datos, para la entrega final la seguimos mejorando
            if (!empty($nombre) && !empty($categoria)){
                //Llamamos al modelo
                if ($this->modeloJuego->actualizarJuego($juegoId, $nombre, $categoria, $pts_vic, $pts_emp, $pts_der, $modalidades, $formatos)){
                    $_SESSION['mensaje'] = "Juego actualizado exitosamente";
                } else {
                    $_SESSION['mensaje'] = "Error al actualizar el juego";
                }
            }else{
                $_SESSION['mensaje'] = "Todos los campos son obligatorios";
            }

            //Redirigimos al usuario a la lista de juegos
            header("Location: " . URL_BASE . "index.php?c=juego&a=index");
            exit;
        }
    }
}