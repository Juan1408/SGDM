# 🎨 Paso a Paso: Guía Definitiva para Dibujar el MER Conceptual de "ASCEND" (Notación Peter Chen)

> **Propósito del Documento:**  
> Esta es la guía paso a paso oficial y verificada para que puedas construir el **Modelo Entidad-Relación (MER/DER) Conceptual** de tu sistema **ASCEND** en plataformas como Draw.io, Lucidchart o StarUML.  
> 
> Esta guía toma como base real las **35 tablas de tu base de datos SQL (`ascend.sql`)** y las reglas de negocio del sistema, corrigiendo las inconsistencias conceptuales comunes y enseñándote a representar cada elemento en la notación exigida por la materia (**Modelo de Peter Chen**).

---

## 📐 Simbología Oficial de Peter Chen (Referencia Rápida)

| Elemento Visual | Figura Geométrica | Uso en el Diagrama | Ejemplo en ASCEND |
| :--- | :---: | :--- | :--- |
| **Entidad Fuerte** | Rectángulo Simple | Concepto con existencia propia e independiente. | `Usuario`, `Torneo`, `Equipo`, `Juego` |
| **Entidad Débil** | Rectángulo Doble | Depende completamente de otra entidad para existir. | `Sesion Activa`, `Encuentro`, `Notificacion` |
| **Subclase / Herencia** | Triángulo con **"ES"** | Especialización o jerarquía de entidades (1:1). | `Usuario` ➔ **ES** ➔ `Perfil Jugador` / `Perfil Organizador` |
| **Relación** | Rombo Simple | Asociación entre 2 o más entidades (1:1, 1:N, N:M). | `Perfil Organizador` **organiza** `Torneo` |
| **Relación Identificadora**| Rombo Doble | Relación que une una Entidad Débil con su Entidad Padre. | `Usuario` **inicia** `Sesion Activa` |
| **Atributo** | Óvalo | Propiedad de una entidad o de una relación. | `nombre`, `fecha_creacion`, `email` |
| **Clave Primaria (PK)** | Óvalo Subrayado | Identificador único inequívoco de la entidad. | <u>`id`</u>, <u>`usuario_id`</u> |

---

## 🗺️ Mapa General: Clasificación Conceptual de las 35 Tablas SQL

En una base de datos relacional existen 35 tablas físicas (`CREATE TABLE`), pero **en el Modelo Conceptual (MER) de Peter Chen NO todas son rectángulos**. Algunas representan entidades, otras son subclases, otras son entidades débiles y otras son **Rombos de Relación N:M**.

A continuación se muestra el mapeo exacto de las 35 tablas:

