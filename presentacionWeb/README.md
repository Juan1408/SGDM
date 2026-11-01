# Presentación web ASCEND

Recorrido E2E de la plataforma, armado como una presentación HTML/CSS/JS sin dependencias externas. No usa PowerPoint ni librerías de terceros: se abre en cualquier navegador moderno.

## Cómo abrirla

Doble clic en `index.html` o, si el stack está levantado, entrar a:

```
http://localhost/SGDM/presentacionWeb/
```

La presentación funciona con `file://` porque no hace peticiones externas. Las 35 capturas están en `capturas/` y pesan alrededor de 24 MB en total; se cargan a demanda.

## Recorrido

1. **Apertura CharruaCode:** medallón circular 3D que gira sobre su propio eje.
2. **Revelación ASCEND:** medallón 3D con la A del logotipo al frente y la copa al reverso; debajo, el título y las estadísticas del proyecto.
3. **35 capturas:** público, alta, jugador, organizador y administrador.
4. **Cierre:** resumen del recorrido.

La marca de CharruaCode queda girando en la esquina superior izquierda durante toda la presentación. Las capturas largas se desplazan solas dentro del marco para mostrar la página completa; `G` pausa ese desplazamiento.

## Controles

| Acción | Cómo |
| --- | --- |
| Avanzar | clic en cualquier parte de la pantalla, `→`, `PageDown`, `Espacio` |
| Retroceder | clic en el borde izquierdo (primeros 56 px), clic derecho, `←`, `PageUp` |
| Avanzar / retroceder | botones redondos, `N` y `.` avanzan; `,` y `Retroceso` vuelven |
| Saltar a un capítulo | `1` a `5` o menú lateral |
| Modo video | `P` o botón ▶ de la barra superior |
| Pausar el desplazamiento de una captura | `G` o botón “Pausar recorrido” |
| Ver la captura completa | botón “Ampliar” o `Enter` |
| Pantalla completa | `F` |
| Ayuda | `?` |
| Cerrar visor o ayuda | `Esc` |

El clic universal está pensado para un mando a distancia que emula el botón izquierdo del mouse: no hace falta apuntar a un botón para avanzar. Los clics sobre controles, enlaces, barra superior, menú lateral, visor y ayuda no navegan. En móvil se navega con los botones y con un deslizamiento horizontal.

## Abrir en una captura concreta

La URL guarda el paso actual:

```
index.html#s12        → captura 12
index.html#fin        → cierre
index.html#ascend     → escena de ASCEND
index.html?intro=manual#s1  → salta la apertura automática
```

`?intro=manual` es útil para presentar sin que la apertura avance sola.

## Estructura

```
presentacionWeb/
├─ index.html
├─ css/presentacion.css
├─ js/datos.js           → capítulos, textos y datos de las 35 capturas
├─ js/presentacion.js    → navegación, modo video, visor y atajos
├─ assets/
│  ├─ charruacode-mark.png   → marca oficial de CharruaCode (medallón y barra superior)
│  ├─ charruacode-glyph.png  → glifo &lt;/&gt; recortado del logo, reverso del medallón
│  ├─ charruacode-logo.png   → logotipo completo de CharruaCode
│  ├─ ascend-logo.png        → logotipo oficial de ASCEND (A + palabra)
│  ├─ ascend-a.png           → recorte de la A, cara frontal del medallón
│  ├─ ascend-mark.png        → copa, reverso del medallón
│  └─ favicon.png
├─ capturas/             → 01 a 35
└─ scripts/cdp-demo.mjs  → recorrido E2E que genera las capturas
```

## Reemplazar los logos

Las tres piezas 3D son PNG con transparencia:

- `charruacode-mark.png`: cuadrado de 960×960 con la marca sobre un disco blanco centrado. La moneda lo recorta con `border-radius: 50%`; conviene mantener el disco centrado y el cuadrado para que el giro no se desplace.
- `charruacode-glyph.png`: el `</>` del logo recortado sobre fondo transparente (561×312). Es el reverso de la moneda y se centra con `place-items: center`.
- `ascend-a.png`: recorte de la A del logotipo (800×752). Es la cara frontal del medallón.
- `ascend-mark.png`: copa con la A y líneas de velocidad (1064×577). Es el reverso.

Si se reemplazan, mantener la transparencia y las proporciones: el CSS ajusta el tamaño con `object-fit: contain`. La moneda de CharruaCode y la marca de la barra superior usan el mismo archivo.

