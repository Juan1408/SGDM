# Matriz de rutas y funcionalidades — SGDM / ASCEND

**Fecha:** 2026-10-21 · **Rama:** `merge_16_10` (`902a2fe`)
**Fuente de rutas:** whitelist de `codigo_fuente/publico/index.php` (líneas 38-61) contrastada con los métodos públicos de los 23 controladores.

---

## Leyenda

| Símbolo | Significado |
|---------|-------------|
| ✅ | Verificado en ejecución (HTTP 200 con contenido, o flujo completo) |
| ⚠️ | Verificado con defecto parcial (renderiza pero una acción falla, o acción no ejecutada con defecto estático) |
| ❌ | Roto y reproducido |
| ⬜ | No ejecutado; estado inferido por inspección |
| 🔒 | Requiere sesión/rol; sin sesión redirige al login |
| 🌐 | Público |

**Método:** `GET`, `POST` o `GET/POST` según el controlador. Muchas acciones GET de panel aceptan POST sin efecto; se indica el método esperado por los formularios.

**Total de entradas en whitelist:** 98. **Con método existente:** 96. **Rutas muertas:** 2.

---

## 1. Matriz completa por controlador

### `auth` — autenticación (público)

| Acción | Método | Rol | Vista / destino | Estado | Nota |
|--------|--------|-----|-----------------|--------|------|
| `mostrarLogin` | GET | 🌐 | `auth/login.php` | ✅ 200 / 2.734 B | Ruta por defecto |
| `procesarLogin` | POST | 🌐 | 302 al panel por rol | ✅ | Admin→admin, organizador→panelOrganizador, jugador→panelJugador; rate limit por IP |
| `mostrarRegistro` | GET | 🌐 | `auth/registro.php` | ✅ 200 / 3.947 B | |
| `procesarRegistro` | POST | 🌐 | 302 login | ⬜ | CSRF y validaciones presentes; no se creó un usuario nuevo en la auditoría |
| `verificarEmail` | GET | 🌐 | Mensaje | ⬜ | No hay envío de correo real; verificar alcance |
| `logout` | GET | 🔒 | 302 login | ✅ | Destruye sesión |

**Enlace roto asociado:** `recuperar-password.html` → **404**. No existe ruta ni archivo.

---

### `inicio` — páginas públicas

| Acción | Método | Rol | Vista | Estado | Nota |
|--------|--------|-----|-------|--------|------|
| `index` | GET | 🌐 | `inicio/index.php` | ✅ 200 / 25.278 B | Home con torneos y ranking |
| `enConstruccion` | GET | 🌐 | `inicio/en-construccion.php` | ✅ 200 | |
| `contacto` | GET | 🌐 | `inicio/contacto.php` | ✅ 200 | |
| `juegos` | GET | 🌐 | `inicio/juegos.php` | ✅ 200 / 12.434 B | Catálogo de juegos desde BD |
| `nosotros` | GET | 🌐 | `inicio/nosotros.php` | ✅ 200 | |
| `rankings` | GET | 🌐 | `inicio/rankings.php` | ✅ 200 / 13.870 B | Ranking por disciplina |
| `perfilPublicoOrganizador` | GET | 🌐 | — | ❌ **muerta** | No existe método; responde 200 con texto "Error 404" |

---

### `torneo` — torneos públicos

| Acción | Método | Rol | Vista | Estado | Nota |
|--------|--------|-----|-------|--------|------|
| `catalogoPublico` | GET | 🌐 | `torneo/catalogo.php` | ✅ 200 / 27.250 B | Filtros y tarjetas |
| `detalle` | GET | 🌐 | `torneo/detalle-torneo.php` | ❌ **P0-02** | 200 / **0 bytes** por `a.descripcion` inexistente (`Torneo.php:121`) |
| `inscribirse` | POST | 🔒 jugador | Redirect al detalle | ⬜ | CSRF; validaciones de cupo/estado en modelo; no ejecutada |
| `detalle` sin `id` | GET | 🌐 | 302 catálogo | ✅ | Degradación correcta |

**Enlaces/recursos:** `detalle-torneo.php:891` referencia `img/chess.jpg`, que no existe (`chess2.jpg` sí).

---

### `perfil` — perfiles públicos

