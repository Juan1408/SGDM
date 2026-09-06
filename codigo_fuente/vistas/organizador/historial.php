<?php
$historial = $datos['historial'] ?? [];
$escapar = static fn ($valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASCEND | Historial</title>
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/botones.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/tarjetas.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/organizador-layout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .historial-pagina { max-width: 1100px; }
        .historial-lista { display: grid; gap: .7rem; }
        .historial-item { display: flex; justify-content: space-between; gap: 1rem; padding: 1rem; background: var(--fondo-tarjeta); border: 1px solid var(--borde-suave); border-radius: var(--radio-md); }
        .historial-item small { display: block; color: var(--texto-secundario); margin-bottom: .35rem; }
        .historial-fecha { color: var(--texto-secundario); white-space: nowrap; font-size: .8rem; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/navbar.html'; ?>
    <?php require_once __DIR__ . '/sidebar.html'; ?>
    <main class="dashboard-organizador historial-pagina">
        <header class="dashboard-header"><div><p class="dashboard-tag">REGISTRO DE ACTIVIDAD</p><h1>Historial</h1><p>Cambios de estado y resultados registrados en tus torneos.</p></div></header>
        <section class="dashboard-panel">
            <h2>Actividad reciente</h2>
            <?php if ($historial === []): ?><p>No hay actividad registrada todavía.</p><?php else: ?>
                <div class="historial-lista">
                    <?php foreach ($historial as $item): ?>
                        <article class="historial-item"><div><small><?php echo $escapar($item['torneo_nombre']); ?> · <?php echo $item['tipo'] === 'estado' ? 'Cambio de estado' : 'Resultado'; ?></small><?php echo $escapar($item['detalle']); ?></div><time class="historial-fecha"><?php echo $escapar($item['fecha']); ?></time></article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
    <script src="<?php echo URL_BASE; ?>js/organizador/includes.js"></script><script src="<?php echo URL_BASE; ?>js/organizador/sidebar.js"></script>
</body>
</html>
