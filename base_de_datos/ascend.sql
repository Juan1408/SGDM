-- =============================================================================
-- SCRIPT SQL COMPLETO – BASE DE DATOS ASCEND (VERSIÓN MULTIDEPORTIVA MODULAR)
-- Normalización estricta en 3ra Forma Normal (3FN) con Herencia / Subtipos (1:1)
-- =============================================================================

-- =============================================================================
-- EXPLICACIÓN DE CONCEPTOS Y DECISIONES ARQUITECTÓNICAS:
--
-- 1. TERCERA FORMA NORMAL (3FN):
--    - 1FN: Todos los atributos son atómicos (sin listas separadas por comas ni grupos repetitivos).
--    - 2FN: Cumple 1FN y no existen dependencias funcionales parciales de claves compuestas.
--    - 3FN: Cumple 2FN y no existen dependencias transitivas; cada columna no clave
--          depende directa y exclusivamente de la clave primaria (PK).
--
-- 2. PATRÓN DE HERENCIA DE TABLAS (Class Table Inheritance / Subtipos 1 a 1):
--    - Tabla Padre: 'usuarios' (Identidad universal, credenciales, email y rol).
--    - Tablas Hijas Especializadas: 'perfiles_jugadores' y 'perfiles_organizadores'.
--    - Clave Primaria Compartida: En las tablas hijas, 'usuario_id' es simultáneamente
--      PRIMARY KEY y FOREIGN KEY hacia 'usuarios(id)' con ON DELETE CASCADE.
--      Esto garantiza matemáticamente que no haya perfiles huérfanos ni campos con
--      valores NULL inaplicables (evita el antipatrón de columnas fantasma).
--
-- 3. POLÍTICAS DE INTEGRIDAD REFERENCIAL (Cascadas):
--    - ON DELETE CASCADE: Al eliminar el registro padre, se eliminan automáticamente
--      sus registros dependientes directos (ej. Usuario -> Perfiles, Sesiones, Tokens;
--      Torneo -> Encuentros, Posiciones, Actividades).
--    - ON DELETE SET NULL: Al eliminar el padre, la clave foránea pasa a valer NULL
--      (vital para registros históricos como logs de auditoría o quién confirmó un participante).
--    - ON DELETE RESTRICT (Por defecto): Impide la eliminación si existen registros
--      relacionados críticos (ej. no se puede borrar un Rol si hay Usuarios asignados).
-- =============================================================================

CREATE DATABASE IF NOT EXISTS ascend CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE ascend;

-- Desactivar temporalmente la verificación de claves foráneas para recrear sin conflictos de orden
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- ELIMINACIÓN DE TABLAS EN ORDEN INVERSO DE DEPENDENCIAS
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS newsletter_subscriptores;

DROP TABLE IF EXISTS torneo_comentarios;

DROP TABLE IF EXISTS torneo_actividades;

DROP TABLE IF EXISTS usuario_logros;

DROP TABLE IF EXISTS logros;

DROP TABLE IF EXISTS perfiles_organizadores;

DROP TABLE IF EXISTS perfiles_jugadores;

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

DROP TABLE IF EXISTS rol_permisos;

DROP TABLE IF EXISTS permisos;

DROP TABLE IF EXISTS juegos;

DROP TABLE IF EXISTS roles;

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================================
-- 1. TABLAS MAESTRAS (Catálogos del Sistema)
-- =============================================================================

-- 1.1 permisos: Catálogo de privilegios granulares para control de acceso (RBAC)
CREATE TABLE permisos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_permiso VARCHAR(50) UNIQUE NOT NULL,
    descripcion TEXT
) ENGINE = InnoDB;

-- 1.2 roles: Catálogo de perfiles de acceso (Admin, Organizador, Participante, Público)
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) UNIQUE NOT NULL,
    descripcion TEXT,
    nivel_permiso INT DEFAULT 0
) ENGINE = InnoDB;

-- 1.3 rol_permisos: Relación muchos a muchos (N:M) entre Roles y Permisos
CREATE TABLE rol_permisos (
    rol_id INT NOT NULL,
    permiso_id INT NOT NULL,
    asignado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (rol_id, permiso_id),
    CONSTRAINT fk_rolpermiso_rol FOREIGN KEY (rol_id) REFERENCES roles (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_rolpermiso_permiso FOREIGN KEY (permiso_id) REFERENCES permisos (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- 1.4 modalidades: Catálogo de modalidades de competencia (Individual vs Equipos)
CREATE TABLE modalidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) UNIQUE NOT NULL,
    descripcion TEXT
) ENGINE = InnoDB;

-- 1.5 sistemas_puntuacion: Reglas de asignación de puntos por victoria, empate y derrota
CREATE TABLE sistemas_puntuacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) UNIQUE NOT NULL,
    puntos_victoria DECIMAL(5, 2) NOT NULL,
    puntos_empate DECIMAL(5, 2) NOT NULL,
    puntos_derrota DECIMAL(5, 2) NOT NULL,
    descripcion TEXT
) ENGINE = InnoDB;

