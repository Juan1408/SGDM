<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta name="description" content="Política de Privacidad de ASCEND: qué datos personales tratamos, para qué, cómo los protegemos y cómo ejercer tus derechos.">
   <title>ASCEND - Política de Privacidad</title>

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
            <h1>Política de <span>Privacidad</span></h1>
            <p class="legal-resumen">
               Acá explicamos qué datos personales recopila ASCEND, para qué los usamos,
               quién puede verlos, cuánto tiempo los conservamos y cómo podés ejercer tus
               derechos conforme a la Ley 18.331 de Uruguay.
            </p>
            <p class="legal-meta">
               <span><i class="fa-regular fa-calendar"></i> Versión <?php echo VERSION_PRIVACIDAD; ?> · Vigente desde el 28 de octubre de 2026</span>
               <span><i class="fa-solid fa-file-lines"></i> <a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=terminos">Leer los Términos y Condiciones</a></span>
            </p>
         </div>
      </section>

      <div class="legal-cuerpo">

         <aside class="legal-indice" aria-label="Índice de la Política de Privacidad">
            <h2>Contenido</h2>
            <ol>
               <li><a href="#responsable">Responsable del tratamiento</a></li>
               <li><a href="#datos">Qué datos tratamos</a></li>
               <li><a href="#finalidad">Para qué los usamos</a></li>
               <li><a href="#visibilidad">Datos públicos y visibilidad</a></li>
               <li><a href="#cookies">Cookies y almacenamiento local</a></li>
               <li><a href="#compartir">Con quién compartimos</a></li>
               <li><a href="#conservacion">Cuánto tiempo conservamos</a></li>
               <li><a href="#seguridad">Cómo protegemos los datos</a></li>
               <li><a href="#derechos">Tus derechos</a></li>
               <li><a href="#menores">Menores de edad</a></li>
               <li><a href="#transferencias">Transferencias internacionales</a></li>
               <li><a href="#cambios">Cambios y contacto</a></li>
            </ol>
            <div class="legal-indice-otro">
               <a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=terminos">
                  Términos y Condiciones <i class="fa-solid fa-arrow-right"></i>
               </a>
            </div>
         </aside>

         <article class="legal-documento">

            <div class="legal-aviso">
               <strong>Lo esencial:</strong> usamos tus datos para que la plataforma funcione,
               para gestionar torneos y para proteger las cuentas. No vendemos datos personales.
               Tu correo y tu teléfono personal nunca se muestran en el perfil público.
            </div>

            <section id="responsable">
               <h2><span class="legal-numero">01</span> Responsable del tratamiento</h2>
               <p>
                  El responsable del tratamiento de los datos personales recopilados en ASCEND es
                  el equipo del proyecto ASCEND, desarrollado en el Instituto Tecnológico Superior
                  Arias-Balparda, Montevideo, Uruguay.
               </p>
               <p>
                  <strong>Contacto para privacidad:</strong>
                  <a href="mailto:arena.core@torneos.com?subject=Datos%20personales">arena.core@torneos.com</a>
                  (indicá «Datos personales» en el asunto).
               </p>
               <p>
                  Esta Política se aplica al sitio web de ASCEND y a las funcionalidades disponibles
                  para jugadores, organizadores y administradores. Se complementa con los
                  <a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=terminos">Términos y Condiciones</a>.
               </p>
            </section>

            <section id="datos">
               <h2><span class="legal-numero">02</span> Qué datos tratamos</h2>
               <p>
                  Tratamos únicamente los datos necesarios para operar la plataforma. La siguiente
                  tabla resume las categorías, su origen y si son obligatorios.
               </p>

               <p class="legal-tabla-ayuda">
                  En pantallas pequeñas, deslizá la tabla hacia los costados para ver todas las columnas.
               </p>

               <div class="legal-tabla-contenedor">
                  <table class="legal-tabla">                     <thead>
                        <tr>
                           <th>Categoría</th>
                           <th>Datos</th>
                           <th>Origen y obligatoriedad</th>
                        </tr>
                     </thead>
                     <tbody>
                        <tr>
                           <td><strong>Identificación y contacto</strong></td>
                           <td>Nombre completo, correo electrónico, teléfono y rol (jugador u organizador).</td>
                           <td>Los proporcionás al registrarte. Nombre, correo, rol y contraseña son obligatorios; el teléfono es opcional.</td>
                        </tr>
                        <tr>
                           <td><strong>Perfil público</strong></td>
                           <td>Foto de perfil, imagen de portada, apodo o gamertag, biografía, país, ciudad, fecha de nacimiento, Discord, Instagram, X (Twitter), nivel y experiencia.</td>
                           <td>Los cargás vos de forma opcional. Podés elegir qué campos se muestran públicamente.</td>
                        </tr>
                        <tr>
                           <td><strong>Perfil de organizador</strong></td>
                           <td>Nombre de la organización, biografía, localidad, teléfono de contacto, sitio web y estado de verificación.</td>
                           <td>Los proporciona el organizador. El nombre de la organización es obligatorio; el resto es opcional.</td>
                        </tr>
                        <tr>
                           <td><strong>Actividad en la plataforma</strong></td>
                           <td>Equipos, miembros y capitanes, solicitudes, inscripciones, encuentros, resultados, posiciones, logros, notificaciones y torneos guardados.</td>
                           <td>Se generan por el uso del servicio y son necesarios para su funcionamiento.</td>
                        </tr>
                        <tr>
                           <td><strong>Datos técnicos y de seguridad</strong></td>
                           <td>Dirección IP, agente de usuario (navegador y dispositivo), identificadores de sesión, registros de acceso, auditoría de cambios y registros de errores.</td>
                           <td>Se generan automáticamente al interactuar con el sitio, con fines de seguridad y diagnóstico.</td>
                        </tr>
                        <tr>
                           <td><strong>Aceptación de documentos legales</strong></td>
                           <td>Versión aceptada de los Términos y Condiciones y de la Política de Privacidad, fecha y hora de la aceptación, dirección IP y agente de usuario.</td>
                           <td>Se registra automáticamente al crear la cuenta, como constancia de tu consentimiento.</td>
                        </tr>
                     </tbody>
                  </table>
               </div>

               <p>
                  La contraseña se almacena como un hash criptográfico (bcrypt); nunca se guarda en
                  texto plano ni se muestra en la interfaz.
               </p>
               <p>
                  No solicitamos datos sensibles (salud, biometría, origen étnico, religión,
                  orientación sexual) ni datos financieros. Tampoco pedimos documento de identidad
                  ni domicilio exacto.
               </p>
            </section>

            <section id="finalidad">
               <h2><span class="legal-numero">03</span> Para qué los usamos</h2>
               <p>Tratamos los datos personales para las siguientes finalidades:</p>
               <ul>
                  <li>Crear y administrar tu cuenta, verificar tu correo electrónico y autenticarte.</li>
                  <li>Gestionar equipos, inscripciones, torneos, calendarios, resultados y rankings.</li>
                  <li>Mostrar tu perfil público con los datos que decidiste hacer visibles.</li>
                  <li>Enviar notificaciones operativas relacionadas con la cuenta o los torneos.</li>
                  <li>Prevenir fraudes, investigar incidentes y mantener la seguridad de la plataforma.</li>
                  <li>Atender consultas, reclamos y solicitudes de ejercicio de derechos.</li>
                  <li>Cumplir obligaciones legales o requerimientos de autoridad competente.</li>
               </ul>
               <p>
                  Nos basamos en tu consentimiento al registrarte y en la necesidad de ejecutar el
                  servicio que solicitás. Los registros técnicos de seguridad se justifican en la
                  protección de la plataforma y de sus usuarios, conforme a los fines previstos en
                  la Ley 18.331.
               </p>
               <p>
                  No usamos tus datos para publicidad, no los vendemos y no tomamos decisiones
                  automatizadas que produzcan efectos jurídicos sobre vos. Los rankings son
                  deportivos y se calculan a partir de los resultados cargados por los
                  organizadores.
               </p>
               <div class="legal-aviso legal-aviso--violeta">
                  <strong>Entorno académico:</strong> en la versión actual del proyecto, el envío
                  de correos de verificación está simulado. El sistema muestra el enlace de
                  activación en pantalla y registra el evento en un archivo local, sin enviar un
                  correo real a tu casilla.
               </div>
            </section>

            <section id="visibilidad">
               <h2><span class="legal-numero">04</span> Datos públicos y visibilidad</h2>
               <p>
                  ASCEND es una plataforma comunitaria: parte de tu información se muestra a otras
                  personas para que puedan identificarte en equipos y torneos.
               </p>
               <ul>
                  <li><strong>Siempre visible en tu perfil público:</strong> tu nombre completo, foto de perfil, apodo, nivel y equipos.</li>
                  <li><strong>Visible solo si lo autorizás:</strong> biografía, ciudad y país, edad, Discord, Instagram, X (Twitter) y torneos en los que participás, según las casillas de privacidad de tu perfil.</li>
                  <li><strong>Nunca visible públicamente:</strong> tu correo electrónico y tu teléfono personal.</li>
                  <li><strong>Perfil de organizador:</strong> el nombre de la organización, la foto, la verificación y los torneos que organiza son públicos. La biografía, localidad, sitio web y teléfono de contacto solo se muestran si el organizador los marca como públicos.</li>
                  <li><strong>Participantes de torneos:</strong> tu nombre completo y tu foto pueden aparecer en las listas de participantes y en los cruces de un torneo.</li>
               </ul>
               <p>
                  Podés cambiar la visibilidad de los campos opcionales desde «Editar perfil». Los
                  datos que no marcás como públicos se ocultan en la vista pública, pero se
                  conservan en la base de datos para que puedas volver a mostrarlos.
               </p>
               <p>
                  El equipo administrador puede acceder a la ficha completa de una cuenta para
                  tareas de soporte, moderación y auditoría. Ese acceso está reservado al equipo
                  administrador y las acciones administrativas sensibles quedan registradas para
                  auditoría.
               </p>
            </section>

            <section id="cookies">
               <h2><span class="legal-numero">05</span> Cookies y almacenamiento local</h2>
               <p>
                  ASCEND usa una cookie de sesión indispensable para mantener tu inicio de sesión.
                  No utiliza cookies publicitarias, de perfilado ni píxeles de redes sociales.
               </p>
               <ul>
                  <li><strong>Cookie de sesión (PHPSESSID):</strong> identifica tu sesión en el servidor. Es HttpOnly, SameSite=Lax y se marca Secure cuando el sitio se sirve por HTTPS. La sesión expira por inactividad a los 30 minutos.</li>
                  <li><strong>Almacenamiento local del navegador:</strong> guardamos preferencias de interfaz, como el tema claro u oscuro del panel administrativo, las notificaciones ya leídas, la posición de la portada de un perfil y borradores locales del panel de organizador. Estos datos quedan en tu dispositivo y no se envían automáticamente al servidor. Podés borrarlos desde la configuración de tu navegador.</li>
               </ul>
               <p>
                  El sitio carga tipografías desde Google Fonts y una librería de íconos desde
                  cdnjs (Cloudflare). Al hacerlo, tu navegador realiza una solicitud a esos
                  servidores, que pueden ver tu dirección IP y datos técnicos de la solicitud.
                  No controlamos sus prácticas de privacidad; te recomendamos revisar sus
                  políticas.
               </p>
            </section>

            <section id="compartir">
               <h2><span class="legal-numero">06</span> Con quién compartimos</h2>
               <p>No vendemos, alquilamos ni cedemos tus datos personales con fines comerciales. Los datos pueden ser accesibles en estos supuestos:</p>
               <ul>
                  <li><strong>Otros usuarios:</strong> solo la información que se muestra en tu perfil público y en las listas de participantes.</li>
                  <li><strong>Equipo administrador:</strong> para soporte, moderación, auditoría y control de la plataforma.</li>
                  <li><strong>Proveedores de infraestructura:</strong> si el proyecto se aloja en servidores contratados, esos proveedores podrán tratar datos técnicos bajo obligaciones de confidencialidad y seguridad.</li>
                  <li><strong>Autoridades:</strong> cuando exista una obligación legal o un requerimiento válido de autoridad competente.</li>
               </ul>
               <p>
                  Los organizadores de un torneo pueden ver los datos necesarios para gestionar
                  inscripciones y participantes, como el nombre y el equipo de cada jugador. No
                  tienen acceso a tu correo ni a tu teléfono personal.
               </p>
            </section>

            <section id="conservacion">
               <h2><span class="legal-numero">07</span> Cuánto tiempo conservamos los datos</h2>
               <p>
                  Conservamos los datos de tu cuenta mientras esté activa y sean necesarios para
                  prestarte el servicio. Si solicitás la baja, la cuenta se desactiva y deja de ser
                  visible en la plataforma.
               </p>
               <p>
                  Por la naturaleza del sistema, la baja es lógica: se conservan el historial
                  deportivo (equipos, inscripciones, resultados y rankings) y los registros de
                  seguridad y auditoría, porque permiten mantener la integridad de las
                  competiciones y responder ante incidentes. También se conserva la constancia de
                  aceptación de los documentos legales, con su versión y fecha, mientras exista la
                  cuenta o sea necesaria para acreditar el consentimiento. No existe un proceso
                  automático de purga de esos registros.
               </p>
               <p>
                  Podés solicitar la supresión de los datos que no deban conservarse por esas
                  razones. Evaluaremos el pedido conforme a la Ley 18.331 y te informaremos el
                  resultado. Los datos guardados en el almacenamiento local de tu navegador
                  permanecen hasta que los borres.
               </p>
            </section>

            <section id="seguridad">
               <h2><span class="legal-numero">08</span> Cómo protegemos los datos</h2>
               <p>Aplicamos medidas técnicas y organizativas para reducir riesgos de acceso no autorizado, pérdida o alteración:</p>
               <ul>
                  <li>Contraseñas almacenadas con hash bcrypt, nunca en texto plano.</li>
                  <li>Consultas preparadas contra la base de datos para prevenir inyección SQL.</li>
                  <li>Tokens de protección contra falsificación de solicitudes (CSRF) en los formularios.</li>
                  <li>Sesiones con cookie HttpOnly, SameSite=Lax, expiración por inactividad y regeneración del identificador al iniciar sesión.</li>
                  <li>Verificación del rol y del estado de la cuenta en cada solicitud.</li>
                  <li>Cabeceras HTTP de seguridad y control de acceso a las rutas internas.</li>
                  <li>Registro de accesos, cambios administrativos y errores para auditoría.</li>
               </ul>
               <p>
                  Ningún sistema es completamente seguro. Si detectás un incidente o un acceso
                  indebido, escribinos de inmediato a
                  <a href="mailto:arena.core@torneos.com?subject=Incidente%20de%20seguridad">arena.core@torneos.com</a>.
                  En una puesta en producción, el sitio debe servirse mediante HTTPS.
               </p>
            </section>

            <section id="derechos">
               <h2><span class="legal-numero">09</span> Tus derechos</h2>
               <p>Como titular de los datos personales, la Ley 18.331 te reconoce los derechos de:</p>
               <ul>
                  <li><strong>Acceso:</strong> conocer qué datos tuyos tratamos y para qué.</li>
                  <li><strong>Rectificación y actualización:</strong> corregir datos inexactos o incompletos.</li>
                  <li><strong>Inclusión:</strong> solicitar que se incorpore información que corresponda.</li>
                  <li><strong>Supresión:</strong> pedir la eliminación de datos que no deban conservarse.</li>
                  <li><strong>Oposición:</strong> oponerte a determinados tratamientos, cuando corresponda.</li>
                  <li><strong>Revocación del consentimiento:</strong> retirar tu consentimiento para los datos opcionales, sin afectar la licitud del tratamiento previo.</li>
               </ul>
               <p>
                  Para ejercerlos, escribí a
                  <a href="mailto:arena.core@torneos.com?subject=Datos%20personales">arena.core@torneos.com</a>
                  con el asunto «Datos personales», indicando tu nombre, el correo asociado a la
                  cuenta y el derecho que querés ejercer. Podemos pedirte información adicional
                  para verificar tu identidad y evitar que terceros accedan a tus datos.
               </p>
               <p>
                  Responderemos en un plazo máximo de cinco días hábiles, conforme a la Ley 18.331
                  y su normativa reglamentaria. El ejercicio de estos derechos es gratuito.
               </p>
               <p>
                  Si considerás que no dimos una respuesta adecuada, podés presentar una denuncia
                  ante la Unidad Reguladora y de Control de Datos Personales (URCDP) de Uruguay:
                  <a href="https://www.gub.uy/unidad-reguladora-control-datos-personales/" target="_blank" rel="noopener noreferrer">www.gub.uy/unidad-reguladora-control-datos-personales</a>.
               </p>
            </section>

            <section id="menores">
               <h2><span class="legal-numero">10</span> Menores de edad</h2>
               <p>
                  ASCEND está dirigida a personas mayores de 13 años. Si tenés entre 13 y 17 años,
                  necesitás la autorización de tu madre, padre o responsable legal para registrarte
                  y participar en torneos.
               </p>
               <p>
                  No solicitamos deliberadamente datos de menores de 13 años. Si detectamos una
                  cuenta de un menor de esa edad, la suspenderemos y eliminaremos los datos que
                  correspondan. Las personas responsables legales pueden escribir a
                  <a href="mailto:arena.core@torneos.com?subject=Datos%20de%20un%20menor">arena.core@torneos.com</a>
                  para consultar, rectificar o solicitar la baja.
               </p>
            </section>

            <section id="transferencias">
               <h2><span class="legal-numero">11</span> Transferencias internacionales</h2>
               <p>
                  El alojamiento previsto para este proyecto es local o institucional. Sin embargo,
                  la carga de tipografías y de la librería de íconos desde proveedores externos
                  (Google Fonts y cdnjs/Cloudflare) implica que tu dirección IP y datos técnicos de
                  la solicitud sean procesados por esos proveedores fuera del Uruguay.
               </p>
               <p>
                  Si en el futuro se contrata un servicio de alojamiento o de correo fuera del país,
                  procuraremos que el proveedor ofrezca garantías adecuadas de protección y lo
                  informaremos en esta Política, conforme a la Ley 18.331.
               </p>
            </section>

            <section id="cambios">
               <h2><span class="legal-numero">12</span> Cambios y contacto</h2>
               <p>
                  Podemos actualizar esta Política para reflejar cambios en el sistema o en la
                  normativa. La versión vigente estará publicada en esta página con su fecha. Si el
                  cambio es sustancial, procuraremos informarlo por los canales disponibles.
               </p>
               <p>
                  Por consultas sobre privacidad:
                  <a href="mailto:arena.core@torneos.com?subject=Datos%20personales">arena.core@torneos.com</a>.
                  También podés consultar los
                  <a href="<?php echo URL_BASE; ?>index.php?c=inicio&a=terminos">Términos y Condiciones</a>.
               </p>
            </section>

            <div class="legal-pie-documento">
               <p>
                  <strong>Documento:</strong> Política de Privacidad de ASCEND · Versión <?php echo VERSION_PRIVACIDAD; ?> ·
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
