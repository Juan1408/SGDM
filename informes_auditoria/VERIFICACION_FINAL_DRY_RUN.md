# Verificación final — Dry run Docker y estado de entrega

**Fecha:** 2026-10-21  
**Rama:** `deuda_tecnica_implementada` (creada desde `merge_16_10`)  
**Base:** commit `902a2fe` + correcciones de la auditoría incluidas en esta rama  
**Objetivo:** levantar el stack completo desde un volumen vacío con el compose final y dejar evidencia ejecutada para la entrega.

> **Nota de vigencia:** este informe describe el dry run con la semilla `001`–`006`. La migración posterior `008_semilla_consistente.sql` renombró las cuentas `organizador.qaN`/`jugador.qaNN` a correos con nombres reales (por ejemplo `lucia.fernandez@ascend.test`) y amplió torneos, notificaciones y guardados. El listado vigente está en `Documentos_Juan/Usuarios_Sembrados.md`; los nombres “QA” que aparecen aquí corresponden a la evidencia histórica de esa ejecución.

---

## 1. Entorno ejecutado

- Docker Desktop sobre Windows; proyecto Compose `docker`.
- Imágenes fijadas: `php:8.2.29-apache`, `mysql:8.0.46`, `phpmyadmin/phpmyadmin:5.2.2`, `grafana/grafana:11.3.0`.
- Servicios:
  - `sgdm_web` → `http://localhost:8080/`
  - `sgdm_db` → `127.0.0.1:3307` (healthy)
  - `sgdm_phpmyadmin` → `http://127.0.0.1:8081/`
  - `sgdm_grafana` → `http://127.0.0.1:3001/`
- Volumen `docker_sgdm_datos` creado vacío para esta prueba y eliminado al finalizar.

Comandos principales:

```bash
docker compose -f docker/docker-compose.yml up -d --build
docker compose -f docker/docker-compose.yml ps
docker compose -f docker/docker-compose.yml logs db
```

Resultado: los cuatro servicios arrancaron; `sgdm_db` quedó `healthy`. MySQL ejecutó `001`→`005` automáticamente y quedó listo para conexiones.

---

## 2. Base de datos inicializada

Conteos obtenidos dentro del contenedor:

| Elemento | Esperado | Obtenido |
|---|---:|---:|
| Tablas | 41 | **41** |
| Usuarios | 51 | **51** |
| Roles | 4 | **4** |
| Perfiles de organizador | 7 | **7** |
| Perfiles de jugador | 43 | **43** |
| Juegos | 5 | **5** |
| Torneos | 12 | **12** |
| Equipos | 12 | **12** |
| Participantes de torneo | 30 | **30** |
| Encuentros | 16 | **16** |
| Posiciones | 30 | **30** |
| Actividades | 36 | **36** |
| Políticas de contraseña | 1 | **1** |
| Errores de sistema | 0 | **0** |

Nota: el checklist de auditoría previo menciona 12 encuentros; el seed actual tiene **16** porque se agregaron fixtures para que todos los torneos tengan partidos coherentes.

---

## 3. Runtime del contenedor

- PHP **8.2.29**.
- Extensiones presentes: `pdo_mysql`, `mysqli`, `mbstring`, `fileinfo`.
- `mod_rewrite` activo.
- Directorios de subida existentes y escribibles por `www-data`:
  - `img/perfiles/subidos`
  - `img/juegos/subidos`
  - `img/torneos/subidos`

---

## 4. Rutas y flujos verificados por HTTP

### 4.1 Público — 16/16 con HTTP 200

Login, registro, home, juegos, rankings, contacto, nosotros, catálogo, detalle de torneos `1200`, `1201`, `1202`, `1210`, `1209`, perfil público de organizador `2` y `1000`, y perfil público de jugador `3`. Los detalles y perfiles devolvieron contenido (7–44 KB), no pantallas vacías.

### 4.2 Administrador — 18/18 con HTTP 200

