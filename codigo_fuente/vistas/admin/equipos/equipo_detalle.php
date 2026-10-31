<?php
/**
 * Ficha administrativa de un equipo (solo lectura + renombre).
 * Vista inyectada por EquipoAdminControlador::ver().
 *
 * @var array $datos
 */
$equipo = $datos['equipo'];
$resumen = $datos['resumen'];
$capitanes = $datos['capitanes'];
$miembros = $datos['miembros'];
$participaciones = $datos['participaciones'];
$partidos = $datos['partidos'];
$solicitudesPendientes = (int) $datos['solicitudes_pendientes'];

// Escudo: puede venir como ruta relativa (img/equipos/...) o como URL absoluta.
$escudo = trim((string) ($equipo['escudo_url'] ?? ''));
$escudoSrc = '';
if ($escudo !== '') {
    $escudoSrc = preg_match('#^https?://#i', $escudo) ? $escudo : URL_BASE . ltrim($escudo, '/');
}

$formatearFecha = function ($valor, string $formato = 'd/m/Y') {
    if (empty($valor)) {
        return '—';
    }
    $marca = strtotime((string) $valor);
    return $marca === false ? '—' : date($formato, $marca);
};

$etiquetaInscripcion = [
    'pendiente' => ['Pendiente', 'estado-pendiente'],
    'confirmado' => ['Confirmado', 'estado-exito'],
    'rechazado' => ['Rechazado', 'estado-fallido'],
    'cancelado' => ['Cancelado', 'estado-cancelado'],
];

$etiquetaTorneo = [
    'borrador' => 'Borrador',
    'inscripciones_abiertas' => 'Inscripciones abiertas',
    'en_curso' => 'En curso',
    'finalizado' => 'Finalizado',
    'cancelado' => 'Cancelado',
];

$etiquetaResultado = [
    'V' => ['Victoria', 'ficha-resultado-victoria'],
    'E' => ['Empate', 'ficha-resultado-empate'],
    'D' => ['Derrota', 'ficha-resultado-derrota'],
    'P' => ['Pendiente', 'ficha-resultado-pendiente'],
];
?>

<div class="ficha-encabezado">
    <div>
        <a href="<?php echo URL_BASE; ?>index.php?c=equipoAdmin&a=index" class="boton-secundario">
            <i class="fa-solid fa-arrow-left"></i> Volver al directorio
        </a>
    </div>
    <?php if (!empty($solicitudesPendientes)): ?>
        <span class="ficha-aviso" title="Solicitudes de ingreso sin responder">
            <i class="fa-solid fa-user-clock"></i>
            <?php echo $solicitudesPendientes; ?> solicitud<?php echo $solicitudesPendientes === 1 ? '' : 'es'; ?> pendiente<?php echo $solicitudesPendientes === 1 ? '' : 's'; ?>
        </span>
    <?php endif; ?>
</div>

