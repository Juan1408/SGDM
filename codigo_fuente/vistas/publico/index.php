<?php
if (!defined('URL_BASE')) {
    require_once __DIR__ . '/../../configuracion/constantes.php';
}
?>
<?php
$torneosDestacados = $torneosDestacados ?? [];
$proximosEnfrentamientos = $proximosEnfrentamientos ?? [];
$estadisticas = $estadisticas ?? [];
$topJugadores = $topJugadores ?? [];

//Ruta de imagen: URL completa si viene con http(s), si no se antepone URL_BASE
$urlImagen = static function (?string $ruta, string $porDefecto): string {
   $ruta = trim((string) $ruta);
   if ($ruta === '') {
      return URL_BASE . $porDefecto;
   }
   return preg_match('#^https?://#i', $ruta) ? $ruta : URL_BASE . ltrim($ruta, '/');
};

$mesesCorto = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
$fechaCorta = static function (?string $fecha) use ($mesesCorto): string {
   if (empty($fecha)) {
      return 'A confirmar';
   }
   $marca = strtotime($fecha);
   return date('d', $marca) . ' ' . $mesesCorto[(int) date('n', $marca) - 1] . ' ' . date('Y', $marca);
};

$etiquetasFormato = ['liga' => 'Liga', 'suizo' => 'Sistema suizo', 'eliminacion_directa' => 'Eliminación directa'];
$etiquetasEstado = ['inscripciones_abiertas' => 'Inscripciones abiertas', 'en_curso' => 'En curso'];

//Categoria del juego (admin) a la etiqueta de las tarjetas, igual que en el catalogo de juegos
$categoriaTexto = static function (?string $categoria): string {
   $c = strtolower((string) $categoria);
   if ($c === 'esport') {
      return 'eSports';
   }
   return strpos($c, 'deporte') === 0 ? 'Tradicional' : 'De mesa';
};

//Icono y texto del canal del partido: plataforma de la transmision, o la cancha / ubicacion
$canalPartido = static function (array $p): array {
   $stream = trim((string) ($p['stream_url'] ?? ''));
   if (preg_match('#^https?://#i', $stream)) {
      $host = strtolower((string) parse_url($stream, PHP_URL_HOST));
      $plataformas = [
         'twitch' => ['fa-brands fa-twitch', 'Twitch'],
         'youtu' => ['fa-brands fa-youtube', 'YouTube'],
         'discord' => ['fa-brands fa-discord', 'Discord'],
         'facebook' => ['fa-brands fa-facebook', 'Facebook'],
         'tiktok' => ['fa-brands fa-tiktok', 'TikTok'],
      ];
      foreach ($plataformas as $clave => $datos) {
         if (strpos($host, $clave) !== false) {
            return $datos;
         }
      }
      return ['fa-solid fa-tv', 'Transmisión'];
   }
   $lugar = trim((string) ($p['cancha'] ?? '')) ?: trim((string) ($p['ubicacion'] ?? ''));
   return ['fa-solid fa-location-dot', $lugar !== '' ? $lugar : 'A definir'];
};
?>
<!DOCTYPE html>
<html lang="es">

<head>

   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title>ASCEND</title>

   <!-- CSS -->
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/navbar.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/footer.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/carruseles.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/publico/home.css">

   <!-- Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

   <!-- Google Fonts -->
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
      rel="stylesheet">

</head>

