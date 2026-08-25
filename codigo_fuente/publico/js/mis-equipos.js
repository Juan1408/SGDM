"use strict";

document.addEventListener("DOMContentLoaded", () => {

   const lista = document.getElementById("mis-equipos-lista");
   const mensajeVacio = document.getElementById("mis-equipos-vacio");

   // Equipos de ejemplo donde participás pero NO sos el creador
   // (en un caso real, vendría de la tabla equipo_miembros filtrada
   // por tu usuario_id, donde equipos.creado_por != vos)
   const EQUIPOS_DONDE_PARTICIPO = [
      { id: 2, nombre: "Durazno FC", logo: "../../publico/img/logos/durazno.png" },
      { id: 3, nombre: "Flores Chess Club", logo: "../../publico/img/logos/flores.png" }
   ];

   function crearTarjetaCreador(equipo) {
      return `
         <li>
            <article class="mi-tarjeta-equipo">
               <img src="${equipo.escudo_url || '../../publico/img/logos/canelones.png'}" alt="${equipo.nombre_equipo}">

               <div class="mi-equipo-info">
                  <h2>${equipo.nombre_equipo}</h2>
                  <span class="mi-equipo-badge badge-lider">Líder</span>
               </div>

               <a href="crear-equipo.html" class="boton-secundario">
                  Gestionar
               </a>
            </article>
         </li>
      `;
   }

   function crearTarjetaParticipante(equipo) {
      return `
         <li>
            <article class="mi-tarjeta-equipo">
               <img src="${equipo.logo}" alt="${equipo.nombre}">

               <div class="mi-equipo-info">
                  <h2>${equipo.nombre}</h2>
                  <span class="mi-equipo-badge badge-miembro">Participante</span>
               </div>

               <a href="perfil-equipo.html?id=${equipo.id}" class="boton-secundario">
                  Ver perfil
               </a>
            </article>
         </li>
      `;
   }

   const equipoCreado = localStorage.getItem("equipoGestionado");
   let html = "";

   if (equipoCreado) {
      html += crearTarjetaCreador(JSON.parse(equipoCreado));
   }

   EQUIPOS_DONDE_PARTICIPO.forEach((equipo) => {
      html += crearTarjetaParticipante(equipo);
   });

   if (!html) {
      lista.hidden = true;
      mensajeVacio.classList.remove("oculto");
   } else {
      lista.innerHTML = html;
   }

});
