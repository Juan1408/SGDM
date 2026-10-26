-- 006_seed_qa_ampliado.sql
-- Amplía la semilla QA de 003 con 6 torneos, 11 equipos y datos de todos los formatos
-- (liga completa, eliminación directa y suizo; modalidad de equipos e individual).
-- Incluye inscripciones pendientes/rechazadas/canceladas, guardados, solicitudes de equipo,
-- agenda, historial de estados, notificaciones, podios y posiciones recalculadas.
-- Idempotente: usa IDs fijos e INSERT IGNORE; solo reinicia las posiciones de 1212-1217.

USE ascend;
SET NAMES utf8mb4;

INSERT IGNORE INTO equipos (id,nombre_equipo,juego_id,descripcion,ubicacion,anio_fundacion,creado_por,activo,codigo_invitacion) VALUES
(1112,'Escuadra QA 13',1,'Equipo sintético de cobertura QA para llaves, ligas y solicitudes','Montevideo',2024,1000,1,'QA1112'),
(1113,'Escuadra QA 14',1,'Equipo sintético de cobertura QA para llaves, ligas y solicitudes','Canelones',2025,1001,1,'QA1113'),
(1114,'Escuadra QA 15',1,'Equipo sintético de cobertura QA para llaves, ligas y solicitudes','Maldonado',2024,1002,1,'QA1114'),
(1115,'Escuadra QA 16',1,'Equipo sintético de cobertura QA para llaves, ligas y solicitudes','Salto',2025,1003,1,'QA1115'),
(1116,'Escuadra QA 17',1,'Equipo sintético de cobertura QA para llaves, ligas y solicitudes','Montevideo',2024,1004,1,'QA1116'),
(1117,'Escuadra QA 18',2,'Equipo sintético de cobertura QA para llaves, ligas y solicitudes','Canelones',2025,1005,1,'QA1117'),
(1118,'Escuadra QA 19',2,'Equipo sintético de cobertura QA para llaves, ligas y solicitudes','Maldonado',2024,1000,1,'QA1118'),
(1119,'Escuadra QA 20',2,'Equipo sintético de cobertura QA para llaves, ligas y solicitudes','Salto',2025,1001,1,'QA1119'),
(1120,'Escuadra QA 21',2,'Equipo sintético de cobertura QA para llaves, ligas y solicitudes','Montevideo',2024,1002,1,'QA1120'),
(1121,'Escuadra QA 22',2,'Equipo sintético de cobertura QA para llaves, ligas y solicitudes','Canelones',2025,1003,1,'QA1121'),
(1122,'Escuadra QA 23',1,'Equipo sintético de cobertura QA para llaves, ligas y solicitudes','Maldonado',2024,1000,1,'QA1122');

INSERT IGNORE INTO equipo_miembros (equipo_id,usuario_id,fecha_union,numero_camiseta,posicion,es_activo) VALUES
(1112,1010,'2026-06-01 12:00:00',1,'Titular',1),
(1112,1011,'2026-06-02 12:00:00',2,'Defensa',1),
(1112,1012,'2026-06-03 12:00:00',3,'Atacante',1),
(1112,1013,'2026-06-04 12:00:00',4,'Suplente',1),
(1112,1014,'2026-06-05 12:00:00',5,'Capitán',1),
(1113,1013,'2026-06-03 12:00:00',1,'Titular',1),
(1113,1014,'2026-06-04 12:00:00',2,'Defensa',1),
(1113,1015,'2026-06-05 12:00:00',3,'Atacante',1),
(1113,1016,'2026-06-06 12:00:00',4,'Suplente',1),
(1113,1017,'2026-06-07 12:00:00',5,'Capitán',1),
(1114,1016,'2026-06-05 12:00:00',1,'Titular',1),
(1114,1017,'2026-06-06 12:00:00',2,'Defensa',1),
(1114,1018,'2026-06-07 12:00:00',3,'Atacante',1),
(1114,1019,'2026-06-08 12:00:00',4,'Suplente',1),
(1114,1020,'2026-06-09 12:00:00',5,'Capitán',1),
(1115,1019,'2026-06-07 12:00:00',1,'Titular',1),
(1115,1020,'2026-06-08 12:00:00',2,'Defensa',1),
(1115,1021,'2026-06-09 12:00:00',3,'Atacante',1),
(1115,1022,'2026-06-10 12:00:00',4,'Suplente',1),
(1115,1023,'2026-06-11 12:00:00',5,'Capitán',1),
(1116,1022,'2026-06-09 12:00:00',1,'Titular',1),
(1116,1023,'2026-06-10 12:00:00',2,'Defensa',1),
(1116,1024,'2026-06-11 12:00:00',3,'Atacante',1),
(1116,1025,'2026-06-12 12:00:00',4,'Suplente',1),
(1116,1026,'2026-06-13 12:00:00',5,'Capitán',1),
(1117,1025,'2026-06-11 12:00:00',1,'Titular',1),
(1117,1026,'2026-06-12 12:00:00',2,'Defensa',1),
(1117,1027,'2026-06-13 12:00:00',3,'Atacante',1),
(1117,1028,'2026-06-14 12:00:00',4,'Suplente',1),
(1117,1029,'2026-06-15 12:00:00',5,'Capitán',1),
(1118,1028,'2026-06-13 12:00:00',1,'Titular',1),
(1118,1029,'2026-06-14 12:00:00',2,'Defensa',1),
(1118,1030,'2026-06-15 12:00:00',3,'Atacante',1),
(1118,1031,'2026-06-16 12:00:00',4,'Suplente',1),
(1118,1032,'2026-06-17 12:00:00',5,'Capitán',1),
(1119,1031,'2026-06-15 12:00:00',1,'Titular',1),
(1119,1032,'2026-06-16 12:00:00',2,'Defensa',1),
(1119,1033,'2026-06-17 12:00:00',3,'Atacante',1),
(1119,1034,'2026-06-18 12:00:00',4,'Suplente',1),
(1119,1035,'2026-06-19 12:00:00',5,'Capitán',1),
(1120,1034,'2026-06-17 12:00:00',1,'Titular',1),
(1120,1035,'2026-06-18 12:00:00',2,'Defensa',1),
(1120,1036,'2026-06-19 12:00:00',3,'Atacante',1),
(1120,1037,'2026-06-20 12:00:00',4,'Suplente',1),
(1120,1038,'2026-06-21 12:00:00',5,'Capitán',1),
(1121,1037,'2026-06-19 12:00:00',1,'Titular',1),
(1121,1038,'2026-06-20 12:00:00',2,'Defensa',1),
(1121,1039,'2026-06-21 12:00:00',3,'Atacante',1),
(1121,1040,'2026-06-22 12:00:00',4,'Suplente',1),
(1121,1041,'2026-06-23 12:00:00',5,'Capitán',1),
(1122,1040,'2026-06-21 12:00:00',1,'Titular',1),
(1122,1041,'2026-06-22 12:00:00',2,'Defensa',1),
(1122,1042,'2026-06-23 12:00:00',3,'Atacante',1),
(1122,1043,'2026-06-24 12:00:00',4,'Suplente',1),
(1122,1044,'2026-06-25 12:00:00',5,'Capitán',1);

