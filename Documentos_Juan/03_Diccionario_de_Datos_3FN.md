# 📊 Documento 03-3FN: Esquema de Relaciones en Tercera Forma Normal (3FN)

---

## 🎯 1. Propósito del Documento
Este documento especifica el **Esquema de las Relaciones** en **Tercera Forma Normal (3FN)** para la base de datos del sistema **SGDM ASCEND**. Corresponde a la estructura física final de las 35 tablas del script `ascend.sql`, habiendo eliminado todas las **Dependencias Funcionales Transitivas** e implementado el patrón de Herencia de Tablas (Class Table Inheritance 1:1).

---

## 📐 2. Simbología y Convención del Esquema de Relaciones

Siguiendo el estándar académico formal para esquemas de relaciones relacionales:

- **NombreTabla**: Identificador de la relación en negrita.
- <u>`atributo`</u>: **Clave Primaria (PK)** (Subrayado).
- *`atributo`*: **Clave Foránea (FK)** (Cursiva).
- <u>*`atributo`*</u>: **Clave Primaria y Foránea al mismo tiempo (PK/FK)** (Subrayado y Cursiva).
- `atributo`: Atributo atómico simple.

---

## 🏛️ 3. Esquema de las Relaciones (35 Tablas en 3FN)

### Base de datos "ASCEND"

