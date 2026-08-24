"use strict";

// Lee el ?id= de la URL. Si no hay ninguno, usa "copa-ascend" por defecto.
function obtenerIdTorneoDesdeURL() {
   const params = new URLSearchParams(window.location.search);
   return params.get("id") || "copa-ascend";
}

// Rellena el header, meta, info, premios, participantes, miembros y ranking
function poblarDetallesTorneo(torneo) {
   const esEquipo = torneo.tipoParticipacion === "equipos";

   const setTexto = (id, valor) => {
      const el = document.getElementById(id);
      if (el) el.textContent = valor;
   };

   const setImagen = (id, src) => {
      const el = document.getElementById(id);
      if (el) el.src = src;
   };

   setImagen("hero-logo-1", torneo.equipo1.logo);
   setImagen("hero-logo-2", torneo.equipo2.logo);
   setTexto("hero-equipo-1", torneo.equipo1.nombre);
   setTexto("hero-equipo-2", torneo.equipo2.nombre);
   setTexto("hero-subtitulo", torneo.subtitulo);
   setTexto("hero-fechas", torneo.fechas);

   setTexto("meta-formato", torneo.meta.formato);
   setTexto("meta-equipo", torneo.meta.equipos);
   setTexto("meta-estado", torneo.meta.estado);

   setTexto("info-descripcion", torneo.descripcion);
   setTexto("info-reglas", torneo.reglas);

   setTexto("primer-premio", torneo.premios.primero);
   setTexto("segundo-premio", torneo.premios.segundo);
   setTexto("tercer-premio", torneo.premios.tercero);

   // --- Equipos/Participantes que participan ---
   const gridParticipantes = document.getElementById("participantes-grid");
   if (gridParticipantes) {
      gridParticipantes.innerHTML = torneo.participantes.map((p) => `
   <li>
      <article class="participantes-tarjeta">
         <img src="${p.imagen}" alt="${p.nombre}">
         <div class="participantes-info">
            <h3>${p.nombre}</h3>
            <p>${p.deporte}</p>
            <p>${p.cantidad}</p>
            <p>${p.puntos}</p>
         </div>
      </article>
   </li>
`).join("");
   }

   // --- Miembros del equipo (solo si es de equipos) ---
   const gridMiembros = document.getElementById("miembros-grilla");
   if (gridMiembros && torneo.miembros) {
   gridMiembros.innerHTML = torneo.miembros.map((m) => `
   <li>
      <article class="miembro-tarjeta">
         <div class="miembro-foto">
            <div class="miembro-fondo"></div>
            <img src="${m.foto}" alt="${m.nombre}">
         </div>
         <h3>${m.nombre}</h3>
      </article>
   </li>
`).join("");
   }

   // --- Rankings del equipo / individual ---
   const filasRanking = document.getElementById("ranking-filas");
   if (filasRanking && torneo.rankingEquipo) {
         filasRanking.innerHTML = torneo.rankingEquipo.filas.map((fila, indice) => {
          const columnaLogo = esEquipo ? `
          <td class="ranking-equipo-logo">
         <img src="${torneo.rankingEquipo.logoEquipo}">
         </td>
       ` : "";

   return `
      <tr class="ranking-row">
         ${columnaLogo}
         <td>${indice + 1}</td>
         <td class="ranking-jugador">
            <img src="${fila.jugadorFoto}">
            <span>${fila.jugadorNombre}</span>
         </td>
         <td>${fila.juego}</td>
         <td>${fila.partidas}</td>
         <td class="ranking-puntos">${fila.puntos}</td>
      </tr>
   `;
      }).join("");
   }

   // --- Ajustar textos y layout según sea de equipos o individual ---
   const tituloParticipantes = document.getElementById("titulo-participantes");
   if (tituloParticipantes) {
      tituloParticipantes.textContent = esEquipo ? "Equipos que participan" : "Participantes";
   }

   const tituloRanking = document.getElementById("titulo-ranking");   // <-- NUEVO
   if (tituloRanking) {                                                // <-- NUEVO
      tituloRanking.textContent = esEquipo ? "Rankings del Equipo" : "Ranking de Jugadores";   // <-- NUEVO
   } 

   const seccionMiembros = document.querySelector(".equipo-miembros");
   if (seccionMiembros) {
      seccionMiembros.style.display = esEquipo ? "" : "none";
   }

   const rankingTablaContainer = document.getElementById("ranking-tabla-contenedor");
   const rankingHead = document.querySelector(".ranking-head");
   if (rankingTablaContainer && rankingHead) {
      if (esEquipo) {
   rankingTablaContainer.classList.remove("ranking-individual");
   rankingHead.innerHTML = `
      <th>Equipo</th>
      <th>Pos</th>
      <th>Jugador</th>
      <th>Juego</th>
      <th>Partidas</th>
      <th>Puntos</th>
   `;
} else {
   rankingTablaContainer.classList.add("ranking-individual");
   rankingHead.innerHTML = `
      <th>Pos</th>
      <th>Jugador</th>
      <th>Juego</th>
      <th>Partidas</th>
      <th>Puntos</th>
   `;
}
   }

   // --- Agenda del torneo (pestaña Encuentros) ---
   const listaActividades = document.getElementById("lista-actividades");
   if (listaActividades && torneo.actividades) {

      const iconosPorTipo = {
         ceremonia: "fa-flag",
         partido: "fa-gamepad",
         pausa: "fa-mug-hot",
         premiacion: "fa-trofeo"
      };

      listaActividades.innerHTML = torneo.actividades.map((a) => `
       <li class="actividad-item tipo-${a.tipo}">
      <div class="actividad-fecha">
         <span>${a.fecha}</span>
         <span class="actividad-hora">${a.hora}</span>
      </div>
      <div class="actividad-contenido">
         <div class="actividad-icono">
            <i class="fa-solid ${iconosPorTipo[a.tipo] || 'fa-calendar'}"></i>
         </div>
         <span class="actividad-titulo">${a.titulo}</span>
         </div>
        </li>
      `).join("");
   }

   // --- Resultados detallados ---
   const listaResultados = document.getElementById("lista-resultados");
   if (listaResultados && torneo.resultados) {
      listaResultados.innerHTML = torneo.resultados.map((r, indice) => `
         <li class="resultado-card" data-indice="${indice}">
            <div class="resultado-cabecera">
               <div>
                  <span class="resultado-encuentro">${r.encuentro}</span>
                  <span class="resultado-fecha">${r.fecha}</span>
               </div>
               <span class="resultado-marcador">${r.marcador}</span>
               <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="resultado-detalles">
               ${r.detalles.map((d) => `
                  <div class="resultado-detalle-item">
                     <span>${d.tipo}</span>
                     <span>${d.valor}</span>
                  </div>
               `).join("")}
            </div>
         </li>
      `).join("");

      listaResultados.querySelectorAll(".resultado-cabecera").forEach((cabecera) => {
         cabecera.addEventListener("click", () => {
            cabecera.closest(".resultado-card").classList.toggle("abierto");
         });
      });
   }
}

