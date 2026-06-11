const menuToggle = document.getElementById('menu-toggle');
const navLinks = document.getElementById('nav-links');

menuToggle.addEventListener('click', () => {
   navLinks.classList.toggle('active');
});

/*  DROPDOWN  */

const dropdown = document.querySelector(".dropdown");
const dropdownBtn = document.getElementById("tournamentsDropdown");

dropdownBtn.addEventListener("click", (e) => {
   e.stopPropagation();
   dropdown.classList.toggle("open");
});

document.addEventListener("click", (e) => {
   if (!dropdown.contains(e.target)) {
      dropdown.classList.remove("open");
   }
});