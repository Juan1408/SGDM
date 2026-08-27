# 📊 Documento 03: Diccionario de Datos Completo y Mapeo Técnico (HTML ➔ PHP ➔ MySQL)

---

## 🎯 1. Propósito del Diccionario de Datos
Este documento es la referencia técnica fundamental para el equipo de desarrollo. Define de forma exhaustiva y normalizada:
1. La estructura relacional en **Tercera Forma Normal (3FN)** del script [base_de_datos/sgdm_multideporte.sql](file:///c:/Users/juani/Documents/GitHub/SGDM/base_de_datos/sgdm_multideporte.sql).
2. El patrón de diseño de **Herencia de Tablas (Class Table Inheritance / Subtipos 1 a 1)** para separar la identidad de los usuarios de sus perfiles especializados (`perfiles_jugadores` y `perfiles_organizadores`).
3. El mapeo exacto entre los campos de los **Formularios HTML5 (`<input name="...">`, `<select name="...">`)**, los métodos de petición **HTTP (`GET` / `POST`)**, el tipo de dato que procesará **PHP (Hypertext Preprocessor)** en el servidor y la columna de destino en la base de datos **MySQL**.

---

## 🌐 2. Glosario de Términos y Siglas de Base de Datos

* **SQL (Structured Query Language - Lenguaje de Consulta Estructurada)**: Lenguaje estándar de programación para crear, modificar, consultar y eliminar información en bases de datos relacionales.
* **PK (Primary Key - Clave Primaria)**: Columna o conjunto de columnas cuyos valores identifican de forma unívoca e irrepetible a cada fila o registro de una tabla. No puede contener valores nulos (`NULL`).
* **FK (Foreign Key - Clave Foránea / Forastera)**: Columna en una tabla que hace referencia a la Clave Primaria de otra tabla, estableciendo una relación formal y garantizando la integridad de los datos.
* **3FN (Tercera Forma Normal)**: Estado de diseño relacional donde se garantiza la atomicidad de los datos (1FN), la ausencia de dependencias parciales en claves compuestas (2FN) y la eliminación de dependencias transitivas (3FN). Ningún dato no clave depende de otra columna que no sea la clave primaria.
* **Class Table Inheritance (Herencia de Tablas de Clase)**: Patrón de diseño de base de datos donde se crea una tabla padre para los datos comunes a todas las entidades, y tablas hijas separadas para los datos exclusivos de cada subtipo, vinculadas por una relación `1:1` donde la clave primaria de la tabla hija es a la vez su clave foránea hacia el padre.
* **ON DELETE CASCADE (Eliminación en Cascada)**: Regla de integridad referencial donde, al eliminar un registro padre, el motor de la base de datos elimina de forma automática todos los registros hijos que dependen de él.
* **ON DELETE SET NULL (Fijar en Nulo al Eliminar)**: Regla donde, al eliminar el registro padre, el valor de la clave foránea en los registros hijos se convierte automáticamente en `NULL`, evitando que se pierda el historial de eventos o auditorías.
* **ON DELETE RESTRICT (Restringir Eliminación)**: Regla que prohíbe terminantemente borrar un registro padre mientras existan registros hijos que lo estén referenciando, protegiendo la integridad del sistema.
* **RBAC (Role-Based Access Control - Control de Acceso Basado en Roles)**: Modelo de seguridad donde los privilegios del sistema se asignan a roles (por ejemplo: Administrador, Organizador, Participante) y los usuarios heredan los permisos del rol que tienen asignado.
* **PDO (PHP Data Objects - Objetos de Datos de PHP)**: Extensión oficial de PHP que provee una interfaz orientada a objetos, segura y unificada para interactuar con bases de datos como MySQL, facilitando el uso de sentencias preparadas contra inyecciones SQL.

---

## 🏛️ 3. Diagrama Entidad-Relación y Estructura de Herencia

```mermaid
erDiagram
    ROLES ||--o{ USUARIOS : "asigna rol (1:N)"
    ROLES ||--|{ ROL_PERMISOS : "contiene (N:M)"
    PERMISOS ||--|{ ROL_PERMISOS : "agrupa (N:M)"
    
    USUARIOS ||--|| PERFILES_JUGADORES : "especializa (1:1)"
    USUARIOS ||--|| PERFILES_ORGANIZADORES : "especializa (1:1)"
    
    USUARIOS ||--o{ TOKENS_VERIFICACION_EMAIL : "genera (1:N)"
    USUARIOS ||--o{ TOKENS_RECUPERACION : "genera (1:N)"
    USUARIOS ||--o{ SESIONES_ACTIVAS : "inicia (1:N)"
    USUARIOS ||--o{ LOGS_ACCESO : "registra intento (1:N)"
    USUARIOS ||--o{ NOTIFICACIONES : "recibe (1:N)"
    
    USUARIOS ||--o{ EQUIPOS : "crea / funda (1:N)"
    EQUIPOS ||--|{ EQUIPO_MIEMBROS : "integra (N:M)"
    USUARIOS ||--|{ EQUIPO_MIEMBROS : "participa (N:M)"
    EQUIPOS ||--|{ EQUIPO_CAPITANES : "lidera (N:M)"
    USUARIOS ||--|{ EQUIPO_CAPITANES : "designa (N:M)"
    
    JUEGOS ||--o{ TORNEOS : "define disciplina (1:N)"
    MODALIDADES ||--o{ TORNEOS : "establece formato (1:N)"
    SISTEMAS_PUNTUACION ||--o{ TORNEOS : "aplica puntaje (1:N)"
    USUARIOS ||--o{ TORNEOS : "organiza (1:N)"
    
    TORNEOS ||--|{ PARTICIPANTES_TORNEO : "inscribe (1:N)"
    TORNEOS ||--|{ TORNEO_ENCUENTROS : "programa fixture (1:N)"
    TORNEOS ||--|{ TORNEO_POSICIONES : "clasifica (1:N)"
    TORNEOS ||--o{ TORNEO_ACTIVIDADES : "agenta eventos (1:N)"
    TORNEOS ||--o{ TORNEO_COMENTARIOS : "recibe consultas (1:N)"
    
    TORNEO_ENCUENTROS ||--o{ RESULTADOS_DETALLE : "detalla estadisticas (1:N)"
    
    LOGROS ||--|{ USUARIO_LOGROS : "desbloquea (N:M)"
    USUARIOS ||--|{ USUARIO_LOGROS : "consigue (N:M)"
```

---

## 📖 4. Diccionario de Tablas Completo (35 Tablas)

## 📖 Diccionario de Tablas Completo (Auto-Generado desde MySQL en 3FN)

### Tabla: `auditoria_cambios`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `int(11)` | No | **PK** | `NULL` | auto_increment |
| `tabla_afectada` | `varchar(100)` | No | **FK/IDX** | `NULL` |  |
| `registro_id` | `int(11)` | No | - | `NULL` |  |
| `accion` | `varchar(20)` | No | - | `NULL` |  |
| `usuario_id` | `int(11)` | Sí | **FK/IDX** | NULL |  |
| `datos_viejos` | `longtext` | Sí | - | NULL |  |
| `datos_nuevos` | `longtext` | Sí | - | NULL |  |
| `ip_origen` | `varchar(45)` | Sí | - | NULL |  |
| `user_agent` | `text` | Sí | - | NULL |  |
| `fecha_hora` | `timestamp` | No | **FK/IDX** | current_timestamp() |  |

---

### Tabla: `equipos`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `int(11)` | No | **PK** | `NULL` | auto_increment |
| `nombre_equipo` | `varchar(100)` | No | **UNIQUE** | `NULL` |  |
| `escudo_url` | `text` | Sí | - | NULL |  |
| `banner_url` | `text` | Sí | - | NULL |  |
| `descripcion` | `text` | Sí | - | NULL |  |
| `ubicacion` | `varchar(100)` | Sí | - | NULL |  |
| `anio_fundacion` | `int(11)` | Sí | - | NULL |  |
| `fecha_creacion` | `timestamp` | No | - | current_timestamp() |  |
| `creado_por` | `int(11)` | No | **FK/IDX** | `NULL` |  |
| `activo` | `tinyint(1)` | Sí | - | 1 |  |
| `codigo_invitacion` | `varchar(20)` | Sí | **UNIQUE** | NULL |  |

---

### Tabla: `equipo_capitanes`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `equipo_id` | `int(11)` | No | **PK** | `NULL` |  |
| `usuario_id` | `int(11)` | No | **PK** | `NULL` |  |
| `fecha_asignacion` | `timestamp` | No | - | current_timestamp() |  |
| `asignado_por` | `int(11)` | No | **FK/IDX** | `NULL` |  |
| `es_capitan_principal` | `tinyint(1)` | Sí | - | 0 |  |

---

### Tabla: `equipo_miembros`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `equipo_id` | `int(11)` | No | **PK** | `NULL` |  |
| `usuario_id` | `int(11)` | No | **PK** | `NULL` |  |
| `fecha_union` | `timestamp` | No | - | current_timestamp() |  |
| `numero_camiseta` | `int(11)` | Sí | - | NULL |  |
| `posicion` | `varchar(50)` | Sí | - | NULL |  |
| `es_activo` | `tinyint(1)` | Sí | - | 1 |  |

---

### Tabla: `historial_contrasenas`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `int(11)` | No | **PK** | `NULL` | auto_increment |
| `usuario_id` | `int(11)` | No | **FK/IDX** | `NULL` |  |
| `hash_anterior` | `text` | No | - | `NULL` |  |
| `fecha_cambio` | `timestamp` | No | - | current_timestamp() |  |

---

### Tabla: `juegos`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `int(11)` | No | **PK** | `NULL` | auto_increment |
| `nombre` | `varchar(100)` | No | **UNIQUE** | `NULL` |  |
| `categoria` | `varchar(50)` | No | - | `NULL` | esport-shooter, esport-moba, deporte-fisico, juego-mesa |
| `formato_equipo_defecto` | `int(11)` | Sí | - | 1 | Cantidad habitual de integrantes por equipo |
| `puntos_victoria` | `decimal(5,2)` | Sí | - | 3.00 |  |
| `puntos_empate` | `decimal(5,2)` | Sí | - | 1.00 |  |
| `puntos_derrota` | `decimal(5,2)` | Sí | - | 0.00 |  |
| `activo` | `tinyint(1)` | Sí | - | 1 |  |
| `creado_en` | `timestamp` | No | - | current_timestamp() |  |

---

### Tabla: `logros`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `int(11)` | No | **PK** | `NULL` | auto_increment |
| `codigo` | `varchar(50)` | No | **UNIQUE** | `NULL` |  |
| `nombre` | `varchar(100)` | No | - | `NULL` |  |
| `descripcion` | `text` | No | - | `NULL` |  |
| `icono` | `varchar(100)` | Sí | - | 'fa-trophy' |  |
| `puntos_recompensa` | `int(11)` | Sí | - | 50 |  |
| `creado_en` | `timestamp` | No | - | current_timestamp() |  |

---

### Tabla: `logs_acceso`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `int(11)` | No | **PK** | `NULL` | auto_increment |
| `email_intentado` | `varchar(255)` | Sí | - | NULL |  |
| `usuario_id` | `int(11)` | Sí | **FK/IDX** | NULL |  |
| `exito` | `tinyint(1)` | No | - | `NULL` |  |
| `ip_origen` | `varchar(45)` | No | - | `NULL` |  |
| `user_agent` | `text` | Sí | - | NULL |  |
| `mensaje_error` | `text` | Sí | - | NULL |  |
| `fecha_hora` | `timestamp` | No | - | current_timestamp() |  |

---

### Tabla: `logs_actividad`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `int(11)` | No | **PK** | `NULL` | auto_increment |
| `usuario_id` | `int(11)` | Sí | **FK/IDX** | NULL |  |
| `accion` | `varchar(100)` | No | - | `NULL` |  |
| `descripcion` | `text` | Sí | - | NULL |  |
| `ip_origen` | `varchar(45)` | Sí | - | NULL |  |
| `user_agent` | `text` | Sí | - | NULL |  |
| `fecha_hora` | `timestamp` | No | - | current_timestamp() |  |

---

### Tabla: `modalidades`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `int(11)` | No | **PK** | `NULL` | auto_increment |
| `nombre` | `varchar(50)` | No | **UNIQUE** | `NULL` |  |
| `descripcion` | `text` | Sí | - | NULL |  |

---

### Tabla: `newsletter_subscriptores`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `int(11)` | No | **PK** | `NULL` | auto_increment |
| `email` | `varchar(255)` | No | **UNIQUE** | `NULL` |  |
| `activo` | `tinyint(1)` | Sí | - | 1 |  |
| `fecha_suscripcion` | `timestamp` | No | - | current_timestamp() |  |

---

### Tabla: `notificaciones`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `int(11)` | No | **PK** | `NULL` | auto_increment |
| `usuario_id` | `int(11)` | No | **FK/IDX** | `NULL` |  |
| `tipo` | `varchar(50)` | No | - | `NULL` |  |
| `titulo` | `varchar(255)` | No | - | `NULL` |  |
| `mensaje` | `text` | No | - | `NULL` |  |
| `enlace_relacionado` | `varchar(500)` | Sí | - | NULL |  |
| `leido` | `tinyint(1)` | Sí | - | 0 |  |
| `fecha_lectura` | `timestamp` | Sí | - | NULL |  |
| `creado_en` | `timestamp` | No | - | current_timestamp() |  |

---

### Tabla: `participantes_torneo`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `int(11)` | No | **PK** | `NULL` | auto_increment |
| `torneo_id` | `int(11)` | No | **FK/IDX** | `NULL` |  |
| `tipo` | `enum('equipo','usuario')` | No | **FK/IDX** | `NULL` |  |
| `referencia_id` | `int(11)` | No | - | `NULL` | ID de la tabla equipos o usuarios según el tipo |
| `nombre` | `varchar(255)` | No | - | `NULL` | Nombre denormalizado para rendimiento visual rápido |
| `estado` | `varchar(50)` | Sí | - | 'pendiente' |  |
| `fecha_inscripcion` | `timestamp` | No | - | current_timestamp() |  |
| `confirmado_por` | `int(11)` | Sí | **FK/IDX** | NULL |  |
| `fecha_confirmacion` | `timestamp` | Sí | - | NULL |  |

---

### Tabla: `perfiles_jugadores`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `usuario_id` | `int(11)` | No | **PK** | `NULL` |  |
| `apodo_gamertag` | `varchar(50)` | Sí | - | NULL |  |
| `bio` | `text` | Sí | - | NULL |  |
| `banner_url` | `text` | Sí | - | NULL |  |
| `nivel` | `int(11)` | Sí | - | 1 |  |
| `experiencia_puntos` | `int(11)` | Sí | - | 0 |  |
| `pais` | `varchar(100)` | Sí | - | 'Uruguay' |  |
| `ciudad` | `varchar(100)` | Sí | - | NULL |  |
| `fecha_nacimiento` | `date` | Sí | - | NULL |  |
| `discord_tag` | `varchar(100)` | Sí | - | NULL |  |
| `instagram_url` | `varchar(255)` | Sí | - | NULL |  |
| `twitter_url` | `varchar(255)` | Sí | - | NULL |  |
| `actualizado_en` | `timestamp` | No | - | current_timestamp() | on update current_timestamp() |

---

### Tabla: `perfiles_organizadores`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `usuario_id` | `int(11)` | No | **PK** | `NULL` |  |
| `nombre_organizacion` | `varchar(150)` | No | - | 'Organizador Independiente' |  |
| `bio_organizacion` | `text` | Sí | - | NULL |  |
| `localidad` | `varchar(100)` | Sí | - | NULL |  |
| `telefono_contacto` | `varchar(50)` | Sí | - | NULL |  |
| `sitio_web` | `varchar(255)` | Sí | - | NULL |  |
| `verificado_oficial` | `tinyint(1)` | Sí | - | 0 |  |
| `actualizado_en` | `timestamp` | No | - | current_timestamp() | on update current_timestamp() |

---

### Tabla: `permisos`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `int(11)` | No | **PK** | `NULL` | auto_increment |
| `nombre_permiso` | `varchar(50)` | No | **UNIQUE** | `NULL` |  |
| `descripcion` | `text` | Sí | - | NULL |  |

---

### Tabla: `politicas_contrasenas`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `int(11)` | No | **PK** | `NULL` | auto_increment |
| `longitud_minima` | `int(11)` | Sí | - | 8 |  |
| `requiere_mayuscula` | `tinyint(1)` | Sí | - | 1 |  |
| `requiere_minuscula` | `tinyint(1)` | Sí | - | 1 |  |
| `requiere_numero` | `tinyint(1)` | Sí | - | 1 |  |
| `requiere_caracter_especial` | `tinyint(1)` | Sí | - | 1 |  |
| `expiracion_dias` | `int(11)` | Sí | - | 90 |  |
| `historial_cantidad` | `int(11)` | Sí | - | 5 |  |
| `actualizado_en` | `timestamp` | No | - | current_timestamp() | on update current_timestamp() |
| `actualizado_por` | `int(11)` | Sí | **FK/IDX** | NULL |  |

---

### Tabla: `resultados_detalle`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `int(11)` | No | **PK** | `NULL` | auto_increment |
| `encuentro_id` | `int(11)` | No | **FK/IDX** | `NULL` |  |
| `tipo_dato` | `varchar(50)` | No | - | `NULL` | kills_local, kills_visitante, motivo_victoria, duracion_minutos |
| `valor` | `varchar(255)` | No | - | `NULL` |  |

---

### Tabla: `roles`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `int(11)` | No | **PK** | `NULL` | auto_increment |
| `nombre_rol` | `varchar(50)` | No | **UNIQUE** | `NULL` |  |
| `descripcion` | `text` | Sí | - | NULL |  |
| `nivel_permiso` | `int(11)` | Sí | - | 0 |  |

---

### Tabla: `rol_permisos`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `rol_id` | `int(11)` | No | **PK** | `NULL` |  |
| `permiso_id` | `int(11)` | No | **PK** | `NULL` |  |
| `asignado_en` | `timestamp` | No | - | current_timestamp() |  |

---

### Tabla: `sesiones_activas`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `int(11)` | No | **PK** | `NULL` | auto_increment |
| `usuario_id` | `int(11)` | No | **FK/IDX** | `NULL` |  |
| `token_sesion` | `varchar(255)` | No | **UNIQUE** | `NULL` |  |
| `ip_origen` | `varchar(45)` | No | - | `NULL` |  |
| `user_agent` | `text` | Sí | - | NULL |  |
| `ultima_actividad` | `timestamp` | No | - | current_timestamp() |  |
| `creado_en` | `timestamp` | No | - | current_timestamp() |  |
| `expira_en` | `timestamp` | No | - | (current_timestamp() + interval 7 day) |  |
| `activa` | `tinyint(1)` | Sí | - | 1 |  |

---

### Tabla: `sistemas_puntuacion`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `int(11)` | No | **PK** | `NULL` | auto_increment |
| `nombre` | `varchar(50)` | No | **UNIQUE** | `NULL` |  |
| `puntos_victoria` | `decimal(5,2)` | No | - | `NULL` |  |
| `puntos_empate` | `decimal(5,2)` | No | - | `NULL` |  |
| `puntos_derrota` | `decimal(5,2)` | No | - | `NULL` |  |
| `descripcion` | `text` | Sí | - | NULL |  |

---

### Tabla: `solicitudes_equipo`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `int(11)` | No | **PK** | `NULL` | auto_increment |
| `equipo_id` | `int(11)` | No | **FK/IDX** | `NULL` |  |
| `usuario_id` | `int(11)` | No | **FK/IDX** | `NULL` |  |
| `mensaje` | `text` | Sí | - | NULL |  |
| `estado` | `varchar(20)` | Sí | - | 'pendiente' |  |
| `fecha_solicitud` | `timestamp` | No | - | current_timestamp() |  |
| `fecha_respuesta` | `timestamp` | Sí | - | NULL |  |
| `respondido_por` | `int(11)` | Sí | **FK/IDX** | NULL |  |

---

### Tabla: `tokens_recuperacion`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `int(11)` | No | **PK** | `NULL` | auto_increment |
| `usuario_id` | `int(11)` | No | **FK/IDX** | `NULL` |  |
| `token` | `varchar(255)` | No | **UNIQUE** | `NULL` |  |
| `expira_en` | `timestamp` | No | - | (current_timestamp() + interval 1 hour) |  |
| `usado` | `tinyint(1)` | Sí | - | 0 |  |
| `creado_en` | `timestamp` | No | - | current_timestamp() |  |

---

### Tabla: `tokens_verificacion_email`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `int(11)` | No | **PK** | `NULL` | auto_increment |
| `usuario_id` | `int(11)` | No | **FK/IDX** | `NULL` |  |
| `token` | `varchar(255)` | No | **UNIQUE** | `NULL` |  |
| `expira_en` | `timestamp` | No | - | (current_timestamp() + interval 24 hour) |  |
| `usado` | `tinyint(1)` | Sí | - | 0 |  |
| `creado_en` | `timestamp` | No | - | current_timestamp() |  |

---

### Tabla: `torneos`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `int(11)` | No | **PK** | `NULL` | auto_increment |
| `nombre` | `varchar(255)` | No | - | `NULL` |  |
| `juego_id` | `int(11)` | No | **FK/IDX** | `NULL` |  |
| `descripcion` | `text` | Sí | - | NULL |  |
| `formato` | `varchar(50)` | No | - | `NULL` |  |
| `estado` | `varchar(50)` | No | **FK/IDX** | 'borrador' |  |
| `cupo_max_equipos` | `int(11)` | No | - | `NULL` |  |
| `cupo_min_equipos` | `int(11)` | Sí | - | 2 |  |
| `fecha_inicio_inscripcion` | `date` | Sí | - | NULL |  |
| `fecha_limite_inscripcion` | `date` | Sí | - | NULL |  |
| `fecha_inicio` | `date` | Sí | **FK/IDX** | NULL |  |
| `fecha_fin` | `date` | Sí | - | NULL |  |
| `reglas` | `text` | Sí | - | NULL |  |
| `premios` | `text` | Sí | - | NULL |  |
| `ubicacion` | `varchar(255)` | Sí | - | NULL |  |
| `localidad` | `varchar(100)` | Sí | - | NULL |  |
| `comunidad` | `varchar(100)` | Sí | - | NULL |  |
| `banner_url` | `text` | Sí | - | NULL |  |
| `stream_url` | `varchar(255)` | Sí | - | NULL |  |
| `organizador_id` | `int(11)` | No | **FK/IDX** | `NULL` |  |
| `creado_en` | `timestamp` | No | - | current_timestamp() |  |
| `actualizado_en` | `timestamp` | No | - | current_timestamp() | on update current_timestamp() |
| `modalidad_id` | `int(11)` | No | **FK/IDX** | 1 |  |
| `sistema_puntuacion_id` | `int(11)` | Sí | **FK/IDX** | NULL |  |
| `puntos_victoria` | `decimal(5,2)` | Sí | - | 3.00 |  |
| `puntos_empate` | `decimal(5,2)` | Sí | - | 1.00 |  |
| `puntos_derrota` | `decimal(5,2)` | Sí | - | 0.00 |  |
| `tipo_resultado` | `enum('goles','puntos','rondas','booleano')` | Sí | - | 'goles' |  |
| `mejor_de` | `int(11)` | Sí | - | 1 | Número de mapas o partidas para ganar la serie (BO1, BO3, BO5) |

---

### Tabla: `torneo_actividades`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `int(11)` | No | **PK** | `NULL` | auto_increment |
| `torneo_id` | `int(11)` | No | **FK/IDX** | `NULL` |  |
| `titulo` | `varchar(150)` | No | - | `NULL` |  |
| `tipo` | `enum('administrativo','reunion','competencia','premiacion')` | Sí | - | 'competencia' |  |
| `fecha` | `date` | No | - | `NULL` |  |
| `hora` | `time` | Sí | - | NULL |  |
| `creado_en` | `timestamp` | No | - | current_timestamp() |  |

---

### Tabla: `torneo_cambio_estado`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `int(11)` | No | **PK** | `NULL` | auto_increment |
| `torneo_id` | `int(11)` | No | **FK/IDX** | `NULL` |  |
| `estado_anterior` | `varchar(50)` | Sí | - | NULL |  |
| `estado_nuevo` | `varchar(50)` | No | - | `NULL` |  |
| `motivo` | `text` | Sí | - | NULL |  |
| `usuario_id` | `int(11)` | No | **FK/IDX** | `NULL` |  |
| `fecha_cambio` | `timestamp` | No | - | current_timestamp() |  |

---

### Tabla: `torneo_comentarios`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `int(11)` | No | **PK** | `NULL` | auto_increment |
| `torneo_id` | `int(11)` | No | **FK/IDX** | `NULL` |  |
| `usuario_id` | `int(11)` | Sí | **FK/IDX** | NULL |  |
| `nombre_autor` | `varchar(100)` | No | - | `NULL` |  |
| `email_autor` | `varchar(255)` | No | - | `NULL` |  |
| `comentario` | `text` | No | - | `NULL` |  |
| `aprobado` | `tinyint(1)` | Sí | - | 1 |  |
| `fecha_hora` | `timestamp` | No | - | current_timestamp() |  |

---

### Tabla: `torneo_config`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `torneo_id` | `int(11)` | No | **PK** | `NULL` |  |
| `clave` | `varchar(100)` | No | **PK** | `NULL` |  |
| `valor` | `text` | No | - | `NULL` |  |

---

### Tabla: `torneo_encuentros`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `int(11)` | No | **PK** | `NULL` | auto_increment |
| `torneo_id` | `int(11)` | No | **FK/IDX** | `NULL` |  |
| `ronda` | `int(11)` | No | **FK/IDX** | `NULL` |  |
| `participante_local_id` | `int(11)` | Sí | **FK/IDX** | NULL |  |
| `participante_visitante_id` | `int(11)` | Sí | **FK/IDX** | NULL |  |
| `fecha_hora_programada` | `timestamp` | Sí | **FK/IDX** | NULL |  |
| `cancha` | `varchar(100)` | Sí | - | NULL |  |
| `resultado_local` | `decimal(10,2)` | Sí | - | NULL |  |
| `resultado_visitante` | `decimal(10,2)` | Sí | - | NULL |  |
| `estado` | `varchar(50)` | Sí | - | 'programado' |  |
| `participante_ganador_id` | `int(11)` | Sí | **FK/IDX** | NULL |  |
| `siguiente_encuentro_id` | `int(11)` | Sí | **FK/IDX** | NULL |  |
| `canal_transmision` | `varchar(100)` | Sí | - | NULL |  |
| `creado_en` | `timestamp` | No | - | current_timestamp() |  |
| `ultima_modificacion` | `timestamp` | No | - | current_timestamp() | on update current_timestamp() |
| `modificado_por_usuario_id` | `int(11)` | Sí | **FK/IDX** | NULL |  |

---

### Tabla: `torneo_posiciones`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `torneo_id` | `int(11)` | No | **PK** | `NULL` |  |
| `participante_id` | `int(11)` | No | **PK** | `NULL` |  |
| `partidos_jugados` | `int(11)` | Sí | - | 0 |  |
| `partidos_ganados` | `int(11)` | Sí | - | 0 |  |
| `partidos_empatados` | `int(11)` | Sí | - | 0 |  |
| `partidos_perdidos` | `int(11)` | Sí | - | 0 |  |
| `puntos_favor` | `decimal(10,2)` | Sí | - | 0.00 | Goles/Puntos/Rondas a favor |
| `puntos_contra` | `decimal(10,2)` | Sí | - | 0.00 | Goles/Puntos/Rondas en contra |
| `diferencia_goles` | `decimal(10,2)` | Sí | - | NULL | STORED GENERATED |
| `puntos` | `int(11)` | Sí | **FK/IDX** | 0 |  |
| `sanciones_puntos` | `int(11)` | Sí | - | 0 |  |
| `ultima_actualizacion` | `timestamp` | No | - | current_timestamp() | on update current_timestamp() |

---

### Tabla: `torneo_suizo_parejas`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `torneo_id` | `int(11)` | No | **PK** | `NULL` |  |
| `participante_a_id` | `int(11)` | No | **PK** | `NULL` |  |
| `participante_b_id` | `int(11)` | No | **PK** | `NULL` |  |
| `ronda` | `int(11)` | No | - | `NULL` |  |
| `ya_se_enfrentaron` | `tinyint(1)` | Sí | - | 1 |  |
| `fecha_encuentro` | `timestamp` | Sí | - | NULL |  |

---

### Tabla: `usuarios`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `int(11)` | No | **PK** | `NULL` | auto_increment |
| `email` | `varchar(255)` | No | **UNIQUE** | `NULL` |  |
| `contrasena_hash` | `text` | No | - | `NULL` |  |
| `nombre_completo` | `varchar(255)` | No | - | `NULL` |  |
| `telefono` | `varchar(20)` | Sí | - | NULL |  |
| `foto_perfil_url` | `text` | Sí | - | NULL |  |
| `rol_id` | `int(11)` | No | **FK/IDX** | `NULL` |  |
| `esta_activo` | `tinyint(1)` | Sí | - | 1 |  |
| `email_verificado` | `tinyint(1)` | Sí | - | 0 |  |
| `fecha_registro` | `timestamp` | No | - | current_timestamp() |  |
| `ultimo_acceso` | `timestamp` | Sí | - | NULL |  |

---

### Tabla: `usuario_logros`
| Columna | Tipo de Dato | Nulo | Clave | Valor por Defecto | Extra |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `usuario_id` | `int(11)` | No | **PK** | `NULL` |  |
| `logro_id` | `int(11)` | No | **PK** | `NULL` |  |
| `estado` | `enum('bloqueado','en_curso','desbloqueado')` | Sí | - | 'desbloqueado' |  |
| `progreso_actual` | `int(11)` | Sí | - | 1 |  |
| `progreso_objetivo` | `int(11)` | Sí | - | 1 |  |
| `fecha_desbloqueo` | `timestamp` | No | - | current_timestamp() |  |

---



--- 

## 🚀 Mapeos HTML de Formularios (Conservados)

### 📋 Mapeo Formulario ➔ PHP ➔ MySQL (Usuarios y Perfiles)

* **Vistas HTML Involucradas**:
  * [vistas/admin/usuarios/formulario.html](file:///c:/Users/juani/Documents/GitHub/SGDM/codigo_fuente/vistas/admin/usuarios/formulario.html)
  * [vistas/auth/registro.html](file:///c:/Users/juani/Documents/GitHub/SGDM/codigo_fuente/vistas/auth/registro.html)
  * [vistas/organizador/perfil.html](file:///c:/Users/juani/Documents/GitHub/SGDM/codigo_fuente/vistas/organizador/perfil.html)
  * [vistas/jugador/perfil-jugador.html](file:///c:/Users/juani/Documents/GitHub/SGDM/codigo_fuente/vistas/jugador/perfil-jugador.html)

| Campo en HTML (`name` / `id`) | Método HTTP | Variable en PHP | Tipo PHP | Tabla Destino | Columna Destino SQL | Validación y Procesamiento en PHP |
| :--- | :---: | :--- | :---: | :--- | :--- | :--- |
| `name="nombre"` | `POST` | `$_POST['nombre']` | `string` | `usuarios` | `nombre_completo` | `trim(strip_tags($_POST['nombre']))`. Longitud 3 a 255 caracteres. |
| `name="correo"` | `POST` | `$_POST['correo']` | `string` | `usuarios` | `email` | `filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL)`. Comprobar que no exista en BD. |
| `name="contrasena"` | `POST` | `$_POST['contrasena']` | `string` | `usuarios` | `contrasena_hash` | `password_hash($_POST['contrasena'], PASSWORD_BCRYPT, ['cost' => 10])`. |
| `name="rol"` | `POST` | `$_POST['rol']` | `int` | `usuarios` | `rol_id` | `(int)$_POST['rol']`. Comprobar que exista en tabla `roles`. |
| `name="telefono"` | `POST` | `$_POST['telefono']` | `string` | `usuarios` | `telefono` | Expresión regular para números telefónicos. |
| `id="inputOrganizacion"` | `POST` | `$_POST['organizacion']`| `string`| `perfiles_organizadores` | `nombre_organizacion` | Requerido si el rol es Organizador. |
| `id="inputLocalidad"` | `POST` | `$_POST['localidad']` | `string` | `perfiles_organizadores` | `localidad` | Sanitización de caracteres especiales. |
| `id="inputBiografia"` | `POST` | `$_POST['biografia']` | `string` | `perfiles_organizadores` | `bio_organizacion` | Longitud máxima 300 caracteres. |
| `id="input-banner"` | `POST (FILE)` | `$_FILES['banner']` | `array` | `perfiles_jugadores` | `banner_url` | Validación MIME de imagen (`image/jpeg`, `image/png`), redimensionado y subida segura al servidor. |
| `id="input-avatar"` | `POST (FILE)` | `$_FILES['avatar']` | `array` | `usuarios` | `foto_perfil_url` | Validación de tamaño (máx 2MB), subida a `publico/uploads/avatars/`. |

---

### 🏆 MÓDULO B: Torneos, Disciplinas y Encuentros

#### 1. Tabla: `juegos` (Catálogo de Disciplinas)
| Columna SQL | Tipo de Dato MySQL | Clave | Nulo | Valor por Defecto | Descripción |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `INT AUTO_INCREMENT` | **PK** | No | Auto | Identificador de la disciplina deportiva o eSport. |
| `nombre` | `VARCHAR(100)` | **UNIQUE** | No | Ninguno | Nombre del juego (ej. *Valorant*, *Fútbol 5*, *Rugby 7s*). |
| `categoria` | `VARCHAR(50)` | - | No | Ninguno | Categoría: `esport-shooter`, `esport-moba`, `deporte-fisico`, `juego-mesa`. |
| `formato_equipo_defecto` | `INT` | - | No | `1` | Jugadores estándar por equipo (1 para individual, 5, 7, 11). |
| `puntos_victoria` | `DECIMAL(5,2)` | - | No | `3.00` | Puntos otorgados por ganar un partido. |
| `puntos_empate` | `DECIMAL(5,2)` | - | No | `1.00` | Puntos otorgados por empatar. |
| `puntos_derrota` | `DECIMAL(5,2)` | - | No | `0.00` | Puntos otorgados por perder. |
| `activo` | `BOOLEAN` | - | No | `TRUE` | Permite habilitar o deshabilitar la disciplina. |

---

#### 2. Tabla: `torneos` (Estructura de la Competencia)
| Columna SQL | Tipo de Dato MySQL | Clave | Nulo | Valor por Defecto | Descripción |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `INT AUTO_INCREMENT` | **PK** | No | Auto | Identificador único del torneo. |
| `nombre` | `VARCHAR(255)` | - | No | Ninguno | Título oficial de la competencia. |
| `juego_id` | `INT` | **FK** | No | Ninguno | Referencia a `juegos(id)` (`ON DELETE RESTRICT`). |
| `organizador_id` | `INT` | **FK** | No | Ninguno | Referencia a `usuarios(id)` (`ON DELETE RESTRICT`). |
| `formato` | `VARCHAR(50)` | - | No | Ninguno | `liga`, `eliminacion_directa`, `suizo`. |
| `estado` | `VARCHAR(50)` | - | No | `'borrador'` | `'borrador'`, `'inscripciones_abiertas'`, `'en_curso'`, `'finalizado'`, `'cancelado'`. |
| `modalidad_id` | `INT` | **FK** | No | `1` | Referencia a `modalidades(id)` (`1: Individual`, `2: Equipos`). |
| `sistema_puntuacion_id` | `INT` | **FK** | Sí | `NULL` | Referencia a `sistemas_puntuacion(id)`. |
| `cupo_max_equipos` | `INT` | - | No | Ninguno | Capacidad máxima de participantes (mínimo 2). |
| `fecha_inicio_inscripcion` | `DATE` | - | Sí | `NULL` | Fecha de apertura de inscripciones. |
| `fecha_limite_inscripcion` | `DATE` | - | Sí | `NULL` | Fecha límite para anotarse. |
| `fecha_inicio` | `DATE` | - | Sí | `NULL` | Fecha de inicio de los partidos. |
| `fecha_fin` | `DATE` | - | Sí | `NULL` | Fecha de conclusión (`CHECK (fecha_inicio <= fecha_fin)`). |
| `ubicacion` | `VARCHAR(255)` | - | Sí | `NULL` | Sede física o servidor en línea. |
| `localidad` | `VARCHAR(100)` | - | Sí | `NULL` | Ciudad o departamento anfitrión. |
| `comunidad` | `VARCHAR(100)` | - | Sí | `NULL` | Club u organización anfitriona. |
| `banner_url` | `TEXT` | - | Sí | `NULL` | Imagen visual de cabecera del torneo. |
| `stream_url` | `VARCHAR(255)` | - | Sí | `NULL` | Enlace a transmisión en vivo (Twitch / YouTube). |
| `reglas` | `TEXT` | - | Sí | `NULL` | Reglamento oficial detallado. |
| `premios` | `TEXT` | - | Sí | `NULL` | Descripción del Prize Pool o trofeos. |
| `tipo_resultado` | `ENUM` | - | No | `'goles'` | Unidad de puntaje: `'goles'`, `'puntos'`, `'rondas'`, `'booleano'`. |
| `mejor_de` | `INT` | - | No | `1` | Cantidad de partidas por serie (BO1, BO3, BO5). |

---

#### 3. Tabla: `participantes_torneo` (Inscripciones de Equipos o Jugadores)
| Columna SQL | Tipo de Dato MySQL | Clave | Nulo | Valor por Defecto | Descripción |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `INT AUTO_INCREMENT` | **PK** | No | Auto | Identificador del participante en el torneo. |
| `torneo_id` | `INT` | **FK** | No | Ninguno | Referencia a `torneos(id)` con `ON DELETE CASCADE`. |
| `tipo` | `ENUM('equipo','usuario')` | - | No | Ninguno | Determina si el competidor es una escuadra o un jugador individual. |
| `referencia_id` | `INT` | - | No | Ninguno | ID de la tabla `equipos` o `usuarios` según el tipo. |
| `nombre` | `VARCHAR(255)` | - | No | Ninguno | Nombre del equipo o jugador para carga veloz. |
| `estado` | `VARCHAR(50)` | - | No | `'pendiente'` | `'pendiente'`, `'confirmado'`, `'rechazado'`, `'cancelado'`. |
| `fecha_inscripcion` | `TIMESTAMP` | - | No | `CURRENT_TIMESTAMP` | Fecha de solicitud de inscripción. |
| `confirmado_por` | `INT` | **FK** | Sí | `NULL` | Usuario que aprobó el cupo (`ON DELETE SET NULL`). |

---

#### 4. Tabla: `torneo_encuentros` (Fixture y Partidos)
| Columna SQL | Tipo de Dato MySQL | Clave | Nulo | Valor por Defecto | Descripción |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `INT AUTO_INCREMENT` | **PK** | No | Auto | Identificador único del enfrentamiento. |
| `torneo_id` | `INT` | **FK** | No | Ninguno | Referencia a `torneos(id)` con `ON DELETE CASCADE`. |
| `ronda` | `INT` | - | No | Ninguno | Número de fecha (en Liga) o fase (1: Cuartos, 2: Semis, etc.). |
| `participante_local_id` | `INT` | **FK** | Sí | `NULL` | Referencia a `participantes_torneo(id)`. |
| `participante_visitante_id` | `INT` | **FK** | Sí | `NULL` | Referencia a `participantes_torneo(id)`. |
| `fecha_hora_programada` | `TIMESTAMP` | - | Sí | `NULL` | Fecha y hora pactada para el partido. |
| `cancha` | `VARCHAR(100)` | - | Sí | `NULL` | Número de cancha, mesa o sala de juego. |
| `resultado_local` | `DECIMAL(10,2)` | - | Sí | `NULL` | Marcador o puntos obtenidos por el local. |
| `resultado_visitante` | `DECIMAL(10,2)` | - | Sí | `NULL` | Marcador o puntos obtenidos por el visitante. |
| `estado` | `VARCHAR(50)` | - | No | `'programado'` | `'programado'`, `'en_curso'`, `'finalizado'`, `'cancelado'`, `'aplazado'`. |
| `participante_ganador_id` | `INT` | **FK** | Sí | `NULL` | Ganador del partido (`ON DELETE SET NULL`). |
| `siguiente_encuentro_id` | `INT` | **FK** | Sí | `NULL` | Llave sucesiva en eliminación directa. |

---

#### 5. Tabla: `torneo_posiciones` (Tabla de Clasificación)
| Columna SQL | Tipo de Dato MySQL | Clave | Nulo | Valor por Defecto | Descripción |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `torneo_id` | `INT` | **PK, FK** | No | Ninguno | Clave compuesta vinculada a `torneos(id)`. |
| `participante_id` | `INT` | **PK, FK** | No | Ninguno | Clave compuesta vinculada a `participantes_torneo(id)`. |
| `partidos_jugados` | `INT` | - | No | `0` | Total de encuentros disputados (PJ). |
| `partidos_ganados` | `INT` | - | No | `0` | Partidos ganados (PG). |
| `partidos_empatados` | `INT` | - | No | `0` | Partidos empatados (PE). |
| `partidos_perdidos` | `INT` | - | No | `0` | Partidos perdidos (PP). |
| `puntos_favor` | `DECIMAL(10,2)` | - | No | `0.00` | Goles, puntos o rondas a favor (GF). |
| `puntos_contra` | `DECIMAL(10,2)` | - | No | `0.00` | Goles, puntos o rondas en contra (GC). |
| `diferencia_goles` | `DECIMAL(10,2)` | - | No | `STORED` | Columna generada: `(puntos_favor - puntos_contra)`. |
| `puntos` | `INT` | - | No | `0` | Puntos totales en la tabla de posiciones (PTS). |

---

### 📋 Mapeo Formulario ➔ PHP ➔ MySQL (Torneos y Resultados)

* **Vistas HTML Involucradas**:
  * [vistas/admin/torneos/crear.html](file:///c:/Users/juani/Documents/GitHub/SGDM/codigo_fuente/vistas/admin/torneos/crear.html)
  * [vistas/organizador/crear-torneo.html](file:///c:/Users/juani/Documents/GitHub/SGDM/codigo_fuente/vistas/organizador/crear-torneo.html)
  * [vistas/admin/resultados/formulario.html](file:///c:/Users/juani/Documents/GitHub/SGDM/codigo_fuente/vistas/admin/resultados/formulario.html)

| Campo en HTML (`name` / `id`) | Método HTTP | Variable en PHP | Tipo PHP | Tabla Destino | Columna Destino SQL | Validación y Procesamiento en PHP |
| :--- | :---: | :--- | :---: | :--- | :--- | :--- |
| `name="nombre"` | `POST` | `$_POST['nombre']` | `string` | `torneos` | `nombre` | Longitud de 3 a 100 caracteres. Sanitizar con `htmlspecialchars`. |
| `name="disciplina"` | `POST` | `$_POST['disciplina']` | `int` | `torneos` | `juego_id` | Validar que sea un entero y exista en `juegos`. |
| `name="tipo_modalidad"` | `POST` | `$_POST['tipo_modalidad']` | `int` | `torneos` | `modalidad_id` | Validar `1` (Individual) o `2` (Equipos). |
| `name="formato_competicion"` | `POST` | `$_POST['formato_competicion']`| `string`| `torneos` | `formato` | Validar en lista blanca: `in_array($val, ['liga','eliminacion_directa','suizo'])`. |
| `name="cupos"` | `POST` | `$_POST['cupos']` | `int` | `torneos` | `cupo_max_equipos` | `min: 2`, `max: 64`. |
| `name="fechaInicio"` | `POST` | `$_POST['fechaInicio']` | `string` | `torneos` | `fecha_inicio` | Formato fecha `Y-m-d`. Validar que `fechaInicio <= fechaFin`. |
| `name="fechaFin"` | `POST` | `$_POST['fechaFin']` | `string` | `torneos` | `fecha_fin` | Formato fecha `Y-m-d`. |
| `name="ubicacion"` | `POST` | `$_POST['ubicacion']` | `string` | `torneos` | `ubicacion` | Texto libre de hasta 255 caracteres. |
| `name="reglas"` | `POST` | `$_POST['reglas']` | `string` | `torneos` | `reglas` | Texto plano de reglamento. |
| `name="torneo"` | `POST` | `$_POST['torneo']` | `int` | `torneo_encuentros`| `torneo_id` | ID del torneo para filtrar el encuentro. |
| `name="encuentro"` | `POST` | `$_POST['encuentro']` | `int` | `torneo_encuentros`| `id` | Clave primaria del encuentro a actualizar. |
| `name="puntuacion_local"` | `POST` | `$_POST['puntuacion_local']` | `float` | `torneo_encuentros`| `resultado_local` | Número no negativo (`>= 0`). Dispara recálculo en `torneo_posiciones`. |
| `name="puntuacion_visitante"`| `POST`| `$_POST['puntuacion_visitante']`| `float`| `torneo_encuentros`| `resultado_visitante`| Número no negativo. Determina ganador y actualiza estado a `'finalizado'`. |

---

### 🛡️ MÓDULO C: Seguridad, Auditoría y Comunicaciones

#### 1. Tabla: `auditoria_cambios` (Bitácora de Modificaciones Críticas)
| Columna SQL | Tipo de Dato MySQL | Clave | Nulo | Valor por Defecto | Descripción |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `INT AUTO_INCREMENT` | **PK** | No | Auto | Identificador del evento auditado. |
| `tabla_afectada` | `VARCHAR(100)` | - | No | Ninguno | Nombre de la tabla modificada (ej. `torneos`, `usuarios`, `resultados`). |
| `registro_id` | `INT` | - | No | Ninguno | ID del registro insertado, actualizado o borrado. |
| `accion` | `VARCHAR(20)` | - | No | Ninguno | Operación realizada: `'INSERT'`, `'UPDATE'`, `'DELETE'`. |
| `usuario_id` | `INT` | **FK** | Sí | `NULL` | Usuario que ejecutó la acción (`ON DELETE SET NULL`). |
| `datos_viejos` | `JSON` | - | Sí | `NULL` | Snapshot en JSON de los datos anteriores al cambio. |
| `datos_nuevos` | `JSON` | - | Sí | `NULL` | Snapshot en JSON de los nuevos datos guardados. |
| `ip_origen` | `VARCHAR(45)` | - | Sí | `NULL` | Dirección IP del cliente (`$_SERVER['REMOTE_ADDR']`). |
| `user_agent` | `TEXT` | - | Sí | `NULL` | Navegador y sistema operativo del usuario. |
| `fecha_hora` | `TIMESTAMP` | - | No | `CURRENT_TIMESTAMP` | Momento exacto del cambio. |

---

#### 2. Tabla: `politicas_contrasenas` (Parámetros Globales de Claves)
| Columna SQL | Tipo de Dato MySQL | Clave | Nulo | Valor por Defecto | Descripción |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id` | `INT AUTO_INCREMENT` | **PK** | No | Auto | Registro único de configuración. |
| `longitud_minima` | `INT` | - | No | `8` | Cantidad mínima obligatoria de caracteres (ej. 8). |
| `requiere_mayuscula` | `BOOLEAN` | - | No | `TRUE` | Obliga a incluir al menos una letra mayúscula (`A-Z`). |
| `requiere_minuscula` | `BOOLEAN` | - | No | `TRUE` | Obliga a incluir al menos una letra minúscula (`a-z`). |
| `requiere_numero` | `BOOLEAN` | - | No | `TRUE` | Obliga a incluir al menos un dígito (`0-9`). |
| `requiere_caracter_especial`| `BOOLEAN` | - | No | `TRUE` | Obliga a incluir símbolos (`!@#$%^&*`). |
| `expiracion_dias` | `INT` | - | No | `90` | Días de validez de una clave antes de forzar cambio. |
| `historial_cantidad` | `INT` | - | No | `5` | Impide reutilizar las últimas N contraseñas anteriores. |
