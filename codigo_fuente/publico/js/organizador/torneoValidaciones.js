"use strict";

/*
=====================================================
    VALIDACIONES DE TORNEOS
=====================================================
*/

window.TorneoValidaciones = (() => {
    const TRANSICIONES_ESTADO = {
        "Inscripciones abiertas": [
            "Inscripciones abiertas",
            "En juego"
        ],
        "En juego": [
            "En juego",
            "Finalizado"
        ],
        "Finalizado": ["Finalizado"]
    };

    const CUPOS_ELIMINACION = [2, 4, 8, 16, 32, 64];

    const LIMITES_CUPOS = {
        "Liga": { minimo: 2, maximo: 40 },
        "Sistema Suizo": { minimo: 4, maximo: 64 }
    };

    /*
        Devuelve la fecha local actual en formato YYYY-MM-DD.
    */
    function obtenerFechaActual() {
        const hoy = new Date();
        const año = hoy.getFullYear();
        const mes = String(hoy.getMonth() + 1).padStart(2, "0");
        const dia = String(hoy.getDate()).padStart(2, "0");

        return `${año}-${mes}-${dia}`;
    }

    /*
        Los datos estructurales solo pueden cambiarse
        antes de que existan participantes.
    */
    function puedeEditarSistema(torneo) {
        return (
            torneo.estado === "Inscripciones abiertas" &&
            torneo.participantesActuales === 0
        );
    }

    /*
        Fechas y cupos quedan bloqueados cuando el torneo
        comienza o finaliza.
    */
    function puedeEditarDatosPrevios(torneo) {
        return torneo.estado === "Inscripciones abiertas";
    }

    function obtenerEstadosPermitidos(torneo) {
        return TRANSICIONES_ESTADO[torneo.estado] ?? [
            torneo.estado
        ];
    }

    /*
        Opciones disponibles para una llave de
        eliminación directa.
    */
    function obtenerCuposEliminacion(participantes = 0) {
        return CUPOS_ELIMINACION.filter(
            (cupos) => cupos >= participantes
        );
    }

    function obtenerLimitesCupos(sistema) {
        if (sistema === "Eliminación directa") {
            return { minimo: 2, maximo: 64 };
        }

        return LIMITES_CUPOS[sistema] ?? {
            minimo: 2,
            maximo: 64
        };
    }

    /*
        Valida la cantidad máxima de equipos o jugadores.
    */
    function validarCupos(torneo, cupos, sistema) {
        if (!Number.isInteger(cupos)) {
            return "Los cupos deben ser un número entero.";
        }

        if (cupos < torneo.participantesActuales) {
            return (
                "Los cupos no pueden ser menores a los " +
                "participantes registrados."
            );
        }

        if (sistema === "Eliminación directa") {
            return CUPOS_ELIMINACION.includes(cupos)
                ? null
                : (
                    "En eliminación directa los cupos deben ser " +
                    "2, 4, 8, 16, 32 o 64."
                );
        }

        const limites = obtenerLimitesCupos(sistema);

        if (cupos < limites.minimo || cupos > limites.maximo) {
            return (
                `En ${sistema} los cupos deben estar entre ` +
                `${limites.minimo} y ${limites.maximo}.`
            );
        }

        /*
            Liga y Sistema Suizo aceptan cantidades
            pares o impares.
        */
        return null;
    }

    /*
        Valida fechas y evita modificar datos de un
        torneo que ya comenzó.
    */
    function validarFechas(
        torneo,
        fechaCierre,
        fechaInicio,
        estadoNuevo
    ) {
        if (!fechaCierre || !fechaInicio) {
            return "Debes completar ambas fechas.";
        }

        /*
            Si ya está En juego o Finalizado, las fechas
            deben conservar sus valores originales.
        */
        if (!puedeEditarDatosPrevios(torneo)) {
            const modificadas =
                fechaInicio !== torneo.fechaInicio ||
                fechaCierre !== torneo.fechaCierre;

            return modificadas
                ? "Las fechas no pueden modificarse después de comenzar el torneo."
                : null;
        }

        if (fechaCierre > fechaInicio) {
            return (
                "El cierre de inscripciones no puede ser " +
                "posterior a la fecha de inicio."
            );
        }

        const hoy = obtenerFechaActual();

        /*
            Mientras siga abierto, ninguna fecha puede
            quedar en el pasado.
        */
        if (estadoNuevo === "Inscripciones abiertas") {
            if (fechaCierre < hoy) {
                return (
                    "El cierre de inscripciones no puede ser " +
                    "anterior a la fecha actual."
                );
            }

            if (fechaInicio < hoy) {
                return (
                    "La fecha de inicio no puede ser anterior " +
                    "a la fecha actual."
                );
            }
        }

        /*
            Para comenzar el torneo, tanto el inicio como
            el cierre de inscripciones deben haber llegado.
        */
        if (estadoNuevo === "En juego") {
            if (fechaInicio > hoy) {
                return (
                    "No se puede comenzar el torneo antes " +
                    "de su fecha de inicio."
                );
            }

            if (fechaCierre > hoy) {
                return (
                    "No se puede comenzar el torneo mientras " +
                    "las inscripciones continúan abiertas."
                );
            }
        }

        return null;
    }

    return {
        obtenerFechaActual,
        puedeEditarSistema,

        puedeEditarCupos: puedeEditarDatosPrevios,
        puedeEditarFechas: puedeEditarDatosPrevios,

        obtenerEstadosPermitidos,
        obtenerCuposEliminacion,
        obtenerLimitesCupos,

        validarCupos,
        validarFechas
    };
})();