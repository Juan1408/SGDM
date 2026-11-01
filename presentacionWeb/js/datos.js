/* Datos del recorrido E2E. Las 35 capturas se generaron el 01/11/2026
   con presentacionWeb/scripts/cdp-demo.mjs sobre el stack Docker. */

window.ASCEND_CHAPTERS = [
  {
    id: 'publico',
    label: 'Recorrido público',
    short: 'Público',
    range: '01–07',
    from: 1,
    to: 7,
    blurb: 'Un visitante investiga la plataforma sin crear cuenta.'
  },
  {
    id: 'alta',
    label: 'Alta y verificación',
    short: 'Alta',
    range: '08–10',
    from: 8,
    to: 10,
    blurb: 'Registro, aceptación legal y email verificado.'
  },
  {
    id: 'jugador',
    label: 'Jugador',
    short: 'Jugador',
    range: '11–19',
    from: 11,
    to: 19,
    blurb: 'Equipo, código de invitación e inscripción a un torneo.'
  },
  {
    id: 'organizador',
    label: 'Organizador',
    short: 'Organizador',
    range: '20–26',
    from: 20,
    to: 26,
    blurb: 'Solicitudes, aprobación, participantes y calendario.'
  },
  {
    id: 'admin',
    label: 'Administrador',
    short: 'Admin',
    range: '27–35',
    from: 27,
    to: 35,
    blurb: 'Usuarios, roles, torneos, auditoría y errores.'
  }
];

