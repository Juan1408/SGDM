-- =============================================================================
-- 008_semilla_consistente.sql
-- Capa final de la semilla de demostración. Deja la base coherente con lo que
-- el CRUD de administración puede crear:
--   * 9 disciplinas activas: 3 de eSports, 3 de deporte físico y 3 de mesa.
--   * Por cada tipo: 2 disciplinas de equipos y 1 individual.
--   * Cada disciplina permite exactamente 2 formatos de torneo.
--   * Torneos y equipos heredados quedan dentro de esas reglas (compatibles
--     con los triggers de 007_restricciones_disciplinas.sql).
--   * Nombres, correos, equipos, torneos, sedes, actividades y notificaciones
--     con datos realistas y enlazados entre sí.
--   * Una única contraseña de demostración para todas las cuentas sembradas.
--
-- ORDEN DE LAS MIGRACIONES
--   001 esquema · 002 catálogo y cuentas base · 003 usuarios/equipos/torneos
--   relacionales · 004 autenticación · 005 correcciones · 006 ampliación de
--   torneos · 007 triggers de disciplinas · 008 esta capa de normalización.
--
--   003 y 006 siguen siendo necesarios: crean las filas base (usuarios,
--   perfiles, equipos, torneos 1200-1217, participantes y encuentros) que
--   esta migración renombra, corrige y amplía. 008 no las reemplaza: garantiza
--   que el estado final sea único, coherente y legible para la demo.
--
-- IMPORTANTE
--   - Solo modifica IDs reservados por las semillas (1-3, 1000-1051,
--     1100-1132, 1200-1225 y sus tablas relacionadas).
--   - Es idempotente: puede ejecutarse más de una vez.
--   - No debe ejecutarse sobre una base con datos reales: reescribe nombres,
--     correos, resultados y contraseñas de los IDs sembrados.
--   - No crea ni altera estructuras de tablas.
--   - Los archivos sueltos actualizar_bd*.sql del repositorio son históricos:
--     sus cambios ya están incorporados en 001 y 005.
-- =============================================================================

USE ascend;
SET NAMES utf8mb4;

-- -----------------------------------------------------------------------------
-- 0. Mapas temporales de renombrado (se eliminan solos al cerrar la sesión).
-- -----------------------------------------------------------------------------
DROP TEMPORARY TABLE IF EXISTS _map_usuarios;
CREATE TEMPORARY TABLE _map_usuarios (
    usuario_id INT PRIMARY KEY,
    nombre_nuevo VARCHAR(120) NOT NULL,
    email_nuevo VARCHAR(255) NOT NULL,
    apodo_nuevo VARCHAR(50) NULL,
    organizacion_nueva VARCHAR(150) NULL
);

INSERT INTO _map_usuarios (usuario_id, nombre_nuevo, email_nuevo, apodo_nuevo, organizacion_nueva) VALUES
(1000,'Lucía Fernández','lucia.fernandez@ascend.test',NULL,'Circuito Nacional de eSports'),
(1001,'Martín Acosta','martin.acosta@ascend.test',NULL,'Club Atlético Cordón'),
(1002,'Sofía Pereira','sofia.pereira@ascend.test',NULL,'Liga Universitaria de Deportes'),
(1003,'Diego Cabrera','diego.cabrera@ascend.test',NULL,'Club Social y Deportivo del Plata'),
(1004,'Camila Silva','camila.silva@ascend.test',NULL,'Arena Gamer Montevideo'),
(1005,'Federico Núñez','federico.nunez@ascend.test',NULL,'Federación Uruguaya de eSports'),
(1010,'Mateo Rodríguez','mateo.rodriguez@ascend.test','MateoR10',NULL),
(1011,'Juan Ignacio Pérez','juan.perez@ascend.test','NachoPerez',NULL),
(1012,'Santiago Vidal','santiago.vidal@ascend.test','SantiV',NULL),
(1013,'Agustina López','agustina.lopez@ascend.test','AgusLopez',NULL),
(1014,'Bruno Silva','bruno.silva@ascend.test','BrunoSilva',NULL),
(1015,'Camila Torres','camila.torres@ascend.test','CamiTorres',NULL),
(1016,'Nicolás Acosta','nicolas.acosta@ascend.test','NicoAcosta',NULL),
(1017,'Valentina Suárez','valentina.suarez@ascend.test','ValeSuarez',NULL),
(1018,'Diego Fernández','diego.fernandez@ascend.test','DieguitoF',NULL),
(1019,'Martina Cabrera','martina.cabrera@ascend.test','MartuCab',NULL),
(1020,'Facundo Núñez','facundo.nunez@ascend.test','FakuNunez',NULL),
(1021,'Julieta Pereira','julieta.pereira@ascend.test','JuliPereira',NULL),
(1022,'Rodrigo Sosa','rodrigo.sosa@ascend.test','RodriSosa',NULL),
(1023,'Florencia Méndez','florencia.mendez@ascend.test','FlorMendez',NULL),
(1024,'Ignacio Olivera','ignacio.olivera@ascend.test','NachoOliveira',NULL),
(1025,'Lucía Ramos','lucia.ramos@ascend.test','LuciRamos',NULL),
(1026,'Tomás Barrios','tomas.barrios@ascend.test','TomyBarrios',NULL),
(1027,'Sofía Giménez','sofia.gimenez@ascend.test','SofiGimenez',NULL),
(1028,'Matías Castro','matias.castro@ascend.test','MatiCastro',NULL),
(1029,'Emilia Rojas','emilia.rojas@ascend.test','EmiRojas',NULL),
(1030,'Gabriel Varela','gabriel.varela@ascend.test','GaboVarela',NULL),
(1031,'Renata Molina','renata.molina@ascend.test','RenataM',NULL),
(1032,'Sebastián Prieto','sebastian.prieto@ascend.test','SebaPrieto',NULL),
(1033,'Paula Benítez','paula.benitez@ascend.test','PauBenitez',NULL),
(1034,'Andrés Cabrera','andres.cabrera@ascend.test','AndreCab',NULL),
(1035,'Martina Ferreyra','martina.ferreyra@ascend.test','MartiFer',NULL),
(1036,'Leandro Duarte','leandro.duarte@ascend.test','LeanDuarte',NULL),
(1037,'Carolina Aguirre','carolina.aguirre@ascend.test','CaroAguirre',NULL),
(1038,'Federico Techera','federico.techera@ascend.test','FedeTechera',NULL),
(1039,'Valentina Correa','valentina.correa@ascend.test','ValenCorrea',NULL),
(1040,'Martín Villar','martin.villar@ascend.test','TinchoVillar',NULL),
(1041,'Daniela Ortiz','daniela.ortiz@ascend.test','DaniOrtiz',NULL),
(1042,'Gonzalo Reyes','gonzalo.reyes@ascend.test','GonzaReyes',NULL),
(1043,'Micaela Píriz','micaela.piriz@ascend.test','MicaPiriz',NULL),
(1044,'Emiliano Fagúndez','emiliano.fagundez@ascend.test','EmiFagundez',NULL),
(1045,'Rocío Larrañaga','rocio.larranaga@ascend.test','RochiLar',NULL),
(1046,'Maximiliano Borges','maximiliano.borges@ascend.test','MaxiBorges',NULL),
(1047,'Antonella Cardozo','antonella.cardozo@ascend.test','AntoCardozo',NULL),
(1048,'Joaquín Medina','joaquin.medina@ascend.test','JoacoMedina',NULL),
(1049,'Belén Ibarra','belen.ibarra@ascend.test','BeluIbarra',NULL),
(1050,'Cristian Perdomo','cristian.perdomo@ascend.test','CrisPerdomo',NULL),
(1051,'Mariana Zeballos','mariana.zeballos@ascend.test','MariZeballos',NULL);

DROP TEMPORARY TABLE IF EXISTS _map_equipos;
CREATE TEMPORARY TABLE _map_equipos (
    equipo_id INT PRIMARY KEY,
    nombre_nuevo VARCHAR(120) NOT NULL
);

INSERT INTO _map_equipos (equipo_id, nombre_nuevo) VALUES
(1100,'Nocturnos eSports'),
(1101,'Río de la Plata Gaming'),
(1102,'Bohemios del Este'),
(1103,'Cordón Atlético'),
(1104,'La Vieja Escuela'),
(1105,'Furia Austral'),
(1106,'Guardianes del Este'),
(1107,'Ateneo del Plata'),
(1108,'Deportivo Malvín'),
(1109,'Mano a Mano'),
(1110,'Vanguardia Gaming'),
(1111,'Nexo Austral'),
(1112,'Cóndores de Montevideo'),
(1113,'Titanes del Plata'),
(1114,'Lobos de Malvín'),
(1115,'Halcones de Salto'),
(1116,'Centinelas del Este'),
(1117,'Pumas de Canelones'),
(1118,'Dragones de Maldonado'),
(1119,'Legión Salto'),
(1120,'Fénix Montevideo'),
(1121,'Tormenta Azul'),
(1122,'Soldados del Sur');

