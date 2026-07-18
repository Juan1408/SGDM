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
      cargarPartial('#navbar-placeholder', 'navbar.html'),
      cargarPartial('#sidebar-placeholder', 'sidebar.html')
   ]).then(() => {
      // avisamos que los partials ya están en el DOM
      document.dispatchEvent(new Event('partialsListos'));
   });
});

