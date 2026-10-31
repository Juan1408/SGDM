"use strict";

document.addEventListener("DOMContentLoaded", () => {

   const inputBuscar = document.getElementById("input-buscar-jugador");
   const listaResultados = document.getElementById("lista-resultados-busqueda");

   if (!inputBuscar || !listaResultados) return;

   const equipoId = inputBuscar.dataset.equipoId;
   const baseUrl = window.URL_BASE_JS || "/";

   let temporizador = null;

   //Evita inyectar HTML crudo de la base de datos (mismo cuidado que htmlspecialchars en PHP)
   function escaparHtml(texto) {
      const contenedor = document.createElement("div");
      contenedor.textContent = texto || "";
      return contenedor.innerHTML;
   }

   function renderizarResultados(jugadores) {
      if (!Array.isArray(jugadores) || jugadores.length === 0) {
         listaResultados.innerHTML = '<p class="lista-vacia">No se encontraron jugadores disponibles.</p>';
         return;
      }

      listaResultados.innerHTML = jugadores.map((jugador) => {
         const avatar = jugador.foto_perfil_url
            ? escaparHtml(jugador.foto_perfil_url)
            : baseUrl + "img/avatars/a1.png";

         const apodo = jugador.apodo_gamertag
            ? `<span>@${escaparHtml(jugador.apodo_gamertag)}</span>`
            : "";

         return `
            <div class="fila-jugador">
               <img class="fila-jugador-avatar" src="${avatar}" alt="${escaparHtml(jugador.nombre_completo)}">
               <div class="fila-jugador-info">
                  <span class="fila-jugador-nombre">${escaparHtml(jugador.nombre_completo)}</span>
                  ${apodo}
               </div>
               <form method="POST" action="${baseUrl}index.php?c=equipo&a=invitarJugador">
                  <input type="hidden" name="equipo_id" value="${equipoId}">
                  <input type="hidden" name="jugador_id" value="${jugador.id}">
                  <button type="submit" class="boton-invitar">Invitar</button>
               </form>
            </div>
         `;
      }).join("");
   }

   function buscar(texto) {
      if (texto.trim() === "") {
         listaResultados.innerHTML = '<p class="lista-vacia">Escribí un nombre o apodo para buscar jugadores.</p>';
         return;
      }

      const url = baseUrl + "index.php?c=equipo&a=buscarJugadoresAjax&id=" + equipoId + "&buscar=" + encodeURIComponent(texto);

      fetch(url)
         .then((respuesta) => respuesta.json())
         .then(renderizarResultados)
         .catch(() => {
            listaResultados.innerHTML = '<p class="lista-vacia">Ocurrió un error al buscar. Intentá de nuevo.</p>';
         });
   }

   //Esperamos 350ms despues de la ultima tecla antes de buscar, para no mandar
   //una peticion al servidor por cada letra que se escribe
   inputBuscar.addEventListener("input", () => {
      clearTimeout(temporizador);
      const texto = inputBuscar.value;
      temporizador = setTimeout(() => buscar(texto), 350);
   });

});
