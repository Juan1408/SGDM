<?php
/**
 * Vista: dashboard.php
 * Requiere que vengan cargados desde PanelJugadorControlador::dashboard():
 * $datos (perfil), $equipos, $torneosActivos, $proximosPartidos, $record, $solicitudesPendientes
 *
 * Reutiliza clases ya existentes en el proyecto (equipo-estadisticas, mi-tarjeta-equipo,
 * fila-jugador, tarjeta-equipo) para mantener consistencia visual con el resto del sitio,
 * ya que esta pantalla todavia no tenia maquetacion propia.
 */

$nombre = $datos['nombre_completo'] ?? 'Jugador';
$nivel = $datos['nivel'] ?? 1;
$victorias = $record['victorias'];
$derrotas = $record['derrotas'];
$totalPartidos = $victorias + $derrotas;
$winrate = $totalPartidos > 0 ? round(($victorias / $totalPartidos) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>ASCEND - Panel del Jugador</title>

   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/navbar.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/footer.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/jugador/perfil-jugador.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/jugador/crear-equipo.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/jugador/gestionar-equipo.css">

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>

   <!-- NAVBAR ---------------------------->   
    <?php include __DIR__ . '/../componentes/navbar-jugador.php'; ?>

   <main class="equipo-pagina">

      <div class="crear-equipo-header">
         <h1>Hola, <?php echo htmlspecialchars($nombre); ?></h1>
         <p>Nivel <?php echo (int) $nivel; ?> · Este es tu panel de jugador.</p>
      </div>

      <!-- ============================== -->
      <!-- METRICAS PERSONALES            -->
      <!-- ============================== -->
      <dl class="equipo-estadisticas">
         <div class="tarjeta-estadistica">
            <dd><?php echo $winrate; ?>%</dd>
            <dt>Win/Loss ratio</dt>
         </div>
         <div class="tarjeta-estadistica">
            <dd><?php echo $victorias; ?>-<?php echo $derrotas; ?></dd>
            <dt>Victorias-Derrotas</dt>
         </div>
         <div class="tarjeta-estadistica">
            <dd><?php echo (int) $torneosActivos; ?></dd>
            <dt>Torneos Activos</dt>
         </div>
         <div class="tarjeta-estadistica">
            <dd><?php echo count($equipos); ?></dd>
            <dt>Mis Equipos</dt>
         </div>
      </dl>

      <!-- ============================== -->
      <!-- PROXIMOS PARTIDOS              -->
      <!-- ============================== -->
      <section class="tarjeta-equipo">
         <h2>Próximos Partidos</h2>

         <?php if (empty($proximosPartidos)): ?>
            <p class="lista-vacia">No tenés partidos programados por el momento.</p>
         <?php else: ?>
            <div class="lista-jugadores">
               <?php foreach ($proximosPartidos as $partido): ?>
                  <div class="fila-jugador">
                     <div class="fila-jugador-info">
                        <span class="fila-jugador-nombre">
                           <?php echo htmlspecialchars($partido['nombre_local'] ?? '?'); ?>
                           vs
                           <?php echo htmlspecialchars($partido['nombre_visitante'] ?? '?'); ?>
                        </span>
                        <span class="fila-jugador-usuario">
                           <?php echo htmlspecialchars($partido['torneo_nombre']); ?>
                           <?php if (!empty($partido['fecha_hora_programada'])): ?>
                              · <?php echo date('d/m/Y H:i', strtotime($partido['fecha_hora_programada'])); ?>
                           <?php endif; ?>
                        </span>
                     </div>
                  </div>
               <?php endforeach; ?>
            </div>
         <?php endif; ?>
      </section>

      <!-- ============================== -->
      <!-- SOLICITUDES PENDIENTES (resumen) -->
      <!-- ============================== -->
      <?php if (!empty($solicitudesPendientes)): ?>
         <section class="tarjeta-equipo">
            <h2>Tenés <?php echo count($solicitudesPendientes); ?> invitación(es) pendiente(s)</h2>
            <p>
               <a href="<?php echo URL_BASE; ?>index.php?c=equipo&a=solicitudes">Ver mis solicitudes</a>
            </p>
         </section>
      <?php endif; ?>

      <!-- ============================== -->
      <!-- MIS EQUIPOS                    -->
      <!-- ============================== -->
      <section class="equipo-miembros">
         <h2 class="seccion-gradiente-titulo">Mis Equipos</h2>

         <?php if (empty($equipos)): ?>
            <p class="lista-vacia">
               Todavía no formás parte de ningún equipo.
               <a href="<?php echo URL_BASE; ?>index.php?c=equipo&a=crear">Creá el tuyo</a>.
            </p>
         <?php else: ?>
            <ul class="mis-equipos-grid">
               <?php foreach ($equipos as $equipo): ?>
                  <li>
                     <article class="mi-tarjeta-equipo">
                        <img src="<?php echo htmlspecialchars($equipo['escudo_url'] ?: URL_BASE . 'img/logos/canelones.png'); ?>" alt="<?php echo htmlspecialchars($equipo['nombre_equipo']); ?>">

                        <div class="mi-equipo-info">
                           <h3><?php echo htmlspecialchars($equipo['nombre_equipo']); ?></h3>
                           <?php if ($equipo['es_capitan']): ?>
                              <span class="mi-equipo-badge badge-lider">Líder</span>
                           <?php else: ?>
                              <span class="mi-equipo-badge badge-miembro">Miembro</span>
                           <?php endif; ?>
                        </div>

                        <?php if ($equipo['es_capitan']): ?>
                           <a href="<?php echo URL_BASE; ?>index.php?c=equipo&a=gestionar&id=<?php echo (int) $equipo['id']; ?>" class="boton-secundario">Gestionar</a>
                        <?php else: ?>
                           <a href="<?php echo URL_BASE; ?>index.php?c=equipo&a=verPerfil&id=<?php echo (int) $equipo['id']; ?>" class="boton-secundario">Ver perfil</a>
                        <?php endif; ?>
                     </article>
                  </li>
               <?php endforeach; ?>
            </ul>
         <?php endif; ?>
      </section>

   </main>

   <!-- FOOTER ---------------------------->
   <?php include __DIR__ . '/../componentes/footer.html'; ?>


</body>
</html>
