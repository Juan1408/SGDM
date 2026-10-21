-- =============================================================================
-- 005_correcciones_esquema.sql
-- Correcciones de columnas faltantes detectadas en la auditoría.
-- Es idempotente: si la columna ya existe (base nueva con 001 actualizado),
-- no hace nada. Se puede ejecutar sobre bases existentes sin perder datos.
-- =============================================================================

USE ascend;

SET @bd_actual = DATABASE();

-- 1. torneo_actividades.descripcion
SET @sql = (
    SELECT IF(
        EXISTS(
            SELECT 1 FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = @bd_actual
              AND TABLE_NAME = 'torneo_actividades'
              AND COLUMN_NAME = 'descripcion'
        ),
        'SELECT 1',
        'ALTER TABLE torneo_actividades ADD COLUMN descripcion TEXT NULL AFTER titulo'
    )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2. perfiles_organizadores.banner_url
SET @sql = (
    SELECT IF(
        EXISTS(
            SELECT 1 FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = @bd_actual
              AND TABLE_NAME = 'perfiles_organizadores'
              AND COLUMN_NAME = 'banner_url'
        ),
        'SELECT 1',
        'ALTER TABLE perfiles_organizadores ADD COLUMN banner_url TEXT NULL AFTER bio_organizacion'
    )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 3. perfiles_organizadores.campos_publicos
SET @sql = (
    SELECT IF(
        EXISTS(
            SELECT 1 FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = @bd_actual
              AND TABLE_NAME = 'perfiles_organizadores'
              AND COLUMN_NAME = 'campos_publicos'
        ),
        'SELECT 1',
        "ALTER TABLE perfiles_organizadores ADD COLUMN campos_publicos VARCHAR(255) NOT NULL DEFAULT '' AFTER banner_url"
    )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 4. torneo_suizo_parejas: incluir ronda en la PK
-- Sin ronda, una revancha entre los mismos dos participantes (inevitable en grupos chicos)
-- choca contra la PK y aborta la generacion de la ronda completa.
SET @sql = (
    SELECT IF(
        EXISTS(
            SELECT 1 FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = @bd_actual
              AND TABLE_NAME = 'torneo_suizo_parejas'
              AND INDEX_NAME = 'PRIMARY'
              AND COLUMN_NAME = 'ronda'
        ),
        'SELECT 1',
        'ALTER TABLE torneo_suizo_parejas DROP PRIMARY KEY, ADD PRIMARY KEY (torneo_id, participante_a_id, participante_b_id, ronda)'
    )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
