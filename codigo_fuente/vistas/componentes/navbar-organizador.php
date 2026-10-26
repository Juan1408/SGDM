<?php
/**
 * Componente: navbar-organizador.php
 * Navbar exclusivo del rol Organizador. Mismo diseño que navbar-jugador.php
 * (escritorio identico al navbar publico, "Mi Perfil" como ultimo <li> del mismo <ul>,
 * en mobile el avatar abre un panel que se desliza desde la izquierda).
 * Incluye el sidebar flotante de escritorio (mismo estilo que el del jugador). En mobile ese
 * sidebar se oculta y sus accesos quedan en el panel que abre el avatar.
 */

//Que item del sidebar se marca como activo segun la accion actual
$accionNavOrg = $_GET['a'] ?? '';
$mapaNavOrg = [
   'dashboard' => 'inicio',
   'misTorneos' => 'torneos', 'gestionar' => 'torneos', 'administrar' => 'torneos',
   'crear' => 'crear', 'guardarEtapa' => 'crear',
   'calendario' => 'calendario',
   'resultados' => 'resultados',
   'historial' => 'historial',
];
$itemActivoOrg = (($_GET['c'] ?? '') === 'panelOrganizador') ? ($mapaNavOrg[$accionNavOrg] ?? '') : '';
$claseActivoOrg = static fn (string $item): string => $item === $itemActivoOrg ? ' activo' : '';
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

