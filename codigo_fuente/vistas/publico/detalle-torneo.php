<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>ASCEND</title>

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/navbar.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/footer.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/publico/detalle-torneo.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/publico/home.css">

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
      rel="stylesheet">

   <!-- Tailwind: solo se usa para el botón "Ver más miembros" -->
   <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>

   <!-- NAVBAR ---------------------------->

   <?php include __DIR__ . '/../componentes/navbar-publico.php'; ?>


   <main class="tournament-detail">

         <?php
         $etiquetasFormato = [
            'liga' => 'Liga',
            'suizo' => 'Sistema suizo',
            'eliminacion_directa' => 'Eliminación directa',
         ];
         $etiquetasEstadoTorneo = [
            'inscripciones_abiertas' => 'Inscripciones abiertas',
            'en_curso' => 'En curso',
            'finalizado' => 'Finalizado',
         ];
         $mesesCorto = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
         $formatearFecha = static function (?string $fecha, bool $conAnio = true) use ($mesesCorto): string {
            if (empty($fecha)) {
               return 'A confirmar';
            }
            $marca = strtotime($fecha);
            return date('d', $marca) . ' ' . $mesesCorto[(int) date('n', $marca) - 1] . ($conAnio ? ' ' . date('Y', $marca) : '');
         };
         $fechaInicioTexto = $formatearFecha($torneo['fecha_inicio']);
         $fechaFinTexto = $formatearFecha($torneo['fecha_fin']);
         $inscripcionInicioTexto = $formatearFecha($torneo['fecha_inicio_inscripcion']);
         $inscripcionCierreTexto = $formatearFecha($torneo['fecha_limite_inscripcion']);

         // Ubicación: presencial = dirección + localidad; online = "Online (PC)", etc.
         $ubicacionTexto = trim((string) ($torneo['ubicacion'] ?? ''));
         if ($ubicacionTexto !== '' && trim((string) ($torneo['localidad'] ?? '')) !== '') {
            $ubicacionTexto .= ', ' . trim($torneo['localidad']);
         }
         $organizacionTexto = trim((string) ($torneo['comunidad'] ?? ''));

         // Transmisión: solo se acepta un enlace http(s); el ícono depende de la plataforma
         $streamUrl = trim((string) ($torneo['stream_url'] ?? ''));
         if (!preg_match('#^https?://#i', $streamUrl)) {
            $streamUrl = '';
         }
         $iconoStream = 'fa-solid fa-tv';
         $hostStream = strtolower((string) parse_url($streamUrl, PHP_URL_HOST));
         foreach (['twitch' => 'fa-brands fa-twitch', 'youtu' => 'fa-brands fa-youtube', 'discord' => 'fa-brands fa-discord', 'facebook' => 'fa-brands fa-facebook', 'tiktok' => 'fa-brands fa-tiktok'] as $clave => $iconoPlataforma) {
            if (strpos($hostStream, $clave) !== false) {
               $iconoStream = $iconoPlataforma;
               break;
            }
         }

         $tiposResultado = ['goles' => 'Goles', 'puntos' => 'Puntos', 'rondas' => 'Rondas', 'booleano' => 'Victoria o derrota'];
         $iconosAgenda = [
            'administrativo' => 'fa-clipboard-list',
            'reunion' => 'fa-users',
            'competencia' => 'fa-flag-checkered',
            'premiacion' => 'fa-award',
            'partido' => 'fa-gamepad',
         ];
         $numeroMarcador = static fn ($valor): string => rtrim(rtrim(number_format((float) $valor, 2, '.', ''), '0'), '.');
      
               // --- Datos del fixture para Participantes, Brackets y Resultados ---
         $participaEquipos = strtolower(trim($torneo['nombre_modalidad'])) !== 'individual';
         $porRonda = [];
         foreach ($encuentros as $partidoFixture) {
            $porRonda[(int) $partidoFixture['ronda']][] = $partidoFixture;
         }
         ksort($porRonda);
         $participantesPrimeraRonda = count($porRonda[1] ?? []) * 2;
         $nombresRondaEliminacion = [
            2 => 'Final',
            4 => 'Semifinales',
            8 => 'Cuartos de final',
            16 => 'Octavos de final',
            32 => 'Dieciseisavos de final',
            64 => 'Treintaidosavos de final',
         ];
         $etiquetaRonda = static function (int $ronda) use ($torneo, $participantesPrimeraRonda, $nombresRondaEliminacion): string {
            if ($torneo['formato'] === 'eliminacion_directa') {
               $enRonda = (int) ($participantesPrimeraRonda / (2 ** ($ronda - 1)));
               return $nombresRondaEliminacion[$enRonda] ?? 'Ronda de ' . $enRonda;
            }
            return ($torneo['formato'] === 'liga' ? 'Fecha ' : 'Ronda ') . $ronda;
         };
         $imagenPorDefecto = $participaEquipos ? URL_BASE . 'img/logos/canelones.png' : URL_BASE . 'img/avatars/a1.png';
      ?>

      <?php
         $bannerEstilo = !empty($torneo['banner_url'])
            ? ' style="--td-banner: url(\'' . htmlspecialchars(addcslashes(URL_BASE . $torneo['banner_url'], "\"'()\\")) . '\');"'
            : '';
      ?>
      <section class="td-hero"<?php echo $bannerEstilo; ?>>

         <div class="td-hero-contenido td-hero-contenido--solo">            
            <h1><?php echo htmlspecialchars($torneo['nombre']); ?></h1>
            <p id="hero-subtitulo"><?php echo htmlspecialchars($torneo['nombre_juego']); ?> · <?php echo htmlspecialchars($etiquetasFormato[$torneo['formato']] ?? $torneo['formato']); ?></p>
            <span id="hero-duracion">Duración del torneo: <?php echo htmlspecialchars($fechaInicioTexto); ?> - <?php echo htmlspecialchars($fechaFinTexto); ?></span><br>
            <span id="hero-fechas">Inscripciones: <?php echo htmlspecialchars($inscripcionInicioTexto); ?> - <?php echo htmlspecialchars($inscripcionCierreTexto); ?></span>
                     <?php
               $inscripcionesAbiertas = $torneo['estado'] === 'inscripciones_abiertas'
                  && (empty($torneo['fecha_limite_inscripcion']) || $torneo['fecha_limite_inscripcion'] >= date('Y-m-d'));
               $estadoTexto = ($torneo['estado'] === 'inscripciones_abiertas' && !$inscripcionesAbiertas)
                  ? 'Inscripciones cerradas'
                  : ($etiquetasEstadoTorneo[$torneo['estado']] ?? $torneo['estado']);
               $esModalidadIndividual = strtolower(trim($torneo['nombre_modalidad'])) === 'individual';
               $etiquetasInscripcion = [
                  'pendiente' => 'Tu inscripción está pendiente de aprobación',
                  'confirmado' => 'Ya estás inscripto en este torneo',
                  'rechazado' => 'Tu inscripción fue rechazada',
                  'cancelado' => 'Tu inscripción fue cancelada',
               ];
            ?>
            <div class="td-actions">
                  <p><?php echo htmlspecialchars($estadoTexto); ?></p>

               <?php if ($miInscripcion): ?>
                  <p><?php echo htmlspecialchars($etiquetasInscripcion[$miInscripcion['estado']] ?? $miInscripcion['estado']); ?></p>

               <?php elseif (!$inscripcionesAbiertas): ?>
                  <span class="td-boton-inscripcion td-boton-inscripcion--cerrado">Inscripciones cerradas</span>

               <?php elseif (!$esJugadorLogueado && isset($_SESSION['usuario_id'])): ?>
                  <span class="td-boton-inscripcion td-boton-inscripcion--cerrado">Solo los jugadores pueden inscribirse</span>

               <?php elseif (!$esJugadorLogueado): ?>
                  <a href="<?php echo URL_BASE; ?>index.php?c=auth&a=mostrarLogin" class="td-boton-inscripcion">Inscribirse</a>

               <?php elseif ($esModalidadIndividual): ?>
                  <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=torneo&a=inscribirse">
                     <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                  <input type="hidden" name="torneo_id" value="<?php echo (int) $torneo['id']; ?>">
                     <input type="hidden" name="tipo" value="usuario">
                     <button type="submit" class="td-boton-inscripcion">Inscribirse</button>
                  </form>

               <?php else: ?>
                  <button type="button" class="td-boton-inscripcion"
                     onclick="document.getElementById('modal-inscripcion').showModal()">Inscribirse</button>

                  <dialog class="td-modal" id="modal-inscripcion">
                     <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=torneo&a=inscribirse">
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                     <input type="hidden" name="torneo_id" value="<?php echo (int) $torneo['id']; ?>">
                        <input type="hidden" name="tipo" value="equipo">

                        <h2><?php echo htmlspecialchars($torneo['nombre']); ?>: Modalidad - Equipos</h2>

                        <label for="equipo_id">Seleccionar equipo</label>
                        <select name="equipo_id" id="equipo_id" required>
                           <option value=""><?php echo empty($misEquiposCapitan) ? 'No sos capitán de ningún equipo' : 'Elegí tu equipo'; ?></option>
                           <?php foreach ($misEquiposCapitan as $equipoCapitan): ?>
                              <option value="<?php echo (int) $equipoCapitan['id']; ?>"><?php echo htmlspecialchars($equipoCapitan['nombre_equipo']); ?></option>
                           <?php endforeach; ?>
                        </select>

                        <div class="td-modal-acciones">
                           <button type="submit" class="td-modal-boton">Inscribirse</button>
                           <button type="button" class="td-modal-cancelar"
                              onclick="this.closest('dialog').close()">Cancelar</button>
                        </div>
                     </form>
                  </dialog>
               <?php endif; ?>
            </div>
