"use strict";

/*
=====================================================
   ELEMENTOS
=====================================================
*/

const formularioCrearTorneo = document.getElementById(
   "crear-torneo-form"
);

const notificacionCrearTorneo =
   document.getElementById(
      "crear-torneo-notificacion"
   );

const disciplinaSelect =
   document.getElementById("discipline");

const modalidadInput =
   document.getElementById("modality");

const sistemaSelect =
   document.getElementById(
      "competitionSystem"
   );

const cuposInput =
   document.getElementById("participants");

const ayudaCupos =
   document.getElementById("ayudaCupos");

const fechaInicioInscripcionInput =
   document.getElementById(
      "inscriptionStart"
   );

const fechaCierreInput =
   document.getElementById(
      "inscriptionEnd"
   );

const fechaInicioInput =
   document.getElementById(
      "tournamentStart"
   );

const fechaFinInput =
   document.getElementById(
      "tournamentEnd"
   );

const botonAgregarEvento =
   document.getElementById(
      "add-event-button"
   );

const tituloEventoInput =
   document.getElementById("eventTitle");

const tipoEventoSelect =
   document.getElementById("eventType");

const fechaEventoInput =
   document.getElementById("eventDate");

const horaEventoInput =
   document.getElementById("eventTime");

const listaFechas =
   document.getElementById("dates-list");

let eventosTemporales = [];


/*
=====================================================
   NOTIFICACIONES
=====================================================
*/

function mostrarNotificacion(
   mensaje,
   tipo = "error"
) {
   if (!notificacionCrearTorneo) {
      return;
   }

   notificacionCrearTorneo.className =
      `crear-torneo-notificacion ${tipo}`;

   notificacionCrearTorneo.textContent =
      mensaje;
}

function limpiarNotificacion() {
   if (!notificacionCrearTorneo) {
      return;
   }

   notificacionCrearTorneo.className =
      "crear-torneo-notificacion";

   notificacionCrearTorneo.textContent = "";
}


/*
=====================================================
   DISCIPLINAS Y SISTEMAS
=====================================================
*/

function cargarDisciplinas() {
   obtenerDisciplinas().forEach(
      (disciplina) => {
         const opcion =
            document.createElement("option");

         opcion.value = disciplina;
         opcion.textContent = disciplina;

         disciplinaSelect.appendChild(opcion);
      }
   );
}

function actualizarDisciplina() {
   const disciplina =
      disciplinaSelect.value;

   const configuracion =
      obtenerConfiguracionDisciplina(
         disciplina
      );

   modalidadInput.value =
      configuracion?.modalidad ?? "";

   sistemaSelect.innerHTML = "";

   if (!configuracion) {
      sistemaSelect.disabled = true;

      sistemaSelect.innerHTML = `
         <option value="">
            Seleccioná primero una disciplina
         </option>
      `;

      return;
   }

   sistemaSelect.disabled = false;

   sistemaSelect.innerHTML = `
      <option value="">
         Seleccionar sistema
      </option>
   `;

   configuracion.sistemas.forEach(
      (sistema) => {
         const opcion =
            document.createElement("option");

         opcion.value = sistema;
         opcion.textContent = sistema;

         sistemaSelect.appendChild(opcion);
      }
   );
}

function actualizarAyudaCupos() {
   const sistema = sistemaSelect.value;

   if (
      sistema === "Eliminación directa"
   ) {
      ayudaCupos.textContent =
         "Debe ser 2, 4, 8, 16, 32 o 64.";

      cuposInput.min = "2";
      cuposInput.max = "64";

      return;
   }

   if (sistema === "Liga") {
      ayudaCupos.textContent =
         "La liga admite entre 2 y 40 participantes.";

      cuposInput.min = "2";
      cuposInput.max = "40";

      return;
   }

   if (sistema === "Sistema Suizo") {
      ayudaCupos.textContent =
         "El sistema suizo admite entre 4 y 64 participantes.";

      cuposInput.min = "4";
      cuposInput.max = "64";

      return;
   }

   ayudaCupos.textContent =
      "Indicá la cantidad máxima de equipos o jugadores.";
}


/*
=====================================================
   FECHAS
=====================================================
*/