<link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/navbar.css">
<!-- footer.css se carga aca para que las vistas del organizador solo tengan que incluir footer.html -->
<link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/footer.css">

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
   /* Sidebar del organizador: riel pegado al borde izquierdo, de alto completo
      (debajo del navbar fijo de 80px). Pisa la version "tarjeta" del jugador
      solo para este rol, sin tocar navbar.css. */
   .sidebar-jugador-flotante.sidebar-organizador-flotante {
      left: 0;
      top: 80px;
      bottom: 0;
      margin: 0;
      height: auto;
      max-height: none;
      width: 88px;
      padding: 24px 0;
      align-items: center;
      border-radius: 0;
      border: none;
      border-right: 1px solid rgba(255, 255, 255, 0.08);
      background: rgba(10, 10, 24, 0.92);
      /* Si el riel queda corto (footer a la vista), los iconos se deslizan adentro */
      overflow-y: auto;
      overflow-x: hidden;
      scrollbar-width: none;
   }

   .sidebar-organizador-flotante::-webkit-scrollbar { width: 0; }
   .sidebar-organizador-flotante nav { flex-shrink: 0; }

   /* Sidebar flotante: marca de item activo y submenu plegable */
   .sidebar-flotante-item.activo,
   .sidebar-flotante-submenu a.activo {
      background: rgba(0, 255, 238, 0.12);
      color: #00FFEE;
   }

   .sidebar-flotante-desplegable.cerrado .sidebar-flotante-submenu { display: none; }
   .sidebar-flotante-desplegable.cerrado .sidebar-flotante-toggle i { transform: rotate(180deg); }

   /* El navbar pasa a mobile en 880px: el flotante se oculta a la par */
   @media (max-width: 880px) {
      .sidebar-jugador-flotante { display: none; }
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
                     <a href="<?php echo URL_BASE; ?>index.php?c=perfil&a=organizador&id=<?php echo (int) ($_SESSION['usuario_id'] ?? 0); ?>"><i class="fa-solid fa-user"></i> Ver mi perfil</a>
                     <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=editarPerfil"><i class="fa-solid fa-pen"></i> Editar perfil</a>
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

   <a href="<?php echo URL_BASE; ?>index.php?c=perfil&a=organizador&id=<?php echo (int) ($_SESSION['usuario_id'] ?? 0); ?>"><i class="fa-solid fa-user"></i> Ver mi perfil</a>
   <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=editarPerfil"><i class="fa-solid fa-pen"></i> Editar perfil</a>

   <div class="etiqueta-seccion">Mi actividad</div>
   <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=dashboard"><i class="fa-solid fa-house"></i> Inicio</a>
   <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=misTorneos"><i class="fa-solid fa-list"></i> Mis torneos</a>
   <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=crear"><i class="fa-solid fa-plus"></i> Crear torneo</a>
   <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=calendario"><i class="fa-solid fa-calendar-days"></i> Calendario</a>
   <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=resultados"><i class="fa-solid fa-clipboard-check"></i> Resultados</a>
   <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=historial"><i class="fa-solid fa-clock-rotate-left"></i> Historial</a>

   <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=auth&a=logout">
                           <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
      <button type="submit" class="cerrar-sesion"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</button>
   </form>

</aside>

<!-- Sidebar de escritorio (se oculta en mobile; sus accesos estan en el panel del avatar) -->
<aside class="sidebar-jugador-flotante sidebar-organizador-flotante" id="sidebar-organizador-flotante" aria-label="Navegación del organizador">
   <nav aria-label="Menú del organizador">
      <ul class="sidebar-flotante-lista">
         <li>
            <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=dashboard" class="sidebar-flotante-item<?php echo $claseActivoOrg('inicio'); ?>" title="Inicio">
               <i class="fa-solid fa-house" aria-hidden="true"></i>
               <span>Inicio</span>
            </a>
         </li>
         <li class="sidebar-flotante-desplegable">
            <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=misTorneos" class="sidebar-flotante-item sidebar-flotante-boton<?php echo $claseActivoOrg('torneos'); ?>" title="Mis torneos">
               <i class="fa-solid fa-trophy" aria-hidden="true"></i>
               <span>Torneos</span>
            </a>
            <button type="button" class="sidebar-flotante-toggle" aria-expanded="true" aria-controls="submenu-torneos" aria-label="Mostrar submenú de torneos">
               <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
            </button>
            <ul class="sidebar-flotante-submenu" id="submenu-torneos">
               <li>
                  <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=crear" class="<?php echo trim($claseActivoOrg('crear')); ?>" title="Crear torneo">
                     <i class="fa-solid fa-plus" aria-hidden="true"></i>
                     <span>Crear</span>
                  </a>
               </li>
            </ul>
         </li>
         <li>
            <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=calendario" class="sidebar-flotante-item<?php echo $claseActivoOrg('calendario'); ?>" title="Calendario">
               <i class="fa-solid fa-calendar-days" aria-hidden="true"></i>
               <span>Calendario</span>
            </a>
         </li>
         <li>
            <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=resultados" class="sidebar-flotante-item<?php echo $claseActivoOrg('resultados'); ?>" title="Resultados">
               <i class="fa-solid fa-chart-simple" aria-hidden="true"></i>
               <span>Resultados</span>
            </a>
         </li>
         <li>
            <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=historial" class="sidebar-flotante-item<?php echo $claseActivoOrg('historial'); ?>" title="Historial">
               <i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i>
               <span>Historial</span>
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
      const toggleSubmenuTorneos = document.querySelector(".sidebar-flotante-toggle");
      if (toggleSubmenuTorneos) {
         toggleSubmenuTorneos.addEventListener("click", () => {
            const item = toggleSubmenuTorneos.closest(".sidebar-flotante-desplegable");
            const cerrado = item.classList.toggle("cerrado");
            toggleSubmenuTorneos.setAttribute("aria-expanded", String(!cerrado));
         });
      }
      /* El riel no pisa el footer: al llegar al footer, el riel se acorta y sus iconos
         pasan a deslizarse adentro (como un carrusel vertical). */
      const rielOrganizador = document.getElementById("sidebar-organizador-flotante");
      const footerPagina = document.querySelector("footer");
      if (rielOrganizador) {
         const ajustarRiel = () => {
            let abajo = 0;
            if (footerPagina) {
               const tope = footerPagina.getBoundingClientRect().top;
               abajo = Math.max(0, window.innerHeight - tope);
            }
            rielOrganizador.style.bottom = abajo + "px";
         };
         window.addEventListener("scroll", ajustarRiel, { passive: true });
         window.addEventListener("resize", ajustarRiel);
         window.addEventListener("load", ajustarRiel);
         ajustarRiel();
      }
   });
</script>