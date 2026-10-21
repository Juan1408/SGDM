# Informe de auditoría integral — SGDM / ASCEND

**Fecha:** 2026-10-21
**Rama auditada:** `merge_16_10` — HEAD `902a2fe`
**Alcance:** navegación y rutas, flujos de negocio, base de datos y semillas, Docker/PHP/Apache, integración MVC y errores en ejecución.
**Naturaleza:** auditoría de solo lectura. **No se modificó ningún archivo del proyecto** durante esta fase.

---

## 0. Veredicto ejecutivo

**El sistema no está en condiciones de presentarse como entrega estable en su estado actual.**

Se reprodujeron en ejecución **cuatro defectos bloqueantes (P0)** que dejan pantallas completas en blanco o inutilizan un CRUD principal:

| # | Defecto | Impacto visible | Estado |
|---|---------|-----------------|--------|
| P0-01 | La guía de despliegue reimporta `001_ascend.sql` después de que Docker ya lo ejecutó; ese script empieza con `DROP TABLE` | Una instalación siguiendo la guía queda con esquema recreado y semillas de `002`/`003` borradas | Confirmado por inspección (no ejecutado por ser destructivo) |
| P0-02 | Falta la columna `torneo_actividades.descripcion` | Detalle público de torneo devuelve **HTTP 200 con 0 bytes** | **Reproducido en ejecución** |
| P0-03 | Faltan `perfiles_organizadores.banner_url` y `campos_publicos` | Perfil público de organizador y perfil/edición del panel del organizador devuelven **HTTP 200 con 0 bytes** | **Reproducido en ejecución** |
| P0-04 | Error de sintaxis en `vistas/admin/juegos/juegos_editar.php:27` | El admin no puede editar juegos: la vista no renderiza el formulario | **Reproducido en ejecución** y `php -l` |

Los cuatro tienen corrección acotada (una columna, dos columnas, un carácter y un paso de documentación). **Ninguno exige reescribir módulos.**

Además hay riesgos P1 de despliegue que impiden la promesa de "levanta con un comando": `DB_PORT` no existe en la configuración, `APP_URL` está fijada a `localhost`, el `DocumentRoot` de Docker expone todo el repositorio y Grafana choca con el puerto 3000.

**Recomendación:** autorizar la Fase 1 del plan (Día 1: P0 de base de datos y pantallas blancas + Día 2: CRUDs rotos). El resto del plan está en `PLAN_ESTABILIZACION_5_DIAS.md`.

---

## 1. Método, evidencia y límites

### 1.1 Qué se hizo

1. **Reconocimiento:** estructura real, `git status`, documentación existente, herramientas disponibles.
2. **Auditoría estática:** lectura de front controller, 23 controladores, 15 modelos, vistas críticas, migraciones, Docker, `.htaccess`, JS.
3. **Auditoría en ejecución:** se levantó un MySQL 8 descartable (`sgdm_audit_db`, volumen `sgdm_audit_datos`) con las migraciones reales y un servidor PHP embebido en `127.0.0.1:8099` apuntando a esa base. Se recorrieron rutas públicas, de jugador, organizador y admin con sesiones reales.
4. **Consultas SQL de verificación:** conteos, columnas, colaciones, coherencia de semillas.
5. **Pruebas:** `php -l` sobre los 104 archivos PHP, `node --check` sobre los 48 JS y PHPUnit (7 pruebas).
6. **Despliegue:** `docker compose config`, inspección de la imagen `docker-web`, puertos ocupados, revisión de guías.

### 1.2 Regla de clasificación aplicada

- **Confirmado en ejecución:** se observó la respuesta HTTP, el cuerpo o el registro en `errores_sistema`.
- **Confirmado por inspección:** el código o el esquema demuestran el defecto sin ambigüedad; no se ejecutó por requerir datos, permisos o acciones destructivas.
- **Probable / hipótesis:** depende de un estado no reproducido; se lista aparte y **no** se usa para el veredicto.

### 1.3 Límites

- XAMPP Apache y MySQL estaban detenidos. **No se iniciaron** para no tocar datos locales del estudiante.
- No se ejecutó el paso destructivo de la guía Docker (reimportar `001`).
- No se probaron pagos, correo real, ni integraciones externas (no existen en el código).
- No se hizo auditoría de ciberseguridad completa; solo se registran exposiciones evidentes de despliegue.
- No se validó en navegador real el 100 % de los formularios; se usaron peticiones HTTP y sesiones, más inspección de vistas. Los flujos no ejecutados se marcan como tales en la matriz.

### 1.4 Organización de la auditoría