1. **permisos** (<u>`id`</u>, `nombre_permiso`, `descripcion`)
2. **roles** (<u>`id`</u>, `nombre_rol`, `descripcion`, `nivel_permiso`)
3. **rol_permisos** (<u>*`rol_id`*</u>, <u>*`permiso_id`*</u>, `asignado_en`)
4. **modalidades** (<u>`id`</u>, `nombre`, `descripcion`)
5. **sistemas_puntuacion** (<u>`id`</u>, `nombre`, `puntos_victoria`, `puntos_empate`, `puntos_derrota`, `descripcion`)
6. **juegos** (<u>`id`</u>, `nombre`, `categoria`, `formato_equipo_defecto`, `puntos_victoria`, `puntos_empate`, `puntos_derrota`, `activo`, `creado_en`)
7. **usuarios** (<u>`id`</u>, `email`, `contrasena_hash`, `nombre_completo`, `telefono`, `foto_perfil_url`, *`rol_id`*, `esta_activo`, `email_verificado`, `fecha_registro`, `ultimo_acceso`)
8. **perfiles_jugadores** (<u>*`usuario_id`*</u>, `apodo_gamertag`, `bio`, `banner_url`, `nivel`, `experiencia_puntos`, `pais`, `ciudad`, `fecha_nacimiento`, `discord_tag`, `instagram_url`, `twitter_url`, `actualizado_en`)
9. **perfiles_organizadores** (<u>*`usuario_id`*</u>, `nombre_organizacion`, `bio_organizacion`, `localidad`, `telefono_contacto`, `sitio_web`, `verificado_oficial`, `actualizado_en`)
10. **tokens_verificacion_email** (<u>`id`</u>, *`usuario_id`*, `token`, `expira_en`, `usado`, `creado_en`)
11. **tokens_recuperacion** (<u>`id`</u>, *`usuario_id`*, `token`, `expira_en`, `usado`, `creado_en`)
12. **sesiones_activas** (<u>`id`</u>, *`usuario_id`*, `token_sesion`, `ip_origen`, `user_agent`, `ultima_actividad`, `creado_en`, `expira_en`, `activa`)
13. **logs_acceso** (<u>`id`</u>, `email_intentado`, *`usuario_id`*, `exito`, `ip_origen`, `user_agent`, `mensaje_error`, `fecha_hora`)
14. **equipos** (<u>`id`</u>, `nombre_equipo`, `escudo_url`, `banner_url`, `descripcion`, `ubicacion`, `anio_fundacion`, `fecha_creacion`, *`creado_por`*, `activo`, `codigo_invitacion`)
15. **equipo_miembros** (<u>*`equipo_id`*</u>, <u>*`usuario_id`*</u>, `fecha_union`, `numero_camiseta`, `posicion`, `es_activo`)
16. **equipo_capitanes** (<u>*`equipo_id`*</u>, <u>*`usuario_id`*</u>, `fecha_asignacion`, *`asignado_por`*, `es_capitan_principal`)
17. **solicitudes_equipo** (<u>`id`</u>, *`equipo_id`*, *`usuario_id`*, `mensaje`, `estado`, `fecha_solicitud`, `fecha_respuesta`, *`respondido_por`*)
18. **torneos** (<u>`id`</u>, `nombre`, *`juego_id`*, `descripcion`, `formato`, `estado`, `cupo_max_equipos`, `cupo_min_equipos`, `fecha_inicio_inscripcion`, `fecha_limite_inscripcion`, `fecha_inicio`, `fecha_fin`, `reglas`, `premios`, `ubicacion`, `localidad`, `comunidad`, `banner_url`, `stream_url`, *`organizador_id`*, `creado_en`, `actualizado_en`, *`modalidad_id`*, *`sistema_puntuacion_id`*, `puntos_victoria`, `puntos_empate`, `puntos_derrota`, `tipo_resultado`, `mejor_de`)
19. **torneo_cambio_estado** (<u>`id`</u>, *`torneo_id`*, `estado_anterior`, `estado_nuevo`, `motivo`, *`usuario_id`*, `fecha_cambio`)
20. **participantes_torneo** (<u>`id`</u>, *`torneo_id`*, `tipo`, `referencia_id`, `nombre`, `estado`, `fecha_inscripcion`, *`confirmado_por`*, `fecha_confirmacion`)
21. **torneo_encuentros** (<u>`id`</u>, *`torneo_id`*, `ronda`, *`participante_local_id`*, *`participante_visitante_id`*, `fecha_hora_programada`, `cancha`, `resultado_local`, `resultado_visitante`, `estado`, *`participante_ganador_id`*, *`siguiente_encuentro_id`*, `canal_transmision`, `creado_en`, `ultima_modificacion`, *`modificado_por_usuario_id`*)
22. **torneo_posiciones** (<u>*`torneo_id`*</u>, <u>*`participante_id`*</u>, `partidos_jugados`, `partidos_ganados`, `partidos_empatados`, `partidos_perdidos`, `puntos_favor`, `puntos_contra`, `diferencia_goles`, `puntos`, `sanciones_puntos`, `ultima_actualizacion`)
23. **torneo_suizo_parejas** (<u>*`torneo_id`*</u>, <u>*`participante_a_id`*</u>, <u>*`participante_b_id`*</u>, `ronda`, `ya_se_enfrentaron`, `fecha_encuentro`)
24. **resultados_detalle** (<u>`id`</u>, *`encuentro_id`*, `tipo_dato`, `valor`)
25. **torneo_config** (<u>*`torneo_id`*</u>, <u>`clave`</u>, `valor`)
26. **torneo_actividades** (<u>`id`</u>, *`torneo_id`*, `titulo`, `tipo`, `fecha`, `hora`, `creado_en`)
27. **torneo_comentarios** (<u>`id`</u>, *`torneo_id`*, *`usuario_id`*, `nombre_autor`, `email_autor`, `comentario`, `aprobado`, `fecha_hora`)
28. **logros** (<u>`id`</u>, `codigo`, `nombre`, `descripcion`, `icono`, `puntos_recompensa`, `creado_en`)
29. **usuario_logros** (<u>*`usuario_id`*</u>, <u>*`logro_id`*</u>, `estado`, `progreso_actual`, `progreso_objetivo`, `fecha_desbloqueo`)
30. **notificaciones** (<u>`id`</u>, *`usuario_id`*, `tipo`, `titulo`, `mensaje`, `enlace_relacionado`, `leido`, `fecha_lectura`, `creado_en`)
31. **newsletter_subscriptores** (<u>`id`</u>, `email`, `activo`, `fecha_suscripcion`)
32. **auditoria_cambios** (<u>`id`</u>, `tabla_afectada`, `registro_id`, `accion`, *`usuario_id`*, `datos_viejos`, `datos_nuevos`, `ip_origen`, `user_agent`, `fecha_hora`)
33. **politicas_contrasenas** (<u>`id`</u>, `longitud_minima`, `requiere_mayuscula`, `requiere_minuscula`, `requiere_numero`, `requiere_caracter_especial`, `expiracion_dias`, `historial_cantidad`, `actualizado_en`, *`actualizado_por`*)
34. **historial_contrasenas** (<u>`id`</u>, *`usuario_id`*, `hash_anterior`, `fecha_cambio`)
35. **logs_actividad** (<u>`id`</u>, *`usuario_id`*, `accion`, `descripcion`, `ip_origen`, `user_agent`, `fecha_hora`)

---

## ✅ 4. Verificación de Integridad en 3FN

La base de datos en 3FN cumple con las siguientes condiciones estrictas:
1. **1FN Cumplida**: Todos los campos contienen valores escalares indivisibles sin grupos repetitivos.
2. **2FN Cumplida**: No existen dependencias funcionales parciales en tablas con claves compuestas.
3. **3FN Cumplida**: Cada atributo no clave depende **única y exclusivamente** de la Clave Primaria (eliminación de dependencias transitivas).
