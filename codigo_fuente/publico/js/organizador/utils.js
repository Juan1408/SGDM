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
            "Cada equipo debe presentarse quince minutos antes de cada partida.",

fixture: [
    {
        round: 1,
        label: "Cuartos de final",
        matches: [
            {
                id: 1,
                teamA: "Team Phoenix",
                teamB: "Dark Wolves",
                date: "2026-08-15",
                time: "18:00",
                evento: "Online",
                status: "Pendiente"
            },
            {
                id: 2,
                teamA: "Nova Esports",
                teamB: "Blue Dragons",
                date: "2026-08-15",
                time: "19:30",
                evento: "Online",
                status: "Pendiente"
            },
            {
                id: 3,
                teamA: "Infinity Gaming",
                teamB: "Red Titans",
                date: "2026-08-16",
                time: "18:00",
                evento: "Online",
                status: "Pendiente"
            },
            {
                id: 4,
                teamA: "Shadow Crew",
                teamB: "Team Aurora",
                date: "2026-08-16",
                time: "19:30",
                evento: "Online",
                status: "Pendiente"
            }
        ]
    },
    {
        round: 2,
        label: "Semifinales",
        matches: [
            {
                id: 5,
                teamA: "Ganador partido 1",
                teamB: "Ganador partido 2",
                date: "2026-08-20",
                time: "18:00",
                evento: "Online",
                status: "Pendiente"
            },
            {
                id: 6,
                teamA: "Ganador partido 3",
                teamB: "Ganador partido 4",
                date: "2026-08-20",
                time: "20:00",
                evento: "Online",
                status: "Pendiente"
            }
        ]
    },
    {
        round: 3,
        label: "Final",
        matches: [
            {
                id: 7,
                teamA: "Ganador semifinal 1",
                teamB: "Ganador semifinal 2",
                date: "2026-08-24",
                time: "20:00",
                evento: "Online",
                status: "Pendiente"
            }
        ]
    }
],

participantesData: [
    {
        id: 1,
        nombre: "Team Phoenix",
        capitan: "Juan Álvarez",
        tipo: "Equipo",
        estado: "Confirmado"
    },
    {
        id: 2,
        nombre: "Dark Wolves",
        capitan: "Carla Suárez",
        tipo: "Equipo",
        estado: "Confirmado"
    },
    {
        id: 3,
        nombre: "Nova Esports",
        capitan: "Marco López",
        tipo: "Equipo",
        estado: "Confirmado"
    },
    {
        id: 4,
        nombre: "Blue Dragons",
        capitan: "Sofía Méndez",
        tipo: "Equipo",
        estado: "Confirmado"
    },
    {
        id: 5,
        nombre: "Infinity Gaming",
        capitan: "Martín Díaz",
        tipo: "Equipo",
        estado: "Confirmado"
    },
    {
        id: 6,
        nombre: "Red Titans",
        capitan: "Lucía Pérez",
        tipo: "Equipo",
        estado: "Confirmado"
    },
    {
        id: 7,
        nombre: "Shadow Crew",
        capitan: "Diego Flores",
        tipo: "Equipo",
        estado: "Confirmado"
    },
    {
        id: 8,
        nombre: "Team Aurora",
        capitan: "Ana Torres",
        tipo: "Equipo",
        estado: "Confirmado"
    }
],

eventos: [
    {
        id: 1,
        tipo: "administrativo",
        titulo: "Cierre de inscripciones",
        descripcion:
            "Último día para confirmar equipos y documentación.",
        fecha: "2026-08-10",
        hora: "23:59",
        ubicacion: "Online",
        estado: "Pendiente"
    },
    {
        id: 2,
        tipo: "reunion",
        titulo: "Reunión de capitanes",
        descripcion:
            "Repaso del reglamento y confirmación de horarios.",
        fecha: "2026-08-14",
        hora: "20:00",
        ubicacion: "Discord oficial",
        estado: "Pendiente"
    },
    {
        id: 3,
        tipo: "competencia",
        titulo: "Inicio del torneo",
        descripcion:
            "Comienzo oficial de la primera ronda.",
        fecha: "2026-08-15",
        hora: "18:00",
        ubicacion: "Online",
        estado: "Pendiente"
    },
    {
        id: 4,
        tipo: "premiacion",
        titulo: "Final y premiación",
        descripcion:
            "Partido final y entrega de reconocimientos.",
        fecha: "2026-08-24",
        hora: "20:00",
        ubicacion: "Online",
        estado: "Pendiente"
    }
],