INSERT IGNORE INTO equipo_capitanes (equipo_id,usuario_id,asignado_por,es_capitan_principal) VALUES
(1112,1014,1000,1),
(1113,1017,1001,1),
(1114,1020,1002,1),
(1115,1023,1003,1),
(1116,1026,1004,1),
(1117,1029,1005,1),
(1118,1032,1000,1),
(1119,1035,1001,1),
(1120,1038,1002,1),
(1121,1041,1003,1),
(1122,1044,1000,1);

INSERT IGNORE INTO torneos (id,nombre,juego_id,descripcion,formato,estado,cupo_max_equipos,cupo_min_equipos,fecha_inicio_inscripcion,fecha_limite_inscripcion,fecha_inicio,fecha_fin,reglas,premios,ubicacion,localidad,comunidad,organizador_id,modalidad_id,sistema_puntuacion_id,puntos_victoria,puntos_empate,puntos_derrota,tipo_resultado,mejor_de,banner_url,stream_url) VALUES
(1212,'Liga QA Completa 8',1,'Liga QA de 8 equipos con calendario completo todos contra todos. Sirve para validar posiciones, diferencia de goles y la tabla pública.','liga','finalizado',8,8,'2026-07-01','2026-07-31','2026-08-01','2026-08-07','Cada equipo juega una vez contra cada rival. Victoria 3 puntos, empate 1, derrota 0. Resultados sintéticos para QA.','["Copa QA Liga","Subcampeonato QA","Tercer puesto QA"]','Arena QA','Montevideo','Uruguay',1000,2,1,3,1,0,'goles',1,'img/torneos/card1.jpg',NULL),
(1213,'Eliminatoria QA Equipos 8',2,'Llave de eliminación directa de 8 equipos con cuartos, semifinales y final. Sirve para validar el bracket completo y el podio.','eliminacion_directa','finalizado',8,8,'2026-08-15','2026-08-30','2026-09-01','2026-09-03','Eliminación directa al mejor de 3 mapas. El ganador avanza de ronda. Resultados sintéticos para QA.','["Trofeo QA Eliminatoria","Medalla de plata QA","Medalla de bronce QA"]','Arena QA','Montevideo','Uruguay',1001,2,3,2,1,0,'rondas',3,'img/torneos/card2.png','https://www.twitch.tv/ascendqa'),
(1214,'Suizo QA Individual 8',5,'Sistema suizo individual de 8 jugadores con tres rondas. Las dos primeras están finalizadas y la tercera queda programada para probar próximos encuentros.','suizo','en_curso',16,4,'2026-09-20','2026-10-05','2026-10-10','2026-10-24','Emparejamientos suizos por puntaje. Victoria 1 punto, tablas 0.5, derrota 0. Sin bye: cantidad par de participantes.','["Trofeo QA Suizo","Medalla QA","Diploma QA"]','Arena QA','Montevideo','Uruguay',1002,1,2,1,0.5,0,'puntos',3,'img/torneos/card3.png','https://www.youtube.com/@ascendqa'),
(1215,'Liga QA Individual 10',5,'Liga individual de 10 jugadores con tres fechas parciales. Sirve para validar la grilla de participantes con "Cargar más" y la tabla de 10 posiciones.','liga','en_curso',16,4,'2026-09-25','2026-10-10','2026-10-12','2026-10-26','Todos contra todos en tres fechas parciales. Victoria 3 puntos, empate 1, derrota 0. Resultados sintéticos para QA.','["Copa QA Individual","Subcampeonato QA","Tercer puesto QA"]','Arena QA','Montevideo','Uruguay',1003,1,1,3,1,0,'puntos',1,'img/torneos/card4.png',NULL),
(1216,'Eliminatoria QA Individual 8',5,'Llave individual de 8 jugadores con cuartos, semifinales y final. Sirve para validar el bracket individual y el podio.','eliminacion_directa','finalizado',8,8,'2026-08-20','2026-09-04','2026-09-05','2026-09-07','Eliminación directa a partido único. El ganador avanza de ronda. Resultados sintéticos para QA.','["Trofeo QA Individual","Medalla QA","Diploma QA"]','Arena QA','Montevideo','Uruguay',1004,1,2,1,0.5,0,'puntos',1,'img/torneos/card5.jpg','https://www.youtube.com/watch?v=qa-ascend'),
(1217,'Abierta QA Solicitudes',1,'Torneo abierto con inscripciones confirmadas, pendientes, rechazadas y canceladas. Sirve para validar la bandeja de solicitudes del organizador y los próximos encuentros.','liga','inscripciones_abiertas',8,4,'2026-10-15','2026-11-05','2026-11-10','2026-11-14','Inscripciones abiertas hasta el 5 de noviembre. Los cupos se confirman por aprobación del organizador. Datos sintéticos para QA.','["Copa QA Abierta","Subcampeonato QA","Tercer puesto QA"]','Arena QA','Montevideo','Uruguay',1000,2,1,3,1,0,'goles',1,'img/torneos/card6.png',NULL);

INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion,fecha_inscripcion) VALUES
(1330,1212,'equipo',1100,'Escuadra QA 01','confirmado',1000,'2026-07-10 10:00:00','2026-07-05 10:00:00'),
(1331,1212,'equipo',1105,'Escuadra QA 06','confirmado',1000,'2026-07-10 10:00:00','2026-07-05 10:00:00'),
(1332,1212,'equipo',1110,'Escuadra QA 11','confirmado',1000,'2026-07-10 10:00:00','2026-07-05 10:00:00'),
(1333,1212,'equipo',1112,'Escuadra QA 13','confirmado',1000,'2026-07-10 10:00:00','2026-07-05 10:00:00'),
(1334,1212,'equipo',1113,'Escuadra QA 14','confirmado',1000,'2026-07-10 10:00:00','2026-07-05 10:00:00'),
(1335,1212,'equipo',1114,'Escuadra QA 15','confirmado',1000,'2026-07-10 10:00:00','2026-07-05 10:00:00'),
(1336,1212,'equipo',1115,'Escuadra QA 16','confirmado',1000,'2026-07-10 10:00:00','2026-07-05 10:00:00'),
(1337,1212,'equipo',1116,'Escuadra QA 17','confirmado',1000,'2026-07-10 10:00:00','2026-07-05 10:00:00'),
(1340,1213,'equipo',1101,'Escuadra QA 02','confirmado',1001,'2026-08-25 10:00:00','2026-08-20 10:00:00'),
(1341,1213,'equipo',1106,'Escuadra QA 07','confirmado',1001,'2026-08-25 10:00:00','2026-08-20 10:00:00'),
(1342,1213,'equipo',1111,'Escuadra QA 12','confirmado',1001,'2026-08-25 10:00:00','2026-08-20 10:00:00'),
(1343,1213,'equipo',1117,'Escuadra QA 18','confirmado',1001,'2026-08-25 10:00:00','2026-08-20 10:00:00'),
(1344,1213,'equipo',1118,'Escuadra QA 19','confirmado',1001,'2026-08-25 10:00:00','2026-08-20 10:00:00'),
(1345,1213,'equipo',1119,'Escuadra QA 20','confirmado',1001,'2026-08-25 10:00:00','2026-08-20 10:00:00'),
(1346,1213,'equipo',1120,'Escuadra QA 21','confirmado',1001,'2026-08-25 10:00:00','2026-08-20 10:00:00'),
(1347,1213,'equipo',1121,'Escuadra QA 22','confirmado',1001,'2026-08-25 10:00:00','2026-08-20 10:00:00'),
(1350,1214,'usuario',1010,'Jugador QA 01','confirmado',1002,'2026-09-28 10:00:00','2026-09-25 10:00:00'),
(1351,1214,'usuario',1011,'Jugador QA 02','confirmado',1002,'2026-09-28 10:00:00','2026-09-25 10:00:00'),
(1352,1214,'usuario',1012,'Jugador QA 03','confirmado',1002,'2026-09-28 10:00:00','2026-09-25 10:00:00'),
(1353,1214,'usuario',1013,'Jugador QA 04','confirmado',1002,'2026-09-28 10:00:00','2026-09-25 10:00:00'),
(1354,1214,'usuario',1014,'Jugador QA 05','confirmado',1002,'2026-09-28 10:00:00','2026-09-25 10:00:00'),
(1355,1214,'usuario',1015,'Jugador QA 06','confirmado',1002,'2026-09-28 10:00:00','2026-09-25 10:00:00'),
(1356,1214,'usuario',1016,'Jugador QA 07','confirmado',1002,'2026-09-28 10:00:00','2026-09-25 10:00:00'),
(1357,1214,'usuario',1017,'Jugador QA 08','confirmado',1002,'2026-09-28 10:00:00','2026-09-25 10:00:00'),
(1360,1215,'usuario',1018,'Jugador QA 09','confirmado',1003,'2026-10-03 10:00:00','2026-09-30 10:00:00'),
(1361,1215,'usuario',1019,'Jugador QA 10','confirmado',1003,'2026-10-03 10:00:00','2026-09-30 10:00:00'),
(1362,1215,'usuario',1020,'Jugador QA 11','confirmado',1003,'2026-10-03 10:00:00','2026-09-30 10:00:00'),
(1363,1215,'usuario',1021,'Jugador QA 12','confirmado',1003,'2026-10-03 10:00:00','2026-09-30 10:00:00'),
(1364,1215,'usuario',1022,'Jugador QA 13','confirmado',1003,'2026-10-03 10:00:00','2026-09-30 10:00:00'),
(1365,1215,'usuario',1023,'Jugador QA 14','confirmado',1003,'2026-10-03 10:00:00','2026-09-30 10:00:00'),
(1366,1215,'usuario',1024,'Jugador QA 15','confirmado',1003,'2026-10-03 10:00:00','2026-09-30 10:00:00'),
(1367,1215,'usuario',1025,'Jugador QA 16','confirmado',1003,'2026-10-03 10:00:00','2026-09-30 10:00:00'),
(1368,1215,'usuario',1026,'Jugador QA 17','confirmado',1003,'2026-10-03 10:00:00','2026-09-30 10:00:00'),
(1369,1215,'usuario',1027,'Jugador QA 18','confirmado',1003,'2026-10-03 10:00:00','2026-09-30 10:00:00'),
(1370,1216,'usuario',1028,'Jugador QA 19','confirmado',1004,'2026-08-28 10:00:00','2026-08-25 10:00:00'),
(1371,1216,'usuario',1029,'Jugador QA 20','confirmado',1004,'2026-08-28 10:00:00','2026-08-25 10:00:00'),
(1372,1216,'usuario',1030,'Jugador QA 21','confirmado',1004,'2026-08-28 10:00:00','2026-08-25 10:00:00'),
(1373,1216,'usuario',1031,'Jugador QA 22','confirmado',1004,'2026-08-28 10:00:00','2026-08-25 10:00:00'),
(1374,1216,'usuario',1032,'Jugador QA 23','confirmado',1004,'2026-08-28 10:00:00','2026-08-25 10:00:00'),
(1375,1216,'usuario',1033,'Jugador QA 24','confirmado',1004,'2026-08-28 10:00:00','2026-08-25 10:00:00'),
(1376,1216,'usuario',1034,'Jugador QA 25','confirmado',1004,'2026-08-28 10:00:00','2026-08-25 10:00:00'),
(1377,1216,'usuario',1035,'Jugador QA 26','confirmado',1004,'2026-08-28 10:00:00','2026-08-25 10:00:00'),
(1380,1217,'equipo',1100,'Escuadra QA 01','confirmado',1000,'2026-10-16 10:00:00','2026-10-15 10:00:00'),
(1381,1217,'equipo',1105,'Escuadra QA 06','confirmado',1000,'2026-10-16 10:00:00','2026-10-16 10:00:00'),
(1382,1217,'equipo',1110,'Escuadra QA 11','confirmado',1000,'2026-10-16 10:00:00','2026-10-17 10:00:00'),
(1383,1217,'equipo',1112,'Escuadra QA 13','confirmado',1000,'2026-10-16 10:00:00','2026-10-18 10:00:00'),
(1384,1217,'equipo',1113,'Escuadra QA 14','pendiente',NULL,NULL,'2026-10-17 10:00:00'),
(1385,1217,'equipo',1114,'Escuadra QA 15','pendiente',NULL,NULL,'2026-10-18 10:00:00'),
(1386,1217,'equipo',1115,'Escuadra QA 16','pendiente',NULL,NULL,'2026-10-19 10:00:00'),
(1387,1217,'equipo',1116,'Escuadra QA 17','rechazado',1000,'2026-10-19 15:00:00','2026-10-19 10:00:00'),
(1388,1217,'equipo',1122,'Escuadra QA 23','cancelado',1000,'2026-10-18 15:00:00','2026-10-18 10:00:00');

INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES
(1450,1212,1,1330,1337,'2026-08-01 14:00:00','Cancha QA 1',6,2,'finalizado',1330,1000),
(1451,1212,1,1331,1336,'2026-08-01 16:00:00','Cancha QA 1',4,2,'finalizado',1331,1000),
(1452,1212,1,1332,1335,'2026-08-01 18:00:00','Cancha QA 2',4,1,'finalizado',1332,1000),
(1453,1212,1,1333,1334,'2026-08-01 20:00:00','Cancha QA 2',2,2,'finalizado',NULL,1000),
(1454,1212,2,1330,1336,'2026-08-02 14:00:00','Cancha QA 1',4,1,'finalizado',1330,1000),
(1455,1212,2,1337,1335,'2026-08-02 16:00:00','Cancha QA 1',2,3,'finalizado',1335,1000),
(1456,1212,2,1331,1334,'2026-08-02 18:00:00','Cancha QA 2',3,1,'finalizado',1331,1000),
(1457,1212,2,1332,1333,'2026-08-02 20:00:00','Cancha QA 2',3,2,'finalizado',1332,1000),
(1458,1212,3,1330,1335,'2026-08-03 14:00:00','Cancha QA 1',5,2,'finalizado',1330,1000),
(1459,1212,3,1336,1334,'2026-08-03 16:00:00','Cancha QA 1',1,3,'finalizado',1334,1000),
(1460,1212,3,1337,1333,'2026-08-03 18:00:00','Cancha QA 2',2,3,'finalizado',1333,1000),
(1461,1212,3,1331,1332,'2026-08-03 20:00:00','Cancha QA 2',2,2,'finalizado',NULL,1000),
(1462,1212,4,1330,1334,'2026-08-04 14:00:00','Cancha QA 1',3,1,'finalizado',1330,1000),
(1463,1212,4,1335,1333,'2026-08-04 16:00:00','Cancha QA 1',2,3,'finalizado',1333,1000),
(1464,1212,4,1336,1332,'2026-08-04 18:00:00','Cancha QA 2',1,1,'finalizado',NULL,1000),
(1465,1212,4,1337,1331,'2026-08-04 20:00:00','Cancha QA 2',2,5,'finalizado',1331,1000),
(1466,1212,5,1330,1333,'2026-08-05 14:00:00','Cancha QA 1',4,1,'finalizado',1330,1000),
(1467,1212,5,1334,1332,'2026-08-05 16:00:00','Cancha QA 1',1,1,'finalizado',NULL,1000),
(1468,1212,5,1335,1331,'2026-08-05 18:00:00','Cancha QA 2',2,3,'finalizado',1331,1000),
(1469,1212,5,1336,1337,'2026-08-05 20:00:00','Cancha QA 2',2,2,'finalizado',NULL,1000),
(1470,1212,6,1330,1332,'2026-08-06 14:00:00','Cancha QA 1',2,2,'finalizado',NULL,1000),
(1471,1212,6,1333,1331,'2026-08-06 16:00:00','Cancha QA 1',2,3,'finalizado',1331,1000),
(1472,1212,6,1334,1337,'2026-08-06 18:00:00','Cancha QA 2',3,1,'finalizado',1334,1000),
(1473,1212,6,1335,1336,'2026-08-06 20:00:00','Cancha QA 2',3,2,'finalizado',1335,1000),
(1474,1212,7,1330,1331,'2026-08-07 14:00:00','Cancha QA 1',3,1,'finalizado',1330,1000),
(1475,1212,7,1332,1337,'2026-08-07 16:00:00','Cancha QA 1',4,2,'finalizado',1332,1000),
(1476,1212,7,1333,1336,'2026-08-07 18:00:00','Cancha QA 2',4,1,'finalizado',1333,1000),
(1477,1212,7,1334,1335,'2026-08-07 20:00:00','Cancha QA 2',2,2,'finalizado',NULL,1000),
(1480,1213,1,1340,1341,'2026-09-01 14:00:00','Arena QA',3,1,'finalizado',1340,1001),
(1481,1213,1,1342,1343,'2026-09-01 16:00:00','Arena QA',2,3,'finalizado',1343,1001),
(1482,1213,1,1344,1345,'2026-09-01 18:00:00','Arena QA',3,0,'finalizado',1344,1001),
(1483,1213,1,1346,1347,'2026-09-01 20:00:00','Arena QA',1,3,'finalizado',1347,1001),
(1484,1213,2,1340,1343,'2026-09-02 14:00:00','Arena QA',3,2,'finalizado',1340,1001),
(1485,1213,2,1344,1347,'2026-09-02 16:00:00','Arena QA',0,3,'finalizado',1347,1001),
(1486,1213,3,1340,1347,'2026-09-03 14:00:00','Arena QA',3,2,'finalizado',1340,1001),
(1490,1214,1,1350,1351,'2026-10-10 16:00:00','Mesa QA',1,0,'finalizado',1350,1002),
(1491,1214,1,1352,1353,'2026-10-10 18:00:00','Mesa QA',0.5,0.5,'finalizado',NULL,1002),
(1492,1214,1,1354,1355,'2026-10-10 20:00:00','Mesa QA',1,0,'finalizado',1354,1002),
(1493,1214,1,1356,1357,'2026-10-10 22:00:00','Mesa QA',0,1,'finalizado',1357,1002),
(1494,1214,2,1350,1354,'2026-10-17 16:00:00','Mesa QA',0.5,0.5,'finalizado',NULL,1002),
(1495,1214,2,1357,1352,'2026-10-17 18:00:00','Mesa QA',1,0,'finalizado',1357,1002),
(1496,1214,2,1351,1355,'2026-10-17 20:00:00','Mesa QA',1,0,'finalizado',1351,1002),
(1497,1214,2,1353,1356,'2026-10-17 22:00:00','Mesa QA',1,0,'finalizado',1353,1002),
(1498,1214,3,1350,1357,'2026-10-24 16:00:00','Mesa QA',NULL,NULL,'programado',NULL,1002),
(1499,1214,3,1354,1352,'2026-10-24 18:00:00','Mesa QA',NULL,NULL,'programado',NULL,1002),
(1500,1214,3,1351,1353,'2026-10-24 20:00:00','Mesa QA',NULL,NULL,'programado',NULL,1002),
(1501,1214,3,1355,1356,'2026-10-24 22:00:00','Mesa QA',NULL,NULL,'programado',NULL,1002),
(1510,1215,1,1360,1369,'2026-10-12 15:00:00','Mesa QA',3,1,'finalizado',1360,1003),
(1511,1215,1,1361,1368,'2026-10-12 16:00:00','Mesa QA',2,2,'finalizado',NULL,1003),
(1512,1215,1,1362,1367,'2026-10-12 17:00:00','Mesa QA',3,0,'finalizado',1362,1003),
(1513,1215,1,1363,1366,'2026-10-12 18:00:00','Mesa QA',1,2,'finalizado',1366,1003),
(1514,1215,1,1364,1365,'2026-10-12 19:00:00','Mesa QA',2,0,'finalizado',1364,1003),
(1515,1215,2,1360,1368,'2026-10-19 15:00:00','Mesa QA',2,1,'finalizado',1360,1003),
(1516,1215,2,1369,1367,'2026-10-19 16:00:00','Mesa QA',1,1,'finalizado',NULL,1003),
(1517,1215,2,1361,1366,'2026-10-19 17:00:00','Mesa QA',3,2,'finalizado',1361,1003),
(1518,1215,2,1362,1365,'2026-10-19 18:00:00','Mesa QA',0,2,'finalizado',1365,1003),
(1519,1215,2,1363,1364,'2026-10-19 19:00:00','Mesa QA',1,3,'finalizado',1364,1003),
(1520,1215,3,1360,1367,'2026-10-26 15:00:00','Mesa QA',NULL,NULL,'programado',NULL,1003),
(1521,1215,3,1368,1366,'2026-10-26 16:00:00','Mesa QA',NULL,NULL,'programado',NULL,1003),
(1522,1215,3,1369,1365,'2026-10-26 17:00:00','Mesa QA',NULL,NULL,'programado',NULL,1003),
(1523,1215,3,1361,1364,'2026-10-26 18:00:00','Mesa QA',NULL,NULL,'programado',NULL,1003),
(1524,1215,3,1362,1363,'2026-10-26 19:00:00','Mesa QA',NULL,NULL,'programado',NULL,1003),
(1530,1216,1,1370,1371,'2026-09-05 16:00:00','Mesa QA',2,0,'finalizado',1370,1004),
(1531,1216,1,1372,1373,'2026-09-05 18:00:00','Mesa QA',1,2,'finalizado',1373,1004),
(1532,1216,1,1374,1375,'2026-09-05 20:00:00','Mesa QA',2,1,'finalizado',1374,1004),
(1533,1216,1,1376,1377,'2026-09-05 22:00:00','Mesa QA',0,2,'finalizado',1377,1004),
(1534,1216,2,1370,1373,'2026-09-06 16:00:00','Mesa QA',2,1,'finalizado',1370,1004),
(1535,1216,2,1374,1377,'2026-09-06 18:00:00','Mesa QA',1,2,'finalizado',1377,1004),
(1536,1216,3,1370,1377,'2026-09-07 16:00:00','Mesa QA',2,1,'finalizado',1370,1004),
(1540,1217,1,1380,1382,'2026-11-10 20:00:00','Arena QA',NULL,NULL,'programado',NULL,1000),
(1541,1217,1,1381,1383,'2026-11-10 22:00:00','Arena QA',NULL,NULL,'programado',NULL,1000);

