A continuación, se presenta una reorganización explicativa de los componentes técnicos esenciales utilizados para la gestión y conexión segura de bases de datos:

1. PHP Data Objects (PDO)
En versiones anteriores de PHP, la comunicación con las bases de datos se realizaba mediante funciones tradicionales como mysqli_query, las cuales presentaban importantes vulnerabilidades de seguridad. En la actualidad, PDO se erige como una clase moderna y orientada a objetos que opera como un traductor universal entre el código y el motor de datos.
Su principal beneficio radica en la protección automática que brinda contra ataques de "SQL Injection" (uno de los métodos de hackeo más extendidos a nivel global) a través de la implementación de "Sentencias Preparadas". Cuando se ejecuta la instrucción $this->pdo = new PDO(...), se está inicializando este traductor y suministrándole las credenciales de acceso a MySQL.

2. Data Source Name (DSN)
El DSN ($dsn) consiste en una cadena de texto (String) que establece los parámetros de localización o coordenadas esenciales para PDO. Su función es especificar las directrices de conexión, tales como el uso de una base de datos MySQL, el host correspondiente, el nombre de la base y el tipo de codificación del texto.

3. La Constante Mágica __DIR__
En el ecosistema de PHP, __DIR__ es identificada como una "constante mágica". Cuenta con la propiedad de reconocer con total exactitud la carpeta en la que se encuentra almacenado el archivo en ejecución, lo que facilita la localización de rutas relativas de manera independiente a la computadora donde se implemente el sistema.

4. Gestión de Errores: try { ... } catch { ... } y PDOException
Para garantizar la estabilidad de la aplicación, se estructuran los siguientes mecanismos de contingencia:
Estructura Try-Catch: Funciona como una red de seguridad operativa. La instrucción determina que el sistema intente (try) procesar un bloque de código; en caso de que ocurra una falla crítica, el sistema captura (catch) la anomalía para evitar el despliegue de una pantalla de error incomprensible para el usuario, permitiendo mostrar un mensaje amigable en su lugar.
PDOException: Representa la alarma específica del sistema de datos. Si PDO no logra establecer el enlace debido a que MySQL se encuentra inactivo o a que los datos de acceso son erróneos, se genera e interrumpe el flujo mediante una alerta denominada PDOException.


Archivo: modelos/Conexion.php
Su propósito fundamental es establecer un enlace técnico entre la lógica del sistema y el motor de MySQL bajo estrictos protocolos de integridad.

En cuanto a la infraestructura de comunicación, se emplea PDO (PHP Data Objects). Esta herramienta se desempeña como un mediador universal que resguarda la aplicación de forma nativa frente a vectores de ataque por inyección SQL.
Para optimizar los recursos del servidor, se implementa el Patrón Singleton (private static $instancia). Mediante la privacidad del método __construct(), se impide la generación de conexiones redundantes. La lógica se centraliza en getInstance(), función encargada de verificar la existencia previa del enlace para su reutilización o creación exclusiva.
La estabilidad se gestiona mediante el bloque try { ... } catch { ... }, que opera como una red de seguridad operativa. Este mecanismo permite que, ante una falla crítica de MySQL, el sistema capture (catch) la anomalía y despliegue una respuesta controlada en lugar de interrumpir la experiencia del usuario con errores técnicos.

5. Archivo: publico/test_bd.php
Consiste en un módulo de diagnóstico temporal diseñado para validar la integridad de las fases iniciales de configuración.

El procedimiento consistió en la integración de la clase Conexion mediante las instrucciones require_once y el espacio de nombres correspondiente. Tras solicitar la instancia de datos, se verificaba el estado de Apache y MySQL en el entorno XAMPP a través de una confirmación visual en pantalla.

6. Archivo: ayudantes/Sesion.php
Actúa como el guardián de seguridad del sistema, estableciendo una barrera perimetral para restringir el acceso a los módulos administrativos según el estado de autenticación.

Dada la naturaleza del protocolo HTTP, PHP utiliza la Superglobal $_SESSION. Este repositorio temporal en el servidor almacena la información vital del usuario, como su identidad y privilegios, tras una validación exitosa de credenciales.
Debido a que las funciones se definieron como static, se emplea la referencia self:: para la invocación de herramientas internas de la clase, eliminando la necesidad de instanciar nuevos objetos.
Controles de Ciberseguridad (OWASP):
session_regenerate_id(true): Actualiza el identificador de sesión durante el acceso para mitigar riesgos de Session Fixation.
Control de Expiración (time() + 1800): Implementa una política de Timeboxing que finaliza la sesión tras 30 minutos de inactividad.
Depuración de Cookies: Durante el cierre de sesión, se eliminan los registros locales en el navegador para garantizar la máxima protección de los datos.

7. Archivo: modelos/Usuario.php
Su propósito fundamental es desempeñarse como el mediador técnico (Modelo de Datos) encargado exclusivamente de la comunicación con la tabla de usuarios en MySQL.

Bajo una Arquitectura Orientada a Objetos, se define una clase cuya responsabilidad única es la interacción con la información. Mediante el método __construct(), se inicializa el enlace al solicitar la instancia a la clase Conexion, garantizando que al instanciar new Usuario(), la conexión al motor de datos se establezca de forma automática.
Para garantizar la Protección contra Inyección SQL, el método buscarPorEmail implementa el uso de "Marcadores" (:email). Esta técnica evita la concatenación directa de variables en la instrucción SQL, mitigando uno de los vectores de ataque más críticos en el desarrollo web.
El flujo operativo de PDO (prepare -> execute -> fetch):
prepare(): Transmite la estructura de la consulta a MySQL para su compilación segura y preventiva.
execute(): Realiza la sustitución técnica del marcador por el valor real proporcionado por el usuario.
fetch(PDO::FETCH_ASSOC): Recupera la información obtenida y la transforma en un Arreglo Asociativo, facilitando su lectura mediante claves descriptivas como $usuario['contrasena_hash'].

8. Archivo: controladores/AuthControlador.php
Objetivo: Recibir el formulario de login, procesarlo de forma segura y decidir a qué pantalla redirigir al usuario.

El patrón Controlador: Es el director de orquesta. Primero atrapo los datos que el usuario escribió ($_POST). Luego, llamo al Modelo de Datos (buscarPorEmail) para pedirle información a la base de datos. Finalmente, si todo está correcto, llamo a mi Clase de Seguridad (Sesion::iniciarLogin) y le digo al navegador hacia dónde ir (header("Location: ...")).
$_SERVER['REQUEST_METHOD']: Lo usamos para distinguir si el usuario recién entró a la página por primera vez (método GET, entonces le mostramos el formulario), o si acaba de hacer clic en el botón "Ingresar" (método POST, entonces procesamos sus datos).
Función password_verify(): Una genialidad de PHP. Como en la base de datos guardamos las contraseñas encriptadas con BCRYPT (nunca se guardan en texto plano por seguridad), esta función compara mágicamente el texto que escribió el usuario (ej: "MiClave123") con el garabato inentendible guardado en la BD (ej: "$2y$10$e8wYVjFw6rNKGZqfD7h..."). Si el algoritmo matemático coincide, devuelve true.
Manejo de Errores Vía Interfaz: Si el email o la contraseña están mal, creamos una variable $error = "..." y cargamos nuevamente el HTML del login (require). El archivo HTML tomará esa variable y dibujará un cartelito rojo en la pantalla.

9. Archivo: publico/index.php (Front Controller)
Objetivo: Ser el único punto de entrada de toda la aplicación por motivos de seguridad y ruteo centralizado.

Seguridad Front Controller: Evita que los usuarios naveguen por carpetas internas (como /modelos/ o /controladores/). Todo el mundo entra por publico/index.php.
Carga Centralizada: Es el mejor lugar para importar la configuración global y las herramientas que usamos siempre (como la Base de Datos o la Sesión).
Ruteo Dinámico (Routing): En lugar de tener 50 archivos distintos (login.php, registro.php, perfil.php), usamos variables $_GET en la URL.
Ejemplo: ?c=auth&a=mostrarLogin significa Controlador = AuthControlador, Acción = mostrarLogin().
Clases Variables: PHP tiene un superpoder que nos permite instanciar clases usando textos dinámicos. Primero armamos el texto con el nombre de la clase ($claseCompleta = "App\Controladores\AuthControlador";) y luego creamos el objeto escribiendo new $claseCompleta();.

10. Archivo: controladores/AdminControlador.php
Su finalidad principal es salvaguardar el perímetro del área administrativa y organizar la información que se proyectará en el tablero de control (Dashboard).

Control de Acceso (__construct): Esta clase prioriza la ejecución de su constructor de forma inmediata. Al integrar Sesion::requerirLogin(), se establece una restricción técnica: si un usuario intenta vulnerar la ruta directa sin una autenticación previa, el sistema interrumpe el acceso y lo redirige al módulo de acceso.
El método dashboard(): Representa el núcleo operativo del controlador. En fases posteriores, se encargará de realizar las consultas SQL para cuantificar usuarios y torneos vigentes, suministrando estos indicadores a la interfaz dashboard.php.

11. Concepto: Adaptación de Vistas y Rutas Absolutas (URL_BASE)
Garantiza la correcta carga de activos digitales (estilos CSS, imágenes y scripts) con total independencia del controlador que invoque la interfaz visual.

Conflicto de Rutas Relativas (../): Bajo el patrón MVC, la interpretación del navegador se centraliza en publico/index.php, perdiendo la noción de la jerarquía física de carpetas que se utilizaba en maquetación estática.
La solución estratégica: Se implementa la constante URL_BASE para establecer una referencia absoluta. Esto obliga al sistema a localizar los recursos desde el directorio raíz del proyecto de forma unificada.
Arquitectura Limpia (CSS vs PHP): Se prohíbe el uso de estilos en línea para preservar la separación de responsabilidades. La lógica de presentación se confina a archivos .css, mientras que el .php se reserva exclusivamente para la lógica de datos.
12. Actualización: ayudantes/Sesion.php
Optimización del repositorio temporal del servidor para almacenar metadatos adicionales tras una validación de identidad exitosa.

Se identificó la necesidad técnica de personalizar la interfaz del Dashboard. Para ello, se expandió la captura de información durante el proceso de autenticación inicial.
Se incorporó la asignación de $_SESSION['usuario_nombre'] dentro de la lógica de iniciarLogin(), vinculándola directamente con los registros de la base de datos.
Gestión de Persistencia de Sesión: Cualquier modificación estructural en el manejo de sesiones requiere la invalidación de los tokens activos. Es imperativo ejecutar un logout para forzar una nueva carga de variables en la memoria de PHP.

13. Refactorización del Controlador (Clean Code) y Arreglos Asociativos
Objetivo: Mantener el AdminControlador limpio y estructurar los datos que se envían a la vista.

Funciones Privadas: En lugar de tener una sola función gigante que haga todo, creamos funciones de apoyo privadas (private function obtenerUsuarios()). Estas funciones solo pueden ser usadas por el propio controlador y ayudan a que el código sea modular, fácil de leer y de mantener.
El Arreglo $datos: En el patrón MVC, es una excelente práctica agrupar toda la información que el controlador extrajo de la base de datos dentro de un único arreglo (ej: $datos = ['totalUsuarios' => X, 'totalTorneos' => Y]). De esta forma, le entregamos a la Vista un solo "paquete" con todo lo que necesita imprimir.

14. Modelo: Torneo.php
Objetivo: Extender la capacidad de nuestro sistema para interactuar con la tabla de torneos en MySQL.

Estandarización: Al igual que el modelo Usuario, le aplicamos el patrón Singleton en el constructor (Conexion::getInstance()->getBD()) para asegurar el rendimiento.
Filtros SQL: Implementamos el método obtenerTodos() usando la instrucción COUNT(*) acompañada de una cláusula WHERE para contar exclusivamente los torneos que están en estado activo o de inscripciones abiertas, descartando los cancelados o finalizados.
15. Arquitectura de Vistas (Plantillas Anidadas)
Objetivo: Reemplazar el viejo enrutador falso de Javascript por inyección directa de PHP.

