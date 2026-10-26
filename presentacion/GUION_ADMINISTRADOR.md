# ASCEND — Guion de defensa: Administrador, Seguridad, Sistemas Operativos, Docker y Base de Datos

- **Uso:** documento personal de defensa para los 40 minutos de preguntas.
- **Alcance:** módulo de administración, seguridad de la aplicación, implementación en Docker, diseño de base de datos y toolkit de administración de sistemas.
- **Formato:** 20 minutos de exposición (guion general, `GUION_DEFENSA.md`) + 40 minutos de defensa (este documento).
- **Total:** 80 minutos, incluidos 20 de preparación.

---

## 1. Cómo usar este documento

Tres reglas, en este orden:

1. **Decisión → motivo → evidencia.** Nunca empieces por la tecnología. Empezá por el problema, después la alternativa descartada, después la evidencia concreta (archivo, tabla, línea, comando).
2. **No inventar números.** Todo número que digas tiene que salir de una fuente que puedas abrir: `README.md`, `informes_auditoria/VERIFICACION_FINAL_DRY_RUN.md`, una consulta a la base o un archivo del repositorio. Si no lo tenés a mano, decí “lo verifico y te lo confirmo” en lugar de improvisar.
3. **Declarar el límite antes de que lo encuentren.** Cada tema tiene una sección “Límite declarado” con la frase exacta. Un límite reconocido con precisión vale más que una respuesta perfecta memorizada.

**Regla de oro para las preguntas:** si la pregunta es amplia (“¿es seguro?”), acotá. “Seguro contra qué” es una respuesta válida y profesional.

---

## 2. Mapa de tiempos (80 minutos)

| Bloque | Duración | Qué pasa |
| --- | --- | --- |
| Preparación | 20 min | Stack arriba, tres pestañas listas, torneo 1217 abierto, video de respaldo local |
| Exposición | 20 min | 12 diapositivas, 1:30 promedio por slide |
| Defensa | 40 min | Preguntas del jurado, este documento |

**Reparto sugerido de la defensa por área** (no es obligatorio, pero evita el silencio incómodo):

| Minutos | Foco | Quién responde primero |
| --- | --- | --- |
| 0–8 | Funcionalidad y alcance del producto | Integrante de producto/backend |
| 8–16 | Seguridad de la aplicación | Administrador (vos) |
| 16–24 | Base de datos y modelo | Administrador (vos) |
| 24–32 | Docker, despliegue y operación | Administrador (vos) |
| 32–38 | Sistemas operativos y toolkit | Administrador (vos) |
| 38–40 | Cierre y límites | Todos, cierre breve |

Si el jurado salta de área, no corrijas el orden: respondé y devolvé el turno. Lo importante es que **cada respuesta termine en el sistema**, no en una opinión.

**Relación con el guion general.** `GUION_DEFENSA.md` asigna las diapositivas 7, 8 y 9 a otros expositores como orden de palabra. Eso no cambia quién responde en la defensa: las preguntas de administración, seguridad, Docker, base de datos y toolkit caen en este documento. Si el equipo mantiene ese reparto, durante la exposición no interrumpas; en preguntas, tomá el área.

**Frases de transición útiles:**
- “Eso lo puedo mostrar en la base si querés.”
- “Ese punto lo tiene documentado [compañero], pero te adelanto la decisión y después amplía.”
- “Esa es una limitación conocida; la tenemos declarada y te digo por qué no la resolvimos en esta entrega.”

---

## 3. Tus intervenciones durante la exposición (20 min)

No necesitás hablar en las 12 diapositivas. El panel de administración **no tiene diapositiva propia**: se defiende en la ronda de preguntas. Si querés una frase en la exposición, va en la 3 y dura quince segundos. El resto son diapositivas que quizá presentes vos o un compañero; si las presenta otro, no interrumpas y guardá estos párrafos para la defensa.

### Diapositiva 3 — Tres roles (≈15 s, opcional)

> “El admin no ve torneos: ve el sistema. Trece secciones, desde usuarios y catálogo hasta auditoría y monitoreo de errores. Dos reglas que lo definen: no puede bloquearse a sí mismo, y ‘ver como’ entra a otra sesión en modo solo lectura.”

### Diapositiva 6 — Arquitectura (≈30 s)

> “La aplicación es MVC en PHP 8 sin framework, con un único punto de entrada: `publico/index.php`. Ese archivo valida la ruta contra una lista blanca de controladores y acciones; cualquier ruta fuera de esa lista devuelve 404. El document root de Apache apunta solo a `publico/`, así que `configuracion/`, `modelos/` y `vistas/` no son alcanzables por HTTP. Lo verifiqué: esas rutas devuelven 404. Y todo el stack corre con Docker Compose, con imágenes fijadas por versión: PHP 8.2, MySQL 8, phpMyAdmin y Grafana.”

### Diapositiva 8 — Seguridad (≈60 s)

> “La seguridad está en capas. Autenticación con bcrypt y límite de intentos por IP: cinco fallos bloquean la IP cinco minutos. Autorización por rol en el servidor, no en el menú. CSRF con un token de sesión incrustado en cada formulario, validado en el POST; la política es que toda acción que cambie estado sea POST. Sesión con cookie HttpOnly y SameSite Lax, regeneración de ID al autenticar y expiración por inactividad a los treinta minutos. Y cabeceras: CSP, X-Frame-Options, nosniff, Referrer-Policy y Permissions-Policy. Un formulario sin token válido recibe 419.”

### Diapositiva 9 — Toolkit y operación (≈40 s)

> “El sistema no solo se programa: se opera. Hay un toolkit en Bash con 32 scripts y 9 módulos: instalación, usuarios y sudoers acotados, logs, auditoría con `auditd`, respaldos con SHA-256, replicación maestro-esclavo, procesos con confirmación por palabra clave y estado del servidor. Cada módulo se puede ejecutar solo y el menú delega en ellos. Lo que está validado es la sintaxis y la ayuda de cada comando; no se ejecutó en un servidor real, y eso lo declaro.”

### Diapositiva 11 — Evidencia (≈30 s)

> “Los números de esta diapositiva salen de la verificación final: 16 de 16 rutas públicas, 18 de 18 administrativas, flujo de jugador y organizador, control de errores con 500 genérico y sin stack trace, y el aislamiento del docroot comprobado con 404 en las carpetas internas. El toolkit de sistemas operativos tiene 32 scripts y 24 comandos con ayuda; eso está validado sintácticamente, no ejecutado en un servidor real, y lo digo así.”

### Cierre (≈20 s)

> “Quedan cuarenta minutos para preguntas. Podemos profundizar en cualquier decisión: administración, seguridad, base de datos, Docker o el toolkit de sistemas operativos. Estamos listos.”

---

## 4. Cómo responder: estructura de 30–60 segundos

Cinco pasos. Si te trabás, volvé al paso 2.

