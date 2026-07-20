// =========================================================
// SIMULACIÓN DE LOGIN MULTI-ROL (MOCKUP)
// =========================================================
document.addEventListener('DOMContentLoaded', function () {
   const formLogin = document.querySelector('#login-form form');

   if (formLogin) {
      formLogin.addEventListener('submit', function (e) {
         e.preventDefault();

         const email = this.querySelector('input[type=\"email\"]').value.toLowerCase();
         // No validamos la contraseña en la simulación, solo el email

         if (email === 'jugador@ascend.com') {
            window.location.href = '../jugador/perfil-jugador.html';
         } else if (email === 'organizador@ascend.com') {
            window.location.href = '../organizador/dashboard.html';
         } else if (email === 'admin@ascend.com') {
            // El admin ya está estructurado en codigo_fuente
            window.location.href = '../../../codigo_fuente/vistas/admin/index.html';
         } else {
            alert('Credenciales incorrectas.\n\nUsuarios de prueba:\n- jugador@ascend.com\n- organizador@ascend.com\n- admin@ascend.com');
         }
      });
   }
});

