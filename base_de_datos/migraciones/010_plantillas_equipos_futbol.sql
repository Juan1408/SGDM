-- =============================================================================
-- 010_plantillas_equipos_futbol.sql
-- =============================================================================
-- PROPÓSITO
--   Completar las plantillas de los 12 equipos de Fútbol 5 creados por 009:
--   asignar un capitán principal a cada uno y sumar miembros activos para que
--   cumplan la regla de inscripción vigente en TorneoControlador::inscribirse().
--
-- REGLAS DE NEGOCIO QUE RESPETA
--   1) Un equipo colectivo necesita al menos 2 miembros activos para inscribirse.
--   2) Un jugador no puede pertenecer a dos equipos distintos del mismo torneo
--      (Torneo::obtenerMiembrosEnConflicto()).
--
-- CORRECCIÓN INCLUIDA
--   009 asignó como creadores de 1142, 1143 y 1144 a 1019, 1020 y 1021, que ya
--   eran miembros de 1103. Eso dejaba a tres jugadores en dos equipos del mismo
--   torneo (1203), violando la regla 2. Acá se reasignan esos equipos a tres
--   jugadores libres y se eliminan las membresías duplicadas.
--
-- ALCANCE
--   Solo datos. No crea ni modifica tablas, columnas, índices ni triggers.
--   Toca únicamente equipos 1103, 1108, 1129, 1130 y 1133 a 1144, sus filas de
--   equipo_miembros/equipo_capitanes y ningún usuario nuevo.
--
-- ORDEN E IDEMPOTENCIA
--   Debe ejecutarse después de 009. Es idempotente: puede aplicarse varias veces.
--   En una base existente:
--     mysql -uroot -proot ascend < base_de_datos/migraciones/010_plantillas_equipos_futbol.sql
--   En Docker:
--     docker exec -i sgdm_db mysql -uroot -proot123 ascend < base_de_datos/migraciones/010_plantillas_equipos_futbol.sql
-- =============================================================================

START TRANSACTION;

-- -----------------------------------------------------------------------------
-- 1. Reasignar los equipos 1142, 1143 y 1144 a jugadores libres
--    (1049, 1050 y 1051 no pertenecen a ningún equipo inscripto en 1203).
-- -----------------------------------------------------------------------------
UPDATE equipos SET creado_por = 1049 WHERE id = 1142 AND creado_por = 1019;
UPDATE equipos SET creado_por = 1050 WHERE id = 1143 AND creado_por = 1020;
UPDATE equipos SET creado_por = 1051 WHERE id = 1144 AND creado_por = 1021;

-- Eliminar las membresías que generaban conflicto en el torneo 1203.
DELETE FROM equipo_miembros WHERE equipo_id = 1142 AND usuario_id = 1019;
DELETE FROM equipo_miembros WHERE equipo_id = 1143 AND usuario_id = 1020;
DELETE FROM equipo_miembros WHERE equipo_id = 1144 AND usuario_id = 1021;

-- Alta de los nuevos creadores como miembros activos de su equipo.
INSERT INTO equipo_miembros (equipo_id, usuario_id, fecha_union, numero_camiseta, posicion, es_activo) VALUES
    (1142, 1049, '2026-10-28 10:00:00', 10, 'Delantero', 1),
    (1143, 1050, '2026-10-28 10:00:00', 10, 'Delantero', 1),
    (1144, 1051, '2026-10-28 10:00:00', 10, 'Delantero', 1)
ON DUPLICATE KEY UPDATE
    es_activo = 1,
    numero_camiseta = VALUES(numero_camiseta),
    posicion = VALUES(posicion);

-- -----------------------------------------------------------------------------
-- 2. Capitán principal de cada equipo nuevo.
--    asignado_por = 1003 (organizador del torneo 1203), igual que en 008/009.
-- -----------------------------------------------------------------------------
INSERT INTO equipo_capitanes (equipo_id, usuario_id, fecha_asignacion, asignado_por, es_capitan_principal) VALUES
    (1133, 1010, '2026-10-28 10:00:00', 1003, 1),
    (1134, 1011, '2026-10-28 10:00:00', 1003, 1),
    (1135, 1012, '2026-10-28 10:00:00', 1003, 1),
    (1136, 1013, '2026-10-28 10:00:00', 1003, 1),
    (1137, 1014, '2026-10-28 10:00:00', 1003, 1),
    (1138, 1015, '2026-10-28 10:00:00', 1003, 1),
    (1139, 1016, '2026-10-28 10:00:00', 1003, 1),
    (1140, 1017, '2026-10-28 10:00:00', 1003, 1),
    (1141, 1018, '2026-10-28 10:00:00', 1003, 1),
    (1142, 1049, '2026-10-28 10:00:00', 1003, 1),
    (1143, 1050, '2026-10-28 10:00:00', 1003, 1),
    (1144, 1051, '2026-10-28 10:00:00', 1003, 1)
