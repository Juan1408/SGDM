<?php
$juegosRanking = $juegosRanking ?? [];
$juegoSeleccionado = $juegoSeleccionado ?? null;
$rankingJugadores = $rankingJugadores ?? [];
$rankingEquipos = $rankingEquipos ?? [];
$juegosDestacados = $juegosDestacados ?? [];

//Iniciales para el avatar de texto
$iniciales = function (string $nombre): string {
  $ini = '';
  foreach (array_slice(preg_split('/\s+/', trim($nombre)), 0, 2) as $palabra) {
    $ini .= mb_strtoupper(mb_substr($palabra, 0, 1));
  }
  return $ini !== '' ? $ini : '?';
};

//Puntos: sin decimales si es entero (1.250), con uno si no (12,5)
$formatoPuntos = function ($valor): string {
  $v = (float) $valor;
  return number_format($v, $v == floor($v) ? 0 : 1, ',', '.');
};

//Tabla del ranking (una por pestaña)
$imprimirTabla = function (array $lista, string $columnaNombre) use ($iniciales, $formatoPuntos): void {
  echo '<div class="seccion-tabla"><table><thead><tr class="ranking-row head">'
    . '<th>Pos</th><th>' . htmlspecialchars($columnaNombre) . '</th>'
     . '<th class="rk-encuentros">Partidas</th><th class="rk-encuentros">Victorias</th><th class="col-puntos">Puntos</th>'
     . '</tr></thead><tbody>';
  foreach ($lista as $i => $r) {
    $pos = $i + 1;
    $medalla = $pos === 1 ? ' top3 gold' : ($pos === 2 ? ' top3 silver' : ($pos === 3 ? ' top3 bronze' : ''));
    echo '<tr class="ranking-row">'
       . '<td class="rk-pos' . $medalla . '">' . $pos . '</td>'
       . '<td class="rk-player"><div class="rk-avatar">' . htmlspecialchars($iniciales($r['nombre'])) . '</div><span>' . htmlspecialchars($r['nombre']) . '</span></td>'
       . '<td class="rk-encuentros">' . (int) $r['partidos_jugados'] . '</td>'
       . '<td class="rk-encuentros">' . (int) $r['partidos_ganados'] . '</td>'
       . '<td class="rk-points">' . $formatoPuntos($r['puntos_totales']) . '</td>'
       . '</tr>';
  }
  echo '</tbody></table></div>';
};
?>

<!DOCTYPE html>
<html lang="es">

<head>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>ASCEND</title>

  
  <!-- CSS -->
  <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
  <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
  <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/navbar.css">
  <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/publico/home.css">
  <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/footer.css">
  <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/carruseles.css">
  <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/publico/rankings.css">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet">

</head>

