<?php
$resultados = $datos['resultados'] ?? [];
$escapar = static fn ($valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASCEND | Resultados</title>
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/botones.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/tarjetas.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/organizador-layout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .resultados-pagina { max-width: 1100px; }
        .resultados-lista { display: grid; gap: .7rem; }
        .resultado-item { display: flex; justify-content: space-between; align-items: center; gap: 1rem; padding: 1rem; background: var(--fondo-tarjeta); border: 1px solid var(--borde-suave); border-radius: var(--radio-md); }
        .resultado-item small { display: block; color: var(--texto-secundario); margin-bottom: .35rem; }
        .resultado-marcador { font-size: 1.1rem; font-weight: 700; white-space: nowrap; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/navbar.html'; ?>
    <?php require_once __DIR__ . '/sidebar.html'; ?>
    <main class="dashboard-organizador resultados-pagina">
        <header class="dashboard-header"><div><p class="dashboard-tag">COMPETENCIA</p><h1>Resultados</h1><p>Partidos de tus torneos y resultados cargados.</p></div></header>
        <section class="dashboard-panel">
            <h2>Partidos</h2>
            <?php if ($resultados === []): ?><p>No hay partidos registrados todavía.</p><?php else: ?>
                <div class="resultados-lista">
                    <?php foreach ($resultados as $resultado): ?>
                        <article class="resultado-item"><div><small><?php echo $escapar($resultado['torneo_nombre']); ?> · Ronda <?php echo (int) $resultado['ronda']; ?></small><?php echo $escapar($resultado['local_nombre'] ?? 'Pendiente'); ?> vs <?php echo $escapar($resultado['visitante_nombre'] ?? 'Pendiente'); ?></div><div class="resultado-marcador"><?php echo $resultado['resultado_local'] !== null ? $escapar($resultado['resultado_local'] . ' - ' . $resultado['resultado_visitante']) : 'Pendiente'; ?></div><span><?php echo $escapar($resultado['estado']); ?></span></article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
    <script src="<?php echo URL_BASE; ?>js/organizador/includes.js"></script><script src="<?php echo URL_BASE; ?>js/organizador/sidebar.js"></script>
</body>
</html>
