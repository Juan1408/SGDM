<?php
/** @var String $vistaInyectada */
// Título opcional por vista (por defecto se mantiene el saludo del dashboard)
$tituloPantalla = isset($tituloPantalla) && is_string($tituloPantalla) && $tituloPantalla !== ''
    ? $tituloPantalla
    : 'Bienvenido, ' . ($_SESSION['usuario_nombre'] ?? 'Administrador') . '!';

// Hojas de estilo adicionales opcionales, relativas a publico/ (ej: css/admin/ficha.css)
$estilosExtra = isset($estilosExtra) && is_array($estilosExtra) ? $estilosExtra : [];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tituloPantalla); ?> - ASCEND</title>
    <!-- Estilos cargados en paralelo (Sin @import) para máxima velocidad (HTTP/2) -->
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/admin/base.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/admin/layout.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/admin/componentes.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/admin/utilidades.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/admin/perfil.css">
    <?php foreach ($estilosExtra as $hojaEstilo): ?>
        <?php if (is_string($hojaEstilo) && $hojaEstilo !== ''): ?>
            <link rel="stylesheet" href="<?php echo URL_BASE . htmlspecialchars(ltrim($hojaEstilo, '/'), ENT_QUOTES, 'UTF-8'); ?>">
        <?php endif; ?>
    <?php endforeach; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Script Anti-FOUC (Flash of Unstyled Content): Aplica el tema oscuro antes de renderizar el DOM -->
    <script>
        if (localStorage.getItem('admin_tema') === 'dark') {
            document.documentElement.classList.add('tema-dark-fouc'); // Opcional, pero usaremos el body luego
        }
    </script>
</head>

<body class="tema-admin en-inicio" 
      data-flash-mensaje="<?php echo htmlspecialchars($_SESSION['mensaje'] ?? ''); ?>"
      data-flash-tipo="<?php echo htmlspecialchars($_SESSION['mensaje_tipo'] ?? 'info'); ?>">
    <?php 
        // Limpiamos los mensajes de la sesión una vez inyectados en el DOM
        if (isset($_SESSION['mensaje'])) {
            unset($_SESSION['mensaje']);
            unset($_SESSION['mensaje_tipo']);
        }
    ?>

    <!-- Aplicamos la clase dark-mode inmediatamente si estaba guardado, para no parpadear -->
    <script>
        if (localStorage.getItem('admin_tema') === 'dark') {
            document.body.classList.add('dark-mode');
        }
    </script>

    <header class="cabecera-principal">
        <div class="contenedor-logo">
            <a href="#dashboard" class="enlace-logo">
                <img src="<?php echo URL_BASE; ?>img/logos/ascend-logo01.png" alt="Logotipo del Sistema">
            </a>
        </div>

        <div class="acciones-cabecera" style="display: flex; align-items: center; gap: 1rem;">
            <!-- Selector de Tema Claro/Oscuro -->
            <button id="btnToggleTema" class="btn-tema" aria-label="Cambiar Tema" title="Cambiar a Modo Oscuro" style="background: none; border: none; cursor: pointer; color: var(--color-texto-principal); font-size: 1.3rem; transition: transform 0.3s ease;">
                <i class="fa-solid fa-moon"></i>
            </button>

            <div class="perfil-admin dropdown">
                <button class="dropdown-btn" id="btnPerfilAdmin">
                    <span class="avatar-placeholder"><?php echo strtoupper(substr($_SESSION['usuario_nombre'] ?? 'A', 0, 1)); ?></span>
                    <span class="nombre-admin-pc">
                        <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario'); ?> 
                        <span class="etiqueta-rol">(Administrador General)</span> ▾
                    </span>
                    <span class="nombre-admin-movil" style="display: none;">
                        <?php echo htmlspecialchars(explode(' ', $_SESSION['usuario_nombre'] ?? 'Usuario')[0]); ?> ▾
                    </span>
                </button>
                <div class="dropdown-content" id="menuPerfilAdmin">
                <a href="<?php echo URL_BASE; ?>index.php?c=admin&a=editarPerfil">Editar Perfil</a>
                <a href="<?php echo URL_BASE; ?>index.php?c=politicaContrasena&a=index">Configuración</a>
                <a href="<?php echo URL_BASE; ?>index.php?c=auth&a=logout" class="enlace-salir">Cerrar Sesión</a>
            </div>
            </div> <!-- CIERRE DE .perfil-admin -->
        </div> <!-- CIERRE DE .acciones-cabecera -->
    </header>

    <div class="barra-movil">
        <button class="boton-menu-movil" id="btnMenuMovil">Abrir Menú</button>
    </div>

    <div class="cuerpo-admin">
        <!--Incluimos el menu lateral de admin -->
        <?php require_once __DIR__ . '/plantilla/menu_lateral.php'; ?>

        <main class="area-contenido" id="area-contenido">
            <!--Aca le vamos a dar la bienvenida al administrador con el nombre que traiga de la base de datos-->
            <h1 class="titulo-pantalla"><?php echo htmlspecialchars($tituloPantalla); ?></h1>

            <!-- Aca inyectamos las tarjetas y tablas de la vista -->
            <?php require_once __DIR__ . '/' . $vistaInyectada; ?>
        </main>

    </div>

    <footer class="pie-pagina">
        <div class="pie-contenedor">
            <div class="pie-texto">
                <p>&copy; 2026 ASCEND. Todos los derechos reservados.</p>
            </div>
            <div class="pie-enlaces">
                <a href="#">Términos de Servicio</a>
                <a href="#">Política de Privacidad</a>
                <a href="#">Centro de Ayuda</a>
            </div>
        </div>
    </footer>

    <!-- Contenedor para Notificaciones Toast -->
    <div class="contenedor-notificaciones" id="contenedorNotificaciones"></div>

    <script src="<?php echo URL_BASE; ?>js/admin/admin.js"></script>
    <script src="<?php echo URL_BASE; ?>js/admin/tema.js"></script>
    <script src="<?php echo URL_BASE; ?>js/admin/reportes.js"></script>

</body>

</html>
