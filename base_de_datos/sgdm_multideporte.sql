-- =============================================================================
-- SCRIPT SQL COMPLETO – BASE DE DATOS SGDM VERSIÓN MULTIDEPORTIVA
-- 25 tablas normalizadas en 3FN, con relaciones y cascadas.
-- =============================================================================

-- =============================================================================
-- EXPLICACIÓN DE DECISIONES DE CASCADA:
-- ON DELETE CASCADE: cuando se borra un registro padre, se borran automáticamente
--                    los hijos (ej: borrar un torneo -> encuentros, posiciones, config).
-- ON DELETE SET NULL: cuando se borra el padre, la referencia queda NULL
--                     (útil para logs o auditoría donde no queremos perder la fila).
-- ON DELETE RESTRICT (por defecto): impide borrar si hay hijos referenciados
--                                   (para proteger integridad crítica como roles o usuarios).
-- =============================================================================

CREATE DATABASE IF NOT EXISTS sgdm;
USE sgdm;

-- Desactivar temporalmente la verificación de claves foráneas para recrear/crear sin problemas de orden
SET FOREIGN_KEY_CHECKS = 0;

-- Drop tables if they exist to allow clean reinstallations
DROP TABLE IF EXISTS logs_actividad;
DROP TABLE IF EXISTS historial_contrasenas;
DROP TABLE IF EXISTS politicas_contrasenas;
DROP TABLE IF EXISTS auditoria_cambios;
DROP TABLE IF EXISTS notificaciones;
DROP TABLE IF EXISTS torneo_config;
DROP TABLE IF EXISTS resultados_detalle;
DROP TABLE IF EXISTS torneo_suizo_parejas;
DROP TABLE IF EXISTS torneo_posiciones;
DROP TABLE IF EXISTS torneo_encuentros;
DROP TABLE IF EXISTS participantes_torneo;
DROP TABLE IF EXISTS torneo_cambio_estado;
DROP TABLE IF EXISTS torneos;
DROP TABLE IF EXISTS solicitudes_equipo;
DROP TABLE IF EXISTS equipo_capitanes;
DROP TABLE IF EXISTS equipo_miembros;
DROP TABLE IF EXISTS equipos;
DROP TABLE IF EXISTS logs_acceso;
DROP TABLE IF EXISTS sesiones_activas;
DROP TABLE IF EXISTS tokens_recuperacion;
DROP TABLE IF EXISTS tokens_verificacion_email;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS sistemas_puntuacion;
DROP TABLE IF EXISTS modalidades;
DROP TABLE IF EXISTS roles;

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================================
-- 1. TABLAS MAESTRAS (Catálogos)
-- =============================================================================

-- 1. roles
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) UNIQUE NOT NULL,
    descripcion TEXT,
    nivel_permiso INT DEFAULT 0
);

-- 2. modalidades (individual / equipos)
CREATE TABLE modalidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) UNIQUE NOT NULL,
    descripcion TEXT
);

-- 3. sistemas_puntuacion (catálogo de sistemas predefinidos)
CREATE TABLE sistemas_puntuacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) UNIQUE NOT NULL,
    puntos_victoria DECIMAL(5,2) NOT NULL,
    puntos_empate DECIMAL(5,2) NOT NULL,
    puntos_derrota DECIMAL(5,2) NOT NULL,
    descripcion TEXT
);

-- =============================================================================
-- 2. USUARIOS Y AUTENTICACIÓN
-- =============================================================================

-- 4. usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    contrasena_hash TEXT NOT NULL,
    nombre_completo VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    fecha_nacimiento DATE,
    foto_perfil_url TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    rol_id INT NOT NULL,
    esta_activo BOOLEAN DEFAULT TRUE,
    email_verificado BOOLEAN DEFAULT FALSE,
    ultimo_acceso TIMESTAMP NULL,
    CONSTRAINT fk_usuarios_rol FOREIGN KEY (rol_id) REFERENCES roles(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
);

-- 5. tokens_verificacion_email
CREATE TABLE tokens_verificacion_email (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    expira_en TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP + INTERVAL 24 HOUR),
    usado BOOLEAN DEFAULT FALSE,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_token_verif_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

