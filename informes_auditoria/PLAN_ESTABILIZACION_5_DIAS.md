# Plan de estabilización de 5 días — SGDM / ASCEND

**Base:** informe de auditoría del 2026-10-21 (rama `merge_16_10`, `902a2fe`).
**Objetivo:** llegar a una demo estable y reproducible, con los flujos visibles funcionando, sin reescribir módulos ni agregar funcionalidades.
**Regla de oro:** primero evidencia, después priorización, luego corrección mínima, al final regresión y aceptación.

---

## 1. Supuestos y capacidad

- Equipo sugerido: **Dev A** (backend/modelos), **Dev B** (vistas/admin), **Dev C** (infra/BD), **QA** (puede ser rotativo). Si hay una sola persona, el orden y las dependencias se mantienen; los esfuerzos se suman.
- Jornada de 6 h efectivas. Total del plan: ~55-60 h de trabajo, con holgura para regresión.
- **No se incluye** rediseño visual, nuevas features, reescritura de MVC, cambio de framework ni auditoría de seguridad completa.
- **No se toca** el esquema salvo las 3 columnas demostradas como faltantes (P0-02/P0-03). Cualquier otro cambio requiere justificación y aprobación.
- Ramas: una rama de estabilización por bloque o commits pequeños sobre la rama actual, según lo autorice el responsable. No se cambia de rama ni se descartan cambios de los estudiantes.

---

## 2. Resumen de días y puertas de calidad

| Día | Foco | Entregable | Puerta de salida |
|-----|------|------------|------------------|
| 1 | P0 de BD y pantallas blancas + entorno limpio | Columnas, parse error, guía corregida | Detalle de torneo, perfiles de organizador y edición de juego funcionan; home/login/catálogo OK |
| 2 | CRUDs rotos y errores que engañan | Formularios admin, actividad, manejador 500 | Admin puede bloquear/eliminar; organizador edita actividad; errores ya no son 200 vacíos |
| 3 | Despliegue reproducible | Docker docroot, `DB_PORT`, `APP_URL`, Grafana, guía única | `docker compose up` limpio + 10 rutas de humo + acceso por IP |
| 4 | Flujos de negocio y P2 selectivos | Inscripción, equipos, fixture/resultados, semillas | Flujo completo de un torneo de principio a fin sin errores |
| 5 | Regresión, documentación y congelamiento | Checklist completo, demo ensayada, tag/entrega | 0 P0/P1 abiertos; checklist firmado |

**Congelamiento:** al final del Día 5 no se aceptan cambios funcionales; solo bloqueantes demostrados.

---

## 3. Día 1 — Bloqueantes de datos y pantallas blancas

**Meta:** que las tres pantallas actualmente en blanco rendericen y que la instalación limpia no se autodestruya.

| ID | Tarea | Responsable | Archivos | Depende de | Esfuerzo | Criterio de aceptación | Fallback |
|----|-------|-------------|----------|------------|----------|------------------------|----------|
| D1-01 | Agregar `descripcion TEXT NULL` a `torneo_actividades` en `001` + script `ALTER` idempotente para bases existentes | Dev C | `base_de_datos/migraciones/001_ascend.sql`; nuevo `base_de_datos/migraciones/005_fix_columnas.sql` (o bloque al final de 001) | — | 1 h | `SHOW COLUMNS` devuelve la columna; detalle `1200/1201/1202/1210` 200 con contenido; sin nuevos errores | Si no se autoriza migración nueva, aplicar `ALTER` manual documentado y dejar 001 listo para bases nuevas |
| D1-02 | Agregar `banner_url TEXT NULL` y `campos_publicos VARCHAR(255) NOT NULL DEFAULT ''` a `perfiles_organizadores` (mismos tipos que `perfiles_jugadores`) | Dev C | `001_ascend.sql`, script del D1-01 | — | 1 h | Perfil público id 2/1000 y panel perfil/editar 200 con contenido; guardar perfil sin error | Igual que D1-01 |
| D1-03 | Corregir `?"` → `?>` en la vista de edición de juego | Dev B | `codigo_fuente/vistas/admin/juegos/juegos_editar.php:27` | — | 15 min | `php -l` OK; formulario visible; guardar sin cambios redirige y persiste | Ninguno; es un carácter |
| D1-04 | Quitar el paso destructivo de la guía y documentar el arranque limpio | Dev C | `Documentos_Juan/Guia_Despliegue_10_Minutos.md`; `COMANDOS_DOCKER.md` si aplica | D1-01/02 | 1 h | La guía no reimporta 001; incluye verificación de conteos; `down -v` solo como opción explícita | Dejar una nota de advertencia si no se reescribe la guía completa |
| D1-05 | Verificar entorno limpio: base descartable + servidor PHP; correr 12 rutas críticas | QA | — | D1-01/02/03 | 2 h | 12/12 OK; `errores_sistema` sin las 3 SQLSTATE ni el parse error | Si falla, congelar y depurar antes de avanzar |
| D1-06 | Actualizar fixture de `TorneoTest` con `juegos.categoria` | Dev A | `tests/.../TorneoTest.php` | — | 30 min | `phpunit` 7/7 | Marcar el test como skip documentado si el fixture no es confiable |

