"use strict";

/*
=====================================================
    CONTROLADOR DE ADMINISTRACIÓN
=====================================================
*/

const parametros = new URLSearchParams(
    window.location.search
);

const torneoId =
    Number(parametros.get("id")) ||
    TORNEOS[0]?.id;

let torneoActual = obtenerTorneoPorId(torneoId);

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
        ".tournament-menu__button"
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

const SECCIONES_PENDIENTES = {
    fixture: [
        "Fixture",
        "Aquí se mostrarán los cruces, rondas o llaves.",
        "fa-sitemap"
    ],

    cronograma: [
        "Cronograma",
        "Aquí se administrarán fechas y horarios.",
        "fa-calendar-days"
    ],

    solicitudes: [
        "Solicitudes",
        "Aquí se aceptarán o rechazarán inscripciones.",
        "fa-envelope-open-text"
    ],

    resultados: [
        "Resultados",
        "Aquí se registrarán los resultados.",
        "fa-flag-checkered"
    ],

    participantes: [
        "Participantes",
        "Aquí se mostrará la lista de inscritos.",
        "fa-users"
    ],

    posiciones: [
        "Posiciones",
        "Aquí se mostrará la tabla y el ranking.",
        "fa-ranking-star"
    ]
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