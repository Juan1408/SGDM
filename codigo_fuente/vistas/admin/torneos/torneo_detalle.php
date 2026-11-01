<?php
/**
 * Ficha administrativa de un torneo (solo lectura).
 * Vista inyectada por TorneoAdminControlador::ver().
 *
 * @var array $datos
 */
$torneo = $datos['torneo'];
$participantes = $datos['participantes'] ?? [];
$posiciones = $datos['posiciones'] ?? [];
$fixture = $datos['fixture'] ?? [];
$podio = $datos['podio'] ?? null;
$actividades = $datos['actividades'] ?? [];
$origen = $datos['origen'] ?? '';

$escapar = static fn ($valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
$formatearFecha = static function ($valor, string $formato = 'd/m/Y'): string {
    if (empty($valor)) {
        return '—';
    }
    $marca = strtotime((string) $valor);
    return $marca === false ? '—' : date($formato, $marca);
};
$urlRecurso = static function (string $ruta): string {
    $ruta = trim($ruta);
    if ($ruta === '') {
        return '';
    }
    return preg_match('#^https?://#i', $ruta) ? $ruta : URL_BASE . ltrim($ruta, '/');
};

$etiquetasFormato = [
    'liga' => 'Liga',
    'suizo' => 'Sistema suizo',
    'eliminacion_directa' => 'Eliminación directa',
];
$etiquetasEstado = [
    'borrador' => ['Borrador', 'badge'],
    'inscripciones_abiertas' => ['Inscripciones abiertas', 'badge badge-success'],
    'en_curso' => ['En curso', 'badge badge-success'],
    'finalizado' => ['Finalizado', 'badge badge-success'],
    'cancelado' => ['Cancelado', 'badge badge-danger'],
];
$etiquetasTipoResultado = [
    'goles' => 'Goles',
    'puntos' => 'Puntos',
    'rondas' => 'Rondas',
    'booleano' => 'Victoria / derrota',
];
$etiquetasInscripcion = [
    'pendiente' => ['Pendiente', 'estado-pendiente'],
    'confirmado' => ['Confirmado', 'estado-exito'],
    'rechazado' => ['Rechazado', 'estado-fallido'],
    'cancelado' => ['Cancelado', 'estado-cancelado'],
];
$etiquetasPartido = [
    'programado' => 'Programado',
    'en_curso' => 'En curso',
    'finalizado' => 'Finalizado',
    'cancelado' => 'Cancelado',
    'aplazado' => 'Aplazado',
];
$etiquetasActividad = [
    'administrativo' => 'Administrativo',
    'reunion' => 'Reunión',
    'competencia' => 'Competencia',
    'premiacion' => 'Premiación',
];

[$textoEstado, $claseEstado] = $etiquetasEstado[$torneo['estado']] ?? [$torneo['estado'], 'badge'];

$confirmados = 0;
$pendientes = 0;
foreach ($participantes as $participante) {
    if ($participante['estado'] === 'confirmado') {
        $confirmados++;
    } elseif ($participante['estado'] === 'pendiente') {
        $pendientes++;
    }
}
$partidosFinalizados = 0;
foreach ($fixture as $partido) {
    if ($partido['estado'] === 'finalizado') {
        $partidosFinalizados++;
    }
}
$tienePodio = is_array($podio) && !empty($podio['primero']);

if ($origen === 'historial') {
    $urlVolver = URL_BASE . 'index.php?c=torneoHistorial&a=index';
    $textoVolver = 'Volver al historial';
} elseif ($origen === 'dashboard') {
    $urlVolver = URL_BASE . 'index.php?c=admin&a=dashboard';
    $textoVolver = 'Volver al resumen';
} else {
    $urlVolver = URL_BASE . 'index.php?c=torneoAdmin&a=index';
    $textoVolver = 'Volver al directorio';
}

$ubicacion = trim((string) $torneo['ubicacion']);
$localidad = trim((string) $torneo['localidad']);
$banner = $urlRecurso((string) ($torneo['banner_url'] ?? ''));
$organizadorFoto = $urlRecurso((string) ($torneo['organizador_foto'] ?? ''));
$mejorDe = (int) ($torneo['mejor_de'] ?? 1);
$formatoMarcador = static fn ($valor): string => rtrim(rtrim(number_format((float) $valor, 2, '.', ''), '0'), '.');

// Los premios pueden venir como texto libre o como arreglo JSON (semillas).
// En ambos casos mostramos el contenido real, sin inventar datos.
$premiosTexto = trim((string) ($torneo['premios'] ?? ''));
$premiosDecodificados = json_decode($premiosTexto, true);
if (is_array($premiosDecodificados)) {
    $premiosItems = array_filter(array_map(
        static fn ($item): string => is_scalar($item) ? trim((string) $item) : '',
        $premiosDecodificados
    ), static fn ($item): bool => $item !== '');
    $premiosTexto = implode(' · ', $premiosItems);
}
?>

<div class="ficha-encabezado">
    <div>
        <a href="<?php echo $urlVolver; ?>" class="boton-secundario">
            <i class="fa-solid fa-arrow-left"></i> <?php echo $escapar($textoVolver); ?>
        </a>
        <a href="<?php echo URL_BASE; ?>index.php?c=torneoAdmin&a=editar&id=<?php echo (int) $torneo['id']; ?>" class="boton-secundario">
            <i class="fa-solid fa-pen"></i> Editar torneo
        </a>
    </div>
</div>

<?php if ($banner !== ''): ?>
    <div class="ficha-torneo-banner">
        <img src="<?php echo $escapar($banner); ?>" alt="Banner de <?php echo $escapar($torneo['nombre']); ?>">
    </div>
<?php endif; ?>

<!-- Identidad del torneo -->
<section class="ficha-hero">
    <div class="ficha-hero-datos">
        <div class="ficha-hero-titulo">
            <h2><?php echo $escapar($torneo['nombre']); ?></h2>
            <span class="<?php echo $escapar($claseEstado); ?>"><?php echo $escapar($textoEstado); ?></span>
        </div>

        <p class="ficha-hero-disciplina">
            <i class="fa-solid fa-gamepad"></i>
            <?php echo $escapar($torneo['nombre_juego']); ?>
            <?php if (!empty($torneo['categoria_juego'])): ?>
                <span class="ficha-separador">·</span>
                <?php echo $escapar($torneo['categoria_juego']); ?>
            <?php endif; ?>
            <span class="ficha-separador">·</span>
            <i class="fa-solid fa-hashtag"></i> ID <?php echo (int) $torneo['id']; ?>
            <span class="ficha-separador">·</span>
            <?php echo $escapar($etiquetasFormato[$torneo['formato']] ?? $torneo['formato']); ?>
        </p>

        <?php if (!empty($torneo['descripcion'])): ?>
            <p class="ficha-hero-descripcion"><?php echo nl2br($escapar($torneo['descripcion'])); ?></p>
        <?php endif; ?>

        <dl class="ficha-meta">
            <div>
                <dt>Organizador</dt>
                <dd>
                    <a class="ficha-enlace" href="<?php echo URL_BASE; ?>index.php?c=usuario&a=ver&id=<?php echo (int) $torneo['organizador_id']; ?>">
                        <?php echo $escapar($torneo['organizador_nombre']); ?>
                    </a>
                    <span class="ficha-subtexto"><?php echo $escapar($torneo['organizador_email']); ?></span>
                </dd>
            </div>
            <div>
                <dt>Inicio del torneo</dt>
                <dd><?php echo $formatearFecha($torneo['fecha_inicio']); ?></dd>
            </div>
            <div>
                <dt>Fin del torneo</dt>
                <dd><?php echo $formatearFecha($torneo['fecha_fin']); ?></dd>
            </div>
            <div>
                <dt>Ubicación</dt>
                <dd><?php echo $escapar($ubicacion !== '' ? $ubicacion : '—'); ?></dd>
            </div>
            <div>
                <dt>Localidad</dt>
                <dd><?php echo $escapar($localidad !== '' ? $localidad : '—'); ?></dd>
            </div>
            <div>
                <dt>Comunidad</dt>
                <dd><?php echo $escapar(trim((string) $torneo['comunidad']) !== '' ? $torneo['comunidad'] : '—'); ?></dd>
            </div>
        </dl>
    </div>
</section>

<!-- Resumen del torneo -->
<section class="ficha-resumen ficha-resumen-torneo" aria-label="Resumen del torneo">
    <div class="ficha-tarjeta">
        <span class="ficha-tarjeta-numero"><?php echo $confirmados; ?><small> / <?php echo (int) $torneo['cupo_max_equipos']; ?></small></span>
        <span class="ficha-tarjeta-etiqueta">Confirmados</span>
    </div>
    <div class="ficha-tarjeta">
        <span class="ficha-tarjeta-numero"><?php echo $pendientes; ?></span>
        <span class="ficha-tarjeta-etiqueta">Solicitudes pendientes</span>
    </div>
    <div class="ficha-tarjeta">
        <span class="ficha-tarjeta-numero"><?php echo $partidosFinalizados; ?><small> / <?php echo count($fixture); ?></small></span>
        <span class="ficha-tarjeta-etiqueta">Partidos jugados</span>
    </div>
    <div class="ficha-tarjeta">
        <span class="ficha-tarjeta-numero"><?php echo count($actividades); ?></span>
        <span class="ficha-tarjeta-etiqueta">Actividades</span>
    </div>
    <div class="ficha-tarjeta">
        <span class="ficha-tarjeta-numero"><?php echo $tienePodio ? 'Sí' : 'No'; ?></span>
        <span class="ficha-tarjeta-etiqueta">Podio registrado</span>
    </div>
</section>

<!-- Configuración cargada por el organizador -->
<section class="ficha-seccion">
    <header class="ficha-seccion-cabecera">
        <h3><i class="fa-solid fa-sliders"></i> Configuración del torneo</h3>
        <p class="texto-ayuda">Datos cargados por el organizador al crear y configurar el torneo.</p>
    </header>

    <dl class="ficha-meta ficha-meta-perfil">
        <div>
            <dt>Disciplina</dt>
            <dd><?php echo $escapar($torneo['nombre_juego']); ?></dd>
        </div>
        <div>
            <dt>Categoría</dt>
            <dd><?php echo $escapar($torneo['categoria_juego'] ?: '—'); ?></dd>
        </div>
        <div>
            <dt>Modalidad</dt>
            <dd><?php echo $escapar($torneo['nombre_modalidad'] ?: '—'); ?></dd>
        </div>
        <div>
            <dt>Formato de competencia</dt>
            <dd><?php echo $escapar($etiquetasFormato[$torneo['formato']] ?? $torneo['formato']); ?></dd>
        </div>
        <div>
            <dt>Sistema de puntuación</dt>
            <dd><?php echo $escapar($torneo['nombre_sistema_puntuacion'] ?: 'Configuración manual'); ?></dd>
        </div>
        <div>
            <dt>Tipo de resultado</dt>
            <dd><?php echo $escapar($etiquetasTipoResultado[$torneo['tipo_resultado']] ?? $torneo['tipo_resultado']); ?></dd>
        </div>
        <div>
            <dt>Puntos por victoria / empate / derrota</dt>
            <dd>
                <?php echo $formatoMarcador($torneo['puntos_victoria']); ?> /
                <?php echo $formatoMarcador($torneo['puntos_empate']); ?> /
                <?php echo $formatoMarcador($torneo['puntos_derrota']); ?>
            </dd>
        </div>
        <div>
            <dt>Serie</dt>
            <dd><?php echo $mejorDe > 1 ? 'Al mejor de ' . $mejorDe . ' (BO' . $mejorDe . ')' : 'Partido único'; ?></dd>
        </div>
        <div>
            <dt>Cupo de participantes</dt>
            <dd>
                Mínimo <?php echo (int) ($torneo['cupo_min_equipos'] ?: 2); ?> ·
                Máximo <?php echo (int) $torneo['cupo_max_equipos']; ?>
            </dd>
        </div>
        <div>
            <dt>Inscripciones</dt>
            <dd>
                <?php echo $formatearFecha($torneo['fecha_inicio_inscripcion']); ?> —
                <?php echo $formatearFecha($torneo['fecha_limite_inscripcion']); ?>
            </dd>
        </div>
        <div>
            <dt>Creado</dt>
            <dd><?php echo $formatearFecha($torneo['creado_en'], 'd/m/Y H:i'); ?></dd>
        </div>
        <div>
            <dt>Última actualización</dt>
            <dd><?php echo $formatearFecha($torneo['actualizado_en'], 'd/m/Y H:i'); ?></dd>
        </div>
        <?php if (!empty($torneo['stream_url'])): ?>
            <div class="ficha-meta-ancho">
                <dt>Transmisión</dt>
                <dd><?php echo $escapar($torneo['stream_url']); ?></dd>
            </div>
        <?php endif; ?>
        <?php if (!empty($torneo['descripcion_modalidad'])): ?>
            <div class="ficha-meta-ancho">
                <dt>Descripción de la modalidad</dt>
                <dd><?php echo $escapar($torneo['descripcion_modalidad']); ?></dd>
            </div>
        <?php endif; ?>
    </dl>
</section>

<!-- Podio -->
<section class="ficha-seccion" id="podio">
    <header class="ficha-seccion-cabecera">
        <h3><i class="fa-solid fa-trophy"></i> Podio</h3>
        <p class="texto-ayuda">Resultado final registrado por el sistema al finalizar el torneo.</p>
    </header>

    <?php if ($tienePodio): ?>
        <div class="ficha-podio">
            <?php
            $puestos = [
                ['primero', '1°', 'Campeón', 'podio-oro', 'fa-crown'],
                ['segundo', '2°', 'Subcampeón', 'podio-plata', 'fa-medal'],
                ['tercero', '3°', 'Tercer puesto', 'podio-bronce', 'fa-medal'],
            ];
            ?>
            <?php foreach ($puestos as [$clave, $posicion, $etiqueta, $clase, $icono]): ?>
                <article class="ficha-podio-puesto <?php echo $clase; ?>">
                    <span class="ficha-podio-posicion"><?php echo $posicion; ?></span>
                    <i class="fa-solid <?php echo $icono; ?>"></i>
                    <strong><?php echo $escapar(!empty($podio[$clave]) ? $podio[$clave] : 'Sin registrar'); ?></strong>
                    <span class="ficha-podio-etiqueta"><?php echo $etiqueta; ?></span>
                </article>
            <?php endforeach; ?>
        </div>
    <?php elseif ($torneo['estado'] === 'cancelado'): ?>
        <p class="ficha-vacio">El torneo fue cancelado, por lo que no se registró un podio.</p>
    <?php elseif ($torneo['estado'] !== 'finalizado'): ?>
        <p class="ficha-vacio">El podio se registra automáticamente cuando el torneo pasa a finalizado.</p>
    <?php else: ?>
        <p class="ficha-vacio">No hay un podio registrado para este torneo.</p>
    <?php endif; ?>
</section>

<!-- Participantes -->
<section class="ficha-seccion">
    <header class="ficha-seccion-cabecera">
        <h3><i class="fa-solid fa-users"></i> Participantes (<?php echo count($participantes); ?>)</h3>
        <p class="texto-ayuda">Incluye equipos y jugadores en cualquier estado de inscripción. Los confirmados son los que compiten en el fixture.</p>
    </header>

    <?php if (empty($participantes)): ?>
        <p class="ficha-vacio">El torneo todavía no tiene participantes inscriptos.</p>
    <?php else: ?>
        <table class="tabla-estandar">
            <thead>
                <tr>
                    <th>Participante</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Inscripción</th>
                    <th>Miembros</th>
                    <th>PJ</th>
                    <th>G</th>
                    <th>E</th>
                    <th>P</th>
                    <th>Puntos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($participantes as $participante): ?>
                    <?php
                    [$textoInscripcion, $claseInscripcion] = $etiquetasInscripcion[$participante['estado']] ?? [$participante['estado'], ''];
                    $esEquipo = $participante['tipo'] === 'equipo';
                    $urlParticipante = $esEquipo
                        ? URL_BASE . 'index.php?c=equipoAdmin&a=ver&id=' . (int) $participante['referencia_id']
                        : URL_BASE . 'index.php?c=usuario&a=ver&id=' . (int) $participante['referencia_id'];
                    ?>
                    <tr>
                        <td>
                            <span class="ficha-jugador">
                                <?php if (!empty($participante['imagen'])): ?>
                                    <img class="ficha-avatar <?php echo $esEquipo ? 'ficha-avatar-escudo' : ''; ?>"
                                         src="<?php echo $escapar($urlRecurso((string) $participante['imagen'])); ?>" alt="">
                                <?php endif; ?>
                                <a class="ficha-enlace" href="<?php echo $urlParticipante; ?>">
                                    <?php echo $escapar($participante['nombre']); ?>
                                </a>
                            </span>
                        </td>
                        <td><?php echo $esEquipo ? 'Equipo' : 'Jugador'; ?></td>
                        <td><span class="<?php echo $escapar($claseInscripcion); ?>"><?php echo $escapar($textoInscripcion); ?></span></td>
                        <td><?php echo $formatearFecha($participante['fecha_inscripcion'], 'd/m/Y H:i'); ?></td>
                        <td><?php echo $esEquipo ? (int) $participante['total_miembros'] : '—'; ?></td>
                        <td><?php echo (int) $participante['partidos_jugados']; ?></td>
                        <td><?php echo (int) $participante['partidos_ganados']; ?></td>
                        <td><?php echo (int) $participante['partidos_empatados']; ?></td>
                        <td><?php echo (int) $participante['partidos_perdidos']; ?></td>
                        <td><?php echo $formatoMarcador($participante['puntos']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<!-- Tabla de posiciones -->
<?php if (!empty($posiciones)): ?>
    <section class="ficha-seccion">
        <header class="ficha-seccion-cabecera">
            <h3><i class="fa-solid fa-ranking-star"></i> Tabla de posiciones</h3>
            <p class="texto-ayuda">Clasificación de los participantes confirmados con los puntos y la diferencia del torneo.</p>
        </header>

        <table class="tabla-estandar">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Participante</th>
                    <th>PJ</th>
                    <th>G</th>
                    <th>E</th>
                    <th>P</th>
                    <th>A favor</th>
                    <th>En contra</th>
                    <th>Diferencia</th>
                    <th>Puntos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posiciones as $posicion): ?>
                    <tr>
                        <td><?php echo (int) $posicion['posicion']; ?>°</td>
                        <td class="texto-negrita"><?php echo $escapar($posicion['nombre']); ?></td>
                        <td><?php echo (int) $posicion['partidos_jugados']; ?></td>
                        <td><?php echo (int) $posicion['partidos_ganados']; ?></td>
                        <td><?php echo (int) $posicion['partidos_empatados']; ?></td>
                        <td><?php echo (int) $posicion['partidos_perdidos']; ?></td>
                        <td><?php echo $formatoMarcador($posicion['puntos_favor']); ?></td>
                        <td><?php echo $formatoMarcador($posicion['puntos_contra']); ?></td>
                        <td><?php echo $formatoMarcador($posicion['diferencia_goles']); ?></td>
                        <td><strong><?php echo $formatoMarcador($posicion['puntos']); ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
<?php endif; ?>

<!-- Fixture y resultados -->
<section class="ficha-seccion">
    <header class="ficha-seccion-cabecera">
        <h3><i class="fa-solid fa-calendar-days"></i> Fixture y resultados (<?php echo count($fixture); ?>)</h3>
        <p class="texto-ayuda">Todos los encuentros generados para el torneo, con su programación y marcador cuando existe.</p>
    </header>

    <?php if (empty($fixture)): ?>
        <p class="ficha-vacio">El torneo todavía no tiene encuentros generados.</p>
    <?php else: ?>
        <table class="tabla-estandar">
            <thead>
                <tr>
                    <th>Ronda</th>
                    <th>Fecha</th>
                    <th>Local</th>
                    <th>Marcador</th>
                    <th>Visitante</th>
                    <th>Cancha</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fixture as $partido): ?>
                    <?php
                    $marcador = ($partido['resultado_local'] !== null && $partido['resultado_visitante'] !== null)
                        ? $formatoMarcador($partido['resultado_local']) . ' - ' . $formatoMarcador($partido['resultado_visitante'])
                        : '—';
                    $ganadorLocal = (int) ($partido['participante_ganador_id'] ?? 0) === (int) ($partido['participante_local_id'] ?? 0)
                        && !empty($partido['participante_ganador_id']);
                    $ganadorVisitante = (int) ($partido['participante_ganador_id'] ?? 0) === (int) ($partido['participante_visitante_id'] ?? 0)
                        && !empty($partido['participante_ganador_id']);
                    ?>
                    <tr>
                        <td>Ronda <?php echo (int) $partido['ronda']; ?></td>
                        <td><?php echo $formatearFecha($partido['fecha_hora_programada'], 'd/m/Y H:i'); ?></td>
                        <td class="<?php echo $ganadorLocal ? 'texto-negrita' : ''; ?>"><?php echo $escapar($partido['local_nombre'] ?: 'Por definir'); ?></td>
                        <td><strong><?php echo $escapar($marcador); ?></strong></td>
                        <td class="<?php echo $ganadorVisitante ? 'texto-negrita' : ''; ?>"><?php echo $escapar($partido['visitante_nombre'] ?: 'Por definir'); ?></td>
                        <td><?php echo $escapar($partido['cancha'] ?: '—'); ?></td>
                        <td><?php echo $escapar($etiquetasPartido[$partido['estado']] ?? $partido['estado']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<!-- Cronograma -->
<?php if (!empty($actividades)): ?>
    <section class="ficha-seccion">
        <header class="ficha-seccion-cabecera">
            <h3><i class="fa-solid fa-list-check"></i> Cronograma (<?php echo count($actividades); ?>)</h3>
            <p class="texto-ayuda">Actividades programadas por el organizador.</p>
        </header>

        <table class="tabla-estandar">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Tipo</th>
                    <th>Actividad</th>
                    <th>Descripción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($actividades as $actividad): ?>
                    <tr>
                        <td><?php echo $formatearFecha($actividad['fecha']); ?></td>
                        <td><?php echo $escapar($actividad['hora'] ? substr($actividad['hora'], 0, 5) . ' hs' : '—'); ?></td>
                        <td><?php echo $escapar($etiquetasActividad[$actividad['tipo']] ?? $actividad['tipo']); ?></td>
                        <td class="texto-negrita"><?php echo $escapar($actividad['titulo']); ?></td>
                        <td><?php echo $escapar($actividad['descripcion'] ?: '—'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
<?php endif; ?>

<!-- Reglas y premios -->
<?php if (!empty($torneo['reglas']) || !empty($torneo['premios'])): ?>
    <section class="ficha-seccion">
        <header class="ficha-seccion-cabecera">
            <h3><i class="fa-solid fa-book-open"></i> Reglas y premios</h3>
        </header>

        <?php if ($premiosTexto !== ''): ?>
            <h4 class="ficha-subtitulo">Premios</h4>
            <p class="ficha-texto"><?php echo nl2br($escapar($premiosTexto)); ?></p>
        <?php endif; ?>

        <?php if (!empty($torneo['reglas'])): ?>
            <h4 class="ficha-subtitulo">Reglas</h4>
            <p class="ficha-texto"><?php echo nl2br($escapar($torneo['reglas'])); ?></p>
        <?php endif; ?>
    </section>
<?php endif; ?>
