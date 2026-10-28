-- =============================================================================
-- 009_equipos_futbol_escudos.sql
-- Deja Fútbol 5 con 16 equipos y un escudo propio por equipo, y abre la
-- "Copa Ciudad de Fútbol 5" (torneo 1203) para que se vea desde el sitio
-- público.
--
--   * Renombra los 4 equipos de Fútbol 5 de la semilla (1103, 1108, 1129,
--     1130) y les asigna escudo.
--   * Crea 12 equipos nuevos (1133-1144) con escudo, capitán y datos básicos.
--   * Inscribe los 16 equipos en el torneo 1203 (liga, cupo 16) y pasa el
--     torneo de 'borrador' a 'inscripciones_abiertas'.
--   * Sincroniza el nombre de esos equipos en participantes, posiciones y en
--     el podio del torneo 1208, que también los usa.
--
-- ARCHIVOS DE IMAGEN
--   Las imágenes NO van en base_de_datos/migraciones: esa carpeta se monta
--   únicamente en MySQL (/docker-entrypoint-initdb.d) y Apache no la sirve.
--   Deben estar en:
--
--     codigo_fuente/publico/img/equipos/<archivo>.png
--
--   `escudo_url` guarda una ruta RELATIVA al DocumentRoot, no una URL
--   absoluta. Así el mismo dato funciona en XAMPP
--   (http://localhost/SGDM/codigo_fuente/publico/) y en Docker
--   (http://localhost:8080/). Varias vistas aceptan ruta relativa o URL
--   completa: si el valor empieza con http(s) se usa tal cual; si no, se
--   antepone URL_BASE.
--
--   Si un archivo falta, el navegador mostrará la imagen rota en ese equipo.
--   Para cambiar un nombre de archivo, editá su UPDATE/INSERT.
--
-- ALCANCE
--   - Solo toca IDs de semilla: equipos 1103-1144, participantes y posiciones
--     del torneo 1203, y el podio del torneo 1208.
--   - No crea ni altera tablas, ni modifica el esquema.
--   - Es idempotente: puede ejecutarse más de una vez sin duplicar filas.
--   - Debe aplicarse después de 008, porque reutiliza sus equipos y torneos.
-- =============================================================================

USE ascend;
SET NAMES utf8mb4;

-- -----------------------------------------------------------------------------
-- 1. Equipos de Fútbol 5 que ya existían: nombre nuevo + escudo.
--    Se actualizan por ID porque el nombre anterior deja de existir.
-- -----------------------------------------------------------------------------
UPDATE equipos
   SET nombre_equipo = 'Zonal City FC',
       escudo_url    = 'img/equipos/zonal-city-fc.png',
       ubicacion     = 'Cordón, Montevideo',
       descripcion   = 'Equipo de fútbol 5 formado en el Cordón, con cantera propia.'
 WHERE id = 1103;

UPDATE equipos
   SET nombre_equipo = 'Deportivo Test',
       escudo_url    = 'img/equipos/deportivo-test.png',
       ubicacion     = 'Malvín, Montevideo',
       descripcion   = 'Equipo de fútbol 5 de Malvín que juega de local en la cancha del club.'
 WHERE id = 1108;

UPDATE equipos
   SET nombre_equipo = 'Unión Beta',
       escudo_url    = 'img/equipos/union-beta.png',
       ubicacion     = 'Sayago, Montevideo',
       descripcion   = 'Equipo de fútbol 5 surgido de la liga barrial de Sayago.'
 WHERE id = 1129;

UPDATE equipos
   SET nombre_equipo = 'Sporting',
       escudo_url    = 'img/equipos/sporting.png',
       ubicacion     = 'Carrasco, Montevideo',
       descripcion   = 'Equipo de fútbol 5 de Carrasco con plantel estable desde 2015.'
 WHERE id = 1130;

-- -----------------------------------------------------------------------------
-- 2. Equipos nuevos de Fútbol 5 (12). Uno por cada escudo restante.
--    `creado_por` es el jugador que figura como capitán en equipo_miembros.
-- -----------------------------------------------------------------------------
INSERT INTO equipos
    (id, nombre_equipo, juego_id, escudo_url, descripcion, ubicacion,
     anio_fundacion, creado_por, activo, codigo_invitacion, fecha_creacion)
VALUES
(1133,'Aston Birra',4,'img/equipos/aston-birra.png',
 'Equipo de fútbol 5 del barrio Sur, armado entre amigos del secundario.',
 'Sur, Montevideo',2018,1010,1,UPPER(LEFT(MD5('ascend-equipo-1133'),8)),'2026-10-26 19:46:20'),
(1134,'Bayern de los Caídos',4,'img/equipos/bayern-de-los-caidos.png',
 'Club de fútbol 5 con hinchada propia y camiseta roja.',
 'Palermo, Montevideo',2015,1011,1,UPPER(LEFT(MD5('ascend-equipo-1134'),8)),'2026-10-26 19:46:20'),
(1135,'Inter de Milanesa',4,'img/equipos/inter-de-milanesa.png',
 'Equipo de fútbol 5 nacido en una rotisería del barrio.',
 'Centro, Montevideo',2017,1012,1,UPPER(LEFT(MD5('ascend-equipo-1135'),8)),'2026-10-26 19:46:20'),
(1136,'La Naranja Mecánica',4,'img/equipos/la-naranja-mecanica.png',
 'Fútbol 5 de juego ofensivo y presión alta.',
 'Pocitos, Montevideo',2016,1013,1,UPPER(LEFT(MD5('ascend-equipo-1136'),8)),'2026-10-26 19:46:20'),
(1137,'Los Galácticos',4,'img/equipos/los-galacticos.png',
 'Fútbol 5 de exjugadores de liga barrial que se reencontraron en la Copa.',
 'Buceo, Montevideo',2014,1014,1,UPPER(LEFT(MD5('ascend-equipo-1137'),8)),'2026-10-26 19:46:20'),
(1138,'Los Invencibles',4,'img/equipos/los-invencibles.png',
 'Fútbol 5 con racha invicta en los torneos de barrio.',
 'Unión, Montevideo',2019,1015,1,UPPER(LEFT(MD5('ascend-equipo-1138'),8)),'2026-10-26 19:46:20'),
(1139,'Los Magos del Balón',4,'img/equipos/los-magos-del-balon.png',
 'Fútbol 5 de toque corto y posesión.',
 'Parque Rodó, Montevideo',2020,1016,1,UPPER(LEFT(MD5('ascend-equipo-1139'),8)),'2026-10-26 19:46:20'),
(1140,'Los Magos',4,'img/equipos/los-magos.png',
 'Equipo de fútbol 5 de Pocitos con plantel joven.',
 'Pocitos, Montevideo',2018,1017,1,UPPER(LEFT(MD5('ascend-equipo-1140'),8)),'2026-10-26 19:46:20'),
(1141,'Quality',4,'img/equipos/quality.png',
 'Fútbol 5 corporativo que compite en la Copa desde 2021.',
 'Punta Carretas, Montevideo',2021,1018,1,UPPER(LEFT(MD5('ascend-equipo-1141'),8)),'2026-10-26 19:46:20'),
(1142,'Real Simulador',4,'img/equipos/real-simulador.png',
 'Fútbol 5 conocido por reclamar cada falta al árbitro.',
 'Cordón, Montevideo',2017,1019,1,UPPER(LEFT(MD5('ascend-equipo-1142'),8)),'2026-10-26 19:46:20'),
(1143,'Vodka Juniors',4,'img/equipos/vodka-juniors.png',
 'Fútbol 5 del este de Montevideo, con tercer tiempo largo.',
 'Malvín, Montevideo',2016,1020,1,UPPER(LEFT(MD5('ascend-equipo-1143'),8)),'2026-10-26 19:46:20'),
(1144,'Zonal United',4,'img/equipos/zonal-united.png',
 'Fútbol 5 armado para la liga zonal de Montevideo.',
 'Sayago, Montevideo',2019,1021,1,UPPER(LEFT(MD5('ascend-equipo-1144'),8)),'2026-10-26 19:46:20')
ON DUPLICATE KEY UPDATE
    nombre_equipo    = VALUES(nombre_equipo),
    juego_id         = VALUES(juego_id),
    escudo_url       = VALUES(escudo_url),
    descripcion      = VALUES(descripcion),
    ubicacion        = VALUES(ubicacion),
    anio_fundacion   = VALUES(anio_fundacion),
    activo           = VALUES(activo);

-- -----------------------------------------------------------------------------
-- 3. Capitán de cada equipo nuevo. Un integrante por equipo para que el
--    perfil público no quede vacío.
-- -----------------------------------------------------------------------------
INSERT IGNORE INTO equipo_miembros
    (equipo_id, usuario_id, fecha_union, numero_camiseta, posicion, es_activo)
VALUES
(1133,1010,'2026-10-26 19:46:20',10,'Delantero',1),
(1134,1011,'2026-10-26 19:46:20',10,'Delantero',1),
(1135,1012,'2026-10-26 19:46:20',10,'Delantero',1),
(1136,1013,'2026-10-26 19:46:20',10,'Delantero',1),
(1137,1014,'2026-10-26 19:46:20',10,'Delantero',1),
(1138,1015,'2026-10-26 19:46:20',10,'Delantero',1),
(1139,1016,'2026-10-26 19:46:20',10,'Delantero',1),
(1140,1017,'2026-10-26 19:46:20',10,'Delantero',1),
(1141,1018,'2026-10-26 19:46:20',10,'Delantero',1),
(1142,1019,'2026-10-26 19:46:20',10,'Delantero',1),
(1143,1020,'2026-10-26 19:46:20',10,'Delantero',1),
(1144,1021,'2026-10-26 19:46:20',10,'Delantero',1);

-- -----------------------------------------------------------------------------
-- 4. Los 16 equipos quedan inscriptos en la Copa Ciudad de Fútbol 5 (1203).
--    El torneo tiene cupo máximo 16, así que se completa exactamente.
-- -----------------------------------------------------------------------------
INSERT INTO participantes_torneo
    (torneo_id, tipo, referencia_id, nombre, estado,
     fecha_inscripcion, confirmado_por, fecha_confirmacion)
SELECT 1203, 'equipo', e.id, e.nombre_equipo, 'confirmado',
       '2026-10-26 19:46:20', 1003, '2026-10-26 19:46:20'
FROM equipos e
WHERE e.juego_id = 4
  AND e.id IN (1103,1108,1129,1130,1133,1134,1135,1136,1137,1138,1139,1140,1141,1142,1143,1144)
  AND NOT EXISTS (
        SELECT 1 FROM participantes_torneo p
        WHERE p.torneo_id = 1203 AND p.tipo = 'equipo' AND p.referencia_id = e.id
  );

-- Filas de posiciones en cero para los recién inscriptos, con el mismo
-- criterio que usa 008 al recalcular.
INSERT INTO torneo_posiciones
    (torneo_id, participante_id, partidos_jugados, partidos_ganados,
     partidos_empatados, partidos_perdidos, puntos_favor, puntos_contra,
     puntos, sanciones_puntos)
SELECT 1203, p.id, 0, 0, 0, 0, 0.00, 0.00, 0, 0
FROM participantes_torneo p
WHERE p.torneo_id = 1203 AND p.tipo = 'equipo'
  AND NOT EXISTS (
        SELECT 1 FROM torneo_posiciones tp
        WHERE tp.torneo_id = 1203 AND tp.participante_id = p.id
  );

-- -----------------------------------------------------------------------------
-- 5. El torneo pasa a inscripciones abiertas con la ventana vigente, para que
--    se vea en el listado público con sus 16 equipos y escudos.
-- -----------------------------------------------------------------------------
UPDATE torneos
   SET estado = 'inscripciones_abiertas',
       fecha_inicio_inscripcion = '2026-10-20',
       fecha_limite_inscripcion = '2026-11-26'
 WHERE id = 1203;

-- -----------------------------------------------------------------------------
-- 6. Sincronización de nombres.
--    participantes_torneo guarda una copia del nombre del equipo y el podio
--    del torneo 1208 (suizo finalizado) se guarda como texto en torneo_config.
-- -----------------------------------------------------------------------------
UPDATE participantes_torneo p
  JOIN equipos e ON e.id = p.referencia_id
   SET p.nombre = e.nombre_equipo
 WHERE p.tipo = 'equipo'
   AND e.id IN (1103,1108,1129,1130,1133,1134,1135,1136,1137,1138,1139,1140,1141,1142,1143,1144);

UPDATE torneo_config
   SET valor = '{"primero": "Zonal City FC", "segundo": "Deportivo Test", "tercero": "Unión Beta"}'
 WHERE torneo_id = 1208 AND clave = 'podio';

COMMIT;

-- Fin de 009_equipos_futbol_escudos.sql
