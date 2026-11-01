<?php

/**
 * Vista: perfil-equipo.php
 * Requiere que vengan cargados desde EquipoControlador::verPerfil():
 * $equipo (array de la tabla equipos), $capitanes, $miembros, $yaEsMiembro (bool)
 *
 * Sin JavaScript por ahora: es una pantalla de lectura. El boton "Solicitar Unirse"
 * todavia no esta conectado (se resuelve en el Paso 3.5).
 */

$nombreEquipo = $equipo['nombre_equipo'];
$descripcion = $equipo['descripcion'] ?: 'Este equipo todavía no cargó una descripción.';
$ubicacion = $equipo['ubicacion'] ?: 'Sin especificar';
$anioFundacion = $equipo['anio_fundacion'] ?: 'Sin especificar';

//Las rutas de imagen se guardan relativas a publico/ (ej: img/equipos/subidos/ab12.png)
$urlImagenEquipo = static function (?string $ruta, string $porDefecto): string {
    $ruta = trim((string) $ruta);
    if ($ruta === '') {
        return URL_BASE . $porDefecto;
    }
    return preg_match('#^(https?:)?//#i', $ruta) ? $ruta : URL_BASE . ltrim($ruta, '/');
};

$escudoUrl = $urlImagenEquipo($equipo['escudo_url'] ?? '', 'img/logos/canelones.png');
$bannerUrl = $urlImagenEquipo($equipo['banner_url'] ?? '', 'img/live.jpg');

//Mismas etiquetas que usa el catalogo publico de torneos
$etiquetasFormatoTorneo = [
   'liga' => 'Liga',
   'suizo' => 'Sistema suizo',
   'eliminacion_directa' => 'Eliminación directa',
];
$etiquetasEstadoTorneoEquipo = [
   'borrador' => 'Borrador',
   'inscripciones_abiertas' => 'Inscripciones abiertas',
   'en_curso' => 'En curso',
   'finalizado' => 'Finalizado',
   'cancelado' => 'Cancelado',
];
$etiquetasInscripcionEquipo = [
   'pendiente' => 'Inscripción pendiente',
   'confirmado' => 'Inscripción confirmada',
   'rechazado' => 'Inscripción rechazada',
   'cancelado' => 'Participación cancelada',
];

//Formato compacto para puntos y diferencias (2, 2.5, -1...)
$numeroMarcador = static fn ($valor): string => rtrim(rtrim(number_format((float) $valor, 2, '.', ''), '0'), '.');

$torneosEquipo = $torneosEquipo ?? [];
$posicionesEquipo = $posicionesEquipo ?? [];
?>
<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>ASCEND - <?php echo htmlspecialchars($nombreEquipo); ?></title>

   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/navbar.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/footer.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/jugador/perfil-equipo.css">

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>

   <!-- NAVBAR ---------------------------->