Separación Cascarón / Contenido: Mantenemos la estructura profesional de las maquetas donde dashboard.php funciona como el cascarón (contiene el <head>, los estilos, el menú lateral y el pie de página).
Inyección Dinámica (require_once): Dentro del cascarón, utilizamos PHP para inyectar la vista interna (inicio.php que contiene las tarjetas) usando require_once __DIR__ . '/inicio.php';. Esto garantiza que PHP arme el rompecabezas completo en el servidor antes de enviárselo al navegador, manteniendo la seguridad de la sesión intacta.


16. Integración de Tablas Relacionales (SQL Avanzado)
Objetivo: Obtener métricas complejas cruzando información de múltiples tablas en el Modelo Equipo.php.

Uso de LEFT JOIN y GROUP BY: Para poder contar cuántos miembros tiene un equipo, abandonamos las consultas simples de una sola tabla. Utilizamos un LEFT JOIN para fusionar la tabla equipos con equipo_miembros, y luego agrupamos (GROUP BY) los resultados por ID de equipo. Finalmente, la función COUNT(em.usuario_id) se encargó de calcular el total de integrantes por fila de manera dinámica y altamente eficiente.

17. Inyección de Datos Dinámicos en Vistas (Bucles foreach)
Objetivo: Reemplazar el código HTML estático de las tablas del Dashboard por filas generadas dinámicamente según los registros de la base de datos.

Procesamiento de Arreglos: El Controlador recupera los registros utilizando el método fetchAll(PDO::FETCH_ASSOC), obteniendo un arreglo multidimensional.
Renderizado con foreach: En la vista (inicio.php), se emplea la estructura de control foreach para iterar sobre el arreglo. Esta técnica instruye a PHP para generar una fila HTML (<tr>) por cada registro iterado.
Manejo de Estados Vacíos: Se implementa la validación empty() para asegurar que, ante la ausencia de registros en la base de datos, la interfaz mantenga su integridad estructural y presente un mensaje informativo al usuario.

18. Control de Acceso Basado en Roles (RBAC) y Seguridad
Objetivo: Restringir el acceso a rutas administrativas, garantizando que usuarios con privilegios inferiores (Ej: Jugadores) no puedan ingresar a módulos críticos.

Implementación en la Clase de Seguridad (Sesion.php): Se desarrolló el método estático validarAdmin(), el cual ejecuta una validación de autorización estricta. Se verifica que el rol_id en la variable superglobal $_SESSION corresponda al valor 1 (Administrador General). De no cumplirse esta condición, el sistema interrumpe la carga de la página mediante exit y redirige la petición hacia el módulo de acceso con un parámetro de denegación.
Aplicación en el Controlador (AdminControlador.php): El método constructor (__construct) fue actualizado para invocar Sesion::validarAdmin() previo a cualquier instanciación de modelos o renderizado de vistas, garantizando el blindaje automático de la clase.

19. Enrutamiento MVC desde la Interfaz de Usuario
Objetivo: Interconectar el menú lateral con el Front Controller (index.php) para habilitar la navegación dinámica entre módulos bajo el patrón MVC.

Refactorización de Enlaces: Se sustituyeron los anclajes HTML estáticos por rutas absolutas estructuradas (ej: href="<?php echo URL_BASE; ?>index.php?c=usuario&a=index"). Esta nomenclatura instruye al enrutador central para instanciar el Controlador pertinente y ejecutar la acción requerida.
Habilitación de Controladores: Se reacondicionó el archivo UsuarioControlador.php, implementando su respectivo constructor con validación de roles y un método index() como punto de entrada para la gestión de usuarios.

20. Arquitectura de Interfaz y Experiencia de Usuario (UX)
Objetivo: Alinear la estructura de navegación del Administrador General con los requisitos funcionales documentados en las especificaciones del proyecto.

Reestructuración Lógica del Menú: Se diseñó una nueva jerarquía de navegación basada en tres bloques funcionales:
1. Gestión Humana: Centraliza la administración de todas las entidades (Administrativos, Organizadores, Jugadores y Equipos).
2. Gestión de Competencias: Agrupa las facultades operativas de nivel superior (Catálogo de Juegos, Supervisión de Torneos, Registro Global de Resultados).
3. Configuración del Sistema: Aísla las funciones de mantenimiento y auditoría (Roles, Alertas Globales, Logs).
Justificación de Diseño: Esta categorización optimiza la usabilidad (UX) del software, disminuye la carga cognitiva durante la navegación y establece bases para una programación modular de futuros Controladores.

21. Patrón de Plantilla Maestra (Inyección Dinámica de Vistas)
Objetivo: Mitigar la duplicación de código HTML estructural (cabeceras, menús y pies de página) mediante el uso de un contenedor maestro reutilizable.

Resolución Arquitectónica: Se refactorizó el archivo dashboard.php para cargar vistas internas de forma dinámica a través de la variable $vistaInyectada (ej: require_once __DIR__ . '/' . $vistaInyectada;).
Responsabilidad del Controlador: El Controlador asume la responsabilidad de definir el valor de $vistaInyectada previo a la invocación de la plantilla. Esta modificación permite un ensamblaje dinámico y centralizado de las vistas en el servidor.

22. Pantalla de Gestión de Usuarios (usuarios_index.php)
Objetivo: Implementar una interfaz administrativa que exponga el directorio completo de cuentas registradas en el sistema.

Modelo de Datos (INNER JOIN): Se desarrolló el método obtenerTodosUsuariosConRoles() en la clase Usuario. La consulta implementa un INNER JOIN con la tabla roles, permitiendo vincular la llave foránea (rol_id) con la descripción nominal del rol, optimizando así la lectura de los datos.
Vista y Lógica Condicional: Se integró lógica de presentación dentro de las iteraciones de la tabla. Mediante estructuras condicionales en PHP, la interfaz renderiza componentes visuales diferenciados (etiquetas de "Activo" o "Bloqueado") basándose en el estado booleano almacenado en la base de datos.

23. Patrón de Diseño: Strategy (Arquitectura de Algoritmos)
Objetivo: Encapsular algoritmos complejos y variables (como la generación de fixtures y cruces) fuera de la clase principal, permitiendo su intercambio dinámico sin alterar el núcleo del sistema.

Planteamiento del Problema: Si la clase Torneo debiera gestionar internamente la generación de fixtures para modalidades de Liga, Eliminación Directa y Suizo, se generaría un bloque de código monolítico e insostenible (violación del principio Open/Closed de SOLID).
Solución Arquitectónica (Strategy): Se delegó la responsabilidad algorítmica a clases independientes (ModuloLiga, ModuloEliminacion, ModuloSuizo). La clase Torneo interactúa con estas clases como "estrategias" conectables. 
Beneficio Operativo: Alta escalabilidad. La integración de futuros formatos de competición (ej. Doble Eliminación o Fase de Grupos) solo requiere el desarrollo de una nueva clase módulo, preservando la integridad y estabilidad del código base.

24. Patrón de Diseño: Singleton (Gestión de Instancias)
Objetivo: Garantizar que durante todo el ciclo de ejecución de la aplicación, exista una única instancia global para la conexión a la base de datos, previniendo el agotamiento de recursos del servidor (Too Many Connections).

Implementación en Conexion.php: Se privatizó el método constructor (__construct()) para bloquear la instanciación externa (new Conexion()). 
Controlador de Instancia: Se centralizó el acceso a través del método estático getInstance(). Este método verifica la existencia previa de la conexión; si no existe, la crea, y si ya existe, retorna la referencia activa.
Beneficio Operativo: Optimización de recursos y mitigación del riesgo de colapso de MySQL ante solicitudes simultáneas concurrentes.


25. Centralizacion de Controladores y Redireccion Dinamica (Gestion de Usuarios)
Objetivo: Evitar la duplicacion de codigo en operaciones comunes (editar perfiles, cambiar estado) manteniendo interfaces especializadas por rol.

Patron de Controlador Multiproposito: Se refactorizo UsuarioControlador.php para actuar como manejador central de operaciones CRUD. Aunque las vistas y listados esten divididos por sub-controladores (JugadorControlador, OrganizadorControlador), todas las acciones de guardado y edicion convergen en un unico punto.
Redireccion Dinamica basada en Roles: Para mantener un flujo de UX transparente, el metodo actualizarUsuario() inspecciona la variable $datosActualizar['rol_id'] y utiliza esta informacion para redirigir (con header) al administrador de vuelta a la tabla especifica (Jugadores u Organizadores) desde la cual se origino la peticion.

26. Suplantacion de Identidad (Impersonation)
Objetivo: Otorgar al Administrador la capacidad temporal de operar bajo los permisos y la vista de otro usuario registrado para facilitar soporte tecnico y auditoria.

Gestion Avanzada de Sesiones: Se diseno el metodo verComo() dentro de UsuarioControlador. Este metodo respalda el ID original del administrador en $_SESSION['admin_antes_suplantar'] antes de sobreescribir las variables criticas de sesion con los datos del usuario objetivo.
Responsabilidad Arquitectonica: Al ubicar esta funcion en UsuarioControlador en lugar de un sub-controlador especifico, se respeta el Principio de Responsabilidad Unica (SRP), permitiendo su reutilizacion universal para cualquier rol del sistema sin generar acoplamiento.

27. Patron Flash Messages (Notificaciones Toast Hibridas)
Objetivo: Proveer retroalimentacion visual no bloqueante (Toast Notifications) al usuario tras completar operaciones de backend, prescindiendo de AJAX.

Interconexion PHP-Javascript: Se implemento un flujo hibrido. El Controlador define $_SESSION['mensaje'] y ordena una redireccion. La vista maestra (dashboard.php) actua como listener: si detecta la variable en memoria, inyecta dinamicamente un bloque <script> que dispara la funcion frontend mostrarNotificacionToast(), procediendo inmediatamente a destruir la variable (unset) para garantizar que el mensaje sea efimero (Flash Message).


28. Bajas Fisicas vs Logicas (Modulo Usuarios)
Objetivo: Completar el CRUD de usuarios con una eliminacion definitiva de la base de datos, distinguiendola de las suspensiones temporales.

Baja Logica (Bloquear): Implementada anteriormente cambiando el estado activo a 0. Preserva la integridad referencial y las estadisticas historicas (ej. torneos jugados).
Baja Fisica (Eliminar Registro): Se implemento el metodo eliminarUsuario() usando la instruccion DELETE FROM con parametros vinculados (PDO). Esta accion destructiva se reservo exclusivamente para el Rol 1 (Administrador Supremo) y se centralizo en UsuarioControlador.php para poder eliminar indistintamente a jugadores, organizadores o miembros del staff.

29. Módulo de Catálogo de Juegos (Disciplinas)
Objetivo: Permitir a los administradores gestionar la oferta deportiva y de eSports de la plataforma.

Arquitectura Base (CRUD Completo): Se construyó el modelo Juego.php y JuegoControlador.php implementando todas las operaciones CRUD (Crear, Leer, Actualizar, Borrar Lógicamente).
Valores por Defecto para Simplificación: En el método crearJuego() del modelo, se decidió fijar (quemar) los valores de los puntos (3 para victoria, 1 para empate, 0 para derrota) y el estado activo directamente en la consulta SQL. Esto redujo drásticamente la complejidad del formulario frontend (juegos_crear.php), mejorando la usabilidad al pedirle al usuario únicamente 3 datos clave: Nombre, Categoría y Formato de Equipo.
Baja Lógica (Switch de Estado): Se implementó el método cambiarEstado() utilizando un operador ternario para alternar entre 1 y 0. Esto permite "apagar" un juego para que no se puedan crear nuevos torneos con él, pero preservando el historial de torneos pasados (integridad de datos).
Edición Dinámica: Se desarrollaron los métodos obtenerJuegoPorId() y actualizarJuego(). En la vista de edición (juegos_editar.php), se utilizaron condicionales ternarios en línea (`$juego['categoria'] === 'esport' ? 'selected' : ''`) para auto-completar los selectores desplegables `<select>` con los datos previos del registro.

30. Módulo de Políticas de Seguridad y Contraseñas (politicas_contrasenas - ADM-07)
Objetivo: Permitir al Administrador General parametrizar dinámicamente las reglas de complejidad, expiración y reutilización de contraseñas para todos los usuarios del sistema.

