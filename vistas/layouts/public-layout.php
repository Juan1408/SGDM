<!DOCTYPE html>
<html lang="es">

<head>

   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title><?= $titulo ?? 'SGDM'; ?></title>

   <!-- CSS -->
   <link rel="stylesheet" href="/sgdm/publico/css/main.css">

   <!-- Font Awesome -->
   <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
   />

</head>

<body>

   <?php include __DIR__ . '/../components/navbar.php'; ?>

   <main>
      <?= $contenido ?>
   </main>

   <?php include __DIR__ . '/../components/footer.php'; ?>

</body>

</html>