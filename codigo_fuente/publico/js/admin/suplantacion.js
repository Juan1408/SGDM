/**
 * ============================================================================
 * SCRIPT: suplantacion.js
 * ============================================================================
 * Propósito: Muestra un banner global persistente cuando un administrador
 *            está suplantando la sesión de otro usuario (Ver Como).
 * Ubicación: codigo_fuente/publico/js/admin/suplantacion.js
 * ============================================================================
 */

document.addEventListener('DOMContentLoaded', function() {
    // Verificamos si existe la variable global con la URL de restauración
    // Esta variable es inyectada por el Front Controller (index.php)
    if (typeof window.URL_RESTAURAR_SESION === 'undefined') {
        return;
    }

    var banner = document.createElement('div');
    
    // Estilos del banner
    banner.style.position = 'fixed';
    banner.style.top = '0';
    banner.style.left = '0';
    banner.style.width = '100%';
    banner.style.backgroundColor = '#ff4757'; // Rojo alerta
    banner.style.color = '#fff';
    banner.style.textAlign = 'center';
    banner.style.padding = '10px';
    banner.style.fontWeight = 'bold';
    banner.style.zIndex = '999999';
    banner.style.boxShadow = '0 4px 6px rgba(0,0,0,0.3)';
    
    // Contenido HTML del banner
    banner.innerHTML = `
        Estás en modo suplantación (Ver como). 
        <a href="${window.URL_RESTAURAR_SESION}" style="color: #fff; text-decoration: none; margin-left: 15px; background: rgba(0,0,0,0.2); padding: 5px 12px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.4); transition: background 0.2s;">
            <i class="fa-solid fa-arrow-right-from-bracket" style="margin-right: 5px;"></i> Deshacer y volver al Administrador
        </a>
    `;
    
    // Inyectamos el banner al principio del body
    document.body.prepend(banner);
    
    // Empujamos el body un poco hacia abajo para que el banner no tape el header/navbar
    document.body.style.paddingTop = '40px';
});
