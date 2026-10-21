# Checklist de entrega final — SGDM / ASCEND

**Versión:** 2026-10-21 · **Rama:** `merge_16_10` (`902a2fe`)
**Uso:** marcar cada ítem solo con evidencia ejecutada. Un ítem inferido por lectura no se marca.
**Criterio global:** no se entrega con P0/P1 abiertos. Los P2 no corregidos deben estar documentados como limitación conocida.

> **Nota de actualización (2026-10-21):** este checklist conserva los valores de la auditoría previa a las correcciones. Los conteos de 12 encuentros y el escenario suizo con 5 participantes/bye quedaron desactualizados. La verificación posterior al dry run está en `VERIFICACION_FINAL_DRY_RUN.md`; usar ese documento para el estado final y las limitaciones.

---

## 0. Bloqueantes de entrega (no negociables)

- [ ] **P0-01** La guía de despliegue no reimporta `001` ni destruye la base después de inicializarla.
- [ ] **P0-02** El detalle público de torneo devuelve 200 con contenido (ids 1200, 1201, 1202, 1210).
- [ ] **P0-03** El perfil público de organizador y el perfil/edición del panel devuelven 200 con contenido (ids 2 y 1000).
- [ ] **P0-04** El admin puede abrir y guardar la edición de un juego.
- [ ] **P1-06** Una excepción forzada devuelve HTTP 500 con página de error, no 200 vacío.

Si alguno está sin marcar, **no se presenta**.

---

## 1. Entorno y reproducibilidad

- [ ] Clon limpio en una carpeta nueva (o `docker compose down -v` sobre un volumen de prueba, nunca sobre datos del estudiante).
- [ ] `docker compose config` sin errores.
- [ ] `docker compose up -d` deja `web` y `db` saludables.
- [ ] `docker compose ps` muestra los servicios esperados; Grafana no bloquea el arranque.
- [ ] La app responde en `http://localhost:8080/publico/`.
- [ ] La app responde por IP de la máquina (`http://IP:8080/publico/`) con CSS/JS y enlaces correctos.
- [ ] `php -v` del contenedor es 8.2.x; `php -m` incluye `pdo_mysql`, `mysqli`, `mbstring`, `fileinfo`.
- [ ] Apache tiene `mod_rewrite` activo (si se usan URLs limpias) o se documenta que no se usan.
- [ ] Zona horaria `America/Montevideo` aplicada en PHP.
- [ ] Los logs de Apache/PHP son accesibles para diagnóstico.

---

## 2. Base de datos

### 2.1 Esquema

- [ ] Los 4 scripts se ejecutan sin errores en una base vacía.
- [ ] `SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='ascend';` → **41**.
- [ ] Existe `torneo_actividades.descripcion`.
- [ ] Existen `perfiles_organizadores.banner_url` y `campos_publicos`.
- [ ] Existe `juegos.imagen_url`.
- [ ] `SHOW CREATE TABLE torneo_suizo_parejas` coincide con la PK esperada; si se usa suizo, la PK incluye `ronda` o hay id propio.
- [ ] No hay tablas duplicadas ni definiciones divergentes de `politicas_contrasenas`.
- [ ] La colación es consistente con el motor de la demo (MySQL 8 vs MariaDB 10.4).

### 2.2 Semillas

- [ ] `usuarios` = **51**; `roles` = **4**; `perfiles_organizadores` = **7**; `perfiles_jugadores` = **43**.
- [ ] `juegos` = **5**; `torneos` = **12**; `equipos` = **12**; `participantes_torneo` = **30**.
- [ ] `torneo_encuentros` = **12**; `torneo_posiciones` = **30**; `torneo_actividades` = **36**.
- [ ] Login con `admin@ascend.com` / `Admin123!`, `organizador@ascend.com` / `Admin123!`, `jugador@ascend.com` / `Admin123!`.
- [ ] Los torneos que se van a mostrar tienen posiciones con PJ = G+E+P.
- [ ] Los torneos `en_curso` tienen partidos coherentes (no todos finalizados).
- [ ] Los torneos individuales no tienen participantes de equipo.
- [ ] Los torneos de eliminación tienen 4/8/16 participantes (no 3).
- [ ] La política de contraseña activa es una sola y coincide con la UI.

