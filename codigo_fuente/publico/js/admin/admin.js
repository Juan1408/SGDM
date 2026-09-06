/**
 * ============================================================================
 * ARCHIVO DE INTERACCIONES JAVASCRIPT DEL PANEL DE ADMINISTRADOR
 * ============================================================================
 * Este archivo contiene la lógica para las animaciones y funcionalidades 
 * dinámicas (interactivas) del panel.
 */

// Esperar a que todo el HTML termine de cargar antes de ejecutar el código
document.addEventListener('DOMContentLoaded', function() {

    /* ========================================================================
       0. MENÚ HAMBURGUESA (MÓVILES)
       ======================================================================== */
    const btnMenuMovil = document.getElementById('btnMenuMovil');
    const menuLateral = document.querySelector('.menu-lateral');

    if (btnMenuMovil && menuLateral) {
        btnMenuMovil.addEventListener('click', function(evento) {
            evento.stopPropagation(); // Evitar propagación para no activar el cierre inmediatamente
            menuLateral.classList.toggle('activo');
        });

        // Cerrar el menú lateral al hacer clic en cualquier otro lado de la pantalla
        document.addEventListener('click', function(evento) {
            // Si el menú está abierto y el clic NO fue dentro del menú ni en el botón
            if (menuLateral.classList.contains('activo') && 
                !menuLateral.contains(evento.target) && 
                !btnMenuMovil.contains(evento.target)) {
                menuLateral.classList.remove('activo');
            }
        });
    }

    /* ========================================================================
       0.5 MENÚ DESPLEGABLE DE PERFIL (PC)
       ======================================================================== */
    const btnPerfilAdmin = document.getElementById('btnPerfilAdmin');
    const menuPerfilAdmin = document.getElementById('menuPerfilAdmin');

    if (btnPerfilAdmin && menuPerfilAdmin) {
        // Al hacer clic en el botón, alternar el menú
        btnPerfilAdmin.addEventListener('click', function(evento) {
            evento.stopPropagation(); // Evitar que el clic se propague al document
            menuPerfilAdmin.classList.toggle('mostrar');
        });

        // Si se hace clic en cualquier otro lado de la pantalla, cerrar el menú
        document.addEventListener('click', function(evento) {
            if (!menuPerfilAdmin.contains(evento.target)) {
                menuPerfilAdmin.classList.remove('mostrar');
            }
        });
    }

    /* ========================================================================
       1. MENÚ ACORDEÓN (SUBMENÚS EN LA BARRA LATERAL)
       ======================================================================== */
    const enlacesDelMenu = document.querySelectorAll('.menu-enlace');
    const todosLosSubmenus = document.querySelectorAll('.menu-sublista');
    
    // Ocultar submenús por defecto
    todosLosSubmenus.forEach(function(submenu) {
        submenu.style.display = 'none';
    });

    // Añadir el evento click a cada enlace
    enlacesDelMenu.forEach(function(enlaceActual) {
        enlaceActual.addEventListener('click', function(evento) {
            const posibleSubmenu = enlaceActual.nextElementSibling;
            
            if (posibleSubmenu && posibleSubmenu.classList.contains('menu-sublista')) {
                evento.preventDefault(); // Evitar que la página salte
                
                const estaAbierto = posibleSubmenu.style.display === 'block';

                if (estaAbierto) {
                    posibleSubmenu.style.display = 'none';
                } else {
                    // Cerrar los demás submenús antes de abrir este (efecto acordeón puro)
                    todosLosSubmenus.forEach(function(submenu) {
                        submenu.style.display = 'none';
                    });
                    posibleSubmenu.style.display = 'block';
                }
            } else {
                // Si el enlace NO tiene submenú, es un link normal a una pantalla.
                // En móviles, queremos que al hacer clic se cierre el menú lateral.
                if (window.innerWidth <= 768 && menuLateral.classList.contains('activo')) {
                    menuLateral.classList.remove('activo');
                }
            }
        });
    });

    /* ========================================================================
       2. ALERTAS DE CONFIRMACIÓN PARA ACCIONES DESTRUCTIVAS
       ========================================================================
       Esta función busca cualquier botón rojo (clase .boton-peligro) en la 
       pantalla y lanza una ventana preguntando si el usuario está seguro.
       Ideal para evitar borrar torneos o usuarios por error.
    */
    // Seleccionamos todos los botones que tengan la clase 'boton-peligro' o 'accion-eliminar'
    const botonesDePeligro = document.querySelectorAll('.boton-peligro');

    botonesDePeligro.forEach(function(boton) {
        // A cada botón le asignamos un evento 'click'
        boton.addEventListener('click', function(evento) {
            // window.confirm() abre la ventanita nativa del navegador con Aceptar/Cancelar
            const usuarioConfirma = window.confirm("¿Estás absolutamente seguro de que deseas realizar esta acción? Es irreversible.");
            
            // Si el usuario hace clic en 'Cancelar', la variable será 'false'
            if (usuarioConfirma === false) {
                // evento.preventDefault() detiene la acción, evitando que se borre el dato
                evento.preventDefault();
            }
            // Si presiona 'Aceptar', el código sigue su curso natural y se elimina el dato.
        });
    });

    /* ========================================================================
       3. VALIDACIÓN EN TIEMPO REAL DE FORMULARIOS
       ========================================================================
       Esta función revisa que no se envíen formularios vacíos. En HTML5 ya 
       existe el atributo 'required', pero esto añade una validación extra
       personalizada con JavaScript.
    */
    const formularios = document.querySelectorAll('.validacion-activa');

    formularios.forEach(function(formulario) {
        formulario.addEventListener('submit', function(evento) {
            // Buscamos todos los campos de texto/selects obligatorios dentro de ESTE formulario
            const camposRequeridos = formulario.querySelectorAll('.campo-requerido');
            let formularioValido = true; // Asumimos que todo está bien inicialmente

            camposRequeridos.forEach(function(campo) {
                // campo.value.trim() quita los espacios en blanco sobrantes
                if (campo.value.trim() === "") {
                    formularioValido = false; // Marcamos que hubo un error
                    campo.style.border = "2px solid #e74c3c"; // Pintamos el borde rojo para avisarle
                } else {
                    campo.style.border = "1px solid #ccc"; // Restauramos el borde normal
                }
            });

            if (formularioValido === false) {
                evento.preventDefault(); // Detenemos el envío del formulario al servidor
                alert("Por favor, completa todos los campos obligatorios antes de continuar.");
            }
        });
    });

});


function mostrarNotificacionToast(mensaje, tipo = 'exito') {
    const contenedor = document.getElementById('contenedorNotificaciones');
    if (!contenedor) return;

    const toast = document.createElement('div');
    toast.className = 'notificacion-toast';
    if (tipo === 'error') toast.style.backgroundColor = '#e74c3c';
    if (tipo === 'info') toast.style.backgroundColor = '#3498db';

    toast.innerHTML = `<span style="font-size: 1.2rem;">${tipo === 'exito' ? '✓' : (tipo === 'error' ? '⚠' : 'ℹ')}</span> <span>${mensaje}</span>`;
    contenedor.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('ocultar');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
