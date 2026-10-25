# ASCEND — Guion de defensa oral

**Duración total:** 80 minutos · **Preparación:** 20 minutos · **Exposición:** 20 minutos · **Defensa:** 40 minutos  
**Equipo:** 4 integrantes · **Materias:** Ingeniería de software, Full stack, Bases de datos, Ciberseguridad, Sistemas operativos  
**Archivo de diapositivas:** [ASCEND_Defensa.pptx](ASCEND_Defensa.pptx)

> Regla de oro: la diapositiva es un apoyo, no un guion para leer. Cada bloque de abajo está escrito para decirse con palabras propias; si se pierden, vuelvan a la frase subrayada de la diapositiva.

---

## 1. Reparto y tiempos

| # | Diapositiva | Minuto | Quién | Objetivo |
|---|---|---|---|---|
| 1 | ASCEND | 0:00 – 1:30 | M1 | Gancho: no es solo una app, es un sistema operable |
| 2 | El problema | 1:30 – 3:00 | M1 | Instalar la tensión: sostener > organizar |
| 3 | Tres roles | 3:00 – 4:30 | M1 | Recorrido del usuario en 4 etapas |
| 4 | Demo | 4:30 – 6:00 | M2 | Ver el sistema funcionando |
| 5 | Alcance | 6:00 – 7:30 | M1 | Qué está y qué no, sin ambigüedad |
| 6 | Arquitectura | 7:30 – 9:00 | M2 | MVC, capas y Docker |
| 7 | Base de datos | 9:00 – 11:00 | M3 | 41 tablas, 6 migraciones, seed real |
| 8 | Seguridad | 11:00 – 13:00 | M4 | RBAC, CSRF, bcrypt, cabeceras |
| 9 | Toolkit de operación | 13:00 – 15:00 | M4 | La entrega de Sistemas Operativos |
| 10 | Cinco materias | 15:00 – 16:30 | M1 | Integración, no cinco proyectos pegados |
| 11 | Evidencia y límites | 16:30 – 18:00 | M1 | Lo probado y lo no probado |
| 12 | Gracias | 18:00 – 19:30 | Todos | Cierre y paso a la defensa |
| — | Buffer | 19:30 – 20:00 | Todos | Respirar, ordenar el material y ceder la palabra |

**Reparto por materia**

- **M1 — Producto, ingeniería de software y cierre.** Abre, cuenta el problema y el alcance, conecta las cinco materias y cierra. Es quien cuida el tiempo.
- **M2 — Full stack y demo.** Maneja la aplicación en vivo y explica arquitectura y flujo MVC.
- **M3 — Bases de datos.** Modelo, migraciones, seed, respaldo y replicación.
- **M4 — Ciberseguridad y sistemas operativos.** Controles de seguridad y toolkit de administración.

El reparto es de **orden de palabra**, no de autoría. En la defensa técnica de cada área responde quien la implementó; el mapa de preguntas está en `GUION_ADMINISTRADOR.md`.

Si alguien se traba, el siguiente retoma con “y eso conecta con…”. Nadie corrige al compañero en voz alta frente al jurado.

---

## 2. Antes de empezar (checklist de 20 minutos)

1. Stack levantado y verificado:
   `docker compose -f docker/docker-compose.yml ps` → `sgdm_web`, `sgdm_db` healthy.
2. Base con los números de la demo: **41 tablas, 51 usuarios, 18 torneos**.
3. Tres pestañas listas: catálogo público, sesión de jugador, sesión de organizador. **No dejar contraseñas visibles.**
4. Torneo **1217** abierto en el detalle y ranking listo para mostrar.
5. Video de 90 segundos descargado en local y capturas en una carpeta. No depender de la red.
6. Diapositivas en modo presentador, timer visible y este guion impreso.
7. Cerrar notificaciones del sistema y sesiones personales del navegador.
8. Acordar una señal discreta para avisar “quedan 2 minutos”.
9. Acordar quién responde cada área en la defensa de 40 minutos: admin/producto, full stack, datos, seguridad y sistemas operativos. El mapa está en `GUION_ADMINISTRADOR.md`.
10. Tener abiertos, sin mostrarlos todavía, el esquema de la base, el `docker-compose.yml`, un log de auditoría y una terminal con el toolkit. Se usan solo si el jurado pide evidencia.

