<?php
/** @var String $vistaInyectada */
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - ASCEND</title>
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/admin/admin.css">
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
        <button class="boton-menu-movil" id="btnMenuMovil">☰ Abrir Menú</button>
    </div>

    <div class="cuerpo-admin">
        <aside class="menu-lateral">
            <nav>
                <ul class="menu-lista">
                    <li><a href="#dashboard" class="menu-enlace">Inicio</a></li>

                                        <!-- 1. GESTIÓN HUMANA -->
                    <li><a href="#" class="menu-enlace">Gestión de Usuarios</a>
                        <ul class="menu-sublista">
                            <li><a href="<?php echo URL_BASE; ?>index.php?c=usuario&a=index" class="menu-enlace">Usuarios Administrativos</a></li>
                            <li><a href="#organizadores/lista" class="menu-enlace">Organizadores (Directorio)</a></li>
                            <li><a href="#jugadores/lista" class="menu-enlace">Jugadores Registrados</a></li>
                        </ul>
                    </li>

                    <li><a href="#" class="menu-enlace">Equipos y Solicitudes</a>
                        <ul class="menu-sublista">
                            <li><a href="#equipos/lista" class="menu-enlace">Directorio de Equipos</a></li>
                            <li><a href="#equipos/solicitudes" class="menu-enlace">Aprobar Solicitudes</a></li>
                        </ul>
                    </li>

                    <!-- 2. GESTIÓN DE COMPETENCIAS (Rol Omnipotente) -->
                    <li><a href="#" class="menu-enlace">Juegos y Disciplinas</a>
                        <ul class="menu-sublista">
                            <li><a href="#juegos/lista" class="menu-enlace">Ver Catálogo</a></li>
                            <li><a href="#juegos/formulario" class="menu-enlace">Registrar Nuevo Juego</a></li>
                        </ul>
                    </li>

                    <li><a href="#" class="menu-enlace">Administrar Torneos</a>
                        <ul class="menu-sublista">
                            <li><a href="#torneos/lista" class="menu-enlace">Ver y Configurar</a></li>
                            <li><a href="#torneos/crear" class="menu-enlace">Crear Torneo Oficial</a></li>
                            <li><a href="#torneos/historial" class="menu-enlace">Historial de Torneos</a></li>
                        </ul>
                    </li>

                    <li><a href="#" class="menu-enlace">Resultados Globales</a>
                        <ul class="menu-sublista">
                            <li><a href="#resultados/lista" class="menu-enlace">Ver Todos</a></li>
                            <li><a href="#resultados/formulario" class="menu-enlace">Registrar o Corregir</a></li>
                        </ul>
                    </li>

                    <!-- 3. CONFIGURACIÓN DEL SISTEMA -->
                    <li><a href="#" class="menu-enlace">Configuración del Sistema</a>
                        <ul class="menu-sublista">
                            <li><a href="#roles/lista" class="menu-enlace">Roles y Permisos</a></li>
                            <li><a href="#comunicaciones/enviar" class="menu-enlace">Enviar Alertas Globales</a></li>
                            <li><a href="#auditoria/logs" class="menu-enlace">Logs de Auditoría</a></li>
                        </ul>
                    </li>

                </ul>
            </nav>
        </aside>

        <main class="area-contenido" id="area-contenido">
<!--Aca le vamos a dar la bienvenida al administrador con el nombre que traiga de la base de datos-->
           <h1 class="titulo-pantalla">
                Bienvenido, <?php echo $_SESSION['usuario_nombre']; ?>!
           </h1>

           <!-- Aca inyectamos las tarjetas y tablas de la vista inicio.php-->
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

    <script src="<?php echo URL_BASE; ?>js/admin/reportes.js"></script>
    <script src="<?php echo URL_BASE; ?>js/admin/admin.js"></script>
    <!-- Script exclusivo de maquetación (simula el router y el backend) -->
    <!-- TODO: Eliminar cuando se integre el backend PHP -->
    <script src="<?php echo URL_BASE; ?>js/admin/mockup-router.js"></script>
</body>

</html>

