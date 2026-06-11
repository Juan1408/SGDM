const games = [
   {
      title: "Valorant",
      category: "eSports",
      text: "Torneos competitivos por equipos, con partidas rápidas, rankings y resultados.",
      img: "img/juegos/valorant.jpg"
   },
   {
      title: "Ajedrez",
      category: "Mental",
      text: "Competencias mentales para jugadores estratégicos.",
      img: "img/juegos/chess.jpg"
   },
   {
      title: "UNO",
      category: "Mesa",
      text: "Torneos de juegos de mesa y cartas para competir de forma simple y divertida.",
      img: "img/torneos/uno.jpeg"
   },
   {
      title: "Rocket League",
      category: "eSports",
      text: "Competencias rápidas de autos y fútbol.",
      img: "img/juegos/fly.jpg"
   },
   {
      title: "CS2",
      category: "eSports",
      text: "Torneos tácticos de precisión y estrategia.",
      img: "img/torneos/bn.jpg"
   }
];

let currentGame = 0;

const title = document.getElementById("showcaseTitle");
const text = document.getElementById("showcaseText");
const category = document.getElementById("gameCategory");
const slides = document.querySelectorAll(".game-slide");

function updateGamesCarousel() {
   const leftIndex = (currentGame - 1 + games.length) % games.length;
   const rightIndex = (currentGame + 1) % games.length;

   const visibleGames = [games[leftIndex], games[currentGame], games[rightIndex]];
   const classes = ["game-slide side left", "game-slide active", "game-slide side right"];

   slides.forEach((slide, index) => {
      slide.className = classes[index];
      slide.querySelector("img").src = visibleGames[index].img;
      slide.querySelector("img").alt = visibleGames[index].title;
      slide.querySelector("h4").textContent = visibleGames[index].title;
   });

   title.textContent = games[currentGame].title;
   text.textContent = games[currentGame].text;
   category.textContent = games[currentGame].category;
}

slides[0].addEventListener("click", () => {
   currentGame = (currentGame - 1 + games.length) % games.length;
   updateGamesCarousel();
});

slides[2].addEventListener("click", () => {
   currentGame = (currentGame + 1) % games.length;
   updateGamesCarousel();
});

updateGamesCarousel();