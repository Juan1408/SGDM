<?php
/**
 * Componente: navbar-jugador.php
 * Navbar exclusivo del rol Jugador. Archivo 100% independiente: no incluye ni depende
 * de navbar-publico.html (compartido), para no pisarse con lo que otros compañeros
 * puedan estar tocando ahi en paralelo mientras programan Organizador/Publico/Admin.
 *
 * Cuando llegue el momento de mergear todo, existe una version que unifica esto en un
 * solo archivo inteligente por rol (navbar.php, guardada aparte) - se retoma en ese momento.
 *
 * Diseño: escritorio identico al navbar-publico.html real (Mi Perfil como ultimo <li>
 * del mismo <ul>). En mobile, el avatar (derecha) abre un panel que absorbe el sidebar
 * (Solicitudes/Equipos/Historial/Guardados + Ver Perfil/Configuracion/Cerrar sesion,
 * panel se desliza desde la izquierda). El hamburguesa (izquierda) sigue abriendo
 * Inicio/Torneos/Rankings/Nosotros/Contacto, sin cambios.
 */
?>

<?php
//Foto del usuario logueado para el avatar y el panel del menu movil (si no tiene, se usa el icono)
$urlFotoNav = '';
try {
   require_once __DIR__ . '/../../modelos/Conexion.php';
   $stmtFotoNav = \App\Modelos\Conexion::getInstance()->getBD()->prepare('SELECT foto_perfil_url FROM usuarios WHERE id = :id LIMIT 1');
   $stmtFotoNav->execute([':id' => (int) ($_SESSION['usuario_id'] ?? 0)]);
   $fotoNav = trim((string) $stmtFotoNav->fetchColumn());
   if ($fotoNav !== '') {
      $urlFotoNav = preg_match('#^https?://#i', $fotoNav) ? $fotoNav : URL_BASE . ltrim($fotoNav, '/');
   }
} catch (\Throwable $e) {
   $urlFotoNav = '';
}
?>

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

   @media (min-width: 881px) {
      .avatar-mobile-toggle,
      .panel-mi-perfil-mobile {
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

      <a class="logo-contenedor" href="<?php echo URL_BASE; ?>index.php?c=panelJugador&a=dashboard">
         <img src="<?php echo URL_BASE; ?>img/logos/ascend-logo01.png" alt="Logo de ASCEND" class="logo-navbar">
      </a>

      <button class="avatar-mobile-toggle" id="avatar-toggle-jugador" type="button" aria-label="Abrir menú de perfil">
         <?php if ($urlFotoNav !== ''): ?>
            <img src="<?php echo htmlspecialchars($urlFotoNav); ?>" alt="Mi perfil">
         <?php else: ?>
            <i class="fa-solid fa-circle-user"></i>
         <?php endif; ?>
      </button>

      <ul class="navbar-menu" id="nav-links">

         <li><a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=index">Inicio</a></li>

         <li class="desplegable">
            <button class="boton-desplegable" type="button" aria-expanded="false">
               Torneos <i class="fa-solid fa-chevron-down"></i>
            </button>
            <div class="contenido-desplegable">
               <a href="<?php echo URL_BASE; ?>index.php?c=torneo&a=catalogoPublico">Todos los Torneos</a>
               <a href="<?php echo URL_BASE; ?>index.php?c=torneo&a=catalogoPublico">Detalle Torneos</a>
               <a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=juegos">Juegos</a>
            </div>
         </li>

         <li><a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=rankings">Rankings</a></li>
         <li><a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=nosotros">Nosotros</a></li>
         <li><a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=contacto">Contacto</a></li>

         <!-- Se oculta en mobile (lo reemplaza el avatar + panel de abajo) -->
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
                     Jugador
                     <i class="fa-solid fa-chevron-down"></i>
                  </button>
                  <div class="dropdown-mi-perfil" id="dropdown-mi-perfil">
                     <a href="<?php echo URL_BASE; ?>index.php?c=panelJugador&a=perfil"><i class="fa-solid fa-user"></i> Mi Perfil</a>
                     <a href="<?php echo URL_BASE; ?>index.php?c=panelJugador&a=editarPerfil"><i class="fa-solid fa-pen"></i> Editar perfil</a>
                     <a href="<?php echo URL_BASE; ?>index.php?c=panelJugador&a=perfil"><i class="fa-solid fa-gear"></i> Configuración</a>
                     <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=auth&a=logout">
                           <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</button>
                     </form>
                  </div>
               </div>
            </div>
         </li>

      </ul>

   </div>
</nav>

<!-- Panel de perfil para mobile: absorbe lo que en escritorio es el sidebar flotante -->
<aside class="panel-mi-perfil-mobile" id="panel-perfil-mobile-jugador">

   <button type="button" class="panel-cerrar" id="panel-perfil-cerrar" aria-label="Cerrar menú de perfil">
      <span class="bar"></span>
      <span class="bar"></span>
   </button>

   <div class="encabezado-perfil">
      <?php if ($urlFotoNav !== ''): ?>
         <img class="encabezado-foto" src="<?php echo htmlspecialchars($urlFotoNav); ?>" alt="Tu foto de perfil">
      <?php else: ?>
         <i class="fa-solid fa-circle-user"></i>
      <?php endif; ?>
      <div>
         <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Mi cuenta'); ?></strong>
      </div>
   </div>

   <a href="<?php echo URL_BASE; ?>index.php?c=panelJugador&a=perfil"><i class="fa-solid fa-user"></i> Mi Perfil</a>
   <a href="<?php echo URL_BASE; ?>index.php?c=panelJugador&a=perfil"><i class="fa-solid fa-gear"></i> Configuración</a>

   <div class="etiqueta-seccion">Mi actividad</div>
   <a href="<?php echo URL_BASE; ?>index.php?c=panelJugador&a=dashboard"><i class="fa-solid fa-house"></i> Inicio</a>
   <a href="<?php echo URL_BASE; ?>index.php?c=equipo&a=solicitudes"><i class="fa-solid fa-user-plus"></i> Solicitudes</a>
   <a href="<?php echo URL_BASE; ?>index.php?c=equipo&a=index"><i class="fa-solid fa-people-group"></i> Mis Equipos</a>
   <a href="<?php echo URL_BASE; ?>index.php?c=equipo&a=crear"><i class="fa-solid fa-plus"></i> Crear equipo</a>
   <a href="<?php echo URL_BASE; ?>index.php?c=panelJugador&a=perfil#historial"><i class="fa-solid fa-clock-rotate-left"></i> Historial</a>
   <a href="<?php echo URL_BASE; ?>index.php?c=guardados&a=index"><i class="fa-solid fa-bookmark"></i> Guardados</a>

   <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=auth&a=logout">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
      <button type="submit" class="cerrar-sesion"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</button>
   </form>

</aside>

<!-- Sidebar de escritorio: mismo contenido, visible siempre en pantallas grandes -->
<aside class="sidebar-jugador-flotante" id="sidebar-jugador-flotante" aria-label="Navegación del jugador">
   <nav aria-label="Menú del jugador">
      <ul class="sidebar-flotante-lista">
         <li>
            <a href="<?php echo URL_BASE; ?>index.php?c=panelJugador&a=dashboard" class="sidebar-flotante-item" title="Inicio">
               <i class="fa-solid fa-house" aria-hidden="true"></i>
               <span>Inicio</span>
            </a>
         </li>
         <li>
            <a href="<?php echo URL_BASE; ?>index.php?c=equipo&a=solicitudes" class="sidebar-flotante-item" title="Solicitudes">
               <i class="fa-solid fa-user-plus" aria-hidden="true"></i>
               <span>Solicitudes</span>
            </a>
         </li>
         <li class="sidebar-flotante-desplegable">
            <a href="<?php echo URL_BASE; ?>index.php?c=equipo&a=index" class="sidebar-flotante-item sidebar-flotante-boton" title="Mis equipos">
               <i class="fa-solid fa-people-group" aria-hidden="true"></i>
               <span>Equipos</span>
            </a>
            <button type="button" class="sidebar-flotante-toggle" aria-expanded="true" aria-controls="submenu-equipos" aria-label="Mostrar submenú de equipos">
               <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
            </button>
            <ul class="sidebar-flotante-submenu" id="submenu-equipos">
               <li>
                  <a href="<?php echo URL_BASE; ?>index.php?c=equipo&a=crear" title="Crear equipo">
                     <i class="fa-solid fa-plus" aria-hidden="true"></i>
                     <span>Crear equipo</span>
                  </a>
               </li>
            </ul>
         </li>
         <li>
            <a href="<?php echo URL_BASE; ?>index.php?c=panelJugador&a=perfil#historial" class="sidebar-flotante-item" title="Historial">
               <i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i>
               <span>Historial</span>
            </a>
         </li>
         <li>
            <a href="<?php echo URL_BASE; ?>index.php?c=guardados&a=index" class="sidebar-flotante-item" title="Guardados">
               <i class="fa-solid fa-bookmark" aria-hidden="true"></i>
               <span>Guardados</span>
            </a>
         </li>
      </ul>
   </nav>
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
      document.getElementById("panel-perfil-cerrar").addEventListener("click", cerrarPaneles);

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
<script src="<?php echo URL_BASE; ?>js/sidebar-riel.js"></script>