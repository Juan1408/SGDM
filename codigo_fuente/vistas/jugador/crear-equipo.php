<?php
/**
 * Vista: crear-equipo.php
 * Por ahora solo maneja el ESTADO 1 (crear equipo nuevo).
 * El ESTADO 2 (gestionar equipo si ya sos lider) se resuelve en el Paso 3.6,
 * usando el bloque "vista-gestionar-equipo" que ya estaba pensado en este mismo archivo
 * (gestionar-equipo.html se elimina, no se usa).
 *
 * No se carga JS: el formulario es un POST simple, la validacion de "obligatorio"
 * la hace el navegador con el atributo required. La subida real de escudo/banner
 * se conecta mas adelante (por eso esos inputs de archivo por ahora no hacen nada visible).
 */
?>
<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>ASCEND - Crear Equipo</title>

   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/navbar.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/footer.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/jugador/crear-equipo.css">

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>

   <!-- NAVBAR ---------------------------->
   <?php include __DIR__ . '/../componentes/navbar-jugador.php'; ?>

   <main class="crear-equipo-pagina">

      <?php if (!empty($error)): ?>
         <div class="mensaje-error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <?php if (isset($_GET['exito'])): ?>
         <div class="mensaje-exito">Equipo creado correctamente.</div>
      <?php endif; ?>

      <div id="vista-crear-equipo">

         <div class="crear-equipo-header">
            <h1>Crear Equipo</h1>
            <p>Armá tu equipo para anotarte a torneos y sumar miembros. El código de invitación se genera automáticamente al crearlo.</p>
         </div>

         <form class="crear-equipo-formulario" id="form-crear-equipo"
            action="<?php echo URL_BASE; ?>index.php?c=equipo&a=procesarCreacion" method="POST">

            <div class="tarjeta-equipo crear-equipo-imagenes">
               <div class="campo-imagen campo-banner">
                  <div class="preview-banner" id="preview-banner-crear">
                     <span class="placeholder-texto">Sin banner todavía</span>
                  </div>
                  <label for="input-banner-crear" class="boton-subir-imagen">
                     <i class="fa-solid fa-image"></i> Subir banner
                  </label>
                  <input type="file" id="input-banner-crear" accept="image/*" hidden disabled>
               </div>

               <div class="campo-imagen campo-escudo">
                  <div class="preview-escudo" id="preview-escudo-crear">
                     <i class="fa-solid fa-shield-halved placeholder-icono"></i>
                  </div>
                  <label for="input-escudo-crear" class="boton-subir-imagen">
                     <i class="fa-solid fa-camera"></i> Subir escudo
                  </label>
                  <input type="file" id="input-escudo-crear" accept="image/*" hidden disabled>
               </div>
               <!-- Los inputs de archivo quedan deshabilitados (disabled) hasta que se programe -->
               <!-- la subida real al servidor. Por ahora crear un equipo no depende de esto. -->
            </div>

            <div class="tarjeta-equipo">
               <h2>Datos del equipo</h2>

               <div class="campo-grupo">
                  <label for="crear-nombre">Nombre del equipo</label>
                  <input type="text" name="nombre_equipo" id="crear-nombre" placeholder="Ej: Fire Wolves" required maxlength="100">
               </div>

               <div class="campo-fila">
                  <div class="campo-grupo">
                     <label for="crear-localidad">Localidad</label>
                     <input type="text" name="ubicacion" id="crear-localidad" placeholder="Ej: Montevideo - Uruguay" maxlength="100">
                  </div>

                  <div class="campo-grupo">
                     <label for="crear-anio">Año de fundación</label>
                     <input type="number" name="anio_fundacion" id="crear-anio" placeholder="Ej: 2024" min="1950" max="2026">
                  </div>
               </div>

               <div class="campo-grupo">
                  <label for="crear-descripcion">Descripción</label>
                  <textarea name="descripcion" id="crear-descripcion" rows="4" placeholder="Contá de qué se trata tu equipo..."></textarea>
               </div>
            </div>

            <div class="crear-equipo-acciones">
               <button type="submit" class="boton-registro">Crear Equipo</button>
            </div>

         </form>

      </div>

   </main>

   <!-- FOOTER ---------------------------->
   <?php include __DIR__ . '/../componentes/footer.html'; ?>

   <!-- crear-equipo.js NO se carga: este formulario no necesita JS -->
   <!-- (required + POST nativo alcanzan; se vuelve a agregar cuando -->
   <!-- se programe la subida real de imagenes y el estado "Gestionar" del paso 3.6) -->

</body>
</html>
