<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>ASCEND - Crear Cuenta</title>

   <!-- CSS -->
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/publico/login.css">

   <!-- Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

   <!-- Google Fonts -->
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

  <main class="login-contenedor">
    <div class="login-panel">
      <div class="formulario-contenido active" id="formulario-registro">
        
        <a href="<?php echo URL_BASE; ?>index.html" class="btn-volver-inicio"><i class="fa-solid fa-arrow-left"></i> Volver al Inicio</a>
        <div class="formulario-logo">
          <a href="<?php echo URL_BASE; ?>index.html">
            <img src="<?php echo URL_BASE; ?>img/logos/ascend-png.png" alt="Logo de ASCEND">
          </a>
        </div>
        <h1>Crear Cuenta</h1>

        <?php if (!empty($error)): ?>
          <div class="alerta alerta-error">
            <i class="fa-solid fa-triangle-exclamation"></i> <?php echo $error; ?>
          </div>
        <?php endif; ?>

        <form action="<?php echo URL_BASE; ?>index.php?c=auth&a=procesarRegistro" method="POST">
          <div class="input-group">
            <input type="text" name="nombre_completo" id="registro-nombre" required value="<?php echo htmlspecialchars($_POST['nombre_completo'] ?? ''); ?>">
            <label for="registro-nombre">Nombre completo</label>
          </div>

          <fieldset class="role-toggle">
            <legend>Tipo de Cuenta / Rol</legend>
            <div class="role-options">
              <input type="radio" name="rol" id="rol-jugador" value="jugador" <?php echo (!isset($_POST['rol']) || $_POST['rol'] === 'jugador') ? 'checked' : ''; ?>>
              <label for="rol-jugador"><i class="fa-solid fa-user"></i> Jugador</label>
              
              <input type="radio" name="rol" id="rol-organizador" value="organizador" <?php echo (isset($_POST['rol']) && $_POST['rol'] === 'organizador') ? 'checked' : ''; ?>>
              <label for="rol-organizador"><i class="fa-solid fa-user-shield"></i> Organizador</label>
            </div>
          </fieldset>

          <div class="input-group">
            <input type="email" name="email" id="registro-email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            <label for="registro-email">Correo electrónico</label>
          </div>

          <div class="input-group">
            <input type="text" name="telefono" id="registro-telefono" value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>">
            <label for="registro-telefono">Teléfono / Celular (Opcional)</label>
          </div>

          <div class="input-group password-group">
            <input type="password" name="contrasena" id="registro-password" required>
            <label for="registro-password">Contraseña (Mínimo 8 caracteres)</label>
            <button type="button" class="btn-mostrar-contrasena" aria-label="Mostrar contraseña">
              <i class="fa-regular fa-eye-slash"></i>
            </button>
          </div>

          <div class="input-group password-group">
            <input type="password" name="contrasena_confirmar" id="registro-password-confirmar" required>
            <label for="registro-password-confirmar">Confirmar contraseña</label>
            <button type="button" class="btn-mostrar-contrasena" aria-label="Mostrar contraseña">
              <i class="fa-regular fa-eye-slash"></i>
            </button>
          </div>

          <button type="submit" class="auth-btn">Registrarse</button>
        </form>

        <p class="switch-text">¿Ya tienes cuenta? <a href="<?php echo URL_BASE; ?>index.php?c=auth&a=mostrarLogin">Iniciar sesión</a></p>
      </div>
    </div>
  </main>

  <script src="<?php echo URL_BASE; ?>js/login.js"></script>
</body>
</html>
