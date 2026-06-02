-- 003_crear_tabla_participantes.sql
-- Crea la tabla `participantes`

CREATE TABLE IF NOT EXISTS participantes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  torneo_id INT NOT NULL,
  usuario_id INT,
  alias VARCHAR(100),
  seed INT,
  FOREIGN KEY (torneo_id) REFERENCES torneos(id)
);
