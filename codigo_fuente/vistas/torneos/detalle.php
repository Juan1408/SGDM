<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?? 'Detalle del torneo - SGDM'; ?></title>
    <link rel="stylesheet" href="/publico/css/style.css">
</head>
<body>
    <?php
    $torneo = (object)($torneo ?? []);
    require_once __DIR__ . '/../parciales/cabecera.php';
    ?>

    <main class="contenedor-pantalla" style="max-width: 900px;">
        <div class="tarjeta-autenticacion" style="padding: 30px;">
            <a href="/torneos" style="color: #93c5fd; text-decoration:none; margin-bottom: 12px; display:inline-block;">← Volver a torneos</a>
            <h1 style="font-family: var(--fuente-titulo); font-size: 2rem; margin-bottom: 8px;"><?php echo htmlspecialchars($torneo->nombre); ?></h1>
            <p style="color: var(--color-texto-secundario); margin-bottom: 18px;">Detalle del torneo configurado en la fase 3.</p>

            <div style="display:grid; gap:12px; color: var(--color-texto-principal);">
                <p><strong>Formato:</strong> <?php echo htmlspecialchars($torneo->formato); ?></p>
                <p><strong>Estado:</strong> <?php echo htmlspecialchars($torneo->estado); ?></p>
                <p><strong>Modalidad:</strong> <?php echo $torneo->modalidadId === 1 ? 'Individual' : 'Equipos'; ?></p>
                <p><strong>Descripción:</strong> <?php echo htmlspecialchars($torneo->descripcion ?: 'Sin descripción.'); ?></p>
                <p><strong>Tipo de resultado:</strong> <?php echo htmlspecialchars($torneo->tipoResultado); ?></p>
                <p><strong>Mejor de:</strong> <?php echo (int)$torneo->mejorDe; ?> partidas/mapas</p>
                <p><strong>Puntuación:</strong> Victoria <?php echo $torneo->puntosVictoria; ?> · Empate <?php echo $torneo->puntosEmpate; ?> · Derrota <?php echo $torneo->puntosDerrota; ?></p>
                <p><strong>Cupo:</strong> <?php echo (int)$torneo->cupoMinEquipos; ?> mínimo / <?php echo (int)$torneo->cupoMaxEquipos; ?> máximo</p>
            </div>

            <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top: 18px;">
                <a href="/torneos/resultados?id=<?php echo (int)$torneo->id; ?>" class="boton-primario" style="display:inline-block; width:auto; text-decoration:none; padding:10px 14px;">Ver resultados</a>
                <a href="/torneos" class="boton-primario" style="display:inline-block; width:auto; text-decoration:none; padding:10px 14px;">Volver a torneos</a>
            </div>
        </div>
    </main>
</body>
</html>
