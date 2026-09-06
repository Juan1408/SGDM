<?php
$eventos = $datos['calendario'] ?? [];
$escapar = static fn ($valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASCEND | Calendario</title>
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/botones.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/tarjetas.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/organizador-layout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .calendario-pagina { max-width: 1000px; }
        .calendario-lista { display: grid; gap: .75rem; }
        .calendario-item { display: grid; grid-template-columns: 110px 1fr; gap: 1rem; align-items: center; padding: 1rem; background: var(--fondo-tarjeta); border: 1px solid var(--borde-suave); border-radius: var(--radio-md); }
        .calendario-fecha { color: var(--color-secundario); font-weight: 700; }
        .calendario-item small { display: block; color: var(--texto-secundario); margin-top: .3rem; }
        .calendario-vacio { color: var(--texto-secundario); }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/navbar.html'; ?>
    <?php require_once __DIR__ . '/sidebar.html'; ?>
    <main class="dashboard-organizador calendario-pagina">
        <header class="dashboard-header"><div><p class="dashboard-tag">AGENDA DEL ORGANIZADOR</p><h1>Calendario</h1><p>Actividades y partidos de tus torneos.</p></div></header>
        <section class="dashboard-panel">
            <h2>Próximos eventos</h2>
            <?php if ($eventos === []): ?><p class="calendario-vacio">No hay actividades ni partidos programados.</p><?php else: ?>
                <div class="calendario-lista">
                    <?php foreach ($eventos as $evento): ?>
                        <article class="calendario-item"><time class="calendario-fecha"><?php echo $escapar($evento['fecha'] ?: 'Sin fecha'); ?><?php if ($evento['hora']): ?><small><?php echo $escapar($evento['hora']); ?></small><?php endif; ?></time><div><strong><?php echo $escapar($evento['titulo']); ?></strong><small><?php echo $escapar($evento['torneo_nombre']); ?> · <?php echo $escapar(ucfirst($evento['tipo'])); ?></small></div></article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
    <script src="<?php echo URL_BASE; ?>js/organizador/includes.js"></script><script src="<?php echo URL_BASE; ?>js/organizador/sidebar.js"></script>
</body>
</html>
