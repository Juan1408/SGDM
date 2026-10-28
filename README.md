# ASCEND / SGDM

Sistema de gestión de torneos multideportivo con arquitectura MVC en PHP 8 y MySQL 8.

## Estructura

```
SGDM/
├── codigo_fuente/
│   ├── publico/          # DocumentRoot: index.php, CSS, JS e imágenes
│   ├── controladores/    # Controladores por rol (admin, organizador, jugador)
│   ├── modelos/          # Acceso a datos (PDO) y reglas de negocio
│   ├── vistas/           # Plantillas PHP
│   ├── configuracion/    # Constantes y conexión a la base
│   └── core/             # Sesión, CSRF, errores
├── base_de_datos/
│   └── migraciones/      # 001 → 011 (esquema, semillas, triggers, consistencia, escudos, plantillas y aceptaciones legales)
├── docker/               # Dockerfile, vhost de Apache y docker-compose.yml
├── tests/                # PHPUnit (unitarias e integración)
└── publico/              # Recursos estáticos heredados (no se sirven en Docker)
```

## Requisitos

- Docker y Docker Compose, o
- PHP 8.0+ y MySQL 8 / MariaDB 10.4+ (XAMPP).

## Arranque con Docker (recomendado)

Desde la raíz del repositorio:

```bash
docker compose -f docker/docker-compose.yml up -d --build
docker compose -f docker/docker-compose.yml ps
```

Servicios:

| Servicio | URL / puerto | Notas |
| --- | --- | --- |
| Aplicación | http://localhost:8080/ | Apache sirve `codigo_fuente/publico` como raíz |
| MySQL | 127.0.0.1:3307 | Contenedor `db`, base `ascend` |
| phpMyAdmin | http://127.0.0.1:8081/ | Solo accesible desde el host |
| Grafana | http://127.0.0.1:3001/ | Monitoreo opcional |

La base se inicializa **solo la primera vez** que se crea el volumen `sgdm_datos`. MySQL ejecuta automáticamente los archivos de `base_de_datos/migraciones/` en orden alfabético:

1. `001_ascend.sql` — esquema completo.
2. `002_datos_base.sql` — catálogos y datos base.
3. `003_ascend_seed_relacional_qa.sql` — datos sintéticos de QA (solo para una base limpia; no es idempotente).
4. `004_actualizar_auth.sql` — tablas de autenticación.
5. `005_correcciones_esquema.sql` — correcciones idempotentes.
6. `006_seed_qa_ampliado.sql` — ampliación idempotente de la semilla QA (más torneos, formatos y estados).
7. `007_restricciones_disciplinas.sql` — triggers que validan modalidad y formato contra el catálogo de cada juego.
8. `008_semilla_consistente.sql` — normaliza la semilla: 9 disciplinas, nombres y correos reales, relaciones válidas, calendarios, notificaciones, guardados, solicitudes, podios y contraseña única de demo. Es idempotente y solo debe aplicarse sobre datos de siembra.
9. `009_equipos_futbol_escudos.sql` — deja Fútbol 5 con 16 equipos (renombra los 4 de la semilla y crea 12 nuevos), asigna los 16 escudos y abre la Copa Ciudad de Fútbol 5 (`1203`) con los 16 equipos inscriptos. Idempotente.
10. `010_plantillas_equipos_futbol.sql` — completa las plantillas de los 12 equipos nuevos (`1133`–`1144`) con capitán principal y al menos dos miembros activos, y corrige tres membresías cruzadas que `009` había dejado en el torneo `1203`. Solo datos; idempotente.
11. `011_aceptaciones_legales.sql` — crea la tabla `aceptaciones_legales`, que guarda la constancia de aceptación de Términos y Condiciones y Política de Privacidad por usuario, tipo de documento, versión, fecha, IP y user-agent. Es aditiva e idempotente; no modifica tablas existentes.