1. **Reformular en una frase.** “La pregunta es por qué el borrado de torneos es físico y no lógico.” Esto te da dos segundos y confirma que entendiste.
2. **La decisión.** “La decisión fue borrado físico con confirmación y registro de auditoría.”
3. **El motivo.** “Porque un torneo con encuentros y posiciones no se puede ‘ocultar’ sin dejar datos huérfanos o inconsistentes; el histórico se conserva en una sección aparte para los torneos que sí deben quedar consultables.”
4. **La evidencia.** “Está en `TorneoAdminControlador::eliminar()` y en la tabla `logs_actividad`.”
5. **El límite o el siguiente paso.** “Lo que hoy falta es que sea POST con CSRF; hoy se dispara por GET. Es deuda declarada y la corrección es acotada.”

Si la pregunta es de una materia que no es tu área, hacé el paso 1 y 5 y cedé: “Eso lo responde [compañero], que lo implementó.”

---

## 5. Banco de preguntas — Administración y producto

### A1. ¿Por qué trece secciones de administración y no un panel único?

Porque cada sección tiene un ciclo de vida distinto y un riesgo distinto. Usuarios y roles son identidad; torneos y resultados son operación; auditoría y errores son observabilidad. Separarlas permite aplicar permisos y validaciones específicas sin un `if` gigante. El costo es más navegación; el beneficio es que un error en una sección no arrastra a las demás.

**Evidencia:** `vistas/admin/plantilla/menu_lateral.php`, `controladores/AdminControlador.php`.

### A2. ¿Cómo evitás que un administrador se bloquee a sí mismo?

`cambiarEstadoUsuario()` compara el id objetivo con `$_SESSION['usuario_id']` y rechaza la operación si son iguales. Es una regla de negocio en el controlador, no una validación de interfaz.

**Evidencia:** `UsuarioControlador::cambiarEstadoUsuario()`.

**Límite:** la protección cubre el auto-bloqueo. El auto-borrado no tiene un guard equivalente, y tampoco hay una regla que impida dejar el sistema sin ningún administrador activo. Son dos riesgos declarados; la corrección es la misma comparación de id en el borrado y una validación de “último admin” antes de cambiar el rol.

### A3. Explícame el modo “ver como”. ¿No es una puerta trasera?

Es una herramienta de soporte: el administrador entra a la sesión de un usuario para reproducir un problema sin pedirle la contraseña. Tres controles la acotan:

1. Solo un administrador puede iniciarla, y es POST con CSRF.
2. Guarda el id del administrador original en `$_SESSION['admin_antes_suplantar']`.
3. Mientras esa clave existe, `index.php` bloquea **cualquier POST** salvo `restaurarSesion`. Es decir, la sesión suplantada es de solo lectura.

**Evidencia:** `UsuarioControlador::verComo()`, `publico/index.php` (guard de suplantación), `VERIFICACION_FINAL_DRY_RUN.md` §4.5 (un POST a `guardados/guardar` durante la suplantación devolvió 302 sin insertar filas).

**Límite:** la suplantación no se registra hoy en `logs_actividad` al iniciar ni al terminar. Es una deuda de auditoría concreta; la corrección es una línea en cada método.

**Trampa frecuente:** “¿Y si el admin suplantado cambia la contraseña?” No puede: es POST, y los POST están bloqueados durante la suplantación.

### A4. ¿Por qué el borrado de torneos es físico?

Porque el torneo arrastra encuentros, posiciones e inscripciones. Un borrado lógico dejaría esas filas apuntando a un torneo “invisible” y obligaría a filtrar `deleted_at` en cada consulta. Para los torneos que deben quedar consultables existe la sección Historial, que conserva el registro. La decisión es explícita: se borra de verdad, con confirmación en la interfaz y registro en auditoría.

**Evidencia:** `TorneoAdminControlador::eliminar()`, tabla `logs_actividad`.

**Límite, y es importante:** hoy `eliminar()` toma el id por `$_GET` y no exige POST ni CSRF. La confirmación que ve el usuario es un `confirm()` del navegador, que cualquiera puede saltear; no es un control de servidor. Eso es un defecto real, no una decisión de diseño. Está declarado como deuda y la corrección es acotada: mover el id al cuerpo del POST y validar el token.

### A5. ¿Por qué hay borrado lógico en usuarios y físico en torneos?

Porque el usuario tiene historial que no debe perderse: inscripciones, equipos, posiciones. El torneo, en cambio, es una entidad operativa que se puede archivar en Historial. La regla no es “siempre lógico” ni “siempre físico”: se elige según qué datos dependen de la entidad y qué obligación de conservación existe.

### A6. ¿Cómo se modera un torneo desde el panel?

`TorneoAdminControlador::index` filtra por juego, estado y orden; `editar` y `actualizar` permiten modificarlo. El administrador puede intervenir un torneo de cualquier organizador, que es justamente la diferencia con el rol organizador, limitado a sus propios torneos.

**Evidencia:** `TorneoAdminControlador.php`; en el dry run, el organizador que intentó administrar el torneo ajeno 1200 recibió 302 (control de propiedad correcto).

### A7. ¿Se pueden crear administradores desde el panel?

**No.** El panel lista usuarios administrativos, permite editar datos y cambiar el rol, pero no tiene formulario de alta de administrador. Es una funcionalidad planificada y no implementada. La respuesta correcta es reconocerlo, no defenderlo: “el alta de administradores quedó fuera del alcance de esta entrega; el cambio de rol sí está implementado”.

**Evidencia:** `UsuarioControlador::actualizarUsuario()` actualiza nombre, email y `rol_id`; no existe acción de creación. La matriz de roles del proyecto lo listaba como ADM-01; la implementación no lo cubre.

### A8. ¿Cómo se monitorean los errores?

`ManejadorErrores` captura excepciones y errores, calcula un hash MD5 del error para deduplicar, inserta o incrementa la ocurrencia en `errores_sistema` y devuelve una página 500 genérica, sin traza y sin salida parcial. El panel de Monitoreo de Errores lee esa tabla.

**Evidencia:** `core/ManejadorErrores.php`; dry run §4.6: 500 controlado, sin stack trace, registro eliminado después de la prueba.

**Límite:** el hash MD5 es para agrupar, no para seguridad. No hay alertas automáticas cuando aparece un error nuevo; el panel es de consulta.

### A9. ¿Qué registra la auditoría y qué no?

`logs_actividad` registra usuario, acción, descripción, IP y user-agent. Hoy se escribe en login, logout, guardado de política de contraseña, actualización de torneo y borrado de torneo.

**Límite declarado:** bloquear usuario, borrar usuario, cambiar rol y las dos puntas de la suplantación **no** se registran. La tabla `auditoria_cambios` existe en el esquema pero no tiene uso en el código. No voy a decir que la auditoría es forense: es una auditoría de acciones seleccionadas.

**Evidencia:** `ayudantes/Auditorias.php`; búsqueda de `Auditoria::registrar` en el código; `auditoria_cambios` sin referencias PHP.

### A10. ¿La política de contraseña se aplica de verdad?

Se aplica en el registro: `PoliticaContrasena::validarPassword()` valida longitud, mayúscula, minúscula, número y símbolo según la configuración. El panel permite editar esos parámetros.

**Límite, y hay que decirlo con precisión:** `expiracion_dias` e `historial_cantidad` se guardan y se muestran, pero **no se aplican**; no existe flujo de cambio de contraseña ni uso de `historial_contrasenas`. La política es efectiva en el alta, no en el ciclo de vida completo.

