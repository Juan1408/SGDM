-- =============================================================================
-- 007_restricciones_disciplinas.sql
-- Garantiza la integridad referencial y de negocio entre torneos y las
-- modalidades/formatos permitidos para cada disciplina (juegos).
-- =============================================================================

USE ascend;

-- 1. Triggers de protección en inserción y actualización
DROP TRIGGER IF EXISTS trg_torneos_valida_juego_before_insert;
DROP TRIGGER IF EXISTS trg_torneos_valida_juego_before_update;

DELIMITER $$

CREATE TRIGGER trg_torneos_valida_juego_before_insert
BEFORE INSERT ON torneos
FOR EACH ROW
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM juego_modalidades_permitidas 
        WHERE juego_id = NEW.juego_id AND modalidad_id = NEW.modalidad_id
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Integridad fallida: la modalidad no está permitida para esta disciplina.';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM juego_formatos_permitidos 
        WHERE juego_id = NEW.juego_id AND formato_codigo = NEW.formato
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Integridad fallida: el formato de torneo no está permitido para esta disciplina.';
    END IF;
END$$

CREATE TRIGGER trg_torneos_valida_juego_before_update
BEFORE UPDATE ON torneos
FOR EACH ROW
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM juego_modalidades_permitidas 
        WHERE juego_id = NEW.juego_id AND modalidad_id = NEW.modalidad_id
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Integridad fallida: la modalidad no está permitida para esta disciplina.';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM juego_formatos_permitidos 
        WHERE juego_id = NEW.juego_id AND formato_codigo = NEW.formato
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Integridad fallida: el formato de torneo no está permitido para esta disciplina.';
    END IF;
END$$

DELIMITER ;
