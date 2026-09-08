# 📊 Documento 03-1FN: Esquema de Relaciones en Primera Forma Normal (1FN)

---

## 🎯 1. Propósito del Documento
Este documento define la estructura en **Primera Forma Normal (1FN)** de la base de datos del sistema **SGDM ASCEND**. Se ha eliminado el estado no normalizado (0FN), erradicando campos multivaluados, listas concatenadas y grupos repetitivos.

---

## 📐 2. Simbología y Convención del Esquema de Relaciones

Siguiendo el estándar académico formal para esquemas de relaciones relacionales:

- **NombreTabla**: Identificador de la relación en negrita.
- <u>`atributo`</u>: **Clave Primaria (PK)** (Subrayado).
- *`atributo`*: **Clave Foránea (FK)** (Cursiva).
- <u>*`atributo`*</u>: **Clave Primaria y Foránea al mismo tiempo (PK/FK)** (Subrayado y Cursiva).
- `atributo`: Atributo atómico simple.

---

## 🏛️ 3. Esquema de las Relaciones

### Base de datos "ASCEND_1FN"

- **usuarios_1fn** (<u>`id`</u>, `email`, `contrasena_hash`, `nombre_completo`, `telefono`, `rol_nombre`, `nivel_permiso_rol`)
- **perfiles_1fn** (<u>*`usuario_id`*</u>, `apodo_gamertag`, `bio`, `banner_url`, `nivel`, `experiencia_puntos`, `pais`, `ciudad`, `fecha_nacimiento`, `discord_tag`, `instagram_url`, `twitter_url`, `nombre_organizacion`, `bio_organizacion`, `localidad`, `telefono_contacto`, `sitio_web`, `verificado_oficial`, `tipo_perfil`)
- **equipos_1fn** (<u>`id`</u>, `nombre_equipo`, `escudo_url`, `banner_url`, `descripcion`, `ubicacion`, `anio_fundacion`, `fecha_creacion`, *`creado_por`*, `codigo_invitacion`)
- **equipo_miembros_1fn** (<u>*`equipo_id`*</u>, <u>*`usuario_id`*</u>, `nombre_equipo`, `nombre_usuario`, `fecha_union`, `numero_camiseta`, `posicion`, `es_activo`)
- **equipo_capitanes_1fn** (<u>*`equipo_id`*</u>, <u>*`usuario_id`*</u>, `fecha_asignacion`, *`asignado_por`*, `es_capitan_principal`)
- **solicitudes_equipo_1fn** (<u>`id`</u>, *`equipo_id`*, *`usuario_id`*, `mensaje`, `estado`, `fecha_solicitud`, `fecha_respuesta`, *`respondido_por`*)
- **roles_1fn** (<u>`id`</u>, `nombre_rol`, `descripcion`, `nivel_permiso`)
- **permisos_1fn** (<u>`id`</u>, `nombre_permiso`, `descripcion`)
- **rol_permisos_1fn** (<u>*`rol_id`*</u>, <u>*`permiso_id`*</u>, `nombre_rol`, `nombre_permiso`, `asignado_en`)
- **juegos_1fn** (<u>`id`</u>, `nombre`, `categoria`, `formato_equipo_defecto`, `puntos_victoria`, `puntos_empate`, `puntos_derrota`, `activo`, `creado_en`)
- **modalidades_1fn** (<u>`id`</u>, `nombre`, `descripcion`)
- **sistemas_puntuacion_1fn** (<u>`id`</u>, `nombre`, `puntos_victoria`, `puntos_empate`, `puntos_derrota`, `descripcion`)
- **torneos_1fn** (<u>`id`</u>, `nombre`, *`juego_id`*, `juego_nombre`, `juego_categoria`, `descripcion`, `formato`, `estado`, `cupo_max_equipos`, `cupo_min_equipos`, `fecha_inicio_inscripcion`, `fecha_limite_inscripcion`, `fecha_inicio`, `fecha_fin`, `reglas`, `premios`, `ubicacion`, `localidad`, `comunidad`, `banner_url`, `stream_url`, *`organizador_id`*, `organizador_nombre`, *`modalidad_id`*, *`sistema_puntuacion_id`*, `puntos_victoria`, `puntos_empate`, `puntos_derrota`, `tipo_resultado`, `mejor_de`)
- **participantes_torneo_1fn** (<u>*`torneo_id`*</u>, <u>`participante_id`</u>, `tipo`, `referencia_id`, `nombre`, `estado`, `fecha_inscripcion`, *`confirmado_por`*, `fecha_confirmacion`)
- **torneo_encuentros_1fn** (<u>`id`</u>, *`torneo_id`*, `ronda`, *`participante_local_id`*, *`participante_visitante_id`*, `fecha_hora_programada`, `cancha`, `resultado_local`, `resultado_visitante`, `estado`, *`participante_ganador_id`*, *`siguiente_encuentro_id`*, `canal_transmision`, *`modificado_por_usuario_id`*)
- **torneo_posiciones_1fn** (<u>*`torneo_id`*</u>, <u>*`participante_id`*</u>, `partidos_jugados`, `partidos_ganados`, `partidos_empatados`, `partidos_perdidos`, `puntos_favor`, `puntos_contra`, `diferencia_goles`, `puntos`, `sanciones_puntos`)
- **logros_1fn** (<u>`id`</u>, `codigo`, `nombre`, `descripcion`, `icono`, `puntos_recompensa`)
- **usuario_logros_1fn** (<u>*`usuario_id`*</u>, <u>*`logro_id`*</u>, `estado`, `progreso_actual`, `progreso_objetivo`, `fecha_desbloqueo`)

---

## ⚠️ 4. Diagnóstico de Anomalías en 1FN

En esta fase de **1FN**, se han garantizado datos atómicos y claves primarias, pero existen **Dependencias Funcionales Parciales** en las tablas compuestas:
1. `nombre_equipo` en `equipo_miembros_1fn` depende únicamente de `equipo_id`, provocando redundancia.
2. `nombre_usuario` en `equipo_miembros_1fn` depende únicamente de `usuario_id`.
3. `nombre_rol` y `nombre_permiso` en `rol_permisos_1fn` dependen parcialmente de sus respectivas claves individuales.

> **Siguiente Paso:** Aplicar la **Segunda Forma Normal (2FN)** para eliminar todas las dependencias parciales en claves compuestas.