| Acción | Método | Rol | Vista | Estado | Nota |
|--------|--------|-----|-------|--------|------|
| `jugador` | GET | 🌐 | `perfil/jugador.php` | ✅ 200 / 7.451 B | `?id=1100` verificado |
| `organizador` | GET | 🌐 | `perfil/organizador.php` | ❌ **P0-03** | 200 / **0 bytes** por `p.banner_url` (`Organizador.php:54`); ids 2 y 1000 |

---

### `panelJugador` — panel del jugador

| Acción | Método | Rol | Vista | Estado | Nota |
|--------|--------|-----|-------|--------|------|
| `dashboard` | GET | 🔒 jugador | `jugador/dashboard.php` | ✅ | |
| `perfil` | GET | 🔒 jugador | `jugador/perfil.php` | ✅ | |
| `editarPerfil` | GET | 🔒 jugador | `jugador/editar-perfil.php` | ✅ | |
| `actualizarPerfil` | POST | 🔒 jugador | 302 perfil | ⬜ | CSRF; no verifica resultado del UPDATE (P2-09) |

---

### `equipo` — equipos (jugador)

| Acción | Método | Rol | Vista / efecto | Estado | Nota |
|--------|--------|-----|----------------|--------|------|
| `index` | GET | 🔒 jugador | `equipo/index.php` | ✅ | Lista equipo/solicitudes |
| `crear` | GET | 🔒 jugador | `equipo/crear.php` | ✅ | |
| `procesarCreacion` | POST | 🔒 jugador | 302 index | ⚠️ | INSERT sin `juego_id` (P2-10) |
| `verPerfil` | GET | 🔒 jugador | `equipo/perfil.php` | ✅ 200 | `?id=1100` |
| `gestionar` | GET | 🔒 jugador | `equipo/gestionar.php` | ✅ 200 | Solo capitán (validación en controlador/modelo) |
| `solicitarUnion` | POST | 🔒 jugador | 302 | ⬜ | UNIQUE impide re-solicitar tras rechazo (P2-11) |
| `solicitudes` | GET | 🔒 jugador | `equipo/solicitudes.php` | ✅ | |
| `responderInvitacion` | POST | 🔒 jugador | 302 | ⬜ | Sin validación de destinatario (P2-12) |
| `responderSolicitudRecibida` | POST | 🔒 jugador | 302 | ⬜ | Sin validación de pertenencia (P2-12) |
| `invitarJugador` | POST | 🔒 jugador | 302 | ⬜ | |
| `actualizarEquipo` | POST | 🔒 jugador | 302 | ⬜ | |
| `expulsarMiembro` | POST | 🔒 jugador | 302 | ⬜ | |
| `buscarJugadoresAjax` | GET | 🔒 jugador | JSON | ⬜ | |
| `hacerCapitan` | POST | 🔒 jugador | 302 | ⚠️ | No valida que el destino sea miembro (P2-13) |
| `dejarEquipo` | POST | 🔒 jugador | 302 | ⬜ | |

---

### `guardados` — torneos guardados (jugador)

| Acción | Método | Rol | Estado | Nota |
|--------|--------|-----|--------|------|
| `index` | GET | 🔒 jugador | ✅ | |
| `guardar` | POST | 🔒 jugador | ⬜ | CSRF |
| `quitar` | POST | 🔒 jugador | ⬜ | CSRF |

---

### `panelOrganizador` — panel del organizador

