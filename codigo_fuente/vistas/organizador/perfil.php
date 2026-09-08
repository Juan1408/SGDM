<?php
$perfil = $datos['perfil'] ?? [];
$escapar = static fn ($valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASCEND | Mi perfil</title>
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/botones.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/componentes/tarjetas.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/organizador-layout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .perfil-pagina { max-width: 880px; }
        .perfil-formulario { display: grid; gap: 1rem; padding: 1.25rem; background: var(--fondo-tarjeta); border: 1px solid var(--borde-suave); border-radius: var(--radio-lg); }
        .perfil-formulario label { display: grid; gap: .35rem; color: var(--texto-secundario); font-size: .85rem; }
        .perfil-formulario input, .perfil-formulario textarea { width: 100%; box-sizing: border-box; padding: .7rem; border: 1px solid var(--borde-suave); border-radius: var(--radio-md); background: rgba(255,255,255,.04); color: #fff; font: inherit; }
        .perfil-formulario textarea { min-height: 110px; resize: vertical; }
        .perfil-notificacion { margin-bottom: 1rem; padding: .8rem; border-radius: var(--radio-md); background: rgba(0,255,238,.1); }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/navbar.html'; ?>
    <?php require_once __DIR__ . '/sidebar.html'; ?>
    <main class="dashboard-organizador perfil-pagina">
        <header class="dashboard-header"><div><p class="dashboard-tag">CUENTA DEL ORGANIZADOR</p><h1>Mi perfil</h1><p>Actualiza tus datos personales y los de tu organización.</p></div></header>
        <?php if (!empty($datos['mensaje'])): ?><p class="perfil-notificacion"><?php echo $escapar($datos['mensaje']); ?></p><?php endif; ?>
        <form class="perfil-formulario" method="post" action="<?php echo URL_BASE; ?>index.php?c=panelOrganizador&amp;a=guardarPerfil">
            <label>Nombre completo<input name="nombre_completo" value="<?php echo $escapar($perfil['nombre_completo'] ?? ''); ?>" maxlength="255" required></label>
            <label>Correo electrónico<input type="email" value="<?php echo $escapar($perfil['email'] ?? ''); ?>" disabled></label>
            <label>Teléfono<input name="telefono" value="<?php echo $escapar($perfil['telefono'] ?? ''); ?>" maxlength="20"></label>
            <label>Organización<input name="nombre_organizacion" value="<?php echo $escapar($perfil['nombre_organizacion'] ?? ''); ?>" maxlength="150" required></label>
            <label>Localidad<input name="localidad" value="<?php echo $escapar($perfil['localidad'] ?? ''); ?>" maxlength="100"></label>
            <label>Teléfono de contacto<input name="telefono_contacto" value="<?php echo $escapar($perfil['telefono_contacto'] ?? ''); ?>" maxlength="50"></label>
            <label>Sitio web<input type="url" name="sitio_web" value="<?php echo $escapar($perfil['sitio_web'] ?? ''); ?>" maxlength="255"></label>
            <label>Biografía<textarea name="bio_organizacion" maxlength="2000"><?php echo $escapar($perfil['bio_organizacion'] ?? ''); ?></textarea></label>
            <button class="boton boton-gestion" type="submit"><i class="fa-solid fa-floppy-disk"></i> Guardar perfil</button>
        </form>
    </main>
    <script src="<?php echo URL_BASE; ?>js/organizador/includes.js"></script><script src="<?php echo URL_BASE; ?>js/organizador/sidebar.js"></script>
</body>
</html>