<!-- Identidad del equipo -->
<section class="ficha-hero">
    <div class="ficha-hero-escudo">
        <?php if ($escudoSrc !== ''): ?>
            <img src="<?php echo htmlspecialchars($escudoSrc, ENT_QUOTES, 'UTF-8'); ?>"
                 alt="Escudo de <?php echo htmlspecialchars($equipo['nombre_equipo'], ENT_QUOTES, 'UTF-8'); ?>">
        <?php else: ?>
            <span class="ficha-hero-escudo-vacio">
                <?php echo htmlspecialchars(mb_strtoupper(mb_substr($equipo['nombre_equipo'], 0, 1, 'UTF-8'), 'UTF-8'), ENT_QUOTES, 'UTF-8'); ?>
            </span>
        <?php endif; ?>
    </div>

    <div class="ficha-hero-datos">
        <div class="ficha-hero-titulo">
            <h2><?php echo htmlspecialchars($equipo['nombre_equipo'], ENT_QUOTES, 'UTF-8'); ?></h2>
            <?php if ((int) $equipo['activo'] === 1): ?>
                <span class="badge badge-success">Activo</span>
            <?php else: ?>
                <span class="badge badge-danger">Inactivo</span>
            <?php endif; ?>
        </div>

        <p class="ficha-hero-disciplina">
            <i class="fa-solid fa-gamepad"></i>
            <?php echo htmlspecialchars($equipo['nombre_juego'] ?? 'Sin disciplina asignada', ENT_QUOTES, 'UTF-8'); ?>
            <span class="ficha-separador">·</span>
            <i class="fa-solid fa-hashtag"></i> ID <?php echo (int) $equipo['id']; ?>
        </p>

        <?php if (!empty($equipo['descripcion'])): ?>
            <p class="ficha-hero-descripcion"><?php echo nl2br(htmlspecialchars($equipo['descripcion'], ENT_QUOTES, 'UTF-8')); ?></p>
        <?php endif; ?>

        <dl class="ficha-meta">
            <div>
                <dt>Fecha de creación</dt>
                <dd><?php echo $formatearFecha($equipo['fecha_creacion'], 'd/m/Y H:i'); ?></dd>
            </div>
            <div>
                <dt>Creador</dt>
                <dd><?php echo htmlspecialchars($equipo['creador_nombre'], ENT_QUOTES, 'UTF-8'); ?></dd>
            </div>
            <div>
                <dt>Ubicación</dt>
                <dd><?php echo htmlspecialchars($equipo['ubicacion'] ?: '—', ENT_QUOTES, 'UTF-8'); ?></dd>
            </div>
            <div>
                <dt>Año de fundación</dt>
                <dd><?php echo !empty($equipo['anio_fundacion']) ? (int) $equipo['anio_fundacion'] : '—'; ?></dd>
            </div>
            <div>
                <dt>Miembros activos</dt>
                <dd><?php echo count($miembros); ?></dd>
            </div>
        </dl>
    </div>
</section>

<!-- Resumen competitivo -->
<section class="ficha-resumen" aria-label="Resumen competitivo del equipo">
    <div class="ficha-tarjeta">
        <span class="ficha-tarjeta-numero"><?php echo (int) $resumen['partidas']; ?></span>
        <span class="ficha-tarjeta-etiqueta">Partidas</span>
    </div>
    <div class="ficha-tarjeta">
        <span class="ficha-tarjeta-numero"><?php echo (int) $resumen['victorias']; ?></span>
        <span class="ficha-tarjeta-etiqueta">Victorias</span>
    </div>
    <div class="ficha-tarjeta">
        <span class="ficha-tarjeta-numero"><?php echo (int) $resumen['empates']; ?></span>
        <span class="ficha-tarjeta-etiqueta">Empates</span>
    </div>
    <div class="ficha-tarjeta">
        <span class="ficha-tarjeta-numero"><?php echo (int) $resumen['derrotas']; ?></span>
        <span class="ficha-tarjeta-etiqueta">Derrotas</span>
    </div>
    <div class="ficha-tarjeta">
        <span class="ficha-tarjeta-numero"><?php echo (int) $resumen['torneos_ganados']; ?></span>
        <span class="ficha-tarjeta-etiqueta">Torneos ganados</span>
    </div>
    <div class="ficha-tarjeta">
        <span class="ficha-tarjeta-numero"><?php echo count($participaciones); ?></span>
        <span class="ficha-tarjeta-etiqueta">Participaciones</span>
    </div>
</section>