### A11. ¿Qué hace “Gestión de Módulos”?

Activa o desactiva los formatos de torneo disponibles: liga, eliminación y suizo, sobre `formatos_torneo.activo`. No es un sistema de feature flags general; es específico de formatos. Decirlo así evita una sobreventa.

**Evidencia:** `FormatoControlador.php`.

### A12. ¿Para qué sirven las alertas globales?

El administrador envía un mensaje a una audiencia (`todos`, `jugador`, `organizador`), que se materializa como filas en `notificaciones`, una por usuario. No hay envío de email real; es una notificación interna.

**Límite:** si la pregunta es “¿esto escala a 10.000 usuarios?”, la respuesta es que hoy inserta fila por usuario y no está pensado para ese volumen.

### A13. ¿Por qué el dashboard muestra esos indicadores y no otros?

Muestra organizadores totales, torneos activos, jugadores, equipos y los últimos cinco de cada uno. La lógica es “qué necesita saber alguien que entra a operar hoy”, no “qué se puede contar”. Un dashboard con veinte métricas se vuelve decorativo.

**Evidencia:** `AdminControlador::dashboard()`.

### A14. ¿Qué pasa si dos administradores editan el mismo registro?

No hay bloqueo optimista ni control de versión: gana el último que guarda. Es una limitación conocida de un MVP con un panel de baja concurrencia. La solución correcta sería una columna de versión y una validación en el `UPDATE`; no está implementada.

### A15. ¿Cómo se prueba que el panel funciona?

Con la verificación de rutas: 18 de 18 rutas administrativas respondieron 200, incluida la edición de juego con persistencia de categoría. Además hay pruebas unitarias del panel: bloquear usuario, gestionar catálogo, gestionar errores, actualizar políticas, dashboard de torneos activos y alerta global.

**Evidencia:** `VERIFICACION_FINAL_DRY_RUN.md` §4.2; `tests/unitarias/AdminTest.php`.

**Límite:** 7 pruebas en total en el proyecto. No hay pruebas end-to-end ni integración continua.

---

## 6. Banco de preguntas — Seguridad de la aplicación

### B1. ¿Cómo se autoriza una acción?

En el servidor. El menú oculta opciones, pero eso es cosmético. Cada controlador protegido llama a `Sesion::validarAdmin()`, `validarOrganizador()` o `validarJugador()`, que verifican el rol en sesión y redirigen al login si no corresponde. La ocultación del menú es usabilidad; el guard es seguridad.

**Evidencia:** `ayudantes/Sesion.php`, constructores de los controladores por rol.

**Matiz honesto:** `UsuarioControlador` no valida en el constructor, a propósito, para permitir `restaurarSesion` durante la suplantación; valida en cada método. Es una decisión deliberada y está documentada.

### B2. ¿Qué es CSRF y cómo lo resuelven?

Cross-Site Request Forgery: un sitio externo hace que el navegador del usuario envíe una petición autenticada. La defensa es el patrón Synchronizer Token: se genera un token con `random_bytes(32)`, se guarda en sesión, se incrusta en el formulario y se compara con `hash_equals` en el POST. Si no coincide, 419.

**Evidencia:** `Sesion::generarTokenCSRF()`, `validarTokenCSRF()`.

**Límite real:** quedan dos acciones que cambian estado por GET y sin token: `torneoAdmin/eliminar` y `juego/cambiarEstado`. Son defectos, no decisiones. Están declarados y la corrección es acotada.

**Cómo decirlo:** “La política es POST + token para todo lo que cambia estado. Tenemos dos excepciones que incumplen la política; las conocemos y están en la lista de corrección.”

### B3. ¿Por qué no alcanza con validar en JavaScript?

Porque el cliente no es confiable: cualquiera puede desactivar JavaScript, modificar el HTML o enviar la petición con `curl`. Toda regla que protege datos tiene que estar en el servidor. JavaScript mejora la experiencia; no protege.

### B4. ¿Cómo previenen inyección SQL?

Con PDO y sentencias preparadas en todas las consultas que reciben datos. Los valores van por parámetros, no concatenados. Además, el usuario de la aplicación tiene solo DML (SELECT, INSERT, UPDATE, DELETE), no DDL ni privilegios administrativos, así que un fallo de inyección no podría alterar el esquema.

**Evidencia:** `modelos/*`, uso de `prepare()`/`execute()`; DCL en `Documentos_Juan/08_DCL_Seguridad_y_Permisos.md`.

**Matiz:** hay que revisar si alguna consulta arma SQL dinámico con orden o filtros. Los filtros de orden del panel se resuelven con listas permitidas, no con concatenación libre; si el jurado pregunta, mostralo.

### B5. ¿Cómo previenen XSS?

Dos capas: escapado de salida con `htmlspecialchars` en las vistas y una CSP con `default-src 'self'`.

**Límite honesto:** la CSP incluye `'unsafe-inline'` para scripts y estilos, así que no es una CSP estricta. Es una defensa parcial, y decirlo es mejor que venderla como completa. La política estricta requeriría mover los estilos y scripts inline a archivos y usar nonces.

**Evidencia:** `core/CabecerasSeguridad.php`.

### B6. ¿Cómo se almacenan las contraseñas?

Con `password_hash()` y `PASSWORD_BCRYPT`. Nunca en texto plano, nunca con MD5 o SHA1. La verificación es `password_verify()`. El costo de bcrypt hace que un ataque de diccionario offline sea caro.

**Evidencia:** `Usuario::registrarUsuario()`, `SeguridadAuth`.

**Nota:** las contraseñas de demo del README son públicas a propósito y de prueba; no son un ejemplo de gestión de secretos en producción.

### B7. ¿Por qué limitar por IP y no por usuario?

Porque frena el ataque automatizado antes de tocar la cuenta y no revela si el usuario existe. Cinco fallos en cinco minutos bloquean la IP.

**Límite y tradeoff:** detrás de NAT, varios usuarios comparten IP y un atacante puede bloquear a un tercero. Es el costo de una defensa simple; la alternativa es combinar IP con usuario y usar captcha o backoff progresivo, que no está implementado. El desbloqueo está documentado como procedimiento operativo (`TRUNCATE intentos_login` o borrar por IP).

**Evidencia:** `SeguridadAuth`, tabla `intentos_login`; `COMANDOS_DOCKER.md` §5.

### B8. ¿Cómo manejan la sesión?

Cookie con `HttpOnly` (no accesible desde JavaScript), `SameSite=Lax` (mitiga CSRF en navegación cruzada), `Secure` cuando hay HTTPS, expiración a los 30 minutos de inactividad, y `session_regenerate_id(true)` al autenticar para evitar fijación de sesión.

**Límite:** en la demo no hay HTTPS, así que `Secure` no se activa. En producción es obligatorio.

**Evidencia:** `Sesion.php`.

### B9. ¿Qué cabeceras de seguridad envían y para qué?

- `X-Frame-Options: SAMEORIGIN` — evita clickjacking desde otro origen.
- `X-Content-Type-Options: nosniff` — evita que el navegador adivine tipos MIME.
- `Referrer-Policy: strict-origin-when-cross-origin` — limita la fuga de URL a terceros.
- `Permissions-Policy` — desactiva cámara, micrófono y geolocalización.
- CSP `default-src 'self'` — restringe orígenes.

