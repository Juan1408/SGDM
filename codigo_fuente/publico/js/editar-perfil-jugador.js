"use strict";

/**
 * Pagina "Editar perfil" del jugador.
 * Solo muestra una vista previa del banner y la foto que se eligen; los archivos se suben al guardar.
 */
document.addEventListener("DOMContentLoaded", () => {

   const MAX_BYTES = 2 * 1024 * 1024;

   function conectarVistaPrevia(idInput, idImagen) {
      const input = document.getElementById(idInput);
      const imagen = document.getElementById(idImagen);
      if (!input || !imagen) return;

      input.addEventListener("change", () => {
         const archivo = input.files && input.files[0];
         if (!archivo) return;

         if (archivo.size > MAX_BYTES) {
            alert("La imagen supera los 2 MB. Elegí una más liviana.");
            input.value = "";
            return;
         }

         const lector = new FileReader();
         lector.onload = () => { imagen.src = lector.result; };
         lector.readAsDataURL(archivo);
      });
   }

   conectarVistaPrevia("input-banner", "vista-banner");
   conectarVistaPrevia("input-avatar", "vista-avatar");
});
