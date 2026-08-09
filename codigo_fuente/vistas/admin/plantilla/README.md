# 🖼️ Paquete: `vistas/admin/plantilla/` (Layout Modular)

## 📌 Propósito
Este paquete contiene los fragmentos visuales comunes de la interfaz del Administrador. Permite reutilizar el encabezado, menú lateral y pie de página en todas las pantallas sin duplicar código HTML.

---

## 📂 Archivos Contenidos

| Archivo | Responsabilidad |
| :--- | :--- |
| `cabecera.php` | Etiquetas `<head>`, hojas de estilos CSS y barra superior con usuario logueado. |
| `menu_lateral.php` | Menú lateral (`<aside>`) con enlaces dinámicos en PHP a cada módulo. |
| `pie_pagina.php` | Cierre de etiquetas HTML (`</main>`, `</div>`, `</body>`), footer y carga de scripts JS. |

---

## 💡 Cómo ensamblar una pantalla del Administrador
Cualquier vista específica del Admin (por ejemplo, el listado de usuarios) se arma en 3 bloques:

```php
<?php
$tituloPagina = "Listado de Usuarios - ASCEND";
require_once __DIR__ . '/../plantilla/cabecera.php';
require_once __DIR__ . '/../plantilla/menu_lateral.php';
?>

<!-- Contenido propio de la pantalla -->
<h2>Gestión de Usuarios</h2>
<table class="tabla-admin">
    <!-- Filas generadas con PHP -->
</table>

<?php
require_once __DIR__ . '/../plantilla/pie_pagina.php';
?>
```