| Fase | Frente | Ejecutor | Resultado |
|------|--------|----------|-----------|
| 0 | Reconocimiento y entorno | Agente principal | Estructura, Git, herramientas |
| A/B | Rutas, vistas, navegación y flujos | Subagente + verificación principal | Matriz de rutas y enlaces rotos |
| C | Esquema, semillas, migraciones | Subagente + verificación principal | Auditoría de BD |
| D | Docker, PHP, Apache, despliegue | Subagente + verificación principal | Auditoría de despliegue |
| E/F | Integración MVC, runtime, pruebas | Agente principal | Errores reproducidos, PHPUnit, lint |
| G | Consolidación | Agente principal | Este informe y anexos |

Las afirmaciones de los subagentes fueron revalidadas contra el código o la ejecución. Las que no resistieron verificación se degradaron o se eliminaron (por ejemplo, se comprobó que `verComo` funciona en su camino feliz y que las rutas muertas no tienen enlaces entrantes).

---

## 2. Estructura real del proyecto

```
SGDM/
├── .htaccess                     # RewriteRule a index.php de la RAÍZ (no existe)
├── composer.json / phpunit.xml
├── base_de_datos/
│   ├── migraciones/
│   │   ├── 001_ascend.sql        # esquema + DROP TABLE + semillas base + hotfix
│   │   ├── 002_datos_ejemplo.sql # usuarios/roles/perfiles base
│   │   ├── 003_ascend_seed_relacional_qa.sql # 12 torneos y datos QA
│   │   └── 004_actualizar_auth.sql# intentos_login + políticas
│   └── ...
├── codigo_fuente/
│   ├── publico/
│   │   ├── index.php             # front controller y whitelist
│   │   ├── css/ js/ img/         # assets
│   │   └── (sin .htaccess)
│   ├── configuracion/            # constantes.php, base_de_datos.php
│   ├── core/                     # ManejadorErrores, CabecerasSeguridad
│   ├── ayudantes/                # Sesion, CSRF
│   ├── controladores/            # 23 controladores
│   ├── modelos/                  # 15 modelos PDO
│   └── vistas/                   # públicas, jugador, organizador, admin
├── docker/
│   ├── docker-compose.yml        # web 8080, db 3307, phpmyadmin 8081, grafana 3000
│   └── Dockerfile                # php:8.2-apache
├── Documentos_Juan/              # guías de despliegue
├── tests/                        # PHPUnit
└── vendor/
```

**Arquitectura:** MVC propio sin framework ni autoloader de aplicación. El front controller arma el nombre del controlador con `ucfirst($_GET['c'])`, valida contra una whitelist y exige que la acción exista. Las vistas se incluyen desde los controladores. La conexión es un Singleton PDO. Los tests sí usan autoload de Composer.

**Ruteo:** `index.php?c=<controlador>&a=<acción>` (+ parámetros). No hay rutas limpias activas: el `.htaccess` de la raíz apunta a un `index.php` inexistente y el de `publico/` no existe; en Docker `AllowOverride None` lo vuelve inerte.

---

## 3. Estado de los flujos principales

| Flujo | Estado | Evidencia |
|-------|--------|-----------|
| Home pública, login, registro, catálogo, juegos, rankings, contacto, nosotros | Operativo | 200 con contenido (25 KB, 2,7 KB, 3,9 KB, 27 KB, 12 KB, 13 KB, etc.) |
| Detalle público de torneo | **Roto** | P0-02: 200 / 0 bytes |
| Perfil público de jugador | Operativo | `perfil&a=jugador&id=...` 200 / 7,4 KB |
| Perfil público de organizador | **Roto** | P0-03: 200 / 0 bytes |
| Login admin / jugador / organizador | Operativo | 302 al panel correspondiente |
| Panel admin: dashboard, usuarios, torneos, equipos, auditoría, errores, políticas | Operativo (lectura) | Múltiples 200 con contenido |
| Admin: editar juego | **Roto** | P0-04: sin formulario |
| Admin: bloquear/activar organizador | **Roto** | Enlace GET contra acción POST → 405 (inspección) |
| Admin: eliminar jugador | **Roto** | Formulario con markup corrupto y sin `rol_id` (inspección) |
| Panel organizador: dashboard, torneos, resultados, calendario, historial | Operativo | 200 con contenido |
| Organizador: crear torneo | **Roto en etapa 3** | La vista carga, pero `guardarEtapa` falla al leer/insertar `torneo_actividades.descripcion` (P0-02) |
| Panel organizador: perfil y editar perfil | **Roto** | P0-03 |
| Organizador: administrar torneo | Parcial | 200; edición de actividades tiene formulario truncado (inspección) |
| Panel jugador: dashboard, perfil, editar, equipo, solicitudes, guardados | Operativo | 200 con contenido |
| Inscripción a torneo / gestión de equipos | Parcial | Rutas 200; reglas de negocio con defectos P2 (ver §5) |
| "Ver como" (suplantación) | Operativo en camino feliz | POST con `id` en URL → panel del jugador; restaurar funciona. Defecto latente en respaldo/limpieza y warnings con id inválido |
| Recuperación de contraseña | **Roto** | El enlace del login apunta a `recuperar-password.html`, que no existe → 404 |