window.ASCEND_SLIDES = [
  {
    n: 1,
    file: '01-home-publica.png',
    chapter: 'publico',
    title: 'La puerta de entrada',
    lead: 'Un visitante sin cuenta entiende qué es ASCEND, qué torneos hay y dónde entrar según su rol.',
    points: [
      'Catálogo, torneos destacados y llamados a registrarse como jugador u organizador.',
      'La navegación pública no requiere sesión ni expone datos privados.'
    ],
    fact: ['Rol visitante', 'sin login'],
    route: 'index.php?c=inicio&a=index',
    caption: 'Home pública'
  },
  {
    n: 2,
    file: '02-catalogo-torneos.png',
    chapter: 'publico',
    title: 'Buscar sin saber el nombre',
    lead: 'El catálogo filtra por juego, estado y modalidad; el visitante compara antes de decidir.',
    points: [
      'Filtros visibles y tarjetas con estado de inscripción.',
      'Cada torneo abre su ficha pública con cupos, reglas y participantes.'
    ],
    fact: ['Catálogo', '26 torneos en la base'],
    route: 'index.php?c=torneo&a=catalogoPublico',
    caption: 'Catálogo de torneos'
  },
  {
    n: 3,
    file: '03-detalle-torneo.png',
    chapter: 'publico',
    title: 'La ficha que convence',
    lead: 'Torneo 1217, inscripciones abiertas: reglas, cupos, equipos confirmados y acceso a inscribirse.',
    points: [
      'Estado, fechas, formato y cupo máximo en una sola pantalla.',
      'El botón de inscripción aparece solo con sesión de jugador.'
    ],
    fact: ['5 / 8 cupos', 'Copa Counter-Strike'],
    route: 'index.php?c=torneo&a=detalle&id=1217',
    caption: 'Detalle del torneo 1217'
  },
  {
    n: 4,
    file: '04-organizadores.png',
    chapter: 'publico',
    title: 'Quién organiza',
    lead: 'Directorio público de organizadores con acceso a su perfil y a su actividad.',
    points: [
      'La confianza también es información: nombre, perfil y torneos.',
      'El perfil público no expone datos sensibles de la cuenta.'
    ],
    fact: ['Perfil público', 'solo lectura'],
    route: 'index.php?c=organizador&a=index',
    caption: 'Directorio de organizadores'
  },
  {
    n: 5,
    file: '05-perfil-organizador.png',
    chapter: 'publico',
    title: 'La reputación a la vista',
    lead: 'El perfil de Lucía Fernández reúne sus torneos y su actividad como organizadora.',
    points: [
      'Un organizador nuevo puede mostrar historial desde su primer torneo.',
      'El visitante decide inscribirse con contexto, no a ciegas.'
    ],
    fact: ['Trazabilidad', 'perfil público'],
    route: 'index.php?c=perfil&a=organizador&id=1000',
    caption: 'Perfil público del organizador'
  },
  {
    n: 6,
    file: '06-contacto.png',
    chapter: 'publico',
    title: 'El canal de soporte',
    lead: 'Formulario de contacto y suscripción a novedades, con validación en servidor.',
    points: [
      'El mensaje se valida y se confirma al usuario.',
      'La newsletter es opt-in: no es un envío automático.'
    ],
    fact: ['POST + CSRF', 'sin credenciales'],
    route: 'index.php?c=inicio&a=contacto',
    caption: 'Contacto y newsletter'
  },
  {
    n: 7,
    file: '07-formatos.png',
    chapter: 'publico',
    title: 'Explicar las reglas antes de jugar',
    lead: 'Página pública que explica liga, eliminación directa y suizo, en individual y por equipos.',
    points: [
      'El formato deja de ser una palabra técnica y se vuelve una decisión comprensible.',
      'Los tres motores están implementados y sembrados con datos reales.'
    ],
    fact: ['3 formatos', 'liga · eliminación · suizo'],
    route: 'index.php?c=inicio&a=formatos',
    caption: 'Formatos de torneo'
  },
  {
    n: 8,
    file: '08-formulario-registro.png',
    chapter: 'alta',
    title: 'Alta con reglas claras',
    lead: 'Nombre, email, teléfono y contraseña; aceptación legal y validación de formato de correo.',
    points: [
      'La contraseña cumple la política de composición desde el registro.',
      'El alta no habilita el login hasta verificar el email.'
    ],
    fact: ['Email verificado', 'requisito de login'],
    route: 'index.php?c=auth&a=mostrarRegistro',
    caption: 'Formulario de registro'
  },
  {
    n: 9,
    file: '09-cuenta-creada-verificacion.png',
    chapter: 'alta',
    title: 'Verificación simulada, control real',
    lead: 'La cuenta queda creada y el enlace de verificación se muestra en pantalla porque no hay SMTP.',
    points: [
      'El token es de un solo uso y tiene vencimiento.',
      'En producción, ese mismo flujo se conecta a un proveedor de correo.'
    ],
    fact: ['Token', 'un solo uso'],
    route: 'POST index.php?c=auth&a=procesarRegistro',
    caption: 'Cuenta creada, verificación pendiente'
  },
  {
    n: 10,
    file: '10-correo-verificado.png',
    chapter: 'alta',
    title: 'Recién ahora se puede entrar',
    lead: 'Al verificar, el login queda habilitado y el jugador entra a su panel.',
    points: [
      'El sistema no confía en un email escrito: exige demostrar acceso.',
      'La verificación también protege contra cuentas con direcciones mal tipeadas.'
    ],
    fact: ['email_verificado = 1', 'condición de login'],
    route: 'index.php?c=auth&a=verificarEmail&token=••••',
    caption: 'Correo verificado'
  },
  {
    n: 11,
    file: '11-panel-jugador.png',
    chapter: 'jugador',
    title: 'La carrera del jugador',
    lead: 'Dashboard con equipo, torneos, inscripciones, notificaciones y accesos rápidos.',
    points: [
      'Cada tarjeta responde una pregunta: qué tengo, qué sigue, qué pasó.',
      'El menú cambia por rol, pero la autorización se valida en el servidor.'
    ],
    fact: ['Rol 3', 'participante'],
    route: 'index.php?c=panelJugador&a=index',
    caption: 'Panel del jugador'
  },
  {
    n: 12,
    file: '12-crear-equipo.png',
    chapter: 'jugador',
    title: 'De jugador a capitán',
    lead: 'Nombre, juego, ubicación, año y descripción; el equipo nace con un capitán.',
    points: [
      'El juego se elige del catálogo, no se escribe libre.',
      'La validación ocurre en el cliente y se repite en el servidor.'
    ],
    fact: ['1 capitán', 'por equipo'],
    route: 'index.php?c=equipo&a=crear',
    caption: 'Crear equipo'
  },
  {
    n: 13,
    file: '13-equipo-creado.png',
    chapter: 'jugador',
    title: 'El equipo ya existe',
    lead: 'Ficha del equipo con identidad, plantilla y estado de inscripción.',
    points: [
      'El jugador puede gestionar su perfil de equipo sin tocar la base.',
      'El equipo queda listo para invitar miembros o inscribirse.'
    ],
    fact: ['Equipo demo', '#1145'],
    route: 'index.php?c=equipo&a=ver&id=1145',
    caption: 'Equipo creado'
  },
  {
    n: 14,
    file: '14-codigo-invitacion.png',
    chapter: 'jugador',
    title: 'Invitar sin planillas',
    lead: 'Código único de invitación para sumar jugadores al equipo.',
    points: [
      'El código se comparte y el sistema valida pertenencia y estado del equipo.',
      'No hace falta que el organizador cargue nombres a mano.'
    ],
    fact: ['Código', 'unirse por enlace'],
    route: 'index.php?c=equipo&a=gestionar&id=1145',
    caption: 'Código de invitación'
  },
  {
    n: 15,
    file: '15-segundo-registro.png',
    chapter: 'jugador',
    title: 'Otro jugador, misma puerta',
    lead: 'Sofía completa su propio registro y verifica el correo.',
    points: [
      'Cada jugador tiene identidad y credenciales propias.',
      'El equipo no comparte cuentas: comparte un código.'
    ],
    fact: ['2.º jugador', 'cuenta propia'],
    route: 'index.php?c=auth&a=mostrarRegistro',
    caption: 'Registro del segundo jugador'
  },
  {
    n: 16,
    file: '16-unirse-por-codigo.png',
    chapter: 'jugador',
    title: 'Entrar a un equipo existente',
    lead: 'El jugador ingresa el código y solicita sumarse al equipo.',
    points: [
      'El código evita buscar equipos por nombre.',
      'La operación queda asociada al usuario autenticado.'
    ],
    fact: ['Código', 'validado en servidor'],
    route: 'index.php?c=equipo&a=solicitudes',
    caption: 'Unirse por código'
  },
  {
    n: 17,
    file: '17-equipo-con-dos-miembros.png',
    chapter: 'jugador',
    title: 'Plantilla en crecimiento',
    lead: 'El equipo muestra dos integrantes activos y el capitán conserva la gestión.',
    points: [
      'Las altas y bajas se registran como estado, no como borrado destructivo.',
      'La plantilla visible se actualiza sin recargar datos a mano.'
    ],
    fact: ['2 miembros', 'activos'],
    route: 'index.php?c=equipo&a=ver&id=1145',
    caption: 'Equipo con dos miembros'
  },
  {
    n: 18,
    file: '18-inscripcion-equipo.png',
    chapter: 'jugador',
    title: 'Inscribir el equipo',
    lead: 'Desde el detalle del torneo, el capitán elige su equipo y envía la solicitud.',
    points: [
      'La inscripción es una solicitud, no una confirmación automática.',
      'El sistema valida cupo, estado del torneo y pertenencia al equipo.'
    ],
    fact: ['Solicitud', 'pendiente de aprobación'],
    route: 'index.php?c=torneo&a=detalle&id=1217',
    caption: 'Inscripción del equipo'
  },
  {
    n: 19,
    file: '19-inscripcion-pendiente.png',
    chapter: 'jugador',
    title: 'El estado importa',
    lead: 'La pantalla confirma que la solicitud quedó pendiente y qué debe pasar después.',
    points: [
      'El jugador no ve un “éxito” ambiguo: ve el estado real.',
      'El organizador decide; el jugador puede cancelar mientras espera.'
    ],
    fact: ['Pendiente', 'aprobación del organizador'],
    route: 'index.php?c=torneo&a=detalle&id=1217',
    caption: 'Inscripción pendiente'
  },
  {
    n: 20,
    file: '20-panel-organizador.png',
    chapter: 'organizador',
    title: 'Cambiar de sombrero',
    lead: 'Lucía entra como organizadora y ve sus torneos, solicitudes y métricas.',
    points: [
      'El mismo usuario puede ser jugador y organizador, pero cada sesión tiene un rol activo.',
      'El panel no mezcla permisos: el guard decide qué se muestra y qué se puede hacer.'
    ],
    fact: ['Rol 2', 'organizador'],
    route: 'index.php?c=panelOrganizador&a=index',
    caption: 'Panel del organizador'
  },
  {
    n: 21,
    file: '21-solicitudes-inscripcion.png',
    chapter: 'organizador',
    title: 'La bandeja de decisiones',
    lead: 'Solicitudes de inscripción con equipo, jugadores y estado; aprobar o rechazar.',
    points: [
      'Cada decisión cambia el estado y deja rastro para el jugador.',
      'El organizador solo ve torneos propios.'
    ],
    fact: ['Aprobar / rechazar', 'confirmación explícita'],
    route: 'index.php?c=panelOrganizador&a=administrar&seccion=solicitudes&id=1217',
    caption: 'Solicitudes de inscripción'
  },
  {
    n: 22,
    file: '22-inscripcion-aprobada.png',
    chapter: 'organizador',
    title: 'De pendiente a confirmada',
    lead: 'La solicitud del equipo demo pasa a confirmada y el cupo se actualiza.',
    points: [
      'El cambio se refleja en la ficha pública del torneo.',
      'El jugador recibe la novedad en su panel.'
    ],
    fact: ['Confirmada', '5 de 8 cupos'],
    route: 'index.php?c=panelOrganizador&a=administrar&seccion=solicitudes&id=1217',
    caption: 'Inscripción aprobada'
  },
  {
    n: 23,
    file: '23-participantes-confirmados.png',
    chapter: 'organizador',
    title: 'Quién está dentro',
    lead: 'Lista de participantes confirmados con equipo y jugadores.',
    points: [
      'El organizador prepara el fixture sobre datos confirmados, no sobre intenciones.',
      'El estado pendiente sigue visible para no perder solicitudes.'
    ],
    fact: ['5 / 8', 'cupos ocupados'],
    route: 'index.php?c=panelOrganizador&a=administrar&seccion=participantes&id=1217',
    caption: 'Participantes confirmados'
  },
  {
    n: 24,
    file: '24-mis-torneos.png',
    chapter: 'organizador',
    title: 'Portafolio del organizador',
    lead: 'Todos sus torneos con estado, formato y acceso a la administración.',
    points: [
      'Un organizador puede llevar varios torneos en paralelo.',
      'La ficha administrativa concentra configuración y participantes.'
    ],
    fact: ['Panel propio', 'solo torneos propios'],
    route: 'index.php?c=panelOrganizador&a=misTorneos',
    caption: 'Mis torneos'
  },
  {
    n: 25,
    file: '25-asistente-crear-torneo.png',
    chapter: 'organizador',
    title: 'Crear sin olvidar reglas',
    lead: 'Asistente por pasos: juego, formato, modalidad, cupos, fechas y reglas.',
    points: [
      'El formato determina qué campos y validaciones aplican.',
      'Las fechas y los cupos se validan antes de crear el torneo.'
    ],
    fact: ['3 formatos', 'configurables'],
    route: 'index.php?c=panelOrganizador&a=crear',
    caption: 'Asistente de creación'
  },
  {
    n: 26,
    file: '26-calendario-organizador.png',
    chapter: 'organizador',
    title: 'El torneo en el tiempo',
    lead: 'Calendario del organizador con actividades, fechas y encuentros.',
    points: [
      'Una sola vista para saber qué viene y qué se jugó.',
      'El calendario se alimenta de la configuración del torneo.'
    ],
    fact: ['97 encuentros', 'en la base demo'],
    route: 'index.php?c=panelOrganizador&a=calendario',
    caption: 'Calendario del organizador'
  },
  {
    n: 27,
    file: '27-panel-admin.png',
    chapter: 'admin',
    title: 'La vista del sistema',
    lead: 'El administrador no administra un torneo: administra la plataforma.',
    points: [
      'Usuarios, equipos, juegos, torneos, resultados, auditoría y errores.',
      'El menú se organiza por dominio, no por tabla de la base.'
    ],
    fact: ['Rol 1', 'administrador general'],
    route: 'index.php?c=admin&a=dashboard',
    caption: 'Panel de administración'
  },
  {
    n: 28,
    file: '28-usuarios.png',
    chapter: 'admin',
    title: 'Gobernar identidades',
    lead: 'Listado de usuarios administrativos con estado, rol y acceso a la ficha.',
    points: [
      'La ficha prioriza consulta sobre edición.',
      'El cambio de rol está limitado por la política del backend.'
    ],
    fact: ['Política de roles', '1 inmutable · 2↔3'],
    route: 'index.php?c=usuario&a=index',
    caption: 'Usuarios administrativos'
  },
  {
    n: 29,
    file: '29-ficha-usuario.png',
    chapter: 'admin',
    title: 'Ver sin editar de más',
    lead: 'Ficha administrativa con datos de la cuenta, rol y actividad.',
    points: [
      'El email del jugador queda en solo lectura.',
      'El administrador puede consultar sin convertirse en el usuario.'
    ],
    fact: ['Solo lectura', 'datos sensibles'],
    route: 'index.php?c=usuario&a=ver&id=1052',
    caption: 'Ficha de usuario'
  },
  {
    n: 30,
    file: '30-editar-rol.png',
    chapter: 'admin',
    title: 'El límite del poder',
    lead: 'Solo se puede alternar entre organizador y jugador; el rol 1 no se edita y el 4 está reservado.',
    points: [
      'La regla vive en el backend, no solo en el select.',
      'Cambiar el rol de un administrador está bloqueado.'
    ],
    fact: ['2 ↔ 3', 'único cambio permitido'],
    route: 'index.php?c=usuario&a=editarUsuario&id=1052',
    caption: 'Edición de rol'
  },
  {
    n: 31,
    file: '31-torneos-admin.png',
    chapter: 'admin',
    title: 'Supervisar todos los torneos',
    lead: 'Listado global con filtros y acceso a la ficha administrativa.',
    points: [
      'El admin puede intervenir cualquier torneo, incluso de otro organizador.',
      'Esa capacidad es la diferencia con el rol organizador.'
    ],
    fact: ['Intervención', 'global'],
    route: 'index.php?c=torneoAdmin&a=index',
    caption: 'Torneos desde administración'
  },
  {
    n: 32,
    file: '32-ficha-torneo-admin.png',
    chapter: 'admin',
    title: 'Todo el torneo en una ficha',
    lead: 'Configuración, inscripciones, participantes y estado del torneo 1217.',
    points: [
      'El equipo demo figura confirmado y el cupo muestra 5 de 8.',
      'El admin audita sin depender de la pantalla del organizador.'
    ],
    fact: ['1217', 'Copa Counter-Strike'],
    route: 'index.php?c=torneoAdmin&a=ver&id=1217',
    caption: 'Ficha administrativa del torneo'
  },
  {
    n: 33,
    file: '33-auditoria-dia.png',
    chapter: 'admin',
    title: 'Qué pasó hoy',
    lead: 'Log de auditoría filtrado por fecha: quién hizo qué y cuándo.',
    points: [
      'Incluye altas, cambios de estado, acciones administrativas y suplantación.',
      'El filtro por día convierte el log en una herramienta, no en un archivo.'
    ],
    fact: ['61 eventos', '01/11/2026'],
    route: 'index.php?c=auditoria&a=index&fecha=2026-11-01',
    caption: 'Auditoría del día'
  },
  {
    n: 34,
    file: '34-errores-sistema.png',
    chapter: 'admin',
    title: 'Cuando algo falla',
    lead: 'Monitoreo de errores del sistema con ruta, mensaje y contexto.',
    points: [
      'Los errores no se muestran al usuario con detalles internos.',
      'El dashboard permite priorizar por recurrencia.'
    ],
    fact: ['Observabilidad', 'errores_sistema'],
    route: 'index.php?c=errorTracking&a=dashboard',
    caption: 'Monitoreo de errores'
  },
  {
    n: 35,
    file: '35-admin-dashboard-final.png',
    chapter: 'admin',
    title: 'El ciclo completo',
    lead: 'El administrador vuelve al inicio con la foto global: usuarios, torneos, equipos y actividad.',
    points: [
      'El recorrido termina donde empezó: en una plataforma que se puede operar.',
      '35 capturas, cuatro perfiles y un mismo hilo: del visitante al administrador.'
    ],
    fact: ['35 capturas', 'E2E real'],
    route: 'index.php?c=admin&a=dashboard',
    caption: 'Dashboard final'
  }
];
