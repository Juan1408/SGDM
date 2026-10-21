# Guía de Comandos Docker: SGDM / ASCEND

Comandos para operar los contenedores del proyecto desde la raíz del repositorio (`SGDM/`). Funcionan igual en Windows (CMD/PowerShell) y Linux/macOS; solo cambia el formato de las variables de entorno.

## 1. Servicios y puertos

| Servicio | Contenedor | Puerto host | URL |
| --- | --- | --- | --- |
| Web (PHP 8.2 + Apache) | `sgdm_web` | 8080 | http://localhost:8080/ |
| MySQL 8.0 | `sgdm_db` | 3307 | 127.0.0.1:3307 |
| phpMyAdmin | `sgdm_phpmyadmin` | 8081 (solo host) | http://127.0.0.1:8081/ |
| Grafana | `sgdm_grafana` | 3001 (solo host) | http://127.0.0.1:3001/ |

El docroot de Apache es `codigo_fuente/publico`; por eso la aplicación se abre en `http://localhost:8080/` y no en `/publico/`.

## 2. Levantar la aplicación completa

```bash
docker compose -f docker/docker-compose.yml up -d --build
docker compose -f docker/docker-compose.yml ps
docker compose -f docker/docker-compose.yml logs -f web
```

Para detener sin borrar datos:

```bash
docker compose -f docker/docker-compose.yml stop
```

Para apagar y eliminar los contenedores (el volumen `sgdm_datos` se conserva):

```bash
docker compose -f docker/docker-compose.yml down
```

> `down -v` elimina también el volumen de MySQL y, con él, todos los datos. Usalo solo para recrear la base desde cero y con una copia de respaldo si la información es importante.

## 3. Solo la base de datos (modo híbrido con XAMPP)

```bash
docker compose -f docker/docker-compose.yml up -d db
```

El contenedor publica MySQL en `127.0.0.1:3307`. Para que la aplicación PHP del host se conecte, definí `DB_PORT=3307` (y las demás variables si cambiaron las credenciales):

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

Sin `DB_PORT`, la conexión usa el puerto 3306 (MySQL/MariaDB local de XAMPP) y no llega al contenedor.

## 4. Migraciones e inicialización

MySQL ejecuta automáticamente los archivos montados en `/docker-entrypoint-initdb.d` **solo cuando el volumen `sgdm_datos` está vacío**. Orden:

1. `001_ascend.sql`
2. `002_datos_ejemplo.sql`
3. `003_ascend_seed_relacional_qa.sql` (datos de QA; pensado para base limpia)
4. `004_actualizar_auth.sql`
5. `005_correcciones_esquema.sql` (idempotente)
6. `006_seed_qa_ampliado.sql` (idempotente; amplía la cobertura de QA)

Verificación después del primer arranque:

```bash
docker compose -f docker/docker-compose.yml exec db \
  mysql -u root -proot123 -N -B ascend \
  -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='ascend'; SELECT COUNT(*) FROM usuarios; SELECT COUNT(*) FROM torneos;"
```

Debe devolver `41` tablas, `51` usuarios y `18` torneos.

Si cambiás un `.sql`, el contenedor no lo reaplica sobre una base existente. Para recrear todo desde cero:

```bash
docker compose -f docker/docker-compose.yml down -v
docker compose -f docker/docker-compose.yml up -d --build
```

No reimportes `001_ascend.sql` sobre una base en uso: contiene `DROP TABLE` y borra los datos.

## 5. Desbloquear una IP por intentos fallidos

El login bloquea la IP en `intentos_login` después de varios intentos fallidos.

### Opción A: limpiar todos los bloqueos (SQL)

```bash
docker compose -f docker/docker-compose.yml exec db \
  mysql -u root -proot123 ascend -e "TRUNCATE TABLE intentos_login;"
```

### Opción B: desbloquear una IP específica

```bash
docker compose -f docker/docker-compose.yml exec db \
  mysql -u root -proot123 ascend \
  -e "DELETE FROM intentos_login WHERE ip_origen = 'TU_IP_AQUI';"
```

### Opción C: script PHP desde el host

`desbloquear.php` está en la raíz del repositorio y usa la configuración de la aplicación. Ejecutalo desde el host apuntando al MySQL del contenedor:

```bash
DB_HOST=127.0.0.1 DB_PORT=3307 DB_NAME=ascend DB_USER=root DB_PASSWORD=root123 \
  php desbloquear.php
```

En PowerShell:

```powershell
$env:DB_HOST="127.0.0.1"; $env:DB_PORT="3307"; $env:DB_NAME="ascend"
$env:DB_USER="root"; $env:DB_PASSWORD="root123"
php desbloquear.php
```

El script no está dentro del bind mount del contenedor web, por lo que no se ejecuta como `/var/www/html/../desbloquear.php`.

## 6. Mantenimiento y diagnóstico

```bash
# Estado de contenedores
docker compose -f docker/docker-compose.yml ps

# Logs en tiempo real
docker compose -f docker/docker-compose.yml logs -f db
docker compose -f docker/docker-compose.yml logs -f web

# Consola interactiva de MySQL
docker compose -f docker/docker-compose.yml exec db mysql -u root -proot123 ascend

# Reiniciar servicios
docker compose -f docker/docker-compose.yml restart
```

Si `web` no arranca porque `db` no está saludable, revisá `logs db`. El healthcheck de MySQL tiene `start_period: 30s`; en equipos lentos el primer arranque puede tardar más.

## 7. Datos de conexión

| Parámetro | Red interna Docker | Desde el host |
| --- | --- | --- |
| Host | `db` | `127.0.0.1` |
| Puerto | `3306` | `3307` |
| Base | `ascend` | `ascend` |
| Usuario de la app | `ascend_usuario` | `ascend_usuario` |
| Contraseña de la app | `ascend_password` | `ascend_password` |
| Root | `root` | `root` |
| Contraseña root | `root123` | `root123` |

## 8. Imágenes y permisos

Las carpetas de subida (`publico/img/perfiles/subidos`, `publico/img/juegos/subidos` y `publico/img/torneos/subidos`) se crean y se asignan a `www-data` al iniciar el contenedor web. Si en un bind mount de Windows los archivos nuevos quedan con otro propietario, verificá que Apache pueda escribir:

```bash
docker compose -f docker/docker-compose.yml exec web \
  ls -ld publico/img/perfiles/subidos publico/img/juegos/subidos publico/img/torneos/subidos
```

Si hiciera falta, repetí el `chown` dentro del contenedor:

```bash
docker compose -f docker/docker-compose.yml exec web \
  chown -R www-data:www-data publico/img/perfiles/subidos publico/img/juegos/subidos publico/img/torneos/subidos
```