DROP TEMPORARY TABLE IF EXISTS _map_torneos;
CREATE TEMPORARY TABLE _map_torneos (
    torneo_id INT PRIMARY KEY,
    nombre_nuevo VARCHAR(150) NOT NULL,
    descripcion_nueva TEXT NOT NULL
);

INSERT INTO _map_torneos (torneo_id, nombre_nuevo, descripcion_nueva) VALUES
(1200,'Liga Apertura de Counter-Strike','Primera fecha del circuito de Counter-Strike 1.6 con tres escuadras y calendario todos contra todos.'),
(1201,'Liga Uruguaya de League of Legends','Liga de League of Legends con tres equipos y enfrentamientos de ida y vuelta.'),
(1202,'Suizo de Básquetbol 3x3','Torneo suizo de básquetbol 3x3 abierto a equipos de todo el país.'),
(1203,'Copa Ciudad de Fútbol 5','Copa de fútbol 5 para equipos amateur de Montevideo.'),
(1204,'Copa de Truco - Llave de Campeones','Llave de truco con enfrentamientos directos y final única.'),
(1205,'Torneo Nocturno de Counter-Strike','Competencia nocturna de Counter-Strike 1.6 con tres equipos y partidas semanales.'),
(1206,'Copa de Plata de League of Legends','Copa de plata de League of Legends con inscripción abierta para equipos amateurs.'),
(1207,'Eliminatoria de Básquetbol 3x3','Llave de eliminación directa de básquetbol 3x3.'),
(1208,'Suizo de Fútbol 5 - Copa Barrio','Copa barrial de fútbol 5 con sistema suizo y cuatro equipos.'),
(1209,'Liga Relámpago de Truco','Liga relámpago de truco con cuatro equipos y partidas todos contra todos.'),
(1210,'Apertura Gamer de Counter-Strike','Apertura del circuito gamer de Counter-Strike 1.6.'),
(1211,'Torneo de Invierno de League of Legends','Torneo de invierno de League of Legends para equipos de la región.'),
(1212,'Liga Metropolitana de Counter-Strike','Liga metropolitana de Counter-Strike 1.6 con ocho escuadras y siete fechas.'),
(1213,'Copa de Campeones de League of Legends','Llave de campeones de League of Legends con cuartos, semifinales y final.'),
(1214,'Abierto de Ajedrez de Montevideo','Abierto de ajedrez con sistema suizo y ocho jugadores.'),
(1215,'Liga Rápida de Ajedrez','Liga rápida de ajedrez con diez jugadores y partidas todos contra todos.'),
(1216,'Copa EA Sports FC 26','Llave individual de EA Sports FC 26 con ocho jugadores.'),
(1217,'Inscripción Abierta - Copa Counter-Strike','Copa de Counter-Strike 1.6 con inscripciones abiertas y cupos limitados.');

-- -----------------------------------------------------------------------------
-- 1. Catálogo de disciplinas (9 juegos, 3 por tipo).
-- -----------------------------------------------------------------------------
START TRANSACTION;

UPDATE juegos SET nombre='Counter-Strike 1.6', categoria='esport-shooter',
    puntos_victoria=3, puntos_empate=1, puntos_derrota=0,
    imagen_url='img/juegos/valorant.jpg', activo=1
WHERE id=1;

UPDATE juegos SET nombre='League of Legends', categoria='esport-moba',
    puntos_victoria=3, puntos_empate=1, puntos_derrota=0,
    imagen_url='img/juegos/dos.jpg', activo=1
WHERE id=2;

UPDATE juegos SET nombre='Básquetbol 3x3', categoria='deporte-fisico',
    puntos_victoria=2, puntos_empate=1, puntos_derrota=0,
    imagen_url='img/juegos/basquet.png', activo=1
WHERE id=3;

UPDATE juegos SET nombre='Fútbol 5', categoria='deporte-fisico',
    puntos_victoria=3, puntos_empate=1, puntos_derrota=0,
    imagen_url='img/juegos/futbol5.jpg', activo=1
WHERE id=4;

UPDATE juegos SET nombre='Ajedrez', categoria='juego-mesa',
    puntos_victoria=1, puntos_empate=0.5, puntos_derrota=0,
    imagen_url='img/juegos/chess.jpg', activo=1
WHERE id=5;

INSERT INTO juegos (id, nombre, categoria, puntos_victoria, puntos_empate, puntos_derrota, imagen_url, activo) VALUES
(101,'Truco','juego-mesa',2,0,0,'img/juegos/truco.jpg',1),
(102,'Catan','juego-mesa',3,0,0,NULL,1),
(103,'Tenis','deporte-fisico',2,0,0,NULL,1),
(104,'EA Sports FC 26','esport',3,1,0,'img/juegos/uno1.jpg',1)
ON DUPLICATE KEY UPDATE
    nombre=VALUES(nombre), categoria=VALUES(categoria),
    puntos_victoria=VALUES(puntos_victoria), puntos_empate=VALUES(puntos_empate),
    puntos_derrota=VALUES(puntos_derrota), imagen_url=VALUES(imagen_url), activo=VALUES(activo);

-- Modalidades permitidas: 2 de equipos + 1 individual por tipo.
DELETE FROM juego_modalidades_permitidas
WHERE juego_id IN (1,2,3,4,5,101,102,103,104);

INSERT INTO juego_modalidades_permitidas (juego_id, modalidad_id) VALUES
(1,2),(2,2),(3,2),(4,2),
(101,2),(102,2),
(5,1),(103,1),(104,1);

-- Formatos permitidos: exactamente dos por disciplina.
DELETE FROM juego_formatos_permitidos
WHERE juego_id IN (1,2,3,4,5,101,102,103,104);

INSERT INTO juego_formatos_permitidos (juego_id, formato_codigo) VALUES
(1,'liga'),(1,'eliminacion_directa'),
(2,'liga'),(2,'eliminacion_directa'),
(3,'suizo'),(3,'eliminacion_directa'),
(4,'liga'),(4,'suizo'),
(5,'suizo'),(5,'liga'),
(101,'liga'),(101,'eliminacion_directa'),
(102,'suizo'),(102,'liga'),
(103,'liga'),(103,'eliminacion_directa'),
(104,'liga'),(104,'eliminacion_directa');

-- -----------------------------------------------------------------------------
-- 2. Corrección de torneos y equipos heredados.
--    Se actualiza el catálogo primero para que los triggers de 007 acepten
--    los nuevos pares juego/modalidad y juego/formato.
-- -----------------------------------------------------------------------------
UPDATE equipos SET juego_id=101 WHERE id IN (1104,1109);

UPDATE torneos SET juego_id=101, formato='eliminacion_directa', modalidad_id=2,
    tipo_resultado='puntos', mejor_de=3
WHERE id=1204;

UPDATE torneos SET juego_id=101, formato='liga', modalidad_id=2,
    tipo_resultado='puntos', mejor_de=1
WHERE id=1209;

UPDATE torneos SET juego_id=104, formato='eliminacion_directa', modalidad_id=1,
    tipo_resultado='goles', mejor_de=1
WHERE id=1216;

-- Puntuación coherente con la configuración predeterminada de cada disciplina.
UPDATE torneos t
JOIN juegos j ON j.id = t.juego_id
SET t.sistema_puntuacion_id = NULL,
    t.puntos_victoria = j.puntos_victoria,
    t.puntos_empate = j.puntos_empate,
    t.puntos_derrota = j.puntos_derrota
WHERE t.id BETWEEN 1200 AND 1225;

-- Tipo de resultado según la disciplina.
UPDATE torneos SET tipo_resultado='puntos' WHERE id IN (1202,1207);
UPDATE torneos SET tipo_resultado='goles' WHERE id IN (1203,1208);
UPDATE torneos SET tipo_resultado='rondas' WHERE id IN (1212,1217);

-- Cupos ajustados a la cantidad real de participantes de los torneos cerrados.
UPDATE torneos SET cupo_max_equipos=3 WHERE id=1200;
UPDATE torneos SET cupo_max_equipos=2 WHERE id=1204;
UPDATE torneos SET cupo_max_equipos=4 WHERE id=1208;
UPDATE torneos SET cupo_max_equipos=4 WHERE id=1209;

-- Organizador acorde a la disciplina del torneo.
UPDATE torneos SET organizador_id=1003 WHERE id=1204;

-- -----------------------------------------------------------------------------
-- 3. Nombres realistas de usuarios, equipos y torneos.
-- -----------------------------------------------------------------------------
UPDATE usuarios SET nombre_completo='Administración ASCEND' WHERE id=1;
UPDATE usuarios SET nombre_completo='Valentina Rodríguez' WHERE id=2;
UPDATE usuarios SET nombre_completo='Marcos Benítez' WHERE id=3;

UPDATE usuarios u
JOIN _map_usuarios m ON m.usuario_id = u.id
SET u.nombre_completo = m.nombre_nuevo,
    u.email = m.email_nuevo;

-- Perfiles de organizador: descripción y sitio coherentes con cada comunidad.
DROP TEMPORARY TABLE IF EXISTS _map_org;
CREATE TEMPORARY TABLE _map_org (
    usuario_id INT PRIMARY KEY,
    bio_organizacion TEXT NOT NULL,
    sitio_web VARCHAR(255) NULL
);

