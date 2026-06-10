<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?? 'Crear Equipo - SGDM'; ?></title>
    <link rel="stylesheet" href="/publico/css/style.css">
    <link rel="stylesheet" href="/publico/css/equipos.css">
</head>
<body>
    <?php require_once __DIR__ . '/../parciales/cabecera.php'; ?>

    <main class="contenedor-pagina" style="max-width: 600px;">
        <div class="barra-superior">
            <h1 class="titulo-pagina">Crear Nuevo Equipo</h1>
            <a href="/equipos" style="color: var(--color-texto-secundario); text-decoration:none;">← Volver</a>
        </div>

        <?php if (isset($errores) && !empty($errores)): ?>
            <div class="alerta alerta-error">
                <ul style="list-style-position:inside; margin:0; padding:0;">
                    <?php foreach ($errores as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="tarjeta-formulario">
            <form action="/equipos/crear" method="POST">
                <input type="hidden" name="token_csrf" value="<?php echo htmlspecialchars($token_csrf); ?>">

                <div class="grupo-formulario">
                    <label for="nombre_equipo">Nombre del Equipo *</label>
                    <input type="text" id="nombre_equipo" name="nombre_equipo" class="control-formulario"
                           placeholder="Ej: Los Dragones FC" required
                           value="<?php echo htmlspecialchars($datos['nombre_equipo'] ?? ''); ?>">
                </div>

                <div class="grupo-formulario">
                    <label for="descripcion">Descripción o Lema</label>
                    <textarea id="descripcion" name="descripcion" class="control-formulario"
                              rows="3" placeholder="Contá de qué se trata tu equipo..."
                              style="resize: vertical;"><?php echo htmlspecialchars($datos['descripcion'] ?? ''); ?></textarea>
                </div>

                <div class="info-box" style="margin-bottom: 20px; padding: 12px 16px; background: rgba(59,130,246,0.1); border: 1px solid rgba(59,130,246,0.25); border-radius: 8px; font-size:0.88rem; color: var(--color-texto-secundario);">
                    💡 Al crear el equipo serás designado automáticamente como <strong style="color: var(--color-texto-principal);">Capitán Principal</strong>. Se generará un <strong style="color: var(--color-texto-principal);">Código de Invitación</strong> que podrás compartir con futuros miembros.
                </div>

                <button type="submit" class="boton-primario">Crear Equipo</button>
            </form>
        </div>
    </main>
</body>
</html>