| # | Tabla SQL (`ascend.sql`) | Tipo Conceptual en MER | Representación en Diagrama |
| :-: | :--- | :--- | :--- |
| 1 | `usuarios` | **Entidad Fuerte (Superclase)** | Rectángulo `Usuario` (Padre) |
| 2 | `perfiles_jugadores` | **Subclase (Especialización)** | Rectángulo `Perfil Jugador` (Hijo vía Triángulo **ES**) |
| 3 | `perfiles_organizadores` | **Subclase (Especialización)** | Rectángulo `Perfil Organizador` (Hijo vía Triángulo **ES**) |
| 4 | `roles` | **Entidad Fuerte** | Rectángulo `Rol` |
| 5 | `permisos` | **Entidad Fuerte** | Rectángulo `Permiso` |
| 6 | `juegos` | **Entidad Fuerte** | Rectángulo `Juego` |
| 7 | `modalidades` | **Entidad Fuerte** | Rectángulo `Modalidad` |
| 8 | `sistemas_puntuacion` | **Entidad Fuerte** | Rectángulo `Sistema Puntuacion` |
| 9 | `equipos` | **Entidad Fuerte** | Rectángulo `Equipo` |
| 10 | `torneos` | **Entidad Fuerte** | Rectángulo `Torneo` |
| 11 | `logros` | **Entidad Fuerte** | Rectángulo `Logro` |
| 12 | `newsletter_subscriptores` | **Entidad Fuerte** | Rectángulo `Subscriptor Newsletter` |
| 13 | `politicas_contrasenas` | **Entidad Fuerte / Config** | Rectángulo `Politica Contraseña` |
| 14 | `tokens_verificacion_email` | **Entidad Débil** | Rectángulo Doble `Token Email` (Débil de `Usuario`) |
| 15 | `tokens_recuperacion` | **Entidad Débil** | Rectángulo Doble `Token Recuperacion` (Débil de `Usuario`) |
| 16 | `sesiones_activas` | **Entidad Débil** | Rectángulo Doble `Sesion Activa` (Débil de `Usuario`) |
| 17 | `notificaciones` | **Entidad Débil** | Rectángulo Doble `Notificacion` (Débil de `Usuario`) |
| 18 | `torneo_config` | **Entidad Débil** | Rectángulo Doble `Torneo Config` (Débil de `Torneo`) |
| 19 | `torneo_encuentros` | **Entidad Débil** | Rectángulo Doble `Encuentro` (Débil de `Torneo`) |
| 20 | `torneo_posiciones` | **Entidad Débil** | Rectángulo Doble `Posicion Torneo` (Débil de `Torneo`) |
| 21 | `rol_permisos` | **Relación N:M** | Rombo **posee** (entre `Rol` y `Permiso`) |
| 22 | `equipo_miembros` | **Relación N:M** | Rombo **pertenece a** (entre `Perfil Jugador` y `Equipo`) con atributos |
| 23 | `equipo_capitanes` | **Relación 1:N / N:M** | Rombo **lidera / capitanea** (entre `Perfil Jugador` y `Equipo`) |
| 24 | `solicitudes_equipo` | **Relación N:M** | Rombo **solicita unirse** (entre `Perfil Jugador` y `Equipo`) |
| 25 | `participantes_torneo` | **Relación N:M** | Rombo **participa en** (entre `Perfil Jugador`/`Equipo` y `Torneo`) |
| 26 | `torneo_cambio_estado` | **Relación / Historial** | Rombo **cambia estado** (entre `Usuario` y `Torneo`) |
| 27 | `torneo_suizo_parejas` | **Relación N:M / Historial**| Rombo **emparejado en suizo** (entre `Participante` y `Torneo`) |
| 28 | `resultados_detalle` | **Atributos de Relación** | Óvalos o Rombo **registra detalle** en `Encuentro` |
| 29 | `torneo_actividades` | **Entidad Débil / Relación** | Rectángulo Doble o Rombo **programa actividad** en `Torneo` |
| 30 | `torneo_comentarios` | **Entidad Débil / Relación** | Rectángulo Doble **comenta en** (entre `Usuario` y `Torneo`) |
| 31 | `usuario_logros` | **Relación N:M** | Rombo **desbloquea** (entre `Perfil Jugador` y `Logro`) |
| 32 | `logs_acceso` | **Log Auditoría (Relación)** | Rombo / Entidad Débil **registra acceso** en `Usuario` |
| 33 | `logs_actividad` | **Log Auditoría (Relación)** | Rombo / Entidad Débil **registra actividad** en `Usuario` |
| 34 | `auditoria_cambios` | **Log Forense (Relación)** | Rombo / Entidad Débil **audita cambios** en `Usuario` |
| 35 | `historial_contrasenas` | **Entidad Débil** | Rectángulo Doble **historial clave** (Débil de `Usuario`) |

---

## 🛠️ Paso a Paso Guiado para Construir el Diagrama

---

### 📌 Paso 1: Dibuja la Jerarquía de Usuarios y la Herencia ("ES")

> **Aclaración de Dominio (Respuesta a tus dudas):**  
> `Usuario` es la entidad Padre (Superclase) que guarda los datos de inicio de sesión (`email`, `contrasena_hash`, `nombre_completo`).  
> Sus hijas directas son `Perfil Jugador` y `Perfil Organizador`.  
> 
> **¿Y el Administrador?**  
> - **En SQL (`ascend.sql`):** El Administrador es un registro dentro de `usuarios` que tiene `rol_id` apuntando al Rol "Administrador General" (Nivel 100). No necesita una tabla de perfil propia porque no tiene gamertags ni datos de organización.  
> - **En el MER Conceptual (Peter Chen):** Tienes dos formas válidas de dibujarlo:  
>   1. **Opción A (Recomendada para la materia):** Dibujar `Administrador` como una 3ª subclase hija bajo el triángulo **ES** (para mostrar que el sistema tiene 3 tipos de usuarios conceptuales).  
>   2. **Opción B (Estricta a la BD):** Conectar `Usuario` mediante un rombo **tiene** al `Rol`, y especificar que si `Rol` = "Administrador", este gestiona la auditoría y catálogos globales.