INSERT IGNORE INTO resultados_detalle (id,encuentro_id,tipo_dato,valor) VALUES
(2500,1477,'mvp','Jugador QA 01'),
(2501,1477,'duracion_minutos','90'),
(2502,1477,'observacion','Partido de cierre de la liga sintética QA'),
(2503,1476,'observacion','Partido de la última fecha con diferencia de goles'),
(2504,1486,'mvp','Jugador QA 01'),
(2505,1486,'duracion_minutos','75'),
(2506,1486,'observacion','Final sintética de la eliminatoria de equipos QA'),
(2507,1494,'observacion','Ronda suiza con tablas sintéticas'),
(2508,1519,'observacion','Cierre de la segunda fecha individual QA'),
(2509,1536,'mvp','Jugador QA 28'),
(2510,1536,'duracion_minutos','60'),
(2511,1536,'observacion','Final sintética de la eliminatoria individual QA');

INSERT IGNORE INTO torneo_suizo_parejas (torneo_id,participante_a_id,participante_b_id,ronda,ya_se_enfrentaron,fecha_encuentro) VALUES
(1214,1350,1351,1,0,'2026-10-10 16:00:00'),
(1214,1352,1353,1,0,'2026-10-10 16:00:00'),
(1214,1354,1355,1,0,'2026-10-10 16:00:00'),
(1214,1356,1357,1,0,'2026-10-10 16:00:00'),
(1214,1350,1354,2,0,'2026-10-17 16:00:00'),
(1214,1357,1352,2,0,'2026-10-17 16:00:00'),
(1214,1351,1355,2,0,'2026-10-17 16:00:00'),
(1214,1353,1356,2,0,'2026-10-17 16:00:00'),
(1214,1350,1357,3,0,'2026-10-24 16:00:00'),
(1214,1354,1352,3,0,'2026-10-24 16:00:00'),
(1214,1351,1353,3,0,'2026-10-24 16:00:00'),
(1214,1355,1356,3,0,'2026-10-24 16:00:00');

