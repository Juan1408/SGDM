<?php
/**
 * Vista: perfil-publico-organizador.php  (vistas/publico/)
 * Perfil publico de un organizador. Lo carga PerfilControlador::organizador().
 * Variables: $datos (ya filtrado por el modelo segun lo que el organizador deja publico),
 *            $torneos, $esDuenio.
 * Nunca llegan email ni telefono personal.
 */

$urlImagen = function (?string $valor, string $porDefecto): string {
   if (empty($valor)) { return URL_BASE . $porDefecto; }
   return preg_match('#^(https?:)?//#i', $valor) ? $valor : URL_BASE . ltrim($valor, '/');
};

$nombreOrganizacion = $datos['nombre_organizacion'] ?? '';
$nombrePersona = $datos['nombre_completo'] ?? $nombreOrganizacion;
//La organizacion se muestra aparte solo si es distinta del nombre de la persona
$mostrarOrganizacion = $nombreOrganizacion !== '' && $nombreOrganizacion !== $nombrePersona;
$bio = trim((string) ($datos['bio_organizacion'] ?? ''));
$localidad = trim((string) ($datos['localidad'] ?? ''));
$telefonoContacto = trim((string) ($datos['telefono_contacto'] ?? ''));
$sitioWeb = trim((string) ($datos['sitio_web'] ?? ''));
if (!preg_match('#^https?://#i', $sitioWeb)) {
   $sitioWeb = '';
}
$verificado = !empty($datos['verificado_oficial']);
$avatarUrl = $urlImagen($datos['foto_perfil_url'] ?? '', 'img/avatars/a1.png');
$bannerUrl = $urlImagen($datos['banner_url'] ?? '', 'img/bg.jpg');

//Cantidades reales
$totalTorneos = count($torneos);
$enCurso = count(array_filter($torneos, fn($t) => $t['estado'] === 'en_curso'));
$finalizados = count(array_filter($torneos, fn($t) => $t['estado'] === 'finalizado'));

$mesesCorto = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
$formatearFecha = function (?string $fecha) use ($mesesCorto): string {
   if (empty($fecha)) { return 'Fecha a confirmar'; }
   $marca = strtotime($fecha);
   return date('d', $marca) . ' ' . $mesesCorto[(int) date('n', $marca) - 1] . ' ' . date('Y', $marca);
};

