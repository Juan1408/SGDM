# 🎮 Paquete: `controladores/` (Capa de Control)

## 📌 Propósito
Este paquete representa la **C (Controlador)** dentro del patrón de arquitectura **MVC**. Cada clase actúa como el intermediario entre las solicitudes del navegador (HTTP GET/POST), los Modelos de base de datos y las Vistas de presentación.

---

## 📂 Archivos Contenidos (Módulo Administrador)

| Archivo | Entidad Principal | Métodos Clave |
| :--- | :--- | :--- |
| `AdminControlador.php` | Métricas y Dashboard | `dashboard()` |
| `UsuarioControlador.php` | Usuarios y Cuentas | `index()`, `crear()`, `guardar()`, `editar()`, `cambiarEstado()` |
| `RolControlador.php` | Roles y Permisos (RBAC) | `index()` |
| `JuegoControlador.php` | Disciplinas Deportivas | `index()` |
| `TorneoAdminControlador.php` | Supervisión de Torneos | `index()` |
| `AuditoriaControlador.php` | Logs y Trazabilidad | `accesos()`, `cambios()` |

---

## 🔄 Flujo Estándar de un Controlador
1. **Verificar Acceso**: Llama a `Sesion::requerirAdmin()` en su constructor.
2. **Procesar Datos**: Recibe variables `$_GET` o `$_POST` y las valida con `Validador.php`.
3. **Llamar al Modelo**: Ejecuta las consultas en el Modelo correspondiente.
4. **Cargar la Vista**: Hace `require_once` del archivo de vista pasándole los datos necesarios.
