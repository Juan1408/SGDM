<?php if(false): /* ARCHIVO DESHABILITADO TEMPORALMENTE (Para programar desde cero) */ ?>
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
<aside class="barra-lateral">
    <nav class="navegacion-admin">
        <ul>
            <li>
                <a href="index.php?c=admin&a=dashboard">
                    📊 <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?php echo URL_BASE; ?>index.php?c=usuario&a=index" class="nav-link">
                    👥 <span>Usuarios</span>
                </a>
            </li>
            <li>
                <a href="index.php?c=rol&a=index">
                    🛡️ <span>Roles y Permisos</span>
                </a>
            </li>
            <li>
                <a href="index.php?c=juego&a=index">
                    🎮 <span>Disciplinas</span>
                </a>
            </li>
            <li>
                <a href="index.php?c=torneoAdmin&a=index">
                    🏆 <span>Torneos</span>
                </a>
            </li>
            <li>
                <a href="index.php?c=auditoria&a=accesos">
                    📋 <span>Logs y Auditoría</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
<main class="area-trabajo">

<?php endif; ?>
