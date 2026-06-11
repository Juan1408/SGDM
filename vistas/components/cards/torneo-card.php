<?php
/*
   Datos esperados:

   $torneo = [
      'nombre' => 'Valorant Champion Series',
      'imagen' => '/img/torneos/valorant.jpg',
      'categoria' => 'eSports',
      'participantes' => 32,
      'estado' => 'Finales',
      'tipo' => 'Eliminación Directa',
      'fecha_inicio' => 'Mayo 2026',
      'fecha_fin' => 'Junio 2026'
   ];
*/
?>


<div class="torneo-card">

   <div class="torneo-card-header">
      <h2 class="torneo-title">
         <?= $torneo['nombre']; ?>
      </h2>
   </div>

   <div class="torneo-image-container">
      <img
         src="<?= $torneo['imagen']; ?>"
         alt="<?= $torneo['nombre']; ?>"
         class="torneo-image"
      >
   </div>

   <div class="torneo-info-icons">

      <div class="torneo-info-item">
         <i class="fa-solid fa-gamepad"></i>
         <span><?= $torneo['categoria']; ?></span>
      </div>

      <div class="torneo-info-item">
         <i class="fa-solid fa-users"></i>
         <span><?= $torneo['participantes']; ?> Personas</span>
      </div>

      <div class="torneo-info-item">
         <i class="fa-solid fa-trophy"></i>
         <span><?= $torneo['estado']; ?></span>
      </div>

   </div>

   <div class="torneo-type-container">
      <div class="torneo-type">
         <i class="fa-solid fa-sitemap"></i>
         <span><?= $torneo['tipo']; ?></span>
      </div>
   </div>

   <hr class="torneo-divider">

   <div class="torneo-date">
      <?= $torneo['fecha_inicio']; ?> - <?= $torneo['fecha_fin']; ?>
   </div>

</div>