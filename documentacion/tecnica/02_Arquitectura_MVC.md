# 🧩 Arquitectura del Sistema (Patrón MVC)

ASCEND está construido enteramente utilizando **PHP 8 Nativo** bajo el paradigma de **Programación Orientada a Objetos (POO)** y orquestado mediante el patrón de diseño arquitectónico **MVC (Modelo-Vista-Controlador)**. 

No se utilizaron frameworks externos (como Laravel o Symfony), lo que demuestra un dominio total sobre el flujo de ejecución web por parte del equipo desarrollador.

---

## 1. El Controlador Frontal (`index.php`)

Toda la aplicación utiliza un patrón de "Front Controller" o Controlador Frontal. 
- **¿Qué es?** Existe un único punto de entrada a la aplicación web: el archivo `index.php` en la raíz del proyecto.
- **¿Cómo funciona?** Gracias al archivo `.htaccess`, cualquier petición web que haga un usuario (ej: `/torneos/ver/5`) es redirigida internamente a `index.php`. 
- **El Enrutador (Router):** Dentro del `index.php`, un enrutador dinámico lee la URL, extrae los parámetros `c` (Controlador) y `a` (Acción/Método), instancia la clase correspondiente y ejecuta la lógica. Esto mantiene las URLs limpias y seguras.

---

## 2. Los Modelos (`codigo_fuente/modelos/`)

Los modelos son el **"Cerebro de Datos"**. Son las únicas clases que tienen permitido hablar con la Base de Datos.
- Heredan indirectamente o consumen la clase `Conexion.php` (la cual encapsula la lógica de PDO usando el patrón Singleton).
- Contienen toda la lógica de negocio gruesa. Por ejemplo, el archivo `Torneo.php` no solo hace un `SELECT` de los torneos, sino que incluye la lógica de los motores de competencia (algoritmos para generar llaves de eliminación directa o emparejamientos suizos).
- **Ejemplos clave:** `Usuario.php`, `Equipo.php`, `Juego.php`.

---

## 3. Los Controladores (`codigo_fuente/controladores/`)

Los controladores son los **"Directores de Orquesta"**. Reciben la petición del usuario, validan los permisos, le piden los datos al Modelo, y se los envían a la Vista.
- Aplican validaciones estrictas (RBAC) gracias a la clase helper `Sesion.php`.
- Separan la lógica según el rol del usuario (Ej: `panelOrganizadorControlador.php` vs `panelAdminControlador.php`), garantizando que la lógica de un administrador nunca se mezcle accidentalmente con la de un jugador.

---

## 4. Las Vistas (`codigo_fuente/vistas/`)

Las vistas representan la **"Interfaz de Usuario (UI)"**. Es la capa de presentación, compuesta exclusivamente por HTML, CSS nativo y bloques mínimos de PHP (únicamente para imprimir variables o hacer ciclos iterativos `foreach`).
- **Separación de Responsabilidades:** Cumpliendo con los estándares web modernos, las vistas de ASCEND **no contienen consultas SQL ni lógica de negocio compleja**.
- Se apoyan en el uso de "Plantillas" (`plantilla/cabecera.php`, `menu_lateral.php`) para evitar repetir el código del diseño (navbar, footer) en las más de 40 pantallas que tiene el sistema.