---

## 4. Hallazgos bloqueantes (P0)

### P0-01 — La guía de despliegue destruye la base después de inicializarla

**Evidencia (inspección documental + SQL):**

- `Documentos_Juan/Guia_Despliegue_10_Minutos.md` indica, en el paso 3 de Docker, ejecutar:
  ```powershell
  Get-Content -Raw -LiteralPath .\base_de_datos\migraciones\001_ascend.sql | docker exec -i sgdm_db mysql -uroot -proot123 ascend
  ```
- `docker-compose.yml` monta `../base_de_datos/migraciones` en `/docker-entrypoint-initdb.d`; el entrypoint de MySQL ejecuta `001`→`004` solo cuando el volumen está vacío.
- `001_ascend.sql` comienza con `SET FOREIGN_KEY_CHECKS=0` y una secuencia de `DROP TABLE IF EXISTS` (líneas 43-123).

**Consecuencia:** al seguir la guía, el esquema se recrea y se pierden los usuarios, torneos y datos de `002`/`003`. Explica los síntomas de "faltan datos" o "algunas pantallas vacías" en una instalación nueva.

**Corrección mínima:** eliminar ese paso de la guía (el entrypoint ya hizo el trabajo) y agregar una verificación de conteos. Si se necesita reimportar, hacerlo solo con `docker compose down -v` y un arranque limpio. No tocar el SQL para esta corrección.

**Criterio de aceptación:** instalación desde cero siguiendo la guía → `usuarios=51`, `torneos=12`, `roles=4`, sin pasos manuales de SQL.

---

### P0-02 — Detalle público de torneo en blanco por columna inexistente

**Reproducción:**

```
GET http://127.0.0.1:8099/index.php?c=torneo&a=detalle&id=1200  -> 200, 0 bytes
GET http://127.0.0.1:8099/index.php?c=torneo&a=detalle&id=1202  -> 200, 0 bytes
```

**Error registrado en `errores_sistema`:**

```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'a.descripcion' in 'field list'
Archivo: codigo_fuente/modelos/Torneo.php, línea 121
```

**Causa raíz:** `torneo_actividades` se define en `001_ascend.sql` (líneas 616-630) con `id, torneo_id, titulo, tipo, fecha, hora, creado_en`, **sin `descripcion`**. El modelo consulta `a.descripcion` en la agenda pública (líneas 105-121 y 526-546) y también en 870, 907 y 926.

**Impacto adicional:** `Torneo::guardarActividades` (líneas 512-557) primero **lee** `descripcion` (línea 526) y luego **inserta** en esa columna (537-539). Por lo tanto, la creación de un torneo por el organizador también falla en la etapa 3 con "No se pudieron guardar las actividades", y el guardado de actividades desde `administrar-torneo` falla igual. No es solo una pantalla pública: bloquea un flujo central del organizador.

**Corrección mínima viable:** agregar `descripcion TEXT NULL` a la definición de la tabla en `001` y un `ALTER TABLE` idempotente para bases existentes. Alternativa: quitar la columna de las consultas, pero se perdería un campo que la vista probablemente espera y que el formulario de actividades podría enviar; la columna es la opción menos invasiva.

**Criterio de aceptación:** `detalle&id=1200`, `1201`, `1202` y `1210` devuelven 200 con contenido, listan actividades y no generan nuevas filas en `errores_sistema`. Además, crear un torneo con actividades completa la etapa 3 y persiste las descripciones.

---

### P0-03 — Perfiles de organizador en blanco por dos columnas inexistentes

**Reproducción:**

```
GET ?c=perfil&a=organizador&id=2      -> 200, 0 bytes
GET ?c=perfil&a=organizador&id=1000   -> 200, 0 bytes
GET ?c=panelOrganizador&a=perfil      -> 200, 0 bytes (sesión organizador)
GET ?c=panelOrganizador&a=editarPerfil-> 200, 0 bytes
```

**Errores registrados:**

```
Unknown column 'p.banner_url' in 'field list' — Organizador.php:54
Unknown column 'p.banner_url' in 'field list' — Organizador.php:79
```

**Causa raíz:** `perfiles_organizadores` (001, líneas 252-262) tiene `usuario_id, nombre_organizacion, bio_organizacion, localidad, telefono_contacto, sitio_web, verificado_oficial, actualizado_en`; **no** tiene `banner_url` ni `campos_publicos`. `perfiles_jugadores` sí las tiene. El modelo las selecciona en 48-49 y 73-74, y las actualiza en 141 y 150-151.

**Corrección mínima:** agregar ambas columnas a `001` y un `ALTER TABLE` idempotente para bases existentes, con los mismos tipos/defaults que en `perfiles_jugadores`: `banner_url TEXT NULL` y `campos_publicos VARCHAR(255) NOT NULL DEFAULT ''`. No cambiar la lógica del modelo.