-- 6. tokens_recuperacion
CREATE TABLE tokens_recuperacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    expira_en TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP + INTERVAL 1 HOUR),
    usado BOOLEAN DEFAULT FALSE,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_token_recup_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

-- 7. sesiones_activas
CREATE TABLE sesiones_activas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    token_sesion VARCHAR(255) UNIQUE NOT NULL,
    ip_origen VARCHAR(45) NOT NULL,
    user_agent TEXT,
    ultima_actividad TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expira_en TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP + INTERVAL 7 DAY),
    activa BOOLEAN DEFAULT TRUE,
    CONSTRAINT fk_sesion_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

-- 8. logs_acceso (histórico, no se borra aunque el usuario desaparezca)
CREATE TABLE logs_acceso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email_intentado VARCHAR(255),
    usuario_id INT NULL,
    exito BOOLEAN NOT NULL,
    ip_origen VARCHAR(45) NOT NULL,
    user_agent TEXT,
    mensaje_error TEXT,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_log_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
);

-- =============================================================================
-- 3. EQUIPOS Y GESTIÓN DE MIEMBROS
-- =============================================================================

-- 9. equipos
CREATE TABLE equipos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_equipo VARCHAR(100) UNIQUE NOT NULL,
    escudo_url TEXT,
    descripcion TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    creado_por INT NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    codigo_invitacion VARCHAR(20) UNIQUE,
    CONSTRAINT fk_equipo_creador FOREIGN KEY (creado_por) REFERENCES usuarios(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
);

