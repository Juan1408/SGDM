<?php
namespace App\Controladores;

/**
 * Controlador publico general (Inicio, Contacto, Juegos, Nosotros, Rankings,
 * Terminos y Condiciones, Politica de Privacidad, Formatos de torneo,
 * En Construccion).
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
        $ultimosComentarios = $modeloTorneo->obtenerUltimosComentariosComunidad(1);
        require_once __DIR__ . '/../vistas/publico/index.php';
    }

    //Suscripcion al boletin de la home (formulario POST con proteccion CSRF)
    public function suscribirNewsletter(){
        $destino = URL_BASE . 'index.php?c=inicio&a=index#newsletter';

        //Pagina a la que se vuelve (el formulario tambien esta en el detalle de torneo).
        //Solo se aceptan rutas internas "index.php?..." para evitar redirecciones a otros sitios.
        $volver = trim((string) ($_POST['volver'] ?? ''));
        if (preg_match('/^index\.php\?[A-Za-z0-9_=&%.\-]+(#[A-Za-z0-9_\-]+)?$/', $volver)) {
            $destino = URL_BASE . $volver;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $destino);
            exit;
        }

        \App\Ayudantes\Sesion::requerirTokensCSRF();

        $email = strtolower(trim((string) ($_POST['email'] ?? '')));

        if ($email === '' || strlen($email) > 255 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['newsletter_error'] = 'Ingresá un correo electrónico válido.';
            header('Location: ' . $destino);
            exit;
        }

        try {
            require_once __DIR__ . '/../modelos/Newsletter.php';
            $resultado = (new \App\Modelos\Newsletter())->suscribir($email);
        } catch (\Throwable $th) {
            error_log('Error al suscribir al newsletter: ' . $th->getMessage());
            $_SESSION['newsletter_error'] = 'No pudimos registrar tu suscripción. Probá de nuevo en unos minutos.';
            header('Location: ' . $destino);
            exit;
        }

        $_SESSION['newsletter_exito'] = $resultado === 'ya_suscripto'
            ? 'Ese correo ya estaba suscripto al boletín. ¡Gracias por seguir con nosotros!'
            : '¡Listo! Te suscribiste al boletín de ASCEND.';
        header('Location: ' . $destino);
        exit;
    }

    //Envio del formulario de contacto (POST + CSRF). El mensaje le llega a los administradores como notificacion.
    public function enviarContacto(){
        $destino = URL_BASE . 'index.php?c=inicio&a=contacto#formulario-contacto';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $destino);
            exit;
        }

        \App\Ayudantes\Sesion::requerirTokensCSRF();

        //Campo trampa para bots: un usuario real no lo ve y lo deja vacio
        if (trim((string) ($_POST['sitio_web'] ?? '')) !== '') {
            $_SESSION['contacto_exito'] = '¡Gracias! Recibimos tu mensaje.';
            header('Location: ' . $destino);
            exit;
        }

        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $mensaje = trim((string) ($_POST['mensaje'] ?? ''));
        $largo = static function (string $t): int {
            return function_exists('mb_strlen') ? mb_strlen($t, 'UTF-8') : strlen($t);
        };

        //Se conserva lo escrito para que no tenga que volver a tipearlo si hay un error
        $_SESSION['contacto_viejo'] = ['nombre' => $nombre, 'email' => $email, 'mensaje' => $mensaje];

        $error = null;
        if ($largo($nombre) < 2 || $largo($nombre) > 100) {
            $error = 'Escribí tu nombre (entre 2 y 100 caracteres).';
        } elseif ($email === '' || strlen($email) > 255 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Ingresá un correo electrónico válido.';
        } elseif ($largo($mensaje) < 10) {
            $error = 'El mensaje tiene que tener al menos 10 caracteres.';
        } elseif ($largo($mensaje) > 2000) {
            $error = 'El mensaje no puede superar los 2000 caracteres.';
        } elseif (isset($_SESSION['contacto_ultimo_envio']) && (time() - (int) $_SESSION['contacto_ultimo_envio']) < 30) {
            $error = 'Esperá unos segundos antes de enviar otro mensaje.';
        }

        if ($error !== null) {
            $_SESSION['contacto_error'] = $error;
            header('Location: ' . $destino);
            exit;
        }

        //Freno contra spam por sesion: maximo 5 mensajes por hora
        $ahora = time();
        $envios = array_values(array_filter(
            (array) ($_SESSION['contacto_envios'] ?? []),
            static function ($marca) use ($ahora) { return ($ahora - (int) $marca) < 3600; }
        ));
        if (count($envios) >= 5) {
            $_SESSION['contacto_error'] = 'Enviaste muchos mensajes seguidos. Probá de nuevo más tarde.';
            header('Location: ' . $destino);
            exit;
        }

        try {
            require_once __DIR__ . '/../modelos/MensajeContacto.php';
            (new \App\Modelos\MensajeContacto())->enviarAAdministradores($nombre, $email, $mensaje);
        } catch (\Throwable $th) {
            error_log('Error al guardar un mensaje de contacto: ' . $th->getMessage());
            $_SESSION['contacto_error'] = 'No pudimos enviar tu mensaje. Probá de nuevo en unos minutos.';
            header('Location: ' . $destino);
            exit;
        }

        unset($_SESSION['contacto_viejo'], $_SESSION['contacto_error']);
        $_SESSION['contacto_ultimo_envio'] = time();
        $envios[] = $ahora;
        $_SESSION['contacto_envios'] = $envios;
        $_SESSION['contacto_exito'] = '¡Gracias! Recibimos tu mensaje y te vamos a responder por correo.';
        header('Location: ' . $destino);
        exit;
    }

    //Pantalla generica para secciones que todavia no estan programadas
    public function enConstruccion(){
        require_once __DIR__ . '/../vistas/publico/en-construccion.php';
    }

    //Terminos y Condiciones de uso: pagina publica, sin consultas a la base de datos
    public function terminos(){
        require_once __DIR__ . '/../vistas/publico/terminos.php';
    }

    //Politica de Privacidad: pagina publica, sin consultas a la base de datos
    public function privacidad(){
        require_once __DIR__ . '/../vistas/publico/privacidad.php';
    }

     //Formatos de torneo: pagina publica explicativa, sin consultas a la base de datos
    public function formatos(){
        require_once __DIR__ . '/../vistas/publico/formatos.php';
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
