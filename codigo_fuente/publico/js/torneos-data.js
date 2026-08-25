const TORNEOS_DB = {

   "copa-ascend": {
      subtitulo: "Torneo de Rugby",
      tipoParticipacion: "equipos",
      equipo1: { nombre: "BLACK FERNS", logo: "../../publico/img/bf.png" },
      equipo2: { nombre: "WOMAN'S EAGLES", logo: "../../publico/img/eagles.png" },
      fechas: "26 Mayo 2026 - 08 Diciembre 2026",
      meta: { formato: "Eliminación directa", equipos: "32 Equipos", estado: "En curso" },
      descripcion: "Torneo competitivo de rugby entre equipos destacados. Los participantes competirán en una estructura organizada con fechas, encuentros, resultados y seguimiento dentro de ASCEND.",
      reglas: "Los equipos deberán respetar los horarios establecidos, el formato del torneo y las indicaciones del organizador. Los resultados serán cargados y validados dentro del sistema.",
      premios: { primero: "+ $200", segundo: "+ $100", tercero: "+ $50" },

      participantes: [
   { nombre: "Black Ferns Team", deporte: "Rugby", cantidad: "15 Participantes", puntos: "Puntos: 3200", imagen: "../../publico/img/black ferns/black-ferns_mobilell.png" },
   { nombre: "Woman's Eagles", deporte: "Rugby", cantidad: "15 Participantes", puntos: "Puntos: 2980", imagen: "../../publico/img/black ferns/black-ferns_mobilell.png" },
   { nombre: "Rugby Stars", deporte: "Rugby", cantidad: "15 Participantes", puntos: "Puntos: 2450", imagen: "../../publico/img/black ferns/black-ferns_mobilell.png" }
],

miembros: [
   { nombre: "Ayesha Leti", foto: "../../publico/img/black ferns/ayesha-leti.png" },
   { nombre: "Alana Borland", foto: "../../publico/img/black ferns/alana-borland.png" },
   { nombre: "Portia Woodman", foto: "../../publico/img/black ferns/portia-woodman.png" },
   { nombre: "Sylvia Brunt", foto: "../../publico/img/black ferns/sylvia-brunt.png" },
   { nombre: "Kate Henwood", foto: "../../publico/img/black ferns/kate-henwood.png" },
   { nombre: "Maiakawanak", foto: "../../publico/img/black ferns/maiakawanak.png" },
   { nombre: "Jorja Miller", foto: "../../publico/img/black ferns/jorja-miller.png" },
   { nombre: "Kennedy Tukuafu", foto: "../../publico/img/black ferns/kennedy-tukuafu.png" },
   { nombre: "Tanya Kalounivale", foto: "../../publico/img/black ferns/tanya-kalounivale.png" },
   { nombre: "Braxton Sorensen", foto: "../../publico/img/black ferns/braxton-sorensen.png" }
],

rankingEquipo: {
   logoEquipo: "../../publico/img/logos/BF.png",
   filas: [
      { jugadorFoto: "../../publico/img/black ferns/ayesha-leti.png", jugadorNombre: "Ayesha Leti", juego: "Rugby", partidas: 7, puntos: "3.000" },
      { jugadorFoto: "../../publico/img/black ferns/alana-borland.png", jugadorNombre: "Alana Borland", juego: "Rugby", partidas: 6, puntos: "2.800" },
      { jugadorFoto: "../../publico/img/black ferns/portia-woodman.png", jugadorNombre: "Portia Woodman", juego: "Rugby", partidas: 7, puntos: "2.750" },
      { jugadorFoto: "../../publico/img/black ferns/sylvia-brunt.png", jugadorNombre: "Sylvia Brunt", juego: "Rugby", partidas: 5, puntos: "2.400" }
   ]
},

      nombre: "Black Ferns vs Woman's Eagles",
      formato: "eliminacion_directa",
      totalJugadoresPorEquipo: 5,
      campeon: "Por definir",
      rondas: [
         {
            nombreRonda: "Octavos",
            partidos: [
               { id: 1, equipo1: "Fire Wolves", score1: 3, equipo2: "Cyber Titans", score2: 1, ganador: "Fire Wolves" },
               { id: 2, equipo1: "Nova Chess", score1: 0, equipo2: "Mental Squad", score2: 2, ganador: "Mental Squad" },
               { id: 3, equipo1: "Dragon Crew", score1: 2, equipo2: "Shadow Team", score2: 0, ganador: "Dragon Crew" },
               { id: 4, equipo1: "Neon Knights", score1: 1, equipo2: "Omega Squad", score2: 2, ganador: "Omega Squad" },
               { id: 5, equipo1: "Pixel Fox", score1: 2, equipo2: "Dark Lions", score2: 1, ganador: "Pixel Fox" },
               { id: 6, equipo1: "Aqua Team", score1: 0, equipo2: "Red Hawks", score2: 3, ganador: "Red Hawks" },
               { id: 7, equipo1: "Blue Core", score1: 1, equipo2: "Venom Club", score2: 2, ganador: "Venom Club" },
               { id: 8, equipo1: "Solar Rush", score1: 2, equipo2: "Iron Squad", score2: 0, ganador: "Solar Rush" }
            ]
         },
         {
            nombreRonda: "Cuartos",
            partidos: [
               { id: 9, equipo1: "Fire Wolves", score1: null, equipo2: "Mental Squad", score2: null, ganador: null },
               { id: 10, equipo1: "Dragon Crew", score1: null, equipo2: "Omega Squad", score2: null, ganador: null },
               { id: 11, equipo1: "Pixel Fox", score1: null, equipo2: "Red Hawks", score2: null, ganador: null },
               { id: 12, equipo1: "Venom Club", score1: null, equipo2: "Solar Rush", score2: null, ganador: null }
            ]
         },
         {
            nombreRonda: "Semifinales",
            partidos: [
               { id: 13, equipo1: "Por definir", score1: null, equipo2: "Por definir", score2: null, ganador: null },
               { id: 14, equipo1: "Por definir", score1: null, equipo2: "Por definir", score2: null, ganador: null }
            ]
         },
         {
            nombreRonda: "Gran Final",
            partidos: [
               { id: 15, equipo1: "Por definir", score1: null, equipo2: "Por definir", score2: null, ganador: null }
            ]
         }
      ],
      
      actividades: [
   { fecha: "26 Mayo 2026", hora: "18:00", tipo: "ceremonia", titulo: "Ceremonia de apertura" },
   { fecha: "26 Mayo 2026", hora: "19:00", tipo: "partido", titulo: "Inicio de Octavos de Final" },
   { fecha: "02 Junio 2026", hora: "18:00", tipo: "partido", titulo: "Cuartos de Final" },
   { fecha: "09 Junio 2026", hora: "18:00", tipo: "partido", titulo: "Semifinales" },
   { fecha: "20 Junio 2026", hora: "20:00", tipo: "premiacion", titulo: "Gran Final y entrega de premios" }
],

resultados: [
   {
      encuentro: "Fire Wolves vs Cyber Titans",
      marcador: "3 - 1",
      fecha: "26 Mayo 2026",
      detalles: [
         { tipo: "Goleador", valor: "Juan Pérez (2), Marco López (1)" },
         { tipo: "Posesión", valor: "Fire Wolves 58%" }
      ]
   },
   {
      encuentro: "Mental Squad vs Nova Chess",
      marcador: "2 - 0",
      fecha: "26 Mayo 2026",
      detalles: [
         { tipo: "MVP", valor: "Carla Suárez" }
      ]
   }
],
   },


   "ajedrez-master": {
      subtitulo: "Torneo de Ajedrez",
      tipoParticipacion: "individual",
      equipo1: { nombre: "MARÍA GONZÁLEZ", logo: "../../publico/img/avatars/a2.png" },
      equipo2: { nombre: "TOMÁS RIVERO", logo: "../../publico/img/avatars/a3.png" },
      fechas: "10 Junio 2026 - 15 Junio 2026",
      meta: { formato: "Sistema suizo", equipos: "16 Personas", estado: "Abierto" },
      descripcion: "Torneo individual de ajedrez bajo sistema suizo: todos los jugadores compiten la misma cantidad de rondas, enfrentándose contra rivales de puntaje similar en cada ronda.",
      reglas: "Cada ronda se empareja según el puntaje acumulado. Victoria = 1 punto, empate = 0.5 puntos, derrota = 0 puntos. El ranking final define al campeón.",
      premios: { primero: "+ $150", segundo: "+ $80", tercero: "+ $40" },

      participantes: [
   { nombre: "María González", deporte: "Ajedrez", cantidad: "1 Participante", puntos: "Puntos: 4.5", imagen: "../../publico/img/avatars/a2.png" },
   { nombre: "Tomás Rivero", deporte: "Ajedrez", cantidad: "1 Participante", puntos: "Puntos: 4.0", imagen: "../../publico/img/avatars/a3.png" },
   { nombre: "Lucía Fontana", deporte: "Ajedrez", cantidad: "1 Participante", puntos: "Puntos: 3.5", imagen: "../../publico/img/avatars/a1.png" }
],


rankingEquipo: {
   filas: [
      { jugadorFoto: "../../publico/img/avatars/a2.png", jugadorNombre: "María González", juego: "Ajedrez", partidas: 5, puntos: "4.5" },
      { jugadorFoto: "../../publico/img/avatars/a3.png", jugadorNombre: "Tomás Rivero", juego: "Ajedrez", partidas: 5, puntos: "4.0" },
      { jugadorFoto: "../../publico/img/avatars/a1.png", jugadorNombre: "Lucía Fontana", juego: "Ajedrez", partidas: 5, puntos: "3.5" }
   ]
},

      nombre: "Ajedrez Master",
      formato: "suizo",
      totalJugadoresPorEquipo: 1,
      campeon: "Por definir",
      tablaPosiciones: [
         { posicion: 1, equipo: "María González", PJ: 5, G: 4, P: 1, Puntos: 4.5 },
         { posicion: 2, equipo: "Tomás Rivero", PJ: 5, G: 4, P: 1, Puntos: 4.0 },
         { posicion: 3, equipo: "Lucía Fontana", PJ: 5, G: 3, P: 2, Puntos: 3.5 },
         { posicion: 4, equipo: "Pedro Castro", PJ: 5, G: 3, P: 2, Puntos: 3.5 },
         { posicion: 5, equipo: "Valentina Acosta", PJ: 5, G: 2, P: 3, Puntos: 2.5 },
         { posicion: 6, equipo: "Nicolás Méndez", PJ: 5, G: 1, P: 4, Puntos: 1.5 }
      ],
      
      actividades: [
   { fecha: "10 Junio 2026", hora: "10:00", tipo: "ceremonia", titulo: "Acreditación de jugadores" },
   { fecha: "10 Junio 2026", hora: "11:00", tipo: "partido", titulo: "Ronda 1" },
   { fecha: "11 Junio 2026", hora: "11:00", tipo: "partido", titulo: "Ronda 2" },
   { fecha: "12 Junio 2026", hora: "11:00", tipo: "partido", titulo: "Ronda 3" },
   { fecha: "15 Junio 2026", hora: "17:00", tipo: "premiacion", titulo: "Entrega de trofeos" }
],
   
rondas: [
   {
      numero: 1,
      partidos: [
         { blancas: "María González", negras: "Tomás Rivero", resultado: "1-0" },
         { blancas: "Lucía Fontana", negras: "Pedro Castro", resultado: "0-1" },
         { blancas: "Valentina Acosta", negras: "Nicolás Méndez", resultado: "1-0" }
      ]
   },
   {
      numero: 2,
      partidos: [
         { blancas: "María González", negras: "Lucía Fontana", resultado: "1-0" },
         { blancas: "Pedro Castro", negras: "Valentina Acosta", resultado: "½-½" },
         { blancas: "Tomás Rivero", negras: "Nicolás Méndez", resultado: "1-0" }
      ]
   },
   {
      numero: 3,
      partidos: [
         { blancas: "María González", negras: "Pedro Castro", resultado: "1-0" },
         { blancas: "Tomás Rivero", negras: "Valentina Acosta", resultado: "1-0" },
         { blancas: "Lucía Fontana", negras: "Nicolás Méndez", resultado: "0-1" }
      ]
   }
],

resultados: [
   {
      encuentro: "María González vs Tomás Rivero",
      marcador: "1 - 0",
      fecha: "10 Junio 2026",
      detalles: [
         { tipo: "Apertura", valor: "Defensa Siciliana" },
         { tipo: "Duración", valor: "42 jugadas" }
      ]
   },
   {
      encuentro: "Lucía Fontana vs Pedro Castro",
      marcador: "0 - 1",
      fecha: "10 Junio 2026",
      detalles: [
         { tipo: "Apertura", valor: "Gambito de Dama" }
      ]
   }
],

}

   ,

   "futbol-5": {
   subtitulo: "Torneo de Fútbol 5",
   tipoParticipacion: "equipos",
   equipo1: { nombre: "DURAZNO", logo: "../../publico/img/logos/durazno.png" },
   equipo2: { nombre: "FLORES", logo: "../../publico/img/logos/flores.png" },
   fechas: "16 Mayo 2026 - 20 Junio 2026",
   meta: { formato: "Liga", equipos: "12 Equipos", estado: "En curso" },
   descripcion: "Torneo de Fútbol 5 bajo formato de liga: todos los equipos se enfrentan entre sí a lo largo de la temporada, sumando puntos por cada partido ganado. El de más puntos al final es el campeón.",
   reglas: "Victoria = 3 puntos, empate = 1 punto, derrota = 0 puntos. Se juega una fecha por semana durante todo el mes. La tabla se actualiza después de cada jornada.",
   premios: { primero: "+ $200", segundo: "+ $100", tercero: "+ $50" },

   participantes: [
      { nombre: "Leones FC", deporte: "Fútbol 5", cantidad: "7 Participantes", puntos: "Puntos: 18", imagen: "../../publico/img/avatars/a1.png" },
      { nombre: "Atlético Norte", deporte: "Fútbol 5", cantidad: "7 Participantes", puntos: "Puntos: 15", imagen: "../../publico/img/avatars/a1.png" },
      { nombre: "Deportivo Sur", deporte: "Fútbol 5", cantidad: "7 Participantes", puntos: "Puntos: 12", imagen: "../../publico/img/avatars/a1.png" }
   ],

   miembros: [
      { nombre: "Nicolás Pereyra", foto: "../../publico/img/avatars/a1.png" },
      { nombre: "Bruno Castro", foto: "../../publico/img/avatars/a3.png" },
      { nombre: "Federico Silva", foto: "../../publico/img/avatars/a2.png" },
      { nombre: "Diego Martínez", foto: "../../publico/img/avatars/a1.png" },
      { nombre: "Agustín López", foto: "../../publico/img/avatars/a3.png" },
      { nombre: "Santiago Núñez", foto: "../../publico/img/avatars/a2.png" },
      { nombre: "Gonzalo Ramírez", foto: "../../publico/img/avatars/a1.png" }
   ],

   rankingEquipo: {
      logoEquipo: "../../publico/img/avatars/a1.png",
      filas: [
         { jugadorFoto: "../../publico/img/avatars/a1.png", jugadorNombre: "Nicolás Pereyra", juego: "Fútbol 5", partidas: 6, puntos: "18" },
         { jugadorFoto: "../../publico/img/avatars/a3.png", jugadorNombre: "Bruno Castro", juego: "Fútbol 5", partidas: 6, puntos: "15" },
         { jugadorFoto: "../../publico/img/avatars/a2.png", jugadorNombre: "Federico Silva", juego: "Fútbol 5", partidas: 6, puntos: "12" }
      ]
   },

   nombre: "Campeonato Futbol 5",
   formato: "liga",
   totalJugadoresPorEquipo: 7,
   campeon: "Por definir",
   tablaPosiciones: [
      { posicion: 1, equipo: "Leones FC", PJ: 6, G: 6, P: 0, Puntos: 18 },
      { posicion: 2, equipo: "Atlético Norte", PJ: 6, G: 5, P: 1, Puntos: 15 },
      { posicion: 3, equipo: "Deportivo Sur", PJ: 6, G: 4, P: 2, Puntos: 12 },
      { posicion: 4, equipo: "Racing Este", PJ: 6, G: 3, P: 3, Puntos: 9 },
      { posicion: 5, equipo: "Unión Oeste", PJ: 6, G: 2, P: 4, Puntos: 6 },
      { posicion: 6, equipo: "Sportivo Central", PJ: 6, G: 1, P: 5, Puntos: 3 }
   ],

   actividades: [
   { fecha: "16 Mayo 2026", hora: "19:00", tipo: "ceremonia", titulo: "Presentación de equipos" },
   { fecha: "23 Mayo 2026", hora: "19:00", tipo: "partido", titulo: "Fecha 1" },
   { fecha: "30 Mayo 2026", hora: "19:00", tipo: "partido", titulo: "Fecha 2" },
   { fecha: "13 Junio 2026", hora: "19:00", tipo: "pausa", titulo: "Semana libre" },
   { fecha: "20 Junio 2026", hora: "19:00", tipo: "premiacion", titulo: "Última fecha y premiación" }
],

resultados: [
   {
      encuentro: "Leones FC vs Atlético Norte",
      marcador: "4 - 2",
      fecha: "16 Mayo 2026",
      detalles: [
         { tipo: "Goleador", valor: "Nicolás Pereyra (3)" },
         { tipo: "Tarjetas", valor: "1 amarilla - Atlético Norte" }
      ]
   }
],

}
};