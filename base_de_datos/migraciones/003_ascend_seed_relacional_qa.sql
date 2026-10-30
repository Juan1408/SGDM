-- ASCEND: semillas relacionales para QA / desarrollo
-- Datos sintéticos. No usar en producción.
-- Usa IDs reservados desde 1000 y corre en orden respetando las FK.
-- Contraseña de prueba compartida: hash bcrypt ya usado por el proyecto.
--
-- IMPORTANTE: pensado para una base recién creada (001 + 002). No es
-- totalmente idempotente: algunas inserciones dependen de IDs reservados y
-- de AUTO_INCREMENT. Para reimportarlo, recrear la base antes de ejecutarlo.
-- Las posiciones de los torneos QA se recalculan al final a partir de los
-- encuentros finalizados y de la configuración de puntuación de cada torneo.
USE ascend;
SET NAMES utf8mb4;
-- Relaciones válidas del catálogo multideportivo (idempotentes).
INSERT IGNORE INTO juego_modalidades_permitidas (juego_id,modalidad_id)
SELECT j.id,m.id FROM juegos j CROSS JOIN modalidades m
WHERE (j.id IN (1,2,3,4) AND m.id=2) OR (j.id=5 AND m.id=1);
INSERT IGNORE INTO juego_formatos_permitidos (juego_id,formato_codigo)
SELECT j.id,f.codigo FROM juegos j CROSS JOIN formatos_torneo f
WHERE j.id BETWEEN 1 AND 5 AND f.codigo IN ('liga','eliminacion_directa','suizo');
START TRANSACTION;

INSERT IGNORE INTO usuarios (id,email,contrasena_hash,nombre_completo,telefono,rol_id,esta_activo,email_verificado) VALUES
(1000,'organizador.qa1@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Organizador QA 1', '+5989901000',2,1,1),
(1001,'organizador.qa2@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Organizador QA 2', '+5989901001',2,1,1),
(1002,'organizador.qa3@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Organizador QA 3', '+5989901002',2,1,1),
(1003,'organizador.qa4@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Organizador QA 4', '+5989901003',2,1,1),
(1004,'organizador.qa5@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Organizador QA 5', '+5989901004',2,1,1),
(1005,'organizador.qa6@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Organizador QA 6', '+5989901005',2,1,1),
(1010,'jugador.qa01@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 01', '+5989901010',3,1,1),
(1011,'jugador.qa02@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 02', '+5989901011',3,1,1),
(1012,'jugador.qa03@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 03', '+5989901012',3,1,1),
(1013,'jugador.qa04@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 04', '+5989901013',3,1,1),
(1014,'jugador.qa05@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 05', '+5989901014',3,1,1),
(1015,'jugador.qa06@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 06', '+5989901015',3,1,1),
(1016,'jugador.qa07@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 07', '+5989901016',3,1,1),
(1017,'jugador.qa08@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 08', '+5989901017',3,1,1),
(1018,'jugador.qa09@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 09', '+5989901018',3,1,1),
(1019,'jugador.qa10@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 10', '+5989901019',3,1,1),
(1020,'jugador.qa11@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 11', '+5989901020',3,1,1),
(1021,'jugador.qa12@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 12', '+5989901021',3,1,1),
(1022,'jugador.qa13@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 13', '+5989901022',3,1,1),
(1023,'jugador.qa14@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 14', '+5989901023',3,1,1),
(1024,'jugador.qa15@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 15', '+5989901024',3,1,1),
(1025,'jugador.qa16@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 16', '+5989901025',3,1,1),
(1026,'jugador.qa17@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 17', '+5989901026',3,1,1),
(1027,'jugador.qa18@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 18', '+5989901027',3,1,1),
(1028,'jugador.qa19@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 19', '+5989901028',3,1,1),
(1029,'jugador.qa20@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 20', '+5989901029',3,1,1),
(1030,'jugador.qa21@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 21', '+5989901030',3,1,1),
(1031,'jugador.qa22@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 22', '+5989901031',3,1,1),
(1032,'jugador.qa23@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 23', '+5989901032',3,1,1),
(1033,'jugador.qa24@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 24', '+5989901033',3,1,1),
(1034,'jugador.qa25@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 25', '+5989901034',3,1,1),
(1035,'jugador.qa26@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 26', '+5989901035',3,1,1),
(1036,'jugador.qa27@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 27', '+5989901036',3,1,1),
(1037,'jugador.qa28@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 28', '+5989901037',3,1,1),
(1038,'jugador.qa29@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 29', '+5989901038',3,1,1),
(1039,'jugador.qa30@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 30', '+5989901039',3,1,1),
(1040,'jugador.qa31@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 31', '+5989901040',3,1,1),
(1041,'jugador.qa32@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 32', '+5989901041',3,1,1),
(1042,'jugador.qa33@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 33', '+5989901042',3,1,1),
(1043,'jugador.qa34@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 34', '+5989901043',3,1,1),
(1044,'jugador.qa35@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 35', '+5989901044',3,1,1),
(1045,'jugador.qa36@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 36', '+5989901045',3,1,1),
(1046,'jugador.qa37@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 37', '+5989901046',3,1,1),
(1047,'jugador.qa38@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 38', '+5989901047',3,1,1),
(1048,'jugador.qa39@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 39', '+5989901048',3,1,1),
(1049,'jugador.qa40@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 40', '+5989901049',3,1,1),
(1050,'jugador.qa41@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 41', '+5989901050',3,1,1),
(1051,'jugador.qa42@ascend.test','$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee','Jugador QA 42', '+5989901051',3,1,1);

INSERT IGNORE INTO perfiles_organizadores (usuario_id,nombre_organizacion,bio_organizacion,localidad,telefono_contacto,verificado_oficial) VALUES
(1000,'Liga QA 1','Organización sintética para pruebas de flujo','Montevideo','+5989901000',1),
(1001,'Liga QA 2','Organización sintética para pruebas de flujo','Montevideo','+5989901001',1),
(1002,'Liga QA 3','Organización sintética para pruebas de flujo','Montevideo','+5989901002',1),
(1003,'Liga QA 4','Organización sintética para pruebas de flujo','Montevideo','+5989901003',1),
(1004,'Liga QA 5','Organización sintética para pruebas de flujo','Montevideo','+5989901004',1),
(1005,'Liga QA 6','Organización sintética para pruebas de flujo','Montevideo','+5989901005',1);

INSERT IGNORE INTO perfiles_jugadores (usuario_id,apodo_gamertag,bio,nivel,experiencia_puntos,pais,ciudad,fecha_nacimiento,discord_tag) VALUES
(1010,'Player1010','Perfil de prueba #1',1,0,'Uruguay','Montevideo','1988-01-01','Player1010#UY'),
(1011,'Player1011','Perfil de prueba #2',2,317,'Uruguay','Las Piedras','1989-02-02','Player1011#UY'),
(1012,'Player1012','Perfil de prueba #3',3,634,'Uruguay','Canelones','1990-03-03','Player1012#UY'),
(1013,'Player1013','Perfil de prueba #4',4,951,'Uruguay','Maldonado','1991-04-04','Player1013#UY'),
(1014,'Player1014','Perfil de prueba #5',5,1268,'Uruguay','Salto','1992-05-05','Player1014#UY'),
(1015,'Player1015','Perfil de prueba #6',6,1585,'Uruguay','Montevideo','1993-06-06','Player1015#UY'),
(1016,'Player1016','Perfil de prueba #7',7,1902,'Uruguay','Las Piedras','1994-07-07','Player1016#UY'),
(1017,'Player1017','Perfil de prueba #8',8,2219,'Uruguay','Canelones','1995-08-08','Player1017#UY'),
(1018,'Player1018','Perfil de prueba #9',9,2536,'Uruguay','Maldonado','1996-09-09','Player1018#UY'),
(1019,'Player1019','Perfil de prueba #10',10,2853,'Uruguay','Salto','1997-10-10','Player1019#UY'),
(1020,'Player1020','Perfil de prueba #11',11,3170,'Uruguay','Montevideo','1998-11-11','Player1020#UY'),
(1021,'Player1021','Perfil de prueba #12',12,3487,'Uruguay','Las Piedras','1999-12-12','Player1021#UY'),
(1022,'Player1022','Perfil de prueba #13',13,3804,'Uruguay','Canelones','2000-01-13','Player1022#UY'),
(1023,'Player1023','Perfil de prueba #14',14,4121,'Uruguay','Maldonado','2001-02-14','Player1023#UY'),
(1024,'Player1024','Perfil de prueba #15',15,4438,'Uruguay','Salto','2002-03-15','Player1024#UY'),
(1025,'Player1025','Perfil de prueba #16',16,4755,'Uruguay','Montevideo','2003-04-16','Player1025#UY'),
(1026,'Player1026','Perfil de prueba #17',17,5072,'Uruguay','Las Piedras','2004-05-17','Player1026#UY'),
(1027,'Player1027','Perfil de prueba #18',18,5389,'Uruguay','Canelones','1988-06-18','Player1027#UY'),
(1028,'Player1028','Perfil de prueba #19',19,5706,'Uruguay','Maldonado','1989-07-19','Player1028#UY'),
(1029,'Player1029','Perfil de prueba #20',20,6023,'Uruguay','Salto','1990-08-20','Player1029#UY'),
(1030,'Player1030','Perfil de prueba #21',1,6340,'Uruguay','Montevideo','1991-09-21','Player1030#UY'),
(1031,'Player1031','Perfil de prueba #22',2,6657,'Uruguay','Las Piedras','1992-10-22','Player1031#UY'),
(1032,'Player1032','Perfil de prueba #23',3,6974,'Uruguay','Canelones','1993-11-23','Player1032#UY'),
(1033,'Player1033','Perfil de prueba #24',4,7291,'Uruguay','Maldonado','1994-12-24','Player1033#UY'),
(1034,'Player1034','Perfil de prueba #25',5,7608,'Uruguay','Salto','1995-01-25','Player1034#UY'),
(1035,'Player1035','Perfil de prueba #26',6,7925,'Uruguay','Montevideo','1996-02-26','Player1035#UY'),
(1036,'Player1036','Perfil de prueba #27',7,8242,'Uruguay','Las Piedras','1997-03-27','Player1036#UY'),
(1037,'Player1037','Perfil de prueba #28',8,8559,'Uruguay','Canelones','1998-04-01','Player1037#UY'),
(1038,'Player1038','Perfil de prueba #29',9,8876,'Uruguay','Maldonado','1999-05-02','Player1038#UY'),
(1039,'Player1039','Perfil de prueba #30',10,9193,'Uruguay','Salto','2000-06-03','Player1039#UY'),
(1040,'Player1040','Perfil de prueba #31',11,9510,'Uruguay','Montevideo','2001-07-04','Player1040#UY'),
(1041,'Player1041','Perfil de prueba #32',12,9827,'Uruguay','Las Piedras','2002-08-05','Player1041#UY'),
(1042,'Player1042','Perfil de prueba #33',13,10144,'Uruguay','Canelones','2003-09-06','Player1042#UY'),
(1043,'Player1043','Perfil de prueba #34',14,10461,'Uruguay','Maldonado','2004-10-07','Player1043#UY'),
(1044,'Player1044','Perfil de prueba #35',15,10778,'Uruguay','Salto','1988-11-08','Player1044#UY'),
(1045,'Player1045','Perfil de prueba #36',16,11095,'Uruguay','Montevideo','1989-12-09','Player1045#UY'),
(1046,'Player1046','Perfil de prueba #37',17,11412,'Uruguay','Las Piedras','1990-01-10','Player1046#UY'),
(1047,'Player1047','Perfil de prueba #38',18,11729,'Uruguay','Canelones','1991-02-11','Player1047#UY'),
(1048,'Player1048','Perfil de prueba #39',19,46,'Uruguay','Maldonado','1992-03-12','Player1048#UY'),
(1049,'Player1049','Perfil de prueba #40',20,363,'Uruguay','Salto','1993-04-13','Player1049#UY'),
(1050,'Player1050','Perfil de prueba #41',1,680,'Uruguay','Montevideo','1994-05-14','Player1050#UY'),
(1051,'Player1051','Perfil de prueba #42',2,997,'Uruguay','Las Piedras','1995-06-15','Player1051#UY');

