"use strict";

document.addEventListener("DOMContentLoaded", () => {

   const listaTorneos = document.getElementById("lista-torneos-favoritos");
   const mensajeTorneosVacio = document.getElementById("mensaje-sin-torneos-favoritos");

   const listaEquipos = document.getElementById("lista-equipos-favoritos");
   const mensajeEquiposVacio = document.getElementById("mensaje-sin-equipos-favoritos");

   const CLAVE_TORNEOS_FAV = "torneosFavoritos";
   const CLAVE_EQUIPOS_FAV = "equiposFavoritos";

   const TORNEOS_FAV_MOCK = [
      {
         id: "copa-ascend",
         nombre: "Virtual Odyssey Chronicles",
         imagen: "../../publico/img/torneos/card1.jpg",
         meta: "eSports · 26 mayo 2026"
      },
      {
         id: "futbol-5",
         nombre: "Copa Futbol 5",
         imagen: "../../publico/img/torneos/card3.png",
         meta: "Deportes mixtos · 08 julio 2026"
      }
   ];

   const EQUIPOS_FAV_MOCK = [
      {
         id: 1,
         nombre: "Fire Wolves",
         logo: "../../publico/img/logos/logo.png",
         categoria: "eSports"
      }
   ];

   function cargar(clave, mock) {
      const guardado = localStorage.getItem(clave);
      return guardado ? JSON.parse(guardado) : mock;
   }

   let torneosFav = cargar(CLAVE_TORNEOS_FAV, TORNEOS_FAV_MOCK);
   let equiposFav = cargar(CLAVE_EQUIPOS_FAV, EQUIPOS_FAV_MOCK);

   function guardar(clave, datos) {
      localStorage.setItem(clave, JSON.stringify(datos));
   }

   function renderTorneos() {
      if (!torneosFav.length) {
         listaTorneos.innerHTML = "";
         mensajeTorneosVacio.classList.remove("oculto");
         return;
      }

      mensajeTorneosVacio.classList.add("oculto");

      listaTorneos.innerHTML = torneosFav.map((t) => `
         <li>
            <article class="torneos-organiza-tarjeta">
               <button type="button" class="boton-quitar-favorito" data-id="${t.id}" title="Quitar de favoritos">
                  <i class="fa-solid fa-heart-crack"></i>
               </button>
               <a href="detalle-torneo.html?id=${t.id}">
                  <img src="${t.imagen}" alt="${t.nombre}">
                  <div class="torneo-organiza-info">
                     <h3>${t.nombre}</h3>
                     <span>${t.meta}</span>
                  </div>
               </a>
            </article>
         </li>
      `).join("");
   }

   function renderEquipos() {
      if (!equiposFav.length) {
         listaEquipos.innerHTML = "";
         mensajeEquiposVacio.classList.remove("oculto");
         return;
      }

      mensajeEquiposVacio.classList.add("oculto");

      listaEquipos.innerHTML = equiposFav.map((e) => `
         <li>
            <article class="mi-tarjeta-equipo">
               <button type="button" class="boton-quitar-favorito" data-id="${e.id}" title="Quitar de favoritos">
                  <i class="fa-solid fa-heart-crack"></i>
               </button>
               <img src="${e.logo}" alt="${e.nombre}">
               <div class="mi-equipo-info">
                  <h3>${e.nombre}</h3>
                  <span class="mi-equipo-badge badge-miembro">${e.categoria}</span>
               </div>
               <a href="perfil-equipo.html" class="boton-secundario">Ver perfil</a>
            </article>
         </li>
      `).join("");
   }

   listaTorneos.addEventListener("click", (evento) => {
      const boton = evento.target.closest(".boton-quitar-favorito");
      if (!boton) return;

      const id = boton.dataset.id;
      torneosFav = torneosFav.filter((t) => String(t.id) !== id);
      guardar(CLAVE_TORNEOS_FAV, torneosFav);
      renderTorneos();
   });

   listaEquipos.addEventListener("click", (evento) => {
      const boton = evento.target.closest(".boton-quitar-favorito");
      if (!boton) return;

      const id = Number(boton.dataset.id);
      equiposFav = equiposFav.filter((e) => e.id !== id);
      guardar(CLAVE_EQUIPOS_FAV, equiposFav);
      renderEquipos();
   });

   renderTorneos();
   renderEquipos();

});
