"use strict";

/*
=====================================================
    CONFIGURACIÓN COMPARTIDA
=====================================================
*/

const CLAVE_TORNEOS = "ascendTorneos";

const CLASES_ESTADO = {
    "Inscripciones abiertas": "abierta",
    "En juego": "curso",
    "Finalizado": "finalizado"
};

const DISCIPLINAS = {
    "Fútbol": {
        categoria: "deportes",
        modalidad: "Equipos",
        integrantes: 11,
        sistemas: ["Liga", "Eliminación directa"]
    },
    "Baloncesto": {
        categoria: "deportes",
        modalidad: "Equipos",
        integrantes: 5,
        sistemas: ["Liga", "Eliminación directa"]
    },
    "Tenis": {
        categoria: "deportes",
        modalidad: "Individual",
        integrantes: 1,
        sistemas: ["Liga", "Eliminación directa"]
    },
    "League of Legends": {
        categoria: "esports",
        modalidad: "Equipos",
        integrantes: 5,
        sistemas: [
            "Liga",
            "Eliminación directa",
            "Sistema Suizo"
        ]
    },
    "Counter-Strike 2": {
        categoria: "esports",
        modalidad: "Equipos",
        integrantes: 5,
        sistemas: [
            "Liga",
            "Eliminación directa",
            "Sistema Suizo"
        ]
    },
    "EA Sports FC": {
        categoria: "esports",
        modalidad: "Individual",
        integrantes: 1,
        sistemas: [
            "Eliminación directa",
            "Sistema Suizo"
        ]
    },
    "Truco": {
        categoria: "mesa",
        modalidad: "Equipos",
        integrantes: 2,
        sistemas: [
            "Liga",
            "Eliminación directa",
            "Sistema Suizo"
        ]
    },
    "Catan": {
        categoria: "mesa",
        modalidad: "Individual",
        integrantes: 1,
        sistemas: ["Liga", "Sistema Suizo"]
    },
    "Ajedrez": {
        categoria: "mesa",
        modalidad: "Individual",
        integrantes: 1,
        sistemas: [
            "Liga",
            "Eliminación directa",
            "Sistema Suizo"
        ]
    }
};


/*
=====================================================
    DATOS INICIALES
=====================================================
*/

const TORNEOS_INICIALES = [
    {
        id: 1,
        nombre: "LoL Summer Cup",
        disciplina: "League of Legends",
        estado: "Inscripciones abiertas",
        participantesActuales: 12,
        cupos: 16,
        fechaInicio: "2026-08-15",
        fechaCierre: "2026-08-10",
        sistema: "Eliminación directa",
        ubicacion: "Online",
        descripcion:
            "Torneo amateur de League of Legends para equipos de cinco jugadores.",
        reglamento:
            "Cada equipo debe presentarse quince minutos antes de cada partida."
    },
    {
        id: 2,
        nombre: "Copa Primavera",
        disciplina: "Fútbol",
        estado: "En juego",
        participantesActuales: 24,
        cupos: 32,
        fechaInicio: "2026-08-22",
        fechaCierre: "2026-08-17",
        sistema: "Liga",
        ubicacion: "Complejo Deportivo Central",
        descripcion:
            "Competencia de fútbol organizada mediante sistema de liga.",
        reglamento:
            "Los partidos tendrán dos tiempos de cuarenta y cinco minutos."
    },
    {
        id: 3,
        nombre: "Torneo Nacional",
        disciplina: "Ajedrez",
        estado: "Finalizado",
        participantesActuales: 64,
        cupos: 64,
        fechaInicio: "2026-05-10",
        fechaCierre: "2026-05-05",
        sistema: "Sistema Suizo",
        ubicacion: "Centro Cultural Montevideo",
        descripcion:
            "Torneo nacional de ajedrez individual mediante sistema suizo.",
        reglamento:
            "Cada participante dispondrá del tiempo establecido por ronda."
    }
];


