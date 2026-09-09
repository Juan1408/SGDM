# 🚀 Guía de Git y GitHub para SGDM (Inicio de Jornada y Manejo de Ramas)

Esta guía te enseña paso a paso cómo iniciar tu día de trabajo en la terminal, revisar las ramas de tus compañeros en GitHub, traer sus actualizaciones a tu rama local mediante `merge`, resolver conflictos y subir tus cambios.

---

## 🌅 1. Rutina de Inicio de Jornada (Buscar y Descargar Actualizaciones)

### Paso 1: Revisar y asegurar tu estado local
Antes de traer código de otros o cambiar de rama, verifica que tu área de trabajo esté limpia o guardada.
```bash
git status
```
> Si tienes cambios pendientes en tus archivos, agrégalos y haz un commit para no perderlos:
```bash
git add .
git commit -m "docs: guardando avances previos a la sincronización"
```

---

### Paso 2: Consultar las novedades en GitHub (`git fetch`)
Busca en GitHub todas las ramas nuevas y actualizaciones que hayan subido tus compañeros, sin modificar aún tus archivos locales.
```bash
git fetch origin
```
> **💡 ¿Por qué `git fetch` y no `git pull` directo?**  
> `git fetch` es como pedir el diario del día: te descarga la lista y el historial de cambios de GitHub de forma 100% segura sin alterar tu código ni provocar meclas accidentales.

---

### Paso 3: Ver todas las ramas disponibles (`git branch -a`)
Consulta la lista de ramas locales y remotas para ver qué han subido tus compañeros:
```bash
git branch -a
```
Ejemplo de salida en la terminal de **SGDM**:
```text
* feat/backend-mvc                           <-- Tu rama actual (marcada con *)
  main                                       <-- Tu rama local main
  remotes/origin/main                        <-- Rama main oficial en GitHub
  remotes/origin/ramaMicha                   <-- Rama con avances de tu compañero
  remotes/origin/integrar_frontend_a_codigo_fuente
  remotes/origin/valentina0630
```

---

### Paso 4: Descargar e integrar los cambios a tu rama (`git merge`)
Estando parado en tu rama actual (ej. `feat/backend-mvc`), descarga e integra la rama que subió tu compañero en GitHub (ej. `origin/ramaMicha`):

```bash
git merge origin/ramaMicha
```

Si tus compañeros integraron cambios a la rama principal `main` y quieres traerlos a tu rama:
```bash
git merge origin/main
```

> **Nota:** Si no hay conflictos, Git unirá el código automáticamente y te dirá `Fast-forward` o `Merge made by the 'ort' strategy`. ¡Listo! Ya tienes el código de tu compañero en tu rama.

---

### Paso 5: (Opcional) Cambiarte a la rama de un compañero para probarla
Si quieres revisar o probar el código de un compañero en su propia rama antes de traerlo a la tuya:

Para crear y conectarte a la rama remota de tu compañero por primera vez:
```bash
git checkout -b ramaMicha origin/ramaMicha
```

Para volver a tu propia rama cuando termines de revisar:
```bash
git checkout feat/backend-mvc
```

---

## ⚡ 2. Resolución de Conflictos de Merge (Si editaron el mismo archivo)

Si al hacer `git merge` la terminal te avisa `CONFLICT (content): Merge conflict in...`:

1. Corre `git status` para ver la lista de archivos en conflicto (en color rojo).
2. Abre el archivo en **VS Code**. Verás un bloque resaltado como este:
   ```text
   <<<<<<< HEAD (Tu código local)
   $rolId = Sesion::obtenerRolId();
   =======
   $rolId = $_SESSION['usuario']['rol_id'];
   >>>>>>> origin/ramaMicha (Código que vino de tu compañero)
   ```
3. Utiliza los botones que VS Code te muestra arriba (`Accept Current Change`, `Accept Incoming Change` o `Accept Both`) o edita el texto manualmente para dejar la versión correcta.
4. Guarda el archivo en VS Code.
5. Marca el conflicto como resuelto y completa el merge en la terminal:
   ```bash
   git add .
   git commit -m "fix: resolucion de conflictos de merge con origin/ramaMicha"
   ```

---

## 📤 3. Al Finalizar la Jornada (Subir tu trabajo a GitHub)

Cuando hayas terminado tu trabajo y probado que el sistema funciona correctamente:

1. Revisa tus cambios:
   ```bash
   git status
   ```
2. Agrega los archivos modificados:
   ```bash
   git add .
   ```
3. Crea tu paquete de cambios con un mensaje claro:
   ```bash
   git commit -m "feat: modulo de organizadores con flujo de aprobacion completado"
   ```
4. Sube los cambios a tu rama en GitHub:
   ```bash
   git push origin feat/backend-mvc
   ```

---

## 🛡️ 4. Recordatorio del Archivo `.gitignore`

Para no subir por accidente archivos temporales o tus notas personales (`Documentos_Juan/`), asegúrate de que tu archivo `.gitignore` en la raíz del proyecto contenga:
```text
Documentos_Juan/
codigo_fuente/logs/
```

---

## 📋 5. Resumen Rápido / Cheatsheet Diario

| Acción | Comando Terminal |
|---|---|
| **Saber en qué rama estás** | `git status` o `git branch` |
| **Buscar ramas y cambios en GitHub** | `git fetch origin` |
| **Ver todas las ramas (locales y remotas)** | `git branch -a` |
| **Traer rama de compañero a mi rama actual** | `git merge origin/nombre_de_rama` |
| **Crear y pasarme a la rama de un compañero** | `git checkout -b nombre_rama origin/nombre_rama` |
| **Volver a mi rama habitual** | `git checkout feat/backend-mvc` |
| **Guardar mis cambios en local** | `git add .` de seguido `git commit -m "mensaje"` |
| **Subir mi rama a GitHub** | `git push origin feat/backend-mvc` |
