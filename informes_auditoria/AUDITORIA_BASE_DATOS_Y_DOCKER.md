# Auditoría de base de datos y despliegue — SGDM / ASCEND

**Fecha:** 2026-10-21 · **Rama:** `merge_16_10` (`902a2fe`)
**Método:** importación real en MySQL 8 limpio, consultas de verificación, inspección del compose/imagen y revisión de guías. **No se usaron datos reales ni se ejecutaron pasos destructivos fuera del contenedor descartable.**

---

## 1. Resumen

| Área | Estado | Detalle |
|------|--------|---------|
| Esquema | ✅ Carga completa | 41 tablas, 4 scripts, sin errores en MySQL 8 |
| Semillas | ⚠️ Cargadas pero incoherentes | 51 usuarios, 12 torneos; datos QA con contradicciones |
| Columnas vs modelos | ❌ Deriva | 3 columnas faltantes que rompen pantallas (P0-02/P0-03) |
| Idempotencia | ❌ No | `001` destruye; `002` sin transacción; `003` con IDs fijos |
| Docker arranque | ⚠️ Parcial | `web`/`db` levantan; Grafana choca en 3000; URL fija a localhost |
| Docroot | ❌ Expuesto | Todo el repositorio servido, `AllowOverride None`, listado activo |
| Guía de despliegue | ❌ Destructiva | Reimporta `001` después del entrypoint |
| Modo híbrido XAMPP+Docker | ❌ No soportado | Falta `DB_PORT`; 3307 inaccesible desde el DSN |
| Pruebas | ⚠️ 6/7 | Fixture de `TorneoTest` desactualizado |

---

## 2. Inventario del esquema

### 2.1 Orden de migraciones

| Archivo | Contenido | Observaciones |
|---------|-----------|---------------|
| `001_ascend.sql` | `CREATE DATABASE`, 41 tablas, DCL, semillas base y un bloque "hotfix" al final | **Empieza con `DROP TABLE` de todo** (líneas 43-123). Define `politicas_contrasenas` dos veces (736 y 898). Crea `guardados` e `intentos_login` solo al final (881/891) |
| `002_datos_ejemplo.sql` | Roles, permisos, usuarios, perfiles, juegos, modalidades, formatos | Sin `USE ascend;` ni transacción explícita. Usa `INSERT IGNORE` en gran parte |
| `003_ascend_seed_relacional_qa.sql` | 12 torneos, equipos, participantes, encuentros, posiciones, actividades, notificaciones, auditoría | `START TRANSACTION` … `COMMIT`; IDs reservados desde 1000. 146 KB |
| `004_actualizar_auth.sql` | `intentos_login`, `politicas_contrasenas` | `CREATE TABLE IF NOT EXISTS`; no altera filas existentes |

**Docker:** el entrypoint de `mysql:8.0` ejecuta los `.sql` montados en `/docker-entrypoint-initdb.d` **solo si el directorio de datos está vacío**. No hay runner incremental ni tabla de migraciones.

### 2.2 Conteos verificados (MySQL 8 limpio, 001→004)

| Tabla | Filas | Tabla | Filas |
|-------|-------|-------|-------|
| `information_schema.tables` (ascend) | **41** | `usuarios` | **51** |
| `roles` | 4 | `perfiles_organizadores` | 7 |
| `perfiles_jugadores` | 43 | `juegos` | 5 |
| `juego_modalidades_permitidas` | 5 | `juego_formatos_permitidos` | 15 |
| `torneos` | 12 | `equipos` | 12 |
| `participantes_torneo` | 30 | `torneo_encuentros` | 12 |
| `torneo_posiciones` | 30 | `torneo_actividades` | 36 |
| `notificaciones` | 126 | `politicas_contrasenas` | 2 |

**Credenciales verificadas:** `admin@ascend.com`, `organizador@ascend.com`, `jugador@ascend.com` con `Admin123!`. Usuarios QA con `password`.

### 2.3 Deriva esquema ↔ modelos (causa de P0)