**Plan B de demo:** si falla la red o el proyector, M2 reproduce el video y M1 sigue con la diapositiva 5. Nunca se improvisa una disculpa larga: se dice “pasamos al video” y se continúa.

---

## 3. Guion diapositiva por diapositiva

### Diapositiva 1 — ASCEND (M1 · 0:00 – 1:30)

**Qué decir:**

“Buenas tardes. Somos el equipo ASCEND. En los próximos 20 minutos vamos a defender una sola idea: un sistema de torneos no se termina cuando la página funciona; se termina cuando otra persona puede desplegarlo, operarlo, auditarlo y recuperarlo.

ASCEND es un gestor multideportivo construido en PHP 8 y MySQL 8, con tres roles, catálogo público y panel de administración. Pero lo que van a ver también es la tercera entrega de Sistemas Operativos: un toolkit de 32 scripts para poner ese sistema en un servidor Linux, con usuarios, logs, respaldos y replicación.

Cinco materias, un mismo hilo. Y un criterio que nos ordenó todo el trabajo: no decir que algo está probado si solo lo revisamos por lectura. Arrancamos por el problema.”

**Apoyo visual:** señalar el cuadro 8 → 4 → 2 → 1 del costado. “Ese cuadro es el dominio; lo que viene es cómo lo sostenemos.”

**Transición:** “¿Por qué hace falta un sistema y no alcanza con una planilla?”

---

### Diapositiva 2 — El problema (M1 · 1:30 – 3:00)

**Qué decir:**

“Organizar un torneo parece un problema de formularios. En realidad es un problema de confianza. Hoy las inscripciones viven en planillas, los resultados en chats y la versión válida en la cabeza de alguien. Cuando esa persona no está, el torneo no se cae por falta de código: se cae por falta de trazabilidad.

ASCEND ataca cuatro frentes. Uno: un catálogo público y un flujo distinto por rol. Dos: resultados, posiciones e historial en una sola fuente. Tres: Docker y migraciones para que el entorno se reconstruya igual. Cuatro: respaldo, auditoría y replicación pensados desde el inicio, no al final.

La frase que resume la diapositiva es esta: un torneo se cae por lo que no está documentado, no por lo que no está programado.”

**Apoyo visual:** recorrer con la mano las dos columnas; no leer las cuatro viñetas una por una.

**Transición:** “Veamos quién usa el sistema y qué recorre.”

---

### Diapositiva 3 — Tres roles, un mismo recorrido (M1 · 3:00 – 4:30)

**Qué decir:**

“El recorrido tiene cuatro etapas. **Descubre:** cualquiera entra al catálogo, ve un torneo y un ranking sin registrarse. **Se inscribe:** el jugador crea o solicita un equipo y el organizador aprueba. **Compite:** el organizador carga fixtures y resultados, y las posiciones se actualizan. **Consulta:** quedan historial, podio y perfil público.

Tres roles: jugador, organizador y admin. El jugador ve una carrera; el organizador ve un torneo; el admin ve el sistema completo. Esa separación no es solo visual: cada uno tiene permisos distintos y el sistema los verifica en el servidor.

La demo va a mostrar ese recorrido en 90 segundos.”

**Transición:** “Y acá viene la parte que suele convencer más que cualquier diagrama.”

---

### Diapositiva 4 — Demo en vivo (M2 · 4:30 – 6:00)

**Qué decir:**

“Son tres pantallas, 90 segundos. **Primero, público:** abrimos el torneo 1217, que tiene inscripciones confirmadas, pendientes, rechazadas y canceladas, y mostramos el ranking. Quiero que vean datos reales, no maquetas: la base trae 18 torneos y 87 encuentros. **Segundo, jugador:** equipo, solicitud e inscripción. Cada rol ve solo su superficie. **Tercero, organizador:** publicamos un resultado y vemos cómo se mueve la posición en el ranking público.

Eso cierra el ciclo del dato: del formulario al ranking, sin planillas intermedias. Si la red falla, tenemos un video de 90 segundos y capturas; la historia no se cae. Y por seguridad, nunca mostramos credenciales en pantalla.”

**Qué hacer en pantalla (orden estricto):**

1. Catálogo → torneo 1217 → ranking. 25 segundos.
2. Cambiar a pestaña de jugador → equipo/solicitud. 30 segundos.
3. Cambiar a organizador → cargar un resultado → volver al ranking público. 35 segundos.

