<?php
$torneo = $datos['torneo'];
$estados = [
    'borrador' => 'Borrador',
    'inscripciones_abiertas' => 'Inscripciones abiertas',
    'en_curso' => 'En curso',
    'finalizado' => 'Finalizado',
    'cancelado' => 'Cancelado',
];
$escapar = static fn ($valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASCEND | Gestionar torneo</title>
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/botones.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/tarjetas.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/organizador-layout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .gestion-torneo { max-width: 880px; }
        .gestion-resumen { display: grid; gap: .75rem; }
        .gestion-resumen div { display: flex; justify-content: space-between; gap: 1rem; padding: .8rem 0; border-bottom: 1px solid var(--borde-suave); }
        .gestion-resumen dt { color: var(--texto-secundario); }
        .gestion-resumen dd { margin: 0; text-align: right; }
        .gestion-acciones { display: flex; gap: .75rem; flex-wrap: wrap; margin-top: 1.5rem; }
        .gestion-formulario { display: grid; gap: 1rem; margin-top: 1.5rem; }
        .gestion-formulario label { display: grid; gap: .35rem; color: var(--texto-secundario); font-size: .85rem; }
        .gestion-formulario input, .gestion-formulario textarea, .gestion-formulario select { width: 100%; box-sizing: border-box; padding: .7rem; border: 1px solid var(--borde-suave); border-radius: var(--radio-md); background: rgba(255,255,255,.04); color: #fff; font: inherit; }
        .gestion-formulario textarea { min-height: 100px; resize: vertical; }
        .gestion-estado { display: flex; align-items: end; gap: .75rem; flex-wrap: wrap; margin-top: 1.5rem; }
        .gestion-estado label { display: grid; gap: .35rem; color: var(--texto-secundario); font-size: .85rem; }
        .inscripciones { margin-top: 1.5rem; }
        .inscripcion { display: flex; justify-content: space-between; align-items: center; gap: 1rem; padding: .85rem 0; border-bottom: 1px solid var(--borde-suave); }
        .inscripcion-datos { min-width: 0; }
        .inscripcion-datos strong, .inscripcion-datos small { display: block; }
        .inscripcion-datos small { color: var(--texto-secundario); margin-top: .25rem; }
        .inscripcion-acciones { display: flex; gap: .5rem; flex-shrink: 0; }
        .inscripcion-acciones button { border: 0; cursor: pointer; }
        .inscripcion-acciones .rechazar { background: rgba(255, 76, 76, .15); color: #ff8080; }
        .cupos-resumen { margin: 1rem 0; padding: .85rem; border: 1px solid var(--borde-suave); border-radius: var(--radio-md); color: var(--texto-secundario); }
        .cupos-resumen strong { color: #fff; }
        .fixture { margin-top: 1.5rem; }
        .fixture-partido { display: flex; justify-content: space-between; gap: 1rem; padding: .75rem 0; border-bottom: 1px solid var(--borde-suave); }
        .fixture-partido small { display: block; color: var(--texto-secundario); }
        .resultado-form { display: inline-flex; gap: .35rem; align-items: center; margin-top: .4rem; }
        .resultado-form input { width: 4rem; padding: .35rem; }
        .posiciones { margin-top: 1.5rem; overflow-x: auto; }
        .posiciones table { width: 100%; border-collapse: collapse; min-width: 620px; }
        .posiciones th, .posiciones td { padding: .65rem; border-bottom: 1px solid var(--borde-suave); text-align: right; }
        .posiciones th:first-child, .posiciones td:first-child { text-align: left; }
        .posiciones th { color: var(--texto-secundario); font-size: .75rem; }
        .podio { margin-top: 1.5rem; }
        .podio-lista { display: grid; gap: .6rem; padding: 0; list-style: none; }
        .podio-lista li { padding: .8rem; border: 1px solid var(--borde-suave); border-radius: var(--radio-md); }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/navbar.html'; ?>
    <?php require_once __DIR__ . '/sidebar.html'; ?>
    <main class="dashboard-organizador gestion-torneo">
        <header class="dashboard-header">
            <div>
                <p class="dashboard-tag">GESTION DE TORNEOS</p>
                <h1><?php echo $escapar($torneo['nombre']); ?></h1>
            </div>
        </header>
        <section class="dashboard-panel">
            <h2>Resumen</h2>
            <?php if (!empty($datos['mensaje'])): ?><p class="crear-torneo-notificacion success"><?php echo $escapar($datos['mensaje']); ?></p><?php endif; ?>
            <dl class="gestion-resumen">
                <div><dt>Juego</dt><dd><?php echo $escapar($torneo['nombre_juego']); ?></dd></div>
                <div><dt>Formato</dt><dd><?php echo $escapar(ucwords(str_replace('_', ' ', $torneo['formato']))); ?></dd></div>
                <div><dt>Estado</dt><dd><?php echo $escapar($estados[$torneo['estado']] ?? $torneo['estado']); ?></dd></div>
                <div><dt>Cupos</dt><dd><?php echo (int) $torneo['cupo_max_equipos']; ?></dd></div>
                <div><dt>Inscripciones</dt><dd><?php echo $escapar($torneo['fecha_inicio_inscripcion']); ?> a <?php echo $escapar($torneo['fecha_limite_inscripcion']); ?></dd></div>
                <div><dt>Competencia</dt><dd><?php echo $escapar($torneo['fecha_inicio']); ?> a <?php echo $escapar($torneo['fecha_fin']); ?></dd></div>
            </dl>
            <?php if (!in_array($torneo['estado'], ['en_curso', 'finalizado', 'cancelado'], true)): ?>
                <form class="gestion-formulario" method="post" action="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&amp;a=guardarGestion">
                    <input type="hidden" name="torneo_id" value="<?php echo (int) $torneo['id']; ?>">
                    <label>Comunidad u organización<input name="comunidad" value="<?php echo $escapar($torneo['comunidad']); ?>" maxlength="100"></label>
                    <label>Premios<textarea name="premios" maxlength="2000"><?php echo $escapar($torneo['premios']); ?></textarea></label>
                    <label>Descripción<textarea name="descripcion" maxlength="2000"><?php echo $escapar($torneo['descripcion']); ?></textarea></label>
                    <label>Reglamento<textarea name="reglas" maxlength="5000"><?php echo $escapar($torneo['reglas']); ?></textarea></label>
                    <label>URL del banner<input type="url" name="banner_url" value="<?php echo $escapar($torneo['banner_url']); ?>" maxlength="1000"></label>
                    <label>Enlace de transmisión<input type="url" name="stream_url" value="<?php echo $escapar($torneo['stream_url']); ?>" maxlength="255"></label>
                    <label>Inicio de inscripciones<input type="date" name="fecha_inicio_inscripcion" value="<?php echo $escapar($torneo['fecha_inicio_inscripcion']); ?>" required></label>
                    <label>Cierre de inscripciones<input type="date" name="fecha_limite_inscripcion" value="<?php echo $escapar($torneo['fecha_limite_inscripcion']); ?>" required></label>
                    <label>Inicio del torneo<input type="date" name="fecha_inicio" value="<?php echo $escapar($torneo['fecha_inicio']); ?>" required></label>
                    <label>Fin del torneo<input type="date" name="fecha_fin" value="<?php echo $escapar($torneo['fecha_fin']); ?>" required></label>
                    <button class="boton boton-gestion" type="submit"><i class="fa-solid fa-floppy-disk"></i> Guardar configuración</button>
                </form>
            <?php endif; ?>
            <form class="gestion-estado" id="formulario-estado" method="post" action="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&amp;a=cambiarEstado">
                <input type="hidden" name="torneo_id" value="<?php echo (int) $torneo['id']; ?>">
                <label>Cambiar estado
                    <select name="estado" required>
                        <?php if ($torneo['estado'] === 'borrador'): ?><option value="inscripciones_abiertas">Abrir inscripciones</option><?php endif; ?>
                        <?php if ($torneo['estado'] === 'inscripciones_abiertas'): ?><option value="en_curso">Iniciar torneo</option><?php endif; ?>
                        <?php if ($torneo['estado'] === 'en_curso'): ?><option value="finalizado">Finalizar torneo</option><?php endif; ?>
                        <?php if (in_array($torneo['estado'], ['borrador', 'inscripciones_abiertas'], true)): ?><option value="cancelado">Cancelar torneo</option><?php endif; ?>
                    </select>
                </label>
                <?php if ($torneo['estado'] !== 'finalizado' && $torneo['estado'] !== 'cancelado'): ?><button class="boton boton-gestion" type="submit">Aplicar estado</button><?php endif; ?>
            </form>
            <section class="inscripciones">
                <h2>Solicitudes de inscripción</h2>
                <?php $inscripciones = $datos['inscripciones'] ?? []; ?>
                <?php $cuposOcupados = (int) ($datos['cuposOcupados'] ?? 0); ?>
                <p class="cupos-resumen">Cupos ocupados: <strong><?php echo $cuposOcupados; ?> / <?php echo (int) $torneo['cupo_max_equipos']; ?></strong>. Disponibles: <strong><?php echo max(0, (int) $torneo['cupo_max_equipos'] - $cuposOcupados); ?></strong></p>
                <?php if ($inscripciones === []): ?>
                    <p>No hay solicitudes para este torneo.</p>
                <?php else: ?>
                    <?php foreach ($inscripciones as $inscripcion): ?>
                        <article class="inscripcion">
                            <div class="inscripcion-datos">
                                <strong><?php echo $escapar($inscripcion['nombre']); ?></strong>
                                <small><?php echo $escapar(ucfirst($inscripcion['tipo'])); ?> · <?php echo $escapar($inscripcion['estado']); ?></small>
                            </div>
                            <?php if ($inscripcion['estado'] === 'pendiente' && $torneo['estado'] === 'inscripciones_abiertas'): ?>
                                <div class="inscripcion-acciones">
                                    <form method="post" action="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&amp;a=responderInscripcion">
                                        <input type="hidden" name="inscripcion_id" value="<?php echo (int) $inscripcion['id']; ?>">
                                        <input type="hidden" name="torneo_id" value="<?php echo (int) $torneo['id']; ?>">
                                        <input type="hidden" name="respuesta" value="confirmado">
                                        <button class="boton boton-gestion" type="submit">Aprobar</button>
                                    </form>
                                    <form method="post" action="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&amp;a=responderInscripcion">
                                        <input type="hidden" name="inscripcion_id" value="<?php echo (int) $inscripcion['id']; ?>">
                                        <input type="hidden" name="torneo_id" value="<?php echo (int) $torneo['id']; ?>">
                                        <input type="hidden" name="respuesta" value="rechazado">
                                        <button class="boton rechazar" type="submit">Rechazar</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
            <section class="fixture">
                <h2>Fixture</h2>
                <?php $fixture = $datos['fixture'] ?? []; ?>
                <?php if ($torneo['estado'] === 'inscripciones_abiertas' || $torneo['estado'] === 'en_curso'): ?>
                    <form method="post" action="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&amp;a=generarFixture">
                        <input type="hidden" name="torneo_id" value="<?php echo (int) $torneo['id']; ?>">
                        <button class="boton boton-gestion" type="submit"><i class="fa-solid fa-diagram-project"></i> <?php echo $fixture === [] ? 'Generar fixture' : 'Regenerar fixture'; ?></button>
                    </form>
                <?php endif; ?>
                <?php if ($fixture === []): ?>
                    <p>Aún no hay partidos generados.</p>
                <?php else: ?>
                    <?php foreach ($fixture as $partido): ?>
                        <div class="fixture-partido">
                            <div><small>Ronda <?php echo (int) $partido['ronda']; ?></small><?php echo $escapar($partido['local_nombre'] ?? 'Pendiente'); ?> <?php echo $partido['resultado_local'] !== null ? $escapar($partido['resultado_local']) : ''; ?> vs <?php echo $partido['resultado_visitante'] !== null ? $escapar($partido['resultado_visitante']) : ''; ?> <?php echo $escapar($partido['visitante_nombre'] ?? 'Pendiente'); ?>
                                <?php if ($partido['estado'] !== 'finalizado' && $torneo['estado'] === 'en_curso'): ?>
                                    <form class="resultado-form" method="post" action="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&amp;a=guardarResultado">
                                        <input type="hidden" name="encuentro_id" value="<?php echo (int) $partido['id']; ?>"><input type="hidden" name="torneo_id" value="<?php echo (int) $torneo['id']; ?>">
                                        <input name="resultado_local" type="number" min="0" step="0.01" required aria-label="Resultado local"><span>-</span><input name="resultado_visitante" type="number" min="0" step="0.01" required aria-label="Resultado visitante"><button class="boton boton-gestion" type="submit">Guardar</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                            <span><?php echo $escapar($partido['estado']); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
            <?php if ($torneo['formato'] === 'liga'): ?>
                <section class="posiciones">
                    <h2>Tabla de posiciones</h2>
                    <?php $posiciones = $datos['posiciones'] ?? []; ?>
                    <?php if ($posiciones === []): ?>
                        <p>Aún no hay resultados para calcular posiciones.</p>
                    <?php else: ?>
                        <table>
                            <thead><tr><th>Participante</th><th>JJ</th><th>G</th><th>E</th><th>P</th><th>Favor</th><th>Contra</th><th>Pts</th></tr></thead>
                            <tbody>
                                <?php foreach ($posiciones as $posicion): ?>
                                    <tr><td><?php echo $escapar($posicion['nombre']); ?></td><td><?php echo (int) $posicion['partidos_jugados']; ?></td><td><?php echo (int) $posicion['partidos_ganados']; ?></td><td><?php echo (int) $posicion['partidos_empatados']; ?></td><td><?php echo (int) $posicion['partidos_perdidos']; ?></td><td><?php echo $escapar($posicion['puntos_favor']); ?></td><td><?php echo $escapar($posicion['puntos_contra']); ?></td><td><strong><?php echo $escapar($posicion['puntos']); ?></strong></td></tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
            <?php if (!empty($datos['podio'])): ?>
                <section class="podio">
                    <h2>Podio</h2>
                    <ol class="podio-lista">
                        <?php if (!empty($datos['podio']['primero'])): ?><li><strong>1.º lugar:</strong> <?php echo $escapar($datos['podio']['primero']); ?></li><?php endif; ?>
                        <?php if (!empty($datos['podio']['segundo'])): ?><li><strong>2.º lugar:</strong> <?php echo $escapar($datos['podio']['segundo']); ?></li><?php endif; ?>
                        <?php if (!empty($datos['podio']['tercero'])): ?><li><strong>3.º lugar:</strong> <?php echo $escapar($datos['podio']['tercero']); ?></li><?php endif; ?>
                    </ol>
                </section>
            <?php endif; ?>
            <div class="gestion-acciones">
                <a class="boton" href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&amp;a=dashboard"><i class="fa-solid fa-arrow-left"></i> Volver</a>
                <span class="boton">Inscripciones</span>
                <span class="boton">Resultados</span>
            </div>
        </section>
    </main>
    <script src="<?php echo URL_BASE; ?>js/organizador/includes.js"></script>
    <script src="<?php echo URL_BASE; ?>js/organizador/sidebar.js"></script>
    <script>
        document.getElementById('formulario-estado')?.addEventListener('submit', (evento) => {
            const estado = evento.currentTarget.querySelector('[name="estado"]')?.value;
            if (estado === 'finalizado' && !window.confirm('¿Confirmas finalizar este torneo? Esta acción no se puede deshacer.')) {
                evento.preventDefault();
            }
        });
    </script>
</body>
</html>
