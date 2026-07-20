"use strict";

/*
=====================================================
   HERO SLIDER
   Rota automáticamente entre las imágenes de fondo
   del hero (.hero-slide), agregando/quitando la
   clase "active" que ya tiene la transición en CSS.
=====================================================
*/

document.addEventListener("DOMContentLoaded", () => {

   const slides = document.querySelectorAll(".hero-slide");

   if (slides.length < 2) {
      return;
   }

   let indiceActual = 0;
   const INTERVALO_MS = 5000;

   setInterval(() => {
      slides[indiceActual].classList.remove("active");

      indiceActual = (indiceActual + 1) % slides.length;

      slides[indiceActual].classList.add("active");
   }, INTERVALO_MS);

});
