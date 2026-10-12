<?php
/**
 * Componente: navbar-organizador.php
 * Navbar exclusivo del rol Organizador. Mismo diseño que navbar-jugador.php
 * (escritorio identico al navbar publico, "Mi Perfil" como ultimo <li> del mismo <ul>,
 * en mobile el avatar abre un panel que se desliza desde la izquierda).
 * No incluye el sidebar flotante: el Organizador ya tiene su propio sidebar.
 */
?>

<link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/navbar.css">

<style>
   .avatar-mobile-toggle {
      display: flex;
      background: transparent;
      border: none;
      cursor: pointer;
      color: #fff;
      font-size: 34px;
      padding: 0;
   }

   .li-perfil-jugador { display: none; }

   .auth-links-jugador-fila {
      display: flex;
      align-items: center;
      gap: 14px;
   }

   .panel-mi-perfil-mobile {
      position: fixed;
      top: 0;
      left: -100%;
      width: min(280px, 85vw);
      height: 100vh;
      display: flex;
      flex-direction: column;
      gap: 4px;
      padding: 100px 1.5rem 40px;
      background: rgba(7, 8, 18, 0.97);
      backdrop-filter: blur(12px);
      border-right: 1px solid rgba(0, 255, 238, 0.2);
      box-shadow: 10px 0 30px rgba(0, 0, 0, 0.5);
      overflow-y: auto;
      transition: left 0.35s cubic-bezier(0.1, 1, 0.1, 1);
      z-index: 1005;
   }

   .panel-mi-perfil-mobile.active { left: 0; }

   .panel-mi-perfil-mobile .encabezado-perfil {
      display: flex;
      align-items: center;
      gap: 12px;
      padding-bottom: 16px;
      margin-bottom: 12px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
   }

   .panel-mi-perfil-mobile .encabezado-perfil i {
      font-size: 40px;
      color: #00FFEE;
   }

   .panel-mi-perfil-mobile .etiqueta-seccion {
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: #6a6a80;
      margin: 14px 0 6px;
   }

   .panel-mi-perfil-mobile a,
   .panel-mi-perfil-mobile button {
      display: flex;
      align-items: center;
      gap: 12px;
      width: 100%;
      background: transparent;
      border: none;
      color: #fff;
      text-decoration: none;
      font-family: inherit;
      font-size: 15px;
      padding: 10px 8px;
      border-radius: 10px;
      cursor: pointer;
      text-align: left;
   }

   .panel-mi-perfil-mobile a:hover,
   .panel-mi-perfil-mobile button:hover {
      background: rgba(255, 255, 255, 0.06);
   }

   .panel-mi-perfil-mobile button.cerrar-sesion { color: #ff6b6b; margin-top: 10px; }
   .panel-mi-perfil-mobile i { width: 18px; text-align: center; }

   .overlay-fondo-jugador {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.5);
      z-index: 1000;
      display: none;
   }
   .overlay-fondo-jugador.active { display: block; }

   @media (min-width: 881px) {
      .avatar-mobile-toggle,
      .panel-mi-perfil-mobile,
      .overlay-fondo-jugador {
         display: none !important;
      }

      .li-perfil-jugador {
         display: block !important;
         width: auto !important;
      }
   }
</style>

<div class="overlay-fondo-jugador" id="overlay-jugador"></div>

<nav class="navbar">
   <div class="navbar-contenedor">

      <button class="navbar-toggle" id="menu-toggle" type="button" aria-label="Abrir menú de navegación" aria-expanded="false">
         <span class="bar"></span>
         <span class="bar"></span>
         <span class="bar"></span>
      </button>

      <a class="logo-contenedor" href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=dashboard">
         <img src="<?php echo URL_BASE; ?>img/logos/ascend-logo01.png" alt="Logo de ASCEND" class="logo-navbar">
      </a>

      <button class="avatar-mobile-toggle" id="avatar-toggle-jugador" type="button" aria-label="Abrir menú de perfil">
         <i class="fa-solid fa-circle-user"></i>
      </button>

      <ul class="navbar-menu" id="nav-links">

         <li><a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=index">Inicio</a></li>

         <li class="desplegable">
            <button class="boton-desplegable" type="button" aria-expanded="false">
               Torneos <i class="fa-solid fa-chevron-down"></i>
            </button>
            <div class="contenido-desplegable">
               <a href="<?php echo URL_BASE; ?>index.php?c=torneo&a=catalogoPublico">Todos los Torneos</a>
               <a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=juegos">Juegos</a>
            </div>
         </li>

         <li><a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=rankings">Rankings</a></li>
         <li><a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=nosotros">Nosotros</a></li>
         <li><a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=contacto">Contacto</a></li>

         <li class="li-perfil-jugador">
            <div class="auth-links-jugador-fila">
               <div class="notificaciones-jugador">
                  <button type="button" class="boton-campana" id="campana-toggle" aria-label="Notificaciones" aria-expanded="false">
                     <i class="fa-solid fa-bell"></i>
                     <span class="badge-notificaciones oculto" id="badge-notificaciones">0</span>
                  </button>
                  <div class="panel-notificaciones" id="panel-notificaciones">
                     <div class="notificaciones-header">
                        <h4>Notificaciones</h4>
                     </div>
                     <div class="notificaciones-lista">
                        <p class="notificaciones-vacio">No tenés notificaciones nuevas.</p>
                     </div>
                  </div>
               </div>

               <div class="perfil-jugador-menu">
                  <button type="button" class="boton-mi-perfil" id="mi-perfil-toggle" aria-expanded="false">
                     <i class="fa-solid fa-circle-user"></i>
                     Organizador
                     <i class="fa-solid fa-chevron-down"></i>
                  </button>
                  <div class="dropdown-mi-perfil" id="dropdown-mi-perfil">
                     <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=perfil"><i class="fa-solid fa-user"></i> Mi Perfil</a>
                     <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=perfil"><i class="fa-solid fa-gear"></i> Configuración</a>
                     <form method="POST" action="<?php echo URL_BASE; ?>
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">index.php?c=auth&a=logout">
                        <button type="submit"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</button>
                     </form>
                  </div>
               </div>
            </div>
         </li>

      </ul>

   </div>
