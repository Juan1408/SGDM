Requerimientos Funcionales de ASCEND
1. Módulo de usuarios y autenticación
RF-001 - Registro de Jugador: El Usuario Público podrá registrar una cuenta de Jugador proporcionando
los datos solicitados por el sistema.
RF-002 - Solicitud de registro como Organizador: El Usuario Público podrá solicitar una cuenta de
Organizador proporcionando los datos solicitados por el sistema.
RF-003 - Inicio de sesión: El usuario registrado podrá iniciar sesión utilizando sus credenciales para
acceder a las funcionalidades correspondientes a su rol.
RF-004 - Cierre de sesión: El usuario autenticado podrá cerrar su sesión cuando desee finalizar el acceso a
su cuenta.
RF-005 - Consulta de perfil: El usuario registrado podrá consultar la información de su propio perfil.
RF-006 - Modificación de perfil: El usuario registrado podrá modificar los datos habilitados de su propio
perfil.
RF-007 - Autorización por rol: El sistema deberá autorizar las funcionalidades correspondientes al rol
autenticado de Jugador, Organizador o Administrador.
2. Módulo de gestión de participantes y equipos
RF-008 - Creación de equipos: El Jugador podrá crear un equipo para participar en disciplinas de
modalidad por equipos.
RF-009 - Validación de referentes: El sistema deberá validar que cada equipo cuente con dos referentes
para habilitar las acciones de representación del equipo.
RF-010 - Envío de invitaciones: Cualquiera de los referentes podrá enviar una invitación a otro Jugador
registrado para incorporarlo al equipo.
RF-011 - Aceptación de invitaciones: El Jugador podrá aceptar una invitación recibida para incorporarse a
un equipo.
RF-012 - Rechazo de invitaciones: El Jugador podrá rechazar una invitación recibida para incorporarse a
un equipo.
RF-013 - Solicitud de inscripción: El usuario habilitado podrá solicitar la inscripción a un torneo durante
el período de inscripción, realizándola personalmente en disciplinas individuales o mediante cualquiera de
los dos referentes en disciplinas por equipos.
RF-014 - Aprobación de inscripción: El Organizador podrá aprobar una solicitud de inscripción
correspondiente a un torneo que organiza.
RF-015 - Rechazo de inscripción: El Organizador podrá rechazar una solicitud de inscripción
correspondiente a un torneo que organiza.
RF-016 - Validación de modalidad: El sistema deberá validar que el participante inscripto corresponda a la
modalidad individual o por equipos definida para la disciplina del torneo.
3. Módulo de torneos
RF-017 - Creación de torneos: El Organizador habilitado podrá crear un torneo correspondiente a una
disciplina disponible en ASCEND.

RF-018 - Publicación de torneos: El Organizador habilitado podrá publicar un torneo creado sin requerir
una aprobación individual del Administrador.
RF-019 - Selección de disciplina: El Organizador podrá seleccionar la disciplina del torneo entre Fútbol,
Básquetbol, Tenis, League of Legends, Counter-Strike 1.6, EA Sports FC 26, Truco, Catan y Ajedrez.
RF-020 - Determinación de modalidad: El sistema deberá asignar la modalidad individual o por equipos
según la disciplina seleccionada.
RF-021 - Selección del formato: El Organizador podrá seleccionar un formato de competencia entre los
formatos habilitados para la disciplina elegida.
RF-022 - Validación del formato: El sistema deberá validar que el formato seleccionado se encuentre
habilitado para la disciplina del torneo.
RF-023 - Configuración de cantidad mínima: El Organizador podrá establecer la cantidad mínima de
participantes o equipos requerida para el torneo, con un valor igual o superior a dos.
RF-024 - Configuración de cantidad máxima: El Organizador podrá establecer la cantidad máxima de
participantes o equipos admitida para el torneo.
RF-025 - Configuración del período de inscripción: El Organizador podrá establecer las fechas de
apertura y cierre del período de inscripción.
RF-026 - Configuración del período del torneo: El Organizador podrá establecer las fechas de inicio y
finalización del torneo.
RF-027 - Programación de enfrentamientos: El Organizador podrá establecer las fechas de los
enfrentamientos generados para el torneo.
RF-028 - Extensión de inscripciones: El Organizador podrá modificar la fecha de cierre de inscripciones
mientras el torneo no haya comenzado.
RF-029 - Reprogramación de enfrentamientos: El Organizador podrá modificar la fecha de un
enfrentamiento mientras este no se haya disputado.
RF-030 - Bloqueo del nombre: El sistema deberá bloquear la edición del nombre del torneo después de su
publicación.
RF-031 - Actualización del estado del torneo: El sistema deberá actualizar el estado del torneo según la
etapa alcanzada durante su desarrollo.
RF-032 - Generación de enfrentamientos: El sistema deberá generar los enfrentamientos del torneo según
el formato de competencia seleccionado.
RF-033 - Asignación de descansos: El sistema deberá asignar un descanso cuando una ronda tenga una
cantidad impar de participantes y el formato de competencia lo requiera.
RF-034 - Rotación de descansos: El sistema deberá priorizar participantes que no hayan recibido un
descanso previo cuando sea necesario asignar una nueva fecha libre.
4. Módulo de liga
RF-035 - Generación del calendario de Liga: El sistema deberá generar un calendario en el que los
participantes disputen los enfrentamientos establecidos por las reglas de la Liga.
RF-036 - Cálculo de puntos de Liga: El sistema deberá calcular los puntos correspondientes a victoria,
empate o derrota a partir del resultado registrado en cada enfrentamiento de Liga.
5. Módulo de eliminación directa
RF-037 - Sorteo de primera ronda: El sistema deberá generar aleatoriamente los enfrentamientos
correspondientes a la primera ronda de una competencia de Eliminación Directa.