INSERT IGNORE INTO torneo_actividades (id,torneo_id,titulo,descripcion,tipo,fecha,hora) VALUES
(2200,1212,'Acreditación y control de inscripciones','Verificación de documentos y cupos del torneo.','administrativo','2026-07-01','18:00:00'),
(2201,1212,'Reunión técnica con capitanes','Repaso de reglas, horarios y canales de comunicación.','reunion','2026-08-01','19:00:00'),
(2202,1212,'Jornada de competencia','Inicio de la jornada principal del torneo.','competencia','2026-08-01','20:00:00'),
(2203,1212,'Premiación','Entrega de trofeos y reconocimientos.','premiacion','2026-08-07','21:00:00'),
(2204,1213,'Acreditación y control de inscripciones','Verificación de documentos y cupos del torneo.','administrativo','2026-08-15','18:00:00'),
(2205,1213,'Reunión técnica con capitanes','Repaso de reglas, horarios y canales de comunicación.','reunion','2026-08-31','19:00:00'),
(2206,1213,'Jornada de competencia','Inicio de la jornada principal del torneo.','competencia','2026-09-01','20:00:00'),
(2207,1213,'Premiación','Entrega de trofeos y reconocimientos.','premiacion','2026-09-03','21:00:00'),
(2208,1214,'Acreditación y control de inscripciones','Verificación de documentos y cupos del torneo.','administrativo','2026-10-05','18:00:00'),
(2209,1214,'Reunión técnica con capitanes','Repaso de reglas, horarios y canales de comunicación.','reunion','2026-10-09','19:00:00'),
(2210,1214,'Jornada de competencia','Inicio de la jornada principal del torneo.','competencia','2026-10-10','20:00:00'),
(2211,1214,'Premiación','Entrega de trofeos y reconocimientos.','premiacion','2026-10-24','21:00:00'),
(2212,1215,'Acreditación y control de inscripciones','Verificación de documentos y cupos del torneo.','administrativo','2026-10-10','18:00:00'),
(2213,1215,'Reunión técnica con capitanes','Repaso de reglas, horarios y canales de comunicación.','reunion','2026-10-11','19:00:00'),
(2214,1215,'Jornada de competencia','Inicio de la jornada principal del torneo.','competencia','2026-10-12','20:00:00'),
(2215,1215,'Premiación','Entrega de trofeos y reconocimientos.','premiacion','2026-10-26','21:00:00'),
(2216,1216,'Acreditación y control de inscripciones','Verificación de documentos y cupos del torneo.','administrativo','2026-08-30','18:00:00'),
(2217,1216,'Reunión técnica con capitanes','Repaso de reglas, horarios y canales de comunicación.','reunion','2026-09-04','19:00:00'),
(2218,1216,'Jornada de competencia','Inicio de la jornada principal del torneo.','competencia','2026-09-05','20:00:00'),
(2219,1216,'Premiación','Entrega de trofeos y reconocimientos.','premiacion','2026-09-07','21:00:00'),
(2220,1217,'Acreditación y control de inscripciones','Verificación de documentos y cupos del torneo.','administrativo','2026-10-20','18:00:00'),
(2221,1217,'Reunión técnica con capitanes','Repaso de reglas, horarios y canales de comunicación.','reunion','2026-11-09','19:00:00'),
(2222,1217,'Jornada de competencia','Inicio de la jornada principal del torneo.','competencia','2026-11-10','20:00:00'),
(2223,1217,'Premiación','Entrega de trofeos y reconocimientos.','premiacion','2026-11-14','21:00:00');