Arquitectura MVC e Infraestructura:
- Modelo (PoliticaContrasena.php): Administra la lectura y persistencia de la fila activa (id = 1) en la tabla `politicas_contrasenas`. Implementa el método `obtenerPoliticaVigente()`, el cual auto-inicializa los valores por defecto si la tabla estuviera vacía.
- Controlador (PoliticaContrasenaControlador.php): Protegido con `Sesion::validarAdmin()`. La acción `guardar()` sanitiza los parámetros POST (longitud mínima, días de expiración, historial), actualiza el registro en la BD asociando el ID del administrador actualizador (`actualizado_por`), dispara la notificación Toast y registra la auditoría en `logs_actividad` mediante `Auditoria::registrar()`.
- Vista Administrable (politicas_contrasenas.php): Interfaz responsiva integrada al layout `dashboard.php` que expone campos numéricos, checkboxes de complejidad y metadatos de última modificación.

Funcionamiento y Lógica de Validación del Algoritmo (`validarPassword()`):
- Evaluación Independiente de Reglas: La regla de longitud mínima (`longitud_minima`) es totalmente independiente de los tipos de caracteres seleccionados (mayúsculas, minúsculas, números, caracteres especiales).
- Criterio de Presencia Mínima: Al activar los 4 requisitos de tipo de carácter con una longitud mínima de 10, el algoritmo NO exige una distribución equitativa o porcentual por cada tipo. Simplemente requiere que la contraseña cumpla con la longitud total global y que posea AL MENOS 1 carácter del tipo solicitado. Por ejemplo, una clave como `Aa1!aaaaaa` es 100% válida (cumple los 10 caracteres y contiene al menos 1 mayúscula, 1 minúscula, 1 número y 1 especial, siendo los caracteres restantes libres).
- Alineación con Estándares Internacionales (OWASP / NSI): Esta lógica independiente preserva la usabilidad del sistema evitando confusiones al usuario y promoviendo el uso de frases de contraseña largas (passphrases), altamente seguras por su longitud.

31. Refactorización Arquitectónica: Restauración de Sesiones y Clean Code (Impersonation)
Objetivo: Perfeccionar la función "Ver Como" asegurando una vía de retorno segura al rol de Administrador, sin violar principios arquitectónicos ni desencadenar bloqueos de seguridad por roles cruzados.

Inyección Global Limpia (Front Controller): En lugar de ensuciar el Front Controller (index.php) imprimiendo etiquetas <script> gigantes con toda la lógica del banner rojo flotante, se aplicó Clean Code. index.php se limitó a crear una variable global (window.URL_RESTAURAR_SESION) y cargar un archivo dedicado (publico/js/admin/suplantacion.js), preservando la separación de responsabilidades (Backend vs Frontend).
Resolución de Conflictos RBAC (Constructor vs Métodos): Al intentar volver a la sesión de administrador usando UsuarioControlador->restaurarSesion(), el sistema generaba un Error 404 encubierto. Esto ocurría porque el método __construct() ejecutaba Sesion::validarAdmin() globalmente. Como el usuario estaba temporalmente con rol de Jugador/Organizador, el constructor lo bloqueaba antes de ejecutar la acción. La solución arquitectónica consistió en retirar el guardia del constructor y colocar Sesion::validarAdmin() de forma individual en cada método sensible, dejando restaurarSesion() de acceso libre para quien posea el token de retorno.
Inicialización Explícita de Sesión: Al retirar el guardia global, PHP perdía la orden de arrancar la sesión (session_start()) para esa ruta específica. Se inyectó explícitamente Sesion::requerirLogin() al inicio de restaurarSesion() garantizando que la superglobal $_SESSION se construya en memoria y pueda validar correctamente $_SESSION['admin_antes_suplantar'] antes de devolver los privilegios.

32. Reestructuración de Base de Datos: Relaciones N:M (Juegos, Modalidades y Formatos)
Objetivo: Refactorizar el esquema de la base de datos (DDL) para soportar la creación dinámica de torneos, permitiendo que un mismo juego soporte múltiples configuraciones (Ej: LoL puede jugarse 5v5 o 1v1, y en formato Liga o Suizo).

Normalización de Modalidades: Se eliminó el campo restrictivo `formato_equipo_defecto` de la tabla `juegos`. En su lugar, se creó la tabla puente `juego_modalidades_permitidas`. Esto permite una relación de Muchos a Muchos (N:M), donde un juego puede tener habilitadas varias modalidades (1v1, 5v5), optimizando la integridad referencial.
Normalización de Formatos de Competición: Se creó la tabla `formatos_torneo` para independizar las estrategias de cruce (`liga`, `eliminacion_directa`, `suizo`). Para vincularlos, se generó la tabla intermedia `juego_formatos_permitidos`. Al crear un torneo, el Controlador ahora consulta estas tablas puente para renderizar dinámicamente las opciones válidas (JSON) según el juego seleccionado, previniendo que el Organizador elija combinaciones inválidas.

33. Asociación Polimórfica: Sistema de Guardados (Favoritos)
Objetivo: Diseñar un sistema universal de "Guardados" (Favoritos) para que el Jugador pueda almacenar tanto Torneos como Equipos que le resulten de interés, sin ensuciar la base de datos con tablas redundantes.

Diseño Polimórfico (DDL): Se creó una única tabla llamada `guardados`. En lugar de tener `torneos_guardados` y `equipos_guardados` por separado, se utilizó el patrón de Asociación Polimórfica mediante el campo `tipo` (restringido con un `CHECK (tipo IN ('torneo', 'equipo'))`) junto con un `referencia_id`. Esto permite que una sola fila referencie dinámicamente a cualquier entidad del sistema.
Restricciones de Integridad: Se aplicó un índice compuesto único (`UNIQUE KEY unique_guardado (usuario_id, tipo, referencia_id)`) para asegurar, a nivel de base de datos, que un usuario no pueda guardar la misma entidad dos veces, ahorrando validaciones costosas en PHP.

34. Trazabilidad de Acciones: Solicitudes de Equipo
Objetivo: Identificar con precisión el origen de las solicitudes de ingreso a un equipo para el módulo de Notificaciones y Solicitudes.

Actualización Estructural: Se añadió el campo `iniciado_por` (Foreign Key hacia `usuarios`) en la tabla `solicitudes_equipo`. Esta modificación estructural permite al sistema distinguir si una solicitud pendiente fue enviada proactivamente por un Jugador (queriendo entrar al equipo), o si fue enviada como una invitación por el Capitán del Equipo (queriendo reclutar al jugador), bifurcando así la lógica de respuesta (Aceptar/Rechazar) en el Controlador.

35. Arquitectura Multigaming e Inferencia de Datos (Directorio de Equipos)
Objetivo: Implementar un panel administrativo ("Directorio de Equipos") que permita listar todos los equipos del sistema, ordenarlos dinámicamente, y filtrarlos por disciplina (Juego) sin alterar el esquema relacional de la Base de Datos.

Manejo de Estructuras Multigaming: La tabla `equipos` fue diseñada para soportar organizaciones "Multigaming" (E-sports), por lo que no contiene la columna restrictiva `juego_id`. Un mismo equipo puede participar en torneos de distintos juegos simultáneamente. 
Inferencia de Filtros mediante JOINs: Para suplir el requerimiento administrativo de "Filtrar por Disciplina" sin romper el esquema Multigaming, el método `obtenerDirectorioAdmin()` en el modelo `Equipo.php` utiliza la técnica de "Inferencia por Participación". Mediante la cláusula `EXISTS`, el sistema busca si el equipo posee registros en la tabla `participantes_torneo` (cruzando con `torneos`) para la disciplina seleccionada. Esto preserva la normalización de la base de datos mientras cumple estrictamente los requisitos de interfaz de usuario.
Agregación de Miembros (Subqueries): En la misma consulta del Directorio, se incluyó una subconsulta `(SELECT COUNT(*) FROM equipo_miembros...)` como columna virtual para calcular en tiempo real el tamaño de cada equipo, evitando redundancia de datos (desnormalización de contadores).

36. Autogestión de Perfil Administrativo (Seguridad en Interfaz)
Objetivo: Habilitar la edición de datos básicos (Nombre y Teléfono) del Administrador General, blindando metadatos sensibles contra modificaciones accidentales o maliciosas.

Lógica de Exclusión Visual y Backend: En la vista de edición (`editar_perfil.php`), los campos críticos como `Correo Electrónico` (credencial de login) y `Rol` (Nivel de Permisos) se renderizan con el atributo `disabled` y estilos visuales de solo lectura. En el Backend, el método `actualizarPerfil()` del modelo `Usuario.php` está rigurosamente escrito para ejecutar el comando `UPDATE` afectando exclusivamente a las columnas `nombre_completo` y `telefono`, imposibilitando la escalada de privilegios o el secuestro de la cuenta (Account Takeover) a través del envío de peticiones POST manipuladas.
Actualización de Sesión en Tiempo Real: Tras guardar los cambios exitosamente en la BD, el controlador sobreescribe la variable `$_SESSION['usuario_nombre']` para que la cabecera responsiva de navegación refleje el nuevo nombre instantáneamente sin requerir que el administrador vuelva a iniciar sesión.

37. Patrón "Read-Only Dashboard": Historial de Torneos
Objetivo: Exhibir un registro auditable de los ciclos competitivos concluidos sin comprometer la base de datos de producción.

Filtro de Ciclo de Vida: El modelo `Torneo.php` fue dotado del método `obtenerHistorialTorneos()` el cual filtra drásticamente los registros con la cláusula `WHERE estado IN ('finalizado', 'cancelado')`. Esto garantiza que los torneos activos nunca se crucen con los registros archivados, protegiendo la integridad de la interfaz administrativa de gestión viva.

38. Integridad Analítica: Salón de la Fama Dinámico (Resultados Globales)
Objetivo: Proveer un Ranking (Top 10) de los mejores jugadores y equipos históricos, resolviendo el desafío arquitectónico que supone cruzar puntuaciones de juegos con sistemas de medición drásticamente distintos (ej. Goles vs Rondas).

Descarte del Modelo "One-Size-Fits-All": Originalmente se pensó en un único Top 10 global. Se detectó que esto corrompería las métricas al sumar puntos de sistemas heterogéneos. 
Solución Dinámica Iterativa: En el `ResultadoGlobalControlador.php`, se programó un enfoque dinámico. Primero se invoca `obtenerJuegosConResultados()` para determinar qué disciplinas (juegos) tienen competencias finalizadas. Posteriormente, a través de un bucle `foreach`, se consulta individualmente el Top 10 específico por cada juego (`obtenerTop10PorJuego($juegoId)`). La vista (`globales.php`) recibe este arreglo multidimensional e itera renderizando una tabla independiente para cada Salón de la Fama, preservando la integridad competitiva al 100%.

39. Pureza Arquitectónica (MVC) y Desacoplamiento de UI: Data Attributes vs Inline Scripts
Objetivo: Erradicar la mezcla de lógica de servidor (PHP) con interactividad de cliente (JS) en las vistas de presentación, purificando la adhesión al patrón Modelo-Vista-Controlador.

Refactorización de Flash Messages: Originalmente, las notificaciones emergentes (Toasts) se disparaban imprimiendo una etiqueta `<script>` vía PHP al pie de `dashboard.php`. Esto rompía la responsabilidad del Controlador y ensuciaba el DOM.
Implementación de Data-Attributes: La vista maestra `dashboard.php` fue sanitizada. Ahora, PHP únicamente inyecta silenciosamente los metadatos en la etiqueta Body: `<body data-flash-mensaje="..." data-flash-tipo="...">`.
Escucha Centralizada: El archivo estático `admin.js` fue dotado de un "Event Listener" global que se ejecuta tras el `DOMContentLoaded`. Este algoritmo lee nativamente el DOM y dispara las notificaciones Toast de forma autónoma. Con esta refactorización, se logra un 100% de aislamiento técnico entre PHP y JS para la lógica de negocios.

40. Auditoría Avanzada y Reforzamiento de Seguridad Perimetral (Fase 3)
Objetivo: Blindar la aplicación a nivel corporativo mitigando vulnerabilidades críticas detectadas (OWASP Top 10) relativas a sesiones, inyección cruzada y fuerza bruta.

