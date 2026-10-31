/**
 * ============================================================================
 * CONTROLADOR UI: Monitoreo de Errores (Admin)
 * ============================================================================
 * Propósito: Maneja la apertura/cierre de acordeones de errores y el modal
 *            de resolución. Filosofía vanilla JS estricta.
 * ============================================================================
 */

document.addEventListener('DOMContentLoaded', () => {
    // Cerrar el modal haciendo clic afuera
    const modal = document.getElementById('modal-resolucion');
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) cerrarModal();
        });
    }
});

/**
 * Abre o cierra el acordeón para mostrar los detalles del error
 * @param {HTMLElement} element El div.error-resumen clickeado
 * @param {number} id El ID del error
 */
function toggleAcordeon(element, id) {
    const tarjeta = element.closest('.tarjeta-error');
    const detalle = document.getElementById('detalle-' + id);
    
    // Si ya está abierto, lo cerramos
    if (tarjeta.classList.contains('abierto')) {
        detalle.style.maxHeight = null;
        tarjeta.classList.remove('abierto');
    } else {
        // Cerramos los demás abiertos (opcional, para mantener orden)
        document.querySelectorAll('.tarjeta-error.abierto').forEach(abierta => {
            if (abierta !== tarjeta) {
                abierta.classList.remove('abierto');
                abierta.querySelector('.error-detalles').style.maxHeight = null;
            }
        });

        // Abrimos el actual
        tarjeta.classList.add('abierto');
        // Usamos scrollHeight para dar la altura exacta del contenido
        detalle.style.maxHeight = detalle.scrollHeight + "px";
    }
}

/**
 * Abre el modal para escribir la nota de resolución
 * @param {number} id 
 * @param {string} textoPrevio (Opcional) Texto de resolución anterior si se va a editar
 */
function abrirModalResolucion(id, textoPrevio = '') {
    document.getElementById('modal-error-id').value = id;
    
    // Si hay texto previo, lo colocamos en el textarea
    const textarea = document.getElementById('detalles_resolucion');
    textarea.value = textoPrevio;
    
    const modal = document.getElementById('modal-resolucion');
    modal.classList.add('activo');
    textarea.focus();
}

/**
 * Cierra el modal de resolución
 */
function cerrarModal() {
    const modal = document.getElementById('modal-resolucion');
    modal.classList.remove('activo');
    document.getElementById('form-resolucion').reset();
}
