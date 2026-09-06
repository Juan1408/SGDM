"use strict";

/*
=====================================================
   ELEMENTOS
=====================================================
*/

const calendarioLista =
   document.getElementById("calendarioLista");

const filtroTorneo =
   document.getElementById("filtroTorneo");

const filtroEstado =
   document.getElementById("filtroEstadoPartido");

const filtroFecha =
   document.getElementById("filtroFechaPartido");

const buscarPartido =
   document.getElementById("buscarPartido");

const limpiarFiltros =
   document.getElementById(
      "limpiarFiltrosCalendario"
   );

const cantidadResultados =
   document.getElementById(
      "cantidadResultadosCalendario"
   );

const totalPartidosElemento =
   document.getElementById(
      "totalPartidosCalendario"
   );

const pendientesElemento =
   document.getElementById(
      "partidosPendientesCalendario"
   );

const finalizadosElemento =
   document.getElementById(
      "partidosFinalizadosCalendario"
   );

const torneosActivosElemento =
   document.getElementById(
      "torneosConPartidosCalendario"
   );


/*
=====================================================
   UTILIDADES
=====================================================
*/

function escaparTexto(valor) {
   const elemento =
      document.createElement("div");

   elemento.textContent =
      String(valor ?? "");

   return elemento.innerHTML;
}

function normalizarTexto(valor) {
   return String(valor ?? "")
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .toLowerCase()
      .trim();
}

function formatearFechaCalendario(fecha) {
   if (!fecha) {
      return {
         dia: "--",
         mes: "Sin fecha",
         completa: "Sin fecha"
      };
   }

   const valor =
      new Date(`${fecha}T00:00:00`);

   if (Number.isNaN(valor.getTime())) {
      return {
         dia: "--",
         mes: fecha,
         completa: fecha
      };
   }

   return {
      dia: new Intl.DateTimeFormat(
         "es-UY",
         {
            day: "2-digit"
         }
      ).format(valor),

      mes: new Intl.DateTimeFormat(
         "es-UY",
         {
            month: "short"
         }
      ).format(valor),

      completa: new Intl.DateTimeFormat(
         "es-UY",
         {
            weekday: "long",
            day: "2-digit",
            month: "long",
            year: "numeric"
         }
      ).format(valor)
   };
}


/*
=====================================================
   OBTENER PARTIDOS
=====================================================
*/

function obtenerTodosLosPartidos() {
   const partidos = [];

   TORNEOS.forEach((torneo) => {
      const fixture =
         Array.isArray(torneo.fixture)
            ? torneo.fixture
            : [];

      fixture.forEach((ronda) => {
         const encuentros =
            Array.isArray(ronda.matches)
               ? ronda.matches
               : [];

         encuentros.forEach((partido) => {
            partidos.push({
               ...partido,

               torneoId: torneo.id,
               torneoNombre: torneo.nombre,
               disciplina: torneo.disciplina,
               sistema: torneo.sistema,

               rondaNumero: ronda.round,
               rondaNombre:
                  ronda.label ||
                  `Ronda ${ronda.round}`
            });
         });
      });
   });

   return partidos;
}

let partidosCalendario =
   obtenerTodosLosPartidos();


/*
=====================================================
   FILTROS
=====================================================
*/

function obtenerPartidosFiltrados() {
   const torneoSeleccionado =
      filtroTorneo.value;

   const estadoSeleccionado =
      filtroEstado.value;

   const fechaSeleccionada =
      filtroFecha.value;

   const busqueda =
      normalizarTexto(buscarPartido.value);

   return partidosCalendario.filter(
      (partido) => {
         const coincideTorneo =
            torneoSeleccionado === "todos" ||
            String(partido.torneoId) ===
               torneoSeleccionado;

         const coincideEstado =
            estadoSeleccionado === "todos" ||
            partido.status ===
               estadoSeleccionado;

         const coincideFecha =
            !fechaSeleccionada ||
            partido.date ===
               fechaSeleccionada;

         const participantes =
            normalizarTexto(
               `${partido.teamA} ${partido.teamB}`
            );

         const coincideBusqueda =
            !busqueda ||
            participantes.includes(busqueda);

         return (
            coincideTorneo &&
            coincideEstado &&
            coincideFecha &&
            coincideBusqueda
         );
      }
   );
}


/*
=====================================================
   AGRUPAR POR FECHA
=====================================================
*/

function agruparPartidosPorFecha(partidos) {
   return partidos.reduce(
      (grupos, partido) => {
         const clave =
            partido.date || "sin-fecha";

         if (!grupos[clave]) {
            grupos[clave] = [];
         }

         grupos[clave].push(partido);

         return grupos;
      },
      {}
   );
}


/*
=====================================================
   TARJETAS
=====================================================
*/

