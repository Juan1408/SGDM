<?php
$escapar = static fn ($valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');

$partidos = $datos['calendario'] ?? [];

$diasSemana = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
$meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

/* Agrupa por día y calcula el resumen */
$dias = [];
$finalizados = 0;
$torneosConPartidos = [];

foreach ($partidos as $partido) {
    $clave = $partido['fecha'] ?: 'sin-fecha';
    $dias[$clave][] = $partido;

    if ($partido['resultado_local'] !== null && $partido['resultado_visitante'] !== null) {
        $finalizados++;
    }
    $torneosConPartidos[(int) $partido['torneo_id']] = $partido['torneo_nombre'];
}
asort($torneosConPartidos);

$totalPartidos = count($partidos);
$pendientes = $totalPartidos - $finalizados;

$urlCrear = URL_BASE . 'index.php?c=panelOrganizador&a=crear';
$urlTorneo = URL_BASE . 'index.php?c=panelOrganizador&a=administrar';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASCEND | Calendario general</title>
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/botones.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/tarjetas.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/organizador-layout.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/organizador/calendario.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <?php require_once __DIR__ . '/../componentes/navbar-organizador.php'; ?>

    <main class="dashboard-organizador calendario-pagina">

        <header class="calendario-header">
            <div>
                <span class="calendario-etiqueta">Agenda general</span>
                <h1>Calendario de enfrentamientos</h1>
                <p>Consultá los partidos programados de todos tus torneos desde un mismo lugar.</p>
            </div>

            <a class="calendario-crear-enlace" href="<?php echo $urlCrear; ?>">
                <i class="fa-solid fa-plus"></i>
                Crear torneo
            </a>
        </header>

        <section class="calendario-resumen">
            <article class="calendario-resumen-tarjeta">
                <span class="calendario-resumen-icono"><i class="fa-solid fa-calendar-check"></i></span>
                <div>
                    <strong><?php echo $totalPartidos; ?></strong>
                    <span>Total de partidos</span>
                </div>
            </article>

            <article class="calendario-resumen-tarjeta">
                <span class="calendario-resumen-icono"><i class="fa-solid fa-clock"></i></span>
                <div>
                    <strong><?php echo $pendientes; ?></strong>
                    <span>Pendientes</span>
                </div>
            </article>

            <article class="calendario-resumen-tarjeta">
                <span class="calendario-resumen-icono"><i class="fa-solid fa-flag-checkered"></i></span>
                <div>
                    <strong><?php echo $finalizados; ?></strong>
                    <span>Finalizados</span>
                </div>
            </article>

            <article class="calendario-resumen-tarjeta">
                <span class="calendario-resumen-icono"><i class="fa-solid fa-trophy"></i></span>
                <div>
                    <strong><?php echo count($torneosConPartidos); ?></strong>
                    <span>Torneos con partidos</span>
                </div>
            </article>
        </section>

        <section class="calendario-filtros">
            <div class="calendario-filtro calendario-filtro--buscar">
                <label for="buscarPartido">Buscar participante</label>
                <div class="calendario-input-icono">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input id="buscarPartido" type="search" placeholder="Equipo o jugador...">
                </div>
            </div>

            <div class="calendario-filtro">
                <label for="filtroTorneo">Torneo</label>
                <select id="filtroTorneo">
                    <option value="todos">Todos los torneos</option>
                    <?php foreach ($torneosConPartidos as $torneoId => $torneoNombre): ?>
                        <option value="<?php echo (int) $torneoId; ?>"><?php echo $escapar($torneoNombre); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="calendario-filtro">
                <label for="filtroEstadoPartido">Estado</label>
                <select id="filtroEstadoPartido">
                    <option value="todos">Todos</option>
                    <option value="pendiente">Pendientes</option>
                    <option value="finalizado">Finalizados</option>
                </select>
            </div>

            <div class="calendario-filtro">
                <label for="filtroFechaPartido">Fecha</label>
                <input id="filtroFechaPartido" type="date">
            </div>

            <button class="calendario-limpiar" id="limpiarFiltrosCalendario" type="button">
                <i class="fa-solid fa-rotate-left"></i>
                Limpiar
            </button>
        </section>

        <section class="calendario-contenido">
            <div class="calendario-contenido-header">
                <div>
                    <span>Agenda</span>
                    <h2>Partidos programados</h2>
                </div>
                <strong id="cantidadResultadosCalendario"><?php echo $totalPartidos; ?> <?php echo $totalPartidos === 1 ? 'enfrentamiento' : 'enfrentamientos'; ?></strong>
            </div>

            <div class="calendario-lista" id="calendarioLista">
                <?php foreach ($dias as $clave => $partidosDia):
                    if ($clave === 'sin-fecha') {
                        $cajaDia = '--';
                        $cajaMes = 'Sin fecha';
                        $fechaCompleta = 'Sin fecha';
                        $fechaIso = '';
                    } else {
                        $ts = strtotime($clave);
                        $cajaDia = date('d', $ts);
                        $cajaMes = mb_substr($meses[(int) date('n', $ts) - 1], 0, 3, 'UTF-8');
                        $fechaCompleta = $diasSemana[(int) date('w', $ts)] . ', ' . date('j', $ts) . ' de ' . $meses[(int) date('n', $ts) - 1] . ' de ' . date('Y', $ts);
                        $fechaIso = $clave;
                    }
                ?>
                    <section class="calendario-dia">
                        <div class="calendario-fecha">
                            <div class="calendario-fecha-caja">
                                <strong><?php echo $escapar($cajaDia); ?></strong>
                                <span><?php echo $escapar($cajaMes); ?></span>
                            </div>
                            <p><?php echo $escapar($fechaCompleta); ?></p>
                        </div>

                        <div class="calendario-dia-partidos">
                            <?php foreach ($partidosDia as $p):
                                $local = $p['local_nombre'] ?? 'Por definir';
                                $visitante = $p['visitante_nombre'] ?? 'Por definir';
                                $finalizado = $p['resultado_local'] !== null && $p['resultado_visitante'] !== null;
                                $hora = $p['hora'] ? substr((string) $p['hora'], 0, 5) : 'Sin hora';
                            ?>
                                <article class="calendario-partido"
                                         data-torneo="<?php echo (int) $p['torneo_id']; ?>"
                                         data-estado="<?php echo $finalizado ? 'finalizado' : 'pendiente'; ?>"
                                         data-fecha="<?php echo $escapar($fechaIso); ?>"
                                         data-busqueda="<?php echo $escapar(mb_strtolower($local . ' ' . $visitante, 'UTF-8')); ?>">

                                    <div class="calendario-partido-info">
                                        <div class="calendario-partido-etiquetas">
                                            <span class="calendario-torneo"><?php echo $escapar($p['torneo_nombre']); ?></span>
                                            <span class="calendario-ronda">Ronda <?php echo (int) $p['ronda']; ?></span>
                                        </div>

                                        <div class="calendario-enfrentamiento">
                                            <div class="calendario-equipo">
                                                <span class="calendario-equipo-icono"><i class="fa-solid fa-shield-halved"></i></span>
                                                <strong><?php echo $escapar($local); ?></strong>
                                            </div>

                                            <?php if ($finalizado): ?>
                                                <div class="calendario-marcador">
                                                    <strong><?php echo (int) $p['resultado_local']; ?></strong>
                                                    <span>-</span>
                                                    <strong><?php echo (int) $p['resultado_visitante']; ?></strong>
                                                </div>
                                            <?php else: ?>
                                                <div class="calendario-versus">VS</div>
                                            <?php endif; ?>

                                            <div class="calendario-equipo calendario-equipo--derecha">
                                                <strong><?php echo $escapar($visitante); ?></strong>
                                                <span class="calendario-equipo-icono"><i class="fa-solid fa-shield-halved"></i></span>
                                            </div>
                                        </div>

                                        <div class="calendario-partido-datos">
                                            <span><i class="fa-solid fa-clock"></i><?php echo $escapar($hora); ?></span>
                                            <span><i class="fa-solid fa-location-dot"></i><?php echo $escapar($p['sede'] ?: 'A definir'); ?></span>
                                            <?php if (!empty($p['juego_nombre'])): ?>
                                                <span><i class="fa-solid fa-gamepad"></i><?php echo $escapar($p['juego_nombre']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="calendario-partido-acciones">
                                        <span class="calendario-estado calendario-estado--<?php echo $finalizado ? 'finalizado' : 'pendiente'; ?>">
                                            <?php echo $finalizado ? 'Finalizado' : 'Pendiente'; ?>
                                        </span>

                                        <a href="<?php echo $urlTorneo; ?>&amp;seccion=<?php echo $finalizado ? 'resultados' : 'fixture'; ?>&amp;id=<?php echo (int) $p['torneo_id']; ?>">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                            Abrir torneo
                                        </a>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endforeach; ?>
            </div>

            <div class="calendario-vacio" id="calendarioVacio" <?php echo $partidos === [] ? '' : 'hidden'; ?>>
                <i class="fa-solid fa-calendar-xmark"></i>
                <h2>No hay enfrentamientos</h2>
                <p><?php echo $partidos === [] ? 'Todavía no hay partidos generados en tus torneos.' : 'No se encontraron partidos que coincidan con los filtros seleccionados.'; ?></p>
            </div>
        </section>

    </main>

    <script src="<?php echo URL_BASE; ?>js/organizador/calendario.js"></script>
    <?php include __DIR__ . '/../componentes/footer.php'; ?>
</body>
</html>
