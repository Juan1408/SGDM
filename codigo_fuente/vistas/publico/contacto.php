<!DOCTYPE html>
<html lang="es">

<head>

   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title>ASCEND - Contacto</title>

   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/navbar.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/publico/home.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/footer.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/publico/contacto.css">

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

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
            <p class="hero-subtitulo">Sistema de Gestión de Deportes y eSports</p>
            <h1>Organiza y <br>Gestiona Torneos<br></h1>
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
      <!-- CONTACTO (mismas clases que ------->
      <!-- tenía en el index) ----------------->
      <!-------------------------------------->

      <section class="contacto reveal">
         <div class="contacto-info">
            <span class="section-tag">CONTACTO ASCEND</span>

            <h1>
               Contactanos y conocé
               <span>nuestro equipo</span>
            </h1>

            <address class="contacto-item">
               <div class="contacto-icon">
                  <i class="fa-solid fa-location-dot"></i>
               </div>
               <div>
                  <h2>Ubicación</h2>
                  <p>Instituto Tecnológico Superior Arias-Balparda</p>
               </div>
            </address>

            <address class="contacto-item">
               <div class="contacto-icon">
                  <i class="fa-solid fa-envelope"></i>
               </div>
               <div>
                  <h2>Contacto</h2>
                  <p>arena.core@torneos.com</p>
               </div>
            </address>

            <div class="contacto-item">
               <div class="contacto-icon">
                  <i class="fa-solid fa-share-nodes"></i>
               </div>
               <div>
                  <h2>Redes</h2>
                  <div class="contacto-redes">
                     <a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=enConstruccion" aria-label="Discord"><i class="fa-brands fa-discord"></i></a>
                     <a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=enConstruccion" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                     <a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=enConstruccion" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
                  </div>
               </div>
            </div>
         </div>

         <form class="contacto-formulario">
            <input type="text" placeholder="Tu nombre *" required>
            <input type="email" placeholder="Tu email *" required>
            <textarea placeholder="Escribí tu mensaje"></textarea>
            <button type="submit">Enviar mensaje</button>
         </form>
      </section>

      <!-------------------------------------->
      <!-- MAPA (sección nueva, no existía --->
      <!-- antes en el index) ----------------->
      <!-------------------------------------->

      <section class="contacto-mapa-seccion reveal">
         <div class="contacto-mapa">
            <iframe
               src="https://www.google.com/maps?q=Instituto+Tecnol%C3%B3gico+Superior+Arias-Balparda&output=embed"
               width="100%"
               height="100%"
               style="border:0;"
               allowfullscreen=""
               loading="lazy"
               referrerpolicy="no-referrer-when-downgrade"
               title="Ubicación de ASCEND">
            </iframe>
         </div>
      </section>

   </main>

   <!-- FOOTER ---------------------------->
   <?php include __DIR__ . '/../componentes/footer.php'; ?>

   <script src="<?php echo URL_BASE; ?>js/navbar.js"></script>
   <script>document.dispatchEvent(new Event("navbarPublicoListo"));</script>
   <script src="<?php echo URL_BASE; ?>js/hero-slider.js"></script>
   <script src="<?php echo URL_BASE; ?>js/scroll-reveal.js"></script>

</body>

</html>