**Evidencia:** `core/CabecerasSeguridad.php`.

### B10. ¿Cómo saben que las carpetas internas no se pueden descargar?

Porque el document root de Apache es `codigo_fuente/publico`. Todo lo demás queda fuera del árbol servido. Se verificó por HTTP: `/configuracion/`, `/modelos/`, `/vistas/`, `/core/`, `/tests/`, `/vendor/`, `/.git/` y `/composer.json` devuelven 404, mientras que los assets legítimos devuelven 200.

**Evidencia:** `docker/apache-vhost.conf`, dry run §4.7.

### B11. ¿Por qué el usuario de base de datos no es root?

Porque el principio de mínimo privilegio: la aplicación necesita leer y escribir datos, no crear tablas ni usuarios. `ascend_app` tiene SELECT/INSERT/UPDATE/DELETE; el usuario de auditoría tiene solo lectura sobre las tablas de logs. Si la aplicación se compromete, el daño está acotado al DML.

**Evidencia:** `Documentos_Juan/08_DCL_Seguridad_y_Permisos.md`.

**Límite:** el `docker-compose.yml` define la contraseña en el entorno. Para producción corresponde un gestor de secretos; está declarado.

### B12. ¿Qué pasa si alguien intenta entrar a una ruta que no existe?

`index.php` valida contra la lista blanca de controladores y acciones. Si no está, 404. Eso reduce la superficie de ataque: no hay “controladores por convención” que se puedan invocar por nombre.

**Evidencia:** `publico/index.php`, `$rutasPermitidas`.

### B13. ¿Cómo se protege la suplantación de un POST malicioso?

Con el guard de `index.php`: si `admin_antes_suplantar` está definida, todo POST se rechaza salvo `usuario/restaurarSesion`. Se probó: un POST a `guardados/guardar` devolvió 302 y no insertó filas.

### B14. ¿Es seguro el sistema?

La respuesta honesta: es razonablemente seguro para una entrega académica y tiene controles reales en autenticación, autorización, CSRF, sesión, cabeceras e inyección SQL. No es un sistema listo para producción: falta HTTPS, gestión de secretos, recuperación de contraseña, auditoría completa y corregir los dos GET con efecto de estado.

**No digas “es seguro”. Decí “tiene estos controles y estas brechas declaradas”.**

### B15. ¿Por qué no usaron un framework de seguridad o un WAF?

Porque el objetivo era demostrar la implementación y el costo de un WAF no se justificaba para una demo local. Un framework habría dado controles probados, pero también habría ocultado decisiones que había que poder explicar. Es un tradeoff consciente, no una omisión.

### B16. ¿Cómo se validan los datos de entrada?

Cada controlador valida tipos y pertenencia antes de llamar al modelo. Por ejemplo, los ids se castean a entero, los filtros se enlazan como parámetros y el orden se resuelve con una lista permitida (`switch`), no concatenando el valor que llega por URL. La base refuerza con `CHECK` y claves foráneas, de modo que la validación no depende de una sola capa.

---

## 7. Banco de preguntas — Base de datos

### C1. ¿Por qué 41 tablas?

Porque el modelo separa identidad, perfiles por rol, catálogos, operación de torneos, resultados y observabilidad. Las 41 tablas incluyen: usuarios y tres tablas de perfil (herencia por tabla de clase), juegos y sus tablas puente de modalidades y formatos, torneos y sus encuentros/posiciones/inscripciones, equipos y miembros, guardados, notificaciones, logs, errores, intentos de login y políticas.

**Evidencia:** `001_ascend.sql` (44 sentencias `CREATE TABLE`, 41 tablas únicas); `README.md`.

**Cómo contarlo:** no enumeres 41 tablas. Agrupá en cinco bloques: identidad, catálogo, operación, resultados, observabilidad.

### C2. ¿El modelo está en tercera forma normal?

Sí, en lo esencial: cada tabla tiene una clave primaria, los atributos dependen de la clave completa y no hay dependencias transitivas. Los casos que suelen generar discusión:

- **Perfiles por rol en tablas separadas** (`perfiles_organizador`, `perfiles_jugador`, …): es herencia por tabla de clase. Evita columnas nulas en `usuarios` y permite atributos propios de cada rol.
- **Tablas puente** (`juego_modalidades_permitidas`, `juego_formatos_permitidos`): resuelven N:M sin listas separadas por comas.
- **`guardados` polimórfica**: una tabla con `tipo` (`torneo` o `equipo`) y un `CHECK` que restringe los valores, más una clave única por usuario/tipo/objeto.

**Evidencia:** `001_ascend.sql`; decisión #33 de `Documentacion_BackEnd.md`.

**Matiz:** `guardados` es intencionalmente polimórfica y por eso no puede tener una FK directa a dos tablas distintas. Se compensa con `CHECK` y unicidad; la integridad referencial plena requeriría dos tablas o triggers. Es una decisión con costo conocido.

### C3. Dame un ejemplo concreto de 3FN.

En `torneos` no se guarda el nombre del juego ni el email del organizador: se guardan `juego_id` y `organizador_id`. Si se guardara el nombre del juego, habría dependencia transitiva y una actualización del catálogo obligaría a reescribir torneos. Con la FK, se cambia en un solo lugar.

### C4. ¿Cómo se evita un torneo duplicado o una inscripción doble?

Con claves únicas y validaciones en el modelo. Por ejemplo, la inscripción de un participante a un torneo tiene unicidad por torneo y participante; los guardados tienen unicidad por usuario, tipo y objeto. La base es la última línea: aunque el controlador fallara, el `INSERT` duplicado no entra.

### C5. ¿Cuántas claves foráneas y restricciones CHECK hay?

64 declaraciones de clave foránea y 11 `CHECK` en `001_ascend.sql`. Los `CHECK` restringen estados, tipos y valores de `guardados`; las FK evitan huérfanos.

**Evidencia:** conteo sobre `001_ascend.sql`.

**Precisión:** son declaraciones, no necesariamente 64 relaciones distintas; algunas tablas declaran más de una FK. Decir “64 declaraciones” es exacto.

### C6. ¿Por qué seis migraciones y no una?

Porque la base evolucionó: `001` es el esquema, `002` los datos base, `003` la semilla relacional de QA, `004` las tablas de autenticación, `005` correcciones idempotentes y `006` una ampliación idempotente de la semilla. Separarlas permite aplicar solo lo que falta en una base existente.

**Límite:** `003` no es idempotente y está pensada para base limpia. Sobre una base existente se aplica `005`, y `006` si ya tenía `003`. Está documentado en el README y en la verificación.

**Trampa:** “¿Y si aplico `001` sobre la base en uso?” No: contiene `DROP TABLE` y borra datos. Es la advertencia explícita del README.

### C7. ¿Por qué los datos de demo son 51 usuarios y 18 torneos?

Porque la semilla está pensada para probar todos los formatos sin crear nada a mano: 23 equipos, 81 participantes, 87 encuentros y 76 filas de posiciones, con una liga de 8 equipos (1212), eliminación directa de equipos (1213), suizo individual (1214), liga individual de 10 (1215), eliminación individual (1216) y un torneo abierto con inscripciones en todos los estados (1217).