#### Instrucciones de Dibujo:
1. Dibuja un **Rectángulo Simple** en la parte superior central llamado **`Usuario`**.
   - Conéctale los óvalos: <u>`id`</u> (Subrayado), `email`, `contrasena_hash`, `nombre_completo`, `telefono`.
2. Debajo de `Usuario`, dibuja un **Triángulo** con la palabra **`ES`** adentro.
3. Desde la base del triángulo **`ES`**, saca líneas hacia dos (o tres) **Rectángulos Simples**:
   - **`Perfil Jugador`** (Subclase 1:1)
     - Atributos (Óvalos): `apodo_gamertag`, `bio`, `nivel`, `puntos_experiencia`, `pais`, `discord_tag`.
   - **`Perfil Organizador`** (Subclase 1:1)
     - Atributos (Óvalos): `nombre_organizacion`, `bio_organizacion`, `sitio_web`, `telefono_contacto`, `verificado_oficial`.
   - **`Administrador`** *(Opción A - Subclase 1:1 Conceptual)*
     - Atributos (Óvalos): `nivel_acceso`, `codigo_seguridad`.

---

### 📌 Paso 2: Corrección de Relaciones de Dominio (Organizador vs Jugador)

> **Inconsistencia Corregida:**  
> - **Incorrecto:** `Usuario` (genérico) crea Torneos y `Usuario` crea Equipos.  
> - **CORRECTO (Regla de Negocio ASCEND):**  
>   - **`Perfil Organizador`** es quien **organiza** los Torneos (`organizador_id`).  
>   - **`Perfil Jugador`** (en función de Capitán) es quien **crea** y **lidera** los Equipos (`creado_por`).  
>   - **`Perfil Jugador`** es quien **pertenece** como miembro a los Equipos.

#### Instrucciones de Dibujo:

#### 2.1 Organización de Torneos:
1. Dibuja el rectángulo **`Torneo`**.
2. Dibuja un **Rombo** entre **`Perfil Organizador`** y **`Torneo`** llamado **`organiza`**.
3. **Líneas y Cardinalidades:**
   - De `Perfil Organizador` al rombo `organiza`: Escribe **`1`** (Un organizador crea N torneos).
   - Del rombo `organiza` a `Torneo`: Escribe **`N`** (Un torneo es organizado por 1 organizador).

#### 2.2 Creación y Capitanía de Equipos:
1. Dibuja el rectángulo **`Equipo`**.
2. Dibuja un **Rombo** entre **`Perfil Jugador`** y **`Equipo`** llamado **`crea / capitanea`**.
3. **Líneas y Cardinalidades:**
   - De `Perfil Jugador` al rombo: Escribe **`1`**.
   - Del rombo a `Equipo`: Escribe **`N`**. *(Un jugador capitán puede crear N equipos, cada equipo tiene 1 creador/capitán principal)*.

#### 2.3 Membresía de Jugadores en Equipos (`equipo_miembros`):
1. Dibuja un **Rombo** entre **`Perfil Jugador`** y **`Equipo`** llamado **`pertenece a`**.
2. **Líneas y Cardinalidades:**
   - De `Perfil Jugador` al rombo: Escribe **`N`** (o `M`).
   - Del rombo a `Equipo`: Escribe **`N`**.
   - *(Lectura: Un Jugador pertenece a N Equipos; Un Equipo tiene N Jugadores).*
3. **¡Atención! Atributos de la Relación:**  
   La tabla intermedia `equipo_miembros` tiene datos propios. Saca **Óvalos** conectados **directamente al Rombo `pertenece a`**:
   - Óvalo: `fecha_union`
   - Óvalo: `posicion`
   - Óvalo: `numero_camiseta`
   - Óvalo: `es_activo`

#### 2.4 Inscripción a Torneos (`participantes_torneo`):
1. Dibuja un **Rombo** entre **`Perfil Jugador`** / **`Equipo`** y **`Torneo`** llamado **`participa en`**.
2. **Cardinalidad:** **`N:M`** (Muchos a Muchos).
3. **Atributos conectados al Rombo:** `fecha_inscripcion`, `estado` (*confirmado, pendiente*), `sembrado_seed`.

---

