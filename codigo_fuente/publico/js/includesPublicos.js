"use strict";

/*
=====================================================
   CARGA DE COMPONENTES PÚBLICOS
=====================================================
*/

document.addEventListener("DOMContentLoaded", () => {
   cargarNavbarPublico();
   cargarFooterPublico();
});


/*
=====================================================
   NAVBAR
=====================================================
*/

async function cargarNavbarPublico() {
   const contenedor = document.getElementById(
      "navbar-publico-placeholder"
   );

   if (!contenedor) {
      return;
   }

   try {
      const respuesta = await fetch(
         "../componentes/navbar-publico.html"
      );

      if (!respuesta.ok) {
         throw new Error(
            `Error HTTP ${respuesta.status}`
         );
      }

      contenedor.innerHTML =
         await respuesta.text();

      /*
         El navbar ya está en el DOM.
         Ahora navbar.js puede conectar sus eventos.
      */
      document.dispatchEvent(
         new Event("navbarPublicoListo")
      );

   } catch (error) {
      console.error(
         "Error cargando el navbar público:",
         error
      );
   }
}


/*
=====================================================
   FOOTER
=====================================================
*/

async function cargarFooterPublico() {
   const contenedor = document.getElementById(
      "footer-publico-placeholder"
   );

   if (!contenedor) {
      return;
   }

   try {
      const respuesta = await fetch(
         "../componentes/footer.html"
      );

      if (!respuesta.ok) {
         throw new Error(
            `Error HTTP ${respuesta.status}`
         );
      }

      contenedor.innerHTML =
         await respuesta.text();

   } catch (error) {
      console.error(
         "Error cargando el footer público:",
         error
      );
   }
}
// --- LOGICA DE MODO ADMINISTRADOR (VER COMO) ---
document.addEventListener('DOMContentLoaded', () => {
   const urlParams = new URLSearchParams(window.location.search);
   if (urlParams.get('admin') === 'true') {
      const btn = document.createElement('a');
      btn.href = '../admin/index.html';
      btn.innerHTML = '<i class="fa-solid fa-arrow-left"></i> Volver al Admin';
      btn.style.position = 'fixed';
      btn.style.bottom = '20px';
      btn.style.left = '20px';
      btn.style.backgroundColor = '#e11d48';
      btn.style.color = 'white';
      btn.style.padding = '10px 20px';
      btn.style.borderRadius = '50px';
      btn.style.textDecoration = 'none';
      btn.style.fontWeight = 'bold';
      btn.style.boxShadow = '0 4px 10px rgba(0,0,0,0.5)';
      btn.style.zIndex = '999999';
      btn.style.display = 'flex';
      btn.style.alignItems = 'center';
      btn.style.gap = '8px';
      document.body.appendChild(btn);
      
      // Preserve admin state across links inside the same module
      document.body.addEventListener('click', (e) => {
         const a = e.target.closest('a');
         if (a && a.href && !a.href.includes('admin=')) {
            // Only modify internal links
            if (a.href.startsWith(window.location.origin) || !a.href.startsWith('http')) {
               const url = new URL(a.href, window.location.href);
               url.searchParams.set('admin', 'true');
               a.href = url.href;
            }
         }
      });
   }
});