function configurarFechasMinimas() {
   const hoy = new Date();

   const fechaHoy =
      hoy.toISOString().split("T")[0];

   fechaInicioInscripcionInput.min =
      fechaHoy;

   fechaCierreInput.min =
      fechaHoy;

   fechaInicioInput.min =
      fechaHoy;

   fechaFinInput.min =
      fechaHoy;

   fechaEventoInput.min =
      fechaHoy;
}

function fechasValidas(datos) {
   const inicioInscripcion =
      datos.get("fechaInicioInscripcion");

   const cierreInscripcion =
      datos.get("fechaCierre");

   const inicioTorneo =
      datos.get("fechaInicio");

   const finTorneo =
      datos.get("fechaFin");

   if (
      inicioInscripcion >
      cierreInscripcion
   ) {
      return (
         "El inicio de inscripciones no puede " +
         "ser posterior al cierre."
      );
   }

   if (
      cierreInscripcion >
      inicioTorneo
   ) {
      return (
         "El cierre de inscripciones debe ser " +
         "igual o anterior al inicio del torneo."
      );
   }

   if (inicioTorneo > finTorneo) {
      return (
         "La fecha de finalización no puede " +
         "ser anterior al inicio del torneo."
      );
   }

   return "";
}


/*
=====================================================
   EVENTOS
=====================================================
*/

function formatearEvento(evento) {
   const fecha =
      formatearFechaCorta(evento.fecha);

   return `
      <article
         class="crear-evento-item"
         data-id="${evento.id}"
      >
         <div>
            <span>
               ${fecha} · ${evento.hora}
            </span>

            <strong>
               ${evento.titulo}
            </strong>

            <small>
               ${evento.tipo}
            </small>
         </div>

         <button
            class="crear-evento-eliminar"
            type="button"
            data-eliminar-evento="${evento.id}"
            aria-label="Eliminar actividad"
         >
            <i class="fa-solid fa-trash"></i>
         </button>
      </article>
   `;
}

function renderizarEventos() {
   if (eventosTemporales.length === 0) {
      listaFechas.textContent =
         "Aún no se agregaron actividades.";

      return;
   }

   const eventosOrdenados = [
      ...eventosTemporales
   ].sort((a, b) => {
      const fechaA =
         new Date(
            `${a.fecha}T${a.hora}`
         );

      const fechaB =
         new Date(
            `${b.fecha}T${b.hora}`
         );

      return fechaA - fechaB;
   });

   listaFechas.innerHTML =
      eventosOrdenados
         .map(formatearEvento)
         .join("");

   document
      .querySelectorAll(
         "[data-eliminar-evento]"
      )
      .forEach((boton) => {
         boton.addEventListener(
            "click",
            () => {
               eliminarEvento(
                  Number(
                     boton.dataset
                        .eliminarEvento
                  )
               );
            }
         );
      });
}

function agregarEvento() {
   const titulo =
      tituloEventoInput.value.trim();

   const tipo =
      tipoEventoSelect.value;

   const fecha =
      fechaEventoInput.value;

   const hora =
      horaEventoInput.value;

   if (!titulo || !fecha || !hora) {
      mostrarNotificacion(
         "Completá el título, la fecha y la hora de la actividad."
      );

      return;
   }

   eventosTemporales.push({
      id: Date.now(),
      tipo,
      titulo,
      descripcion:
         `Actividad programada: ${titulo}.`,
      fecha,
      hora,
      ubicacion:
         document
            .getElementById("venue")
            .value
            .trim() || "A definir",
      estado: "Pendiente"
   });

   tituloEventoInput.value = "";
   fechaEventoInput.value = "";
   horaEventoInput.value = "";

   limpiarNotificacion();
   renderizarEventos();
}

function eliminarEvento(idEvento) {
   eventosTemporales =
      eventosTemporales.filter(
         (evento) =>
            evento.id !== idEvento
      );

   renderizarEventos();
}


/*
=====================================================
   VALIDACIONES
=====================================================
*/

