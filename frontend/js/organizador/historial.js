"use strict";

/*
=====================================================
   HISTORIAL - lista simple de torneos finalizados.
   Reutiliza el componente .tarjeta-lista--torneo
   (mismo que en Dashboard) y el .chip de estado,
   así no se duplica CSS para esta página.
=====================================================
*/

const historialLista = document.getElementById("historialLista");

function mostrarHistorial() {
   const finalizados = TORNEOS.filter(
      (torneo) => torneo.estado === "Finalizado"
   );

   if (finalizados.length === 0) {
      historialLista.innerHTML = `
         <p class="historial-vacio">
            Todavía no tenés torneos finalizados.
         </p>
      `;
      return;
   }

   historialLista.innerHTML = finalizados
      .map(
         (torneo) => `
      <article class="tarjeta-lista tarjeta-lista--torneo">

         <img src="../../img/torneos/card1.jpg" alt="${torneo.nombre}">

         <div class="info-torneo">
            <span class="chip chip--peligro">Finalizado</span>
            <h4>${torneo.nombre}</h4>
            <p>${torneo.disciplina} · ${torneo.sistema}</p>

            <div class="meta-torneo">
               <span>${torneo.participantes}</span>
               <span>${formatearFechaCorta(torneo.fechaInicio)}</span>
            </div>
         </div>

         <a href="administrar-torneo.html?id=${torneo.id}" class="boton boton--gestion">
            Ver detalle
         </a>

      </article>
   `
      )
      .join("");
}

mostrarHistorial();
