<!DOCTYPE html>
<html lang="es">

<head>

   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title>ASCEND - Login</title>

   <!-- CSS -->
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/publico/login.css">

   <!-- Font Awesome -->
   <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
   />

   <!-- Google Fonts -->
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

</head>

<body>

  <main class="login-contenedor">

  <!-- PANEL DEL FORMULARIO -->
  <div class="login-panel">

    <!-- LOGIN -->
    <div class="formulario-contenido active" id="login-form">
      
      <a href="<?php echo URL_BASE; ?>index.html" class="btn-volver-inicio"><i class="fa-solid fa-arrow-left"></i> Volver al Inicio</a>
      <div class="formulario-logo">
        <a href="<?php echo URL_BASE; ?>index.html">
          <img src="<?php echo URL_BASE; ?>img/logos/ascend-png.png" alt="Logo de ASCEND">
        </a>
      </div>
      <h1>Iniciar Sesión</h1>

      <!--Muestra el error de login si existe-->
      <?php if (isset($error)): ?>
        <?php echo $error; ?>
        <?php endif; ?>
      
      <!--Formulario de login-->
      <form action="<?php echo URL_BASE; ?>index.php?c=auth&a=procesarLogin" method="POST">
        <div class="input-group">
          <input name="email" type="email" id="login-email" required>
          <label for="login-email">Correo electrónico</label>
        </div>
        <div class="input-group password-group">
          <input name="contrasena" type="password" id="login-password" required>
          <label for="login-password">Contraseña</label>
          <button type="button" class="btn-mostrar-contrasena" aria-label="Mostrar contraseña">
            <i class="fa-regular fa-eye-slash"></i>
          </button>
        </div>
        <div class="forgot-password">
          <a href="<?php echo URL_BASE; ?>recuperar-password.html">¿Olvidaste tu contraseña?</a>
        </div>
        <button type="submit" class="auth-btn">Ingresar</button>
      </form>
      <p class="switch-text">¿No tienes cuenta? <a href="<?php echo URL_BASE; ?>registro.html">Registrarse</a></p>
    </div>

  </div>
</main>

   <script src="<?php echo URL_BASE; ?>js/login.js"></script>

</body>

</html>
