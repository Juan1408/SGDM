<?php
/**
 * Vista: perfil-equipo.php
 * Requiere que vengan cargados desde EquipoControlador::verPerfil():
 * $equipo (array de la tabla equipos), $capitanes, $miembros, $yaEsMiembro (bool)
 *
 * Sin JavaScript por ahora: es una pantalla de lectura. El boton "Solicitar Unirse"
 * todavia no esta conectado (se resuelve en el Paso 3.5).
 */

$nombreEquipo = $equipo['nombre_equipo'];
$descripcion = $equipo['descripcion'] ?: 'Este equipo todavía no cargó una descripción.';
$ubicacion = $equipo['ubicacion'] ?: 'Sin especificar';
$anioFundacion = $equipo['anio_fundacion'] ?: 'Sin especificar';
$escudoUrl = $equipo['escudo_url'] ?: URL_BASE . 'img/logos/canelones.png';
$bannerUrl = $equipo['banner_url'] ?: URL_BASE . 'img/live.jpg';
?>
<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>ASCEND - <?php echo htmlspecialchars($nombreEquipo); ?></title>

   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/navbar.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/footer.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/jugador/perfil-equipo.css">

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>

   <!-- NAVBAR ---------------------------->
   <?php include __DIR__ . '/../componentes/navbar-jugador.php'; ?>

   <main class="equipo-pagina">

      <?php if (isset($_GET['solicitudEnviada'])): ?>
         <div class="mensaje-exito">Tu solicitud fue enviada. El capitán la va a revisar.</div>
      <?php endif; ?>

      <!-- ============================== -->
      <!-- HEADER: banner, logo, datos    -->
      <!-- ============================== -->
      <header class="equipo-hero">

            <div class="equipo-hero-banner">
            <img src="<?php echo htmlspecialchars($bannerUrl); ?>" alt="Banner del equipo">
 <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=guardados&a=<?php echo $equipoGuardado ? 'quitar' : 'guardar'; ?>" class="form-guardar-equipo">
               <input type="hidden" name="tipo" value="equipo">
               <input type="hidden" name="referencia_id" value="<?php echo (int) $equipo['id']; ?>">
               <input type="hidden" name="volver_a" value="<?php echo URL_BASE; ?>index.php?c=equipo&a=verPerfil&id=<?php echo (int) $equipo['id']; ?>">
               <button type="submit" class="boton-guardar-equipo <?php echo $equipoGuardado ? 'guardado' : ''; ?>" title="<?php echo $equipoGuardado ? 'Quitar de guardados' : 'Guardar equipo'; ?>">
                  <i class="<?php echo $equipoGuardado ? 'fa-solid' : 'fa-regular'; ?> fa-bookmark"></i>
               </button>
            </form>
           
         </div>

         <div class="equipo-identidad">
            <div class="equipo-logo">
               <img src="<?php echo htmlspecialchars($escudoUrl); ?>" alt="Escudo del equipo">
            </div>

            <div class="equipo-info-texto">
               <h1 class="equipo-nombre"><?php echo htmlspecialchars($nombreEquipo); ?></h1>

               <p class="equipo-descripcion"><?php echo htmlspecialchars($descripcion); ?></p>

               <ul class="equipo-datos">
                  <li class="equipo-dato">
                     <i class="fa-solid fa-location-dot"></i>
                     <span><?php echo htmlspecialchars($ubicacion); ?></span>
                  </li>
                  <li class="equipo-dato">
                     <i class="fa-solid fa-calendar"></i>
                     <span>Fundado en <?php echo htmlspecialchars($anioFundacion); ?></span>
                  </li>
               </ul>
            </div>
         </div>

         <!-- Las pestañas Torneos/Ranking/Juegos todavia no tienen contenido real -->
         <!-- (dependen de las fases de Torneos, que no arrancaron). Se muestran -->
         <!-- solo como referencia visual, sin logica de cambio de pestaña por ahora. -->
         <nav class="equipo-tabs" aria-label="Secciones del equipo">
            <ul class="equipo-tabs-lista">
               <li><button type="button" class="equipo-tab active" data-tab="miembros">Miembros</button></li>
               <li><button type="button" class="equipo-tab" data-tab="torneos" disabled>Torneos</button></li>
               <li><button type="button" class="equipo-tab" data-tab="ranking" disabled>Ranking</button></li>
            </ul>
         </nav>

         <!-- Estadisticas todavia de ejemplo -- se conectan en la fase de Torneos/Resultados -->
         <dl class="equipo-estadisticas">
            <div class="tarjeta-estadistica">
               <dd>38</dd>
               <dt>Partidas</dt>
            </div>
            <div class="tarjeta-estadistica">
               <dd>24</dd>
               <dt>Victorias</dt>
            </div>
            <div class="tarjeta-estadistica">
               <dd>3</dd>
               <dt>Torneos ganados</dt>
            </div>
         </dl>

      </header>

      <!-- ============================== -->
      <!-- CAPITANES / LÍDERES            -->
      <!-- ============================== -->
      <section class="tarjeta-equipo equipo-capitanes">

         <div class="capitanes-encabezado">
            <h2>Capitanes / Líderes</h2>
         </div>

         <div class="capitanes-fila">
            <ul class="capitanes-lista">
               <?php foreach ($capitanes as $capitan): ?>
                  <li>
                     <article class="capitan-item">
                        <img src="<?php echo htmlspecialchars($capitan['foto_perfil_url'] ?: URL_BASE . 'img/avatars/a1.png'); ?>" alt="Foto del capitán">
                        <span><?php echo htmlspecialchars($capitan['nombre_completo']); ?></span>
                     </article>
                  </li>
               <?php endforeach; ?>
            </ul>

            <?php if ($yaEsMiembro): ?>
               <span class="ya-eres-miembro">Ya sos parte de este equipo</span>
            <?php elseif ($tieneSolicitudPendiente): ?>
               <span class="ya-eres-miembro">Solicitud enviada, esperando respuesta del capitán</span>
            <?php else: ?>
               <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=equipo&a=solicitarUnion">
                  <input type="hidden" name="equipo_id" value="<?php echo (int) $equipo['id']; ?>">
                  <button type="submit" class="boton-solicitar-unirse">Solicitar Unirse</button>
               </form>
            <?php endif; ?>
         </div>

      </section>
      

      <!-- ============================== -->
      <!-- MIEMBROS DEL EQUIPO            -->
      <!-- ============================== -->
      <section class="equipo-miembros" id="miembros">

         <h2 class="seccion-gradiente-titulo">Miembros del equipo</h2>

         <form method="GET" action="<?php echo URL_BASE; ?>index.php" class="miembros-buscador">
            <input type="hidden" name="c" value="equipo">
            <input type="hidden" name="a" value="verPerfil">
            <input type="hidden" name="id" value="<?php echo (int) $equipo['id']; ?>">
            <input type="text" name="filtro" placeholder="Buscar entre los miembros..." value="<?php echo htmlspecialchars($filtroMiembro); ?>">
            <button type="submit">Buscar</button>
         </form>

         <?php if (empty($miembros)): ?>
            <p>Este equipo todavía no tiene miembros.</p>
         <?php else: ?>
            <ul class="miembros-grilla">
               <?php foreach ($miembros as $miembro): ?>
                  <li>
                     <article class="miembro-tarjeta">
                        <div class="miembro-foto">
                           <div class="miembro-fondo"></div>
                           <img src="<?php echo htmlspecialchars($miembro['foto_perfil_url'] ?: URL_BASE . 'img/avatars/a1.png'); ?>" alt="Jugador">
                        </div>
                        <h3><?php echo htmlspecialchars($miembro['nombre_completo']); ?></h3>
                        <?php if (!empty($miembro['posicion'])): ?>
                           <span class="miembro-posicion"><?php echo htmlspecialchars($miembro['posicion']); ?></span>
                        <?php endif; ?>
                     </article>
                  </li>
               <?php endforeach; ?>
            </ul>
         <?php endif; ?>

      </section>

      <!-- ============================== -->
      <!-- NOVEDADES (todavia de ejemplo -- no existe tabla de noticias) -->
      <!-- ============================== -->
      <section class="tarjeta-equipo equipo-noticias">

         <h2>Novedades</h2>

         <ul class="noticias-lista">
            <li>
               <article class="noticia-item">
                  <img src="<?php echo URL_BASE; ?>img/04.jpg" alt="Noticia sobre la cancha del Club FC">
                  <div class="noticia-info">
                     <h3>Noticia sobre la cancha del Club FC</h3>
                     <span>Fecha: 20/06/2026</span>
                  </div>
               </article>
            </li>
         </ul>

      </section>

   </main>

   <!-- FOOTER ---------------------------->
   <?php include __DIR__ . '/../componentes/footer.html'; ?>


</body>
</html>
