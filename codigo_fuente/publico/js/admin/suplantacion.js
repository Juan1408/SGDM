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
    
    // Contenido del banner: la restauración es POST con token CSRF.
    banner.appendChild(document.createTextNode('Estás en modo suplantación (Ver como). '));

    var formulario = document.createElement('form');
    formulario.method = 'POST';
    formulario.action = window.URL_RESTAURAR_SESION;
    formulario.style.display = 'inline-block';
    formulario.style.marginLeft = '15px';

    var token = document.createElement('input');
    token.type = 'hidden';
    token.name = 'csrf_token';
    token.value = window.CSRF_TOKEN_SUPLANTACION || '';
    formulario.appendChild(token);

    var boton = document.createElement('button');
    boton.type = 'submit';
    boton.style.cssText = 'color: #fff; background: rgba(0,0,0,0.2); padding: 5px 12px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.4); cursor: pointer; font: inherit;';
    boton.innerHTML = '<i class="fa-solid fa-arrow-right-from-bracket" style="margin-right: 5px;"></i> Deshacer y volver al Administrador';
    formulario.appendChild(boton);

    banner.appendChild(formulario);
    
    // Inyectamos el banner al principio del body
    document.body.prepend(banner);
    
    // Empujamos el body un poco hacia abajo para que el banner no tape el header/navbar
    document.body.style.paddingTop = '40px';
});