**Puerta Día 1:** P0-02, P0-03 y P0-04 cerrados con evidencia de runtime; P0-01 corregido en documentación. Si alguna columna genera un efecto colateral (por ejemplo, una vista que asumía otra estructura), se detiene y se analiza antes de seguir.

**Riesgos:** el `ALTER` sobre una base con datos debe ser no destructivo. Probar primero en la base descartable. No ejecutar `down -v` sobre el volumen del estudiante.

---

## 4. Día 2 — CRUDs y errores que engañan

**Meta:** que las acciones visibles de admin y organizador funcionen o fallen con un mensaje claro.

| ID | Tarea | Responsable | Archivos | Depende de | Esfuerzo | Criterio de aceptación | Fallback |
|----|-------|-------------|----------|------------|----------|------------------------|----------|
| D2-01 | Convertir "Bloquear/Activar" organizador en formulario POST con CSRF | Dev B | `vistas/admin/organizadores/organizador_index.php:85`; `UsuarioControlador.php:38-44` | D1 | 1 h | Acción cambia estado y redirige sin 405; CSRF validado | Si no se puede tocar la vista, aceptar POST en el controlador con token en query (menos ideal) |
| D2-02 | Reparar formulario de eliminación de jugador (CSRF + `rol_id`) | Dev B | `vistas/admin/jugadores/jugadores_index.php:54-64`; controlador | D1 | 1 h | Borrar un jugador de prueba funciona; sin texto corrupto en el HTML | Ocultar la acción si no se llega; no dejar un formulario roto |
| D2-03 | Completar el `action` del formulario de edición de actividad | Dev B | `vistas/organizador/administrar-torneo.php:518-519` | D1-01 | 30 min | Editar una actividad persiste y redirige; sin error PHP | Ocultar la edición si no se valida |
| D2-04 | Manejador global: buffer limpio, HTTP 500 y página mínima | Dev A | `codigo_fuente/core/ManejadorErrores.php`; `publico/index.php` | — | 2 h | Excepción forzada → 500 + mensaje genérico + registro; ninguna ruta OK se ve afectada | Mantener log y agregar `error_log` si el 500 rompe algún flujo |
| D2-05 | Corregir `crearEquipo` para insertar `juego_id` y validar que exista | Dev A | `modelos/Equipo.php:36-39`; controlador | — | 1 h | Equipo nuevo tiene juego; listados/filtros correctos | Si el formulario no envía juego, agregar el select |
| D2-06 | Re-solicitud a equipo: `ON DUPLICATE KEY UPDATE` a `pendiente` | Dev A | `modelos/Equipo.php::crearSolicitud` | — | 1 h | Un jugador rechazado puede volver a solicitar | Si el UNIQUE no se puede cambiar, borrar la solicitud rechazada antes de insertar |
| D2-07 | Validar destinatario/iniciador en respuestas de invitación/solicitud y membresía en `hacerCapitan` | Dev A | `modelos/Equipo.php` | — | 2 h | Un usuario ajeno recibe error; no puede nombrar capitán a un no miembro | Si el tiempo aprieta, priorizar `hacerCapitan` y dejar las respuestas con validación mínima |
| D2-08 | Revisión de `php -l` y `node --check` tras los cambios | QA | — | D2-01…07 | 1 h | 104 PHP OK (salvo archivos nuevos con error), 0 errores en JS referenciados | — |

**Puerta Día 2:** admin puede bloquear y eliminar; organizador puede editar actividad; los errores de servidor ya no devuelven 200 vacío.

**Riesgos:** los formularios admin pueden tener más campos corruptos no detectados. Revisar el HTML generado de las tres vistas con `curl`/navegador antes de firmar.

---

## 5. Día 3 — Despliegue reproducible

**Meta:** que un compañero o el docente pueda levantar la app con un solo comando y acceder desde otra máquina.

