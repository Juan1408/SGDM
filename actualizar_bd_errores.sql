USE ascend;

CREATE TABLE IF NOT EXISTS errores_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hash_error VARCHAR(64) UNIQUE NOT NULL,
    mensaje TEXT NOT NULL,
    archivo VARCHAR(255) NOT NULL,
    linea INT NOT NULL,
    stack_trace TEXT NOT NULL,
    contexto_json JSON,
    usuario_id INT NULL,
    rol_usuario VARCHAR(50) NULL,
    cantidad_ocurrencias INT DEFAULT 1,
    primera_vez TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultima_vez TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    resuelto BOOLEAN DEFAULT FALSE,
    CONSTRAINT fk_error_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB;
