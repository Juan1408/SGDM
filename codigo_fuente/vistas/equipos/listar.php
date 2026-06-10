<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?? 'Equipos - SGDM'; ?></title>
    <link rel="stylesheet" href="/publico/css/style.css">
    <link rel="stylesheet" href="/publico/css/equipos.css">
</head>
<body>
    <?php require_once __DIR__ . '/../parciales/cabecera.php'; ?>

    <main class="contenedor-pagina">
        <div class="barra-superior">
            <h1 class="titulo-pagina">Equipos Registrados</h1>
            <a href="/equipos/crear" class="boton-primario" style="width:auto; padding: 10px 20px; text-decoration:none;">+ Crear Equipo</a>
        </div>

        <!-- Alertas de resultado de operaciones previas -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alerta alerta-error">
                <?php
                $mensajesError = [
                    'codigo_invalido' => 'El código de invitación ingresado no es válido o el equipo no está activo.',
                    'ya_miembro' => 'Ya eres miembro de ese equipo.',
                    'solicitud_fallida' => 'No se pudo enviar la solicitud. Intentá de nuevo más tarde.',
                    'no_autorizado' => 'No tenés permisos para realizar esta acción.',
                ];
                echo htmlspecialchars($mensajesError[$_GET['error']] ?? 'Ocurrió un error.');
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['mensaje_exito']) && $_GET['mensaje_exito'] === 'solicitud_enviada'): ?>
            <div class="alerta alerta-exito">Tu solicitud fue enviada correctamente. El capitán del equipo deberá aprobarla.</div>
        <?php endif; ?>

        <!-- Formulario para unirse a equipo con código -->
        <div class="tarjeta-formulario">
            <h2 style="font-size:1rem; color: var(--color-texto-secundario); margin-bottom:12px;">UNIRSE CON CÓDIGO DE INVITACIÓN</h2>
            <form action="/equipos/unirse" method="POST" style="display:flex; gap:10px; flex-wrap:wrap;">
                <input type="hidden" name="token_csrf" value="<?php echo htmlspecialchars($token_csrf); ?>">
                <input type="text" name="codigo_invitacion" class="control-formulario" style="flex:1; min-width:200px;" placeholder="Código de 8 caracteres (ej: AB3C9F72)" required>
                <input type="text" name="mensaje" class="control-formulario" style="flex:2; min-width:200px;" placeholder="Mensaje de presentación (opcional)">
                <button type="submit" class="boton-secundario">Enviar Solicitud</button>
            </form>
        </div>

        <!-- Grilla de equipos -->
        <?php if (empty($equipos)): ?>
            <div class="estado-vacio">
                <p>No hay equipos registrados aún. ¡Sé el primero en crear uno!</p>
            </div>
        <?php else: ?>
            <div class="grilla-equipos">
                <?php foreach ($equipos as $equipo): ?>
                    <a href="/equipos/ver?id=<?php echo $equipo->id; ?>" class="tarjeta-equipo">
                        <div class="escudo-equipo">
                            <?php if ($equipo->escudoUrl): ?>
                                <img src="<?php echo htmlspecialchars($equipo->escudoUrl); ?>" alt="Escudo de <?php echo htmlspecialchars($equipo->nombreEquipo); ?>">
                            <?php else: ?>
                                <span class="escudo-placeholder"><?php echo strtoupper(substr($equipo->nombreEquipo, 0, 2)); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="info-equipo">
                            <h3><?php echo htmlspecialchars($equipo->nombreEquipo); ?></h3>
                            <?php if ($equipo->descripcion): ?>
                                <p><?php echo htmlspecialchars(substr($equipo->descripcion, 0, 80)) . (strlen($equipo->descripcion) > 80 ? '...' : ''); ?></p>
                            <?php endif; ?>
                        </div>
                        <span class="etiqueta-ver">Ver Equipo →</span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
