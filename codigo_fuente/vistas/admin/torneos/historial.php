<div class="contenedor-principal">
    <div class="header-modulo" style="border-bottom: 1px solid var(--color-borde); padding-bottom: 1rem; margin-bottom: 2rem;">
        <div class="titulo-modulo" style="display: flex; align-items: center; gap: 0.75rem;">
            <div class="pulse" style="width: 12px; height: 12px; background-color: var(--color-primario); border-radius: 50%; box-shadow: 0 0 8px var(--color-primario);"></div>
            <h1 style="margin: 0; font-size: 1.5rem; color: var(--color-texto-principal);">Historial de Torneos</h1>
        </div>
        <p style="margin: 0.5rem 0 0 1.5rem; color: var(--color-texto-secundario); font-size: 0.95rem;">
            Registro global de todos los torneos finalizados y cancelados en la plataforma.
        </p>
    </div>

    <div class="tarjeta-tabla" style="background: var(--color-fondo-panel); border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="tabla-estandar" style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead style="background-color: var(--color-fondo-principal); border-bottom: 2px solid var(--color-borde);">
                    <tr>
                        <th style="padding: 1rem; color: var(--color-texto-secundario); font-weight: 600;">ID</th>
                        <th style="padding: 1rem; color: var(--color-texto-secundario); font-weight: 600;">Torneo</th>
                        <th style="padding: 1rem; color: var(--color-texto-secundario); font-weight: 600;">Juego</th>
                        <th style="padding: 1rem; color: var(--color-texto-secundario); font-weight: 600;">Organizador</th>
                        <th style="padding: 1rem; color: var(--color-texto-secundario); font-weight: 600;">Participantes</th>
                        <th style="padding: 1rem; color: var(--color-texto-secundario); font-weight: 600;">Estado</th>
                        <th style="padding: 1rem; color: var(--color-texto-secundario); font-weight: 600; text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($torneos)): ?>
                        <tr>
                            <td colspan="7" style="padding: 2rem; text-align: center; color: var(--color-texto-secundario);">
                                No hay torneos finalizados o cancelados registrados.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($torneos as $torneo): ?>
                            <tr style="border-bottom: 1px solid var(--color-borde); transition: background 0.2s;">
                                <td style="padding: 1rem; color: var(--color-texto-principal);">#<?php echo $torneo['id']; ?></td>
                                <td style="padding: 1rem; font-weight: 500; color: var(--color-texto-principal);"><?php echo htmlspecialchars($torneo['torneo_nombre']); ?></td>
                                <td style="padding: 1rem; color: var(--color-texto-secundario);"><?php echo htmlspecialchars($torneo['juego_nombre']); ?></td>
                                <td style="padding: 1rem; color: var(--color-texto-secundario);"><?php echo htmlspecialchars($torneo['organizador_nombre']); ?></td>
                                <td style="padding: 1rem; color: var(--color-texto-secundario);"><?php echo $torneo['total_participantes']; ?></td>
                                <td style="padding: 1rem;">
                                    <?php if ($torneo['estado'] === 'finalizado'): ?>
                                        <span style="background-color: rgba(69, 182, 118, 0.15); color: var(--color-exito); padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                            Finalizado
                                        </span>
                                    <?php else: ?>
                                        <span style="background-color: rgba(224, 93, 93, 0.15); color: var(--color-peligro); padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                            Cancelado
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <button class="btn" style="padding: 0.5rem 1rem; background-color: var(--color-fondo-principal); color: var(--color-texto-principal); border: 1px solid var(--color-borde); border-radius: 6px; cursor: pointer;" onclick="alert('Funcionalidad en desarrollo para ver podio del torneo #<?php echo $torneo['id']; ?>')">
                                        <i class="fa-solid fa-trophy"></i> Ver Podio
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
