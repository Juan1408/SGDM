-- 004_crear_tabla_enfrentamientos.sql
-- Crea la tabla `enfrentamientos`

CREATE TABLE IF NOT EXISTS enfrentamientos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  torneo_id INT NOT NULL,
  participante_a INT NOT NULL,
  participante_b INT NOT NULL,
  resultado VARCHAR(50),
  fecha DATETIME,
  FOREIGN KEY (torneo_id) REFERENCES torneos(id)
);
