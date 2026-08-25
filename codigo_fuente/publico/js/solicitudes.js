"use strict";

document.addEventListener("DOMContentLoaded", () => {

   const lista = document.getElementById("lista-solicitudes-recibidas");
   const mensajeVacio = document.getElementById("mensaje-sin-solicitudes");

   const CLAVE_SOLICITUDES = "solicitudesRecibidas";
   const CLAVE_EQUIPOS_UNIDO = "equiposUnido"; // equipos a los que ya aceptaste unirte

   const SOLICITUDES_MOCK = [
      {
         id: 201,
         equipoNombre: "Durazno FC",
         equipoLogo: "../../publico/img/logos/durazno.png",
         invitadoPor: "Sofía Méndez"
      },
      {
         id: 202,
         equipoNombre: "Flores Chess Club",
         equipoLogo: "../../publico/img/logos/flores.png",
         invitadoPor: "Bruno Castro"
      }
   ];

   function cargarSolicitudes() {
      const guardadas = localStorage.getItem(CLAVE_SOLICITUDES);
      return guardadas ? JSON.parse(guardadas) : SOLICITUDES_MOCK;
   }

   let solicitudes = cargarSolicitudes();

   function guardarSolicitudes() {
      localStorage.setItem(CLAVE_SOLICITUDES, JSON.stringify(solicitudes));
   }

   function render() {
      if (!solicitudes.length) {
         lista.innerHTML = "";
         mensajeVacio.classList.remove("oculto");
         return;
      }

      mensajeVacio.classList.add("oculto");

      lista.innerHTML = solicitudes.map((s) => `
         <li class="fila-jugador" data-id="${s.id}">
            <img class="fila-jugador-avatar" src="${s.equipoLogo}" alt="${s.equipoNombre}">
            <div class="fila-jugador-info">
               <span class="fila-jugador-nombre">${s.equipoNombre}</span>
               <span class="fila-jugador-invitado-por">Invitado por ${s.invitadoPor}</span>
            </div>
            <div class="fila-jugador-acciones">
               <button type="button" class="boton-aceptar-solicitud" data-id="${s.id}">Aceptar</button>
               <button type="button" class="boton-rechazar-solicitud" data-id="${s.id}">Rechazar</button>
            </div>
         </li>
      `).join("");
   }

   lista.addEventListener("click", (evento) => {
      const id = Number(evento.target.dataset.id);
      if (!id) return;

      const solicitud = solicitudes.find((s) => s.id === id);
      if (!solicitud) return;

      if (evento.target.classList.contains("boton-aceptar-solicitud")) {
         const equiposUnido = JSON.parse(localStorage.getItem(CLAVE_EQUIPOS_UNIDO) || "[]");
         equiposUnido.push({
            id: solicitud.id,
            nombre: solicitud.equipoNombre,
            logo: solicitud.equipoLogo
         });
         localStorage.setItem(CLAVE_EQUIPOS_UNIDO, JSON.stringify(equiposUnido));

         alert(`Te uniste a ${solicitud.equipoNombre}. Ya lo vas a ver en "Mis Equipos".`);
      }

      if (evento.target.classList.contains("boton-rechazar-solicitud")) {
         if (!confirm(`¿Rechazar la invitación de ${solicitud.equipoNombre}?`)) return;
      }

      solicitudes = solicitudes.filter((s) => s.id !== id);
      guardarSolicitudes();
      render();
   });

   render();

});
