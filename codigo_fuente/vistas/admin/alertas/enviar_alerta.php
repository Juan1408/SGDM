<div class="contenedor-principal">
    <div class="header-modulo" style="border-bottom: 1px solid var(--color-borde); padding-bottom: 1rem; margin-bottom: 2rem;">
        <div class="titulo-modulo" style="display: flex; align-items: center; gap: 0.75rem;">
            <div class="pulse" style="width: 12px; height: 12px; background-color: var(--color-primario); border-radius: 50%; box-shadow: 0 0 8px var(--color-primario);"></div>
            <h1 style="margin: 0; font-size: 1.5rem; color: var(--color-texto-principal);">Centro de Comunicaciones</h1>
        </div>
        <p style="margin: 0.5rem 0 0 1.5rem; color: var(--color-texto-secundario); font-size: 0.95rem;">
            Envía alertas globales masivas a las bandejas de entrada de los usuarios.
        </p>
    </div>

    <!-- Formulario de Alerta -->
    <div class="tarjeta-formulario" style="background-color: var(--color-fondo-panel); border: 1px solid var(--color-borde); border-radius: var(--radio-borde); padding: 2rem; box-shadow: var(--sombra-suave); max-width: 800px;">
        
        <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=alerta&a=enviar" onsubmit="return confirm('¿Estás seguro de enviar esta alerta masiva a los usuarios seleccionados?');">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                
                <div class="grupo-input" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="audiencia" style="font-weight: 500; color: var(--color-texto-secundario);">Público Objetivo (Audiencia)</label>
                    <select name="audiencia" id="audiencia" style="padding: 0.6rem; border: 1px solid var(--color-borde); border-radius: 4px; background-color: var(--color-fondo-principal); color: var(--color-texto-principal); font-size: 0.95rem;">
                        <option value="todos">Todos los Usuarios Activos</option>
                        <option value="jugador">Solo Jugadores Registrados</option>
                        <option value="organizador">Solo Organizadores (Creadores de Torneos)</option>
                    </select>
                </div>

                <div class="grupo-input" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="tipo" style="font-weight: 500; color: var(--color-texto-secundario);">Tipo de Mensaje</label>
                    <select name="tipo" id="tipo" style="padding: 0.6rem; border: 1px solid var(--color-borde); border-radius: 4px; background-color: var(--color-fondo-principal); color: var(--color-texto-principal); font-size: 0.95rem;">
                        <option value="info">Información (Aviso general)</option>
                        <option value="exito">Buenas Noticias (Actualizaciones, Premios)</option>
                        <option value="advertencia">Advertencia (Mantenimientos, Cambios)</option>
                        <option value="error">Alerta Crítica (Brechas, Interrupciones)</option>
                    </select>
                </div>
            </div>

            <div class="grupo-input" style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1.5rem;">
                <label for="titulo" style="font-weight: 500; color: var(--color-texto-secundario);">Asunto del Mensaje</label>
                <input type="text" name="titulo" id="titulo" required placeholder="Ej: Actualización de Términos de Servicio" style="padding: 0.6rem; border: 1px solid var(--color-borde); border-radius: 4px; background-color: var(--color-fondo-principal); color: var(--color-texto-principal); font-size: 1rem; width: 100%; box-sizing: border-box;">
            </div>

            <div class="grupo-input" style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 2rem;">
                <label for="mensaje" style="font-weight: 500; color: var(--color-texto-secundario);">Cuerpo del Mensaje</label>
                <textarea name="mensaje" id="mensaje" required rows="6" placeholder="Escribe el comunicado aquí..." style="padding: 0.8rem; border: 1px solid var(--color-borde); border-radius: 4px; background-color: var(--color-fondo-principal); color: var(--color-texto-principal); font-size: 0.95rem; width: 100%; box-sizing: border-box; resize: vertical; font-family: inherit; line-height: 1.5;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primario" style="background-color: var(--color-primario); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: var(--radio-borde); font-size: 1rem; cursor: pointer; transition: background-color 0.2s; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-paper-plane"></i> Enviar a todos ahora
                </button>
            </div>
            
        </form>
    </div>
</div>

<script>
    // Mejora del feedback visual del botón primario
    const btnSubmit = document.querySelector('button[type="submit"]');
    if(btnSubmit) {
        btnSubmit.addEventListener('mouseover', () => {
            btnSubmit.style.backgroundColor = 'var(--color-primario-hover)';
        });
        btnSubmit.addEventListener('mouseout', () => {
            btnSubmit.style.backgroundColor = 'var(--color-primario)';
        });
    }
</script>
