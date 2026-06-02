-- 002_crear_tabla_torneos.sql
-- Crea la tabla `torneos`

CREATE TABLE IF NOT EXISTS torneos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL,
  tipo VARCHAR(50) NOT NULL,
  fecha_inicio DATE,
  config TEXT,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
