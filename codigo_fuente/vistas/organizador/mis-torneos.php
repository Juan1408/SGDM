<?php
$torneos = $datos['torneos'] ?? [];
$estados = [
    'borrador' => ['texto' => 'Borrador', 'clase' => 'estado'],
    'inscripciones_abiertas' => ['texto' => 'Inscripciones abiertas', 'clase' => 'estado abierta'],
    'en_curso' => ['texto' => 'En curso', 'clase' => 'estado curso'],
    'finalizado' => ['texto' => 'Finalizado', 'clase' => 'estado finalizado'],
    'cancelado' => ['texto' => 'Cancelado', 'clase' => 'estado finalizado'],
];
$escapar = static fn ($valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASCEND | Mis torneos</title>
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/botones.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/tarjetas.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/organizador-layout.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/organizador/mis-torneos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .mis-torneos-pagina { max-width: 1100px; }
        .mis-torneos-toolbar { display: flex; gap: .75rem; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-bottom: 1.25rem; }
        .mis-torneos-filtros { display: flex; gap: .5rem; flex-wrap: wrap; }
        .mis-torneos-filtros a { padding: .55rem .8rem; border: 1px solid var(--borde-suave); border-radius: var(--radio-md); color: var(--texto-secundario); text-decoration: none; }
        .mis-torneos-filtros a:hover { color: var(--color-secundario); border-color: var(--color-secundario); }
        .mis-torneos-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem; }
        .mi-torneo-card { display: flex; flex-direction: column; gap: .85rem; padding: 1.1rem; background: var(--fondo-tarjeta); border: 1px solid var(--borde-suave); border-radius: var(--radio-lg); }
        .mi-torneo-card header { display: flex; justify-content: space-between; align-items: flex-start; gap: .75rem; }
        .mi-torneo-card h2 { font-size: 1rem; margin: 0; }
        .mi-torneo-card p { margin: 0; color: var(--texto-secundario); font-size: .85rem; }
        .mi-torneo-meta { display: grid; grid-template-columns: 1fr 1fr; gap: .6rem; margin-top: auto; }
        .mi-torneo-meta span { display: block; padding: .6rem; background: rgba(255,255,255,.03); border-radius: var(--radio-md); font-size: .78rem; color: var(--texto-secundario); }
        .mi-torneo-meta strong { display: block; color: #fff; font-size: .9rem; }
        .mis-torneos-vacio { padding: 2rem; text-align: center; color: var(--texto-secundario); }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/navbar.html'; ?>
    <?php require_once __DIR__ . '/sidebar.html'; ?>
    <main class="dashboard-organizador mis-torneos-pagina">
        <header class="dashboard-header">
            <div>
                <p class="dashboard-tag">GESTION DE TORNEOS</p>
                <h1>Mis torneos</h1>
            </div>
        </header>
        <?php if (!empty($datos['mensaje'])): ?><p class="crear-torneo-notificacion error"><?php echo $escapar($datos['mensaje']); ?></p><?php endif; ?>
        <section class="dashboard-panel">
            <div class="mis-torneos-toolbar">
                <h2>Todos los torneos creados</h2>
                <a class="boton boton-gestion" href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&amp;a=crear"><i class="fa-solid fa-plus"></i> Crear torneo</a>
            </div>
            <nav class="mis-torneos-filtros" aria-label="Filtrar torneos">
                <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&amp;a=misTorneos">Todos</a>
                <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&amp;a=misTorneos&amp;estado=borrador">Borradores</a>
                <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&amp;a=misTorneos&amp;estado=inscripciones_abiertas">Abiertos</a>
                <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&amp;a=misTorneos&amp;estado=en_curso">En curso</a>
                <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&amp;a=misTorneos&amp;estado=finalizado">Finalizados</a>
            </nav>
            <?php if ($torneos === []): ?>
                <p class="mis-torneos-vacio">No hay torneos para mostrar.</p>
            <?php else: ?>
                <div class="mis-torneos-grid">
                    <?php foreach ($torneos as $torneo): ?>
                        <?php $estado = $estados[$torneo['estado']] ?? ['texto' => $torneo['estado'], 'clase' => 'estado']; ?>
                        <?php $ruta = $torneo['estado'] === 'borrador'
                            ? URL_BASE . 'index.php?c=panelOrganizador&amp;a=crear&amp;etapa=2&amp;id=' . (int) $torneo['id']
                            : URL_BASE . 'index.php?c=panelOrganizador&amp;a=gestionar&amp;id=' . (int) $torneo['id']; ?>
                        <article class="mi-torneo-card">
                            <header><h2><?php echo $escapar($torneo['nombre']); ?></h2><span class="<?php echo $estado['clase']; ?>"><?php echo $escapar($estado['texto']); ?></span></header>
                            <p><i class="fa-solid fa-gamepad"></i> <?php echo $escapar($torneo['nombre_juego']); ?> · <?php echo $escapar(ucwords(str_replace('_', ' ', $torneo['formato']))); ?></p>
                            <div class="mi-torneo-meta"><span>Equipos<strong><?php echo (int) $torneo['equipos_confirmados']; ?>/<?php echo (int) $torneo['cupo_max_equipos']; ?></strong></span><span>Inicio<strong><?php echo $torneo['fecha_inicio'] ? $escapar(date('d/m/Y', strtotime($torneo['fecha_inicio']))) : 'Sin fecha'; ?></strong></span></div>
                            <a class="boton boton-gestion" href="<?php echo $ruta; ?>">Gestionar <i class="fa-solid fa-arrow-right"></i></a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
    <script src="<?php echo URL_BASE; ?>js/organizador/includes.js"></script>
    <script src="<?php echo URL_BASE; ?>js/organizador/sidebar.js"></script>
</body>
</html>