### 📌 Paso 3: Entidades de Soporte y Catálogos del Sistema

Agrega las entidades de catálogo independientes (Rectángulos Simples):

1. **`Juego`** (Ajedrez, LOL, Fútbol 5):
   - Atributos: <u>`id`</u>, `nombre`, `categoria`, `formato_equipo_defecto`.
   - Relación con `Torneo`: Rombo **`se juega en`** (Juego `1` ➔ Rombo ➔ `N` Torneo).

2. **`Modalidad`** (1v1, Equipos 5v5):
   - Atributos: <u>`id`</u>, `nombre`, `descripcion`.
   - Relación con `Torneo`: Rombo **`define formato de`** (Modalidad `1` ➔ Rombo ➔ `N` Torneo).

3. **`Sistema Puntuacion`** (3 pts victoria, 1 emp):
   - Atributos: <u>`id`</u>, `nombre`, `puntos_victoria`, `puntos_empate`, `puntos_derrota`.
   - Relación con `Torneo`: Rombo **`rige en`** (Sistema `1` ➔ Rombo ➔ `N` Torneo).

4. **`Rol`** y **`Permiso`**:
   - Dibuja Rectángulo **`Rol`** (<u>`id`</u>, `nombre_rol`, `nivel_permiso`).
   - Dibuja Rectángulo **`Permiso`** (<u>`id`</u>, `nombre_permiso`, `descripcion`).
   - Conéctalos con un Rombo llamado **`posee`** (vía `rol_permisos`). Cardinalidad **`N:M`**.
   - Conecta **`Rol`** con **`Usuario`** mediante un Rombo llamado **`tiene`** (Rol `1` ➔ Rombo ➔ `N` Usuario).

5. **`Logro`** (Trofeos y Medallas):
   - Dibuja Rectángulo **`Logro`** (<u>`id`</u>, `codigo`, `nombre`, `puntos_recompensa`).
   - Conéctalo con **`Perfil Jugador`** mediante el Rombo **`desbloquea`** (`usuario_logros`).
   - Cardinalidad: **`N:M`**. Atributos del Rombo: `estado`, `progreso_actual`, `fecha_desbloqueo`.

---

### 📌 Paso 4: Entidades Débiles y Rombos Identificadores (Líneas Dobles)

Las **Entidades Débiles** no pueden existir sin su entidad fuerte contenedora. Se dibujan con **Rectángulo Doble** y su relación es un **Rombo Doble**.

1. **`Sesion Activa`** (Tabla `sesiones_activas`):
   - Rectángulo Doble: **`Sesion Activa`** (Atributos: `token_sesion`, `ip_origen`, `expira_en`).
   - Rombo Doble entre `Usuario` y `Sesion Activa`: **`inicia`** (Cardinalidad `1:N`).

2. **`Token Email`** y **`Token Recuperacion`**:
   - Rectángulos Dobles: **`Token Email`** / **`Token Recuperacion`**.
   - Rombo Doble con `Usuario`: **`genera`** (Cardinalidad `1:N`).

3. **`Notificacion`**:
   - Rectángulo Doble: **`Notificacion`** (Atributos: `titulo`, `mensaje`, `leido`).
   - Rombo Doble con `Usuario`: **`recibe`** (Cardinalidad `1:N`).

4. **`Encuentro`** (Tabla `torneo_encuentros`):
   - Rectángulo Doble: **`Encuentro`** (Atributos: <u>`id`</u>, `ronda`, `resultado_local`, `resultado_visitante`, `estado`).
   - Rombo Doble entre `Torneo` y `Encuentro`: **`contiene`** (Cardinalidad `1:N`).
   - Relaciones adicionales:
     - `Encuentro` se conecta mediante rombos a dos `Participante` (**Local** y **Visitante**).

5. **`Posicion Torneo`** (Tabla `torneo_posiciones`):
   - Rectángulo Doble: **`Posicion Torneo`** (Atributos: `puntos`, `partidos_jugados`, `diferencia_goles`).
   - Rombo Doble con `Torneo`: **`calcula`** (Cardinalidad `1:N`).

---

### 📌 Paso 4.5: Conexión Explícita de Módulos Especiales y Soporte de Torneo

Para conectar las tablas complementarias del motor deportivo de torneos (`ascend.sql`):