> **Escudos de equipos:** las imágenes se sirven desde `codigo_fuente/publico/img/equipos/`, no desde `base_de_datos/migraciones` (esa carpeta se monta solo en MySQL y Apache no la publica). `009` apunta a rutas relativas, así que el mismo dato funciona en XAMPP y en Docker. Cada `UPDATE`/`INSERT` de `009_equipos_futbol_escudos.sql` indica el archivo que espera; si falta un PNG, ese equipo muestra la imagen rota.

Para verificar el arranque:

```bash
docker compose -f docker/docker-compose.yml exec db \
  mysql -u root -proot123 -N -B ascend \
  -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='ascend'; SELECT COUNT(*) FROM usuarios; SELECT COUNT(*) FROM torneos;"
```

Debe devolver `42` tablas, `51` usuarios y `26` torneos.

La semilla ampliada deja datos para probar todos los formatos sin crear nada a mano: 9 disciplinas activas (3 esports, 3 deportes físicos y 3 juegos de mesa; 2 de equipos y 1 individual por tipo, cada una con exactamente 2 formatos), 45 equipos, 129 participantes, 97 encuentros, 111 filas de posiciones, 151 notificaciones, 84 guardados, 14 solicitudes de equipo, 29 comentarios y 68 actividades. Después de `010`, los 16 equipos de Fútbol 5 tienen capitán y plantilla mínima (172 membresías y 45 capitanías en total), de modo que cumplen la regla de inscripción de al menos 2 miembros activos. Incluye una liga de 16 equipos de fútbol con escudo propio (`1203`), una liga completa de 8 equipos (`1212`), una eliminación directa de equipos (`1213`), un suizo individual (`1214`), una liga individual de 10 (`1215`), una eliminación individual (`1216`), un torneo abierto con inscripciones confirmadas, pendientes, rechazadas y canceladas (`1217`) y torneos para Truco, Catan, Tenis y EA Sports FC 26 (`1218`–`1225`). Cada jugador tiene perfil, equipos, guardados y notificaciones; cada organizador tiene agenda, solicitudes y comentarios en su panel.

> **Consistencia con el CRUD:** todas las combinaciones juego/modalidad/formato de la semilla existen en el catálogo que administra el CRUD de juegos y pasan los triggers de `007`. Los cupos respetan los mínimos del formulario (liga 3, suizo 4, eliminación 2) y los torneos finalizados tienen podio y calendario completo.

> Para recrear la base desde cero: `docker compose -f docker/docker-compose.yml down -v` elimina el volumen de datos. Es destructivo; no lo ejecutes si hay información que debés conservar. No reimportes `001_ascend.sql` sobre una base en uso: recrea las tablas.

> **Base existente (XAMPP):** si ya tenés una base `ascend` con datos, no importes `001`. Aplicá solo `005_correcciones_esquema.sql` y `011_aceptaciones_legales.sql`, que son idempotentes. `006`, `007`, `008`, `009` y `010` solo corresponden si esa base ya tiene la semilla `003`; los cinco son idempotentes. `008` reescribe nombres, relaciones y contraseñas de las cuentas sembradas: no lo ejecutes sobre datos reales. `010` solo toca los equipos `1103`, `1108`, `1129`, `1130` y `1133`–`1144`. `011` no altera filas existentes ni exige reseed: el registro nuevo es el único que comienza a escribir aceptaciones.

### Modo híbrido (XAMPP + solo MySQL en Docker)

Si usás Apache/PHP de XAMPP y únicamente el contenedor de base de datos:

```bash
docker compose -f docker/docker-compose.yml up -d db
```

Configurá el puerto publicado de MySQL antes de iniciar el servidor PHP:

```bash
# Linux/macOS
DB_HOST=127.0.0.1 DB_PORT=3307 DB_NAME=ascend DB_USER=root DB_PASSWORD=root123 \
  php -S 127.0.0.1:8099 -t codigo_fuente/publico
```

```powershell
# PowerShell
$env:DB_HOST="127.0.0.1"; $env:DB_PORT="3307"; $env:DB_NAME="ascend"
$env:DB_USER="root"; $env:DB_PASSWORD="root123"
php -S 127.0.0.1:8099 -t codigo_fuente/publico
```

