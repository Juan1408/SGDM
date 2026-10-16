USE ascend;

CREATE TABLE IF NOT EXISTS intentos_login (
    ip_origen VARCHAR(45) PRIMARY KEY,
    intentos INT NOT NULL DEFAULT 1,
    ultimo_intento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    bloqueado_hasta DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS politicas_contrasenas (
    id INT PRIMARY KEY,
    longitud_minima INT DEFAULT 8,
    requiere_mayuscula BOOLEAN DEFAULT 1,
    requiere_minuscula BOOLEAN DEFAULT 1,
    requiere_numero BOOLEAN DEFAULT 1,
    requiere_caracter_especial BOOLEAN DEFAULT 1,
    expiracion_dias INT DEFAULT 90,
    historial_cantidad INT DEFAULT 5,
    actualizado_por INT NULL,
    FOREIGN KEY (actualizado_por) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB;