Seguridad de Sesiones (Timeboxing y Flags): Se actualizó la clase `Sesion.php` inyectando `session_set_cookie_params()` antes del inicio de sesión. Esto asegura que la cookie de identidad viaje protegida con los flags `HttpOnly` (inaccesible para JavaScript, previniendo secuestros XSS) y `SameSite=Lax`. Adicionalmente, se programó un motor de validación estricta de caducidad (Timeboxing) que expulsa al usuario del servidor si `$_SESSION['expiracion_sesion']` es superado, destruyendo el mito de las sesiones "infinitas".
Políticas HTTP de Seguridad (Security Headers): Se creó el módulo núcleo `CabecerasSeguridad.php` y se inyectó en el Front Controller (`index.php`). Desde este momento, cualquier respuesta enviada al navegador incluye directivas estrictas (`X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`), protegiendo a la plataforma contra Clickjacking y Sniffing de manera nativa.
Protección de Fuerza Bruta (Rate Limiting Profesional): Se desestimó la protección endeble basada en sesiones a favor de un bloqueo riguroso por Base de Datos. Se estructuró la tabla `intentos_login` administrada por el nuevo modelo `SeguridadAuth.php`. El Controlador de Autenticación ahora interroga a este modelo; si una IP registra 5 fallos consecutivos, es bloqueada automáticamente a nivel lógico por 5 minutos, garantizando la integridad de los accesos.
Inmunidad Transaccional (Tokens CSRF): Se implementó el patrón *Synchronizer Token* en `Sesion.php`. Las áreas sensibles de Autenticación y el panel del Administrador General exigen ahora que los formularios envíen un `csrf_token` oculto validado por el servidor, haciendo matemáticamente imposible la falsificación de peticiones en nombre del usuario legítimo.
 
41. Implementacion detallada de proteccion CSRF en formularios de jugador
Objetivo: Evitar que un sitio externo pueda enviar acciones POST en nombre de un usuario que ya tiene una sesion abierta en SGDM.

Patron utilizado: Se aplico el patron Synchronizer Token. El servidor genera un valor aleatorio por sesion y la vista lo incluye como campo oculto dentro de cada formulario POST:

~~~php
<input type="hidden"
       name="csrf_token"
       value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
~~~

Flujo del token:

1. `Sesion::generarTokenCSRF()` inicia la sesion y, si no existe un token, genera 32 bytes aleatorios con `random_bytes(32)`. El resultado se representa en hexadecimal mediante `bin2hex()` y se guarda en `$_SESSION['csrf_token']`.
2. La vista imprime el token en un campo oculto. El usuario no lo ve, pero el navegador lo envia con el formulario.
3. El controlador recibe el valor en `$_POST['csrf_token']`.
4. `Sesion::requerirTokensCSRF()` llama a `validarTokenCSRF()` y compara el token recibido con el token de la sesion mediante `hash_equals()`.
5. Si la comparacion falla, se responde con HTTP 419 y se detiene la accion antes de leer o modificar datos.

Decisiones de implementacion:

- El campo usa el nombre fijo `csrf_token` para que todas las vistas y controladores compartan el mismo contrato.
- Se usa `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` al imprimir el valor dentro de HTML.
- Cada formulario POST tiene su propio campo porque el navegador solo envia los controles que estan dentro del formulario enviado.
- La proteccion se coloca despues de comprobar que el metodo HTTP es POST y antes de procesar los datos recibidos.
- CSRF no sustituye la autenticacion ni la autorizacion. El controlador sigue validando que el usuario este logueado y tenga permisos.

Aplicacion en equipos:

- `crear-equipo.php`: formulario de creacion de equipo.
- `gestionar-equipo.php`: actualizacion, designacion de capitan, expulsion, solicitudes y salida del equipo.
- `EquipoControlador.php`: valida el token con `Sesion::requerirTokensCSRF()` en cada accion POST sensible.

Justificacion para la defensa: El token vincula el formulario con la sesion legitima. Un atacante puede intentar provocar una peticion desde otro sitio, pero no puede leer el token aleatorio de la sesion. Por eso la peticion falsa es rechazada antes de ejecutar cambios.
 
42. Proteccion CSRF en acciones del panel del organizador
Objetivo: Asegurar que las acciones POST que modifican torneos solo se ejecuten mediante formularios legitimos de la sesion activa.

En `PanelOrganizadorControlador` se distinguieron dos casos:

- `guardarPerfil()`, `guardarGestion()` y `guardarEtapa()` ya comprobaban que el metodo HTTP fuera POST. Se mantuvo esa validacion y se dejo `Sesion::requerirTokensCSRF()` inmediatamente despues.
- `cambiarEstado()`, `responderInscripcion()`, `generarFixture()` y `guardarResultado()` no comprobaban el metodo HTTP. Se agrego primero una respuesta HTTP 405 para cualquier metodo distinto de POST y luego la validacion CSRF.

El orden aplicado es intencional:

~~~php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Metodo no permitido');
}

Sesion::requerirTokensCSRF();

// ReciÃ©n despues se leen $_POST y se llama al modelo.
~~~

Esto evita procesar datos enviados por GET y evita procesar datos POST sin un
token asociado a la sesion. La proteccion no reemplaza `Sesion::validarOrganizador`
o `Sesion::validarOrganizadorVerificado()`: esas validaciones siguen determinando
quien esta autenticado y autorizado; el CSRF verifica que la peticion provenga
de un formulario generado para esa sesion.

Se verifico la sintaxis de `panelOrganizadorControlador.php` con `php -l` y no se
detectaron errores.

Justificacion para la defensa: Las acciones de cambiar estados, responder
inscripciones, generar fixtures y guardar resultados modifican informacion del
torneo. Por eso no deben aceptar GET y deben exigir el token CSRF antes de
llamar al modelo o modificar la base de datos.
 
43. CSRF en la vista de gestion de torneos
Objetivo: Alinear todos los formularios POST de gestion de torneos con las validaciones del controlador.

La vista gestionar-torneo.php contiene seis formularios POST:

- Guardar la configuracion general del torneo.
- Cambiar el estado del torneo.
- Aprobar una inscripcion.
- Rechazar una inscripcion.
- Generar o regenerar el fixture.
- Guardar el resultado de un partido.

Cada formulario incluye su propio campo oculto csrf_token. Los formularios de
aprobacion y rechazo se generan dentro de un bucle, pero cada boton envia un
formulario diferente; por eso cada formulario recibe un token independiente.
El formulario de resultado tambien puede repetirse por cada partido y debe
mantener el token dentro de cada instancia.

La vista y el controlador deben estar alineados:

1. La vista envia csrf_token dentro del formulario.
2. El controlador rechaza metodos diferentes de POST.
3. El controlador valida el token mediante Sesion::requerirTokensCSRF().
4. Solo despues se leen los datos y se ejecuta la operacion del modelo.

Justificacion para la defensa: Se protege cada accion que cambia el torneo,
incluyendo acciones repetidas dentro de bucles. Asi no queda un formulario
administrativo sin la misma garantia de autenticidad de la sesion.
 
44. Acciones administrativas de organizadores: migracion de GET a POST
Objetivo: Evitar que aprobar, rechazar o revocar la habilitacion de un organizador pueda ejecutarse mediante un enlace GET.

Antes del cambio, la vista organizador_index.php utilizaba enlaces con id y
decision en la URL. El controlador leia esos valores desde $_GET y ejecutaba
una modificacion de estado. Se reemplazaron esas acciones por formularios POST.

El controlador OrganizadorControlador ahora:

1. Rechaza metodos distintos de POST con HTTP 405.
2. Valida el token de la sesion mediante Sesion::requerirTokensCSRF().
3. Lee id y decision desde $_POST.
4. Acepta unicamente las decisiones aprobar, rechazar y revocar.
5. Ejecuta el modelo y redirecciona al listado.

La vista genera un formulario independiente para cada accion y envia tres
valores: csrf_token, id y decision. El boton visible ya no es un enlace que
modifica datos, sino un submit POST.

Justificacion para la defensa: Aprobar o revocar un organizador cambia sus
permisos dentro del sistema. Las acciones con efectos secundarios no deben
ejecutarse por GET, porque un navegador puede disparar un enlace sin una
confirmacion explicita. POST mas CSRF obliga a enviar una peticion intencional
y vinculada a la sesion del administrador.

Las acciones verComo, editarUsuario y cambiarEstadoUsuario quedan fuera de este
cambio y deben revisarse en sus respectivos controladores.
 
45. Proteccion de la eliminacion de usuarios
Objetivo: Evitar que la baja fisica de un usuario pueda ejecutarse mediante un
enlace GET o una peticion falsificada.

Antes, eliminarUsuario() leia id y rol_id desde $_GET. Esto significaba que una
URL podia provocar una accion destructiva si el administrador tenia una sesion
abierta. La vista jugadores_index.php usaba un enlace para eliminar la cuenta.

El cambio aplicado conserva la logica del modelo, pero cambia el contrato HTTP:

1. Solo se acepta el metodo POST.
2. Se exige una sesion de administrador.
3. Se valida el token CSRF.
4. id y rol_id se leen desde $_POST.
5. El modelo recibe el mismo id que antes.

La vista ahora utiliza un formulario POST con un boton y una confirmacion
JavaScript. El formulario envia csrf_token, id y rol_id. rol_id no identifica
la seguridad de la operacion; se conserva para que el controlador redirija al
listado de jugadores u organizadores correspondiente despues de la operacion.

La confirmacion visual no reemplaza las validaciones del servidor. La proteccion
real esta en la combinacion de autorizacion de administrador, metodo POST y
token CSRF.

No se elimino ningun usuario durante este cambio. Se validaron la sintaxis del
controlador y de la vista con php -l.
 
46. Entregables DevOps para despliegue y defensa
Objetivo: Preparar un procedimiento repetible para levantar ASCEND en una VM
Linux dentro de Proxmox, operar Docker, conservar logs y recuperar la base.

Se agregaron tres documentos operativos:

- Guia_Despliegue_10_Minutos.md: secuencia rapida de preparacion, copia,
  validacion, build, arranque y prueba HTTP.
- Guia_DevOps_Servidor_Linux.md: responsabilidades del operador, usuarios,
  permisos, firewall, hora, actualizaciones y monitoreo.
- Guia_Backup_Restauracion_Logs.md: backup MySQL, checksum, retencion,
  restauracion controlada, logs y hashes de archivos PHP.

Los comandos destructivos se separaron de los comandos de operacion diaria. La
restauracion y la eliminacion de volumenes exigen una decision explicita, un
backup previo y una verificacion del objetivo.

Durante la validacion se detecto que el Compose actual no pasa la comprobacion
porque la condicion de depends_on no esta expresada como un mapeo YAML. Este
control se mantiene en la guia de despliegue para que la presentacion no
continue con una configuracion invalida.
 
47. Auditoria real de conectividad y despliegue dual
Objetivo: comprobar que ASCEND funciona tanto en XAMPP como en Docker y separar
los errores de conexion de los problemas de esquema.

Pruebas realizadas:

- XAMPP MySQL responde con root y la base ascend existe.
- XAMPP responde HTTP 200 en la URL local.
- Docker Compose valida su configuracion.
- sgdm_db queda en estado healthy.
- El usuario de aplicacion ascend_usuario@% conecta con permisos CRUD limitados.
- sgdm_web responde HTTP 200 en el puerto 8080.
- phpMyAdmin y Grafana responden en sus puertos locales.
- Todos los archivos PHP pasan php -l.

Correcciones de conexion:

1. La configuracion PHP ahora lee DB_PASSWORD desde variables de entorno.
   En XAMPP el fallback sigue siendo contraseña vacia; en Docker se usa
   ascend_password.
2. Se corrigio depends_on para que web espere a que db este saludable.
3. Se corrigio el healthcheck para expandir MYSQL_ROOT_PASSWORD dentro del
   shell del contenedor.
4. Se creo el usuario Docker de aplicacion que faltaba en el volumen existente,
   sin modificar tablas ni datos.

Hallazgo de integracion:

