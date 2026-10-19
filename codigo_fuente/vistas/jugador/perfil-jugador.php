<?php
/**
 * Vista: perfil-jugador.php
 * Tiene dos modos (los dos son de SOLO LECTURA):
 *  - Perfil propio ($esPublico = false): lo carga panelJugadorControlador::perfil(). Muestra todo.
 *    Para editar se usa la pagina aparte editar-perfil-jugador.php (menu Jugador > Editar perfil).
 *  - Perfil publico ($esPublico = true): lo carga PerfilControlador::jugador(). Solo lectura; el modelo
 *    ya dejo en null todo lo que el jugador no marco como publico (nunca llegan email ni telefono).
 * Variables que llegan: $datos, $equipos, $torneos, $camposPublicos (campos opcionales publicos),
 *                       y $esPublico.
 */

$esPublico = !empty($esPublico);
$camposPublicos = $camposPublicos ?? [];
$torneos = $torneos ?? [];

//Una opcion es visible si es el perfil propio, o si el jugador la marco como publica
$ver = fn(string $campo): bool => !$esPublico || in_array($campo, $camposPublicos, true);

//Imagenes: pueden ser rutas relativas a publico/ (subidas) o URLs completas
$urlImagen = function (?string $valor, string $porDefecto): string {
   if (empty($valor)) { return URL_BASE . $porDefecto; }
   return preg_match('#^(https?:)?//#i', $valor) ? $valor : URL_BASE . ltrim($valor, '/');
};

$nombre = $datos['nombre_completo'] ?? '';
if ($esPublico && !empty($datos['apodo_gamertag'])) {
   //En el perfil publico se muestra el apodo; si no tiene, el nombre
   $nombre = $datos['apodo_gamertag'];
}
$email = $datos['email'] ?? '';
$bio = $datos['bio'] ?? '';
$ciudad = $datos['ciudad'] ?? '';
$pais = $datos['pais'] ?? '';
$fechaNacimiento = $datos['fecha_nacimiento'] ?? '';
$discord = $datos['discord_tag'] ?? '';
$instagram = $datos['instagram_url'] ?? '';
$twitter = $datos['twitter_url'] ?? '';
$nivel = $datos['nivel'] ?? 1;
$avatarUrl = $urlImagen($datos['foto_perfil_url'] ?? '', 'img/avatars/a1.png');
$bannerUrl = $urlImagen($datos['banner_url'] ?? '', 'img/bg.jpg');

//Ubicacion (ciudad + pais)
$ubicacionTexto = trim($ciudad . (($ciudad && $pais) ? ' - ' : '') . $pais);
$ubicacionVista = $ubicacionTexto !== '' ? $ubicacionTexto : 'Sin especificar';

//Edad a partir de fecha_nacimiento
$edadTexto = 'Edad: sin especificar';
if (!empty($fechaNacimiento)) {
   $edadTexto = 'Edad: ' . (new DateTime())->diff(new DateTime($fechaNacimiento))->y;
}

//Que mostrar en el encabezado: en el perfil propio siempre; en el publico, solo si es publico Y tiene dato
$mostrarBio = $ver('bio') && ($bio !== '' || !$esPublico);
$mostrarUbicacion = $ver('ubicacion') && ($ubicacionTexto !== '' || !$esPublico);
$mostrarEdad = $ver('edad') && (!empty($fechaNacimiento) || !$esPublico);

//Redes: los enlaces solo si son http(s)
$esEnlace = fn(string $u): bool => (bool) preg_match('#^https?://#i', $u);
$mostrarDiscord = $ver('discord') && $discord !== '';
$mostrarInstagram = $ver('instagram') && $instagram !== '' && $esEnlace($instagram);
$mostrarTwitter = $ver('twitter') && $twitter !== '' && $esEnlace($twitter);
$hayRedes = $mostrarDiscord || $mostrarInstagram || $mostrarTwitter;

$etiquetaEstado = ['confirmado' => 'Confirmado', 'pendiente' => 'Pendiente de aprobación'];

