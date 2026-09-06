<?php
/** @var String $vistaInyectada */
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - ASCEND</title>
    <!-- Estilos cargados en paralelo (Sin @import) para máxima velocidad (HTTP/2) -->
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/admin/base.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/admin/layout.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/admin/componentes.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/admin/utilidades.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/admin/perfil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="tema-admin en-inicio">
    <header class="cabecera-principal">
        <div class="contenedor-logo">
            <a href="#dashboard" class="enlace-logo">
                <img src="<?php echo URL_BASE; ?>img/logos/ascend-logo01.png" alt="Logotipo del Sistema">
            </a>
           
        </div>

        <div class="perfil-admin dropdown">
            <button class="dropdown-btn" id="btnPerfilAdmin">
                <span class="avatar-placeholder">A</span>
                <span>Administrador General ▾</span>
            </button>
            <div class="dropdown-content" id="menuPerfilAdmin">
                <a href="#perfil/editar-perfil">Editar Perfil</a>
                <a href="#configuracion/general">Configuración</a>
                <a href="../organizador/dashboard.html" class="enlace-destacado">Mis Torneos (Modo
                    Organizador)</a>
                <a href="<?php echo URL_BASE; ?>auth/login.html" class="enlace-salir">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <div class="barra-movil">
        <button class="boton-menu-movil" id="btnMenuMovil">Abrir Menú</button>
    </div>

    <div class="cuerpo-admin">
        <!--Incluimos el menu lateral de admin -->
        <?php require_once __DIR__ . '/plantilla/menu_lateral.php'; ?>

        <main class="area-contenido" id="area-contenido">
            <!--Aca le vamos a dar la bienvenida al administrador con el nombre que traiga de la base de datos-->
            <h1 class="titulo-pantalla">
                Bienvenido, <?php echo $_SESSION['usuario_nombre']; ?>!
            </h1>

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
    <script src="<?php echo URL_BASE; ?>js/admin/reportes.js"></script>
    
    <?php if (isset($_SESSION['mensaje'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof mostrarNotificacionToast === 'function') {
                    // El mensaje se muestra y se borra de la sesión (Flash Message)
                    mostrarNotificacionToast("<?php echo $_SESSION['mensaje']; ?>");
                }
            });
        </script>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>
</body>

</html>

