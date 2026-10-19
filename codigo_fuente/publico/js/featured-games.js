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
const track = document.getElementById('juegos-track');
const prevBtn = document.getElementById('prev-btn');
const nextBtn = document.getElementById('next-btn');

if (track && prevBtn && nextBtn) {
   const CARD_WIDTH = 190;
   const GAP = 28;
   const paso = CARD_WIDTH + GAP;
   const wrapper = track.parentElement; // .carrusel-track-wrapper
   let current = 0;

   function totalCards() { return track.children.length; }

   // Cuántas tarjetas entran en el ancho visible ahora mismo
   function visibles() {
      return Math.max(1, Math.floor((wrapper.clientWidth + GAP) / paso));
   }

   function update() {
      const max = Math.max(0, totalCards() - visibles());
      current = Math.max(0, Math.min(current, max));
      track.style.transform = `translateX(-${current * paso}px)`;
      prevBtn.disabled = current === 0;
      nextBtn.disabled = current >= max;
   }

   prevBtn.addEventListener('click', () => { current--; update(); });
   nextBtn.addEventListener('click', () => { current++; update(); });
   window.addEventListener('resize', update);
   update();
}

