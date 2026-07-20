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

    organizacion: "ASCEND",

    localidad: "Montevideo",

    biografia:
        "Organizadora de torneos y competencias dentro de ASCEND.",

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
/*
=====================================================
    MODALES Y EVENTOS (Añadidos para Maquetación)
=====================================================
*/

document.addEventListener('DOMContentLoaded', () => {

    // 1. Abrir modales
    const btnEditarPerfil = document.getElementById('editarPerfil');
    const btnEditarBiografia = document.getElementById('editarBiografia');
    const btnCambiarPassword = document.getElementById('cambiarPassword');

    if (btnEditarPerfil) {
        btnEditarPerfil.addEventListener('click', () => {
            const modal = document.getElementById('modalEditarPerfil');
            if (modal) {
                modal.classList.add('abierto');
                // Llenar datos actuales
                document.getElementById('inputNombre').value = perfil.nombre;
                document.getElementById('inputEmail').value = perfil.correo;
                document.getElementById('inputTelefono').value = perfil.telefono || '';
                document.getElementById('inputOrganizacion').value = perfil.organizacion;
                document.getElementById('inputLocalidad').value = perfil.localidad;
            }
        });
    }

    if (btnEditarBiografia) {
        btnEditarBiografia.addEventListener('click', () => {
            const modal = document.getElementById('modalBiografia');
            if (modal) {
                modal.classList.add('abierto');
                document.getElementById('inputBiografia').value = perfil.biografia;
            }
        });
    }

    if (btnCambiarPassword) {
        btnCambiarPassword.addEventListener('click', () => {
            const modal = document.getElementById('modalPassword');
            if (modal) modal.classList.add('abierto');
        });
    }

    // 2. Cerrar modales (botones X y Cancelar)
    document.querySelectorAll('[data-cerrar-modal]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const idModal = e.currentTarget.getAttribute('data-cerrar-modal');
            const modal = document.getElementById(idModal);
            if (modal) modal.classList.remove('abierto');
        });
    });

    // 3. Guardar cambios
    const formPerfil = document.getElementById('formEditarPerfil');
    if (formPerfil) {
        formPerfil.addEventListener('submit', (e) => {
            e.preventDefault();
            perfil.nombre = document.getElementById('inputNombre').value;
            perfil.correo = document.getElementById('inputEmail').value;
            perfil.telefono = document.getElementById('inputTelefono').value;
            perfil.organizacion = document.getElementById('inputOrganizacion').value;
            perfil.localidad = document.getElementById('inputLocalidad').value;

            guardarPerfil();
            actualizarPerfil();
            document.getElementById('modalEditarPerfil').classList.remove('abierto');
            mostrarNotificacionPerfil('Perfil actualizado correctamente.');
        });
    }

    const formBiografia = document.getElementById('formBiografia');
    if (formBiografia) {
        formBiografia.addEventListener('submit', (e) => {
            e.preventDefault();
            perfil.biografia = document.getElementById('inputBiografia').value;
            guardarPerfil();
            actualizarPerfil();
            document.getElementById('modalBiografia').classList.remove('abierto');
            mostrarNotificacionPerfil('Biografía actualizada.');
        });
    }

    const formPassword = document.getElementById('formPassword');
    if (formPassword) {
        formPassword.addEventListener('submit', (e) => {
            e.preventDefault();
            // Lógica ficticia para la maqueta
            document.getElementById('modalPassword').classList.remove('abierto');
            mostrarNotificacionPerfil('Contraseña cambiada con éxito.');
        });
    }

    // Opcional: Cerrar modal si se hace clic fuera del contenido
    document.querySelectorAll('.perfil-modal').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('abierto');
            }
        });
    });
});

function mostrarNotificacionPerfil(mensaje) {
    const notif = document.getElementById('perfilNotificacion');
    if (!notif) return;
    notif.textContent = mensaje;
    notif.style.display = 'block';
    setTimeout(() => {
        notif.style.display = 'none';
    }, 3000);
}

