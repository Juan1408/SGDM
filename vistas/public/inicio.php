<?php

$torneo = [
   'nombre' => 'Valorant Champion Series',
  'imagen' => '../../publico/img/torneos/valorant.jpg',
   'categoria' => 'eSports',
   'participantes' => 32,
   'estado' => 'Finales',
   'tipo' => 'Eliminación Directa',
   'fecha_inicio' => 'Mayo 2026',
   'fecha_fin' => 'Junio 2026'
];

?>

<!DOCTYPE html>
<html lang="es">

<head>

   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title>SGDM</title>

   <link rel="stylesheet" href="../../publico/css/components/cards.css">

   <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
   />

</head>

<body style="background: #f3f4f6; padding: 40px;">

   <?php include __DIR__ . '/../components/cards/torneo-card.php'; ?>

</body>

</html>