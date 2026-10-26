<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>ASCEND - Juegos</title>

   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/navbar.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/publico/home.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/footer.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/publico/juegos.css">

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

   
</head>

<body>

   <!-- NAVBAR ---------------------------->
   <?php include __DIR__ . '/../componentes/navbar-publico.php'; ?>

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
         <p class="hero-subtitulo">Sistema de Gestión de Deportes y eSports</p>
         <h1>Descubrí <br>Nuestros Juegos<br></h1>
         <p class="hero-descripcion">
            Explorá todas las disciplinas disponibles en ASCEND: eSports, juegos de mesa
            y deportes tradicionales.
         </p>
      </div>

   </section>

   <main class="juegos-pagina">

      <!-------------------------------------->
      <!-- PANEL DE FILTROS ------------------->
      <!-------------------------------------->

      <section class="panel-filtros" aria-label="Filtros de búsqueda de juegos">

         <div class="top-fila">

            <ul class="chips-contenedor">
               <li><button class="chip active" data-categoria="todos">Todos</button></li>
               <li><button class="chip" data-categoria="esports">eSports</button></li>
               <li><button class="chip" data-categoria="tradicionales">Tradicionales</button></li>
               <li><button class="chip" data-categoria="mentales">Mentales</button></li>
            </ul>

            <div class="buscador">
               <i class="fa-solid fa-magnifying-glass"></i>
               <input type="text" id="buscar-juego" placeholder="Buscar juego...">
            </div>

         </div>

         <div class="seleccionar-filtros">

            <select id="filtro-modalidad">
               <option value="">Modalidad</option>
               <option value="individual">Individual</option>
               <option value="equipos">Por equipos</option>
            </select>

            <select id="filtro-orden">
               <option value="">Ordenar por</option>
               <option value="az">Nombre (A-Z)</option>
               <option value="za">Nombre (Z-A)</option>
            </select>

         </div>

      </section>

      <!-------------------------------------->
      <!-- GRID DE JUEGOS --------------------->
      <!-------------------------------------->
      <section class="juegos-grilla-seccion">

                <?php
         $juegos = $juegos ?? [];

         //Categoria de la BD -> [filtro de esta pagina, texto de la tarjeta]
         $categoriaFiltro = function (string $cat): array {
            if (strpos($cat, 'esport') === 0)  { return ['esports', 'eSports']; }
            if (strpos($cat, 'deporte') === 0) { return ['tradicionales', 'Deporte']; }
            return ['mentales', 'Juego de mesa'];
         };
         ?>

         <ul class="juegos-grillas" id="juegos-grillas">

            <?php if (empty($juegos)): ?>
               <li><p>Todavía no hay juegos cargados.</p></li>
            <?php endif; ?>

            <?php foreach ($juegos as $j): ?>
               <?php
               [$filtroCat, $textoCat] = $categoriaFiltro((string) $j['categoria']);

               $modalidad = (string) ($j['modalidad'] ?? '');
               $filtroMod = $modalidad === '' ? '' : (stripos($modalidad, 'individual') !== false ? 'individual' : 'equipos');
               $textoMod = $filtroMod === 'individual' ? 'Individual' : ($filtroMod === 'equipos' ? 'Por equipos' : '');

               $img = (string) ($j['imagen_url'] ?? '');
               if ($img === '') {
                  $imgSrc = URL_BASE . 'img/bg.jpg';
               } else {
                  $imgSrc = preg_match('#^(https?:)?//#i', $img) ? $img : URL_BASE . ltrim($img, '/');
               }
               ?>
               <li>
                  <article class="tarjeta-juego" data-categoria="<?php echo $filtroCat; ?>" data-modalidad="<?php echo $filtroMod; ?>">
                     <div class="tarjeta-info">
                        <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo htmlspecialchars($j['nombre']); ?>">
                        <div class="tarjeta-overlay">
                           <h2><?php echo htmlspecialchars($j['nombre']); ?></h2>
                           <p><?php echo $textoCat . ($textoMod !== '' ? ' · ' . $textoMod : ''); ?></p>
                        </div>
                     </div>
                  </article>
               </li>
            <?php endforeach; ?>

         </ul>

         <div class="ver-mas-boton">
            <a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=enConstruccion" class="cursor-pointer bg-violet-500 rounded-md text-white font-semibold 
      transition duration-300 ease-in-out hover:bg-violet-700 hover:ring-2 hover:ring-violet-800 
      hover:shadow-xl hover:shadow-violet-500 focus:ring-violet-300 focus:shadow-violet-400 px-5 py-2">Ver más fechas</a>
         </div>

         <p class="juegos-sin-resultados oculto" id="juegos-sin-resultados">
            No se encontraron juegos con esos filtros.
         </p>

      </section>

   </main>

   <!-- FOOTER ---------------------------->
   <?php include __DIR__ . '/../componentes/footer.php'; ?>

   <script src="<?php echo URL_BASE; ?>js/navbar.js"></script>
   <script>document.dispatchEvent(new Event("navbarPublicoListo"));</script>
   <script src="<?php echo URL_BASE; ?>js/hero-slider.js"></script>
   <script src="<?php echo URL_BASE; ?>js/scroll-reveal.js"></script>
   <script src="<?php echo URL_BASE; ?>js/juegos.js"></script>

</body>
</html>