INSERT IGNORE INTO torneo_config (torneo_id,clave,valor) VALUES
(1212,'semilla_qa','true'),
(1212,'reglas_version','1'),
(1213,'semilla_qa','true'),
(1213,'reglas_version','1'),
(1214,'semilla_qa','true'),
(1214,'reglas_version','1'),
(1215,'semilla_qa','true'),
(1215,'reglas_version','1'),
(1216,'semilla_qa','true'),
(1216,'reglas_version','1'),
(1217,'semilla_qa','true'),
(1217,'reglas_version','1'),
(1212,'podio','{"primero":"Escuadra QA 01","segundo":"Escuadra QA 06","tercero":"Escuadra QA 11"}'),
(1213,'podio','{"primero":"Escuadra QA 02","segundo":"Escuadra QA 22","tercero":"Escuadra QA 18"}'),
(1216,'podio','{"primero":"Jugador QA 19","segundo":"Jugador QA 26","tercero":"Jugador QA 22"}');

INSERT IGNORE INTO torneo_cambio_estado (id,torneo_id,estado_anterior,estado_nuevo,motivo,usuario_id,fecha_cambio) VALUES
(2400,1212,'borrador','inscripciones_abiertas','Apertura de inscripciones QA',1000,'2026-07-01 09:00:00'),
(2401,1212,'inscripciones_abiertas','en_curso','Inicio de la liga QA',1000,'2026-08-01 09:00:00'),
(2402,1212,'en_curso','finalizado','Cierre de la liga QA',1000,'2026-08-07 22:00:00'),
(2403,1213,'borrador','inscripciones_abiertas','Apertura de la eliminatoria QA',1001,'2026-08-15 09:00:00'),
(2404,1213,'inscripciones_abiertas','en_curso','Inicio de la eliminatoria QA',1001,'2026-09-01 09:00:00'),
(2405,1213,'en_curso','finalizado','Final de la eliminatoria QA',1001,'2026-09-03 22:00:00'),
(2406,1214,'borrador','inscripciones_abiertas','Apertura del suizo QA',1002,'2026-09-20 09:00:00'),
(2407,1214,'inscripciones_abiertas','en_curso','Inicio del suizo QA',1002,'2026-10-10 09:00:00'),
(2408,1215,'borrador','inscripciones_abiertas','Apertura de la liga individual QA',1003,'2026-09-25 09:00:00'),
(2409,1215,'inscripciones_abiertas','en_curso','Inicio de la liga individual QA',1003,'2026-10-12 09:00:00'),
(2410,1216,'borrador','inscripciones_abiertas','Apertura de la eliminatoria individual QA',1004,'2026-08-20 09:00:00'),
(2411,1216,'inscripciones_abiertas','en_curso','Inicio de la eliminatoria individual QA',1004,'2026-09-05 09:00:00'),
(2412,1216,'en_curso','finalizado','Final de la eliminatoria individual QA',1004,'2026-09-07 22:00:00'),
(2413,1217,'borrador','inscripciones_abiertas','Apertura de la torneo abierto QA',1000,'2026-10-15 09:00:00');