<body>

  <!-- NAVBAR ---------------------------->

  <?php include __DIR__ . '/../componentes/navbar-publico.php'; ?>

  <main>

    <!-------------------------------------->
    <!-- HERO ------------------------------>
    <!-------------------------------------->

    <section class="hero reveal">

      <div class="hero-slider">

        <div class="hero-slide active">
          <img src="<?php echo URL_BASE; ?>img/banners/header01.jpg" alt="">
        </div>

        <div class="hero-slide">
          <img src="<?php echo URL_BASE; ?>img/banners/header2.jpg" alt="">
        </div>

        <div class="hero-slide">
          <img src="<?php echo URL_BASE; ?>img/banners/header3.jpg" alt="">
        </div>
      </div>

      <div class="hero-overlay"></div>

      <div class="hero-contenido">

        <p class="hero-subtitulo">
          Sistema de Gestión de Deportes y eSports
        </p>

        <h1>
          Organiza y <br>Gestiona Torneos<br>
        </h1>

        <p class="hero-descripcion">
          Administra competiciones, rankings, participantes y resultados
          desde una sola plataforma moderna.
        </p>

        <div class="hero-botones">

          <a href="<?php echo URL_BASE; ?>index.php?c=torneo&a=catalogoPublico" class="primary-btn">
            <span>Explorar Torneos</span>
          </a>

          <a href="<?php echo URL_BASE; ?>index.php?c=auth&a=mostrarLogin" class="secondary-btn">
            Comenzar
          </a>

        </div>
      </div>
    </section>


    <!-------------------------------------->
    <!-- TOP 3 ----------------------------->
    <!-------------------------------------->

    <section class="seccion-ganadores">
      <div class="ganadores-izquierda">
        <p class="tag">Mejores resultados</p>
        <h2>Los mayores<br><span>ganadores</span><br>de hoy</h2>
        <div class="trofeo">🏆</div>
      </div>

      <div class="ganadores-derecha">
        <span class="ganadores-insignia">Mejores jugadores<?php echo $juegoSeleccionado ? ' · ' . htmlspecialchars($juegoSeleccionado['nombre']) : ''; ?></span>
        <ul class="ganadores-lista">

          <?php if (empty($rankingJugadores)): ?>
            <li><p>Todavía no hay resultados de jugadores en esta disciplina.</p></li>
          <?php endif; ?>

          <?php $clasesTop = ['gold', 'silver', 'bronze']; ?>
          <?php foreach (array_slice($rankingJugadores, 0, 3) as $i => $r): ?>
            <li class="winner-row <?php echo $clasesTop[$i]; ?>">
              <div class="ganador-rango"><?php echo $i + 1; ?></div>
              <div class="ganador-avatar"><?php echo htmlspecialchars($iniciales($r['nombre'])); ?></div>
              <span class="ganador-nombre"><?php echo htmlspecialchars($r['nombre']); ?></span>
              <span class="ganador-monto">+ <?php echo $formatoPuntos($r['puntos_totales']); ?> pts</span>
            </li>
          <?php endforeach; ?>

        </ul>
      </div>
    </section>

    <!-------------------------------------->
    <!-- JUEGOS DESTACADOS ----------------->
    <!-------------------------------------->

    <section class="juegos-destacados">
      <div class="juegos-header">
        <p class="ganador-subtitulo">Competencias activas</p>
        <h2>Juegos Destacados</h2>
      </div>

      <div class="carrusel-wrapper">
        <button class="carrusel-btn" id="prev-btn">&#8592;</button>

        <div class="carrusel-track-wrapper">
          <ul class="juegos-grillas" id="juegos-track">

                        <?php $coloresTarjeta = ['lol', 'apex', 'cs', 'wow', 'val', 'chess']; ?>

            <?php if (empty($juegosDestacados)): ?>
              <li><p class="ranking-vacio">Todavía no hay torneos creados.</p></li>
            <?php endif; ?>

            <?php foreach ($juegosDestacados as $i => $jd): ?>
              <?php
                $img = trim((string) ($jd['imagen_url'] ?? ''));
                $imgUrl = $img === ''
                  ? URL_BASE . 'img/bg.jpg'
                  : (preg_match('#^https?://#i', $img) ? $img : URL_BASE . ltrim($img, '/'));
                $nTorneos = (int) $jd['total_torneos'];
              ?>
              <li>
                <article class="tarjeta-juego <?php echo $coloresTarjeta[$i % count($coloresTarjeta)]; ?>">
                  <div class="tarjeta-info">
                    <img src="<?php echo htmlspecialchars($imgUrl); ?>" alt="<?php echo htmlspecialchars($jd['nombre']); ?>">
                    <div class="tarjeta-overlay">
                      <h3><?php echo htmlspecialchars($jd['nombre']); ?></h3>
                      <p><?php echo $nTorneos . ($nTorneos === 1 ? ' torneo' : ' torneos'); ?></p>
                    </div>
                  </div>
                </article>
              </li>
            <?php endforeach; ?>

          </ul>
        </div>

        <button class="carrusel-btn" id="next-btn">&#8594;</button>
      </div>
    </section>

    <!-------------------------------------->
    <!-- RANKINGS -------------------------->
    <!-------------------------------------->

    <?php $tabInicial = (empty($rankingJugadores) && !empty($rankingEquipos)) ? 'equipos' : 'jugadores'; ?>
    <section class="seccion-ranking">
      <div class="ranking-header">
        <p>Tabla completa</p>
        <h2>Ranking<?php echo $juegoSeleccionado ? ': ' . htmlspecialchars($juegoSeleccionado['nombre']) : ' general'; ?></h2>

        <?php if (!empty($juegosRanking)): ?>
        <!-- Cada disciplina tiene su propio ranking: sus puntos no son comparables con los de otro juego -->
        <form method="get" action="<?php echo URL_BASE; ?>index.php" class="ranking-filtro">
          <input type="hidden" name="c" value="inicio">
          <input type="hidden" name="a" value="rankings">
          <label for="rk-juego">Disciplina</label>
          <select id="rk-juego" name="juego">
            <?php foreach ($juegosRanking as $j): ?>
              <option value="<?php echo (int) $j['id']; ?>"<?php echo ((int) $j['id'] === (int) $juegoSeleccionado['id']) ? ' selected' : ''; ?>><?php echo htmlspecialchars($j['nombre']); ?></option>
            <?php endforeach; ?>
          </select>
          <noscript><button type="submit" class="rk-tab">Ver</button></noscript>
        </form>
        <?php endif; ?>

        <ul class="ranking-tabs">
          <li><button type="button" class="rk-tab<?php echo $tabInicial === 'jugadores' ? ' active' : ''; ?>" data-tab="jugadores">Jugadores</button></li>
          <li><button type="button" class="rk-tab<?php echo $tabInicial === 'equipos' ? ' active' : ''; ?>" data-tab="equipos">Equipos</button></li>
        </ul>
      </div>

      <div id="rk-panel-jugadores"<?php echo $tabInicial === 'jugadores' ? '' : ' hidden'; ?>>
        <?php if (empty($rankingJugadores)): ?>
          <p class="ranking-vacio">Todavía no hay jugadores con resultados en esta disciplina.</p>
        <?php else: ?>
          <?php $imprimirTabla($rankingJugadores, 'Jugador'); ?>
        <?php endif; ?>
      </div>

      <div id="rk-panel-equipos"<?php echo $tabInicial === 'equipos' ? '' : ' hidden'; ?>>
        <?php if (empty($rankingEquipos)): ?>
          <p class="ranking-vacio">Todavía no hay equipos con resultados en esta disciplina.</p>
        <?php else: ?>
          <?php $imprimirTabla($rankingEquipos, 'Equipo'); ?>
        <?php endif; ?>
      </div>
    </section>

  </main>


  <!-- FOOTER ---------------------------->

  <?php include __DIR__ . '/../componentes/footer.php'; ?>



  <script src="<?php echo URL_BASE; ?>js/navbar.js"></script>
  <script>document.dispatchEvent(new Event("navbarPublicoListo"));</script>
  <script src="<?php echo URL_BASE; ?>js/hero-slider.js"></script>
  <script src="<?php echo URL_BASE; ?>js/scroll-reveal.js"></script>
  <script src="<?php echo URL_BASE; ?>js/featured-games.js"></script>
    <script src="<?php echo URL_BASE; ?>js/rankings.js"></script>

</body>

</html>
