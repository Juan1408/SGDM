<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?? 'Torneos - SGDM'; ?></title>
    <link rel="stylesheet" href="/publico/css/style.css">
</head>
<body>
    <?php require_once __DIR__ . '/../parciales/cabecera.php'; ?>

    <main class="contenedor-pantalla" style="max-width: 1100px;">
        <div class="tarjeta-autenticacion" style="padding: 30px;">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap; margin-bottom:24px;">
                <div>
                    <h1 style="font-family: var(--fuente-titulo); font-size: 2rem; margin-bottom: 8px;">Torneos disponibles</h1>
                    <p style="color: var(--color-texto-secundario);">Aquí se listan los torneos creados para la fase 3 del proyecto SGDM.</p>
                </div>
                <?php if (!empty($puede_crear_torneo)): ?>
                    <a href="/torneos/crear" class="boton-primario" style="display:inline-block; width:auto; text-decoration:none; padding:12px 18px;">+ Crear torneo</a>
                <?php endif; ?>
            </div>

            <?php if (isset($_GET['mensaje_exito']) && $_GET['mensaje_exito'] === 'torneo_creado'): ?>
                <div class="alerta alerta-exito">El torneo fue creado correctamente y ya aparece en la lista.</div>
            <?php endif; ?>

            <?php if (empty($torneos)): ?>
                <div class="alerta alerta-error">Aún no hay torneos registrados. Crea el primero para comenzar el flujo del módulo 3.</div>
            <?php else: ?>
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:16px;">
                    <?php foreach ($torneos as $torneo): ?>
                        <article class="tarjeta-autenticacion" style="padding: 18px; animation: none;">
                            <p style="text-transform: uppercase; letter-spacing: .12em; color: #93c5fd; font-size: .78rem; margin-bottom: 8px;"><?php echo htmlspecialchars($torneo->formato); ?></p>
                            <h2 style="font-size: 1.15rem; margin-bottom: 8px;"><?php echo htmlspecialchars($torneo->nombre); ?></h2>
                            <p style="color: var(--color-texto-secundario); font-size: .95rem; margin-bottom: 10px;"><?php echo htmlspecialchars($torneo->descripcion ?: 'Sin descripción adicional.'); ?></p>
                            <ul style="list-style:none; display:grid; gap:6px; color: var(--color-texto-secundario); font-size: .9rem; margin-bottom: 14px;">
                                <li>Estado: <strong><?php echo htmlspecialchars($torneo->estado); ?></strong></li>
                                <li>Modalidad: <strong><?php echo $torneo->modalidadId === 1 ? 'Individual' : 'Equipos'; ?></strong></li>
                                <li>Puntos: V <?php echo $torneo->puntosVictoria; ?> / E <?php echo $torneo->puntosEmpate; ?> / D <?php echo $torneo->puntosDerrota; ?></li>
                            </ul>
                            <a href="/torneos/detalle?id=<?php echo (int)$torneo->id; ?>" class="boton-primario" style="display:inline-block; width:auto; text-decoration:none; padding:10px 14px;">Ver detalle</a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
