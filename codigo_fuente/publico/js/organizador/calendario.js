"use strict";

/* Filtros en vivo del calendario. El contenido lo arma PHP. */

const partidos = Array.from(document.querySelectorAll("#calendarioLista .calendario-partido"));
const dias = Array.from(document.querySelectorAll("#calendarioLista .calendario-dia"));
const buscar = document.getElementById("buscarPartido");
const filtroTorneo = document.getElementById("filtroTorneo");
const filtroEstado = document.getElementById("filtroEstadoPartido");
const filtroFecha = document.getElementById("filtroFechaPartido");
const limpiar = document.getElementById("limpiarFiltrosCalendario");
const cantidad = document.getElementById("cantidadResultadosCalendario");
const vacio = document.getElementById("calendarioVacio");

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
   const estado = filtroEstado.value;
   const fecha = filtroFecha.value;
   let visibles = 0;

   partidos.forEach((partido) => {
      const coincide =
         (!texto || normalizar(partido.dataset.busqueda).includes(texto)) &&
         (torneo === "todos" || partido.dataset.torneo === torneo) &&
         (estado === "todos" || partido.dataset.estado === estado) &&
         (!fecha || partido.dataset.fecha === fecha);

      partido.style.display = coincide ? "" : "none";
      if (coincide) visibles++;
   });

   /* Un día sin partidos visibles se oculta entero */
   dias.forEach((dia) => {
      dia.style.display = Array.from(dia.querySelectorAll(".calendario-partido")).some((p) => p.style.display !== "none") ? "" : "none";
   });

   cantidad.textContent = `${visibles} ${visibles === 1 ? "enfrentamiento" : "enfrentamientos"}`;

   if (partidos.length) {
      vacio.hidden = visibles > 0;
   }
}

buscar.addEventListener("input", aplicarFiltros);
[filtroTorneo, filtroEstado, filtroFecha].forEach((el) => el.addEventListener("change", aplicarFiltros));

limpiar.addEventListener("click", () => {
   buscar.value = "";
   filtroTorneo.value = "todos";
   filtroEstado.value = "todos";
   filtroFecha.value = "";
   aplicarFiltros();
});
