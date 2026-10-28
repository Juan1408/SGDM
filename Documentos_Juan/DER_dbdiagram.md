# 📊 Código DBML para dbdiagram.io (Diagrama con Colores)

Copia todo el bloque de código a continuación y pégalo en el editor de **[dbdiagram.io](https://dbdiagram.io/d)**. 
Este código soluciona los errores estrictos de validación (añadiendo índices de claves compuestas y marcando las foráneas obligatorias con `not null`) y agrupa visualmente las tablas por **Módulos con colores**.

```dbml
// ==========================================
// 1. ROLES Y PERMISOS (Rojo)
// ==========================================
Table roles [headercolor: #E53935] {
  id int [pk, increment]
  nombre_rol varchar [unique]
  descripcion text
  nivel_permiso int
}

Table permisos [headercolor: #E53935] {
  id int [pk, increment]
  nombre_permiso varchar [unique]
  descripcion text
}

Table rol_permisos [headercolor: #E53935] {
  rol_id int [not null]
  permiso_id int [not null]
  asignado_en datetime

  indexes {
    (rol_id, permiso_id) [pk]
  }
}

TableGroup Seguridad {
  roles
  permisos
  rol_permisos
}

// ==========================================
// 2. USUARIOS Y HERENCIA DE PERFILES (Azul)
// ==========================================
Table usuarios [headercolor: #1E88E5] {
  id int [pk, increment]
  email varchar [unique]
  contrasena_hash varchar
  nombre_completo varchar
  telefono varchar
  foto_perfil_url varchar
  rol_id int [not null]
  esta_activo boolean
  email_verificado boolean
  fecha_registro datetime
  ultimo_acceso datetime
}

Table perfiles_jugadores [headercolor: #1E88E5] {
  usuario_id int [pk]
  apodo_gamertag varchar
  bio text
  banner_url varchar
  nivel int
  experiencia_puntos int
  pais varchar
  ciudad varchar
  fecha_nacimiento date
  discord_tag varchar
  instagram_url varchar
  twitter_url varchar
}

Table perfiles_organizadores [headercolor: #1E88E5] {
  usuario_id int [pk]
  nombre_organizacion varchar
  bio_organizacion text
  localidad varchar
  telefono_contacto varchar
  sitio_web varchar
  verificado_oficial boolean
}

Table tokens_verificacion_email [headercolor: #1E88E5] {
  id int [pk, increment]
  usuario_id int [not null]
  token varchar [unique]
  expira_en datetime
  usado boolean
}

Table tokens_recuperacion [headercolor: #1E88E5] {
  id int [pk, increment]
  usuario_id int [not null]
  token varchar [unique]
  expira_en datetime
  usado boolean
}

Table sesiones_activas [headercolor: #1E88E5] {
  id int [pk, increment]
  usuario_id int [not null]
  token_sesion varchar [unique]
  ip_origen varchar
  user_agent text
  ultima_actividad datetime
  activa boolean
}

Table logs_acceso [headercolor: #1E88E5] {
  id int [pk, increment]
  email_intentado varchar
  usuario_id int [not null]
  exito boolean
  ip_origen varchar
  fecha_hora datetime
}

Table aceptaciones_legales [headercolor: #1E88E5] {
  id int [pk, increment]
  usuario_id int [not null]
  tipo_documento varchar [not null]
  version varchar [not null]
  aceptado_en datetime
  ip_origen varchar
  user_agent varchar

  indexes {
    (usuario_id, tipo_documento, version) [unique]
  }
}

TableGroup Autenticacion_Usuarios {
  usuarios
  perfiles_jugadores
  perfiles_organizadores
  tokens_verificacion_email
  tokens_recuperacion
  sesiones_activas
  logs_acceso
  aceptaciones_legales
}

// ==========================================
// 3. CATÁLOGOS BASE (Morado)
// ==========================================
Table juegos [headercolor: #8E24AA] {
  id int [pk, increment]
  nombre varchar [unique]
  categoria varchar
  puntos_victoria decimal
  puntos_empate decimal
  puntos_derrota decimal
  activo boolean
}

Table formatos_torneo [headercolor: #8E24AA] {
  codigo varchar [pk]
  nombre varchar
  descripcion text
  activo boolean
}

Table juego_formatos_permitidos [headercolor: #8E24AA] {
  juego_id int [not null]
  formato_codigo varchar [not null]

  indexes {
    (juego_id, formato_codigo) [pk]
  }
}

Table juego_modalidades_permitidas [headercolor: #8E24AA] {
  juego_id int [not null]
  modalidad_id int [not null]

  indexes {
    (juego_id, modalidad_id) [pk]
  }
}

Table modalidades [headercolor: #8E24AA] {
  id int [pk, increment]
  nombre varchar [unique]
  descripcion text
}

Table sistemas_puntuacion [headercolor: #8E24AA] {
  id int [pk, increment]
  nombre varchar [unique]
  puntos_victoria decimal
  puntos_empate decimal
  puntos_derrota decimal
}

TableGroup Catalogos_Base {
  juegos
  formatos_torneo
  juego_formatos_permitidos
  juego_modalidades_permitidas
  modalidades
  sistemas_puntuacion
}

// ==========================================
// 4. EQUIPOS Y MEMBRESÍAS (Naranja)
// ==========================================
Table equipos [headercolor: #FB8C00] {
  id int [pk, increment]
  nombre_equipo varchar [unique]
  escudo_url varchar
  descripcion text
  creado_por int [not null]
  activo boolean
  codigo_invitacion varchar [unique]
}

Table equipo_miembros [headercolor: #FB8C00] {
  equipo_id int [not null]
  usuario_id int [not null]
  fecha_union datetime
  numero_camiseta int
  posicion varchar
  es_activo boolean

  indexes {
    (equipo_id, usuario_id) [pk]
  }
}

Table equipo_capitanes [headercolor: #FB8C00] {
  equipo_id int [not null]
  usuario_id int [not null]
  fecha_asignacion datetime
  asignado_por int [not null]
  es_capitan_principal boolean

  indexes {
    (equipo_id, usuario_id) [pk]
  }
}

Table solicitudes_equipo [headercolor: #FB8C00] {
  id int [pk, increment]
  equipo_id int [not null]
  usuario_id int [not null]
  iniciado_por int [not null]
  mensaje text
  estado varchar
  fecha_solicitud datetime
}

TableGroup Equipos {
  equipos
  equipo_miembros
  equipo_capitanes
  solicitudes_equipo
}

// ==========================================
// 5. NÚCLEO DE TORNEOS Y COMPETENCIAS (Verde)
// ==========================================
Table torneos [headercolor: #43A047] {
  id int [pk, increment]
  nombre varchar
  juego_id int [not null]
  organizador_id int [not null]
  modalidad_id int [not null]
  sistema_puntuacion_id int [not null]
  formato varchar
  estado varchar
  cupo_max_equipos int
  fecha_inicio date
  fecha_fin date
}

Table torneo_cambio_estado [headercolor: #43A047] {
  id int [pk, increment]
  torneo_id int [not null]
  estado_anterior varchar
  estado_nuevo varchar
  motivo text
  usuario_id int [not null]
  fecha_cambio datetime
}

Table participantes_torneo [headercolor: #43A047] {
  id int [pk, increment]
  torneo_id int [not null]
  tipo varchar
  referencia_id int
  nombre varchar
  estado varchar
  fecha_inscripcion datetime
}

Table torneo_encuentros [headercolor: #43A047] {
  id int [pk, increment]
  torneo_id int [not null]
  ronda int
  participante_local_id int [not null]
  participante_visitante_id int [not null]
  resultado_local decimal
  resultado_visitante decimal
  estado varchar
  participante_ganador_id int [not null]
}

Table torneo_posiciones [headercolor: #43A047] {
  torneo_id int [not null]
  participante_id int [not null]
  partidos_jugados int
  partidos_ganados int
  partidos_empatados int
  partidos_perdidos int
  puntos_favor decimal
  puntos_contra decimal
  diferencia_goles decimal
  puntos int

  indexes {
    (torneo_id, participante_id) [pk]
  }
}

Table torneo_suizo_parejas [headercolor: #43A047] {
  torneo_id int [not null]
  participante_a_id int [not null]
  participante_b_id int [not null]
  ronda int
  ya_se_enfrentaron boolean

  indexes {
    (torneo_id, participante_a_id, participante_b_id) [pk]
  }
}

Table resultados_detalle [headercolor: #43A047] {
  id int [pk, increment]
  encuentro_id int [not null]
  tipo_dato varchar
  valor varchar
}

Table torneo_config [headercolor: #43A047] {
  torneo_id int [not null]
  clave varchar [not null]
  valor text

  indexes {
    (torneo_id, clave) [pk]
  }
}

Table torneo_actividades [headercolor: #43A047] {
  id int [pk, increment]
  torneo_id int [not null]
  titulo varchar
  tipo varchar
  fecha date
}

Table torneo_comentarios [headercolor: #43A047] {
  id int [pk, increment]
  torneo_id int [not null]
  usuario_id int [not null]
  nombre_autor varchar
  email_autor varchar
  comentario text
}

TableGroup Torneos {
  torneos
  torneo_cambio_estado
  participantes_torneo
  torneo_encuentros
  torneo_posiciones
  torneo_suizo_parejas
  resultados_detalle
  torneo_config
  torneo_actividades
  torneo_comentarios
}

// ==========================================
// 6. GAMIFICACIÓN (Amarillo / Dorado)
// ==========================================
Table logros [headercolor: #FDD835] {
  id int [pk, increment]
  codigo varchar [unique]
  nombre varchar
  descripcion text
  puntos_recompensa int
}

Table usuario_logros [headercolor: #FDD835] {
  usuario_id int [not null]
  logro_id int [not null]
  estado varchar
  progreso_actual int
  fecha_desbloqueo datetime

  indexes {
    (usuario_id, logro_id) [pk]
  }
}

TableGroup Gamificacion {
  logros
  usuario_logros
}

// ==========================================
// 7. COMUNICACIONES Y AUDITORÍA (Gris Azulado)
// ==========================================
Table notificaciones [headercolor: #546E7A] {
  id int [pk, increment]
  usuario_id int [not null]
  tipo varchar
  titulo varchar
  mensaje text
  leido boolean
}

Table newsletter_subscriptores [headercolor: #546E7A] {
  id int [pk, increment]
  email varchar [unique]
  activo boolean
}

Table auditoria_cambios [headercolor: #546E7A] {
  id int [pk, increment]
  tabla_afectada varchar
  registro_id int
  accion varchar
  usuario_id int [not null]
  ip_origen varchar
  fecha_hora datetime
}

Table politicas_contrasenas [headercolor: #546E7A] {
  id int [pk, increment]
  longitud_minima int
  requiere_mayuscula boolean
  requiere_minuscula boolean
  requiere_numero boolean
  expiracion_dias int
}

Table historial_contrasenas [headercolor: #546E7A] {
  id int [pk, increment]
  usuario_id int [not null]
  hash_anterior text
  fecha_cambio datetime
}

Table logs_actividad [headercolor: #546E7A] {
  id int [pk, increment]
  usuario_id int [not null]
  accion varchar
  ip_origen varchar
  fecha_hora datetime
}

Table errores_sistema [headercolor: #546E7A] {
  id int [pk, increment]
  hash_error varchar [unique]
  tipo_error varchar
  mensaje text
  archivo varchar
  linea int
  stack_trace text
  contexto_json text
  usuario_id int
  rol_usuario varchar
  cantidad_ocurrencias int
  primera_vez datetime
  ultima_vez datetime
  estado varchar
  detalles_resolucion text
}

Table intentos_login [headercolor: #546E7A] {
  ip_origen varchar [pk]
  intentos int
  ultimo_intento datetime
  bloqueado_hasta datetime
}

TableGroup Comunicacion_Auditoria {
  notificaciones
  newsletter_subscriptores
  auditoria_cambios
  politicas_contrasenas
  historial_contrasenas
  logs_actividad
  errores_sistema
  intentos_login
}

// ==========================================
// RELACIONES (Claves Foráneas)
// ==========================================

// Módulo Usuarios/Seguridad
Ref: rol_permisos.rol_id > roles.id
Ref: rol_permisos.permiso_id > permisos.id
Ref: usuarios.rol_id > roles.id
Ref: perfiles_jugadores.usuario_id - usuarios.id
Ref: perfiles_organizadores.usuario_id - usuarios.id
Ref: tokens_verificacion_email.usuario_id > usuarios.id
Ref: tokens_recuperacion.usuario_id > usuarios.id
Ref: sesiones_activas.usuario_id > usuarios.id
Ref: logs_acceso.usuario_id > usuarios.id
Ref: aceptaciones_legales.usuario_id > usuarios.id
Ref: errores_sistema.usuario_id > usuarios.id
Ref: notificaciones.usuario_id > usuarios.id
Ref: historial_contrasenas.usuario_id > usuarios.id
Ref: logs_actividad.usuario_id > usuarios.id
Ref: auditoria_cambios.usuario_id > usuarios.id

// Módulo Equipos
Ref: equipos.creado_por > usuarios.id
Ref: equipo_miembros.equipo_id > equipos.id
Ref: equipo_miembros.usuario_id > usuarios.id
Ref: equipo_capitanes.equipo_id > equipos.id
Ref: equipo_capitanes.usuario_id > usuarios.id
Ref: equipo_capitanes.asignado_por > usuarios.id
Ref: solicitudes_equipo.equipo_id > equipos.id
Ref: solicitudes_equipo.usuario_id > usuarios.id
Ref: solicitudes_equipo.iniciado_por > usuarios.id

// Módulo Catálogos
Ref: juego_formatos_permitidos.juego_id > juegos.id
Ref: juego_formatos_permitidos.formato_codigo > formatos_torneo.codigo
Ref: juego_modalidades_permitidas.juego_id > juegos.id
Ref: juego_modalidades_permitidas.modalidad_id > modalidades.id

// Módulo Torneos
Ref: torneos.juego_id > juegos.id
Ref: torneos.organizador_id > usuarios.id
Ref: torneos.modalidad_id > modalidades.id
Ref: torneos.sistema_puntuacion_id > sistemas_puntuacion.id
Ref: torneo_cambio_estado.torneo_id > torneos.id
Ref: torneo_cambio_estado.usuario_id > usuarios.id
Ref: participantes_torneo.torneo_id > torneos.id
Ref: torneo_encuentros.torneo_id > torneos.id
Ref: torneo_encuentros.participante_local_id > participantes_torneo.id
Ref: torneo_encuentros.participante_visitante_id > participantes_torneo.id
Ref: torneo_encuentros.participante_ganador_id > participantes_torneo.id
Ref: torneo_posiciones.torneo_id > torneos.id
Ref: torneo_posiciones.participante_id > participantes_torneo.id
Ref: torneo_suizo_parejas.torneo_id > torneos.id
Ref: torneo_suizo_parejas.participante_a_id > participantes_torneo.id
Ref: torneo_suizo_parejas.participante_b_id > participantes_torneo.id
Ref: torneo_config.torneo_id > torneos.id
Ref: torneo_actividades.torneo_id > torneos.id
Ref: torneo_comentarios.torneo_id > torneos.id
Ref: torneo_comentarios.usuario_id > usuarios.id
Ref: resultados_detalle.encuentro_id > torneo_encuentros.id

// Módulo Gamificación
Ref: usuario_logros.logro_id > logros.id
Ref: usuario_logros.usuario_id > usuarios.id

Table guardados [headercolor: #4CAF50] {
  id int [pk, increment]
  usuario_id int [not null]
  tipo varchar [not null, note: "torneo o equipo"]
  referencia_id int [not null]
  fecha_guardado timestamp

  indexes {
    (usuario_id, tipo, referencia_id) [unique]
  }
}

Ref: guardados.usuario_id > usuarios.id
```
