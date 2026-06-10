<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?? 'Iniciar Sesión - SGDM'; ?></title>
    <link rel="stylesheet" href="/publico/css/style.css">
</head>
<body>
    <div class="contenedor-pantalla">
        <div class="tarjeta-autenticacion">
            <div class="cabecera-autenticacion">
                <h1>SGDM 2.0</h1>
                <p>Sistema de Gestión Deportiva Modular</p>
            </div>

            <!-- Mostrar mensaje de éxito si existe -->
            <?php if (isset($mensaje_exito)): ?>
                <div class="alerta alerta-exito">
                    <?php echo htmlspecialchars($mensaje_exito); ?>
                </div>
            <?php endif; ?>

            <!-- Mostrar mensaje de error si existe -->
            <?php if (isset($error)): ?>
                <div class="alerta alerta-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form action="/login" method="POST">
                <!-- Token CSRF para mitigar ataques de falsificación de peticiones en sitios cruzados -->
                <input type="hidden" name="token_csrf" value="<?php echo htmlspecialchars($token_csrf); ?>">

                <div class="grupo-formulario">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" class="control-formulario" placeholder="ejemplo@correo.com" required autocomplete="email">
                </div>

                <div class="grupo-formulario">
                    <label for="contrasena">Contraseña</label>
                    <input type="password" id="contrasena" name="contrasena" class="control-formulario" placeholder="••••••••" required autocomplete="current-password">
                </div>

                <button type="submit" class="boton-primario">Acceder al Sistema</button>
            </form>

            <div class="enlace-accion">
                <p>¿No tienes una cuenta? <a href="/registro">Regístrate aquí</a></p>
            </div>
        </div>
    </div>
</body>
</html>