INSERT INTO _map_org (usuario_id, bio_organizacion, sitio_web) VALUES
(1000,'Circuito nacional de eSports con torneos de Counter-Strike, League of Legends y EA Sports FC en Montevideo y el interior.','https://ascend.test/organizadores/circuito-nacional-esports'),
(1001,'Club deportivo de Montevideo dedicado al fútbol 5, el básquetbol 3x3 y el tenis formativo.','https://ascend.test/organizadores/club-atletico-cordon'),
(1002,'Liga universitaria que reúne equipos de distintas facultades en disciplinas individuales y de conjunto.','https://ascend.test/organizadores/liga-universitaria'),
(1003,'Club social y deportivo del Plata, sede de torneos de truco, Catan y fútbol barrial.','https://ascend.test/organizadores/club-del-plata'),
(1004,'Arena gamer de Montevideo con cabinas, streaming y torneos presenciales de deportes electrónicos.','https://ascend.test/organizadores/arena-gamer'),
(1005,'Federación que coordina el calendario nacional de eSports y los procesos de clasificación.','https://ascend.test/organizadores/federacion-esports');

UPDATE perfiles_organizadores po
JOIN _map_org m ON m.usuario_id = po.usuario_id
SET po.bio_organizacion = m.bio_organizacion,
    po.sitio_web = m.sitio_web,
    po.localidad = 'Montevideo',
    po.verificado_oficial = 1;

UPDATE perfiles_jugadores p
JOIN _map_usuarios m ON m.usuario_id = p.usuario_id
SET p.apodo_gamertag = COALESCE(m.apodo_nuevo, p.apodo_gamertag),
    p.discord_tag = CONCAT(COALESCE(m.apodo_nuevo, p.apodo_gamertag), '#UY'),
    p.bio = CASE p.usuario_id % 3
        WHEN 0 THEN 'Jugador de deportes electrónicos con experiencia en torneos universitarios.'
        WHEN 1 THEN 'Aficionado a los juegos de mesa y al deporte competitivo.'
        ELSE 'Competidor amateur de eSports y fútbol 5.'
    END
WHERE m.apodo_nuevo IS NOT NULL;

UPDATE perfiles_organizadores po
JOIN _map_usuarios m ON m.usuario_id = po.usuario_id
SET po.nombre_organizacion = m.organizacion_nueva,
    po.bio_organizacion = 'Organización dedicada a la promoción y organización de torneos en Uruguay.'
WHERE m.organizacion_nueva IS NOT NULL;

UPDATE equipos e
JOIN _map_equipos m ON m.equipo_id = e.id
JOIN juegos j ON j.id = e.juego_id
SET e.nombre_equipo = m.nombre_nuevo,
    e.descripcion = CONCAT('Equipo de ', j.nombre, ' con base en ', e.ubicacion, '.'),
    e.codigo_invitacion = UPPER(LEFT(MD5(CONCAT('ascend-equipo-', e.id)), 8));

UPDATE torneos t
JOIN _map_torneos mt ON mt.torneo_id = t.id
JOIN juegos j ON j.id = t.juego_id
JOIN perfiles_organizadores po ON po.usuario_id = t.organizador_id
SET t.nombre = mt.nombre_nuevo,
    t.descripcion = mt.descripcion_nueva,
    t.reglas = CASE t.formato
        WHEN 'liga' THEN CONCAT('Todos contra todos. Victoria ', FORMAT(t.puntos_victoria,0),
            ' puntos, empate ', FORMAT(t.puntos_empate,1), ', derrota ', FORMAT(t.puntos_derrota,0),
            '. Se consagra quien sume más puntos; la diferencia define los desempates.')
        WHEN 'suizo' THEN CONCAT('Sistema suizo con emparejamientos por puntaje. Victoria ',
            FORMAT(t.puntos_victoria,0), ' puntos, empate ', FORMAT(t.puntos_empate,1),
            ', derrota ', FORMAT(t.puntos_derrota,0), '.')
        ELSE 'Eliminación directa. Quien pierde queda fuera del torneo y el ganador avanza de ronda.'
    END,
    t.premios = CONCAT('["Trofeo ASCEND de ', j.nombre, '","Medalla de plata","Medalla de bronce"]'),
    t.ubicacion = CASE
        WHEN j.categoria LIKE 'esport%' THEN 'Arena Gamer Montevideo'
        WHEN j.categoria = 'deporte-fisico' THEN 'Club Atlético Cordón'
        ELSE 'Club de Juegos de Mesa del Plata'
    END,
    t.comunidad = po.nombre_organizacion
WHERE t.id BETWEEN 1200 AND 1217;

-- Sedes de las canchas programadas.
UPDATE torneo_encuentros
SET cancha = CASE
    WHEN torneo_id IN (1200,1201,1205,1206,1210,1211,1213,1217) THEN 'Arena Gamer Montevideo'
    WHEN torneo_id = 1212 THEN CASE cancha
        WHEN 'Cancha QA 1' THEN 'Arena Gamer - Estación 1'
        ELSE 'Arena Gamer - Estación 2' END
    WHEN torneo_id IN (1202,1207) THEN 'Gimnasio Cordón'
    WHEN torneo_id IN (1203,1208) THEN 'Cancha Parque Rodó'
    WHEN torneo_id IN (1204,1209) THEN 'Salón del Club del Plata'
    WHEN torneo_id IN (1214,1215) THEN 'Sala de Ajedrez'
    WHEN torneo_id = 1216 THEN 'Arena Gamer - Cabina 1'
    ELSE cancha
END
WHERE torneo_id BETWEEN 1200 AND 1225;

-- Fechas coherentes para los torneos heredados de 003/006 que quedaron con
-- calendarios en el pasado pese a estar abiertos, en curso o en borrador.
DROP TEMPORARY TABLE IF EXISTS _map_fechas;
CREATE TEMPORARY TABLE _map_fechas (
    torneo_id INT PRIMARY KEY,
    ini_inscripcion DATE NOT NULL,
    lim_inscripcion DATE NOT NULL,
    inicio DATE NOT NULL,
    fin DATE NOT NULL
);

INSERT INTO _map_fechas (torneo_id, ini_inscripcion, lim_inscripcion, inicio, fin) VALUES
(1201, '2026-08-25', '2026-09-18', '2026-09-25', '2026-11-10'),
(1202, '2026-10-01', '2026-11-08', '2026-11-14', '2026-11-28'),
(1203, '2026-11-01', '2026-11-26', '2026-12-01', '2026-12-12'),
(1205, '2026-09-01', '2026-09-24', '2026-10-01', '2026-11-20'),
(1206, '2026-10-10', '2026-11-18', '2026-11-25', '2026-12-20'),
(1207, '2026-11-05', '2026-11-28', '2026-12-02', '2026-12-12'),
(1210, '2026-10-15', '2026-11-24', '2026-11-30', '2026-12-20'),
(1211, '2026-11-08', '2026-11-30', '2026-12-05', '2026-12-15');

UPDATE torneos t
JOIN _map_fechas f ON f.torneo_id = t.id
SET t.fecha_inicio_inscripcion = f.ini_inscripcion,
    t.fecha_limite_inscripcion = f.lim_inscripcion,
    t.fecha_inicio = f.inicio,
    t.fecha_fin = f.fin;

-- Reubica los encuentros de esos torneos una ronda por día a partir del inicio.
UPDATE torneo_encuentros e
JOIN _map_fechas f ON f.torneo_id = e.torneo_id
SET e.fecha_hora_programada = TIMESTAMP(DATE_ADD(f.inicio, INTERVAL (e.ronda - 1) DAY), '20:00:00');

-- -----------------------------------------------------------------------------
-- 4. Equipos nuevos para completar las disciplinas de equipos.
-- -----------------------------------------------------------------------------
UPDATE equipos SET codigo_invitacion = UPPER(LEFT(MD5(CONCAT('ascend-equipo-', id)), 8))
WHERE id BETWEEN 1100 AND 1122;

