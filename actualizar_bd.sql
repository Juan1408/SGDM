-- =======================================================
-- SCRIPT DE ACTUALIZACIÓN UNIFICADA DE BASE DE DATOS
-- =======================================================

-- 1. Crear tabla intermedia de modalidades permitidas (N:M)
CREATE TABLE IF NOT EXISTS juego_modalidades_permitidas (
    juego_id INT NOT NULL,
    modalidad_id INT NOT NULL,
    PRIMARY KEY (juego_id, modalidad_id),
    CONSTRAINT fk_jmp_juego FOREIGN KEY (juego_id) REFERENCES juegos (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_jmp_modalidad FOREIGN KEY (modalidad_id) REFERENCES modalidades (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- 2. Quitar el campo innecesario de la tabla juegos (ahora es N:M)
ALTER TABLE juegos DROP COLUMN formato_equipo_defecto;

-- 3. Crear la nueva tabla de Formatos de Torneo
CREATE TABLE IF NOT EXISTS formatos_torneo (
    codigo VARCHAR(50) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    activo BOOLEAN DEFAULT TRUE
) ENGINE = InnoDB;

-- 4. Insertar los 3 formatos iniciales
INSERT IGNORE INTO formatos_torneo (codigo, nombre, descripcion, activo) VALUES 
('liga', 'Fase de Grupos / Liga', 'Todos juegan contra todos y suman puntos.', 1),
('eliminacion_directa', 'Eliminatoria Directa', 'El que pierde queda eliminado del torneo.', 1),
('suizo', 'Sistema Suizo', 'Emparejamientos por rondas según el puntaje acumulado.', 1);

-- 5. Crear tabla intermedia entre juegos y formatos permitidos (N:M)
CREATE TABLE IF NOT EXISTS juego_formatos_permitidos (
    juego_id INT NOT NULL,
    formato_codigo VARCHAR(50) NOT NULL,
    PRIMARY KEY (juego_id, formato_codigo),
    CONSTRAINT fk_jfp_juego FOREIGN KEY (juego_id) REFERENCES juegos (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_jfp_formato FOREIGN KEY (formato_codigo) REFERENCES formatos_torneo (codigo) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

-- 6. Agregar el campo iniciado_por a la tabla solicitudes_equipo
ALTER TABLE solicitudes_equipo ADD COLUMN iniciado_por INT NOT NULL AFTER usuario_id;
ALTER TABLE solicitudes_equipo ADD CONSTRAINT fk_solicitud_iniciado FOREIGN KEY (iniciado_por) REFERENCES usuarios (id) ON DELETE CASCADE ON UPDATE CASCADE;

-- 7. Crear la tabla de guardados genéricos (Asociación Polimórfica)
CREATE TABLE IF NOT EXISTS guardados ( 
    id INT AUTO_INCREMENT PRIMARY KEY, 
    usuario_id INT NOT NULL, 
    tipo VARCHAR(20) NOT NULL CHECK (tipo IN ('torneo', 'equipo')), 
    referencia_id INT NOT NULL, 
    fecha_guardado TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    UNIQUE KEY unique_guardado (usuario_id, tipo, referencia_id), 
    CONSTRAINT fk_guardado_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE ON UPDATE CASCADE 
) ENGINE = InnoDB;

-- 8. Vincular Equipos a un Juego Específico (Abandono de Multigaming)
-- Nota: Lo dejamos como NULL por defecto para que los equipos viejos no rompan la DB.
ALTER TABLE equipos ADD COLUMN juego_id INT NULL AFTER nombre_equipo;
ALTER TABLE equipos ADD CONSTRAINT fk_equipo_juego FOREIGN KEY (juego_id) REFERENCES juegos (id) ON DELETE CASCADE ON UPDATE CASCADE;
