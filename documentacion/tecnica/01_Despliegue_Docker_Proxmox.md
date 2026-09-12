# 🐳 Guía de Despliegue: Docker y Proxmox

## 1. Introducción al Entorno de ASCEND
Para garantizar que la Plataforma ASCEND funcione exactamente igual en la computadora de desarrollo (local) que en el servidor de producción, utilizamos **Docker** (para empaquetar la aplicación) y **Proxmox VE** (como hipervisor o servidor principal).

- **Docker:** Contenedores aislados que ejecutan la base de datos (MySQL), el servidor web (Apache + PHP 8.2), y herramientas de monitoreo (Grafana, phpMyAdmin).
- **Proxmox VE:** El sistema operativo base (bare-metal) donde crearemos una Máquina Virtual Linux. Dentro de esta VM es donde instalaremos Docker.

---

## 2. Topología de Contenedores (Docker Compose)
El proyecto utiliza un archivo `docker-compose.yml` que orquesta los siguientes 4 servicios:

| Contenedor | Imagen Base | Puertos | Propósito |
|---|---|---|---|
| `sgdm_web` | `php:8.2-apache` | `8080:80` | Ejecuta el código PHP nativo y sirve las vistas HTML/CSS. |
| `sgdm_db` | `mysql:8.0` | `3306:3306` | Base de datos relacional. Nombre: `ascend`. |
| `sgdm_phpmyadmin`| `phpmyadmin` | `8081:80` | Interfaz gráfica web para gestionar MySQL. |
| `sgdm_grafana` | `grafana` | `3000:3000` | Herramienta de monitoreo de recursos del servidor. |

---

## 3. Comandos de Uso Frecuente (Cheatsheet)

Para utilizar Docker en el entorno local (Windows o Linux), asegúrate de estar posicionado en la carpeta `docker/` del proyecto.

### Levantar el proyecto
```bash
docker-compose up -d --build
```
> El flag `-d` corre los contenedores en segundo plano (detached). `--build` fuerza a Docker a reconstruir la imagen de PHP si hicimos cambios en el `Dockerfile`.

### Bajar el proyecto
```bash
docker-compose down
```
> Apaga los contenedores y destruye la red virtual, pero **mantiene los datos** gracias al volumen persistente `sgdm_datos`.

### Ver logs (Auditoría)
Para ver si hay errores en PHP o Apache:
```bash
docker-compose logs -f web
```
Para ver los logs de arranque de la base de datos:
```bash
docker-compose logs -f db
```

---

## 4. Persistencia de Datos y Migraciones

**Volúmenes:** La carpeta de la base de datos interna de MySQL está mapeada a un volumen de Docker. Si borras el contenedor, la información no se pierde.
**Sincronización Web:** La carpeta `codigo_fuente` está conectada al contenedor `web` como un "bind mount". Esto significa que si editas un archivo en VS Code, el servidor Apache de Docker lo verá modificado instantáneamente.

## 5. Próximos Pasos en Producción (Proxmox)
Cuando la aplicación esté lista para defenderse frente al tribunal:
1. Se instalará una VM Ubuntu Server en Proxmox.
2. Se instalará Docker en dicha VM.
3. Se copiará la carpeta `docker` y `codigo_fuente` a la VM.
4. Se ejecutará `docker-compose up -d`. ¡El servidor estará en vivo en la red local!
