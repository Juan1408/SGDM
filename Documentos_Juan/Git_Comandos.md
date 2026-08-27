# 🚀 Guía Definitiva de Git para SGDM

Esta guía es tu manual rápido para trabajar en equipo con Git. Te explicará qué comandos usar, cuándo usarlos y cómo evitar subir tus archivos personales (como esta misma carpeta `Documentos_Juan`).

---

## 1. El Flujo de Trabajo Diario (Lo que siempre harás)

Cada vez que te sientes a programar, el orden de los comandos siempre debe ser este:

### A. Al empezar el día (TRAER CAMBIOS)
Antes de escribir una sola línea de código, debes descargar lo que tus compañeros hicieron:
```bash
git pull origin main
```
> **¿Por qué?** Si no lo haces y modificas un archivo que ellos también modificaron, se generará un conflicto. Siempre asegúrate de estar en la última versión.

### B. Durante el día (AGREGAR Y GUARDAR TUS CAMBIOS)
Hiciste cambios, probaste en tu navegador y funciona. ¡Es hora de guardar!

**Paso 1: Seleccionar qué quieres guardar**
Aquí está el truco que me pediste. Si haces `git add .`, vas a subir TODO, incluyendo tu carpeta de apuntes personales. Para elegir qué subir, usa:

Para agregar una carpeta específica (ej. código fuente):
```bash
git add codigo_fuente/
```

Para agregar un archivo específico:
```bash
git add base_de_datos/ascend.sql
```

**Paso 2: Empaquetar los cambios (Commit)**
Una vez que agregaste lo que querías, le pones una etiqueta descriptiva:
```bash
git commit -m "feat: login de jugador y organizador funcionando"
```
> **Tip:** Usa verbos claros en tus commits (`fix` para arreglos, `feat` para cosas nuevas, `docs` para documentación).

### C. Al terminar (ENVIAR CAMBIOS AL EQUIPO)
Ya guardaste tu paquete local, ahora súbelo a GitHub para que los demás lo vean:
```bash
git push origin main
```

---

## 2. El Truco del Archivo `.gitignore` (La forma automática)

Estar seleccionando carpeta por carpeta con `git add` puede ser cansador. La mejor forma de evitar subir tu carpeta `Documentos_Juan` por accidente es ignorándola por completo.

Abre el archivo llamado `.gitignore` (está en la carpeta principal de tu proyecto) y agrega esta línea al final:
```text
Documentos_Juan/
```
Al hacer esto, ¡Git se volverá ciego ante esa carpeta! A partir de ese momento, podrás usar el comando rápido:
```bash
git add .
```
Y Git subirá todo **EXCEPTO** la carpeta `Documentos_Juan`. 

---

## 3. Comandos Salvavidas (Para cuando te equivoques)

**"¿Qué archivos he modificado y qué está listo para el commit?"**
```bash
git status
```
> Úsalo siempre antes de hacer un commit para ver exactamente qué vas a subir (lo verde se sube, lo rojo no).

**"¡Agregué un archivo con `git add` por error y no quiero subirlo!"**
```bash
git restore --staged nombre_del_archivo
```

**"Quiero borrar todos los cambios que hice hoy y volver a como estaba mi último commit (CUIDADO, esto borra tu trabajo)"**
```bash
git restore .
```

---

## 4. Resumen Visual del Flujo

1. `git pull origin main` (Empiezas tu turno)
2. *...escribes código, el sistema funciona...*
3. `git status` (Revisas qué modificaste)
4. `git add codigo_fuente/` (Preparas los archivos de código)
5. `git commit -m "Se agregó X cosa"` (Empaquetas)
6. `git push origin main` (Subes a la nube)