<body>

   <!-- NAVBAR ---------------------------->

   <?php include __DIR__ . '/../componentes/navbar-publico.php'; ?>

   <main>

      <!-------------------------------------->
      <!-- HERO ------------------------------>
      <!-------------------------------------->

      <section class="hero reveal">

         <div class="hero-slider">

            <div class="hero-slide active">
               <img src="<?php echo URL_BASE; ?>img/banners/header01.jpg" alt="">
            </div>

            <div class="hero-slide">
               <img src="<?php echo URL_BASE; ?>img/banners/header2.jpg" alt="">
            </div>

            <div class="hero-slide">
               <img src="<?php echo URL_BASE; ?>img/banners/header3.jpg" alt="">
            </div>
         </div>

         <div class="hero-overlay"></div>

         <div class="hero-contenido">

            <p class="hero-subtitulo">
               Sistema de Gestión de Deportes y eSports
            </p>

            <h1>
               Organiza y <br>Gestiona Torneos<br>
            </h1>

            <p class="hero-descripcion">
               Administra competiciones, rankings, participantes y resultados
               desde una sola plataforma moderna.
            </p>

            <div class="hero-botones">

               <a href="<?php echo URL_BASE; ?>index.php?c=torneo&a=catalogoPublico" class="primary-btn">
                  <span>Explorar Torneos</span>
               </a>

               <a href="<?php echo URL_BASE; ?>index.php?c=auth&a=mostrarRegistro" class="secondary-btn">
                  Comenzar
               </a>

            </div>
         </div>
      </section>

      <!-------------------------------------->
      <!-- TARJETAS -------------------------->
      <!-------------------------------------->

      <section class="categoria-tarjetas reveal" aria-label="Categorías de torneos">

         <ul class="categoria-tarjetas-lista">

            <li>
               <article class="tarjetas">
                  <i class="fa-solid fa-chess-board"></i>
                  <h2>Juegos de Mesa</h2>
                  <p>Participá en torneos de ajedrez, damas, cartas y otros juegos estratégicos.</p>
               </article>
            </li>

            <li>
               <article class="tarjetas tarjeta-activa">
                  <i class="fa-solid fa-gamepad"></i>
                  <h2>eSports</h2>
                  <p>Competencias online de videojuegos, equipos, rankings y resultados.</p>
               </article>
            </li>

            <li>
               <article class="tarjetas">
                  <i class="fa-solid fa-trophy"></i>
                  <h2>Juegos Tradicionales</h2>
                  <p>Organizá torneos de deportes, juegos clásicos y competencias presenciales.</p>
               </article>
            </li>

         </ul>

      </section>

      <!-------------------------------------->
      <!-- FUNCIONALIDADES ------------------->
      <!-------------------------------------->

      <section class="funcionalidades reveal">

         <div class="funcionalidades-imagen">
            <img src="<?php echo URL_BASE; ?>img/bg.jpg" alt="Funcionalidades Ascend">
         </div>

         <div class="funcionalidades-contenido">

            <p class="caracteristica-tag"># FUNCIONALIDADES DE ASCEND</p>

            <h2>
               Gestioná todo el torneo
               <span>desde un solo lugar</span>
            </h2>

            <ul class="funcionalidades-slider">

               <li class="funcionalidades-item">
                  <div class="caracteristica-icon cyan">
                     <i class="fa-solid fa-trophy"></i>
                  </div>
                  <div>
                     <h3>Crear torneos</h3>
                     <p>Configurá torneos de liga, eliminación directa o sistema suizo.</p>
                  </div>
               </li>

               <li class="funcionalidades-item">
                  <div class="caracteristica-icon pink">
                     <i class="fa-solid fa-users"></i>
                  </div>
                  <div>
                     <h3>Gestionar participantes</h3>
                     <p>Registrá jugadores, equipos e inscripciones de forma ordenada.</p>
                  </div>
               </li>

               <li class="funcionalidades-item">
                  <div class="caracteristica-icon cyan">
                     <i class="fa-solid fa-calendar-days"></i>
                  </div>
                  <div>
                     <h3>Calendario de partidas</h3>
                     <p>Mostrá fechas, horarios, rondas y próximos enfrentamientos.</p>
                  </div>
               </li>

               <li class="funcionalidades-item">
                  <div class="caracteristica-icon pink">
                     <i class="fa-solid fa-chart-simple"></i>
                  </div>
                  <div>
                     <h3>Resultados y posiciones</h3>
                     <p>Cargá resultados y generá tablas, rankings o llaves.</p>
                  </div>
               </li>

            </ul>

         </div>

      </section>


      <!-------------------------------------->
      <!-- TORNEOS DESTACADOS ---------------->
      <!-------------------------------------->

      <section class="torneos-destacados reveal">

         <div class="seccion-titulo">

            <h2>Torneos Destacados</h2>

            <a href="<?php echo URL_BASE; ?>index.php?c=torneo&a=catalogoPublico">
               Ver todos
            </a>

         </div>

         <div class="torneos-slider">
            <ul class="torneos-carrusel" mask>

               <?php if (empty($torneosDestacados)): ?>
                  <li><p>Todavía no hay torneos con inscripciones abiertas.</p></li>
               <?php endif; ?>

               <?php foreach ($torneosDestacados as $t): ?>
               <li>
                  <a href="<?php echo URL_BASE; ?>index.php?c=torneo&a=detalle&id=<?php echo (int) $t['id']; ?>" class="torneo-tarjeta-enlace">
                     <article class="torneo-tarjeta">
                        <img src="<?php echo htmlspecialchars($urlImagen($t['banner_url'], 'img/torneos/card1.jpg')); ?>" alt="<?php echo htmlspecialchars($t['nombre']); ?>">
                        <div class="tarjeta-contenido">
                           <h3><?php echo htmlspecialchars($t['nombre']); ?></h3>
                           <div class="tarjeta-meta">
                              <span>
                                 <i class="fa-solid fa-gamepad"></i>
                                 <?php echo htmlspecialchars($categoriaTexto($t['categoria_juego'])); ?>
                              </span>
                              <span>
                                 <i class="fa-solid fa-users"></i>
                                 <?php echo (int) $t['cupo_max_equipos']; ?> cupos
                              </span>
                              <span>
                                 <i class="fa-solid fa-trophy"></i>
                                 <?php echo htmlspecialchars($etiquetasEstado[$t['estado']] ?? $t['estado']); ?>
                              </span>
                           </div>
                           <div class="tarjeta-tipo">
                              <i class="fa-solid fa-diagram-project"></i>
                              <p><?php echo htmlspecialchars($etiquetasFormato[$t['formato']] ?? $t['formato']); ?></p>
                           </div>
                           <div class="tarjeta-fecha">
                              <?php echo htmlspecialchars($fechaCorta($t['fecha_inicio']) . ' - ' . $fechaCorta($t['fecha_fin'])); ?>
                           </div>
                        </div>
                     </article>
                  </a>
               </li>
               <?php endforeach; ?>
            </ul>
         </div>
      </section>

      <!-------------------------------------->
      <!-- AGENDA DEL DÍA -------------------->
      <!-------------------------------------->

      <section class="calendario-vs reveal">

         <div class="agenda-header">
            <p class="seccion-subtitulo"># CALENDARIO</p>
            <h2>Próximos Enfrentamientos</h2>
         </div>

         <ul class="vs-lista">

            <?php if (empty($proximosEnfrentamientos)): ?>
               <li><p>Todavía no hay enfrentamientos programados.</p></li>
            <?php endif; ?>

            <?php foreach ($proximosEnfrentamientos as $p): ?>
               <?php [$iconoCanal, $textoCanal] = $canalPartido($p); ?>
               <li>
                  <a href="<?php echo URL_BASE; ?>index.php?c=torneo&a=detalle&id=<?php echo (int) $p['torneo_id']; ?>" class="enfrentamiento-tarjeta">

                     <div class="enfrentamiento-equipo">
                        <img src="<?php echo htmlspecialchars($urlImagen($p['local_imagen'], 'img/avatars/a1.png')); ?>" alt="<?php echo htmlspecialchars($p['local_nombre']); ?>" class="enfrentamiento-logo">
                        <span class="enfrentamiento-nombre"><?php echo htmlspecialchars($p['local_nombre']); ?></span>
                     </div>

                     <div class="enfrentamiento-centro">
                        <span class="enfrentamiento-vs">Vs</span>
                        <span class="enfrentamiento-fecha"><?php echo htmlspecialchars($fechaCorta($p['fecha_hora_programada']) . ' · ' . date('H:i', strtotime($p['fecha_hora_programada']))); ?></span>
                        <span class="enfrentamiento-canal">
                           <i class="<?php echo $iconoCanal; ?>"></i> <?php echo htmlspecialchars($textoCanal); ?>
                        </span>
                     </div>

                     <div class="enfrentamiento-equipo">
                        <img src="<?php echo htmlspecialchars($urlImagen($p['visitante_imagen'], 'img/avatars/a1.png')); ?>" alt="<?php echo htmlspecialchars($p['visitante_nombre']); ?>" class="enfrentamiento-logo">
                        <span class="enfrentamiento-nombre"><?php echo htmlspecialchars($p['visitante_nombre']); ?></span>
                     </div>

                  </a>
               </li>
            <?php endforeach; ?>
         </ul>
      </section>

      <!-------------------------------------->
      <!--COMUNIDA Y NOTICIAS----------------->
      <!-------------------------------------->

      <section class="comunidad-y-noticias reveal">

         <div class="comunidad-contenido">
            <span># COMUNIDAD ASCEND</span>
            <h2>
               Enterate de las novedades
               <strong>y conectá con jugadores</strong>
            </h2>
            <p>
               Seguí noticias de torneos, anuncios importantes, resultados destacados
               y actividades de la comunidad ASCEND.
            </p>
            <a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=enConstruccion">Ver comunidad</a>
         </div>

         <ul class="noticias-grid">

            <li>
               <article class="noticias-tarjeta noticias-nuevas">
                  <img src="<?php echo URL_BASE; ?>img/04.jpg" alt="Noticia">
                  <div>
                     <span>Noticias</span>
                     <h3>Nuevo torneo de Valorant disponible</h3>
                     <p>Inscripciones abiertas para equipos de LATAM.</p>
                  </div>
               </article>
            </li>

            <li>
               <article class="noticias-tarjeta">
                  <span>Comunidad</span>
                  <h3>Top jugadores de la semana</h3>
                  <p>Conocé los perfiles más destacados.</p>
               </article>
            </li>

            <li>
               <article class="noticias-tarjeta">
                  <span>Eventos</span>
                  <h3>Calendario de partidas actualizado</h3>
                  <p>Nuevas fechas agregadas al sistema.</p>
               </article>
            </li>

         </ul>

      </section>

      <!-------------------------------------->
      <!-- ESTADÍSTICAS ---------------------->
      <!-------------------------------------->

      <section class="estadisticas-banner reveal">

         <div class="stats-overlay"></div>

         <dl class="estadisticas-grid">

            <?php foreach ([['usuarios', 'Usuarios'], ['encuentros', 'Encuentros'], ['torneos', 'Torneos'], ['equipos', 'Equipos']] as [$clave, $etiqueta]): ?>
            <div class="estadisticas-item">
               <dd><?php echo number_format((int) ($estadisticas[$clave] ?? 0), 0, ',', '.'); ?></dd>
               <dt><?php echo $etiqueta; ?></dt>
            </div>
            <?php endforeach; ?>

         </dl>

      </section>

      <!-------------------------------------->
      <!-- TOP JUGADORES --------------------->
      <!-------------------------------------->
   <?php if (!empty($topJugadores)): ?>
      <section class="top-jugadores reveal">

         <div class="top-jugadores-header">
            <span>Ranking de jugadores</span>
            <h2>Top Jugadores</h2>
            <p>Competidores destacados por rendimiento, partidas ganadas y participación.</p>
         </div>

         <ul class="top-jugadores-fila">

                     <?php foreach ($topJugadores as $i => $jug): ?>
            <li>
               <article class="top-jugador-tarjeta <?php echo $i % 2 === 0 ? 'drop-down' : 'drop-up'; ?>">
                  <img
                     src="<?php echo htmlspecialchars($urlImagen($jug['imagen'], 'img/avatars/a1.png')); ?>"
                     alt="<?php echo htmlspecialchars($jug['nombre']); ?>"
                     class="jugador-img">

                  <div class="jugador-info">
                     <h3><?php echo htmlspecialchars($jug['nombre']); ?></h3>
                     <p><i class="fa-solid fa-trophy"></i> <?php echo (int) $jug['victorias']; ?> <?php echo (int) $jug['victorias'] === 1 ? 'victoria' : 'victorias'; ?></p>
                  </div>
               </article>
            </li>
            <?php endforeach; ?>
            
         </ul>
      </section>
   <?php endif; ?>

      <!-------------------------------------->
      <!-- NEWSLETTER ------------------------->
      <!-------------------------------------->

      <section class="newsletter-seccion reveal">
         <div class="newsletter-caja">
            <h2>Boletín de Noticias ASCEND</h2>
            <form class="newsletter-formulario" onsubmit="event.preventDefault();">
               <input type="email" placeholder="Introduce tu correo electrónico" required>
               <button type="submit">Suscribirse</button>
            </form>
         </div>
      </section>

   </main>

   <!-- FOOTER ---------------------------->

   <?php include __DIR__ . '/../componentes/footer.php'; ?>


   <script src="<?php echo URL_BASE; ?>js/scroll-reveal.js"></script>
   <script src="<?php echo URL_BASE; ?>js/preloader.js"></script>
   <script src="<?php echo URL_BASE; ?>js/navbar.js"></script>
   <script>document.dispatchEvent(new Event("navbarPublicoListo"));</script>
   <script src="<?php echo URL_BASE; ?>js/hero-slider.js"></script>
   <script src="<?php echo URL_BASE; ?>js/featured-games.js"></script>

</body>

</html>
