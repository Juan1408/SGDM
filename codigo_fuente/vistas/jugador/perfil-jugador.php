<?php
/**
 * Vista: perfil-jugador.php
 * Requiere que $datos venga cargado por panelJugadorControlador (perfil() o actualizarPerfil()).
 * $datos viene de Jugador::obtenerPerfil() -> puede tener campos nulos si el jugador
 * todavia no tiene fila en perfiles_jugadores.
 */

$nombre = $datos['nombre_completo'] ?? '';
$email = $datos['email'] ?? '';
$bio = $datos['bio'] ?? '';
$ciudad = $datos['ciudad'] ?? '';
$pais = $datos['pais'] ?? '';
$fechaNacimiento = $datos['fecha_nacimiento'] ?? '';
$nivel = $datos['nivel'] ?? 1;
$avatarUrl = $datos['foto_perfil_url'] ?: URL_BASE . 'img/avatars/a1.png';
$bannerUrl = $datos['banner_url'] ?: URL_BASE . 'img/bg.jpg';

//Texto de ubicacion para mostrar en modo lectura (ciudad + pais)
$ubicacionTexto = trim($ciudad . (($ciudad && $pais) ? ' - ' : '') . $pais);
if ($ubicacionTexto === '') {
   $ubicacionTexto = 'Sin especificar';
}

