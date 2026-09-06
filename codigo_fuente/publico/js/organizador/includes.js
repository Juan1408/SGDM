// Carga navbar.html y sidebar.html en cada página,
// y avisa cuando terminó para que sidebar.js pueda enganchar sus eventos.

document.addEventListener('DOMContentLoaded', () => {
   const cargarPartial = async (selector, ruta) => {
      const contenedor = document.querySelector(selector);
      if (!contenedor) return;

      try {
         const res = await fetch(ruta);
         const html = await res.text();
         contenedor.innerHTML = html;
      } catch (err) {
         console.error(`Error cargando ${ruta}:`, err);
      }
   };

   Promise.all([
      cargarPartial('#navbar-placeholder', '/ejerciciosUTU/SGDM-feat-backend-mvc/codigo_fuente/vistas/organizador/navbar.html'),
      cargarPartial('#sidebar-placeholder', '/ejerciciosUTU/SGDM-feat-backend-mvc/codigo_fuente/vistas/organizador/sidebar.html')
   ]).then(() => {
      // avisamos que los partials ya están en el DOM
      document.dispatchEvent(new Event('partialsListos'));
   });
});


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