| Acción | Método | Rol | Vista | Estado | Nota |
|--------|--------|-----|-------|--------|------|
| `dashboard` | GET | 🔒 organizador | `organizador/dashboard.php` | ✅ | |
| `crear` | GET | 🔒 organizador verificado | `organizador/crear-torneo.php` | ✅ | |
| `misTorneos` | GET | 🔒 organizador | `organizador/mis-torneos.php` | ✅ | |
| `resultados` | GET | 🔒 organizador | `organizador/resultados.php` | ✅ | |
| `calendario` | GET | 🔒 organizador | `organizador/calendario.php` | ✅ | |
| `historial` | GET | 🔒 organizador | `organizador/historial.php` | ✅ | Cuenta `aprobado` inexistente (P2-14) |
| `perfil` | GET | 🔒 organizador | `organizador/perfil.php` | ❌ **P0-03** | 200 / 0 bytes |
| `editarPerfil` | GET | 🔒 organizador | `organizador/editar-perfil.php` | ❌ **P0-03** | 200 / 0 bytes |
| `guardarPerfil` | POST | 🔒 organizador | 302 perfil | ❌ | UPDATE a columnas inexistentes; fallaría aunque la vista cargara |
| `administrar` | GET | 🔒 organizador dueño | `organizador/administrar-torneo.php` | ✅ | 200 para 1200/1206 con organizador 1000; 302 si no es dueño |
| `cambiarEstado` | POST | 🔒 organizador | 302 | ⬜ | |
| `responderInscripcion` | POST | 🔒 organizador | 302 | ⬜ | |
| `quitarParticipante` | POST | 🔒 organizador | 302 | ⬜ | |
| `generarFixture` | POST | 🔒 organizador | 302 | ⚠️ | Suizo con PK sin ronda (P2-16) e impar sin bye (P2-17) |
| `programarEncuentro` | POST | 🔒 organizador | 302 | ⬜ | |
| `agregarActividad` | POST | 🔒 organizador | 302 | ⬜ | Escribe `descripcion`? Verificar tras P0-02 |
| `editarActividad` | POST | 🔒 organizador | 302 | ❌ | Formulario con `action` truncado (`administrar-torneo.php:518-519`) |
| `eliminarActividad` | POST | 🔒 organizador | 302 | ⬜ | |
| `editarTorneo` | GET | 🔒 organizador | Vista de edición | ⬜ | |
| `guardarResultado` | POST | 🔒 organizador | 302 | ⚠️ | Podio fuera de transacción (P2-18) |
| `guardarEtapa` | POST | 🔒 organizador | 302 | ⚠️ | Es el POST real de creación por etapas. La etapa 3 falla por `descripcion` ausente (`Torneo::guardarActividades`, P0-02) |

**Rutas de creación/edición:** `crear` es solo la vista GET por etapas; el POST real de creación se envía a `guardarEtapa`. En la auditoría se verificó la vista GET; la creación efectiva no se ejecutó y se sabe que la etapa 3 falla por P0-02.

---

### `admin` — panel administrador

| Acción | Método | Rol | Vista | Estado | Nota |
|--------|--------|-----|-------|--------|------|
| `dashboard` | GET | 🔒 admin | `admin/dashboard.php` | ✅ | |
| `editarPerfil` | GET/POST | 🔒 admin | `admin/editar-perfil.php` | ✅ | GET verificado; POST no ejecutado |

---

### `usuario` — gestión de usuarios (admin)

| Acción | Método | Rol | Estado | Nota |
|--------|--------|-----|--------|------|
| `index` | GET | 🔒 admin | ✅ | Listado |
| `cambiarEstadoUsuario` | POST | 🔒 admin | ⚠️ | Funciona por POST; el enlace de organizadores usa GET → 405 (P2-01) |
| `editarUsuario` | GET | 🔒 admin | ✅ | |
| `verComo` | POST | 🔒 admin | ✅ | Camino feliz verificado; respaldo se sobrescribe y warning con id inválido (P2-19) |
| `restaurarSesion` | GET | 🔒 login | ✅ | Restaura admin desde suplantación |
| `actualizarUsuario` | POST | 🔒 admin | ⬜ | No actualiza `telefono` (P2-20 relacionado) |
| `eliminarUsuario` | POST | 🔒 admin | ⚠️ | Borrado físico con FK `RESTRICT` (P2-07); formulario de jugador corrupto (P2-02) |

---

### `jugador` / `organizador` — listados admin

| Ruta | Método | Rol | Estado | Nota |
|------|--------|-----|--------|------|
| `jugador&a=index` | GET | 🔒 admin | ✅ | Vista con formulario de borrado corrupto (P2-02) |
| `organizador&a=index` | GET | 🔒 admin | ✅ | Enlace de bloqueo GET (P2-01) |
| `organizador&a=cambiarVerificacion` | — | — | ❌ **muerta** | No existe método; 200 con "Error 404" |
| `organizador&a=responderSolicitud` | POST | 🔒 admin | ⬜ | |

---

### `juego` — catálogo de juegos (admin)

| Acción | Método | Rol | Vista | Estado | Nota |
|--------|--------|-----|-------|--------|------|
| `index` | GET | 🔒 admin | `admin/juegos/juegos_index.php` | ✅ | |
| `crear` | GET | 🔒 admin | `admin/juegos/juegos_crear.php` | ✅ | |
| `guardar` | POST | 🔒 admin | 302 index | ⬜ | Upload de carátula no probado |
| `cambiarEstado` | POST | 🔒 admin | 302 | ⬜ | |
| `editar` | GET | 🔒 admin | `admin/juegos/juegos_editar.php` | ❌ **P0-04** | 200 sin formulario; parse error línea 27 |
| `actualizar` | POST | 🔒 admin | 302 index | ❌ | Depende de la vista rota; no ejecutable desde UI |