Dashboard, usuarios, edición de usuario, torneos, edición de torneo, equipos, auditoría, errores, resultados globales, historial, juegos (lista, crear y editar), organizadores, jugadores, políticas de contraseña, alertas y formatos.

**P0-04 — edición de juego:** guardar sin cambios devolvió `302` con el mensaje “Juego actualizado exitosamente” y persistió la categoría original `esport-shooter`. Se corrigió durante este dry run la lista de categorías del formulario (ver sección 6).

### 4.3 Organizador

- Cuenta demo `organizador@ascend.com`: dashboard, mis torneos, resultados, calendario, historial, perfil, editar perfil y crear torneo → **200**.
- Intentar administrar el torneo ajeno `1200` → **302** (control de propiedad correcto).
- Cuenta QA `organizador.qa1@ascend.test`: administrar `1200` y `1206`, resultados y calendario → **200**.

### 4.4 Jugador — 8/8 con HTTP 200

Dashboard, perfil, editar perfil, equipos, crear equipo, solicitudes, guardados y catálogo público.

### 4.5 Suplantación y modo solo lectura

- “Ver como” jugador `3` → **302** al panel del jugador.
- POST a `guardados/guardar` estando suplantado → **302** al panel, sin insertar el guardado (se verificó en base: 0 filas).
- Restaurar sesión → **302** al dashboard del admin y dashboard posterior **200**.

### 4.6 Error controlado

Excepción forzada con un archivo temporal que usa el manejador real:

- HTTP **500**.
- Página genérica “Ocurrió un error interno”.
- Sin salida parcial (“salida-parcial…” no apareció).
- Sin mensaje de excepción ni stack trace en la respuesta.
- El error se registró y el registro de prueba se eliminó; `errores_sistema` volvió a **0**.

### 4.7 Aislamiento del docroot y assets

Devuelven **404**: `/configuracion/`, `/modelos/`, `/vistas/`, `/core/`, `/tests/`, `/vendor/`, `/.git/`, `/composer.json` y `/configuracion/base_de_datos.php`.

Devuelven **200**: `css/base.css`, `js/featured-games.js`, `img/chess2.jpg`, `img/juegos/uno.jpg`, `img/bn.jpg` y `css/admin/admin_modalidades.css`.

Se escribió y sirvió un archivo de prueba con `www-data` en `img/perfiles/subidos` (HTTP 200) y se eliminó.

### 4.8 Acceso por IP

`http://172.19.96.1:8080/` → **200**, con CSS resuelto como `http://172.19.96.1:8080/css/...`. La detección automática de `URL_BASE` funciona sin fijar `APP_URL`.

### 4.9 Servicios auxiliares

- phpMyAdmin → **200**.
- Grafana → **302** al login (comportamiento normal).
- Logs web sin errores PHP / fatales durante el recorrido.

---

## 5. Pruebas automatizadas complementarias

- PHPUnit 9.6.36 / PHP 8.0.30: **7 tests, 25 assertions, OK** contra MySQL 8 desechable.
- `php -l` sobre todo el PHP: **0 errores**.
- `node --check` sobre todo el JS público: **0 errores**.
- `docker compose config`: válido.

---

## 6. Hallazgo corregido durante el dry run

La edición de un juego no conservaba la categoría de los seeds:

- Los juegos 1 y 2 usan `esport-shooter` y `esport-moba`.
- Los formularios de crear/editar solo ofrecían `esport`, `deporte-fisico` y `juego-mesa`.
- Al guardar sin cambios, la categoría se convertía silenciosamente en `esport`.

Corrección aplicada:

- `codigo_fuente/vistas/admin/juegos/juegos_editar.php`: se agregaron las opciones `esport-shooter` y `esport-moba`.
- `codigo_fuente/vistas/admin/juegos/juegos_crear.php`: mismas opciones.
- `codigo_fuente/vistas/publico/torneos.php`: la categoría genérica `esport` también se mapea al chip `esports`.

Reintento: guardar sin cambios → `302`, mensaje de éxito y categoría `esport-shooter` persistida.