| Columna consultada | Tabla | ¿Existe en 001? | Modelo que la usa | Efecto |
|--------------------|-------|-----------------|-------------------|--------|
| `descripcion` | `torneo_actividades` | ❌ | `Torneo.php:105-121, 526-546, 870, 907, 926`; `guardarActividades` 512-557 | Detalle público en blanco y creación/edición de actividades falla |
| `banner_url` | `perfiles_organizadores` | ❌ | `Organizador.php:48-49, 73-74, 141, 150-151` | Perfil público y panel en blanco |
| `campos_publicos` | `perfiles_organizadores` | ❌ | `Organizador.php` | Igual |
| `imagen_url` | `juegos` | ✅ | `Juego.php` | OK |
| `banner_url` / `campos_publicos` | `perfiles_jugadores` | ✅ | `Jugador.php` | OK — confirma la asimetría |

**Conclusión:** no es un problema de datos ni de conexión; es una migración incompleta. La tabla hermana de jugadores ya tiene las columnas, lo que demuestra la intención del diseño.

### 2.4 Otros defectos de esquema

| ID | Defecto | Evidencia | Riesgo |
|----|---------|-----------|--------|
| DB-01 | `torneo_suizo_parejas` PK `(torneo_id, participante_a_id, participante_b_id)` sin `ronda` | 001:583-587 | Dos rondas con la misma pareja violan PK; el fixture suizo falla |
| DB-02 | `politicas_contrasenas` declarada dos veces con definiciones divergentes | 001:736 y 898 | La segunda definición no se aplica si la tabla existe; confusión y columnas distintas según el orden |
| DB-03 | `guardados` e `intentos_login` creadas al final (hotfix), no en el bloque principal | 001:881, 891 | Si se recorta el script o se ejecuta parcialmente, faltan tablas que el código usa |
| DB-04 | `DROP TABLE` masivo al inicio | 001:43-123 | Cualquier reimportación borra datos; incompatible con "actualizar" |
| DB-05 | `SET FOREIGN_KEY_CHECKS=0` y luego 1 | 001 | Correcto para recrear, peligroso si se corta a mitad |
| DB-06 | DCL crea `ascend_app@localhost` y `ascend_audit@localhost` | 001 | No se usan: la app usa `ascend_usuario@%` del compose; los usuarios locales quedan sin propósito |
| DB-07 | Colación efectiva `utf8mb4_0900_ai_ci` en Docker vs `utf8mb4_unicode_ci` declarada | `CREATE DATABASE IF NOT EXISTS` no cambia una BD ya creada por el entrypoint | Diferencias de orden/comparación entre MySQL 8 y MariaDB 10.4 (XAMPP) |
| DB-08 | `002` sin `USE ascend;` | Cabecera | Import manual en otra base falla; con `mysql ... ascend` funciona |
| DB-09 | `003` con IDs fijos y `INSERT IGNORE` | 003 | Reimportar sobre datos del estudiante mezcla filas silenciosamente |
| DB-10 | `002` sin transacción | 002 | Import parcial deja usuarios/perfiles a medias |

---

## 3. Coherencia de las semillas

Se verificaron reglas deportivas básicas sobre los datos QA:

| Verificación | Resultado | Interpretación |
|--------------|-----------|----------------|
| Posiciones con PJ = G+E+P | **18/30 no cumplen** | Tablas de posiciones inconsistentes |
| Torneos `en_curso` con su único encuentro `finalizado` | 1201, 1205, 1209 | Estado de torneo vs partidos contradictorio |
| Torneos individuales con participantes de equipo | 1204, 1209 | Modalidad mal aplicada |
| Eliminación directa con 3 participantes | 1201, 1210 | Bracket inválido |
| Políticas de contraseña duplicadas | ids 1 y 1000 | Dos políticas activas; ¿cuál usa el código? |
| Notificaciones | 126 | Volumen alto pero coherente con 51 usuarios |

**Impacto:** si la demo muestra rankings, posiciones o el detalle de esos torneos, los números pueden ser absurdos. No es un bloqueante de arranque, pero sí de credibilidad. **No se propone corregir el esquema para esto**, solo los datos o la selección de torneos a mostrar.

