-- datos_ejemplo.sql
-- Semillas para poblar la base de datos con datos de prueba

-- =============================================================================
-- 9. DATOS SEMILLA (Seeders Iniciales para Pruebas y Producción)
-- =============================================================================

-- Permisos
INSERT IGNORE INTO
    permisos (nombre_permiso, descripcion)
VALUES (
        'crear_torneos',
        'Permite crear nuevas competencias'
    ),
    (
        'editar_torneos',
        'Permite modificar configuraciones y reglamentos'
    ),
    (
        'borrar_torneos',
        'Permite eliminar torneos'
    ),
    (
        'cargar_resultados',
        'Permite asentar scores y ganadores'
    ),
    (
        'crear_usuarios',
        'Permite dar de alta cuentas administrativas'
    ),
    (
        'enviar_mensajes',
        'Permite emitir comunicados masivos'
    ),
    (
        'descargar_reportes',
        'Permite exportar datos en PDF, Excel y CSV'
    ),
    (
        'ver_auditoria',
        'Permite auditar accesos y cambios críticos'
    );

-- Roles
INSERT IGNORE INTO
    roles (
        id,
        nombre_rol,
        descripcion,
        nivel_permiso
    )
VALUES (
        1,
        'Administrador General',
        'Superusuario con acceso irrestricto',
        3
    ),
    (
        2,
        'Organizador',
        'Gestor de competencias, fixtures y resultados',
        2
    ),
    (
        3,
        'Participante / Jugador',
        'Competidor en torneos y miembro de equipos',
        1
    ),
    (
        4,
        'Usuario Publico',
        'Espectador con permisos de solo lectura',
        0
    );

-- Asignar todos los permisos al Administrador General (rol_id = 1)
INSERT IGNORE INTO
    rol_permisos (rol_id, permiso_id)
SELECT 1, id
FROM permisos;

-- Modalidades
INSERT IGNORE INTO
    modalidades (id, nombre, descripcion)
VALUES (
        1,
        'individual',
        'Competencia de 1 contra 1'
    ),
    (
        2,
        'equipos',
        'Competencia colectiva por escuadras'
    );

-- Sistemas de Puntuación
INSERT IGNORE INTO
    sistemas_puntuacion (
        id,
        nombre,
        puntos_victoria,
        puntos_empate,
        puntos_derrota,
        descripcion
    )
VALUES (
        1,
        'estandar_3_1_0',
        3.00,
        1.00,
        0.00,
        '3 pts Victoria, 1 pt Empate, 0 pts Derrota (Fútbol/Rugby estándar)'
    ),
    (
        2,
        'ajedrez_1_0.5_0',
        1.00,
        0.50,
        0.00,
        '1 pt Victoria, 0.5 pt Tablas, 0 pts Derrota'
    ),
    (
        3,
        'esports_mapas_2_1_0',
        2.00,
        1.00,
        0.00,
        '2 pts Victoria por mapa, 1 pt Empate, 0 pts Derrota'
    );

-- Disciplinas / Juegos
INSERT IGNORE INTO
    juegos (
        id,
        nombre,
        categoria,
        puntos_victoria,
        puntos_empate,
        puntos_derrota
    )
VALUES (
        1,
        'Valorant',
        'esport-shooter',
        3.00,
        1.00,
        0.00
    ),
    (
        2,
        'League of Legends',
        'esport-moba',
        3.00,
        0.00,
        0.00
    ),
    (
        3,
        'Rugby 7s',
        'deporte-fisico',
        4.00,
        2.00,
        0.00
    ),
    (
        4,
        'Fútbol 5',
        'deporte-fisico',
        3.00,
        1.00,
        0.00
    ),
    (
        5,
        'Ajedrez',
        'juego-mesa',
        1.00,
        0.50,
        0.00
    );

-- Políticas de Contraseñas por Defecto
INSERT IGNORE INTO
    politicas_contrasenas (
        id,
        longitud_minima,
        requiere_mayuscula,
        requiere_minuscula,
        requiere_numero,
        requiere_caracter_especial,
        expiracion_dias,
        historial_cantidad
    )
VALUES (1, 8, 1, 1, 1, 1, 90, 5);