INSERT IGNORE INTO equipos (id,nombre_equipo,juego_id,descripcion,ubicacion,anio_fundacion,creado_por,activo,codigo_invitacion) VALUES (1100,'Escuadra QA 01',1,'Equipo sintético para validar membresías y torneos','Montevideo',2012,1000,1,'QA1100');
INSERT IGNORE INTO equipos (id,nombre_equipo,juego_id,descripcion,ubicacion,anio_fundacion,creado_por,activo,codigo_invitacion) VALUES (1101,'Escuadra QA 02',2,'Equipo sintético para validar membresías y torneos','Canelones',2013,1001,1,'QA1101');
INSERT IGNORE INTO equipos (id,nombre_equipo,juego_id,descripcion,ubicacion,anio_fundacion,creado_por,activo,codigo_invitacion) VALUES (1102,'Escuadra QA 03',3,'Equipo sintético para validar membresías y torneos','Maldonado',2014,1002,1,'QA1102');
INSERT IGNORE INTO equipos (id,nombre_equipo,juego_id,descripcion,ubicacion,anio_fundacion,creado_por,activo,codigo_invitacion) VALUES (1103,'Escuadra QA 04',4,'Equipo sintético para validar membresías y torneos','Montevideo',2015,1003,1,'QA1103');
INSERT IGNORE INTO equipos (id,nombre_equipo,juego_id,descripcion,ubicacion,anio_fundacion,creado_por,activo,codigo_invitacion) VALUES (1104,'Escuadra QA 05',5,'Equipo sintético para validar membresías y torneos','Canelones',2016,1004,1,'QA1104');
INSERT IGNORE INTO equipos (id,nombre_equipo,juego_id,descripcion,ubicacion,anio_fundacion,creado_por,activo,codigo_invitacion) VALUES (1105,'Escuadra QA 06',1,'Equipo sintético para validar membresías y torneos','Maldonado',2017,1005,1,'QA1105');
INSERT IGNORE INTO equipos (id,nombre_equipo,juego_id,descripcion,ubicacion,anio_fundacion,creado_por,activo,codigo_invitacion) VALUES (1106,'Escuadra QA 07',2,'Equipo sintético para validar membresías y torneos','Montevideo',2018,1000,1,'QA1106');
INSERT IGNORE INTO equipos (id,nombre_equipo,juego_id,descripcion,ubicacion,anio_fundacion,creado_por,activo,codigo_invitacion) VALUES (1107,'Escuadra QA 08',3,'Equipo sintético para validar membresías y torneos','Canelones',2019,1001,1,'QA1107');
INSERT IGNORE INTO equipos (id,nombre_equipo,juego_id,descripcion,ubicacion,anio_fundacion,creado_por,activo,codigo_invitacion) VALUES (1108,'Escuadra QA 09',4,'Equipo sintético para validar membresías y torneos','Maldonado',2020,1002,1,'QA1108');
INSERT IGNORE INTO equipos (id,nombre_equipo,juego_id,descripcion,ubicacion,anio_fundacion,creado_por,activo,codigo_invitacion) VALUES (1109,'Escuadra QA 10',5,'Equipo sintético para validar membresías y torneos','Montevideo',2021,1003,1,'QA1109');
INSERT IGNORE INTO equipos (id,nombre_equipo,juego_id,descripcion,ubicacion,anio_fundacion,creado_por,activo,codigo_invitacion) VALUES (1110,'Escuadra QA 11',1,'Equipo sintético para validar membresías y torneos','Canelones',2022,1004,1,'QA1110');
INSERT IGNORE INTO equipos (id,nombre_equipo,juego_id,descripcion,ubicacion,anio_fundacion,creado_por,activo,codigo_invitacion) VALUES (1111,'Escuadra QA 12',2,'Equipo sintético para validar membresías y torneos','Maldonado',2023,1005,1,'QA1111');
INSERT IGNORE INTO equipo_miembros (equipo_id,usuario_id,fecha_union,numero_camiseta,posicion,es_activo) VALUES (1100,1010,'2026-01-01 12:00:00',1,'Titular',1), (1100,1011,'2026-02-02 12:00:00',2,'Suplente',1), (1100,1012,'2026-03-03 12:00:00',3,'Defensa',1), (1100,1013,'2026-04-04 12:00:00',4,'Atacante',1), (1100,1014,'2026-05-05 12:00:00',5,'Capitán',1);
INSERT IGNORE INTO equipo_capitanes (equipo_id,usuario_id,asignado_por,es_capitan_principal) VALUES (1100,1010,1000,1);
INSERT IGNORE INTO equipo_miembros (equipo_id,usuario_id,fecha_union,numero_camiseta,posicion,es_activo) VALUES (1101,1013,'2026-01-02 12:00:00',1,'Titular',1), (1101,1014,'2026-02-03 12:00:00',2,'Suplente',1), (1101,1015,'2026-03-04 12:00:00',3,'Defensa',1), (1101,1016,'2026-04-05 12:00:00',4,'Atacante',1), (1101,1017,'2026-05-06 12:00:00',5,'Capitán',1);
INSERT IGNORE INTO equipo_capitanes (equipo_id,usuario_id,asignado_por,es_capitan_principal) VALUES (1101,1013,1001,1);
INSERT IGNORE INTO equipo_miembros (equipo_id,usuario_id,fecha_union,numero_camiseta,posicion,es_activo) VALUES (1102,1016,'2026-01-03 12:00:00',1,'Titular',1), (1102,1017,'2026-02-04 12:00:00',2,'Suplente',1), (1102,1018,'2026-03-05 12:00:00',3,'Defensa',1), (1102,1019,'2026-04-06 12:00:00',4,'Atacante',1), (1102,1020,'2026-05-07 12:00:00',5,'Capitán',1);
INSERT IGNORE INTO equipo_capitanes (equipo_id,usuario_id,asignado_por,es_capitan_principal) VALUES (1102,1016,1002,1);
INSERT IGNORE INTO equipo_miembros (equipo_id,usuario_id,fecha_union,numero_camiseta,posicion,es_activo) VALUES (1103,1019,'2026-01-04 12:00:00',1,'Titular',1), (1103,1020,'2026-02-05 12:00:00',2,'Suplente',1), (1103,1021,'2026-03-06 12:00:00',3,'Defensa',1), (1103,1022,'2026-04-07 12:00:00',4,'Atacante',1), (1103,1023,'2026-05-08 12:00:00',5,'Capitán',1);
INSERT IGNORE INTO equipo_capitanes (equipo_id,usuario_id,asignado_por,es_capitan_principal) VALUES (1103,1019,1003,1);
INSERT IGNORE INTO equipo_miembros (equipo_id,usuario_id,fecha_union,numero_camiseta,posicion,es_activo) VALUES (1104,1022,'2026-01-05 12:00:00',1,'Titular',1), (1104,1023,'2026-02-06 12:00:00',2,'Suplente',1), (1104,1024,'2026-03-07 12:00:00',3,'Defensa',1), (1104,1025,'2026-04-08 12:00:00',4,'Atacante',1), (1104,1026,'2026-05-09 12:00:00',5,'Capitán',1);
INSERT IGNORE INTO equipo_capitanes (equipo_id,usuario_id,asignado_por,es_capitan_principal) VALUES (1104,1022,1004,1);
INSERT IGNORE INTO equipo_miembros (equipo_id,usuario_id,fecha_union,numero_camiseta,posicion,es_activo) VALUES (1105,1025,'2026-01-06 12:00:00',1,'Titular',1), (1105,1026,'2026-02-07 12:00:00',2,'Suplente',1), (1105,1027,'2026-03-08 12:00:00',3,'Defensa',1), (1105,1028,'2026-04-09 12:00:00',4,'Atacante',1), (1105,1029,'2026-05-10 12:00:00',5,'Capitán',1);
INSERT IGNORE INTO equipo_capitanes (equipo_id,usuario_id,asignado_por,es_capitan_principal) VALUES (1105,1025,1005,1);
INSERT IGNORE INTO equipo_miembros (equipo_id,usuario_id,fecha_union,numero_camiseta,posicion,es_activo) VALUES (1106,1028,'2026-01-07 12:00:00',1,'Titular',1), (1106,1029,'2026-02-08 12:00:00',2,'Suplente',1), (1106,1030,'2026-03-09 12:00:00',3,'Defensa',1), (1106,1031,'2026-04-10 12:00:00',4,'Atacante',1), (1106,1032,'2026-05-11 12:00:00',5,'Capitán',1);
INSERT IGNORE INTO equipo_capitanes (equipo_id,usuario_id,asignado_por,es_capitan_principal) VALUES (1106,1028,1000,1);
INSERT IGNORE INTO equipo_miembros (equipo_id,usuario_id,fecha_union,numero_camiseta,posicion,es_activo) VALUES (1107,1031,'2026-01-08 12:00:00',1,'Titular',1), (1107,1032,'2026-02-09 12:00:00',2,'Suplente',1), (1107,1033,'2026-03-10 12:00:00',3,'Defensa',1), (1107,1034,'2026-04-11 12:00:00',4,'Atacante',1), (1107,1035,'2026-05-12 12:00:00',5,'Capitán',1);
INSERT IGNORE INTO equipo_capitanes (equipo_id,usuario_id,asignado_por,es_capitan_principal) VALUES (1107,1031,1001,1);
INSERT IGNORE INTO equipo_miembros (equipo_id,usuario_id,fecha_union,numero_camiseta,posicion,es_activo) VALUES (1108,1034,'2026-01-09 12:00:00',1,'Titular',1), (1108,1035,'2026-02-10 12:00:00',2,'Suplente',1), (1108,1036,'2026-03-11 12:00:00',3,'Defensa',1), (1108,1037,'2026-04-12 12:00:00',4,'Atacante',1), (1108,1038,'2026-05-13 12:00:00',5,'Capitán',1);
INSERT IGNORE INTO equipo_capitanes (equipo_id,usuario_id,asignado_por,es_capitan_principal) VALUES (1108,1034,1002,1);
INSERT IGNORE INTO equipo_miembros (equipo_id,usuario_id,fecha_union,numero_camiseta,posicion,es_activo) VALUES (1109,1037,'2026-01-10 12:00:00',1,'Titular',1), (1109,1038,'2026-02-11 12:00:00',2,'Suplente',1), (1109,1039,'2026-03-12 12:00:00',3,'Defensa',1), (1109,1040,'2026-04-13 12:00:00',4,'Atacante',1), (1109,1041,'2026-05-14 12:00:00',5,'Capitán',1);
INSERT IGNORE INTO equipo_capitanes (equipo_id,usuario_id,asignado_por,es_capitan_principal) VALUES (1109,1037,1003,1);
INSERT IGNORE INTO equipo_miembros (equipo_id,usuario_id,fecha_union,numero_camiseta,posicion,es_activo) VALUES (1110,1040,'2026-01-11 12:00:00',1,'Titular',1), (1110,1041,'2026-02-12 12:00:00',2,'Suplente',1), (1110,1042,'2026-03-13 12:00:00',3,'Defensa',1), (1110,1043,'2026-04-14 12:00:00',4,'Atacante',1), (1110,1044,'2026-05-15 12:00:00',5,'Capitán',1);
INSERT IGNORE INTO equipo_capitanes (equipo_id,usuario_id,asignado_por,es_capitan_principal) VALUES (1110,1040,1004,1);
INSERT IGNORE INTO equipo_miembros (equipo_id,usuario_id,fecha_union,numero_camiseta,posicion,es_activo) VALUES (1111,1043,'2026-01-12 12:00:00',1,'Titular',1), (1111,1044,'2026-02-13 12:00:00',2,'Suplente',1), (1111,1045,'2026-03-14 12:00:00',3,'Defensa',1), (1111,1046,'2026-04-15 12:00:00',4,'Atacante',1), (1111,1047,'2026-05-16 12:00:00',5,'Capitán',1);
INSERT IGNORE INTO equipo_capitanes (equipo_id,usuario_id,asignado_por,es_capitan_principal) VALUES (1111,1043,1005,1);
INSERT IGNORE INTO torneos
(id,nombre,juego_id,descripcion,formato,estado,cupo_max_equipos,cupo_min_equipos,fecha_inicio_inscripcion,fecha_limite_inscripcion,fecha_inicio,fecha_fin,reglas,premios,ubicacion,localidad,comunidad,organizador_id,modalidad_id,sistema_puntuacion_id,puntos_victoria,puntos_empate,puntos_derrota,tipo_resultado,mejor_de)
VALUES (1200,'Copa QA 01',1,'Torneo sintético para pruebas integrales','liga','finalizado',16,2,'2026-01-01','2026-12-01','2026-01-01','2026-01-05','Reglas de prueba; resultados ficticios','Trofeo QA','Arena de pruebas','Montevideo','Uruguay',1000,2,1,3,1,0,'rondas',1);
INSERT IGNORE INTO torneo_config (torneo_id,clave,valor) VALUES (1200,'semilla_qa','true'),(1200,'reglas_version','1');
INSERT IGNORE INTO torneo_actividades (torneo_id,titulo,tipo,fecha,hora) VALUES (1200,'Reunión técnica QA','reunion','2026-01-01','18:00:00'),(1200,'Jornada de competencia','competencia','2026-01-01','20:00:00'),(1200,'Premiación','premiacion','2026-01-05','21:00:00');
INSERT IGNORE INTO torneos
(id,nombre,juego_id,descripcion,formato,estado,cupo_max_equipos,cupo_min_equipos,fecha_inicio_inscripcion,fecha_limite_inscripcion,fecha_inicio,fecha_fin,reglas,premios,ubicacion,localidad,comunidad,organizador_id,modalidad_id,sistema_puntuacion_id,puntos_victoria,puntos_empate,puntos_derrota,tipo_resultado,mejor_de)
VALUES (1201,'Copa QA 02',2,'Torneo sintético para pruebas integrales','liga','en_curso',16,2,'2026-01-01','2026-12-01','2026-02-02','2026-02-06','Reglas de prueba; resultados ficticios','Trofeo QA','Arena de pruebas','Montevideo','Uruguay',1001,2,2,3,0,0,'puntos',3);
INSERT IGNORE INTO torneo_config (torneo_id,clave,valor) VALUES (1201,'semilla_qa','true'),(1201,'reglas_version','1');
INSERT IGNORE INTO torneo_actividades (torneo_id,titulo,tipo,fecha,hora) VALUES (1201,'Reunión técnica QA','reunion','2026-02-02','18:00:00'),(1201,'Jornada de competencia','competencia','2026-02-02','20:00:00'),(1201,'Premiación','premiacion','2026-02-06','21:00:00');
INSERT IGNORE INTO torneos
(id,nombre,juego_id,descripcion,formato,estado,cupo_max_equipos,cupo_min_equipos,fecha_inicio_inscripcion,fecha_limite_inscripcion,fecha_inicio,fecha_fin,reglas,premios,ubicacion,localidad,comunidad,organizador_id,modalidad_id,sistema_puntuacion_id,puntos_victoria,puntos_empate,puntos_derrota,tipo_resultado,mejor_de)
VALUES (1202,'Copa QA 03',3,'Torneo sintético para pruebas integrales','suizo','inscripciones_abiertas',16,2,'2026-01-01','2026-12-01','2026-03-03','2026-03-07','Reglas de prueba; resultados ficticios','Trofeo QA','Arena de pruebas','Montevideo','Uruguay',1002,2,3,1,0,0,'goles',5);
INSERT IGNORE INTO torneo_config (torneo_id,clave,valor) VALUES (1202,'semilla_qa','true'),(1202,'reglas_version','1');
INSERT IGNORE INTO torneo_actividades (torneo_id,titulo,tipo,fecha,hora) VALUES (1202,'Reunión técnica QA','reunion','2026-03-03','18:00:00'),(1202,'Jornada de competencia','competencia','2026-03-03','20:00:00'),(1202,'Premiación','premiacion','2026-03-07','21:00:00');
INSERT IGNORE INTO torneos
(id,nombre,juego_id,descripcion,formato,estado,cupo_max_equipos,cupo_min_equipos,fecha_inicio_inscripcion,fecha_limite_inscripcion,fecha_inicio,fecha_fin,reglas,premios,ubicacion,localidad,comunidad,organizador_id,modalidad_id,sistema_puntuacion_id,puntos_victoria,puntos_empate,puntos_derrota,tipo_resultado,mejor_de)
VALUES (1203,'Copa QA 04',4,'Torneo sintético para pruebas integrales','liga','borrador',16,2,'2026-01-01','2026-12-01','2026-04-04','2026-04-08','Reglas de prueba; resultados ficticios','Trofeo QA','Arena de pruebas','Montevideo','Uruguay',1003,2,1,4,2,0,'puntos',1);
INSERT IGNORE INTO torneo_config (torneo_id,clave,valor) VALUES (1203,'semilla_qa','true'),(1203,'reglas_version','1');
INSERT IGNORE INTO torneo_actividades (torneo_id,titulo,tipo,fecha,hora) VALUES (1203,'Reunión técnica QA','reunion','2026-04-04','18:00:00'),(1203,'Jornada de competencia','competencia','2026-04-04','20:00:00'),(1203,'Premiación','premiacion','2026-04-08','21:00:00');
INSERT IGNORE INTO torneos
(id,nombre,juego_id,descripcion,formato,estado,cupo_max_equipos,cupo_min_equipos,fecha_inicio_inscripcion,fecha_limite_inscripcion,fecha_inicio,fecha_fin,reglas,premios,ubicacion,localidad,comunidad,organizador_id,modalidad_id,sistema_puntuacion_id,puntos_victoria,puntos_empate,puntos_derrota,tipo_resultado,mejor_de)
VALUES (1204,'Copa QA 05',5,'Torneo sintético para pruebas integrales','eliminacion_directa','finalizado',16,2,'2026-01-01','2026-12-01','2026-05-05','2026-05-09','Reglas de prueba; resultados ficticios','Trofeo QA','Arena de pruebas','Montevideo','Uruguay',1004,2,2,2,0.5,0,'booleano',3);
INSERT IGNORE INTO torneo_config (torneo_id,clave,valor) VALUES (1204,'semilla_qa','true'),(1204,'reglas_version','1');
INSERT IGNORE INTO torneo_actividades (torneo_id,titulo,tipo,fecha,hora) VALUES (1204,'Reunión técnica QA','reunion','2026-05-05','18:00:00'),(1204,'Jornada de competencia','competencia','2026-05-05','20:00:00'),(1204,'Premiación','premiacion','2026-05-09','21:00:00');
INSERT IGNORE INTO torneos
(id,nombre,juego_id,descripcion,formato,estado,cupo_max_equipos,cupo_min_equipos,fecha_inicio_inscripcion,fecha_limite_inscripcion,fecha_inicio,fecha_fin,reglas,premios,ubicacion,localidad,comunidad,organizador_id,modalidad_id,sistema_puntuacion_id,puntos_victoria,puntos_empate,puntos_derrota,tipo_resultado,mejor_de)
VALUES (1205,'Copa QA 06',1,'Torneo sintético para pruebas integrales','liga','en_curso',16,2,'2026-01-01','2026-12-01','2026-06-06','2026-06-10','Reglas de prueba; resultados ficticios','Trofeo QA','Arena de pruebas','Montevideo','Uruguay',1005,2,3,3,1,0,'rondas',5);
INSERT IGNORE INTO torneo_config (torneo_id,clave,valor) VALUES (1205,'semilla_qa','true'),(1205,'reglas_version','1');
INSERT IGNORE INTO torneo_actividades (torneo_id,titulo,tipo,fecha,hora) VALUES (1205,'Reunión técnica QA','reunion','2026-06-06','18:00:00'),(1205,'Jornada de competencia','competencia','2026-06-06','20:00:00'),(1205,'Premiación','premiacion','2026-06-10','21:00:00');
INSERT IGNORE INTO torneos
(id,nombre,juego_id,descripcion,formato,estado,cupo_max_equipos,cupo_min_equipos,fecha_inicio_inscripcion,fecha_limite_inscripcion,fecha_inicio,fecha_fin,reglas,premios,ubicacion,localidad,comunidad,organizador_id,modalidad_id,sistema_puntuacion_id,puntos_victoria,puntos_empate,puntos_derrota,tipo_resultado,mejor_de)
VALUES (1206,'Copa QA 07',2,'Torneo sintético para pruebas integrales','liga','inscripciones_abiertas',16,2,'2026-01-01','2026-12-01','2026-07-07','2026-07-11','Reglas de prueba; resultados ficticios','Trofeo QA','Arena de pruebas','Montevideo','Uruguay',1000,2,1,3,0,0,'puntos',1);
INSERT IGNORE INTO torneo_config (torneo_id,clave,valor) VALUES (1206,'semilla_qa','true'),(1206,'reglas_version','1');
INSERT IGNORE INTO torneo_actividades (torneo_id,titulo,tipo,fecha,hora) VALUES (1206,'Reunión técnica QA','reunion','2026-07-07','18:00:00'),(1206,'Jornada de competencia','competencia','2026-07-07','20:00:00'),(1206,'Premiación','premiacion','2026-07-11','21:00:00');
INSERT IGNORE INTO torneos
(id,nombre,juego_id,descripcion,formato,estado,cupo_max_equipos,cupo_min_equipos,fecha_inicio_inscripcion,fecha_limite_inscripcion,fecha_inicio,fecha_fin,reglas,premios,ubicacion,localidad,comunidad,organizador_id,modalidad_id,sistema_puntuacion_id,puntos_victoria,puntos_empate,puntos_derrota,tipo_resultado,mejor_de)
VALUES (1207,'Copa QA 08',3,'Torneo sintético para pruebas integrales','eliminacion_directa','borrador',16,2,'2026-01-01','2026-12-01','2026-08-08','2026-08-12','Reglas de prueba; resultados ficticios','Trofeo QA','Arena de pruebas','Montevideo','Uruguay',1001,2,2,1,0,0,'goles',3);
INSERT IGNORE INTO torneo_config (torneo_id,clave,valor) VALUES (1207,'semilla_qa','true'),(1207,'reglas_version','1');
INSERT IGNORE INTO torneo_actividades (torneo_id,titulo,tipo,fecha,hora) VALUES (1207,'Reunión técnica QA','reunion','2026-08-08','18:00:00'),(1207,'Jornada de competencia','competencia','2026-08-08','20:00:00'),(1207,'Premiación','premiacion','2026-08-12','21:00:00');
INSERT IGNORE INTO torneos
(id,nombre,juego_id,descripcion,formato,estado,cupo_max_equipos,cupo_min_equipos,fecha_inicio_inscripcion,fecha_limite_inscripcion,fecha_inicio,fecha_fin,reglas,premios,ubicacion,localidad,comunidad,organizador_id,modalidad_id,sistema_puntuacion_id,puntos_victoria,puntos_empate,puntos_derrota,tipo_resultado,mejor_de)
VALUES (1208,'Copa QA 09',4,'Torneo sintético para pruebas integrales','suizo','finalizado',16,2,'2026-01-01','2026-12-01','2026-09-09','2026-09-13','Reglas de prueba; resultados ficticios','Trofeo QA','Arena de pruebas','Montevideo','Uruguay',1002,2,3,4,2,0,'puntos',5);
INSERT IGNORE INTO torneo_config (torneo_id,clave,valor) VALUES (1208,'semilla_qa','true'),(1208,'reglas_version','1');
INSERT IGNORE INTO torneo_actividades (torneo_id,titulo,tipo,fecha,hora) VALUES (1208,'Reunión técnica QA','reunion','2026-09-09','18:00:00'),(1208,'Jornada de competencia','competencia','2026-09-09','20:00:00'),(1208,'Premiación','premiacion','2026-09-13','21:00:00');
INSERT IGNORE INTO torneos
(id,nombre,juego_id,descripcion,formato,estado,cupo_max_equipos,cupo_min_equipos,fecha_inicio_inscripcion,fecha_limite_inscripcion,fecha_inicio,fecha_fin,reglas,premios,ubicacion,localidad,comunidad,organizador_id,modalidad_id,sistema_puntuacion_id,puntos_victoria,puntos_empate,puntos_derrota,tipo_resultado,mejor_de)
VALUES (1209,'Copa QA 10',5,'Torneo sintético para pruebas integrales','liga','finalizado',16,2,'2026-01-01','2026-12-01','2026-01-10','2026-01-14','Reglas de prueba; resultados ficticios','Trofeo QA','Arena de pruebas','Montevideo','Uruguay',1003,2,1,2,0.5,0,'booleano',1);
INSERT IGNORE INTO torneo_config (torneo_id,clave,valor) VALUES (1209,'semilla_qa','true'),(1209,'reglas_version','1');
INSERT IGNORE INTO torneo_actividades (torneo_id,titulo,tipo,fecha,hora) VALUES (1209,'Reunión técnica QA','reunion','2026-01-10','18:00:00'),(1209,'Jornada de competencia','competencia','2026-01-10','20:00:00'),(1209,'Premiación','premiacion','2026-01-14','21:00:00');
INSERT IGNORE INTO torneos
(id,nombre,juego_id,descripcion,formato,estado,cupo_max_equipos,cupo_min_equipos,fecha_inicio_inscripcion,fecha_limite_inscripcion,fecha_inicio,fecha_fin,reglas,premios,ubicacion,localidad,comunidad,organizador_id,modalidad_id,sistema_puntuacion_id,puntos_victoria,puntos_empate,puntos_derrota,tipo_resultado,mejor_de)
VALUES (1210,'Copa QA 11',1,'Torneo sintético para pruebas integrales','liga','inscripciones_abiertas',16,2,'2026-01-01','2026-12-01','2026-02-11','2026-02-15','Reglas de prueba; resultados ficticios','Trofeo QA','Arena de pruebas','Montevideo','Uruguay',1004,2,2,3,1,0,'rondas',3);
INSERT IGNORE INTO torneo_config (torneo_id,clave,valor) VALUES (1210,'semilla_qa','true'),(1210,'reglas_version','1');
INSERT IGNORE INTO torneo_actividades (torneo_id,titulo,tipo,fecha,hora) VALUES (1210,'Reunión técnica QA','reunion','2026-02-11','18:00:00'),(1210,'Jornada de competencia','competencia','2026-02-11','20:00:00'),(1210,'Premiación','premiacion','2026-02-15','21:00:00');
INSERT IGNORE INTO torneos
(id,nombre,juego_id,descripcion,formato,estado,cupo_max_equipos,cupo_min_equipos,fecha_inicio_inscripcion,fecha_limite_inscripcion,fecha_inicio,fecha_fin,reglas,premios,ubicacion,localidad,comunidad,organizador_id,modalidad_id,sistema_puntuacion_id,puntos_victoria,puntos_empate,puntos_derrota,tipo_resultado,mejor_de)
VALUES (1211,'Copa QA 12',2,'Torneo sintético para pruebas integrales','liga','borrador',16,2,'2026-01-01','2026-12-01','2026-03-12','2026-03-16','Reglas de prueba; resultados ficticios','Trofeo QA','Arena de pruebas','Montevideo','Uruguay',1005,2,3,3,0,0,'puntos',5);
INSERT IGNORE INTO torneo_config (torneo_id,clave,valor) VALUES (1211,'semilla_qa','true'),(1211,'reglas_version','1');
INSERT IGNORE INTO torneo_actividades (torneo_id,titulo,tipo,fecha,hora) VALUES (1211,'Reunión técnica QA','reunion','2026-03-12','18:00:00'),(1211,'Jornada de competencia','competencia','2026-03-12','20:00:00'),(1211,'Premiación','premiacion','2026-03-16','21:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1300,1200,'equipo',1100,'Escuadra QA 01','confirmado',1000,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1301,1200,'equipo',1105,'Escuadra QA 06','confirmado',1000,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1302,1200,'equipo',1110,'Escuadra QA 11','confirmado',1000,'2026-01-02 12:00:00');
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1400,1200,1,1300,1302,'2026-01-02 20:00:00','Cancha 1',3,1,'finalizado',1300,1000);
INSERT IGNORE INTO resultados_detalle (encuentro_id,tipo_dato,valor) VALUES (1400,'mvp','Jugador QA 1010'),(1400,'duracion_minutos','45'),(1400,'observacion','Resultado ficticio para QA');
INSERT IGNORE INTO torneo_cambio_estado (torneo_id,estado_anterior,estado_nuevo,motivo,usuario_id) VALUES (1200,'borrador','inscripciones_abiertas','Apertura inicial QA',1000),(1200,'inscripciones_abiertas','finalizado','Transición sintética QA',1000);
INSERT IGNORE INTO torneo_comentarios (torneo_id,usuario_id,nombre_autor,email_autor,comentario,aprobado) VALUES (1200,1034,'Jugador QA','jugador.qa25@ascend.test','Comentario de prueba del torneo 1200',1);
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1303,1201,'equipo',1101,'Escuadra QA 02','confirmado',1001,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1304,1201,'equipo',1106,'Escuadra QA 07','confirmado',1001,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1305,1201,'equipo',1111,'Escuadra QA 12','confirmado',1001,'2026-01-02 12:00:00');
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1404,1201,1,1303,1305,'2026-02-02 20:00:00','Cancha 1',3,1,'finalizado',1303,1001);
INSERT IGNORE INTO resultados_detalle (encuentro_id,tipo_dato,valor) VALUES (1404,'mvp','Jugador QA 1010'),(1404,'duracion_minutos','45'),(1404,'observacion','Resultado ficticio para QA');
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1405,1201,1,1303,1304,'2026-02-03 20:00:00','Cancha 1',NULL,NULL,'programado',NULL,1001);
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1406,1201,2,1304,1305,'2026-02-04 20:00:00','Cancha 1',NULL,NULL,'programado',NULL,1001);
INSERT IGNORE INTO torneo_cambio_estado (torneo_id,estado_anterior,estado_nuevo,motivo,usuario_id) VALUES (1201,'borrador','inscripciones_abiertas','Apertura inicial QA',1001),(1201,'inscripciones_abiertas','en_curso','Transición sintética QA',1001);
INSERT IGNORE INTO torneo_comentarios (torneo_id,usuario_id,nombre_autor,email_autor,comentario,aprobado) VALUES (1201,1035,'Jugador QA','jugador.qa26@ascend.test','Comentario de prueba del torneo 1201',1);
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1306,1202,'equipo',1102,'Escuadra QA 03','confirmado',1002,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1307,1202,'equipo',1107,'Escuadra QA 08','confirmado',1002,'2026-01-02 12:00:00');
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1408,1202,1,1306,1307,'2026-03-02 20:00:00','Cancha 1',NULL,NULL,'programado',NULL,1002);
INSERT IGNORE INTO torneo_suizo_parejas (torneo_id,participante_a_id,participante_b_id,ronda,ya_se_enfrentaron,fecha_encuentro) VALUES (1202,1306,1307,1,1,'2026-03-01 20:00:00');
INSERT IGNORE INTO torneo_cambio_estado (torneo_id,estado_anterior,estado_nuevo,motivo,usuario_id) VALUES (1202,'borrador','inscripciones_abiertas','Apertura inicial QA',1002),(1202,'inscripciones_abiertas','inscripciones_abiertas','Transición sintética QA',1002);
INSERT IGNORE INTO torneo_comentarios (torneo_id,usuario_id,nombre_autor,email_autor,comentario,aprobado) VALUES (1202,1036,'Jugador QA','jugador.qa27@ascend.test','Comentario de prueba del torneo 1202',1);
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1308,1203,'equipo',1103,'Escuadra QA 04','confirmado',1003,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1309,1203,'equipo',1108,'Escuadra QA 09','confirmado',1003,'2026-01-02 12:00:00');
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1412,1203,1,1308,1309,'2026-04-02 20:00:00','Cancha 1',NULL,NULL,'programado',NULL,1003);
INSERT IGNORE INTO torneo_cambio_estado (torneo_id,estado_anterior,estado_nuevo,motivo,usuario_id) VALUES (1203,'borrador','inscripciones_abiertas','Apertura inicial QA',1003),(1203,'inscripciones_abiertas','borrador','Transición sintética QA',1003);
INSERT IGNORE INTO torneo_comentarios (torneo_id,usuario_id,nombre_autor,email_autor,comentario,aprobado) VALUES (1203,1037,'Jugador QA','jugador.qa28@ascend.test','Comentario de prueba del torneo 1203',1);
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1310,1204,'equipo',1104,'Escuadra QA 05','confirmado',1004,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1311,1204,'equipo',1109,'Escuadra QA 10','confirmado',1004,'2026-01-02 12:00:00');
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1416,1204,1,1310,1311,'2026-05-02 20:00:00','Cancha 1',3,1,'finalizado',1310,1004);
INSERT IGNORE INTO resultados_detalle (encuentro_id,tipo_dato,valor) VALUES (1416,'mvp','Jugador QA 1010'),(1416,'duracion_minutos','45'),(1416,'observacion','Resultado ficticio para QA');
INSERT IGNORE INTO torneo_cambio_estado (torneo_id,estado_anterior,estado_nuevo,motivo,usuario_id) VALUES (1204,'borrador','inscripciones_abiertas','Apertura inicial QA',1004),(1204,'inscripciones_abiertas','finalizado','Transición sintética QA',1004);
INSERT IGNORE INTO torneo_comentarios (torneo_id,usuario_id,nombre_autor,email_autor,comentario,aprobado) VALUES (1204,1038,'Jugador QA','jugador.qa29@ascend.test','Comentario de prueba del torneo 1204',1);
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1312,1205,'equipo',1100,'Escuadra QA 01','confirmado',1005,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1313,1205,'equipo',1105,'Escuadra QA 06','confirmado',1005,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1314,1205,'equipo',1110,'Escuadra QA 11','confirmado',1005,'2026-01-02 12:00:00');
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1420,1205,1,1312,1314,'2026-06-02 20:00:00','Cancha 1',3,1,'finalizado',1312,1005);
INSERT IGNORE INTO resultados_detalle (encuentro_id,tipo_dato,valor) VALUES (1420,'mvp','Jugador QA 1010'),(1420,'duracion_minutos','45'),(1420,'observacion','Resultado ficticio para QA');
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1421,1205,1,1312,1313,'2026-06-03 20:00:00','Cancha 1',NULL,NULL,'programado',NULL,1005);
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1422,1205,2,1313,1314,'2026-06-04 20:00:00','Cancha 1',NULL,NULL,'programado',NULL,1005);
INSERT IGNORE INTO torneo_cambio_estado (torneo_id,estado_anterior,estado_nuevo,motivo,usuario_id) VALUES (1205,'borrador','inscripciones_abiertas','Apertura inicial QA',1005),(1205,'inscripciones_abiertas','en_curso','Transición sintética QA',1005);
INSERT IGNORE INTO torneo_comentarios (torneo_id,usuario_id,nombre_autor,email_autor,comentario,aprobado) VALUES (1205,1039,'Jugador QA','jugador.qa30@ascend.test','Comentario de prueba del torneo 1205',1);
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1315,1206,'equipo',1101,'Escuadra QA 02','confirmado',1000,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1316,1206,'equipo',1106,'Escuadra QA 07','confirmado',1000,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1317,1206,'equipo',1111,'Escuadra QA 12','confirmado',1000,'2026-01-02 12:00:00');
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1424,1206,1,1315,1317,'2026-07-02 20:00:00','Cancha 1',NULL,NULL,'programado',NULL,1000);
INSERT IGNORE INTO torneo_cambio_estado (torneo_id,estado_anterior,estado_nuevo,motivo,usuario_id) VALUES (1206,'borrador','inscripciones_abiertas','Apertura inicial QA',1000),(1206,'inscripciones_abiertas','inscripciones_abiertas','Transición sintética QA',1000);
INSERT IGNORE INTO torneo_comentarios (torneo_id,usuario_id,nombre_autor,email_autor,comentario,aprobado) VALUES (1206,1040,'Jugador QA','jugador.qa31@ascend.test','Comentario de prueba del torneo 1206',1);
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1318,1207,'equipo',1102,'Escuadra QA 03','confirmado',1001,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1319,1207,'equipo',1107,'Escuadra QA 08','confirmado',1001,'2026-01-02 12:00:00');
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1428,1207,1,1318,1319,'2026-08-02 20:00:00','Cancha 1',NULL,NULL,'programado',NULL,1001);
INSERT IGNORE INTO torneo_cambio_estado (torneo_id,estado_anterior,estado_nuevo,motivo,usuario_id) VALUES (1207,'borrador','inscripciones_abiertas','Apertura inicial QA',1001),(1207,'inscripciones_abiertas','borrador','Transición sintética QA',1001);
INSERT IGNORE INTO torneo_comentarios (torneo_id,usuario_id,nombre_autor,email_autor,comentario,aprobado) VALUES (1207,1041,'Jugador QA','jugador.qa32@ascend.test','Comentario de prueba del torneo 1207',1);
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1320,1208,'equipo',1103,'Escuadra QA 04','confirmado',1002,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1321,1208,'equipo',1108,'Escuadra QA 09','confirmado',1002,'2026-01-02 12:00:00');
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1432,1208,1,1320,1321,'2026-09-02 20:00:00','Cancha 1',3,1,'finalizado',1320,1002);
INSERT IGNORE INTO resultados_detalle (encuentro_id,tipo_dato,valor) VALUES (1432,'mvp','Jugador QA 1010'),(1432,'duracion_minutos','45'),(1432,'observacion','Resultado ficticio para QA');
INSERT IGNORE INTO torneo_suizo_parejas (torneo_id,participante_a_id,participante_b_id,ronda,ya_se_enfrentaron,fecha_encuentro) VALUES (1208,1320,1321,1,1,'2026-03-01 20:00:00');
INSERT IGNORE INTO torneo_cambio_estado (torneo_id,estado_anterior,estado_nuevo,motivo,usuario_id) VALUES (1208,'borrador','inscripciones_abiertas','Apertura inicial QA',1002),(1208,'inscripciones_abiertas','finalizado','Transición sintética QA',1002);
INSERT IGNORE INTO torneo_comentarios (torneo_id,usuario_id,nombre_autor,email_autor,comentario,aprobado) VALUES (1208,1042,'Jugador QA','jugador.qa33@ascend.test','Comentario de prueba del torneo 1208',1);
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1322,1209,'equipo',1104,'Escuadra QA 05','confirmado',1003,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1323,1209,'equipo',1109,'Escuadra QA 10','confirmado',1003,'2026-01-02 12:00:00');
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1436,1209,1,1322,1323,'2026-01-02 20:00:00','Cancha 1',3,1,'finalizado',1322,1003);
INSERT IGNORE INTO resultados_detalle (encuentro_id,tipo_dato,valor) VALUES (1436,'mvp','Jugador QA 1010'),(1436,'duracion_minutos','45'),(1436,'observacion','Resultado ficticio para QA');
INSERT IGNORE INTO torneo_cambio_estado (torneo_id,estado_anterior,estado_nuevo,motivo,usuario_id) VALUES (1209,'borrador','inscripciones_abiertas','Apertura inicial QA',1003),(1209,'inscripciones_abiertas','en_curso','Transición sintética QA',1003),(1209,'en_curso','finalizado','Cierre sintético QA',1003);
INSERT IGNORE INTO torneo_comentarios (torneo_id,usuario_id,nombre_autor,email_autor,comentario,aprobado) VALUES (1209,1043,'Jugador QA','jugador.qa34@ascend.test','Comentario de prueba del torneo 1209',1);
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1324,1210,'equipo',1100,'Escuadra QA 01','confirmado',1004,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1325,1210,'equipo',1105,'Escuadra QA 06','confirmado',1004,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1326,1210,'equipo',1110,'Escuadra QA 11','confirmado',1004,'2026-01-02 12:00:00');
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1440,1210,1,1324,1326,'2026-02-02 20:00:00','Cancha 1',NULL,NULL,'programado',NULL,1004);
INSERT IGNORE INTO torneo_cambio_estado (torneo_id,estado_anterior,estado_nuevo,motivo,usuario_id) VALUES (1210,'borrador','inscripciones_abiertas','Apertura inicial QA',1004),(1210,'inscripciones_abiertas','inscripciones_abiertas','Transición sintética QA',1004);
INSERT IGNORE INTO torneo_comentarios (torneo_id,usuario_id,nombre_autor,email_autor,comentario,aprobado) VALUES (1210,1044,'Jugador QA','jugador.qa35@ascend.test','Comentario de prueba del torneo 1210',1);
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1327,1211,'equipo',1101,'Escuadra QA 02','confirmado',1005,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1328,1211,'equipo',1106,'Escuadra QA 07','confirmado',1005,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1329,1211,'equipo',1111,'Escuadra QA 12','confirmado',1005,'2026-01-02 12:00:00');
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1444,1211,1,1327,1329,'2026-03-02 20:00:00','Cancha 1',NULL,NULL,'programado',NULL,1005);
INSERT IGNORE INTO torneo_cambio_estado (torneo_id,estado_anterior,estado_nuevo,motivo,usuario_id) VALUES (1211,'borrador','inscripciones_abiertas','Apertura inicial QA',1005),(1211,'inscripciones_abiertas','borrador','Transición sintética QA',1005);
INSERT IGNORE INTO torneo_comentarios (torneo_id,usuario_id,nombre_autor,email_autor,comentario,aprobado) VALUES (1211,1045,'Jugador QA','jugador.qa36@ascend.test','Comentario de prueba del torneo 1211',1);

