<!DOCTYPE html>
<html lang="es">

<head>

   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title>ASCEND</title>

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

   <!-- Layout compartido de organizador -->
   <!-- Página específica -->
    <link rel="stylesheet" href="../../publico/css/variables.css">
   <link rel="stylesheet" href="../../publico/css/base.css">
   <link rel="stylesheet" href="../../publico/css/componentes/botones.css">
   <link rel="stylesheet" href="../../publico/css/componentes/tarjetas.css">
   <link rel="stylesheet" href="../../publico/css/componentes/chips.css">
   <link rel="stylesheet" href="../../publico/css/layouts/organizador-layout.css">
   <link rel="stylesheet" href="../../publico/css/layouts/footer.css">
   <link rel="stylesheet" href="../../publico/css/organizador/dashboard.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
      rel="stylesheet">

</head>

<body>

   <header id="navbar-placeholder"></header>
   <aside id="sidebar-placeholder"></aside>

   <main class="dashboard-organizador">

      <header class="dashboard-header">
         <div>
            <p class="dashboard-tag">PANEL DEL ORGANIZADOR</p>
            <h1>Bienvenida, Valentina</h1>
         </div>
      </header>

      <!-- STATS -->
       
      <dl class="estadisticas">

         <div class="tarjeta-estadistica">
            <div class="tarjeta-estadistica-icono">
               <i class="fa-solid fa-trofeo"></i>
            </div>
            <div>
               <dt>Torneos Activos</dt>
               <dd>3</dd>
            </div>
         </div>

         <div class="tarjeta-estadistica">
            <div class="tarjeta-estadistica-icono tarjeta-estadistica-icono-rosa">
               <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div>
               <dt>Próximos Partidos</dt>
               <dd>8</dd>
            </div>
         </div>

         <div class="tarjeta-estadistica">
            <div class="tarjeta-estadistica-icono tarjeta-estadistica-icono-celeste">
               <i class="fa-solid fa-user-check"></i>
            </div>
            <div>
               <dt>Solicitudes</dt>
               <dd>2</dd>
            </div>
         </div>

         <div class="tarjeta-estadistica">
            <div class="tarjeta-estadistica-icono tarjeta-estadistica-icono-violeta">
               <i class="fa-solid fa-clipboard-check"></i>
            </div>
            <div>
               <dt>Resultados</dt>
               <dd>2</dd>
            </div>
         </div>

      </dl>

      <!-- CONTENIDO -->

      <section class="dashboard-grid" aria-label="Resumen del organizador">

         <!-- TORNEOS ACTIVOS -->

         <section class="dashboard-panel">

            <header class="panel-header">
               <h2>Torneos Activos</h2>
               <a href="../publico/en-construccion.html">Ver todos</a>
            </header>

            <ul class="lista-torneo">

               <li class="tarjeta-lista tarjeta-lista-torneo">

                  <img src="../../publico/img/torneos/card5.jpg" alt="Torneo">

                  <div class="info-torneo">
                     <header>
                        <span class="chip chip-exito">En curso</span>
                        <h3>Virtual Odyssey Chronicles</h3>
                     </header>

                     <p>
                        <i class="fa-solid fa-gamepad"></i>
                        eSports · Eliminación directa
                     </p>

                     <div class="meta-torneo">
                        <span>32 equipos</span>
                        <span>Ronda 2/4</span>
                     </div>
                  </div>

                  <a href="../publico/en-construccion.html" class="boton boton-gestion">Gestionar</a>

               </li>

               <li class="tarjeta-lista tarjeta-lista-torneo">

                  <img src="../../publico/img/black ferns/black-ferns_mobilell.png" alt="Torneo">

                  <div class="info-torneo">
                     <header>
                        <span class="chip chip-exito">En curso</span>
                        <h3>Copa Black Ferns 2026</h3>
                     </header>

                     <p>
                        <i class="fa-solid fa-trofeo"></i>
                        Rugby · Liga
                     </p>

                     <div class="meta-torneo">
                        <span>16 equipos</span>
                        <span>Fecha 3/8</span>
                     </div>
                  </div>

                  <a href="../publico/en-construccion.html" class="boton boton-gestion">Gestionar</a>

               </li>

            </ul>

         </section>

         <!-- PRÓXIMOS ENCUENTROS -->
         <section class="dashboard-panel">

            <header class="panel-header">
               <h2>Próximos Encuentros</h2>
               <a href="../publico/en-construccion.html">Calendario</a>
            </header>

            <ul class="lista-encuentros">

               <li class="tarjeta-lista tarjeta-lista-encuentro">

                  <time class="fecha-encuentro" datetime="2026-07-12">
                     <strong>12</strong>
                     <span>JUL</span>
                  </time>

                  <div class="equipo-encuentro">
                     <div class="equipos">
                        <img src="../../publico/img/logos/artigas.png" alt="Artigas">
                        <span>Artigas</span>
                     </div>

                     <strong class="equipos-vs">VS</strong>

                     <div class="equipos">
                        <img src="../../publico/img/logos/flores.png" alt="Flores">
                        <span>Flores</span>
                     </div>
                  </div>

                  <span class="chip chip-alerta">Pendiente</span>

               </li>

               <li class="tarjeta-lista tarjeta-lista-encuentro">

                  <time class="fecha-encuentro" datetime="2026-07-15">
                     <strong>15</strong>
                     <span>JUL</span>
                  </time>

                  <div class="equipo-encuentro">
                     <div class="equipos">
                        <img src="../../publico/img/logos/durazno.png" alt="Durazno">
                        <span>Durazno</span>
                     </div>

                     <strong class="equipos-vs">VS</strong>

                     <div class="equipos">
                        <img src="../../publico/img/logos/canelones.png" alt="Canelones">
                        <span>Canelones</span>
                     </div>
                  </div>

                  <span class="chip chip-peligro">En vivo</span>

               </li>

            </ul>

         </section>

      </section>

   </main>

   <script src="../../publico/js/organizador/includes.js"></script>
   <script src="../../publico/js/organizador/sidebar.js"></script>

</body>

</html>