/*
=====================================================
    FUNCIONES GENERALES
=====================================================
*/

function obtenerConfiguracionDisciplina(disciplina) {
    return DISCIPLINAS[disciplina] ?? null;
}

function obtenerDisciplinas() {
    return Object.keys(DISCIPLINAS);
}

function sistemaPermitido(disciplina, sistema) {
    return DISCIPLINAS[disciplina]?.sistemas.includes(sistema) ?? false;
}

function esPotenciaDeDos(numero) {
    return (
        Number.isInteger(numero) &&
        numero >= 2 &&
        (numero & (numero - 1)) === 0
    );
}

function obtenerClaseEstado(estado) {
    return CLASES_ESTADO[estado] ?? "finalizado";
}

function formatearFechaCorta(fecha) {
    if (!fecha) {
        return "Sin fecha";
    }

    return new Intl.DateTimeFormat("es-UY", {
        day: "2-digit",
        month: "short",
        year: "numeric"
    }).format(new Date(`${fecha}T00:00:00`));
}


/*
=====================================================
    NORMALIZACIÓN
=====================================================
*/

/**
 * Completa los datos derivados y corrige valores antiguos.
 */
function normalizarTorneo(torneo) {
    const configuracion =
        obtenerConfiguracionDisciplina(torneo.disciplina);

    const normalizado = {
        ...torneo,
        participantesActuales:
            Number(torneo.participantesActuales) || 0,
        cupos: Number(torneo.cupos) || 2,
        claseEstado: obtenerClaseEstado(torneo.estado)
    };

    if (configuracion) {
        normalizado.categoria = configuracion.categoria;
        normalizado.modalidad = configuracion.modalidad;
        normalizado.integrantesPorEquipo =
            configuracion.integrantes;

        if (!configuracion.sistemas.includes(normalizado.sistema)) {
            normalizado.sistema =
                normalizado.sistema === "Grupos + Eliminación" &&
                configuracion.sistemas.includes("Eliminación directa")
                    ? "Eliminación directa"
                    : configuracion.sistemas[0];
        }
    }

    const unidad =
        normalizado.modalidad === "Equipos"
            ? "equipos"
            : "jugadores";

    normalizado.participantes =
        `${normalizado.participantesActuales} / ` +
        `${normalizado.cupos} ${unidad}`;

    normalizado.fecha =
        normalizado.estado === "Finalizado"
            ? "Finalizado"
            : formatearFechaCorta(normalizado.fechaInicio);

    return normalizado;
}


/*
=====================================================
    ALMACENAMIENTO
=====================================================
*/

function cargarTorneos() {
    try {
        const guardados = JSON.parse(
            localStorage.getItem(CLAVE_TORNEOS)
        );

        const origen = Array.isArray(guardados)
            ? guardados
            : TORNEOS_INICIALES;

        return origen.map(normalizarTorneo);
    } catch (error) {
        console.error("Error al cargar los torneos:", error);
        return TORNEOS_INICIALES.map(normalizarTorneo);
    }
}

let TORNEOS = cargarTorneos();

function guardarTorneos() {
    try {
        TORNEOS = TORNEOS.map(normalizarTorneo);

        localStorage.setItem(
            CLAVE_TORNEOS,
            JSON.stringify(TORNEOS)
        );

        return true;
    } catch (error) {
        console.error("Error al guardar los torneos:", error);
        return false;
    }
}


/*
=====================================================
    CONSULTAS Y CAMBIOS
=====================================================
*/

function obtenerTorneoPorId(id) {
    return TORNEOS.find(
        (torneo) => torneo.id === Number(id)
    );
}

function actualizarTorneo(id, cambios) {
    const torneo = obtenerTorneoPorId(id);

    if (!torneo) {
        return null;
    }

    Object.assign(torneo, cambios);
    Object.assign(torneo, normalizarTorneo(torneo));

    return guardarTorneos() ? torneo : null;
}