// Función que renderiza dinámicamente el bracket o la tabla, según el formato
function renderTournament(torneo) {
   const container = document.getElementById('brackets-render-box');
   const title = document.getElementById('tournament-title');
   const metaInfo = document.getElementById('tournament-info-meta');

   title.textContent = torneo.nombre;
   metaInfo.textContent = `Formato: ${torneo.formato.replace('_', ' ')} | Modo: ${torneo.totalJugadoresPorEquipo}v${torneo.totalJugadoresPorEquipo}`;
   container.innerHTML = "";

   if (torneo.formato === "eliminacion_directa") {

      container.className = "bracket-visual";
      container.innerHTML = "";

      const octavos = torneo.rondas[0];
      const cuartos = torneo.rondas[1];
      const semifinales = torneo.rondas[2];
      const final = torneo.rondas[3];

      function crearColumna(ronda, claseExtra, desde = 0, hasta = ronda.partidos.length) {
         const columna = document.createElement("div");
         columna.className = `bracket-round ${claseExtra}`;

         const titulo = document.createElement("h3");
         titulo.textContent = ronda.nombreRonda;
         columna.appendChild(titulo);

         ronda.partidos.slice(desde, hasta).forEach(partido => {
            columna.innerHTML += `
            <div class="bracket-matchup ${partido.ganador ? 'completed' : ''}">
               <div class="matchup-team ${partido.ganador === partido.equipo1 ? 'winner' : ''}">
                  <span>${partido.equipo1}</span>
                  <strong>${partido.score1 !== null ? partido.score1 : '-'}</strong>
               </div>

               <div class="matchup-team ${partido.ganador === partido.equipo2 ? 'winner' : ''}">
                  <span>${partido.equipo2}</span>
                  <strong>${partido.score2 !== null ? partido.score2 : '-'}</strong>
               </div>
            </div>
         `;
         });

         return columna;
      }

      const octavosIzq = crearColumna(octavos, "left-side", 0, 4);
      const cuartosIzq = crearColumna(cuartos, "left-side compact", 0, 2);
      const semisIzq = crearColumna(semifinales, "left-side compact", 0, 1);

      const centro = document.createElement("div");
      centro.className = "bracket-center";

      centro.innerHTML = `
      <div class="final-match">
         <h3>${final.nombreRonda}</h3>

         <div class="bracket-matchup final-box">
            <div class="matchup-team">
               <span>${final.partidos[0].equipo1}</span>
               <strong>${final.partidos[0].score1 !== null ? final.partidos[0].score1 : '-'}</strong>
            </div>

            <div class="matchup-team">
               <span>${final.partidos[0].equipo2}</span>
               <strong>${final.partidos[0].score2 !== null ? final.partidos[0].score2 : '-'}</strong>
            </div>
         </div>

         <div class="champion-box">
            <span>Champion</span>
            <strong>${torneo.campeon}</strong>
         </div>
      </div>
   `;

      const semisDer = crearColumna(semifinales, "right-side compact", 1, 2);
      const cuartosDer = crearColumna(cuartos, "right-side compact", 2, 4);
      const octavosDer = crearColumna(octavos, "right-side", 4, 8);

      container.appendChild(octavosIzq);
      container.appendChild(cuartosIzq);
      container.appendChild(semisIzq);
      container.appendChild(centro);
      container.appendChild(semisDer);
      container.appendChild(cuartosDer);
      container.appendChild(octavosDer);
   }

   const contenedorRondas = document.getElementById("rondas-suizo");
      if (contenedorRondas) {
         if (torneo.formato === "suizo" && torneo.rondas) {
            contenedorRondas.innerHTML = torneo.rondas.map((ronda) => `
               <div class="ronda-card">
                  <h3>Ronda ${ronda.numero}</h3>
                  ${ronda.partidos.map((p) => `
                     <div class="ronda-partido">
                        <span class="ronda-jugador blancas">${p.blancas}</span>
                        <span class="ronda-resultado">${p.resultado}</span>
                        <span class="ronda-jugador negras">${p.negras}</span>
                     </div>
                  `).join("")}
               </div>
            `).join("");
         } else {
            contenedorRondas.innerHTML = "";
         }
      }

else if (torneo.formato === "liga" || torneo.formato === "suizo") {
      const esEquipoTabla = torneo.tipoParticipacion === "equipos";   // <-- NUEVA línea

      let tableHTML = `
         <table class="league-table">
            <thead>
               <tr>
                  <th>Pos</th>
                  <th>${esEquipoTabla ? "Equipo" : "Participante"}</th>   
                  <th>PJ</th>
                  <th>G</th>
                  <th>P</th>
                  <th>Pts</th>
               </tr>
            </thead>
            <tbody>
      `;

      torneo.tablaPosiciones.forEach(row => {
         tableHTML += `
            <tr>
               <td style="color: #00f0ff; font-weight: bold;">${row.posicion}</td>
               <td style="font-weight: 600;">${row.equipo}</td>
               <td>${row.PJ}</td>
               <td style="color: #30f5d2;">${row.G}</td>
               <td style="color: #ff00c8;">${row.P}</td>
               <td style="font-weight: bold; color: #b03eff;">${row.Puntos}</td>
            </tr>
         `;
      });

      tableHTML += `</tbody></table>`;
      container.innerHTML = tableHTML;
   }
}

// Ejecutar al cargar la página
document.addEventListener("DOMContentLoaded", () => {
   const idTorneo = obtenerIdTorneoDesdeURL();
   const torneo = TORNEOS_DB[idTorneo];

   if (!torneo) {
      console.warn(`No se encontró el torneo con id "${idTorneo}"`);
      return;
   }

   poblarDetallesTorneo(torneo);
   renderTournament(torneo);
});