INSERT INTO equipos (id, nombre_equipo, juego_id, descripcion, ubicacion, anio_fundacion, creado_por, activo, codigo_invitacion) VALUES
(1123,'Los Gardelitos',101,'Equipo de Truco con base en Montevideo.','Montevideo',2019,1003,1,UPPER(LEFT(MD5('ascend-equipo-1123'),8))),
(1124,'Cuatro Ases',101,'Equipo de Truco con base en Montevideo.','Montevideo',2021,1003,1,UPPER(LEFT(MD5('ascend-equipo-1124'),8))),
(1125,'Colonos del Plata',102,'Equipo de Catan con base en Montevideo.','Montevideo',2020,1003,1,UPPER(LEFT(MD5('ascend-equipo-1125'),8))),
(1126,'Puerto Madera',102,'Equipo de Catan con base en Montevideo.','Montevideo',2022,1003,1,UPPER(LEFT(MD5('ascend-equipo-1126'),8))),
(1127,'Ruta de la Seda',102,'Equipo de Catan con base en Montevideo.','Montevideo',2018,1003,1,UPPER(LEFT(MD5('ascend-equipo-1127'),8))),
(1128,'Isla de Lobos',102,'Equipo de Catan con base en Montevideo.','Montevideo',2023,1003,1,UPPER(LEFT(MD5('ascend-equipo-1128'),8))),
(1129,'Club Atlético Sayago',4,'Equipo de Fútbol 5 con base en Montevideo.','Montevideo',2017,1002,1,UPPER(LEFT(MD5('ascend-equipo-1129'),8))),
(1130,'Racing de Carrasco',4,'Equipo de Fútbol 5 con base en Montevideo.','Montevideo',2015,1002,1,UPPER(LEFT(MD5('ascend-equipo-1130'),8))),
(1131,'Aguada Básquet',3,'Equipo de Básquetbol 3x3 con base en Montevideo.','Montevideo',2016,1002,1,UPPER(LEFT(MD5('ascend-equipo-1131'),8))),
(1132,'Paso Molino Básquet',3,'Equipo de Básquetbol 3x3 con base en Montevideo.','Montevideo',2014,1002,1,UPPER(LEFT(MD5('ascend-equipo-1132'),8)))
ON DUPLICATE KEY UPDATE
    nombre_equipo=VALUES(nombre_equipo), juego_id=VALUES(juego_id),
    descripcion=VALUES(descripcion), ubicacion=VALUES(ubicacion),
    anio_fundacion=VALUES(anio_fundacion), creado_por=VALUES(creado_por),
    activo=VALUES(activo), codigo_invitacion=VALUES(codigo_invitacion);

INSERT IGNORE INTO equipo_miembros (equipo_id, usuario_id, fecha_union, numero_camiseta, posicion, es_activo) VALUES
(1123,1010,'2026-08-01 12:00:00',1,'Capitán',1),
(1123,1011,'2026-08-02 12:00:00',2,'Titular',1),
(1123,1012,'2026-08-03 12:00:00',3,'Suplente',1),
(1124,1013,'2026-08-04 12:00:00',1,'Capitán',1),
(1124,1014,'2026-08-05 12:00:00',2,'Titular',1),
(1124,1015,'2026-08-06 12:00:00',3,'Suplente',1),
(1125,1016,'2026-08-07 12:00:00',1,'Capitán',1),
(1125,1017,'2026-08-08 12:00:00',2,'Titular',1),
(1125,1018,'2026-08-09 12:00:00',3,'Suplente',1),
(1126,1019,'2026-08-10 12:00:00',1,'Capitán',1),
(1126,1020,'2026-08-11 12:00:00',2,'Titular',1),
(1126,1021,'2026-08-12 12:00:00',3,'Suplente',1),
(1127,1022,'2026-08-13 12:00:00',1,'Capitán',1),
(1127,1023,'2026-08-14 12:00:00',2,'Titular',1),
(1127,1024,'2026-08-15 12:00:00',3,'Suplente',1),
(1128,1025,'2026-08-16 12:00:00',1,'Capitán',1),
(1128,1026,'2026-08-17 12:00:00',2,'Titular',1),
(1128,1027,'2026-08-18 12:00:00',3,'Suplente',1),
(1129,1028,'2026-08-19 12:00:00',1,'Capitán',1),
(1129,1029,'2026-08-20 12:00:00',2,'Titular',1),
(1129,1030,'2026-08-21 12:00:00',3,'Suplente',1),
(1130,1031,'2026-08-22 12:00:00',1,'Capitán',1),
(1130,1032,'2026-08-23 12:00:00',2,'Titular',1),
(1130,1033,'2026-08-24 12:00:00',3,'Suplente',1),
(1131,1048,'2026-08-25 12:00:00',1,'Capitán',1),
(1131,1049,'2026-08-26 12:00:00',2,'Titular',1),
(1131,1050,'2026-08-27 12:00:00',3,'Suplente',1),
(1132,1051,'2026-08-28 12:00:00',1,'Capitán',1),
(1132,1045,'2026-08-29 12:00:00',2,'Titular',1),
(1132,1046,'2026-08-30 12:00:00',3,'Suplente',1);

INSERT IGNORE INTO equipo_capitanes (equipo_id, usuario_id, asignado_por, es_capitan_principal) VALUES
(1123,1010,1003,1),
(1124,1013,1003,1),
(1125,1016,1003,1),
(1126,1019,1003,1),
(1127,1022,1003,1),
(1128,1025,1003,1),
(1129,1028,1002,1),
(1130,1031,1002,1),
(1131,1048,1002,1),
(1132,1051,1002,1);

-- -----------------------------------------------------------------------------
-- 5. Torneos nuevos: completan los dos formatos de cada disciplina.
-- -----------------------------------------------------------------------------
INSERT INTO torneos (id, nombre, juego_id, descripcion, formato, estado,
    cupo_max_equipos, cupo_min_equipos, fecha_inicio_inscripcion, fecha_limite_inscripcion,
    fecha_inicio, fecha_fin, reglas, premios, ubicacion, localidad, comunidad,
    organizador_id, modalidad_id, sistema_puntuacion_id, puntos_victoria, puntos_empate,
    puntos_derrota, tipo_resultado, mejor_de, banner_url, stream_url) VALUES
(1218,'Copa de Truco - Fase de Liga',101,
    'Fase de liga de truco entre equipos de barrio, con partidas todos contra todos.',
    'liga','inscripciones_abiertas',8,4,'2026-10-20','2026-11-15','2026-11-20','2026-11-29',
    'Todos contra todos. Victoria 2 puntos, derrota 0. Se consagra quien sume más puntos.',
    '["Trofeo ASCEND de Truco","Medalla de plata","Medalla de bronce"]',
    'Club de Juegos de Mesa del Plata','Montevideo','Club Social y Deportivo del Plata',
    1003,2,NULL,2,0,0,'puntos',1,'img/torneos/card6.png',NULL),
(1219,'Mano a Mano - Copa de Truco',101,
    'Copa de truco con eliminación directa y final a tres manos.',
    'eliminacion_directa','borrador',8,4,'2026-11-01','2026-11-22','2026-11-28','2026-11-29',
    'Eliminación directa al mejor de tres manos. Quien pierde queda fuera.',
    '["Trofeo ASCEND de Truco","Medalla de plata","Medalla de bronce"]',
    'Club de Juegos de Mesa del Plata','Montevideo','Club Social y Deportivo del Plata',
    1003,2,NULL,2,0,0,'puntos',3,'img/torneos/card6.png',NULL),
(1220,'Torneo Suizo de Catan',102,
    'Torneo suizo de Catan para cuatro mesas.',
    'suizo','inscripciones_abiertas',8,4,'2026-10-22','2026-11-18','2026-11-22','2026-11-23',
    'Sistema suizo con emparejamientos por puntaje. Victoria 3 puntos, derrota 0.',
    '["Trofeo ASCEND de Catan","Medalla de plata","Medalla de bronce"]',
    'Club de Juegos de Mesa del Plata','Montevideo','Club Social y Deportivo del Plata',
    1003,2,NULL,3,0,0,'puntos',1,'img/torneos/card05.jpg',NULL),
(1221,'Liga de Catan del Plata',102,
    'Liga de Catan con partidas todos contra todos.',
    'liga','borrador',8,4,'2026-11-05','2026-11-26','2026-12-01','2026-12-06',
    'Todos contra todos. Victoria 3 puntos, derrota 0. La diferencia define los desempates.',
    '["Trofeo ASCEND de Catan","Medalla de plata","Medalla de bronce"]',
    'Club de Juegos de Mesa del Plata','Montevideo','Club Social y Deportivo del Plata',
    1003,2,NULL,3,0,0,'puntos',1,'img/torneos/card05.jpg',NULL),
(1222,'Liga de Tenis Individual',103,
    'Liga individual de tenis con ocho jugadores y formato todos contra todos.',
    'liga','inscripciones_abiertas',8,4,'2026-10-24','2026-11-20','2026-11-24','2026-12-03',
    'Todos contra todos al mejor de tres sets. Victoria 2 puntos, derrota 0.',
    '["Trofeo ASCEND de Tenis","Medalla de plata","Medalla de bronce"]',
    'Club Atlético Cordón','Montevideo','Club Atlético Cordón',
    1001,1,NULL,2,0,0,'puntos',3,'img/torneos/card4.png',NULL),
(1223,'Copa de Tenis - Llave Abierta',103,
    'Llave abierta de tenis individual al mejor de tres sets.',
    'eliminacion_directa','borrador',8,4,'2026-11-10','2026-12-01','2026-12-05','2026-12-06',
    'Eliminación directa al mejor de tres sets. El ganador avanza de ronda.',
    '["Trofeo ASCEND de Tenis","Medalla de plata","Medalla de bronce"]',
    'Club Atlético Cordón','Montevideo','Club Atlético Cordón',
    1001,1,NULL,2,0,0,'puntos',3,'img/torneos/card4.png',NULL),