**Criterio de aceptación:** perfil público de organizador (ids 2 y 1000) y perfil/edición del panel devuelven contenido; guardar perfil no produce error; no aparecen nuevas filas en `errores_sistema`.

---

### P0-04 — Edición de juegos inutilizada por error de sintaxis

**Reproducción:**

```
GET ?c=juego&a=editar&id=1 -> 200, 7.424 bytes, sin <form>, sin "Nombre del Juego"
```

**Evidencia:** `codigo_fuente/vistas/admin/juegos/juegos_editar.php`, línea 27:

```php
value="<?php echo htmlspecialchars($juego['nombre']); ?" required>
```

Falta `>`. `php -l` confirma `syntax error, unexpected token "?"` en la línea 27. Es el **único** error de sintaxis entre 104 archivos PHP.

**Corrección mínima:** reemplazar `?"` por `?>"`. Verificar además que el resto de la vista no tenga errores de escape.

**Criterio de aceptación:** la página muestra el formulario con nombre, categoría, imagen, puntajes, modalidades y formatos; guardar una edición sin cambios devuelve 302 y persiste.

---

## 5. Hallazgos P1 (alto impacto para la entrega)

### P1-01 — El modo "XAMPP + base Docker" documentado no puede conectar

`base_de_datos.php` solo acepta `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `DB_CHARSET`. `Conexion.php:21` construye el DSN sin puerto. El compose publica MySQL en **3307**, pero `COMANDOS_DOCKER.md` propone usar solo el contenedor `db` con XAMPP: el host `localhost` sin puerto apunta a 3306 (MySQL/MariaDB local), no al contenedor.

**Corrección mínima:** agregar `DB_PORT` (default 3306) y concatenarlo al DSN; documentar `DB_PORT=3307` para ese modo. Es un cambio de 3 líneas y una variable de entorno.

**Criterio:** PHP CLI con `DB_HOST=127.0.0.1 DB_PORT=3307 ...` conecta y lee `usuarios`; XAMPP sigue funcionando sin `DB_PORT`.

### P1-02 — `APP_URL` fija a `localhost` rompe el acceso por IP o dominio

`docker-compose.yml` define `APP_URL=http://localhost:8080/publico/`. `constantes.php` prioriza `APP_URL` sobre la detección por `HTTP_HOST`. Si la demo se hace desde otra máquina, Proxmox o un dominio, todos los enlaces, CSS y JS apuntan a `localhost` del cliente.

**Corrección mínima:** dejar `APP_URL` vacía por defecto y usar la detección existente; o parametrizar con `${APP_URL:-}`. Para la demo, fijarla a la URL real en un `.env`.

**Criterio:** acceder por `http://IP:8080/publico/` carga assets y navegación sin enlaces a `localhost`.

### P1-03 — El contenedor web expone todo el repositorio y lista directorios

La imagen `docker-web` verificada tiene:

```
DocumentRoot /var/www/html
<Directory /var/www/html> AllowOverride None; Options Indexes FollowSymLinks
```

El compose monta `../codigo_fuente:/var/www/html`. Por lo tanto quedan accesibles `configuracion/`, `modelos/`, `vistas/`, `core/`, logs si se generan, y el listado de directorios. El `.htaccess` de la raíz no aplica (está fuera del docroot y `AllowOverride None`).

**Corrección mínima:** `DocumentRoot /var/www/html/publico` con un vhost o `sed` en el Dockerfile, y `AllowOverride All` si se quiere conservar reescritura. Verificar que las rutas relativas del front controller (`__DIR__/../`) sigan funcionando (lo harán, porque el código está un nivel arriba). Alternativa conservadora para la entrega: mantener docroot y agregar un `.htaccess` en `publico/` + `AllowOverride`.

**Criterio:** `GET /configuracion/base_de_datos.php` y `GET /modelos/` no devuelven contenido; la app sigue navegando.

### P1-04 — Grafana no puede publicar el puerto 3000

En la máquina auditada, el puerto 3000 está ocupado por `node.exe` (PID 23336). `docker compose up` falla al crear ese servicio. Grafana no es necesario para la app.

**Corrección mínima:** cambiar a `3001:3000` o poner Grafana bajo un perfil opcional (`profiles: ["observabilidad"]`) para que no bloquee el arranque por defecto.

**Criterio:** `docker compose up -d` termina sin error y `web`/`db` quedan saludables.

### P1-05 — Enlace de recuperación de contraseña a un archivo inexistente

El login enlaza a `recuperar-password.html`; la petición devuelve 404. No existe controlador, vista ni ruta de recuperación.

**Corrección mínima:** quitar el enlace visible y rotularlo como "no disponible" **o** implementar el flujo si es requisito académico. Quitarlo no elimina una funcionalidad existente: nunca estuvo implementada. Decidir con el alcance de la entrega.

