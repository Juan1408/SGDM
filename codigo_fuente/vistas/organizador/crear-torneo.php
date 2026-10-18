<?php
$etapa = (int) ($datos['etapa'] ?? 1);
$torneo = $datos['torneo'] ?? null;
$juegos = $datos['juegos'] ?? [];
$modalidades = $datos['modalidades'] ?? [];
$sistemasPuntuacion = $datos['sistemasPuntuacion'] ?? [];
$actividades = $datos['actividades'] ?? [];

// Lo que el usuario había escrito antes de un error (se vuelve a mostrar en el formulario).
// Los campos con error llegan vacíos; el resto se conserva.
$previo = is_array($datos['previo'] ?? null) ? $datos['previo'] : [];
$fuente = array_merge($torneo ?? [], $previo);

$valor = static function (string $campo, string $defecto = '') use ($fuente): string {
    $dato = (string) ($fuente[$campo] ?? '');
    return htmlspecialchars($dato !== '' ? $dato : $defecto, ENT_QUOTES, 'UTF-8');
};
$e = static fn ($texto): string => htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
$seleccionado = static function (string $campo, $opcion) use ($fuente): string {
    return (string) ($fuente[$campo] ?? '') === (string) $opcion ? 'selected' : '';
};
$buscarNombre = static function (array $lista, $id): string {
    foreach ($lista as $item) {
        if ((int) $item['id'] === (int) $id) {
            return (string) $item['nombre'];
        }
    }
    return '';
};
$formatearFecha = static fn (?string $fecha): string => $fecha ? date('d/m/Y', strtotime($fecha)) : 'Sin definir';

$etiquetasEstado = [
    'liga' => 'Liga',
    'suizo' => 'Sistema suizo',
    'eliminacion_directa' => 'Eliminación directa',
];
$nombresPuntuacion = [
    'estandar_3_1_0' => 'Clásico: 3-1-0',
    'ajedrez_1_0.5_0' => 'Ajedrez: 1-0,5-0',
    'esports_mapas_2_1_0' => 'eSports por mapas: 2-1-0',
];
$formatoAyuda = [
    'liga' => 'Todos juegan contra todos y se suman puntos. Admite de 3 a 20 participantes.',
    'suizo' => 'Los emparejamientos se organizan por rondas. Admite una cantidad par de participantes, de 4 a 64.',
    'eliminacion_directa' => 'El perdedor queda eliminado y el ganador avanza. Admite 2, 4, 8, 16, 32 o 64 participantes.',
];
$tiposResultado = [
    'goles' => 'Goles',
    'puntos' => 'Puntos',
    'rondas' => 'Rondas',
    'booleano' => 'Victoria o derrota',
];
$tiposActividad = [
    'competencia' => 'Competencia',
    'reunion' => 'Reunión',
    'administrativo' => 'Administrativo',
    'premiacion' => 'Premiación',
];

// Banner actual (si el borrador ya tiene uno propio)
$bannerActual = !empty($torneo['banner_url'])
    ? URL_BASE . $torneo['banner_url']
    : URL_BASE . 'img/banners/humo1.jpg';

// Ubicación: presencial u online (la plataforma se guarda dentro de "ubicacion")
$ubicacionGuardada = (string) ($torneo['ubicacion'] ?? '');
$esOnline = strpos($ubicacionGuardada, 'Online') === 0;
$plataformaPc = $esOnline && strpos($ubicacionGuardada, 'PC') !== false;
$plataformaMovil = $esOnline && strpos($ubicacionGuardada, 'Móvil') !== false;
$direccion = $esOnline ? '' : $ubicacionGuardada;
if (isset($previo['modo_ubicacion'])) {
    $esOnline = $previo['modo_ubicacion'] === 'online';
    $plataformaPc = in_array('PC', (array) ($previo['plataforma'] ?? []), true);
    $plataformaMovil = in_array('Móvil', (array) ($previo['plataforma'] ?? []), true);
    $direccion = (string) ($previo['ubicacion'] ?? '');
}

