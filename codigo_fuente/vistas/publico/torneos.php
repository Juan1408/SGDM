<?php
$etiquetasFormato = [
   'liga' => 'Liga',
   'suizo' => 'Sistema suizo',
   'eliminacion_directa' => 'Eliminación directa',
];
$etiquetasEstadoTorneo = [
   'inscripciones_abiertas' => 'Inscripciones abiertas',
   'en_curso' => 'En curso',
   'finalizado' => 'Finalizado',
];
$etiquetasEstadoFiltro = $etiquetasEstadoTorneo + ['proximos' => 'Próximos'];
$etiquetasLugar = ['online' => 'Online', 'presencial' => 'Presencial'];

//El select de juegos solo ofrece juegos que tienen torneos publicados
$juegosCatalogo = [];
foreach ($torneos as $torneoCatalogo) {
   $juegosCatalogo[(int) $torneoCatalogo['juego_id']] = $torneoCatalogo['nombre_juego'];
}
asort($juegosCatalogo);

//Categoria de la base -> chip del catalogo
$tipoDeJuego = static function (string $categoria): string {
   if ($categoria === 'esport' || strpos($categoria, 'esport-') === 0) { return 'esports'; }
   if ($categoria === 'deporte-fisico') { return 'tradicionales'; }
   if ($categoria === 'juego-mesa') { return 'mentales'; }
   return '';
};
?>

<!DOCTYPE html>
<html lang="es">

