/**
 * ============================================================================
 * ARCHIVO DE JAVASCRIPT: REPORTES ADMINISTRADOR
 * ============================================================================
 * Lógica específica para la pantalla de exportación de reportes.
 */

// Usamos delegación de eventos en 'document' porque las vistas se cargan 
// dinámicamente mediante el router (innerHTML), por lo que los elementos 
// no existen al momento de cargar la página inicial.
document.addEventListener('click', function(evento) {
    
    // Botón Descargar PDF
    if (evento.target.closest('#btn-exportar-pdf')) {
        evento.preventDefault();
        mostrarNotificacionToast('Generando PDF...', 'info');
    }
    
    // Botón Descargar Excel
    if (evento.target.closest('#btn-exportar-excel')) {
        evento.preventDefault();
        mostrarNotificacionToast('Generando Excel...', 'info');
    }
    
    // Botón Descargar CSV
    if (evento.target.closest('#btn-exportar-csv')) {
        evento.preventDefault();
        mostrarNotificacionToast('Generando Reporte CSV...', 'info');
    }
    
});