### 2.3 Migraciones

- [ ] Está documentado que el entrypoint solo ejecuta scripts con volumen vacío.
- [ ] Existe un script/instrucción idempotente para aplicar las 3 columnas a una base existente.
- [ ] Se probó el `ALTER` en una copia, no en la base de la demo.
- [ ] No se ejecutó `DROP`/`down -v` sobre datos del estudiante.

---

## 3. Rutas y navegación

### 3.1 Público

- [ ] Home 200 con contenido y torneos/ranking.
- [ ] Login 200; registro 200; catálogo 200; juegos 200; rankings 200; contacto 200; nosotros 200.
- [ ] Detalle de torneo 200 con actividades y botón de inscripción.
- [ ] Perfil público de jugador 200.
- [ ] Perfil público de organizador 200.
- [ ] Ningún enlace del login produce 404 (recuperación de contraseña resuelta u ocultada).
- [ ] Las imágenes de la demo cargan (`chess2.jpg` o el asset correcto).
- [ ] El CSS de modalidades carga (ruta correcta).

### 3.2 Jugador

- [ ] Dashboard, perfil, editar perfil 200.
- [ ] Crear equipo 200 y POST funcional con `juego_id`.
- [ ] Solicitar unión, aceptar/rechazar invitación y re-solicitar tras rechazo.
- [ ] Gestionar equipo: nombrar capitán solo a miembro; expulsar; dejar equipo.
- [ ] Guardados: guardar/quitar.
- [ ] Inscripción a torneo: una sola vez, cupo lleno rechaza con mensaje.
- [ ] Ver como (admin→jugador) funciona y restaura; el banner aparece.

### 3.3 Organizador

- [ ] Dashboard, mis torneos, resultados, calendario, historial 200.
- [ ] Perfil y editar perfil 200; guardar perfil persiste.
- [ ] Crear torneo 200 y POST funcional.
- [ ] Administrar torneo propio 200; torneo ajeno 302.
- [ ] Agregar/editar/eliminar actividad.
- [ ] Generar fixture de liga con 4 y 8 participantes.
- [ ] Generar eliminación con 4/8.
- [ ] Si se muestra suizo: 4 y 5 participantes (bye) sin colisión.
- [ ] Programar encuentro.
- [ ] Guardar resultado y podio; posiciones coherentes.

### 3.4 Admin

- [ ] Dashboard, usuarios, torneos, equipos, auditoría, errores, resultados globales, historial 200.
- [ ] Juegos: listar, crear, editar (formulario completo), guardar, cambiar estado.
- [ ] Organizadores: bloquear/activar por POST con CSRF; verificación.
- [ ] Jugadores: eliminar con formulario correcto o baja lógica.
- [ ] Usuarios: editar; email duplicado muestra error controlado; borrado con historial no rompe.
- [ ] Torneos: editar/actualizar/eliminar.
- [ ] Políticas de contraseña: guardar.
- [ ] Alertas: index/enviar (si se muestra).
- [ ] Error tracking: dashboard y detalle.

---

## 4. Integridad y errores

- [ ] `errores_sistema` no recibe nuevas filas al recorrer las rutas de demo.
- [ ] No hay `SQLSTATE[42S22]` ni `Unknown column` en los logs.
- [ ] No hay warnings de "array offset on bool" en flujos normales.
- [ ] `php -l` sobre 104 archivos: 0 errores.
- [ ] `node --check` sobre los JS referenciados: 0 errores.
- [ ] PHPUnit: 7/7 (o 6/7 con el test desactualizado marcado como skip y justificado).
- [ ] Los formularios críticos incluyen `csrf_token` y el servidor lo valida.
- [ ] Los POST que fallan muestran mensaje, no pantalla blanca.
- [ ] Las redirecciones por rol no dejan bucles.
- [ ] El modo suplantación bloquea escrituras y permite restaurar.

