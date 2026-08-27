1. Contenedores vacíos para maquetación global (<div> en lugar de <header>
y <footer>): En casi todos los archivos se observa: <div id="navbar-publicoplaceholder"></div> y <div id="footer-publico-placeholder"></div>.
a. ¿Qué solución sugiere para mejorar esto?
**Respuesta:** Se deben utilizar etiquetas semánticas de HTML5 en lugar de divs genéricos. Los contenedores deberían ser `<header id="navbar-publico-placeholder"></header>` y `<footer id="footer-publico-placeholder"></footer>`. Esto mejora drásticamente la accesibilidad (permitiendo a los lectores de pantalla identificar correctamente las regiones clave de la página) y el posicionamiento SEO.

2. En archivos como detalle-torneo.html, index.html y torneos.html, se salta el
encabezado principal <h1> e inician las secciones directamente con <h2> o
<h3>.
a. ¿Cuántos <h1> debe existir por página para definir el tema principal?
**Respuesta:** El verdadero problema radica en **ambos puntos**, ya que destruyen la estructura lógica del documento, afectando críticamente la **accesibilidad (A11y)** y el **SEO**:

1. **Falta de un `<h1>` (el punto de partida):** Los usuarios con discapacidades visuales utilizan lectores de pantalla para generar un índice o "árbol" de navegación de la página basado en los encabezados. El `<h1>` es el título principal de ese índice. Si no existe, el usuario no tiene un contexto claro de qué trata la página en la que aterrizó. Solo debe haber **un (1) único `<h1>`** por página.
2. **Saltar directamente a `<h2>` o `<h3>` (estructura rota):** Al iniciar con un `<h2>` o saltar niveles (ej. de `<h2>` a `<h4>`), el lector de pantalla interpreta que "falta una pieza" en el medio. Esto confunde al usuario, haciéndole creer que se perdió parte del contenido o que la página está mal construida.

En resumen: No tener un `<h1>` quita el contexto principal, y saltar niveles rompe la navegación para tecnologías asistivas (y de paso, los motores de búsqueda como Google penalizan el SEO porque asumen que tu contenido está desorganizado).

3. En detalle-torneo.html, las tablas de rankings se construyen utilizando
múltiples <div> con clases CSS.
a. ¿Qué etiqueta nativa debe utilizar para no romper la accesibilidad
para lectores de pantalla?
**Respuesta:** Se debe utilizar la etiqueta nativa `<table>`. Para mantener la semántica y accesibilidad correcta, debe acompañarse de sus elementos correspondientes: `<thead>` (encabezados de tabla), `<tbody>` (cuerpo), `<tr>` (filas), `<th>` (celdas de encabezado que ayudan a los lectores de pantalla a asociar columnas/filas) y `<td>` (celdas de datos).

4. ¿Qué afecta la dependencia de las cadenas @import?
**Respuesta:** Afecta gravemente el **rendimiento (performance) y el tiempo de carga de la página**. Cuando se usan cadenas de `@import` en los archivos CSS, el navegador se ve obligado a descargar los archivos de manera secuencial (uno tras otro) en lugar de en paralelo. Esto bloquea el renderizado de la página hasta que todos los archivos CSS hayan sido descargados y procesados. La recomendación es usar la etiqueta `<link rel="stylesheet">` en el HTML, que permite descargas en paralelo.