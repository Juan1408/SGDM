<?php
/**
 * Vista Parcial: cabecera.php
 * 
 * Barra de navegación superior compartida por todas las páginas autenticadas.
 * Muestra el nombre del usuario activo y enlaces de navegación principales.
 */
require_once __DIR__ . '/../../nucleo/AdministradorSesion.php';
AdministradorSesion::iniciar();

/**
 * @var string $nombreUsuario Nombre del usuario en sesión para mostrar en la barra.
 */
$nombreUsuario = $_SESSION['nombre_completo'] ?? 'Invitado';
?>
<nav class="barra-nav">
    <a href="/torneos" class="logo-nav">⚽ SGDM 2.0</a>
    <ul class="menu-nav">
        <li><a href="/torneos">Torneos</a></li>
        <li><a href="/equipos">Equipos</a></li>
        <li><a href="/roles">Roles</a></li>
    </ul>
    <div class="usuario-nav">
        <span>👤 <?php echo htmlspecialchars($nombreUsuario); ?></span>
        <a href="/logout" class="boton-logout">Salir</a>
    </div>
</nav>