**Cobertura de pruebas:** los usuarios QA usan el hash bcrypt de `password`; los tres principales usan `Admin123!`. El login de los tres se probó con éxito.

---

## 4. Docker y despliegue

### 4.1 Topología del compose

| Servicio | Imagen | Puerto host | Notas |
|----------|--------|-------------|-------|
| `web` | build `docker/Dockerfile` (`php:8.2-apache`) | **8080:80** | Monta `../codigo_fuente:/var/www/html`; variables DB y `APP_URL=http://localhost:8080/publico/`; crea/chown de carpetas de subida; depende de `db` healthy |
| `db` | `mysql:8.0` | **3307:3306** | root/root123; app `ascend_usuario`; volumen `sgdm_datos`; migraciones read-only en el entrypoint |
| `phpmyadmin` | `phpmyadmin:latest` | **8081:80** | Depende de db |
| `grafana` | `grafana/grafana:latest` | **3000:3000** | Servicio ajeno al núcleo; sin provisión de dashboards en el compose |

`docker compose config` es válido; proyecto `docker`; volumen `docker_sgdm_datos`.

### 4.2 Hallazgos de despliegue

#### D-01 — Guía destructiva (P0-01)

`Guia_Despliegue_10_Minutos.md`, paso 3 de Docker, reimporta `001_ascend.sql` con `Get-Content ... | docker exec ...`. Como `001` hace `DROP TABLE`, se pierden las semillas de `002`/`003` y cualquier dato. **No se ejecutó** por ser destructivo; la evidencia es el SQL y el flujo del entrypoint.

#### D-02 — `DB_PORT` ausente (P1-01)

`base_de_datos.php` no lee `DB_PORT`; `Conexion.php:21` no lo concatena. El modo documentado "XAMPP + solo el contenedor db" queda apuntando a `localhost:3306` (MySQL/MariaDB de XAMPP), no al 3307 del contenedor.

#### D-03 — `APP_URL` fija (P1-02)

`APP_URL=http://localhost:8080/publico/` se inyecta en el compose. `constantes.php` la prioriza. En una demo desde otra máquina/IP/Proxmox, todos los enlaces y assets apuntan al localhost del visitante.

#### D-04 — Docroot y listado (P1-03)

Imagen `docker-web` inspeccionada:

```
DocumentRoot /var/www/html
<Directory /var/www/html>
    AllowOverride None
    Options Indexes FollowSymLinks
```

Con el bind mount en `/var/www/html`, se sirven `configuracion/`, `modelos/`, `vistas/`, `core/`, `tests/` y el listado de directorios. `configuracion/base_de_datos.php` no imprime credenciales al solicitarse (es un `return`), pero expone estructura; otros archivos podrían filtrar rutas o lógica. El `.htaccess` de la raíz no se lee y apunta a un `index.php` inexistente; `codigo_fuente/publico/.htaccess` no existe.

#### D-05 — Grafana y puerto 3000 (P1-04)

En la máquina auditada el puerto 3000 está ocupado por `node.exe` (PID 23336). `docker compose up` falla al publicar Grafana. No es necesario para la app; conviene un perfil opcional o el puerto 3001.

#### D-06 — Healthcheck y primer arranque

`db` tiene healthcheck, pero sin `start_period`; en un equipo lento MySQL puede agotar reintentos. El entrypoint solo corre migraciones con volumen vacío: si se cambia un `.sql`, no se reaplica. No hay runner de migraciones.

#### D-07 — Imágenes flotantes

`mysql:8.0`, `php:8.2-apache`, `phpmyadmin:latest`, `grafana/grafana:latest`. Para una entrega reproducible conviene fijar versiones/digests. `phpmyadmin:latest` puede cambiar de comportamiento entre demos.

#### D-08 — Uploads y permisos

El compose crea y hace `chown` de las carpetas de imágenes. En un bind mount Windows→Linux, los archivos nuevos pueden quedar con propietario root; si el proceso Apache es `www-data`, podría no poder escribir. No se probó un upload real. Verificar en Día 3.

