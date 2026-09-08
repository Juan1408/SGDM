<?php
$etapa = (int) ($datos['etapa'] ?? 1);
$torneo = $datos['torneo'] ?? null;
$juegos = $datos['juegos'] ?? [];
$modalidades = $datos['modalidades'] ?? [];
$sistemasPuntuacion = $datos['sistemasPuntuacion'] ?? [];
$valor = static function (string $campo, string $defecto = '') use ($torneo): string {
    return htmlspecialchars((string) ($torneo[$campo] ?? $defecto), ENT_QUOTES, 'UTF-8');
};
$etiquetasEstado = [
    'liga' => 'Liga',
    'suizo' => 'Sistema suizo',
    'eliminacion_directa' => 'Eliminacion directa',
];
$nombresPuntuacion = [
    'estandar_3_1_0' => 'Clasico: 3-1-0',
    'ajedrez_1_0.5_0' => 'Ajedrez: 1-0,5-0',
    'esports_mapas_2_1_0' => 'eSports por mapas: 2-1-0',
];
$formatoAyuda = [
    'liga' => 'Todos juegan contra todos y se suman puntos.',
    'suizo' => 'Los emparejamientos se organizan por rondas.',
    'eliminacion_directa' => 'El perdedor queda eliminado y el ganador avanza.',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASCEND | Crear torneo</title>
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/botones.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/tarjetas.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/chips.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/organizador-layout.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/organizador/crear-torneo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .etapas-torneo { display: flex; gap: .5rem; flex-wrap: wrap; margin: 1.5rem 0; }
        .etapa-torneo { padding: .65rem 1rem; border: 1px solid var(--borde-suave); border-radius: var(--radio-md); color: var(--texto-secundario); }
        .etapa-torneo.activa { border-color: var(--color-secundario); color: var(--color-secundario); }
        .etapa-torneo.completada { color: var(--color-primario); }
        .wizard-torneo { max-width: 880px; }
        .wizard-acciones { display: flex; justify-content: space-between; gap: 1rem; margin-top: 1.5rem; }
        .wizard-resumen { display: grid; gap: .75rem; }
        .wizard-resumen div { display: flex; justify-content: space-between; gap: 1rem; padding: .8rem 0; border-bottom: 1px solid var(--borde-suave); }
        .wizard-resumen dt { color: var(--texto-secundario); }
        .wizard-resumen dd { margin: 0; text-align: right; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/navbar.html'; ?>
    <?php require_once __DIR__ . '/sidebar.html'; ?>

    <main class="dashboard-organizador crear-torneo-pagina wizard-torneo">
        <header class="crear-torneo-header">
            <div>
                <p class="crear-torneo-etiqueta">Gestion de torneos</p>
                <h1>Crear torneo por etapas</h1>
                <p>El torneo se guarda como borrador hasta completar la revision.</p>
            </div>
            <a class="crear-torneo-volver" href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=dashboard">
                <i class="fa-solid fa-arrow-left"></i> Volver al panel
            </a>
        </header>

        <?php if (!empty($datos['mensaje'])): ?>
            <div class="crear-torneo-notificacion error" role="alert">
                <?php echo htmlspecialchars($datos['mensaje'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <nav class="etapas-torneo" aria-label="Etapas de creacion">
            <span class="etapa-torneo <?php echo $etapa === 1 ? 'activa' : 'completada'; ?>">1. Datos basicos</span>
            <span class="etapa-torneo <?php echo $etapa === 2 ? 'activa' : ($etapa > 2 ? 'completada' : ''); ?>">2. Fechas y reglas</span>
            <span class="etapa-torneo <?php echo $etapa === 3 ? 'activa' : ''; ?>">3. Revision y publicacion</span>
        </nav>

        <?php if ($etapa === 1): ?>
            <form class="crear-torneo-bloque" method="post" action="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=guardarEtapa">
                <input type="hidden" name="etapa" value="1">
                <h2>Datos basicos</h2>
                <div class="crear-torneo-campo">
                    <label for="nombre">Nombre del torneo</label>
                    <input id="nombre" name="nombre" type="text" minlength="3" maxlength="255" required placeholder="Ej.: Copa ASCEND 2026">
                </div>
                <div class="crear-torneo-campo">
                    <label for="juego_id">Juego o disciplina</label>
                    <select id="juego_id" name="juego_id" required>
                        <option value="">Seleccionar juego activo</option>
                        <?php foreach ($juegos as $juego): ?>
                            <option value="<?php echo (int) $juego['id']; ?>"><?php echo htmlspecialchars($juego['nombre'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="crear-torneo-campo">
                    <label for="modalidad_id">Modalidad</label>
                    <select id="modalidad_id" name="modalidad_id" required>
                        <option value="">Seleccionar modalidad</option>
                        <?php foreach ($modalidades as $modalidad): ?>
                            <option value="<?php echo (int) $modalidad['id']; ?>"><?php echo htmlspecialchars(ucfirst($modalidad['nombre']), ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="crear-torneo-campo">
                    <label for="sistema_puntuacion_id">Sistema de puntuacion</label>
                    <select id="sistema_puntuacion_id" name="sistema_puntuacion_id">
                        <option value="">Usar configuracion predeterminada</option>
                        <?php foreach ($sistemasPuntuacion as $sistema): ?>
                            <?php $nombrePuntuacion = $nombresPuntuacion[$sistema['nombre']] ?? ucwords(str_replace('_', ' ', $sistema['nombre'])); ?>
                            <option value="<?php echo (int) $sistema['id']; ?>"><?php echo htmlspecialchars($nombrePuntuacion, ENT_QUOTES, 'UTF-8'); ?> (victoria <?php echo $sistema['puntos_victoria']; ?>, empate <?php echo $sistema['puntos_empate']; ?>, derrota <?php echo $sistema['puntos_derrota']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <small>Define cuántos puntos recibe cada participante según el resultado.</small>
                </div>
                <div class="crear-torneo-campo">
                    <label for="formato">Formato de competencia</label>
                    <select id="formato" name="formato" required>
                        <option value="">Seleccionar formato</option>
                        <?php foreach ($etiquetasEstado as $codigo => $etiqueta): ?>
                            <option value="<?php echo $codigo; ?>"><?php echo $etiqueta; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small id="ayuda-formato">Selecciona un formato para ver cómo se organizará la competencia.</small>
                </div>
                <div class="crear-torneo-campo">
                    <label for="cupo_max_equipos">Cupo maximo de equipos</label>
                    <input id="cupo_max_equipos" name="cupo_max_equipos" type="number" min="2" max="64" required>
                </div>
                <div class="wizard-acciones">
                    <span></span>
                    <button class="boton boton-gestion" type="submit">Guardar y continuar <i class="fa-solid fa-arrow-right"></i></button>
                </div>
            </form>
        <?php elseif ($etapa === 2 && $torneo): ?>
            <form class="crear-torneo-bloque" method="post" action="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=guardarEtapa">
                <input type="hidden" name="etapa" value="2">
                <input type="hidden" name="torneo_id" value="<?php echo (int) $torneo['id']; ?>">
                <h2>Fechas, reglas y ubicacion</h2>
                <div class="crear-torneo-campo"><label for="fecha_inicio_inscripcion">Inicio de inscripciones</label><input id="fecha_inicio_inscripcion" name="fecha_inicio_inscripcion" type="date" value="<?php echo $valor('fecha_inicio_inscripcion'); ?>" required></div>
                <div class="crear-torneo-campo"><label for="fecha_limite_inscripcion">Cierre de inscripciones</label><input id="fecha_limite_inscripcion" name="fecha_limite_inscripcion" type="date" value="<?php echo $valor('fecha_limite_inscripcion'); ?>" required></div>
                <div class="crear-torneo-campo"><label for="fecha_inicio">Inicio del torneo</label><input id="fecha_inicio" name="fecha_inicio" type="date" value="<?php echo $valor('fecha_inicio'); ?>" required></div>
                <div class="crear-torneo-campo"><label for="fecha_fin">Fin estimado</label><input id="fecha_fin" name="fecha_fin" type="date" value="<?php echo $valor('fecha_fin'); ?>" required></div>
                <div class="crear-torneo-campo"><label for="ubicacion">Ubicacion</label><input id="ubicacion" name="ubicacion" type="text" value="<?php echo $valor('ubicacion'); ?>" maxlength="255"></div>
                <div class="crear-torneo-campo"><label for="localidad">Localidad</label><input id="localidad" name="localidad" type="text" value="<?php echo $valor('localidad'); ?>" maxlength="100"></div>
                <div class="crear-torneo-campo"><label for="comunidad">Comunidad u organizacion</label><input id="comunidad" name="comunidad" type="text" value="<?php echo $valor('comunidad'); ?>" maxlength="100"></div>
                <div class="crear-torneo-campo"><label for="premios">Premios</label><textarea id="premios" name="premios" maxlength="2000"><?php echo $valor('premios'); ?></textarea></div>
                <div class="crear-torneo-campo"><label for="banner_url">URL del banner</label><input id="banner_url" name="banner_url" type="url" value="<?php echo $valor('banner_url'); ?>" maxlength="1000"></div>
                <div class="crear-torneo-campo"><label for="stream_url">Enlace de transmision</label><input id="stream_url" name="stream_url" type="url" value="<?php echo $valor('stream_url'); ?>" maxlength="255"></div>
                <div class="crear-torneo-campo"><label for="tipo_resultado">Tipo de resultado</label><select id="tipo_resultado" name="tipo_resultado"><option value="goles">Goles</option><option value="puntos">Puntos</option><option value="rondas">Rondas</option><option value="booleano">Victoria o derrota</option></select></div>
                <div class="crear-torneo-campo"><label for="mejor_de">Mejor de</label><input id="mejor_de" name="mejor_de" type="number" min="1" max="99" value="<?php echo $valor('mejor_de', '1'); ?>"></div>
                <fieldset class="crear-torneo-campo">
                    <legend>Agenda inicial</legend>
                    <?php for ($indice = 0; $indice < 3; $indice++): ?>
                        <div class="crear-evento-fila">
                            <input name="actividad_titulo[]" type="text" maxlength="150" placeholder="Actividad <?php echo $indice + 1; ?>">
                            <select name="actividad_tipo[]"><option value="competencia">Competencia</option><option value="reunion">Reunion</option><option value="administrativo">Administrativo</option><option value="premiacion">Premiacion</option></select>
                            <input name="actividad_fecha[]" type="date">
                            <input name="actividad_hora[]" type="time">
                        </div>
                    <?php endfor; ?>
                </fieldset>
                <div class="crear-torneo-campo"><label for="descripcion">Descripcion</label><textarea id="descripcion" name="descripcion" maxlength="2000"><?php echo $valor('descripcion'); ?></textarea></div>
                <div class="crear-torneo-campo"><label for="reglas">Reglamento</label><textarea id="reglas" name="reglas" maxlength="5000"><?php echo $valor('reglas'); ?></textarea></div>
                <div class="wizard-acciones"><a class="boton" href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=crear&id=<?php echo (int) $torneo['id']; ?>">Anterior</a><button class="boton boton-gestion" type="submit">Guardar y continuar <i class="fa-solid fa-arrow-right"></i></button></div>
            </form>
        <?php elseif ($etapa === 3 && $torneo): ?>
            <section class="crear-torneo-bloque">
                <h2>Revisa tu torneo</h2>
                <dl class="wizard-resumen">
                    <div><dt>Nombre</dt><dd><?php echo $valor('nombre'); ?></dd></div>
                    <div><dt>Juego</dt><dd><?php echo $valor('nombre_juego'); ?></dd></div>
                    <div><dt>Formato</dt><dd><?php echo $etiquetasEstado[$torneo['formato']] ?? $valor('formato'); ?></dd></div>
                    <div><dt>Cupos</dt><dd><?php echo (int) $torneo['cupo_max_equipos']; ?></dd></div>
                    <div><dt>Inscripciones</dt><dd><?php echo $valor('fecha_inicio_inscripcion'); ?> a <?php echo $valor('fecha_limite_inscripcion'); ?></dd></div>
                    <div><dt>Competencia</dt><dd><?php echo $valor('fecha_inicio'); ?> a <?php echo $valor('fecha_fin'); ?></dd></div>
                </dl>
                <form class="wizard-acciones" method="post" action="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=guardarEtapa">
                    <input type="hidden" name="etapa" value="3">
                    <input type="hidden" name="torneo_id" value="<?php echo (int) $torneo['id']; ?>">
                    <a class="boton" href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=crear&etapa=2&id=<?php echo (int) $torneo['id']; ?>">Anterior</a>
                    <button class="boton boton-gestion" type="submit"><i class="fa-solid fa-trophy"></i> Publicar e iniciar inscripciones</button>
                </form>
            </section>
        <?php else: ?>
            <p class="crear-torneo-notificacion error">No se encontro el borrador solicitado.</p>
        <?php endif; ?>
    </main>
    <script>
        const formato = document.getElementById('formato');
        const ayudaFormato = document.getElementById('ayuda-formato');
        const ayudasFormato = <?php echo json_encode($formatoAyuda, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
        formato?.addEventListener('change', () => {
            ayudaFormato.textContent = ayudasFormato[formato.value] || 'Selecciona un formato para ver cómo se organizará la competencia.';
        });
    </script>
    <script src="<?php echo URL_BASE; ?>js/organizador/includes.js"></script>
    <script src="<?php echo URL_BASE; ?>js/organizador/sidebar.js"></script>
</body>
</html>