(1224,'Liga EA Sports FC 26',104,
    'Liga de EA Sports FC 26 con ocho jugadores y calendario completo.',
    'liga','inscripciones_abiertas',8,4,'2026-10-25','2026-11-21','2026-11-25','2026-12-06',
    'Todos contra todos. Victoria 3 puntos, empate 1, derrota 0.',
    '["Trofeo ASCEND de EA Sports FC 26","Medalla de plata","Medalla de bronce"]',
    'Arena Gamer Montevideo','Montevideo','Arena Gamer Montevideo',
    1004,1,NULL,3,1,0,'goles',1,'img/torneos/card5.jpg',NULL),
(1225,'Copa Relámpago de Counter-Strike',1,
    'Copa relámpago de Counter-Strike 1.6 con llave de cuatro equipos.',
    'eliminacion_directa','inscripciones_abiertas',4,4,'2026-10-26','2026-11-22','2026-11-28','2026-11-29',
    'Eliminación directa al mejor de tres mapas. El ganador avanza de ronda.',
    '["Trofeo ASCEND de Counter-Strike 1.6","Medalla de plata","Medalla de bronce"]',
    'Arena Gamer Montevideo','Montevideo','Circuito Nacional de eSports',
    1000,2,NULL,3,1,0,'rondas',3,'img/torneos/card1.jpg',NULL)
ON DUPLICATE KEY UPDATE
    nombre=VALUES(nombre), juego_id=VALUES(juego_id), descripcion=VALUES(descripcion),
    formato=VALUES(formato), estado=VALUES(estado), cupo_max_equipos=VALUES(cupo_max_equipos),
    cupo_min_equipos=VALUES(cupo_min_equipos),
    fecha_inicio_inscripcion=VALUES(fecha_inicio_inscripcion),
    fecha_limite_inscripcion=VALUES(fecha_limite_inscripcion),
    fecha_inicio=VALUES(fecha_inicio), fecha_fin=VALUES(fecha_fin),
    reglas=VALUES(reglas), premios=VALUES(premios), ubicacion=VALUES(ubicacion),
    localidad=VALUES(localidad), comunidad=VALUES(comunidad),
    organizador_id=VALUES(organizador_id), modalidad_id=VALUES(modalidad_id),
    sistema_puntuacion_id=VALUES(sistema_puntuacion_id),
    puntos_victoria=VALUES(puntos_victoria), puntos_empate=VALUES(puntos_empate),
    puntos_derrota=VALUES(puntos_derrota), tipo_resultado=VALUES(tipo_resultado),
    mejor_de=VALUES(mejor_de), banner_url=VALUES(banner_url), stream_url=VALUES(stream_url);

-- Mínimos de cupo coherentes con las reglas del formulario de creación:
-- liga desde 3 participantes, suizo desde 4 y eliminación directa desde 2.
UPDATE torneos
SET cupo_min_equipos = LEAST(cupo_max_equipos, CASE
        WHEN formato = 'liga' THEN GREATEST(COALESCE(cupo_min_equipos, 3), 3)
        WHEN formato = 'suizo' THEN GREATEST(COALESCE(cupo_min_equipos, 4), 4)
        ELSE GREATEST(COALESCE(cupo_min_equipos, 2), 2)
    END)
WHERE id BETWEEN 1200 AND 1225;

-- Participantes nuevos.
INSERT INTO participantes_torneo (id, torneo_id, tipo, referencia_id, nombre, estado,
    confirmado_por, fecha_confirmacion, fecha_inscripcion) VALUES
(1390,1218,'equipo',1104,'La Vieja Escuela','confirmado',1003,'2026-10-24 10:00:00','2026-10-22 10:00:00'),
(1391,1218,'equipo',1109,'Mano a Mano','confirmado',1003,'2026-10-24 10:00:00','2026-10-22 11:00:00'),
(1392,1218,'equipo',1123,'Los Gardelitos','confirmado',1003,'2026-10-25 10:00:00','2026-10-23 10:00:00'),
(1393,1218,'equipo',1124,'Cuatro Ases','confirmado',1003,'2026-10-25 10:00:00','2026-10-23 11:00:00'),
(1394,1222,'usuario',1010,'Mateo Rodríguez','confirmado',1001,'2026-10-26 10:00:00','2026-10-24 10:00:00'),
(1395,1222,'usuario',1011,'Juan Ignacio Pérez','confirmado',1001,'2026-10-26 10:00:00','2026-10-24 10:05:00'),
(1396,1222,'usuario',1012,'Santiago Vidal','confirmado',1001,'2026-10-26 10:00:00','2026-10-24 10:10:00'),
(1397,1222,'usuario',1013,'Agustina López','confirmado',1001,'2026-10-26 10:00:00','2026-10-24 10:15:00'),
(1398,1222,'usuario',1014,'Bruno Silva','confirmado',1001,'2026-10-27 10:00:00','2026-10-25 10:00:00'),
(1399,1222,'usuario',1015,'Camila Torres','confirmado',1001,'2026-10-27 10:00:00','2026-10-25 10:05:00'),
(1400,1222,'usuario',1016,'Nicolás Acosta','confirmado',1001,'2026-10-27 10:00:00','2026-10-25 10:10:00'),
(1401,1222,'usuario',1017,'Valentina Suárez','confirmado',1001,'2026-10-27 10:00:00','2026-10-25 10:15:00'),
(1402,1224,'usuario',1036,'Leandro Duarte','confirmado',1004,'2026-10-26 10:00:00','2026-10-25 10:00:00'),
(1403,1224,'usuario',1037,'Carolina Aguirre','confirmado',1004,'2026-10-26 10:00:00','2026-10-25 10:05:00'),
(1404,1224,'usuario',1038,'Federico Techera','confirmado',1004,'2026-10-26 10:00:00','2026-10-25 10:10:00'),
(1405,1224,'usuario',1039,'Valentina Correa','confirmado',1004,'2026-10-26 10:00:00','2026-10-25 10:15:00'),
(1406,1224,'usuario',1040,'Martín Villar','confirmado',1004,'2026-10-27 10:00:00','2026-10-26 10:00:00'),
(1407,1224,'usuario',1041,'Daniela Ortiz','confirmado',1004,'2026-10-27 10:00:00','2026-10-26 10:05:00'),
(1408,1224,'usuario',1042,'Gonzalo Reyes','confirmado',1004,'2026-10-27 10:00:00','2026-10-26 10:10:00'),
(1409,1224,'usuario',1043,'Micaela Píriz','confirmado',1004,'2026-10-27 10:00:00','2026-10-26 10:15:00'),
(1410,1208,'equipo',1129,'Club Atlético Sayago','confirmado',1002,'2026-09-05 10:00:00','2026-09-01 10:00:00'),
(1411,1208,'equipo',1130,'Racing de Carrasco','confirmado',1002,'2026-09-05 10:00:00','2026-09-01 11:00:00'),
(1412,1209,'equipo',1123,'Los Gardelitos','confirmado',1003,'2026-01-08 10:00:00','2026-01-06 10:00:00'),
(1413,1209,'equipo',1124,'Cuatro Ases','confirmado',1003,'2026-01-08 10:00:00','2026-01-06 11:00:00'),
(1414,1220,'equipo',1125,'Colonos del Plata','confirmado',1003,'2026-10-26 10:00:00','2026-10-23 10:00:00'),
(1415,1220,'equipo',1126,'Puerto Madera','confirmado',1003,'2026-10-26 10:00:00','2026-10-23 11:00:00'),
(1416,1220,'equipo',1127,'Ruta de la Seda','confirmado',1003,'2026-10-27 10:00:00','2026-10-24 10:00:00'),
(1417,1220,'equipo',1128,'Isla de Lobos','confirmado',1003,'2026-10-27 10:00:00','2026-10-24 11:00:00'),
(1420,1225,'equipo',1100,'Nocturnos eSports','confirmado',1000,'2026-10-27 10:00:00','2026-10-26 10:00:00'),
(1421,1225,'equipo',1105,'Furia Austral','confirmado',1000,'2026-10-27 10:00:00','2026-10-26 10:05:00'),
(1422,1225,'equipo',1110,'Vanguardia Gaming','confirmado',1000,'2026-10-27 10:00:00','2026-10-26 10:10:00'),
(1423,1225,'equipo',1122,'Soldados del Sur','confirmado',1000,'2026-10-27 10:00:00','2026-10-26 10:15:00'),
(1424,1202,'equipo',1131,'Aguada Básquet','confirmado',1002,'2026-10-27 09:00:00','2026-10-25 09:00:00'),
(1425,1202,'equipo',1132,'Paso Molino Básquet','confirmado',1002,'2026-10-27 09:10:00','2026-10-25 09:10:00'),
(1426,1203,'equipo',1129,'Club Atlético Sayago','confirmado',1003,'2026-10-27 09:20:00','2026-10-25 09:20:00')
ON DUPLICATE KEY UPDATE
    tipo=VALUES(tipo), referencia_id=VALUES(referencia_id), nombre=VALUES(nombre),
    estado=VALUES(estado), confirmado_por=VALUES(confirmado_por),
    fecha_confirmacion=VALUES(fecha_confirmacion), fecha_inscripcion=VALUES(fecha_inscripcion);

