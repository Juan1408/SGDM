ALTER TABLE modalidades ADD COLUMN activo BOOLEAN DEFAULT TRUE;

CREATE TABLE juego_modalidades_permitidas (
    juego_id INT NOT NULL,
    modalidad_id INT NOT NULL,
    PRIMARY KEY (juego_id, modalidad_id),
    CONSTRAINT fk_jmp_juego FOREIGN KEY (juego_id) REFERENCES juegos (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_jmp_modalidad FOREIGN KEY (modalidad_id) REFERENCES modalidades (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;