1. **`torneo_suizo_parejas` (Sistema Suizo de Emparejamiento):**
   - **Figura:** **Rombo Simple** llamado **`< emparejado en suizo >`**.
   - **Conexiones:** Conecta una línea a **`Torneo`** y dos líneas hacia **`ParticipanteTorneo`** (o `Participante`).
   - **Cardinalidad:** **`N:M`**.
   - **Óvalos colgados del Rombo:** `ronda`, `ya_se_enfrentaron` (Booleano), `fecha_encuentro`.

2. **`torneo_config` (Configuraciones Dinámicas Clave-Valor):**
   - **Figura:** **Rectángulo Doble** llamado **`Torneo Config`** (Entidad Débil).
   - **Atributos (Óvalos):** <u>`clave`</u> (Clave Parcial) y `valor`.
   - **Conexión:** Conecta a **`Torneo`** mediante un **Rombo Doble** llamado **`< configura >`**.
   - **Cardinalidad:** `Torneo` (1) $\rightarrow$ `< configura >` $\rightarrow$ (N) `Torneo Config`.

3. **`torneo_actividades` (Cronograma / Agenda del Torneo):**
   - **Figura:** **Rectángulo Doble** llamado **`Torneo Actividad`** (Entidad Débil).
   - **Atributos (Óvalos):** <u>`id`</u>, `titulo`, `tipo` (*administrativo, reunion, competencia, premiacion*), `fecha`, `hora`.
   - **Conexión:** Conecta a **`Torneo`** mediante un **Rombo Doble** llamado **`< programa >`**.
   - **Cardinalidad:** `Torneo` (1) $\rightarrow$ `< programa >` $\rightarrow$ (N) `Torneo Actividad`.

4. **`torneo_comentarios` (Interacción Comunitaria y Foro):**
   - **Figura:** **Rectángulo Doble** llamado **`Torneo Comentario`** (Entidad Débil).
   - **Atributos (Óvalos):** <u>`id`</u>, `nombre_autor`, `email_autor`, `comentario`, `aprobado`, `fecha_hora`.
   - **Conexión:** Conecta a **`Torneo`** mediante un **Rombo Doble** llamado **`< recibe comentario >`** (Cardinalidad `1:N`) y opcionalmente a **`Usuario`** (Rombo `escribe`).

5. **`torneo_cambio_estado` (Historial de Transiciones de Estado):**
   - **Figura:** **Rombo Simple** llamado **`< cambia estado >`** (Relación Histórica).
   - **Conexiones:** Conecta a **`Torneo`** y a **`Usuario`** / `Perfil Organizador` (el responsable que ejecutó el cambio).
   - **Cardinalidad:** `Torneo` (N) $\leftarrow$ `< cambia estado >` $\rightarrow$ (1) `Usuario`.
   - **Óvalos colgados del Rombo:** `estado_anterior`, `estado_nuevo`, `motivo`, `fecha_cambio`.

6. **`resultados_detalle` (Métricas Específicas por Encuentro):**
   - **Figura:** **Rectángulo Doble** llamado **`Resultado Detalle`** (Entidad Débil de `Encuentro`).
   - **Atributos (Óvalos):** <u>`id`</u>, `tipo_dato` (*kills_local, mvp, duracion*), `valor`.
   - **Conexión:** Conecta a **`Encuentro`** mediante un **Rombo Doble** llamado **`< detalla >`**.
   - **Cardinalidad:** `Encuentro` (1) $\rightarrow$ `< detalla >` $\rightarrow$ (N) `Resultado Detalle`.

---

### 📌 Paso 5: Representación de Logs, Auditoría y Seguridad

Para incluir las tablas de seguridad y auditoría en el MER sin sobrecargar el diagrama:

1. **`auditoria_cambios` / `logs_acceso` / `logs_actividad`**:
   - Se dibujan como **Entidades Débiles de Auditoría** (Rectángulos Dobles) o se conectan a **`Usuario`** mediante rombos de trazabilidad:
     - `Usuario` --[registra log (1:N)]--> **`Log Acceso`**
     - `Usuario` --[ejecuta accion (1:N)]--> **`Log Actividad`**
     - `Usuario` / `Administrador` --[audita (1:N)]--> **`Auditoria Cambios`**
2. **`politicas_contrasenas`** y **`historial_contrasenas`**:
   - `Politica Contraseña`: Entidad global configurada por el `Administrador`.
   - `Historial Contraseña`: Entidad Débil conectada a `Usuario` (`1:N`).

