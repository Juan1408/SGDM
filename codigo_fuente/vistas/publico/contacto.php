<!DOCTYPE html>
<html lang="es">

<head>

   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title>ASCEND - Contacto</title>

   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
   <script src="<?php echo URL_BASE; ?>js/tema.js"></script>
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/efectos.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/navbar.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/publico/home.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/footer.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/titulos.css">
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
            <p class="hero-subtitulo efecto-glitch">Sistema de Gestión de Deportes y eSports</p>
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
         <h2 class="seccion-gradiente-titulo contacto-titulo">Contactanos y conocé nuestro equipo</h2>

         <div class="contacto-info">

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
                  <p>charrua.code@ascend.com</p>
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

         <?php
         //Valores previos (si hubo error) o datos de la sesion si la persona ya inicio sesion
         $viejo = $_SESSION['contacto_viejo'] ?? [];
         unset($_SESSION['contacto_viejo']);
         $valorNombre = $viejo['nombre'] ?? (string) ($_SESSION['usuario_nombre'] ?? '');
         $valorEmail = $viejo['email'] ?? (string) ($_SESSION['email'] ?? '');
         $valorMensaje = $viejo['mensaje'] ?? '';
         ?>
         <form class="contacto-formulario" id="formulario-contacto" method="POST" action="<?php echo URL_BASE; ?>index.php?c=inicio&a=enviarContacto">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
            <!-- Campo trampa para bots: oculto para las personas -->
            <input type="text" name="sitio_web" class="contacto-trampa" tabindex="-1" autocomplete="off" aria-hidden="true">
            <?php if (!empty($_SESSION['contacto_exito'])): ?>
               <p class="contacto-mensaje contacto-mensaje--exito" role="status"><?php echo htmlspecialchars($_SESSION['contacto_exito']); unset($_SESSION['contacto_exito']); ?></p>
            <?php endif; ?>
            <?php if (!empty($_SESSION['contacto_error'])): ?>
               <p class="contacto-mensaje contacto-mensaje--error" role="alert"><?php echo htmlspecialchars($_SESSION['contacto_error']); unset($_SESSION['contacto_error']); ?></p>
            <?php endif; ?>
            <input type="text" name="nombre" placeholder="Tu nombre *" maxlength="100" required value="<?php echo htmlspecialchars($valorNombre, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="email" name="email" placeholder="Tu email *" maxlength="255" required value="<?php echo htmlspecialchars($valorEmail, ENT_QUOTES, 'UTF-8'); ?>">
            <textarea name="mensaje" placeholder="Escribí tu mensaje *" minlength="10" maxlength="2000" required><?php echo htmlspecialchars($valorMensaje, ENT_QUOTES, 'UTF-8'); ?></textarea>
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
   <script src="<?php echo URL_BASE; ?>js/efecto-glitch.js"></script>
   <script src="<?php echo URL_BASE; ?>js/hero-slider.js"></script>
   <script src="<?php echo URL_BASE; ?>js/scroll-reveal.js"></script>

</body>

</html>