-- Posiciones de los torneos QA derivadas de los encuentros finalizados.
-- Se recalculan desde cero para que partidos jugados/ganados/empatados/perdidos
-- y puntos sean coherentes con los resultados y la configuración de cada torneo.
DELETE FROM torneo_posiciones WHERE torneo_id BETWEEN 1200 AND 1211;

INSERT INTO torneo_posiciones
    (torneo_id, participante_id, partidos_jugados, partidos_ganados,
     partidos_empatados, partidos_perdidos, puntos_favor, puntos_contra, puntos, sanciones_puntos)
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
          AND e.torneo_id BETWEEN 1200 AND 1211
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
          AND e.torneo_id BETWEEN 1200 AND 1211
    ) AS lados
    GROUP BY lados.torneo_id, lados.participante_id
) AS e ON e.torneo_id = p.torneo_id AND e.participante_id = p.id
WHERE p.torneo_id BETWEEN 1200 AND 1211
  AND p.estado = 'confirmado';

INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1010,1,'en_curso',2,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1010,2,'bloqueado',3,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1010,3,'desbloqueado',4,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1010,4,'en_curso',5,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1011,1,'bloqueado',3,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1011,2,'desbloqueado',4,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1011,3,'en_curso',5,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1011,4,'bloqueado',6,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1012,1,'desbloqueado',4,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1012,2,'en_curso',5,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1012,3,'bloqueado',6,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1012,4,'desbloqueado',7,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1013,1,'en_curso',5,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1013,2,'bloqueado',6,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1013,3,'desbloqueado',7,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1013,4,'en_curso',8,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1014,1,'bloqueado',6,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1014,2,'desbloqueado',7,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1014,3,'en_curso',8,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1014,4,'bloqueado',9,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1015,1,'desbloqueado',7,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1015,2,'en_curso',8,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1015,3,'bloqueado',9,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1015,4,'desbloqueado',10,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1016,1,'en_curso',8,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1016,2,'bloqueado',9,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1016,3,'desbloqueado',10,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1016,4,'en_curso',1,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1017,1,'bloqueado',9,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1017,2,'desbloqueado',10,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1017,3,'en_curso',1,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1017,4,'bloqueado',2,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1018,1,'desbloqueado',10,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1018,2,'en_curso',1,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1018,3,'bloqueado',2,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1018,4,'desbloqueado',3,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1019,1,'en_curso',1,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1019,2,'bloqueado',2,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1019,3,'desbloqueado',3,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1019,4,'en_curso',4,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1020,1,'bloqueado',2,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1020,2,'desbloqueado',3,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1020,3,'en_curso',4,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1020,4,'bloqueado',5,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1021,1,'desbloqueado',3,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1021,2,'en_curso',4,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1021,3,'bloqueado',5,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1021,4,'desbloqueado',6,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1022,1,'en_curso',4,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1022,2,'bloqueado',5,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1022,3,'desbloqueado',6,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1022,4,'en_curso',7,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1023,1,'bloqueado',5,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1023,2,'desbloqueado',6,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1023,3,'en_curso',7,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1023,4,'bloqueado',8,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1024,1,'desbloqueado',6,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1024,2,'en_curso',7,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1024,3,'bloqueado',8,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1024,4,'desbloqueado',9,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1025,1,'en_curso',7,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1025,2,'bloqueado',8,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1025,3,'desbloqueado',9,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1025,4,'en_curso',10,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1026,1,'bloqueado',8,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1026,2,'desbloqueado',9,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1026,3,'en_curso',10,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1026,4,'bloqueado',1,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1027,1,'desbloqueado',9,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1027,2,'en_curso',10,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1027,3,'bloqueado',1,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1027,4,'desbloqueado',2,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1028,1,'en_curso',10,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1028,2,'bloqueado',1,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1028,3,'desbloqueado',2,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1028,4,'en_curso',3,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1029,1,'bloqueado',1,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1029,2,'desbloqueado',2,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1029,3,'en_curso',3,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1029,4,'bloqueado',4,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1030,1,'desbloqueado',2,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1030,2,'en_curso',3,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1030,3,'bloqueado',4,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1030,4,'desbloqueado',5,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1031,1,'en_curso',3,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1031,2,'bloqueado',4,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1031,3,'desbloqueado',5,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1031,4,'en_curso',6,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1032,1,'bloqueado',4,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1032,2,'desbloqueado',5,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1032,3,'en_curso',6,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1032,4,'bloqueado',7,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1033,1,'desbloqueado',5,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1033,2,'en_curso',6,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1033,3,'bloqueado',7,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1033,4,'desbloqueado',8,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1034,1,'en_curso',6,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1034,2,'bloqueado',7,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1034,3,'desbloqueado',8,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1034,4,'en_curso',9,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1035,1,'bloqueado',7,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1035,2,'desbloqueado',8,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1035,3,'en_curso',9,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1035,4,'bloqueado',10,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1036,1,'desbloqueado',8,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1036,2,'en_curso',9,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1036,3,'bloqueado',10,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1036,4,'desbloqueado',1,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1037,1,'en_curso',9,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1037,2,'bloqueado',10,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1037,3,'desbloqueado',1,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1037,4,'en_curso',2,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1038,1,'bloqueado',10,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1038,2,'desbloqueado',1,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1038,3,'en_curso',2,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1038,4,'bloqueado',3,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1039,1,'desbloqueado',1,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1039,2,'en_curso',2,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1039,3,'bloqueado',3,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1039,4,'desbloqueado',4,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1040,1,'en_curso',2,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1040,2,'bloqueado',3,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1040,3,'desbloqueado',4,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1040,4,'en_curso',5,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1041,1,'bloqueado',3,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1041,2,'desbloqueado',4,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1041,3,'en_curso',5,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1041,4,'bloqueado',6,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1042,1,'desbloqueado',4,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1042,2,'en_curso',5,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1042,3,'bloqueado',6,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1042,4,'desbloqueado',7,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1043,1,'en_curso',5,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1043,2,'bloqueado',6,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1043,3,'desbloqueado',7,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1043,4,'en_curso',8,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1044,1,'bloqueado',6,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1044,2,'desbloqueado',7,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1044,3,'en_curso',8,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1044,4,'bloqueado',9,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1045,1,'desbloqueado',7,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1045,2,'en_curso',8,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1045,3,'bloqueado',9,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1045,4,'desbloqueado',10,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1046,1,'en_curso',8,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1046,2,'bloqueado',9,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1046,3,'desbloqueado',10,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1046,4,'en_curso',1,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1047,1,'bloqueado',9,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1047,2,'desbloqueado',10,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1047,3,'en_curso',1,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1047,4,'bloqueado',2,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1048,1,'desbloqueado',10,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1048,2,'en_curso',1,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1048,3,'bloqueado',2,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1048,4,'desbloqueado',3,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1049,1,'en_curso',1,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1049,2,'bloqueado',2,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1049,3,'desbloqueado',3,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1049,4,'en_curso',4,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1050,1,'bloqueado',2,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1050,2,'desbloqueado',3,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1050,3,'en_curso',4,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1050,4,'bloqueado',5,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1051,1,'desbloqueado',3,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1051,2,'en_curso',4,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1051,3,'bloqueado',5,10);
INSERT IGNORE INTO usuario_logros (usuario_id,logro_id,estado,progreso_actual,progreso_objetivo) VALUES (1051,4,'desbloqueado',6,10);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1010,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1010','/torneos/1200',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1010,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1010','/torneos/1200',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1010,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1010','/torneos/1200',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1011,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1011','/torneos/1201',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1011,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1011','/torneos/1201',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1011,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1011','/torneos/1201',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1012,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1012','/torneos/1202',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1012,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1012','/torneos/1202',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1012,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1012','/torneos/1202',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1013,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1013','/torneos/1203',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1013,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1013','/torneos/1203',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1013,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1013','/torneos/1203',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1014,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1014','/torneos/1204',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1014,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1014','/torneos/1204',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1014,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1014','/torneos/1204',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1015,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1015','/torneos/1205',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1015,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1015','/torneos/1205',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1015,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1015','/torneos/1205',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1016,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1016','/torneos/1206',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1016,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1016','/torneos/1206',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1016,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1016','/torneos/1206',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1017,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1017','/torneos/1207',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1017,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1017','/torneos/1207',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1017,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1017','/torneos/1207',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1018,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1018','/torneos/1208',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1018,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1018','/torneos/1208',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1018,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1018','/torneos/1208',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1019,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1019','/torneos/1209',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1019,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1019','/torneos/1209',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1019,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1019','/torneos/1209',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1020,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1020','/torneos/1210',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1020,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1020','/torneos/1210',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1020,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1020','/torneos/1210',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1021,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1021','/torneos/1211',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1021,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1021','/torneos/1211',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1021,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1021','/torneos/1211',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1022,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1022','/torneos/1200',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1022,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1022','/torneos/1200',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1022,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1022','/torneos/1200',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1023,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1023','/torneos/1201',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1023,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1023','/torneos/1201',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1023,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1023','/torneos/1201',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1024,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1024','/torneos/1202',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1024,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1024','/torneos/1202',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1024,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1024','/torneos/1202',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1025,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1025','/torneos/1203',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1025,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1025','/torneos/1203',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1025,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1025','/torneos/1203',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1026,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1026','/torneos/1204',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1026,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1026','/torneos/1204',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1026,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1026','/torneos/1204',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1027,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1027','/torneos/1205',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1027,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1027','/torneos/1205',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1027,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1027','/torneos/1205',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1028,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1028','/torneos/1206',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1028,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1028','/torneos/1206',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1028,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1028','/torneos/1206',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1029,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1029','/torneos/1207',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1029,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1029','/torneos/1207',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1029,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1029','/torneos/1207',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1030,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1030','/torneos/1208',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1030,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1030','/torneos/1208',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1030,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1030','/torneos/1208',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1031,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1031','/torneos/1209',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1031,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1031','/torneos/1209',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1031,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1031','/torneos/1209',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1032,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1032','/torneos/1210',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1032,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1032','/torneos/1210',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1032,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1032','/torneos/1210',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1033,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1033','/torneos/1211',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1033,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1033','/torneos/1211',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1033,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1033','/torneos/1211',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1034,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1034','/torneos/1200',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1034,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1034','/torneos/1200',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1034,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1034','/torneos/1200',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1035,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1035','/torneos/1201',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1035,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1035','/torneos/1201',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1035,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1035','/torneos/1201',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1036,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1036','/torneos/1202',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1036,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1036','/torneos/1202',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1036,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1036','/torneos/1202',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1037,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1037','/torneos/1203',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1037,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1037','/torneos/1203',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1037,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1037','/torneos/1203',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1038,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1038','/torneos/1204',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1038,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1038','/torneos/1204',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1038,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1038','/torneos/1204',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1039,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1039','/torneos/1205',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1039,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1039','/torneos/1205',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1039,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1039','/torneos/1205',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1040,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1040','/torneos/1206',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1040,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1040','/torneos/1206',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1040,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1040','/torneos/1206',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1041,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1041','/torneos/1207',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1041,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1041','/torneos/1207',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1041,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1041','/torneos/1207',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1042,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1042','/torneos/1208',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1042,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1042','/torneos/1208',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1042,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1042','/torneos/1208',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1043,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1043','/torneos/1209',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1043,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1043','/torneos/1209',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1043,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1043','/torneos/1209',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1044,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1044','/torneos/1210',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1044,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1044','/torneos/1210',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1044,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1044','/torneos/1210',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1045,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1045','/torneos/1211',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1045,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1045','/torneos/1211',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1045,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1045','/torneos/1211',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1046,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1046','/torneos/1200',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1046,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1046','/torneos/1200',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1046,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1046','/torneos/1200',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1047,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1047','/torneos/1201',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1047,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1047','/torneos/1201',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1047,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1047','/torneos/1201',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1048,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1048','/torneos/1202',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1048,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1048','/torneos/1202',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1048,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1048','/torneos/1202',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1049,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1049','/torneos/1203',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1049,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1049','/torneos/1203',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1049,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1049','/torneos/1203',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1050,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1050','/torneos/1204',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1050,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1050','/torneos/1204',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1050,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1050','/torneos/1204',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1051,'info','Aviso QA 1','Notificación sintética para validar bandeja del usuario 1051','/torneos/1205',1);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1051,'inscripcion','Aviso QA 2','Notificación sintética para validar bandeja del usuario 1051','/torneos/1205',0);
INSERT IGNORE INTO notificaciones (usuario_id,tipo,titulo,mensaje,enlace_relacionado,leido) VALUES (1051,'resultado','Aviso QA 3','Notificación sintética para validar bandeja del usuario 1051','/torneos/1205',1);
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('organizador.qa1@ascend.test',1000,1,'192.0.2.1','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1000,'seed.qa','Actividad sintética de prueba','192.0.2.1','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1000,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('organizador.qa2@ascend.test',1001,1,'192.0.2.2','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1001,'seed.qa','Actividad sintética de prueba','192.0.2.2','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1001,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('organizador.qa3@ascend.test',1002,1,'192.0.2.3','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1002,'seed.qa','Actividad sintética de prueba','192.0.2.3','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1002,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('organizador.qa4@ascend.test',1003,1,'192.0.2.4','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1003,'seed.qa','Actividad sintética de prueba','192.0.2.4','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1003,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('organizador.qa5@ascend.test',1004,1,'192.0.2.5','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1004,'seed.qa','Actividad sintética de prueba','192.0.2.5','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1004,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('organizador.qa6@ascend.test',1005,1,'192.0.2.6','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1005,'seed.qa','Actividad sintética de prueba','192.0.2.6','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1005,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa01@ascend.test',1010,1,'192.0.2.7','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1010,'seed.qa','Actividad sintética de prueba','192.0.2.7','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1010,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa02@ascend.test',1011,1,'192.0.2.8','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1011,'seed.qa','Actividad sintética de prueba','192.0.2.8','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1011,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa03@ascend.test',1012,1,'192.0.2.9','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1012,'seed.qa','Actividad sintética de prueba','192.0.2.9','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1012,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa04@ascend.test',1013,1,'192.0.2.10','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1013,'seed.qa','Actividad sintética de prueba','192.0.2.10','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1013,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa05@ascend.test',1014,1,'192.0.2.11','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1014,'seed.qa','Actividad sintética de prueba','192.0.2.11','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1014,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa06@ascend.test',1015,1,'192.0.2.12','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1015,'seed.qa','Actividad sintética de prueba','192.0.2.12','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1015,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa07@ascend.test',1016,1,'192.0.2.13','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1016,'seed.qa','Actividad sintética de prueba','192.0.2.13','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1016,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa08@ascend.test',1017,1,'192.0.2.14','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1017,'seed.qa','Actividad sintética de prueba','192.0.2.14','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1017,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa09@ascend.test',1018,1,'192.0.2.15','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1018,'seed.qa','Actividad sintética de prueba','192.0.2.15','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1018,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa10@ascend.test',1019,1,'192.0.2.16','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1019,'seed.qa','Actividad sintética de prueba','192.0.2.16','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1019,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa11@ascend.test',1020,1,'192.0.2.17','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1020,'seed.qa','Actividad sintética de prueba','192.0.2.17','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1020,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa12@ascend.test',1021,1,'192.0.2.18','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1021,'seed.qa','Actividad sintética de prueba','192.0.2.18','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1021,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa13@ascend.test',1022,1,'192.0.2.19','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1022,'seed.qa','Actividad sintética de prueba','192.0.2.19','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1022,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa14@ascend.test',1023,1,'192.0.2.20','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1023,'seed.qa','Actividad sintética de prueba','192.0.2.20','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1023,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa15@ascend.test',1024,1,'192.0.2.21','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1024,'seed.qa','Actividad sintética de prueba','192.0.2.21','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1024,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa16@ascend.test',1025,1,'192.0.2.22','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1025,'seed.qa','Actividad sintética de prueba','192.0.2.22','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1025,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa17@ascend.test',1026,1,'192.0.2.23','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1026,'seed.qa','Actividad sintética de prueba','192.0.2.23','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1026,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa18@ascend.test',1027,1,'192.0.2.24','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1027,'seed.qa','Actividad sintética de prueba','192.0.2.24','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1027,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa19@ascend.test',1028,1,'192.0.2.25','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1028,'seed.qa','Actividad sintética de prueba','192.0.2.25','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1028,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa20@ascend.test',1029,1,'192.0.2.26','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1029,'seed.qa','Actividad sintética de prueba','192.0.2.26','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1029,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa21@ascend.test',1030,1,'192.0.2.27','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1030,'seed.qa','Actividad sintética de prueba','192.0.2.27','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1030,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa22@ascend.test',1031,1,'192.0.2.28','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1031,'seed.qa','Actividad sintética de prueba','192.0.2.28','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1031,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa23@ascend.test',1032,1,'192.0.2.29','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1032,'seed.qa','Actividad sintética de prueba','192.0.2.29','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1032,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa24@ascend.test',1033,1,'192.0.2.30','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1033,'seed.qa','Actividad sintética de prueba','192.0.2.30','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1033,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa25@ascend.test',1034,1,'192.0.2.31','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1034,'seed.qa','Actividad sintética de prueba','192.0.2.31','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1034,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa26@ascend.test',1035,1,'192.0.2.32','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1035,'seed.qa','Actividad sintética de prueba','192.0.2.32','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1035,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa27@ascend.test',1036,1,'192.0.2.33','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1036,'seed.qa','Actividad sintética de prueba','192.0.2.33','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1036,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa28@ascend.test',1037,1,'192.0.2.34','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1037,'seed.qa','Actividad sintética de prueba','192.0.2.34','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1037,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa29@ascend.test',1038,1,'192.0.2.35','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1038,'seed.qa','Actividad sintética de prueba','192.0.2.35','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1038,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa30@ascend.test',1039,1,'192.0.2.36','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1039,'seed.qa','Actividad sintética de prueba','192.0.2.36','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1039,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa31@ascend.test',1040,1,'192.0.2.37','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1040,'seed.qa','Actividad sintética de prueba','192.0.2.37','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1040,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa32@ascend.test',1041,1,'192.0.2.38','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1041,'seed.qa','Actividad sintética de prueba','192.0.2.38','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1041,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa33@ascend.test',1042,1,'192.0.2.39','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1042,'seed.qa','Actividad sintética de prueba','192.0.2.39','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1042,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa34@ascend.test',1043,1,'192.0.2.40','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1043,'seed.qa','Actividad sintética de prueba','192.0.2.40','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1043,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa35@ascend.test',1044,1,'192.0.2.41','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1044,'seed.qa','Actividad sintética de prueba','192.0.2.41','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1044,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa36@ascend.test',1045,1,'192.0.2.42','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1045,'seed.qa','Actividad sintética de prueba','192.0.2.42','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1045,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa37@ascend.test',1046,1,'192.0.2.43','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1046,'seed.qa','Actividad sintética de prueba','192.0.2.43','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1046,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa38@ascend.test',1047,1,'192.0.2.44','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1047,'seed.qa','Actividad sintética de prueba','192.0.2.44','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1047,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa39@ascend.test',1048,1,'192.0.2.45','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1048,'seed.qa','Actividad sintética de prueba','192.0.2.45','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1048,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa40@ascend.test',1049,1,'192.0.2.46','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1049,'seed.qa','Actividad sintética de prueba','192.0.2.46','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1049,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa41@ascend.test',1050,1,'192.0.2.47','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1050,'seed.qa','Actividad sintética de prueba','192.0.2.47','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1050,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa42@ascend.test',1051,1,'192.0.2.48','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1051,'seed.qa','Actividad sintética de prueba','192.0.2.48','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1051,'$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee');
INSERT IGNORE INTO newsletter_subscriptores (email,activo) VALUES ('newsletter.qa001@ascend.test',1), ('newsletter.qa002@ascend.test',1), ('newsletter.qa003@ascend.test',1), ('newsletter.qa004@ascend.test',1), ('newsletter.qa005@ascend.test',1), ('newsletter.qa006@ascend.test',1), ('newsletter.qa007@ascend.test',1), ('newsletter.qa008@ascend.test',1), ('newsletter.qa009@ascend.test',1), ('newsletter.qa010@ascend.test',1), ('newsletter.qa011@ascend.test',1), ('newsletter.qa012@ascend.test',1), ('newsletter.qa013@ascend.test',1), ('newsletter.qa014@ascend.test',1), ('newsletter.qa015@ascend.test',1), ('newsletter.qa016@ascend.test',1), ('newsletter.qa017@ascend.test',1), ('newsletter.qa018@ascend.test',1), ('newsletter.qa019@ascend.test',1), ('newsletter.qa020@ascend.test',1), ('newsletter.qa021@ascend.test',1), ('newsletter.qa022@ascend.test',1), ('newsletter.qa023@ascend.test',1), ('newsletter.qa024@ascend.test',1), ('newsletter.qa025@ascend.test',1), ('newsletter.qa026@ascend.test',1), ('newsletter.qa027@ascend.test',1), ('newsletter.qa028@ascend.test',1), ('newsletter.qa029@ascend.test',1), ('newsletter.qa030@ascend.test',1);
INSERT IGNORE INTO sesiones_activas (usuario_id,token_sesion,ip_origen,user_agent,expira_en,activa) VALUES (1000,'qa-session-1000-token','192.0.2.1','QA Browser/1.0',CURRENT_TIMESTAMP + INTERVAL 7 DAY,1);
INSERT IGNORE INTO sesiones_activas (usuario_id,token_sesion,ip_origen,user_agent,expira_en,activa) VALUES (1001,'qa-session-1001-token','192.0.2.2','QA Browser/1.0',CURRENT_TIMESTAMP + INTERVAL 7 DAY,1);
INSERT IGNORE INTO sesiones_activas (usuario_id,token_sesion,ip_origen,user_agent,expira_en,activa) VALUES (1002,'qa-session-1002-token','192.0.2.3','QA Browser/1.0',CURRENT_TIMESTAMP + INTERVAL 7 DAY,1);
INSERT IGNORE INTO sesiones_activas (usuario_id,token_sesion,ip_origen,user_agent,expira_en,activa) VALUES (1003,'qa-session-1003-token','192.0.2.4','QA Browser/1.0',CURRENT_TIMESTAMP + INTERVAL 7 DAY,1);
INSERT IGNORE INTO sesiones_activas (usuario_id,token_sesion,ip_origen,user_agent,expira_en,activa) VALUES (1004,'qa-session-1004-token','192.0.2.5','QA Browser/1.0',CURRENT_TIMESTAMP + INTERVAL 7 DAY,1);
INSERT IGNORE INTO sesiones_activas (usuario_id,token_sesion,ip_origen,user_agent,expira_en,activa) VALUES (1005,'qa-session-1005-token','192.0.2.6','QA Browser/1.0',CURRENT_TIMESTAMP + INTERVAL 7 DAY,1);
INSERT IGNORE INTO sesiones_activas (usuario_id,token_sesion,ip_origen,user_agent,expira_en,activa) VALUES (1010,'qa-session-1010-token','192.0.2.11','QA Browser/1.0',CURRENT_TIMESTAMP + INTERVAL 7 DAY,1);
INSERT IGNORE INTO sesiones_activas (usuario_id,token_sesion,ip_origen,user_agent,expira_en,activa) VALUES (1011,'qa-session-1011-token','192.0.2.12','QA Browser/1.0',CURRENT_TIMESTAMP + INTERVAL 7 DAY,1);
INSERT IGNORE INTO sesiones_activas (usuario_id,token_sesion,ip_origen,user_agent,expira_en,activa) VALUES (1012,'qa-session-1012-token','192.0.2.13','QA Browser/1.0',CURRENT_TIMESTAMP + INTERVAL 7 DAY,1);
INSERT IGNORE INTO sesiones_activas (usuario_id,token_sesion,ip_origen,user_agent,expira_en,activa) VALUES (1013,'qa-session-1013-token','192.0.2.14','QA Browser/1.0',CURRENT_TIMESTAMP + INTERVAL 7 DAY,1);
INSERT IGNORE INTO sesiones_activas (usuario_id,token_sesion,ip_origen,user_agent,expira_en,activa) VALUES (1014,'qa-session-1014-token','192.0.2.15','QA Browser/1.0',CURRENT_TIMESTAMP + INTERVAL 7 DAY,1);
INSERT IGNORE INTO sesiones_activas (usuario_id,token_sesion,ip_origen,user_agent,expira_en,activa) VALUES (1015,'qa-session-1015-token','192.0.2.16','QA Browser/1.0',CURRENT_TIMESTAMP + INTERVAL 7 DAY,1);
INSERT IGNORE INTO sesiones_activas (usuario_id,token_sesion,ip_origen,user_agent,expira_en,activa) VALUES (1016,'qa-session-1016-token','192.0.2.17','QA Browser/1.0',CURRENT_TIMESTAMP + INTERVAL 7 DAY,1);
INSERT IGNORE INTO sesiones_activas (usuario_id,token_sesion,ip_origen,user_agent,expira_en,activa) VALUES (1017,'qa-session-1017-token','192.0.2.18','QA Browser/1.0',CURRENT_TIMESTAMP + INTERVAL 7 DAY,1);
INSERT IGNORE INTO sesiones_activas (usuario_id,token_sesion,ip_origen,user_agent,expira_en,activa) VALUES (1018,'qa-session-1018-token','192.0.2.19','QA Browser/1.0',CURRENT_TIMESTAMP + INTERVAL 7 DAY,1);
INSERT IGNORE INTO sesiones_activas (usuario_id,token_sesion,ip_origen,user_agent,expira_en,activa) VALUES (1019,'qa-session-1019-token','192.0.2.20','QA Browser/1.0',CURRENT_TIMESTAMP + INTERVAL 7 DAY,1);
INSERT IGNORE INTO tokens_verificacion_email (usuario_id,token,expira_en,usado) VALUES (1000,'qa-verify-token-1000',CURRENT_TIMESTAMP + INTERVAL 1 DAY,1);
INSERT IGNORE INTO tokens_recuperacion (usuario_id,token,expira_en,usado) VALUES (1000,'qa-reset-token-1000',CURRENT_TIMESTAMP + INTERVAL 1 HOUR,1);
INSERT IGNORE INTO tokens_verificacion_email (usuario_id,token,expira_en,usado) VALUES (1001,'qa-verify-token-1001',CURRENT_TIMESTAMP + INTERVAL 1 DAY,1);
INSERT IGNORE INTO tokens_recuperacion (usuario_id,token,expira_en,usado) VALUES (1001,'qa-reset-token-1001',CURRENT_TIMESTAMP + INTERVAL 1 HOUR,1);
INSERT IGNORE INTO tokens_verificacion_email (usuario_id,token,expira_en,usado) VALUES (1002,'qa-verify-token-1002',CURRENT_TIMESTAMP + INTERVAL 1 DAY,1);
INSERT IGNORE INTO tokens_recuperacion (usuario_id,token,expira_en,usado) VALUES (1002,'qa-reset-token-1002',CURRENT_TIMESTAMP + INTERVAL 1 HOUR,1);
INSERT IGNORE INTO tokens_verificacion_email (usuario_id,token,expira_en,usado) VALUES (1003,'qa-verify-token-1003',CURRENT_TIMESTAMP + INTERVAL 1 DAY,1);
INSERT IGNORE INTO tokens_recuperacion (usuario_id,token,expira_en,usado) VALUES (1003,'qa-reset-token-1003',CURRENT_TIMESTAMP + INTERVAL 1 HOUR,1);
INSERT IGNORE INTO tokens_verificacion_email (usuario_id,token,expira_en,usado) VALUES (1004,'qa-verify-token-1004',CURRENT_TIMESTAMP + INTERVAL 1 DAY,1);
INSERT IGNORE INTO tokens_recuperacion (usuario_id,token,expira_en,usado) VALUES (1004,'qa-reset-token-1004',CURRENT_TIMESTAMP + INTERVAL 1 HOUR,1);
INSERT IGNORE INTO tokens_verificacion_email (usuario_id,token,expira_en,usado) VALUES (1005,'qa-verify-token-1005',CURRENT_TIMESTAMP + INTERVAL 1 DAY,1);
INSERT IGNORE INTO tokens_recuperacion (usuario_id,token,expira_en,usado) VALUES (1005,'qa-reset-token-1005',CURRENT_TIMESTAMP + INTERVAL 1 HOUR,1);
INSERT IGNORE INTO tokens_verificacion_email (usuario_id,token,expira_en,usado) VALUES (1010,'qa-verify-token-1010',CURRENT_TIMESTAMP + INTERVAL 1 DAY,1);
INSERT IGNORE INTO tokens_recuperacion (usuario_id,token,expira_en,usado) VALUES (1010,'qa-reset-token-1010',CURRENT_TIMESTAMP + INTERVAL 1 HOUR,1);
INSERT IGNORE INTO tokens_verificacion_email (usuario_id,token,expira_en,usado) VALUES (1011,'qa-verify-token-1011',CURRENT_TIMESTAMP + INTERVAL 1 DAY,1);
INSERT IGNORE INTO tokens_recuperacion (usuario_id,token,expira_en,usado) VALUES (1011,'qa-reset-token-1011',CURRENT_TIMESTAMP + INTERVAL 1 HOUR,1);
INSERT IGNORE INTO tokens_verificacion_email (usuario_id,token,expira_en,usado) VALUES (1012,'qa-verify-token-1012',CURRENT_TIMESTAMP + INTERVAL 1 DAY,1);
INSERT IGNORE INTO tokens_recuperacion (usuario_id,token,expira_en,usado) VALUES (1012,'qa-reset-token-1012',CURRENT_TIMESTAMP + INTERVAL 1 HOUR,1);
INSERT IGNORE INTO tokens_verificacion_email (usuario_id,token,expira_en,usado) VALUES (1013,'qa-verify-token-1013',CURRENT_TIMESTAMP + INTERVAL 1 DAY,1);
INSERT IGNORE INTO tokens_recuperacion (usuario_id,token,expira_en,usado) VALUES (1013,'qa-reset-token-1013',CURRENT_TIMESTAMP + INTERVAL 1 HOUR,1);
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1201,'UPDATE',1001,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','finalizado'),'192.0.2.1','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1202,'DELETE',1002,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','cancelado'),'192.0.2.2','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1203,'INSERT',1003,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','en_curso'),'192.0.2.3','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1204,'UPDATE',1004,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','finalizado'),'192.0.2.4','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1205,'DELETE',1005,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','cancelado'),'192.0.2.5','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1206,'INSERT',1010,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','en_curso'),'192.0.2.6','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1207,'UPDATE',1011,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','finalizado'),'192.0.2.7','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1208,'DELETE',1012,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','cancelado'),'192.0.2.8','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1209,'INSERT',1013,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','en_curso'),'192.0.2.9','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1210,'UPDATE',1014,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','finalizado'),'192.0.2.10','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1211,'DELETE',1015,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','cancelado'),'192.0.2.11','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1200,'INSERT',1016,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','en_curso'),'192.0.2.12','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1201,'UPDATE',1017,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','finalizado'),'192.0.2.13','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1202,'DELETE',1018,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','cancelado'),'192.0.2.14','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1203,'INSERT',1019,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','en_curso'),'192.0.2.15','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1204,'UPDATE',1020,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','finalizado'),'192.0.2.16','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1205,'DELETE',1021,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','cancelado'),'192.0.2.17','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1206,'INSERT',1022,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','en_curso'),'192.0.2.18','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1207,'UPDATE',1023,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','finalizado'),'192.0.2.19','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1208,'DELETE',1024,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','cancelado'),'192.0.2.20','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1209,'INSERT',1025,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','en_curso'),'192.0.2.21','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1210,'UPDATE',1026,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','finalizado'),'192.0.2.22','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1211,'DELETE',1027,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','cancelado'),'192.0.2.23','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1200,'INSERT',1028,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','en_curso'),'192.0.2.24','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1201,'UPDATE',1029,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','finalizado'),'192.0.2.25','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1202,'DELETE',1030,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','cancelado'),'192.0.2.26','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1203,'INSERT',1031,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','en_curso'),'192.0.2.27','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1204,'UPDATE',1032,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','finalizado'),'192.0.2.28','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1205,'DELETE',1033,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','cancelado'),'192.0.2.29','QA Browser/1.0');
INSERT IGNORE INTO auditoria_cambios (tabla_afectada,registro_id,accion,usuario_id,datos_viejos,datos_nuevos,ip_origen,user_agent)
VALUES ('torneos',1206,'INSERT',1034,JSON_OBJECT('estado','borrador'),JSON_OBJECT('estado','en_curso'),'192.0.2.30','QA Browser/1.0');
COMMIT;

-- =============================================================================
-- AMPLIACIÓN DE SEMILLA QA (antes 006_seed_qa_ampliado.sql)
-- =============================================================================
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

-- =============================================================================
-- TRIGGERS DE INTEGRIDAD DE DISCIPLINAS (antes 007_restricciones_disciplinas.sql)
-- =============================================================================
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

-- =============================================================================
-- NORMALIZACIÓN Y SEMILLA CONSISTENTE (antes 008_semilla_consistente.sql)
-- =============================================================================
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
SET contrasena_hash = '$2y$10$KVQwgLaAVeM35fdAzeTskuGbGntBtFeRusUnYNwDW53p1Q.7/w8Ee'
WHERE id IN (1,2,3) OR id BETWEEN 1000 AND 1051;

COMMIT;

-- Fin de 008_semilla_consistente.sql

-- =============================================================================
-- EQUIPOS DE FÚTBOL 5 CON ESCUDOS (antes 009_equipos_futbol_escudos.sql)
-- =============================================================================
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

-- =============================================================================
-- PLANTILLAS Y CAPITANES DE FÚTBOL 5 (antes 010_plantillas_equipos_futbol.sql)
-- =============================================================================
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
