<?php
/**
 * ============================================================================
 * PLANTILLA MODULAR: menu_lateral.php
 * ============================================================================
 * Propósito: Renderiza la barra lateral con los enlaces dinámicos del Admin.
 * Ubicación: codigo_fuente/vistas/admin/plantilla/menu_lateral.php
 * ============================================================================
 */
?>
<aside class="menu-lateral">
    <nav>
        <ul class="menu-lista">
            <li><a href="<?php echo URL_BASE; ?>index.php?c=admin&a=dashboard" class="menu-enlace">Inicio</a></li>

            <!-- 1. GESTIÓN HUMANA -->
            <li><a href="#" class="menu-enlace">Gestión de Usuarios</a>
                <ul class="menu-sublista">
                    <li><a href="<?php echo URL_BASE; ?>index.php?c=usuario&a=index" class="menu-enlace">Usuarios
                            Administrativos</a></li>
                    <li><a href="<?php echo URL_BASE; ?>index.php?c=organizador&a=index" class="menu-enlace">Organizadores
                            Registrados</a></li>
                    <li><a href="<?php echo URL_BASE; ?>index.php?c=jugador&a=index" class="menu-enlace">Jugadores
                            Registrados</a></li>
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