**Si algo falla:** no reiniciar el stack frente al jurado. Pasar al video y decir “esto es exactamente lo que acaban de ver en la diapositiva”.

**Transición:** “Eso es lo que se ve. Ahora, qué está construido y qué no.”

---

### Diapositiva 5 — Alcance (M1 · 6:00 – 7:30)

**Qué decir:**

“Este es el alcance, y lo decimos completo. **Público:** catálogo, detalle, rankings, perfiles y filtros. **Jugador:** equipos, solicitudes, inscripciones, guardados, historial, notificaciones y perfil editable. **Organización y admin:** torneos, formatos, fixtures, resultados, posiciones, usuarios, juegos, modalidades, auditoría y errores.

Lo que **no** está: recuperación de contraseña, bye en el sistema suizo y email real. No es un olvido; está declarado en el README y en la matriz de requisitos. Preferimos que lo sepan por nosotros y no que lo descubran en la pregunta.

Un alcance chico y sostenido vale más que una lista larga de promesas.”

**Transición:** “Con eso claro, veamos cómo está armado por dentro.”

---

### Diapositiva 6 — Arquitectura (M2 · 7:30 – 9:00)

**Qué decir:**

“No usamos framework: usamos un MVC explícito. Todo entra por `publico/index.php`, el front controller. El router arma controlador y acción con `?c=` y `?a=`. El controlador valida rol, sesión y CSRF. El modelo habla con MySQL mediante PDO y consultas preparadas. La vista devuelve HTML distinto por rol.

Alrededor hay cuatro capas que no se ven en la pantalla pero sostienen todo: sesión y CSRF, cabeceras de seguridad, manejo de errores y consultas parametrizadas. Abajo, Docker Compose: PHP 8.2 con Apache, MySQL 8.0, phpMyAdmin y Grafana opcional.

Un solo punto de entrada y ningún controlador hablando SQL directamente. Eso es lo que hace que el resto sea testeable y que el reemplazo de una capa no rompa las otras.”

**Pregunta probable:** “¿Por qué no Laravel?” → “Fue una decisión de la materia y del alcance: queríamos controlar y explicar el flujo completo. El costo es más código propio; la ventaja es que cada capa está a la vista y documentada. No decimos que sea mejor que un framework.”

**Transición:** “Ese flujo se apoya en una base concreta.”

---

### Diapositiva 7 — Base de datos (M3 · 9:00 – 11:00)

**Qué decir:**

“La base es el contrato del sistema. **41 tablas** y **seis migraciones** versionadas: esquema, catálogos, seed, autenticación, correcciones y ampliación del seed. Cada migración tiene un orden y un propósito; no se importa un archivo suelto y se espera que salga bien.

El seed no es decorativo. Cubre liga, eliminación directa y suizo, en individual y por equipos, con **18 torneos, 23 equipos, 81 participantes y 87 encuentros**. Eso permite que la demo y las pruebas tengan casos reales: inscripciones en cuatro estados, posiciones, historial y podios.

Y todo se reconstruye desde un volumen vacío con un comando. La frase: la calidad de los datos define la calidad de la demo. Si la base tuviera tres filas, la demo no podría contar nada.”

**Apoyo visual:** recorrer las barras de izquierda a derecha; detenerse en 87 encuentros.

**Pregunta probable:** “¿Por qué 41 tablas?” → “Porque el dominio incluye usuarios, roles, torneos, formatos, equipos, participantes, encuentros, posiciones, historial, notificaciones, auditoría y errores. Preferimos normalizar y poder explicar cada tabla antes que meter todo en cinco tablas difíciles de auditar.”

**Transición:** “Con los datos y el flujo claros, pasamos a cómo se protege.”

---

### Diapositiva 8 — Seguridad (M4 · 11:00 – 13:00)

**Qué decir:**

“Seguridad en cuatro capas. **Acceso:** guards en cada controlador; el jugador no entra al panel del organizador y el organizador no administra torneos ajenos. Lo probamos: intentar administrar el torneo 1200 con otra cuenta devuelve redirección, no un 200. **Formularios:** token CSRF de sesión en cada formulario, validación en POST y regeneración de sesión al autenticar. **Credenciales:** bcrypt, política de contraseña y bloqueo temporal por IP en la tabla de intentos. **Transporte:** CSP, X-Frame-Options, nosniff, Referrer-Policy y Permissions-Policy.

