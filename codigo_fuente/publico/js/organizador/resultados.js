"use strict";

/* Filtros en vivo de resultados. El contenido lo arma PHP. */

const items = Array.from(document.querySelectorAll("#resultadosLista .resultado-item"));
const buscar = document.getElementById("buscarResultado");
const filtroTorneo = document.getElementById("filtroTorneoResultados");
const filtroFecha = document.getElementById("filtroFechaResultados");
const limpiar = document.getElementById("limpiarFiltrosResultados");
const cantidad = document.getElementById("cantidadResultados");
const vacio = document.getElementById("resultadosVacio");

function normalizar(valor) {
   return String(valor ?? "")
      .normalize("NFD")
      .replace(/[̀-ͯ]/g, "")
      .toLowerCase()
      .trim();
}

function aplicarFiltros() {
   const texto = normalizar(buscar.value);
   const torneo = filtroTorneo.value;
   const fecha = filtroFecha.value;
   let visibles = 0;

   items.forEach((item) => {
      const coincide =
         (!texto || normalizar(item.dataset.busqueda).includes(texto)) &&
         (torneo === "todos" || item.dataset.torneo === torneo) &&
         (!fecha || item.dataset.fecha === fecha);

      item.style.display = coincide ? "" : "none";
      if (coincide) visibles++;
   });

   cantidad.textContent = `${visibles} resultado${visibles === 1 ? "" : "s"}`;

   if (items.length) {
      vacio.hidden = visibles > 0;
   }
}

buscar.addEventListener("input", aplicarFiltros);
filtroTorneo.addEventListener("change", aplicarFiltros);
filtroFecha.addEventListener("change", aplicarFiltros);

limpiar.addEventListener("click", () => {
   buscar.value = "";
   filtroTorneo.value = "todos";
   filtroFecha.value = "";
   aplicarFiltros();
});
