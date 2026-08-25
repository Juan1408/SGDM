"use strict";

/*
=====================================================
   ELEMENTOS
=====================================================
*/

const resultadosLista =
   document.getElementById("resultadosLista");

const filtroTorneo =
   document.getElementById("filtroTorneoResultados");

const filtroFecha =
   document.getElementById("filtroFechaResultados");

const buscarResultado =
   document.getElementById("buscarResultado");

const limpiarFiltros =
   document.getElementById("limpiarFiltrosResultados");

const cantidadResultados =
   document.getElementById("cantidadResultados");

const totalElemento =
   document.getElementById("totalResultadosCargados");

const estaSemanaElemento =
   document.getElementById("resultadosEstaSemana");

const torneosConResultadosElemento =
   document.getElementById("torneosConResultados");

/*
=====================================================
   UTILIDADES
=====================================================
*/

function normalizarTexto(valor) {
   return String(valor ?? "")
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .toLowerCase()
      .trim();
}

function formatearFechaResultado(fecha) {
   if (!fecha) return "";
   const [anio, mes, dia] = fecha.split("-");
   const fechaObj = new Date(anio, mes - 1, dia);

   return fechaObj.toLocaleDateString("es-UY", {
      day: "numeric",
      month: "short",
      year: "numeric"
   });
}

/*
=====================================================
   EXTRAER TODOS LOS RESULTADOS DEL MOCK TORNEOS
=====================================================
*/

function obtenerTodosLosResultados() {
   const resultados = [];

   TORNEOS.forEach((torneo) => {
      (torneo.fixture || []).forEach((ronda) => {
         (ronda.matches || []).forEach((partido) => {

            const tieneResultado =
               partido.status === "Finalizado" &&
               partido.scoreA !== null &&
               partido.scoreA !== undefined &&
               partido.scoreB !== null &&
               partido.scoreB !== undefined;

            if (!tieneResultado) return;

            resultados.push({
               torneoId: torneo.id,
               torneoNombre: torneo.nombre,
               ronda: ronda.label,
               teamA: partido.teamA,
               teamB: partido.teamB,
               scoreA: partido.scoreA,
               scoreB: partido.scoreB,
               fecha: partido.date
            });
         });
      });
   });

   return resultados.sort((a, b) => new Date(b.fecha) - new Date(a.fecha));
}

/*
=====================================================
   RENDER
=====================================================
*/

function renderResultados() {
   const todos = obtenerTodosLosResultados();

   const texto = normalizarTexto(buscarResultado.value);
   const torneoSeleccionado = filtroTorneo.value;
   const fechaSeleccionada = filtroFecha.value;

   const filtrados = todos.filter((r) => {
      const coincideTexto = !texto ||
         normalizarTexto(r.teamA).includes(texto) ||
         normalizarTexto(r.teamB).includes(texto);

      const coincideTorneo = torneoSeleccionado === "todos" ||
         String(r.torneoId) === torneoSeleccionado;

      const coincideFecha = !fechaSeleccionada || r.fecha === fechaSeleccionada;

      return coincideTexto && coincideTorneo && coincideFecha;
   });

   cantidadResultados.textContent =
      `${filtrados.length} resultado${filtrados.length === 1 ? "" : "s"}`;

   if (!filtrados.length) {
      resultadosLista.innerHTML = `
         <div class="resultados-vacio">
            <i class="fa-solid fa-clipboard-question"></i>
            <h2>Sin resultados</h2>
            <p>No hay resultados cargados con esos filtros.</p>
         </div>
      `;
      return;
   }

   resultadosLista.innerHTML = filtrados.map((r) => {
      const ganaA = r.scoreA > r.scoreB;
      const ganaB = r.scoreB > r.scoreA;

      return `
         <article class="resultado-item">

            <div class="resultado-info">

               <div class="resultado-etiquetas">
                  <span class="resultado-torneo">${r.torneoNombre}</span>
                  <span class="resultado-fecha">${r.ronda} · ${formatearFechaResultado(r.fecha)}</span>
               </div>

               <div class="resultado-enfrentamiento">
                  <div class="resultado-equipo ${ganaA ? "resultado-equipo--ganador" : ""}">
                     <strong>${r.teamA}</strong>
                  </div>

                  <div class="resultado-marcador">
                     <span>${r.scoreA}</span>
                     <span>-</span>
                     <span>${r.scoreB}</span>
                  </div>

                  <div class="resultado-equipo resultado-equipo--derecha ${ganaB ? "resultado-equipo--ganador" : ""}">
                     <strong>${r.teamB}</strong>
                  </div>
               </div>

            </div>

            <div class="resultado-acciones">
               <a href="administrar-torneo.html?id=${r.torneoId}&seccion=resultados">
                  <i class="fa-solid fa-arrow-up-right-from-square"></i>
                  Ver torneo
               </a>
            </div>

         </article>
      `;
   }).join("");
}

function actualizarResumen() {
   const todos = obtenerTodosLosResultados();

   totalElemento.textContent = todos.length;

   const hoy = new Date();
   const haceUnaSemana = new Date();
   haceUnaSemana.setDate(hoy.getDate() - 7);

   const estaSemana = todos.filter((r) => {
      const fecha = new Date(r.fecha);
      return fecha >= haceUnaSemana && fecha <= hoy;
   });

   estaSemanaElemento.textContent = estaSemana.length;

   const torneosUnicos = new Set(todos.map((r) => r.torneoId));
   torneosConResultadosElemento.textContent = torneosUnicos.size;
}

function poblarFiltroTorneos() {
   const torneosConResultados = TORNEOS.filter((torneo) =>
      (torneo.fixture || []).some((ronda) =>
         (ronda.matches || []).some((p) => p.status === "Finalizado")
      )
   );

   filtroTorneo.innerHTML = `
      <option value="todos">Todos los torneos</option>
      ${torneosConResultados.map((t) => `<option value="${t.id}">${t.nombre}</option>`).join("")}
   `;
}

/*
=====================================================
   EVENTOS
=====================================================
*/

buscarResultado.addEventListener("input", renderResultados);
filtroTorneo.addEventListener("change", renderResultados);
filtroFecha.addEventListener("change", renderResultados);

limpiarFiltros.addEventListener("click", () => {
   buscarResultado.value = "";
   filtroTorneo.value = "todos";
   filtroFecha.value = "";
   renderResultados();
});

/*
=====================================================
   INICIO
=====================================================
*/

poblarFiltroTorneos();
actualizarResumen();
renderResultados();