**Criterio:** ningún enlace del login produce 404.

### P1-06 — El manejador global oculta errores y devuelve 200 en blanco

`ManejadorErrores::capturarExcepcion` registra en `errores_sistema` pero no emite salida ni cambia el código HTTP. Con `ob_start()` en el front controller, una excepción a mitad de render produce **200 con cuerpo vacío**. Esto convirtió tres defectos de esquema en "pantallas blancas" indistinguibles de un problema de red.

**Corrección mínima:** en el manejador, limpiar el buffer, enviar `http_response_code(500)` y una página mínima de error (sin stack trace en producción), manteniendo el registro. Es un cambio localizado en `core/ManejadorErrores.php`.

**Criterio:** forzar una excepción de prueba devuelve 500 con mensaje genérico y queda registrada; ninguna pantalla legítima cambia.

---

## 6. Hallazgos P2 (corregir si el tiempo lo permite; afectan demo y QA)

| ID | Hallazgo | Evidencia | Impacto | Corrección mínima |
|----|----------|-----------|---------|-------------------|
| P2-01 | "Bloquear/Activar" organizador es un `<a>` GET; `UsuarioControlador::cambiarEstadoUsuario` exige POST y responde 405 | `vistas/admin/organizadores/organizador_index.php:85`; controlador 38-44 | Acción admin visible no funciona | Convertir en formulario POST con CSRF o aceptar GET validando CSRF; preferible POST |
| P2-02 | Formulario de eliminación de jugador con markup corrupto y sin `rol_id` | `vistas/admin/jugadores/jugadores_index.php:54-64` contiene texto `fix.md 2026-10-07 6 / 11` dentro del `<input>` | El borrado admin falla o envía datos inválidos | Reescribir el bloque del formulario, incluir `csrf_token` y `rol_id` |
| P2-03 | Formulario de edición de actividad truncado | `vistas/organizador/administrar-torneo.php:518-519`: `action="<?php echo URL_BASE; ?>` sin acción ni cierre | Editar actividad no envía al controlador | Completar `index.php?c=panelOrganizador&a=editarActividad` y cerrar comilla |
| P2-04 | Semillas QA incoherentes | 18/30 posiciones con PJ ≠ G+E+P; torneos 1201/1205/1209 `en_curso` con su único partido `finalizado`; 1204/1209 individuales con participantes de equipo; 1201/1210 eliminación con 3 participantes; políticas duplicadas id 1 y 1000 | Rankings y tablas pueden mostrar datos absurdos en la demo | Corregir datos en `003` o aceptar y no mostrar esos torneos; no tocar el esquema |
| P2-05 | `002` no incluye `USE ascend;` ni transacción | Cabecera de `002` | Importación manual en otra base falla; import parcial deja basura | Agregar `USE`/transacción o documentar el import con `mysql ... ascend` |
| P2-06 | `003` abre transacción pero no es idempotente en todos los bloques | `START TRANSACTION` … `COMMIT`; usa `INSERT IGNORE`, pero los IDs fijos pueden colisionar con datos del estudiante | Reimportar QA puede mezclar datos | Ejecutar solo en base limpia; documentarlo |
| P2-07 | Borrado de usuario físico con FKs `RESTRICT` | `Usuario::eliminar` / controlador | Un usuario con torneos/equipos no se puede borrar; error 500 genérico | Usar baja lógica (`esta_activo=0`) para la demo, o capturar y explicar |
| P2-08 | Email duplicado en registro/edición produce 500 | `Usuario` sin captura de `23000` | Mala experiencia, error no controlado | Capturar `PDOException` y volver al formulario con mensaje |
| P2-09 | Actualización de perfil de jugador/organizador no verifica resultado de `execute()` | `Jugador.php`, `Organizador.php` | Guardados que fallan silenciosamente | Comprobar `rowCount`/errores y mostrar mensaje |
| P2-10 | `crearEquipo` no inserta `juego_id` | `Equipo.php:36-39` | Equipos sin juego; filtros y torneos por juego fallan | Incluir `juego_id` en el INSERT y validarlo |
| P2-11 | Re-solicitud a equipo rechazado falla por UNIQUE `(equipo_id, usuario_id)` | `Equipo::crearSolicitud` siempre INSERT | Un jugador rechazado no puede volver a solicitar | `INSERT ... ON DUPLICATE KEY UPDATE estado='pendiente'` |
| P2-12 | `responderInvitacion` / `responderSolicitudRecibida` no validan destinatario/iniciador | `Equipo.php` | Un usuario podría responder solicitudes ajenas si conoce el id | Validar `usuario_id`/`equipo_id` contra sesión antes de update |
| P2-13 | `hacerCapitan` no valida pertenencia | `Equipo.php` | Puede nombrar capitán a un no miembro | Verificar membresía |
| P2-14 | Historial de torneos cuenta `estado='aprobado'`, inexistente | `Torneo.php:414` | Historial muestra 0 o datos incorrectos | Contar `confirmado` (o el estado real definido) |
| P2-15 | `publicarBorrador` no comprueba `rowCount` | `Torneo.php:574-584` | Publicación "exitosa" aunque no cambie nada | Verificar filas y redirigir con error |
| P2-16 | PK de `torneo_suizo_parejas` sin `ronda` | `001:583-587` | Emparejamientos de distintas rondas colisionan | Incluir `ronda` en la PK o un id propio; cambio de esquema justificado |
| P2-17 | Suizo con cantidad impar de participantes | `Torneo.php:1227-1265` | Sin bye explícito, puede fallar o dejar jugador sin pareja | Definir bye y probar; no es P0 para la demo si no se usa suizo |
| P2-18 | Podio/resultados fuera de transacción | `Torneo.php` (guardar resultado/podio) | Datos parciales si falla una escritura | Envolver en transacción |
| P2-19 | Suplantación: respaldo se sobrescribe y queda flag ante fallo | `UsuarioControlador::verComo` 144/147 | Warnings y posible pérdida del admin original | Respaldar solo si no existe; limpiar flag si el usuario no existe |
| P2-20 | Eliminación de jugador desde admin espera `rol_id` que el formulario no envía | Ver P2-02 | Redirección incorrecta/borrado no ejecutado | Incluir el campo o inferir rol en el controlador |