-- Encuentros que completan calendarios de torneos finalizados.
INSERT INTO torneo_encuentros (id, torneo_id, ronda, participante_local_id,
    participante_visitante_id, fecha_hora_programada, cancha, resultado_local,
    resultado_visitante, estado, participante_ganador_id, modificado_por_usuario_id) VALUES
(1620,1200,2,1300,1301,'2026-01-03 20:00:00','Arena Gamer Montevideo',2,0,'finalizado',1300,1000),
(1621,1200,3,1301,1302,'2026-01-04 20:00:00','Arena Gamer Montevideo',1,1,'finalizado',NULL,1000),
(1622,1208,1,1410,1411,'2026-09-09 18:00:00','Cancha Parque Rodó',2,2,'finalizado',NULL,1002),
(1623,1208,2,1320,1410,'2026-09-10 18:00:00','Cancha Parque Rodó',2,1,'finalizado',1320,1002),
(1624,1208,2,1411,1321,'2026-09-10 20:00:00','Cancha Parque Rodó',0,3,'finalizado',1321,1002),
(1625,1209,2,1322,1412,'2026-01-11 18:00:00','Salón del Club del Plata',2,0,'finalizado',1322,1003),
(1626,1209,2,1322,1413,'2026-01-12 18:00:00','Salón del Club del Plata',1,1,'finalizado',NULL,1003),
(1627,1209,3,1323,1412,'2026-01-12 20:00:00','Salón del Club del Plata',3,2,'finalizado',1323,1003),
(1628,1209,3,1323,1413,'2026-01-13 18:00:00','Salón del Club del Plata',2,0,'finalizado',1323,1003),
(1629,1209,3,1412,1413,'2026-01-13 20:00:00','Salón del Club del Plata',2,1,'finalizado',1412,1003)
ON DUPLICATE KEY UPDATE
    ronda=VALUES(ronda), participante_local_id=VALUES(participante_local_id),
    participante_visitante_id=VALUES(participante_visitante_id),
    fecha_hora_programada=VALUES(fecha_hora_programada), cancha=VALUES(cancha),
    resultado_local=VALUES(resultado_local), resultado_visitante=VALUES(resultado_visitante),
    estado=VALUES(estado), participante_ganador_id=VALUES(participante_ganador_id),
    modificado_por_usuario_id=VALUES(modificado_por_usuario_id);

INSERT IGNORE INTO torneo_suizo_parejas (torneo_id, participante_a_id, participante_b_id, ronda, ya_se_enfrentaron, fecha_encuentro) VALUES
(1208,1410,1411,1,0,'2026-09-09 18:00:00'),
(1208,1320,1410,2,0,'2026-09-10 18:00:00'),
(1208,1411,1321,2,0,'2026-09-10 20:00:00');

-- -----------------------------------------------------------------------------
-- 6. Sincronización de nombres guardados como copia (snapshots).
-- -----------------------------------------------------------------------------
UPDATE participantes_torneo p
JOIN equipos e ON p.tipo = 'equipo' AND p.referencia_id = e.id
SET p.nombre = e.nombre_equipo
WHERE p.torneo_id BETWEEN 1200 AND 1225;

UPDATE participantes_torneo p
JOIN usuarios u ON p.tipo = 'usuario' AND p.referencia_id = u.id
SET p.nombre = u.nombre_completo
WHERE p.torneo_id BETWEEN 1200 AND 1225;

UPDATE torneo_comentarios c
JOIN usuarios u ON c.usuario_id = u.id
SET c.nombre_autor = u.nombre_completo,
    c.email_autor = u.email,
    c.comentario = CASE c.id % 4
        WHEN 0 THEN 'Excelente organización y muy buen nivel de juego.'
        WHEN 1 THEN '¿Se publican las posiciones actualizadas después de cada fecha?'
        WHEN 2 THEN 'Gran ambiente en la cancha, felicitaciones a los organizadores.'
        ELSE 'Ya estamos anotados para la próxima fecha.'
    END
WHERE c.torneo_id BETWEEN 1200 AND 1225;

UPDATE torneo_cambio_estado
SET motivo = CASE estado_nuevo
    WHEN 'borrador' THEN 'Creación del torneo en borrador.'
    WHEN 'inscripciones_abiertas' THEN 'Apertura de inscripciones.'
    WHEN 'en_curso' THEN 'Inicio de la competencia.'
    WHEN 'finalizado' THEN 'Cierre del torneo y publicación del podio.'
    WHEN 'cancelado' THEN 'Cancelación del torneo.'
    ELSE 'Actualización del estado del torneo.'
END
WHERE torneo_id BETWEEN 1200 AND 1225;

-- -----------------------------------------------------------------------------
-- 7. Agenda, comentarios, notificaciones, guardados y solicitudes.
--    Todo queda enlazado a los torneos y usuarios reales de la semilla.
-- -----------------------------------------------------------------------------
-- Actividades heredadas de 003: se reemplazan los títulos "QA" y se alinean
-- las fechas con el calendario de cada torneo.
UPDATE torneo_actividades a
JOIN torneos t ON t.id = a.torneo_id
SET a.titulo = CASE a.tipo
        WHEN 'reunion' THEN 'Reunión técnica con capitanes'
        WHEN 'competencia' THEN 'Jornada de competencia'
        WHEN 'premiacion' THEN 'Ceremonia de premiación'
        ELSE a.titulo
    END,
    a.descripcion = CASE a.tipo
        WHEN 'reunion' THEN 'Reunión previa con capitanes para repasar reglamento, horarios y sedes.'
        WHEN 'competencia' THEN 'Control de asistencia, calentamiento y inicio de las partidas programadas.'
        WHEN 'premiacion' THEN 'Entrega de trofeos y medallas a los tres primeros puestos del torneo.'
        ELSE a.descripcion
    END,
    a.fecha = CASE a.tipo
        WHEN 'reunion' THEN DATE_SUB(t.fecha_inicio, INTERVAL 2 DAY)
        WHEN 'competencia' THEN t.fecha_inicio
        WHEN 'premiacion' THEN t.fecha_fin
        ELSE a.fecha
    END
WHERE a.id BETWEEN 1 AND 36
  AND a.torneo_id BETWEEN 1200 AND 1211;

-- Agenda inicial de los torneos nuevos que todavía no tienen actividades.
INSERT INTO torneo_actividades (id, torneo_id, titulo, descripcion, tipo, fecha, hora) VALUES
(2300,1218,'Acreditación de equipos','Presentación de planteles y control de documentos.','administrativo','2026-11-18','18:00:00'),
(2301,1219,'Reunión técnica con capitanes','Repaso del reglamento de la copa y sorteo de la llave.','reunion','2026-11-26','19:00:00'),
(2302,1220,'Acreditación de equipos','Confirmación de mesas y entrega de material de juego.','administrativo','2026-11-20','18:00:00'),
(2303,1221,'Reunión técnica con capitanes','Presentación del calendario de la liga y reglas de desempate.','reunion','2026-11-29','19:00:00'),
(2304,1222,'Acreditación de jugadores','Control de inscripciones y sorteo del cuadro de la liga.','administrativo','2026-11-22','18:00:00'),
(2305,1223,'Reunión técnica con jugadores','Explicación del formato de llave y horarios de los partidos.','reunion','2026-12-03','19:00:00'),
(2306,1224,'Acreditación de jugadores','Verificación de cuentas y configuración de las consolas.','administrativo','2026-11-23','18:00:00'),
(2307,1225,'Acreditación de equipos','Control de planteles y sorteo de la llave relámpago.','administrativo','2026-11-26','18:00:00')
ON DUPLICATE KEY UPDATE
    titulo=VALUES(titulo), descripcion=VALUES(descripcion), tipo=VALUES(tipo),
    fecha=VALUES(fecha), hora=VALUES(hora);

