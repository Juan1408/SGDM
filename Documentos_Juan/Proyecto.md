INSTITUTO TECNOLÓGICO
SUPERIOR
A R I A S - B A L P A R D A
tu futuro
PROYECTO INFORMÁTICA
2026
Bachillerato Tecnológico
Tecnologías de la Información
Tramo 8 | Grado 3°
BT Tecnologías de la Información
Instituto Tecnológico Superior
“Arias - Balparda”
2026
Índice
Índice..................................................................................................................................0
1. Fundamentación del proyecto........................................................................................3
1.1 Presentación general............................................................................................. 3
1.2 Necesidad tecnológica y social.............................................................................. 3
1.3 Problema a resolver............................................................................................... 4
1.4 Justificación del proyecto....................................................................................... 5
Dimensión tecnológica........................................................................................... 5
Dimensión organizativa.......................................................................................... 5
Dimensión educativa.............................................................................................. 5
Dimensión social.................................................................................................... 5
2. Objetivos del proyecto....................................................................................................5
2.1 Objetivo general..................................................................................................... 5
2.2 Objetivos específicos............................................................................................. 6
3. Público objetivo.............................................................................................................. 7
4. Alcance del proyecto......................................................................................................7
4.1 Alcance técnico...................................................................................................... 7
Modelo....................................................................................................................7
Vista........................................................................................................................8
Controlador.............................................................................................................8
4.2 Requisitos de diseño de software.......................................................................... 8
4.3 Módulos funcionales mínimos................................................................................ 9
5. Roles del sistema...........................................................................................................9
5.1 Administrador general............................................................................................ 9
5.2 Organizador de torneo......................................................................................... 10
5.3 Participante.......................................................................................................... 10
5.4 Usuario público.....................................................................................................10
6. Tecnologías a utilizar.................................................................................................... 11
7. Interfaz de usuario (Mockups)...................................................................................... 11
8. Documentación obligatoria........................................................................................... 11
8.1 Documentación funcional..................................................................................... 12
8.2 Documentación técnica........................................................................................ 12
8.3 Documentación de seguridad...............................................................................12
8.4 Manual de usuario................................................................................................13
9. Exclusiones del sistema...............................................................................................13
10. Funcionalidades opcionales.......................................................................................13
11. Competencias Generales del MCN (Tramo 8)........................................................... 14
12. Competencias específicas tecnológicas y criterios de logro......................................15
Ingeniería del Software:............................................................................................. 15
Programación Full Stack:........................................................................................... 16
Administración de Sistemas Operativos:....................................................................17
Tutoría de Proyecto UTULAB:....................................................................................18
13. Plan de trabajo........................................................................................................... 20
1
14. Requerimientos de funcionamiento empresarial........................................................20
15. Requerimientos distinguidos por asignatura.............................................................. 21
Ingeniería de software................................................................................................21
Primera entrega....................................................................................................21
Segunda entrega..................................................................................................22
Tercera entrega.................................................................................................... 22
Administración de sistemas operativos...................................................................... 22
Primera entrega....................................................................................................22
Segunda entrega..................................................................................................22
Tercera entrega.................................................................................................... 23
Programación Fullstack..............................................................................................23
Primera entrega....................................................................................................23
Segunda entrega..................................................................................................23
Tercera entrega.................................................................................................... 23
Tutoría de proyecto UTULAB..................................................................................... 24
Primera entrega....................................................................................................24
Segunda entrega..................................................................................................24
Tercera entrega.................................................................................................... 24
Ciberseguridad........................................................................................................... 24
Primera entrega....................................................................................................24
Segunda entrega..................................................................................................24
Tercera entrega.................................................................................................... 25
16. Cronograma............................................................................................................... 25
17. Conformación de grupos............................................................................................25
18. Defensa y entrega final.............................................................................................. 26
19. ¿Cómo debo entregar mis entregas digitales?.......................................................... 27
20. ¿Cómo debo entregar, la ENTREGA IMPRESA?......................................................27
21. Reglamento vigente de proyecto BT Tecnologías de la Información......................... 28
Créditos............................................................................................................................29
Idea original................................................................................................................29
Realización y Coordinación........................................................................................29
Colaboración adicional............................................................................................... 29
2
1. Fundamentación del proyecto
1.1 Presentación general
El Sistema de Gestión Deportiva Modular, en adelante SGDM, es una plataforma web
destinada a la organización, administración y seguimiento de competencias deportivas,
mentales y electrónicas. El sistema busca resolver la necesidad de contar con una herramienta
flexible que permita gestionar diferentes formatos de torneo desde una misma plataforma,
evitando que cada organización deba utilizar soluciones separadas, planillas manuales o
sistemas diseñados para un único tipo de deporte.
El proyecto parte de una realidad actual: muchas instituciones, clubes, comunidades y
organizadores independientes desarrollan competencias de naturaleza muy diversa. En un
mismo entorno pueden coexistir torneos de fútbol, ajedrez, tenis de mesa, videojuegos
competitivos, juegos de cartas, deportes escolares u otras disciplinas. Aunque estas
actividades tienen reglas propias, comparten necesidades de gestión comunes, como
inscripción de participantes, generación de enfrentamientos, registro de resultados,
publicación de calendarios, administración de rankings y visualización pública de
información.
El SGDM propone una solución modular que permita adaptar la plataforma a distintos tipos
de competencia mediante módulos independientes. Cada módulo representará una forma de
organización competitiva, por ejemplo liga, eliminación directa o sistema suizo. De esta
manera, el sistema no estará limitado a un deporte específico, sino que podrá utilizarse en
diferentes contextos donde se requiera administrar participantes, enfrentamientos y
resultados.
1.2 Necesidad tecnológica y social
En el contexto actual, la separación entre deportes tradicionales, deportes mentales y deportes
electrónicos es cada vez menos rígida. Las instituciones educativas, clubes y comunidades
juveniles suelen organizar eventos que combinan diferentes tipos de competencias. Sin
embargo, muchas herramientas de gestión deportiva se encuentran pensadas para un único
modelo de competencia o para una disciplina específica.
3
Esta rigidez genera varios problemas:
● obliga a los organizadores a utilizar herramientas diferentes para cada tipo de
torneo
● aumenta la posibilidad de errores al manejar resultados de forma manual
● dificulta la publicación ordenada de calendarios, tablas y posiciones
● impide reutilizar reglas o configuraciones entre competencias similares
● limita la participación de organizaciones pequeñas que no cuentan con
recursos técnicos propios
● complica el seguimiento histórico de torneos, jugadores y equipos.
El proyecto busca democratizar el acceso a herramientas de organización competitiva
mediante una plataforma que pueda ser utilizada por clubes, instituciones educativas,
federaciones pequeñas, comunidades de videojuegos, organizadores de torneos recreativos y
grupos independientes.
La solución no se limita a los deportes físicos. También contempla competencias donde el
rendimiento no depende necesariamente de la actividad corporal directa, como ajedrez,
juegos de cartas, videojuegos o disciplinas de resolución estratégica. Por esa razón, el término
“deportiva” se utiliza en sentido amplio, incluyendo toda competencia organizada bajo reglas,
participantes, enfrentamientos y resultados.
1.3 Problema a resolver
El problema principal que aborda este proyecto es la falta de una plataforma única, modular y
adaptable para gestionar torneos con diferentes formatos de competencia.
Actualmente, un organizador que desea administrar distintos tipos de eventos puede
enfrentarse a situaciones como las siguientes:
● para una liga debe crear manualmente calendarios todos contra todos
● para un torneo de eliminación directa debe armar llaves y actualizarlas
después de cada ronda
● para un torneo con sistema suizo debe emparejar participantes según
rendimiento acumulado, evitando repeticiones
● para publicar resultados debe actualizar documentos, imágenes, redes sociales
o planillas externas
4
● para gestionar participantes debe mantener registros dispersos en distintos
archivos
Estas tareas consumen tiempo, generan errores y dificultan la transparencia del proceso
competitivo. Por lo tanto, el SGDM se plantea como una herramienta que centraliza la
gestión de la competencia, automatiza procesos repetitivos y permite mostrar información
actualizada de forma ordenada.
1.4 Justificación del proyecto
El proyecto se justifica desde cuatro dimensiones principales:
Dimensión tecnológica
Permite aplicar conocimientos de programación full stack, bases de datos, arquitectura MVC,
contenedores, automatización, seguridad web y despliegue de servicios.
Dimensión organizativa
Facilita la administración de torneos, reduce errores humanos y mejora la comunicación entre
organizadores y participantes.
Dimensión educativa
Permite integrar contenidos de distintas unidades curriculares, como Ingeniería de Software,
Programación, Administración de Sistemas Operativos y Ciberseguridad.
Dimensión social
Ofrece una solución accesible para organizaciones que necesitan gestionar competencias sin
depender de herramientas costosas o excesivamente especializadas.
2. Objetivos del proyecto
2.1 Objetivo general
Diseñar, desarrollar e implementar una plataforma web modular para la gestión de torneos
deportivos, mentales y electrónicos, que permita administrar participantes, generar estructuras
5
de competencia, registrar resultados, visualizar información pública y desplegar módulos
independientes mediante contenedores.
2.2 Objetivos específicos
El proyecto deberá cumplir los siguientes objetivos específicos:
1. Diseñar una arquitectura de software basada en el patrón
Modelo-Vista-Controlador.
2. Implementar una aplicación web con separación clara entre lógica de negocio,
presentación y acceso a datos.
3. Desarrollar un sistema de autenticación y autorización basado en roles.
4. Permitir la creación y administración de torneos de diferentes tipos.
5. Implementar al menos tres módulos de competencia: liga, eliminación directa
y sistema suizo.
6. Automatizar la generación de enfrentamientos según el tipo de torneo
seleccionado.
7. Permitir el registro, edición y validación de resultados.
8. Generar tablas de posiciones, llaves o clasificaciones según corresponda.
9. Diseñar una interfaz pública responsive para consulta de calendarios,
resultados y posiciones.
10. Diseñar un panel de administración para la gestión interna de torneos,
participantes y configuraciones.
11. Implementar una base de datos relacional que asegure persistencia, integridad
y trazabilidad de la información.
12. Desplegar el sistema mediante contenedores Docker.
13. Crear scripts de administración para instalación, despliegue, respaldo y
mantenimiento básico.
14. Aplicar controles mínimos de ciberseguridad basados en buenas prácticas y
OWASP Top 10.
15. Elaborar documentación técnica, funcional y de usuario.
6
3. Público objetivo
El SGDM estará orientado a los siguientes tipos de usuarios u organizaciones:
● clubes deportivos
● instituciones educativas
● docentes que organicen competencias internas
● centros juveniles
● federaciones o asociaciones pequeñas
● organizadores independientes
● comunidades de deportes electrónicos
● comunidades de juegos de mesa, cartas o estrategia
● participantes que deseen consultar sus calendarios, resultados y posiciones
El sistema deberá diseñarse considerando que no todos los usuarios tendrán conocimientos
técnicos. Por esta razón, la interfaz pública y el panel administrativo deberán utilizar textos
claros, formularios comprensibles y mensajes de error que expliquen el problema y orienten
al usuario sobre cómo corregirlo.
4. Alcance del proyecto
4.1 Alcance técnico
El sistema se desarrollará como una aplicación web full stack utilizando PHP para el
backend, HTML, CSS y JavaScript para el frontend, y una base de datos relacional para la
persistencia de información Mysql.
La arquitectura del sistema deberá seguir el patrón Modelo-Vista-Controlador.
Modelo
El modelo será responsable de representar las entidades principales del sistema y gestionar la
comunicación con la base de datos. Entre las entidades mínimas se deberán considerar (como
recomendación):
7
● Usuario
● Rol
● Participante
● Equipo
● Torneo
● Tipo de torneo
● Módulo de competencia
● Ronda
● Enfrentamiento
● Resultado
● Tabla de posiciones
● Configuración del torneo
● Auditoría o registro de actividad.
Vista
La vista será responsable de mostrar la información al usuario. Deberá incluir vistas
diferenciadas para:
● usuarios no autenticados
● participantes autenticados
● administradores
● consulta pública de torneos
● gestión interna del sistema
Controlador
El controlador será responsable de recibir las solicitudes, validar permisos, coordinar la
lógica de negocio y seleccionar la respuesta correspondiente. Los controladores deberán
evitar mezclar código de presentación con lógica de negocio.
4.2 Requisitos de diseño de software
El diseño del sistema deberá contemplar:
● separación de responsabilidades
● bajo acoplamiento entre módulos
● alta cohesión dentro de cada módulo
8
● reutilización de componentes comunes
● validación de datos en frontend y backend
● manejo centralizado de errores
● uso de rutas claras y coherentes
● documentación de funciones y clases relevantes
● estructura de carpetas ordenada
4.3 Módulos funcionales mínimos
El sistema deberá incluir, como mínimo, los siguientes módulos:
1. Módulo de usuarios y autenticación.
2. Módulo de gestión de participantes y equipos.
3. Módulo de torneos.
4. Módulo de liga.
5. Módulo de eliminación directa.
6. Módulo de sistema suizo.
7. Módulo de resultados.
8. Módulo de consulta pública.
9. Módulo de administración del sistema.
5. Roles del sistema
El sistema deberá contemplar roles diferenciados, con permisos claramente definidos.
5.1 Administrador general
El administrador general tendrá control completo sobre el sistema. Sus funciones serán:
➔ crear, editar y eliminar usuarios administrativos
➔ crear y configurar torneos
➔ habilitar o deshabilitar módulos
➔ gestionar participantes y equipos
9
➔ registrar y modificar resultados
➔ acceder a reportes
➔ consultar registros de auditoría
➔ Administrar configuraciones generales del sistema
5.2 Organizador de torneo
El organizador tendrá permisos limitados a los torneos que administra. Podrá:
➔ configurar torneos asignados
➔ inscribir participantes
➔ generar rondas o llaves
➔ cargar resultados
➔ corregir resultados si cuenta con autorización
➔ publicar o cerrar rondas
➔ consultar reportes del torneo.
5.3 Participante
El participante podrá:
➔ iniciar sesión
➔ consultar los torneos en los que participa
➔ ver calendario de enfrentamientos
➔ consultar resultados
➔ consultar posición o clasificación
➔ editar datos básicos de su perfil, si el sistema lo permite.
El participante no podrá modificar resultados, crear torneos ni alterar configuraciones de
competencia.
5.4 Usuario público
El usuario público no necesitará autenticación. Podrá:
➔ ver torneos publicados;
➔ consultar calendarios;
➔ ver resultados oficiales;
10
➔ consultar tablas de posiciones o llaves;
➔ acceder a perfiles públicos de equipos o participantes, si están habilitados.
No podrá realizar acciones que modifiquen información del sistema.
6. Tecnologías a utilizar
● Frontend: HTML, CSS, JavaScript, JSON
● Backend: PHP, JSON
● Base de datos: MySQL
● Otros (opcional): APIs de terceros (Google Maps para ubicación por ejemplo), sistema
de control de versiones (Git)
7. Interfaz de usuario (Mockups)
Se desarrollarán mockups de la interfaz de usuario para visualizar la estructura y el flujo de la
aplicación. Esto incluirá:
● Página de inicio
● Página de búsqueda de torneos
● Página de detalle del torneo
● Perfil del usuario
● Formulario de creación de competencia
● Entre otras
8. Documentación obligatoria
El proyecto deberá entregar documentación clara y suficiente para comprender, instalar, usar
y evaluar el sistema.
11
8.1 Documentación funcional
Deberá incluir:
● descripción del problema
● objetivos
● alcance
● roles del sistema
● funcionalidades
● reglas de negocio
● casos de uso principales
● capturas o prototipos de interfaz
8.2 Documentación técnica
Deberá incluir:
● arquitectura general
● estructura de carpetas
● modelo de base de datos
● explicación de módulos
● instrucciones de instalación
● configuración de Docker
● scripts
● decisiones técnicas tomadas
8.3 Documentación de seguridad
Deberá incluir:
● riesgos identificados
● medidas aplicadas
● roles y permisos
● protección de credenciales
● validaciones implementadas
● registro de auditoría
12
8.4 Manual de usuario
Deberá explicar cómo:
● iniciar sesión
● crear un torneo
● registrar participantes
● seleccionar tipo de torneo
● generar enfrentamientos
● cargar resultados
● consultar posiciones
● publicar información
9. Exclusiones del sistema
Para evitar ambigüedades, se establece que el sistema no incluirá:
● procesamiento de pagos
● integración con pasarelas de pago
● integración con redes sociales
● inteligencia artificial embebida
● arbitraje automático
● sistema de apuestas
● venta de entradas
● control de acceso físico a eventos
10. Funcionalidades opcionales
Si el equipo completa los requisitos mínimos, podrá implementar funcionalidades adicionales
como:
● exportación de reportes en PDF
● exportación de datos en CSV
● notificaciones por correo electrónico
13
● personalización visual de torneos
● ranking histórico de participantes
● módulo de grupos + playoffs
● doble eliminación
● panel estadístico avanzado
● integración con APIs externas
● sistema de alertas o comunicados
● carga masiva de participantes
Las funcionalidades opcionales no reemplazan los requisitos mínimos. Solo serán
consideradas como mejoras si el sistema base está completo y funcionando.
11. Competencias Generales del MCN (Tramo 8)
El desarrollo de este proyecto contribuirá al logro de las siguientes competencias generales
del Marco Curricular Nacional para el Tramo 8, con un énfasis particular en aquellas
potenciadas por las unidades curriculares tecnológicas de la orientación:
● Comunicación: Expresarse oralmente y por escrito de forma eficiente, interpretar datos
e información para elaborar reportes técnicos, interactuar asertivamente.
● Pensamiento computacional: Identificar, aplicar y elaborar modelos para la solución de
problemas, promover, planificar, crear o modificar respuestas algorítmicas o dispositivos
aplicados utilizando nuevas tecnologías.
● Pensamiento crítico: Identificar fenómenos sociales, locales y globales, comprender su
interrelación, posicionarse desde una mirada crítica, analítica y reflexiva, plantear
preguntas para analizar temas complejos, fundamentar un punto de vista complejo.
● Pensamiento creativo: Planifica, organiza y coordina acciones creativas e innovadoras,
generar propuestas orientadas a la innovación, experimentar con tecnologías.
● Pensamiento científico: Identifica, aplica y elabora modelos para la solución de
problemas, sigue procedimientos de investigación e incorpora metodologías apropiadas.
● Metacognitiva: Reflexionar de forma autónoma sobre sus procesos de construcción de
pensamiento y de estrategias para un aprendizaje permanente.
● Relación con los otros: Actuar con empatía, respetar y valorar las singularidades,
participar asertivamente en sus interacciones, promover acciones comunes, trabajar en
14
equipos de trabajo.
● Iniciativa y orientación a la acción: Comprometerse en la búsqueda autónoma de un
proyecto de vida, planificar, organizar y coordinar acciones creativas e innovadoras.
● Ciudadanía local, global y digital: Reconocer y promover derechos y
responsabilidades, participar en espacios digitales de intercambio y producción
fomentando la innovación, utilizar, producir y evaluar la información digital de forma
creativa, crítica y responsable.
● Intrapersonal: Reflexionar, reconocer y expresar emociones, deseos e intereses,
reconocer y atender los procesos de transformación de su cuerpo, valorar y reflexionar
de forma autónoma sobre sus procesos de construcción de pensamiento.
12. Competencias específicas tecnológicas y criterios de
logro
Este proyecto se alinea directamente con las competencias específicas tecnológicas (CET) y
sus correspondientes Criterios de Logro (CL) del Tramo 8 para la orientación Tecnologías de
la Información, abordadas en las unidades curriculares de Administración de Sistemas
Operativos, Ingeniería del Software, Programación Full Stack y Tutoría de Proyecto
UTULAB.
Ingeniería del Software:
● CET1. Analiza, diseña y documenta la especificación de requerimientos de un sistema
informático, así como la creación de modelos, para la representación, de manera precisa,
de las necesidades y expectativas de los usuarios y stakeholders.
○ CL1: Evalúa y determina un análisis exhaustivo y preciso de los requerimientos del
sistema, gestionándolos de manera efectiva a lo largo de todo el ciclo de vida del
proyecto. Identifica los posibles usuarios clasificándolos según la asignación de
roles primarios del sistema informático. Diseña y establece el uso de técnicas de
relevamiento, mediante la estructura y normativas de formularios estándar de la
ingeniería del software, asegurando la decisión lógica del sistema en su autonomía.
● CET2. Diseña, diagrama e implementa el modelado de sistemas informáticos para la
solución de problemas, de manera robusta, escalable y alineada con los requerimientos
del usuario.
15
○ CL2: Diseña e implementa modelos de sistemas informáticos que representan el
entorno en el que opera, incluyendo factores externos, interacciones y restricciones.
Aplica la clasificación de los diferentes tipos de diagramas UML, desarrollando los
aspectos del sistema, mediante la unificación del lenguaje de modelado.
● CET3. Caracteriza e implementa las fases de gestión, planificación y control de
proyecto, para el desarrollo del software, asegurando entrega a tiempo, con controles de
presupuesto y la calidad requerida.
○ CL3: Identifica y analiza problemas comunes en la gestión de proyectos de
software, solucionando retrasos, desajustes de presupuesto y problemas de
comunicación, mediante el diseño de cronogramas de actividades. Elabora
cronogramas utilizando diagramas de barras y estructuras de red (Gantt y PERT) en
la planificación y visualización del progreso del proyecto. Ajusta y optimiza
cronogramas en respuesta a cambios en el proyecto, asegurando que los plazos se
cumplan y los recursos se utilicen de manera eficiente. Propone soluciones efectivas
y prácticas, implementando estrategias proactivas en la mitigación de riesgos, la
resolución de conflictos y cálculos de estimación del desarrollo del software.
Programación Full Stack:
● CET1. Implementa técnicas de desarrollo Front End utilizando HTML5, CSS3 y
JavaScript, para la elaboración de productos informáticos dinámicos, mediante un diseño
responsive y moderno de la arquitectura web.
○ CL1: Mejora la performance de las páginas web mediante el uso de HTML5, CSS y
JavaScript.
● CET2. Integra los fundamentos del desarrollo Backend con PHP para programar
soluciones informáticas, acorde a las necesidades del usuario en procesos de innovación
tecnológica.
○ CL2: Diseña y prototipa páginas web mediante editores de código y web mediante
análisis de mercado. Publica sitios web usando aplicaciones con servicios web
siguiendo estrategias de marketing digital.
16
● CET3. Diseña, implementa y gestiona bases de datos relacionales utilizando un gestor
de base de datos, mediante la aplicación de principios de modelado, lenguaje SQL y
técnicas de administración para desarrollar soluciones eficientes de almacenamiento y
recuperación de datos en aplicaciones web con PHP.
○ CL3: Crea modelos conceptuales y lógicos de bases de datos utilizando el Modelo
Entidad-Relación y el Modelo Relacional, aplicando las formas normales y
estableciendo restricciones adecuadas que garanticen la integridad y eficiencia de
los datos. Utiliza el lenguaje SQL y define estructuras de datos (DDL), realizando
operaciones CRUD (CREATE, READ, UPDATE, DELETE). Implementa relaciones
y JOINS, maneja transacciones, crea subquerys y vistas, y administra usuarios en
entorno MySQL.
● CET4. Desarrolla e implementa un proyecto full stack integrando tecnologías front-end
y back-end con base de datos relacionales, para crear una web dinámica que resuelve
problemas informáticos de mediano porte, con énfasis en la seguridad, funcionalidad y
experiencia del usuario.
○ CL4: Crea una aplicación web funcional que integra tecnologías front-end (HTML,
CSS, JavaScript) y back-end (PHP, MySQL), implementando operaciones CRUD y
comunicación asincrónica. Implementa medidas de seguridad efectivas mediante
validación de datos y evitando ataques del usuario al sistema. Realiza pruebas
funcionales y de usuario, despliega la aplicación en un servidor web, y establece un
plan de mantenimiento y actualización.
Administración de Sistemas Operativos:
● CET1. Implementa la administración de servicios del sistema operativo para diseñar
aplicaciones, mediante la terminal bash y lenguajes de programación orientados al
scripting, automatizando las diferentes tareas rutinarias del sistema, desde el lado del
servidor.
○ CL1: Desarrolla scripts funcionales que permiten administrar diferentes servicios
del sistema operativo, mediante ejercitación práctica en la terminal bash. Utiliza de
manera efectiva los comandos básicos de bash y las estructuras de control, en
lenguajes de scripting, diseñando aplicaciones. Configura permisos y gestiona la
seguridad del sistema, a través de scripts, asegurando un entorno seguro y funcional.
17
● CET2. Analiza y evalúa la gestión de máquinas virtuales y contenedores para brindar
soluciones eficientes y realizar el despliegue de aplicaciones.
○ CL2: Administra entornos virtuales de manera eficiente, utilizando herramientas de
gestión de máquinas virtuales. Implementa contenedores de forma efectiva,
utilizando herramientas de gestión de contenedores, incluyendo la configuración de
redes, volúmenes y variables de entorno. Asegurar el funcionamiento adecuado y
óptimo de los recursos disponibles en el servidor, mediante la implementación de
aplicaciones, en entornos virtuales y de contenedores.
Tutoría de Proyecto UTULAB:
● CET1. Aplica y experimenta en forma creativa con técnicas y herramientas manuales,
tradicionales, analógicas y digitales de prototipado para el testeo de ideas y propuestas
en el desarrollo de proyectos técnicos y tecnológicos, generados tanto individual como
colectivamente.
○ CL1: Utiliza herramientas manuales, tradicionales, analógicas y digitales de
prototipado disponibles en el laboratorio de tecnologías UTULAB en la generación
de maquetas y simulaciones en 2D y 3D.
● CET2. Identifica y aplica herramientas analíticas, creativas y proyectuales para la
indagación, la identificación y el análisis de situaciones problema en contexto y para la
generación de propuestas orientadas a la innovación.
○ CL1: Selecciona y aplica herramientas analíticas en actividades de indagación,
identificación y análisis de situaciones problema.
○ CL2: Genera alternativas de manera estructurada a partir de las reflexiones
realizadas a partir de las herramientas creativas.
○ CL3: Selecciona y aplica herramientas creativas en la generación de ideas y
propuestas, a partir de las reflexiones desprendidas del análisis y la definición del
problema.
○ CL4: Selecciona y aplica herramientas de validación de ideas y prototipos en el
desarrollo del proyecto.
18
● CET3. Diseña y evalúa experiencias de usuario e interfaces digitales, aplicando
principios de diseño centrado en las personas y accesibilidad.
○ CL1. Aplica metodologías de investigación para comprender las necesidades y
expectativas de los usuarios.
○ CL2. Diseña flujos de usuario y arquitecturas de información claras y eficientes para
proyectos digitales.
○ CL3. Crea prototipos de interfaces digitales que respondan a los principios de
usabilidad y estética.
○ CL4. Realiza evaluaciones de usabilidad para identificar áreas de mejora en las
propuestas de diseño.
○ CL5. Incorpora principios de accesibilidad en el diseño de interfaces digitales para
garantizar su uso por parte de diversas poblaciones de usuarios.
● CET4. Gestiona y comunica el proceso de desarrollo de proyectos tecnológicos,
demostrando capacidad de seguimiento y articulación con los objetivos del proyecto de
egreso.
○ CL1. Planifica las etapas y tareas necesarias para el desarrollo de un proyecto
tecnológico.
○ CL2. Utiliza herramientas de seguimiento para monitorear el progreso del proyecto.
○ CL3. Comunica de manera efectiva los avances, desafíos y resultados del proyecto a
diferentes audiencias.
○ CL4. Articula las actividades del proyecto individual o grupal con los requisitos y
objetivos del proyecto de egreso.
○ CL5. Identifica y propone soluciones a posibles desviaciones o problemas que
surjan durante la ejecución del proyecto.
Para cada entrega los docentes de las unidades curriculares seguirán la guía de la letra de
proyecto para que los alumnos preparen la mencionada entrega garantizando que los grupos
logren a la fecha de entrega final definida el producto solicitado cumpliendo los
requerimientos de funcionamiento del proyecto y criterio de logro de las unidades
curriculares.
19
13. Plan de trabajo
Deberán desarrollar el proyecto de acuerdo a las pautas acordadas y estudiadas en las
unidades curriculares Ingeniería de software y Tutoría de proyecto UTULab considerando los
fundamentos para el análisis y diseño. Se integrarán los conocimientos y habilidades
adquiridos en Administración de Sistemas Operativos, Programación Full Stack y
Ciberseguridad para la implementación y despliegue del proyecto.
● Implementación y documentación
● Despliegue de la aplicación
● Documentación del proyecto (manual de usuario, manual técnico)
14. Requerimientos de funcionamiento empresarial
La realización de este Proyecto, será a través de la creación de una nueva Empresa al
efecto de cumplir el mismo.
Asimismo, se solicita la recomendación del hardware óptimo a adquirir para cubrir las
necesidades.
Para ello, se hará especial hincapié en los siguientes parámetros:
● Costos
● Claridad expositiva
● Documentación presentada
● Fundamentación de las afirmaciones realizadas
● Amigabilidad del Sistema con respecto a los Usuarios directos
● Vigencia, en el momento actual, de las herramientas presentadas, para las
diferentes soluciones.
● Utilización de herramientas gráficas que ilustren a los interesados, sobre sus
requerimientos.
En un tema tan delicado como éste, se quiere dejar sentado que para la realización de éste
proyecto será de fundamental importancia todo lo que se realice en materia de seguridad
tanto en lo que se refiere a los propios datos como al equipamiento que sea utilizado para
estos fines, por lo tanto, se espera obtener junto con la propuesta del sistema a realizar,
un estudio profundo sobre éste tema para datos de carácter “muy” delicados.
20
En éste sentido, se pretende que, por lo menos, deberá quedar registrado cualquier cambio
que se realice en la información de la Base de Datos (es decir, que deberá mantenerse
una historia de todo lo realizado ante la Base de Datos), así como deberá considerarse la
posibilidad de tener que realizar cambios en la red local, equipamiento, etc. caso para el
cual se deberán prever los recaudos necesarios para que la posibilidad de pérdida de
información se reduzca al mínimo posible.
El grupo de proyecto, entonces, deberá realizar un estudio profundo sobre:
● Todo el Hardware necesario para que los puestos previstos funcionen con el mejor
rendimiento posible.
● Todos los elementos de seguridad necesarios para que dicha red interna no sufra interferencias
y que el riesgo de caída, pérdida de velocidad o ingresos no autorizados queden reducidos al
mínimo posible.
● Al implementar alguno de los Servicios que dé la RED, cómo, por ejemplo, DHCP, HTTP
(Intranet), etc., deberá especificarse la configuración de los mismos, así como deberá
especificarse la conexión a Internet.
Se espera que logren una plataforma web funcional y eficiente que facilite la conexión entre
todas las partes del servicio, mejorando la accesibilidad y calidad de los servicios ofrecidos.
Además, se espera que el proyecto demuestre las habilidades y conocimientos adquiridos
durante el bachillerato, en consonancia con las competencias generales y específicas del
Tramo 8.
15. Requerimientos distinguidos por asignatura
Ingeniería de software
Primera entrega
● Documentación de inicio y planificación del proyecto
○ Cómo está organizado su equipo, según Mantei y Constantine, justificada
○ Reglas del grupo (mínimo 7)
○ Ciclo de vida de su proyecto con fundamentación.
○ Roles dentro del equipo
○ Diagrama de Gantt hasta la primer entrega
● Justificación de técnica/s de relevamiento de datos elegida /s.
● Modelo de formulario/s empleado/s para relevar datos.
● Resultados crudos de las técnicas de relevamiento utilizadas
● Documento ESRE (IEEE 29148) con los apartados mínimos:
21
○ Propósito
○ Alcance
○ Definiciones, acrónimos y abreviaciones
○ Limitaciones
● Identificación de roles y perfiles de usuario de la aplicación con sus responsabilidades
y nivel de acceso (tabla de datos)
Segunda entrega
● Actualización de ESRE con requisitos específicos (funcionales y no funcionales)
● UML – diagramas
○ Diagramas UML (Casos de Uso) (Planilla y diagramación)
○ Diagramas de clase.
● Estudio de factibilidad
● Plan de contingencias.
● Diagrama de Gantt (hasta la segunda entrega).
Tercera entrega
● Documento de calidad de software en entornos web (familia ISO 25000)
● Plan de testing con implementación de pruebas de testing manual y unitario (phpunit,
cypress, etc)
● Encuesta de percepción de calidad
● Plan de seguridad web basado en OWASP
● Cálculo de métricas de proyecto (punto de función)
● Diagrama de Gantt (cronograma de la gestión del proyecto completo).
● Manual de usuario operacionales
● Manual de administrador
Administración de sistemas operativos
Primera entrega
● Estudio de sistemas operativos cliente y servidor.
● Análisis de usuarios de sistema.
● Script de gestión de usuarios del sistema implementado en el servidor.
Segunda entrega
● Política de respaldos.
22
○ Tipos de respaldos a utilizar
○ Cronograma de respaldo definido
● Script de respaldos y automatización (cron) implementado en el servidor.
● Sistema de monitoreo implementado en el servidor (zabbix, grafana, etc).
Tercera entrega
● Políticas de seguridad.
● Implementación de firewall, iptables, antivirus, entre otros dentro del servidor.
● Script de consultas y monitoreo de base de datos dentro del servidor.
● Script general de gestión del servidor
● Implementación con Docker de la aplicación web
Programación Fullstack
Primera entrega
● Maquetado de todo el sitio mediante filosofía Mobile First (utilizando Flexbox o
Grid).
Segunda entrega
● Modelo relacional normalizado.
● DCL implementado
● Configuración de usuarios de BD con las restricciones pertinentes.
● Implementación de los modelos alineados al modelo relacional
● Integración con PHP utilizando POO (mínimo gestión de usuarios funcionando).
● Implementación con Apache
Tercera entrega
● Implementación completa del sistema, de manera portable mediante Docker con datos
de prueba (mínimo 50 registros cada componente)
23
Tutoría de proyecto UTULAB
Primera entrega
● Definición de nombre y logo de grupo de proyecto (con justificación)
● Definición de logo de la aplicación
● Fundamentos de pensamiento basado en S.C.A.M.P.E.R
● Formularios de acta de reunión
● Actas de reunión formal interna (mínimo 6)
● Fundamentación de las elecciones de diseño del sistema
Segunda entrega
● Análisis FODA del proyecto a realizar
● Repositorio git accesible con toda la implementación del proyecto
● Actas de reunión formal interna de la segunda entrega (mínimo 6)
● Análisis costo-beneficio del sistema a desarrollar
● Seguimiento de planificación del sistema
Tercera entrega
● Actas de reunión formal interna de la tercera entrega (mínimo 6)
● Modelo 3D de producto diseñado para marketing de la empresa
● Manual de usuario de todos los roles del sistema
● Seguimiento final de la planificación del sistema
Ciberseguridad
Primera entrega
● Identificación de amenazas específicas para su sistema
● Posibles vulnerabilidades en infraestructura (servidor, base de datos, red interna)
● Controles de acceso y roles de usuarios
● Definición de políticas de contraseñas
● Políticas y medidas de seguridad
Segunda entrega
● Validaciones aplicadas en frontend y backend.
24
● Encriptación de contraseñas aplicada en el proyecto
● Esquema de red con implementaciones de seguridad
● Registro de implementaciones de seguridad para servidores (IDS, IPS, firewall,
fail2ban, etc)
Tercera entrega
● Aspectos de hashing, cifrado y firma digital implementado en el proyecto. Documento
de especificación de implementaciones
● Implementación de certificado SSL dentro del servidor
● Políticas de seguridad de usuario
● Informe OWASP de aspectos de seguridad del sitio
16. Cronograma
Se establecen las siguientes fechas clave para las entregas parciales y la defensa final:
● Primera entrega (digital): Lunes 27 de julio.
● Segunda entrega (digital): Lunes 14 de septiembre.
● Entrega final (impresa): Lunes 9 de noviembre.
● Defensas: 23, 24 y 25 de noviembre.
Se publican en tiempo y forma con horarios.
17. Conformación de grupos
● Los Grupos deberán estar conformados por:
○ Alumnos de un mismo tercer año.
○ Un mínimo de 3 (tres) y un máximo de 4 (cuatro) alumnos.
○ En caso de fuerza mayor, el Profesor de TUTORÍA DE PROYECTO UTU-LAB
decide casos excepcionales.
● La conformación del Grupo, deberá:
○ Ser dirigida al docente de Tutoría de proyecto UTULab dirigida a él y entregar copia
impresa a coordinación docente, de acuerdo con las normas de documentación
vigentes.
○ Deberá ser presentada en la semana siguiente a la publicación de este documento.
○ En dicha nota, deberá figurar la siguiente información mínima (tendrá un estándar
25
de presentación publicado en la página web en la sección INFORMÁTICA):
■ Nombre del tercer año al cual pertenece el grupo.
■ Nombre de fantasía asignado al grupo, debiendo ser un nombre con
posibilidades comerciales y que contenga un significado que los alumnos
puedan explicar llegado el caso.
■ Para cada uno de los alumnos, deberá aparecer:
■ Nombre completo y foto.
■ Número de cédula de identidad.
■ Teléfono particular, si lo tuviera.
■ Teléfono celular, si lo tuviera.
■ Dirección de email.
■ No se aceptará ninguna nota que no identifique plenamente al alumno, es
decir, que no contenga nombre y apellido del alumno.
■ Tener un representante, entre sus integrantes, el cual deberá ser explicitado.
● NO SE PODRÁN REALIZAR CAMBIOS EN LOS INTEGRANTES DE LOS
GRUPOS DE PROYECTOS LUEGO DE LA SEGUNDA ENTREGA.
18. Defensa y entrega final
● En ésta fecha cada uno de los grupos de cada tercer año deberá “vender” al plantel
docente el proyecto que han realizado.
● Junto con la “venta” deberá entregarse material impreso que justifique la misma.
26
19. ¿Cómo debo entregar mis entregas digitales?
Se deberán entregar, DOS PENDRIVE *, debidamente identificados, en un SOBRE DE
PAPEL con:
● Nombre fantasía del grupo de proyecto
● Grupo en que se cursa.
Dentro de cada PENDRIVE, deberá haber una carpeta por MATERIA, correctamente
identificadas con:
● Nombre de la materia
● Nombre del docente
Dentro de cada carpeta de cada MATERIA:
● Carpeta de la materia (formato PDF y que respete el estándar de documentación
publicado)
● Aplicación funcionando si corresponde.
● Cualquier archivo o imagen adicional
Recuerde que toda la documentación debe cumplir con la estandarización brindada por
la escuela. La misma se encuentra en nuestra PÁGINA WEB en la sección de
INFORMÁTICA.
Finalizada la corrección los PENDRIVE se devuelven para que puedan ser reutilizados.
* El segundo PENDRIVE será una copia del primero, ES LA COPIA DE RESPALDO.
20. ¿Cómo debo entregar, la ENTREGA IMPRESA?
Se deberá entregar una carpeta impresa POR CADA MATERIA debidamente identificada.
Los documentos impresos deberán cumplir con los estándares de documentación de la
escuela, que se encuentran publicados en la página web del instituto. Se deberá llevar dos
copias del estándar de entrega de documentos (una para la institución y otra para el grupo)
Notas IMPORTANTES:
Las fechas de cada una de las entregas/defensas serán inmodificables debido que estas son
coordinadas entre cuatro escuelas haciendo casi imposible su modificación.
27
Para este Proyecto regirá la integración de “Grupos” vigente para la primera entrega, con las
modificaciones que hayan sido realizadas y autorizadas desde ese momento. Se recuerda que
el grupo de Proyecto será indivisible a no ser que sea comunicada a la coordinación la
disolución del mismo y los ajustes que ello signifique en otros grupos. De todas maneras, es
de destacar que:
● Dicha disolución no tendrá efecto hasta tanto no sea aprobada por el cuerpo docente
● Todo lo realizado, en materia de proyecto, hasta ese momento, es propiedad
intelectual de todos los integrantes, es decir, pertenece a todos los integrantes del
grupo, por igual.
21. Reglamento vigente de proyecto BT Tecnologías de la
Información
EL REGLAMENTO DE REALIZACIÓN Y EVALUACIÓN DEL PROYECTO SE
ENCUENTRA EN VÍAS DE APROBACIÓN.
EL REGLAMENTO DE EMT SE USARÁ COMO REFERENCIA POR EL MOMENTO.
EL REGLAMENTO DE EMT DE INFORMÁTICA NO ESTÁ VIGENTE Y NO ES EL
REGLAMENTO POR EL CUAL SE RIGE EL PLAN QUE CURSAN (BT Tecnologías de la
Información, plan para la EMS 2023)
● Plan no vigente:
○ Los alumnos que se encuentran CURSANDO POR MATERIA por primera
vez de 3º año, NO DEBEN REALIZAR PROYECTO, se les recuerda que
deben informar en ADMINISTRACIÓN que se encuentran cursando por
materia y cuales, ANTES DE LA PRIMERA REUNIÓN DE
PROFESORES.
○ Los alumnos que ya cursaron 3º, el año pasado y se encuentre recursando
materias, NO DEBEN REALIZAR PROYECTO, conservan la nota
obtenida en el proyecto del año anterior, siendo esta el 50% de la nota de este
año en cada una de las materias que recurse, aunque pueden optar por
realizarlo nuevamente si pretenden mejorar la misma.
28
○ Los alumnos que se encuentran cursando 3º año por primera vez, todas las
materias, deberán realizar el proyecto para obtener la aprobación de cada una
de ellas, recordando que el mismo es el 50% de la nota.
29
Créditos
Idea original
Prof. Joaquín Perez
Prof. Federico Ventura
Realización y Coordinación
Prof. Christian Barrios
Colaboración adicional
Prof. Esther Chiribao
Prof. Roberto Franco
30