---

## 7. Hallazgos P3 (deuda y prolijidad)

- **Rutas muertas en la whitelist:** `organizador&a=cambiarVerificacion` e `inicio&a=perfilPublicoOrganizador`. No tienen método y no tienen enlaces entrantes; devuelven 200 con texto "Error 404". Quitarlas o implementarlas.
- **Respuesta 404 inconsistente:** controlador/acción fuera de whitelist → 404 real; acción inexistente dentro de whitelist → 200 con HTML de error. Unificar.
- **JS muerto con errores de sintaxis:** `crearTorneo.js:32` (`const ayuda-cupos`), `perfil.js:368` (const duplicada), `torneoEdicion.js:2` (IIFE truncada). Los 20 scripts realmente referenciados existen y no tienen estos errores; los tres archivos no se cargan. Limpiar o reparar si se van a usar.
- **Asset faltante:** `vistas/admin/modalidades/index.php:10` apunta a un CSS inexistente bajo `publico/css/`; el archivo real está en la raíz `SGDM/publico/css/admin_modalidades.css`.
- **Imagen faltante:** `detalle-torneo.php:891` referencia `img/chess.jpg`; existe `chess2.jpg`.
- **10 `href="#"`**: la mayoría son cabeceras de categoría del menú admin y placeholders del footer. No son fallos funcionales, pero conviene rotularlos.
- **Tablas/features sin uso:** verificar `permisos`, `roles_permisos` y tablas de auditoría antes de defenderlas en la demo.
- **README y `COMANDOS_DOCKER.md` desactualizados:** rutas `/var/www/html/../desbloquear.php` fuera del bind mount; instrucciones que no coinciden con el compose actual.
- **Imágenes flotantes:** `mysql:8.0`, `php:8.2-apache`, `phpmyadmin:latest`, `grafana/grafana:latest`; para una entrega reproducible conviene fijar digest/tag.
- **Healthcheck sin `start_period`:** MySQL puede tardar en inicializar; `depends_on: condition: service_healthy` está bien, pero conviene `start_period` y reintentos explícitos.
- **Permisos de subidas:** el compose crea y hace `chown` de las carpetas, pero un bind mount en Windows/Linux puede dejar root-owned los archivos nuevos. Verificar en la demo.

---

## 8. Lo que sí funciona (para no sobredimensionar el veredicto)

- **Esquema:** los 4 scripts se ejecutan sin errores en MySQL 8 limpio; 41 tablas creadas.
- **Semillas:** 51 usuarios, 4 roles, 7 perfiles de organizador, 43 de jugador, 5 juegos, 5 modalidades, 15 formatos, 12 torneos, 12 equipos, 30 participantes, 12 partidos, 36 actividades, 126 notificaciones.
- **Login y sesiones:** los tres roles autentican y redirigen a su panel. CSRF presente en los formularios críticos revisados.
- **Navegación pública principal:** home, catálogo, juegos, rankings, contacto, nosotros y perfil de jugador renderizan con contenido.
- **Paneles:** dashboard admin, usuarios, torneos, equipos, auditoría, errores y políticas; dashboard/mis torneos/resultados/calendario/historial del organizador; dashboard/perfil/editar/equipo/solicitudes/guardados del jugador.
- **"Ver como":** el camino feliz funciona (POST con `id` en la URL, panel del jugador, restauración). El modo solo lectura bloquea POST mientras suplanta.
- **Manejador de errores:** registra en `errores_sistema` con hash, contexto censurado y contador de ocurrencias; fue la herramienta que permitió diagnosticar los P0.
- **Pruebas:** 6 de 7 PHPUnit pasan; la restante es un fixture desactualizado, no un fallo de aplicación.