| ID | Tarea | Responsable | Archivos | Depende de | Esfuerzo | Criterio de aceptación | Fallback |
|----|-------|-------------|----------|------------|----------|------------------------|----------|
| D3-01 | Agregar `DB_PORT` (default 3306) a configuración y DSN | Dev C | `configuracion/base_de_datos.php`, `modelos/Conexion.php:21` | — | 30 min | XAMPP sin `DB_PORT` sigue OK; híbrido con 3307 conecta | — |
| D3-02 | `APP_URL` opcional (`${APP_URL:-}`) y documentación de la URL de demo | Dev C | `docker/docker-compose.yml`, `constantes.php` | — | 30 min | Acceso por IP carga assets y enlaces correctos | Fijar la IP en un `.env` de demo |
| D3-03 | Servir `publico/` como docroot y desactivar listado | Dev C | `docker/Dockerfile`, `docker/docker-compose.yml` | D1 | 2 h | `/configuracion/`, `/modelos/`, `/vistas/` no listan ni sirven; app navega | Si el docroot rompe rutas, usar `.htaccess` en `publico/` + `AllowOverride All` |
| D3-04 | Grafana opcional o puerto 3001 | Dev C | `docker-compose.yml` | — | 15 min | `docker compose up -d` sin error de puerto | Quitar Grafana del compose de entrega o dejarlo con perfil |
| D3-05 | Healthcheck con `start_period` y documentar que las migraciones solo corren con volumen vacío | Dev C | `docker-compose.yml`; README | — | 30 min | Primer arranque estable; documentación clara | — |
| D3-06 | Prueba de humo Docker: 10 rutas + login + una escritura | QA | — | D3-01…05 | 3 h | 10/10 OK; escritura persiste tras reinicio | Si falla el docroot, volver atrás y usar el fallback D3-03 |
| D3-07 | Importar 001→004 en MariaDB 10.4 (XAMPP) y correr 10 rutas | QA | — | D1 | 2 h | Sin errores de sintaxis/colación; rutas OK | Si MariaDB falla, documentar que la entrega es MySQL 8/Docker y no prometer XAMPP puro |
| D3-08 | Corregir rutas y advertencias de `COMANDOS_DOCKER.md` | Dev C | `COMANDOS_DOCKER.md` | D3-01…05 | 1 h | Ninguna instrucción apunta fuera del bind mount ni reimporta 001 | — |

**Puerta Día 3:** `docker compose up -d` desde clon limpio → app usable en `localhost:8080/publico/` y por IP; sin exposición de internals; XAMPP documentado o descartado explícitamente.

**Riesgos:** cambiar el docroot puede romper rutas relativas de assets. Mitigación: prueba de humo con `curl` a CSS/JS y 10 rutas antes de commit. No cambiar el esquema en este día.

---

## 6. Día 4 — Flujos de negocio y P2 selectivos

**Meta:** demostrar un torneo de principio a fin, con datos coherentes.

| ID | Tarea | Responsable | Archivos | Depende de | Esfuerzo | Criterio de aceptación | Fallback |
|----|-------|-------------|----------|------------|----------|------------------------|----------|
| D4-01 | Probar y corregir inscripción a torneo (cupo, duplicado, estado) | Dev A | `Torneo.php`, `TorneoControlador.php` | D1 | 2 h | Jugador se inscribe una vez; cupo lleno rechaza con mensaje | Si no se llega, restringir la demo a un torneo con cupo |
| D4-02 | Flujo de equipo completo: crear → solicitar → aceptar/rechazar → reintentar → capitanear → expulsar/dejar | Dev A/QA | `Equipo.php`, `EquipoControlador.php` | D2-05/06/07 | 3 h | Todos los pasos con mensajes claros; sin 500 | Si falla un paso, documentarlo y no mostrarlo en la demo |
| D4-03 | Fixture de liga y eliminación con 4/8 participantes; suizo con 4 y 5 (bye) | Dev A | `Torneo.php` | D4-01 | 3 h | Fixture generado sin colisiones; suizo impar no rompe | Si el suizo falla, no ofrecerlo en la demo y documentar la limitación |
| D4-04 | Guardar resultado y podio en transacción; verificar posiciones | Dev A | `Torneo.php` | D4-03 | 2 h | Resultado y podio consistentes; sin filas parciales | Si no se llega, guardar solo resultados y no mostrar podio |
| D4-05 | Historial: contar estado real (`confirmado`) y verificar | Dev A | `Torneo.php:414` | — | 30 min | Historial muestra participantes correctos | — |
| D4-06 | Limpiar semillas QA incoherentes o excluir torneos problemáticos de la demo | Dev C/QA | `003_ascend_seed_relacional_qa.sql` (solo si se autoriza) | — | 2 h | Posiciones PJ=G+E+P; estados coherentes; modalidad correcta | No tocar datos: elegir 3 torneos coherentes para la demo |
| D4-07 | Cubrir recuperación de contraseña: quitar enlace o implementar | Dev B | `vistas/auth/login.php` | — | 15 min (quitar) / 4 h (implementar) | Ningún enlace 404 | Quitar el enlace y rotular "no disponible" |
| D4-08 | Revisar assets faltantes (`chess.jpg`, CSS de modalidades) | Dev B | vistas citadas | — | 30 min | Sin 404 en la consola del navegador en las rutas de demo | Copiar el asset existente con el nombre esperado |