// Premios: lista de 3 textos (primero, segundo y tercer lugar)
$premiosGuardados = json_decode((string) ($torneo['premios'] ?? ''), true);
$premiosGuardados = is_array($premiosGuardados) ? array_values($premiosGuardados) : [];
$premiosGuardados = array_pad(array_map('strval', array_slice($premiosGuardados, 0, 3)), 3, '');
if (isset($previo['modo_ubicacion'])) {
    $premiosGuardados = [
        mb_substr((string) ($previo['premio_1'] ?? ''), 0, 60),
        mb_substr((string) ($previo['premio_2'] ?? ''), 0, 60),
        mb_substr((string) ($previo['premio_3'] ?? ''), 0, 60),
    ];
}
$puestosPremio = [
    1 => ['Primer lugar', 'fa-trophy', 'oro'],
    2 => ['Segundo lugar', 'fa-medal', 'plata'],
    3 => ['Tercer lugar', 'fa-award', 'bronce'],
];

if (isset($previo['actividad_titulo']) && is_array($previo['actividad_titulo'])) {
    $actividades = [];
    foreach ($previo['actividad_titulo'] as $indice => $tituloPrevio) {
        $actividades[] = [
            'titulo' => (string) $tituloPrevio,
            'descripcion' => (string) ($previo['actividad_descripcion'][$indice] ?? ''),
            'tipo' => (string) ($previo['actividad_tipo'][$indice] ?? 'competencia'),
            'fecha' => (string) ($previo['actividad_fecha'][$indice] ?? ''),
            'hora' => (string) ($previo['actividad_hora'][$indice] ?? ''),
        ];
    }
}

$nombreModalidad = $fuente ? strtolower($buscarNombre($modalidades, $fuente['modalidad_id'] ?? 0)) : '';
$etiquetaCupo = $nombreModalidad === 'individual' ? 'Cupo máximo de jugadores'
    : ($nombreModalidad === 'equipos' ? 'Cupo máximo de equipos' : 'Cupo máximo');