</div>
      </section>

      <!-------------------------------------->
      <!-- BOTONES --------------------------->
      <!-------------------------------------->

      <section class="td-tabs-search">
         <div class="td-tabs">
            <button class="active" data-tab="info">Información</button>
            <button data-tab="participantes">Participantes</button>
            <button data-tab="encuentros">Encuentros</button>
            <button data-tab="bracket">Brackets</button>
            <button data-tab="resultados">Resultados</button>
         </div>

         <div class="td-search">
            <input type="text" placeholder="Buscar torneo">
         </div>
      </section>

      <!-------------------------------------->
      <!-- INFO GENERAL ---------------------->
      <!-------------------------------------->

      <section class="td-contenido">

         <div class="panel-pestañas active" id="info">

            <div class="info-layout">

               <!-- COLUMNA PRINCIPAL -->
               <div class="info-principal">

               <section class="td-info-general">
                        <div class="td-meta">
                        <span id="meta-formato"><?php echo htmlspecialchars($etiquetasFormato[$torneo['formato']] ?? $torneo['formato']); ?></span>
                        <span id="meta-equipo"><?php echo (int) $torneo['cupo_max_equipos']; ?> Cupos</span>
                        <span id="meta-modalidad">Modalidad: <?php echo htmlspecialchars($torneo['nombre_modalidad']); ?></span>
                        <?php if ($ubicacionTexto !== ''): ?>
                           <span id="meta-ubicacion">Ubicación: <?php echo htmlspecialchars($ubicacionTexto); ?></span>
                        <?php endif; ?>
                        <?php if ($organizacionTexto !== ''): ?>
                           <span id="meta-organizacion">Organización: <?php echo htmlspecialchars($organizacionTexto); ?></span>
                        <?php endif; ?>
                     </div>

                     <h2>Información</h2>
                     <p id="info-descripcion">
                        <?php echo nl2br(htmlspecialchars($torneo['descripcion'] ?: 'El organizador todavía no cargó una descripción para este torneo.')); ?>
                     </p>

                     <h2>Reglas</h2>
                     <p id="info-reglas">
                        <?php echo nl2br(htmlspecialchars($torneo['reglas'] ?: 'El organizador todavía no cargó reglas para este torneo.')); ?>
                     </p>
               </section>

                  <?php
                  // Premios: se guardan como lista de 3 textos (primer, segundo y tercer lugar)
                  $premiosTorneo = json_decode((string) ($torneo['premios'] ?? ''), true);
                  $tarjetasPremio = [];
                  if (is_array($premiosTorneo)) {
                     $puestosPremio = [
                        ['Primer lugar', 'fa-trophy', ''],
                        ['Segundo lugar', 'fa-medal', 'plata'],
                        ['Tercer lugar', 'fa-award', 'bronce'],
                     ];
                     foreach ($puestosPremio as $indice => [$tituloPuesto, $iconoPuesto, $clasePuesto]) {
                        $textoPremio = trim((string) ($premiosTorneo[$indice] ?? ''));
                        if ($textoPremio !== '') {
                           $tarjetasPremio[] = [$tituloPuesto, $iconoPuesto, $clasePuesto, $textoPremio];
                        }
                     }
                  } elseif (!empty($torneo['premios'])) {
                     // Torneos viejos con los premios escritos como texto libre
                     $tarjetasPremio[] = ['Premios', 'fa-trophy', '', $torneo['premios']];
                  }
                  ?>
                  <?php if ($tarjetasPremio): ?>
               <section class="td-trofeos">
                     <ul class="td-trofeos-lista">
                        <?php foreach ($tarjetasPremio as [$tituloPuesto, $iconoPuesto, $clasePuesto, $textoPremio]): ?>
                        <li>
                           <article class="tarjeta-trofeo<?php echo $clasePuesto ? ' tarjeta-trofeo--' . $clasePuesto : ''; ?>">
                              <i class="fa-solid <?php echo $iconoPuesto; ?>"></i>
                              <h3><?php echo $tituloPuesto; ?></h3>
                              <p><?php echo htmlspecialchars($textoPremio, ENT_QUOTES, 'UTF-8'); ?></p>
                           </article>
                        </li>
                        <?php endforeach; ?>
                     </ul>
               </section>
                  <?php endif; ?>

                  <section class="td-comentarios">
                     <h2>Comentarios</h2>

                     <ul class="lista-comentarios">
                        <li>
                           <article class="caja-comentario">
                              <div class="comentario-avatar"></div>
                              <div>
                                 <h3>Usuario <span>29/09/2026</span></h3>
                                 <p>Comentario hecho por el usuario sobre el torneo.</p>
                                 <button>Responder</button>
                              </div>
                           </article>
                        </li>

                        <li>
                           <article class="caja-comentario">
                              <div class="comentario-avatar"></div>
                              <div>
                                 <h3>Usuario <span>29/09/2026</span></h3>
                                 <p>Consulta sobre horarios, equipos o inscripción.</p>
                                 <button>Responder</button>
                              </div>
                           </article>
                        </li>
                     </ul>

                     <a class="load-more" href="<?php echo URL_BASE; ?>index.php?c=inicio&a=enConstruccion">Cargar más</a>
                  </section>

                  <section class="td-formulario-comentarios">
                     <h2>Dejar un comentario</h2>

                     <div class="formulario-fila">
                        <input type="text" placeholder="Nombre">
                        <input type="email" placeholder="Email">
                     </div>

                     <textarea placeholder="Escribe tu comentario aquí..."></textarea>

                     <button
                        onclick="alert('Tu comentario ha sido enviado para revisión (Simulación)')">Comentar</button>
                  </section>

               </div>

               <!-- SIDEBAR DERECHA -->
               <aside class="td-sidebar">

                  <div class="proximo-torneo">
                     <h2>Próximo Torneo</h2>
                     <img src="<?php echo URL_BASE; ?>img/torneos/card5.jpg" alt="Próximo torneo">
                     <h3>Virtual Odyssey Chronicles</h3>
                  </div>

                  <div class="perfil-organizador" id="organizerProfile">
                     <div class="perfil-organizador-header">
                        <div class="organizer-avatar" id="organizerAvatar">

                     <?php
                        $fotoOrg = $torneo['foto_organizador'] ?? '';
                        $fotoOrgUrl = $fotoOrg === ''
                           ? URL_BASE . 'img/avatars/a1.png'
                           : (preg_match('#^(https?:)?//#i', $fotoOrg) ? $fotoOrg : URL_BASE . ltrim($fotoOrg, '/'));
                        ?>
                        <img id="foto-organizador" src="<?php echo htmlspecialchars($fotoOrgUrl); ?>" alt="Foto del organizador">
                        </div>

                     </div>
                     <div class="perfil-organizador-label"><?php echo htmlspecialchars($torneo['nombre_organizador']); ?></div>
                        <div class="perfil-organizador-actions perfil-organizador-actions--centered">
                        <button class="perfil-boton-primario">Seguir</button>
                           <a href="<?php echo URL_BASE; ?>index.php?c=perfil&a=organizador&id=<?php echo (int) $torneo['organizador_id']; ?>" class="perfil-boton-secundario">Ver perfil</a>
                     </div>
                  </div>

               </aside>

            </div>

         </div>

         <!-------------------------------------->
         <!--- PARTICIPANTES -------------------->
         <!-------------------------------------->

      <div class="panel-pestañas" id="participantes">

            <?php if ($participaEquipos): ?>

               <!-- ======= MODALIDAD EQUIPOS: lista de equipos + miembros del equipo elegido ======= -->
               <section class="seccion-participantes">
                  <h2 id="titulo-participantes">Equipos que participan</h2>

                  <?php if (empty($participantes)): ?>
                     <p>Todavía no hay participantes confirmados.</p>
                  <?php else: ?>
                     <p class="participantes-ayuda">Tocá un equipo para ver sus miembros.</p>
                     <ul class="participantes-grilla" id="participantes-grid">
                        <?php foreach ($participantes as $indiceParticipante => $participante): ?>
                           <li<?php echo $indiceParticipante >= 3 ? ' data-oculto="1" style="display:none"' : ''; ?>>
                              <article class="participantes-tarjeta participantes-tarjeta--seleccionable<?php echo $indiceParticipante === 0 ? ' seleccionado' : ''; ?>"
                                 data-equipo-tarjeta="<?php echo (int) $participante['referencia_id']; ?>"
                                 data-equipo-nombre="<?php echo htmlspecialchars($participante['nombre']); ?>"
                                 tabindex="0" role="button">
                                 <img src="<?php echo htmlspecialchars($participante['imagen'] ?: $imagenPorDefecto); ?>" alt="<?php echo htmlspecialchars($participante['nombre']); ?>">

                                 <div class="participantes-info">
                                    <h3>
                                       <a href="<?php echo URL_BASE; ?>index.php?c=equipo&a=verPerfil&id=<?php echo (int) $participante['referencia_id']; ?>"><?php echo htmlspecialchars($participante['nombre']); ?></a>
                                    </h3>
                                    <p><?php echo htmlspecialchars($torneo['nombre_juego']); ?></p>
                                    <p><?php echo (int) $participante['miembros']; ?> Participantes</p>
                                    <?php if (in_array($torneo['formato'], ['liga', 'suizo'], true)): ?>
                                       <p>Puntos: <?php echo $numeroMarcador($participante['puntos'] ?? 0); ?></p>
                                    <?php endif; ?>
                                 </div>
                              </article>
                           </li>
                        <?php endforeach; ?>
                     </ul>

                     <?php if (count($participantes) > 3): ?>
                        <div class="ver-mas-miembros">
                           <button type="button" data-cargar-mas="participantes-grid" data-paso="3" class="cursor-pointer bg-violet-500 rounded-md text-white font-semibold 
       transition duration-300 ease-in-out hover:bg-violet-700 hover:ring-2 hover:ring-violet-800 
       hover:shadow-xl hover:shadow-violet-500 focus:ring-violet-300 focus:shadow-violet-400 px-5 py-2">Cargar más</button>
                        </div>
                     <?php endif; ?>
                  <?php endif; ?>
               </section>

               <!-- MIEMBROS DEL EQUIPO SELECCIONADO -->
               <?php if (!empty($participantes)): ?>
               <section class="equipo-miembros">

                  <h2 class="seccion-gradiente-titulo">
                     Miembros de <span id="nombre-equipo-seleccionado"><?php echo htmlspecialchars($participantes[0]['nombre']); ?></span>
                  </h2>

                  <?php foreach ($participantes as $indiceParticipante => $participante): ?>
                     <div class="miembros-equipo-panel" data-miembros-equipo="<?php echo (int) $participante['referencia_id']; ?>"<?php echo $indiceParticipante > 0 ? ' style="display:none"' : ''; ?>>
                        <?php if (empty($participante['lista_miembros'])): ?>
                           <p>Este equipo todavía no tiene miembros.</p>
                        <?php else: ?>
                           <ul class="miembros-grilla">
                              <?php foreach ($participante['lista_miembros'] as $miembro): ?>
                                 <li>
                                    <a class="enlace-jugador" href="<?php echo URL_BASE; ?>index.php?c=perfil&a=jugador&id=<?php echo (int) $miembro['id']; ?>">
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
                                    </a>
                                 </li>
                              <?php endforeach; ?>
                           </ul>
                        <?php endif; ?>
                     </div>
                  <?php endforeach; ?>

               </section>
               <?php endif; ?>

               <!--------- RANKINGS DE LOS MIEMBROS DEL EQUIPO SELCCIONADO ---------->

               <section class="ranking-equipo">

                  <div class="ranking-header">
                     <h2 id="titulo-ranking">Rankings del Equipo</h2>
                  </div>

                  <div class="ranking-tabla" id="ranking-tabla-contenedor">

                     <table>
                        <thead>
                           <tr id="ranking-head" class="ranking-head">
                              <!-- Se llena por JS: cambia según sea equipo o individual -->
                           </tr>
                        </thead>
                        <tbody id="ranking-filas">
                           <!-- Se llena por JS -->
                        </tbody>
                     </table>

                  </div>

               </section>

            <?php else: ?>

               <!-- ======= MODALIDAD INDIVIDUAL: solo "Jugadores que participan" ======= -->
               <section class="equipo-miembros">

                  <h2 class="seccion-gradiente-titulo">Jugadores que participan</h2>

                  <?php if (empty($participantes)): ?>
                     <p>Todavía no hay participantes confirmados.</p>
                  <?php else: ?>
                     <ul class="miembros-grilla" id="jugadores-grid">
                        <?php foreach ($participantes as $indiceParticipante => $participante): ?>
                           <li<?php echo $indiceParticipante >= 8 ? ' data-oculto="1" style="display:none"' : ''; ?>>
                              <a class="enlace-jugador" href="<?php echo URL_BASE; ?>index.php?c=perfil&a=jugador&id=<?php echo (int) $participante['referencia_id']; ?>">
                                 <article class="miembro-tarjeta">
                                    <div class="miembro-foto">
                                       <div class="miembro-fondo"></div>
                                       <img src="<?php echo htmlspecialchars($participante['imagen'] ?: $imagenPorDefecto); ?>" alt="<?php echo htmlspecialchars($participante['nombre']); ?>">
                                    </div>
                                    <h3><?php echo htmlspecialchars($participante['nombre']); ?></h3>
                                    <?php if (in_array($torneo['formato'], ['liga', 'suizo'], true)): ?>
                                       <span class="miembro-posicion">Puntos: <?php echo $numeroMarcador($participante['puntos'] ?? 0); ?></span>
                                    <?php endif; ?>
                                 </article>
                              </a>
                           </li>
                        <?php endforeach; ?>
                     </ul>

                     <?php if (count($participantes) > 8): ?>
                        <div class="ver-mas-miembros">
                           <button type="button" data-cargar-mas="jugadores-grid" data-paso="8" class="cursor-pointer bg-violet-500 rounded-md text-white font-semibold 
       transition duration-300 ease-in-out hover:bg-violet-700 hover:ring-2 hover:ring-violet-800 
       hover:shadow-xl hover:shadow-violet-500 focus:ring-violet-300 focus:shadow-violet-400 px-5 py-2">Cargar más</button>
                        </div>
                     <?php endif; ?>
                  <?php endif; ?>

               </section>

            <?php endif; ?>

            <script>
               (function () {
                  // "Cargar más": muestra de a "data-paso" elementos ocultos de la grilla indicada
                  document.querySelectorAll('[data-cargar-mas]').forEach(function (boton) {
                     boton.addEventListener('click', function () {
                        var selector = '#' + boton.getAttribute('data-cargar-mas') + ' li[data-oculto]';
                        var paso = parseInt(boton.getAttribute('data-paso'), 10) || 3;
                        var ocultos = document.querySelectorAll(selector);
                        for (var i = 0; i < paso && i < ocultos.length; i++) {
                           ocultos[i].style.display = '';
                           ocultos[i].removeAttribute('data-oculto');
                        }
                        if (!document.querySelector(selector)) {
                           boton.parentNode.style.display = 'none';
                        }
                     });
                  });

                  // Equipos: al elegir una tarjeta se muestran los miembros de ese equipo
                  var tarjetas = document.querySelectorAll('[data-equipo-tarjeta]');
                  var paneles = document.querySelectorAll('[data-miembros-equipo]');
                  var titulo = document.getElementById('nombre-equipo-seleccionado');

                  function seleccionar(tarjeta) {
                     var id = tarjeta.getAttribute('data-equipo-tarjeta');
                     tarjetas.forEach(function (t) { t.classList.toggle('seleccionado', t === tarjeta); });
                     paneles.forEach(function (p) {
                        p.style.display = p.getAttribute('data-miembros-equipo') === id ? '' : 'none';
                     });
                     if (titulo) { titulo.textContent = tarjeta.getAttribute('data-equipo-nombre'); }
                  }

                  tarjetas.forEach(function (tarjeta) {
                     tarjeta.addEventListener('click', function (evento) {
                        if (evento.target.closest('a')) { return; } // el nombre del equipo es un enlace a su perfil
                        seleccionar(tarjeta);
                     });
                     tarjeta.addEventListener('keydown', function (evento) {
                        if (evento.key === 'Enter' || evento.key === ' ') {
                           evento.preventDefault();
                           seleccionar(tarjeta);
                        }
                     });
                  });
               })();
            </script>

         </div>

         <!-------------------------------------->
         <!--- ENCUENTROS ----------------------->
         <!-------------------------------------->

                  <div class="panel-pestañas" id="encuentros">

            <!-- Agenda: solo actividades (los partidos van en Próximos Enfrentamientos) -->
            <div class="td-info-general">
               <h2>Agenda del Torneo</h2>
               <?php $actividades = array_values(array_filter($agenda, static fn ($item) => $item['tipo'] !== 'partido')); ?>
               <?php if (empty($actividades)): ?>
                  <p>El organizador todavía no cargó actividades.</p>
               <?php else: ?>
                  <ul class="lista-actividades" id="lista-actividades">
                     <?php foreach ($actividades as $item): ?>
                        <li class="actividad-item tipo-<?php echo htmlspecialchars($item['tipo']); ?>">
                           <div class="actividad-fecha">
                              <span><?php echo htmlspecialchars($formatearFecha($item['fecha'], false)); ?></span>
                              <?php if (!empty($item['hora'])): ?>
                                 <span class="actividad-hora"><?php echo htmlspecialchars(substr($item['hora'], 0, 5)); ?></span>
                              <?php endif; ?>
                           </div>
                           <div class="actividad-contenido">
                              <span class="actividad-icono"><i class="fa-solid <?php echo $iconosAgenda[$item['tipo']] ?? 'fa-calendar-day'; ?>"></i></span>
                              <span class="actividad-titulo"><?php echo htmlspecialchars($item['titulo']); ?></span>
                                 <?php if (!empty($item['descripcion'])): ?>
                                 <span class="actividad-descripcion"><?php echo htmlspecialchars($item['descripcion']); ?></span>
                              <?php endif; ?>
                           </div>
                        </li>
                     <?php endforeach; ?>
                  </ul>
               <?php endif; ?>
            </div>

            <!-- Próximos Enfrentamientos -->
            <?php
            $hoy = date('Y-m-d');
            $proximos = array_values(array_filter($encuentros, static fn ($partido) =>
               $partido['estado'] === 'programado'
               && $partido['fecha_hora_programada']
               && substr($partido['fecha_hora_programada'], 0, 10) >= $hoy
               && $partido['participante_local_id'] && $partido['participante_visitante_id']));
            usort($proximos, static fn ($a, $b) => strcmp($a['fecha_hora_programada'], $b['fecha_hora_programada']));
            ?>
            <?php if ($proximos): ?>
            <section class="calendario-vs reveal">

               <div class="agenda-header">
                  <p class="seccion-subtitulo"># CALENDARIO</p>
                  <h2>Próximos Enfrentamientos</h2>
               </div>

               <ul class="vs-lista" id="proximos-lista">
                  <?php foreach ($proximos as $indiceProximo => $partido): ?>
                     <?php $marca = strtotime($partido['fecha_hora_programada']); ?>
                     <li<?php echo $indiceProximo >= 5 ? ' data-oculto="1" style="display:none"' : ''; ?>>
                        <div class="enfrentamiento-tarjeta">

                           <div class="enfrentamiento-equipo">
                              <img src="<?php echo htmlspecialchars($partido['local_imagen'] ?: $imagenPorDefecto); ?>" alt="<?php echo htmlspecialchars($partido['local_nombre']); ?>" class="enfrentamiento-logo">
                              <span class="enfrentamiento-nombre"><?php echo htmlspecialchars($partido['local_nombre']); ?></span>
                           </div>

                           <div class="enfrentamiento-centro">
                              <span class="enfrentamiento-vs">Vs</span>
                              <span class="enfrentamiento-fecha"><?php echo htmlspecialchars($formatearFecha($partido['fecha_hora_programada']) . ' · ' . date('H:i', $marca)); ?></span>
                              <span class="enfrentamiento-canal">
                                 <i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($partido['cancha'] ?: ($ubicacionTexto !== '' ? $ubicacionTexto : 'A definir')); ?>
                              </span>
                           </div>

                           <div class="enfrentamiento-equipo">
                              <img src="<?php echo htmlspecialchars($partido['visitante_imagen'] ?: $imagenPorDefecto); ?>" alt="<?php echo htmlspecialchars($partido['visitante_nombre']); ?>" class="enfrentamiento-logo">
                              <span class="enfrentamiento-nombre"><?php echo htmlspecialchars($partido['visitante_nombre']); ?></span>
                           </div>

                        </div>
                     </li>
                  <?php endforeach; ?>
               </ul>

               <?php if (count($proximos) > 5): ?>
               <div class="ver-mas-miembros">
                  <button type="button" id="proximos-cargar-mas" class="cursor-pointer bg-violet-500 rounded-md text-white font-semibold 
       transition duration-300 ease-in-out hover:bg-violet-700 hover:ring-2 hover:ring-violet-800 
       hover:shadow-xl hover:shadow-violet-500 focus:ring-violet-300 focus:shadow-violet-400 px-5 py-2">Cargar más</button>
               </div>
               <script>
                  (function () {
                     var boton = document.getElementById('proximos-cargar-mas');
                     boton.addEventListener('click', function () {
                        var ocultos = document.querySelectorAll('#proximos-lista li[data-oculto]');
                        for (var i = 0; i < 5 && i < ocultos.length; i++) {
                           ocultos[i].style.display = '';
                           ocultos[i].removeAttribute('data-oculto');
                        }
                        if (!document.querySelector('#proximos-lista li[data-oculto]')) {
                           boton.parentNode.style.display = 'none';
                        }
                     });
                  })();
               </script>
               <?php endif; ?>
            </section>
            <?php endif; ?>

         </div>
          
         <!-------------------------------------->
         <!--- RESULTADOS ----------------------->
         <!-------------------------------------->


            <div class="panel-pestañas" id="resultados">
            <div class="td-info-general">
               <h2>Resultados</h2>
               <div class="td-meta">
                  <span>Tipo de resultado: <?php echo htmlspecialchars($tiposResultado[$torneo['tipo_resultado']] ?? $torneo['tipo_resultado']); ?></span>
                  <span>Mejor de <?php echo max(1, (int) $torneo['mejor_de']); ?></span>
               </div>

               <?php
               $jugados = array_values(array_filter($encuentros, static fn ($partido) => $partido['estado'] === 'finalizado'));
               usort($jugados, static fn ($a, $b) => [$b['ronda'], $b['id']] <=> [$a['ronda'], $a['id']]);
               ?>
               <?php if ($jugados === []): ?>
                  <p>Todavía no hay resultados cargados.</p>
               <?php else: ?>
                  <ul class="lista-resultados" id="lista-resultados">
                     <?php foreach ($jugados as $partido): ?>
                        <li class="resultado-card">
                           <div class="resultado-cabecera">
                              <div>
                                 <span class="resultado-encuentro"><?php echo htmlspecialchars(($partido['local_nombre'] ?? 'Por definir') . ' vs ' . ($partido['visitante_nombre'] ?? 'Por definir')); ?></span>
                                 <span class="resultado-fecha">
                                    <?php echo htmlspecialchars($etiquetaRonda((int) $partido['ronda'])); ?>
                                    <?php echo $partido['fecha_hora_programada'] ? ' · ' . htmlspecialchars($formatearFecha($partido['fecha_hora_programada'])) : ''; ?>
                                 </span>
                              </div>
                              <span class="resultado-marcador"><?php echo $numeroMarcador($partido['resultado_local']); ?> - <?php echo $numeroMarcador($partido['resultado_visitante']); ?></span>
                           </div>
                        </li>
                     <?php endforeach; ?>
                  </ul>
               <?php endif; ?>
            </div>
         </div>


         <!-------------------------------------->
         <!--- BRACKETS ------------------------->
         <!-------------------------------------->


         <div class="panel-pestañas" id="bracket">

               <div class="bracket-container">
               <h2 class="bracket-title" id="tournament-title"><?php echo htmlspecialchars($torneo['nombre']); ?></h2>
               <p class="bracket-subtitle" id="tournament-info-meta">
                  FORMATO: <?php echo htmlspecialchars(mb_strtoupper($etiquetasFormato[$torneo['formato']] ?? $torneo['formato'])); ?>
                  | MODALIDAD: <?php echo htmlspecialchars(mb_strtoupper($torneo['nombre_modalidad'])); ?>
               </p>

               <?php if ($encuentros === []): ?>
                  <p class="bracket-subtitle">EL ORGANIZADOR TODAVÍA NO GENERÓ EL FIXTURE</p>

               <?php elseif ($torneo['formato'] === 'eliminacion_directa'): ?>
                  <?php
                  $htmlPartido = static function (array $m, string $extra = '') use ($numeroMarcador): string {
                     $e = static fn ($valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
                     $marcador = static fn ($valor): string => $valor === null ? '-' : $numeroMarcador($valor);
                     return '<div class="bracket-matchup' . ($extra !== '' ? ' ' . $extra : '') . ($m['ganador'] ? ' completed' : '') . '">'
                        . '<div class="matchup-team' . ($m['ganador'] === 'local' ? ' winner' : '') . '"><span>' . $e($m['local']) . '</span><strong>' . $marcador($m['res_local']) . '</strong></div>'
                        . '<div class="matchup-team' . ($m['ganador'] === 'visitante' ? ' winner' : '') . '"><span>' . $e($m['visitante']) . '</span><strong>' . $marcador($m['res_visitante']) . '</strong></div>'
                        . '</div>';
                  };

                  // Se arma el árbol completo; los partidos que todavía no existen quedan como "Por definir"
                  $totalRondas = max(1, (int) round(log(max(2, $participantesPrimeraRonda), 2)));
                  $partidosEliminacion = [];
                  for ($r = 1; $r <= $totalRondas; $r++) {
                     $cantidad = max(1, (int) ($participantesPrimeraRonda / (2 ** $r)));
                     for ($i = 0; $i < $cantidad; $i++) {
                        $real = $porRonda[$r][$i] ?? null;
                        $jugado = $real && $real['estado'] === 'finalizado';
                        $partidosEliminacion[$r][] = [
                           'local' => $real['local_nombre'] ?? 'Por definir',
                           'visitante' => $real['visitante_nombre'] ?? 'Por definir',
                           'res_local' => $jugado ? $real['resultado_local'] : null,
                           'res_visitante' => $jugado ? $real['resultado_visitante'] : null,
                           'ganador' => $jugado && $real['participante_ganador_id']
                              ? ((int) $real['participante_ganador_id'] === (int) $real['participante_local_id'] ? 'local' : 'visitante')
                              : null,
                        ];
                     }
                  }
                  $final = $partidosEliminacion[$totalRondas][0];
                  $campeon = $final['ganador'] === 'local' ? $final['local'] : ($final['ganador'] === 'visitante' ? $final['visitante'] : 'Por definir');
                  $columnas = 2 * ($totalRondas - 1) + 1;
                  ?>
                  <div class="bracket-visual" id="brackets-render-box"<?php echo $columnas !== 7 ? ' style="grid-template-columns: repeat(' . $columnas . ', minmax(0, 1fr));"' : ''; ?>>

                     <?php for ($r = 1; $r < $totalRondas; $r++): ?>
                        <div class="bracket-round left-side<?php echo $r > 1 ? ' compact' : ''; ?>">
                           <h3><?php echo htmlspecialchars($etiquetaRonda($r)); ?></h3>
                           <?php foreach (array_slice($partidosEliminacion[$r], 0, intdiv(count($partidosEliminacion[$r]), 2)) as $m) { echo $htmlPartido($m); } ?>
                        </div>
                     <?php endfor; ?>

                     <div class="bracket-center">
                        <div class="final-match">
                           <h3>Gran Final</h3>
                           <?php echo $htmlPartido($final, 'final-box'); ?>
                           <div class="champion-box">
                              <span>Champion</span>
                              <strong><?php echo htmlspecialchars($campeon); ?></strong>
                           </div>
                        </div>
                     </div>

                     <?php for ($r = $totalRondas - 1; $r >= 1; $r--): ?>
                        <div class="bracket-round right-side<?php echo $r > 1 ? ' compact' : ''; ?>">
                           <h3><?php echo htmlspecialchars($etiquetaRonda($r)); ?></h3>
                           <?php foreach (array_slice($partidosEliminacion[$r], intdiv(count($partidosEliminacion[$r]), 2)) as $m) { echo $htmlPartido($m); } ?>
                        </div>
                     <?php endfor; ?>

                  </div>

               <?php else: ?>

                  <?php if ($torneo['formato'] === 'suizo'): ?>
                     <div class="rondas-suizo" id="rondas-suizo">
                        <?php foreach ($porRonda as $numeroRonda => $partidosRonda): ?>
                           <div class="ronda-card">
                              <h3>Ronda <?php echo (int) $numeroRonda; ?></h3>
                              <?php foreach ($partidosRonda as $partido): ?>
                                 <div class="ronda-partido">
                                    <span class="ronda-jugador blancas"><?php echo htmlspecialchars($partido['local_nombre'] ?? 'Por definir'); ?></span>
                                    <span class="ronda-resultado">
                                       <?php echo $partido['estado'] === 'finalizado'
                                          ? $numeroMarcador($partido['resultado_local']) . ' - ' . $numeroMarcador($partido['resultado_visitante'])
                                          : 'vs'; ?>
                                    </span>
                                    <span class="ronda-jugador negras"><?php echo htmlspecialchars($partido['visitante_nombre'] ?? 'Por definir'); ?></span>
                                 </div>
                              <?php endforeach; ?>
                           </div>
                        <?php endforeach; ?>
                     </div>
                  <?php endif; ?>

                  <?php if ($torneo['formato'] === 'liga'): ?>
                  <div class="brackets-dynamic-wrapper" id="brackets-render-box">
                     <table class="league-table">
                        <thead>
                           <tr>
                              <th>Pos</th>
                              <th><?php echo $participaEquipos ? 'Equipo' : 'Participante'; ?></th>
                              <th>PJ</th>
                              <th>G</th>
                              <th>E</th>
                              <th>P</th>
                              <th>Pts</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php foreach ($posiciones as $indice => $fila): ?>
                              <tr>
                                 <td style="color: #00f0ff; font-weight: bold;"><?php echo $indice + 1; ?></td>
                                 <td style="font-weight: 600;"><?php echo htmlspecialchars($fila['nombre']); ?></td>
                                 <td><?php echo (int) $fila['partidos_jugados']; ?></td>
                                 <td style="color: #30f5d2;"><?php echo (int) $fila['partidos_ganados']; ?></td>
                                 <td><?php echo (int) $fila['partidos_empatados']; ?></td>
                                 <td style="color: #ff00c8;"><?php echo (int) $fila['partidos_perdidos']; ?></td>
                                 <td style="font-weight: bold; color: #b03eff;"><?php echo $numeroMarcador($fila['puntos']); ?></td>
                              </tr>
                           <?php endforeach; ?>
                        </tbody>
                     </table>
                  </div>
                  <?php endif; ?>

               <?php endif; ?>
            </div>

            

            <!-------------------------------------->
            <!--- STREAMING Y NOTICIAS ------------->
            <!-------------------------------------->

            <?php if ($streamUrl !== ''): ?>
            <div class="seccion-streaming">
               <div class="contenido-streaming">
                  <h2><?php echo $torneo['estado'] === 'en_curso' ? 'Streaming Ahora' : 'Streaming del torneo'; ?></h2>
                  <div class="redes-streaming">
                     <a href="<?php echo htmlspecialchars($streamUrl); ?>" target="_blank" rel="noopener noreferrer" aria-label="Ver transmisión"><i class="<?php echo $iconoStream; ?>"></i></a>
                  </div>
               </div>
               <div class="streaming-video-placeholder">
                  <a class="streaming-video-enlace" href="<?php echo htmlspecialchars($streamUrl); ?>" target="_blank" rel="noopener noreferrer">
                     <img src="<?php echo URL_BASE; ?>img/chess.jpg" alt="Transmisión del torneo">
                     <?php if ($torneo['estado'] === 'en_curso'): ?>
                        <div class="live-badge"><i class="fa-solid fa-circle"></i> EN VIVO</div>
                     <?php endif; ?>
                  </a>
               </div>
            </div>
            <?php endif; ?>

            <div class="newsletter-seccion">
               <div class="newsletter-caja">
                  <h2>Boletín de Noticias ASCEND</h2>
                  <form class="newsletter-formulario" onsubmit="event.preventDefault();">
                     <input type="email" placeholder="Introduce tu correo electrónico" required>
                     <button type="submit">SUSCRIBIRSE</button>
                  </form>
               </div>
            </div>

         </div>


      </section>

   </main>

   <!-- FOOTER ---------------------------->

   <?php include __DIR__ . '/../componentes/footer.php'; ?>

   <script src="<?php echo URL_BASE; ?>js/navbar.js"></script>
   <script>document.dispatchEvent(new Event("navbarPublicoListo"));</script>
   <script src="<?php echo URL_BASE; ?>js/torneo-tabs.js"></script>
   <script src="<?php echo URL_BASE; ?>js/scroll-reveal.js"></script>


</body>

</html>