</nav>

<aside class="panel-mi-perfil-mobile" id="panel-perfil-mobile-jugador">

   <div class="encabezado-perfil">
      <i class="fa-solid fa-circle-user"></i>
      <div>
         <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Mi cuenta'); ?></strong>
      </div>
   </div>

   <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=perfil"><i class="fa-solid fa-user"></i> Mi Perfil</a>
   <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=perfil"><i class="fa-solid fa-gear"></i> Configuración</a>

   <div class="etiqueta-seccion">Mi actividad</div>
   <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=misTorneos"><i class="fa-solid fa-list"></i> Mis torneos</a>
   <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=crear"><i class="fa-solid fa-plus"></i> Crear torneo</a>
   <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=calendario"><i class="fa-solid fa-calendar-days"></i> Calendario</a>
   <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=resultados"><i class="fa-solid fa-clipboard-check"></i> Resultados</a>
   <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=historial"><i class="fa-solid fa-clock-rotate-left"></i> Historial</a>

   <form method="POST" action="<?php echo URL_BASE; ?>
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">index.php?c=auth&a=logout">
      <button type="submit" class="cerrar-sesion"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</button>
   </form>

</aside>

<script>
   document.addEventListener("DOMContentLoaded", () => {
      const btnMenu = document.getElementById("menu-toggle");
      const navLinks = document.getElementById("nav-links");
      const btnAvatar = document.getElementById("avatar-toggle-jugador");
      const panelPerfil = document.getElementById("panel-perfil-mobile-jugador");
      const overlay = document.getElementById("overlay-jugador");
      const btnCampana = document.getElementById("campana-toggle");
      const panelNotif = document.getElementById("panel-notificaciones");
      const btnMiPerfil = document.getElementById("mi-perfil-toggle");
      const dropdownPerfil = document.getElementById("dropdown-mi-perfil");

      function cerrarPaneles() {
         navLinks.classList.remove("active");
         btnMenu.classList.remove("active");
         panelPerfil.classList.remove("active");
         overlay.classList.remove("active");
      }

      btnMenu.addEventListener("click", () => {
         const abierto = navLinks.classList.contains("active");
         cerrarPaneles();
         if (!abierto) {
            navLinks.classList.add("active");
            btnMenu.classList.add("active");
            overlay.classList.add("active");
         }
      });

      btnAvatar.addEventListener("click", () => {
         const abierto = panelPerfil.classList.contains("active");
         cerrarPaneles();
         if (!abierto) {
            panelPerfil.classList.add("active");
            overlay.classList.add("active");
         }
      });

      overlay.addEventListener("click", cerrarPaneles);

      btnCampana.addEventListener("click", (evento) => {
         evento.stopPropagation();
         const abierto = panelNotif.classList.toggle("open");
         btnCampana.setAttribute("aria-expanded", abierto);
      });

      btnMiPerfil.addEventListener("click", (evento) => {
         evento.stopPropagation();
         const abierto = dropdownPerfil.classList.toggle("open");
         btnMiPerfil.setAttribute("aria-expanded", abierto);
      });

      document.addEventListener("click", () => {
         panelNotif.classList.remove("open");
         dropdownPerfil.classList.remove("open");
      });

      const toggleDesplegable = document.querySelector(".desplegable .boton-desplegable");
      if (toggleDesplegable) {
         toggleDesplegable.addEventListener("click", () => {
            toggleDesplegable.closest(".desplegable").classList.toggle("open");
         });
      }
   });
</script>