RF-038 - Avance de ganadores: El sistema deberá asignar al ganador de cada enfrentamiento al cruce
correspondiente de la siguiente ronda hasta alcanzar la final.
6. Módulo de sistema suizo
RF-039 - Generación de primera ronda: El sistema deberá ordenar a los participantes según el ranking de
la disciplina y generar los emparejamientos iniciales aplicando aleatoriedad entre participantes
compatibles con el criterio de emparejamiento definido.
RF-040 - Emparejamiento por puntuación: El sistema deberá generar las rondas posteriores priorizando
enfrentamientos entre participantes con puntajes acumulados iguales o similares.
RF-041 - Control de enfrentamientos repetidos: El sistema deberá evitar repetir un enfrentamiento entre
los mismos participantes mientras exista otra combinación válida para la ronda.
7. Módulo de resultados
RF-042 - Registro de resultados: El Organizador podrá registrar el resultado de un enfrentamiento
perteneciente a un torneo que administra.
RF-043 - Corrección de resultados: El Organizador podrá modificar un resultado registrado cuando las
condiciones del torneo permitan su corrección.
RF-044 - Actualización del estado del enfrentamiento: El sistema deberá actualizar el estado del
enfrentamiento a partir del resultado registrado.
RF-045 - Determinación del ganador: El sistema deberá determinar el ganador de un enfrentamiento
cuando el resultado registrado permita establecerlo.
RF-046 - Actualización de clasificación: El sistema deberá actualizar las posiciones o clasificaciones de
los participantes después de registrar un resultado según las reglas del formato utilizado.
RF-047 - Actualización de ranking: El sistema deberá actualizar el ranking del Jugador en la disciplina
correspondiente a partir de los resultados oficiales registrados.
RF-048 - Almacenamiento del historial de resultados: El sistema deberá almacenar los resultados de los
enfrentamientos finalizados para permitir su consulta posterior.
8. Módulo de consulta pública
RF-049 - Navegación pública: El Usuario Público podrá acceder a las secciones públicas de ASCEND sin
iniciar sesión.
RF-050 - Búsqueda de torneos: El Usuario Público podrá buscar torneos publicados mediante los criterios
disponibles.
RF-051 - Filtrado de torneos: El Usuario Público podrá filtrar los torneos publicados mediante los
criterios disponibles.
RF-052 - Consulta de torneo: El Usuario Público podrá consultar la información general, disciplina,
modalidad, formato, fechas y estado de un torneo publicado.
RF-053 - Consulta de participantes: El Usuario Público podrá consultar los Jugadores o Equipos
participantes de un torneo publicado.
RF-054 - Consulta de enfrentamientos: El Usuario Público podrá consultar los enfrentamientos y el
calendario de un torneo publicado.
RF-055 - Consulta de resultados: El Usuario Público podrá consultar los resultados oficiales de los
enfrentamientos disputados.

RF-056 - Consulta de clasificación: El Usuario Público podrá consultar las posiciones o clasificaciones de
un torneo según su formato de competencia.
RF-057 - Consulta de perfiles públicos: El Usuario Público podrá consultar los perfiles públicos de
Jugadores, Equipos y Organizadores registrados en ASCEND.
RF-058 - Consulta de rankings: El Usuario Público podrá consultar el ranking de Jugadores
correspondiente a cada disciplina.
9. Módulo de administración del sistema
RF-059 - Acceso al panel de administración: El Administrador podrá acceder al panel de administración
para utilizar las funcionalidades correspondientes a su rol.
RF-060 - Consulta de solicitudes de Organizador: El Administrador podrá consultar las solicitudes
pendientes de registro como Organizador.
RF-061 - Aprobación de Organizador: El Administrador podrá aprobar una solicitud de registro como
Organizador para habilitar la cuenta correspondiente.
RF-062 - Rechazo de Organizador: El Administrador podrá rechazar una solicitud de registro como
Organizador.
RF-063 - Consulta de torneos: El Administrador podrá consultar los torneos registrados en la plataforma.
RF-064 - Modificación de torneos: El Administrador podrá modificar la información habilitada de los
torneos registrados.
RF-065 - Consulta de usuarios y equipos: El Administrador podrá consultar la información de Jugadores,
Organizadores y Equipos registrados.
RF-066 - Modificación de usuarios y equipos: El Administrador podrá modificar la información habilitada
de Jugadores, Organizadores y Equipos registrados.
RF-067 - Bloqueo de cuentas: El Administrador podrá bloquear temporal o permanentemente una cuenta
de Jugador u Organizador conservando su historial.
RF-068 - Bloqueo de equipos: El Administrador podrá bloquear temporal o permanentemente un Equipo
conservando su historial.
RF-069 - Eliminación de cuentas: El Administrador podrá eliminar una cuenta de Jugador u Organizador
cuando corresponda.
RF-070 - Configuración del estado de módulos: El Administrador podrá modificar el estado de un módulo
funcional entre habilitado y deshabilitado.
RF-071 - Consulta de auditoría: El Administrador podrá consultar los registros de actividad y
modificaciones almacenados por el sistema.
RF-072 - Consulta de estadísticas: El Administrador podrá consultar las estadísticas generales disponibles
sobre usuarios, equipos, torneos y competencias.
RF-073 - Visualización por rol: El Administrador podrá utilizar la función "Ver como" para interactuar
temporalmente con ASCEND desde la perspectiva del rol seleccionado sin modificar su rol de
Administrador.
RF-074 - Registro de actividad: El sistema deberá registrar las operaciones y modificaciones relevantes
realizadas sobre la información almacenada para permitir su posterior auditoría.