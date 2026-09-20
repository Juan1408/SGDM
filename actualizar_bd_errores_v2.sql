USE ascend;

ALTER TABLE errores_sistema
ADD COLUMN tipo_error VARCHAR(50) DEFAULT 'codigo' AFTER hash_error,
ADD COLUMN estado ENUM('nuevo', 'revision', 'resuelto') DEFAULT 'nuevo' AFTER ultima_vez,
ADD COLUMN detalles_resolucion TEXT NULL AFTER estado;

-- Migrar datos viejos (si los hay) de la columna booleana resuelto a la nueva columna estado
UPDATE errores_sistema SET estado = 'resuelto' WHERE resuelto = 1;

-- Eliminar la columna vieja resuelto
ALTER TABLE errores_sistema DROP COLUMN resuelto;
