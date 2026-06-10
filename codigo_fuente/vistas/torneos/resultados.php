<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?? 'Resultados - SGDM'; ?></title>
    <link rel="stylesheet" href="/publico/css/style.css">
</head>
<body>
    <?php
    $torneo = (object)($torneo ?? []);
    $encuentros = $encuentros ?? [];
    $tabla = $tabla ?? [];
    require_once __DIR__ . '/../parciales/cabecera.php';
    ?>

    <main class="contenedor-pantalla" style="max-width: 1100px;">
        <div class="tarjeta-autenticacion" style="padding: 30px;">
            <a href="/torneos/ver?id=<?php echo (int)$torneo->id; ?>" style="color: #93c5fd; text-decoration:none; margin-bottom: 12px; display:inline-block;">← Volver al detalle</a>
            <h1 style="font-family: var(--fuente-titulo); font-size: 1.9rem; margin-bottom: 8px;">Resultados del torneo</h1>
            <p style="color: var(--color-texto-secundario); margin-bottom: 24px;">Módulo 4: registra resultados y revisa la tabla de posiciones en tiempo real.</p>

            <?php if (isset($_GET['mensaje_exito']) && $_GET['mensaje_exito'] === 'resultado_guardado'): ?>
                <div class="alerta alerta-exito">El resultado fue registrado correctamente.</div>
            <?php endif; ?>

            <section style="display:grid; gap:18px; grid-template-columns: 1.1fr 0.9fr; align-items:start;">
                <article class="tarjeta-autenticacion" style="padding: 18px;">
                    <h2 style="font-size: 1.05rem; margin-bottom: 10px;">Partidos</h2>
                    <?php if (empty($encuentros)): ?>
                        <p class="alerta alerta-error">Aún no hay partidos para mostrar. Inscribe participantes y vuelve a abrir esta vista para generar un fixture base.</p>
                    <?php else: ?>
                        <div style="display:grid; gap:12px;">
                            <?php foreach ($encuentros as $encuentro): ?>
                                <div style="border:1px solid rgba(148,163,184,0.25); border-radius:14px; padding:14px; background: rgba(15,23,42,0.35);">
                                    <p style="font-size:.88rem; color:#93c5fd; margin-bottom:6px;">Ronda <?php echo (int)$encuentro->ronda; ?> · Estado: <?php echo htmlspecialchars($encuentro->estado); ?></p>
                                    <form method="post" action="/torneos/resultados/guardar" style="display:grid; gap:8px;">
                                        <input type="hidden" name="encuentro_id" value="<?php echo (int)$encuentro->id; ?>">
                                        <input type="hidden" name="torneo_id" value="<?php echo (int)$torneo->id; ?>">
                                        <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                                            <strong><?php echo htmlspecialchars($encuentro->participanteLocalId ?? 'Sin rival'); ?></strong>
                                            <input type="number" step="0.1" name="resultado_local" value="<?php echo $encuentro->resultadoLocal ?? ''; ?>" style="width:90px; padding:8px; border-radius:10px; border:1px solid #334155; background:#0f172a; color:#fff;">
                                            <span>vs</span>
                                            <input type="number" step="0.1" name="resultado_visitante" value="<?php echo $encuentro->resultadoVisitante ?? ''; ?>" style="width:90px; padding:8px; border-radius:10px; border:1px solid #334155; background:#0f172a; color:#fff;">
                                            <strong><?php echo htmlspecialchars($encuentro->participanteVisitanteId ?? 'Sin rival'); ?></strong>
                                        </div>
                                        <button type="submit" class="boton-primario" style="width:auto; padding:10px 14px;">Guardar resultado</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </article>

                <article class="tarjeta-autenticacion" style="padding: 18px;">
                    <h2 style="font-size: 1.05rem; margin-bottom: 10px;">Tabla de posiciones</h2>
                    <?php if (empty($tabla)): ?>
                        <p class="alerta alerta-error">No hay posiciones aún. Registra resultados para que aparezca la clasificación.</p>
                    <?php else: ?>
                        <table style="width:100%; border-collapse:collapse; color:#e5eefb; font-size: .95rem;">
                            <thead>
                                <tr style="border-bottom:1px solid rgba(148,163,184,0.25); text-align:left;">
                                    <th style="padding:8px 6px;">#</th>
                                    <th style="padding:8px 6px;">Participante</th>
                                    <th style="padding:8px 6px;">PJ</th>
                                    <th style="padding:8px 6px;">PG</th>
                                    <th style="padding:8px 6px;">PE</th>
                                    <th style="padding:8px 6px;">PP</th>
                                    <th style="padding:8px 6px;">PTS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; foreach ($tabla as $fila): ?>
                                    <tr style="border-bottom:1px solid rgba(148,163,184,0.15);">
                                        <td style="padding:8px 6px;"><?php echo $i++; ?></td>
                                        <td style="padding:8px 6px;"><?php echo htmlspecialchars($fila['participante']->nombre); ?></td>
                                        <td style="padding:8px 6px;"><?php echo (int)$fila['pj']; ?></td>
                                        <td style="padding:8px 6px;"><?php echo (int)$fila['pg']; ?></td>
                                        <td style="padding:8px 6px;"><?php echo (int)$fila['pe']; ?></td>
                                        <td style="padding:8px 6px;"><?php echo (int)$fila['pp']; ?></td>
                                        <td style="padding:8px 6px; font-weight:700; color:#bfdbfe;"><?php echo (int)$fila['pts']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </article>
            </section>
        </div>
    </main>
</body>
</html>