Ahora el límite, y lo decimos fuerte: esto no es seguridad de producción. Las credenciales de demo son públicas. Para producción faltan HTTPS, rotación y hardening del servidor. Una defensa honesta también enumera lo que falta.”

**Pregunta probable:** “¿Entonces es seguro?” → “Tiene controles razonables para un MVP académico y los límites están declarados. No lo presentamos como listo para producción.”

**Transición:** “Y así como se protege, también se opera.”

---

### Diapositiva 9 — Toolkit de Sistemas Operativos (M4 · 13:00 – 15:00)

**Qué decir:**

“La tercera entrega de Sistemas Operativos es este toolkit en Bash: **32 scripts, nueve módulos**, un menú que delega y scripts que también corren solos. Instalación, usuarios y sudoers, logs, auditoría con auditd, respaldos locales y remotos, replicación maestro-esclavo, procesos, estado y documentación.

Tres propiedades lo ordenan. **Menor privilegio:** el sudo está acotado por grupo y el auditor no recibe sudo. **Recuperable:** copia remota con checksums y poda deshabilitada por defecto. **Trazable:** todo queda en `auditoria.log` y en journald con la etiqueta `ascend-admin`.

Validamos sintaxis **32 de 32** y ayuda **24 de 24**. La ejecución real de los instaladores, respaldos y replicación en una VM Linux queda pendiente, y la declaramos como pendiente. No la presentamos como hecha.”

**Pregunta probable:** “¿Por qué no lo ejecutaron?” → “El entorno de desarrollo es Windows/XAMPP y no teníamos una VM Linux desechable con snapshot. No quisimos ejecutar instaladores ni cambios de usuarios en el host. El informe de aceptación queda bloqueado hasta tener esa VM.”

**Transición:** “Ahora sí, el mapa completo de las cinco materias.”

---

### Diapositiva 10 — Cinco materias, una entrega (M1 · 15:00 – 16:30)

**Qué decir:**

“**Ingeniería de software:** requisitos, arquitectura MVC, auditoría técnica y checklist de entrega. **Full stack:** PHP, routing, sesiones, vistas por rol y formularios validados; las rutas de los tres roles respondieron 200 en el dry run. **Bases de datos:** 41 tablas, seis migraciones, seed, respaldo y replicación. **Ciberseguridad:** RBAC, CSRF, bcrypt, cabeceras y menor privilegio. **Sistemas operativos:** LAMP/Docker, systemd, logs, respaldos, replicación y procesos.

No son cinco proyectos pegados: es el mismo flujo mirado desde cinco materias. La misma base que usa la demo es la que se respalda y se replica; el mismo servidor que corre Apache es el que se audita y se monitorea.”

**Transición:** “Y para cerrar, lo que probamos y lo que no.”

---

### Diapositiva 11 — Evidencia y límites (M1 · 16:30 – 18:00)

**Qué decir:**

“**32 de 32** scripts con sintaxis válida. **24 de 24** con ayuda operativa. **16 de 16** rutas públicas y **18 de 18** rutas de administración con HTTP 200. Verificamos el stack desde un volumen vacío, la base con 41 tablas, 51 usuarios y 18 torneos, los tres paneles en navegador y la suite local.

Lo que **no** verificamos todavía: los instaladores y operaciones del toolkit en una VM Linux, la restauración real de un respaldo y la replicación maestro-esclavo real. No convertimos análisis estático en prueba funcional. El informe de aceptación del toolkit queda **bloqueado** hasta contar con esa VM.

Esa es la diferencia entre defender lo que hicimos y defender lo que nos gustaría haber hecho.”

**Transición:** “Con eso, cerramos.”

---

### Diapositiva 12 — Gracias / paso a la defensa (Todos · 18:00 – 19:30)

**Qué decir:**

“Cerramos con la idea del inicio: un torneo se gana en la cancha; un sistema se sostiene en la operación. Muchas gracias. Quedan cuarenta minutos para preguntas; estamos listos para profundizar en cualquier decisión.”

**Mientras el jurado pregunta:** el integrante de la materia involucrada responde primero; M1 complementa o cierra. En la defensa de 40 minutos, cada respuesta dura entre 30 y 60 segundos y termina volviendo al sistema, no a una opinión. Si no saben, dicen “no lo medimos” o “está declarado como pendiente”, y no inventan.