#### D-09 — `COMANDOS_DOCKER.md` desactualizado

- Propone ejecutar `db` solo y conectar XAMPP (ver D-02).
- Usa `/var/www/html/../desbloquear.php`, fuera del bind mount (`/var/www/html` es `codigo_fuente`, no la raíz del repo).
- No menciona el paso destructivo de la guía principal.

#### D-10 — `.htaccess` y reescritura

El `.htaccess` de la raíz (`RewriteRule ^ index.php [QSA,L]`) no tiene un `index.php` en la raíz. En XAMPP, si el docroot es `SGDM`, la regla lleva a un 404; la app funciona igual por la URL larga `codigo_fuente/publico/index.php?...`. En Docker, `AllowOverride None` lo ignora. **No hay URLs limpias en ningún entorno.**

---

## 5. Pruebas de base de datos ejecutadas

| Prueba | Comando | Resultado |
|--------|---------|-----------|
| Inicialización limpia | `docker run mysql:8.0` con migraciones montadas | 001→004 sin errores; 41 tablas |
| Conteos | `SELECT COUNT(*)` por tabla | Ver §2.2 |
| Columnas faltantes | `SHOW COLUMNS ... LIKE` | 3 ausentes, 1 presente |
| Coherencia de posiciones | `SELECT ... WHERE PJ <> G+E+P` | 18/30 |
| Estados vs partidos | join torneos/encuentros | 1201/1205/1209 contradiction |
| Credenciales | login real de 3 roles | OK |
| MariaDB 10.4 | — | **No ejecutada** |
| Reimportación idempotente | — | **No ejecutada** (destructiva) |

### 5.1 Limitaciones

- No se importó en MariaDB 10.4 (XAMPP). El esquema usa `utf8mb4_0900_ai_ci` efectivo y JSON; MariaDB 10.4 soporta JSON como alias de LONGTEXT, pero pueden cambiar comportamientos. **Probar antes de una entrega en XAMPP puro.**
- No se probó la concurrencia de inscripciones ni el suizo con datos nuevos.
- No se probó un upload real de imágenes.
- No se probó el arranque completo `docker compose up` por el conflicto de puerto 3000; se validó la configuración y se levantó MySQL de forma aislada.

---

## 6. Recomendaciones de despliegue (mínimas, para el plan)

1. **Guía:** eliminar la reimportación de `001`; verificar conteos después del primer arranque. Para recrear: `docker compose down -v` (con advertencia explícita).
2. **`DB_PORT`:** agregar con default 3306; usar 3307 en el modo híbrido.
3. **`APP_URL`:** dejar vacía por defecto; usar `${APP_URL:-}` en compose; documentar cómo fijarla a la IP de demo.
4. **Docroot:** servir `publico/` como raíz; probar 10 rutas y assets. Si no se puede, agregar `.htaccess` en `publico/` y `AllowOverride All`.
5. **Grafana:** perfil opcional o puerto 3001.
6. **Migraciones:** agregar un `README` que explique "solo se ejecutan con volumen vacío"; no hace falta runner para la entrega, pero sí advertirlo.
7. **Semillas:** limpiar los datos QA incoherentes o excluir esos torneos de la demo.
8. **Prueba MariaDB:** importar 001→004 en MariaDB 10.4 y ejecutar las 10 rutas críticas.

---

## 7. Criterios de aceptación de la fase de despliegue

- [ ] Desde un clon limpio: `docker compose up -d` deja `web` y `db` saludables y la app responde en `http://localhost:8080/publico/`.
- [ ] `GET /configuracion/`, `/modelos/`, `/vistas/` no listan ni sirven archivos internos.
- [ ] `http://IP:8080/publico/` carga CSS/JS y navega sin enlaces a localhost.
- [ ] Los 4 scripts se ejecutan una sola vez; los conteos coinciden con §2.2.
- [ ] El modo XAMPP+Docker (si se conserva) conecta al 3307 con `DB_PORT`.
- [ ] Ninguna guía contiene un paso que borre la base después de inicializarla.