//Navbar segun quien mira
$rolSesion = (int) ($_SESSION['rol_id'] ?? 0);
if ($rolSesion === 3) {
   $archivoNavbar = 'navbar-jugador.php';
} elseif ($rolSesion === 2) {
   $archivoNavbar = 'navbar-organizador.php';
} else {
   $archivoNavbar = 'navbar-publico.php';
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>ASCEND - <?php echo htmlspecialchars($nombrePersona); ?></title>

   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
   <script src="<?php echo URL_BASE; ?>js/tema.js"></script>
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/titulos.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/botones.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/navbar.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/footer.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/publico/perfil-publico-organizador.css">

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>

   <!-- NAVBAR ---------------------------->
   <?php include __DIR__ . '/../componentes/' . $archivoNavbar; ?>

   <main class="equipo-pagina">

      <?php if ($esDuenio && isset($_GET['exito'])): ?>
         <div class="mensaje-exito">Perfil actualizado correctamente.</div>
      <?php endif; ?>

      <!-- ============================== -->
      <!-- HEADER: banner, foto, datos    -->
      <!-- ============================== -->
      <header class="equipo-hero">

         <div class="equipo-hero-banner">
            <img src="<?php echo htmlspecialchars($bannerUrl); ?>" alt="Banner del organizador">
         </div>

         <div class="equipo-identidad">
            <div class="equipo-logo">
               <img src="<?php echo htmlspecialchars($avatarUrl); ?>" alt="Foto del organizador">
            </div>

            <div class="equipo-info-texto">
               <span class="organizador-etiqueta">Organizador<?php echo $verificado ? ' · <i class="fa-solid fa-circle-check"></i> Verificado' : ''; ?></span>
               <h1 class="equipo-nombre"><?php echo htmlspecialchars($nombrePersona); ?></h1>

               <?php if ($bio !== ''): ?>
                  <p class="equipo-descripcion"><?php echo nl2br(htmlspecialchars($bio)); ?></p>
               <?php endif; ?>

               <?php if ($mostrarOrganizacion || $localidad !== '' || $telefonoContacto !== '' || $sitioWeb !== ''): ?>
               <div class="equipo-datos">
                  <?php if ($mostrarOrganizacion): ?>
                     <div class="equipo-dato">
                        <i class="fa-solid fa-building"></i>
                        <span><?php echo htmlspecialchars($nombreOrganizacion); ?></span>
                     </div>
                  <?php endif; ?>
                  <?php if ($localidad !== ''): ?>
                     <div class="equipo-dato">
                        <i class="fa-solid fa-location-dot"></i>
                        <span><?php echo htmlspecialchars($localidad); ?></span>
                     </div>
                  <?php endif; ?>
                  <?php if ($telefonoContacto !== ''): ?>
                     <div class="equipo-dato">
                        <i class="fa-solid fa-phone"></i>
                        <span><?php echo htmlspecialchars($telefonoContacto); ?></span>
                     </div>
                  <?php endif; ?>
                  <?php if ($sitioWeb !== ''): ?>
                     <div class="equipo-dato">
                        <i class="fa-solid fa-globe"></i>
                        <a href="<?php echo htmlspecialchars($sitioWeb); ?>" target="_blank" rel="noopener noreferrer"><?php echo htmlspecialchars($sitioWeb); ?></a>
                     </div>
                  <?php endif; ?>
               </div>
               <?php endif; ?>
            </div>
         </div>

         <div class="equipo-stats">
            <div class="stat-card">
               <strong><?php echo $totalTorneos; ?></strong>
               <span>Torneos</span>
            </div>
            <div class="stat-card">
               <strong><?php echo $enCurso; ?></strong>
               <span>En curso</span>
            </div>
            <div class="stat-card">
               <strong><?php echo $finalizados; ?></strong>
               <span>Finalizados</span>
            </div>
         </div>

      </header>

      <!-- ============================== -->
      <!-- TORNEOS QUE ORGANIZA           -->
      <!-- ============================== -->
      <section class="team-members" id="torneos-organiza">

         <h2 class="seccion-gradiente-titulo">Torneos que organiza</h2>

         <?php if (empty($torneos)): ?>
            <p>Todavía no publicó ningún torneo.</p>
         <?php else: ?>
            <?php
            // Se muestran 9 torneos al inicio; el botón "Ver más" revela de a 3
            $torneosIniciales = 9;
            $torneosPaso = 3;
            ?>
            <ul class="torneos-organiza-grid" id="torneos-organiza-lista">
               <?php foreach (array_values($torneos) as $indiceTorneo => $torneo): ?>
                  <li<?php echo $indiceTorneo >= $torneosIniciales ? ' data-oculto="1" style="display:none"' : ''; ?>>
                     <a href="<?php echo URL_BASE; ?>index.php?c=torneo&a=detalle&id=<?php echo (int) $torneo['id']; ?>" class="torneo-organiza-card">
                        <img src="<?php echo htmlspecialchars($urlImagen($torneo['banner_url'] ?? '', 'img/torneos/card1.jpg')); ?>" alt="<?php echo htmlspecialchars($torneo['nombre']); ?>">
                        <div class="torneo-organiza-info">
                           <h3><?php echo htmlspecialchars($torneo['nombre']); ?></h3>
                           <span><?php echo htmlspecialchars($torneo['nombre_juego']); ?> · <?php echo htmlspecialchars($formatearFecha($torneo['fecha_inicio'])); ?></span>
                        </div>
                     </a>
                  </li>
               <?php endforeach; ?>
            </ul>

            <?php if (count($torneos) > $torneosIniciales): ?>
               <div class="ver-mas-contenedor">
                  <button type="button" id="torneos-organiza-ver-mas" class="boton boton--ver-mas" data-paso="<?php echo $torneosPaso; ?>">Ver más</button>
               </div>
               <script>
                  (function () {
                     var boton = document.getElementById('torneos-organiza-ver-mas');
                     var paso = parseInt(boton.getAttribute('data-paso'), 10) || 3;
                     boton.addEventListener('click', function () {
                        var ocultos = document.querySelectorAll('#torneos-organiza-lista li[data-oculto]');
                        for (var i = 0; i < paso && i < ocultos.length; i++) {
                           ocultos[i].style.display = '';
                           ocultos[i].removeAttribute('data-oculto');
                        }
                        if (!document.querySelector('#torneos-organiza-lista li[data-oculto]')) {
                           boton.parentNode.style.display = 'none';
                        }
                     });
                  })();
               </script>
            <?php endif; ?>
         <?php endif; ?>

      </section>

   </main>

   <!-- FOOTER ---------------------------->
   <?php include __DIR__ . '/../componentes/footer.php'; ?>

   <?php if ($archivoNavbar === 'navbar-publico.php'): ?>
   <script src="<?php echo URL_BASE; ?>js/navbar.js"></script>
   <script>document.dispatchEvent(new Event("navbarPublicoListo"));</script>
   <?php endif; ?>

</body>
</html>