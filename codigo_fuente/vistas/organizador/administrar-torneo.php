<?php
$torneo = $datos['torneo'];
$seccion = $datos['seccion'];
$escapar = static fn ($valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
$formatearFecha = static fn (?string $fecha): string => $fecha ? date('d/m/Y', strtotime($fecha)) : 'A confirmar';

$etiquetasFormato = [
    'liga' => 'Liga',
    'suizo' => 'Sistema suizo',
    'eliminacion_directa' => 'Eliminación directa',
];
$etiquetasEstado = [
    'borrador' => ['Borrador', 'finalizado'],
    'inscripciones_abiertas' => ['Inscripciones abiertas', 'abierta'],
    'en_curso' => ['En curso', 'curso'],
    'finalizado' => ['Finalizado', 'finalizado'],
    'cancelado' => ['Cancelado', 'finalizado'],
];
[$textoEstado, $claseEstado] = $etiquetasEstado[$torneo['estado']] ?? [$torneo['estado'], 'finalizado'];
if ($torneo['estado'] === 'inscripciones_abiertas' && $torneo['fecha_limite_inscripcion'] && $torneo['fecha_limite_inscripcion'] < date('Y-m-d')) {
    [$textoEstado, $claseEstado] = ['Inscripciones cerradas', 'curso'];
}


$pendientes = array_values(array_filter($datos['inscripciones'], static fn ($inscripcion) => $inscripcion['estado'] === 'pendiente'));
$puedeResponder = $torneo['estado'] === 'inscripciones_abiertas';
$ubicacion = trim($torneo['ubicacion'] . ($torneo['localidad'] ? ' - ' . $torneo['localidad'] : ''), ' -');
$tarjetas = [
    ['Disciplina', $torneo['nombre_juego']],
    ['Modalidad', $datos['nombreModalidad']],
    ['Sistema de competencia', $etiquetasFormato[$torneo['formato']] ?? $torneo['formato']],
    ['Participantes', $datos['cuposOcupados'] . ' de ' . (int) $torneo['cupo_max_equipos']],
    ['Inscripciones', $formatearFecha($torneo['fecha_inicio_inscripcion']) . ' - ' . $formatearFecha($torneo['fecha_limite_inscripcion'])],
    ['Duración del torneo', $formatearFecha($torneo['fecha_inicio']) . ' - ' . $formatearFecha($torneo['fecha_fin'])],
    ['Ubicación', $ubicacion !== '' ? $ubicacion : 'Sin definir'],
    ['Organización', $torneo['comunidad'] ?: 'Sin definir'],
];

$mesesCorto = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sept', 'oct', 'nov', 'dic'];
$fechaCorta = static fn (string $fecha): string => date('d', strtotime($fecha)) . ' ' . $mesesCorto[(int) date('n', strtotime($fecha)) - 1] . ' ' . date('Y', strtotime($fecha));
$ubicacionActividad = $ubicacion !== '' ? $ubicacion : 'A definir';

$estadosPartido = [
    'programado' => 'Programado',
    'en_curso' => 'En curso',
    'finalizado' => 'Finalizado',
    'cancelado' => 'Cancelado',
    'aplazado' => 'Aplazado',
];
$nombresRondaEliminacion = [
    2 => 'Final',
    4 => 'Semifinales',
    8 => 'Cuartos de final',
    16 => 'Octavos de final',
    32 => 'Dieciseisavos de final',
    64 => 'Treintaidosavos de final',
];


$accionesEstado = [
    'borrador' => [
        ['inscripciones_abiertas', 'Abrir inscripciones', 'fa-door-open', false, '¿Abrir las inscripciones del torneo?'],
        ['cancelado', 'Cancelar torneo', 'fa-ban', true, '¿Cancelar el torneo? Esta acción no se puede deshacer.'],
    ],
    'inscripciones_abiertas' => [
        ['en_curso', 'Iniciar torneo', 'fa-play', false, '¿Iniciar el torneo? Ya no se podrán aceptar ni quitar participantes.'],
        ['cancelado', 'Cancelar torneo', 'fa-ban', true, '¿Cancelar el torneo? Esta acción no se puede deshacer.'],
    ],
    'en_curso' => [
        ['finalizado', 'Finalizar torneo', 'fa-flag-checkered', true, '¿Finalizar el torneo? Esta acción no se puede deshacer.'],
    ],
];
$botonesEstado = $accionesEstado[$torneo['estado']] ?? [];
$formatoMarcador = static fn ($valor): string => rtrim(rtrim(number_format((float) $valor, 2, '.', ''), '0'), '.');
?>
<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title>ASCEND | Administrar torneo</title>

   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
      rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/botones.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/tarjetas.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/chips.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/organizador-layout.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/footer.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/organizador/administrar-torneo.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/organizador/administrar-secciones.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/organizador/administrar-responsive.css">
</head>

<body>

      <?php require_once __DIR__ . '/../componentes/navbar-organizador.php'; ?>

   <main class="dashboard-organizador administrar-torneo-pagina">

      <a class="back-link" href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=misTorneos">
         <i class="fa-solid fa-arrow-left"></i>
         Volver a Mis torneos
      </a>

      <?php if (!empty($datos['mensaje'])): ?>
         <p class="success-message"><?php echo $escapar($datos['mensaje']); ?></p>
      <?php endif; ?>

            <header class="torneo-header">
         <div>
            <span class="torneo-header-label">Administrar torneo</span>
            <h1><?php echo $escapar($torneo['nombre']); ?></h1>
            <p><?php echo $escapar($torneo['nombre_juego']); ?> · <?php echo $escapar($etiquetasFormato[$torneo['formato']] ?? $torneo['formato']); ?></p>
         </div>

         <div class="torneo-header-estado">
            <span class="estado <?php echo $claseEstado; ?>"><?php echo $escapar($textoEstado); ?></span>

            <?php if ($botonesEstado !== []): ?>
               <div class="torneo-header-acciones">
                  <?php foreach ($botonesEstado as [$estadoNuevo, $textoBoton, $iconoBoton, $peligro, $confirmacion]): ?>
                                         <form method="post" action="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=cambiarEstado"
                        onsubmit="return confirm('<?php echo $escapar($confirmacion); ?>');">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="torneo_id" value="<?php echo (int) $torneo['id']; ?>">
                        <input type="hidden" name="estado" value="<?php echo $estadoNuevo; ?>">
                        <input type="hidden" name="seccion" value="<?php echo $escapar($seccion); ?>">
                        <button class="estado-boton <?php echo $peligro ? 'estado-boton--peligro' : ''; ?>" type="submit">
                           <i class="fa-solid <?php echo $iconoBoton; ?>"></i>
                           <?php echo $escapar($textoBoton); ?>
                        </button>
                     </form>
                  <?php endforeach; ?>
               </div>
            <?php endif; ?>
         </div>
      </header>

      <nav class="torneo-menu">
         <ul class="torneo-menu-lista">

            <li>
               <button class="torneo-menu-boton <?php echo $seccion === 'informacion' ? 'active' : ''; ?>" type="button" data-section="informacion">
                  <i class="fa-solid fa-circle-info"></i>
                  <span>Información</span>
               </button>
            </li>

            <li class="torneo-menu-grupo">
               <button class="torneo-menu-boton torneo-menu-toggle" type="button">
                  <i class="fa-solid fa-users"></i>
                  <span>Participantes</span>
                  <i class="fa-solid fa-chevron-down"></i>
               </button>
               <ul class="torneo-menu-submenu">
                  <li><button type="button" data-section="participantes">Lista de participantes</button></li>
                  <li><button type="button" data-section="solicitudes">Solicitudes</button></li>
               </ul>
            </li>

            <li class="torneo-menu-grupo">
               <button class="torneo-menu-boton torneo-menu-toggle" type="button">
                  <i class="fa-solid fa-sitemap"></i>
                  <span>Competencia</span>
                  <i class="fa-solid fa-chevron-down"></i>
               </button>
               <ul class="torneo-menu-submenu">
                  <li><button type="button" data-section="fixture">Fixture</button></li>
                  <li><button type="button" data-section="resultados">Resultados</button></li>
                  <li><button type="button" data-section="posiciones">Posiciones</button></li>
                  <li><button type="button" data-section="cronograma">Cronograma</button></li>
               </ul>
            </li>

            <li class="torneo-menu-grupo">
               <button class="torneo-menu-boton torneo-menu-toggle" type="button">
                  <i class="fa-solid fa-gear"></i>
                  <span>Configuración</span>
                  <i class="fa-solid fa-chevron-down"></i>
               </button>
               <ul class="torneo-menu-submenu">
                  <li><button type="button" data-section="editar">Editar torneo</button></li>
               </ul>
            </li>

         </ul>
      </nav>

      <section class="torneo-panel" id="contenidoTorneo">

         <?php if ($seccion === 'informacion'): ?>

            <h2 class="panel-title">Información general</h2>
            <p class="panel-description">Consulta los datos principales y la configuración de la competencia.</p>

            <div class="information-grid">
               <?php foreach ($tarjetas as [$etiqueta, $valor]): ?>
                  <article class="information-card">
                     <span><?php echo $escapar($etiqueta); ?></span>
                     <strong><?php echo $escapar($valor); ?></strong>
                  </article>
               <?php endforeach; ?>
            </div>

            <div class="tournament-description">
               <strong>Descripción</strong>
               <p><?php echo nl2br($escapar($torneo['descripcion'] ?: 'Sin descripción.')); ?></p>
            </div>

            <div class="tournament-description">
               <strong>Reglamento</strong>
               <p><?php echo nl2br($escapar($torneo['reglas'] ?: 'Sin reglamento.')); ?></p>
            </div>

                     <?php elseif ($seccion === 'solicitudes'): ?>

            <?php if ($pendientes === []): ?>
               <div class="section-placeholder">
                  <i class="fa-solid fa-envelope-open-text" aria-hidden="true"></i>
                  <h2>Solicitudes</h2>
                  <p>No hay solicitudes pendientes para este torneo.</p>
               </div>
            <?php else: ?>
               <div class="solicitudes-layout">

                  <div class="solicitudes-encabezado">
                     <div>
                        <span class="fixture-summary-label">Inscripciones</span>
                        <h2 class="panel-title">Solicitudes pendientes</h2>
                        <p class="panel-description">
                           <?php echo $puedeResponder
                              ? 'Aceptá o rechazá las solicitudes recibidas para el torneo.'
                              : 'Las inscripciones solo se pueden responder antes de que el torneo comience.'; ?>
                        </p>
                     </div>

                     <div class="participantes-resumen">
                        <strong><?php echo count($pendientes); ?></strong>
                        <span>Pendientes</span>
                     </div>
                  </div>

                  <div class="solicitudes-lista">
                     <?php foreach ($pendientes as $solicitud): ?>
                        <article class="solicitud-card">

                           <div class="solicitud-card__encabezado">
                              <div>
                                 <span class="solicitud-tipo"><?php echo $solicitud['tipo'] === 'equipo' ? 'Equipo' : 'Jugador'; ?></span>
                                 <h3><?php echo $escapar($solicitud['nombre']); ?></h3>
                              </div>
                              <span class="solicitud-estado">Pendiente</span>
                           </div>

                           <div class="solicitud-card__datos">
                              <p>
                                 <i class="fa-solid fa-user"></i>
                                 <strong>Responsable:</strong>
                                 <?php echo $escapar($solicitud['responsable'] ?: 'Sin definir'); ?>
                              </p>
                              <p>
                                 <i class="fa-solid fa-calendar-day"></i>
                                 <strong>Fecha:</strong>
                                 <?php echo $formatearFecha($solicitud['fecha_inscripcion']); ?>
                              </p>
                           </div>

                           <?php if ($puedeResponder): ?>
                              <div class="solicitud-card__acciones">
                                 <?php foreach (['confirmado' => ['aceptar', 'fa-check', 'Aceptar'], 'rechazado' => ['rechazar', 'fa-xmark', 'Rechazar']] as $respuesta => [$clase, $icono, $texto]): ?>
                                    <form method="post" action="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=responderInscripcion">
                                       <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="inscripcion_id" value="<?php echo (int) $solicitud['id']; ?>">
                                       <input type="hidden" name="torneo_id" value="<?php echo (int) $torneo['id']; ?>">
                                       <input type="hidden" name="respuesta" value="<?php echo $respuesta; ?>">
                                       <button class="solicitud-boton solicitud-boton--<?php echo $clase; ?>" type="submit">
                                          <i class="fa-solid <?php echo $icono; ?>"></i>
                                          <?php echo $texto; ?>
                                       </button>
                                    </form>
                                 <?php endforeach; ?>
                              </div>
                           <?php endif; ?>

                        </article>
                     <?php endforeach; ?>
                  </div>

               </div>
            <?php endif; ?>

                 <?php elseif ($seccion === 'participantes'): ?>

            <?php $confirmados = array_values(array_filter($datos['inscripciones'], static fn ($inscripcion) => $inscripcion['estado'] === 'confirmado')); ?>

            <div class="participantes-layout">

               <div class="participantes-encabezado">
                  <div>
                     <span class="fixture-summary-label">Inscripciones</span>
                     <h2 class="panel-title">Lista de participantes</h2>
                     <p class="panel-description">Participantes confirmados en el torneo.</p>
                  </div>

                  <div class="participantes-resumen">
                     <strong><?php echo count($confirmados); ?> / <?php echo (int) $torneo['cupo_max_equipos']; ?></strong>
                     <span>Confirmados</span>
                  </div>
               </div>

               <?php if ($confirmados === []): ?>
                  <div class="section-placeholder">
                     <i class="fa-solid fa-users" aria-hidden="true"></i>
                     <h2>Participantes</h2>
                     <p>Todavía no hay participantes confirmados.</p>
                  </div>
               <?php else: ?>
                  <div class="participantes-list">
                     <?php foreach ($confirmados as $participante): ?>
                        <article class="participante-card">

                           <div class="participante-card-header">
                              <div>
                                 <span class="participante-tipo"><?php echo $participante['tipo'] === 'equipo' ? 'Equipo' : 'Jugador'; ?></span>
                                 <h3><?php echo $escapar($participante['nombre']); ?></h3>
                              </div>
                              <span class="participante-status">Confirmado</span>
                           </div>

                           <div class="participante-info">
                              <p><i class="fa-solid fa-user"></i><strong>Responsable:</strong> <?php echo $escapar($participante['responsable'] ?: 'Sin definir'); ?></p>
                              <p><i class="fa-solid fa-calendar-day"></i><strong>Inscripto:</strong> <?php echo $formatearFecha($participante['fecha_inscripcion']); ?></p>
                           </div>

                           <?php if ($participante['tipo'] === 'equipo'): ?>
                              <a class="participante-btn participante-btn-view" href="<?php echo URL_BASE; ?>index.php?c=equipo&a=verPerfil&id=<?php echo (int) $participante['referencia_id']; ?>">
                              <i class="fa-solid fa-people-group"></i>Ver equipo
                           </a>
                        <?php else: ?>
                              <a class="participante-btn participante-btn-view" href="<?php echo URL_BASE; ?>index.php?c=perfil&a=jugador&id=<?php echo (int) $participante['referencia_id']; ?>">
                              <i class="fa-solid fa-user"></i>Ver jugador
                           </a>
                        <?php endif; ?>

                              <?php if ($puedeResponder): ?>
                                 <form method="post" action="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=quitarParticipante" onsubmit="return confirm('¿Eliminar a este participante del torneo?');">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="inscripcion_id" value="<?php echo (int) $participante['id']; ?>">
                                    <input type="hidden" name="torneo_id" value="<?php echo (int) $torneo['id']; ?>">
                                    <button class="participante-btn participante-btn-remove" type="submit">
                                       <i class="fa-solid fa-user-minus"></i>Eliminar
                                    </button>
                                 </form>
                              <?php endif; ?>
                           </div>

                        </article>
                     <?php endforeach; ?>
                  </div>
               <?php endif; ?>

            </div>

            <?php elseif ($seccion === 'cronograma'): ?>

            <?php
            $actividades = $datos['actividades'];
            $actividadesPorDia = [];
            foreach ($actividades as $actividad) {
                $actividadesPorDia[$actividad['fecha']][] = $actividad;
            }
            $puedeEditarCronograma = !in_array($torneo['estado'], ['finalizado', 'cancelado'], true);
            $tiposActividad = [
                'competencia' => 'Competencia',
                'reunion' => 'Reunión',
                'administrativo' => 'Administrativo',
                'premiacion' => 'Premiación',
            ];
            ?>

            <div class="cronograma-layout">

               <div class="cronograma-encabezado">
                  <div>
                     <span class="fixture-summary-label">Agenda del torneo</span>
                     <h2 class="panel-title">Cronograma</h2>
                     <p class="panel-description">
                        <?php echo $puedeEditarCronograma
                           ? 'Agregá y administrá las actividades importantes, reuniones y fechas principales.'
                           : 'Las actividades no se pueden modificar porque el torneo está finalizado o cancelado.'; ?>
                     </p>
                  </div>

                  <div class="participantes-resumen">
                     <strong><?php echo count($actividades); ?></strong>
                     <span>Actividades</span>
                  </div>
               </div>

               <?php if ($puedeEditarCronograma): ?>
                  <form class="edit-form cronograma-form" method="post" action="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=agregarActividad">
                     <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                  <input type="hidden" name="torneo_id" value="<?php echo (int) $torneo['id']; ?>">

                     <div class="edit-form__grid">
                        <div class="form-group form-group--full">
                           <label for="nueva-titulo">Título</label>
                           <input type="text" id="nueva-titulo" name="titulo" maxlength="150" required placeholder="Ej: Reunión de capitanes">
                        </div>

                        <div class="form-group form-group--full">
                           <label for="nueva-descripcion">Descripción (opcional)</label>
                           <textarea id="nueva-descripcion" name="descripcion" rows="2" maxlength="255" placeholder="Breve detalle de la actividad"></textarea>
                        </div>

                        <div class="form-group">
                           <label for="nueva-tipo">Tipo</label>
                           <select id="nueva-tipo" name="tipo" required>
                              <?php foreach ($tiposActividad as $valor => $texto): ?>
                                 <option value="<?php echo $valor; ?>"><?php echo $texto; ?></option>
                              <?php endforeach; ?>
                           </select>
                        </div>

                        <div class="form-group">
                           <label for="nueva-fecha">Fecha</label>
                           <input type="date" id="nueva-fecha" name="fecha" required>
                        </div>
                        <div class="form-group">
                           <label for="nueva-hora">Hora</label>
                           <input type="time" id="nueva-hora" name="hora">
                        </div>
                     </div>

                     <div class="form-actions">
                        <button class="button-primary" type="submit">Agregar actividad</button>
                     </div>
                  </form>
               <?php endif; ?>

               <?php if ($actividades === []): ?>
                  <div class="section-placeholder">
                     <i class="fa-solid fa-calendar-days" aria-hidden="true"></i>
                     <h2>Cronograma</h2>
                     <p>Este torneo todavía no tiene actividades programadas.</p>
                  </div>
               <?php else: ?>
                  <div class="cronograma-lista">
                     <?php foreach ($actividadesPorDia as $fecha => $delDia): ?>
                        <section class="cronograma-dia">

                           <div class="cronograma-dia__fecha">
                              <span><?php echo $escapar($fechaCorta($fecha)); ?></span>
                           </div>

                           <div class="cronograma-dia__lista">
                              <?php foreach ($delDia as $actividad): ?>
                                 <article class="cronograma-evento">

                                    <div class="cronograma-evento__hora">
                                       <span><?php echo $actividad['hora'] ? $escapar(substr($actividad['hora'], 0, 5)) : 'Sin hora'; ?></span>
                                    </div>

                                    <div class="cronograma-evento__contenido">

                                       <div class="cronograma-evento__encabezado">
                                          <div>
                                             <span class="cronograma-tipo cronograma-tipo--<?php echo $escapar($actividad['tipo']); ?>">
                                                <?php echo $escapar($actividad['tipo']); ?>
                                             </span>
                                             <h3><?php echo $escapar($actividad['titulo']); ?></h3>
                                          </div>

                                          <span class="cronograma-estado">
                                             <?php echo $actividad['fecha'] < date('Y-m-d') ? 'Realizada' : 'Pendiente'; ?>
                                          </span>
                                       </div>

                                        <?php if (!empty($actividad['descripcion'])): ?>
                                          <p><?php echo $escapar($actividad['descripcion']); ?></p>
                                       <?php endif; ?>

                                       <div class="cronograma-evento__datos">
                                          <span>
                                             <i class="fa-solid fa-location-dot"></i>
                                             <?php echo $escapar($ubicacionActividad); ?>
                                          </span>
                                       </div>

                                       <?php if ($puedeEditarCronograma): ?>
                                          <div class="cronograma-evento__acciones">
                                             <button class="fixture-programar-boton" type="button"
                                                onclick="document.getElementById('actividad-<?php echo (int) $actividad['id']; ?>').showModal()">
                                                <i class="fa-solid fa-pen"></i>
                                                Editar
                                             </button>
                                             <form method="post" action="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=eliminarActividad"
                                                onsubmit="return confirm('¿Eliminar esta actividad?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                                                <input type="hidden" name="torneo_id" value="<?php echo (int) $torneo['id']; ?>">
                                                <input type="hidden" name="actividad_id" value="<?php echo (int) $actividad['id']; ?>">
                                                <button class="participante-btn participante-btn-remove" type="submit">
                                                   <i class="fa-solid fa-trash"></i>Eliminar
                                                </button>
                                             </form>
                                          </div>

                                          <dialog class="programar-modal" id="actividad-<?php echo (int) $actividad['id']; ?>">
                                             <form class="edit-form" method="post" action="<?php echo URL_BASE; ?>
                                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                                             <input type="hidden" name="torneo_id" value="<?php echo (int) $torneo['id']; ?>">
                                                <input type="hidden" name="actividad_id" value="<?php echo (int) $actividad['id']; ?>">

                                                <div class="edit-form__grid">
                                                   <div class="form-group form-group--full">
                                                      <label for="titulo-<?php echo (int) $actividad['id']; ?>">Título</label>
                                                      <input type="text" id="titulo-<?php echo (int) $actividad['id']; ?>" name="titulo" maxlength="150" required
                                                         value="<?php echo $escapar($actividad['titulo']); ?>">
                                                   </div>

                                                    <div class="form-group form-group--full">
                                                      <label for="descripcion-<?php echo (int) $actividad['id']; ?>">Descripción (opcional)</label>
                                                      <textarea id="descripcion-<?php echo (int) $actividad['id']; ?>" name="descripcion" rows="2" maxlength="255"><?php echo $escapar($actividad['descripcion'] ?? ''); ?></textarea>
                                                   </div>

                                                   <div class="form-group">
                                                      <label for="tipo-<?php echo (int) $actividad['id']; ?>">Tipo</label>
                                                      <select id="tipo-<?php echo (int) $actividad['id']; ?>" name="tipo" required>
                                                         <?php foreach ($tiposActividad as $valor => $texto): ?>
                                                            <option value="<?php echo $valor; ?>" <?php echo $actividad['tipo'] === $valor ? 'selected' : ''; ?>><?php echo $texto; ?></option>
                                                         <?php endforeach; ?>
                                                      </select>
                                                   </div>
                                                   <div class="form-group">
                                                      <label for="fecha-act-<?php echo (int) $actividad['id']; ?>">Fecha</label>
                                                      <input type="date" id="fecha-act-<?php echo (int) $actividad['id']; ?>" name="fecha" required
                                                         value="<?php echo $escapar($actividad['fecha']); ?>">
                                                   </div>
                                                   <div class="form-group">
                                                      <label for="hora-act-<?php echo (int) $actividad['id']; ?>">Hora</label>
                                                      <input type="time" id="hora-act-<?php echo (int) $actividad['id']; ?>" name="hora"
                                                         value="<?php echo $actividad['hora'] ? $escapar(substr($actividad['hora'], 0, 5)) : ''; ?>">
                                                   </div>
                                                </div>

                                                <div class="form-actions">
                                                   <button class="button-primary" type="submit">Guardar</button>
                                                   <button class="button-secondary" type="button" onclick="this.closest('dialog').close()">Cancelar</button>
                                                </div>
                                             </form>
                                          </dialog>
                                       <?php endif; ?>

                                    </div>

                                 </article>
                              <?php endforeach; ?>
                           </div>

                        </section>
                     <?php endforeach; ?>
                  </div>
               <?php endif; ?>

            </div>

            <?php elseif ($seccion === 'fixture'): ?>

            <?php
            $fixture = $datos['fixture'];
            $rondas = [];
            foreach ($fixture as $partido) {
                $rondas[(int) $partido['ronda']][] = $partido;
            }
            ksort($rondas);
            $rondaActiva = (int) ($_GET['ronda'] ?? 0);
            if (!isset($rondas[$rondaActiva])) {
                $rondaActiva = $rondas ? array_key_first($rondas) : 0;
            }
            $partidosPrimeraRonda = $rondas ? count(reset($rondas)) : 0;
            $etiquetaRonda = static function (int $ronda) use ($torneo, $partidosPrimeraRonda, $nombresRondaEliminacion): string {
                if ($torneo['formato'] === 'eliminacion_directa') {
                    $enRonda = (int) ($partidosPrimeraRonda * 2 / (2 ** ($ronda - 1)));
                    return $nombresRondaEliminacion[$enRonda] ?? 'Ronda de ' . $enRonda;
                }
                return ($torneo['formato'] === 'liga' ? 'Fecha ' : 'Ronda ') . $ronda;
            };
            $hayResultados = (bool) array_filter($fixture, static fn ($partido) => $partido['estado'] === 'finalizado');
            $torneoAbierto = in_array($torneo['estado'], ['inscripciones_abiertas', 'en_curso'], true);
            $puedeGenerar = $torneoAbierto && !$hayResultados;
            $puedeProgramar = $torneoAbierto;
            ?>

            <div class="fixture-layout">

               <div class="fixture-acciones">
                  <div>
                     <span class="fixture-summary-label">Gestión del fixture</span>
                     <p>
                        <?php
                        if ($hayResultados) {
                            echo 'El fixture no se puede regenerar porque ya hay resultados registrados.';
                        } elseif (!$torneoAbierto) {
                            echo 'El fixture solo se puede generar mientras el torneo está abierto o en curso.';
                        } elseif ($torneo['formato'] === 'eliminacion_directa') {
                            echo 'Generá automáticamente las llaves con los participantes confirmados.';
                        } elseif ($torneo['formato'] === 'liga') {
                            echo 'Generá todas las fechas: cada participante juega contra todos los demás.';
                        } else {
                            echo 'Generá la primera ronda; las siguientes se arman al cargar los resultados.';
                        }
                        ?>
                     </p>
                  </div>

                  <?php if ($puedeGenerar): ?>
                     <form method="post" action="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=generarFixture"
                        onsubmit="return confirm('<?php echo $fixture ? 'Esto reemplazará el fixture actual. ¿Querés continuar?' : '¿Querés generar automáticamente el fixture?'; ?>');">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="torneo_id" value="<?php echo (int) $torneo['id']; ?>">
                        <button class="fixture-generar-boton" type="submit">
                           <i class="fa-solid fa-wand-magic-sparkles"></i>
                           <?php echo $fixture ? 'Regenerar fixture' : 'Generar fixture'; ?>
                        </button>
                     </form>
                  <?php endif; ?>
               </div>

               <div class="fixture-summary-panel">
                  <div>
                     <span class="fixture-summary-label">Fixture del torneo</span>
                     <h2><?php echo $escapar($torneo['nombre']); ?></h2>
                     <p><?php echo $formatearFecha($torneo['fecha_inicio']); ?> — <?php echo $formatearFecha($torneo['fecha_fin']); ?></p>
                  </div>

                  <div class="fixture-summary-boxes">
                     <div class="fixture-summary-item">
                        <span><?php echo count($rondas); ?></span>
                        <p><?php echo $torneo['formato'] === 'liga' ? 'Fechas' : 'Rondas'; ?></p>
                     </div>
                     <div class="fixture-summary-item">
                        <span><?php echo count($fixture); ?></span>
                        <p>Partidos</p>
                     </div>
                  </div>
               </div>

               <?php if ($rondas === []): ?>
                  <div class="section-placeholder">
                     <i class="fa-solid fa-sitemap" aria-hidden="true"></i>
                     <h2>Fixture</h2>
                     <p>Este torneo todavía no tiene rondas ni partidos configurados.</p>
                  </div>
               <?php else: ?>

                  <div class="fixture-rounds">
                     <?php foreach (array_keys($rondas) as $numeroRonda): ?>
                        <a class="fixture-round-button <?php echo $numeroRonda === $rondaActiva ? 'active' : ''; ?>"
                           href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=administrar&seccion=fixture&id=<?php echo (int) $torneo['id']; ?>&ronda=<?php echo $numeroRonda; ?>">
                           <?php echo $escapar($etiquetaRonda($numeroRonda)); ?>
                        </a>
                     <?php endforeach; ?>
                  </div>

                  <div class="fixture-match-list">
                     <?php foreach ($rondas[$rondaActiva] as $partido): ?>
                        <?php
                        $marca = $partido['fecha_hora_programada'] ? strtotime($partido['fecha_hora_programada']) : null;
                        $sede = $partido['cancha'] ?: $ubicacionActividad;
                        $puedeProgramarEste = $puedeProgramar && in_array($partido['estado'], ['programado', 'aplazado'], true)
                            && $partido['participante_local_id'] && $partido['participante_visitante_id'];
                        ?>
                        <article class="fixture-card">
                           <div class="fixture-card-main">

                              <div class="fixture-card-title">
                                 <h3>
                                    <?php echo $escapar($partido['local_nombre'] ?? 'Por definir'); ?>
                                    <span>vs</span>
                                    <?php echo $escapar($partido['visitante_nombre'] ?? 'Por definir'); ?>
                                 </h3>
                                 <span class="fixture-card-badge"><?php echo $escapar($estadosPartido[$partido['estado']] ?? $partido['estado']); ?></span>
                              </div>

                              <div class="fixture-tarjeta-info">
                                 <p><i class="fa-solid fa-calendar-day"></i><strong>Fecha:</strong> <?php echo $marca ? date('d/m/Y', $marca) : 'A definir'; ?></p>
                                 <p><i class="fa-solid fa-clock"></i><strong>Hora:</strong> <?php echo $marca ? date('H:i', $marca) : 'A definir'; ?></p>
                                 <p><i class="fa-solid fa-location-dot"></i><strong>Sede:</strong> <?php echo $escapar($sede); ?></p>
                              </div>

                              <?php if ($puedeProgramarEste): ?>
                                 <div class="fixture-card-acciones">
                                    <button class="fixture-programar-boton" type="button"
                                       onclick="document.getElementById('programar-<?php echo (int) $partido['id']; ?>').showModal()">
                                       <i class="fa-solid fa-calendar-plus"></i>
                                       <?php echo $marca ? 'Reprogramar' : 'Programar'; ?>
                                    </button>
                                 </div>

                                 <dialog class="programar-modal" id="programar-<?php echo (int) $partido['id']; ?>">
                                    <form class="edit-form" method="post" action="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=programarEncuentro">
                                       <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                                       <input type="hidden" name="torneo_id" value="<?php echo (int) $torneo['id']; ?>">
                                       <input type="hidden" name="encuentro_id" value="<?php echo (int) $partido['id']; ?>">
                                       <input type="hidden" name="ronda" value="<?php echo (int) $partido['ronda']; ?>">

                                       <h3><?php echo $escapar(($partido['local_nombre'] ?? '') . ' vs ' . ($partido['visitante_nombre'] ?? '')); ?></h3>

                                       <div class="edit-form__grid">
                                          <div class="form-group">
                                             <label for="fecha-<?php echo (int) $partido['id']; ?>">Fecha</label>
                                             <input type="date" id="fecha-<?php echo (int) $partido['id']; ?>" name="fecha" required
                                                value="<?php echo $marca ? date('Y-m-d', $marca) : ''; ?>"
                                                <?php echo $torneo['fecha_inicio'] ? 'min="' . $escapar($torneo['fecha_inicio']) . '"' : ''; ?>
                                                <?php echo $torneo['fecha_fin'] ? 'max="' . $escapar($torneo['fecha_fin']) . '"' : ''; ?>>
                                          </div>
                                          <div class="form-group">
                                             <label for="hora-<?php echo (int) $partido['id']; ?>">Hora</label>
                                             <input type="time" id="hora-<?php echo (int) $partido['id']; ?>" name="hora" required
                                                value="<?php echo $marca ? date('H:i', $marca) : ''; ?>">
                                          </div>
                                          <div class="form-group form-group--full">
                                             <label for="sede-<?php echo (int) $partido['id']; ?>">Sede</label>
                                             <input type="text" id="sede-<?php echo (int) $partido['id']; ?>" name="sede" maxlength="100"
                                                value="<?php echo $escapar($partido['cancha'] ?? ''); ?>"
                                                placeholder="<?php echo $escapar($ubicacionActividad); ?>">
                                             <span class="form-help">Si la dejás vacía se usa la ubicación del torneo.</span>
                                          </div>
                                       </div>

                                       <div class="form-actions">
                                          <button class="button-primary" type="submit">Guardar</button>
                                          <button class="button-secondary" type="button" onclick="this.closest('dialog').close()">Cancelar</button>
                                       </div>
                                    </form>
                                 </dialog>
                              <?php endif; ?>

                           </div>
                        </article>
                     <?php endforeach; ?>
                  </div>

               <?php endif; ?>

            </div>

            
            <?php elseif ($seccion === 'resultados'): ?>

            <?php
            $fixture = $datos['fixture'];
            $rondas = [];
            foreach ($fixture as $partido) {
                $rondas[(int) $partido['ronda']][] = $partido;
            }
            ksort($rondas);
            $rondaActiva = (int) ($_GET['ronda'] ?? 0);
            if (!isset($rondas[$rondaActiva])) {
                $rondaActiva = $rondas ? array_key_first($rondas) : 0;
            }
            $partidosPrimeraRonda = $rondas ? count(reset($rondas)) : 0;
            $etiquetaRonda = static function (int $ronda) use ($torneo, $partidosPrimeraRonda, $nombresRondaEliminacion): string {
                if ($torneo['formato'] === 'eliminacion_directa') {
                    $enRonda = (int) ($partidosPrimeraRonda * 2 / (2 ** ($ronda - 1)));
                    return $nombresRondaEliminacion[$enRonda] ?? 'Ronda de ' . $enRonda;
                }
                return ($torneo['formato'] === 'liga' ? 'Fecha ' : 'Ronda ') . $ronda;
            };
            $torneoEnCurso = $torneo['estado'] === 'en_curso';
            $cantidadFinalizados = count(array_filter($fixture, static fn ($partido) => $partido['estado'] === 'finalizado'));
            ?>

            <div class="fixture-layout">

               <div class="fixture-acciones">
                  <div>
                     <span class="fixture-summary-label">Carga de resultados</span>
                     <p>
                        <?php
                        if ($torneoEnCurso) {
                            echo 'Cargá el marcador de cada partido. Una vez guardado, el resultado no se puede modificar.';
                        } elseif ($torneo['estado'] === 'finalizado') {
                            echo 'El torneo finalizó. Estos son los resultados registrados.';
                        } elseif ($torneo['estado'] === 'cancelado') {
                            echo 'El torneo fue cancelado.';
                        } else {
                            echo 'Los resultados se pueden cargar cuando el torneo esté en curso. Iniciá el torneo desde los botones de arriba.';
                        }
                        ?>
                     </p>
                  </div>
               </div>

               <div class="fixture-summary-panel">
                  <div>
                     <span class="fixture-summary-label">Resultados del torneo</span>
                     <h2><?php echo $escapar($torneo['nombre']); ?></h2>
                  </div>

                  <div class="fixture-summary-boxes">
                     <div class="fixture-summary-item">
                        <span><?php echo $cantidadFinalizados; ?> / <?php echo count($fixture); ?></span>
                        <p>Partidos jugados</p>
                     </div>
                  </div>
               </div>

               <?php if ($rondas === []): ?>
                  <div class="section-placeholder">
                     <i class="fa-solid fa-flag-checkered" aria-hidden="true"></i>
                     <h2>Resultados</h2>
                     <p>Todavía no hay partidos. Generá el fixture primero.</p>
                  </div>
               <?php else: ?>

                  <div class="fixture-rounds">
                     <?php foreach (array_keys($rondas) as $numeroRonda): ?>
                        <a class="fixture-round-button <?php echo $numeroRonda === $rondaActiva ? 'active' : ''; ?>"
                           href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=administrar&seccion=resultados&id=<?php echo (int) $torneo['id']; ?>&ronda=<?php echo $numeroRonda; ?>">
                           <?php echo $escapar($etiquetaRonda($numeroRonda)); ?>
                        </a>
                     <?php endforeach; ?>
                  </div>

                  <div class="fixture-match-list">
                     <?php foreach ($rondas[$rondaActiva] as $partido): ?>
                        <?php
                        $marca = $partido['fecha_hora_programada'] ? strtotime($partido['fecha_hora_programada']) : null;
                        $ambosDefinidos = $partido['participante_local_id'] && $partido['participante_visitante_id'];
                        $puedeCargar = $torneoEnCurso && $ambosDefinidos && $partido['estado'] !== 'finalizado';
                        ?>
                        <article class="fixture-card">
                           <div class="fixture-card-main">

                              <div class="fixture-card-title">
                                 <h3>
                                    <?php echo $escapar($partido['local_nombre'] ?? 'Por definir'); ?>
                                    <span>vs</span>
                                    <?php echo $escapar($partido['visitante_nombre'] ?? 'Por definir'); ?>
                                 </h3>
                                 <span class="fixture-card-badge"><?php echo $escapar($estadosPartido[$partido['estado']] ?? $partido['estado']); ?></span>
                              </div>

                              <div class="fixture-tarjeta-info">
                                 <p><i class="fa-solid fa-calendar-day"></i><strong>Fecha:</strong> <?php echo $marca ? date('d/m/Y', $marca) : 'A definir'; ?></p>
                                 <p><i class="fa-solid fa-clock"></i><strong>Hora:</strong> <?php echo $marca ? date('H:i', $marca) : 'A definir'; ?></p>
                              </div>

                              <?php if ($partido['estado'] === 'finalizado'): ?>
                                 <p class="marcador-final">
                                    <i class="fa-solid fa-flag-checkered"></i>
                                    Resultado:
                                    <strong><?php echo $formatoMarcador($partido['resultado_local']); ?> - <?php echo $formatoMarcador($partido['resultado_visitante']); ?></strong>
                                 </p>
                              <?php elseif ($puedeCargar): ?>
                                 <form class="marcador-form" method="post"
                                 
                                                                     action="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=guardarResultado"
                                    onsubmit="return confirm('¿Guardar el resultado? No se podrá modificar después.');">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="torneo_id" value="<?php echo (int) $torneo['id']; ?>">
                                    <input type="hidden" name="encuentro_id" value="<?php echo (int) $partido['id']; ?>">
                                    <input type="hidden" name="ronda" value="<?php echo (int) $partido['ronda']; ?>">

                                    <input type="number" name="resultado_local" min="0" step="any" required
                                       aria-label="Resultado de <?php echo $escapar($partido['local_nombre']); ?>">
                                    <span>-</span>
                                    <input type="number" name="resultado_visitante" min="0" step="any" required
                                       aria-label="Resultado de <?php echo $escapar($partido['visitante_nombre']); ?>">

                                    <button class="fixture-programar-boton" type="submit">
                                       <i class="fa-solid fa-check"></i>
                                       Guardar resultado
                                    </button>
                                 </form>
                              <?php endif; ?>

                           </div>
                        </article>
                     <?php endforeach; ?>
                  </div>

               <?php endif; ?>

            </div>

            
            <?php elseif ($seccion === 'posiciones'): ?>

            <?php
            $posiciones = $datos['posiciones'];
            $podio = $datos['podio'];
            $esEliminacion = $torneo['formato'] === 'eliminacion_directa';
            ?>

            <div class="fixture-layout">

               <div class="fixture-acciones">
                  <div>
                     <span class="fixture-summary-label">Clasificación</span>
                     <h2 class="panel-title">Posiciones</h2>
                     <p class="panel-description">
                        <?php echo $esEliminacion
                           ? 'En eliminación directa no hay tabla: el avance se ve en las llaves y el podio se define en la final.'
                           : 'La tabla se actualiza automáticamente al cargar cada resultado.'; ?>
                     </p>
                  </div>
               </div>

               <?php if (!$esEliminacion): ?>
                  <?php if ($posiciones === []): ?>
                     <div class="section-placeholder">
                        <i class="fa-solid fa-ranking-star" aria-hidden="true"></i>
                        <h2>Posiciones</h2>
                        <p>Todavía no hay resultados cargados.</p>
                     </div>
                  <?php else: ?>
                     <div class="posiciones-tabla-contenedor">
                        <table class="posiciones-tabla">
                           <thead>
                              <tr>
                                 <th>#</th>
                                 <th class="posiciones-nombre">Participante</th>
                                 <th title="Partidos jugados">PJ</th>
                                 <th title="Ganados">G</th>
                                 <th title="Empatados">E</th>
                                 <th title="Perdidos">P</th>
                                 <th title="A favor">GF</th>
                                 <th title="En contra">GC</th>
                                 <th title="Diferencia">DG</th>
                                 <th title="Puntos">Pts</th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php foreach ($posiciones as $indice => $fila): ?>
                                 <tr class="<?php echo $indice < 3 ? 'posiciones-top' : ''; ?>">
                                    <td><?php echo $indice + 1; ?></td>
                                    <td class="posiciones-nombre"><?php echo $escapar($fila['nombre']); ?></td>
                                    <td><?php echo (int) $fila['partidos_jugados']; ?></td>
                                    <td><?php echo (int) $fila['partidos_ganados']; ?></td>
                                    <td><?php echo (int) $fila['partidos_empatados']; ?></td>
                                    <td><?php echo (int) $fila['partidos_perdidos']; ?></td>
                                    <td><?php echo $formatoMarcador($fila['puntos_favor']); ?></td>
                                    <td><?php echo $formatoMarcador($fila['puntos_contra']); ?></td>
                                    <td><?php echo $formatoMarcador((float) $fila['puntos_favor'] - (float) $fila['puntos_contra']); ?></td>
                                    <td><strong><?php echo $formatoMarcador($fila['puntos']); ?></strong></td>
                                 </tr>
                              <?php endforeach; ?>
                           </tbody>
                        </table>
                     </div>
                  <?php endif; ?>
               <?php endif; ?>

               <?php if ($podio): ?>
                  <div class="podio-panel">
                     <span class="fixture-summary-label">Podio final</span>
                     <ol class="podio-lista">
                        <li><i class="fa-solid fa-trophy"></i><span>1.º</span><strong><?php echo $escapar($podio['primero'] ?? '—'); ?></strong></li>
                        <li><i class="fa-solid fa-medal"></i><span>2.º</span><strong><?php echo $escapar($podio['segundo'] ?? '—'); ?></strong></li>
                        <li><i class="fa-solid fa-medal"></i><span>3.º</span><strong><?php echo $escapar($podio['tercero'] ?? '—'); ?></strong></li>
                     </ol>
                  </div>
               <?php elseif ($esEliminacion): ?>
                  <div class="section-placeholder">
                     <i class="fa-solid fa-trophy" aria-hidden="true"></i>
                     <h2>Podio</h2>
                     <p>El podio se define cuando el torneo se finaliza.</p>
                  </div>
               <?php endif; ?>

            </div>

            
            <?php elseif ($seccion === 'editar'): ?>

            <?php
            $estadoTorneo = $torneo['estado'];
            $torneoEditable = !in_array($estadoTorneo, ['finalizado', 'cancelado'], true);
            $fechasLibres = in_array($estadoTorneo, ['borrador', 'inscripciones_abiertas'], true);
            $premiosActuales = json_decode((string) ($torneo['premios'] ?? ''), true);
            $premiosActuales = is_array($premiosActuales) ? $premiosActuales : [];
            $bannerActual = (string) ($torneo['banner_url'] ?? '');
            $bannerActualUrl = $bannerActual === '' ? '' : (preg_match('#^https?://#i', $bannerActual) ? $bannerActual : URL_BASE . ltrim($bannerActual, '/'));
            ?>

            <div class="cronograma-layout">

               <div class="cronograma-encabezado">
                  <div>
                     <span class="fixture-summary-label">Configuración</span>
                     <h2 class="panel-title">Editar torneo</h2>
                     <p class="panel-description">
                        <?php
                        if (!$torneoEditable) {
                            echo 'Un torneo finalizado o cancelado no se puede editar.';
                        } elseif ($estadoTorneo === 'en_curso') {
                            echo 'El torneo ya comenzó: podés actualizar los datos generales y la fecha de finalización.';
                        } else {
                            echo 'Modificá los datos del torneo. Las fechas deben respetar el orden de cada etapa.';
                        }
                        ?>
                     </p>
                  </div>
               </div>

               <div class="information-grid">
                  <article class="information-card">
                     <span>Disciplina</span>
                     <strong><?php echo $escapar($torneo['nombre_juego']); ?></strong>
                  </article>
                  <article class="information-card">
                     <span>Modalidad</span>
                     <strong><?php echo $escapar($datos['nombreModalidad']); ?></strong>
                  </article>
                  <article class="information-card">
                     <span>Sistema de competencia</span>
                     <strong><?php echo $escapar($etiquetasFormato[$torneo['formato']] ?? $torneo['formato']); ?></strong>
                  </article>
                  <article class="information-card">
                     <span>Cupo</span>
                     <strong><?php echo (int) $torneo['cupo_max_equipos']; ?></strong>
                  </article>
               </div>
               <p class="form-help">Disciplina, modalidad, sistema y cupo no se pueden modificar una vez creado el torneo.</p>

               <?php if ($torneoEditable): ?>
                  <form class="edit-form cronograma-form" method="post" enctype="multipart/form-data"
                                       action="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=editarTorneo">
                     <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                     <input type="hidden" name="torneo_id" value="<?php echo (int) $torneo['id']; ?>">

                     <div class="edit-form__grid">

                        <div class="form-group form-group--full">
                           <label for="editar-descripcion">Descripción</label>
                           <textarea id="editar-descripcion" name="descripcion" rows="4"><?php echo $escapar($torneo['descripcion'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-group form-group--full">
                           <label for="editar-reglas">Reglamento</label>
                           <textarea id="editar-reglas" name="reglas" rows="5"><?php echo $escapar($torneo['reglas'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-group form-group--full">
                           <label for="editar-comunidad">Organización / comunidad</label>
                           <input type="text" id="editar-comunidad" name="comunidad" maxlength="100" value="<?php echo $escapar($torneo['comunidad'] ?? ''); ?>">
                        </div>

                        <?php foreach ([1 => 'Primer lugar', 2 => 'Segundo lugar', 3 => 'Tercer lugar'] as $puesto => $etiquetaPuesto): ?>
                           <div class="form-group">
                              <label for="editar-premio-<?php echo $puesto; ?>"><?php echo $etiquetaPuesto; ?></label>
                              <input type="text" id="editar-premio-<?php echo $puesto; ?>" name="premio_<?php echo $puesto; ?>" maxlength="60"
                                 value="<?php echo $escapar($premiosActuales[$puesto - 1] ?? ''); ?>" placeholder="Opcional">
                           </div>
                        <?php endforeach; ?>

                        <div class="form-group form-group--full">
                           <label for="editar-stream">Link de transmisión</label>
                           <input type="url" id="editar-stream" name="stream_url" maxlength="255" placeholder="https://..."
                              value="<?php echo $escapar($torneo['stream_url'] ?? ''); ?>">
                        </div>

                        <div class="form-group form-group--full">
                           <label for="editar-banner">Banner</label>
                           <input type="file" id="editar-banner" name="banner" accept="image/jpeg,image/png,image/webp">
                           <span class="form-help">JPG, PNG o WEBP de hasta 2 MB. Si no subís uno nuevo se conserva el actual.</span>
                           <?php if ($bannerActualUrl !== ''): ?>
                              <img class="editar-banner-actual" src="<?php echo $escapar($bannerActualUrl); ?>" alt="Banner actual del torneo">
                           <?php endif; ?>
                        </div>

                        <div class="form-group">
                           <label for="editar-insc-inicio">Inicio de inscripciones</label>
                           <input type="date" id="editar-insc-inicio" name="fecha_inicio_inscripcion" required
                              value="<?php echo $escapar($torneo['fecha_inicio_inscripcion']); ?>" <?php echo $fechasLibres ? '' : 'disabled'; ?>>
                        </div>
                        <div class="form-group">
                           <label for="editar-insc-fin">Cierre de inscripciones</label>
                           <input type="date" id="editar-insc-fin" name="fecha_limite_inscripcion" required
                              value="<?php echo $escapar($torneo['fecha_limite_inscripcion']); ?>" <?php echo $fechasLibres ? '' : 'disabled'; ?>>
                        </div>
                        <div class="form-group">
                           <label for="editar-inicio">Inicio del torneo</label>
                           <input type="date" id="editar-inicio" name="fecha_inicio" required
                              value="<?php echo $escapar($torneo['fecha_inicio']); ?>" <?php echo $fechasLibres ? '' : 'disabled'; ?>>
                        </div>
                        <div class="form-group">
                           <label for="editar-fin">Fin del torneo</label>
                           <input type="date" id="editar-fin" name="fecha_fin" required
                              value="<?php echo $escapar($torneo['fecha_fin']); ?>"
                              <?php echo $estadoTorneo === 'en_curso' ? 'min="' . $escapar($torneo['fecha_inicio']) . '"' : ''; ?>>
                           <?php if ($estadoTorneo === 'en_curso'): ?>
                              <span class="form-help">Es la única fecha que se puede cambiar con el torneo en curso.</span>
                           <?php endif; ?>
                        </div>

                     </div>

                     <div class="form-actions">
                        <button class="button-primary" type="submit">Guardar cambios</button>
                     </div>
                  </form>
               <?php endif; ?>

            </div>

         <?php endif; ?>

      </section>

   </main>

   <script>
      window.ADMINISTRAR_URL = "<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=administrar&id=<?php echo (int) $torneo['id']; ?>";
   </script>
   <script src="<?php echo URL_BASE; ?>js/organizador/administrarMenu.js"></script>
   <?php include __DIR__ . '/../componentes/footer.html'; ?>

</body>
</html>