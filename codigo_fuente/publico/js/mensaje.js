const loginForm = document.getElementById('login-form');
const registerForm = document.getElementById('formulario-registro');
const recoveryForm = document.getElementById('recovery-form');

function hideAllForms() {
   loginForm.classList.remove('active');
   registerForm.classList.remove('active');
   recoveryForm.classList.remove('active');
}

function showLogin() {
   hideAllForms();
   loginForm.classList.add('active');
}

function showRegister() {
   hideAllForms();
   registerForm.classList.add('active');
}

function showRecovery() {
   hideAllForms();

   document.getElementById('recuperacion-paso-1').style.display = 'block';
   document.getElementById('recuperacion-paso-2').style.display = 'none';
   document.getElementById('recuperacion-paso-3').style.display = 'none';

   recoveryForm.classList.add('active');
}

function showRecoveryCode() {
   document.getElementById('recuperacion-paso-1').style.display = 'none';
   document.getElementById('recuperacion-paso-2').style.display = 'block';
   document.getElementById('recuperacion-paso-3').style.display = 'none';
}

function showRecoveryPassword() {
   document.getElementById('recuperacion-paso-1').style.display = 'none';
   document.getElementById('recuperacion-paso-2').style.display = 'none';
   document.getElementById('recuperacion-paso-3').style.display = 'block';
}


// =========================================================
// SIMULACI N DE LOGIN MULTI-ROL (MOCKUP)
// =========================================================
document.addEventListener("DOMContentLoaded", function () {
   const formLogin = document.querySelector("#login-form form");

   if (formLogin) {
      formLogin.addEventListener("submit", function (e) {
         e.preventDefault();

         const email = this.querySelector("input[type=\"email\"]").value.toLowerCase();
         // No validamos la contrase a en la simulaci n, solo el email

         if (email === "jugador@ascend.com") {
            window.location.href = "../jugador/perfil-jugador.html";
         } else if (email === "organizador@ascend.com") {
            window.location.href = "../organizador/dashboard.html";
         } else if (email === "admin@ascend.com") {
            // El admin ya est  estructurado en codigo_fuente
            window.location.href = "../../../codigo_fuente/vistas/admin/index.html";
         } else {
            alert("Credenciales incorrectas.\n\nUsuarios de prueba:\n- jugador@ascend.com\n- organizador@ascend.com\n- admin@ascend.com");
         }
      });
   }
});