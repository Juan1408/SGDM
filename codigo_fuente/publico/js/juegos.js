"use strict";

document.addEventListener("DOMContentLoaded", () => {

   const grid = document.getElementById("juegos-grillas");
   const tarjetas = Array.from(document.querySelectorAll(".juegos-grillas > li"));
   const mensajeVacio = document.getElementById("juegos-sin-resultados");

   const chips = document.querySelectorAll(".chip");
   const inputBuscar = document.getElementById("buscar-juego");
   const selectModalidad = document.getElementById("filtro-modalidad");
   const selectOrden = document.getElementById("filtro-orden");

   let categoriaActiva = "todos";

   function aplicarFiltros() {
      const texto = inputBuscar.value.trim().toLowerCase();
      const modalidad = selectModalidad.value;

      let visibles = 0;

      tarjetas.forEach((li) => {
         const tarjeta = li.querySelector(".tarjeta-juego");
         const nombre = tarjeta.querySelector("h2").textContent.toLowerCase();
         const categoria = tarjeta.dataset.categoria;
         const modalidadTarjeta = tarjeta.dataset.modalidad;

         const coincideCategoria = categoriaActiva === "todos" || categoria === categoriaActiva;
         const coincideTexto = !texto || nombre.includes(texto);
         const coincideModalidad = !modalidad || modalidadTarjeta === modalidad;

         const visible = coincideCategoria && coincideTexto && coincideModalidad;
         li.style.display = visible ? "" : "none";

         if (visible) visibles++;
      });

      mensajeVacio.classList.toggle("oculto", visibles > 0);
   }

   function aplicarOrden() {
      const orden = selectOrden.value;
      if (!orden) return;

      const ordenadas = [...tarjetas].sort((a, b) => {
         const nombreA = a.querySelector(".tarjeta-juego h2").textContent;
         const nombreB = b.querySelector(".tarjeta-juego h2").textContent;
         return orden === "az"
            ? nombreA.localeCompare(nombreB)
            : nombreB.localeCompare(nombreA);
      });

      ordenadas.forEach((li) => grid.appendChild(li));
   }

   chips.forEach((chip) => {
      chip.addEventListener("click", () => {
         chips.forEach((c) => c.classList.remove("active"));
         chip.classList.add("active");
         categoriaActiva = chip.dataset.categoria;
         aplicarFiltros();
      });
   });

   inputBuscar.addEventListener("input", aplicarFiltros);
   selectModalidad.addEventListener("change", aplicarFiltros);
   selectOrden.addEventListener("change", aplicarOrden);

});