---

## 7. Limitaciones conocidas de esta entrega

1. **Recuperación de contraseña:** no está implementada. El enlace del login se reemplazó por el texto “Recuperación de contraseña no disponible en esta versión”. No hay flujo de email.
2. **Sistema suizo:** admite únicamente cantidades pares de 4 a 64 participantes. No implementa bye; el servidor rechaza cantidades impares y la vista solo ofrece valores pares.
3. **Seed QA:** `003_ascend_seed_relacional_qa.sql` está pensado para una base limpia y no es totalmente idempotente. Sobre una base existente aplicar `005_correcciones_esquema.sql` y, si ya tiene `003`, reaplicar `006_seed_qa_ampliado.sql` (idempotente) para ampliar la cobertura.
4. **Cuenta demo de organizador:** `organizador@ascend.com` no tiene torneos precargados. Para mostrar “Administrar torneo” con datos existentes usar `organizador.qa1@ascend.test` / `password` (torneos `1200`, `1206`, `1212`, `1217`), o crear un torneo durante la demo.
5. **Credenciales de demo:** son públicas y de prueba. No usar este despliegue como producción sin cambiar credenciales, activar HTTPS y revisar el endurecimiento del servidor.
6. **Alcance:** es una entrega académica/MVP. No incluye recuperación de contraseña, notificaciones por email reales ni auditoría de seguridad productiva.

---

## 8. Empaquetado y entrega

Los cambios quedaron commiteados en la rama `deuda_tecnica_implementada`, creada desde `merge_16_10`; no se tocó `main`. El commit incluye los archivos nuevos:

- `base_de_datos/migraciones/005_correcciones_esquema.sql`
- `base_de_datos/migraciones/006_seed_qa_ampliado.sql`
- `codigo_fuente/publico/css/admin/admin_modalidades.css`
- `docker/apache-vhost.conf` (el `Dockerfile` lo copia; sin él falla el build)
- `informes_auditoria/` (incluye este informe)
- `codigo_fuente/configuracion/README.md`

El commit incluye además los 33 archivos modificados del árbol de trabajo.

**Atención con `Documentos_Juan/`:** `.gitignore` ignora esa carpeta (regla `Documentos_Juan/`), por lo que la guía de despliegue editada **no se versionó con Git**. Sigue disponible en el árbol local y el `README.md` cubre el mismo procedimiento. Si se necesita en el repositorio, agregarla con `git add -f Documentos_Juan/Guia_Despliegue_10_Minutos.md` o quitar la regla del `.gitignore`.

---

## 9. Limpieza

Al finalizar:

```bash
docker compose -f docker/docker-compose.yml down -v
```

Se eliminaron los cuatro contenedores, la red `docker_sgdm_red` y el volumen de prueba `docker_sgdm_datos`. No quedan contenedores ni volúmenes `sgdm_*`. Las redes `sgdm_devcontainer_*` que siguen apareciendo en `docker network ls` son del entorno Dev Container, preexistentes, y no se tocaron. No se tocó ningún volumen ni dato previo del estudiante. Los archivos temporales de la prueba se eliminaron.

---

## 10. Ampliación de la semilla QA (`006_seed_qa_ampliado.sql`)

**Motivo:** los 12 torneos de `003` alcanzaban para demostrar el flujo, pero varias pantallas quedaban pobres: ligas de 3 equipos, una sola llave, sin casos individuales, sin solicitudes pendientes y sin guardados. No se reprodujo una pantalla blanca: las 9 URLs públicas de torneos sembrados respondieron `200` y los logs no mostraban errores PHP. La sensación de “pantalla vacía” era falta de densidad de datos, no una caída.

**Contenido agregado (IDs reservados, sin tocar los existentes):**

