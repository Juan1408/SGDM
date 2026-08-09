# ⚙️ Paquete: `configuracion/`

## 📌 Propósito
Este paquete centraliza todos los parámetros estáticos, credenciales de infraestructura y rutas del servidor necesarias para que la aplicación PHP funcione en cualquier entorno (desarrollo local en XAMPP, servidor de pruebas o contenedor Docker).

---

## 📂 Archivos Contenidos

| Archivo | Descripción | Responsabilidad |
| :--- | :--- | :--- |
| `base_datos.php` | Credenciales de MySQL (`DB_HOST`, `DB_USER`, `DB_PASSWORD`, `DB_NAME`). | Permitir modificar los datos de conexión en un único punto sin tocar el código fuente. |
| `constantes.php` | Rutas del servidor (`RUTA_MODELOS`, `RUTA_VISTAS`, `URL_BASE`). | Evitar rutas relativas rotas (`../../`) y estandarizar la inclusión de archivos mediante constantes. |

---

## 💡 Guía de Uso
En el archivo principal `publico/index.php`, estos dos archivos se cargan al inicio:
```php
require_once __DIR__ . '/../configuracion/constantes.php';
require_once __DIR__ . '/../configuracion/base_datos.php';
```
De esta manera, todas las constantes quedan disponibles globalmente para cualquier modelo o controlador.
