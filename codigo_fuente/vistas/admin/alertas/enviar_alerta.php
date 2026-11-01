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
            <input type="hidden" name="csrf_token" value="<?php echo \App\Ayudantes\Sesion::generarTokenCSRF(); ?>">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                
                <div class="grupo-input" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="audiencia" style="font-weight: 500; color: var(--color-texto-secundario);">Público Objetivo (Audiencia)</label>
                    <select name="audiencia" id="audiencia" style="padding: 0.6rem; border: 1px solid var(--color-borde); border-radius: 4px; background-color: var(--color-fondo-principal); color: var(--color-texto-principal); font-size: 0.95rem;">
                        <option value="todos">Todos los Jugadores y Organizadores</option>
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

    <?php
    $notificacionesAdmin = $notificacionesAdmin ?? [];
    $newsletterTotal = (int) ($newsletterTotal ?? 0);
    $newsletterUltimos = $newsletterUltimos ?? [];
    $noLeidasAdmin = 0;
    foreach ($notificacionesAdmin as $notificacionAdmin) {
        if ((int) ($notificacionAdmin['leido'] ?? 0) === 0) {
            $noLeidasAdmin++;
        }
    }
    ?>

    <!-- BANDEJA DEL ADMINISTRADOR: mensajes de contacto recibidos -->
    <div class="tarjeta-formulario" style="background-color: var(--color-fondo-panel); border: 1px solid var(--color-borde); border-radius: var(--radio-borde); padding: 2rem; box-shadow: var(--sombra-suave); max-width: 1000px; margin-top: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem;">
            <div>
                <h2 style="margin: 0; font-size: 1.2rem; color: var(--color-texto-principal);">Notificaciones recibidas</h2>
                <p style="margin: 0.35rem 0 0; color: var(--color-texto-secundario); font-size: 0.9rem;">
                    Los mensajes enviados desde el formulario de contacto llegan a esta bandeja.
                </p>
            </div>
            <?php if ($noLeidasAdmin > 0): ?>
                <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=notificacion&a=marcarTodas">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" style="background: none; border: 1px solid var(--color-borde); color: var(--color-texto-principal); padding: 0.5rem 0.9rem; border-radius: var(--radio-borde); cursor: pointer;">
                        Marcar todo leído (<?php echo (int) $noLeidasAdmin; ?>)
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <?php if (empty($notificacionesAdmin)): ?>
            <p style="margin: 0; color: var(--color-texto-secundario);">Todavía no recibiste mensajes.</p>
        <?php else: ?>
            <ul style="list-style: none; margin: 0; padding: 0; display: grid; gap: 0.75rem;">
                <?php foreach ($notificacionesAdmin as $notificacionAdmin): ?>
                    <?php
                    $esNoLeida = (int) ($notificacionAdmin['leido'] ?? 0) === 0;
                    $fechaNotificacion = !empty($notificacionAdmin['creado_en'])
                        ? date('d/m/Y H:i', strtotime($notificacionAdmin['creado_en']))
                        : '';
                    ?>
                    <li style="border: 1px solid var(--color-borde); border-left: 3px solid <?php echo $esNoLeida ? 'var(--color-primario)' : 'transparent'; ?>; border-radius: 4px; padding: 0.9rem 1rem; background-color: var(--color-fondo-principal);">
                        <div style="display: flex; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                            <strong style="color: var(--color-texto-principal);"><?php echo htmlspecialchars((string) $notificacionAdmin['titulo']); ?></strong>
                            <small style="color: var(--color-texto-secundario);"><?php echo htmlspecialchars($fechaNotificacion); ?></small>
                        </div>
                        <p style="margin: 0.5rem 0 0; color: var(--color-texto-secundario); white-space: pre-line;"><?php echo htmlspecialchars((string) $notificacionAdmin['mensaje']); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <!-- RESUMEN DEL BOLETIN -->
    <div class="tarjeta-formulario" style="background-color: var(--color-fondo-panel); border: 1px solid var(--color-borde); border-radius: var(--radio-borde); padding: 2rem; box-shadow: var(--sombra-suave); max-width: 1000px; margin-top: 2rem;">
        <h2 style="margin: 0 0 0.35rem; font-size: 1.2rem; color: var(--color-texto-principal);">Boletín de noticias</h2>
        <p style="margin: 0 0 1rem; color: var(--color-texto-secundario); font-size: 0.9rem;">
            <?php echo (int) $newsletterTotal; ?> correo<?php echo $newsletterTotal === 1 ? '' : 's'; ?> activo<?php echo $newsletterTotal === 1 ? '' : 's'; ?> en la lista.
        </p>

        <?php if (!empty($newsletterUltimos)): ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                    <thead>
                        <tr style="text-align: left; color: var(--color-texto-secundario);">
                            <th style="padding: 0.5rem; border-bottom: 1px solid var(--color-borde);">Correo</th>
                            <th style="padding: 0.5rem; border-bottom: 1px solid var(--color-borde);">Suscripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($newsletterUltimos as $suscriptor): ?>
                            <tr>
                                <td style="padding: 0.5rem; border-bottom: 1px solid var(--color-borde); color: var(--color-texto-principal);"><?php echo htmlspecialchars((string) $suscriptor['email']); ?></td>
                                <td style="padding: 0.5rem; border-bottom: 1px solid var(--color-borde); color: var(--color-texto-secundario);">
                                    <?php echo !empty($suscriptor['fecha_suscripcion']) ? htmlspecialchars(date('d/m/Y', strtotime($suscriptor['fecha_suscripcion']))) : ''; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
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
