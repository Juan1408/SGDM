<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?? 'Registro - SGDM'; ?></title>
    <link rel="stylesheet" href="/publico/css/style.css">
</head>
<body>
    <div class="contenedor-pantalla">
        <div class="tarjeta-autenticacion">
            <div class="cabecera-autenticacion">
                <h1>Crear Cuenta</h1>
                <p>Únete a la plataforma de gestión deportiva</p>
            </div>

            <!-- Listado de errores de validación -->
            <?php if (isset($errores) && !empty($errores)): ?>
                <div class="alerta alerta-error">
                    <ul style="list-style-position: inside; padding-left: 0; margin: 0;">
                        <?php foreach ($errores as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="/registro" method="POST">
                <input type="hidden" name="token_csrf" value="<?php echo htmlspecialchars($token_csrf); ?>">

                <div class="grupo-formulario">
                    <label for="nombre_completo">Nombre Completo</label>
                    <input type="text" id="nombre_completo" name="nombre_completo" class="control-formulario" placeholder="Juan Pérez" required value="<?php echo htmlspecialchars($datos['nombre_completo'] ?? ''); ?>">
                </div>

                <div class="grupo-formulario">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" class="control-formulario" placeholder="ejemplo@correo.com" required value="<?php echo htmlspecialchars($datos['email'] ?? ''); ?>">
                </div>

                <div class="grupo-formulario">
                    <label for="contrasena">Contraseña</label>
                    <input type="password" id="contrasena" name="contrasena" class="control-formulario" placeholder="Mínimo 8 caracteres, números y letras" required>
                </div>

                <div class="grupo-formulario">
                    <label for="telefono">Teléfono (Opcional)</label>
                    <input type="tel" id="telefono" name="telefono" class="control-formulario" placeholder="+598 99 123 456" value="<?php echo htmlspecialchars($datos['telefono'] ?? ''); ?>">
                </div>

                <div class="grupo-formulario">
                    <label for="fecha_nacimiento">Fecha de Nacimiento (Opcional)</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="control-formulario" value="<?php echo htmlspecialchars($datos['fecha_nacimiento'] ?? ''); ?>">
                </div>

                <button type="submit" class="boton-primario">Registrarse</button>
            </form>

            <div class="enlace-accion">
                <p>¿Ya tienes una cuenta? <a href="/login">Inicia sesión aquí</a></p>
            </div>
        </div>
    </div>
</body>
</html>
