<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta name="description" content="Términos y Condiciones de uso de ASCEND, plataforma de gestión de torneos deportivos, juegos de mesa y eSports.">
   <title>ASCEND - Términos y Condiciones</title>

   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/navbar.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/footer.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/publico/legal.css">

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
      rel="stylesheet">
</head>

<body>

   <?php include __DIR__ . '/../componentes/navbar-publico.php'; ?>

   <main>

      <section class="legal-hero">
         <div class="legal-hero-contenido">
            <p class="legal-etiqueta"># Legal</p>
            <h1>Términos y <span>Condiciones</span></h1>
            <p class="legal-resumen">
               Estas reglas explican cómo funciona ASCEND, qué podés hacer en la plataforma,
               cuáles son tus responsabilidades y cómo se resuelven los conflictos. Al crear
               una cuenta o usar el sitio, aceptás estos Términos y la Política de Privacidad.
            </p>
            <p class="legal-meta">
               <span><i class="fa-regular fa-calendar"></i> Versión <?php echo VERSION_TERMINOS; ?> · Vigente desde el 28 de octubre de 2026</span>
               <span><i class="fa-solid fa-shield-halved"></i> <a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=privacidad">Leer la Política de Privacidad</a></span>
            </p>
         </div>
      </section>

      <div class="legal-cuerpo">

         <aside class="legal-indice" aria-label="Índice de los Términos y Condiciones">
            <h2>Contenido</h2>
            <ol>
               <li><a href="#quienes-somos">Quiénes somos y qué regula este documento</a></li>
               <li><a href="#aceptacion">Aceptación de los Términos</a></li>
               <li><a href="#cuenta">Registro y seguridad de la cuenta</a></li>
               <li><a href="#roles">Roles y responsabilidades</a></li>
               <li><a href="#uso-aceptable">Uso aceptable</a></li>
               <li><a href="#torneos">Torneos, resultados y organizadores</a></li>
               <li><a href="#contenido">Contenido publicado por los usuarios</a></li>
               <li><a href="#propiedad">Propiedad intelectual</a></li>
               <li><a href="#disponibilidad">Disponibilidad, cambios y bajas</a></li>
               <li><a href="#responsabilidad">Responsabilidad</a></li>
               <li><a href="#menores">Menores de edad</a></li>
               <li><a href="#ley-contacto">Ley aplicable y contacto</a></li>
            </ol>
            <div class="legal-indice-otro">
               <a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=privacidad">
                  Política de Privacidad <i class="fa-solid fa-arrow-right"></i>
               </a>
            </div>
         </aside>

         <article class="legal-documento">

            <div class="legal-aviso legal-aviso--violeta">
               <strong>Resumen rápido:</strong> ASCEND es una herramienta para gestionar torneos.
               Los organizadores definen las reglas de cada competencia y cargan los resultados.
               Vos sos responsable de tu cuenta y de los datos que publicás. No se procesan pagos
               dentro de la plataforma.
            </div>

            <section id="quienes-somos">
               <h2><span class="legal-numero">01</span> Quiénes somos y qué regula este documento</h2>
               <p>
                  ASCEND es una plataforma de gestión de torneos deportivos, juegos de mesa y
                  eSports. Permite administrar cuentas, equipos, inscripciones, calendarios,
                  encuentros, resultados y rankings en un mismo lugar.
               </p>
               <p>
                  El servicio es desarrollado como proyecto de egreso del Bachillerato Tecnológico
                  en Tecnologías de la Información del Instituto Tecnológico Superior Arias-Balparda,
                  Montevideo, Uruguay. El responsable del sitio y del tratamiento de los datos
                  personales es el equipo ASCEND.
               </p>
               <p>
                  Estos Términos regulan el acceso y el uso de la plataforma. La
                  <a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=privacidad">Política de Privacidad</a>
                  explica qué datos personales se tratan, con qué finalidad y cómo podés ejercer
                  tus derechos. Si hubiera alguna contradicción sobre datos personales, prevalece
                  la Política de Privacidad.
               </p>
               <p>
                  <strong>Contacto:</strong>
                  <a href="mailto:arena.core@torneos.com">arena.core@torneos.com</a>.
               </p>
            </section>

            <section id="aceptacion">
               <h2><span class="legal-numero">02</span> Aceptación de los Términos</h2>
               <p>
                  Al registrarte, acceder o utilizar ASCEND aceptás estos Términos y la Política
                  de Privacidad. Si no estás de acuerdo, no uses la plataforma.
               </p>
               <p>
                  La aceptación por medios electrónicos tiene validez conforme a la normativa
                  uruguaya sobre documento y firma electrónica (Ley 18.600). No se exige firma
                  manuscrita ni presentación en papel.
               </p>
               <p>
                  Al crear la cuenta, la aceptación queda registrada en el sistema con la versión
                  del documento, la fecha y hora, y datos técnicos de la conexión (dirección IP y
                  navegador), como constancia de tu consentimiento.
               </p>
               <p>
                  Declarás tener capacidad legal para contratar y usar el servicio. Si sos menor
                  de edad, se aplica la sección <a href="#menores">Menores de edad</a>. Podemos
                  pedirte que aceptes una versión actualizada cuando introduzcamos cambios
                  sustanciales.
               </p>
            </section>

            <section id="cuenta">
               <h2><span class="legal-numero">03</span> Registro y seguridad de la cuenta</h2>
               <p>
                  Para crear una cuenta debés proporcionar tu nombre completo, un correo
                  electrónico válido, el tipo de cuenta (jugador u organizador) y una contraseña.
                  El teléfono es opcional.
               </p>
               <ul>
                  <li>El correo electrónico es único por cuenta y debe verificarse antes del primer inicio de sesión.</li>
                  <li>En el entorno académico actual, la verificación de correo se simula: el sistema muestra el enlace de activación en pantalla en lugar de enviar un correo real.</li>
                  <li>El correo electrónico no puede modificarse desde el perfil. Si necesitás corregirlo, escribinos a <a href="mailto:arena.core@torneos.com">arena.core@torneos.com</a>.</li>
                  <li>Sos responsable de mantener la confidencialidad de tu contraseña y de toda la actividad realizada con tu cuenta.</li>
                  <li>No compartas tu cuenta ni permitas que otra persona la use. Avisanos de inmediato si detectás un acceso no autorizado.</li>
                  <li>Podemos suspender preventivamente una cuenta ante indicios de compromiso de seguridad, suplantación de identidad o incumplimiento de estos Términos.</li>
               </ul>
            </section>

            <section id="roles">
               <h2><span class="legal-numero">04</span> Roles y responsabilidades</h2>
               <p>
                  ASCEND distingue tres roles. Los permisos de cada uno pueden ajustarse por
                  motivos de seguridad o por la evolución del proyecto.
               </p>
               <h3>Jugador</h3>
               <p>
                  Puede crear y gestionar sus equipos, solicitar unirse a otros, inscribirse en
                  torneos y consultar calendarios, resultados, rankings y su historial. No puede
                  crear torneos, cargar resultados ni modificar configuraciones de competencia.
               </p>
               <h3>Organizador</h3>
               <p>
                  Puede crear y administrar los torneos que organice: inscripciones, participantes,
                  fixture, programación de encuentros y carga de resultados. Solo puede gestionar
                  sus propios torneos. Su perfil puede ser verificado por el equipo administrador;
                  la verificación es una distinción y no lo exime de sus responsabilidades.
               </p>
               <h3>Administrador</h3>
               <p>
                  Es un rol interno del equipo ASCEND. No está disponible para el registro público.
                  Se utiliza para tareas de soporte, moderación, auditoría y control de la
                  plataforma, con acceso sujeto a registros de actividad.
               </p>
            </section>

            <section id="uso-aceptable">
               <h2><span class="legal-numero">05</span> Uso aceptable</h2>
               <p>Al usar ASCEND te comprometés a no realizar las siguientes conductas:</p>
               <ul>
                  <li>Usar la plataforma para fines ilícitos o contrarios a estos Términos.</li>
                  <li>Suplantar la identidad de otra persona, equipo u organización, o proporcionar datos falsos.</li>
                  <li>Acosar, amenazar, discriminar o publicar contenido violento, sexual, de odio o que vulnere derechos de terceros.</li>
                  <li>Intentar acceder a cuentas, paneles o áreas restringidas sin autorización, o eludir controles de autenticación, permisos o protección contra falsificación de solicitudes.</li>
                  <li>Escanear, probar vulnerabilidades, introducir malware o interferir con el funcionamiento del sitio y de sus servicios.</li>
                  <li>Extraer datos de forma automatizada mediante bots, rastreadores o técnicas similares sin autorización expresa.</li>
                  <li>Alterar resultados, rankings, inscripciones, fixture o cualquier registro de competencia por medios no autorizados.</li>
                  <li>Publicar datos personales de terceros sin su consentimiento.</li>
                  <li>Usar marcas, logos o materiales de ASCEND o de terceros sin autorización.</li>
               </ul>
               <p>
                  El incumplimiento puede provocar la moderación o eliminación de contenido, la
                  suspensión o cancelación de la cuenta y, cuando corresponda, la comunicación a
                  las autoridades competentes.
               </p>
            </section>

            <section id="torneos">
               <h2><span class="legal-numero">06</span> Torneos, resultados y organizadores</h2>
               <p>
                  ASCEND es una herramienta de gestión. Los torneos son organizados y dirigidos por
                  el organizador que los crea, quien define las reglas, fechas, cupos, formato,
                  sistema de puntuación y cualquier premio o condición adicional.
               </p>
               <ul>
                  <li>La inscripción se considera formalizada cuando el organizador la confirma y siempre dentro del cupo máximo del torneo.</li>
                  <li>Solo el organizador del torneo puede cargar o corregir resultados. El equipo administrador puede intervenir ante incidentes, reclamos o indicios de fraude.</li>
                  <li>Los reclamos deportivos deben presentarse primero al organizador. Si no se resuelven, podés escribirnos para que evaluemos el caso.</li>
                  <li>ASCEND no procesa pagos, no recauda inscripciones y no garantiza premios anunciados por los organizadores. Cualquier pago o premio fuera de la plataforma es responsabilidad exclusiva del organizador.</li>
                  <li>Podemos suspender o cancelar un torneo ante incumplimientos, fraude o riesgos para los participantes.</li>
               </ul>
            </section>

            <section id="contenido">
               <h2><span class="legal-numero">07</span> Contenido publicado por los usuarios</h2>
               <p>
                  Conservás la titularidad del contenido que publicás: nombre y descripción de
                  equipos, imágenes de perfil o portada, biografía y demás datos de tu perfil.
               </p>
               <p>
                  Nos otorgás una licencia no exclusiva, gratuita y limitada para alojar, reproducir
                  y mostrar ese contenido dentro de la plataforma, con el único fin de prestar el
                  servicio mientras tu cuenta esté activa. Esta licencia no nos permite vender tu
                  contenido ni usarlo fuera de ASCEND.
               </p>
               <p>
                  Declarás tener los derechos necesarios sobre las imágenes y los datos que
                  publicás. Podemos moderar o retirar contenido que vulnere estos Términos, la ley
                  o derechos de terceros. No publiques datos personales de otras personas sin su
                  consentimiento.
               </p>
            </section>

            <section id="propiedad">
               <h2><span class="legal-numero">08</span> Propiedad intelectual</h2>
               <p>
                  La marca ASCEND, su logotipo, el diseño, el código fuente y los textos propios de
                  la plataforma pertenecen a sus autores. El uso del servicio no te transfiere
                  ningún derecho sobre ellos.
               </p>
               <p>
                  No se permite copiar, modificar, distribuir ni explotar comercialmente estos
                  elementos sin autorización, salvo los usos permitidos por la ley. Los nombres de
                  juegos, equipos, ligas y marcas de terceros pertenecen a sus respectivos
                  titulares; su mención en ASCEND no implica afiliación ni patrocinio.
               </p>
            </section>

            <section id="disponibilidad">
               <h2><span class="legal-numero">09</span> Disponibilidad, cambios y bajas</h2>
               <p>
                  ASCEND es un proyecto académico en desarrollo. Podemos realizar tareas de
                  mantenimiento, modificar funciones o interrumpir el servicio temporalmente. No
                  garantizamos disponibilidad ininterrumpida ni ausencia total de errores.
               </p>
               <p>
                  Podemos suspender o cancelar cuentas por incumplimiento de estos Términos,
                  fraude, riesgos de seguridad o requerimiento de autoridad competente.
               </p>
               <p>
                  Podés solicitar la baja de tu cuenta escribiendo a
                  <a href="mailto:arena.core@torneos.com">arena.core@torneos.com</a>. La baja
                  desactiva el acceso a la cuenta. El historial deportivo y los registros de
                  seguridad pueden conservarse conforme a la Política de Privacidad.
               </p>
               <p>
                  Podemos actualizar estos Términos. La versión vigente estará siempre publicada en
                  esta página con su fecha. Si el cambio es sustancial, procuraremos informarlo por
                  los canales disponibles. El uso de la plataforma después de la actualización
                  implica su aceptación.
               </p>
            </section>

            <section id="responsabilidad">
               <h2><span class="legal-numero">10</span> Responsabilidad</h2>
               <p>Usás la plataforma bajo tu responsabilidad. En la medida permitida por la ley, no respondemos por:</p>
               <ul>
                  <li>Actos, omisiones o decisiones deportivas de organizadores, jugadores o terceros.</li>
                  <li>La veracidad o exactitud de la información publicada por los usuarios.</li>
                  <li>Premios, pagos, cobros o acuerdos realizados fuera de la plataforma.</li>
                  <li>Fallas de conexión, dispositivos, navegadores o servicios de terceros.</li>
                  <li>Daños indirectos, lucro cesante o pérdida de datos derivados del uso o de la imposibilidad de usar el servicio.</li>
               </ul>
               <p>
                  Nada en esta sección limita los derechos irrenunciables que la Ley 17.250 reconoce
                  a los consumidores, ni la responsabilidad por dolo o culpa grave. Vos respondés
                  por el contenido que publicás y por el uso que se haga de tu cuenta.
               </p>
            </section>

            <section id="menores">
               <h2><span class="legal-numero">11</span> Menores de edad</h2>
               <p>
                  ASCEND está dirigida a personas mayores de 13 años. Si tenés entre 13 y 17 años,
                  necesitás la autorización de tu madre, padre o responsable legal para usar la
                  plataforma y participar en torneos.
               </p>
               <p>
                  No solicitamos deliberadamente datos de menores de 13 años. Si detectamos una
                  cuenta perteneciente a un menor de esa edad, la suspenderemos y eliminaremos los
                  datos que correspondan.
               </p>
               <p>
                  Las personas responsables legales pueden escribir a
                  <a href="mailto:arena.core@torneos.com">arena.core@torneos.com</a> para consultar,
                  rectificar o solicitar la baja de los datos de un menor a su cargo. Los
                  organizadores deben extremar los recaudos cuando sus torneos estén dirigidos a
                  menores de edad.
               </p>
            </section>

            <section id="ley-contacto">
               <h2><span class="legal-numero">12</span> Ley aplicable y contacto</h2>
               <p>
                  Estos Términos se rigen por las leyes de la República Oriental del Uruguay, en
                  particular la Ley 18.331 (Protección de Datos Personales), la Ley 17.250
                  (Defensa del Consumidor), la Ley 18.600 (Documento y Firma Electrónica) y la
                  Ley 17.823 (Código de la Niñez y la Adolescencia).
               </p>
               <p>
                  Ante cualquier controversia, procuraremos primero una solución amistosa. Si no
                  fuera posible, serán competentes los tribunales de la República Oriental del
                  Uruguay.
               </p>
               <p>
                  Por consultas generales sobre estos Términos:
                  <a href="mailto:arena.core@torneos.com">arena.core@torneos.com</a>. Para ejercer
                  tus derechos sobre datos personales, indicá «Datos personales» en el asunto y
                  consultá la <a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=privacidad">Política de Privacidad</a>.
               </p>
            </section>

            <div class="legal-pie-documento">
               <p>
                  <strong>Documento:</strong> Términos y Condiciones de ASCEND · Versión <?php echo VERSION_TERMINOS; ?> ·
                  Vigente desde el 28 de octubre de 2026.
               </p>
               <p>
                  Este documento forma parte del proyecto académico ASCEND. Antes de una puesta en
                  producción comercial, se recomienda su revisión por un profesional del derecho.
               </p>
               <a class="legal-enlace-volver" href="<?php echo URL_BASE; ?>index.php?c=inicio&a=index">
                  <i class="fa-solid fa-arrow-left"></i> Volver al inicio
               </a>
            </div>

         </article>

      </div>

   </main>

   <?php include __DIR__ . '/../componentes/footer.php'; ?>

   <script src="<?php echo URL_BASE; ?>js/navbar.js"></script>
   <script>document.dispatchEvent(new Event("navbarPublicoListo"));</script>

</body>

</html>
