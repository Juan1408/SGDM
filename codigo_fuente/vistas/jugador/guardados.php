<?php
/**
 * Vista: guardados.php
 * Requiere que vengan cargados desde GuardadosControlador::index(): $torneosGuardados, $equiposGuardados
 * Reemplaza a favoritos.html. Usa las clases reales de favoritos.js (torneos-organiza-tarjeta,
 * boton-quitar-favorito, mi-tarjeta-equipo, etc.) para mantener el mismo estilo.
 *
 * Sin JavaScript: "Quitar" es un mini-formulario POST, igual que en el resto del modulo.
 */
?>
<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>ASCEND - Guardados</title>

   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/navbar.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/footer.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/jugador/perfil-jugador.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/jugador/favoritos.css">

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>

   <!-- NAVBAR ---------------------------->
   <?php include __DIR__ . '/../componentes/navbar-jugador.php'; ?>

   <main class="equipo-pagina">

      <div class="favoritos-header">
         <h1>Guardados</h1>
         <p>Los torneos y equipos que marcaste para seguir de cerca.</p>
      </div>

      <!-- TORNEOS GUARDADOS -->
      <section class="equipo-miembros">

         <h2 class="seccion-gradiente-titulo">Torneos Guardados</h2>

         <?php if (empty($torneosGuardados)): ?>
            <p class="lista-vacia">
               Todavía no guardaste ningún torneo.
               <?php if (empty($equiposGuardados)): ?>
                  <br>(Esta sección va a tener contenido cuando exista el módulo de Torneos.)
               <?php endif; ?>
            </p>
         <?php else: ?>
            <ul class="torneos-organiza-grilla">
               <?php foreach ($torneosGuardados as $torneo): ?>
                  <li>
                     <article class="torneos-organiza-tarjeta">
                        <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=guardados&a=quitar">
                           <input type="hidden" name="tipo" value="torneo">
                           <input type="hidden" name="referencia_id" value="<?php echo (int) $torneo['id']; ?>">
                           <button type="submit" class="boton-quitar-favorito" title="Quitar de guardados">
                              <i class="fa-solid fa-heart-crack"></i>
                           </button>
                        </form>
                        <a href="<?php echo URL_BASE; ?>index.php?c=torneo&a=detalle&id=<?php echo (int) $torneo['id']; ?>">
                           <img src="<?php echo htmlspecialchars($torneo['banner_url'] ?: URL_BASE . 'img/torneos/card1.jpg'); ?>" alt="<?php echo htmlspecialchars($torneo['nombre']); ?>">
                           <div class="torneo-organiza-info">
                              <h3><?php echo htmlspecialchars($torneo['nombre']); ?></h3>
                              <span><?php echo htmlspecialchars(ucfirst($torneo['estado'])); ?></span>
                           </div>
                        </a>
                     </article>
                  </li>
               <?php endforeach; ?>
            </ul>
         <?php endif; ?>

      </section>

      <!-- EQUIPOS GUARDADOS -->
      <section class="equipo-miembros">

         <h2 class="seccion-gradiente-titulo">Equipos Guardados</h2>

         <?php if (empty($equiposGuardados)): ?>
            <p class="lista-vacia">
               Todavía no guardaste ningún equipo.
            </p>
         <?php else: ?>
            <ul class="mis-equipos-grid">
               <?php foreach ($equiposGuardados as $equipo): ?>
                  <li>
                     <article class="mi-tarjeta-equipo">
                        <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=guardados&a=quitar">
                           <input type="hidden" name="tipo" value="equipo">
                           <input type="hidden" name="referencia_id" value="<?php echo (int) $equipo['id']; ?>">
                           <button type="submit" class="boton-quitar-favorito" title="Quitar de guardados">
                              <i class="fa-solid fa-heart-crack"></i>
                           </button>
                        </form>
                        <img src="<?php echo htmlspecialchars($equipo['escudo_url'] ?: URL_BASE . 'img/logos/canelones.png'); ?>" alt="<?php echo htmlspecialchars($equipo['nombre_equipo']); ?>">
                        <div class="mi-equipo-info">
                           <h3><?php echo htmlspecialchars($equipo['nombre_equipo']); ?></h3>
                        </div>
                        <a href="<?php echo URL_BASE; ?>index.php?c=equipo&a=verPerfil&id=<?php echo (int) $equipo['id']; ?>" class="boton-secundario">Ver perfil</a>
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
