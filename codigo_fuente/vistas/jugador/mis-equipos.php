<?php
/**
 * Vista: mis-equipos.php
 * Requiere que $equipos venga cargado por EquipoControlador::index()
 * $equipos viene de Equipo::obtenerEquiposDelJugador() -> array de equipos, cada uno
 * con 'es_capitan' (0 o 1) para saber que insignia/boton mostrar.
 *
 * Sin JavaScript: es una lista de solo lectura, no necesita nada de interactividad.
 */
?>
<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>ASCEND - Mis Equipos</title>

   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/navbar.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/footer.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/jugador/crear-equipo.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/jugador/mis-equipos.css">

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>

   <!-- NAVBAR ---------------------------->
   <?php include __DIR__ . '/../componentes/navbar-jugador.php'; ?>

   <main class="crear-equipo-pagina">

      <div class="crear-equipo-header mis-equipos-header">
         <div>
            <h1>Mis Equipos</h1>
            <p>Los equipos de los que formás parte. Los que creaste vos los podés editar; a los demás solo entrás a mirar su perfil.</p>
         </div>

         <a href="<?php echo URL_BASE; ?>index.php?c=equipo&a=crear" class="boton-registro">
            <i class="fa-solid fa-plus"></i> Crear equipo nuevo
         </a>
      </div>

      <?php if (empty($equipos)): ?>

         <p class="mis-equipos-vacio">
            Todavía no formás parte de ningún equipo.
            <a href="<?php echo URL_BASE; ?>index.php?c=equipo&a=crear">Creá el tuyo</a>
            o pedile a un líder que te invite.
         </p>

      <?php else: ?>

         <ul class="mis-equipos-grid" id="mis-equipos-lista">
            <?php foreach ($equipos as $equipo): ?>
               <li>
                  <article class="mi-tarjeta-equipo">
                     <img src="<?php echo htmlspecialchars($equipo['escudo_url'] ?: URL_BASE . 'img/logos/canelones.png'); ?>"
                        alt="<?php echo htmlspecialchars($equipo['nombre_equipo']); ?>">

                     <div class="mi-equipo-info">
                        <h2><?php echo htmlspecialchars($equipo['nombre_equipo']); ?></h2>
                        <?php if ($equipo['es_capitan']): ?>
                           <span class="mi-equipo-badge badge-lider">Líder</span>
                        <?php else: ?>
                           <span class="mi-equipo-badge badge-miembro">Miembro</span>
                        <?php endif; ?>
                     </div>

                     <?php if ($equipo['es_capitan']): ?>
                        <a href="<?php echo URL_BASE; ?>index.php?c=equipo&a=gestionar&id=<?php echo (int) $equipo['id']; ?>" class="boton-secundario">
                           Gestionar
                        </a>
                     <?php else: ?>
                        <a href="<?php echo URL_BASE; ?>index.php?c=equipo&a=verPerfil&id=<?php echo (int) $equipo['id']; ?>" class="boton-secundario">
                           Ver perfil
                        </a>
                     <?php endif; ?>
                  </article>
               </li>
            <?php endforeach; ?>
         </ul>

      <?php endif; ?>

   </main>

   <!-- FOOTER ---------------------------->
   <?php include __DIR__ . '/../componentes/footer.html'; ?>

   <!-- mis-equipos.js NO se carga: la lista se arma del lado del servidor -->

</body>
</html>
