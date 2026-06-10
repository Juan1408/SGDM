<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?? 'Roles y permisos - SGDM'; ?></title>
    <link rel="stylesheet" href="/publico/css/style.css">
</head>
<body>
    <?php
    $roles = $roles ?? [];
    $rol_actual = $rol_actual ?? null;
    require_once __DIR__ . '/../parciales/cabecera.php';
    ?>

    <main class="contenedor-pantalla" style="max-width: 1100px;">
        <div class="tarjeta-autenticacion" style="padding: 30px;">
            <h1 style="font-family: var(--fuente-titulo); font-size: 2rem; margin-bottom: 8px;">Módulo de roles y permisos</h1>
            <p style="color: var(--color-texto-secundario); margin-bottom: 18px;">Este módulo identifica los cuatro perfiles del sistema: público, participante, organizador y administrador.</p>

            <div class="alerta alerta-exito" style="margin-bottom: 18px;">
                Rol actual en sesión: <strong><?php echo htmlspecialchars((int)($rol_actual ?? 0)); ?></strong>
            </div>

            <section style="display:grid; gap:14px; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">
                <?php foreach ($roles as $rol): ?>
                    <article class="tarjeta-autenticacion" style="padding: 16px;">
                        <p style="text-transform: uppercase; letter-spacing: .12em; color: #93c5fd; font-size: .75rem; margin-bottom: 8px;">Nivel <?php echo (int)$rol->nivelPermiso; ?></p>
                        <h2 style="font-size: 1.05rem; margin-bottom: 6px;"><?php echo htmlspecialchars($rol->nombreRol); ?></h2>
                        <p style="color: var(--color-texto-secundario); font-size: .95rem; margin-bottom: 10px;"><?php echo htmlspecialchars($rol->descripcion ?? 'Sin descripción adicional.'); ?></p>
                        <ul style="list-style:none; display:grid; gap:6px; color: var(--color-texto-secundario); font-size: .92rem;">
                            <li>• Puede ver torneos y páginas públicas.</li>
                            <li>• Puede crear o administrar competiciones si su nivel lo permite.</li>
                            <li>• Puede acceder al panel de administración con permisos superiores.</li>
                        </ul>
                    </article>
                <?php endforeach; ?>
            </section>

            <div class="alerta alerta-exito" style="margin-top: 18px;">Este módulo queda preparado para extenderse con reglas de acceso reales por rol en la siguiente fase.</div>
        </div>
    </main>
</body>
</html>
