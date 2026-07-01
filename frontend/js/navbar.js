const menuToggle = document.getElementById('menu-toggle');
const navLinks = document.getElementById('nav-links');
const dropdown = document.querySelector(".dropdown");
const dropdownBtn = document.getElementById("tournamentsDropdown");

// Abrir y cerrar el menú lateral + Animación de la hamburguesa
menuToggle.addEventListener('click', () => {
   menuToggle.classList.toggle('active');
   navLinks.classList.toggle('active');
});

// Control del Dropdown
dropdownBtn.addEventListener("click", (e) => {
   e.stopPropagation();
   dropdown.classList.toggle("open");
});

// Cerrar el dropdown al hacer clic en cualquier otra parte
document.addEventListener("click", (e) => {
   if (!dropdown.contains(e.target)) {
      dropdown.classList.remove("open");
   }
});


/**HERO TRANSICION* */

const slides = document.querySelectorAll(".hero-slide");

let actual = 0;

setInterval(() => {

   slides[actual].classList.remove("active");

   actual++;

   if (actual >= slides.length) {
      actual = 0;
   }

   slides[actual].classList.add("active");

}, 5000);