Los dos medallones giran con `rotateY` sin `perspective` y con las caras en el mismo plano (`transform: none` / `rotateY(180deg)`, sin `translateZ`). Así cada cara se angosta sobre el centro exacto del medallón: con `perspective` y caras separadas en Z, el centro visible se corre hasta el valor del `translateZ` al pasar por 45°. No hace falta agregar profundidad; si se agrega, el giro vuelve a desplazarse.

Los colores de marca salen de los PNG oficiales: CharruaCode navy `#0a1f33` y azul `#1f8fc4`; ASCEND azul `#2a1ff5`, violeta `#8a2ff0` y magenta `#f005f8`. Están en `:root` como `--cc-*` y `--asc-*`.

## Regenerar las capturas

El script `scripts/cdp-demo.mjs` automatiza Chrome por CDP, recorre la aplicación real y guarda las capturas. Requiere:

- Stack Docker arriba (`sgdm_web` en `http://127.0.0.1:8080`, `sgdm_db` healthy).
- Chrome instalado en la ruta estándar de Windows o `CHROME_BIN` apuntando al ejecutable.
- `DEMO_PASSWORD` exportada con la contraseña de demostración. El script no la imprime.

```bash
cd C:/xampp/htdocs/SGDM
DEMO_PASSWORD="…" node presentacionWeb/scripts/cdp-demo.mjs
```

El recorrido crea dos jugadores y un equipo demo, los inscribe en el torneo 1217 y aprueba la inscripción con la cuenta organizadora. El estado queda en `scratch/demo-state.json`; para repetir solo el bloque de administración:

```bash
DEMO_RESUME=admin DEMO_PASSWORD="…" node presentacionWeb/scripts/cdp-demo.mjs
```

## Verificación en navegador

El 01/11/2026 se abrió la presentación en Chrome headless (CDP) y se comprobó, con capturas de pantalla de la propia presentación:

- Apertura CharruaCode con la moneda 3D girando, revelación ASCEND y entrada al recorrido.
- Las 35 capturas cargan (`naturalWidth > 0`), el hash y la numeración coinciden del 1 al 35, y las capturas largas activan el paneo automático.
- Visor ampliado, ayuda de atajos, flechas del teclado, modo video, cierre y enlaces directos `#s1`–`#s35`, `#ascend`, `#fin`.
- Sin errores de consola, excepciones ni solicitudes fallidas.
- Sin desborde horizontal a 1440×900, 1280×720, 1920×1080 y 390×844.

La captura `01-home-publica.png` se volvió a tomar recargando el hero completo; la versión anterior había quedado sin la imagen de fondo.

El 01/11/2026 se agregó la navegación por clic universal y después se corrigió el giro de los medallones: se midió el centro proyectado de la cara visible a 15 ángulos (0° a 330°) y se pasó de un desvío de hasta 14 px a 0,00 px en todos los ángulos; también se quitó la flotación vertical de la moneda. El `</>` del reverso se recortó del logo oficial y quedó centrado, y los títulos y botones de las dos aperturas usan los colores muestreados de los logos.

Lo anterior se verificó con renderizados en Chrome headless a 1600×900 y 1366×768, y con una prueba automatizada de clics que cubre: avance desde el centro, retroceso desde el borde izquierdo, clic derecho, clic sobre el botón siguiente (un solo paso), clic en la barra superior (sin efecto), paso de ASCEND a la captura 1, regreso de la captura 1 a ASCEND y comportamiento en el cierre. Se verificó con renderizados en Chrome headless a 1600×900 y 1366×768 y con una prueba automatizada de clics que cubre: avance desde el centro, retroceso desde el borde izquierdo, clic derecho, clic sobre el botón siguiente (un solo paso), clic en la barra superior (sin efecto), paso de ASCEND a la captura 1, regreso de la captura 1 a ASCEND y comportamiento en el cierre. Falta probarlo con el mando físico en el aula.

La verificación es automatizada sobre el DOM y las capturas de la propia página; no sustituye una revisión visual humana en el proyector del aula.

## Nota sobre los datos

Las capturas son de una base de demostración. El seed trae 51 usuarios, 26 torneos, 45 equipos y 97 encuentros; la corrida E2E agrega 2 usuarios, 1 equipo y 1 inscripción. Por eso las pantallas muestran 53 usuarios, 46 equipos y 130 participantes. Las cifras de la presentación están alineadas con esa corrida del 01/11/2026.
