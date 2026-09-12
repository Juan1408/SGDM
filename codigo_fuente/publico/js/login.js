document.addEventListener('DOMContentLoaded', function () {
   const botonesMostrarContrasena = document.querySelectorAll('.btn-mostrar-contrasena');

   botonesMostrarContrasena.forEach(boton => {
      boton.addEventListener('click', function () {
         const campoContrasena = this.closest('.password-group').querySelector('input');
         
         if (campoContrasena) {
            const visible = campoContrasena.type === 'text';
            campoContrasena.type = visible ? 'password' : 'text';
            
            const icono = this.querySelector('i');
            if (icono) {
               icono.classList.toggle('fa-eye', !visible);
               icono.classList.toggle('fa-eye-slash', visible);
            }
         }
      });
   });
});
