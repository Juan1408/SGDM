<?php
/**
 * Componente: panel-notificaciones.php
 * Campana de notificaciones con datos reales de la tabla `notificaciones`.
 * Se incluye desde los tres navbars cuando hay una sesion iniciada.
 * Sin JS obligatorio: el marcado de leidas son formularios POST con CSRF.
 */

use App\Modelos\Notificacion;

require_once __DIR__ . '/../../modelos/Notificacion.php';

$idPanelNotificaciones = (int) ($_SESSION['usuario_id'] ?? 0);
if ($idPanelNotificaciones <= 0) {
   return;
}

$listaPanelNotificaciones = [];
$totalPanelNoLeidas = 0;
try {
   $modeloPanelNotificaciones = new Notificacion();
   $listaPanelNotificaciones = $modeloPanelNotificaciones->obtenerRecientes($idPanelNotificaciones, 8);
   $totalPanelNoLeidas = $modeloPanelNotificaciones->contarNoLeidas($idPanelNotificaciones);
} catch (\Throwable $e) {
   //Si la base falla, el navbar sigue funcionando con el estado vacio
   $listaPanelNotificaciones = [];
   $totalPanelNoLeidas = 0;
}

$iconosPanelNotificaciones = [
   'info' => 'fa-circle-info',
   'exito' => 'fa-circle-check',
   'advertencia' => 'fa-triangle-exclamation',
   'error' => 'fa-circle-exclamation',
   'partido' => 'fa-futbol',
   'resultado' => 'fa-clipboard-check',
   'inscripcion' => 'fa-user-plus',
];

$fechaPanelNotificacion = static function (?string $fecha): string {
   if (empty($fecha)) {
      return '';
   }
   $marca = strtotime($fecha);
   return $marca ? date('d/m H:i', $marca) : '';
};

$tokenPanelNotificaciones = \App\Ayudantes\Sesion::generarTokenCSRF();
?>
<div class="notificaciones-jugador">
   <button type="button" class="boton-campana" id="campana-toggle" aria-label="Notificaciones" aria-expanded="false">
      <i class="fa-solid fa-bell"></i>
      <span class="badge-notificaciones <?php echo $totalPanelNoLeidas > 0 ? '' : 'oculto'; ?>" id="badge-notificaciones"><?php echo $totalPanelNoLeidas > 9 ? '9+' : (int) $totalPanelNoLeidas; ?></span>
   </button>

   <div class="panel-notificaciones" id="panel-notificaciones">
      <div class="notificaciones-header">
         <h4>Notificaciones</h4>
         <?php if ($totalPanelNoLeidas > 0): ?>
            <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=notificacion&a=marcarTodas" class="notificaciones-marcar-todas">
               <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($tokenPanelNotificaciones, ENT_QUOTES, 'UTF-8'); ?>">
               <button type="submit" id="btn-marcar-todo-leido">Marcar todo leído</button>
            </form>
         <?php endif; ?>
      </div>

      <div class="notificaciones-lista" id="notificaciones-lista">
         <?php if (empty($listaPanelNotificaciones)): ?>
            <p class="notificaciones-vacio">No tenés notificaciones nuevas.</p>
         <?php else: ?>
            <?php foreach ($listaPanelNotificaciones as $notificacion): ?>
               <?php
               $tipoNotificacion = (string) ($notificacion['tipo'] ?? 'info');
               $iconoNotificacion = $iconosPanelNotificaciones[$tipoNotificacion] ?? 'fa-bell';
               $noLeida = (int) ($notificacion['leido'] ?? 0) === 0;
               ?>
               <div class="notificacion-item <?php echo $noLeida ? 'no-leida' : ''; ?>" data-tipo="<?php echo htmlspecialchars($tipoNotificacion, ENT_QUOTES, 'UTF-8'); ?>">
                  <div class="notificacion-icono">
                     <i class="fa-solid <?php echo $iconoNotificacion; ?>"></i>
                  </div>
                  <div class="notificacion-contenido">
                     <span class="notificacion-titulo"><?php echo htmlspecialchars((string) $notificacion['titulo']); ?></span>
                     <span class="notificacion-mensaje"><?php echo htmlspecialchars((string) $notificacion['mensaje']); ?></span>
                     <span class="notificacion-fecha"><?php echo htmlspecialchars($fechaPanelNotificacion($notificacion['creado_en'] ?? null)); ?></span>
                  </div>
                  <?php if ($noLeida): ?>
                     <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=notificacion&a=marcarLeida" class="notificacion-marcar">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($tokenPanelNotificaciones, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="id" value="<?php echo (int) $notificacion['id']; ?>">
                        <button type="submit" title="Marcar como leída" aria-label="Marcar como leída">
                           <i class="fa-solid fa-check"></i>
                        </button>
                     </form>
                  <?php endif; ?>
               </div>
            <?php endforeach; ?>
         <?php endif; ?>
      </div>
   </div>
</div>