-- 1.6 juegos: Catálogo de disciplinas deportivas físicas y digitales (eSports)
CREATE TABLE juegos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) UNIQUE NOT NULL,
    categoria VARCHAR(50) NOT NULL COMMENT 'esport-shooter, esport-moba, deporte-fisico, juego-mesa',
    formato_equipo_defecto INT DEFAULT 1 COMMENT 'Cantidad habitual de integrantes por equipo',
    puntos_victoria DECIMAL(5, 2) DEFAULT 3.00,
    puntos_empate DECIMAL(5, 2) DEFAULT 1.00,
    puntos_derrota DECIMAL(5, 2) DEFAULT 0.00,
    activo BOOLEAN DEFAULT TRUE,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB;

-- =============================================================================
-- 2. USUARIOS, AUTENTICACIÓN Y HERENCIA DE PERFILES (1:1 en 3FN)
-- =============================================================================

-- 2.1 usuarios: TABLA PADRE (Superclase de identidad y credenciales)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    contrasena_hash TEXT NOT NULL,
    nombre_completo VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    foto_perfil_url TEXT,
    rol_id INT NOT NULL,
    esta_activo BOOLEAN DEFAULT TRUE,
    email_verificado BOOLEAN DEFAULT FALSE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL,
    CONSTRAINT fk_usuarios_rol FOREIGN KEY (rol_id) REFERENCES roles (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB;

-- 2.2 perfiles_jugadores: TABLA HIJA (Especialización para Jugadores y Competidores)
-- Relación 1 a 1 estricta: usuario_id es PRIMARY KEY y FOREIGN KEY simultáneamente.
CREATE TABLE perfiles_jugadores (
    usuario_id INT PRIMARY KEY,
    apodo_gamertag VARCHAR(50),
    bio TEXT,
    banner_url TEXT,
    nivel INT DEFAULT 1,
    experiencia_puntos INT DEFAULT 0,
    pais VARCHAR(100) DEFAULT 'Uruguay',
    ciudad VARCHAR(100),
    fecha_nacimiento DATE,
    discord_tag VARCHAR(100),
    instagram_url VARCHAR(255),
    twitter_url VARCHAR(255),
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_perfil_jugador_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- 2.3 perfiles_organizadores: TABLA HIJA (Especialización para Organizadores de Torneos)
-- Relación 1 a 1 estricta: usuario_id es PRIMARY KEY y FOREIGN KEY simultáneamente.
CREATE TABLE perfiles_organizadores (
    usuario_id INT PRIMARY KEY,
    nombre_organizacion VARCHAR(150) NOT NULL DEFAULT 'Organizador Independiente',
    bio_organizacion TEXT,
    localidad VARCHAR(100),
    telefono_contacto VARCHAR(50),
    sitio_web VARCHAR(255),
    verificado_oficial BOOLEAN DEFAULT FALSE,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_perfil_organizador_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- 2.4 tokens_verificacion_email: Tokens de activación de cuenta por correo electrónico
CREATE TABLE tokens_verificacion_email (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    expira_en TIMESTAMP NOT NULL DEFAULT(
        CURRENT_TIMESTAMP + INTERVAL 24 HOUR
    ),
    usado BOOLEAN DEFAULT FALSE,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_token_verif_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- 2.5 tokens_recuperacion: Tokens temporales para restablecimiento de contraseña olvidada
CREATE TABLE tokens_recuperacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    expira_en TIMESTAMP NOT NULL DEFAULT(
        CURRENT_TIMESTAMP + INTERVAL 1 HOUR
    ),
    usado BOOLEAN DEFAULT FALSE,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_token_recup_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- 2.6 sesiones_activas: Control de sesiones concurrentes e información de dispositivos
CREATE TABLE sesiones_activas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    token_sesion VARCHAR(255) UNIQUE NOT NULL,
    ip_origen VARCHAR(45) NOT NULL,
    user_agent TEXT,
    ultima_actividad TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expira_en TIMESTAMP NOT NULL DEFAULT(
        CURRENT_TIMESTAMP + INTERVAL 7 DAY
    ),
    activa BOOLEAN DEFAULT TRUE,
    CONSTRAINT fk_sesion_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- 2.7 logs_acceso: Registro inmutable de intentos de inicio de sesión (Auditoría de Seguridad)
CREATE TABLE logs_acceso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email_intentado VARCHAR(255),
    usuario_id INT NULL,
    exito BOOLEAN NOT NULL,
    ip_origen VARCHAR(45) NOT NULL,
    user_agent TEXT,
    mensaje_error TEXT,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_log_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB;

-- =============================================================================
-- 3. GESTIÓN DE EQUIPOS Y MEMBRESÍAS
-- =============================================================================

-- 3.1 equipos: Entidades de competencia colectiva
CREATE TABLE equipos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_equipo VARCHAR(100) UNIQUE NOT NULL,
    escudo_url TEXT,
    banner_url TEXT,
    descripcion TEXT,
    ubicacion VARCHAR(100),
    anio_fundacion INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    creado_por INT NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    codigo_invitacion VARCHAR(20) UNIQUE,
    CONSTRAINT fk_equipo_creador FOREIGN KEY (creado_por) REFERENCES usuarios (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB;

-- 3.2 equipo_miembros: Relación muchos a muchos (N:M) entre Equipos y Jugadores
CREATE TABLE equipo_miembros (
    equipo_id INT NOT NULL,
    usuario_id INT NOT NULL,
    fecha_union TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    numero_camiseta INT,
    posicion VARCHAR(50),
    es_activo BOOLEAN DEFAULT TRUE,
    PRIMARY KEY (equipo_id, usuario_id),
    CONSTRAINT fk_miembro_equipo FOREIGN KEY (equipo_id) REFERENCES equipos (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_miembro_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- 3.3 equipo_capitanes: Asignación de líderes y capitanes a un equipo
CREATE TABLE equipo_capitanes (
    equipo_id INT NOT NULL,
    usuario_id INT NOT NULL,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    asignado_por INT NOT NULL,
    es_capitan_principal BOOLEAN DEFAULT FALSE,
    PRIMARY KEY (equipo_id, usuario_id),
    CONSTRAINT fk_capitan_equipo FOREIGN KEY (equipo_id) REFERENCES equipos (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_capitan_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_capitan_asignador FOREIGN KEY (asignado_por) REFERENCES usuarios (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB;

-- 3.4 solicitudes_equipo: Solicitudes de ingreso a escuadras con flujo de aprobación
CREATE TABLE solicitudes_equipo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT NOT NULL,
    usuario_id INT NOT NULL,
    mensaje TEXT,
    estado VARCHAR(20) DEFAULT 'pendiente' CHECK (
        estado IN (
            'pendiente',
            'aprobado',
            'rechazado',
            'cancelado'
        )
    ),
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_respuesta TIMESTAMP NULL,
    respondido_por INT NULL,
    UNIQUE KEY unique_solicitud (equipo_id, usuario_id),
    CONSTRAINT fk_solicitud_equipo FOREIGN KEY (equipo_id) REFERENCES equipos (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_solicitud_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_solicitud_respondido FOREIGN KEY (respondido_por) REFERENCES usuarios (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB;

-- =============================================================================
-- 4. NÚCLEO DE TORNEOS Y COMPETICIONES
-- =============================================================================

-- 4.1 torneos: Estructura principal de una competencia deportiva o eSport
CREATE TABLE torneos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    juego_id INT NOT NULL,
    descripcion TEXT,
    formato VARCHAR(50) NOT NULL CHECK (
        formato IN (
            'liga',
            'eliminacion_directa',
            'suizo'
        )
    ),
    estado VARCHAR(50) NOT NULL DEFAULT 'borrador' CHECK (
        estado IN (
            'borrador',
            'inscripciones_abiertas',
            'en_curso',
            'finalizado',
            'cancelado'
        )
    ),
    cupo_max_equipos INT NOT NULL CHECK (cupo_max_equipos >= 2),
    cupo_min_equipos INT DEFAULT 2 CHECK (cupo_min_equipos >= 2),
    fecha_inicio_inscripcion DATE,
    fecha_limite_inscripcion DATE,
    fecha_inicio DATE,
    fecha_fin DATE,
    reglas TEXT,
    premios TEXT,
    ubicacion VARCHAR(255),
    localidad VARCHAR(100),
    comunidad VARCHAR(100),
    banner_url TEXT,
    stream_url VARCHAR(255),
    organizador_id INT NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    -- Configuración deportiva multideporte:
    modalidad_id INT NOT NULL DEFAULT 1,
    sistema_puntuacion_id INT NULL,
    puntos_victoria DECIMAL(5, 2) DEFAULT 3.00,
    puntos_empate DECIMAL(5, 2) DEFAULT 1.00,
    puntos_derrota DECIMAL(5, 2) DEFAULT 0.00,
    tipo_resultado ENUM(
        'goles',
        'puntos',
        'rondas',
        'booleano'
    ) DEFAULT 'goles',
    mejor_de INT DEFAULT 1 COMMENT 'Número de mapas o partidas para ganar la serie (BO1, BO3, BO5)',
    CONSTRAINT fk_torneo_organizador FOREIGN KEY (organizador_id) REFERENCES usuarios (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_torneo_juego FOREIGN KEY (juego_id) REFERENCES juegos (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_torneo_modalidad FOREIGN KEY (modalidad_id) REFERENCES modalidades (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_torneo_sistema_puntuacion FOREIGN KEY (sistema_puntuacion_id) REFERENCES sistemas_puntuacion (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fechas_validas CHECK (
        fecha_inicio IS NULL
        OR fecha_fin IS NULL
        OR fecha_inicio <= fecha_fin
    )
) ENGINE = InnoDB;

-- 4.2 torneo_cambio_estado: Historial inmutable de transiciones de estado de un torneo
CREATE TABLE torneo_cambio_estado (
    id INT AUTO_INCREMENT PRIMARY KEY,
    torneo_id INT NOT NULL,
    estado_anterior VARCHAR(50),
    estado_nuevo VARCHAR(50) NOT NULL,
    motivo TEXT,
    usuario_id INT NOT NULL,
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cambio_torneo FOREIGN KEY (torneo_id) REFERENCES torneos (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_cambio_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB;

-- 4.3 participantes_torneo: Abstracción polimórfica de competidores (Equipos o Jugadores individuales)
CREATE TABLE participantes_torneo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    torneo_id INT NOT NULL,
    tipo ENUM('equipo', 'usuario') NOT NULL,
    referencia_id INT NOT NULL COMMENT 'ID de la tabla equipos o usuarios según el tipo',
    nombre VARCHAR(255) NOT NULL COMMENT 'Nombre denormalizado para rendimiento visual rápido',
    estado VARCHAR(50) DEFAULT 'pendiente' CHECK (
        estado IN (
            'pendiente',
            'confirmado',
            'rechazado',
            'cancelado'
        )
    ),
    fecha_inscripcion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    confirmado_por INT NULL,
    fecha_confirmacion TIMESTAMP NULL,
    UNIQUE KEY unico_participante (
        torneo_id,
        tipo,
        referencia_id
    ),
    CONSTRAINT fk_participante_torneo FOREIGN KEY (torneo_id) REFERENCES torneos (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_participante_confirmador FOREIGN KEY (confirmado_por) REFERENCES usuarios (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB;

-- 4.4 torneo_encuentros: Fixture, partidos y llaves de eliminación
CREATE TABLE torneo_encuentros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    torneo_id INT NOT NULL,
    ronda INT NOT NULL,
    participante_local_id INT NULL,
    participante_visitante_id INT NULL,
    fecha_hora_programada TIMESTAMP NULL,
    cancha VARCHAR(100),
    resultado_local DECIMAL(10, 2) DEFAULT NULL,
    resultado_visitante DECIMAL(10, 2) DEFAULT NULL,
    estado VARCHAR(50) DEFAULT 'programado' CHECK (
        estado IN (
            'programado',
            'en_curso',
            'finalizado',
            'cancelado',
            'aplazado'
        )
    ),
    participante_ganador_id INT NULL,
    siguiente_encuentro_id INT NULL,
    canal_transmision VARCHAR(100),
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultima_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    modificado_por_usuario_id INT NULL,
    CONSTRAINT fk_encuentro_torneo FOREIGN KEY (torneo_id) REFERENCES torneos (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_encuentro_local FOREIGN KEY (participante_local_id) REFERENCES participantes_torneo (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_encuentro_visitante FOREIGN KEY (participante_visitante_id) REFERENCES participantes_torneo (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_encuentro_ganador FOREIGN KEY (participante_ganador_id) REFERENCES participantes_torneo (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_encuentro_siguiente FOREIGN KEY (siguiente_encuentro_id) REFERENCES torneo_encuentros (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_encuentro_modificador FOREIGN KEY (modificado_por_usuario_id) REFERENCES usuarios (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB;

-- 4.5 torneo_posiciones: Tabla de clasificación y estadísticas actualizadas en tiempo real
CREATE TABLE torneo_posiciones (
    torneo_id INT NOT NULL,
    participante_id INT NOT NULL,
    partidos_jugados INT DEFAULT 0,
    partidos_ganados INT DEFAULT 0,
    partidos_empatados INT DEFAULT 0,
    partidos_perdidos INT DEFAULT 0,
    puntos_favor DECIMAL(10, 2) DEFAULT 0.00 COMMENT 'Goles/Puntos/Rondas a favor',
    puntos_contra DECIMAL(10, 2) DEFAULT 0.00 COMMENT 'Goles/Puntos/Rondas en contra',
    diferencia_goles DECIMAL(10, 2) GENERATED ALWAYS AS (puntos_favor - puntos_contra) STORED,
    puntos DECIMAL(10, 2) DEFAULT 0.00,
    sanciones_puntos INT DEFAULT 0,
    ultima_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (torneo_id, participante_id),
    CONSTRAINT fk_posiciones_torneo FOREIGN KEY (torneo_id) REFERENCES torneos (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_posiciones_participante FOREIGN KEY (participante_id) REFERENCES participantes_torneo (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- 4.6 torneo_suizo_parejas: Historial de emparejamientos para formato de Sistema Suizo
CREATE TABLE torneo_suizo_parejas (
    torneo_id INT NOT NULL,
    participante_a_id INT NOT NULL,
    participante_b_id INT NOT NULL,
    ronda INT NOT NULL,
    ya_se_enfrentaron BOOLEAN DEFAULT TRUE,
    fecha_encuentro TIMESTAMP NULL,
    PRIMARY KEY (
        torneo_id,
        participante_a_id,
        participante_b_id
    ),
    CONSTRAINT fk_suizo_torneo FOREIGN KEY (torneo_id) REFERENCES torneos (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_suizo_participanteA FOREIGN KEY (participante_a_id) REFERENCES participantes_torneo (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_suizo_participanteB FOREIGN KEY (participante_b_id) REFERENCES participantes_torneo (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT distintos CHECK (
        participante_a_id != participante_b_id
    )
) ENGINE = InnoDB;

-- 4.7 resultados_detalle: Métricas específicas del encuentro (Kills, MVP, tiempo, faltas)
CREATE TABLE resultados_detalle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    encuentro_id INT NOT NULL,
    tipo_dato VARCHAR(50) NOT NULL COMMENT 'kills_local, kills_visitante, motivo_victoria, duracion_minutos',
    valor VARCHAR(255) NOT NULL,
    CONSTRAINT fk_detalle_encuentro FOREIGN KEY (encuentro_id) REFERENCES torneo_encuentros (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- 4.8 torneo_config: Pares clave-valor para configuraciones dinámicas y personalizadas
CREATE TABLE torneo_config (
    torneo_id INT NOT NULL,
    clave VARCHAR(100) NOT NULL,
    valor TEXT NOT NULL,
    PRIMARY KEY (torneo_id, clave),
    CONSTRAINT fk_config_torneo FOREIGN KEY (torneo_id) REFERENCES torneos (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- 4.9 torneo_actividades: Agenda inicial de actividades del torneo (Reuniones, Premiaciones)
CREATE TABLE torneo_actividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    torneo_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    tipo ENUM(
        'administrativo',
        'reunion',
        'competencia',
        'premiacion'
    ) DEFAULT 'competencia',
    fecha DATE NOT NULL,
    hora TIME NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_actividades_torneo FOREIGN KEY (torneo_id) REFERENCES torneos (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- 4.10 torneo_comentarios: Interacción comunitaria y consultas públicas en el torneo
CREATE TABLE torneo_comentarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    torneo_id INT NOT NULL,
    usuario_id INT NULL,
    nombre_autor VARCHAR(100) NOT NULL,
    email_autor VARCHAR(255) NOT NULL,
    comentario TEXT NOT NULL,
    aprobado BOOLEAN DEFAULT TRUE,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_comentario_torneo FOREIGN KEY (torneo_id) REFERENCES torneos (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_comentario_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB;

-- =============================================================================
-- 5. GAMIFICACIÓN: TROFEOS Y LOGROS DE JUGADORES
-- =============================================================================

-- 5.1 logros: Catálogo de medallas y objetivos desbloqueables
CREATE TABLE logros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    icono VARCHAR(100) DEFAULT 'fa-trophy',
    puntos_recompensa INT DEFAULT 50,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB;

-- 5.2 usuario_logros: Relación muchos a muchos (N:M) de logros conseguidos por jugadores
CREATE TABLE usuario_logros (
    usuario_id INT NOT NULL,
    logro_id INT NOT NULL,
    estado ENUM(
        'bloqueado',
        'en_curso',
        'desbloqueado'
    ) DEFAULT 'desbloqueado',
    progreso_actual INT DEFAULT 1,
    progreso_objetivo INT DEFAULT 1,
    fecha_desbloqueo TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (usuario_id, logro_id),
    CONSTRAINT fk_usuariologro_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_usuariologro_logro FOREIGN KEY (logro_id) REFERENCES logros (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- =============================================================================
-- 6. COMUNICACIONES Y MARKETING
-- =============================================================================

-- 6.1 notificaciones: Bandeja de entrada masiva e individual
CREATE TABLE notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo VARCHAR(50) NOT NULL CHECK (
        tipo IN (
            'info',
            'exito',
            'advertencia',
            'error',
            'partido',
            'resultado',
            'inscripcion'
        )
    ),
    titulo VARCHAR(255) NOT NULL,
    mensaje TEXT NOT NULL,
    enlace_relacionado VARCHAR(500),
    leido BOOLEAN DEFAULT FALSE,
    fecha_lectura TIMESTAMP NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notificacion_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- 6.2 newsletter_subscriptores: Boletín de noticias público
CREATE TABLE newsletter_subscriptores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    fecha_suscripcion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB;

-- =============================================================================
-- 7. SEGURIDAD Y AUDITORÍA
-- =============================================================================

-- 7.1 auditoria_cambios: Bitácora inmutable de modificaciones en tablas críticas
CREATE TABLE auditoria_cambios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tabla_afectada VARCHAR(100) NOT NULL,
    registro_id INT NOT NULL,
    accion VARCHAR(20) NOT NULL CHECK (
        accion IN ('INSERT', 'UPDATE', 'DELETE')
    ),
    usuario_id INT NULL,
    datos_viejos JSON,
    datos_nuevos JSON,
    ip_origen VARCHAR(45),
    user_agent TEXT,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_auditoria_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB;

-- 7.2 politicas_contrasenas: Parámetros de seguridad de claves del sistema
CREATE TABLE politicas_contrasenas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    longitud_minima INT DEFAULT 8,
    requiere_mayuscula BOOLEAN DEFAULT TRUE,
    requiere_minuscula BOOLEAN DEFAULT TRUE,
    requiere_numero BOOLEAN DEFAULT TRUE,
    requiere_caracter_especial BOOLEAN DEFAULT TRUE,
    expiracion_dias INT DEFAULT 90,
    historial_cantidad INT DEFAULT 5,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    actualizado_por INT NULL,
    CONSTRAINT fk_politicas_actualizador FOREIGN KEY (actualizado_por) REFERENCES usuarios (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB;

-- 7.3 historial_contrasenas: Histórico para impedir reutilización de contraseñas
CREATE TABLE historial_contrasenas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    hash_anterior TEXT NOT NULL,
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_historial_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- 7.4 logs_actividad: Registro de operaciones del usuario en la plataforma
CREATE TABLE logs_actividad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    accion VARCHAR(100) NOT NULL,
    descripcion TEXT,
    ip_origen VARCHAR(45),
    user_agent TEXT,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_logact_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB;

-- =============================================================================
-- 8. ÍNDICES ESTRATÉGICOS PARA RENDIMIENTO (Performance Tuning)
-- =============================================================================

CREATE INDEX idx_usuarios_rol ON usuarios (rol_id);

CREATE INDEX idx_usuarios_email ON usuarios (email);

CREATE INDEX idx_torneos_organizador ON torneos (organizador_id);

CREATE INDEX idx_torneos_estado ON torneos (estado);

CREATE INDEX idx_torneos_fechas ON torneos (fecha_inicio, fecha_fin);

CREATE INDEX idx_encuentros_torneo ON torneo_encuentros (torneo_id);

CREATE INDEX idx_encuentros_ronda ON torneo_encuentros (ronda);

CREATE INDEX idx_encuentros_fecha ON torneo_encuentros (fecha_hora_programada);

CREATE INDEX idx_posiciones_puntos ON torneo_posiciones (
    puntos DESC,
    diferencia_goles DESC
);

CREATE INDEX idx_participantes_torneo ON participantes_torneo (torneo_id);

CREATE INDEX idx_participantes_referencia ON participantes_torneo (tipo, referencia_id);

CREATE INDEX idx_auditoria_tabla ON auditoria_cambios (tabla_afectada, registro_id);

CREATE INDEX idx_auditoria_fecha ON auditoria_cambios (fecha_hora);

-- =============================================================================
-- 9. DATOS SEMILLA (Seeders Iniciales para Pruebas y Producción)
-- =============================================================================

-- Permisos
INSERT INTO
    permisos (nombre_permiso, descripcion)
VALUES (
        'crear_torneos',
        'Permite crear nuevas competencias'
    ),
    (
        'editar_torneos',
        'Permite modificar configuraciones y reglamentos'
    ),
    (
        'borrar_torneos',
        'Permite eliminar torneos'
    ),
    (
        'cargar_resultados',
        'Permite asentar scores y ganadores'
    ),
    (
        'crear_usuarios',
        'Permite dar de alta cuentas administrativas'
    ),
    (
        'enviar_mensajes',
        'Permite emitir comunicados masivos'
    ),
    (
        'descargar_reportes',
        'Permite exportar datos en PDF, Excel y CSV'
    ),
    (
        'ver_auditoria',
        'Permite auditar accesos y cambios críticos'
    );

-- Roles
INSERT INTO
    roles (
        id,
        nombre_rol,
        descripcion,
        nivel_permiso
    )
VALUES (
        1,
        'Administrador General',
        'Superusuario con acceso irrestricto',
        3
    ),
    (
        2,
        'Organizador',
        'Gestor de competencias, fixtures y resultados',
        2
    ),
    (
        3,
        'Participante / Jugador',
        'Competidor en torneos y miembro de equipos',
        1
    ),
    (
        4,
        'Usuario Publico',
        'Espectador con permisos de solo lectura',
        0
    );

-- Asignar todos los permisos al Administrador General (rol_id = 1)
INSERT INTO
    rol_permisos (rol_id, permiso_id)
SELECT 1, id
FROM permisos;

-- Modalidades
INSERT INTO
    modalidades (id, nombre, descripcion)
VALUES (
        1,
        'individual',
        'Competencia de 1 contra 1'
    ),
    (
        2,
        'equipos',
        'Competencia colectiva por escuadras'
    );

-- Sistemas de Puntuación
INSERT INTO
    sistemas_puntuacion (
        id,
        nombre,
        puntos_victoria,
        puntos_empate,
        puntos_derrota,
        descripcion
    )
VALUES (
        1,
        'estandar_3_1_0',
        3.00,
        1.00,
        0.00,
        '3 pts Victoria, 1 pt Empate, 0 pts Derrota (Fútbol/Rugby estándar)'
    ),
    (
        2,
        'ajedrez_1_0.5_0',
        1.00,
        0.50,
        0.00,
        '1 pt Victoria, 0.5 pt Tablas, 0 pts Derrota'
    ),
    (
        3,
        'esports_mapas_2_1_0',
        2.00,
        1.00,
        0.00,
        '2 pts Victoria por mapa, 1 pt Empate, 0 pts Derrota'
    );

-- Disciplinas / Juegos
INSERT INTO
    juegos (
        id,
        nombre,
        categoria,
        formato_equipo_defecto,
        puntos_victoria,
        puntos_empate,
        puntos_derrota
    )
VALUES (
        1,
        'Valorant',
        'esport-shooter',
        5,
        3.00,
        1.00,
        0.00
    ),
    (
        2,
        'League of Legends',
        'esport-moba',
        5,
        3.00,
        0.00,
        0.00
    ),
    (
        3,
        'Rugby 7s',
        'deporte-fisico',
        7,
        4.00,
        2.00,
        0.00
    ),
    (
        4,
        'Fútbol 5',
        'deporte-fisico',
        5,
        3.00,
        1.00,
        0.00
    ),
    (
        5,
        'Ajedrez',
        'juego-mesa',
        1,
        1.00,
        0.50,
        0.00
    );

-- Políticas de Contraseñas por Defecto
INSERT INTO
    politicas_contrasenas (
        id,
        longitud_minima,
        requiere_mayuscula,
        requiere_minuscula,
        requiere_numero,
        requiere_caracter_especial,
        expiracion_dias,
        historial_cantidad
    )
VALUES (1, 8, 1, 1, 1, 1, 90, 5);

-- Logros Iniciales del Sistema
INSERT INTO
    logros (
        id,
        codigo,
        nombre,
        descripcion,
        icono,
        puntos_recompensa
    )
VALUES (
        1,
        'primera_batalla',
        'Primera Batalla',
        'Jugaste tu primer torneo en la plataforma',
        'fa-shield-halved',
        100
    ),
    (
        2,
        'campeon',
        'Campeón',
        'Ganaste un torneo oficial dentro de ASCEND',
        'fa-trophy',
        500
    ),
    (
        3,
        'veterano',
        'Veterano',
        'Completaste más de 10 torneos oficiales',
        'fa-award',
        300
    ),
    (
        4,
        'en_racha',
        'En Racha',
        'Ganaste 3 partidos consecutivos',
        'fa-fire',
        200
    );

-- Usuario Administrador Inicial (Contraseña de prueba: Admin123!)
INSERT INTO
    usuarios (
        id,
        email,
        contrasena_hash,
        nombre_completo,
        telefono,
        rol_id,
        esta_activo,
        email_verificado
    )
VALUES (
        1,
        'admin@ascend.com',
        '$2y$10$1.sqxv9SKZkmiTBZIcGhtuees.DhhY6x003C9k.WP0mRcv2qI1IMK',
        'Administrador General',
        '+59899123456',
        1,
        1,
        1
    );

-- Usuario Organizador Inicial
INSERT INTO
    usuarios (
        id,
        email,
        contrasena_hash,
        nombre_completo,
        telefono,
        rol_id,
        esta_activo,
        email_verificado
    )
VALUES (
        2,
        'organizador@ascend.com',
        '$2y$10$1.sqxv9SKZkmiTBZIcGhtuees.DhhY6x003C9k.WP0mRcv2qI1IMK',
        'Valentina Organizadora',
        '+59899654321',
        2,
        1,
        1
    );

INSERT INTO
    perfiles_organizadores (
        usuario_id,
        nombre_organizacion,
        bio_organizacion,
        localidad,
        telefono_contacto,
        verificado_oficial
    )
VALUES (
        2,
        'Club Deportivo ASCEND',
        'Organizadora oficial de competencias regionales de eSports y Rugby',
        'Montevideo',
        '+59899654321',
        1
    );

-- Usuario Jugador Inicial
INSERT INTO
    usuarios (
        id,
        email,
        contrasena_hash,
        nombre_completo,
        telefono,
        rol_id,
        esta_activo,
        email_verificado
    )
VALUES (
        3,
        'jugador@ascend.com',
        '$2y$10$1.sqxv9SKZkmiTBZIcGhtuees.DhhY6x003C9k.WP0mRcv2qI1IMK',
        'Marcos Competidor',
        '+59899777888',
        3,
        1,
        1
    );

INSERT INTO
    perfiles_jugadores (
        usuario_id,
        apodo_gamertag,
        bio,
        nivel,
        experiencia_puntos,
        pais,
        ciudad,
        discord_tag
    )
VALUES (
        3,
        'ShadowStriker',
        'Jugador competitivo de Valorant y Rugby virtual',
        3,
        3928,
        'Uruguay',
        'Montevideo',
        'Shadow#1234'
    );

INSERT INTO
    usuario_logros (
        usuario_id,
        logro_id,
        estado,
        progreso_actual,
        progreso_objetivo
    )
VALUES (3, 1, 'desbloqueado', 1, 1),
    (3, 2, 'desbloqueado', 1, 1),
    (3, 3, 'en_curso', 6, 10);

-- =============================================================================
-- 10. DECLARACIONES DCL (Data Control Language - Seguridad y Permisos)
-- =============================================================================
-- Implementación del Principio de Mínimo Privilegio (Least Privilege)

-- 10.1 Creación del Usuario de Producción para la Aplicación Web (PHP Backend)
CREATE USER IF NOT EXISTS 'ascend_app'@'localhost' IDENTIFIED BY 'AscendSecure2026!';

-- Concesión de permisos operacionales CRUD sobre todas las tablas del sistema
GRANT SELECT, INSERT, UPDATE, DELETE ON ascend.* TO 'ascend_app'@'localhost';

-- 10.2 Creación del Usuario de Auditoría y Reportes (Solo Lectura)
CREATE USER IF NOT EXISTS 'ascend_audit'@'localhost' IDENTIFIED BY 'AscendAuditReadOnly2026!';

-- Concesión de privilegios exclusivos de lectura sobre logs y tablas de reporte
GRANT SELECT ON ascend.logs_acceso TO 'ascend_audit'@'localhost';
GRANT SELECT ON ascend.auditoria_cambios TO 'ascend_audit'@'localhost';
GRANT SELECT ON ascend.logs_actividad TO 'ascend_audit'@'localhost';
GRANT SELECT ON ascend.torneos TO 'ascend_audit'@'localhost';
GRANT SELECT ON ascend.torneo_posiciones TO 'ascend_audit'@'localhost';

-- 10.3 Refresco y aplicación inmediata de permisos en el motor de base de datos
FLUSH PRIVILEGES;
