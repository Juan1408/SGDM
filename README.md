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
│   └── migraciones/      # 001 → 006 (esquema, semillas y correcciones)
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
2. `002_datos_ejemplo.sql` — catálogos y datos base.
3. `003_ascend_seed_relacional_qa.sql` — datos sintéticos de QA (solo para una base limpia; no es idempotente).
4. `004_actualizar_auth.sql` — tablas de autenticación.
5. `005_correcciones_esquema.sql` — correcciones idempotentes.
6. `006_seed_qa_ampliado.sql` — ampliación idempotente de la semilla QA (más torneos, formatos y estados).

Para verificar el arranque:

```bash
docker compose -f docker/docker-compose.yml exec db \
  mysql -u root -proot123 -N -B ascend \
  -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='ascend'; SELECT COUNT(*) FROM usuarios; SELECT COUNT(*) FROM torneos;"
```

Debe devolver `41` tablas, `51` usuarios y `18` torneos.

La semilla ampliada deja datos para probar todos los formatos sin crear nada a mano: 23 equipos, 81 participantes, 87 encuentros y 76 filas de posiciones. Incluye una liga completa de 8 equipos (`1212`), una eliminación directa de equipos (`1213`), un suizo individual (`1214`), una liga individual de 10 (`1215`), una eliminación individual (`1216`) y un torneo abierto con inscripciones confirmadas, pendientes, rechazadas y canceladas (`1217`). También carga guardados, solicitudes de equipo, agenda, historial de estados, notificaciones y podios.

> Para recrear la base desde cero: `docker compose -f docker/docker-compose.yml down -v` elimina el volumen de datos. Es destructivo; no lo ejecutes si hay información que debés conservar. No reimportes `001_ascend.sql` sobre una base en uso: recrea las tablas.

> **Base existente (XAMPP):** si ya tenés una base `ascend` con datos, no importes `001`. Aplicá solo `005_correcciones_esquema.sql`, que es idempotente. `006_seed_qa_ampliado.sql` solo corresponde si esa base ya tiene la semilla `003`.

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
- El **sistema suizo** solo admite cantidades pares de 4 a 64 participantes; no implementa bye.
- `003_ascend_seed_relacional_qa.sql` es solo para una base limpia; sobre una base existente aplicá `005_correcciones_esquema.sql`.
- `006_seed_qa_ampliado.sql` es idempotente y se puede reaplicar sobre una base que ya tenga `003`; no reemplaza a `003` ni crea usuarios. Solo reinicia las posiciones de los torneos `1212`–`1217`.
- Las **credenciales de demo son públicas**; no usar este despliegue como producción sin cambiarlas, activar HTTPS y revisar el endurecimiento del servidor.
- Es una entrega académica/MVP: no incluye email real, recuperación de contraseña ni auditoría de seguridad productiva.

### Cuentas de demostración

| Rol | Email | Contraseña |
| --- | --- | --- |
| Administrador | `admin@ascend.com` | `Admin123!` |
| Organizador | `organizador@ascend.com` | `Admin123!` |
| Jugador | `jugador@ascend.com` | `Admin123!` |
| Organizadores QA (torneos precargados `1200`–`1217`) | `organizador.qa1@ascend.test` … `organizador.qa5@ascend.test` | `password` |

## Pruebas

```bash
composer install
vendor/bin/phpunit
```

`phpunit.xml` usa la base `ascend_test`. Creala antes de ejecutar las pruebas de integración y aplicá allí `001`–`006` (sin las semillas de QA si preferís un entorno mínimo). Las pruebas unitarias no requieren base de datos.

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

- `COMANDOS_DOCKER.md`: operación y diagnóstico de los contenedores.
- `Documentos_Juan/Guia_Despliegue_10_Minutos.md`: puesta en marcha paso a paso. Ojo: `.gitignore` ignora `Documentos_Juan/`, así que en una entrega por Git hay que agregarla con `git add -f`; en un zip, incluirla manualmente.
- `informes_auditoria/`: auditoría técnica, checklist de entrega y verificación final del dry run.