-- Comentarios adicionales en los torneos de la semilla ampliada.
INSERT INTO torneo_comentarios (id, torneo_id, usuario_id, nombre_autor, email_autor, comentario, aprobado, fecha_hora) VALUES
(100,1212,1010,'Mateo Rodríguez','mateo.rodriguez@ascend.test','La liga está muy pareja; la última fecha definió el podio.','1','2026-10-20 18:30:00'),
(101,1212,1011,'Juan Ignacio Pérez','juan.perez@ascend.test','Excelente transmisión de la fecha 7, se entendió todo el bracket.','1','2026-10-21 10:15:00'),
(102,1213,1012,'Santiago Vidal','santiago.vidal@ascend.test','La llave de campeones tuvo series muy ajustadas.','1','2026-10-18 20:00:00'),
(103,1213,1013,'Agustina López','agustina.lopez@ascend.test','Felicitaciones a los organizadores por la puntualidad.','1','2026-10-19 09:40:00'),
(104,1214,1014,'Bruno Silva','bruno.silva@ascend.test','¿La ronda 4 se juega en la misma sala de ajedrez?','1','2026-10-22 17:20:00'),
(105,1214,1015,'Camila Torres','camila.torres@ascend.test','Muy buen nivel del abierto, las partidas fueron intensas.','1','2026-10-23 11:05:00'),
(106,1215,1016,'Nicolás Acosta','nicolas.acosta@ascend.test','La liga rápida es ideal para jugar después del trabajo.','1','2026-10-24 21:10:00'),
(107,1215,1017,'Valentina Suárez','valentina.suarez@ascend.test','¿Se puede consultar el historial de cada jugador?','1','2026-10-25 08:30:00'),
(108,1216,1018,'Diego Fernández','diego.fernandez@ascend.test','La final de EA Sports FC estuvo increíble.','1','2026-09-08 22:00:00'),
(109,1216,1019,'Martina Cabrera','martina.cabrera@ascend.test','Gran organización y muy buena calidad de las cabinas.','1','2026-09-09 10:00:00'),
(110,1217,1020,'Facundo Núñez','facundo.nunez@ascend.test','Ya reservamos el lugar para la Copa Counter-Strike.','1','2026-10-26 19:00:00'),
(111,1217,1021,'Julieta Pereira','julieta.pereira@ascend.test','¿Quedan cupos para equipos del interior?','1','2026-10-27 09:30:00'),
(112,1218,1022,'Rodrigo Sosa','rodrigo.sosa@ascend.test','Nos anotamos con Los Gardelitos para la fase de liga.','1','2026-10-24 12:00:00'),
(113,1220,1023,'Florencia Méndez','florencia.mendez@ascend.test','¿Las mesas se sortean antes de cada ronda?','1','2026-10-25 10:30:00'),
(114,1222,1024,'Ignacio Olivera','ignacio.olivera@ascend.test','La liga de tenis tiene muy buen nivel este año.','1','2026-10-26 16:00:00'),
(115,1224,1025,'Lucía Ramos','lucia.ramos@ascend.test','¿Se puede jugar con mando propio en la liga de EA FC?','1','2026-10-27 11:00:00'),
(116,1225,1026,'Tomás Barrios','tomas.barrios@ascend.test','La copa relámpago es ideal para probar la escuadra nueva.','1','2026-10-27 18:00:00')
ON DUPLICATE KEY UPDATE
    torneo_id=VALUES(torneo_id), usuario_id=VALUES(usuario_id),
    nombre_autor=VALUES(nombre_autor), email_autor=VALUES(email_autor),
    comentario=VALUES(comentario), aprobado=VALUES(aprobado),
    fecha_hora=VALUES(fecha_hora);

-- Bandeja de los jugadores: tres avisos por cuenta, enlazados a un torneo en el
-- que participan (o a uno recomendado si todavía no se inscribieron).
DELETE FROM notificaciones WHERE id BETWEEN 1 AND 126;

INSERT INTO notificaciones (id, usuario_id, tipo, titulo, mensaje, enlace_relacionado, leido)
SELECT
    1 + (x.usuario_id - 1010) * 3 + x.n,
    x.usuario_id,
    CASE x.n WHEN 0 THEN 'inscripcion' WHEN 1 THEN 'partido' ELSE 'resultado' END,
    CASE x.n
        WHEN 0 THEN IF(x.participa,
            CONCAT('Inscripción confirmada: ', x.torneo_nombre),
            CONCAT('Torneo recomendado: ', x.torneo_nombre))
        WHEN 1 THEN CONCAT('Calendario publicado: ', x.torneo_nombre)
        ELSE IF(x.tiene_posiciones,
            CONCAT('Posiciones actualizadas: ', x.torneo_nombre),
            CONCAT('Convocatoria abierta: ', x.torneo_nombre))
    END,
    CASE x.n
        WHEN 0 THEN IF(x.participa,
            CONCAT('Tu lugar en ', x.torneo_nombre, ' quedó confirmado. Revisá el calendario y la sede.'),
            CONCAT('Se abrió la inscripción de ', x.torneo_nombre, '. Consultá las bases y los cupos disponibles.'))
        WHEN 1 THEN CONCAT('La organización publicó el calendario de ', x.torneo_nombre, '. Consultá día, hora y cancha.')
        ELSE IF(x.tiene_posiciones,
            CONCAT('Ya podés ver la tabla de posiciones y los últimos resultados de ', x.torneo_nombre, '.'),
            CONCAT('Las inscripciones de ', x.torneo_nombre, ' siguen abiertas. ¡Sumate con tu equipo!'))
    END,
    CONCAT('index.php?c=torneo&a=detalle&id=', x.torneo_id),
    CASE WHEN x.n = 0 THEN 0 ELSE 1 END
FROM (
    SELECT
        u.id AS usuario_id,
        n.n,
        t.id AS torneo_id,
        t.nombre AS torneo_nombre,
        EXISTS (
            SELECT 1
            FROM participantes_torneo p
            LEFT JOIN equipo_miembros em
                   ON p.tipo = 'equipo' AND em.equipo_id = p.referencia_id
            WHERE p.torneo_id = t.id
              AND ((p.tipo = 'usuario' AND p.referencia_id = u.id)
                   OR (p.tipo = 'equipo' AND em.usuario_id = u.id))
        ) AS participa,
        EXISTS (SELECT 1 FROM torneo_posiciones tp WHERE tp.torneo_id = t.id) AS tiene_posiciones
    FROM usuarios u
    CROSS JOIN (SELECT 0 AS n UNION ALL SELECT 1 UNION ALL SELECT 2) n
    JOIN torneos t ON t.id = COALESCE(
        (SELECT p.torneo_id
         FROM participantes_torneo p
         WHERE p.tipo = 'usuario' AND p.referencia_id = u.id
           AND p.torneo_id BETWEEN 1200 AND 1225
         ORDER BY p.id LIMIT 1),
        (SELECT p.torneo_id
         FROM participantes_torneo p
         JOIN equipo_miembros em ON em.equipo_id = p.referencia_id
         WHERE p.tipo = 'equipo' AND em.usuario_id = u.id
           AND p.torneo_id BETWEEN 1200 AND 1225
         ORDER BY p.id LIMIT 1),
        1217
    )
    WHERE u.id BETWEEN 1010 AND 1051
) x;

-- Bandeja de los organizadores: solicitudes, calendario y estado del torneo.
DELETE FROM notificaciones WHERE id BETWEEN 200 AND 217;

INSERT INTO notificaciones (id, usuario_id, tipo, titulo, mensaje, enlace_relacionado, leido)
SELECT
    200 + (x.usuario_id - 1000) * 3 + x.n,
    x.usuario_id,
    CASE x.n WHEN 0 THEN 'inscripcion' WHEN 1 THEN 'info' ELSE 'resultado' END,
    CASE x.n
        WHEN 0 THEN CONCAT('Solicitudes pendientes: ', x.torneo_nombre)
        WHEN 1 THEN CONCAT('Calendario activo: ', x.torneo_nombre)
        ELSE CONCAT('Estado del torneo: ', x.torneo_nombre)
    END,
    CASE x.n
        WHEN 0 THEN CONCAT('Revisá las solicitudes de ingreso y las inscripciones de ', x.torneo_nombre, ' desde el panel de administración.')
        WHEN 1 THEN CONCAT('El calendario de ', x.torneo_nombre, ' está publicado. Verificá sedes y horarios antes de la próxima fecha.')
        ELSE CONCAT(x.torneo_nombre, ' está en estado ', x.estado, '. Actualizá resultados y posiciones cuando corresponda.')
    END,
    CONCAT('index.php?c=panelOrganizador&a=administrar&id=', x.torneo_id),
    CASE WHEN x.n = 0 THEN 0 ELSE 1 END
FROM (
    SELECT
        po.usuario_id,
        n.n,
        t.id AS torneo_id,
        t.nombre AS torneo_nombre,
        t.estado
    FROM perfiles_organizadores po
    CROSS JOIN (SELECT 0 AS n UNION ALL SELECT 1 UNION ALL SELECT 2) n
    JOIN torneos t ON t.id = (
        SELECT t2.id FROM torneos t2
        WHERE t2.organizador_id = po.usuario_id
        ORDER BY t2.id DESC LIMIT 1
    )
    WHERE po.usuario_id BETWEEN 1000 AND 1005
) x;

-- Guardados: cada jugador conserva un torneo y un equipo en su lista.
DELETE FROM guardados WHERE id BETWEEN 2700 AND 2799;

INSERT IGNORE INTO guardados (id, usuario_id, tipo, referencia_id, fecha_guardado)
SELECT 2700 + (u.id - 1010) * 2, u.id, 'torneo',
       1200 + ((u.id - 1010) % 26), '2026-10-26 19:46:20'
FROM usuarios u
WHERE u.id BETWEEN 1010 AND 1051
UNION ALL
SELECT 2701 + (u.id - 1010) * 2, u.id, 'equipo',
       1100 + ((u.id - 1010) % 23), '2026-10-26 19:46:20'
FROM usuarios u
WHERE u.id BETWEEN 1010 AND 1051;

