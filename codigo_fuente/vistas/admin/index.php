<?php
// Enrutador de Maquetas Estáticas (Mockups)
// Extraemos la vista al inicio para poder usarla en las clases del HTML
$vista = isset($_GET['vista']) ? $_GET['vista'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - ASCEND</title>
    <link rel="stylesheet" href="../../publico/css/admin.css">
</head>

<body class="tema-admin <?php echo ($vista === 'dashboard') ? 'en-inicio' : 'en-interna'; ?>">
    <header class="cabecera-principal">
        <div class="contenedor-logo">
            <a href="index.php?vista=dashboard" class="enlace-logo">
                <img src="../../publico/img/logos/logoAscend.jpg" alt="Logotipo del Sistema">
            </a>
            <h1 class="titulo-cabecera">ASCEND</h1>
        </div>
        
        <div class="perfil-admin dropdown">
            <button class="dropdown-btn" id="btnPerfilAdmin">
                <span class="avatar-placeholder">A</span>
                <span>Administrador General ▾</span>
            </button>
            <div class="dropdown-content" id="menuPerfilAdmin">
                <a href="#">Editar Perfil</a>
                <a href="#">Cambiar Contraseña</a>
                <a href="/salir" class="enlace-salir">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <div class="barra-movil">
        <button class="boton-menu-movil" id="btnMenuMovil">☰ Abrir Menú</button>
    </div>

    <div class="cuerpo-admin">
        <aside class="menu-lateral">
            <nav>
                <ul class="menu-lista">
                    <li><a href="index.php?vista=dashboard" class="menu-enlace">Inicio</a></li>
                    
                    <li><a href="#" class="menu-enlace">Usuarios</a>
                        <ul class="menu-sublista">
                            <li><a href="index.php?vista=usuarios/lista" class="menu-enlace">Ver Todos</a></li>
                            <li><a href="index.php?vista=usuarios/formulario" class="menu-enlace">Registrar Nuevo Usuario</a></li>
                        </ul>
                    </li>
                    
                    <li><a href="#" class="menu-enlace">Participantes y Equipos</a>
                        <ul class="menu-sublista">
                            <li><a href="index.php?vista=equipos/lista" class="menu-enlace">Ver Todos</a></li>
                            <li><a href="index.php?vista=equipos/formulario" class="menu-enlace">Registrar Nuevo P/E</a></li>
                        </ul>
                    </li>
                    
                    <li><a href="#" class="menu-enlace">Torneos</a>
                        <ul class="menu-sublista">
                            <li><a href="index.php?vista=torneos/lista" class="menu-enlace">Ver Todos</a></li>
                            <li><a href="index.php?vista=torneos/crear" class="menu-enlace">Crear Nuevo Torneo</a></li>
                        </ul>
                    </li>
                    
                    <li><a href="#" class="menu-enlace">Resultados</a>
                        <ul class="menu-sublista">
                            <li><a href="index.php?vista=resultados/lista" class="menu-enlace">Ver Todos</a></li>
                            <li><a href="index.php?vista=resultados/formulario" class="menu-enlace">Registrar Nuevo</a></li>
                        </ul>
                    </li>
                    
                    <li><a href="#" class="menu-enlace">Configuración</a>
                        <ul class="menu-sublista">
                            <li><a href="index.php?vista=configuracion/general" class="menu-enlace">Sistema</a></li>
                            <li><a href="index.php?vista=configuracion/auditoria" class="menu-enlace">Auditoría</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="area-contenido">
            <?php
            // 2. Construimos la ruta segura hacia el archivo .html
            // Reemplazamos posibles trucos de seguridad (como ../) para evitar que lean otros archivos.
            $vistaSegura = str_replace(['../', '..\\'], '', $vista);
            $rutaArchivo = __DIR__ . '/' . $vistaSegura . '.html';
            
            // 3. Verificamos si el archivo de la maqueta realmente existe
            if (file_exists($rutaArchivo)) {
                // Inyectamos el pedazo de HTML en el centro de la página
                include $rutaArchivo;
            } else {
                // Si alguien pone una URL inventada, mostramos un error elegante
                echo '<div>';
                echo '  <h2 class="titulo-pantalla" style="color: #e74c3c;">Error 404</h2>';
                echo '  <p>La pantalla solicitada <strong>(' . htmlspecialchars($vistaSegura) . ')</strong> no existe o aún no ha sido maquetada.</p>';
                echo '  <a href="index.php?vista=dashboard" class="boton-secundario">Volver al Inicio</a>';
                echo '</div>';
            }
            ?>
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
    <script src="../../publico/js/admin.js"></script>
</body>

</html>