La aplicación lee `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` y `DB_CHARSET` del entorno (`codigo_fuente/configuracion/base_de_datos.php`).

## Configuración

- Credenciales por defecto para Docker: usuario `ascend_usuario`, contraseña `ascend_password`, base `ascend`.
- `APP_URL` es opcional. Si está vacía, la aplicación detecta la URL desde la petición; para una demo por IP o dominio conviene definirla, por ejemplo `APP_URL=http://192.168.1.50:8080/`.
- No se suben credenciales reales al repositorio; usá variables de entorno o un `.env` local.

## Estado de entrega y limitaciones

**Estado verificado (2026-10-21):** MVP/demo entregable. El dry run completo del stack, los P0/P1 y las rutas críticas están documentados en `informes_auditoria/VERIFICACION_FINAL_DRY_RUN.md`.

Limitaciones conocidas:

- La **recuperación de contraseña** no está implementada; el login muestra “Recuperación de contraseña no disponible en esta versión”.
- **No existe un flujo de cambio de email con re-verificación.** El correo solo se define al registrarse; por eso el campo es de solo lectura para el jugador y para el administrador.
- La **auditoría administrativa** (`logs_actividad`) es de mejor esfuerzo: si la escritura del log falla, la acción ya confirmada se mantiene y el error queda en el log del servidor.
- La **verificación de correo** depende de un enlace mostrado en pantalla; no hay envío real de emails.
- Si **`APP_URL` no está definida**, `URL_BASE` se arma con el encabezado `Host` de la petición. Para una demo por IP o dominio conviene fijarla y así evitar enlaces y redirecciones construidos con un host manipulado.
- El **sistema suizo** solo admite cantidades pares de 4 a 64 participantes; no implementa bye.
- `003_ascend_seed_relacional_qa.sql` es solo para una base limpia; sobre una base existente aplicá `005_correcciones_esquema.sql`.
- `006_seed_qa_ampliado.sql` es idempotente y se puede reaplicar sobre una base que ya tenga `003`; no reemplaza a `003` ni crea usuarios. Solo reinicia las posiciones de los torneos `1212`–`1217`.
- `008_semilla_consistente.sql` es idempotente y se puede reaplicar sobre la semilla. Usa IDs reservados (`1000`–`1051`, `1100`–`1132`, `1200`–`1225`, `1390`–`1426`) y modifica únicamente esos registros.
- `009_equipos_futbol_escudos.sql` renombra los 4 equipos de Fútbol 5, crea 12 equipos nuevos (`1133`–`1144`), completa su `escudo_url` y los inscribe en el torneo `1203`; también pasa `1203` a inscripciones abiertas y sincroniza el podio de `1208`. Se puede reaplicar y requiere los 16 PNG en `codigo_fuente/publico/img/equipos/`.
- `010_plantillas_equipos_futbol.sql` asigna capitán principal y al menos dos miembros activos a los 12 equipos nuevos, reasigna los equipos `1142`–`1144` a jugadores libres y elimina las membresías duplicadas que violaban la regla de un equipo por torneo. No modifica el esquema y se puede reaplicar.
- La cadena de siembra es `003` → `006` → `008`: `003` crea usuarios, equipos y torneos relacionales; `006` amplía torneos y participantes; `008` normaliza nombres, correos, formatos, calendarios, notificaciones y posiciones. Las tres se necesitan en una base limpia. Los archivos sueltos `actualizar_bd*.sql` que existían en la raíz eran históricos y no formaban parte de la instalación; ya se retiraron porque sus cambios están en `001` y `005`.
- Las **credenciales de demo son públicas**; no usar este despliegue como producción sin cambiarlas, activar HTTPS y revisar el endurecimiento del servidor.
- Es una entrega académica/MVP: no incluye email real, recuperación de contraseña ni auditoría de seguridad productiva.

### Administración de equipos y usuarios

El panel de administración incorpora dos fichas administrativas, una acción de renombre y la edición controlada de usuarios:

- **Ficha de equipo** (`index.php?c=equipoAdmin&a=ver&id=…`): escudo, estado, disciplina, capitán, plantilla, participaciones, posiciones, partidos y resumen deportivo. Incluye el formulario para renombrar el equipo; el cambio se propaga en una transacción a `participantes_torneo` y al podio de `torneo_config`, y queda registrado en `logs_actividad`.
- **Ficha de usuario** (`index.php?c=usuario&a=ver&id=…`): datos de contacto, rol, perfil especializado, equipos, participaciones, aceptación de Términos y Privacidad (versión, fecha, IP y navegador), intentos de acceso, sesiones (sin tokens) y actividad reciente. Es solo lectura y cada consulta se audita con la acción `ADMIN_CONSULTA_USUARIO`.
- **Edición de usuario:** se abre desde la ficha (`Editar nombre o rol`) y permite cambiar únicamente nombre y rol. El email es de solo lectura porque cambiarlo invalidaría la verificación del usuario; el backend ignora cualquier email enviado en el POST. Cada cambio se audita con `ADMIN_ACTUALIZA_USUARIO`.
- **Retorno:** guardar o cancelar vuelve al listado de origen (`usuarios`, `jugadores`, `organizadores`) o a la ficha desde la que se editó. Un `origen` no reconocido usa el listado del rol del usuario, no una URL externa.
- **Baja de cuentas:** la acción de eliminar de los listados es una **baja lógica** (`esta_activo = 0`). No borra perfiles, membresías, capitanías ni historial; la cuenta deja de poder iniciar sesión y sus sesiones activas se cierran en la siguiente petición. Se audita con `ADMIN_BAJA_USUARIO`.
- **Cambio de rol:** al guardar se crea, dentro de la misma transacción, el perfil especializado que falte para el rol destino (jugador u organizador) y se conservan los perfiles existentes. El formulario muestra cuántos torneos, equipos y capitanías dependen del usuario. Promover a **Administrador** exige marcar una confirmación explícita; el backend rechaza la promoción sin ella.
- **Bloqueo y activación:** se auditan con `ADMIN_CAMBIA_ESTADO_USUARIO`; el administrador no puede bloquear su propia cuenta.
- **Ver como (suplantación):** solo sobre jugadores y organizadores, nunca sobre otro administrador ni sobre la propia cuenta. El inicio y el fin se auditan (`ADMIN_SUPLANTA_INICIO` / `ADMIN_SUPLANTA_FIN`); la restauración es POST con CSRF y regenera el identificador de sesión. Mientras dura la suplantación la sesión es de solo lectura: se bloquean los POST.
- Los listados de usuarios, jugadores, organizadores y equipos enlazan a estas fichas. El botón de edición redundante de los listados se retiró: la edición se inicia desde la ficha.

### Acceso y verificación de correo

- El registro exige verificar el correo antes de habilitar el login. Un intento con la cuenta sin verificar se rechaza con un mensaje y queda registrado en `logs_acceso` con el motivo. En modo demo el enlace de verificación se muestra en pantalla porque no se envían correos reales.
- El email es inmutable desde la interfaz: el administrador solo puede cambiar nombre y rol, y el jugador solo su nombre, foto, biografía y datos deportivos. Ambos formularios muestran el correo como campo de solo lectura y los controladores ignoran cualquier email recibido en el POST.
- Cambiar un correo requeriría un flujo de verificación del nuevo correo antes de reemplazarlo; ese flujo **no está implementado** (ver limitaciones).

### Aceptación de Términos y Privacidad

El registro exige marcar una casilla con enlaces a `index.php?c=inicio&a=terminos` y `index.php?c=inicio&a=privacidad`. La validación se repite en el servidor: sin `aceptar_terminos=1` no se crea la cuenta, aunque se omita el atributo `required` del navegador.

