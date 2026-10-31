<?php

/**
 * Vista: solicitudes.php
 * Requiere que $invitaciones y $solicitudesEnviadas vengan cargados por EquipoControlador::solicitudes()
 * Usa las clases reales de solicitudes.css (fila-jugador, boton-aceptar-solicitud, etc.),
 * las mismas que ya usaba solicitudes.js en la maquetación original.
 */
?>
<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>ASCEND - Solicitudes</title>

   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
   <script src="<?php echo URL_BASE; ?>js/tema.js"></script>
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/navbar.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/footer.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/jugador/crear-equipo.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/jugador/solicitudes.css">

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>

   <!-- NAVBAR ---------------------------->
   <?php include __DIR__ . '/../componentes/navbar-jugador.php'; ?>

   <main class="crear-equipo-pagina">

      <div class="crear-equipo-header">
         <h1>Solicitudes</h1>
         <p>Invitaciones que recibiste de otros equipos y las solicitudes que enviaste para unirte a uno.</p>
      </div>

      <div class="crear-equipo-formulario">

         <section class="tarjeta-equipo solicitudes-contenedor">

            <h2 class="solicitudes-subtitulo">Invitaciones recibidas</h2>
            <p class="solicitudes-ayuda">Equipos que te invitaron a sumarte. Aceptá o rechazá cada una.</p>

            <?php if (empty($invitaciones)): ?>

               <p class="lista-vacia">
                  No tenés invitaciones pendientes por el momento.
               </p>

            <?php else: ?>

               <ul class="lista-jugadores" id="lista-solicitudes-recibidas">
                  <?php foreach ($invitaciones as $invitacion): ?>
                     <li class="fila-jugador">
                        <img class="fila-jugador-avatar"
                           src="<?php echo htmlspecialchars($invitacion['escudo_url'] ?: URL_BASE . 'img/logos/canelones.png'); ?>"
                           alt="<?php echo htmlspecialchars($invitacion['nombre_equipo']); ?>">

                        <div class="fila-jugador-info">
                           <span class="fila-jugador-nombre"><?php echo htmlspecialchars($invitacion['nombre_equipo']); ?></span>
                           <span class="fila-jugador-invitado-por">Invitado por <?php echo htmlspecialchars($invitacion['invitado_por_nombre']); ?></span>
                        </div>

                        <div class="fila-jugador-acciones">
                           <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=equipo&a=responderInvitacion">
                              <input type="hidden"
                                 name="csrf_token"
                                 value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                              <input type="hidden" name="solicitud_id" value="<?php echo (int) $invitacion['id']; ?>">
                              <input type="hidden" name="respuesta" value="aceptar">
                              <button type="submit" class="boton-aceptar-solicitud">Aceptar</button>
                           </form>
                           <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=equipo&a=responderInvitacion">
                              <input type="hidden"
                                 name="csrf_token"
                                 value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                              <input type="hidden" name="solicitud_id" value="<?php echo (int) $invitacion['id']; ?>">
                              <input type="hidden" name="respuesta" value="rechazar">
                              <button type="submit" class="boton-rechazar-solicitud">Rechazar</button>
                           </form>
                        </div>
                     </li>
                  <?php endforeach; ?>
               </ul>

            <?php endif; ?>

         </section>

         <!-- SOLICITUDES QUE ENVIÓ EL JUGADOR -->
         <section class="tarjeta-equipo solicitudes-contenedor">

            <h2 class="solicitudes-subtitulo">Mis solicitudes enviadas</h2>
            <p class="solicitudes-ayuda">Equipos a los que pediste unirte. Mientras esté pendiente, podés cancelarla.</p>

            <?php if (empty($solicitudesEnviadas)): ?>

               <p class="lista-vacia">
                  Todavía no enviaste solicitudes para unirte a un equipo.
               </p>

            <?php else: ?>

               <?php
               $etiquetasEstado = [
                  'pendiente' => 'Pendiente',
                  'aprobado'  => 'Aceptada',
                  'rechazado' => 'Rechazada',
                  'cancelado' => 'Cancelada',
               ];
               ?>

               <ul class="lista-jugadores" id="lista-solicitudes-enviadas">
                  <?php foreach ($solicitudesEnviadas as $enviada): ?>
                     <?php $estado = (string) $enviada['estado']; ?>
                     <li class="fila-jugador">
                        <img class="fila-jugador-avatar"
                           src="<?php echo htmlspecialchars($enviada['escudo_url'] ?: URL_BASE . 'img/logos/canelones.png'); ?>"
                           alt="<?php echo htmlspecialchars($enviada['nombre_equipo']); ?>">

                        <div class="fila-jugador-info">
                           <a class="fila-jugador-nombre solicitud-enlace-equipo"
                              href="<?php echo URL_BASE; ?>index.php?c=equipo&a=verPerfil&id=<?php echo (int) $enviada['equipo_id']; ?>">
                              <?php echo htmlspecialchars($enviada['nombre_equipo']); ?>
                           </a>
                           <span class="fila-jugador-invitado-por">
                              Enviada el <?php echo htmlspecialchars(date('d/m/Y', strtotime($enviada['fecha_solicitud']))); ?>
                           </span>
                        </div>

                        <div class="fila-jugador-acciones">
                           <span class="solicitud-estado solicitud-estado--<?php echo htmlspecialchars($estado); ?>">
                              <?php echo htmlspecialchars($etiquetasEstado[$estado] ?? ucfirst($estado)); ?>
                           </span>

                           <?php if ($estado === 'pendiente'): ?>
                              <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=equipo&a=cancelarSolicitud">
                                 <input type="hidden"
                                    name="csrf_token"
                                    value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                                 <input type="hidden" name="solicitud_id" value="<?php echo (int) $enviada['id']; ?>">
                                 <button type="submit" class="boton-rechazar-solicitud">Cancelar solicitud</button>
                              </form>
                           <?php endif; ?>
                        </div>
                     </li>
                  <?php endforeach; ?>
               </ul>

            <?php endif; ?>

         </section>

      </div>

   </main>

   <!-- FOOTER ---------------------------->
   <?php include __DIR__ . '/../componentes/footer.php'; ?>

   <!-- solicitudes.js NO se carga: la lista se arma del lado del servidor -->

</body>

</html>