**Evidencia:** `README.md`; `006_seed_qa_ampliado.sql`.

### C8. ¿Por qué no usaron un ORM?

Porque el proyecto es académico y el objetivo incluía demostrar el acceso a datos con PDO y SQL explícito. Un ORM habría acelerado el CRUD, pero habría ocultado las consultas y las decisiones de integridad. El costo es más código repetitivo; el beneficio es control y explicabilidad.

### C9. ¿Cómo respaldan la base?

En el contexto de Docker, el volumen nombrado `sgdm_datos` conserva los datos al recrear contenedores. Para respaldo real, el toolkit de sistemas operativos incluye `mysqldump` + `rsync` por SSH, con `SHA256SUMS`, manifiesto, verificación local y remota, retención de 14 días y poda remota desactivada por defecto.

**Límite:** el respaldo y la restauración **no se ejecutaron** en un servidor real; están validados sintácticamente. Un respaldo no está verificado hasta que se restaura, y eso hay que decirlo.

**Evidencia:** `administracion_sistemas/documentacion/04_RESPALDOS_REMOTOS.md`.

### C10. ¿Cómo replican la base?

Replicación asíncrona MySQL con binlog en formato ROW, `binlog_row_image=FULL`, `server-id` distinto, esclavo en `read_only=ON` y expiración de binlog a 7 días. La promoción es manual.

**Límite:** el RPO no es cero; con replicación asíncrona se puede perder la última transacción si el maestro cae. Y no se probó una promoción real.

**Evidencia:** `05_REPLICACION_MYSQL.md`.

### C11. ¿Qué pasa si la base se cae?

El contenedor web depende de la base saludable al arrancar, pero no hay reintento automático a nivel de aplicación: una consulta fallida genera un error controlado y se registra. En producción correspondería un pool con reintentos y un healthcheck de aplicación; no está implementado.

### C12. ¿Cómo escala esto?

Verticalmente, sin problema para el volumen de una demo. Horizontalmente, el cuello es la base única y no hay un almacén compartido de sesiones: cada servidor PHP manejaría las suyas. Para escalar habría que separar sesiones a un almacén compartido, añadir réplicas de lectura y cachear catálogos. No está hecho y no se pretende que lo esté.

### C13. ¿Por qué `guardados` tiene un `CHECK` y no dos tablas?

Porque la operación es idéntica para torneo y equipo: guardar una referencia para consultarla después. Dos tablas duplicarían la lógica y las consultas. El `CHECK` restringe el tipo y la clave única evita duplicados. El costo es que no hay FK directa; se acepta a cambio de simplicidad.

### C14. ¿Hay vistas, triggers o procedimientos?

No. Todo el cálculo vive en PHP. Es una decisión de mantenibilidad: la lógica es visible y testeable en el código, y la base se limita a almacenar e imponer integridad.

---

## 8. Banco de preguntas — Docker y operación

### D1. ¿Por qué Docker y no XAMPP?

Porque Docker da un entorno reproducible: la misma versión de PHP, Apache y MySQL en cualquier máquina, sin depender de lo que esté instalado. XAMPP fue el entorno de desarrollo inicial; el problema fue justamente la diferencia de esquema entre entornos, que se documentó y se resolvió.

**Evidencia:** decisión #47 de `Documentacion_BackEnd.md` (XAMPP 39 tablas vs Docker 26 en un momento dado; desincronización de esquema).

### D2. ¿Qué servicios levanta el stack?

Cuatro: web (PHP 8.2 + Apache, puerto 8080), db (MySQL 8.0, puerto 3307), phpMyAdmin (8081, solo host) y Grafana (3001, solo host). Red bridge `sgdm_red` y volumen `sgdm_datos`.

**Evidencia:** `docker/docker-compose.yml`.

### D3. ¿Por qué las imágenes están fijadas por versión?

Porque `latest` cambia sin aviso y rompe la reproducibilidad. `php:8.2.29-apache`, `mysql:8.0.46`, `phpmyadmin:5.2.2` y `grafana:11.3.0` son versiones concretas. Si hay que actualizar, se actualiza a propósito y se prueba.

### D4. ¿Cómo se asegura que la web no arranque antes que la base?

Con un healthcheck en MySQL y `depends_on: condition: service_healthy` en el servicio web. El healthcheck tiene `start_period: 30s` porque en equipos lentos el primer arranque tarda.

**Evidencia:** `docker-compose.yml`.