function validarCupos(
   cupos,
   sistema
) {
   if (
      !Number.isInteger(cupos) ||
      cupos < 2
   ) {
      return (
         "La cantidad de cupos debe ser " +
         "un número entero válido."
      );
   }

   if (
      sistema ===
         "Eliminación directa" &&
      !esPotenciaDeDos(cupos)
   ) {
      return (
         "La eliminación directa requiere " +
         "2, 4, 8, 16, 32 o 64 cupos."
      );
   }

   if (
      sistema === "Liga" &&
      cupos > 40
   ) {
      return (
         "Una liga puede tener como máximo " +
         "40 participantes."
      );
   }

   if (
      sistema ===
         "Sistema Suizo" &&
      (cupos < 4 || cupos > 64)
   ) {
      return (
         "El sistema suizo admite entre " +
         "4 y 64 participantes."
      );
   }

   return "";
}

function obtenerSiguienteIdTorneo() {
   const ids = TORNEOS.map(
      (torneo) =>
         Number(torneo.id) || 0
   );

   return ids.length > 0
      ? Math.max(...ids) + 1
      : 1;
}


/*
=====================================================
   CREAR TORNEO
=====================================================
*/

function crearTorneo(evento) {
   evento.preventDefault();
   limpiarNotificacion();

   if (
      !formularioCrearTorneo
         .checkValidity()
   ) {
      formularioCrearTorneo
         .reportValidity();

      return;
   }

   const datos =
      new FormData(
         formularioCrearTorneo
      );

   const sistema =
      String(datos.get("sistema"));

   const cupos =
      Number(datos.get("cupos"));

   const errorFechas =
      fechasValidas(datos);

   if (errorFechas) {
      mostrarNotificacion(errorFechas);
      return;
   }

   const errorCupos =
      validarCupos(cupos, sistema);

   if (errorCupos) {
      mostrarNotificacion(errorCupos);
      return;
   }

   const ubicacion =
      String(
         datos.get("ubicacion")
      ).trim();

   const localidad =
      String(
         datos.get("localidad")
      ).trim();

   const nuevoTorneo = {
      id: obtenerSiguienteIdTorneo(),

      nombre:
         String(
            datos.get("nombre")
         ).trim(),

      disciplina:
         String(
            datos.get("disciplina")
         ),

      estado:
         "Inscripciones abiertas",

      participantesActuales: 0,
      cupos,

      fechaInicioInscripcion:
         String(
            datos.get(
               "fechaInicioInscripcion"
            )
         ),

      fechaCierre:
         String(
            datos.get("fechaCierre")
         ),

      fechaInicio:
         String(
            datos.get("fechaInicio")
         ),

      fechaFin:
         String(
            datos.get("fechaFin")
         ),

      sistema,

      ubicacion:
         localidad
            ? `${ubicacion}, ${localidad}`
            : ubicacion,

      comunidad:
         String(
            datos.get("comunidad")
         ).trim(),

      descripcion:
         String(
            datos.get("descripcion")
         ).trim(),

      reglamento:
         String(
            datos.get("reglamento")
         ).trim(),

      participantesData: [],
      solicitudesData: [],
      fixture: [],

      eventos: eventosTemporales.map(
         (evento, indice) => ({
            ...evento,
            id: indice + 1
         })
      )
   };

   TORNEOS.push(
      normalizarTorneo(nuevoTorneo)
   );

   const guardado =
      guardarTorneos();

   if (!guardado) {
      mostrarNotificacion(
         "No fue posible guardar el torneo."
      );

      return;
   }

   mostrarNotificacion(
      "El torneo fue creado correctamente.",
      "success"
   );

   setTimeout(() => {
      window.location.href =
         `administrar-torneo.html?id=${nuevoTorneo.id}`;
   }, 700);
}


/*
=====================================================
   EVENTOS
=====================================================
*/

disciplinaSelect.addEventListener(
   "change",
   actualizarDisciplina
);

sistemaSelect.addEventListener(
   "change",
   actualizarAyudaCupos
);

botonAgregarEvento.addEventListener(
   "click",
   agregarEvento
);

formularioCrearTorneo.addEventListener(
   "submit",
   crearTorneo
);


/*
=====================================================
   INICIO
=====================================================
*/

cargarDisciplinas();
configurarFechasMinimas();
renderizarEventos();