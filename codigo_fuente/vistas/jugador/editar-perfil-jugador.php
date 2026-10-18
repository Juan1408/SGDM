<?php
/**
 * Vista: editar-perfil-jugador.php
 * Pagina para editar el perfil propio. Se llega desde el navbar: Jugador > Editar perfil.
 * Lo carga panelJugadorControlador::editarPerfil() (y actualizarPerfil() si hubo un error al guardar).
 * El formulario se envia a panelJugador/actualizarPerfil.
 * Variables: $datos, $camposPublicos (campos opcionales que hoy son publicos), y opcionalmente $error.
 */

$camposPublicos = $camposPublicos ?? [];

$urlImagen = function (?string $valor, string $porDefecto): string {
   if (empty($valor)) { return URL_BASE . $porDefecto; }
   return preg_match('#^(https?:)?//#i', $valor) ? $valor : URL_BASE . ltrim($valor, '/');
};

$avatarUrl = $urlImagen($datos['foto_perfil_url'] ?? '', 'img/avatars/a1.png');
$bannerUrl = $urlImagen($datos['banner_url'] ?? '', 'img/bg.jpg');

//Opciones de privacidad (clave => etiqueta)
$opcionesPrivacidad = [
   'bio' => 'Biografía',
   'ubicacion' => 'Ciudad y país',
   'edad' => 'Edad',
   'discord' => 'Discord',
   'instagram' => 'Instagram',
   'twitter' => 'X (Twitter)',
   'torneos' => 'Torneos en los que participo',
];
?>
<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>ASCEND - Editar perfil</title>

   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/navbar.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/footer.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/jugador/perfil-jugador.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/jugador/editar-perfil-jugador.css">

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>

   <!-- NAVBAR ---------------------------->
   <?php include __DIR__ . '/../componentes/navbar-jugador.php'; ?>

   <main class="equipo-pagina editar-perfil-pagina">

      <div class="editar-perfil-encabezado">
         <h1>Editar perfil</h1>
         <a href="<?php echo URL_BASE; ?>index.php?c=panelJugador&a=perfil" class="editar-perfil-volver">
            <i class="fa-solid fa-arrow-left"></i> Volver a mi perfil
         </a>
      </div>

      <?php if (!empty($error)): ?>
         <div class="mensaje-error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form action="<?php echo URL_BASE; ?>index.php?c=panelJugador&a=actualizarPerfil" method="POST"
            enctype="multipart/form-data" id="form-editar-perfil">

         <!-- ============================== -->
         <!-- SECCION 1: MIS DATOS           -->
         <!-- ============================== -->
         <section class="tarjeta-equipo editar-perfil-seccion">
            <h2>Mis datos</h2>

            <div class="editar-imagenes">
               <div class="editar-banner">
                  <img src="<?php echo htmlspecialchars($bannerUrl); ?>" alt="Banner" id="vista-banner">
                  <label for="input-banner" class="editar-boton-imagen"><i class="fa-solid fa-camera"></i> Cambiar banner</label>
                  <input type="file" name="banner" id="input-banner" accept="image/jpeg,image/png,image/webp" hidden>
               </div>

               <div class="editar-avatar">
                  <img src="<?php echo htmlspecialchars($avatarUrl); ?>" alt="Foto de perfil" id="vista-avatar">
                  <label for="input-avatar" class="editar-boton-imagen"><i class="fa-solid fa-camera"></i> Cambiar foto</label>
                  <input type="file" name="avatar" id="input-avatar" accept="image/jpeg,image/png,image/webp" hidden>
               </div>
            </div>
            <p class="editar-ayuda">JPG, PNG o WEBP, de hasta 2 MB.</p>

            <div class="editar-campos">
               <label class="editar-campo">
                  <span>Nombre</span>
                  <input type="text" name="nombre_completo" maxlength="120" required
                     value="<?php echo htmlspecialchars($datos['nombre_completo'] ?? ''); ?>">
               </label>

               <label class="editar-campo">
                  <span>Email</span>
                  <input type="email" name="email" maxlength="150" required
                     value="<?php echo htmlspecialchars($datos['email'] ?? ''); ?>">
               </label>

               <label class="editar-campo editar-campo-ancho">
                  <span>Biografía</span>
                  <textarea name="bio" rows="4" maxlength="500" placeholder="Contá algo sobre vos (máx. 500 caracteres)"><?php echo htmlspecialchars($datos['bio'] ?? ''); ?></textarea>
               </label>

               <label class="editar-campo">
                  <span>Ciudad</span>
                  <input type="text" name="ciudad" maxlength="100"
                     value="<?php echo htmlspecialchars($datos['ciudad'] ?? ''); ?>">
               </label>

               <label class="editar-campo">
                  <span>País</span>
                  <input type="text" name="pais" maxlength="100"
                     value="<?php echo htmlspecialchars($datos['pais'] ?? ''); ?>">
               </label>

               <label class="editar-campo">
                  <span>Fecha de nacimiento</span>
                  <input type="date" name="fecha_nacimiento" max="<?php echo date('Y-m-d'); ?>"
                     value="<?php echo htmlspecialchars($datos['fecha_nacimiento'] ?? ''); ?>">
               </label>

               <label class="editar-campo">
                  <span><i class="fa-brands fa-discord"></i> Discord</span>
                  <input type="text" name="discord_tag" maxlength="60" placeholder="Tu usuario de Discord"
                     value="<?php echo htmlspecialchars($datos['discord_tag'] ?? ''); ?>">
               </label>

               <label class="editar-campo">
                  <span><i class="fa-brands fa-instagram"></i> Instagram</span>
                  <input type="url" name="instagram_url" maxlength="255" placeholder="https://instagram.com/usuario"
                     value="<?php echo htmlspecialchars($datos['instagram_url'] ?? ''); ?>">
               </label>

               <label class="editar-campo">
                  <span><i class="fa-brands fa-x-twitter"></i> X (Twitter)</span>
                  <input type="url" name="twitter_url" maxlength="255" placeholder="https://x.com/usuario"
                     value="<?php echo htmlspecialchars($datos['twitter_url'] ?? ''); ?>">
               </label>
            </div>
         </section>

         <!-- ============================== -->
         <!-- SECCION 2: QUE VE EL PUBLICO   -->
         <!-- ============================== -->
         <section class="tarjeta-equipo editar-perfil-seccion">
            <h2>Qué ve el público</h2>
            <p class="editar-ayuda">
               Marcá lo que querés mostrar en tu perfil público.
               Siempre se ven tu apodo, foto, banner, nivel y equipos. Tu email y teléfono nunca se muestran.
            </p>

            <div class="editar-privacidad">
               <?php foreach ($opcionesPrivacidad as $clave => $etiqueta): ?>
                  <label class="editar-privacidad-opcion">
                     <input type="checkbox" name="publico[]" value="<?php echo $clave; ?>"
                        <?php echo in_array($clave, $camposPublicos, true) ? 'checked' : ''; ?>>
                     <span><?php echo htmlspecialchars($etiqueta); ?></span>
                  </label>
               <?php endforeach; ?>
            </div>
         </section>

         <div class="editar-acciones">
            <button type="submit" class="boton-registro">Guardar cambios</button>
            <a href="<?php echo URL_BASE; ?>index.php?c=panelJugador&a=perfil" class="boton-secundario">Cancelar</a>
         </div>

      </form>

   </main>

   <!-- FOOTER ---------------------------->
   <?php include __DIR__ . '/../componentes/footer.html'; ?>

   <script src="<?php echo URL_BASE; ?>js/editar-perfil-jugador.js"></script>

</body>
</html>
