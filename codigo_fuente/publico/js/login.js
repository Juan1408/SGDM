document.addEventListener('DOMContentLoaded', function () {
   const botonMostrarContrasena = document.querySelector('.btn-mostrar-contrasena');
   const campoContrasena = document.querySelector('#login-password');

   if (botonMostrarContrasena && campoContrasena) {
      botonMostrarContrasena.addEventListener('click', function () {
         const visible = campoContrasena.type === 'text';
         campoContrasena.type = visible ? 'password' : 'text';
         this.querySelector('i')?.classList.toggle('fa-eye', !visible);
         this.querySelector('i')?.classList.toggle('fa-eye-slash', visible);
      });
   }
});
