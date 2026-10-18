<?php
$escapar = static fn ($valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');

/* Solo los partidos que ya tienen marcador cargado */
$resultados = array_values(array_filter(
    $datos['resultados'] ?? [],
    static fn (array $r): bool => $r['resultado_local'] !== null && $r['resultado_visitante'] !== null
));

/* Resumen */
$limiteSemana = strtotime('-7 days');
$totalResultados = count($resultados);
$estaSemana = 0;
$torneosConResultados = [];

foreach ($resultados as $r) {
    if (strtotime((string) $r['ultima_modificacion']) >= $limiteSemana) {
        $estaSemana++;
    }
    $torneosConResultados[(int) $r['torneo_id']] = $r['torneo_nombre'];
}
asort($torneosConResultados);

/* Ajustá esta ruta a la acción que abre la gestión de un torneo */
$urlVerTorneo = URL_BASE . 'index.php?c=panelOrganizador&a=administrar&seccion=resultados&id=';
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
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/organizador/resultados.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <?php require_once __DIR__ . '/../componentes/navbar-organizador.php'; ?>

    <main class="dashboard-organizador resultados-pagina">

        <header class="resultados-header">
            <div>
                <span class="resultados-etiqueta">Agenda general</span>
                <h1>Resultados</h1>
                <p>Últimos resultados cargados en todos tus torneos, en un mismo lugar.</p>
            </div>
        </header>

        <dl class="resultados-resumen">
            <div class="resultados-resumen-tarjeta">
                <span class="resultados-resumen-icono"><i class="fa-solid fa-clipboard-check"></i></span>
                <div>
                    <dd><?php echo $totalResultados; ?></dd>
                    <dt>Resultados cargados</dt>
                </div>
            </div>

            <div class="resultados-resumen-tarjeta">
                <span class="resultados-resumen-icono"><i class="fa-solid fa-calendar-week"></i></span>
                <div>
                    <dd><?php echo $estaSemana; ?></dd>
                    <dt>Esta semana</dt>
                </div>
            </div>

            <div class="resultados-resumen-tarjeta">
                <span class="resultados-resumen-icono"><i class="fa-solid fa-trophy"></i></span>
                <div>
                    <dd><?php echo count($torneosConResultados); ?></dd>
                    <dt>Torneos con resultados</dt>
                </div>
            </div>
        </dl>

        <section class="resultados-filtros">
            <div class="resultados-filtro resultados-filtro--buscar">
                <label for="buscarResultado">Buscar participante</label>
                <div class="resultados-input-icono">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input id="buscarResultado" type="search" placeholder="Equipo o jugador...">
                </div>
            </div>

            <div class="resultados-filtro">
                <label for="filtroTorneoResultados">Torneo</label>
                <select id="filtroTorneoResultados">
                    <option value="todos">Todos los torneos</option>
                    <?php foreach ($torneosConResultados as $torneoId => $torneoNombre): ?>
                        <option value="<?php echo (int) $torneoId; ?>"><?php echo $escapar($torneoNombre); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="resultados-filtro">
                <label for="filtroFechaResultados">Fecha</label>
                <input id="filtroFechaResultados" type="date">
            </div>

            <button class="resultados-limpiar" id="limpiarFiltrosResultados" type="button">
                <i class="fa-solid fa-rotate-left"></i>
                Limpiar
            </button>
        </section>

        <section class="resultados-contenido">
            <div class="resultados-contenido-header">
                <div>
                    <span>Agenda</span>
                    <h2>Últimos resultados</h2>
                </div>
                <strong id="cantidadResultados"><?php echo $totalResultados; ?> resultado<?php echo $totalResultados === 1 ? '' : 's'; ?></strong>
            </div>

            <div class="resultados-lista" id="resultadosLista">
                <?php foreach ($resultados as $r):
                    $local = $r['local_nombre'] ?? '—';
                    $visitante = $r['visitante_nombre'] ?? '—';
                    $golesLocal = (int) $r['resultado_local'];
                    $golesVisitante = (int) $r['resultado_visitante'];
                    $timestamp = strtotime((string) $r['ultima_modificacion']);
                ?>
                    <article class="resultado-item"
                             data-torneo="<?php echo (int) $r['torneo_id']; ?>"
                             data-fecha="<?php echo date('Y-m-d', $timestamp); ?>"
                             data-busqueda="<?php echo $escapar(mb_strtolower($local . ' ' . $visitante, 'UTF-8')); ?>">
                        <div class="resultado-info">
                            <div class="resultado-etiquetas">
                                <span class="resultado-torneo"><?php echo $escapar($r['torneo_nombre']); ?></span>
                                <span class="resultado-fecha">Ronda <?php echo (int) $r['ronda']; ?> · <?php echo date('d/m/Y', $timestamp); ?></span>
                            </div>

                            <div class="resultado-enfrentamiento">
                                <div class="resultado-equipo<?php echo $golesLocal > $golesVisitante ? ' resultado-equipo--ganador' : ''; ?>">
                                    <strong><?php echo $escapar($local); ?></strong>
                                </div>

                                <div class="resultado-marcador">
                                    <span><?php echo $golesLocal; ?></span>
                                    <span>-</span>
                                    <span><?php echo $golesVisitante; ?></span>
                                </div>

                                <div class="resultado-equipo resultado-equipo--derecha<?php echo $golesVisitante > $golesLocal ? ' resultado-equipo--ganador' : ''; ?>">
                                    <strong><?php echo $escapar($visitante); ?></strong>
                                </div>
                            </div>
                        </div>

                        <div class="resultado-acciones">
                            <a href="<?php echo $urlVerTorneo . (int) $r['torneo_id']; ?>">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                Ver torneo
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="resultados-vacio" id="resultadosVacio" <?php echo $resultados === [] ? '' : 'hidden'; ?>>
                <i class="fa-solid fa-clipboard-question"></i>
                <h2>Sin resultados</h2>
                <p><?php echo $resultados === [] ? 'Todavía no cargaste resultados en tus torneos.' : 'No hay resultados cargados con esos filtros.'; ?></p>
            </div>
        </section>

    </main>

    <script src="<?php echo URL_BASE; ?>js/organizador/resultados.js"></script>
    <?php include __DIR__ . '/../componentes/footer.html'; ?>
</body>
</html>
