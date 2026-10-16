-- ASCEND: semillas relacionales para QA / desarrollo
-- Datos sintéticos. No usar en producción.
-- Usa IDs reservados desde 1000 y corre en orden respetando las FK.
-- Contraseña de prueba compartida: hash bcrypt ya usado por el proyecto.
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
(1000,'organizador.qa1@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Organizador QA 1', '+5989901000',2,1,1),
(1001,'organizador.qa2@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Organizador QA 2', '+5989901001',2,1,1),
(1002,'organizador.qa3@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Organizador QA 3', '+5989901002',2,1,1),
(1003,'organizador.qa4@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Organizador QA 4', '+5989901003',2,1,1),
(1004,'organizador.qa5@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Organizador QA 5', '+5989901004',2,1,1),
(1005,'organizador.qa6@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Organizador QA 6', '+5989901005',2,1,1),
(1010,'jugador.qa01@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 01', '+5989901010',3,1,1),
(1011,'jugador.qa02@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 02', '+5989901011',3,1,1),
(1012,'jugador.qa03@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 03', '+5989901012',3,1,1),
(1013,'jugador.qa04@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 04', '+5989901013',3,1,1),
(1014,'jugador.qa05@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 05', '+5989901014',3,1,1),
(1015,'jugador.qa06@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 06', '+5989901015',3,1,1),
(1016,'jugador.qa07@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 07', '+5989901016',3,1,1),
(1017,'jugador.qa08@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 08', '+5989901017',3,1,1),
(1018,'jugador.qa09@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 09', '+5989901018',3,1,1),
(1019,'jugador.qa10@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 10', '+5989901019',3,1,1),
(1020,'jugador.qa11@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 11', '+5989901020',3,1,1),
(1021,'jugador.qa12@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 12', '+5989901021',3,1,1),
(1022,'jugador.qa13@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 13', '+5989901022',3,1,1),
(1023,'jugador.qa14@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 14', '+5989901023',3,1,1),
(1024,'jugador.qa15@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 15', '+5989901024',3,1,1),
(1025,'jugador.qa16@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 16', '+5989901025',3,1,1),
(1026,'jugador.qa17@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 17', '+5989901026',3,1,1),
(1027,'jugador.qa18@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 18', '+5989901027',3,1,1),
(1028,'jugador.qa19@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 19', '+5989901028',3,1,1),
(1029,'jugador.qa20@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 20', '+5989901029',3,1,1),
(1030,'jugador.qa21@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 21', '+5989901030',3,1,1),
(1031,'jugador.qa22@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 22', '+5989901031',3,1,1),
(1032,'jugador.qa23@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 23', '+5989901032',3,1,1),
(1033,'jugador.qa24@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 24', '+5989901033',3,1,1),
(1034,'jugador.qa25@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 25', '+5989901034',3,1,1),
(1035,'jugador.qa26@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 26', '+5989901035',3,1,1),
(1036,'jugador.qa27@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 27', '+5989901036',3,1,1),
(1037,'jugador.qa28@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 28', '+5989901037',3,1,1),
(1038,'jugador.qa29@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 29', '+5989901038',3,1,1),
(1039,'jugador.qa30@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 30', '+5989901039',3,1,1),
(1040,'jugador.qa31@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 31', '+5989901040',3,1,1),
(1041,'jugador.qa32@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 32', '+5989901041',3,1,1),
(1042,'jugador.qa33@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 33', '+5989901042',3,1,1),
(1043,'jugador.qa34@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 34', '+5989901043',3,1,1),
(1044,'jugador.qa35@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 35', '+5989901044',3,1,1),
(1045,'jugador.qa36@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 36', '+5989901045',3,1,1),
(1046,'jugador.qa37@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 37', '+5989901046',3,1,1),
(1047,'jugador.qa38@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 38', '+5989901047',3,1,1),
(1048,'jugador.qa39@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 39', '+5989901048',3,1,1),
(1049,'jugador.qa40@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 40', '+5989901049',3,1,1),
(1050,'jugador.qa41@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 41', '+5989901050',3,1,1),
(1051,'jugador.qa42@ascend.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jugador QA 42', '+5989901051',3,1,1);

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
VALUES (1201,'Copa QA 02',2,'Torneo sintético para pruebas integrales','eliminacion_directa','en_curso',16,2,'2026-01-01','2026-12-01','2026-02-02','2026-02-06','Reglas de prueba; resultados ficticios','Trofeo QA','Arena de pruebas','Montevideo','Uruguay',1001,2,2,3,0,0,'puntos',3);
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
VALUES (1204,'Copa QA 05',5,'Torneo sintético para pruebas integrales','eliminacion_directa','finalizado',16,2,'2026-01-01','2026-12-01','2026-05-05','2026-05-09','Reglas de prueba; resultados ficticios','Trofeo QA','Arena de pruebas','Montevideo','Uruguay',1004,1,2,2,0.5,0,'booleano',3);
INSERT IGNORE INTO torneo_config (torneo_id,clave,valor) VALUES (1204,'semilla_qa','true'),(1204,'reglas_version','1');
INSERT IGNORE INTO torneo_actividades (torneo_id,titulo,tipo,fecha,hora) VALUES (1204,'Reunión técnica QA','reunion','2026-05-05','18:00:00'),(1204,'Jornada de competencia','competencia','2026-05-05','20:00:00'),(1204,'Premiación','premiacion','2026-05-09','21:00:00');
INSERT IGNORE INTO torneos
(id,nombre,juego_id,descripcion,formato,estado,cupo_max_equipos,cupo_min_equipos,fecha_inicio_inscripcion,fecha_limite_inscripcion,fecha_inicio,fecha_fin,reglas,premios,ubicacion,localidad,comunidad,organizador_id,modalidad_id,sistema_puntuacion_id,puntos_victoria,puntos_empate,puntos_derrota,tipo_resultado,mejor_de)
VALUES (1205,'Copa QA 06',1,'Torneo sintético para pruebas integrales','suizo','en_curso',16,2,'2026-01-01','2026-12-01','2026-06-06','2026-06-10','Reglas de prueba; resultados ficticios','Trofeo QA','Arena de pruebas','Montevideo','Uruguay',1005,2,3,3,1,0,'rondas',5);
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
VALUES (1209,'Copa QA 10',5,'Torneo sintético para pruebas integrales','liga','en_curso',16,2,'2026-01-01','2026-12-01','2026-01-10','2026-01-14','Reglas de prueba; resultados ficticios','Trofeo QA','Arena de pruebas','Montevideo','Uruguay',1003,1,1,2,0.5,0,'booleano',1);
INSERT IGNORE INTO torneo_config (torneo_id,clave,valor) VALUES (1209,'semilla_qa','true'),(1209,'reglas_version','1');
INSERT IGNORE INTO torneo_actividades (torneo_id,titulo,tipo,fecha,hora) VALUES (1209,'Reunión técnica QA','reunion','2026-01-10','18:00:00'),(1209,'Jornada de competencia','competencia','2026-01-10','20:00:00'),(1209,'Premiación','premiacion','2026-01-14','21:00:00');
INSERT IGNORE INTO torneos
(id,nombre,juego_id,descripcion,formato,estado,cupo_max_equipos,cupo_min_equipos,fecha_inicio_inscripcion,fecha_limite_inscripcion,fecha_inicio,fecha_fin,reglas,premios,ubicacion,localidad,comunidad,organizador_id,modalidad_id,sistema_puntuacion_id,puntos_victoria,puntos_empate,puntos_derrota,tipo_resultado,mejor_de)
VALUES (1210,'Copa QA 11',1,'Torneo sintético para pruebas integrales','eliminacion_directa','inscripciones_abiertas',16,2,'2026-01-01','2026-12-01','2026-02-11','2026-02-15','Reglas de prueba; resultados ficticios','Trofeo QA','Arena de pruebas','Montevideo','Uruguay',1004,2,2,3,1,0,'rondas',3);
INSERT IGNORE INTO torneo_config (torneo_id,clave,valor) VALUES (1210,'semilla_qa','true'),(1210,'reglas_version','1');
INSERT IGNORE INTO torneo_actividades (torneo_id,titulo,tipo,fecha,hora) VALUES (1210,'Reunión técnica QA','reunion','2026-02-11','18:00:00'),(1210,'Jornada de competencia','competencia','2026-02-11','20:00:00'),(1210,'Premiación','premiacion','2026-02-15','21:00:00');
INSERT IGNORE INTO torneos
(id,nombre,juego_id,descripcion,formato,estado,cupo_max_equipos,cupo_min_equipos,fecha_inicio_inscripcion,fecha_limite_inscripcion,fecha_inicio,fecha_fin,reglas,premios,ubicacion,localidad,comunidad,organizador_id,modalidad_id,sistema_puntuacion_id,puntos_victoria,puntos_empate,puntos_derrota,tipo_resultado,mejor_de)
VALUES (1211,'Copa QA 12',2,'Torneo sintético para pruebas integrales','suizo','borrador',16,2,'2026-01-01','2026-12-01','2026-03-12','2026-03-16','Reglas de prueba; resultados ficticios','Trofeo QA','Arena de pruebas','Montevideo','Uruguay',1005,2,3,3,0,0,'puntos',5);
INSERT IGNORE INTO torneo_config (torneo_id,clave,valor) VALUES (1211,'semilla_qa','true'),(1211,'reglas_version','1');
INSERT IGNORE INTO torneo_actividades (torneo_id,titulo,tipo,fecha,hora) VALUES (1211,'Reunión técnica QA','reunion','2026-03-12','18:00:00'),(1211,'Jornada de competencia','competencia','2026-03-12','20:00:00'),(1211,'Premiación','premiacion','2026-03-16','21:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1300,1200,'equipo',1100,'Escuadra QA 01','confirmado',1000,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1301,1200,'equipo',1105,'Escuadra QA 06','confirmado',1000,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1302,1200,'equipo',1110,'Escuadra QA 11','confirmado',1000,'2026-01-02 12:00:00');
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1200,1300,4,3,0,0,12,5,6,0);
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1200,1301,4,2,1,1,10,6,3,0);
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1200,1302,4,1,0,2,8,7,0,0);
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1400,1200,1,1300,1302,'2026-01-02 20:00:00','Cancha 1',3,1,'finalizado',1300,1000);
INSERT IGNORE INTO resultados_detalle (encuentro_id,tipo_dato,valor) VALUES (1400,'mvp','Jugador QA 1010'),(1400,'duracion_minutos','45'),(1400,'observacion','Resultado ficticio para QA');
INSERT IGNORE INTO torneo_cambio_estado (torneo_id,estado_anterior,estado_nuevo,motivo,usuario_id) VALUES (1200,'borrador','inscripciones_abiertas','Apertura inicial QA',1000),(1200,'inscripciones_abiertas','finalizado','Transición sintética QA',1000);
INSERT IGNORE INTO torneo_comentarios (torneo_id,usuario_id,nombre_autor,email_autor,comentario,aprobado) VALUES (1200,1034,'Jugador QA','jugador.qa25@ascend.test','Comentario de prueba del torneo 1200',1);
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1303,1201,'equipo',1101,'Escuadra QA 02','confirmado',1001,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1304,1201,'equipo',1106,'Escuadra QA 07','confirmado',1001,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1305,1201,'equipo',1111,'Escuadra QA 12','confirmado',1001,'2026-01-02 12:00:00');
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1201,1303,4,3,0,0,12,5,6,0);
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1201,1304,4,2,1,1,10,6,3,0);
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1201,1305,4,1,0,2,8,7,0,0);
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1404,1201,1,1303,1305,'2026-02-02 20:00:00','Cancha 1',3,1,'finalizado',1303,1001);
INSERT IGNORE INTO resultados_detalle (encuentro_id,tipo_dato,valor) VALUES (1404,'mvp','Jugador QA 1010'),(1404,'duracion_minutos','45'),(1404,'observacion','Resultado ficticio para QA');
INSERT IGNORE INTO torneo_cambio_estado (torneo_id,estado_anterior,estado_nuevo,motivo,usuario_id) VALUES (1201,'borrador','inscripciones_abiertas','Apertura inicial QA',1001),(1201,'inscripciones_abiertas','en_curso','Transición sintética QA',1001);
INSERT IGNORE INTO torneo_comentarios (torneo_id,usuario_id,nombre_autor,email_autor,comentario,aprobado) VALUES (1201,1035,'Jugador QA','jugador.qa26@ascend.test','Comentario de prueba del torneo 1201',1);
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1306,1202,'equipo',1102,'Escuadra QA 03','confirmado',1002,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1307,1202,'equipo',1107,'Escuadra QA 08','confirmado',1002,'2026-01-02 12:00:00');
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1202,1306,4,3,0,0,12,5,3,0);
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1202,1307,4,2,1,1,10,6,0,0);
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1408,1202,1,1306,1307,'2026-03-02 20:00:00','Cancha 1',NULL,NULL,'programado',NULL,1002);
INSERT IGNORE INTO torneo_suizo_parejas (torneo_id,participante_a_id,participante_b_id,ronda,ya_se_enfrentaron,fecha_encuentro) VALUES (1202,1306,1307,1,1,'2026-03-01 20:00:00');
INSERT IGNORE INTO torneo_cambio_estado (torneo_id,estado_anterior,estado_nuevo,motivo,usuario_id) VALUES (1202,'borrador','inscripciones_abiertas','Apertura inicial QA',1002),(1202,'inscripciones_abiertas','inscripciones_abiertas','Transición sintética QA',1002);
INSERT IGNORE INTO torneo_comentarios (torneo_id,usuario_id,nombre_autor,email_autor,comentario,aprobado) VALUES (1202,1036,'Jugador QA','jugador.qa27@ascend.test','Comentario de prueba del torneo 1202',1);
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1308,1203,'equipo',1103,'Escuadra QA 04','confirmado',1003,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1309,1203,'equipo',1108,'Escuadra QA 09','confirmado',1003,'2026-01-02 12:00:00');
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1203,1308,4,3,0,0,12,5,3,0);
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1203,1309,4,2,1,1,10,6,0,0);
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1412,1203,1,1308,1309,'2026-04-02 20:00:00','Cancha 1',NULL,NULL,'programado',NULL,1003);
INSERT IGNORE INTO torneo_cambio_estado (torneo_id,estado_anterior,estado_nuevo,motivo,usuario_id) VALUES (1203,'borrador','inscripciones_abiertas','Apertura inicial QA',1003),(1203,'inscripciones_abiertas','borrador','Transición sintética QA',1003);
INSERT IGNORE INTO torneo_comentarios (torneo_id,usuario_id,nombre_autor,email_autor,comentario,aprobado) VALUES (1203,1037,'Jugador QA','jugador.qa28@ascend.test','Comentario de prueba del torneo 1203',1);
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1310,1204,'equipo',1104,'Escuadra QA 05','confirmado',1004,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1311,1204,'equipo',1109,'Escuadra QA 10','confirmado',1004,'2026-01-02 12:00:00');
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1204,1310,4,3,0,0,12,5,3,0);
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1204,1311,4,2,1,1,10,6,0,0);
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1416,1204,1,1310,1311,'2026-05-02 20:00:00','Cancha 1',3,1,'finalizado',1310,1004);
INSERT IGNORE INTO resultados_detalle (encuentro_id,tipo_dato,valor) VALUES (1416,'mvp','Jugador QA 1010'),(1416,'duracion_minutos','45'),(1416,'observacion','Resultado ficticio para QA');
INSERT IGNORE INTO torneo_cambio_estado (torneo_id,estado_anterior,estado_nuevo,motivo,usuario_id) VALUES (1204,'borrador','inscripciones_abiertas','Apertura inicial QA',1004),(1204,'inscripciones_abiertas','finalizado','Transición sintética QA',1004);
INSERT IGNORE INTO torneo_comentarios (torneo_id,usuario_id,nombre_autor,email_autor,comentario,aprobado) VALUES (1204,1038,'Jugador QA','jugador.qa29@ascend.test','Comentario de prueba del torneo 1204',1);
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1312,1205,'equipo',1100,'Escuadra QA 01','confirmado',1005,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1313,1205,'equipo',1105,'Escuadra QA 06','confirmado',1005,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1314,1205,'equipo',1110,'Escuadra QA 11','confirmado',1005,'2026-01-02 12:00:00');
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1205,1312,4,3,0,0,12,5,6,0);
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1205,1313,4,2,1,1,10,6,3,0);
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1205,1314,4,1,0,2,8,7,0,0);
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1420,1205,1,1312,1314,'2026-06-02 20:00:00','Cancha 1',3,1,'finalizado',1312,1005);
INSERT IGNORE INTO resultados_detalle (encuentro_id,tipo_dato,valor) VALUES (1420,'mvp','Jugador QA 1010'),(1420,'duracion_minutos','45'),(1420,'observacion','Resultado ficticio para QA');
INSERT IGNORE INTO torneo_suizo_parejas (torneo_id,participante_a_id,participante_b_id,ronda,ya_se_enfrentaron,fecha_encuentro) VALUES (1205,1312,1313,1,1,'2026-03-01 20:00:00');
INSERT IGNORE INTO torneo_cambio_estado (torneo_id,estado_anterior,estado_nuevo,motivo,usuario_id) VALUES (1205,'borrador','inscripciones_abiertas','Apertura inicial QA',1005),(1205,'inscripciones_abiertas','en_curso','Transición sintética QA',1005);
INSERT IGNORE INTO torneo_comentarios (torneo_id,usuario_id,nombre_autor,email_autor,comentario,aprobado) VALUES (1205,1039,'Jugador QA','jugador.qa30@ascend.test','Comentario de prueba del torneo 1205',1);
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1315,1206,'equipo',1101,'Escuadra QA 02','confirmado',1000,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1316,1206,'equipo',1106,'Escuadra QA 07','confirmado',1000,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1317,1206,'equipo',1111,'Escuadra QA 12','confirmado',1000,'2026-01-02 12:00:00');
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1206,1315,4,3,0,0,12,5,6,0);
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1206,1316,4,2,1,1,10,6,3,0);
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1206,1317,4,1,0,2,8,7,0,0);
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1424,1206,1,1315,1317,'2026-07-02 20:00:00','Cancha 1',NULL,NULL,'programado',NULL,1000);
INSERT IGNORE INTO torneo_cambio_estado (torneo_id,estado_anterior,estado_nuevo,motivo,usuario_id) VALUES (1206,'borrador','inscripciones_abiertas','Apertura inicial QA',1000),(1206,'inscripciones_abiertas','inscripciones_abiertas','Transición sintética QA',1000);
INSERT IGNORE INTO torneo_comentarios (torneo_id,usuario_id,nombre_autor,email_autor,comentario,aprobado) VALUES (1206,1040,'Jugador QA','jugador.qa31@ascend.test','Comentario de prueba del torneo 1206',1);
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1318,1207,'equipo',1102,'Escuadra QA 03','confirmado',1001,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1319,1207,'equipo',1107,'Escuadra QA 08','confirmado',1001,'2026-01-02 12:00:00');
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1207,1318,4,3,0,0,12,5,3,0);
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1207,1319,4,2,1,1,10,6,0,0);
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1428,1207,1,1318,1319,'2026-08-02 20:00:00','Cancha 1',NULL,NULL,'programado',NULL,1001);
INSERT IGNORE INTO torneo_cambio_estado (torneo_id,estado_anterior,estado_nuevo,motivo,usuario_id) VALUES (1207,'borrador','inscripciones_abiertas','Apertura inicial QA',1001),(1207,'inscripciones_abiertas','borrador','Transición sintética QA',1001);
INSERT IGNORE INTO torneo_comentarios (torneo_id,usuario_id,nombre_autor,email_autor,comentario,aprobado) VALUES (1207,1041,'Jugador QA','jugador.qa32@ascend.test','Comentario de prueba del torneo 1207',1);
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1320,1208,'equipo',1103,'Escuadra QA 04','confirmado',1002,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1321,1208,'equipo',1108,'Escuadra QA 09','confirmado',1002,'2026-01-02 12:00:00');
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1208,1320,4,3,0,0,12,5,3,0);
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1208,1321,4,2,1,1,10,6,0,0);
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1432,1208,1,1320,1321,'2026-09-02 20:00:00','Cancha 1',3,1,'finalizado',1320,1002);
INSERT IGNORE INTO resultados_detalle (encuentro_id,tipo_dato,valor) VALUES (1432,'mvp','Jugador QA 1010'),(1432,'duracion_minutos','45'),(1432,'observacion','Resultado ficticio para QA');
INSERT IGNORE INTO torneo_suizo_parejas (torneo_id,participante_a_id,participante_b_id,ronda,ya_se_enfrentaron,fecha_encuentro) VALUES (1208,1320,1321,1,1,'2026-03-01 20:00:00');
INSERT IGNORE INTO torneo_cambio_estado (torneo_id,estado_anterior,estado_nuevo,motivo,usuario_id) VALUES (1208,'borrador','inscripciones_abiertas','Apertura inicial QA',1002),(1208,'inscripciones_abiertas','finalizado','Transición sintética QA',1002);
INSERT IGNORE INTO torneo_comentarios (torneo_id,usuario_id,nombre_autor,email_autor,comentario,aprobado) VALUES (1208,1042,'Jugador QA','jugador.qa33@ascend.test','Comentario de prueba del torneo 1208',1);
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1322,1209,'equipo',1104,'Escuadra QA 05','confirmado',1003,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1323,1209,'equipo',1109,'Escuadra QA 10','confirmado',1003,'2026-01-02 12:00:00');
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1209,1322,4,3,0,0,12,5,3,0);
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1209,1323,4,2,1,1,10,6,0,0);
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1436,1209,1,1322,1323,'2026-01-02 20:00:00','Cancha 1',3,1,'finalizado',1322,1003);
INSERT IGNORE INTO resultados_detalle (encuentro_id,tipo_dato,valor) VALUES (1436,'mvp','Jugador QA 1010'),(1436,'duracion_minutos','45'),(1436,'observacion','Resultado ficticio para QA');
INSERT IGNORE INTO torneo_cambio_estado (torneo_id,estado_anterior,estado_nuevo,motivo,usuario_id) VALUES (1209,'borrador','inscripciones_abiertas','Apertura inicial QA',1003),(1209,'inscripciones_abiertas','en_curso','Transición sintética QA',1003);
INSERT IGNORE INTO torneo_comentarios (torneo_id,usuario_id,nombre_autor,email_autor,comentario,aprobado) VALUES (1209,1043,'Jugador QA','jugador.qa34@ascend.test','Comentario de prueba del torneo 1209',1);
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1324,1210,'equipo',1100,'Escuadra QA 01','confirmado',1004,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1325,1210,'equipo',1105,'Escuadra QA 06','confirmado',1004,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1326,1210,'equipo',1110,'Escuadra QA 11','confirmado',1004,'2026-01-02 12:00:00');
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1210,1324,4,3,0,0,12,5,6,0);
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1210,1325,4,2,1,1,10,6,3,0);
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1210,1326,4,1,0,2,8,7,0,0);
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1440,1210,1,1324,1326,'2026-02-02 20:00:00','Cancha 1',NULL,NULL,'programado',NULL,1004);
INSERT IGNORE INTO torneo_cambio_estado (torneo_id,estado_anterior,estado_nuevo,motivo,usuario_id) VALUES (1210,'borrador','inscripciones_abiertas','Apertura inicial QA',1004),(1210,'inscripciones_abiertas','inscripciones_abiertas','Transición sintética QA',1004);
INSERT IGNORE INTO torneo_comentarios (torneo_id,usuario_id,nombre_autor,email_autor,comentario,aprobado) VALUES (1210,1044,'Jugador QA','jugador.qa35@ascend.test','Comentario de prueba del torneo 1210',1);
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1327,1211,'equipo',1101,'Escuadra QA 02','confirmado',1005,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1328,1211,'equipo',1106,'Escuadra QA 07','confirmado',1005,'2026-01-02 12:00:00');
INSERT IGNORE INTO participantes_torneo (id,torneo_id,tipo,referencia_id,nombre,estado,confirmado_por,fecha_confirmacion) VALUES (1329,1211,'equipo',1111,'Escuadra QA 12','confirmado',1005,'2026-01-02 12:00:00');
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1211,1327,4,3,0,0,12,5,6,0);
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1211,1328,4,2,1,1,10,6,3,0);
INSERT IGNORE INTO torneo_posiciones (torneo_id,participante_id,partidos_jugados,partidos_ganados,partidos_empatados,partidos_perdidos,puntos_favor,puntos_contra,puntos,sanciones_puntos) VALUES (1211,1329,4,1,0,2,8,7,0,0);
INSERT IGNORE INTO torneo_encuentros (id,torneo_id,ronda,participante_local_id,participante_visitante_id,fecha_hora_programada,cancha,resultado_local,resultado_visitante,estado,participante_ganador_id,modificado_por_usuario_id) VALUES (1444,1211,1,1327,1329,'2026-03-02 20:00:00','Cancha 1',NULL,NULL,'programado',NULL,1005);
INSERT IGNORE INTO torneo_suizo_parejas (torneo_id,participante_a_id,participante_b_id,ronda,ya_se_enfrentaron,fecha_encuentro) VALUES (1211,1327,1328,1,1,'2026-03-01 20:00:00');
INSERT IGNORE INTO torneo_cambio_estado (torneo_id,estado_anterior,estado_nuevo,motivo,usuario_id) VALUES (1211,'borrador','inscripciones_abiertas','Apertura inicial QA',1005),(1211,'inscripciones_abiertas','borrador','Transición sintética QA',1005);
INSERT IGNORE INTO torneo_comentarios (torneo_id,usuario_id,nombre_autor,email_autor,comentario,aprobado) VALUES (1211,1045,'Jugador QA','jugador.qa36@ascend.test','Comentario de prueba del torneo 1211',1);
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
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1000,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('organizador.qa2@ascend.test',1001,1,'192.0.2.2','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1001,'seed.qa','Actividad sintética de prueba','192.0.2.2','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1001,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('organizador.qa3@ascend.test',1002,1,'192.0.2.3','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1002,'seed.qa','Actividad sintética de prueba','192.0.2.3','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1002,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('organizador.qa4@ascend.test',1003,1,'192.0.2.4','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1003,'seed.qa','Actividad sintética de prueba','192.0.2.4','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1003,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('organizador.qa5@ascend.test',1004,1,'192.0.2.5','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1004,'seed.qa','Actividad sintética de prueba','192.0.2.5','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1004,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('organizador.qa6@ascend.test',1005,1,'192.0.2.6','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1005,'seed.qa','Actividad sintética de prueba','192.0.2.6','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1005,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa01@ascend.test',1010,1,'192.0.2.7','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1010,'seed.qa','Actividad sintética de prueba','192.0.2.7','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1010,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa02@ascend.test',1011,1,'192.0.2.8','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1011,'seed.qa','Actividad sintética de prueba','192.0.2.8','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1011,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa03@ascend.test',1012,1,'192.0.2.9','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1012,'seed.qa','Actividad sintética de prueba','192.0.2.9','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1012,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa04@ascend.test',1013,1,'192.0.2.10','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1013,'seed.qa','Actividad sintética de prueba','192.0.2.10','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1013,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa05@ascend.test',1014,1,'192.0.2.11','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1014,'seed.qa','Actividad sintética de prueba','192.0.2.11','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1014,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa06@ascend.test',1015,1,'192.0.2.12','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1015,'seed.qa','Actividad sintética de prueba','192.0.2.12','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1015,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa07@ascend.test',1016,1,'192.0.2.13','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1016,'seed.qa','Actividad sintética de prueba','192.0.2.13','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1016,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa08@ascend.test',1017,1,'192.0.2.14','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1017,'seed.qa','Actividad sintética de prueba','192.0.2.14','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1017,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa09@ascend.test',1018,1,'192.0.2.15','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1018,'seed.qa','Actividad sintética de prueba','192.0.2.15','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1018,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa10@ascend.test',1019,1,'192.0.2.16','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1019,'seed.qa','Actividad sintética de prueba','192.0.2.16','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1019,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa11@ascend.test',1020,1,'192.0.2.17','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1020,'seed.qa','Actividad sintética de prueba','192.0.2.17','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1020,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa12@ascend.test',1021,1,'192.0.2.18','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1021,'seed.qa','Actividad sintética de prueba','192.0.2.18','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1021,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa13@ascend.test',1022,1,'192.0.2.19','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1022,'seed.qa','Actividad sintética de prueba','192.0.2.19','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1022,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa14@ascend.test',1023,1,'192.0.2.20','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1023,'seed.qa','Actividad sintética de prueba','192.0.2.20','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1023,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa15@ascend.test',1024,1,'192.0.2.21','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1024,'seed.qa','Actividad sintética de prueba','192.0.2.21','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1024,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa16@ascend.test',1025,1,'192.0.2.22','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1025,'seed.qa','Actividad sintética de prueba','192.0.2.22','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1025,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa17@ascend.test',1026,1,'192.0.2.23','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1026,'seed.qa','Actividad sintética de prueba','192.0.2.23','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1026,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa18@ascend.test',1027,1,'192.0.2.24','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1027,'seed.qa','Actividad sintética de prueba','192.0.2.24','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1027,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa19@ascend.test',1028,1,'192.0.2.25','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1028,'seed.qa','Actividad sintética de prueba','192.0.2.25','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1028,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa20@ascend.test',1029,1,'192.0.2.26','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1029,'seed.qa','Actividad sintética de prueba','192.0.2.26','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1029,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa21@ascend.test',1030,1,'192.0.2.27','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1030,'seed.qa','Actividad sintética de prueba','192.0.2.27','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1030,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa22@ascend.test',1031,1,'192.0.2.28','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1031,'seed.qa','Actividad sintética de prueba','192.0.2.28','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1031,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa23@ascend.test',1032,1,'192.0.2.29','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1032,'seed.qa','Actividad sintética de prueba','192.0.2.29','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1032,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa24@ascend.test',1033,1,'192.0.2.30','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1033,'seed.qa','Actividad sintética de prueba','192.0.2.30','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1033,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa25@ascend.test',1034,1,'192.0.2.31','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1034,'seed.qa','Actividad sintética de prueba','192.0.2.31','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1034,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa26@ascend.test',1035,1,'192.0.2.32','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1035,'seed.qa','Actividad sintética de prueba','192.0.2.32','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1035,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa27@ascend.test',1036,1,'192.0.2.33','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1036,'seed.qa','Actividad sintética de prueba','192.0.2.33','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1036,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa28@ascend.test',1037,1,'192.0.2.34','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1037,'seed.qa','Actividad sintética de prueba','192.0.2.34','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1037,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa29@ascend.test',1038,1,'192.0.2.35','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1038,'seed.qa','Actividad sintética de prueba','192.0.2.35','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1038,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa30@ascend.test',1039,1,'192.0.2.36','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1039,'seed.qa','Actividad sintética de prueba','192.0.2.36','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1039,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa31@ascend.test',1040,1,'192.0.2.37','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1040,'seed.qa','Actividad sintética de prueba','192.0.2.37','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1040,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa32@ascend.test',1041,1,'192.0.2.38','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1041,'seed.qa','Actividad sintética de prueba','192.0.2.38','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1041,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa33@ascend.test',1042,1,'192.0.2.39','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1042,'seed.qa','Actividad sintética de prueba','192.0.2.39','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1042,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa34@ascend.test',1043,1,'192.0.2.40','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1043,'seed.qa','Actividad sintética de prueba','192.0.2.40','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1043,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa35@ascend.test',1044,1,'192.0.2.41','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1044,'seed.qa','Actividad sintética de prueba','192.0.2.41','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1044,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa36@ascend.test',1045,1,'192.0.2.42','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1045,'seed.qa','Actividad sintética de prueba','192.0.2.42','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1045,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa37@ascend.test',1046,1,'192.0.2.43','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1046,'seed.qa','Actividad sintética de prueba','192.0.2.43','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1046,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa38@ascend.test',1047,1,'192.0.2.44','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1047,'seed.qa','Actividad sintética de prueba','192.0.2.44','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1047,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa39@ascend.test',1048,1,'192.0.2.45','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1048,'seed.qa','Actividad sintética de prueba','192.0.2.45','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1048,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa40@ascend.test',1049,1,'192.0.2.46','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1049,'seed.qa','Actividad sintética de prueba','192.0.2.46','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1049,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa41@ascend.test',1050,1,'192.0.2.47','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1050,'seed.qa','Actividad sintética de prueba','192.0.2.47','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1050,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO logs_acceso (email_intentado,usuario_id,exito,ip_origen,user_agent,mensaje_error) VALUES ('jugador.qa42@ascend.test',1051,1,'192.0.2.48','QA Browser/1.0',NULL);
INSERT IGNORE INTO logs_actividad (usuario_id,accion,descripcion,ip_origen,user_agent) VALUES (1051,'seed.qa','Actividad sintética de prueba','192.0.2.48','QA Browser/1.0');
INSERT IGNORE INTO historial_contrasenas (usuario_id,hash_anterior) VALUES (1051,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT IGNORE INTO newsletter_subscriptores (email,activo) VALUES ('newsletter.qa001@ascend.test',1), ('newsletter.qa002@ascend.test',1), ('newsletter.qa003@ascend.test',1), ('newsletter.qa004@ascend.test',1), ('newsletter.qa005@ascend.test',1), ('newsletter.qa006@ascend.test',1), ('newsletter.qa007@ascend.test',1), ('newsletter.qa008@ascend.test',1), ('newsletter.qa009@ascend.test',1), ('newsletter.qa010@ascend.test',1), ('newsletter.qa011@ascend.test',1), ('newsletter.qa012@ascend.test',1), ('newsletter.qa013@ascend.test',1), ('newsletter.qa014@ascend.test',1), ('newsletter.qa015@ascend.test',1), ('newsletter.qa016@ascend.test',1), ('newsletter.qa017@ascend.test',1), ('newsletter.qa018@ascend.test',1), ('newsletter.qa019@ascend.test',1), ('newsletter.qa020@ascend.test',1), ('newsletter.qa021@ascend.test',1), ('newsletter.qa022@ascend.test',1), ('newsletter.qa023@ascend.test',1), ('newsletter.qa024@ascend.test',1), ('newsletter.qa025@ascend.test',1), ('newsletter.qa026@ascend.test',1), ('newsletter.qa027@ascend.test',1), ('newsletter.qa028@ascend.test',1), ('newsletter.qa029@ascend.test',1), ('newsletter.qa030@ascend.test',1);
INSERT IGNORE INTO politicas_contrasenas (id,longitud_minima,requiere_mayuscula,requiere_minuscula,requiere_numero,requiere_caracter_especial,expiracion_dias,historial_cantidad,actualizado_por) VALUES (1000,10,1,1,1,1,120,8,1000);
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
