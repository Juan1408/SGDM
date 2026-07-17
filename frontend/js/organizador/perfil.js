"use strict";

/*
=====================================================
    CONFIGURACIÓN
=====================================================
*/

const CLAVE_PERFIL_ORGANIZADOR =
    "ascendPerfilOrganizador";

const PERFIL_PREDETERMINADO = {

    nombre: "Valentina",

    correo: "valentina@email.com",

    telefono: "",

    organizacion: "SGDM",

    localidad: "Montevideo",

    biografia:
        "Organizadora de torneos y competencias dentro de SGDM.",

    foto:
        "../../img/avatar.png",

    password: "123456"

};

let perfil = cargarPerfil();


/*
=====================================================
    CARGA Y GUARDADO
=====================================================
*/

function cargarPerfil() {

    try {

        const guardado = JSON.parse(
            localStorage.getItem(
                CLAVE_PERFIL_ORGANIZADOR
            )
        );

        return {

            ...PERFIL_PREDETERMINADO,

            ...guardado

        };

    } catch {

        return {

            ...PERFIL_PREDETERMINADO

        };

    }

}

function guardarPerfil() {

    localStorage.setItem(

        CLAVE_PERFIL_ORGANIZADOR,

        JSON.stringify(perfil)

    );

}

/*
=====================================================
    ELEMENTOS
=====================================================
*/

const fotoPerfil =
    document.getElementById("fotoPerfil");

const nombreResumen =
    document.getElementById("nombreResumen");

const correoResumen =
    document.getElementById("correoResumen");

const nombreDisplay =
    document.getElementById("nombreDisplay");

const emailDisplay =
    document.getElementById("emailDisplay");

const telefonoDisplay =
    document.getElementById("telefonoDisplay");

const organizacionDisplay =
    document.getElementById("organizacionDisplay");

const localidadDisplay =
    document.getElementById("localidadDisplay");

const biografiaDisplay =
    document.getElementById("biografiaDisplay");

const cantidadTorneos =
    document.getElementById("cantidadTorneosPerfil");

const cantidadActivos =
    document.getElementById("cantidadActivosPerfil");

const cantidadParticipantes =
    document.getElementById(
        "cantidadParticipantesPerfil"
    );

    /*
=====================================================
    ACTUALIZAR INTERFAZ
=====================================================
*/

function actualizarPerfil() {

    fotoPerfil.src = perfil.foto;

    nombreResumen.textContent =
        perfil.nombre;

    correoResumen.textContent =
        perfil.correo;

    nombreDisplay.textContent =
        perfil.nombre;

    emailDisplay.textContent =
        perfil.correo;

    telefonoDisplay.textContent =
        perfil.telefono || "Sin definir";

    organizacionDisplay.textContent =
        perfil.organizacion;

    localidadDisplay.textContent =
        perfil.localidad;

    biografiaDisplay.textContent =
        perfil.biografia;

    actualizarSidebar();

    actualizarEstadisticas();

}

/*
=====================================================
    SIDEBAR
=====================================================
*/

function actualizarSidebar() {

    const nombreSidebar =
        document.querySelector(
            ".sidebar-user h3"
        );

    const fotoSidebar =
        document.querySelector(
            ".sidebar-user img"
        );

    if (nombreSidebar) {

        nombreSidebar.textContent =
            perfil.nombre;

    }

    if (fotoSidebar) {

        fotoSidebar.src =
            perfil.foto;

    }

}

/*
=====================================================
    ESTADÍSTICAS
=====================================================
*/

function actualizarEstadisticas() {

    cantidadTorneos.textContent =
        TORNEOS.length;

    cantidadActivos.textContent =
        TORNEOS.filter(

            torneo =>
                torneo.estado ===
                "En juego"

        ).length;

    const totalParticipantes =
        TORNEOS.reduce(

            (total, torneo) =>

                total +
                (torneo.participantesActuales || 0),

            0

        );

    cantidadParticipantes.textContent =
        totalParticipantes;

}

/*
=====================================================
    INICIO
=====================================================
*/

actualizarPerfil();