// ============================================================================
// ROUTER DE MAQUETACIÓN (MOCKUP ROUTER)
// ============================================================================
// Este archivo simula el comportamiento de una Single Page Application (SPA) 
// y la lógica de backend para poder navegar entre las maquetas HTML sin PHP.
// TODO: ELIMINAR ESTE ARCHIVO CUANDO SE IMPLEMENTE EL BACKEND REAL EN PHP.

function cargarVista() {
    let rutaCompleta = window.location.hash.substring(1) || 'dashboard';
    let rutaBase = rutaCompleta.split('?')[0]; // ignorar variables como ?id=1 para buscar el archivo
    
    // Actualizar clase del body dependiendo de la pantalla
    document.body.className = 'tema-admin ' + (rutaBase === 'dashboard' ? 'en-inicio' : 'en-interna');
    
    fetch(rutaBase + '.html')
        .then(respuesta => {
            if (!respuesta.ok) throw new Error('No encontrado');
            return respuesta.text();
        })
        .then(codigoHtml => {
            // Inyectamos el HTML en el contenedor principal
            document.getElementById('area-contenido').innerHTML = codigoHtml;
            
            // EJECUTAR LÓGICA ESPECÍFICA DESPUÉS DE CARGAR LA VISTA
            if (rutaBase === 'usuarios/formulario') {
                ejecutarLogicaFormularioUsuario(rutaCompleta);
            }
        })
        .catch(error => {
            document.getElementById('area-contenido').innerHTML = `
                <div>
                    <h2 class="titulo-pantalla" style="color: #e74c3c;">Error 404</h2>
                    <p>La pantalla solicitada <strong>(${rutaBase})</strong> no existe o aún no ha sido maquetada.</p>
                    <a href="#dashboard" class="boton-secundario">Volver al Inicio</a>
                </div>
            `;
        });
}

function ejecutarLogicaFormularioUsuario(rutaCompleta) {
    const partesRuta = rutaCompleta.split('?');
    
    if (partesRuta.length > 1) {
        // urlParams lee las variables como id=1
        const parametrosUrl = new URLSearchParams(partesRuta[1]);
        const identificador = parametrosUrl.get('id');
        
        if (identificador) {
            // 1. Cambiar los textos
            const tituloPantalla = document.querySelector('.titulo-pantalla');
            if(tituloPantalla) tituloPantalla.textContent = 'Editar Usuario';
            
            const botonGuardar = document.querySelector('button[type="submit"]');
            if(botonGuardar) {
                botonGuardar.textContent = 'Actualizar Cambios';
                botonGuardar.style.backgroundColor = '#27ae60'; // Un tono verde para resaltar
            }
            
            // 2. Ocultar la contraseña temporal (se edita aparte)
            const campoContrasena = document.getElementById('contrasena');
            const etiquetaContrasena = document.querySelector('label[for="contrasena"]');
            if (campoContrasena && etiquetaContrasena) {
                campoContrasena.style.display = 'none';
                campoContrasena.removeAttribute('required'); // importante para que HTML5 no bloquee el envío
                etiquetaContrasena.style.display = 'none';
            }

            // 3. Llenar los campos (Simulando la base de datos)
            const campoNombre = document.getElementById('nombre');
            const campoCorreo = document.getElementById('correo');
            const listaRol = document.getElementById('rol');

            if (campoNombre && campoCorreo && listaRol) {
                if (identificador === '1') {
                    campoNombre.value = 'Michael Barneto';
                    campoCorreo.value = 'michaelsoporte@ascend.com';
                    listaRol.value = 'Soporte Tecnico';
                } else if (identificador === '2') {
                    campoNombre.value = 'Mauricio Pedrozo';
                    campoCorreo.value = 'mauricio@ascend.com';
                    listaRol.value = 'Organizador de Torneos';
                }
            }
        }
    }
}

window.addEventListener('hashchange', cargarVista);
window.addEventListener('DOMContentLoaded', cargarVista);

// ============================================================================
// SIMULACIÓN DE INTERACCIONES Y FLUJOS (TOAST Y CONFIRMACIONES)
// ============================================================================

function mostrarNotificacionToast(mensaje, tipo = 'exito') {
    const contenedor = document.getElementById('contenedorNotificaciones');
    if (!contenedor) return;

    const toast = document.createElement('div');
    toast.className = 'notificacion-toast';
    // Si es error/peligro cambiamos el fondo
    if (tipo === 'error') toast.style.backgroundColor = '#e74c3c';
    if (tipo === 'info') toast.style.backgroundColor = '#3498db';

    toast.innerHTML = `
        <span style="font-size: 1.2rem;">${tipo === 'exito' ? '✓' : (tipo === 'error' ? '⚠' : 'ℹ')}</span>
        <span>${mensaje}</span>
    `;

    contenedor.appendChild(toast);

    // Ocultar y remover después de 3 segundos
    setTimeout(() => {
        toast.classList.add('ocultar');
        setTimeout(() => toast.remove(), 300); // esperar animación
    }, 3000);
}

// Interceptar envíos de formularios para simular el guardado
document.addEventListener('submit', function(e) {
    // Si estamos en una vista de admin y el formulario es estándar
    if (e.target.classList.contains('formulario-estandar')) {
        e.preventDefault(); // Evita recargar la página

        const boton = e.target.querySelector('button[type="submit"]');
        const textoOriginal = boton ? boton.textContent : 'Guardando...';
        
        if (boton) {
            boton.textContent = 'Guardando...';
            boton.disabled = true;
        }

        // Simulamos retardo de red
        setTimeout(() => {
            mostrarNotificacionToast('¡Operación realizada con éxito!');
            
            // Lógica de redirección según de dónde venimos
            const rutaActual = window.location.hash;
            if (rutaActual.includes('usuarios/formulario')) window.location.hash = '#usuarios/lista';
            else if (rutaActual.includes('juegos/formulario')) window.location.hash = '#juegos/lista';
            else if (rutaActual.includes('roles/formulario')) window.location.hash = '#roles/lista';
            else if (rutaActual.includes('comunicaciones/enviar')) window.location.hash = '#dashboard';
            else if (rutaActual.includes('torneos/crear') || rutaActual.includes('torneos/configurar')) window.location.hash = '#torneos/lista';
            else if (rutaActual.includes('equipos/formulario')) window.location.hash = '#equipos/lista';
            else if (rutaActual.includes('resultados/formulario')) window.location.hash = '#resultados/lista';
            else if (rutaActual.includes('perfil/editar-perfil')) window.location.hash = '#dashboard';
            
            // Si es configuración general, no redirigir, solo mostrar el toast y resetear botón
            if (boton) {
                boton.textContent = textoOriginal;
                boton.disabled = false;
            }
        }, 800);
    }
});

// Interceptar clics en botones de eliminar/rechazar
document.addEventListener('click', function(e) {
    // Buscamos si hizo clic en un boton-peligro o si es hijo de uno
    const boton = e.target.closest('.boton-peligro');
    
    if (boton) {
        e.preventDefault();
        
        // Simular confirmación nativa
        if (confirm("¿Estás seguro de que deseas realizar esta acción? Esta operación no se puede deshacer.")) {
            // Si es parte de una tabla, eliminamos la fila visualmente
            const fila = boton.closest('tr');
            if (fila) {
                fila.style.opacity = '0.5';
                setTimeout(() => {
                    fila.remove();
                    mostrarNotificacionToast('Elemento eliminado/rechazado.', 'info');
                }, 400);
            } else {
                mostrarNotificacionToast('Acción destructiva ejecutada.', 'error');
            }
        }
    }
});