ON DUPLICATE KEY UPDATE
    es_capitan_principal = 1,
    asignado_por = VALUES(asignado_por);

-- -----------------------------------------------------------------------------
-- 3. Segundo miembro para cada equipo nuevo (mínimo exigido: 2 activos).
--    Todos los usuarios elegidos están libres de otros equipos del torneo 1203.
-- -----------------------------------------------------------------------------
INSERT INTO equipo_miembros (equipo_id, usuario_id, fecha_union, numero_camiseta, posicion, es_activo) VALUES
    (1133, 1024, '2026-10-28 10:00:00', 7, 'Mediocampista', 1),
    (1134, 1025, '2026-10-28 10:00:00', 7, 'Mediocampista', 1),
    (1135, 1026, '2026-10-28 10:00:00', 7, 'Mediocampista', 1),
    (1136, 1027, '2026-10-28 10:00:00', 7, 'Mediocampista', 1),
    (1137, 1039, '2026-10-28 10:00:00', 7, 'Mediocampista', 1),
    (1138, 1040, '2026-10-28 10:00:00', 7, 'Mediocampista', 1),
    (1139, 1041, '2026-10-28 10:00:00', 7, 'Mediocampista', 1),
    (1140, 1042, '2026-10-28 10:00:00', 7, 'Mediocampista', 1),
    (1141, 1043, '2026-10-28 10:00:00', 7, 'Mediocampista', 1),
    (1142, 1044, '2026-10-28 10:00:00', 7, 'Mediocampista', 1),
    (1143, 1045, '2026-10-28 10:00:00', 7, 'Mediocampista', 1),
    (1144, 1046, '2026-10-28 10:00:00', 7, 'Mediocampista', 1)
ON DUPLICATE KEY UPDATE
    es_activo = 1,
    numero_camiseta = VALUES(numero_camiseta),
    posicion = VALUES(posicion);

-- -----------------------------------------------------------------------------
-- 4. Tercer miembro para dos equipos, para que la plantilla no quede en el mínimo.
-- -----------------------------------------------------------------------------
INSERT INTO equipo_miembros (equipo_id, usuario_id, fecha_union, numero_camiseta, posicion, es_activo) VALUES
    (1133, 1047, '2026-10-28 10:00:00', 4, 'Defensor', 1),
    (1134, 1048, '2026-10-28 10:00:00', 4, 'Defensor', 1)
ON DUPLICATE KEY UPDATE
    es_activo = 1,
    numero_camiseta = VALUES(numero_camiseta),
    posicion = VALUES(posicion);

COMMIT;

-- -----------------------------------------------------------------------------
-- 5. Comprobaciones posteriores (ambas deben devolver 0 filas).
--    Se dejan como SELECT para que el operador las vea al aplicar el archivo.
-- -----------------------------------------------------------------------------

-- 5.1 Equipos de Fútbol 5 con menos de 2 miembros activos.
SELECT e.id, e.nombre_equipo, COUNT(em.usuario_id) AS miembros_activos
FROM equipos e
LEFT JOIN equipo_miembros em ON em.equipo_id = e.id AND em.es_activo = 1
WHERE e.juego_id = 4 AND e.id IN (1103, 1108, 1129, 1130, 1133, 1134, 1135, 1136,
                                  1137, 1138, 1139, 1140, 1141, 1142, 1143, 1144)
GROUP BY e.id, e.nombre_equipo
HAVING miembros_activos < 2;

-- 5.2 Jugadores que quedaron en más de un equipo del torneo 1203.
SELECT em.usuario_id, GROUP_CONCAT(DISTINCT em.equipo_id ORDER BY em.equipo_id) AS equipos
FROM equipo_miembros em
INNER JOIN participantes_torneo pt
        ON pt.referencia_id = em.equipo_id
       AND pt.tipo = 'equipo'
       AND pt.torneo_id = 1203
       AND pt.estado IN ('confirmado', 'pendiente')
WHERE em.es_activo = 1
GROUP BY em.usuario_id
HAVING COUNT(DISTINCT em.equipo_id) > 1;
