"use strict";

document.addEventListener("DOMContentLoaded", () => {

   const items = Array.from(document.querySelectorAll("#torneos-grid > li"));
   if (items.length === 0) return;

   const chips = document.querySelectorAll(".chips-contenedor .chip");
   const inputBuscar = document.getElementById("buscar-torneo");
   const selectJuego = document.getElementById("filtro-juego");
   const selectFormato = document.getElementById("filtro-formato");
   const selectLugar = document.getElementById("filtro-lugar");
   const selectEstado = document.getElementById("filtro-estado");
   const mensajeVacio = document.getElementById("torneos-sin-resultados");
   const contenedorMas = document.getElementById("torneos-mas");
   const botonMas = document.getElementById("torneos-cargar-mas");

   const POR_PAGINA = 9;
   const POR_CLIC = 3;

   let tipoActivo = "todos";
   let limite = POR_PAGINA;

   //Sin tildes ni mayusculas: "futbol" encuentra "Fútbol"
   function normalizar(texto) {
      return (texto || "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
   }

   function aplicarFiltros() {
      const texto = normalizar(inputBuscar.value.trim());
      let coincidencias = 0;

      items.forEach((li) => {
         const datos = li.dataset;

         const coincide =
            (tipoActivo === "todos" || datos.tipo === tipoActivo) &&
            (!texto || normalizar(datos.busqueda).includes(texto)) &&
            (!selectJuego.value || datos.juego === selectJuego.value) &&
            (!selectFormato.value || datos.formato === selectFormato.value) &&
            (!selectLugar.value || datos.lugar === selectLugar.value) &&
            (!selectEstado.value ||
               (selectEstado.value === "proximos"
                  ? datos.proximo === "1"
                  : datos.estado === selectEstado.value));

         if (coincide) coincidencias++;
         li.style.display = coincide && coincidencias <= limite ? "" : "none";
      });

      mensajeVacio.classList.toggle("oculto", coincidencias > 0);
      contenedorMas.classList.toggle("oculto", coincidencias <= limite);
   }

   //Cualquier cambio de filtro vuelve a la primera "pagina" de resultados
   function reiniciar() {
      limite = POR_PAGINA;
      aplicarFiltros();
   }

   chips.forEach((chip) => {
      chip.addEventListener("click", () => {
         chips.forEach((c) => c.classList.remove("active"));
         chip.classList.add("active");
         tipoActivo = chip.dataset.tipo;
         reiniciar();
      });
   });

   inputBuscar.addEventListener("input", reiniciar);
   [selectJuego, selectFormato, selectLugar, selectEstado].forEach((select) => {
      select.addEventListener("change", reiniciar);
   });

   botonMas.addEventListener("click", () => {
      limite += POR_CLIC;
      aplicarFiltros();
   });

   aplicarFiltros();
});