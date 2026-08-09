<?php
/**
 * ============================================================================
 * PLANTILLA MODULAR: cabecera.php
 * ============================================================================
 * Propósito: Define el inicio del documento HTML, las etiquetas <head>,
 *            hojas de estilo CSS globales y la barra superior del Admin.
 * Ubicación: codigo_fuente/vistas/admin/plantilla/cabecera.php
 * ============================================================================
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tituloPagina ?? 'Administración - ASCEND') ?></title>
    <!-- Estilos CSS Modulares del Administrador -->
    <link rel="stylesheet" href="css/admin/admin.css">
    <link rel="stylesheet" href="css/admin/base.css">
    <link rel="stylesheet" href="css/admin/layout.css">
    <link rel="stylesheet" href="css/admin/componentes.css">
    <link rel="stylesheet" href="css/admin/utilidades.css">
</head>
<body class="cuerpo-admin">
    <div class="contenedor-admin">
        <!-- Barra de Navegación Superior -->
        <header class="cabecera-admin">
            <div class="logo-admin">
                <h2>ASCEND <span>Panel de Control</span></h2>
            </div>
            <div class="usuario-sesion">
                <span>👤 <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Administrador') ?></span>
                <a href="index.php?c=auth&a=logout" class="enlace-logout">Cerrar Sesión</a>
            </div>
        </header>
        <div class="cuerpo-principal">
