# SGDM — Instrucciones para levantar con Docker

Resumen
- Proyecto SGDM: gestor de torneos con capas MVC minimalistas.

Requisitos
- Docker y Docker Compose instalados.
- Puertos libres (por defecto usa 8080 para la app web).

Levantar el entorno (modo rápido)
1. Construir y levantar contenedores:

```bash
docker-compose up -d --build
```

2. Verificar servicios:

```bash
docker-compose ps
docker-compose logs -f web
```

Importar migraciones (crear las tablas)
- Usando `docker-compose exec -T` para redirigir los .sql hacia MySQL (funciona en Linux/macOS y PowerShell):

```bash
docker-compose exec -T db mysql -u root -pexample sgdm < base_de_datos/migraciones/001_crear_tabla_usuarios.sql
# Para importar todas las migraciones (Linux/macOS):
for f in base_de_datos/migraciones/*.sql; do docker-compose exec -T db mysql -u root -pexample sgdm < "$f"; done
```

Notas para PowerShell (Windows)
- Si usás PowerShell, el primer comando sigue funcionando; para un batch en PowerShell podés ejecutar:

```powershell
Get-ChildItem base_de_datos/migraciones\*.sql | ForEach-Object { docker-compose exec -T db mysql -u root -pexample sgdm < $_.FullName }
```

Semillas (datos de ejemplo)
- Importar el archivo de semillas:

```bash
docker-compose exec -T db mysql -u root -pexample sgdm < base_de_datos/semillas/datos_ejemplo.sql
```

Configuración
- Credenciales y parámetros de base de datos por defecto están en `codigo_fuente/configuracion/base_de_datos.php`.
- Para cambiar credenciales en el entorno Docker, editar `docker/docker-compose.yml` o usar variables de entorno (recomendado en producción).

Acceder a la aplicación
- Una vez levantado el stack, abrir en el navegador:

```
http://localhost:8080
```

Comandos útiles
- Detener y borrar contenedores:

```bash
docker-compose down
```

- Ver logs de la base de datos:

```bash
docker-compose logs -f db
```

Seguridad y buenas prácticas
- NO subir credenciales reales al repositorio. Usar `.env` para variables sensibles.
- En producción, configurar backup regular de la base de datos y activar TLS/HTTPS en el proxy frontal.

Solución de problemas
- Si el contenedor `db` no está listo, ver los logs con `docker-compose logs db` y esperar a que MySQL inicialice.
- Si las migraciones fallan, comprobar que la base de datos `sgdm` exista y que las credenciales coincidan.

Contacto
- Si necesitás que automatice la ejecución de migraciones al levantar el contenedor (init scripts) o genere un `.env` de ejemplo, decímelo y lo hago.