**Anécdota útil:** el `depends_on` sin condición fue un bug real que se encontró y se corrigió (decisión #46).

### D5. ¿Qué pasa con los datos al recrear contenedores?

El volumen nombrado `sgdm_datos` persiste. `down` no borra datos; `down -v` sí, y está advertido en la documentación. Es la diferencia entre reiniciar y destruir.

### D6. ¿Por qué los scripts de inicialización solo corren una vez?

Porque MySQL ejecuta `/docker-entrypoint-initdb.d` solo cuando el volumen está vacío. Es comportamiento del entrypoint, no una configuración nuestra. Por eso cambiar un `.sql` no lo reaplica sobre una base existente: hay que recrear el volumen (destructivo) o aplicar el script a mano.

**Evidencia:** `COMANDOS_DOCKER.md` §4; README.

### D7. ¿Cómo se aísla el document root?

El vhost de Apache apunta a `/var/www/html/publico`. El resto del código queda fuera del árbol servido. Se verificó con 404 en las carpetas internas.

**Evidencia:** `docker/apache-vhost.conf`, dry run §4.7.

### D8. ¿Por qué phpMyAdmin y Grafana están en 127.0.0.1?

Porque son herramientas de operación, no parte de la aplicación. Publicarlas en la red las expondría sin necesidad. El bind a `127.0.0.1` las deja accesibles solo desde el host.

### D9. ¿Cómo se suben imágenes y cómo se resuelven los permisos?

El contenedor crea las carpetas de subida (`img/perfiles/subidos`, `img/juegos/subidos`, `img/torneos/subidos`) y las asigna a `www-data` al iniciar. En un bind mount de Windows puede quedar otro propietario; el procedimiento de `chown` está documentado.

**Evidencia:** `docker-compose.yml` (command de arranque), `COMANDOS_DOCKER.md` §8.

### D10. ¿Cómo diagnostican un problema en el stack?

`docker compose ps` para estado, `logs -f web` y `logs -f db` para errores, y consola MySQL con `exec`. El healthcheck y el `start_period` explican la mayoría de los falsos negativos en el primer arranque.

### D11. ¿Esto es producción?

No. Es una demo reproducible. Para producción faltan: HTTPS, secretos fuera del compose, usuario no root en la imagen, límites de recursos, backups automáticos verificados, monitoreo real y un reverse proxy. Decirlo primero evita que la pregunta se vuelva una emboscada.

### D12. ¿Para qué está Grafana si no hay dashboards?

Está provisionado como servicio, pero sin datasource ni dashboards cargados. Es una base para observabilidad, no observabilidad implementada. Es una limitación declarada.

### D13. ¿Cómo se actualiza la aplicación?

Con `git pull` y recreación del contenedor web; el código está en bind mount, así que un cambio de PHP se refleja sin reconstruir la imagen. Cambios en el Dockerfile o en dependencias requieren `--build`.

### D14. ¿Qué pasa si el puerto 8080 está ocupado?

El stack no levanta. Se cambia el mapeo en el compose o se libera el puerto. Es operación básica, pero conviene tenerlo claro porque es la primera falla que aparece en una demo.

---

## 9. Banco de preguntas — Sistemas operativos y toolkit

### E1. ¿Qué pedía la materia y qué entregaron?

Un conjunto modular de scripts para administrar un servidor Linux: usuarios y permisos, servicios, respaldos, replicación, auditoría, monitoreo de logs y tareas programadas. Se entregaron 32 scripts organizados en 9 módulos, con un menú que los invoca y documentación por módulo.

**Evidencia:** `administracion_sistemas/README.md`.

### E2. ¿Por qué Bash y no Python?

Porque el objetivo es administración de sistemas en Linux: los comandos nativos (`systemctl`, `rsync`, `mysqldump`, `auditctl`, `logrotate`) ya son la interfaz, y Bash los orquesta sin una capa extra. Python habría sido más cómodo para lógica compleja, pero habría añadido una dependencia y ocultado el uso directo de las herramientas del sistema.

### E3. ¿Cómo se aplica el mínimo privilegio?

Con seis cuentas nominales y sudoers acotados: `admin_sistema`, `dba`, `webadmin`, `auditor`, `respaldo` y `operador`. Cada grupo puede ejecutar solo los comandos de su función. El auditor no tiene sudo. El grupo `docker` se excluye deliberadamente porque equivale a root.

**Evidencia:** `documentacion/02_USUARIOS_ROLES.md`.

**Límite:** los sudoers están diseñados y documentados, pero no se aplicaron en un servidor real en esta entrega.

### E4. ¿Cómo evitan que un operador detenga la base por error?

El grupo `dba` puede reiniciar el servicio pero no detenerlo; `%respaldo` y `%operador` tienen `NOPASSWD` solo para los scripts de su alcance. La lista de servicios manejables está en `config/servicios.conf`: `apache2`, `mariadb`, `mysql`, `ssh`, `cron`, `docker`. Fuera de esa lista, el script no actúa.

**Evidencia:** `config/servicios.conf`, `02_USUARIOS_ROLES.md`.

### E5. ¿Cómo funciona la auditoría del sistema?

Con `auditd` y reglas que vigilan archivos sensibles: `/etc/passwd`, `/etc/shadow`, `/etc/group`, `/etc/gshadow`, sudoers, sshd, apache2, mysql, `/etc/ascend-admin`, `/var/www/ascend`, cron, systemd, respaldos y logs; además, `execve` para UID ≥ 1000. Las reglas están en `auditoria/reglas_ascend.rules`.

**Límite:** no se usa el flag inmutable `-e 2`, así que un root comprometido podría modificar las reglas. Es una decisión consciente para no bloquear la operación durante la puesta a punto; en producción se activa al final.

### E6. ¿Cómo se hace un respaldo y cómo sé que sirve?

`mysqldump` + `rsync` por SSH; el archivo se firma con `SHA256SUMS` y se acompaña de un manifiesto; hay verificación local y remota; retención de 14 días; la poda remota está desactivada por defecto; cron a las 02:30. La regla es que un respaldo no está verificado hasta que se restaura, y la documentación lo dice.

**Límite:** no se ejecutó una restauración real en esta entrega.

### E7. ¿Cómo replican y qué pasa si el maestro cae?

Replicación asíncrona con binlog ROW y `read_only=ON` en el esclavo. Si el maestro cae, la promoción es manual y hay pérdida posible de la última transacción (RPO > 0). Es la consecuencia de elegir asíncrono: más simple y con menos impacto en el maestro, a cambio de RPO no cero.

### E8. ¿Cómo se mata un proceso peligroso sin equivocarse?

El script de procesos exige escribir `MATAR` para confirmar. Es una barrera deliberada contra el error humano: el comando destructivo no se ejecuta por un Enter accidental.

### E9. ¿Qué se validó realmente del toolkit?

Validación sintáctica: `bash -n` en 32 de 32 scripts y `--help` en 24 de 24 comandos. No se ejecutaron instaladores, respaldos, replicación ni cambios de usuarios sobre un servidor real.

**Frase exacta:** “Está validado sintácticamente, no funcionalmente en un servidor real. Es la limitación principal de esta entrega y está declarada en el README del módulo.”

**No decir:** “está probado y funciona”. Eso sería falso.

### E10. ¿Por qué no lo probaron en una VM?

Porque el alcance de la entrega y las restricciones de la consigna no incluían ejecutar cambios administrativos sobre un anfitrión real, y una VM desechable no estaba disponible en el entorno de trabajo en ese momento. La decisión fue entregar con validación estática y declararlo, en lugar de afirmar pruebas que no se hicieron. Si el jurado ofrece una VM, la respuesta es sí: el primer paso es una VM desechable con snapshot.

### E11. ¿Cuál sería el primer paso para ponerlo en producción?

1. VM desechable con snapshot.
2. Crear las seis cuentas y aplicar sudoers.
3. Ejecutar el módulo de usuarios y verificar con `sudo -l`.
4. Instalar y configurar `auditd` con las reglas, sin `-e 2` al principio.
5. Probar un respaldo y **restaurarlo** en una base de prueba.
6. Configurar la replicación y simular una caída del maestro.
7. Recién entonces activar `-e 2` y la poda remota.

### E12. ¿Qué pasa si un script falla a mitad de camino?

Los scripts validan prerequisitos y cortan con un mensaje de error y `exit 1` en los pasos críticos; no usan `set -e` global. No hay transaccionalidad real en administración de sistemas: si falla a mitad, hay que revisar el estado. Por eso la recomendación es snapshot antes de ejecutar y logs de cada paso.

### E13. ¿Cómo se evita que un script borre algo que no debe?

Con rutas explícitas, listas permitidas de servicios y confirmación por palabra clave en las operaciones destructivas. La poda remota está desactivada por defecto. Aun así, la regla operativa es snapshot y revisión del log antes de repetir.

### E14. ¿Por qué hay 32 scripts y no uno solo?

Porque cada módulo tiene un riesgo y un dueño distinto, y un script monolítico sería más difícil de auditar y de limitar por sudoers. La modularidad permite que `%respaldo` ejecute solo lo de respaldo, sin acceso al resto.

### E15. ¿Qué relación tiene esto con la aplicación?

La aplicación es el servicio que se administra. El toolkit cubre el ciclo de vida del servidor: usuarios, servicios, respaldos, replicación, auditoría y logs. Son dos entregas que se complementan: una demuestra el software, la otra demuestra cómo se opera.

---

## 9.5. Preguntas de gestión del proyecto

Estas salen de `Documentos_Juan/DocumentacionProgramacion.md`. Es el documento de planificación y control, no el estado de entrega.

### G1. ¿El alcance planificado coincide con lo entregado?

En lo grueso, sí: tres roles, tres formatos, fixtures, posiciones y rankings, catálogo público, auditoría y despliegue con Docker. Las exclusiones están explícitas: pagos, redes sociales, IA embebida, arbitraje automático, apuestas, venta de entradas y control de acceso físico.

**Las diferencias hay que decirlas:**

- El documento habla de nueve disciplinas; la semilla entrega **cinco juegos** y el catálogo permite crear más desde el panel.
- El documento habla de “CRUD completo de usuarios”, y la entrega no tiene alta de administradores.
- El documento habla de “registro de auditoría de operaciones”, y la auditoría cubre acciones seleccionadas.

El documento es de planificación; el estado real es el del `README.md` y el dry run. Donde difieren, manda el sistema.

### G2. ¿Cómo controlaron los cambios?

Con un plan de alcance y un procedimiento de control: toda funcionalidad nueva pasa por una solicitud de cambio y se evalúa por impacto en tiempo y código. La regla que sostuvimos fue no agregar alcance para tapar un problema: primero se corrige lo construido.

**Evidencia:** secciones 2.5, 3.2 y 3.3 de `DocumentacionProgramacion.md`.

### G3. ¿Cuáles eran los riesgos y cuáles se materializaron?

Los tres altos eran conflictos de merge, abandono de un integrante y fixtures incorrectos. Los conflictos de merge **sí** se materializaron, sobre todo en `Sesion.php` y `Torneo.php`; se resolvieron con merges frecuentes y pruebas después de cada integración. La incompatibilidad de versiones de PHP se resolvió con Docker. La pérdida de datos se mitigó con el esquema versionado y el volumen; los fixtures se probaron con 4, 5, 8 y 16 participantes en los formatos que lo admiten, y el suizo quedó restringido a cantidades pares de 4 a 64.

**Evidencia:** matriz de riesgos R-01 a R-07 y lecciones aprendidas.

### G4. ¿Qué aprendieron?

Tres cosas concretas: los conflictos se reducen con propietarios de archivo y merges semanales; el motor de torneos se subestimó y necesita un colchón de estimación; y la herencia por tabla de clase es más limpia que las columnas nulas, pero cuesta más joins. No es una lección genérica: cada una cambió una práctica.

### G5. ¿Por qué tanta documentación de gestión?

Porque la evaluación incluye ingeniería de software y la documentación ordena las decisiones: alcance, riesgos, cambios, calidad y trazabilidad. No reemplaza el código; lo explica. La prueba es que cada decisión técnica de este guion se puede rastrear a un artefacto.

### G6. ¿La lista de pendientes del documento está vigente?

**No.** Es un corte de planificación para la tercera entrega; varios ítems ya se completaron después (Docker, respaldos, pruebas, auditoría). No la cites como estado actual: para eso están el `README.md` y `VERIFICACION_FINAL_DRY_RUN.md`. Si el jurado la lee, aclaralo antes de que la use como prueba de algo que falta.

---

## 10. Tabla de decisiones: qué elegimos, qué descartamos y a qué costo

Usala para responder “¿por qué así y no de otra forma?”. Una fila por respuesta.

| Decisión | Alternativa descartada | Por qué | Costo / límite |
| --- | --- | --- | --- |
| MVC propio sin framework | Laravel / Symfony | Control y explicabilidad; el objetivo era demostrar la implementación | Más código repetitivo, sin ecosistema |
| Front controller + lista blanca | Enrutamiento por convención | Superficie de ataque mínima; ruta desconocida = 404 | Hay que registrar cada ruta |
| PDO con preparadas | ORM, o SQL concatenado | SQL explícito y defensa directa contra inyección | Verbosidad |
| Singleton de conexión | Conexión por consulta / DI | Simplicidad en un MVP; una sola conexión por request | Menos testeable que inyección de dependencias |
| RBAC con guard en servidor | Ocultar en el menú | El cliente no es confiable | Guards repetidos por controlador |
| bcrypt | MD5 / SHA1 | Hash lento y con sal | Costo de CPU en login |
| CSRF Synchronizer Token | Double-submit cookie | Token en sesión, comparación con `hash_equals` | Requiere incluir el token en cada formulario |
| Límite por IP en base | En memoria | Persistente y sobrevive reinicios | NAT puede bloquear a terceros |
| Sesión con timeboxing | Sesión indefinida | Limita ventana de robo de cookie | 30 min puede ser corto para algunos flujos |
| Headers + CSP | Sin cabeceras | Defensa en profundidad barata | CSP con `unsafe-inline` no es estricta |
| Docroot en `publico` | Servir todo el proyecto | Aísla código y configuración | Hay que ajustar el vhost |
| 3FN + tablas puente | Listas separadas por comas | Integridad y consultas limpias | Más joins |
| `guardados` polimórfica | Dos tablas | Una sola lógica de guardado | Sin FK directa; se compensa con CHECK |
| Herencia por tabla de clase | Rol como columna | Atributos por rol sin nulos | Más tablas y joins |
| Strategy para formatos | `if` por formato | Agregar un formato no toca los otros | Más clases |
| Migraciones 001–006 | Un script único | Evolución aplicable por partes | `003` no idempotente |
| Volumen nombrado | Bind mount de datos | Persistencia y portabilidad | `down -v` es destructivo |
| Healthcheck + depends_on | Arranque ciego | Evita que la web arranque sin base | Primer arranque más lento |
| Imágenes fijadas | `latest` | Reproducibilidad | Actualización manual |
| Backups con SHA256 | Copia simple | Verificación de integridad | No reemplaza la prueba de restauración |
| Replicación asíncrona | Síncrona | Menos impacto en el maestro | RPO > 0 |
| Sudoers acotados | `sudo` total | Mínimo privilegio | Más configuración y mantenimiento |
| `auditd` sin `-e 2` | Inmutable desde el inicio | No bloquea la puesta a punto | Un root comprometido puede editar reglas |

---

## 11. Límites declarados (frases exactas)

Decilas así. No las maquilles.

1. **Alta de administradores:** “El panel permite listar, editar y cambiar el rol, pero no crear administradores. Es una funcionalidad planificada que quedó fuera del alcance.”
2. **Auditoría:** “Registramos login, logout, política de contraseña y cambios de torneo. Bloqueo, borrado y cambio de rol de usuarios no se auditan; la tabla `auditoria_cambios` está en el esquema pero sin uso.”
3. **Política de contraseña:** “Se aplica en el registro. La expiración y el historial están configurados pero no se aplican, porque no hay flujo de cambio de contraseña.”
4. **CSRF:** “La política es POST + token. Quedan dos acciones por GET sin token: borrar torneo y cambiar estado de juego. Están identificadas para corrección.”
5. **Borrado de torneos:** “Es físico e irreversible, con auditoría. La confirmación actual es del navegador, no del servidor; el histórico se conserva en la sección Historial.”
6. **Recuperación de contraseña:** “No está implementada; el login lo informa explícitamente.”
7. **Email:** “No hay envío real de correo; las alertas son notificaciones internas.”
8. **Sistema suizo:** “Admite cantidades pares de 4 a 64 participantes; no implementa bye.”
9. **Pruebas:** “Siete pruebas unitarias y verificación de rutas. No hay pruebas end-to-end ni integración continua.”
10. **Toolkit de sistemas:** “Validado sintácticamente (`bash -n`, `--help`), no ejecutado en un servidor real. Respaldos, restauración y replicación no se probaron funcionalmente.”
11. **Grafana:** “Está como servicio, sin datasource ni dashboards.”
12. **Producción:** “Es una demo académica. Faltan HTTPS, gestión de secretos, usuario no root en la imagen y monitoreo real.”

---

## 12. Trampas y preguntas de cierre

### F1. “¿Entonces el sistema no es seguro?”

“Tiene controles reales y brechas declaradas. Seguridad no es un estado binario: es una superficie con un costo. Lo que sí puedo afirmar es qué está cubierto, qué no, y cuál sería el siguiente paso en cada caso.”

### F2. “¿Qué es lo más frágil del sistema?”

“La auditoría incompleta y los dos GET con efecto de estado. Son las dos cosas que corregiría primero, porque son acotadas y tienen impacto directo en trazabilidad y en CSRF.”

### F3. “Si tuvieras dos semanas más, ¿qué harías?”

“Primero, cerrar los dos GET con POST y CSRF y ampliar la auditoría a acciones de usuario y suplantación. Segundo, flujo de cambio de contraseña con expiración e historial. Tercero, ejecutar el toolkit en una VM y probar restauración y promoción. Cuarto, HTTPS y secretos fuera del compose. En ese orden, por impacto y costo.”

### F4. “¿Por qué no usaron CI/CD?”

“Porque el alcance era la entrega y la verificación manual documentada. Un pipeline con `php -l`, PHPUnit y un smoke test de rutas sería el siguiente paso natural; hoy las validaciones se corren a mano y quedan registradas en el informe.”

### F5. “¿Qué decisión revertirías?”

“El borrado físico de torneos por GET. La decisión de fondo —borrado físico— la sostengo; lo que revertiría es la vía de invocación. Debería ser POST con confirmación y token desde el primer día.”

### F6. “¿Cómo sé que lo que decís es cierto?”

“Todo lo que afirmo tiene un archivo o un comando detrás. Te puedo mostrar la tabla, el controlador, el compose o el informe de verificación. Lo que no está verificado lo digo como limitación, no como logro.”

### F7. “¿Qué pasa si MySQL no está disponible en la demo?”

“La aplicación muestra un error controlado y lo registra; no expone la traza. La demo tiene un plan B con video y capturas, y el stack se levanta con un comando.”

### F8. “¿Manejan datos personales?”

“Nombre, email y teléfono. No hay datos sensibles como salud o biometría. Las contraseñas están hasheadas con bcrypt. No hay política formal de retención ni borrado por solicitud del usuario; es una limitación de la entrega.”

### F9. “¿Por qué deberíamos aprobarlos?”

No respondas con adjetivos. Respondé con evidencia: “Porque el sistema funciona de punta a punta en los tres roles, está verificado por rutas y pruebas, y porque conocemos y declaramos sus límites. Lo que no está, sabemos por qué y cuánto costaría.”

### F10. “¿Quién hizo qué?”

Respondé por área, sin jerarquías: “El módulo de administración, seguridad, Docker, base de datos y el toolkit de sistemas los trabajé yo. [Compañero] trabajó [área]. La integración y la verificación fueron conjuntas.” No menciones liderazgo ni coordinación.

---

## 13. Kit de evidencia (tenelo abierto, no lo muestres de entrada)

| Si preguntan por… | Mostrar |
| --- | --- |
| Rutas y control de acceso | `codigo_fuente/publico/index.php` (`$rutasPermitidas`, guard de suplantación) |
| CSRF y sesión | `codigo_fuente/ayudantes/Sesion.php` |
| Cabeceras | `codigo_fuente/core/CabecerasSeguridad.php` |
| Errores | `codigo_fuente/core/ManejadorErrores.php` y tabla `errores_sistema` |
| Auditoría | `codigo_fuente/ayudantes/Auditorias.php` y `SELECT * FROM logs_actividad LIMIT 5` |
| Suplantación | `UsuarioControlador::verComo()` / `restaurarSesion()` |
| Esquema | `base_de_datos/migraciones/001_ascend.sql` |
| Conteos | `SELECT COUNT(*)` sobre `information_schema.tables`, `usuarios`, `torneos` |
| Docker | `docker/docker-compose.yml`, `docker/apache-vhost.conf` |
| Toolkit | `administracion_sistemas/README.md` y `documentacion/` |
| Verificación | `informes_auditoria/VERIFICACION_FINAL_DRY_RUN.md` |

**Comandos de emergencia** (si el jurado pide un número en vivo):

```bash
docker compose -f docker/docker-compose.yml ps
docker compose -f docker/docker-compose.yml exec db mysql -u root -proot123 -N -B ascend \
  -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='ascend'; SELECT COUNT(*) FROM usuarios; SELECT COUNT(*) FROM torneos;"
```

Debe devolver 41, 51, 18.

---

## 14. Frases para usar y frases para evitar

**Usar:**
- “La decisión fue X porque Y; el costo es Z.”
- “Eso está verificado en [archivo/informe].”
- “Eso no está implementado; es una limitación declarada.”
- “Si tuviera que corregirlo, el cambio es acotado: [dos pasos].”
- “Prefiero decirte que no lo probamos antes que afirmar algo que no puedo mostrar.”

**Evitar:**
- “Es totalmente seguro.”
- “Está probado” cuando solo hubo validación sintáctica.
- “Lo hizo la IA” como explicación de una decisión.
- “Eso no es mi parte” sin ofrecer el área correcta.
- Cifras redondas inventadas (“más o menos 100 usuarios”).
- Referencias a liderazgo, coordinación o jerarquía del equipo.

---

## 15. Fuentes

- `README.md` — estado de entrega, conteos, credenciales de demo, limitaciones.
- `informes_auditoria/VERIFICACION_FINAL_DRY_RUN.md` — verificación por rutas, errores, aislamiento y limitaciones.
- `Documentos_Juan/DocumentacionProgramacion.md` — alcance, riesgos, cambios, calidad y lecciones aprendidas.
- `Documentos_Juan/Documentacion_BackEnd.md` — 45+ decisiones técnicas con justificación.
- `Documentos_Juan/08_DCL_Seguridad_y_Permisos.md` — DCL y mínimo privilegio.
- `Documentos_Juan/Matriz_Roles_y_Funcionalidades.md` — matriz planificada (contrastar con código antes de citar).
- `Documentos_Juan/task_administrador.md` — alcance del módulo administrativo.
- `codigo_fuente/publico/index.php`, `ayudantes/Sesion.php`, `ayudantes/Auditorias.php`, `core/CabecerasSeguridad.php`, `core/ManejadorErrores.php`.
- `codigo_fuente/controladores/` — `AdminControlador`, `UsuarioControlador`, `TorneoAdminControlador`, `JuegoControlador`, `FormatoControlador`, `AlertaControlador`, `AuditoriaControlador`.
- `base_de_datos/migraciones/001_ascend.sql` a `006_seed_qa_ampliado.sql`.
- `docker/docker-compose.yml`, `docker/Dockerfile`, `docker/apache-vhost.conf`.
- `administracion_sistemas/README.md` y `administracion_sistemas/documentacion/01`–`05`.
- `COMANDOS_DOCKER.md` — operación, desbloqueo de IP, diagnóstico.
