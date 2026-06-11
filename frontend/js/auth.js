const loginForm = document.getElementById('login-form');
const registerForm = document.getElementById('register-form');
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

   document.getElementById('recovery-step-1').style.display = 'block';
   document.getElementById('recovery-step-2').style.display = 'none';
   document.getElementById('recovery-step-3').style.display = 'none';

   recoveryForm.classList.add('active');
}

function showRecoveryCode() {
   document.getElementById('recovery-step-1').style.display = 'none';
   document.getElementById('recovery-step-2').style.display = 'block';
   document.getElementById('recovery-step-3').style.display = 'none';
}

function showRecoveryPassword() {
   document.getElementById('recovery-step-1').style.display = 'none';
   document.getElementById('recovery-step-2').style.display = 'none';
   document.getElementById('recovery-step-3').style.display = 'block';
}