<?php
if ($rolId === 3) {
    include __DIR__ . '/../componentes/navbar-jugador.php';
} elseif ($rolId === 2) {
    include __DIR__ . '/../componentes/navbar-organizador.php';
} else {
    include __DIR__ . '/../componentes/navbar-publico.php';
}
?>
   <main class="equipo-pagina">

      <?php if (isset($_GET['solicitudEnviada'])): ?>
         <div class="mensaje-exito">Tu solicitud fue enviada. El capitán la va a revisar.</div>
      <?php endif; ?>

      <?php if (isset($_GET['creado'])): ?>
         <div class="aviso-equipo-union" role="status">
            Equipo creado correctamente. Compartí el código de invitación desde "Gestionar equipo" para sumar jugadores.
         </div>
      <?php endif; ?>

      <?php if (($_GET['union'] ?? '') === 'ok'): ?>
         <div class="aviso-equipo-union" role="status">
            Ingresaste al equipo con el código de invitación. Ya figurás entre sus miembros.
         </div>
      <?php elseif (($_GET['union'] ?? '') === 'ya'): ?>
         <div class="aviso-equipo-union" role="status">
            Ya pertenecías a este equipo, así que no hubo cambios.
         </div>
      <?php endif; ?>

      <!-- ============================== -->
      <!-- HEADER: banner, logo, datos    -->
      <!-- ============================== -->
      <header class="equipo-hero">

         <div class="equipo-hero-banner">
            <img src="<?php echo htmlspecialchars($bannerUrl); ?>" alt="Banner del equipo">
         <?php if ($esJugador): ?>
               <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=guardados&a=<?php echo $equipoGuardado ? 'quitar' : 'guardar'; ?>" class="form-guardar-equipo">
               <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
               <input type="hidden" name="tipo" value="equipo">
               <input type="hidden" name="referencia_id" value="<?php echo (int) $equipo['id']; ?>">
               <input type="hidden" name="volver_a" value="<?php echo URL_BASE; ?>index.php?c=equipo&a=verPerfil&id=<?php echo (int) $equipo['id']; ?>">
               <button type="submit" class="boton-guardar-equipo <?php echo $equipoGuardado ? 'guardado' : ''; ?>" title="<?php echo $equipoGuardado ? 'Quitar de guardados' : 'Guardar equipo'; ?>">
                  <i class="<?php echo $equipoGuardado ? 'fa-solid' : 'fa-regular'; ?> fa-bookmark"></i>
               </button>
            </form>
           <?php endif; ?>

         </div>

         <div class="equipo-identidad">
            <div class="equipo-logo">
               <img src="<?php echo htmlspecialchars($escudoUrl); ?>" alt="Escudo del equipo">
            </div>

            <div class="equipo-info-texto">
               <h1 class="equipo-nombre"><?php echo htmlspecialchars($nombreEquipo); ?></h1>

               <p class="equipo-descripcion"><?php echo htmlspecialchars($descripcion); ?></p>

               <ul class="equipo-datos">
                  <li class="equipo-dato">
                     <i class="fa-solid fa-location-dot"></i>
                     <span><?php echo htmlspecialchars($ubicacion); ?></span>
                  </li>
                  <li class="equipo-dato">
                     <i class="fa-solid fa-calendar"></i>
                     <span>Fundado en <?php echo htmlspecialchars($anioFundacion); ?></span>
                  </li>
               </ul>
            </div>
         </div>

         <!-- Pestañas del perfil: Miembros, Torneos y Ranking (datos reales de la BD) -->
         <nav class="equipo-tabs" aria-label="Secciones del equipo" role="tablist">
            <ul class="equipo-tabs-lista">
               <li><button type="button" id="tab-miembros" class="equipo-tab active" data-tab="miembros" role="tab" aria-selected="true" aria-controls="miembros">Miembros</button></li>
               <li><button type="button" id="tab-torneos" class="equipo-tab" data-tab="torneos" role="tab" aria-selected="false" aria-controls="panel-torneos">Torneos</button></li>
               <li><button type="button" id="tab-ranking" class="equipo-tab" data-tab="ranking" role="tab" aria-selected="false" aria-controls="panel-ranking">Ranking</button></li>
            </ul>
         </nav>

         <!-- Estadísticas reales del equipo calculadas desde la base de datos -->
         <dl class="equipo-estadisticas">
            <div class="tarjeta-estadistica">
               <dd><?php echo (int) ($estadisticas['partidas'] ?? 0); ?></dd>
               <dt>Partidas</dt>
            </div>
            <div class="tarjeta-estadistica">
               <dd><?php echo (int) ($estadisticas['victorias'] ?? 0); ?></dd>
               <dt>Victorias</dt>
            </div>
            <div class="tarjeta-estadistica">
               <dd><?php echo (int) ($estadisticas['torneos_ganados'] ?? 0); ?></dd>
               <dt>Torneos ganados</dt>
            </div>
         </dl>

      </header>

      <!-- ============================== -->
      <!-- CAPITANES / LÍDERES            -->
      <!-- ============================== -->
      <section class="tarjeta-equipo equipo-capitanes">

         <div class="capitanes-encabezado">
            <h2>Capitanes / Líderes</h2>
         </div>

         <div class="capitanes-fila">
            <ul class="capitanes-lista">
               <?php foreach ($capitanes as $capitan): ?>
                  <li>
                     <article class="capitan-item">
                        <img src="<?php echo htmlspecialchars($capitan['foto_perfil_url'] ?: URL_BASE . 'img/avatars/a1.png'); ?>" alt="Foto del capitán">
                        <span><?php echo htmlspecialchars($capitan['nombre_completo']); ?></span>
                     </article>
                  </li>
               <?php endforeach; ?>
            </ul>

            <?php if ($esJugador): ?>
            <?php if ($yaEsMiembro): ?>
               <span class="ya-eres-miembro">Ya sos parte de este equipo</span>
            <?php elseif ($tieneSolicitudPendiente): ?>
               <span class="ya-eres-miembro">Solicitud enviada, esperando respuesta del capitán</span>
            <?php else: ?>
               <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=equipo&a=solicitarUnion">
                  <input type="hidden"
                     name="csrf_token"
                     value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                  <input type="hidden" name="equipo_id" value="<?php echo (int) $equipo['id']; ?>">
                  <button type="submit" class="boton-solicitar-unirse">Solicitar Unirse</button>
               </form>
            <?php endif; ?>
            <?php endif; ?>

         </div>

      </section>


      <!-- ============================== -->
      <!-- MIEMBROS DEL EQUIPO            -->
      <!-- ============================== -->
      <section class="equipo-miembros" id="miembros" data-panel="miembros" role="tabpanel" aria-labelledby="tab-miembros">

         <h2 class="seccion-gradiente-titulo">Miembros del equipo</h2>

         <form method="GET" action="<?php echo URL_BASE; ?>index.php" class="miembros-buscador">
            <input type="hidden" name="c" value="equipo">
            <input type="hidden" name="a" value="verPerfil">
            <input type="hidden" name="id" value="<?php echo (int) $equipo['id']; ?>">
            <input type="text" name="filtro" placeholder="Buscar entre los miembros..." value="<?php echo htmlspecialchars($filtroMiembro); ?>">
            <button type="submit">Buscar</button>
         </form>

         <?php if (empty($miembros)): ?>
            <p>Este equipo todavía no tiene miembros.</p>
         <?php else: ?>
            <ul class="miembros-grilla">
               <?php foreach ($miembros as $miembro): ?>
                  <li>
                     <a class="enlace-jugador" href="<?php echo URL_BASE; ?>index.php?c=perfil&a=jugador&id=<?php echo (int) $miembro['id']; ?>">
                        <article class="miembro-tarjeta">
                           <div class="miembro-foto">
                              <div class="miembro-fondo"></div>
                              <img src="<?php echo htmlspecialchars($miembro['foto_perfil_url'] ?: URL_BASE . 'img/avatars/a1.png'); ?>" alt="Jugador">
                           </div>
                           <h3><?php echo htmlspecialchars($miembro['nombre_completo']); ?></h3>
                           <?php if (!empty($miembro['posicion'])): ?>
                              <span class="miembro-posicion"><?php echo htmlspecialchars($miembro['posicion']); ?></span>
                           <?php endif; ?>
                        </article>
                     </a>
                  </li>
               <?php endforeach; ?>
            </ul>
         <?php endif; ?>

      </section>

      <!-- ============================== -->
      <!-- PESTAÑA TORNEOS (datos reales) -->
      <!-- ============================== -->
      <section class="tarjeta-equipo equipo-panel" id="panel-torneos" data-panel="torneos" role="tabpanel" aria-labelledby="tab-torneos" hidden>

         <h2>Torneos del equipo</h2>

         <?php if (empty($torneosEquipo)): ?>
            <p class="equipo-panel-vacio">Este equipo todavía no participó de torneos.</p>
         <?php else: ?>
            <ul class="equipo-torneos-lista">
               <?php foreach ($torneosEquipo as $torneoEquipo): ?>
                  <?php
                  $formatoEtiqueta = $etiquetasFormatoTorneo[$torneoEquipo['formato']] ?? ucfirst((string) $torneoEquipo['formato']);
                  $estadoEtiqueta = $etiquetasEstadoTorneoEquipo[$torneoEquipo['torneo_estado']] ?? (string) $torneoEquipo['torneo_estado'];
                  $inscripcionEtiqueta = $etiquetasInscripcionEquipo[$torneoEquipo['estado_inscripcion']] ?? (string) $torneoEquipo['estado_inscripcion'];
                  $fechaInicio = !empty($torneoEquipo['fecha_inicio']) ? date('d/m/Y', strtotime($torneoEquipo['fecha_inicio'])) : null;
                  $fechaFin = !empty($torneoEquipo['fecha_fin']) ? date('d/m/Y', strtotime($torneoEquipo['fecha_fin'])) : null;
                  if ($fechaInicio && $fechaFin) {
                     $rangoFechas = $fechaInicio . ' – ' . $fechaFin;
                  } elseif ($fechaInicio) {
                     $rangoFechas = 'Desde ' . $fechaInicio;
                  } else {
                     $rangoFechas = 'Fechas a confirmar';
                  }
                  ?>
                  <li class="equipo-torneo-card">
                     <div class="equipo-torneo-info">
                        <h3><?php echo htmlspecialchars($torneoEquipo['torneo_nombre']); ?></h3>
                        <p class="equipo-torneo-meta">
                           <span><?php echo htmlspecialchars($formatoEtiqueta); ?></span>
                           <span><?php echo htmlspecialchars($rangoFechas); ?></span>
                        </p>
                     </div>
                     <div class="equipo-torneo-acciones">
                        <span class="equipo-torneo-estado"><?php echo htmlspecialchars($estadoEtiqueta); ?></span>
                        <span class="equipo-torneo-inscripcion"><?php echo htmlspecialchars($inscripcionEtiqueta); ?></span>
                        <a class="equipo-torneo-enlace" href="<?php echo URL_BASE; ?>index.php?c=torneo&a=detalle&id=<?php echo (int) $torneoEquipo['torneo_id']; ?>">Ver torneo</a>
                     </div>
                  </li>
               <?php endforeach; ?>
            </ul>
         <?php endif; ?>

      </section>

      <!-- ============================== -->
      <!-- PESTAÑA RANKING (datos reales) -->
      <!-- ============================== -->
      <section class="tarjeta-equipo equipo-panel" id="panel-ranking" data-panel="ranking" role="tabpanel" aria-labelledby="tab-ranking" hidden>

         <h2>Ranking del equipo</h2>

         <?php if (empty($posicionesEquipo)): ?>
            <p class="equipo-panel-vacio">Todavía no hay posiciones registradas para este equipo.</p>
         <?php else: ?>
            <div class="equipo-ranking-scroll">
               <table class="equipo-ranking-tabla">
                  <thead>
                     <tr>
                        <th>Torneo</th>
                        <th>Pos</th>
                        <th>PJ</th>
                        <th>G</th>
                        <th>E</th>
                        <th>P</th>
                        <th>Pts</th>
                        <th>Dif</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php foreach ($posicionesEquipo as $posicionEquipo): ?>
                        <?php
                        $diferencia = (float) $posicionEquipo['diferencia_goles'];
                        $diferenciaTexto = ($diferencia > 0 ? '+' : '') . $numeroMarcador($diferencia);
                        ?>
                        <tr>
                           <td><?php echo htmlspecialchars($posicionEquipo['torneo_nombre']); ?></td>
                           <td class="equipo-ranking-posicion"><?php echo (int) $posicionEquipo['posicion']; ?>°</td>
                           <td><?php echo (int) $posicionEquipo['partidos_jugados']; ?></td>
                           <td><?php echo (int) $posicionEquipo['partidos_ganados']; ?></td>
                           <td><?php echo (int) $posicionEquipo['partidos_empatados']; ?></td>
                           <td><?php echo (int) $posicionEquipo['partidos_perdidos']; ?></td>
                           <td class="equipo-ranking-puntos"><?php echo $numeroMarcador($posicionEquipo['puntos']); ?></td>
                           <td><?php echo $diferenciaTexto; ?></td>
                        </tr>
                     <?php endforeach; ?>
                  </tbody>
               </table>
            </div>
         <?php endif; ?>

      </section>

      <!-- ============================== -->
      <!-- NOVEDADES (todavia de ejemplo -- no existe tabla de noticias) -->
      <!-- ============================== -->
      <section class="tarjeta-equipo equipo-noticias">

         <h2>Novedades</h2>

         <ul class="noticias-lista">
            <li>
               <article class="noticia-item">
                  <img src="<?php echo URL_BASE; ?>img/04.jpg" alt="Noticia sobre la cancha del Club FC">
                  <div class="noticia-info">
                     <h3>Noticia sobre la cancha del Club FC</h3>
                     <span>Fecha: 20/06/2026</span>
                  </div>
               </article>
            </li>
         </ul>

      </section>

   </main>

   <!-- FOOTER ---------------------------->
   <?php include __DIR__ . '/../componentes/footer.php'; ?>

   <script src="<?php echo URL_BASE; ?>js/perfil-equipo-tabs.js"></script>

</body>

</html>