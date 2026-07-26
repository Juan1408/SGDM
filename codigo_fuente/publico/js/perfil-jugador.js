"use strict";

document.addEventListener("DOMContentLoaded", () => {

   const esDuenioDelPerfil =
      localStorage.getItem("sesionActiva") === "jugador";

   const accionesEdicion = document.getElementById("perfil-acciones-edicion");
   const btnEditar = document.getElementById("btn-editar-perfil");
   const grupoGuardarCancelar = document.getElementById("grupo-guardar-cancelar");
   const btnGuardar = document.getElementById("btn-guardar-perfil");
   const btnCancelar = document.getElementById("btn-cancelar-edicion");

   const btnCambiarAvatar = document.getElementById("btn-cambiar-avatar");
   const inputAvatar = document.getElementById("input-avatar");
   const imgAvatar = document.getElementById("perfil-avatar-img");

   const btnCambiarBanner = document.getElementById("btn-cambiar-banner");
   const inputBanner = document.getElementById("input-banner");
   const bannerHeader = document.getElementById("perfil-banner");

   const camposEditables = [
      "perfil-nombre",
      "perfil-ubicacion",
      "perfil-email",
      "perfil-edad",
      "perfil-sobre-mi"
   ];

   if (!esDuenioDelPerfil || !accionesEdicion) {
      return; // visitante: perfil de solo lectura, sin botones
   }

   accionesEdicion.classList.remove("oculto");

   // --- Cargar datos guardados anteriormente ---
   const datosGuardados = localStorage.getItem("perfilJugadorDatos");
   if (datosGuardados) {
      try {
         const datos = JSON.parse(datosGuardados);

         camposEditables.forEach((id) => {
            const el = document.getElementById(id);
            if (el && datos[id] !== undefined) {
               el.textContent = datos[id];
            }
         });

         if (datos.avatarUrl && imgAvatar) {
            imgAvatar.src = datos.avatarUrl;
         }

         if (datos.bannerUrl && bannerHeader) {
            bannerHeader.style.backgroundImage =
               `linear-gradient(180deg, rgba(7,8,18,0.15), rgba(7,8,18,0.85)), url(${datos.bannerUrl})`;
         }
      } catch (e) {
         console.warn("No se pudieron cargar los datos del perfil guardados.");
      }
   }

   function activarModoEdicion() {
      camposEditables.forEach((id) => {
         const el = document.getElementById(id);
         if (el) {
            el.setAttribute("contenteditable", "true");
            el.classList.add("campo-en-edicion");
         }
      });

      btnCambiarAvatar.classList.remove("oculto");
      btnCambiarBanner.classList.remove("oculto");
      btnEditar.classList.add("oculto");
      grupoGuardarCancelar.classList.remove("oculto");
   }

   function desactivarModoEdicion() {
      camposEditables.forEach((id) => {
         const el = document.getElementById(id);
         if (el) {
            el.removeAttribute("contenteditable");
            el.classList.remove("campo-en-edicion");
         }
      });

      btnCambiarAvatar.classList.add("oculto");
      btnCambiarBanner.classList.add("oculto");
      btnEditar.classList.remove("oculto");
      grupoGuardarCancelar.classList.add("oculto");
   }

   function leerImagenComoURL(input, callback) {
      if (!input.files || !input.files[0]) return;
      const lector = new FileReader();
      lector.onload = () => callback(lector.result);
      lector.readAsDataURL(input.files[0]);
   }

   btnEditar.addEventListener("click", activarModoEdicion);

   inputAvatar.addEventListener("change", () => {
      leerImagenComoURL(inputAvatar, (url) => {
         imgAvatar.src = url;
      });
   });

   inputBanner.addEventListener("change", () => {
      leerImagenComoURL(inputBanner, (url) => {
         bannerHeader.style.backgroundImage =
            `linear-gradient(180deg, rgba(7,8,18,0.15), rgba(7,8,18,0.85)), url(${url})`;
      });
   });

   btnGuardar.addEventListener("click", () => {
      const datos = {};

      camposEditables.forEach((id) => {
         const el = document.getElementById(id);
         if (el) datos[id] = el.textContent.trim();
      });

      datos.avatarUrl = imgAvatar.src;

      const bannerActual = bannerHeader.style.backgroundImage;
      const match = bannerActual.match(/url\((.*)\)/);
      if (match) datos.bannerUrl = match[1].replace(/^["']|["']$/g, "");

      localStorage.setItem("perfilJugadorDatos", JSON.stringify(datos));
      desactivarModoEdicion();
      alert("Perfil actualizado (simulación).");
   });

   btnCancelar.addEventListener("click", () => {
      window.location.reload();
   });

});