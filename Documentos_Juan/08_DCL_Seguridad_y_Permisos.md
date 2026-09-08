# 📄 Documento 08: Declaraciones DCL (Data Control Language) y Seguridad en MySQL

> **Sistema:** SGDM ASCEND  
> **Ubicación en Código SQL:** [ascend.sql](file:///c:/xampp/htdocs/SGDM/base_de_datos/ascend.sql#L1099-L1124)  
> **Propósito:** Especificar e implementar los comandos de control de acceso DCL (`CREATE USER`, `GRANT`, `FLUSH PRIVILEGES`) bajo el **Principio de Mínimo Privilegio (Principle of Least Privilege)** para entornos de producción y auditoría.

---

## 📐 1. Definición Teórica de DCL

El **DCL (Data Control Language / Lenguaje de Control de Datos)** es el subconjunto de comandos SQL encargados de administrar los permisos de seguridad y roles dentro del motor de base de datos (MySQL / MariaDB).

A diferencia del **DDL** (`CREATE TABLE`) y del **DML** (`INSERT`, `UPDATE`), el DCL gestiona quiénes pueden acceder al servidor de base de datos y qué operaciones específicas tienen permitido ejecutar sobre cada objeto (tablas, vistas, bases de datos).

---

## 🛠️ 2. Sentencias DCL Implementadas en ASCEND

A continuación se detalla el bloque DCL oficial incluido al final del script `ascend.sql`:

```sql
-- =============================================================================
-- DECLARACIONES DCL (Data Control Language - Seguridad y Permisos)
-- =============================================================================

-- 1. Creación del Usuario de Producción para la Aplicación Web (PHP Backend)
CREATE USER IF NOT EXISTS 'ascend_app'@'localhost' IDENTIFIED BY 'AscendSecure2026!';

-- Concesión de permisos operacionales CRUD (Principios de Mínimo Privilegio)
GRANT SELECT, INSERT, UPDATE, DELETE ON ascend.* TO 'ascend_app'@'localhost';

-- 2. Creación del Usuario de Auditoría y Reportes (Solo Lectura)
CREATE USER IF NOT EXISTS 'ascend_audit'@'localhost' IDENTIFIED BY 'AscendAuditReadOnly2026!';

-- Concesión de privilegios exclusivos de lectura sobre logs y estadísticas
GRANT SELECT ON ascend.logs_acceso TO 'ascend_audit'@'localhost';
GRANT SELECT ON ascend.auditoria_cambios TO 'ascend_audit'@'localhost';
GRANT SELECT ON ascend.logs_actividad TO 'ascend_audit'@'localhost';
GRANT SELECT ON ascend.torneos TO 'ascend_audit'@'localhost';
GRANT SELECT ON ascend.torneo_posiciones TO 'ascend_audit'@'localhost';

-- 3. Refresco y aplicación inmediata de permisos en el motor MySQL
FLUSH PRIVILEGES;
```

---

## 🔑 3. Justificación Arquitectura de los Usuarios DCL

| Usuario MySQL | Dominio / Host | Privilegios Otorgados | Justificación de Seguridad |
| :--- | :--- | :--- | :--- |
| `'ascend_app'` | `'localhost'` | `SELECT, INSERT, UPDATE, DELETE` en `ascend.*` | **Usuario Operacional del Backend PHP:** Es la cuenta con la que se conecta [Conexion.php](file:///c:/xampp/htdocs/SGDM/codigo_fuente/modelos/Conexion.php). **NO se le otorgan permisos DDL** (`DROP TABLE`, `ALTER TABLE`) para evitar que un ataque de inyección SQL pueda borrar la estructura de la base de datos. |
| `'ascend_audit'` | `'localhost'` | `SELECT` en tablas específicas de logs y torneos | **Usuario de Inteligencia de Negocios y Auditoría:** Utilizado por analistas de datos o sistemas de reporte para consultar logs sin riesgo de modificar o borrar información sensible. |

---

## 🎓 4. Guía de Respuesta para la Defensa de Examen

Si un profesor te pregunta:  
*"¿Cómo aplicaron DCL en la base de datos y cómo garantizan la seguridad?"*

### Respuesta Modelo:
> *"Implementamos la seguridad en el motor MySQL mediante la **Sección DCL de `ascend.sql`**, aplicando estrictamente el **Principio de Mínimo Privilegio**:  
> 1. Creamos un usuario específico para la aplicación web (`ascend_app`) que solo posee permisos `SELECT`, `INSERT`, `UPDATE` y `DELETE` sobre las tablas operacionales, prohibiendo explícitamente comandos destructivos de estructura como `DROP TABLE`.  
> 2. Creamos un segundo usuario de auditoría solo lectura (`ascend_audit`) con permisos `SELECT` exclusivamente sobre logs y estadísticas.  
> 3. Evitamos el uso del usuario `root` en producción, protegiendo el sistema ante posibles inyecciones SQL."*
