document.addEventListener('partialsListos', () => {

   /* SUBMENÚS DEL SIDEBAR */
   const botonesDesplegables = document.querySelectorAll('.sidebar-desplegable-btn');

   botonesDesplegables.forEach((boton) => {
      boton.addEventListener('click', () => {
         const item = boton.closest('.sidebar-item-desplegable');
         if (!item) return;
         const sidebar = document.querySelector('.dashboard-sidebar');
         if (sidebar && !sidebar.classList.contains('expanded')) {
            // Expandir el sidebar automáticamente
            sidebar.classList.add('expanded');
            // Y luego abrir este menú
            item.classList.add('abierto');
         } else {
            item.classList.toggle('abierto');
         }
         boton.setAttribute('aria-expanded', item.classList.contains('abierto') ? 'true' : 'false');
      });
   });

   /* NOTIFICACIONES DEL NAVBAR */
   const btnNotificaciones = document.getElementById('btnNotificaciones');
   const contenedorNotificaciones = document.querySelector('.navbar-notificaciones');

   if (btnNotificaciones && contenedorNotificaciones) {
      btnNotificaciones.addEventListener('click', (e) => {
         e.stopPropagation();

         const abierto = contenedorNotificaciones.classList.toggle('abierto');
         btnNotificaciones.setAttribute('aria-expanded', abierto);
      });

      document.addEventListener('click', (e) => {
         if (
            contenedorNotificaciones.classList.contains('abierto') &&
            !contenedorNotificaciones.contains(e.target)
         ) {
            contenedorNotificaciones.classList.remove('abierto');
            btnNotificaciones.setAttribute('aria-expanded', 'false');
         }
      });

      document.addEventListener('keydown', (e) => {
         if (e.key === 'Escape' && contenedorNotificaciones.classList.contains('abierto')) {
            contenedorNotificaciones.classList.remove('abierto');
            btnNotificaciones.setAttribute('aria-expanded', 'false');
         }
      });
   }

   /* Marcar todo como leído (solo visual, sin backend) */
   const btnMarcarLeido = document.querySelector('.navbar-notificaciones-marcar');

   if (btnMarcarLeido) {
      btnMarcarLeido.addEventListener('click', () => {
         document
            .querySelectorAll('.navbar-notificaciones-item-sin-leer')
            .forEach((item) => item.classList.remove('navbar-notificaciones-item-sin-leer'));

         const contador = document.querySelector('.navbar-notificaciones-contador');
         if (contador) contador.remove();
      });
   }

   /* TOGGLE MOBILE */
   const toggle = document.getElementById('sidebar-toggle');
   const sidebar = document.querySelector('.dashboard-sidebar');

   if (toggle && sidebar) {
      toggle.addEventListener('click', (e) => {
         e.stopPropagation();
         sidebar.classList.toggle('expanded');
      });

      document.addEventListener('click', (e) => {
         if (sidebar.classList.contains('expanded') && !sidebar.contains(e.target)) {
            sidebar.classList.remove('expanded');
         }
      });
   }

   /* MARCAR LINK ACTIVO SEGÚN LA PÁGINA ACTUAL */
 /* MARCAR LINK ACTIVO SEGÚN LA PÁGINA ACTUAL */
const paginaActual = window.location.pathname
   .split('/')
   .pop()
   .replace('.html', '');

const accionMvc = new URLSearchParams(window.location.search).get('a');
const paginaMvc = {
   perfil: 'perfil',
   dashboard: 'dashboard',
   misTorneos: 'mis-torneos',
   crear: 'crear-torneo',
   gestionar: 'mis-torneos',
   resultados: 'resultados',
   historial: 'historial',
   calendario: 'calendario'
};

const equivalenciasPaginas = {
   'administrar-torneo': 'mis-torneos'
};

const paginaSidebar = paginaMvc[accionMvc]
   || equivalenciasPaginas[paginaActual]
   || paginaActual;

document.querySelectorAll('.sidebar-link').forEach((link) => {
   if (link.dataset.page === paginaSidebar) {
      link.classList.add('active');

      const submenuPadre = link.closest('.sidebar-item-desplegable');

      if (submenuPadre) {
         submenuPadre.classList.add('abierto');
         submenuPadre.querySelector('.sidebar-desplegable-btn')?.setAttribute('aria-expanded', 'true');
      }
   }
})
})
