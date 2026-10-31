<?php
// Generador de Semillas (Basado en el script generado por la IA local)
// Generará el archivo: c:\xampp\htdocs\SGDM\base_de_datos\migraciones\semillas\datos_ejemplo_50.sql

$file = fopen("c:\\xampp\\htdocs\\SGDM\\base_de_datos\\migraciones\\003_datos_ejemplo_50.sql", "w");
if (!$file) {
    die("Error al crear el archivo SQL.");
}

fwrite($file, "-- =============================================================================\n");
fwrite($file, "-- SCRIPT DE SEMILLAS MASIVAS (50+ registros por tabla)\n");
fwrite($file, "-- Generado automáticamente para pruebas de carga\n");
fwrite($file, "-- =============================================================================\n\n");
fwrite($file, "USE ascend;\n\n");
fwrite($file, "SET FOREIGN_KEY_CHECKS = 0;\n\n");

// Generar 50 usuarios
$userRoles = array(1, 2, 3);
fwrite($file, "-- Insertando 50 usuarios\n");
for ($i = 1; $i <= 50; $i++) {
    $role = $userRoles[array_rand($userRoles)];
    $userName = "Usuario_Prueba_" . $i;
    $email = "test" . $i . "@ejemplo.com";
    $hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'; // 'password' hash
    fwrite($file, "INSERT IGNORE INTO usuarios (id, email, contrasena_hash, nombre_completo, rol_id, esta_activo, email_verificado) VALUES ($i, '$email', '$hash', '$userName', $role, 1, 1);\n");
}
fwrite($file, "\n");

// Generar 50 equipos
fwrite($file, "-- Insertando 50 equipos\n");
for ($i = 1; $i <= 50; $i++) {
    $teamName = "Equipo Alpha " . $i;
    $creado_por = rand(1, 50);
    fwrite($file, "INSERT IGNORE INTO equipos (id, nombre_equipo, juego_id, creado_por, activo) VALUES ($i, '$teamName', 1, $creado_por, 1);\n");
}
fwrite($file, "\n");

// Generar 50 torneos
fwrite($file, "-- Insertando 50 torneos\n");
$formatos = ['liga', 'eliminacion_directa', 'suizo'];
for ($i = 1; $i <= 50; $i++) {
    $tournamentName = "Torneo Elite " . $i;
    $organizador = rand(1, 50);
    $formato = $formatos[array_rand($formatos)];
    fwrite($file, "INSERT IGNORE INTO torneos (id, nombre, juego_id, formato, estado, cupo_max_equipos, organizador_id, modalidad_id) VALUES ($i, '$tournamentName', 1, '$formato', 'en_curso', 16, $organizador, 1);\n");
}
fwrite($file, "\n");

// Generar 50 participantes_torneo
fwrite($file, "-- Insertando 50 participantes de torneo\n");
for ($i = 1; $i <= 50; $i++) {
    $teamId = rand(1, 50);
    $tournamentId = rand(1, 50);
    $nombre = "Equipo Alpha " . $teamId;
    fwrite($file, "INSERT IGNORE INTO participantes_torneo (id, torneo_id, tipo, referencia_id, nombre, estado) VALUES ($i, $tournamentId, 'equipo', $teamId, '$nombre', 'confirmado');\n");
}
fwrite($file, "\n");

// Generar 50 torneo_encuentros
fwrite($file, "-- Insertando 50 encuentros\n");
for ($i = 1; $i <= 50; $i++) {
    $tournamentId = rand(1, 50);
    $local = rand(1, 50);
    $visitante = rand(1, 50);
    $ronda = rand(1, 5);
    fwrite($file, "INSERT IGNORE INTO torneo_encuentros (id, torneo_id, ronda, participante_local_id, participante_visitante_id, estado) VALUES ($i, $tournamentId, $ronda, $local, $visitante, 'finalizado');\n");
}

fwrite($file, "\nSET FOREIGN_KEY_CHECKS = 1;\n");
fclose($file);

echo "Archivo semilla_50_registros.sql generado exitosamente.";
?>