XAMPP contiene 39 tablas, mientras que Docker contiene 26 y no tiene
politicas_contrasenas. Por lo tanto, HTTP 200 y una conexion exitosa no
significan que los dos entornos tengan el mismo esquema. La sincronizacion del
esquema queda como una tarea separada y no debe resolverse ejecutando una
migracion destructiva sobre datos existentes.

La comparacion de nombres identifico 13 tablas presentes en XAMPP y ausentes en
Docker: auditoria_cambios, historial_contrasenas, logros, logs_actividad,
newsletter_subscriptores, notificaciones, politicas_contrasenas,
resultados_detalle, torneo_actividades, torneo_comentarios, torneo_config,
torneo_suizo_parejas y usuario_logros.

48. Reinicializacion reproducible de la base Docker

Para eliminar la desincronizacion se genero primero un respaldo del volumen
Docker en `Documentos_Juan/backups_docker_reset/ascend_docker_antes_reset.sql`.
Su SHA-256 es `BDD805A84E7D0D20B210BF5A766EA4821B08C86B2EAD912AE23DDCD41D73DAAC`.
Luego se detuvo el Compose y se elimino exclusivamente el volumen
`docker_sgdm_datos`. La base de XAMPP no fue modificada.

El primer arranque desde cero revelo una incompatibilidad real de MySQL 8 en
`base_de_datos/migraciones/001_ascend.sql`: el `CHECK distintos` de
`torneo_suizo_parejas` usa columnas que tambien poseen claves foraneas con
acciones referenciales. MySQL rechazo la tabla completa. Se corrigio el
script de migracion retirando solamente ese `CHECK`; la validacion equivalente
queda en el flujo de creacion de parejas del modelo `Torneo`.

Tras corregir la migracion, el segundo arranque ejecuto 001 y 002 correctamente.
Docker quedo con 39 tablas, igual que XAMPP, `politicas_contrasenas` presente,
usuario de aplicacion funcional y todos los servicios principales activos.
La portada de Docker y XAMPP respondieron HTTP 200. Docker contiene usuarios
de ejemplo por la migracion 002, mientras XAMPP no; esa diferencia es de datos
iniciales y no de estructura.

49. Consistencia de Creación de Torneos y Disciplinas Deportivas (Reglas de Negocio en Runtime)
Objetivo: Garantizar que las restricciones configuradas por el Administrador General para cada disciplina (formatos permitidos, modalidades habilitadas y sistema de puntuación) se respeten estrictamente en la interfaz de usuario, en el controlador del organizador, en los modelos y en el motor de base de datos.

Hallazgos de Inconsistencia Detectados:
1. En el asistente de creación de torneos (etapa 1 y etapa 2), el formulario permitía elegir cualquier formato (Liga, Suizo, Eliminación Directa) o modalidad (Individual, Equipos) sin respetar las restricciones del catálogo de disciplinas (por ejemplo, Ajedrez estaba configurado exclusivamente para modalidad Individual y formato Suizo con puntuación fija, pero el formulario permitía crearlo como Liga o con modalidad por Equipos).
2. Se permitía seleccionar "Puntuación Personalizada" en disciplinas que poseían puntuación fija reglamentaria predeterminada por la administración (Ajedrez con 1.00 por victoria, 0.50 por empate y 0.00 por derrota).
3. El organizador podía manipular los valores por POST evadiendo la interfaz sin que el backend validara la coherencia contra las tablas relacionales de la disciplina.

Solución Aplicada:
- Frontend (vistas/organizador/crear-torneo.php): Se inyectaron metadatos en data-attributes en el selector de disciplinas (formatos permitidos, modalidades permitidas, puntos fijos de victoria, empate y derrota). Se desarrolló un script reactivo que sincroniza dinámicamente las opciones disponibles: deshabilita y oculta modalidades/formatos incompatibles, preselecciona las válidas y bloquea la personalización de puntos cuando la disciplina cuenta con valores oficiales definidos.
- Backend (controladores/PanelOrganizadorControlador.php y FormatoControlador.php): Se incorporó validación estricta en guardarEtapa() comprobando la disciplina contra las tablas intermedias juego_modalidades_permitidas y juego_formatos_permitidos.
- Triggers de Base de Datos (migraciones/007_restricciones_disciplinas.sql): Se crearon los triggers trg_torneo_validar_disciplina_ins y trg_torneo_validar_disciplina_upd, que operan como barrera de integridad física rechazando inserciones directas con SIGNAL SQLSTATE '45000' ante cualquier intento de registrar un torneo con combinaciones incompatibles.

50. Fase 1 de Remediación: Integridad de Equipos y Disciplinas Colectivas
Objetivo: Restringir la creación de equipos exclusivamente a disciplinas colectivas e impedir que jugadores participen en múltiples equipos rivales dentro de un mismo torneo.

Hallazgos Detectados:
1. Cualquier jugador podía registrar un equipo deportivo asociado a disciplinas individuales (como Ajedrez o Tenis de Mesa individual).
2. No existía validación para impedir que un jugador integrara simultáneamente la plantilla de dos equipos rivales inscriptos en el mismo torneo, abriendo la puerta a que un competidor disputara partidos contra sí mismo o adulterara resultados.
3. Se permitía la postulación de equipos que no alcanzaban la nómina mínima de integrantes activos.

Solución Aplicada:
- Modelo Juego.php: Se incorporaron los métodos obtenerJuegosParaEquipos() (filtrando exclusivamente registros con modalidad_id = 2) y permiteEquipos(int $juegoId): bool.
- Controlador EquipoControlador.php: Se actualizaron crear() y procesarCreacion() restringiendo el catálogo desplegable y validando en el servidor que la disciplina seleccionada acepte equipos.
- Vista crear-equipo.php: Se clarificó la etiqueta y selector hacia "Disciplina colectiva de competencia".
- Modelo Torneo.php: Se desarrolló el método obtenerMiembrosEnConflicto(int $torneoId, int $equipoId): array, que cruza en tiempo real equipo_miembros con participantes_torneo en estado confirmado o pendiente para detectar jugadores compartidos.
- Controlador TorneoControlador.php: En la acción inscribirse(), se validó la inexistencia de jugadores en conflicto y se exigió una nómina mínima de al menos 2 integrantes activos.

51. Fase 2 de Remediación: Ciclo de Vida de Inscripciones (Reintento, Retiro y Limpieza)
Objetivo: Garantizar un flujo consistente en el ciclo de vida de inscripciones, habilitando la re-postulación tras rechazo o cancelación, permitiendo el retiro voluntario del jugador y saneando solicitudes huérfanas al iniciar el torneo.

Hallazgos Detectados:
1. Al rechazar o cancelar una inscripción, la restricción de unicidad UNIQUE KEY unico_participante (torneo_id, tipo, referencia_id) impedía que el usuario o equipo volviera a enviar una solicitud, arrojando un error de clave duplicada (SQLSTATE 23000).
2. La vista de detalle del torneo bloqueaba indefinidamente los botones de inscripción si existía un registro histórico en estado rechazado o cancelado.
3. No existía opción para que un jugador o capitán retirara una solicitud que permaneciera en estado pendiente de aprobación.
4. Al pasar el torneo a estado en_curso, las solicitudes que el organizador no había respondido quedaban permanentemente en estado pendiente, ensuciando los listados y métricas.

Solución Aplicada:
- Modelo Torneo.php: Se reformuló inscribirse() utilizando la cláusula ON DUPLICATE KEY UPDATE, reactivando registros previos en 'rechazado' o 'cancelado' hacia 'pendiente' y renovando la marca temporal.
- Modelo Torneo.php: Se parametrizó obtenerInscripcionDelJugador() con el argumento $soloActivas = true por defecto, separando las solicitudes activas de las históricas para no bloquear la interfaz.
- Modelo Torneo.php: Se implementó cancelarInscripcionParticipante(int $torneoId, string $tipo, int $referenciaId): bool para cancelar postulaciones pendientes mientras las inscripciones sigan abiertas.
- Modelo Torneo.php: En cambiarEstado(), al transicionar el torneo a 'en_curso', se ejecuta de forma atómica y reentrante un UPDATE que descarta automáticamente todas las solicitudes pendientes pasándolas a 'rechazado'.
- Controlador TorneoControlador.php y Vista detalle-torneo.php: Se habilitó la acción cancelarInscripcion() con validación de rol, token CSRF y capitanía. En la vista se integró el botón "Retirar solicitud" y se añadió un aviso contextual que mantiene disponible el formulario de inscripción si hubo un rechazo o cancelación previa.

52. Fase 3 de Remediación: Calidad de Datos, Récord Deportivo y Seguridad Perimetral
Objetivo: Asegurar la veracidad estadística deportiva, erradicar valores fijos hardcodeados y proteger la aplicación contra vulnerabilidades de redirección abierta.

Hallazgos Detectados:
1. El cálculo del récord del jugador en el panel personal sumaba únicamente victorias y derrotas, ignorando empates en deportes que contemplan tablas o paridad reglamentaria.
2. El perfil público de equipo (perfil-equipo.php) presentaba métricas hardcodeadas en HTML estático (38 Partidas, 24 Victorias, 3 Torneos ganados).
3. El controlador GuardadosControlador.php procesaba redirecciones directas desde el parámetro volver_a sin validar el destino, exponiendo la aplicación a ataques de Open Redirect (CWE-601).