---

### `torneoAdmin` — administración de torneos

| Acción | Método | Rol | Estado | Nota |
|--------|--------|-----|--------|------|
| `index` | GET | 🔒 admin | ✅ | |
| `editar` | GET | 🔒 admin | ✅ | |
| `actualizar` | POST | 🔒 admin | ⬜ | |
| `eliminar` | POST | 🔒 admin | ⬜ | |

---

### `equipoAdmin`, `auditoria`, `resultadoGlobal`, `torneoHistorial`

| Ruta | Método | Rol | Estado |
|------|--------|-----|--------|
| `equipoAdmin&a=index` | GET | 🔒 admin | ✅ |
| `auditoria&a=index` | GET | 🔒 admin | ✅ |
| `resultadoGlobal&a=index` | GET | 🔒 admin | ✅ |
| `torneoHistorial&a=index` | GET | 🔒 admin | ✅ |

---

### `errorTracking` — monitoreo de errores

| Acción | Método | Rol | Estado | Nota |
|--------|--------|-----|--------|------|
| `dashboard` | GET | 🔒 admin | ✅ | |
| `verDetalle` | GET | 🔒 admin | ⬜ | |
| `accionError` | POST | 🔒 admin | ⬜ | CSRF |

---

### `formato` — formatos de torneo

| Acción | Método | Rol | Estado | Nota |
|--------|--------|-----|--------|------|
| `index` | GET | 🔒 admin | ✅ | |
| `guardarEstados` | POST | 🔒 admin | ⬜ | CSRF |
| `obtenerPermitidasJSON` | GET | 🔒 login | ⬜ | JSON |

---

### `politicaContrasena` — políticas

| Acción | Método | Rol | Estado | Nota |
|--------|--------|-----|--------|------|
| `index` | GET | 🔒 admin | ✅ | |
| `guardar` | POST | 🔒 admin | ⬜ | CSRF; tabla duplicada en 001 (P3) |

---

### `alerta` — alertas

| Acción | Método | Rol | Estado | Nota |
|--------|--------|-----|--------|------|
| `index` | GET | 🔒 admin | ⬜ | No recorrida |
| `enviar` | POST | 🔒 admin | ⬜ | CSRF; no hay envío real de correo |

---

## 2. Resumen por estado

| Estado | Cantidad | Rutas |
|--------|----------|-------|
| ✅ Verificadas OK | La mayoría de las rutas GET | Públicas principales, paneles de los tres roles, listados admin (detalle por fila en las tablas) |
| ❌ Rotas reproducidas | 5 | `torneo&a=detalle`, `perfil&a=organizador`, `panelOrganizador&a=perfil`, `panelOrganizador&a=editarPerfil` (misma causa P0-03), `juego&a=editar` |
| ❌ Rotas estáticas | 4 | `organizador&a=cambiarVerificacion`, `inicio&a=perfilPublicoOrganizador`, `jugador` eliminar (formulario), `editarActividad` (formulario) |
| ⚠️ Parciales | 4 | `usuario&a=eliminarUsuario`, `equipo&a=procesarCreacion`, `equipo&a=hacerCapitan`, `panelOrganizador&a=guardarEtapa` |
| ⬜ No ejecutadas | 39 | POSTs de escritura y vistas secundarias; requieren datos controlados o no eran necesarias para el veredicto |

> Los POST no ejecutados no se consideran "funcionando". Se marcan como deuda de regresión en el Día 4/5 del plan.

---

## 3. Flujos de negocio verificados o inferidos

### 3.1 Registro e inicio de sesión

| Paso | Estado | Evidencia / brecha |
|------|--------|--------------------|
| Registro de jugador | ⬜ | Vista 200; POST no ejecutado. Valida CSRF, email único, política de contraseña |
| Registro de organizador | ⬜ | Mismo formulario con rol; verificar que el rol no sea manipulable |
| Login admin | ✅ | `admin@ascend.com` / `Admin123!` → panel admin |
| Login organizador | ✅ | `organizador@ascend.com` / `Admin123!` → panel organizador |
| Login jugador | ✅ | `jugador@ascend.com` / `Admin123!` → panel jugador |
| Rate limiting | ⬜ | Tabla `intentos_login`; no se provocaron intentos fallidos masivos |
| Logout | ✅ | Destruye sesión y redirige |
| Recuperación de contraseña | ❌ | Enlace a archivo inexistente |

