// Ranking: el selector de disciplina recarga la pagina con ?juego=ID y las pestañas
// Jugadores / Equipos alternan entre dos paneles que ya genero PHP.
document.addEventListener("DOMContentLoaded", () => {
  const selector = document.getElementById("rk-juego");
  if (selector) {
    selector.addEventListener("change", () => selector.form.submit());
  }

  const tabs = document.querySelectorAll(".rk-tab[data-tab]");
  const panelJugadores = document.getElementById("rk-panel-jugadores");
  const panelEquipos = document.getElementById("rk-panel-equipos");
  if (!tabs.length || !panelJugadores || !panelEquipos) return;

  tabs.forEach(tab => {
    tab.addEventListener("click", () => {
      const verEquipos = tab.dataset.tab === "equipos";
      tabs.forEach(t => t.classList.remove("active"));
      tab.classList.add("active");
      panelJugadores.hidden = verEquipos;
      panelEquipos.hidden = !verEquipos;
    });
  });
});