<!-- Renombre administrativo -->
<section class="ficha-seccion">
    <header class="ficha-seccion-cabecera">
        <h3><i class="fa-solid fa-pen-to-square"></i> Renombrar equipo</h3>
        <p class="texto-ayuda">
            El cambio se aplica al equipo, a sus inscripciones en torneos y a los podios registrados,
            todo dentro de una única transacción. Si algo falla, no se modifica nada.
        </p>
    </header>

    <form action="<?php echo URL_BASE; ?>index.php?c=equipoAdmin&a=actualizarNombre"
          method="POST" class="ficha-formulario-renombre">
        <input type="hidden" name="csrf_token"
               value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="equipo_id" value="<?php echo (int) $equipo['id']; ?>">

        <div class="grupo-formulario">
            <label for="nombre_equipo">Nombre del equipo</label>
            <input type="text" id="nombre_equipo" name="nombre_equipo"
                   value="<?php echo htmlspecialchars($equipo['nombre_equipo'], ENT_QUOTES, 'UTF-8'); ?>"
                   minlength="2" maxlength="100" required>
            <span class="ficha-ayuda-campo">Entre 2 y 100 caracteres. Debe ser único en la plataforma.</span>
        </div>

        <div class="ficha-formulario-acciones">
            <button type="submit" class="boton-accion">
                <i class="fa-solid fa-floppy-disk"></i> Guardar nombre
            </button>
        </div>
    </form>
</section>