-- Logros Iniciales del Sistema
INSERT IGNORE INTO
    logros (
        id,
        codigo,
        nombre,
        descripcion,
        icono,
        puntos_recompensa
    )
VALUES (
        1,
        'primera_batalla',
        'Primera Batalla',
        'Jugaste tu primer torneo en la plataforma',
        'fa-shield-halved',
        100
    ),
    (
        2,
        'campeon',
        'Campeón',
        'Ganaste un torneo oficial dentro de ASCEND',
        'fa-trophy',
        500
    ),
    (
        3,
        'veterano',
        'Veterano',
        'Completaste más de 10 torneos oficiales',
        'fa-award',
        300
    ),
    (
        4,
        'en_racha',
        'En Racha',
        'Ganaste 3 partidos consecutivos',
        'fa-fire',
        200
    );

-- Usuario Administrador Inicial (Contraseña de prueba: Admin123!)
INSERT IGNORE INTO
    usuarios (
        id,
        email,
        contrasena_hash,
        nombre_completo,
        telefono,
        rol_id,
        esta_activo,
        email_verificado
    )
VALUES (
        1,
        'admin@ascend.com',
        '$2y$10$1/Bs9kQBa4wTOBmKf.ipzyterxkNODRZJRKUUJ9KG3vZqURVO',
        'Administrador General',
        '+59899123456',
        1,
        1,
        1
    );

-- Formatos de Torneo
INSERT IGNORE INTO
    formatos_torneo (codigo, nombre, descripcion)
VALUES (
        'liga',
        'Fase de Grupos / Liga',
        'Todos juegan contra todos y suman puntos.'
    ),
    (
        'eliminacion_directa',
        'Eliminatoria Directa',
        'El que pierde queda eliminado del torneo.'
    ),
    (
        'suizo',
        'Sistema Suizo',
        'Emparejamientos por rondas según el puntaje acumulado.'
    );

-- Usuario Organizador Inicial
INSERT IGNORE INTO
    usuarios (
        id,
        email,
        contrasena_hash,
        nombre_completo,
        telefono,
        rol_id,
        esta_activo,
        email_verificado
    )
VALUES (
        2,
        'organizador@ascend.com',
        '$2y$10$1/Bs9kQBa4wTOBmKf.ipzyterxkNODRZJRKUUJ9KG3vZqURVO',
        'Valentina Organizadora',
        '+59899654321',
        2,
        1,
        1
    );

INSERT IGNORE INTO
    perfiles_organizadores (
        usuario_id,
        nombre_organizacion,
        bio_organizacion,
        localidad,
        telefono_contacto,
        verificado_oficial
    )
VALUES (
        2,
        'Club Deportivo ASCEND',
        'Organizadora oficial de competencias regionales de eSports y Rugby',
        'Montevideo',
        '+59899654321',
        1
    );

-- Usuario Jugador Inicial
INSERT IGNORE INTO
    usuarios (
        id,
        email,
        contrasena_hash,
        nombre_completo,
        telefono,
        rol_id,
        esta_activo,
        email_verificado
    )
VALUES (
        3,
        'jugador@ascend.com',
        '$2y$10$1/Bs9kQBa4wTOBmKf.ipzyterxkNODRZJRKUUJ9KG3vZqURVO',
        'Marcos Competidor',
        '+59899777888',
        3,
        1,
        1
    );

INSERT IGNORE INTO
    perfiles_jugadores (
        usuario_id,
        apodo_gamertag,
        bio,
        nivel,
        experiencia_puntos,
        pais,
        ciudad,
        discord_tag
    )
VALUES (
        3,
        'ShadowStriker',
        'Jugador competitivo de Valorant y Rugby virtual',
        3,
        3928,
        'Uruguay',
        'Montevideo',
        'Shadow#1234'
    );

INSERT IGNORE INTO
    usuario_logros (
        usuario_id,
        logro_id,
        estado,
        progreso_actual,
        progreso_objetivo
    )
VALUES (3, 1, 'desbloqueado', 1, 1),
    (3, 2, 'desbloqueado', 1, 1),
    (3, 3, 'en_curso', 6, 10);