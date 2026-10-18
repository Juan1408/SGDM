<?php
/**
 * Vista: editar-perfil-organizador.php
 * Pagina para editar el perfil propio del organizador. Se llega desde el navbar: Organizador > Editar perfil.
 * Lo carga PanelOrganizadorControlador::editarPerfil() (y guardarPerfil() si hubo un error al guardar).
 * El formulario se envia a panelOrganizador/guardarPerfil.
 * Usa los mismos estilos y el mismo JS que editar-perfil-jugador.php.
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
   'localidad' => 'Localidad',
   'sitio_web' => 'Sitio web',
   'telefono_contacto' => 'Teléfono de contacto',
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
   <?php include __DIR__ . '/../componentes/navbar-organizador.php'; ?>

   <main class="equipo-pagina editar-perfil-pagina">

      <div class="editar-perfil-encabezado">
         <h1>Editar perfil</h1>
         <a href="<?php echo URL_BASE; ?>index.php?c=perfil&a=organizador&id=<?php echo (int) ($datos['id'] ?? 0); ?>" class="editar-perfil-volver">
            <i class="fa-solid fa-arrow-left"></i> Volver a mi perfil
         </a>
      </div>

      <?php if (!empty($error)): ?>
         <div class="mensaje-error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form action="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&a=guardarPerfil" method="POST"
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
                  <span>Nombre completo</span>
                  <input type="text" name="nombre_completo" maxlength="120" required
                     value="<?php echo htmlspecialchars($datos['nombre_completo'] ?? ''); ?>">
               </label>

               <label class="editar-campo">
                  <span>Email</span>
                  <input type="email" value="<?php echo htmlspecialchars($datos['email'] ?? ''); ?>" disabled>
               </label>

               <label class="editar-campo">
                  <span>Teléfono personal</span>
                  <input type="text" name="telefono" maxlength="20"
                     value="<?php echo htmlspecialchars($datos['telefono'] ?? ''); ?>">
               </label>

               <label class="editar-campo">
                  <span>Organización</span>
                  <input type="text" name="nombre_organizacion" maxlength="150" required
                     value="<?php echo htmlspecialchars($datos['nombre_organizacion'] ?? ''); ?>">
               </label>

               <label class="editar-campo editar-campo-ancho">
                  <span>Biografía</span>
                  <textarea name="bio_organizacion" rows="4" maxlength="2000" placeholder="Contá quién sos y qué tipo de torneos organizás"><?php echo htmlspecialchars($datos['bio_organizacion'] ?? ''); ?></textarea>
               </label>

               <label class="editar-campo">
                  <span>Localidad</span>
                  <input type="text" name="localidad" maxlength="100"
                     value="<?php echo htmlspecialchars($datos['localidad'] ?? ''); ?>">
               </label>

               <label class="editar-campo">
                  <span>Teléfono de contacto</span>
                  <input type="text" name="telefono_contacto" maxlength="50"
                     value="<?php echo htmlspecialchars($datos['telefono_contacto'] ?? ''); ?>">
               </label>

               <label class="editar-campo editar-campo-ancho">
                  <span><i class="fa-solid fa-globe"></i> Sitio web</span>
                  <input type="url" name="sitio_web" maxlength="255" placeholder="https://tusitio.com"
                     value="<?php echo htmlspecialchars($datos['sitio_web'] ?? ''); ?>">
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
               Siempre se ven el nombre de tu organización, tu foto, tu banner y tus torneos publicados.
               Tu email y tu teléfono personal nunca se muestran.
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
            <a href="<?php echo URL_BASE; ?>index.php?c=perfil&a=organizador&id=<?php echo (int) ($datos['id'] ?? 0); ?>" class="boton-secundario">Cancelar</a>
         </div>

      </form>

   </main>

   <!-- FOOTER ---------------------------->
   <?php include __DIR__ . '/../componentes/footer.html'; ?>

   <script src="<?php echo URL_BASE; ?>js/editar-perfil-jugador.js"></script>

</body>
</html>