### 3.2 Inscripción a torneo (jugador)

1. Catálogo → detalle. **Detalle roto (P0-02)**: el jugador no puede llegar al botón de inscripción desde la UI.
2. `torneo&a=inscribirse` (POST, CSRF). No ejecutado.
3. Validaciones de estado/cupo/modalidad en `Torneo`/`Equipo`. No verificadas en concurrencia.

**Conclusión:** el flujo visible está bloqueado por P0-02 aunque el endpoint POST exista.

### 3.3 Equipos (jugador)

1. Crear equipo: vista OK; el INSERT omite `juego_id` (P2-10).
2. Solicitar unión: POST; el UNIQUE impide re-solicitar tras rechazo (P2-11).
3. Invitar/responder: POST; sin validación de destinatario (P2-12).
4. Gestionar: vista OK; nombrar capitán sin validar membresía (P2-13).
5. Expulsar/dejar equipo: POST; no ejecutados.

### 3.4 Organizador

1. Crear torneo: vista OK; POST no ejecutado.
2. Administrar torneo: 200 para el dueño; 302 para terceros (control correcto).
3. Agregar actividad: POST; la tabla no tiene `descripcion`, pero el formulario puede no enviarla. Verificar tras P0-02.
4. Editar actividad: formulario truncado (P2-03).
5. Generar fixture: liga/eliminación/suizo; riesgos P2-16/17 en suizo.
6. Programar encuentro: POST no ejecutado.
7. Guardar resultado/podio: POST no ejecutado; podio fuera de transacción (P2-18).
8. Perfil del organizador: **roto (P0-03)**.

### 3.5 Administrador

1. Dashboard y listados: OK.
2. Juegos: listar/crear OK; **editar roto (P0-04)**.
3. Usuarios: listar OK; bloquear por POST funciona, pero el enlace de organizadores es GET → 405 (P2-01); eliminar jugador tiene formulario corrupto (P2-02).
4. Torneos: listar/editar OK; actualizar/eliminar no ejecutados.
5. Auditoría, errores, políticas, resultados globales, historial: OK.
6. "Ver como": camino feliz OK; respaldo/limpieza con defecto latente (P2-19).

---

## 4. Enlaces y recursos rotos

| Ubicación | Referencia | Resultado |
|-----------|------------|-----------|
| Login | `recuperar-password.html` | 404 (no existe) |
| `vistas/admin/modalidades/index.php:10` | `URL_BASE . 'publico/css/admin_modalidades.css'` | 404; el CSS real está en `SGDM/publico/css/` |
| `detalle-torneo.php:891` | `img/chess.jpg` | 404; existe `chess2.jpg` |
| Menú admin / footer | 10 `href="#"` | Placeholders; no rompen navegación |
| Whitelist | 2 rutas muertas | 200 con texto de error, sin enlaces entrantes |

---

## 5. Observaciones de navegación

- **No hay URLs limpias.** Todo passa por `index.php?c=...&a=...`. El `.htaccess` de la raíz apunta a un `index.php` inexistente; el de `publico/` no existe; en Docker `AllowOverride None`.
- **Redirecciones por rol:** un jugador que intenta `admin&a=dashboard` es redirigido al login (comportamiento correcto). `jugador&a=index` desde jugador redirige (es admin-only).
- **Suplantación:** al activar "Ver como", el front controller inyecta un banner y un script, y bloquea POST salvo `restaurarSesion`. Funciona, pero el respaldo puede sobrescribirse si se suplanta dos veces sin restaurar.
- **404 inconsistente:** fuera de whitelist → HTTP 404; acción inexistente dentro de whitelist → HTTP 200 con HTML de error. Esto confunde monitoreo y pruebas automatizadas.

---

## 6. Cobertura de la matriz

- **Rutas visitadas con sesión real:** más de 50 (detalle por fila).
- **Rutas POST o secundarias no ejecutadas:** 39 (marcadas ⬜).
- **Rutas muertas:** 2.
- **Defectos reproducidos:** 5 rutas (4 causas raíz).
- **Defectos estáticos en rutas existentes:** 4.

La matriz se debe actualizar después de cada corrección del plan; el Día 5 exige volver a recorrer todas las rutas ✅ más los POST críticos.
