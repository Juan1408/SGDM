# Guía de Comandos Docker: SGDM / ASCEND

Esta guía contiene todos los comandos necesarios para operar los contenedores Docker del proyecto tanto en entorno local (**Windows CMD / PowerShell**) como en un entorno de producción o pruebas (**Servidor Linux / Bash**).

---

## 1. Levantar solo el servicio de Base de Datos (`db`)

Si trabajas con un servidor Apache/PHP local (por ejemplo XAMPP) y solo requieres el motor de base de datos MySQL en Docker:

### En Windows (CMD / Símbolo del sistema)
```cmd
:: Ubicado en la raíz del proyecto (c:\xampp\htdocs\SGDM)
docker compose -f docker/docker-compose.yml up -d db

:: O si estás dentro de la carpeta docker\
cd docker
docker compose up -d db
```

### En Windows (PowerShell)
```powershell
# Ubicado en la raíz del proyecto
docker compose -f docker/docker-compose.yml up -d db
```

### En Servidor Linux / Servidor Remoto (Bash / SSH)
```bash
# Ubicado en la raíz del proyecto (/var/www/SGDM o ruta correspondiente)
docker compose -f docker/docker-compose.yml up -d db

# O con permisos de superusuario si tu usuario no está en el grupo docker:
sudo docker compose -f docker/docker-compose.yml up -d db
```

---

## 2. Levantar la aplicación completa (Web + Base de Datos)

### En Windows (CMD)
```cmd
cd c:\xampp\htdocs\SGDM
docker compose -f docker/docker-compose.yml up -d
```

### En Windows (PowerShell)
```powershell
docker compose -f docker/docker-compose.yml up -d
```

### En Servidor Linux (Bash / SSH)
```bash
docker compose -f docker/docker-compose.yml up -d
```

---

## 3. Desbloquear IP tras bloqueo por intentos fallidos

Cuando se superan 5 intentos fallidos de login, el sistema bloquea la IP en la tabla `intentos_login` de la base de datos `ascend`. Puedes desbloquearla con cualquiera de las siguientes alternativas:

### Opción A: Desbloquear todas las IPs desde MySQL en Docker (Más rápida)

#### En Windows (CMD):
```cmd
docker compose -f docker/docker-compose.yml exec db mysql -u root -proot123 ascend -e "TRUNCATE TABLE intentos_login;"
```

#### En Windows (PowerShell):
*(En PowerShell se usan comillas simples para evitar expansión de variables):*
```powershell
docker compose -f docker/docker-compose.yml exec db mysql -u root -proot123 ascend -e 'TRUNCATE TABLE intentos_login;'
```

#### En Servidor Linux (Bash / SSH):
```bash
docker compose -f docker/docker-compose.yml exec db mysql -u root -proot123 ascend -e "TRUNCATE TABLE intentos_login;"

# O usando directamente el nombre del contenedor:
docker exec -it sgdm_db mysql -u root -proot123 ascend -e "TRUNCATE TABLE intentos_login;"
```

---

### Opción B: Desbloquear una IP específica

Si solo deseas desbloquear tu propia IP (ej. `172.22.0.1` o `192.168.1.50`):

#### En Windows (CMD):
```cmd
docker compose -f docker/docker-compose.yml exec db mysql -u root -proot123 ascend -e "DELETE FROM intentos_login WHERE ip_origen = 'TU_IP_AQUI';"
```

#### En Servidor Linux (Bash):
```bash
docker compose -f docker/docker-compose.yml exec db mysql -u root -proot123 ascend -e "DELETE FROM intentos_login WHERE ip_origen = 'TU_IP_AQUI';"
```

---

### Opción C: Usando el script PHP de desbloqueo

El proyecto incluye el script `desbloquear.php`. Puedes ejecutarlo dentro del contenedor web:

#### En Windows (CMD / PowerShell):
```cmd
docker compose -f docker/docker-compose.yml exec web php /var/www/html/../desbloquear.php
```

#### En Servidor Linux:
```bash
docker compose -f docker/docker-compose.yml exec web php /var/www/html/../desbloquear.php
```

---

## 4. Comandos de Mantenimiento y Diagnóstico

### Ver el estado de los contenedores
```bash
# Windows y Servidor:
docker compose -f docker/docker-compose.yml ps
```

### Ver los logs de la base de datos en tiempo real
```bash
# Windows y Servidor:
docker compose -f docker/docker-compose.yml logs -f db
```

### Ver los logs del servidor web (Apache / PHP)
```bash
# Windows y Servidor:
docker compose -f docker/docker-compose.yml logs -f web
```

### Entrar a la consola interactiva de MySQL
```bash
# Windows y Servidor:
docker compose -f docker/docker-compose.yml exec db mysql -u root -proot123 ascend
```

### Detener los servicios (sin borrar datos)
```bash
# Windows y Servidor:
docker compose -f docker/docker-compose.yml stop
```

### Apagar los servicios
```bash
# Windows y Servidor:
docker compose -f docker/docker-compose.yml down
```

### Reiniciar los servicios
```bash
# Windows y Servidor:
docker compose -f docker/docker-compose.yml restart
```

---

## 5. Datos de Conexión de la Base de Datos

| Parámetro | Valor Docker Interno | Valor desde el Host (tu PC / Workbench) |
| :--- | :--- | :--- |
| **Host** | `db` | `127.0.0.1` o `localhost` |
| **Puerto** | `3306` | `3307` |
| **Base de datos** | `ascend` | `ascend` |
| **Usuario App** | `ascend_usuario` | `ascend_usuario` (o `root`) |
| **Contraseña App** | `ascend_password` | `ascend_password` (o `root123`) |
| **Usuario Root** | `root` | `root` |
| **Contraseña Root** | `root123` | `root123` |