<!-- Capitanes -->
<section class="ficha-seccion">
    <header class="ficha-seccion-cabecera">
        <h3><i class="fa-solid fa-crown"></i> Capitanes</h3>
    </header>

    <?php if (empty($capitanes)): ?>
        <p class="ficha-vacio">Este equipo no tiene capitanes asignados.</p>
    <?php else: ?>
        <ul class="ficha-capitanes">
            <?php foreach ($capitanes as $capitan): ?>
                <li>
                    <?php if (!empty($capitan['foto_perfil_url'])): ?>
                        <img class="ficha-avatar" src="<?php echo htmlspecialchars($capitan['foto_perfil_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="">
                    <?php else: ?>
                        <span class="ficha-avatar ficha-avatar-vacio">
                            <?php echo htmlspecialchars(mb_strtoupper(mb_substr($capitan['nombre_completo'], 0, 1, 'UTF-8'), 'UTF-8'), ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    <?php endif; ?>
                    <a class="ficha-capitan-nombre" href="<?php echo URL_BASE; ?>index.php?c=usuario&a=ver&id=<?php echo (int) $capitan['id']; ?>">
                        <?php echo htmlspecialchars($capitan['nombre_completo'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                    <?php if ((int) $capitan['es_capitan_principal'] === 1): ?>
                        <span class="badge badge-success">Capitán principal</span>
                    <?php else: ?>
                        <span class="badge">Capitán</span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<!-- Plantilla -->
<section class="ficha-seccion">
    <header class="ficha-seccion-cabecera">
        <h3><i class="fa-solid fa-users"></i> Plantilla (<?php echo count($miembros); ?>)</h3>
        <p class="texto-ayuda">Solo se muestran miembros activos. Un equipo necesita al menos 2 para inscribirse en un torneo.</p>
    </header>

    <?php if (empty($miembros)): ?>
        <p class="ficha-vacio">Este equipo no tiene miembros activos.</p>
    <?php else: ?>
        <table class="tabla-estandar">
            <thead>
                <tr>
                    <th>Jugador</th>
                    <th>Posición</th>
                    <th>Dorsal</th>
                    <th>Capitán</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Índice rápido de capitanes para marcar la columna sin consultas extra.
                $idsCapitanes = [];
                foreach ($capitanes as $capitan) {
                    $idsCapitanes[(int) $capitan['id']] = true;
                }
                ?>
                <?php foreach ($miembros as $miembro): ?>
                    <tr>
                        <td>
                            <span class="ficha-jugador">
                                <?php if (!empty($miembro['foto_perfil_url'])): ?>
                                    <img class="ficha-avatar" src="<?php echo htmlspecialchars($miembro['foto_perfil_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="">
                                <?php else: ?>
                                    <span class="ficha-avatar ficha-avatar-vacio">
                                        <?php echo htmlspecialchars(mb_strtoupper(mb_substr($miembro['nombre_completo'], 0, 1, 'UTF-8'), 'UTF-8'), ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                <?php endif; ?>
                                <a href="<?php echo URL_BASE; ?>index.php?c=usuario&a=ver&id=<?php echo (int) $miembro['id']; ?>">
                                    <?php echo htmlspecialchars($miembro['nombre_completo'], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($miembro['posicion'] ?: '—', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo $miembro['numero_camiseta'] !== null ? (int) $miembro['numero_camiseta'] : '—'; ?></td>
                        <td>
                            <?php echo isset($idsCapitanes[(int) $miembro['id']]) ? 'Sí' : 'No'; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<!-- Participaciones en torneos -->
<section class="ficha-seccion">
    <header class="ficha-seccion-cabecera">
        <h3><i class="fa-solid fa-trophy"></i> Participaciones en torneos (<?php echo count($participaciones); ?>)</h3>
    </header>

    <?php if (empty($participaciones)): ?>
        <p class="ficha-vacio">El equipo todavía no se inscribió en ningún torneo.</p>
    <?php else: ?>
        <table class="tabla-estandar">
            <thead>
                <tr>
                    <th>Torneo</th>
                    <th>Estado del torneo</th>
                    <th>Inscripción</th>
                    <th>Posición</th>
                    <th>PJ</th>
                    <th>G</th>
                    <th>E</th>
                    <th>P</th>
                    <th>Puntos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($participaciones as $participacion): ?>
                    <?php
                        $estadoIns = $etiquetaInscripcion[$participacion['estado_inscripcion']] ?? [$participacion['estado_inscripcion'], ''];
                        $estadoTor = $etiquetaTorneo[$participacion['torneo_estado']] ?? $participacion['torneo_estado'];
                    ?>
                    <tr>
                        <td>
                            <span class="texto-negrita"><?php echo htmlspecialchars($participacion['torneo_nombre'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="ficha-subtexto">
                                Inscripto el <?php echo $formatearFecha($participacion['fecha_inscripcion']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($estadoTor, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><span class="<?php echo htmlspecialchars($estadoIns[1], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($estadoIns[0], ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td><?php echo $participacion['posicion'] !== null ? (int) $participacion['posicion'] . '°' : '—'; ?></td>
                        <td><?php echo (int) ($participacion['partidos_jugados'] ?? 0); ?></td>
                        <td><?php echo (int) ($participacion['partidos_ganados'] ?? 0); ?></td>
                        <td><?php echo (int) ($participacion['partidos_empatados'] ?? 0); ?></td>
                        <td><?php echo (int) ($participacion['partidos_perdidos'] ?? 0); ?></td>
                        <td><?php echo (int) ($participacion['puntos'] ?? 0); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<!-- Últimos partidos -->
<section class="ficha-seccion">
    <header class="ficha-seccion-cabecera">
        <h3><i class="fa-solid fa-futbol"></i> Últimos partidos</h3>
        <p class="texto-ayuda">Se listan hasta 15 encuentros, del más reciente al más antiguo.</p>
    </header>

    <?php if (empty($partidos)): ?>
        <p class="ficha-vacio">El equipo todavía no tiene encuentros programados o jugados.</p>
    <?php else: ?>
        <table class="tabla-estandar">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Torneo</th>
                    <th>Ronda</th>
                    <th>Condición</th>
                    <th>Rival</th>
                    <th>Marcador</th>
                    <th>Resultado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($partidos as $partido): ?>
                    <?php
                        $resultado = $partido['resultado'];
                        $etiquetaRes = $etiquetaResultado[$resultado] ?? ['—', ''];
                        $marcador = ($partido['resultado_local'] !== null && $partido['resultado_visitante'] !== null)
                            ? (int) $partido['resultado_local'] . ' - ' . (int) $partido['resultado_visitante']
                            : '—';
                    ?>
                    <tr>
                        <td><?php echo $formatearFecha($partido['fecha_hora_programada'], 'd/m/Y H:i'); ?></td>
                        <td><?php echo htmlspecialchars($partido['torneo_nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($partido['ronda'] ?: '—', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($partido['condicion'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($partido['rival_nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo $marcador; ?></td>
                        <td>
                            <span class="ficha-resultado <?php echo htmlspecialchars($etiquetaRes[1], ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($etiquetaRes[0], ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
