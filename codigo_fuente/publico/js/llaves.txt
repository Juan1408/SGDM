// SIMULACIÓN DE DATOS DINÁMICOS DESDE LA BD
const torneoActivo = {
   nombre: "Copa ASCEND",
   formato: "eliminacion_directa",
   totalJugadoresPorEquipo: 5,
   campeon: "Por definir",

   rondas: [
      {
         nombreRonda: "Octavos",
         partidos: [
            { id: 1, equipo1: "Fire Wolves", score1: 3, equipo2: "Cyber Titans", score2: 1, ganador: "Fire Wolves" },
            { id: 2, equipo1: "Nova Chess", score1: 0, equipo2: "Mental Squad", score2: 2, ganador: "Mental Squad" },
            { id: 3, equipo1: "Dragon Crew", score1: 2, equipo2: "Shadow Team", score2: 0, ganador: "Dragon Crew" },
            { id: 4, equipo1: "Neon Knights", score1: 1, equipo2: "Omega Squad", score2: 2, ganador: "Omega Squad" },
            { id: 5, equipo1: "Pixel Fox", score1: 2, equipo2: "Dark Lions", score2: 1, ganador: "Pixel Fox" },
            { id: 6, equipo1: "Aqua Team", score1: 0, equipo2: "Red Hawks", score2: 3, ganador: "Red Hawks" },
            { id: 7, equipo1: "Blue Core", score1: 1, equipo2: "Venom Club", score2: 2, ganador: "Venom Club" },
            { id: 8, equipo1: "Solar Rush", score1: 2, equipo2: "Iron Squad", score2: 0, ganador: "Solar Rush" }
         ]
      },
      {
         nombreRonda: "Cuartos",
         partidos: [
            { id: 9, equipo1: "Fire Wolves", score1: null, equipo2: "Mental Squad", score2: null, ganador: null },
            { id: 10, equipo1: "Dragon Crew", score1: null, equipo2: "Omega Squad", score2: null, ganador: null },
            { id: 11, equipo1: "Pixel Fox", score1: null, equipo2: "Red Hawks", score2: null, ganador: null },
            { id: 12, equipo1: "Venom Club", score1: null, equipo2: "Solar Rush", score2: null, ganador: null }
         ]
      },
      {
         nombreRonda: "Semifinales",
         partidos: [
            { id: 13, equipo1: "Por definir", score1: null, equipo2: "Por definir", score2: null, ganador: null },
            { id: 14, equipo1: "Por definir", score1: null, equipo2: "Por definir", score2: null, ganador: null }
         ]
      },
      {
         nombreRonda: "Gran Final",
         partidos: [
            { id: 15, equipo1: "Por definir", score1: null, equipo2: "Por definir", score2: null, ganador: null }
         ]
      }
   ],

   tablaPosiciones: []
};

// Función Principal que renderiza dinámicamente según el formato elegido
function renderTournament(torneo) {
   const container = document.getElementById('brackets-render-box'); const title = document.getElementById('tournament-title');
   const metaInfo = document.getElementById('tournament-info-meta');

   // Seteamos textos principales
   title.textContent = torneo.nombre;
   metaInfo.textContent = `Formato: ${torneo.formato.replace('_', ' ')} | Modo: ${torneo.totalJugadoresPorEquipo}v${torneo.totalJugadoresPorEquipo}`;
   container.innerHTML = ""; // Limpiamos contenedor

   // CASO 1: ELIMINACIÓN DIRECTA
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

   // CASO 2: LIGA O SISTEMA SUIZO (Estructura de Tabla de Posiciones)
   else if (torneo.formato === "liga" || torneo.formato === "suizo") {
      let tableHTML = `
         <table class="league-table">
            <thead>
               <tr>
                  <th>Pos</th>
                  <th>Equipo</th>
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
   renderTournament(torneoActivo);
});