**Puerta Día 4:** un torneo completo (crear → inscribir → fixture → resultado → ranking) sin errores; datos de la demo coherentes.

**Riesgos:** el suizo y el podio son los puntos más frágiles. Si el tiempo aprieta, **no se muestra suizo**; se prioriza liga/eliminación. No se cambia la PK sin pruebas.

---

## 7. Día 5 — Regresión, documentación y congelamiento

| ID | Tarea | Responsable | Esfuerzo | Criterio |
|----|-------|-------------|----------|----------|
| D5-01 | Regresión completa de la matriz de rutas (47 verificadas + POST críticos) | QA | 4 h | 0 P0/P1 abiertos; rutas de demo 100 % |
| D5-02 | Ejecutar `php -l`, `node --check`, PHPUnit | QA | 1 h | 104 PHP OK; 0 errores JS referenciados; 7/7 tests |
| D5-03 | Prueba desde cero en otra carpeta/clon + Docker | QA/Dev C | 2 h | Mismos conteos y rutas; guía reproducible |
| D5-04 | Ensayo de demo cronometrado (10-12 min) | Todo el equipo | 2 h | Guion sin pasos rotos; datos precargados |
| D5-05 | Actualizar README/guías y checklist de entrega | Dev C | 1 h | Documentación coincide con el código |
| D5-06 | Congelar: tag o commit de entrega; no más cambios | Dev A | 15 min | Tag creado; árbol limpio salvo informes |

**Puerta Día 5:** checklist de `CHECKLIST_ENTREGA_FINAL.md` completo; demo ensayada; limitaciones documentadas (p. ej. recuperación de contraseña, suizo si no se corrigió).

---

## 8. Priorización si el tiempo se reduce

### Escenario 3 días

1. Día 1 completo (P0).
2. Día 2: D2-01 a D2-04 (formularios críticos + manejador).
3. Día 3: D3-01, D3-02, D3-04, D3-06 + regresión.

**Quedan fuera:** equipos, fixture suizo, semillas QA, recuperación de contraseña. La demo se limita a lo verificado.

### Escenario 1 día (solo demo)

- D1-01, D1-02, D1-03 (P0).
- D1-04 (quitar paso destructivo).
- D2-01/D2-02 si hay 2 h extra.
- Congelar y ensayar.

**No se debe presentar** sin los tres P0 de runtime corregidos: el detalle de torneo, el perfil de organizador y la edición de juego son visibles en cualquier recorrido.

---

## 9. Qué NO se hará en estos 5 días

- Reescribir el MVC, migrar a un framework o cambiar el ruteo.
- Agregar funcionalidades nuevas (salvo decisión explícita sobre recuperación de contraseña).
- Eliminar módulos para ocultar errores.
- Cambiar el esquema más allá de las 3 columnas demostradas.
- Hacer una auditoría de ciberseguridad completa.
- Tocar el volumen `sgdm_datos`, los datos locales del estudiante o ramas compartidas.
- Instalar dependencias nuevas sin justificación.

---

## 10. Tablero de seguimiento sugerido

| ID | Prioridad | Estado | Evidencia de cierre |
|----|-----------|--------|---------------------|
| P0-01 | Bloqueante | Pendiente | Guía sin reimport + conteos |
| P0-02 | Bloqueante | Pendiente | Detalle 200 con contenido |
| P0-03 | Bloqueante | Pendiente | Perfil organizador 200 con contenido |
| P0-04 | Bloqueante | Pendiente | Formulario de juego visible |
| P1-01…06 | Alto | Pendiente | Checklist Día 3 + manejador |
| P2-01…20 | Medio | Selectivo | Día 4 |
| P3 | Bajo | No comprometido | — |

Cada cierre debe incluir: comando ejecutado, resultado observado y archivo/línea modificada. **No se marca "verificado" por inspección.**
