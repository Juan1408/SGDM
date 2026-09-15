# 📋 Tareas de Ejecución y Hoja de Ruta - Módulo Administrador & Segunda Entrega Full Stack

> **Estado General del Proyecto:** 88% Completado para la Segunda Entrega.  
> **Rol Asignado:** Administrador General y Arquitecto Backend / Base de Datos.

---

## 🚨 0. Control de Versiones & Protocolo Inicial
- [ ] **0.1 Recordatorio Diarios:** Ejecutar `git pull` siempre antes de empezar a programar.

---

## 🗄️ 1. Requisitos de Base de Datos y Seguridad (Segunda Entrega)
- [x] **1.1 Modelo Relacional Normalizado (1FN, 2FN, 3FN)**:
  - [x] Crear pasaje a tablas en notación estándar `Tabla(<u>PK</u>, atributo, *FK*)`.
  - [x] Documentar `03_Diccionario_de_Datos_1FN.md`, `03_Diccionario_de_Datos_2FN.md` y `03_Diccionario_de_Datos_3FN.md`.
  - [x] Crear script SQL completo de 35 tablas `base_de_datos/ascend.sql`.
- [x] **1.2 DCL Implementado (Data Control Language)**:
  - [x] Crear declaraciones `CREATE USER`, `GRANT`, `FLUSH PRIVILEGES` en `ascend.sql`.
  - [x] Crear usuario de aplicación `'ascend_app'@'localhost'` con principio de mínimo privilegio (`SELECT, INSERT, UPDATE, DELETE`).
  - [x] Crear usuario de auditoría `'ascend_audit'@'localhost'` con permisos `SELECT` de solo lectura.
  - [x] Documentar [08_DCL_Seguridad_y_Permisos.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/08_DCL_Seguridad_y_Permisos.md).
- [x] **1.3 Documentación de Limitaciones Semánticas del DER**:
  - [x] Crear documento [limitaciones.md](file:///c:/xampp/htdocs/SGDM/Documentos_Juan/limitaciones.md) justificando casos como la participación polimórfica y reglas de negocio.

---

## 🖥️ 2. Infraestructura Servidor Web Apache (Segunda Entrega)
- [x] **2.1 Configuración de Enrutado y Front Controller**:
  - [x] Crear archivo `.htaccess` en la raíz del servidor con `RewriteEngine On` para redirigir peticiones no existentes a `index.php`.
  - [x] Documentar la configuración de `VirtualHost` para Apache.

---

## 👥 3. Gestión de Usuarios y Roles (Módulo Administrador)
- [x] **3.1 Organizadores**:
  - [x] Listar organizadores en el panel administrativo.
  - [x] Cambiar estado / bloquear acceso.
  - [x] Funcionalidad "Ver como" (Simulación de sesión).
- [x] **3.2 Jugadores**:
  - [x] Listar jugadores registrados.
  - [x] Cambiar estado / bloquear acceso.
  - [x] Funcionalidad "Ver como".
- [ ] **3.3 Usuarios Administrativos (CRUD Admin)**:
  - [ ] Interfaz y controlador para crear nuevos usuarios administradores/moderadores.
  - [ ] Edición de datos y baja lógica.

---

## 🎮 4. Gestión de Catálogos (Juegos y Disciplinas)
- [x] **4.1 Ver Catálogo de Juegos**: Listado y cambio de estado activo/inactivo.
- [x] **4.2 Registrar / Editar Juegos**: Formulario de alta y actualización de atributos deportivos.

---

## 🏆 5. Administrar y Moderar Torneos
- [ ] **5.1 Historial y Directorio Global de Torneos**:
  - [ ] Listar todos los torneos del sistema creados por los organizadores.
  - [ ] Filtros dinámicos por disciplina, fecha y estado (borrador, inscripciones abiertas, en curso, finalizados, cancelados).
  - [ ] Modificación de emergencia / moderación administrativa de torneos con nombres o reglas inadecuadas.

---

## ⚙️ 6. Configuración del Sistema y Seguridad (Módulo Administrador)
- [x] **6.1 Políticas de Seguridad y Contraseñas (ADM-07)**:
  - [x] Modelo `PoliticaContrasena.php`, controlador `PoliticaContrasenaControlador.php` y vista `politicas_contrasenas.php`.
  - [x] Configuración dinámica de longitud mínima, caracteres especiales, vencimiento e historial de claves.
  - [x] Método `validarPassword()` integrado con registro de auditoría.
- [x] **6.2 Logs de Auditoría y Seguridad**:
  - [x] Tabla `auditoria_cambios` / `logs_actividad` en SQL.
  - [x] Controlador y vista para consultar historial de operaciones del sistema.
- [ ] **6.3 Feature Flags / Módulos del Sistema**:
  - [ ] Crear interfaz para activar/desactivar módulos dinámicos (Sistema Suizo, Eliminatoria, Ligas) en tiempo real.
- [ ] **6.4 Monitoreo de Errores (Error Tracking)**:
  - [ ] Manejador global de excepciones (`set_exception_handler`) que guarde errores y trazabilidad en la BD.

---

## 🧠 7. Motores Matemáticos de Torneos (Backend Core)
- [ ] **7.1 Motor de Eliminación Directa**: Algoritmo para generar llaves (brackets) y avanzar ganadores.
- [ ] **7.2 Motor de Sistema Suizo**: Algoritmo de emparejamiento por puntuación garantizando no repetición de parejas (`torneo_suizo_parejas`).
- [ ] **7.3 Motor de Ligas**: Algoritmo Round-Robin (todos contra todos).