<head>

   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title>ASCEND</title>

   <!-- CSS -->
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/navbar.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/publico/home.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/footer.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/publico/torneo.css">

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


               <a href="#torneos" class="primary-btn">
                  <span>Explorar Torneos</span>
               </a>

               <a href="<?php echo URL_BASE; ?>index.php?c=auth&a=mostrarLogin" class="secondary-btn">
                  Comenzar
               </a>

            </div>
         </div>
      </section>

      <!-- CONTENIDO -->

      <section class="seccion-torneos">

         <div class="header-pagina">
            <p class="subtitulo-pagina">Explorá las competencias disponibles</p>
            <h1>Todos los Torneos</h1>
            <p>
               Encontrá torneos activos, próximos y finalizados. Filtrá por juego,
               tipo de competencia, región o estado.
            </p>
         </div>

         <div class="custom-shape-divider-bottom-1782780458">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
               <path d="M892.25 114.72L0 0 0 120 1200 120 1200 0 892.25 114.72z" class="shape-fill"></path>
            </svg>
         </div>

      </section>

      <!-------------------------------------->
      <!-- PANEL DE FILTRO ------------------->
      <!-------------------------------------->

         <section class="panel-filtros" aria-label="Filtros de búsqueda de torneos" id="torneos">

         <div class="top-fila">

            <ul class="chips-contenedor">
               <li><button type="button" class="chip active" data-tipo="todos">Todos</button></li>
               <li><button type="button" class="chip" data-tipo="esports">eSports</button></li>
               <li><button type="button" class="chip" data-tipo="tradicionales">Tradicionales</button></li>
               <li><button type="button" class="chip" data-tipo="mentales">Mentales</button></li>
            </ul>

            <div class="buscador">
               <i class="fa-solid fa-magnifying-glass"></i>
               <input type="text" id="buscar-torneo" placeholder="Buscar torneo, juego o lugar...">
            </div>

         </div>

         <div class="seleccionar-filtros">

            <select id="filtro-juego" aria-label="Juego">
               <option value="">Juego</option>
               <?php foreach ($juegosCatalogo as $juegoId => $juegoNombre): ?>
                  <option value="<?php echo (int) $juegoId; ?>"><?php echo htmlspecialchars($juegoNombre); ?></option>
               <?php endforeach; ?>
            </select>

            <select id="filtro-formato" aria-label="Formato">
               <option value="">Formato</option>
               <?php foreach ($etiquetasFormato as $codigo => $etiqueta): ?>
                  <option value="<?php echo $codigo; ?>"><?php echo $etiqueta; ?></option>
               <?php endforeach; ?>
            </select>

            <select id="filtro-lugar" aria-label="Lugar">
               <option value="">Lugar</option>
               <?php foreach ($etiquetasLugar as $codigo => $etiqueta): ?>
                  <option value="<?php echo $codigo; ?>"><?php echo $etiqueta; ?></option>
               <?php endforeach; ?>
            </select>

            <select id="filtro-estado" aria-label="Estado">
               <option value="">Estado</option>
               <?php foreach ($etiquetasEstadoFiltro as $codigo => $etiqueta): ?>
                  <option value="<?php echo $codigo; ?>"><?php echo $etiqueta; ?></option>
               <?php endforeach; ?>
            </select>

         </div>

      </section>

      <!-------------------------------------->
      <!------ TORNEOS ----------------------->
      <!-------------------------------------->

      <section class="torneos-pagina">

         <?php if (empty($torneos)): ?>
            <p class="lista-vacia">Todavía no hay torneos publicados.</p>
         <?php else: ?>
             <ul class="torneos-grid" id="torneos-grid">
               <?php foreach ($torneos as $torneo): ?>
                  <?php
                     $esOnline = strpos((string) $torneo['ubicacion'], 'Online') === 0;
                     $esProximo = !empty($torneo['fecha_inicio']) && $torneo['fecha_inicio'] > date('Y-m-d') && $torneo['estado'] !== 'finalizado';
                  ?>
                  <li data-busqueda="<?php echo htmlspecialchars($torneo['nombre'] . ' ' . $torneo['nombre_juego'] . ' ' . $torneo['localidad'] . ' ' . $torneo['comunidad']); ?>"
                      data-tipo="<?php echo $tipoDeJuego((string) $torneo['categoria_juego']); ?>"
                      data-juego="<?php echo (int) $torneo['juego_id']; ?>"
                      data-formato="<?php echo htmlspecialchars($torneo['formato']); ?>"
                      data-lugar="<?php echo $esOnline ? 'online' : 'presencial'; ?>"
                      data-estado="<?php echo htmlspecialchars($torneo['estado']); ?>"
                      data-proximo="<?php echo $esProximo ? '1' : '0'; ?>">
                  <article class="torneo-tarjeta">
                        <img src="<?php echo htmlspecialchars($torneo['banner_url'] ? (preg_match('#^https?://#i', $torneo['banner_url']) ? $torneo['banner_url'] : URL_BASE . ltrim($torneo['banner_url'], '/')) : URL_BASE . 'img/torneos/card1.jpg'); ?>" alt="<?php echo htmlspecialchars($torneo['nombre']); ?>">
                        <div class="tarjeta-contenido">
                           <h2><?php echo htmlspecialchars($torneo['nombre']); ?></h2>

                           <div class="tarjeta-meta">
                              <span><i class="fa-solid fa-gamepad"></i> <?php echo htmlspecialchars($torneo['nombre_juego']); ?></span>
                              <span><i class="fa-solid fa-users"></i> <?php echo (int) $torneo['cupo_max_equipos']; ?> cupos</span>
                              <span><i class="fa-solid fa-trophy"></i> <?php echo htmlspecialchars($etiquetasEstadoTorneo[$torneo['estado']] ?? $torneo['estado']); ?></span>
                           </div>

                           <div class="tarjeta-tipo">
                              <i class="fa-solid fa-diagram-project"></i>
                              <p><?php echo htmlspecialchars($etiquetasFormato[$torneo['formato']] ?? $torneo['formato']); ?></p>
                           </div>

                           <div class="pie-tarjeta">
                              <span>
                                 <?php echo $torneo['fecha_inicio'] ? htmlspecialchars(date('d M', strtotime($torneo['fecha_inicio']))) : 'Fecha a confirmar'; ?>
                                 <?php echo $torneo['fecha_fin'] ? ' - ' . htmlspecialchars(date('d M', strtotime($torneo['fecha_fin']))) : ''; ?>
                              </span>
                              <a href="<?php echo URL_BASE; ?>index.php?c=torneo&a=detalle&id=<?php echo (int) $torneo['id']; ?>">Ver más</a>
                           </div>
                        </div>
                     </article>
                  </li>
               <?php endforeach; ?>
            </ul>

            <p class="lista-vacia oculto" id="torneos-sin-resultados">No encontramos torneos con esos filtros.</p>

            <div class="ver-mas-boton" id="torneos-mas">
               <button type="button" id="torneos-cargar-mas" class="cursor-pointer bg-violet-500 rounded-md text-white font-semibold 
       transition duration-300 ease-in-out hover:bg-violet-700 hover:ring-2 hover:ring-violet-800 
       hover:shadow-xl hover:shadow-violet-500 focus:ring-violet-300 focus:shadow-violet-400 px-5 py-2">Cargar más</button>
            </div>
         <?php endif; ?>

      </section>

   </main>

   <!-- FOOTER ---------------------------->

   <?php include __DIR__ . '/../componentes/footer.php'; ?>

   <script src="<?php echo URL_BASE; ?>js/navbar.js"></script>
   <script>document.dispatchEvent(new Event("navbarPublicoListo"));</script>
   <script src="<?php echo URL_BASE; ?>js/hero-slider.js"></script>
   <script src="<?php echo URL_BASE; ?>js/scroll-reveal.js"></script>
   <script src="<?php echo URL_BASE; ?>js/torneos.js"></script>
 
</body>

</html>
