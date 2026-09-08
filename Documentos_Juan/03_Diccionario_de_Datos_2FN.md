# 📊 Documento 03-2FN: Esquema de Relaciones en Segunda Forma Normal (2FN)

---

## 🎯 1. Propósito del Documento
Este documento especifica el **Esquema de las Relaciones** en **Segunda Forma Normal (2FN)** para la base de datos del sistema **SGDM ASCEND**. En esta fase se han eliminado todas las **Dependencias Funcionales Parciales**, garantizando que cada atributo no clave en tablas compuestas dependa de la **totalidad** de la Clave Primaria.

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

### Base de datos "ASCEND_2FN"

- **usuarios_2fn** (<u>`id`</u>, `email`, `contrasena_hash`, `nombre_completo`, `telefono`, `foto_perfil_url`, *`rol_id`*, `nombre_rol`, `nivel_permiso`, `esta_activo`, `email_verificado`, `fecha_registro`, `ultimo_acceso`, `apodo_gamertag`, `nombre_organizacion`)
- **perfiles_jugadores_2fn** (<u>*`usuario_id`*</u>, `apodo_gamertag`, `bio`, `banner_url`, `nivel`, `experiencia_puntos`, `pais`, `ciudad`, `fecha_nacimiento`, `discord_tag`, `instagram_url`, `twitter_url`)
- **perfiles_organizadores_2fn** (<u>*`usuario_id`*</u>, `nombre_organizacion`, `bio_organizacion`, `localidad`, `telefono_contacto`, `sitio_web`, `verificado_oficial`)
- **roles** (<u>`id`</u>, `nombre_rol`, `descripcion`, `nivel_permiso`)
- **permisos** (<u>`id`</u>, `nombre_permiso`, `descripcion`)
- **rol_permisos** (<u>*`rol_id`*</u>, <u>*`permiso_id`*</u>, `asignado_en`)
- **modalidades** (<u>`id`</u>, `nombre`, `descripcion`)
- **sistemas_puntuacion** (<u>`id`</u>, `nombre`, `puntos_victoria`, `puntos_empate`, `puntos_derrota`, `descripcion`)
- **juegos** (<u>`id`</u>, `nombre`, `categoria`, `formato_equipo_defecto`, `puntos_victoria`, `puntos_empate`, `puntos_derrota`, `activo`, `creado_en`)
- **equipos** (<u>`id`</u>, `nombre_equipo`, `escudo_url`, `banner_url`, `descripcion`, `ubicacion`, `anio_fundacion`, `fecha_creacion`, *`creado_por`*, `activo`, `codigo_invitacion`)
- **equipo_miembros** (<u>*`equipo_id`*</u>, <u>*`usuario_id`*</u>, `fecha_union`, `numero_camiseta`, `posicion`, `es_activo`)
- **equipo_capitanes** (<u>*`equipo_id`*</u>, <u>*`usuario_id`*</u>, `fecha_asignacion`, *`asignado_por`*, `es_capitan_principal`)
- **solicitudes_equipo** (<u>`id`</u>, *`equipo_id`*, *`usuario_id`*, `mensaje`, `estado`, `fecha_solicitud`, `fecha_respuesta`, *`respondido_por`*)
- **torneos_2fn** (<u>`id`</u>, `nombre`, *`juego_id`*, `descripcion`, `formato`, `estado`, `cupo_max_equipos`, `cupo_min_equipos`, `fecha_inicio_inscripcion`, `fecha_limite_inscripcion`, `fecha_inicio`, `fecha_fin`, `reglas`, `premios`, `ubicacion`, `localidad`, `comunidad`, `banner_url`, `stream_url`, *`organizador_id`*, *`modalidad_id`*, *`sistema_puntuacion_id`*, `puntos_victoria`, `puntos_empate`, `puntos_derrota`, `tipo_resultado`, `mejor_de`)
- **participantes_torneo** (<u>`id`</u>, *`torneo_id`*, `tipo`, `referencia_id`, `nombre`, `estado`, `fecha_inscripcion`, *`confirmado_por`*, `fecha_confirmacion`)
- **torneo_encuentros** (<u>`id`</u>, *`torneo_id`*, `ronda`, *`participante_local_id`*, *`participante_visitante_id`*, `fecha_hora_programada`, `cancha`, `resultado_local`, `resultado_visitante`, `estado`, *`participante_ganador_id`*, *`siguiente_encuentro_id`*, `canal_transmision`, *`modificado_por_usuario_id`*)
- **torneo_posiciones** (<u>*`torneo_id`*</u>, <u>*`participante_id`*</u>, `partidos_jugados`, `partidos_ganados`, `partidos_empatados`, `partidos_perdidos`, `puntos_favor`, `puntos_contra`, `diferencia_goles`, `puntos`, `sanciones_puntos`)
- **logros** (<u>`id`</u>, `codigo`, `nombre`, `descripcion`, `icono`, `puntos_recompensa`)
- **usuario_logros** (<u>*`usuario_id`*</u>, <u>*`logro_id`*</u>, `estado`, `progreso_actual`, `progreso_objetivo`, `fecha_desbloqueo`)

---

## ⚠️ 4. Diagnóstico de Anomalías en 2FN

En esta fase de **2FN**, se han purificado las tablas intermedias compuestas de dependencias parciales. Sin embargo, en las tablas principales persisten **Dependencias Funcionales Transitivas**:
1. En `usuarios_2fn`: `id` $\rightarrow$ `rol_id` $\rightarrow$ `nombre_rol`, `nivel_permiso` (atributos no clave que dependen de otro atributo no clave).
2. Mezcla de subtipos en `usuarios_2fn` (`apodo_gamertag` y `nombre_organizacion` conviven generando celdas `NULL`).

> **Siguiente Paso:** Aplicar la **Tercera Forma Normal (3FN)** para remover dependencias transitivas e implementar herencia de tablas (1:1).
