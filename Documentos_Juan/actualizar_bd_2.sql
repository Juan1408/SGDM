-- 1. Quitar el campo innecesario de la tabla juegos
ALTER TABLE juegos DROP COLUMN formato_equipo_defecto;

-- 2. Quitar el campo activo de modalidades (ahora se usará en formatos)
ALTER TABLE modalidades DROP COLUMN activo;

-- 3. Crear la nueva tabla de Formatos de Torneo
CREATE TABLE formatos_torneo (
    codigo VARCHAR(50) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    activo BOOLEAN DEFAULT TRUE
) ENGINE = InnoDB;

-- 4. Insertar los 3 formatos iniciales
INSERT INTO formatos_torneo (codigo, nombre, descripcion, activo) VALUES 
('liga', 'Fase de Grupos / Liga', 'Todos juegan contra todos y suman puntos.', 1),
('eliminacion_directa', 'Eliminatoria Directa', 'El que pierde queda eliminado del torneo.', 1),
('suizo', 'Sistema Suizo', 'Emparejamientos por rondas según el puntaje acumulado.', 1);

-- 5. Crear tabla intermedia entre juegos y formatos permitidos
CREATE TABLE juego_formatos_permitidos (
    juego_id INT NOT NULL,
    formato_codigo VARCHAR(50) NOT NULL,
    PRIMARY KEY (juego_id, formato_codigo),
    CONSTRAINT fk_jfp_juego FOREIGN KEY (juego_id) REFERENCES juegos (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_jfp_formato FOREIGN KEY (formato_codigo) REFERENCES formatos_torneo (codigo) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;
