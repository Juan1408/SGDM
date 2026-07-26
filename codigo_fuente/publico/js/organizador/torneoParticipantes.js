/*
=====================================================
    PARTICIPANTES
=====================================================
*/

"use strict";

const TorneoParticipantes = (() => {

   function renderizarParticipantes(
      torneoActual,
      contenido,
      actualizarTorneoActual,
      actualizarEncabezado
   ) {
      contenido.innerHTML =
         TorneoVista.participantes(torneoActual);

      document
         .querySelectorAll("[data-accion='ver']")
         .forEach((boton) => {
            boton.addEventListener("click", () => {
               const participante =
                  torneoActual.participantesData.find(
                     (item) =>
                        item.id === Number(boton.dataset.id)
                  );

               if (!participante) {
                  return;
               }

               alert(
                  `${participante.nombre}\n` +
                  `Capitán: ${participante.capitan}\n` +
                  `Tipo: ${participante.tipo}\n` +
                  `Estado: ${participante.estado}`
               );
            });
         });

      document
         .querySelectorAll("[data-accion='eliminar']")
         .forEach((boton) => {
            boton.addEventListener("click", () => {
               eliminarParticipante(
                  torneoActual,
                  Number(boton.dataset.id),
                  contenido,
                  actualizarTorneoActual,
                  actualizarEncabezado
               );
            });
         });
   }

   function eliminarParticipante(
      torneoActual,
      idParticipante,
      contenido,
      actualizarTorneoActual,
      actualizarEncabezado
   ) {
      const participante =
         torneoActual.participantesData.find(
            (item) => item.id === idParticipante
         );

      if (!participante) {
         return;
      }

      const confirmado = confirm(
         `¿Querés eliminar a ${participante.nombre} del torneo?`
      );

      if (!confirmado) {
         return;
      }

      const nuevosParticipantes =
         torneoActual.participantesData.filter(
            (item) => item.id !== idParticipante
         );

      const actualizado = actualizarTorneo(
         torneoActual.id,
         {
            participantesData: nuevosParticipantes,
            participantesActuales:
               nuevosParticipantes.length
         }
      );

      if (!actualizado) {
         alert(
            "No fue posible eliminar al participante."
         );
         return;
      }

      actualizarTorneoActual(actualizado);
      actualizarEncabezado();

      renderizarParticipantes(
         actualizado,
         contenido,
         actualizarTorneoActual,
         actualizarEncabezado
      );
   }

   function renderizarSolicitudes(
      torneoActual,
      contenido,
      actualizarTorneoActual,
      actualizarEncabezado
   ) {
      contenido.innerHTML =
         TorneoVista.solicitudes(torneoActual);

      document
         .querySelectorAll("[data-solicitud-accion]")
         .forEach((boton) => {
            boton.addEventListener("click", () => {
               gestionarSolicitud(
                  torneoActual,
                  Number(boton.dataset.id),
                  boton.dataset.solicitudAccion,
                  contenido,
                  actualizarTorneoActual,
                  actualizarEncabezado
               );
            });
         });
   }

   function gestionarSolicitud(
      torneoActual,
      idSolicitud,
      accion,
      contenido,
      actualizarTorneoActual,
      actualizarEncabezado
   ) {
      const solicitud =
         torneoActual.solicitudesData.find(
            (item) => item.id === idSolicitud
         );

      if (!solicitud) {
         return;
      }

      if (accion === "aceptar") {
         aceptarSolicitud(
            torneoActual,
            solicitud,
            contenido,
            actualizarTorneoActual,
            actualizarEncabezado
         );

         return;
      }

      if (accion === "rechazar") {
         rechazarSolicitud(
            torneoActual,
            solicitud,
            contenido,
            actualizarTorneoActual,
            actualizarEncabezado
         );
      }
   }

   function aceptarSolicitud(
      torneoActual,
      solicitud,
      contenido,
      actualizarTorneoActual,
      actualizarEncabezado
   ) {
      if (
         torneoActual.participantesActuales >=
         torneoActual.cupos
      ) {
         alert(
            "No se puede aceptar la solicitud porque el torneo alcanzó el límite de cupos."
         );

         return;
      }

      const yaExiste =
         torneoActual.participantesData.some(
            (participante) =>
               participante.nombre.toLowerCase() ===
               solicitud.nombre.toLowerCase()
         );

      if (yaExiste) {
         alert(
            "Este participante ya está registrado en el torneo."
         );

         return;
      }

      const confirmado = confirm(
         `¿Aceptar la solicitud de ${solicitud.nombre}?`
      );

      if (!confirmado) {
         return;
      }

      const nuevoParticipante = {
         id: obtenerSiguienteIdParticipante(
            torneoActual
         ),
         nombre: solicitud.nombre,
         capitan: solicitud.responsable,
         tipo: solicitud.tipo,
         estado: "Confirmado"
      };

      const participantesActualizados = [
         ...torneoActual.participantesData,
         nuevoParticipante
      ];

      const solicitudesActualizadas =
         torneoActual.solicitudesData.map(
            (item) =>
               item.id === solicitud.id
                  ? {
                       ...item,
                       estado: "Aceptada"
                    }
                  : item
         );

      const actualizado = actualizarTorneo(
         torneoActual.id,
         {
            participantesData:
               participantesActualizados,

            participantesActuales:
               participantesActualizados.length,

            solicitudesData:
               solicitudesActualizadas
         }
      );

      if (!actualizado) {
         alert(
            "No fue posible aceptar la solicitud."
         );

         return;
      }

      actualizarTorneoActual(actualizado);
      actualizarEncabezado();

      renderizarSolicitudes(
         actualizado,
         contenido,
         actualizarTorneoActual,
         actualizarEncabezado
      );
   }

   function rechazarSolicitud(
      torneoActual,
      solicitud,
      contenido,
      actualizarTorneoActual,
      actualizarEncabezado
   ) {
      const confirmado = confirm(
         `¿Rechazar la solicitud de ${solicitud.nombre}?`
      );

      if (!confirmado) {
         return;
      }

      const solicitudesActualizadas =
         torneoActual.solicitudesData.map(
            (item) =>
               item.id === solicitud.id
                  ? {
                       ...item,
                       estado: "Rechazada"
                    }
                  : item
         );

      const actualizado = actualizarTorneo(
         torneoActual.id,
         {
            solicitudesData:
               solicitudesActualizadas
         }
      );

      if (!actualizado) {
         alert(
            "No fue posible rechazar la solicitud."
         );

         return;
      }

      actualizarTorneoActual(actualizado);

      renderizarSolicitudes(
         actualizado,
         contenido,
         actualizarTorneoActual,
         actualizarEncabezado
      );
   }

   function obtenerSiguienteIdParticipante(
      torneoActual
   ) {
      const ids =
         torneoActual.participantesData.map(
            (participante) =>
               Number(participante.id) || 0
         );

      return ids.length > 0
         ? Math.max(...ids) + 1
         : 1;
   }

   function renderizar(
      seccion,
      torneoActual,
      contenido,
      actualizarTorneoActual,
      actualizarEncabezado
   ) {
      if (seccion === "participantes") {
         renderizarParticipantes(
            torneoActual,
            contenido,
            actualizarTorneoActual,
            actualizarEncabezado
         );

         return true;
      }

      if (seccion === "solicitudes") {
         renderizarSolicitudes(
            torneoActual,
            contenido,
            actualizarTorneoActual,
            actualizarEncabezado
         );

         return true;
      }

      return false;
   }

   return {
      renderizar
   };

})();