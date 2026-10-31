/**
 * ============================================================================
 * SCRIPT: tema.js
 * ============================================================================
 * Propósito: Gestiona el cambio de tema (Claro / Oscuro) en el Administrador.
 *            Persiste la selección en localStorage y cambia el ícono del botón.
 * ============================================================================
 */

(function() {
    const btnToggleTema = document.getElementById('btnToggleTema');
    
    if (!btnToggleTema) return;

    const iconTema = btnToggleTema.querySelector('i');
    const body = document.body;

    // Sincronizar el ícono inicial basado en localStorage
    if (localStorage.getItem('admin_tema') === 'dark') {
        iconTema.classList.remove('fa-moon');
        iconTema.classList.add('fa-sun');
        btnToggleTema.title = "Cambiar a Modo Claro";
    }

    // Evento de clic
    btnToggleTema.addEventListener('click', () => {
        // Animación de rotación 
        iconTema.style.transform = 'rotate(360deg)';
        
        // Timeout corto para resetear la animación
        setTimeout(() => {
            iconTema.style.transform = '';
            
            // Alternar tema
            if (body.classList.contains('dark-mode')) {
                // Cambiar a claro
                body.classList.remove('dark-mode');
                localStorage.setItem('admin_tema', 'light');
                iconTema.classList.remove('fa-sun');
                iconTema.classList.add('fa-moon');
                btnToggleTema.title = "Cambiar a Modo Oscuro";
            } else {
                // Cambiar a oscuro
                body.classList.add('dark-mode');
                localStorage.setItem('admin_tema', 'dark');
                iconTema.classList.remove('fa-moon');
                iconTema.classList.add('fa-sun');
                btnToggleTema.title = "Cambiar a Modo Claro";
            }
        }, 150); // Se cambia en mitad de la transición
    });
})();