-- Solicitudes de equipo: estados pendientes, aprobados y rechazados para que
-- las bandejas de jugadores y organizadores tengan casos reales.
INSERT INTO solicitudes_equipo
    (id, equipo_id, usuario_id, iniciado_por, mensaje, estado, fecha_solicitud, fecha_respuesta, respondido_por) VALUES
(2810,1123,1034,1034,'Quiero sumarme al plantel para la temporada 2026.','pendiente','2026-10-21 10:00:00',NULL,NULL),
(2811,1124,1035,1035,'Soy jugador de truco y tengo disponibilidad los fines de semana.','pendiente','2026-10-21 11:00:00',NULL,NULL),
(2812,1125,1037,1037,'Me interesa participar en la liga de Catan con el equipo.','pendiente','2026-10-22 09:30:00',NULL,NULL),
(2813,1126,1038,1038,'Tengo experiencia en torneos de mesa y puedo cubrir las fechas.','pendiente','2026-10-22 10:15:00',NULL,NULL),
(2814,1127,1039,1039,'Solicitud aprobada por la capitanía del equipo.','aprobado','2026-10-18 12:00:00','2026-10-19 12:00:00',1003),
(2815,1128,1040,1040,'Solicitud rechazada por cupo completo en el plantel.','rechazado','2026-10-17 12:00:00','2026-10-18 12:00:00',1003),
(2816,1129,1041,1041,'Quiero jugar en el equipo de fútbol 5 del club.','pendiente','2026-10-23 14:00:00',NULL,NULL),
(2817,1130,1042,1042,'El capitán aprobó mi ingreso después de la prueba.','aprobado','2026-10-16 16:00:00','2026-10-17 16:00:00',1002),
(2818,1131,1043,1043,'Me interesa el torneo de básquetbol 3x3.','pendiente','2026-10-24 10:00:00',NULL,NULL),
(2819,1132,1044,1044,'Solicitud rechazada porque el plantel ya estaba completo.','rechazado','2026-10-15 09:00:00','2026-10-16 09:00:00',1002)
ON DUPLICATE KEY UPDATE
    equipo_id=VALUES(equipo_id), usuario_id=VALUES(usuario_id),
    iniciado_por=VALUES(iniciado_por), mensaje=VALUES(mensaje),
    estado=VALUES(estado), fecha_solicitud=VALUES(fecha_solicitud),
    fecha_respuesta=VALUES(fecha_respuesta), respondido_por=VALUES(respondido_por);

-- Avisos especiales existentes, alineados con los nombres y torneos nuevos.
UPDATE notificaciones SET titulo='Solicitudes pendientes en Copa Counter-Strike',
    mensaje='Hay 3 equipos esperando aprobación en la Copa Counter-Strike.'
WHERE id=2600;
UPDATE notificaciones SET titulo='Liga Metropolitana completada',
    mensaje='La Liga Metropolitana de Counter-Strike finalizó con 28 partidos jugados.'
WHERE id=2601;
UPDATE notificaciones SET titulo='Copa de Campeones finalizada',
    mensaje='La llave de 8 equipos quedó completa.'
WHERE id=2602;
UPDATE notificaciones SET mensaje='Guardaste la Copa Counter-Strike en tu lista.'
WHERE id=2603;
UPDATE notificaciones SET titulo='Tu equipo ganó la Liga Metropolitana',
    mensaje='Nocturnos eSports terminó primero en la Liga Metropolitana de Counter-Strike.'
WHERE id=2604;
UPDATE notificaciones SET mensaje='La ronda 3 del Abierto de Ajedrez de Montevideo está programada para el 24/10.'
WHERE id=2605;
UPDATE notificaciones SET mensaje='Guardaste el torneo Liga Metropolitana de Counter-Strike.'
WHERE id=2606;

-- -----------------------------------------------------------------------------
-- 8. Recalculo de posiciones (mismo criterio que las semillas 003 y 006).
-- -----------------------------------------------------------------------------
DELETE FROM torneo_posiciones WHERE torneo_id BETWEEN 1200 AND 1225;

INSERT INTO torneo_posiciones
    (torneo_id, participante_id, partidos_jugados, partidos_ganados,
     partidos_empatados, partidos_perdidos, puntos_favor, puntos_contra,
     puntos, sanciones_puntos)
SELECT
    p.torneo_id,
    p.id,
    COALESCE(e.partidos_jugados, 0),
    COALESCE(e.partidos_ganados, 0),
    COALESCE(e.partidos_empatados, 0),
    COALESCE(e.partidos_perdidos, 0),
    COALESCE(e.puntos_favor, 0),
    COALESCE(e.puntos_contra, 0),
    COALESCE(e.partidos_ganados, 0) * t.puntos_victoria
        + COALESCE(e.partidos_empatados, 0) * t.puntos_empate
        + COALESCE(e.partidos_perdidos, 0) * t.puntos_derrota,
    0
FROM participantes_torneo p
INNER JOIN torneos t ON t.id = p.torneo_id
LEFT JOIN (
    SELECT
        lados.torneo_id,
        lados.participante_id,
        COUNT(*) AS partidos_jugados,
        SUM(lados.gano) AS partidos_ganados,
        SUM(lados.empato) AS partidos_empatados,
        SUM(CASE WHEN lados.gano = 0 AND lados.empato = 0 THEN 1 ELSE 0 END) AS partidos_perdidos,
        SUM(lados.puntos_favor) AS puntos_favor,
        SUM(lados.puntos_contra) AS puntos_contra
    FROM (
        SELECT e.torneo_id,
               e.participante_local_id AS participante_id,
               CASE WHEN e.participante_ganador_id = e.participante_local_id
                          OR e.resultado_local > e.resultado_visitante
                    THEN 1 ELSE 0 END AS gano,
               CASE WHEN e.participante_ganador_id IS NULL
                          AND e.resultado_local = e.resultado_visitante
                    THEN 1 ELSE 0 END AS empato,
               e.resultado_local AS puntos_favor,
               e.resultado_visitante AS puntos_contra
        FROM torneo_encuentros e
        WHERE e.estado = 'finalizado'
          AND e.torneo_id BETWEEN 1200 AND 1225
        UNION ALL
        SELECT e.torneo_id,
               e.participante_visitante_id,
               CASE WHEN e.participante_ganador_id = e.participante_visitante_id
                          OR e.resultado_visitante > e.resultado_local
                    THEN 1 ELSE 0 END,
               CASE WHEN e.participante_ganador_id IS NULL
                          AND e.resultado_visitante = e.resultado_local
                    THEN 1 ELSE 0 END,
               e.resultado_visitante,
               e.resultado_local
        FROM torneo_encuentros e
        WHERE e.estado = 'finalizado'
          AND e.torneo_id BETWEEN 1200 AND 1225
    ) AS lados
    GROUP BY lados.torneo_id, lados.participante_id
) AS e ON e.torneo_id = p.torneo_id AND e.participante_id = p.id
WHERE p.torneo_id BETWEEN 1200 AND 1225
  AND p.estado = 'confirmado';

-- Podios de los torneos finalizados, calculados con el mismo orden que la app.
REPLACE INTO torneo_config (torneo_id, clave, valor)
SELECT x.torneo_id, 'podio', JSON_OBJECT(
    'primero', (SELECT p.nombre FROM torneo_posiciones tp
                JOIN participantes_torneo p ON p.id = tp.participante_id
                WHERE tp.torneo_id = x.torneo_id
                ORDER BY tp.puntos DESC, tp.diferencia_goles DESC, tp.puntos_favor DESC, p.id ASC
                LIMIT 1),
    'segundo', (SELECT p.nombre FROM torneo_posiciones tp
                JOIN participantes_torneo p ON p.id = tp.participante_id
                WHERE tp.torneo_id = x.torneo_id
                ORDER BY tp.puntos DESC, tp.diferencia_goles DESC, tp.puntos_favor DESC, p.id ASC
                LIMIT 1 OFFSET 1),
    'tercero', (SELECT p.nombre FROM torneo_posiciones tp
                JOIN participantes_torneo p ON p.id = tp.participante_id
                WHERE tp.torneo_id = x.torneo_id
                ORDER BY tp.puntos DESC, tp.diferencia_goles DESC, tp.puntos_favor DESC, p.id ASC
                LIMIT 1 OFFSET 2))
FROM (SELECT DISTINCT torneo_id FROM torneo_posiciones
      WHERE torneo_id IN (1200,1204,1208,1209,1212,1213,1216)) x;

-- -----------------------------------------------------------------------------
-- 9. Contraseña genérica de demostración para todas las cuentas sembradas.
--    El hash corresponde a la contraseña documentada en
--    Documentos_Juan/Usuarios_Sembrados.md. No usar en producción.
-- -----------------------------------------------------------------------------
UPDATE usuarios
SET contrasena_hash = '$2y$10$o2QIwArbRzL593ulaSwjzu4GvQiSoidHnkwmPvcEAatPldHMvuhWC'
WHERE id IN (1,2,3) OR id BETWEEN 1000 AND 1051;

COMMIT;

-- Fin de 008_semilla_consistente.sql