//Navbar: el perfil propio usa la del jugador; el publico, la que corresponde a quien lo mira
$rolSesion = (int) ($_SESSION['rol_id'] ?? 0);
if (!$esPublico || $rolSesion === 3) {
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
   <?php include __DIR__ . '/../componentes/' . $archivoNavbar; ?>

   <main class="equipo-pagina">

      <?php if (!$esPublico && isset($_GET['exito'])): ?>
         <div class="mensaje-exito">Perfil actualizado correctamente.</div>
      <?php endif; ?>

         <!-- ============================== -->
         <!-- HEADER: banner, avatar, datos  -->
         <!-- ============================== -->
         <header class="equipo-hero" id="perfil-banner">

            <div class="equipo-hero-banner">
               <img src="<?php echo htmlspecialchars($bannerUrl); ?>" alt="Banner del jugador" id="perfil-banner-img">
            </div>

            <div class="equipo-identidad">
               <div class="equipo-logo">
                  <img src="<?php echo htmlspecialchars($avatarUrl); ?>" alt="Avatar jugador" id="perfil-avatar-img">
               </div>

               <div class="equipo-info-texto">
                  <span class="perfil-nivel">Nivel <?php echo (int) $nivel; ?></span>

                  <h1 class="equipo-nombre" id="perfil-nombre"><?php echo htmlspecialchars($nombre); ?></h1>

                  <?php if ($mostrarBio): ?>
                  <p class="equipo-descripcion" id="perfil-sobre-mi"><?php echo htmlspecialchars($bio !== '' ? $bio : 'Todavía no escribiste tu biografía.'); ?></p>
                  <?php endif; ?>

                  <ul class="equipo-datos">
                     <?php if ($mostrarUbicacion): ?>
                     <li class="equipo-dato">
                        <i class="fa-solid fa-location-dot"></i>
                        <span id="perfil-ubicacion-vista"><?php echo htmlspecialchars($ubicacionVista); ?></span>
                     </li>
                     <?php endif; ?>

                     <?php if (!$esPublico): ?>
                     <li class="equipo-dato">
                        <i class="fa-solid fa-envelope"></i>
                        <span id="perfil-email"><?php echo htmlspecialchars($email); ?></span>
                     </li>
                     <?php endif; ?>

                     <?php if ($mostrarEdad): ?>
                     <li class="equipo-dato">
                        <i class="fa-solid fa-cake-candles"></i>
                        <span id="perfil-edad-vista"><?php echo htmlspecialchars($edadTexto); ?></span>
                     </li>
                     <?php endif; ?>
                  </ul>
               </div>
            </div>

            <?php if (!$esPublico): ?>
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
            <?php endif; ?>

         </header>

      <!-- ============================== -->
      <!-- REDES SOCIALES (datos reales)  -->
      <!-- ============================== -->
      <?php if ($hayRedes || !$esPublico): ?>
      <section class="tarjeta-equipo equipo-capitanes">
         <div class="capitanes-fila capitanes-fila--solo-boton">
            <?php if ($hayRedes): ?>
               <p class="perfil-organizador-cta-texto"><?php echo $esPublico ? 'Encontralo también en:' : 'Encontrame también en:'; ?></p>
               <div class="perfil-redes">
                  <?php if ($mostrarDiscord): ?>
                     <span class="perfil-red-discord" title="Discord"><i class="fa-brands fa-discord"></i> <?php echo htmlspecialchars($discord); ?></span>
                  <?php endif; ?>
                  <?php if ($mostrarInstagram): ?>
                     <a href="<?php echo htmlspecialchars($instagram); ?>" target="_blank" rel="noopener noreferrer" title="Instagram"><i class="fa-brands fa-instagram"></i></a>
                  <?php endif; ?>
                  <?php if ($mostrarTwitter): ?>
                     <a href="<?php echo htmlspecialchars($twitter); ?>" target="_blank" rel="noopener noreferrer" title="X"><i class="fa-brands fa-x-twitter"></i></a>
                  <?php endif; ?>
               </div>
            <?php else: ?>
               <p class="perfil-organizador-cta-texto">Todavía no agregaste tus redes. Tocá "Editar Perfil" para sumarlas.</p>
            <?php endif; ?>
         </div>
      </section>
      <?php endif; ?>


      <!-- EQUIPOS: ahora con datos reales -->
      <!-- ============================== -->
      <section class="equipo-miembros">

         <h2 class="seccion-gradiente-titulo">Equipos</h2>

         <?php if (empty($equipos)): ?>
            <?php if ($esPublico): ?>
               <p>Todavía no forma parte de ningún equipo.</p>
            <?php else: ?>
               <p>Todavía no formás parte de ningún equipo. <a href="<?php echo URL_BASE; ?>index.php?c=equipo&a=crear">Creá el tuyo</a>.</p>
            <?php endif; ?>
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

                        <?php if (!$esPublico && $equipo['es_capitan']): ?>
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
      <!-- TORNEOS (datos reales)         -->
      <!-- ============================== -->
      <?php if ($ver('torneos')): ?>
      <section class="equipo-miembros" id="mis-torneos">

         <h2 class="seccion-gradiente-titulo">Torneos</h2>

         <?php if (empty($torneos)): ?>
            <p><?php echo $esPublico ? 'Todavía no participa en ningún torneo.' : 'Todavía no te inscribiste en ningún torneo.'; ?></p>
         <?php else: ?>
            <ul class="torneos-organiza-grilla">
               <?php foreach ($torneos as $t): ?>
                  <li>
                     <a href="<?php echo URL_BASE; ?>index.php?c=torneo&a=detalle&id=<?php echo (int) $t['torneo_id']; ?>" class="torneos-organiza-tarjeta">
                        <img src="<?php echo htmlspecialchars($urlImagen($t['torneo_banner'] ?? '', 'img/torneos/card1.jpg')); ?>" alt="<?php echo htmlspecialchars($t['torneo_nombre']); ?>">
                        <div class="torneo-organiza-info">
                           <h3><?php echo htmlspecialchars($t['torneo_nombre']); ?></h3>
                           <span>
                              <?php echo $t['tipo'] === 'equipo' ? 'Con ' . htmlspecialchars($t['nombre']) : 'Individual'; ?>
                              <?php if (!$esPublico): ?> · <?php echo htmlspecialchars($etiquetaEstado[$t['estado']] ?? $t['estado']); ?><?php endif; ?>
                           </span>
                        </div>
                     </a>
                  </li>
               <?php endforeach; ?>
            </ul>
         <?php endif; ?>

      </section>
      <?php endif; ?>

      <?php if (!$esPublico): ?>
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

                 <?php
         $logros = $logros ?? [];
         $claseLogro = ['desbloqueado' => 'desbloqueado', 'en_curso' => 'en-curso', 'bloqueado' => 'bloqueado'];
         $etiquetaLogro = ['desbloqueado' => 'Logrado', 'en_curso' => 'En curso', 'bloqueado' => 'Bloqueado'];
         ?>
         <ul class="logros-grilla">
            <?php if (empty($logros)): ?>
               <li><p>Todavía no hay logros para mostrar.</p></li>
            <?php else: ?>
               <?php foreach ($logros as $l): ?>
                  <li>
                     <article class="logro-tarjeta <?php echo $claseLogro[$l['estado']] ?? 'bloqueado'; ?>">
                        <div class="logro-icon"></div>
                        <div>
                           <h3><?php echo htmlspecialchars($l['nombre']); ?></h3>
                           <p>
                              <?php echo htmlspecialchars($l['descripcion']); ?>
                              <?php if ($l['estado'] === 'en_curso'): ?>
                                 (<?php echo (int) $l['progreso_actual']; ?>/<?php echo (int) $l['progreso_objetivo']; ?>)
                              <?php endif; ?>
                           </p>
                        </div>
                        <span><?php echo $etiquetaLogro[$l['estado']] ?? 'Bloqueado'; ?></span>
                     </article>
                  </li>
               <?php endforeach; ?>
            <?php endif; ?>
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
      <?php endif; ?>

   </main>

   <!-- FOOTER ---------------------------->
   <?php include __DIR__ . '/../componentes/footer.php'; ?>

   <?php if ($archivoNavbar === 'navbar-publico.php'): ?>
<script src="<?php echo URL_BASE; ?>js/navbar.js"></script>
<script>document.dispatchEvent(new Event("navbarPublicoListo"));</script>
<?php endif; ?>

</body>
</html>
