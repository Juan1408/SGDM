"use strict";

/*
=====================================================
    CONTROLADOR DE ADMINISTRACIÓN
=====================================================
*/

const parametros = new URLSearchParams(
    window.location.search
);

const torneoId = Number(parametros.get("id"));

let torneoActual = Number.isInteger(torneoId)
    ? obtenerTorneoPorId(torneoId)
    : null;

/*
    Guarda qué ronda del fixture está seleccionada.
*/
let rondaFixtureActiva = null;

const elementos = {
    nombre: document.getElementById("nombreTorneo"),

    disciplina: document.getElementById(
        "disciplinaTorneo"
    ),

    estado: document.getElementById("estadoTorneo"),

    contenido: document.getElementById(
        "contenidoTorneo"
    ),

  botones: document.querySelectorAll(
    "[data-section]"
)
};

const SECCIONES = [
    "informacion",
    "fixture",
    "cronograma",
    "solicitudes",
    "resultados",
    "editar",
    "participantes",
    "posiciones"
];

/*
    Fixture ya no está acá porque ahora tiene
    una vista propia.
*/
const SECCIONES_PENDIENTES = {
 

};


/*
=====================================================
    INTERFAZ GENERAL
=====================================================
*/

function actualizarEncabezado() {
    elementos.nombre.textContent =
        torneoActual.nombre;

    elementos.disciplina.textContent =
        `${torneoActual.disciplina} · ` +
        `${torneoActual.sistema}`;

    elementos.estado.textContent =
        torneoActual.estado;

    elementos.estado.className =
        `estado ${torneoActual.claseEstado}`;
}

function activarBoton(seccion) {
    elementos.botones.forEach((boton) => {
        boton.classList.toggle(
            "active",
            boton.dataset.section === seccion
        );
    });
}

function actualizarURL(seccion) {
    const url = new URL(window.location.href);

    url.searchParams.set("id", torneoActual.id);
    url.searchParams.set("seccion", seccion);

    history.replaceState({}, "", url);
}

function mostrarMensaje(texto, tipo = "error") {
    const contenedor = document.getElementById(
        "mensajeEdicion"
    );

    if (!contenedor) {
        return;
    }

    const clase =
        tipo === "success"
            ? "success-message"
            : "error-message";

    contenedor.innerHTML = `
        <p class="${clase}">
            ${TorneoVista.escaparHTML(texto)}
        </p>
    `;
}

/*
=====================================================
    TORNEO ACTUAL
=====================================================
*/

function actualizarTorneoActual(nuevoTorneo) {
   torneoActual = nuevoTorneo;
}

/*
=====================================================
    NAVEGACIÓN INTERNA
=====================================================
*/

function cambiarSeccion(seccion) {
    if (!SECCIONES.includes(seccion)) {
        seccion = "informacion";
    }

    activarBoton(seccion);
    actualizarURL(seccion);

    if (seccion === "informacion") {
        elementos.contenido.innerHTML =
            TorneoVista.informacion(torneoActual);

        return;
    }

 if (TorneoCompetencia.renderizar(seccion)) {
    return;
}

if (
   TorneoParticipantes.renderizar(
      seccion,
      torneoActual,
      elementos.contenido,
      actualizarTorneoActual,
      actualizarEncabezado
   )
) {
   return;
}

    if (seccion === "editar") {
        renderizarFormulario();
        return;
    }

    elementos.contenido.innerHTML =
        TorneoVista.pendiente(
            ...SECCIONES_PENDIENTES[seccion]
        );
}


/*
=====================================================
    FORMULARIO DE EDICIÓN
=====================================================
*/

/**
 * Genera nuevamente el formulario y conecta sus eventos.
 */
function renderizarFormulario() {
    elementos.contenido.innerHTML =
        TorneoVista.formularioEdicion(
            torneoActual
        );

    configurarFormulario();
}

function configurarFormulario() {
    const formulario = document.getElementById(
        "formEditarTorneo"
    );

    const cancelar = document.getElementById(
        "cancelarEdicion"
    );

    const sistema = document.getElementById(
        "sistema"
    );

    if (!formulario || !cancelar) {
        return;
    }

    cancelar.addEventListener("click", () => {
        cambiarSeccion("informacion");
    });

    /*
        Al cambiar el sistema, se adapta el ingreso
        de cupos:

        - Eliminación directa: selector.
        - Liga o Suizo: campo numérico.
    */
    sistema?.addEventListener("change", () => {
        const grupoCupos = document.getElementById(
            "grupoCupos"
        );

        if (!grupoCupos) {
            return;
        }

        grupoCupos.innerHTML =
            TorneoVista.campoCupos(
                torneoActual,
                sistema.value
            );
    });

    formulario.addEventListener(
        "submit",
        guardarCambios
    );
}