INSERT IGNORE INTO notificaciones (id,usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES
(2600,1000,'inscripcion','Solicitudes pendientes en Abierta QA','Hay 3 equipos esperando aprobación en Abierta QA Solicitudes.','index.php?c=panelOrganizador&a=administrar&seccion=solicitudes&id=1217',0),
(2601,1000,'info','Liga QA completada','La Liga QA Completa 8 finalizó con 28 partidos jugados.','index.php?c=torneo&a=detalle&id=1212',1),
(2602,1001,'resultado','Eliminatoria QA finalizada','La llave de 8 equipos quedó completa.','index.php?c=torneo&a=detalle&id=1213',1),
(2603,1010,'inscripcion','Torneo guardado','Guardaste Abierta QA Solicitudes en tu lista.','index.php?c=guardados&a=index',0),
(2604,1010,'resultado','Tu equipo ganó la liga QA','Escuadra QA 01 terminó primero en la Liga QA Completa 8.','index.php?c=torneo&a=detalle&id=1212',0),
(2605,1011,'partido','Próxima ronda suiza','La ronda 3 del Suizo QA Individual está programada para el 24/10.','index.php?c=torneo&a=detalle&id=1214',0),
(2606,1012,'info','Nuevo torneo guardado','Guardaste el torneo Liga QA Completa 8.','index.php?c=guardados&a=index',1);

INSERT IGNORE INTO guardados (id,usuario_id,tipo,referencia_id) VALUES
(2700,1010,'torneo',1206),
(2701,1010,'torneo',1217),
(2702,1010,'equipo',1100),
(2703,1011,'torneo',1214),
(2704,1011,'equipo',1112),
(2705,1012,'torneo',1212);

INSERT IGNORE INTO solicitudes_equipo (id,equipo_id,usuario_id,iniciado_por,mensaje,estado,fecha_solicitud,fecha_respuesta,respondido_por) VALUES
(2800,1112,1036,1036,'Solicitud sintética para probar la bandeja del capitán.','pendiente','2026-10-20 12:00:00',NULL,NULL),
(2801,1100,1051,1051,'Solicitud aprobada de prueba QA.','aprobado','2026-10-18 12:00:00','2026-10-19 12:00:00',1000),
(2802,1101,1050,1050,'Solicitud rechazada de prueba QA.','rechazado','2026-10-17 12:00:00','2026-10-18 12:00:00',1001),
(2803,1113,1049,1017,'Invitación del capitán de Escuadra QA 14 para cubrir la banda derecha.','pendiente','2026-10-20 13:00:00',NULL,NULL);

INSERT IGNORE INTO equipo_miembros (equipo_id,usuario_id,fecha_union,numero_camiseta,posicion,es_activo) VALUES
(1100,1051,'2026-10-19 12:00:00',6,'Suplente',1);

-- Posiciones de los torneos nuevos (1212-1217).
-- diferencia_goles es una columna generada por MySQL (puntos_favor - puntos_contra); no se inserta ni actualiza.
DELETE FROM torneo_posiciones WHERE torneo_id BETWEEN 1212 AND 1217;

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
          AND e.torneo_id BETWEEN 1212 AND 1217
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
          AND e.torneo_id BETWEEN 1212 AND 1217
    ) AS lados
    GROUP BY lados.torneo_id, lados.participante_id
) AS e ON e.torneo_id = p.torneo_id AND e.participante_id = p.id
WHERE p.torneo_id BETWEEN 1212 AND 1217
  AND p.estado = 'confirmado';
