"use strict";

/**
 * Previsualizacion de banner/escudo en "Crear equipo".
 * Solo mejora la experiencia: la validacion real (MIME, tamano y contenido)
 * la hace el servidor con SubidaImagen.
 */
document.addEventListener("DOMContentLoaded", () => {
   const MAX_BYTES = 2 * 1024 * 1024;
   const TIPOS = ["image/jpeg", "image/png", "image/webp"];

   const campos = [
      {
         input: "input-banner-crear",
         preview: "preview-banner-crear",
         error: "error-banner-crear",
         etiqueta: "banner",
      },
      {
         input: "input-escudo-crear",
         preview: "preview-escudo-crear",
         error: "error-escudo-crear",
         etiqueta: "escudo",
      },
   ];

   campos.forEach((campo) => {
      const input = document.getElementById(campo.input);
      const preview = document.getElementById(campo.preview);
      const error = document.getElementById(campo.error);
      if (!input || !preview || !error) return;

      const marcadorOriginal = preview.innerHTML;

      function mostrarError(texto) {
         error.textContent = texto;
         error.classList.remove("oculto");
      }

      function limpiarError() {
         error.textContent = "";
         error.classList.add("oculto");
      }

      function restaurarMarcador() {
         preview.innerHTML = marcadorOriginal;
      }

      input.addEventListener("change", () => {
         limpiarError();

         const archivo = input.files && input.files[0];
         if (!archivo) {
            restaurarMarcador();
            return;
         }

         if (!TIPOS.includes(archivo.type)) {
            mostrarError("Formato no válido. Elegí un archivo JPG, PNG o WebP.");
            input.value = "";
            restaurarMarcador();
            return;
         }

         if (archivo.size > MAX_BYTES) {
            mostrarError("La imagen supera el límite de 2 MB.");
            input.value = "";
            restaurarMarcador();
            return;
         }

         const url = URL.createObjectURL(archivo);
         const imagen = document.createElement("img");
         imagen.alt = "Previsualización del " + campo.etiqueta;
         imagen.src = url;
         imagen.addEventListener("load", () => URL.revokeObjectURL(url), { once: true });

         preview.innerHTML = "";
         preview.appendChild(imagen);
      });
   });
});
