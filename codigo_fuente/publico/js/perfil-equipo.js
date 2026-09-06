document.addEventListener("DOMContentLoaded", () => {
   const btnVerEquipo = document.getElementById("btnVerEquipo");
   const btnVerTorneos = document.getElementById("btnVerTorneos");
   const btnAdministrar = document.getElementById("btnAdministrar");

   const modalAdminEquipo = document.getElementById("modalAdminEquipo");
   const cerrarModalEquipo = document.getElementById("cerrarModalEquipo");
   const cancelarAdmin = document.getElementById("cancelarAdmin");
   const formAdminEquipo = document.getElementById("formAdminEquipo");

   if (btnVerEquipo) {
      btnVerEquipo.addEventListener("click", () => {
         // El botón "Ver equipo" te lleva a la sección de miembros del equipo.
         window.location.href = "#miembros";
      });
   }

   if (btnVerTorneos) {
      btnVerTorneos.addEventListener("click", () => {
         // El botón "Ver torneos" te lleva a la vista pública de torneos
         window.location.href = "../publico/torneos.html";
      });
   }

   // Lógica del modal de administración
   if (btnAdministrar && modalAdminEquipo) {
      btnAdministrar.addEventListener("click", () => {
         modalAdminEquipo.style.display = "flex";
         modalAdminEquipo.setAttribute("aria-hidden", "false");
      });
   }

   if (cerrarModalEquipo && modalAdminEquipo) {
      cerrarModalEquipo.addEventListener("click", () => {
         modalAdminEquipo.style.display = "none";
         modalAdminEquipo.setAttribute("aria-hidden", "true");
      });
   }

   if (cancelarAdmin && modalAdminEquipo) {
      cancelarAdmin.addEventListener("click", () => {
         modalAdminEquipo.style.display = "none";
         modalAdminEquipo.setAttribute("aria-hidden", "true");
      });
   }

   // Cerrar modal al hacer click fuera del contenido
   window.addEventListener("click", (e) => {
      if (e.target === modalAdminEquipo) {
         modalAdminEquipo.style.display = "none";
         modalAdminEquipo.setAttribute("aria-hidden", "true");
      }
   });

   if (formAdminEquipo) {
      formAdminEquipo.addEventListener("submit", (e) => {
         e.preventDefault();
         alert("Cambios guardados con éxito.");
         modalAdminEquipo.style.display = "none";
      });
   }

   // Lógica de Agregar Miembro
   const btnAgregarMiembro = document.getElementById("btnAgregarMiembro");
   const inputNuevoMiembro = document.getElementById("inputNuevoMiembro");
   const listaMiembros = document.getElementById("listaMiembros");

   if (btnAgregarMiembro && inputNuevoMiembro && listaMiembros) {
      btnAgregarMiembro.addEventListener("click", () => {
         const nombre = inputNuevoMiembro.value.trim();
         if (nombre !== "") {
            const li = document.createElement("li");
            li.style.display = "flex";
            li.style.justifyContent = "space-between";
            li.style.alignItems = "center";
            li.style.padding = "5px 0";
            li.style.borderBottom = "1px solid #333";
            li.innerHTML = `
               <span>${nombre}</span>
               <button type="button" class="btnEliminarMiembro" style="background:none; border:none; color:var(--color-alerta); cursor:pointer;">&times;</button>
            `;
            listaMiembros.appendChild(li);
            inputNuevoMiembro.value = "";
         }
      });

      // Delegación de eventos para eliminar miembro
      listaMiembros.addEventListener("click", (e) => {
         if (e.target.classList.contains("btnEliminarMiembro")) {
            e.target.parentElement.remove();
         }
      });
   }
});