Solución Aplicada:
- Modelo Torneo.php: Se refactorizó obtenerRecordDelJugador() para computar empates reales analizando encuentros finalizados con participante_ganador_id IS NULL.
- Vista dashboard.php: Se adaptaron las métricas del jugador hacia el formato deportivo estándar Récord (V - E - D) y se corrigió el porcentaje de efectividad.
- Modelo Equipo.php y Controlador EquipoControlador.php: Se desarrolló obtenerEstadisticasTorneos(int $equipoId): array, calculando dinámicamente desde las tablas torneo_encuentros, participantes_torneo, torneo_posiciones y torneo_config las partidas jugadas, victorias, empates, derrotas y torneos ganados. En la vista se reemplazaron los valores estáticos por datos reales de la base de datos.
- Controlador GuardadosControlador.php: Se implementó el método sanitizarVolverA(?string $url): string, neutralizando URLs externas y protocol-relative (//evil.com), restringiendo los saltos a rutas internas legítimas.
- Validación Integral: Se ejecutó la suite de pruebas tests/test_fases_remediacion.php, verificando el 100% de aprobación en tiempo de ejecución.

53. Fase A de Remediación: Integridad de Fixture y Rectificación de Marcadores en Runtime
Objetivo: Prevenir encuentros huérfanos ("partidos fantasma") al modificar participantes confirmados, permitir la rectificación segura de marcadores por parte del organizador ante errores tipográficos y garantizar la consistencia en tablas de posiciones y cuadros eliminatorios.

Hallazgos de Inconsistencia Detectados:
1. Fixtures Huérfanos por Cancelación de Participante: Si el organizador pre-generaba el fixture de un torneo con inscripciones abiertas y posteriormente cancelaba o eliminaba a un participante confirmado mediante quitarParticipante(), el fixture previo permanecía intacto en la base de datos con partidos asignados a un competidor en estado 'cancelado'. Esto provocaba partidos fantasmas irresolubles al iniciar el torneo.
2. Ausencia de Rectificación de Marcadores: Una vez cargado el resultado de un partido mediante guardarResultado(), el encuentro pasaba irreversiblemente a estado 'finalizado'. No existía ningún endpoint, método de modelo ni interfaz visual que permitiera al organizador corregir un marcador cargado por error sin intervenir manualmente la base de datos.
3. Desincronización y Ruptura de Llaves Eliminatorias: Si un marcador se alteraba indebidamente en eliminación directa cuando ya se habían generado rondas posteriores, la llave quedaba corrupta (el ganador nuevo no se propagaba a la final, o peor aún, se permitía alterar el ganador de semifinales cuando la final ya había sido disputada por el ganador original). Asimismo, en torneos de liga y suizo, no existía recálculo dinámico de posiciones ante cambios de marcadores.

Primera Solución Intentada y Errores Encontrados en Tiempo de Ejecución:
- Error 1: Transacciones PDO Anidadas (PDOException: There is already an active transaction).
  - Lo que se intentó hacer: En los métodos generarFixture(), guardarResultado(), corregirResultado() y quitarParticipante() se abría incondicionalmente una transacción con $this->bd->beginTransaction() y se cerraba con $this->bd->commit().
  - Por qué falló: Cuando estos métodos eran invocados desde un contexto donde ya existía una transacción activa (por ejemplo, suites de pruebas unitarias o transacciones compuestas de negocio), MySQL y PDO arrojaron excepciones fatales debido a que PDO no admite beginTransaction() anidados. Además, en los bloques catch, ejecutar $this->bd->rollBack() abortaba de forma imprevista la transacción externa superior.
  - Solución definitiva aplicada: Se refactorizaron todos los métodos transaccionales del modelo implementando el patrón de transacciones reentrantes:
    ```php
    $transaccionPropia = false;
    if (!$this->bd->inTransaction()) {
        $this->bd->beginTransaction();
        $transaccionPropia = true;
    }
    // ... operaciones ...
    if ($transaccionPropia && $this->bd->inTransaction()) {
        $this->bd->commit();
    }
    // en catch:
    if ($transaccionPropia && $this->bd->inTransaction()) {
        $this->bd->rollBack();
    }
    ```
- Error 2: Reutilización de placeholders con el mismo nombre en PDO (SQLSTATE[HY093]: Invalid parameter number).
  - Lo que se intentó hacer: En la consulta $stmtPost de corregirResultado() se evaluaba si el participante anterior había jugado rondas posteriores mediante:
    `WHERE (participante_local_id = :ganador_anterior OR participante_visitante_id = :ganador_anterior)`
    pasando únicamente un elemento [':ganador_anterior' => $ganadorAnterior] en el array asociativo de execute().
  - Por qué falló: Al operar con PDO sobre MySQL con emulación de consultas deshabilitada (modo nativo seguro), la reutilización del mismo nombre de parámetro sin correspondencia 1 a 1 de argumentos generó el fallo `SQLSTATE[HY093]: Invalid parameter number`.
  - Solución definitiva aplicada: Se asignaron identificadores únicos e independientes a cada marcador (:ganador_ant_loc y :ganador_ant_vis) y se vincularon explícitamente en el execute(), garantizando compatibilidad total con cualquier configuración de PDO.

Solución Final Aplicada y Arquitectura de la Solución:
1. Invalidación Automática de Fixtures Obsoletos (modelos/Torneo.php - quitarParticipante()):
   - Al remover a un participante confirmado mientras las inscripciones están abiertas, el método consulta si ya existen registros en `torneo_encuentros`.
   - Si existía fixture previo, se eliminan atómicamente los registros de `torneo_encuentros`, `torneo_suizo_parejas` y `torneo_posiciones`, retornando `['ok' => true, 'fixture_invalidado' => true, 'mensaje' => 'Participante eliminado. El fixture previo fue invalidado...']`.
   - En el controlador `PanelOrganizadorControlador.php`, la acción `quitarParticipante()` captura el mensaje y alerta al organizador que debe regenerar el fixture con la nueva nómina.
2. Mecanismo de Rectificación de Resultados (modelos/Torneo.php - corregirResultado()):
   - Permite al organizador rectificar marcadores de partidos en estado 'finalizado' pertenecientes a torneos en estado 'en_curso'.
   - Para torneos de Liga y Suizo: Recalcula de forma transparente la tabla completa de posiciones llamando a `recalcularPosiciones($torneoId)`.
   - Para torneos de Eliminación Directa:
     - Evalúa si el nuevo marcador altera la identidad del ganador.
     - Si el ganador cambia, verifica mediante `$stmtPost` si el ganador anterior ya disputó partidos en rondas posteriores (ronda > ronda_actual y estado = 'finalizado'). Si ya disputó partidos posteriores, **bloquea la corrección** arrojando una excepción informativa para proteger la integridad histórica de la competencia.
     - Si la ronda siguiente está programada pero no se ha disputado, actualiza automáticamente el cupo en el partido de la ronda siguiente (`ronda + 1`), propagando al nuevo competidor sin romper la llave.
3. Controlador y Ruteo Seguro:
   - En `controladores/PanelOrganizadorControlador.php` se implementó la acción `corregirResultado()` con verificación de sesión, rol de organizador, validación de token CSRF, casteo y sanitización estricta de marcadores flotantes (`floatval`).
   - Se registró `'corregirResultado'` en la lista blanca de métodos del enrutador en `publico/index.php`.
4. Interfaz de Usuario y UX del Organizador (vistas/organizador/administrar-torneo.php):
   - En cada tarjeta de encuentro finalizado, cuando el torneo está en curso, se añadió el botón "Corregir marcador" estilizado con icono `fa-pen-to-square`.
   - Se diseñó un modal emergente accesible con el formulario de corrección que precarga el marcador actual y envía la rectificación vía POST con su token CSRF.
5. Validación Automatizada (tests/test_fase_a_fixtures.php):
   - Se implementó una suite completa de pruebas que valida:
     - 1.1 y 1.2: Generación e invalidación de fixture al dar de baja un participante.
     - 2.1 y 2.2: Carga inicial y corrección de marcador en liga con recálculo dinámico de posiciones y puntos.
     - 3.1: Generación automática de la final tras semifinales en eliminación directa.
     - 3.2: Propagación exitosa del nuevo ganador a la final al corregir la semifinal (antes de jugarse la final).
     - 3.3: Bloqueo estricto ante el intento de rectificar semifinales cuando la final ya fue disputada.
   - Resultado: 7 / 7 pruebas aprobadas exitosamente (100% PASS).

54. Fase B de Remediación: Blindaje y Bloqueo de Modificaciones de Equipos y Plantillas en Competencia
Objetivo: Blindar el roster de los equipos deportivos durante competiciones en curso, impidiendo que miembros o capitanes abandonen el equipo o sean expulsados afectando la integridad de los partidos programados, e impedir la alteración o disolución de equipos con compromisos competitivos activos.

Hallazgos de Inconsistencia Detectados:
1. Abandono o Expulsión de Miembros en Pleno Torneo: Un capitán podía expulsar a miembros de la plantilla o abandonar el equipo mediante `dejarEquipo()` mientras el equipo se encontraba disputando partidos en un torneo en estado `en_curso`, lo que provocaba que equipos quedaran por debajo de la nómina reglamentaria o alteraran competidores a mitad de fixture.
2. Cambio de Disciplina Deportiva en Equipos Inscriptos: No existía salvaguarda para evitar que el equipo cambiara su disciplina deportiva (`juego_id`) mientras tenía postulaciones pendientes, confirmadas o torneos en curso, lo que creaba una inconsistencia de tipo de deporte respecto al torneo inscripto.
3. Eliminación (Soft Delete) de Equipos con Torneos Activos: El método `eliminarEquipo()` desactivaba al equipo (`activo = 0`) sin comprobar si participaba en torneos vigentes, dejando registros rotos en fixtures y tablas de posiciones.

Primera Solución Intentada y Errores Encontrados en Tiempo de Ejecución:
- Error Detectado: Transacciones no reentrantes en `crearEquipoCompleto()`, `responderInvitacion()` y `responderSolicitudRecibida()` (`PDOException: There is no active transaction` y `Undefined array key "equipoId"`).
- Lo que se intentó hacer: Al ejecutar la suite de pruebas unitarias/integración de Fase B bajo una transacción global de testing, la invocación de `crearEquipoCompleto()` ejecutó un `$this->bd->beginTransaction()` incondicional, arrojando excepción de colisión transaccional en MySQL PDO y abortando prematuramente con `rollBack()`.
- Solución definitiva aplicada: Se refactorizaron todos los métodos transaccionales del modelo `Equipo.php` implementando el patrón de transacciones reentrantes (`$transaccionPropia`), permitiendo que el método participe limpiamente en transacciones superiores o inicie una propia según corresponda sin abortar estados externos.

Solución Final Aplicada y Arquitectura de la Solución:
1. Métodos de Verificación de Compromisos Deportivos (`modelos/Equipo.php`):
   - `tieneTorneosEnCurso(int $equipoId): bool`: Verifica si el equipo posee participantes en estado `confirmado` en torneos `en_curso`.
   - `tieneTorneosActivos(int $equipoId): bool`: Verifica si el equipo posee participantes en estado `confirmado` o `pendiente` en torneos `en_curso` o `inscripciones_abiertas`.
2. Blindaje de Roster (`dejarEquipo` y `expulsarMiembro`):
   - En `Equipo::dejarEquipo()`: Se rechaza la salida si el equipo tiene torneos en curso, retornando el mensaje: *"No podés abandonar el equipo mientras esté disputando un torneo en curso."*
   - En `Equipo::expulsarMiembro()` y `EquipoControlador::expulsarMiembro()`: Se bloquea la expulsión si el equipo está en curso y se redirige con el parámetro `&errorExpulsar=` para alertar al capitán en la interfaz.
3. Blindaje de Disciplina y Eliminación (`cambiarDisciplina` y `eliminarEquipo`):
   - En `Equipo::cambiarDisciplina()` y `EquipoControlador::actualizarEquipo()`: Se bloquea cualquier mutación de `juego_id` si el equipo tiene torneos activos, y se valida además que la nueva disciplina permita modalidad colectiva (`modalidad_id = 2`).
   - En `Equipo::eliminarEquipo()`: Se rechaza el soft-delete si el equipo tiene torneos activos.
4. Enriquecimiento de Modelo y UX (`Equipo::obtenerPorId` y `vistas/jugador/gestionar-equipo.php`):
   - `obtenerPorId()` incluye `nombre_juego` e icono de la disciplina deportiva vía `LEFT JOIN juegos`.
   - En `gestionar-equipo.php` se visualiza la disciplina deportiva asignada con advertencia de bloqueo y se añadió el contenedor para renderizar alertas `errorExpulsar`.
5. Validación Automatizada (`tests/test_fase_b_equipos.php`):
   - Se implementaron 12 pruebas automatizadas que cubren el bloqueo de expulsión, abandono, cambio de disciplina y eliminación en torneos en curso, así como su correcto desbloqueo operativo una vez que la competencia finaliza.
   - Resultado: 12 / 12 pruebas aprobadas exitosamente (100% PASS).
55. Fase C de Remediación: Seguridad, Baneo en Caliente y Eliminación en Cascada Segura de Torneos por Administrador
Objetivo: Garantizar la seguridad operativa y el control administrativo del sistema mediante la expulsión e invalidación inmediata ("baneo en caliente") de sesiones activas de usuarios suspendidos o desactivados por la administración, y posibilitar la eliminación atómica y segura de torneos sin violaciones de clave foránea (FK) en bases de datos relacionales.

Hallazgos de Inconsistencia Detectados:
1. Sesiones Fantasma de Usuarios Suspendidos ("Baneo en Frío"): Cuando un administrador desactivaba o suspendía una cuenta de usuario (`esta_activo = 0`) desde el panel administrativo, el usuario continuaba operando normalmente mientras su sesión PHP (`$_SESSION`) siguiera viva. Podía seguir creando torneos, inscribiéndose o publicando comentarios hasta que cerrara sesión manualmente o caducara la cookie de sesión, permitiendo acciones no autorizadas a usuarios sancionados.
2. Fallo de Eliminación Administrativa de Torneos por Restricción de Claves Foráneas: Al intentar eliminar un torneo desde la administración (`Torneo::eliminarPorAdmin()`), la sentencia `DELETE FROM torneos WHERE id = :id` arrojaba un error fatal `SQLSTATE[23000]: Integrity constraint violation: 1451 Cannot delete or update a parent row: a foreign key constraint fails`. Esto ocurría porque múltiples tablas hijas (`torneo_encuentros`, `torneo_suizo_parejas`, `torneo_posiciones`, `torneo_actividades`, `torneo_comentarios`, `torneo_cambio_estado`, `torneo_config`, `participantes_torneo` y la tabla polimórfica `guardados`) mantienen referencias de clave foránea sin cláusula `ON DELETE CASCADE` a nivel motor.

Primera Solución Intentada y Errores Encontrados en Tiempo de Ejecución:
- Error 1: Fallo de headers y cookies de sesión en entornos CLI y testing (`session_set_cookie_params(): Session cookie parameters cannot be changed after headers have already been sent` y `Cannot modify header information`).
  - Lo que se intentó hacer: En `Sesion::estaLogueado()`, al detectar `esta_activo = 0`, se invocaba directamente `self::destruir()` y `header('Location: ...')`. En entornos de consola o suites de pruebas PHP CLI donde la salida de texto ya había comenzado, esto generaba warnings fatales de PHP.
  - Solución definitiva aplicada: Se blindaron las llamadas de destrucción de cookies y emisión de encabezados HTTP con `if (!headers_sent())` y el operador de control `@session_destroy()`, permitiendo que la lógica de seguridad opere de forma limpia tanto en el servidor web como en entornos de pruebas automatizadas por consola.
- Error 2: Bloqueo de eliminación por registros huérfanos o desorden en el borrado relacional.
  - Lo que se intentó hacer: Intentar borrar inicialmente solo encuentros y participantes.
  - Por qué falló: Tablas secundarias como `torneo_suizo_parejas`, `torneo_posiciones`, `torneo_actividades`, `torneo_comentarios`, `torneo_cambio_estado`, `torneo_config` y `guardados` mantenían bloqueada la eliminación por dependencia de clave foránea.
  - Solución definitiva aplicada: Se implementó un algoritmo de purga relacional en cascada atómica en `Torneo::eliminarPorAdmin()` que elimina en orden estricto de precedencia todas las 8 tablas hijas directas más la referencia polimórfica en `guardados` dentro de una transacción reentrante (`$transaccionPropia`). Si ocurre cualquier imprevisto, la operación se revierte en su totalidad (`rollBack`).
- Error 3: Violación de trigger `trg_torneo_validar_disciplina_ins` en el test automatizado.
  - Lo que se intentó hacer: Insertar un torneo dummy seleccionando el primer `juego_id` activo disponible en la base de datos con modalidad 1 y formato 'liga'.
  - Por qué falló: MySQL activó el trigger de integridad deportiva `trg_torneo_validar_disciplina_ins` arrojando `SQLSTATE[45000]: 1644 Integridad fallida: la modalidad no está permitida para esta disciplina`.
  - Solución definitiva aplicada: Se ajustó la consulta de selección del fixture de prueba para validar que el juego admitiera tanto `modalidad_id = 1` como `formato = 'liga'` consultando `juego_modalidades_permitidas` y `juego_formatos_permitidos`.

Solución Final Aplicada y Arquitectura de la Solución:
1. Mecanismo de Baneo en Caliente (`codigo_fuente/ayudantes/Sesion.php`):
   - Se añadió el método estático `Sesion::verificarUsuarioActivo(): bool`, el cual consulta en tiempo real a la base de datos (`SELECT esta_activo FROM usuarios WHERE id = :id LIMIT 1`) verificando el estado del usuario logueado en la sesión activa.
   - En `Sesion::estaLogueado()`: Antes de validar la expiración y retornar `true`, invoca `verificarUsuarioActivo()`. Si el usuario fue suspendido o desactivado por un administrador (`esta_activo == 0`):
     - Destruye la sesión de inmediato mediante `self::destruir()`.
     - Si los headers no han sido enviados, redirige de inmediato a `index.php?c=auth&a=mostrarLogin&error=cuenta_suspendida`.
     - Retorna `false`, cortando en el acto la ejecución de cualquier controlador protegido.
2. Manejo de Alertas de Suspensión (`codigo_fuente/controladores/AuthControlador.php`):
   - En `AuthControlador::mostrarLogin()` se implementó la captura del parámetro `error=cuenta_suspendida`, inyectando el mensaje de advertencia: *"Tu cuenta ha sido suspendida o desactivada por la administración. Si consideras que se trata de un error, contacta a soporte."*
3. Eliminación Atómica en Cascada Segura (`codigo_fuente/modelos/Torneo.php` - `eliminarPorAdmin()`):
   - Se refactorizó íntegramente `eliminarPorAdmin(int $torneoId): bool` implementando el patrón transaccional reentrante (`$transaccionPropia = false`).
   - Se ejecuta una secuencia de eliminación en orden estricto de integridad relacional:
     1. `torneo_encuentros` (`torneo_id = :id`)
     2. `torneo_suizo_parejas` (`torneo_id = :id`)
     3. `torneo_posiciones` (`torneo_id = :id`)
     4. `torneo_actividades` (`torneo_id = :id`)
     5. `torneo_comentarios` (`torneo_id = :id`)
     6. `torneo_cambio_estado` (`torneo_id = :id`)
     7. `torneo_config` (`torneo_id = :id`)
     8. `participantes_torneo` (`torneo_id = :id`)
     9. `guardados` (`tipo = 'torneo' AND referencia_id = :id`)
     10. `torneos` (`id = :id`)
   - Si no existía el torneo o no se afectaron filas, retorna `false`. Si la eliminación tiene éxito, confirma la transacción (`commit`) y retorna `true`.
4. Validación Automatizada (`tests/test_fase_c_seguridad_admin.php`):
   - Se construyó una suite de pruebas que valida:
     - 1.1: Usuario activo validado normalmente (`verificarUsuarioActivo: true`, `estaLogueado: true`).
     - 1.2: Detección en tiempo real del baneo al cambiar `esta_activo = 0` en base de datos, destruyendo la sesión y bloqueando el acceso.
     - 2.1: Creación de torneo de prueba con dependencias completas en 9 tablas relacionales (11 registros vinculados).
     - 2.2: Eliminación en cascada exitosa sin violaciones de integridad FK, dejando en 0 el torneo principal y todas sus tablas asociadas.
     - 2.3: Manejo defensivo ante IDs inexistentes retornando `false`.
   - Resultado: 5 / 5 pruebas aprobadas exitosamente (100% PASS).

56. Fase D de Remediación: Detección y Advertencia de Conflictos de Horario y Canchas en la Programación de Partidos
Objetivo: Prevenir la superposición de horarios y la sobreasignación de canchas/sedes durante la programación y reprogramación de encuentros deportivos por parte del organizador, garantizando la viabilidad física del cronograma y alertando en tiempo real ante colisiones de sede o participantes.

Hallazgos de Inconsistencia Detectados:
1. Asignación Ciega de Canchas y Sedes sin Detección de Colisiones: En el método `Torneo::programarEncuentro()`, la fecha, hora y sede se actualizaban directamente con un `UPDATE torneo_encuentros SET fecha_hora_programada = :fecha_hora, cancha = :sede WHERE id = :id` sin verificar si la misma cancha ya había sido reservada para otro encuentro en esa misma fecha y franja horaria. Esto permitía sobrecargar una cancha física con múltiples partidos en simultáneo.
2. Superposición Horaria de Participantes (Partidos Simultáneos): No existía ninguna validación que impidiera programar a un mismo participante (jugador o equipo) en dos partidos distintos a la misma hora o con minutos de diferencia dentro del torneo, creando situaciones absurdas donde un competidor debía disputar dos partidos al mismo tiempo.
3. Tratamiento Indiferenciado de Mensajes en la Vista de Administración: El controlador almacenaba errores de programación en la variable genérica `$_SESSION['mensaje']`, renderizándose siempre como un mensaje verde `.success-message` en `administrar-torneo.php`, lo que confundía al organizador haciéndole creer que una operación fallida había sido exitosa.

Primera Solución Intentada y Errores Encontrados en Tiempo de Ejecución:
- Error 1: Sensibilidad a mayúsculas, minúsculas y espacios en nombres de canchas.
  - Lo que se intentó hacer: Comparar directamente `$sede == $canchaRegistrada`.
  - Por qué falló: Un organizador que escribía `"Cancha 1"` para el primer partido y `"cancha 1"` o `"  Cancha 1  "` para el segundo no disparaba el conflicto debido a discrepancias tipográficas simples.
  - Solución definitiva aplicada: Se normalizaron las cadenas en SQL y PHP mediante `LOWER(TRIM(...))` y se aplicó coalescencia con la ubicación general del torneo (`COALESCE(NULLIF(TRIM(e.cancha), ''), :ubicacion_torneo)`) para cubrir también las canchas asignadas por defecto.
- Error 2: Falsos positivos durante la reprogramación de un partido existente.
  - Lo que se intentó hacer: Buscar colisiones en la tabla con la misma fecha y hora.
  - Por qué falló: Si el organizador simplemente actualizaba la cancha de un partido ya programado a las 15:00, el sistema detectaba que "ya existía un partido a las 15:00" (el mismo partido) y bloqueaba la edición.
  - Solución definitiva aplicada: Se incorporó la cláusula de exclusión `e.id != :encuentro_id`, permitiendo reprogramar o editar un encuentro sin colisionar contra sí mismo.
- Error 3: Solapamiento temporal en horarios contiguos no idénticos (ej. 15:00 y 15:15).
  - Lo que se intentó hacer: Validar únicamente igualdad exacta de timestamp (`e.fecha_hora_programada = :fecha_hora`).
  - Por qué falló: Partidos programados a las 15:00 y a las 15:10 en la misma cancha eludían el bloqueo a pesar de superponerse físicamente.
  - Solución definitiva aplicada: Se implementó una ventana de proximidad temporal en minutos mediante `ABS(TIMESTAMPDIFF(MINUTE, e.fecha_hora_programada, :fecha_hora)) < 30`, bloqueando tanto la coincidencia al mismo minuto como solapamientos temporales incompatibles (< 30 min).

Solución Final Aplicada y Arquitectura de la Solución:
1. Método Preventivo de Detección de Conflictos (`codigo_fuente/modelos/Torneo.php` - `verificarConflictosProgramacion()`):
   - Consulta el encuentro especificado obteniendo `torneo_id`, `ronda`, participantes locales/visitantes y la ubicación por defecto del torneo.
   - Determina la cancha efectiva (personalizada o por defecto).
   - Valida conflictos de sede: Comprueba si existe otro encuentro en estado `programado` o `aplazado` en el mismo torneo dentro de la ventana de 30 minutos asignado a la misma cancha. Retorna un mensaje específico con los nombres de los equipos y la hora ocupada.
   - Valida conflictos de participantes: Si el encuentro tiene participantes asignados, comprueba si alguno de ellos ya tiene otro partido programado en ese mismo rango horario. Retorna un mensaje específico con el nombre del participante y la ronda del partido previo.
2. Programación Segura con Retorno Estructurado (`Torneo::programarEncuentro()`):
   - Invoca preventivamente `verificarConflictosProgramacion()`. Si se detecta un conflicto, aborta la actualización y retorna `['ok' => false, 'mensaje' => $conflicto]`.
   - Si no hay colisiones, ejecuta el `UPDATE` y retorna `['ok' => true, 'mensaje' => 'Partido programado correctamente.']`.
3. Controlador y Flujo de Sesión (`codigo_fuente/controladores/PanelOrganizadorControlador.php`):
   - En `programarEncuentro()`: Evalúa el retorno estructurado del modelo; si `ok === false`, setea `$_SESSION['error']` con la causa exacta del conflicto.
   - En `administrar()`: Pasa `'error' => $_SESSION['error'] ?? null` al template y limpia atómicamente ambas variables de sesión.
4. Interfaz de Usuario y UX del Organizador (`codigo_fuente/vistas/organizador/administrar-torneo.php`):
   - Se adaptó la vista para renderizar una alerta destacada en rojo `.error-message` con icono `fa-triangle-exclamation` cuando exista un error o conflicto, y la alerta verde `.success-message` con icono `fa-circle-check` ante operaciones exitosas.
5. Validación Automatizada (`tests/test_fase_d_programacion.php`):
   - Se diseñó una suite automatizada que verifica:
     - 1.1: Programación inicial exitosa sin conflictos.
     - 2.1: Bloqueo inmediato por cancha ocupada en el mismo horario exacto.
     - 2.2: Bloqueo por cancha ocupada normalizando mayúsculas, minúsculas y espacios.
     - 2.3: Bloqueo por solapamiento temporal (< 30 min) en la misma cancha.
     - 3.1: Bloqueo por participante ya comprometido a la misma hora en otro partido.
     - 3.2: Bloqueo por participante con solapamiento temporal (< 30 min).
     - 4.1: Reprogramación del mismo encuentro sin falsos positivos contra sí mismo.
     - 4.2: Programación válida en horario separado (>= 30 min).
   - Resultado: 8 / 8 pruebas aprobadas exitosamente (100% PASS).

57. Resolución de Deudas Técnicas de Auditoría, Limpieza de Semillas y Blindaje Operativo
Objetivo: Atender de forma integral las deudas técnicas y vulnerabilidades identificadas durante la auditoría de arquitectura y seguridad (C1, A1, A2, A3, A4, M8, M9), sanear el entorno de base de datos eliminando scripts de semillas obsoletos que corrompen el modelo relacional, corregir la sobrepoblación de usuarios administradores y blindar los métodos mutantes con POST y tokens CSRF.

Inconsistencias y Hallazgos Detectados:
1. Hallazgo C1 (Crítico): Acciones Destructivas por GET sin Validación CSRF:
   - TorneoAdminControlador::eliminar leía el ID desde $_GET['id'] y ejecutaba directamente la cascada total irreversible de torneos.
   - JuegoControlador::cambiarEstado ejecutaba la baja lógica de disciplinas mediante un simple enlace GET.
   - Navegaciones top-level o ataques de CSRF podían gatillar la eliminación de competencias o disciplinas con la sesión administrativa abierta.
2. Hallazgo A1 y A2 (Alto): verificarUsuarioActivo() Falla Abierto y Rol Desincronizado en Sesión:
   - En Sesion.php, el bloque catch (\Throwable) ante cualquier error de conexión o saturación retornaba true, permitiendo que usuarios suspendidos eludieran la suspensión si la base de datos demoraba o fallaba puntualmente.
   - El rol del usuario ($_SESSION['rol_id']) quedaba estático desde el momento del login. Si un administrador degradaba a un organizador o le revocaba permisos, éste continuaba operando con privilegios hasta que expirara su cookie de sesión.
3. Hallazgo A3 (Alto): Carrera Concurrente en Torneo::cambiarEstado:
   - El estado del torneo se consultaba fuera de transacción y sin bloqueo (obtenerBorrador()).
   - El UPDATE no comprobaba el estado anterior en la cláusula WHERE, permitiendo que dos peticiones concurrentes colisionaran o que el torneo cambiara de estado de forma inconsistente.
4. Hallazgo A4 (Alto): Fuga de Información Sensible al Fallar Conexión PDO:
   - En Conexion.php, el bloque catch (PDOException $e) ejecutaba die("Error al conectar..." . $e->getMessage()), exponiendo host, usuario, DSN y detalles internos de infraestructura ante una falla en el motor de base de datos.
5. Inconsistencias de Semillas y Cuentas Administrativas:
   - El archivo 002_datos_ejemplo.sql contenía datos de la primera versión del sistema que introducían datos que corrompen la integridad referencial.
   - La base de datos contenía 18 usuarios con rol_id = 1 (Administrador General), originados por generadores masivos aleatorios (generador_semillas.php), cuando el sistema en desarrollo únicamente requiere un administrador de control.
   - Existía un torneo residual de pruebas (__TEST_TORNEO_ELIMINACION__ ID 1220) alojado en la base de datos de desarrollo.
6. Hallazgos Menores M8 y M9:
   - stream_url en la creación y edición de torneos no validaba la estructura formal de una URL con FILTER_VALIDATE_URL.
   - ManejadorErrores leía $_SESSION['rol'] en lugar de $_SESSION['rol_id'], registrando siempre NULL en el campo rol_usuario de la tabla errores_sistema.

Solución Aplicada y Arquitectura de Remediación:
1. Reemplazo y Limpieza de Semillas Fundacionales (002_datos_base.sql):
   - Se eliminó definitivamente el archivo obsoleto 002_datos_ejemplo.sql.
   - Se generó base_de_datos/migraciones/002_datos_base.sql conteniendo estrictamente las tablas maestras y catálogos requeridos (permisos, roles, rol_permisos, modalidades, sistemas_puntuacion, juegos, politicas_contrasenas, logros, formatos_torneo).
   - Se conservaron de forma controlada exclusivamente las tres identidades esenciales de prueba solicitadas:
     - admin@ascend.com (ID 1, Administrador General).
     - organizador@ascend.com (ID 2, Organizador con perfil verificado).
     - jugador@ascend.com (ID 3, Jugador con perfil y logros iniciales).
2. Saneamiento de Base de Datos y Purga de Administradores Sintéticos:
   - Se eliminó el torneo residual 1220 de forma limpia mediante cascada atómica.
   - Se ejecutó un script de saneamiento que degradó a los 17 usuarios sintéticos testN@ejemplo.com a participantes (rol_id = 3), dejando a admin@ascend.com como el único Administrador General activo de la plataforma.
3. Blindaje de Operaciones Destructivas contra CSRF y Métodos GET (TorneoAdminControlador, JuegoControlador, Vistas Admin):
   - TorneoAdminControlador::eliminar: Ahora verifica estrictamente $_SERVER['REQUEST_METHOD'] === 'POST', valida el token CSRF mediante Sesion::requerirTokensCSRF() y obtiene el ID desde $_POST['id'].
   - En vistas/admin/torneos/torneos_index.php, se reemplazó el enlace <a> por un <form method="POST"> con input oculto de token CSRF, confirmación JavaScript preventiva y botón con estilos idénticos.
   - JuegoControlador::cambiarEstado: Se migró a POST con validación obligatoria de Sesion::requerirTokensCSRF().
   - En vistas/admin/juegos/juegos_index.php, se adaptó el botón a un formulario POST con token CSRF.
   - En publico/index.php, la regla de solo lectura en modo suplantación bloquea cualquier intento de mutación administrativa.
4. Seguridad Fail-Closed y Sincronización Dinámica de Privilegios (Sesion.php):
   - En Sesion::verificarUsuarioActivo(), se modificó la consulta a SELECT esta_activo, rol_id FROM usuarios WHERE id = :id LIMIT 1.
   - Si la consulta arroja que el usuario fue suspendido (esta_activo !== 1), retorna false y la sesión es destruida de inmediato.
   - Si el rol cambió en la base de datos, se sincroniza en caliente $_SESSION['rol_id'] = (int) $usuario['rol_id'].
   - Política Fail-Closed: Ante cualquier fallo o excepción en la consulta, se registra en error_log y se retorna false (bloqueando accesos no verificados), permitiendo bypass únicamente en ejecuciones CLI que no tengan sesión activa.
5. Bloqueo Pesimista y Guarda Atómica de Estado Anterior (Torneo::cambiarEstado):
   - Dentro de la transacción atómica, se ejecuta SELECT id, estado FROM torneos WHERE id = :id AND organizador_id = :organizador_id FOR UPDATE para serializar transiciones concurrentes sobre el mismo registro.
   - Se valida la matriz de transiciones permitidas contra el estado actual bloqueado.
   - La sentencia de actualización incorpora la guarda de estado anterior:
     UPDATE torneos SET estado = :estado WHERE id = :id AND organizador_id = :organizador_id AND estado = :estado_anterior
   - Se valida rowCount() === 1. Si otra transacción alteró el estado en simultáneo, el UPDATE no afecta filas y se efectúa rollBack().
6. Supresión de Fuga de Información (Conexion.php):
   - Se eliminó la instrucción die() con mensajes detallados de PDO.
   - Ante errores de conexión, el detalle técnico se envía a error_log y se lanza una PDOException con mensaje seguro y genérico, permitiendo que ManejadorErrores capture la excepción y renderice la vista amigable 500.
7. Telemetría de Errores y Validación de URLs (ManejadorErrores.php, PanelOrganizadorControlador.php):
   - En ManejadorErrores.php, se ajustó la captura a $rolUsuario = $_SESSION['rol_id'] ?? $_SESSION['rol'] ?? null;.
   - En PanelOrganizadorControlador.php (tanto en la edición del torneo como en la etapa 2 de creación), se implementó la validación estricta de stream_url utilizando filter_var($streamUrl, FILTER_VALIDATE_URL) y la expresión regular #^https?://#i.

Plan de Reorganización y Estandarización de Scripts SQL (Próxima Implementación):
- Arquitectura de Scripts en 3 Capas Limpias:
  - 001_esquema_base.sql: Definición pura DDL (CREATE TABLE, índices, foreign keys) con integridad referencial consolidada, incorporando de raíz los tipos y columnas corregidos sin necesidad de scripts de parches (004, 005).
  - 002_catalogos_y_usuarios_base.sql: Únicamente datos semilla obligatorios para el arranque del sistema (tablas maestras de configuración y las 3 cuentas canónicas: admin, organizador y jugador).
  - 003_seed_pruebas_qa.sql: Semilla sintética de torneos, equipos y partidos para ambientes de staging/QA. Se unificará para que ningún generador aleatorio vuelva a introducir administradores masivos; todos los usuarios sintéticos se definirán estrictamente como jugadores (rol_id = 3) y organizadores (rol_id = 2).
- Depuración de Generadores Aleatorios:
  - Sustituir generador_semillas.php por un seeder controlado y determinista con IDs fijos e inserciones idempotentes.

Validación y Pruebas Ejecutadas:
- Suite Dedicada tests/test_deudas_tecnicas_auditoria.php:
  - 1.1 - 1.3: Verificación de baneo fail-closed y sincronización dinámica de rol_id en caliente (PASS).
  - 2.1 - 2.2: Transiciones de estado legítimas y rechazo atómico de transiciones inválidas en Torneo::cambiarEstado (PASS).
  - 3.1: Captura correcta de rol_id en ManejadorErrores (PASS).
  - 4.1 - 4.2: Exigencia de método POST y token CSRF en TorneoAdminControlador::eliminar y JuegoControlador::cambiarEstado (PASS).
  - 5.1: Validación de stream_url mediante FILTER_VALIDATE_URL (PASS).
  - Resultado: 9 / 9 pruebas aprobadas exitosamente (100% PASS).
- Regresión Completa de Suites Anteriores:
  - test_fase_a_fixtures.php: 7 / 7 (100% PASS).
  - test_fase_b_equipos.php: 12 / 12 (100% PASS).
  - test_fase_c_seguridad_admin.php: 5 / 5 (100% PASS).
  - test_fases_remediacion.php: 10 / 10 (100% PASS).
  - test_e2e_simulacion_completa.php: 12 / 12 (100% PASS).

58. Corrección de Inconsistencia SQL en Perfil de Equipo y Restauración de Estilos en Detalle de Error
Objetivo: Subsanar el error de columna inexistente al consultar el perfil de equipos y restaurar la carga de hojas de estilo en la vista de detalle de errores del monitor administrativo.

Inconsistencias Detectadas:
1. Error SQLSTATE[42S22] en Equipo::obtenerPorId:
   - En codigo_fuente/modelos/Equipo.php (línea 194), la consulta seleccionaba j.nombre_juego y j.icono_url sobre la tabla juegos.
   - En el esquema relacional, los campos reales son j.nombre y j.imagen_url.
   - Al invocar c=equipo&a=verPerfil&id=..., PDO lanzaba un Fatal Error 1054 por columna inexistente.
2. Pérdida de Estilos Visuales en detalle_error.php:
   - La vista codigo_fuente/vistas/admin/detalle_error.php tenía rutas CSS absolutas hardcodeadas a http://localhost/SGDM/codigo_fuente/publico/css/admin/....
   - Al ejecutarse la aplicación dentro de un contenedor Docker o entorno con DocumentRoot directo (/var/www/html), el navegador intentaba resolver la URL externa hardcodeada y fallaba con 404, provocando que la pantalla se renderizara sin estilos ni tipografía.

Solución Aplicada:
1. Corrección de Consulta SQL en Equipo::obtenerPorId:
   - Se actualizó la proyección a SELECT e.*, j.nombre AS nombre_juego, j.imagen_url AS juego_icono FROM equipos e LEFT JOIN juegos j ON j.id = e.juego_id.
   - Mantiene el alias nombre_juego para retrocompatibilidad total con las vistas consumidoras y mapea correctamente la imagen del juego.
2. Dinamización de Enlaces en detalle_error.php:
   - Se reemplazaron las URLs fijas por la constante dinámica URL_BASE: URL_BASE . css/admin/base.css y URL_BASE . css/admin/detalle_error.css, además de normalizar el enlace de retorno al dashboard.