-- 10. equipo_miembros (relación muchos a muchos)
CREATE TABLE equipo_miembros (
    equipo_id INT NOT NULL,
    usuario_id INT NOT NULL,
    fecha_union TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    numero_camiseta INT,
    posicion VARCHAR(50),
    es_activo BOOLEAN DEFAULT TRUE,
    PRIMARY KEY (equipo_id, usuario_id),
    CONSTRAINT fk_miembro_equipo FOREIGN KEY (equipo_id) REFERENCES equipos(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_miembro_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

-- 11. equipo_capitanes (un usuario puede ser capitán de varios equipos)
CREATE TABLE equipo_capitanes (
    equipo_id INT NOT NULL,
    usuario_id INT NOT NULL,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    asignado_por INT NOT NULL,
    es_capitan_principal BOOLEAN DEFAULT FALSE,
    PRIMARY KEY (equipo_id, usuario_id),
    CONSTRAINT fk_capitan_equipo FOREIGN KEY (equipo_id) REFERENCES equipos(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_capitan_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_capitan_asignador FOREIGN KEY (asignado_por) REFERENCES usuarios(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
);

-- 12. solicitudes_equipo
CREATE TABLE solicitudes_equipo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT NOT NULL,
    usuario_id INT NOT NULL,
    mensaje TEXT,
    estado VARCHAR(20) DEFAULT 'pendiente' CHECK (estado IN ('pendiente', 'aprobado', 'rechazado', 'cancelado')),
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_respuesta TIMESTAMP NULL,
    respondido_por INT NULL,
    UNIQUE KEY unique_solicitud (equipo_id, usuario_id),
    CONSTRAINT fk_solicitud_equipo FOREIGN KEY (equipo_id) REFERENCES equipos(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_solicitud_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_solicitud_respondido FOREIGN KEY (respondido_por) REFERENCES usuarios(id) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
);

-- =============================================================================
-- 4. NÚCLEO DE TORNEOS
-- =============================================================================

-- 13. torneos
CREATE TABLE torneos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    formato VARCHAR(50) NOT NULL CHECK (formato IN ('liga', 'eliminacion_directa', 'suizo')),
    estado VARCHAR(50) NOT NULL DEFAULT 'borrador' CHECK (estado IN ('borrador', 'inscripciones_abiertas', 'en_curso', 'finalizado', 'cancelado')),
    cupo_max_equipos INT NOT NULL CHECK (cupo_max_equipos >= 2),
    cupo_min_equipos INT DEFAULT 2 CHECK (cupo_min_equipos >= 2),
    fecha_inicio DATE,
    fecha_fin DATE,
    fecha_limite_inscripcion DATE,
    reglas TEXT,
    premios TEXT,
    ubicacion VARCHAR(255),
    organizador_id INT NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Campos multideportivos:
    modalidad_id INT NOT NULL DEFAULT 1,
    sistema_puntuacion_id INT NULL,
    puntos_victoria DECIMAL(5,2) DEFAULT 3,
    puntos_empate DECIMAL(5,2) DEFAULT 1,
    puntos_derrota DECIMAL(5,2) DEFAULT 0,
    tipo_resultado ENUM('goles', 'puntos', 'rondas', 'booleano') DEFAULT 'goles',
    mejor_de INT DEFAULT 1 COMMENT 'Número de mapas/partidas para ganar la llave (eliminación directa)',
    CONSTRAINT fk_torneo_organizador FOREIGN KEY (organizador_id) REFERENCES usuarios(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    CONSTRAINT fk_torneo_modalidad FOREIGN KEY (modalidad_id) REFERENCES modalidades(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    CONSTRAINT fk_torneo_sistema_puntuacion FOREIGN KEY (sistema_puntuacion_id) REFERENCES sistemas_puntuacion(id) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
    CONSTRAINT fechas_validas CHECK (fecha_inicio <= fecha_fin)
);

-- 14. torneo_cambio_estado (historial)
CREATE TABLE torneo_cambio_estado (
    id INT AUTO_INCREMENT PRIMARY KEY,
    torneo_id INT NOT NULL,
    estado_anterior VARCHAR(50),
    estado_nuevo VARCHAR(50) NOT NULL,
    motivo TEXT,
    usuario_id INT NOT NULL,
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cambio_torneo FOREIGN KEY (torneo_id) REFERENCES torneos(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_cambio_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
);

-- 15. participantes_torneo (abstracción: puede ser equipo o usuario individual)
CREATE TABLE participantes_torneo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    torneo_id INT NOT NULL,
    tipo ENUM('equipo', 'usuario') NOT NULL,
    referencia_id INT NOT NULL COMMENT 'ID de equipos o usuarios según tipo',
    nombre VARCHAR(255) NOT NULL COMMENT 'Denormalizado para mostrar rápido',
    estado VARCHAR(50) DEFAULT 'pendiente' CHECK (estado IN ('pendiente', 'confirmado', 'rechazado', 'cancelado')),
    fecha_inscripcion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    confirmado_por INT NULL,
    fecha_confirmacion TIMESTAMP NULL,
    UNIQUE KEY unico_participante (torneo_id, tipo, referencia_id),
    CONSTRAINT fk_participante_torneo FOREIGN KEY (torneo_id) REFERENCES torneos(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_participante_confirmador FOREIGN KEY (confirmado_por) REFERENCES usuarios(id) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
    -- Nota: no podemos poner FK a equipos o usuarios porque referencia_id puede apuntar a dos tablas distintas.
);

-- 16. torneo_encuentros (enfrentamientos)
CREATE TABLE torneo_encuentros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    torneo_id INT NOT NULL,
    ronda INT NOT NULL,
    participante_local_id INT NULL,
    participante_visitante_id INT NULL,
    fecha_hora_programada TIMESTAMP NULL,
    cancha VARCHAR(100),
    resultado_local DECIMAL(10,2) DEFAULT NULL,
    resultado_visitante DECIMAL(10,2) DEFAULT NULL,
    estado VARCHAR(50) DEFAULT 'programado' CHECK (estado IN ('programado', 'en_curso', 'finalizado', 'cancelado', 'aplazado')),
    participante_ganador_id INT NULL,
    siguiente_encuentro_id INT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultima_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modificado_por_usuario_id INT NULL,
    CONSTRAINT fk_encuentro_torneo FOREIGN KEY (torneo_id) REFERENCES torneos(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_encuentro_local FOREIGN KEY (participante_local_id) REFERENCES participantes_torneo(id) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
    CONSTRAINT fk_encuentro_visitante FOREIGN KEY (participante_visitante_id) REFERENCES participantes_torneo(id) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
    CONSTRAINT fk_encuentro_ganador FOREIGN KEY (participante_ganador_id) REFERENCES participantes_torneo(id) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
    CONSTRAINT fk_encuentro_siguiente FOREIGN KEY (siguiente_encuentro_id) REFERENCES torneo_encuentros(id) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
    CONSTRAINT fk_encuentro_modificador FOREIGN KEY (modificado_por_usuario_id) REFERENCES usuarios(id) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
);

-- 17. torneo_posiciones (tabla de posiciones actualizada)
CREATE TABLE torneo_posiciones (
    torneo_id INT NOT NULL,
    participante_id INT NOT NULL,
    partidos_jugados INT DEFAULT 0,
    partidos_ganados INT DEFAULT 0,
    partidos_empatados INT DEFAULT 0,
    partidos_perdidos INT DEFAULT 0,
    puntos_favor DECIMAL(10,2) DEFAULT 0 COMMENT 'Goles a favor, puntos a favor, rondas ganadas, etc.',
    puntos_contra DECIMAL(10,2) DEFAULT 0 COMMENT 'Goles en contra, puntos en contra, rondas perdidas',
    diferencia_goles DECIMAL(10,2) GENERATED ALWAYS AS (puntos_favor - puntos_contra) STORED,
    puntos INT DEFAULT 0,
    sanciones_puntos INT DEFAULT 0,
    ultima_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (torneo_id, participante_id),
    CONSTRAINT fk_posiciones_torneo FOREIGN KEY (torneo_id) REFERENCES torneos(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_posiciones_participante FOREIGN KEY (participante_id) REFERENCES participantes_torneo(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

-- 18. torneo_suizo_parejas (evita repetir enfrentamientos)
CREATE TABLE torneo_suizo_parejas (
    torneo_id INT NOT NULL,
    participante_a_id INT NOT NULL,
    participante_b_id INT NOT NULL,
    ronda INT NOT NULL,
    ya_se_enfrentaron BOOLEAN DEFAULT TRUE,
    fecha_encuentro TIMESTAMP NULL,
    PRIMARY KEY (torneo_id, participante_a_id, participante_b_id),
    CONSTRAINT fk_suizo_torneo FOREIGN KEY (torneo_id) REFERENCES torneos(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_suizo_participanteA FOREIGN KEY (participante_a_id) REFERENCES participantes_torneo(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_suizo_participanteB FOREIGN KEY (participante_b_id) REFERENCES participantes_torneo(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

-- 19. resultados_detalle (datos extra del encuentro, ej. kills, motivo de victoria)
CREATE TABLE resultados_detalle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    encuentro_id INT NOT NULL,
    tipo_dato VARCHAR(50) NOT NULL COMMENT 'kills_local, kills_visitante, motivo, tiempo_juego, rondas_ganadas_local, etc.',
    valor VARCHAR(255) NOT NULL,
    CONSTRAINT fk_detalle_encuentro FOREIGN KEY (encuentro_id) REFERENCES torneo_encuentros(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

-- 20. torneo_config (configuración extra clave-valor por torneo)
CREATE TABLE torneo_config (
    torneo_id INT NOT NULL,
    clave VARCHAR(100) NOT NULL,
    valor TEXT NOT NULL,
    PRIMARY KEY (torneo_id, clave),
    CONSTRAINT fk_config_torneo FOREIGN KEY (torneo_id) REFERENCES torneos(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

-- =============================================================================
-- 5. NOTIFICACIONES
-- =============================================================================

-- 21. notificaciones
CREATE TABLE notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo VARCHAR(50) NOT NULL CHECK (tipo IN ('info', 'exito', 'advertencia', 'error', 'partido', 'resultado', 'inscripcion')),
    titulo VARCHAR(255) NOT NULL,
    mensaje TEXT NOT NULL,
    enlace_relacionado VARCHAR(500),
    leido BOOLEAN DEFAULT FALSE,
    fecha_lectura TIMESTAMP NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notificacion_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

-- =============================================================================
-- 6. SEGURIDAD Y AUDITORÍA
-- =============================================================================

-- 22. auditoria_cambios (registro de modificaciones en tablas críticas)
CREATE TABLE auditoria_cambios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tabla_afectada VARCHAR(100) NOT NULL,
    registro_id INT NOT NULL,
    accion VARCHAR(20) NOT NULL CHECK (accion IN ('INSERT', 'UPDATE', 'DELETE')),
    usuario_id INT NULL,
    datos_viejos JSON,
    datos_nuevos JSON,
    ip_origen VARCHAR(45),
    user_agent TEXT,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_auditoria_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
);

-- 23. politicas_contrasenas (solo una fila activa)
CREATE TABLE politicas_contrasenas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    longitud_minima INT DEFAULT 8,
    requiere_mayuscula BOOLEAN DEFAULT TRUE,
    requiere_minuscula BOOLEAN DEFAULT TRUE,
    requiere_numero BOOLEAN DEFAULT TRUE,
    requiere_caracter_especial BOOLEAN DEFAULT TRUE,
    expiracion_dias INT DEFAULT 90,
    historial_cantidad INT DEFAULT 5,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_por INT NULL,
    CONSTRAINT fk_politicas_actualizador FOREIGN KEY (actualizado_por) REFERENCES usuarios(id) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
);

-- 24. historial_contrasenas (últimas N contraseñas por usuario)
CREATE TABLE historial_contrasenas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    hash_anterior TEXT NOT NULL,
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_historial_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

-- 25. logs_actividad (registro de acciones del usuario)
CREATE TABLE logs_actividad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    accion VARCHAR(100) NOT NULL,
    descripcion TEXT,
    ip_origen VARCHAR(45),
    user_agent TEXT,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_logact_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
);

-- =============================================================================
-- 7. ÍNDICES ADICIONALES PARA RENDIMIENTO
-- =============================================================================

-- Torneos
CREATE INDEX idx_torneos_organizador ON torneos(organizador_id);
CREATE INDEX idx_torneos_estado ON torneos(estado);
CREATE INDEX idx_torneos_fecha_inicio ON torneos(fecha_inicio);

-- Encuentros
CREATE INDEX idx_encuentros_torneo ON torneo_encuentros(torneo_id);
CREATE INDEX idx_encuentros_ronda ON torneo_encuentros(ronda);
CREATE INDEX idx_encuentros_fecha ON torneo_encuentros(fecha_hora_programada);

-- Posiciones
CREATE INDEX idx_posiciones_puntos ON torneo_posiciones(puntos DESC);

-- Participantes
CREATE INDEX idx_participantes_torneo ON participantes_torneo(torneo_id);
CREATE INDEX idx_participantes_referencia ON participantes_torneo(tipo, referencia_id);

-- Auditoría
CREATE INDEX idx_auditoria_tabla ON auditoria_cambios(tabla_afectada, registro_id);
CREATE INDEX idx_auditoria_fecha ON auditoria_cambios(fecha_hora);

-- =============================================================================
-- 8. DATOS INICIALES (SEEDERS)
-- =============================================================================

-- Insertar roles
INSERT INTO roles (nombre_rol, descripcion, nivel_permiso) VALUES 
('Administrador General', 'Control total del sistema', 3),
('Organizador', 'Puede crear y administrar torneos', 2),
('Participante', 'Puede unirse a equipos y participar', 1),
('Usuario Publico', 'Solo visualización', 0);

-- Insertar modalidades
INSERT INTO modalidades (nombre, descripcion) VALUES 
('individual', 'Participantes individuales (una persona por participante)'),
('equipos', 'Participantes son equipos de múltiples miembros');

-- Insertar sistemas de puntuación
INSERT INTO sistemas_puntuacion (nombre, puntos_victoria, puntos_empate, puntos_derrota, descripcion) VALUES 
('estandar_3_1_0', 3, 1, 0, 'Estándar: 3 puntos por victoria, 1 por empate, 0 por derrota'),
('ajedrez', 1, 0.5, 0, 'Sistema de ajedrez: 1 punto por victoria, 0.5 por tablas, 0 por derrota'),
('puntos_mapa', 2, 1, 0, 'Para videojuegos: 2 puntos por victoria de mapa, 1 por empate (raro), 0 derrota');

-- Insertar política de contraseñas por defecto
INSERT INTO politicas_contrasenas (longitud_minima, requiere_mayuscula, requiere_minuscula, requiere_numero, requiere_caracter_especial, expiracion_dias, historial_cantidad) VALUES 
(8, 1, 1, 1, 1, 90, 5);
