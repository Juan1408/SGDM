document.addEventListener('DOMContentLoaded', function () {
   // Evita el doble envío del registro: dos POST simultáneos podían crear la
   // cuenta en uno y mostrar "ya registrado" en el otro, como si hubiera fallado.
   const formularioRegistro = document.getElementById('formulario-registro');
   if (formularioRegistro) {
      let registroEnviado = false;
      formularioRegistro.addEventListener('submit', function (evento) {
         if (registroEnviado) {
            evento.preventDefault();
            return;
         }
         registroEnviado = true;

         const boton = formularioRegistro.querySelector('button[type="submit"]');
         if (boton) {
            boton.disabled = true;
            boton.textContent = 'Registrando...';
         }

         // Si la red falla y la página no navega, se permite reintentar.
         window.setTimeout(function () {
            registroEnviado = false;
            if (boton) {
               boton.disabled = false;
               boton.textContent = 'Registrarse';
            }
         }, 15000);
      });
   }

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