//Calculamos la edad a partir de fecha_nacimiento para mostrarla en modo lectura
$edadTexto = 'Edad: sin especificar';
if (!empty($fechaNacimiento)) {
   $nacimiento = new DateTime($fechaNacimiento);
   $hoy = new DateTime();
   $edadTexto = 'Edad: ' . $hoy->diff($nacimiento)->y;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>ASCEND - Perfil Jugador</title>

   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/navbar.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/footer.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/jugador/perfil-jugador.css">

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>

   <!-- NAVBAR ---------------------------->
   <?php include __DIR__ . '/../componentes/navbar-jugador.php'; ?>

   <main class="equipo-pagina">

      <?php if (!empty($error)): ?>
         <div class="mensaje-error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <?php if (isset($_GET['exito'])): ?>
         <div class="mensaje-exito">Perfil actualizado correctamente.</div>
      <?php endif; ?>

      <form action="<?php echo URL_BASE; ?>index.php?c=panelJugador&a=actualizarPerfil" method="POST" id="form-perfil-jugador">

         <!-- ============================== -->
         <!-- HEADER: banner, avatar, stats  -->
         <!-- ============================== -->
         <header class="equipo-hero" id="perfil-banner">

            <button type="button" class="boton-cambiar-banner oculto" id="btn-cambiar-banner">
               <i class="fa-solid fa-camera"></i> Cambiar banner
            </button>
            <input type="file" id="input-banner" accept="image/*" hidden>

            <div class="equipo-hero-banner">
               <img src="<?php echo htmlspecialchars($bannerUrl); ?>" alt="Banner del jugador" id="perfil-banner-img">
            </div>

            <div class="equipo-identidad">
               <div class="equipo-logo">
                  <img src="<?php echo htmlspecialchars($avatarUrl); ?>" alt="Avatar jugador" id="perfil-avatar-img">

                  <label for="input-avatar" class="boton-cambiar-avatar oculto" id="btn-cambiar-avatar" title="Cambiar foto">
                     <i class="fa-solid fa-camera"></i>
                  </label>
                  <input type="file" id="input-avatar" accept="image/*" hidden>
               </div>

               <div class="equipo-info-texto">
                  <span class="perfil-nivel">Nivel <?php echo (int) $nivel; ?></span>

                  <h1 class="equipo-nombre" id="perfil-nombre"><?php echo htmlspecialchars($nombre); ?></h1>
                  <input type="hidden" name="nombre_completo" id="input-nombre-completo">

                  <p class="equipo-descripcion" id="perfil-sobre-mi"><?php echo htmlspecialchars($bio); ?></p>
                  <input type="hidden" name="bio" id="input-bio">

                  <ul class="equipo-datos">
                     <li class="equipo-dato">
                        <i class="fa-solid fa-location-dot"></i>
                        <span id="perfil-ubicacion-vista"><?php echo htmlspecialchars($ubicacionTexto); ?></span>
                        <input type="text" name="ciudad" id="perfil-ciudad" placeholder="Ciudad"
                           value="<?php echo htmlspecialchars($ciudad); ?>" class="oculto campo-formulario">
                        <input type="text" name="pais" id="perfil-pais" placeholder="Pais"
                           value="<?php echo htmlspecialchars($pais); ?>" class="oculto campo-formulario">
                     </li>
                     <li class="equipo-dato">
                        <i class="fa-solid fa-envelope"></i>
                        <span id="perfil-email"><?php echo htmlspecialchars($email); ?></span>
                        <input type="hidden" name="email" id="input-email">
                     </li>
                     <li class="equipo-dato">
                        <i class="fa-solid fa-cake-candles"></i>
                        <span id="perfil-edad-vista"><?php echo htmlspecialchars($edadTexto); ?></span>
                        <input type="date" name="fecha_nacimiento" id="perfil-fecha-nacimiento"
                           value="<?php echo htmlspecialchars($fechaNacimiento); ?>" class="oculto campo-formulario">
                     </li>
                  </ul>

                  <div class="perfil-acciones-edicion oculto" id="perfil-acciones-edicion">
                     <button type="button" id="btn-editar-perfil" class="boton-editar-perfil">
                        <i class="fa-solid fa-pen"></i> Editar Perfil
                     </button>

                     <div class="grupo-guardar-cancelar oculto" id="grupo-guardar-cancelar">
                        <button type="button" id="btn-guardar-perfil" class="boton-registro">Guardar</button>
                        <button type="button" id="btn-cancelar-edicion" class="boton-cancelar-edicion">Cancelar</button>
                     </div>
                  </div>
               </div>
            </div>

            <!-- Las estadisticas (partidas, victorias, etc) todavia son de ejemplo -->
            <!-- Se conectan cuando se programe el modulo de Torneos / Resultados -->
            <dl class="equipo-estadisticas" id="estadisticas">
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
               <div class="tarjeta-estadistica">
                  <dd>3.928</dd>
                  <dt>Puntos</dt>
               </div>
            </dl>

         </header>

      </form>

      <!-- ============================== -->
      <!-- REDES SOCIALES                 -->
      <!-- ============================== -->
      <section class="tarjeta-equipo equipo-capitanes">
         <div class="capitanes-fila capitanes-fila--solo-boton">
            <p class="perfil-organizador-cta-texto">Encontrame también en:</p>
            <div class="perfil-redes">
               <a href="../publico/en-construccion.html"><i class="fa-brands fa-discord"></i></a>
               <a href="../publico/en-construccion.html"><i class="fa-brands fa-instagram"></i></a>
               <a href="../publico/en-construccion.html"><i class="fa-brands fa-x-twitter"></i></a>
            </div>
         </div>
      </section>

      
       <!-- EQUIPOS: ahora con datos reales -->
      <!-- ============================== -->
      <section class="equipo-miembros">

         <h2 class="seccion-gradiente-titulo">Equipos</h2>

         <?php if (empty($equipos)): ?>
            <p>Todavía no formás parte de ningún equipo. <a href="<?php echo URL_BASE; ?>index.php?c=equipo&a=crear">Creá el tuyo</a>.</p>
         <?php else: ?>
            <ul class="mis-equipos-grid">
               <?php foreach ($equipos as $equipo): ?>
                  <li>
                     <article class="mi-tarjeta-equipo">
                        <img src="<?php echo htmlspecialchars($equipo['escudo_url'] ?: URL_BASE . 'img/logos/canelones.png'); ?>" alt="<?php echo htmlspecialchars($equipo['nombre_equipo']); ?>">

                        <div class="mi-equipo-info">
                           <h3><?php echo htmlspecialchars($equipo['nombre_equipo']); ?></h3>
                           <?php if ($equipo['es_capitan']): ?>
                              <span class="mi-equipo-badge badge-lider">Líder</span>
                           <?php else: ?>
                              <span class="mi-equipo-badge badge-miembro">Miembro</span>
                           <?php endif; ?>
                        </div>

                        <?php if ($equipo['es_capitan']): ?>
                           <a href="<?php echo URL_BASE; ?>index.php?c=equipo&a=gestionar&id=<?php echo (int) $equipo['id']; ?>" class="boton-secundario">
                              Gestionar
                           </a>
                        <?php else: ?>
                           <a href="<?php echo URL_BASE; ?>index.php?c=equipo&a=verPerfil&id=<?php echo (int) $equipo['id']; ?>" class="boton-secundario">
                              Ver perfil
                           </a>
                        <?php endif; ?>
                     </article>
                  </li>
               <?php endforeach; ?>
            </ul>
         <?php endif; ?>

      </section>

      <!-- ============================== -->
      <!-- TORNEOS (todavia de ejemplo -- se conecta en la Fase de Torneos) -->
      <!-- ============================== -->
      <section class="equipo-miembros" id="mis-torneos">

         <h2 class="seccion-gradiente-titulo">Torneos</h2>

         <ul class="torneos-organiza-grilla">

            <li>
               <a href="detalle-torneo.html?id=copa-ascend" class="torneos-organiza-tarjeta">
                  <img src="<?php echo URL_BASE; ?>img/torneos/card1.jpg" alt="Virtual Odyssey Chronicles">
                  <div class="torneo-organiza-info">
                     <h3>Virtual Odyssey Chronicles</h3>
                     <span>Puesto: 3 · Puntos: 1.124</span>
                  </div>
               </a>
            </li>

            <li>
               <a href="detalle-torneo.html?id=copa-ascend" class="torneos-organiza-tarjeta">
                  <img src="<?php echo URL_BASE; ?>img/torneos/card2.png" alt="Valorant Champion Series">
                  <div class="torneo-organiza-info">
                     <h3>Valorant Champion Series</h3>
                     <span>Puesto: 1 · Puntos: 2.300</span>
                  </div>
               </a>
            </li>

            <li>
               <a href="detalle-torneo.html?id=futbol-5" class="torneos-organiza-tarjeta">
                  <img src="<?php echo URL_BASE; ?>img/torneos/card3.png" alt="Copa Futbol 5">
                  <div class="torneo-organiza-info">
                     <h3>Copa Futbol 5</h3>
                     <span>Puesto: 4 · Puntos: 980</span>
                  </div>
               </a>
            </li>

         </ul>

      </section>

      <!-- ============================== -->
      <!-- TROFEOS (todavia de ejemplo)    -->
      <!-- ============================== -->
      <section class="tarjeta-equipo equipo-trofeos">

         <h2>Trofeos</h2>

         <ul class="trofeos-fila">
            <li><article class="tarjeta-trofeo"><i class="fa-solid fa-trofeo"></i></article></li>
            <li><article class="tarjeta-trofeo"><i class="fa-solid fa-medal"></i></article></li>
            <li><article class="tarjeta-trofeo"><i class="fa-solid fa-crown"></i></article></li>
            <li><article class="tarjeta-trofeo"><i class="fa-solid fa-trofeo"></i></article></li>
            <li><article class="tarjeta-trofeo"><i class="fa-solid fa-medal"></i></article></li>
            <li><article class="tarjeta-trofeo"><i class="fa-solid fa-crown"></i></article></li>
         </ul>

      </section>

      <!-- ============================== -->
      <!-- LOGROS (todavia de ejemplo -- se conecta en la Fase de Logros) -->
      <!-- ============================== -->
      <section class="tarjeta-equipo equipo-logros" id="historial">

         <h2>Logros</h2>

         <ul class="logros-grilla">

            <li>
               <article class="logro-tarjeta desbloqueado">
                  <div class="logro-icon"></div>
                  <div>
                     <h3>Primera batalla</h3>
                     <p>Jugaste tu primer torneo</p>
                  </div>
                  <span>Logrado</span>
               </article>
            </li>

            <li>
               <article class="logro-tarjeta desbloqueado">
                  <div class="logro-icon"></div>
                  <div>
                     <h3>Campeón</h3>
                     <p>Ganaste un torneo oficial</p>
                  </div>
                  <span>Logrado</span>
               </article>
            </li>

            <li>
               <article class="logro-tarjeta en-curso">
                  <div class="logro-icon"></div>
                  <div>
                     <h3>Veterano</h3>
                     <p>6/10 torneos</p>
                  </div>
                  <span>En curso</span>
               </article>
            </li>

            <li>
               <article class="logro-tarjeta bloqueado">
                  <div class="logro-icon"></div>
                  <div>
                     <h3>En racha</h3>
                     <p>Ganá 3 partidas distintas</p>
                  </div>
                  <span>Bloqueado</span>
               </article>
            </li>

         </ul>

      </section>

      <!-- ============================== -->
      <!-- JUEGOS QUE PARTICIPA (todavia de ejemplo) -->
      <!-- ============================== -->
      <section class="equipo-miembros">

         <h2 class="seccion-gradiente-titulo">Juegos que participa</h2>

         <ul class="juegos-grillas">

            <li>
               <article class="tarjeta-juego lol">
                  <div class="tarjeta-info">
                     <img src="<?php echo URL_BASE; ?>img/juegos/dos.jpg" alt="League of Legends">
                     <div class="tarjeta-overlay">
                        <h3>League of Legends</h3>
                        <p>4 torneos</p>
                     </div>
                  </div>
               </article>
            </li>

            <li>
               <article class="tarjeta-juego apex">
                  <div class="tarjeta-info">
                     <img src="<?php echo URL_BASE; ?>img/juegos/fly.jpg" alt="Apex Legends">
                     <div class="tarjeta-overlay">
                        <h3>Apex Legends</h3>
                        <p>2 torneos</p>
                     </div>
                  </div>
               </article>
            </li>

            <li>
               <article class="tarjeta-juego cs">
                  <div class="tarjeta-info">
                     <img src="<?php echo URL_BASE; ?>img/juegos/odyssey.png" alt="Counter Strike">
                     <div class="tarjeta-overlay">
                        <h3>Counter Strike</h3>
                        <p>5 torneos</p>
                     </div>
                  </div>
               </article>
            </li>

            <li>
               <article class="tarjeta-juego wow">
                  <div class="tarjeta-info">
                     <img src="<?php echo URL_BASE; ?>img/juegos/quantum.png" alt="World of Warcraft">
                     <div class="tarjeta-overlay">
                        <h3>World of Warcraft</h3>
                        <p>1 torneo</p>
                     </div>
                  </div>
               </article>
            </li>

         </ul>

      </section>

   </main>

   <!-- FOOTER ---------------------------->
   <?php include __DIR__ . '/../componentes/footer.html'; ?>

   <script src="<?php echo URL_BASE; ?>js/perfil-jugador.js"></script>

</body>
</html>
