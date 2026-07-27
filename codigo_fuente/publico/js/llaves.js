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

   setTexto("premio-primero", torneo.premios.primero);
   setTexto("premio-segundo", torneo.premios.segundo);
   setTexto("premio-tercero", torneo.premios.tercero);

   // --- Equipos/Participantes que participan ---
   const gridParticipantes = document.getElementById("participantes-grid");
   if (gridParticipantes) {
      gridParticipantes.innerHTML = torneo.participantes.map((p) => `
         <article class="participant-card">
            <img src="${p.imagen}" alt="${p.nombre}">
            <div class="participant-info">
               <h4>${p.nombre}</h4>
               <p>${p.deporte}</p>
               <p>${p.cantidad}</p>
               <p>${p.puntos}</p>
            </div>
         </article>
      `).join("");
   }

   // --- Miembros del equipo (solo si es de equipos) ---
   const gridMiembros = document.getElementById("members-grid");
   if (gridMiembros && torneo.miembros) {
      gridMiembros.innerHTML = torneo.miembros.map((m) => `
         <article class="member-card">
            <div class="member-photo">
               <div class="member-bg"></div>
               <img src="${m.foto}" alt="${m.nombre}">
            </div>
            <h4>${m.nombre}</h4>
         </article>
      `).join("");
   }

   // --- Rankings del equipo / individual ---
   const filasRanking = document.getElementById("ranking-filas");
   if (filasRanking && torneo.rankingEquipo) {
      filasRanking.innerHTML = torneo.rankingEquipo.filas.map((fila, indice) => {
         const columnaLogo = esEquipo ? `
            <div class="ranking-equipo-logo">
               <img src="${torneo.rankingEquipo.logoEquipo}">
            </div>
         ` : "";

         return `
            <article class="ranking-row">
               ${columnaLogo}
               <div>${indice + 1}</div>
               <div class="ranking-jugador">
                  <img src="${fila.jugadorFoto}">
                  <span>${fila.jugadorNombre}</span>
               </div>
               <div>${fila.juego}</div>
               <div>${fila.partidas}</div>
               <div class="ranking-puntos">${fila.puntos}</div>
            </article>
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

   const seccionMiembros = document.querySelector(".team-members");
   if (seccionMiembros) {
      seccionMiembros.style.display = esEquipo ? "" : "none";
   }

   const rankingTablaContainer = document.getElementById("ranking-tabla-container");
   const rankingHead = document.querySelector(".ranking-head");
   if (rankingTablaContainer && rankingHead) {
      if (esEquipo) {
         rankingTablaContainer.classList.remove("ranking-individual");
         rankingHead.innerHTML = `
            <span>Equipo</span>
            <span>Pos</span>
            <span>Jugador</span>
            <span>Juego</span>
            <span>Partidas</span>
            <span>Puntos</span>
         `;
      } else {
         rankingTablaContainer.classList.add("ranking-individual");
         rankingHead.innerHTML = `
            <span>Pos</span>
            <span>Jugador</span>
            <span>Juego</span>
            <span>Partidas</span>
            <span>Puntos</span>
         `;
      }
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

         const titulo = document.createElement("h4");
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
         <h4>${final.nombreRonda}</h4>

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