---

## 5. Despliegue y seguridad básica

- [ ] `GET /configuracion/`, `/modelos/`, `/vistas/`, `/core/` no listan ni sirven archivos.
- [ ] `GET /configuracion/base_de_datos.php` no revela credenciales.
- [ ] `Options -Indexes` efectivo en el docroot.
- [ ] `APP_URL` coincide con la URL de la demo.
- [ ] `DB_PORT` funciona en XAMPP (3306) y en el modo híbrido (3307).
- [ ] Grafana no compite por el 3000 (perfil o 3001).
- [ ] Las imágenes subidas persisten tras reiniciar y son escribibles por Apache.
- [ ] No se exponen `.git`, `vendor`, `tests`, `composer.*` por HTTP.
- [ ] Credenciales de demo no son las de producción; el repo no contiene secretos reales.
- [ ] Los errores no muestran stack trace al usuario final.

---

## 6. Documentación

- [ ] `README.md` describe la estructura actual y el arranque real.
- [ ] `Guia_Despliegue_10_Minutos.md` no tiene pasos destructivos ni contradictorios.
- [ ] `COMANDOS_DOCKER.md` coincide con el compose y el bind mount.
- [ ] Está documentado el modo de demo (Docker vs XAMPP) y sus límites.
- [ ] Está documentada la decisión sobre recuperación de contraseña y suizo si no se corrigieron.
- [ ] Los informes de auditoría están en `informes_auditoria/` y no se sobrescribió documentación previa.
- [ ] El checklist está firmado con fecha y responsable.

---

## 7. Demo (ensayo cronometrado, 10-12 min)

Guion sugerido:

1. **Home pública** (1 min): torneos, ranking, navegación.
2. **Catálogo → detalle de torneo** (1 min): actividades, inscripción visible. *(Antes era pantalla blanca.)*
3. **Perfil público de organizador** (1 min): banner y campos públicos. *(Antes era pantalla blanca.)*
4. **Registro/login de jugador** (1 min) y panel.
5. **Equipo** (2 min): crear, solicitar, aceptar; capitán.
6. **Inscripción a torneo** (1 min).
7. **Organizador** (2 min): crear/administrar torneo, actividad, fixture, resultado.
8. **Admin** (2 min): dashboard, juego (editar), usuarios, auditoría/errores.
9. **Despliegue** (1 min): `docker compose ps` y URL por IP.

- [ ] El guion se ejecutó completo sin errores.
- [ ] Los datos de la demo están precargados y son coherentes.
- [ ] Hay un plan B para cada paso (captura o torneo alternativo) si falla la red.
- [ ] Nadie toca la base durante la demo.
- [ ] Se conoce la respuesta a "¿qué falta?" con las limitaciones documentadas.

---

## 8. Cierre y congelamiento

- [ ] `git status` solo muestra los archivos previstos (informes y cambios autorizados).
- [ ] No se cambiaron de rama ni se descartaron cambios de los estudiantes.
- [ ] No se tocó el volumen `sgdm_datos` ni datos locales.
- [ ] Tag/commit de entrega creado (`entrega-<fecha>`), con mensaje claro.
- [ ] Responsable y fecha de la firma.

---

## 9. Registro de excepciones aceptadas

Completar solo si algo queda sin corregir. **No usar para ocultar P0/P1.**

| ID | Limitación | Motivo | Impacto en demo | Mitigación | Aprobado por |
|----|------------|--------|-----------------|------------|--------------|
| | | | | | |

---

## 10. Firma

| Rol | Nombre | Fecha | Firma |
|-----|--------|-------|-------|
| Responsable técnico | | | |
| QA | | | |
| Docente/aceptación | | | |

**Declaración:** los ítems marcados fueron verificados en ejecución en la fecha indicada. Los no marcados se consideran pendientes o fuera de alcance, y no se presentan como funcionando.