function crearTarjetaPartido(partido) {
   const finalizado =
      partido.status === "Finalizado" &&
      Number.isInteger(partido.scoreA) &&
      Number.isInteger(partido.scoreB);

   const marcador = finalizado
      ? `
         <div class="calendario-marcador">
            <strong>${partido.scoreA}</strong>
            <span>-</span>
            <strong>${partido.scoreB}</strong>
         </div>
      `
      : `
         <div class="calendario-versus">
            VS
         </div>
      `;

   return `
      <article class="calendario-partido">

         <div class="calendario-partido-info">

            <div class="calendario-partido-etiquetas">

               <span class="calendario-torneo">
                  ${escaparTexto(
                     partido.torneoNombre
                  )}
               </span>

               <span class="calendario-ronda">
                  ${escaparTexto(
                     partido.rondaNombre
                  )}
               </span>

            </div>

            <div class="calendario-enfrentamiento">

               <div class="calendario-equipo">
                  <span class="calendario-equipo-icono">
                     <i class="fa-solid fa-shield-halved"></i>
                  </span>

                  <strong>
                     ${escaparTexto(partido.teamA)}
                  </strong>
               </div>

               ${marcador}

               <div class="calendario-equipo calendario-equipo--derecha">
                  <strong>
                     ${escaparTexto(partido.teamB)}
                  </strong>

                  <span class="calendario-equipo-icono">
                     <i class="fa-solid fa-shield-halved"></i>
                  </span>
               </div>

            </div>

            <div class="calendario-partido-datos">

               <span>
                  <i class="fa-solid fa-clock"></i>
                  ${escaparTexto(
                     partido.time || "Sin hora"
                  )}
               </span>

               <span>
                  <i class="fa-solid fa-location-dot"></i>
                  ${escaparTexto(
                     partido.evento || "A definir"
                  )}
               </span>

               <span>
                  <i class="fa-solid fa-gamepad"></i>
                  ${escaparTexto(partido.disciplina)}
               </span>

            </div>

         </div>

         <div class="calendario-partido-acciones">

            <span class="calendario-estado calendario-estado--${
               finalizado
                  ? "finalizado"
                  : "pendiente"
            }">
               ${
                  finalizado
                     ? "Finalizado"
                     : "Pendiente"
               }
            </span>

            <a
               href="administrar-torneo.html?id=${
                  partido.torneoId
               }&seccion=${
                  finalizado
                     ? "resultados"
                     : "fixture"
               }"
            >
               <i class="fa-solid fa-arrow-up-right-from-square"></i>
               Abrir torneo
            </a>

         </div>

      </article>
   `;
}


/*
=====================================================
   RENDERIZAR
=====================================================
*/

function renderizarCalendario() {
   const filtrados =
      obtenerPartidosFiltrados()
         .sort((a, b) => {
            const fechaA =
               new Date(
                  `${a.date || "9999-12-31"}T${
                     a.time || "23:59"
                  }`
               );

            const fechaB =
               new Date(
                  `${b.date || "9999-12-31"}T${
                     b.time || "23:59"
                  }`
               );

            return fechaA - fechaB;
         });

   cantidadResultados.textContent =
      `${filtrados.length} ${
         filtrados.length === 1
            ? "enfrentamiento"
            : "enfrentamientos"
      }`;

   if (filtrados.length === 0) {
      calendarioLista.innerHTML = `
         <div class="calendario-vacio">

            <i class="fa-solid fa-calendar-xmark"></i>

            <h2>No hay enfrentamientos</h2>

            <p>
               No se encontraron partidos que coincidan
               con los filtros seleccionados.
            </p>

         </div>
      `;

      return;
   }

   const grupos =
      agruparPartidosPorFecha(filtrados);

   calendarioLista.innerHTML =
      Object.entries(grupos)
         .map(([fecha, partidos]) => {
            const fechaFormateada =
               formatearFechaCalendario(
                  fecha === "sin-fecha"
                     ? null
                     : fecha
               );

            return `
               <section class="calendario-dia">

                  <div class="calendario-fecha">

                     <div class="calendario-fecha-caja">
                        <strong>
                           ${fechaFormateada.dia}
                        </strong>

                        <span>
                           ${fechaFormateada.mes}
                        </span>
                     </div>

                     <p>
                        ${fechaFormateada.completa}
                     </p>

                  </div>

                  <div class="calendario-dia-partidos">
                     ${partidos
                        .map(crearTarjetaPartido)
                        .join("")}
                  </div>

               </section>
            `;
         })
         .join("");
}


/*
=====================================================
   RESUMEN
=====================================================
*/

function actualizarResumen() {
   const finalizados =
      partidosCalendario.filter(
         (partido) =>
            partido.status === "Finalizado"
      ).length;

   const pendientes =
      partidosCalendario.length -
      finalizados;

   const torneosConPartidos =
      new Set(
         partidosCalendario.map(
            (partido) =>
               partido.torneoId
         )
      ).size;

   totalPartidosElemento.textContent =
      partidosCalendario.length;

   pendientesElemento.textContent =
      pendientes;

   finalizadosElemento.textContent =
      finalizados;

   torneosActivosElemento.textContent =
      torneosConPartidos;
}


/*
=====================================================
   SELECT DE TORNEOS
=====================================================
*/

function cargarFiltroTorneos() {
   TORNEOS
      .filter((torneo) =>
         Array.isArray(torneo.fixture) &&
         torneo.fixture.length > 0
      )
      .forEach((torneo) => {
         const opcion =
            document.createElement("option");

         opcion.value = torneo.id;
         opcion.textContent = torneo.nombre;

         filtroTorneo.appendChild(opcion);
      });
}


/*
=====================================================
   EVENTOS
=====================================================
*/

[
   filtroTorneo,
   filtroEstado,
   filtroFecha
].forEach((elemento) => {
   elemento.addEventListener(
      "change",
      renderizarCalendario
   );
});

buscarPartido.addEventListener(
   "input",
   renderizarCalendario
);

limpiarFiltros.addEventListener(
   "click",
   () => {
      filtroTorneo.value = "todos";
      filtroEstado.value = "todos";
      filtroFecha.value = "";
      buscarPartido.value = "";

      renderizarCalendario();
   }
);


/*
=====================================================
   INICIO
=====================================================
*/

cargarFiltroTorneos();
actualizarResumen();
renderizarCalendario();