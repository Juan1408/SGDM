<?php
// La sesión la decide PHP: si hay un jugador (rol 3) u organizador (rol 2) logueado se muestra su perfil.
// El administrador (rol 1) se ve como visitante por ahora.
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
   session_start();
}
$rolNavbar = isset($_SESSION['usuario_id']) ? (int) ($_SESSION['rol_id'] ?? 0) : 0;
$esJugadorNavbar = $rolNavbar === 3;
$esOrganizadorNavbar = $rolNavbar === 2;
$haySesionNavbar = $esJugadorNavbar || $esOrganizadorNavbar;
$urlPerfilNavbar = URL_BASE . 'index.php?c=' . ($esOrganizadorNavbar ? 'panelOrganizador' : 'panelJugador') . '&a=perfil';
?>
<!-------------------------------------->
<!-- NAVBAR PÚBLICO -------------------->
<!-------------------------------------->

<nav class="navbar">
   <div class="navbar-contenedor">

   <a class="logo-contenedor" href="<?php echo URL_BASE; ?>index.php?c=inicio&a=index">
   <img src="<?php echo URL_BASE; ?>img/logos/ascend-logo01.png" alt="Logo de ASCEND" class="logo-navbar">
</a>

      <button class="navbar-toggle" id="menu-toggle" type="button" aria-label="Abrir menú de navegación"
         aria-expanded="false">
         <span class="bar"></span>
         <span class="bar"></span>
         <span class="bar"></span>
      </button>

      <ul class="navbar-menu" id="nav-links">

         <li>
            <a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=index">
               Inicio
            </a>
         </li>

         <li class="desplegable">

            <button class="boton-desplegable" id="torneo-desplegable" type="button" aria-expanded="false">
               Torneos
               <i class="fa-solid fa-chevron-down"></i>
            </button>

            <div class="contenido-desplegable">

               <a href="<?php echo URL_BASE; ?>index.php?c=torneo&a=catalogoPublico">
                  Todos los Torneos
               </a>

               <a href="<?php echo URL_BASE; ?>index.php?c=torneo&a=detalle">
                  Detalle Torneos
               </a>

               <a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=juegos">
                  Juegos
               </a>

            </div>

         </li>

         <li>
            <a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=rankings">
               Rankings
            </a>
         </li>

         <li>
            <a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=nosotros">
               Nosotros
            </a>
         </li>

         <li>
            <a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=contacto">
               Contacto
            </a>
         </li>

         <?php if (!$haySesionNavbar): ?>

         <li class="auth-links">

            <a href="<?php echo URL_BASE; ?>index.php?c=auth&a=mostrarLogin" class="boton-login">Iniciar Sesión</a>

            <a href="<?php echo URL_BASE; ?>index.php?c=auth&a=mostrarRegistro" class="boton-registro">
               Registrarse
            </a>
         </li>

         <?php else: ?>

         <li class="auth-links auth-links--jugador" id="auth-links-jugador">

   <div class="notificaciones-jugador">
      <button type="button" class="boton-campana" id="campana-toggle" aria-label="Notificaciones" aria-expanded="false">
         <i class="fa-solid fa-bell"></i>
         <?php if ($esJugadorNavbar): ?>
            <span class="badge-notificaciones">3</span>
         <?php else: ?>
            <span class="badge-notificaciones oculto">0</span>
         <?php endif; ?>
      </button>
      <div class="panel-notificaciones" id="panel-notificaciones">
   <div class="notificaciones-header">
      <h4>Notificaciones</h4>
      <?php if ($esJugadorNavbar): ?>
         <button type="button" id="btn-marcar-todo-leido">Marcar todo leído</button>
      <?php endif; ?>
   </div>
   <?php if ($esJugadorNavbar): ?>
      <div class="notificaciones-lista" id="notificaciones-lista">
         <!-- Se llena por JS -->
      </div>
   <?php else: ?>
      <div class="notificaciones-lista">
         <p class="notificaciones-vacio">No tenés notificaciones nuevas.</p>
      </div>
   <?php endif; ?>
</div>
   </div>

   <div class="perfil-jugador-menu">
      <a href="<?php echo $urlPerfilNavbar; ?>" class="boton-mi-perfil" style="text-decoration: none;">
         <i class="fa-solid fa-circle-user"></i>
         <?php echo $esOrganizadorNavbar ? 'Organizador' : 'Jugador'; ?>
      </a>
   </div>

</li>

         <?php endif; ?>

      </ul>

   </div>

</nav>


<?php if ($esJugadorNavbar): ?>
<aside
   class="sidebar-jugador-flotante"
   id="sidebar-jugador-flotante"
   aria-label="Navegación del jugador"
>

   <nav aria-label="Menú del jugador">

      <ul class="sidebar-flotante-lista">

                  <li>
            <a   href="<?php echo URL_BASE; ?>index.php?c=equipo&a=solicitudes"
               class="sidebar-flotante-item"
               title="Solicitudes"
            >
               <i class="fa-solid fa-user-plus" aria-hidden="true"></i>
               <span>Solicitudes</span>
            </a>
         </li>


         <li class="sidebar-flotante-desplegable">

            
            <a   href="<?php echo URL_BASE; ?>index.php?c=equipo&a=index"
               class="sidebar-flotante-item sidebar-flotante-boton"
               title="Mis equipos"
            >
               <i class="fa-solid fa-people-group" aria-hidden="true"></i>
               <span>Equipos</span>
            </a>

            <button
               type="button"
               class="sidebar-flotante-toggle"
               aria-expanded="true"
               aria-controls="submenu-equipos"
               aria-label="Mostrar submenú de equipos"
            >
               <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
            </button>

            <ul
               class="sidebar-flotante-submenu"
               id="submenu-equipos"
            >
               <li>
                  
                  <a   href="<?php echo URL_BASE; ?>index.php?c=equipo&a=crear"
                     title="Crear equipo"
                  >
                     <i class="fa-solid fa-plus" aria-hidden="true"></i>
                     <span>Crear equipo</span>
                  </a>
               </li>
            </ul>

         </li>


         <li>
            
            <a   href="<?php echo URL_BASE; ?>index.php?c=panelJugador&a=perfil#historial"
               class="sidebar-flotante-item"
               title="Historial"
            >
               <i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i>
               <span>Historial</span>
            </a>
         </li>


         <li>
            
             <a  href="<?php echo URL_BASE; ?>index.php?c=guardados&a=index"
               class="sidebar-flotante-item"
               title="Guardados"
            >
               <i class="fa-solid fa-bookmark" aria-hidden="true"></i>
               <span>Guardados</span>
            </a>
         </li>

      </ul>

   </nav>

</aside>
<?php endif; ?>