- Las versiones vigentes se centralizan en `codigo_fuente/configuracion/constantes.php` (`VERSION_TERMINOS` y `VERSION_PRIVACIDAD`) y se muestran en ambas páginas legales.
- Al registrar la cuenta se insertan dos filas en `aceptaciones_legales` (una por documento) **dentro de la misma transacción** que crea el usuario. Si falla cualquier escritura, se revierte toda la cuenta. Se guardan versión, fecha/hora, IP y user-agent.
- La ficha administrativa de usuario muestra la aceptación en modo solo lectura: documento, versión vigente o anterior, fecha, IP y navegador. No existe acción para editar ni revocar la constancia.
- Los usuarios creados antes de `011` no tienen registros históricos; no se inventan aceptaciones retroactivas. La constancia se conserva mientras exista la cuenta, porque sirve para demostrar el consentimiento (Ley 18.331). Es una aceptación electrónica simple: no constituye firma digital certificada ni firma electrónica avanzada (Ley 18.600).

### Cuentas de demostración

Todos los usuarios sembrados comparten la misma contraseña de demostración: **`Ascend2026!`**.

| Rol | Emails | Contraseña |
| --- | --- | --- |
| Administrador | `admin@ascend.com` | `Ascend2026!` |
| Organizador principal | `organizador@ascend.com` | `Ascend2026!` |
| Jugador principal | `jugador@ascend.com` | `Ascend2026!` |
| Organizadores sembrados | `lucia.fernandez@ascend.test` … `federico.nunez@ascend.test` | `Ascend2026!` |
| Jugadores sembrados (42 cuentas) | `mateo.rodriguez@ascend.test` … `mariana.zeballos@ascend.test` | `Ascend2026!` |

El listado completo, con nombre y rol de cada cuenta, está en `Documentos_Juan/Usuarios_Sembrados.md`. Es una contraseña de demostración: cambiala antes de cualquier despliegue real. Los correos usan el dominio reservado `.test` y no envían mensajes reales.

## Pruebas

```bash
composer install
vendor/bin/phpunit
```

`phpunit.xml` usa la base `ascend_test`. Creala antes de ejecutar las pruebas de integración y aplicá allí `001`–`011` (sin las semillas de QA si preferís un entorno mínimo). Las pruebas unitarias no requieren base de datos.

Cobertura actual:

- `FichaAdminTest`: renombre sincronizado de equipos, fichas de equipo y usuario, baja lógica que conserva historial, sincronización de perfil al cambiar de rol, impacto del rol y edición administrativa que nunca modifica el email.
- `PerfilJugadorTest`: la edición de perfil del jugador ignora cualquier email enviado en el POST y conserva `email_verificado`.
- `AceptacionLegalTest`: registro con y sin aceptación, versiones vacías, duplicados, ficha administrativa sin exponer el hash, usuario sin aceptaciones, reversión completa si falla la inserción de la constancia, saneamiento de IP/user-agent, registro de organizadores y rechazo de correos con formato inválido (sin usuario ni constancia).

Resultado local verificado (2026-10-28): **OK (38 tests, 193 assertions)** sobre PHP 8.0.30 / MariaDB 10.4.32.

### Suite sobre Docker

La imagen `docker-web` (PHP 8.2.29) ya trae las extensiones que PHPUnit necesita. Montá el repositorio y usá la base `ascend_test` del contenedor MySQL:

```bash
docker run --rm --network docker_sgdm_red \
  -e DB_HOST=db -e DB_PORT=3306 -e DB_NAME=ascend_test \
  -e DB_USER=root -e DB_PASSWORD=root123 \
  -v "C:/xampp/htdocs/SGDM:/app" -w /app \
  docker-web:latest php vendor/phpunit/phpunit/phpunit --configuration phpunit.xml
```

Resultado verificado (2026-10-28): **OK (38 tests, 193 assertions)** sobre PHP 8.2.29. El contenedor se crea con `--rm`, así que no queda nada corriendo.

### E2E HTTP (opcional)

`tests/e2e/seguridad_admin_e2e.sh` recorre los flujos de seguridad del administrador contra un servidor PHP aislado y `ascend_test`:

