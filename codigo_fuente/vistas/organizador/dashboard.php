<!DOCTYPE html>
<html lang="es">

<head>

   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title>ASCEND</title>

   <!-- Swiper.js -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

   <!-- CSS base y tokens -->
   <!-- Componentes compartidos -->
   <!-- Layout compartido de organizador -->
   <!-- Específico de esta página -->
   <link rel="stylesheet" href="../../publico/css/base-organizador-layout.css">
   <link rel="stylesheet" href="../../publico/css/organizador/dashboard.css">
   <!-- Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

   <!-- Google Fonts -->
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
      rel="stylesheet">

</head>

<body>

   <!---- NAVBAR Y SIDEBAR ------------------->
   <header id="navbar-placeholder"></header>
   <aside id="sidebar-placeholder"></aside>
   <!------------------------------------------>

   <!--------------------------------------------->
   <!------ HEADER ------------------------------->
   <!--------------------------------------------->

   <main class="dashboard-organizador">

      <header class="dashboard-header">

         <div>
            <p class="dashboard-tag">
               PANEL DEL ORGANIZADOR </p>
            <h1>
               Bienvenida, Valentina </h1>
         </div>


      </header>

      <!------------------------------------------->
      <!------- STATS ----------------------------->
      <!------------------------------------------->

      <section class="estadisticas">

         <article class="tarjeta-stat">
            <div class="tarjeta-stat__icono">
               <i class="fa-solid fa-trophy"></i>
            </div>

            <div>
               <p>Torneos Activos</p>
               <h3>3</h3>
            </div>
         </article>

         <article class="tarjeta-stat">
            <div class="tarjeta-stat__icono tarjeta-stat__icono--rosa">
               <i class="fa-solid fa-calendar-days"></i>
            </div>

            <div>
               <p>Próximos Partidos</p>
               <h3>8</h3>
            </div>
         </article>

         <article class="tarjeta-stat">
            <div class="tarjeta-stat__icono tarjeta-stat__icono--celeste">
               <i class="fa-solid fa-user-check"></i>
            </div>

            <div>
               <p>Solicitudes</p>
               <h3>2</h3>
            </div>
         </article>

         <article class="tarjeta-stat">
            <div class="tarjeta-stat__icono tarjeta-stat__icono--violeta">
               <i class="fa-solid fa-clipboard-check"></i>
            </div>

            <div>
               <p>Resultados</p>
               <h3>2</h3>
            </div>
         </article>
      </section>

      <!-------------------------------------------->
      <!------ CONTENIDO --------------------------->
      <!-------------------------------------------->

      <section class="dashboard-grid" aria-label="Resumen del organizador">

         <!-- TORNEOS ACTIVOS -->
         <section class="dashboard-panel">

            <header class="panel-header">
               <h2>Torneos Activos</h2>
               <a href="../publico/en-construccion.html">Ver todos</a>
            </header>

            <div class="lista-torneo" role="list">

               <article class="tarjeta-lista tarjeta-lista--torneo" role="listitem">

                  <img src="../../publico/img/torneos/card5.jpg" alt="Torneo">

                  <div class="info-torneo">

                     <header>
                        <span class="chip chip--exito">En curso</span>

                        <h4>Virtual Odyssey Chronicles</h4>

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

                  <a href="../publico/en-construccion.html" class="boton boton--gestion">Gestionar</a>

               </article>

               <article class="tarjeta-lista tarjeta-lista--torneo" role="listitem">

                  <img src="../../publico/img/black ferns/black-ferns_mobilell.png" alt="Torneo">

                  <div class="info-torneo">
                     <span class="chip chip--exito">En curso</span>

                     <h4>Copa Black Ferns 2026</h4>

                     <p>
                        <i class="fa-solid fa-trophy"></i>
                        Rugby · Liga
                     </p>

                     <div class="meta-torneo">
                        <span>16 equipos</span>
                        <span>Fecha 3/8</span>
                     </div>
                  </div>

                  <a href="../publico/en-construccion.html" class="boton boton--gestion">Gestionar</a>

               </article>

            </div>

         </section>


         <!-- PRÓXIMOS ENCUENTROS -->
         <section class="dashboard-panel">

            <div class="panel-header">
               <h3>Próximos Encuentros</h3>
               <a href="../publico/en-construccion.html">Calendario</a>
            </div>

            <div class="lista-encuentros">

               <article class="tarjeta-lista tarjeta-lista--encuentro">

                  <div class="fecha-encuentro">
                     <strong>12</strong>
                     <span>JUL</span>
                  </div>

                  <section class="equipo-encuentro">

                     <div class="equipos">
                        <img src="../../publico/img/logos/artigas.png" alt="Artigas">
                        <span>Artigas</span>
                     </div>

                     <strong class="equipos-vs">VS</strong>

                     <div class="equipos">
                        <img src="../../publico/img/logos/flores.png" alt="Flores">
                        <span>Flores</span>
                     </div>

                  </section>

                  <span class="chip chip--alerta">Pendiente</span>

               </article>

               <article class="tarjeta-lista tarjeta-lista--encuentro">

                  <time class="fecha-encuentro" datetime="2026-07-12">
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

                  <span class="chip chip--peligro">En vivo</span>

               </article>

            </div>

         </section>

      </section>

   </main>


   <script src="../../publico/js/organizador/includes.js"></script>
   <script src="../../publico/js/organizador/sidebar.js"></script>

</body>

</html>