/*
=====================================================
    GUARDADO
=====================================================
*/

function guardarCambios(evento) {
    evento.preventDefault();

    const formulario = evento.currentTarget;

    if (!formulario.checkValidity()) {
        formulario.reportValidity();
        return;
    }

    const datos = new FormData(formulario);

    /*
        Los controles disabled no se incluyen en
        FormData. En esos casos se conserva el valor
        que ya tiene el torneo.
    */
    const sistema =
        datos.get("sistema") ||
        torneoActual.sistema;

    const cupos =
        datos.has("cupos")
            ? Number(datos.get("cupos"))
            : torneoActual.cupos;

    const fechaInicio =
        datos.get("fechaInicio") ||
        torneoActual.fechaInicio;

    const fechaCierre =
        datos.get("fechaCierre") ||
        torneoActual.fechaCierre;

    const estadoNuevo =
        datos.get("estado") ||
        torneoActual.estado;

    /*
        Se validan primero las fechas y después
        la cantidad de participantes.
    */
    const error =
        TorneoValidaciones.validarFechas(
            torneoActual,
            fechaCierre,
            fechaInicio,
            estadoNuevo
        ) ||
        TorneoValidaciones.validarCupos(
            torneoActual,
            cupos,
            sistema
        );

    if (error) {
        mostrarMensaje(error);
        return;
    }

    const actualizado = actualizarTorneo(
        torneoActual.id,
        {
            nombre: String(
                datos.get("nombre")
            ).trim(),

            sistema,
            estado: estadoNuevo,

            fechaInicio,
            fechaCierre,
            cupos,

            ubicacion: String(
                datos.get("ubicacion")
            ).trim(),

            descripcion: String(
                datos.get("descripcion")
            ).trim(),

            reglamento: String(
                datos.get("reglamento")
            ).trim()
        }
    );

    if (!actualizado) {
        mostrarMensaje(
            "No fue posible guardar los cambios."
        );

        return;
    }

    torneoActual = actualizado;
    actualizarEncabezado();

    /*
        Volvemos a generar el formulario.

        Esto es necesario porque, si el estado cambió
        a En juego o Finalizado, las fechas, los cupos
        y el sistema deben quedar bloqueados de inmediato.
    */
    renderizarFormulario();

    mostrarMensaje(
        "Los cambios se guardaron correctamente.",
        "success"
    );
}


/*
=====================================================
    TORNEO NO ENCONTRADO
=====================================================
*/

function mostrarTorneoNoEncontrado() {
    elementos.nombre.textContent =
        "Torneo no encontrado";

    elementos.disciplina.textContent = "";
    elementos.estado.textContent = "";

    elementos.contenido.innerHTML =
        TorneoVista.pendiente(
            "Torneo no encontrado",
            "No fue posible encontrar el torneo seleccionado.",
            "fa-triangle-exclamation"
        );

    elementos.botones.forEach((boton) => {
        boton.disabled = true;
    });
}


/*
=====================================================
    INICIO
=====================================================
*/

elementos.botones.forEach((boton) => {
    boton.addEventListener("click", () => {
        cambiarSeccion(
            boton.dataset.section
        );
    });
});

const gruposMenu = document.querySelectorAll(
   ".torneo-menu__group"
);

document
   .querySelectorAll(".torneo-menu__toggle")
   .forEach((boton) => {
      boton.addEventListener("click", (evento) => {
         evento.stopPropagation();

         const grupoActual = boton.closest(
            ".torneo-menu__group"
         );

         gruposMenu.forEach((grupo) => {
            if (grupo !== grupoActual) {
               grupo.classList.remove("open");
            }
         });

         grupoActual?.classList.toggle("open");
      });
   });

document.addEventListener("click", (evento) => {
   if (!evento.target.closest(".torneo-menu__group")) {
      gruposMenu.forEach((grupo) => {
         grupo.classList.remove("open");
      });
   }
});

if (!torneoActual) {
    mostrarTorneoNoEncontrado();
} else {
    actualizarEncabezado();

    const seccionSolicitada =
        parametros.get("seccion");

    const seccionInicial =
        SECCIONES.includes(seccionSolicitada)
            ? seccionSolicitada
            : "informacion";

    cambiarSeccion(seccionInicial);
}