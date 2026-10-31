/*
=====================================================
   UI GLOBAL (se carga desde los tres navbars)
   1) Boton violeta "Volver arriba"
   2) Mensajes de confirmacion/error centrados (.mensaje-exito / .mensaje-error)
=====================================================
*/
(function () {
   if (window.__ascendUiGlobal) { return; }
   window.__ascendUiGlobal = true;

   document.addEventListener("DOMContentLoaded", function () {

      /* ---- Boton "Volver arriba" ---- */
      if (!document.getElementById("boton-volver-arriba")) {
         var boton = document.createElement("button");
         boton.type = "button";
         boton.id = "boton-volver-arriba";
         boton.setAttribute("aria-label", "Volver arriba");
         boton.innerHTML = '<i class="fa-solid fa-arrow-up"></i>';
         document.body.appendChild(boton);

         boton.addEventListener("click", function () {
            window.scrollTo({ top: 0, behavior: "smooth" });
         });

         var actualizarBoton = function () {
            boton.classList.toggle("visible", window.scrollY > 400);
         };
         window.addEventListener("scroll", actualizarBoton, { passive: true });
         actualizarBoton();
      }

      /* ---- Mensajes centrados que se van solos ---- */
      document.querySelectorAll(".mensaje-exito, .mensaje-error").forEach(function (mensaje) {
         mensaje.setAttribute("role", mensaje.classList.contains("mensaje-error") ? "alert" : "status");
         mensaje.title = "Tocá para cerrar";

         var cerrar = function () {
            mensaje.classList.add("mensaje-saliendo");
            setTimeout(function () { mensaje.remove(); }, 400);
         };

         mensaje.addEventListener("click", cerrar);
         setTimeout(cerrar, mensaje.classList.contains("mensaje-error") ? 8000 : 5000);
      });
   });
})();