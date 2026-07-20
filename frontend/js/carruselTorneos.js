// =====================
// FEATURED GAMES (home)
// =====================
const games = [
   { title: "Valorant", category: "eSports", text: "Torneos competitivos por equipos, con partidas rápidas, rankings y resultados.", img: "img/juegos/valorant.jpg" },
   { title: "Ajedrez", category: "Mental", text: "Competencias mentales para jugadores estratégicos.", img: "img/juegos/chess.jpg" },
   { title: "UNO", category: "Mesa", text: "Torneos de juegos de mesa y cartas para competir de forma simple y divertida.", img: "img/torneos/uno.jpeg" },
   { title: "Rocket League", category: "eSports", text: "Competencias rápidas de autos y fútbol.", img: "img/juegos/fly.jpg" },
   { title: "CS2", category: "eSports", text: "Torneos tácticos de precisión y estrategia.", img: "img/torneos/bn.jpg" }
];

let currentGame = 0;
const title = document.getElementById("showcaseTitle");
const text = document.getElementById("showcaseText");
const category = document.getElementById("gameCategory");
const gameSlides = document.querySelectorAll(".game-slide");

function updateGamesCarousel() {
   const leftIndex = (currentGame - 1 + games.length) % games.length;
   const rightIndex = (currentGame + 1) % games.length;
   const visibleGames = [games[leftIndex], games[currentGame], games[rightIndex]];
   const classes = ["game-slide side left", "game-slide active", "game-slide side right"];
   gameSlides.forEach((slide, index) => {
      slide.className = classes[index];
      slide.querySelector("img").src = visibleGames[index].img;
      slide.querySelector("img").alt = visibleGames[index].title;
      slide.querySelector("h4").textContent = visibleGames[index].title;
   });
   title.textContent = games[currentGame].title;
   text.textContent = games[currentGame].text;
   category.textContent = games[currentGame].category;
}

if (gameSlides.length > 0) {
   gameSlides[0].addEventListener("click", () => { currentGame = (currentGame - 1 + games.length) % games.length; updateGamesCarousel(); });
   gameSlides[2].addEventListener("click", () => { currentGame = (currentGame + 1) % games.length; updateGamesCarousel(); });
   updateGamesCarousel();
}

// =====================
// CARRUSEL JUEGOS DESTACADOS
// =====================
const track = document.getElementById('games-track');
const prevBtn = document.getElementById('prev-btn');
const nextBtn = document.getElementById('next-btn');

if (track && prevBtn && nextBtn) {
   const cardWidth = 190 + 28;
   const VISIBLE = 4;
   let current = 0;

   function totalCards() { return track.children.length; }

   function update() {
      const max = totalCards() - VISIBLE;
      current = Math.max(0, Math.min(current, max));
      track.style.transform = `translateX(-${current * cardWidth}px)`;
      prevBtn.disabled = current === 0;
      nextBtn.disabled = current >= max;
   }

   prevBtn.addEventListener('click', () => { current--; update(); });
   nextBtn.addEventListener('click', () => { current++; update(); });
   window.addEventListener('resize', update);
   update();
}

// =====================
// RANKINGS
// =====================
const jugadores = [
  { pos: 1, nombre: "Joaquín Silva",    iniciales: "JS", juego: "League of Legends", partidas: 38, puntos: 3920 },
  { pos: 2, nombre: "Maxi Rodríguez",   iniciales: "MR", juego: "Valorant",          partidas: 34, puntos: 2840 },
  { pos: 3, nombre: "Lucía Fontana",    iniciales: "LF", juego: "Ajedrez",           partidas: 29, puntos: 2610 },
  { pos: 4, nombre: "Pedro Castro",     iniciales: "PC", juego: "Counter Strike",    partidas: 31, puntos: 2310 },
  { pos: 5, nombre: "Valentina Acosta", iniciales: "VA", juego: "Apex Legends",      partidas: 27, puntos: 2105 },
  { pos: 6, nombre: "Nicolás Méndez",   iniciales: "NM", juego: "World of Warcraft", partidas: 22, puntos: 1890 }
];

const equipos = [
  { pos: 1, nombre: "Dragones FC",   iniciales: "DF", juego: "League of Legends", partidas: 20, puntos: 5200 },
  { pos: 2, nombre: "Halcones Pro",  iniciales: "HP", juego: "Valorant",          partidas: 18, puntos: 4750 },
  { pos: 3, nombre: "Torres Negras", iniciales: "TN", juego: "Ajedrez",           partidas: 15, puntos: 4100 },
  { pos: 4, nombre: "Equipo Alfa",   iniciales: "EA", juego: "Counter Strike",    partidas: 17, puntos: 3890 },
  { pos: 5, nombre: "Lobos eSports", iniciales: "LE", juego: "Apex Legends",      partidas: 14, puntos: 3420 },
  { pos: 6, nombre: "Clan Estela",   iniciales: "CE", juego: "World of Warcraft", partidas: 12, puntos: 3010 }
];

function medalClass(pos) {
  if (pos === 1) return "top3 gold";
  if (pos === 2) return "top3 silver";
  if (pos === 3) return "top3 bronze";
  return "";
}

function renderTable(data) {
  const body = document.getElementById("ranking-body");
  if (!body) return;
  body.innerHTML = data.map(item => `
    <div class="ranking-row">
      <span class="rk-pos ${medalClass(item.pos)}">${item.pos}</span>
      <div class="rk-player">
        <div class="rk-avatar">${item.iniciales}</div>
        <span>${item.nombre}</span>
      </div>
      <span class="rk-game">${item.juego}</span>
      <span class="rk-encuentros">${item.partidas}</span>
      <span class="rk-points">${item.puntos.toLocaleString("es-UY")}</span>
    </div>
  `).join("");
}

document.addEventListener("DOMContentLoaded", () => {
  const rkTabs = document.querySelectorAll(".rk-tab");
  const colName = document.getElementById("rk-col-name");

  if (rkTabs.length > 0) {
    rkTabs.forEach(tab => {
      tab.addEventListener("click", () => {
        rkTabs.forEach(t => t.classList.remove("active"));
        tab.classList.add("active");
        if (tab.dataset.tab === "equipos") {
          renderTable(equipos);
          if (colName) colName.textContent = "Equipo";
        } else {
          renderTable(jugadores);
          if (colName) colName.textContent = "Jugador";
        }
      });
    });
    renderTable(jugadores);
  }
});