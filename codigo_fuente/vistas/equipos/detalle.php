<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?? 'Detalle de Equipo - SGDM'; ?></title>
    <link rel="stylesheet" href="/publico/css/style.css">
    <link rel="stylesheet" href="/publico/css/equipos.css">
</head>
<body>
    <?php require_once __DIR__ . '/../parciales/cabecera.php'; ?>

    <main class="contenedor-pagina">
        <div class="barra-superior">
            <h1 class="titulo-pagina"><?php echo htmlspecialchars($equipo->nombreEquipo); ?></h1>
            <a href="/equipos" style="color: var(--color-texto-secundario); text-decoration:none;">← Volver a Equipos</a>
        </div>

        <!-- Alertas -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alerta alerta-error">
                <?php
                $mensajes = [
                    'no_autorizado' => 'No tenés permisos para realizar esta acción.',
                    'decision_invalida' => 'La decisión ingresada no es válida.',
                ];
                echo htmlspecialchars($mensajes[$_GET['error']] ?? 'Ocurrió un error.');
                ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['mensaje_exito'])): ?>
            <div class="alerta alerta-exito">
                <?php
                $exitos = [
                    'miembro_aprobado' => 'El miembro fue aprobado e incorporado a la plantilla.',
                    'miembro_rechazado' => 'La solicitud fue rechazada.',
                ];
                echo htmlspecialchars($exitos[$_GET['mensaje_exito']] ?? 'Operación exitosa.');
                ?>
            </div>
        <?php endif; ?>

        <div class="grilla-dos-columnas">
            <!-- Columna izquierda: ficha del equipo -->
            <section class="tarjeta-seccion">
                <div style="text-align:center; margin-bottom: 20px;">
                    <div class="escudo-equipo escudo-grande">
                        <span class="escudo-placeholder"><?php echo strtoupper(substr($equipo->nombreEquipo, 0, 2)); ?></span>
                    </div>
                    <?php if ($equipo->descripcion): ?>
                        <p style="color: var(--color-texto-secundario); margin-top: 12px;"><?php echo htmlspecialchars($equipo->descripcion); ?></p>
                    <?php endif; ?>
                </div>

                <?php if ($es_capitan): ?>
                    <div class="info-box" style="margin-top: 16px;">
                        <p style="font-size: 0.8rem; color: var(--color-texto-secundario); margin-bottom:6px;">CÓDIGO DE INVITACIÓN</p>
                        <p style="font-family: monospace; font-size: 1.4rem; letter-spacing: 0.15em; color: #60a5fa; font-weight: bold;">
                            <?php echo htmlspecialchars($equipo->codigoInvitacion); ?>
                        </p>
                        <p style="font-size: 0.78rem; color: var(--color-texto-secundario); margin-top:6px;">Compartí este código con tus jugadores para que puedan solicitar unirse.</p>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Columna derecha: plantilla -->
            <section class="tarjeta-seccion">
                <h2 class="subtitulo-seccion">Plantilla de Jugadores (<?php echo count($miembros); ?>)</h2>
                <?php if (empty($miembros)): ?>
                    <p style="color: var(--color-texto-secundario);">El equipo aún no tiene miembros.</p>
                <?php else: ?>
                    <ul class="lista-miembros">
                        <?php foreach ($miembros as $miembro): ?>
                            <li class="fila-miembro">
                                <div class="avatar-mini"><?php echo strtoupper(substr($miembro['nombre_completo'], 0, 2)); ?></div>
                                <div>
                                    <strong><?php echo htmlspecialchars($miembro['nombre_completo']); ?></strong>
                                    <?php if ($miembro['posicion'] || $miembro['numero_camiseta']): ?>
                                        <span style="color: var(--color-texto-secundario); font-size:0.82rem;">
                                            #<?php echo $miembro['numero_camiseta'] ?? '-'; ?> · <?php echo htmlspecialchars($miembro['posicion'] ?? ''); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>
        </div>

        <!-- Solicitudes pendientes (sólo para capitanes) -->
        <?php if ($es_capitan && !empty($solicitudes)): ?>
            <section class="tarjeta-seccion" style="margin-top: 24px;">
                <h2 class="subtitulo-seccion">Solicitudes de Admisión Pendientes (<?php echo count($solicitudes); ?>)</h2>
                <ul class="lista-solicitudes">
                    <?php foreach ($solicitudes as $solicitud): ?>
                        <li class="fila-solicitud">
                            <div class="avatar-mini"><?php echo strtoupper(substr($solicitud['nombre_completo'], 0, 2)); ?></div>
                            <div style="flex:1;">
                                <strong><?php echo htmlspecialchars($solicitud['nombre_completo']); ?></strong>
                                <span style="font-size:0.8rem; color:var(--color-texto-secundario);"> – <?php echo htmlspecialchars($solicitud['email']); ?></span>
                                <?php if ($solicitud['mensaje']): ?>
                                    <p style="font-size:0.85rem; color:var(--color-texto-secundario); margin:4px 0 0 0;">"<?php echo htmlspecialchars($solicitud['mensaje']); ?>"</p>
                                <?php endif; ?>
                            </div>
                            <div class="botones-solicitud">
                                <form action="/equipos/solicitudes/procesar" method="POST" style="display:inline;">
                                    <input type="hidden" name="token_csrf" value="<?php echo htmlspecialchars($token_csrf); ?>">
                                    <input type="hidden" name="solicitud_id" value="<?php echo $solicitud['id']; ?>">
                                    <input type="hidden" name="decision" value="aprobar">
                                    <button type="submit" class="boton-exito">✔ Aprobar</button>
                                </form>
                                <form action="/equipos/solicitudes/procesar" method="POST" style="display:inline;">
                                    <input type="hidden" name="token_csrf" value="<?php echo htmlspecialchars($token_csrf); ?>">
                                    <input type="hidden" name="solicitud_id" value="<?php echo $solicitud['id']; ?>">
                                    <input type="hidden" name="decision" value="rechazar">
                                    <button type="submit" class="boton-peligro">✘ Rechazar</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>
