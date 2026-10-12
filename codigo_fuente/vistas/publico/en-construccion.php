<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>ASCEND - En Construcción</title>

   <!-- CSS Principal -->
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/navbar.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/footer.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/publico/home.css">

   <!-- Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

   <!-- Google Fonts -->
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
      rel="stylesheet">
</head>

<body>

   <?php include __DIR__ . '/../componentes/navbar-publico.php'; ?>

   <main>
      <section class="pantalla-construccion">
         <div class="hero-content">
            <i class="fa-solid fa-person-digging"></i>
            <h1>Pantalla en Construcción</h1>
            <p>Esta sección estará disponible para la próxima entrega del proyecto.</p>
            <div class="hero-buttons">
               <a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=index" class="primary-btn">Volver al Inicio</a>
            </div>
         </div>
      </section>
   </main>

   <?php include __DIR__ . '/../componentes/footer.php'; ?>

   <script src="<?php echo URL_BASE; ?>js/navbar.js"></script>
   <script>document.dispatchEvent(new Event("navbarPublicoListo"));</script>

</body>

</html>
