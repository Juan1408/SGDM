<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta name="description" content="Cómo funcionan los formatos de torneo de ASCEND: liga todos contra todos, eliminación directa y sistema suizo. Reglas, fixture, puntuación y guía para organizadores.">
   <title>ASCEND - Formatos de torneo</title>

   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/navbar.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/footer.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/publico/formatos.css">

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
      rel="stylesheet">
</head>

<body>

   <?php include __DIR__ . '/../componentes/navbar-publico.php'; ?>

   <main>

      <section class="formatos-hero">
         <div class="formatos-hero-contenido">
            <p class="formatos-etiqueta"># Competencia</p>
            <h1>Formatos de <span>torneo</span></h1>
            <p class="formatos-resumen">
               ASCEND organiza tres formatos: <strong>liga</strong>, <strong>eliminación directa</strong>
               y <strong>sistema suizo</strong>. El formato se elige al crear el torneo y define cómo se
               arma el fixture, cómo se cuentan los puntos y cómo se consagra el campeón.
            </p>
            <p class="formatos-meta">
               <span><i class="fa-solid fa-sliders"></i> Se selecciona al crear el torneo</span>
               <span><i class="fa-solid fa-list-check"></i> El fixture se genera desde el panel del organizador</span>
               <span>
                  <i class="fa-solid fa-trophy"></i>
                  <a href="<?php echo URL_BASE; ?>index.php?c=torneo&a=catalogoPublico">Ver torneos abiertos</a>
               </span>
            </p>
         </div>
      </section>

      <div class="formatos-cuerpo">

         <aside class="formatos-indice" aria-label="Índice de formatos de torneo">
            <h2>Contenido</h2>
            <ol>
               <li><a href="#liga">Liga (todos contra todos)</a></li>
               <li><a href="#eliminacion-directa">Eliminación directa</a></li>
               <li><a href="#suizo">Sistema suizo</a></li>
               <li><a href="#comparativa">Cómo elegir el formato</a></li>
               <li><a href="#organizadores">Guía para organizadores</a></li>
            </ol>
            <div class="formatos-indice-otro">
               <a href="<?php echo URL_BASE; ?>index.php?c=torneo&a=catalogoPublico">
                  Explorar torneos <i class="fa-solid fa-arrow-right"></i>
               </a>
            </div>
         </aside>

         <article class="formatos-documento">

            <div class="formatos-aviso formatos-aviso--violeta">
               <strong>Antes de empezar:</strong> el formato no se puede cambiar una vez que hay
               resultados cargados. Elegilo pensando en cuántos participantes confirmados vas a tener
               y cuántas fechas podés jugar. El fixture se arma sólo con participantes confirmados.
            </div>

            <!-- ================= LIGA ================= -->
            <section id="liga">
               <h2><span class="formatos-numero">01</span> Liga (todos contra todos)</h2>
               <p>
                  En una liga cada participante enfrenta a todos los demás al menos una vez. Es el
                  formato clásico de temporada: gana quien suma más puntos a lo largo de varias fechas,
                  no quien gana un partido puntual.
               </p>

               <h3>Cómo se arma el fixture</h3>
               <p>
                  Al generar el fixture, el sistema reparte los enfrentamientos en fechas. En cada fecha
                  cada participante juega exactamente una vez. Si la cantidad de participantes es par,
                  se juega una fecha menos que la cantidad de participantes; si es impar, se agrega un
                  descanso por fecha y quien queda libre no suma puntos ese día.
               </p>

               <h3>Cómo se cuentan los puntos</h3>
               <p>
                  Cada juego tiene su sistema de puntuación (por ejemplo, 3 puntos por victoria, 1 por
                  empate y 0 por derrota). El organizador puede ajustar esos valores al crear el torneo.
                  En liga los empates son válidos y suman según esa configuración.
               </p>

               <h3>Cómo se define el campeón</h3>
               <p>
                  La tabla de posiciones ordena por puntos y desempata por diferencia de goles, luego por
                  puntos a favor y, por último, por orden de inscripción. Al cerrar el torneo, y siempre que
                  no queden encuentros pendientes, el primero de la tabla es el campeón y se registra el podio
                  con los tres primeros.
               </p>

               <ul class="formatos-datos">
                  <li><i class="fa-solid fa-users"></i> <strong>Participantes:</strong> dos o más, sin límite fijo</li>
                  <li><i class="fa-regular fa-calendar-days"></i> <strong>Duración:</strong> una fecha menos que la cantidad de participantes (con impar, una más)</li>
                  <li><i class="fa-solid fa-handshake"></i> <strong>Empates:</strong> permitidos</li>
                  <li><i class="fa-solid fa-ranking-star"></i> <strong>Define:</strong> tabla de posiciones</li>
               </ul>

               <p class="formatos-cuando">
                  <strong>Conviene cuando</strong> tenés pocos participantes y varias fechas disponibles,
                  o cuando querés que todos jueguen contra todos y que el título se defina por regularidad.
               </p>
            </section>

            <!-- ================= ELIMINACIÓN DIRECTA ================= -->
            <section id="eliminacion-directa">
               <h2><span class="formatos-numero">02</span> Eliminación directa</h2>
               <p>
                  Es el formato de llaves: quien pierde queda fuera y quien gana avanza a la ronda
                  siguiente hasta la final. Es rápido, intenso y muy claro para el público.
               </p>

               <h3>Cantidad de participantes</h3>
               <p>
                  La llave debe ser completa, sin lugares vacíos. Por eso el formato admite exactamente
                  <strong>2, 4, 8, 16, 32 o 64</strong> participantes confirmados. Si la cantidad no es una
                  de esas, el sistema rechaza la generación del fixture y pide ajustar las inscripciones.
               </p>

               <h3>Cómo se arma el fixture</h3>
               <p>
                  El sistema sortea los cruces de la primera ronda y crea los encuentros siguientes a
                  medida que se conocen los ganadores. Las rondas se nombran según cuántos participantes
                  quedan: final, semifinal, cuartos de final, octavos de final, y así sucesivamente.
               </p>

               <h3>Empates y definición</h3>
               <p>
                  En eliminación directa no se puede cargar un empate: cada encuentro necesita un ganador.
                  Si el reglamento del torneo permite igualar en el tiempo reglamentario, definí antes del
                  partido cómo se desempata (alargue, penales, muerte súbita) y cargá el resultado final con
                  el ganador.
               </p>

               <h3>Cómo se define el campeón</h3>
               <p>
                  El ganador de la final es el campeón y el perdedor queda segundo. El tercer puesto se
                  toma de los semifinalistas eliminados. El podio se registra al cerrar el torneo, cuando
                  ya no quedan encuentros pendientes.
               </p>

               <ul class="formatos-datos">
                  <li><i class="fa-solid fa-users"></i> <strong>Participantes:</strong> 2, 4, 8, 16, 32 o 64</li>
                  <li><i class="fa-regular fa-calendar-days"></i> <strong>Duración:</strong> se calcula automáticamente hasta la final (8 → 3 rondas)</li>
                  <li><i class="fa-solid fa-handshake"></i> <strong>Empates:</strong> no permitidos</li>
                  <li><i class="fa-solid fa-trophy"></i> <strong>Define:</strong> la final</li>
               </ul>

               <p class="formatos-cuando">
                  <strong>Conviene cuando</strong> tenés pocas fechas, muchos participantes y querés un
                  cierre espectacular. Es el formato más exigente con la organización: un error en un
                  resultado condiciona toda la llave.
               </p>
            </section>

            <!-- ================= SUIZO ================= -->
            <section id="suizo">
               <h2><span class="formatos-numero">03</span> Sistema suizo</h2>
               <p>
                  El sistema suizo combina lo mejor de los dos anteriores: nadie queda eliminado y no hace
                  falta que todos jueguen contra todos. En cada ronda se enfrentan participantes con
                  rendimiento parecido, así las últimas rondas son las más competitivas.
               </p>

               <h3>Cantidad de participantes y rondas</h3>
               <p>
                  Se necesita una cantidad <strong>par</strong> de participantes confirmados, porque todos
                  juegan en cada ronda y el sistema no tiene descansos. La cantidad de rondas se calcula
                  automáticamente según el total: 4 participantes → 2 rondas; 6 u 8 → 3 rondas; 16 → 4 rondas.
               </p>

               <h3>Cómo se empareja cada ronda</h3>
               <p>
                  La primera ronda se arma de a pares con los participantes confirmados. A partir de la
                  segunda, el sistema ordena a los participantes por puntos y empareja a los que están cerca
                  en la tabla, evitando repetir un enfrentamiento que ya ocurrió. Cuando se completa una ronda
                  (todos sus encuentros finalizados), la siguiente se genera automáticamente.
               </p>

               <h3>Cómo se define el campeón</h3>
               <p>
                  Al cerrar el torneo se usa la tabla de posiciones: puntos, diferencia de goles, puntos
                  a favor y orden de inscripción. No hay final ni eliminación; el título es para quien
                  sostuvo el mejor rendimiento en toda la competencia.
               </p>

               <ul class="formatos-datos">
                  <li><i class="fa-solid fa-users"></i> <strong>Participantes:</strong> cantidad par</li>
                  <li><i class="fa-regular fa-calendar-days"></i> <strong>Duración:</strong> se calcula según la cantidad de participantes (8 → 3 rondas)</li>
                  <li><i class="fa-solid fa-handshake"></i> <strong>Empates:</strong> permitidos</li>
                  <li><i class="fa-solid fa-ranking-star"></i> <strong>Define:</strong> tabla de posiciones</li>
               </ul>

               <p class="formatos-cuando">
                  <strong>Conviene cuando</strong> tenés muchos participantes y pocas fechas, y querés que
                  todos jueguen la misma cantidad de partidos sin que nadie quede afuera en la primera
                  jornada.
               </p>
            </section>

            <!-- ================= COMPARATIVA ================= -->
            <section id="comparativa">
               <h2><span class="formatos-numero">04</span> Cómo elegir el formato</h2>
               <p>
                  No hay un formato mejor que otro: depende de la cantidad de participantes, del tiempo
                  disponible y de cómo querés que se sienta la competencia.
               </p>

               <div class="formatos-tabla-contenedor">
                  <table class="formatos-tabla">
                     <thead>
                        <tr>
                           <th>Formato</th>
                           <th>Participantes</th>
                           <th>Estructura</th>
                           <th>Empates</th>
                           <th>Se define por</th>
                        </tr>
                     </thead>
                     <tbody>
                        <tr>
                           <td><strong>Liga</strong></td>
                           <td>2 o más</td>
                           <td>Todos contra todos</td>
                           <td>Sí</td>
                           <td>Tabla de posiciones</td>
                        </tr>
                        <tr>
                           <td><strong>Eliminación directa</strong></td>
                           <td>2, 4, 8, 16, 32 o 64</td>
                           <td>Llaves eliminatorias</td>
                           <td>No</td>
                           <td>Final</td>
                        </tr>
                        <tr>
                           <td><strong>Sistema suizo</strong></td>
                           <td>Cantidad par</td>
                           <td>Rondas por rendimiento</td>
                           <td>Sí</td>
                           <td>Tabla de posiciones</td>
                        </tr>
                     </tbody>
                  </table>
               </div>
               <p class="formatos-tabla-ayuda">Deslizá la tabla para ver todas las columnas.</p>

               <p>
                  Como referencia rápida: con 8 participantes, una liga necesita 7 fechas, una eliminación
                  directa se resuelve en 3 rondas y un suizo también en 3 rondas pero con todos jugando
                  las tres.
               </p>
            </section>

            <!-- ================= ORGANIZADORES ================= -->
            <section id="organizadores">
               <h2><span class="formatos-numero">05</span> Guía para organizadores</h2>
               <p>
                  Estos son los pasos y controles que conviene revisar antes de generar el fixture y
                  durante el torneo.
               </p>

               <ol class="formatos-pasos">
                  <li>
                     <strong>Confirmá las inscripciones.</strong> El fixture se arma únicamente con los
                     participantes en estado confirmado. Las solicitudes pendientes no entran.
                  </li>
                  <li>
                     <strong>Verificá la cantidad.</strong> Eliminación directa exige 2, 4, 8, 16, 32 o 64;
                     el sistema suizo exige una cantidad par. La liga acepta cualquier cantidad desde dos.
                  </li>
                  <li>
                     <strong>Revisá el sistema de puntuación.</strong> Podés usar el del juego o definir
                     victoria, empate y derrota para ese torneo.
                  </li>
                  <li>
                     <strong>Generá el fixture desde el panel.</strong> Se puede regenerar mientras no haya
                     resultados cargados; después queda bloqueado para no perder el historial.
                  </li>
                  <li>
                     <strong>Cargá los resultados.</strong> La tabla de posiciones y las llaves se recalculan
                     automáticamente. Si te equivocás, corregí el marcador: el sistema vuelve a calcular.
                  </li>
                  <li>
                     <strong>En eliminación directa, definí el desempate antes de jugar.</strong> Un empate
                     no se puede cargar: siempre tiene que haber un ganador.
                  </li>
                  <li>
                     <strong>En suizo, esperá que termine la ronda.</strong> La siguiente se genera cuando
                     todos los encuentros de la ronda actual están finalizados.
                  </li>
                  <li>
                     <strong>Cerrá el torneo al finalizar.</strong> El sistema exige que no queden
                     encuentros pendientes; al pasar el torneo a finalizado registra el podio y lo envía
                     al historial.
                  </li>
               </ol>

               <div class="formatos-aviso">
                  <strong>Un detalle importante:</strong> en el sistema suizo, si un participante ya jugó
                  contra todos los posibles rivales de su zona de la tabla, el sistema permite repetir un
                  enfrentamiento para completar la ronda. Es una situación poco frecuente, pero conviene
                  tenerla en cuenta en torneos con muy pocos participantes.
               </div>

               <p>
                  Para las reglas de conducta, uso de la plataforma y tratamiento de datos, consultá los
                  <a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=terminos">Términos y Condiciones</a>
                  y la <a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=privacidad">Política de Privacidad</a>.
               </p>

               <div class="formatos-pie-documento">
                  <p>
                     <strong>Documento:</strong> Formatos de torneo de ASCEND · Actualizado el 28 de octubre de 2026.
                  </p>
                  <p>
                     El contenido refleja el comportamiento implementado en la plataforma para los formatos
                     liga, eliminación directa y sistema suizo.
                  </p>
                  <a class="formatos-enlace-volver" href="<?php echo URL_BASE; ?>index.php?c=torneo&a=catalogoPublico">
                     <i class="fa-solid fa-arrow-left"></i> Ver torneos disponibles
                  </a>
               </div>
            </section>

         </article>

      </div>

   </main>

   <?php include __DIR__ . '/../componentes/footer.php'; ?>

   <script src="<?php echo URL_BASE; ?>js/navbar.js"></script>
   <script>document.dispatchEvent(new Event("navbarPublicoListo"));</script>

</body>

</html>
