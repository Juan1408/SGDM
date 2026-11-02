// =========================================================
// EFECTO GLITCH — copia el texto de cada ".efecto-glitch" en
// su atributo data-text (lo usan las capas de color del CSS)
// =========================================================
document.querySelectorAll(".efecto-glitch").forEach(function (el) {
   el.setAttribute("data-text", el.textContent.replace(/\s+/g, " ").trim());
});