```bash
BASE_URL=http://127.0.0.1:8099 \
MYSQL_BIN=/c/xampp/mysql/bin/mysql.exe \
PHP_BIN=/c/xampp/php/php.exe \
bash tests/e2e/seguridad_admin_e2e.sh
```

Cubre verificación de correo, email del jugador en solo lectura, baja lógica y auditoría, bloqueo/activación, suplantación (auto-suplantación, otro admin, restauración por POST con CSRF y Referer no confiable), confirmación de promoción a administrador y regresión de las páginas del panel. Crea sus propios usuarios con un prefijo único y los elimina al terminar.

Para ejecutarlo contra el **stack Docker** sin ensuciar la base demo, levantá un contenedor efímero con la misma imagen apuntando a `ascend_test`:

```bash
docker run -d --rm --name sgdm_e2e_web --network docker_sgdm_red \
  -e DB_HOST=db -e DB_PORT=3306 -e DB_NAME=ascend_test \
  -e DB_USER=root -e DB_PASSWORD=root123 \
  -p 127.0.0.1:8082:80 \
  -v "C:/xampp/htdocs/SGDM/codigo_fuente:/var/www/html" docker-web:latest

# El cliente mysql de XAMPP no soporta caching_sha2_password: usá docker exec.
printf '#!/usr/bin/env bash\nexec docker exec -i sgdm_db mysql "$@"\n' > /tmp/e2e-docker-mysql
chmod +x /tmp/e2e-docker-mysql

BASE_URL=http://127.0.0.1:8082 DB_NAME=ascend_test \
MYSQL_BIN=/tmp/e2e-docker-mysql MYSQL_PASSWORD=root123 \
START_SERVER=0 bash tests/e2e/seguridad_admin_e2e.sh

docker rm -f sgdm_e2e_web
```

No apuntes el E2E a la base `ascend`: crea y borra usuarios de prueba y dejaría rastros en la demo.

`tests/e2e/registro_aceptacion_e2e.sh` verifica el flujo legal completo contra un servidor PHP aislado en el puerto 8098 y `ascend_test`:

```bash
BASE_URL=http://127.0.0.1:8098 \
MYSQL_BIN=/c/xampp/mysql/bin/mysql.exe \
PHP_BIN=/c/xampp/php/php.exe \
bash tests/e2e/registro_aceptacion_e2e.sh
```

Comprueba que las páginas legales publican la versión vigente, que la casilla es obligatoria y sus enlaces abren en otra pestaña, que un POST sin aceptación no crea la cuenta, que un correo con formato inválido tampoco la crea, que un POST con aceptación crea el usuario y las dos constancias (versión, IP, fecha), que un reenvío no duplica filas y que la ficha administrativa muestra la aceptación sin ofrecer acciones de edición o revocación. El formulario además bloquea el doble envío desde `login.js`; se verificó en navegador que el segundo evento `submit` se cancela.

## Comandos útiles

```bash
# Estado y logs
docker compose -f docker/docker-compose.yml ps
docker compose -f docker/docker-compose.yml logs -f web
docker compose -f docker/docker-compose.yml logs -f db

# Consola MySQL
docker compose -f docker/docker-compose.yml exec db mysql -u root -proot123 ascend

# Desbloquear una IP bloqueada por intentos fallidos
docker compose -f docker/docker-compose.yml exec db \
  mysql -u root -proot123 ascend -e "DELETE FROM intentos_login WHERE ip_origen='TU_IP';"

# Detener sin borrar datos
docker compose -f docker/docker-compose.yml stop
```

## Documentación

- `README.md`: arranque, configuración, limitaciones y comandos de prueba (incluye Docker).
- `Documentos_Juan/Guia_Despliegue_10_Minutos.md`: puesta en marcha paso a paso. Ojo: `.gitignore` ignora `Documentos_Juan/`, así que en una entrega por Git hay que agregarla con `git add -f`; en un zip, incluirla manualmente.
- `informes_auditoria/`: auditoría técnica, checklist de entrega y verificación final del dry run.