---

## 9. Hipótesis de alto riesgo (no confirmadas)

Estas no sostienen el veredicto, pero deben vigilarse en la regresión:

1. **Inscripción a torneos con cupo lleno o duplicada:** no se ejecutó el flujo completo; las validaciones existen en el modelo, pero no se verificó concurrencia ni el estado `pendiente`.
2. **Equipos y solicitudes cruzadas:** los defectos P2-10 a P2-13 son de inspección; no se probaron dos sesiones simultáneas.
3. **Fixture suizo:** la PK sin `ronda` es una bomba de tiempo si la demo usa suizo con más de una ronda. No se generó un fixture suizo real.
4. **Borrado de usuarios con historial:** se infiere el fallo por FKs `RESTRICT`; no se ejecutó para no destruir datos de la base descartable.
5. **Correo/verificación:** `email_verificado` existe y se siembra en 1, pero no hay envío real; no se verificó si el login exige verificación.
6. **Subidas de imágenes:** no se probó un upload real de banner/carátula; la ruta y permisos pueden fallar en Docker.
7. **Compatibilidad MariaDB 10.4 (XAMPP):** las migraciones usan sintaxis MySQL 8 (`utf8mb4_0900_ai_ci`, JSON). El esquema se cargó en MySQL 8; **no se importó en MariaDB 10.4**. Si la entrega se hace en XAMPP puro, hay que probarlo.
8. **Rendimiento:** el catálogo y los rankings hacen varias consultas; no se midió con volumen. No es bloqueante para la demo.

---

## 10. Discrepancias documentación ↔ código

| Documento | Dice | Realidad |
|-----------|------|----------|
| `Guia_Despliegue_10_Minutos.md` | Reimportar `001` tras `docker compose up` | Destruye las semillas ya aplicadas |
| `COMANDOS_DOCKER.md` | Usar solo `db` con XAMPP en el puerto 3307 | El DSN no soporta puerto; no conecta |
| `COMANDOS_DOCKER.md` | Ejecutar `/var/www/html/../desbloquear.php` | Fuera del bind mount; no existe en el contenedor |
| `README.md` | Estructura y pasos previos | No refleja el compose ni las migraciones actuales |
| `.htaccess` raíz | Reescribe a `index.php` | No existe `SGDM/index.php`; en Docker ni se lee |
| Whitelist | Rutas `cambiarVerificacion` y `perfilPublicoOrganizador` | No existen como métodos |

---

## 11. Pruebas ejecutables en el entorno actual y resultados

| Prueba | Comando | Resultado |
|--------|---------|-----------|
| Sintaxis PHP (104 archivos) | `find codigo_fuente tests -name '*.php' -print0 \| xargs -0 -n1 php -l` | 103 OK, **1 error** (`juegos_editar.php:27`) |
| Sintaxis JS (48 archivos) | `node --check` por archivo | 45 OK, **3 errores en archivos no referenciados** |
| PHPUnit | `vendor/bin/phpunit` (PHP 8.0, base `ascend_test`) | **6 pass, 1 error** por fixture sin `juegos.categoria` (`TorneoTest.php:12`) |
| Esquema limpio | Importar 001→004 en MySQL 8 vacío | Sin errores; 41 tablas |
| Conteos de semillas | `SELECT COUNT(*)` por tabla | Coinciden con lo esperado (ver §8) |
| Rutas públicas | HTTP GET con servidor embebido | 9 OK; detalle de torneo y perfil de organizador en blanco |
| Rutas admin | Sesión admin real | 10 OK; editar juego sin formulario; bloqueo GET→405 |
| Rutas organizador | Sesión organizador real | 7 OK; perfil/editar en blanco |
| Rutas jugador | Sesión jugador real | 8 OK |
| Docker compose | `docker compose config` | Válido; nombres de proyecto/volumen correctos |
| Imagen web | Inspección de `DocumentRoot`/`AllowOverride` | Expuesta y sin reescritura |
| Puertos | `netstat`/`Get-NetTCPConnection` | 3000 ocupado por Node; 8080/8081/3307 libres |

**Limitación:** PHPUnit se ejecutó con PHP 8.0.30 (XAMPP) contra MySQL 8 descartable; no se ejecutó en PHP 8.2/Docker. El lint y el runtime sí cubren el código.

---

## 12. Riesgos de regresión de las correcciones propuestas

