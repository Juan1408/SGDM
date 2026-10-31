# 🛠️ Paquete: `ayudantes/` (Helpers)

## 📌 Propósito
Este paquete almacena clases con métodos estáticos de soporte que resuelven tareas transversales del sistema (seguridad, validación de datos y gestión de sesiones).

---

## 📂 Archivos Contenidos

| Archivo | Responsabilidad Principal | Métodos Clave |
| :--- | :--- | :--- |
| `Validador.php` | Sanitizar entradas y validar formatos de texto, email, teléfono, fechas, contraseñas e imágenes. | `sanitizar()`, `validarEmail()`, `validarTelefono()`, `validarContrasenaFuerte()`, `validarImagen()`. |
| `Sesion.php` | Control de sesiones HTTP y verificación de permisos por rol (RBAC). | `iniciar()`, `estaAutenticado()`, `requerirAdmin()`, `cerrar()`. |

---

## 💡 Ejemplo de Uso en un Controlador
```php
require_once RUTA_AYUDANTES . 'Sesion.php';
require_once RUTA_AYUDANTES . 'Validador.php';

// 1. Proteger el acceso solo a Administradores
Sesion::requerirAdmin();

// 2. Validar un dato recibido por POST
if (!Validador::validarTelefono($_POST['telefono'])) {
    // Redirigir con mensaje de error
}
```
