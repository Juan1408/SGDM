<!DOCTYPE html>
<html lang="es">

<head>

   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title>ASCEND - Panel Organizador</title>

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

   <!-- Layout compartido de organizador -->
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/botones.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/tarjetas.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/chips.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/organizador-layout.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/footer.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/organizador/dashboard.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
      rel="stylesheet">

</head>

<body>

   <?php require_once __DIR__ . '/navbar.html'; ?>
   <?php require_once __DIR__ . '/sidebar.html'; ?>

   <?php
   $estadisticas = $datos['estadisticas'] ?? ['activos' => 0, 'solicitudes' => 0, 'finalizados' => 0];
   $torneos = $datos['torneos'] ?? [];
   $estadoTorneo = [
      'borrador' => ['texto' => 'Borrador', 'clase' => 'chip-neutro'],
      'inscripciones_abiertas' => ['texto' => 'Inscripciones abiertas', 'clase' => 'chip-alerta'],
      'en_curso' => ['texto' => 'En curso', 'clase' => 'chip-exito'],
      'finalizado' => ['texto' => 'Finalizado', 'clase' => 'chip-info'],
      'cancelado' => ['texto' => 'Cancelado', 'clase' => 'chip-peligro'],
   ];
   ?>

   <main class="dashboard-organizador">

      <header class="dashboard-header">
         <div>
            <p class="dashboard-tag">PANEL DEL ORGANIZADOR</p>
            <h1>Bienvenido, <?php echo htmlspecialchars($datos['nombre'] ?? 'Organizador', ENT_QUOTES, 'UTF-8'); ?></h1>
         </div>
      </header>

      <!-- STATS -->
      <dl class="estadisticas">

         <div class="tarjeta-estadistica">
            <div class="tarjeta-estadistica-icono">
               <i class="fa-solid fa-trofeo"></i>
            </div>
            <div>
               <dt>Torneos Activos</dt>
               <dd><?php echo $estadisticas['activos']; ?></dd>
            </div>
         </div>

         <div class="tarjeta-estadistica">
            <div class="tarjeta-estadistica-icono tarjeta-estadistica-icono-rosa">
               <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div>
               <dt>Solicitudes pendientes</dt>
               <dd><?php echo $estadisticas['solicitudes']; ?></dd>
            </div>
         </div>

         <div class="tarjeta-estadistica">
            <div class="tarjeta-estadistica-icono tarjeta-estadistica-icono-celeste">
               <i class="fa-solid fa-user-check"></i>
            </div>
            <div>
               <dt>Torneos finalizados</dt>
               <dd><?php echo $estadisticas['finalizados']; ?></dd>
            </div>
         </div>

         <div class="tarjeta-estadistica">
            <div class="tarjeta-estadistica-icono tarjeta-estadistica-icono-violeta">
               <i class="fa-solid fa-clipboard-check"></i>
            </div>
            <div>
               <dt>Mis torneos</dt>
               <dd><?php echo count($torneos); ?></dd>
            </div>
         </div>

      </dl>

      <!-- CONTENIDO -->

      <section class="dashboard-grid" aria-label="Resumen del organizador">

         <!-- TORNEOS ACTIVOS -->

         <section class="dashboard-panel">

            <header class="panel-header">
               <h2>Torneos Activos</h2>
               <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=misTorneos">Ver todos</a>
            </header>

            <ul class="lista-torneo">
               <?php if ($torneos === []): ?>
                  <li class="tarjeta-lista">
                     <p>Aún no tienes torneos creados.</p>
                     <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=crear" class="boton boton-gestion">Crear torneo</a>
                  </li>
               <?php else: ?>
                  <?php foreach ($torneos as $torneo): ?>
                     <?php $estado = $estadoTorneo[$torneo['estado']] ?? ['texto' => ucfirst($torneo['estado']), 'clase' => 'chip-neutro']; ?>
                     <li class="tarjeta-lista tarjeta-lista-torneo">
                        <div class="info-torneo">
                           <header>
                              <span class="chip <?php echo $estado['clase']; ?>"><?php echo htmlspecialchars($estado['texto'], ENT_QUOTES, 'UTF-8'); ?></span>
                              <h3><?php echo htmlspecialchars($torneo['nombre'], ENT_QUOTES, 'UTF-8'); ?></h3>
                           </header>
                           <p>
                              <i class="fa-solid fa-gamepad"></i>
                              <?php echo htmlspecialchars($torneo['nombre_juego'], ENT_QUOTES, 'UTF-8'); ?> · <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $torneo['formato'])), ENT_QUOTES, 'UTF-8'); ?>
                           </p>
                           <div class="meta-torneo">
                              <span><?php echo (int) $torneo['equipos_confirmados']; ?>/<?php echo (int) $torneo['cupo_max_equipos']; ?> equipos</span>
                              <span><?php echo $torneo['fecha_inicio'] ? date('d/m/Y', strtotime($torneo['fecha_inicio'])) : 'Sin fecha'; ?></span>
                           </div>
                        </div>
                        <?php $rutaGestion = $torneo['estado'] === 'borrador'
                           ? URL_BASE . 'index.php?c=panelOrganizador&a=crear&etapa=2&id=' . (int) $torneo['id']
                           : URL_BASE . 'index.php?c=panelOrganizador&a=gestionar&id=' . (int) $torneo['id']; ?>
                        <a href="<?php echo htmlspecialchars($rutaGestion, ENT_QUOTES, 'UTF-8'); ?>" class="boton boton-gestion">Gestionar</a>
                      </li>
                  <?php endforeach; ?>
               <?php endif; ?>

            </ul>

         </section>

         <!-- PRÓXIMOS ENCUENTROS -->
         <section class="dashboard-panel">

            <header class="panel-header">
               <h2>Próximos Encuentros</h2>
               <a href="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=calendario">Calendario</a>
            </header>

            <ul class="lista-encuentros">

               <li class="tarjeta-lista tarjeta-lista-encuentro">

                  <time class="fecha-encuentro" datetime="2026-07-12">
                     <strong>12</strong>
                     <span>JUL</span>
                  </time>

                  <div class="equipo-encuentro">
                     <div class="equipos">
                        <img src="<?php echo URL_BASE; ?>img/logos/artigas.png" alt="Artigas">
                        <span>Artigas</span>
                     </div>

                     <strong class="equipos-vs">VS</strong>

                     <div class="equipos">
                        <img src="<?php echo URL_BASE; ?>img/logos/flores.png" alt="Flores">
                        <span>Flores</span>
                     </div>
                  </div>

                  <span class="chip chip-alerta">Pendiente</span>

               </li>

            </ul>

         </section>

      </section>

   </main>

   <script src="<?php echo URL_BASE; ?>js/organizador/includes.js"></script>
   <script src="<?php echo URL_BASE; ?>js/organizador/sidebar.js"></script>

</body>

</html>