solicitudesData: [
    {
        id: 1,
        nombre: "Team Eclipse",
        responsable: "Martina Silva",
        tipo: "Equipo",
        fecha: "2026-08-08",
        estado: "Pendiente"
    },
    {
        id: 2,
        nombre: "Crimson Foxes",
        responsable: "Lucas Rodríguez",
        tipo: "Equipo",
        fecha: "2026-08-09",
        estado: "Pendiente"
    },
    {
        id: 3,
        nombre: "Night Owls",
        responsable: "Camila Pereira",
        tipo: "Equipo",
        fecha: "2026-08-09",
        estado: "Pendiente"
    }
]


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

    const torneoInicial = TORNEOS_INICIALES.find(
    (item) => item.id === Number(torneo.id)
);

const fixtureOrigen = Array.isArray(torneo.fixture)
    ? torneo.fixture
    : torneoInicial?.fixture ?? [];

normalizado.fixture = fixtureOrigen.map((ronda) => ({
    ...ronda,

    matches: Array.isArray(ronda.matches)
        ? ronda.matches.map((partido) => ({
            ...partido,

            scoreA:
                partido.scoreA === null ||
                partido.scoreA === undefined
                    ? null
                    : Number(partido.scoreA),

            scoreB:
                partido.scoreB === null ||
                partido.scoreB === undefined
                    ? null
                    : Number(partido.scoreB),

            winner: partido.winner ?? null,

            status: partido.status ?? "Pendiente"
        }))
        : []
}));

    normalizado.participantesData =
    Array.isArray(torneo.participantesData)
        ? torneo.participantesData
        : torneoInicial?.participantesData ?? [];

    normalizado.eventos =
    Array.isArray(torneo.eventos)
        ? torneo.eventos
        : torneoInicial?.eventos ?? [];

    normalizado.solicitudesData =
    Array.isArray(torneo.solicitudesData)
        ? torneo.solicitudesData
        : torneoInicial?.solicitudesData ?? [];

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
    GENERACIÓN DE FIXTURE
=====================================================
*/

function mezclarLista(lista) {
    const copia = [...lista];

    for (let i = copia.length - 1; i > 0; i--) {
        const indiceAleatorio =
            Math.floor(Math.random() * (i + 1));

        [copia[i], copia[indiceAleatorio]] = [
            copia[indiceAleatorio],
            copia[i]
        ];
    }

    return copia;
}

function obtenerNombreRonda(cantidadParticipantes) {
    const nombres = {
        2: "Final",
        4: "Semifinales",
        8: "Cuartos de final",
        16: "Octavos de final",
        32: "Dieciseisavos de final",
        64: "Treintaidosavos de final"
    };

    return nombres[cantidadParticipantes] ??
        `Ronda de ${cantidadParticipantes}`;
}

function generarFixtureEliminacionDirecta(
    participantes,
    opciones = {}
) {
    const {
        mezclar = true,
        fechaInicio = null,
        horaInicial = "18:00",
        ubicacion = "A definir"
    } = opciones;

    const nombres = participantes
        .map((participante) =>
            typeof participante === "string"
                ? participante
                : participante.nombre
        )
        .filter(Boolean);

    if (!esPotenciaDeDos(nombres.length)) {
        throw new Error(
            "La eliminación directa requiere 2, 4, 8, 16, 32 o 64 participantes."
        );
    }

    const participantesOrdenados = mezclar
        ? mezclarLista(nombres)
        : [...nombres];

    const rondas = [];

    let cantidadEnRonda =
        participantesOrdenados.length;

    let numeroRonda = 1;
    let siguienteIdPartido = 1;
    let idsRondaAnterior = [];

    while (cantidadEnRonda >= 2) {
        const cantidadPartidos =
            cantidadEnRonda / 2;

        const idsRondaActual = [];

        const ronda = {
            round: numeroRonda,
            label:
                obtenerNombreRonda(cantidadEnRonda),
            matches: []
        };

        for (
            let indice = 0;
            indice < cantidadPartidos;
            indice++
        ) {
            const idPartido =
                siguienteIdPartido++;

            idsRondaActual.push(idPartido);

            let teamA;
            let teamB;

            if (numeroRonda === 1) {
                teamA =
                    participantesOrdenados[
                        indice * 2
                    ];

                teamB =
                    participantesOrdenados[
                        indice * 2 + 1
                    ];
            } else {
                teamA =
                    `Ganador partido ${
                        idsRondaAnterior[
                            indice * 2
                        ]
                    }`;

                teamB =
                    `Ganador partido ${
                        idsRondaAnterior[
                            indice * 2 + 1
                        ]
                    }`;
            }

            ronda.matches.push({
                id: idPartido,
                teamA,
                teamB,
                date: fechaInicio,
                time: horaInicial,
                evento: ubicacion,
                scoreA: null,
                scoreB: null,
                winner: null,
                status: "Pendiente"
            });
        }

        rondas.push(ronda);

        idsRondaAnterior = idsRondaActual;
        cantidadEnRonda /= 2;
        numeroRonda++;
    }

    return rondas;
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