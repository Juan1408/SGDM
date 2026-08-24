"use strict";

document.addEventListener(
   "navbarPublicoListo",
   () => {
      const menuToggle =
         document.getElementById("menu-toggle");

      const navLinks =
         document.getElementById("nav-links");

      const desplegable =
         document.querySelector(".desplegable");

      const desplegableBtn =
         document.getElementById(
            "torneo-desplegable"
         );

      /*
      ================================================
         MENÚ HAMBURGUESA
      ================================================
      */

      if (menuToggle && navLinks) {
         menuToggle.addEventListener(
            "click",
            () => {
               const abierto =
                  navLinks.classList.toggle(
                     "active"
                  );

               menuToggle.classList.toggle(
                  "active",
                  abierto
               );

               menuToggle.setAttribute(
                  "aria-expanded",
                  String(abierto)
               );
            }
         );
      }

      /*
      ================================================
         DESPLEGABLE DE TORNEOS
      ================================================
      */

      if (desplegable && desplegableBtn) {
         desplegableBtn.addEventListener(
            "click",
            (evento) => {
               evento.stopPropagation();

               const abierto =
                  desplegable.classList.toggle(
                     "open"
                  );

               desplegableBtn.setAttribute(
                  "aria-expanded",
                  String(abierto)
               );
            }
         );

         document.addEventListener(
            "click",
            (evento) => {
               if (
                  !desplegable.contains(
                     evento.target
                  )
               ) {
                  desplegable.classList.remove(
                     "open"
                  );

                  desplegableBtn.setAttribute(
                     "aria-expanded",
                     "false"
                  );
               }
            }
         );
      }

      /*
================================================
   SESIÓN DE JUGADOR (mockup con localStorage)
================================================
*/

const esJugadorLogueado =
   localStorage.getItem("sesionActiva") === "jugador";

const authLinksJugador =
   document.getElementById("auth-links-jugador");

const sidebarFlotante =
   document.getElementById("sidebar-jugador-flotante");

const authLinksInvitado =
   document.querySelector(".auth-links:not(.auth-links--jugador)");

if (esJugadorLogueado) {
   if (authLinksInvitado) authLinksInvitado.classList.add("oculto");
   if (authLinksJugador) authLinksJugador.classList.remove("oculto");
   if (sidebarFlotante) sidebarFlotante.classList.remove("oculto");
}

const NOTIFICACIONES_MOCK = [
   { id: 1, tipo: "solicitud", titulo: "Nueva solicitud", mensaje: "Ignacio Ferreira quiere unirse a tu equipo Fire Wolves", leido: false },
   { id: 2, tipo: "torneo", titulo: "Copa ASCEND", mensaje: "Empiezan los Cuartos de Final el 02 de junio", leido: false },
   { id: 3, tipo: "resultado", titulo: "Resultado cargado", mensaje: "Se registró el resultado de tu partido en Ajedrez Master", leido: false },
   { id: 4, tipo: "torneo", titulo: "Recordatorio", mensaje: "Tu próximo encuentro es mañana a las 18:00", leido: true }
];

const CLAVE_NOTIFICACIONES = "notificacionesJugador";

function cargarNotificaciones() {
   const guardadas = localStorage.getItem(CLAVE_NOTIFICACIONES);
   return guardadas ? JSON.parse(guardadas) : NOTIFICACIONES_MOCK;
}

function guardarNotificaciones(lista) {
   localStorage.setItem(CLAVE_NOTIFICACIONES, JSON.stringify(lista));
}

let notificaciones = cargarNotificaciones();

const iconosPorTipoNotif = {
   solicitud: "fa-user-plus",
   torneo: "fa-trofeo",
   resultado: "fa-clipboard-check"
};

function renderNotificaciones() {
   const lista = document.getElementById("notificaciones-lista");
   const badge = document.querySelector(".badge-notificaciones");
   if (!lista) return;

   const sinLeer = notificaciones.filter((n) => !n.leido).length;

   if (badge) {
      badge.textContent = sinLeer;
      badge.style.display = sinLeer > 0 ? "flex" : "none";
   }

   if (!notificaciones.length) {
      lista.innerHTML = `<p class="notificaciones-vacio">No tenés notificaciones nuevas.</p>`;
      return;
   }

   lista.innerHTML = notificaciones.map((n) => `
      <div class="notificacion-item ${n.leido ? '' : 'no-leida'}" data-id="${n.id}" data-tipo="${n.tipo}">
         <div class="notificacion-icono">
            <i class="fa-solid ${iconosPorTipoNotif[n.tipo] || 'fa-bell'}"></i>
         </div>
         <div class="notificacion-contenido">
            <span class="notificacion-titulo">${n.titulo}</span>
            <span class="notificacion-mensaje">${n.mensaje}</span>
         </div>
         ${n.leido ? '' : '<span class="notificacion-punto"></span>'}
      </div>
   `).join("");
}

const campanaToggle = document.getElementById("campana-toggle");
const panelNotificaciones = document.getElementById("panel-notificaciones");

if (campanaToggle && panelNotificaciones) {
   campanaToggle.addEventListener("click", (evento) => {
      evento.stopPropagation();
      const abierto = panelNotificaciones.classList.toggle("open");
      campanaToggle.setAttribute("aria-expanded", String(abierto));
   });

   renderNotificaciones();
}

const listaNotifEl = document.getElementById("notificaciones-lista");
if (listaNotifEl) {
   listaNotifEl.addEventListener("click", (evento) => {
      const item = evento.target.closest(".notificacion-item");
      if (!item) return;

      const id = Number(item.dataset.id);
      notificaciones = notificaciones.map((n) => n.id === id ? { ...n, leido: true } : n);
      guardarNotificaciones(notificaciones);
      renderNotificaciones();
   });
}

const btnMarcarTodo = document.getElementById("btn-marcar-todo-leido");
if (btnMarcarTodo) {
   btnMarcarTodo.addEventListener("click", () => {
      notificaciones = notificaciones.map((n) => ({ ...n, leido: true }));
      guardarNotificaciones(notificaciones);
      renderNotificaciones();
   });
}

const miPerfilToggle = document.getElementById("mi-perfil-toggle");
const dropdownMiPerfil = document.getElementById("dropdown-mi-perfil");

if (miPerfilToggle && dropdownMiPerfil) {
   miPerfilToggle.addEventListener("click", (evento) => {
      evento.stopPropagation();
      const abierto = dropdownMiPerfil.classList.toggle("open");
      miPerfilToggle.setAttribute("aria-expanded", String(abierto));
   });
}

document.addEventListener("click", () => {
   if (panelNotificaciones) panelNotificaciones.classList.remove("open");
   if (dropdownMiPerfil) dropdownMiPerfil.classList.remove("open");
});

const cerrarSesionBtn = document.getElementById("cerrar-sesion-btn");

if (cerrarSesionBtn) {
   cerrarSesionBtn.addEventListener("click", () => {
      localStorage.removeItem("sesionActiva");
      window.location.href = "../publico/index.html";
   });
}
   }
);