- **Agregar columnas:** bajo riesgo; solo afecta bases nuevas y requiere `ALTER` para las existentes. No cambia datos.
- **Cambiar docroot en Docker:** riesgo medio; puede romper rutas de assets o el `.htaccess` si no se prueba el flujo completo. Se mitiga con una prueba de humo de 10 rutas.
- **Agregar `DB_PORT`:** riesgo bajo; default 3306 preserva XAMPP.
- **Cambiar el manejador de errores a 500:** riesgo bajo; puede revelar errores hoy ocultos, que es deseable.
- **Tocar formularios admin:** riesgo bajo, pero deben probarse CSRF y redirecciones.
- **Corregir semillas:** riesgo medio si se reimportan sobre datos existentes; hacerlo solo en base limpia.
- **PK de suizo:** riesgo alto si se cambia el esquema; posponer salvo que la demo use suizo.

---

## 13. Conclusión y autorización

La aplicación tiene una base funcional real: esquema completo, semillas ricas, autenticación, paneles y navegación pública en su mayoría operativos. **No es un cascarón.** Pero cuatro defectos reproducidos dejan fuera de servicio el detalle de torneo, los perfiles de organizador y la edición de juegos, y la guía de despliegue puede destruir la base. Eso es incompatible con una entrega estable.

Con las correcciones P0 y los P1 de despliegue, el sistema queda en condiciones de demo. El plan de 5 días prioriza exactamente eso, deja los P2 como "si hay tiempo" y excluye reescrituras.

**No se iniciará ninguna corrección hasta recibir autorización expresa.** Al autorizar, se sugiere aprobar también el orden del plan y el criterio de aceptación de cada bloque.

---

## Anexo A — Evidencia de runtime

Servidor usado: `php -S 127.0.0.1:8099 -t codigo_fuente/publico` con variables `DB_HOST=127.0.0.1`, `DB_NAME=ascend`, `DB_USER=ascend_usuario`, `DB_PASSWORD=ascend_password`, `APP_URL=http://127.0.0.1:8099/`.

Errores tomados de `errores_sistema` (agrupados):

```
SQLSTATE[42S22]: Unknown column 'a.descripcion' — Torneo.php:121
SQLSTATE[42S22]: Unknown column 'p.banner_url' — Organizador.php:54
SQLSTATE[42S22]: Unknown column 'p.banner_url' — Organizador.php:79
syntax error, unexpected token "?" — vistas/admin/juegos/juegos_editar.php:27
Trying to access array offset on value of type bool — UsuarioControlador.php:144
Trying to access array offset on value of type bool — UsuarioControlador.php:147
```

## Anexo B — Consultas de verificación de esquema

```sql
-- Columnas ausentes
SHOW COLUMNS FROM torneo_actividades LIKE 'descripcion';          -- 0 filas
SHOW COLUMNS FROM perfiles_organizadores LIKE 'banner_url';       -- 0 filas
SHOW COLUMNS FROM perfiles_organizadores LIKE 'campos_publicos';  -- 0 filas
SHOW COLUMNS FROM juegos LIKE 'imagen_url';                       -- 1 fila

-- Conteos
SELECT COUNT(*) FROM information_schema.tables
 WHERE table_schema='ascend';                                     -- 41
SELECT COUNT(*) FROM usuarios;                                    -- 51
SELECT COUNT(*) FROM torneos;                                     -- 12
```

## Anexo C — Archivos y líneas citados

| Archivo | Líneas | Tema |
|---------|--------|------|
| `codigo_fuente/publico/index.php` | 38-67, 102-106 | Whitelist y 404 inconsistente |
| `codigo_fuente/configuracion/base_de_datos.php` | 3-9 | Sin `DB_PORT` |
| `codigo_fuente/configuracion/constantes.php` | 19-35 | `URL_BASE`/`APP_URL` |
| `codigo_fuente/modelos/Conexion.php` | 21 | DSN sin puerto |
| `codigo_fuente/modelos/Torneo.php` | 105-121, 414, 526-546, 574-584, 870, 907, 926, 1227-1265 | Agenda, historial, publicación, suizo |
| `codigo_fuente/modelos/Organizador.php` | 48-49, 54, 73-74, 79, 141, 150-151 | Perfil de organizador |
| `codigo_fuente/modelos/Equipo.php` | 36-39 y siguientes | Equipos, solicitudes |
| `codigo_fuente/vistas/admin/juegos/juegos_editar.php` | 27 | Parse error |
| `codigo_fuente/vistas/admin/jugadores/jugadores_index.php` | 54-64 | Formulario corrupto |
| `codigo_fuente/vistas/admin/organizadores/organizador_index.php` | 85 | GET vs POST |
| `codigo_fuente/vistas/organizador/administrar-torneo.php` | 518-519 | Formulario truncado |
| `base_de_datos/migraciones/001_ascend.sql` | 43-123, 252-262, 583-587, 616-630, 736, 881, 891, 898 | Esquema y hotfix |
| `docker/docker-compose.yml` | servicios web/db/grafana | Despliegue |
| `Documentos_Juan/Guia_Despliegue_10_Minutos.md` | paso 3 Docker | Reimport destructivo |