$pasos = [1 => 'Información general', 2 => 'Fechas y datos', 3 => 'Actividades', 4 => 'Revisión y publicación'];
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
        .wizard-acciones { display: flex; justify-content: space-between; gap: 1rem; margin-top: 1.5rem; }
        .wizard-resumen { display: grid; gap: .75rem; }
        .wizard-resumen div { display: flex; justify-content: space-between; gap: 1rem; padding: .8rem 0; border-bottom: 1px solid var(--borde-suave); }
        .wizard-resumen dt { color: var(--texto-secundario); }
        .wizard-resumen dd { margin: 0; text-align: right; }
        .wizard-resumen img { max-width: 220px; border-radius: 8px; }

        /* Ubicación */
        .opciones-linea { display: flex; flex-wrap: wrap; gap: 1.25rem; }
        .opciones-linea label { display: inline-flex; align-items: center; gap: .45rem; cursor: pointer; }
        .opciones-linea input { width: auto; }
        .ubicacion-bloque { display: grid; gap: .85rem; margin-top: .85rem; }
        .ubicacion-bloque[hidden] { display: none; }

        /* Premios */
        .premios-grilla { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .85rem; margin-top: .5rem; }
        .premio-bloque { display: grid; gap: .5rem; justify-items: center; align-content: start; text-align: center; padding: 1rem .75rem; border: 1px dashed var(--borde-suave); border-radius: 10px; }
        .premio-bloque.activo { border-style: solid; background: linear-gradient(var(--color-azul), var(--color-violeta)); }
        .premio-agregar { width: 56px; height: 56px; border-radius: 50%; border: 1px solid var(--borde-suave); background: transparent; color: var(--texto-secundario); font-size: 1.3rem; cursor: pointer; }
        .premio-agregar:hover { color: var(--color-secundario); border-color: var(--color-secundario); }
        .premio-nombre { font-weight: 600; }
        .premio-contenido { display: grid; gap: .5rem; justify-items: center; width: 100%; }
        .premio-contenido[hidden], .premio-agregar[hidden] { display: none; }
        .premio-insignia { font-size: 2.6rem; }
        .premio-insignia.oro { color: #ffb300; }
        .premio-insignia.plata { color: #c0c8d0; }
        .premio-insignia.bronce { color: #cd7f32; }
        .premio-contenido input { width: 100%; text-align: center; }
        .premio-quitar { background: none; border: none; color: var(--texto-secundario); cursor: pointer; font-size: .85rem; text-decoration: underline; }
        @media (max-width: 640px) { .premios-grilla { grid-template-columns: minmax(0, 1fr); } }

        /* Actividades */
        .actividad-fila .actividad-descripcion-campo { grid-column: 1 / -1; }
        .actividad-fila { display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1.2fr) minmax(0, 1fr) minmax(0, 1fr) auto; gap: .6rem; align-items: center; margin: .75rem 0; }
        .actividad-quitar { background: none; border: 1px solid var(--borde-suave); border-radius: 8px; color: var(--texto-secundario); padding: .55rem .7rem; cursor: pointer; }
        .actividad-vacia { color: var(--texto-secundario); margin: .5rem 0; }
        @media (max-width: 760px) { .actividad-fila { grid-template-columns: minmax(0, 1fr); } }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../componentes/navbar-organizador.php'; ?>

    <main class="dashboard-organizador crear-torneo-pagina wizard-torneo">
        <header class="crear-torneo-header">
            <div>
                <p class="crear-torneo-etiqueta">Gestión de torneos</p>
                <h1>Crear torneo por etapas</h1>
                <p>El torneo se guarda como borrador hasta completar la revisión.</p>
            </div>
            <a class="crear-torneo-volver" href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=dashboard">
                <i class="fa-solid fa-arrow-left"></i> Volver al panel
            </a>
        </header>

        <?php if (!empty($datos['mensaje'])): ?>
            <div class="crear-torneo-notificacion error" role="alert">
                <?php echo $e($datos['mensaje']); ?>
            </div>
        <?php endif; ?>

        <nav class="etapas-torneo" aria-label="Etapas de creación">
            <?php foreach ($pasos as $numero => $titulo): ?>
                <span class="etapa-torneo <?php echo $etapa === $numero ? 'activa' : ($etapa > $numero ? 'completada' : ''); ?>"><?php echo $numero . '. ' . $e($titulo); ?></span>
            <?php endforeach; ?>
        </nav>

        <?php if ($etapa === 1): ?>
            <!-- ===================== PASO 1: INFORMACIÓN GENERAL ===================== -->
            <form class="crear-torneo-bloque" method="post" enctype="multipart/form-data" action="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=guardarEtapa">
                <input type="hidden" name="etapa" value="1">
                <?php if ($torneo): ?>
                    <input type="hidden" name="torneo_id" value="<?php echo (int) $torneo['id']; ?>">
                <?php endif; ?>

                <div class="crear-torneo-campo">
                    <label>Banner del torneo</label>
                    <figure class="banner-preview">
                        <img id="banner-vista-previa" src="<?php echo $e($bannerActual); ?>" alt="Vista previa del banner">
                    </figure>
                    <label class="banner-upload">
                        <i class="fa-solid fa-image"></i> Subir imagen desde tu computadora
                        <input type="file" id="banner" name="banner" accept="image/jpeg,image/png,image/webp" hidden>
                    </label>
                    <small>JPG, PNG o WEBP, de hasta 2 MB. Si no subís ninguna, se usa el fondo por defecto.</small>
                </div>
                <div class="crear-torneo-campo">
                    <label for="nombre">Nombre del torneo</label>
                    <input id="nombre" name="nombre" type="text" minlength="3" maxlength="255" required placeholder="Ej.: Copa ASCEND 2026" value="<?php echo $valor('nombre'); ?>">
                </div>
                <div class="crear-torneo-campo">
                    <label for="juego_id">Juego o disciplina</label>
                    <select id="juego_id" name="juego_id" required>
                        <option value="">Seleccionar juego activo</option>
                        <?php foreach ($juegos as $juego): ?>
                            <option value="<?php echo (int) $juego['id']; ?>" <?php echo $seleccionado('juego_id', $juego['id']); ?>><?php echo $e($juego['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="crear-torneo-campo">
                    <label for="modalidad_id">Modalidad</label>
                    <select id="modalidad_id" name="modalidad_id" required>
                        <option value="">Seleccionar modalidad</option>
                        <?php foreach ($modalidades as $modalidad): ?>
                            <option value="<?php echo (int) $modalidad['id']; ?>" <?php echo $seleccionado('modalidad_id', $modalidad['id']); ?>><?php echo $e(ucfirst($modalidad['nombre'])); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="crear-torneo-campo">
                    <label for="sistema_puntuacion_id">Sistema de puntuación</label>
                    <select id="sistema_puntuacion_id" name="sistema_puntuacion_id">
                        <option value="">Usar configuración predeterminada</option>
                        <?php foreach ($sistemasPuntuacion as $sistema): ?>
                            <?php $nombrePuntuacion = $nombresPuntuacion[$sistema['nombre']] ?? ucwords(str_replace('_', ' ', $sistema['nombre'])); ?>
                            <option value="<?php echo (int) $sistema['id']; ?>" <?php echo $seleccionado('sistema_puntuacion_id', $sistema['id']); ?>><?php echo $e($nombrePuntuacion); ?> (victoria <?php echo $sistema['puntos_victoria']; ?>, empate <?php echo $sistema['puntos_empate']; ?>, derrota <?php echo $sistema['puntos_derrota']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <small>Define cuántos puntos recibe cada participante según el resultado.</small>
                </div>
                <div class="crear-torneo-campo">
                    <label for="formato">Formato de competencia</label>
                    <select id="formato" name="formato" required>
                        <option value="">Seleccionar formato</option>
                        <?php foreach ($etiquetasEstado as $codigo => $etiqueta): ?>
                            <option value="<?php echo $codigo; ?>" <?php echo $seleccionado('formato', $codigo); ?>><?php echo $etiqueta; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small id="ayuda-formato"><?php echo $e($formatoAyuda[$torneo['formato'] ?? ''] ?? 'Selecciona un formato para ver cómo se organizará la competencia.'); ?></small>
                </div>
                <div class="crear-torneo-campo">
                    <label for="cupo_max_equipos"><?php echo $e($etiquetaCupo); ?></label>
                    <select id="cupo_max_equipos" name="cupo_max_equipos" required data-valor="<?php echo $valor('cupo_max_equipos'); ?>">
                        <option value="">Elegí primero el formato</option>
                    </select>
                    <small id="ayuda-cupo">La cantidad de participantes permitida depende del formato de competencia.</small>
                </div>
                <div class="crear-torneo-campo">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" maxlength="2000" placeholder="Contá de qué se trata la competencia..."><?php echo $valor('descripcion'); ?></textarea>
                </div>
                <div class="crear-torneo-campo">
                    <label for="reglas">Reglamento</label>
                    <textarea id="reglas" name="reglas" maxlength="5000" placeholder="Indicá las reglas principales..."><?php echo $valor('reglas'); ?></textarea>
                </div>
                <div class="wizard-acciones">
                    <span></span>
                    <button class="boton boton-gestion" type="submit">Guardar y continuar <i class="fa-solid fa-arrow-right"></i></button>
                </div>
            </form>

        <?php elseif ($etapa === 2 && $torneo): ?>
            <!-- ===================== PASO 2: FECHAS Y DATOS ===================== -->
            <form class="crear-torneo-bloque" method="post" action="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=guardarEtapa">
                <input type="hidden" name="etapa" value="2">
                <input type="hidden" name="torneo_id" value="<?php echo (int) $torneo['id']; ?>">

                <div class="crear-torneo-campo"><label for="fecha_inicio_inscripcion">Inicio de inscripciones</label><input id="fecha_inicio_inscripcion" name="fecha_inicio_inscripcion" type="date" value="<?php echo $valor('fecha_inicio_inscripcion'); ?>" required></div>
                <div class="crear-torneo-campo"><label for="fecha_limite_inscripcion">Cierre de inscripciones</label><input id="fecha_limite_inscripcion" name="fecha_limite_inscripcion" type="date" value="<?php echo $valor('fecha_limite_inscripcion'); ?>" required></div>
                <div class="crear-torneo-campo"><label for="fecha_inicio">Inicio del torneo</label><input id="fecha_inicio" name="fecha_inicio" type="date" value="<?php echo $valor('fecha_inicio'); ?>" required></div>
                <div class="crear-torneo-campo"><label for="fecha_fin">Fin estimado</label><input id="fecha_fin" name="fecha_fin" type="date" value="<?php echo $valor('fecha_fin'); ?>" required></div>

                <fieldset class="crear-torneo-campo">
                    <legend>Ubicación</legend>
                    <div class="opciones-linea">
                        <label><input type="radio" name="modo_ubicacion" value="presencial" <?php echo $esOnline ? '' : 'checked'; ?>> Presencial</label>
                        <label><input type="radio" name="modo_ubicacion" value="online" <?php echo $esOnline ? 'checked' : ''; ?>> Online</label>
                    </div>

                    <div id="ubicacion-presencial" class="ubicacion-bloque" <?php echo $esOnline ? 'hidden' : ''; ?>>
                        <div class="crear-torneo-campo"><label for="ubicacion">Dirección</label><input id="ubicacion" name="ubicacion" type="text" maxlength="255" value="<?php echo $e($direccion); ?>" placeholder="Ej.: Av. Italia 1234"></div>
                        <div class="crear-torneo-campo"><label for="localidad">Localidad</label><input id="localidad" name="localidad" type="text" maxlength="100" value="<?php echo $valor('localidad'); ?>"></div>
                    </div>

                    <div id="ubicacion-online" class="ubicacion-bloque" <?php echo $esOnline ? '' : 'hidden'; ?>>
                        <span>Plataforma (podés elegir una o ambas)</span>
                        <div class="opciones-linea">
                            <label><input type="checkbox" name="plataforma[]" value="PC" <?php echo $plataformaPc ? 'checked' : ''; ?>> <i class="fa-solid fa-desktop"></i> PC</label>
                            <label><input type="checkbox" name="plataforma[]" value="Móvil" <?php echo $plataformaMovil ? 'checked' : ''; ?>> <i class="fa-solid fa-mobile-screen"></i> Móvil</label>
                        </div>
                    </div>
                </fieldset>

                <div class="crear-torneo-campo"><label for="comunidad">Organización o club</label><input id="comunidad" name="comunidad" type="text" value="<?php echo $valor('comunidad'); ?>" maxlength="100"></div>

                <fieldset class="crear-torneo-campo">
                    <legend>Premios (opcionales)</legend>
                    <small>Tocá el "+" para agregar el premio de cada puesto. Se muestran como insignias en la página pública del torneo.</small>
                    <div class="premios-grilla">
                        <?php foreach ($puestosPremio as $puesto => [$nombrePuesto, $iconoPuesto, $claseInsignia]): ?>
                            <?php $textoPremio = $premiosGuardados[$puesto - 1]; $activo = $textoPremio !== ''; ?>
                            <div class="premio-bloque <?php echo $activo ? 'activo' : ''; ?>">
                                <button type="button" class="premio-agregar" aria-label="Agregar premio de <?php echo strtolower($nombrePuesto); ?>" <?php echo $activo ? 'hidden' : ''; ?>><i class="fa-solid fa-plus"></i></button>
                                <div class="premio-contenido" <?php echo $activo ? '' : 'hidden'; ?>>
                                    <i class="fa-solid <?php echo $iconoPuesto; ?> premio-insignia <?php echo $claseInsignia; ?>"></i>
                                    <input type="text" name="premio_<?php echo $puesto; ?>" maxlength="60" placeholder="Ej.: $400" value="<?php echo $e($textoPremio); ?>" <?php echo $activo ? '' : 'disabled'; ?>>
                                    <button type="button" class="premio-quitar">Quitar</button>
                                </div>
                                <span class="premio-nombre"><?php echo $e($nombrePuesto); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </fieldset>

                <div class="crear-torneo-campo"><label for="stream_url">Enlace de transmisión (opcional)</label><input id="stream_url" name="stream_url" type="url" value="<?php echo $valor('stream_url'); ?>" maxlength="255"></div>
                <div class="crear-torneo-campo">
                    <label for="tipo_resultado">Tipo de resultado</label>
                    <select id="tipo_resultado" name="tipo_resultado">
                        <?php foreach ($tiposResultado as $codigo => $etiqueta): ?>
                            <option value="<?php echo $codigo; ?>" <?php echo $seleccionado('tipo_resultado', $codigo); ?>><?php echo $e($etiqueta); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="crear-torneo-campo"><label for="mejor_de">Mejor de</label><input id="mejor_de" name="mejor_de" type="number" min="1" max="99" value="<?php echo $valor('mejor_de', '1'); ?>"></div>

                <div class="wizard-acciones"><a class="boton" href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=crear&etapa=1&id=<?php echo (int) $torneo['id']; ?>">Anterior</a><button class="boton boton-gestion" type="submit">Guardar y continuar <i class="fa-solid fa-arrow-right"></i></button></div>
            </form>

        <?php elseif ($etapa === 3 && $torneo): ?>
            <!-- ===================== PASO 3: ACTIVIDADES ===================== -->
            <form class="crear-torneo-bloque" method="post" action="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=guardarEtapa">
                <input type="hidden" name="etapa" value="3">
                <input type="hidden" name="torneo_id" value="<?php echo (int) $torneo['id']; ?>">

                <fieldset class="crear-torneo-campo">
                    <legend>Agenda del torneo</legend>
                    <small>Cargá reuniones, partidos clave o la premiación. Este paso es opcional: podés avanzar sin agregar actividades.</small>
                    <div id="lista-actividades">
                        <?php foreach ($actividades as $actividad): ?>
                            <div class="actividad-fila">
                                <input name="actividad_titulo[]" type="text" maxlength="150" placeholder="Título de la actividad" value="<?php echo $e($actividad['titulo']); ?>">
                                <select name="actividad_tipo[]">
                                    <?php foreach ($tiposActividad as $codigo => $etiqueta): ?>
                                        <option value="<?php echo $codigo; ?>" <?php echo $actividad['tipo'] === $codigo ? 'selected' : ''; ?>><?php echo $e($etiqueta); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input name="actividad_fecha[]" type="date" value="<?php echo $e($actividad['fecha']); ?>">
                                <input name="actividad_hora[]" type="time" value="<?php echo $actividad['hora'] ? $e(substr($actividad['hora'], 0, 5)) : ''; ?>">
                                <button type="button" class="actividad-quitar" aria-label="Quitar actividad"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <p id="actividades-vacio" class="actividad-vacia" <?php echo $actividades ? 'hidden' : ''; ?>>Todavía no agregaste actividades.</p>
                    <button type="button" id="agregar-actividad" class="boton"><i class="fa-solid fa-plus"></i> Agregar actividad</button>
                </fieldset>

                <template id="plantilla-actividad">
                    <div class="actividad-fila">
                        <input name="actividad_titulo[]" type="text" maxlength="150" placeholder="Título de la actividad">
                        <select name="actividad_tipo[]">
                            <?php foreach ($tiposActividad as $codigo => $etiqueta): ?>
                                <option value="<?php echo $codigo; ?>"><?php echo $e($etiqueta); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input name="actividad_fecha[]" type="date">
                        <input name="actividad_hora[]" type="time">
                        <button type="button" class="actividad-quitar" aria-label="Quitar actividad"><i class="fa-solid fa-trash"></i></button>
                        <input class="actividad-descripcion-campo" name="actividad_descripcion[]" type="text" maxlength="255" placeholder="Descripción breve (opcional)">
                        <input class="actividad-descripcion-campo" name="actividad_descripcion[]" type="text" maxlength="255" placeholder="Descripción breve (opcional)" value="<?php echo $e($actividad['descripcion'] ?? ''); ?>">
                    </div>
                </template>

                <div class="wizard-acciones"><a class="boton" href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=crear&etapa=2&id=<?php echo (int) $torneo['id']; ?>">Anterior</a><button class="boton boton-gestion" type="submit">Guardar y continuar <i class="fa-solid fa-arrow-right"></i></button></div>
            </form>

        <?php elseif ($etapa === 4 && $torneo): ?>
            <!-- ===================== PASO 4: REVISIÓN Y PUBLICACIÓN ===================== -->
            <?php
            $sistemaId = (int) ($torneo['sistema_puntuacion_id'] ?? 0);
            $nombreSistema = 'Configuración predeterminada';
            foreach ($sistemasPuntuacion as $sistema) {
                if ((int) $sistema['id'] === $sistemaId) {
                    $nombreSistema = $nombresPuntuacion[$sistema['nombre']] ?? ucwords(str_replace('_', ' ', $sistema['nombre']));
                }
            }
            $ubicacionResumen = $ubicacionGuardada !== '' ? $ubicacionGuardada : 'Sin definir';
            if (!$esOnline && !empty($torneo['localidad'])) {
                $ubicacionResumen .= ' - ' . $torneo['localidad'];
            }
            ?>
            <section class="crear-torneo-bloque">
                <h2>Revisá tu torneo</h2>
                <dl class="wizard-resumen">
                    <div><dt>Banner</dt><dd><img src="<?php echo $e($bannerActual); ?>" alt="Banner del torneo"></dd></div>
                    <div><dt>Nombre</dt><dd><?php echo $valor('nombre'); ?></dd></div>
                    <div><dt>Juego</dt><dd><?php echo $valor('nombre_juego'); ?></dd></div>
                    <div><dt>Modalidad</dt><dd><?php echo $e(ucfirst($nombreModalidad)); ?></dd></div>
                    <div><dt>Sistema de puntuación</dt><dd><?php echo $e($nombreSistema); ?></dd></div>
                    <div><dt>Formato</dt><dd><?php echo $e($etiquetasEstado[$torneo['formato']] ?? $torneo['formato']); ?></dd></div>
                    <div><dt><?php echo $e($etiquetaCupo); ?></dt><dd><?php echo (int) $torneo['cupo_max_equipos']; ?></dd></div>
                    <div><dt>Inscripciones</dt><dd><?php echo $formatearFecha($torneo['fecha_inicio_inscripcion']); ?> a <?php echo $formatearFecha($torneo['fecha_limite_inscripcion']); ?></dd></div>
                    <div><dt>Competencia</dt><dd><?php echo $formatearFecha($torneo['fecha_inicio']); ?> a <?php echo $formatearFecha($torneo['fecha_fin']); ?></dd></div>
                    <div><dt>Ubicación</dt><dd><?php echo $e($ubicacionResumen); ?></dd></div>
                    <div><dt>Organización o club</dt><dd><?php echo $e($torneo['comunidad'] ?: 'Sin definir'); ?></dd></div>
                    <div><dt>Premios</dt><dd>
                        <?php
                        $lineasPremio = [];
                        foreach ($puestosPremio as $puesto => [$nombrePuesto]) {
                            if ($premiosGuardados[$puesto - 1] !== '') {
                                $lineasPremio[] = $e($nombrePuesto) . ': ' . $e($premiosGuardados[$puesto - 1]);
                            }
                        }
                        echo $lineasPremio ? implode('<br>', $lineasPremio) : 'Sin premios';
                        ?>
                    </dd></div>
                    <div><dt>Resultado</dt><dd><?php echo $e($tiposResultado[$torneo['tipo_resultado']] ?? $torneo['tipo_resultado']); ?> · mejor de <?php echo (int) $torneo['mejor_de']; ?></dd></div>
                    <div><dt>Actividades</dt><dd>
                        <?php if ($actividades): ?>
                            <?php foreach ($actividades as $actividad): ?>
                                <?php echo $e($actividad['titulo']); ?> (<?php echo $formatearFecha($actividad['fecha']); ?><?php echo $actividad['hora'] ? ' ' . $e(substr($actividad['hora'], 0, 5)) : ''; ?>)<br>
                            <?php endforeach; ?>
                        <?php else: ?>
                            Sin actividades
                        <?php endif; ?>
                    </dd></div>
                </dl>
                <form class="wizard-acciones" method="post" action="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=guardarEtapa">
                    <input type="hidden" name="etapa" value="4">
                    <input type="hidden" name="torneo_id" value="<?php echo (int) $torneo['id']; ?>">
                    <a class="boton" href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=crear&etapa=3&id=<?php echo (int) $torneo['id']; ?>">Anterior</a>
                    <button class="boton boton-gestion" type="submit"><i class="fa-solid fa-trophy"></i> Publicar e iniciar inscripciones</button>
                </form>
            </section>

        <?php else: ?>
            <p class="crear-torneo-notificacion error">No se encontró el borrador solicitado.</p>
        <?php endif; ?>
    </main>

    <script>
        // ---- Paso 1: ayuda del formato, etiqueta del cupo y vista previa del banner ----
        const formato = document.getElementById('formato');
        const ayudaFormato = document.getElementById('ayuda-formato');
        const ayudasFormato = <?php echo json_encode($formatoAyuda, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
        formato?.addEventListener('change', () => {
            ayudaFormato.textContent = ayudasFormato[formato.value] || 'Selecciona un formato para ver cómo se organizará la competencia.';
        });

        // Cupos permitidos según el formato
        const cupo = document.getElementById('cupo_max_equipos');
        const cuposPorFormato = {
            liga: Array.from({ length: 18 }, (_, i) => i + 3),                    // 3 a 20
            suizo: Array.from({ length: 31 }, (_, i) => (i + 2) * 2),             // 4, 6, ... 64
            eliminacion_directa: [2, 4, 8, 16, 32, 64],
        };
        const cargarCupos = (conservar) => {
            const opciones = cuposPorFormato[formato.value];
            const elegido = conservar ?? cupo.value;
            cupo.innerHTML = '';
            if (!opciones) {
                cupo.add(new Option('Elegí primero el formato', ''));
                return;
            }
            cupo.add(new Option('Seleccionar cupo', ''));
            opciones.forEach((numero) => cupo.add(new Option(numero, numero)));
            cupo.value = opciones.map(String).includes(String(elegido)) ? String(elegido) : '';
        };
        if (formato && cupo) {
            formato.addEventListener('change', () => cargarCupos());
            cargarCupos(cupo.dataset.valor);
        }

        const modalidad = document.getElementById('modalidad_id');
        const etiquetaCupo = document.querySelector('label[for="cupo_max_equipos"]');
        modalidad?.addEventListener('change', () => {
            const texto = modalidad.options[modalidad.selectedIndex].text.toLowerCase();
            etiquetaCupo.textContent = texto === 'individual' ? 'Cupo máximo de jugadores'
                : texto === 'equipos' ? 'Cupo máximo de equipos' : 'Cupo máximo';
        });

        const vistaPrevia = document.getElementById('banner-vista-previa');
        const archivoBanner = document.getElementById('banner');
        archivoBanner?.addEventListener('change', () => {
            if (archivoBanner.files[0]) {
                vistaPrevia.src = URL.createObjectURL(archivoBanner.files[0]);
            }
        });

        // ---- Paso 2: ubicación presencial / online ----
        const bloquePresencial = document.getElementById('ubicacion-presencial');
        const bloqueOnline = document.getElementById('ubicacion-online');
        document.querySelectorAll('input[name="modo_ubicacion"]').forEach((radio) => {
            radio.addEventListener('change', () => {
                const online = radio.value === 'online' && radio.checked;
                if (radio.checked) {
                    bloqueOnline.hidden = !online;
                    bloquePresencial.hidden = online;
                }
            });
        });

        // ---- Paso 2: premios (el "+" activa el bloque de cada puesto) ----
        document.querySelectorAll('.premio-bloque').forEach((bloque) => {
            const agregar = bloque.querySelector('.premio-agregar');
            const contenido = bloque.querySelector('.premio-contenido');
            const campo = bloque.querySelector('input');
            const quitar = bloque.querySelector('.premio-quitar');
            agregar.addEventListener('click', () => {
                agregar.hidden = true;
                contenido.hidden = false;
                campo.disabled = false;
                bloque.classList.add('activo');
                campo.focus();
            });
            quitar.addEventListener('click', () => {
                campo.value = '';
                campo.disabled = true;
                contenido.hidden = true;
                agregar.hidden = false;
                bloque.classList.remove('activo');
            });
        });

        // ---- Paso 3: lista de actividades ----
        const listaActividades = document.getElementById('lista-actividades');
        const plantillaActividad = document.getElementById('plantilla-actividad');
        const mensajeVacio = document.getElementById('actividades-vacio');
        const actualizarVacio = () => {
            if (mensajeVacio) mensajeVacio.hidden = listaActividades.children.length > 0;
        };
        document.getElementById('agregar-actividad')?.addEventListener('click', () => {
            listaActividades.appendChild(plantillaActividad.content.cloneNode(true));
            actualizarVacio();
        });
        listaActividades?.addEventListener('click', (evento) => {
            const boton = evento.target.closest('.actividad-quitar');
            if (boton) {
                boton.closest('.actividad-fila').remove();
                actualizarVacio();
            }
        });
    </script>
    <?php include __DIR__ . '/../componentes/footer.html'; ?>
</body>
</html>