| Bloque | Qué agrega |
| --- | --- |
| Equipos | 11 equipos (`1112`–`1122`), 5 miembros y capitán cada uno |
| Torneos | `1212` liga de 8 equipos; `1213` eliminación de 8 equipos; `1214` suizo individual de 8; `1215` liga individual de 10; `1216` eliminación individual de 8; `1217` abierto con inscripciones en todos los estados |
| Participantes | 51 inscripciones (`1330`–`1388`), incluidas pendientes, rechazadas y canceladas |
| Encuentros | 71 partidos (`1450`–`1541`): 28 de liga completa, 7 de llave de equipos, 12 de suizo, 15 de liga individual, 7 de llave individual y 2 próximos de `1217` |
| Posiciones | Recalculo para `1212`–`1217` (46 filas) con la misma fórmula de la aplicación |
| Otros | 24 actividades, 12 configuraciones + 3 podios, 14 cambios de estado, 7 notificaciones, 6 guardados, 4 solicitudes de equipo (postulación pendiente, aprobada, rechazada e invitación de capitán pendiente) y 12 detalles de resultado |

**Verificación ejecutada (stack limpio, volumen recreado):**

- El entrypoint ejecutó `001`→`006` en orden. Conteos finales: `41` tablas, `51` usuarios, `18` torneos, `23` equipos, `81` participantes, `87` encuentros y `76` posiciones.
- `006` se reaplicó sobre esa base limpia: los conteos no cambiaron y la invitación `2803` conservó `iniciado_por=1017`, por lo que es idempotente.
- Rutas públicas: catálogo `?c=torneo&a=catalogoPublico` y detalles `?c=torneo&a=detalle&id=1212`…`1217`, todos `200`, con el nombre del torneo y los participantes esperados visibles.
- Paneles de organizador (QA1 a QA5): las 30 combinaciones de `1212`–`1216` con `informacion`, `participantes`, `fixture`, `resultados`, `posiciones` y `cronograma` respondieron `200`; `1217/solicitudes` muestra las 3 inscripciones pendientes. Los podios de `1212`, `1213` y `1216` y las 10 posiciones de `1215` se renderizan.
- Jugador QA 01: `?c=guardados&a=index` y dashboard `200`, con los 3 guardados visibles. Jugador QA 40: `?c=equipo&a=solicitudes` `200`, con la invitación de “Escuadra QA 14” iniciada por “Jugador QA 08” y los botones Aceptar/Rechazar. El capitán de `1112` ve en `?c=equipo&a=gestionar&id=1112` la postulación pendiente de “Jugador QA 27” con Aceptar/Rechazar.
- Rankings públicos por disciplina (`juego=1`, `2`, `5`) y resultados globales como admin: `200`, con equipos y jugadores nuevos.
- En total pasaron 35 comprobaciones funcionales (público, jugador, admin y logins) y las 30 secciones de panel, sin errores HTTP ni PHP embebidos.
- La semilla declara `SET NAMES utf8mb4`; se verificó por `HEX` que los acentos quedan una sola vez codificados y que “Solicitud sintética…” se renderiza correctamente.
- Logs de `web` y `db`: sin fatales, warnings PHP ni errores SQL.
- Controles de integridad: sin posiciones faltantes/huérfanas, sin encuentros huérfanos y sin ganador incoherente respecto del marcador.
- `diferencia_goles` es una columna generada por MySQL; la semilla no la inserta ni la actualiza.

**Limitación preexistente:** `1204` y `1209` siguen siendo torneos de Ajedrez (juego `5`) con modalidad de equipos, aunque el catálogo solo habilita individual para ese juego. No se modificaron porque `003` no es idempotente y cambiarlos obligaría a rehacer participantes y encuentros; `006` no agrega casos nuevos de ese tipo.

---

## 11. Conclusión

**Nivel demo/MVP: apto para entregar y demostrar.** Los P0/P1 auditados quedaron verificados en un stack limpio, con base inicializada, rutas críticas, permisos, aislamiento, error 500 y acceso por IP.

**Nivel entrega final cerrada:** el empaquetado quedó resuelto en la rama `deuda_tecnica_implementada`; restan las limitaciones documentadas en la sección 7. **No es un despliegue de producción.**
