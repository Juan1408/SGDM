# 🗄️ Paquete: `modelos/` (Capa de Acceso a Datos)

## 📌 Propósito
Este paquete representa la **M (Modelo)** dentro del patrón de arquitectura **MVC**. Contiene todas las clases que interactúan directamente con el motor de base de datos MySQL mediante sentencias preparadas con **PDO**.

---

## 📂 Archivos Contenidos

| Archivo | Tabla(s) SQL Asociada(s) | Responsabilidad Principal |
| :--- | :--- | :--- |
| `Conexion.php` | N/A (Infraestructura) | Administrar la conexión PDO mediante el patrón **Singleton**. |
| `Usuario.php` | `usuarios`, `perfiles_jugadores`, `perfiles_organizadores` | Operaciones CRUD de usuarios y gestión de especialización de roles en 3FN. |
| `Rol.php` | `roles`, `permisos`, `rol_permisos` | Consulta y asignación de roles y permisos RBAC. |
| `Juego.php` | `juegos`, `modalidades`, `sistemas_puntuacion` | Catálogo de disciplinas deportivas y deportes electrónicos. |
| `Torneo.php` | `torneos`, `torneo_cambio_estado` | Listado, consulta y actualización de estados de torneos. |
| `Auditoria.php` | `logs_acceso`, `auditoria_cambios` | Consulta de pistas de auditoría y registros de seguridad exigidos por UTU. |

---

## 🔒 Regla de Oro en los Modelos
* **Nunca** se escriben etiquetas HTML dentro de un archivo del paquete `modelos/`.
* **Siempre** se usan sentencias preparadas (`$stmt = $this->db->prepare(...)`) para prevenir Inyección SQL.
* Los métodos devuelven datos en bruto (`array`, `int`, `bool` o `null`) para que el Controlador decida qué hacer con ellos.
