<?php /** @var array $error */ ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Error - Ascend</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Estilo base global para las variables de tema (Claro/Oscuro) -->
    <link rel="stylesheet" href="http://localhost/SGDM/codigo_fuente/publico/css/admin/base.css">
    <!-- Estilo específico -->
    <link rel="stylesheet" href="http://localhost/SGDM/codigo_fuente/publico/css/admin/detalle_error.css">
</head>
<body class="tema-admin">

    <!-- Anti-FOUC: Aplicar Dark Mode si está activo en el localStorage antes de renderizar -->
    <script>
        if (localStorage.getItem('admin_tema') === 'dark') {
            document.body.classList.add('dark-mode');
        }
    </script>
    
    <div class="container">
    <a href="index.php?c=errorTracking&a=dashboard" class="btn-back">← Volver al Dashboard</a>
    
    <div class="header">
        <h1><?= htmlspecialchars($error['mensaje']) ?></h1>
        <p>En <strong><?= htmlspecialchars($error['archivo']) ?></strong> (Línea <?= $error['linea'] ?>)</p>
    </div>

    <div class="section">
        <h3 class="section-title">Información General</h3>
        <div class="grid-info">
            <div class="info-box">
                <p>Ocurrencias Totales</p>
                <strong><?= $error['cantidad_ocurrencias'] ?> veces</strong>
            </div>
            <div class="info-box">
                <p>Primera Detección</p>
                <strong><?= date('d/m/Y H:i:s', strtotime($error['primera_vez'])) ?></strong>
            </div>
            <div class="info-box">
                <p>Última Detección</p>
                <strong><?= date('d/m/Y H:i:s', strtotime($error['ultima_vez'])) ?></strong>
            </div>
            <div class="info-box">
                <p>Afectado</p>
                <strong><?= $error['usuario_email'] ? htmlspecialchars($error['usuario_email']) : 'Usuario No Logueado' ?> (<?= $error['rol_usuario'] ?? 'N/A' ?>)</strong>
            </div>
        </div>
    </div>

    <?php if (!empty($error['detalles_resolucion'])): ?>
    <div class="section" style="border-left: 4px solid var(--color-exito);">
        <h3 class="section-title">Resolución Implementada</h3>
        <div style="padding: 1.5rem; color: var(--color-texto-principal); font-size: 0.95rem; line-height: 1.6;">
            <?= nl2br(htmlspecialchars($error['detalles_resolucion'])) ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="section">
        <h3 class="section-title">Contexto (Estado en el momento del fallo)</h3>
        <?php 
            $contexto = json_decode($error['contexto_json'], true);
            $contextoFormateado = json_encode($contexto, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        ?>
        <pre class="code-block"><?= htmlspecialchars($contextoFormateado) ?></pre>
    </div>

    <div class="section">
        <h3 class="section-title">Stack Trace (Traza de ejecución)</h3>
        <pre class="code-block"><?= htmlspecialchars($error['stack_trace']) ?></pre>
    </div>

</div>

</body>
</html>