---

## 4. Preguntas probables y respuestas cortas

Estas son las respuestas de bolsillo para cualquier integrante. El banco ampliado para la defensa de 40 minutos, organizado por área y con la justificación de cada decisión, está en `GUION_ADMINISTRADOR.md`.

**¿Por qué no usaron un framework?**  
Fue una decisión de alcance y de la materia: queríamos controlar y explicar el flujo completo. El costo es más código propio y menos herramientas listas; la ventaja es que cada capa está documentada y es observable. No afirmamos que sea mejor que Laravel.

**¿Cómo evitan que un organizador administre torneos ajenos?**  
Hay un guard de propiedad en el controlador. En la verificación, la cuenta demo intentó administrar el torneo 1200 y recibió una redirección (302), no un 200. La cuenta dueña sí obtuvo 200.

**¿Qué pasa si se cae la base?**  
El toolkit contempla respaldo local y remoto con `SHA256SUMS`, manifiesto y verificación. La restauración real todavía no se ejecutó en una VM, y así está declarado. No decimos “recuperación probada”.

**¿El sistema es seguro?**  
Tiene controles para un MVP: RBAC, CSRF, bcrypt, bloqueo por IP y cabeceras. No es producción: las credenciales de demo son públicas y faltan HTTPS, rotación y hardening.

**¿Por qué el suizo no tiene bye?**  
Porque el alcance implementado admite cantidades pares de 4 a 64. El bye está declarado como fuera de alcance; no lo escondemos ni lo presentamos como terminado.

**¿Cómo escala?**  
El cuello de botella natural es MySQL. El diseño ya separa modelo y controlador, usa consultas preparadas y contempla réplica maestro-esclavo. No es un sistema pensado para millones de usuarios; es un MVP académico con un camino de crecimiento claro.

**¿Qué harían con dos semanas más?**  
Primero, una VM Linux con snapshot y ejecutar el toolkit completo: instalación, respaldo, restauración y replicación. Segundo, automatizar esa verificación en CI. No agregaríamos funcionalidades nuevas antes de cerrar esa deuda.

**¿Qué fue lo más difícil?**  
Respuesta honesta y concreta: la convivencia de cinco materias en un solo entregable y la trazabilidad de datos entre torneos, equipos, encuentros y posiciones. Mencionar un caso real (por ejemplo, que un torneo sin encuentros dejaba la pantalla vacía y se resolvió ampliando el seed) muestra trabajo, no sufrimiento.

**¿Por qué hay tantas tablas?**  
Porque el dominio tiene historial, auditoría, notificaciones y estados. Normalizar permite explicar y auditar cada entidad; el costo es más joins y más migraciones, que están versionadas.

**¿Qué parte no volverían a hacer igual?**  
Cualquier respuesta concreta sirve si termina en aprendizaje: por ejemplo, haber definido antes el contrato de datos y el seed, para no reconstruir la demo al final.

---

## 5. Frases que conviene evitar

| No decir | Decir |
|---|---|
| “Está 100% terminado.” | “Está entregable como MVP, con límites declarados.” |
| “Probamos todo en Linux.” | “Validamos sintaxis y ayuda; la ejecución real en VM está pendiente.” |
| “Es seguro.” | “Tiene controles de seguridad para un MVP; no es producción.” |
| “No tiene bugs.” | “Los P0/P1 auditados están corregidos; quedan límites conocidos y documentados.” |
| “La demo siempre funciona.” | “Tenemos video y capturas como plan B.” |
| “Eso no lo vimos.” | “No lo medimos; está declarado como pendiente en el informe.” |

---

## 6. Cierre operativo

- **M1** controla el tiempo y da la palabra.
- **M2** no toca el teclado hasta que la demo esté en pantalla completa.
- **M3** tiene a mano las cifras: 41 tablas, 51 usuarios, 18 torneos, 87 encuentros.
- **M4** tiene a mano los límites: VM Linux, restauración y replicación reales pendientes.
- Si el jurado interrumpe, se responde corto y se retoma desde la diapositiva actual, no desde el principio.

**Fuentes internas para responder con precisión:** `README.md`, `informes_auditoria/VERIFICACION_FINAL_DRY_RUN.md`, `administracion_sistemas/README.md` e `informes/2026-10-22_aceptacion/INFORME_ACEPTACION_VM.md`.