---

## 🎯 Lista de Cotejo y Verificación Final (35 de 35 Tablas)

A medida que dibujes tu diagrama en Draw.io, marca cada casilla para asegurarte de que representaste las 35 tablas:

- [ ] **1. `usuarios`** (Rectángulo Padre `Usuario`)
- [ ] **2. `perfiles_jugadores`** (Rectángulo Subclase `Perfil Jugador` + Triángulo `ES`)
- [ ] **3. `perfiles_organizadores`** (Rectángulo Subclase `Perfil Organizador` + Triángulo `ES`)
- [ ] **4. `roles`** (Rectángulo `Rol`)
- [ ] **5. `permisos`** (Rectángulo `Permiso`)
- [ ] **6. `juegos`** (Rectángulo `Juego`)
- [ ] **7. `modalidades`** (Rectángulo `Modalidad`)
- [ ] **8. `sistemas_puntuacion`** (Rectángulo `Sistema Puntuacion`)
- [ ] **9. `equipos`** (Rectángulo `Equipo`)
- [ ] **10. `torneos`** (Rectángulo `Torneo`)
- [ ] **11. `logros`** (Rectángulo `Logro`)
- [ ] **12. `newsletter_subscriptores`** (Rectángulo `Subscriptor Newsletter`)
- [ ] **13. `politicas_contrasenas`** (Rectángulo `Politica Contraseña`)
- [ ] **14. `tokens_verificacion_email`** (Rectángulo Doble + Rombo Doble con `Usuario`)
- [ ] **15. `tokens_recuperacion`** (Rectángulo Doble + Rombo Doble con `Usuario`)
- [ ] **16. `sesiones_activas`** (Rectángulo Doble + Rombo Doble con `Usuario`)
- [ ] **17. `notificaciones`** (Rectángulo Doble + Rombo Doble con `Usuario`)
- [ ] **18. `torneo_config`** (Rectángulo Doble + Rombo Doble con `Torneo`)
- [ ] **19. `torneo_encuentros`** (Rectángulo Doble + Rombo Doble con `Torneo`)
- [ ] **20. `torneo_posiciones`** (Rectángulo Doble + Rombo Doble con `Torneo`)
- [ ] **21. `rol_permisos`** (Rombo `posee` entre `Rol` y `Permiso`)
- [ ] **22. `equipo_miembros`** (Rombo `pertenece a` entre `Perfil Jugador` y `Equipo` + Óvalos de atributos)
- [ ] **23. `equipo_capitanes`** (Rombo `crea / capitanea` entre `Perfil Jugador` y `Equipo`)
- [ ] **24. `solicitudes_equipo`** (Rombo `solicita unirse` entre `Perfil Jugador` y `Equipo`)
- [ ] **25. `participantes_torneo`** (Rombo `participa en` entre `Jugador`/`Equipo` y `Torneo`)
- [ ] **26. `torneo_cambio_estado`** (Rombo `cambia estado` entre `Usuario`/`Organizador` y `Torneo`)
- [ ] **27. `torneo_suizo_parejas`** (Rombo `emparejado en suizo` entre `Participante` y `Torneo`)
- [ ] **28. `resultados_detalle`** (Óvalos de detalle en Rombo/Encuentro)
- [ ] **29. `torneo_actividades`** (Rectángulo Doble / Rombo `programa actividad` en `Torneo`)
- [ ] **30. `torneo_comentarios`** (Rectángulo Doble / Rombo `comenta en` entre `Usuario` y `Torneo`)
- [ ] **31. `usuario_logros`** (Rombo `desbloquea` entre `Perfil Jugador` y `Logro`)
- [ ] **32. `logs_acceso`** (Rombo/Entidad Débil `registra acceso` con `Usuario`)
- [ ] **33. `logs_actividad`** (Rombo/Entidad Débil `registra actividad` con `Usuario`)
- [ ] **34. `auditoria_cambios`** (Rombo/Entidad Débil `audita cambios` con `Usuario`)
- [ ] **35. `historial_contrasenas`** (Rectángulo Doble `historial clave` con `Usuario`)

---

¡Siguiendo este paso a paso detallado y cotejado, tendrás un MER conceptual impecable en notación de Peter Chen que refleja fielmente las 35 tablas de tu base de datos y la lógica real del sistema ASCEND!
