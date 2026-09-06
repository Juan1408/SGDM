"use strict";

document.addEventListener("DOMContentLoaded", () => {

   const CLAVE_EQUIPO = "equipoGestionado";

   const vistaCrear = document.getElementById("vista-crear-equipo");
   const vistaGestionar = document.getElementById("vista-gestionar-equipo");

   const equipoExistente = localStorage.getItem(CLAVE_EQUIPO);

   if (equipoExistente) {
      vistaCrear.classList.add("oculto");
      vistaGestionar.classList.remove("oculto");
      iniciarGestion(JSON.parse(equipoExistente));
   } else {
      vistaGestionar.classList.add("oculto");
      iniciarCreacion();
   }

   /*
   ================================================
      ESTADO 1: CREAR EQUIPO
   ================================================
   */

   function iniciarCreacion() {

      const form = document.getElementById("form-crear-equipo");

      const inputBanner = document.getElementById("input-banner-crear");
      const previewBannerImg = document.getElementById("preview-banner-crear-img");
      const placeholderBanner = document.querySelector("#preview-banner-crear .placeholder-texto");

      const inputEscudo = document.getElementById("input-escudo-crear");
      const previewEscudoImg = document.getElementById("preview-escudo-crear-img");
      const placeholderEscudo = document.querySelector("#preview-escudo-crear .placeholder-icono");

      inputBanner.addEventListener("change", () => {
         leerImagenComoURL(inputBanner, (url) => {
            previewBannerImg.src = url;
            previewBannerImg.hidden = false;
            if (placeholderBanner) placeholderBanner.hidden = true;
         });
      });

      inputEscudo.addEventListener("change", () => {
         leerImagenComoURL(inputEscudo, (url) => {
            previewEscudoImg.src = url;
            previewEscudoImg.hidden = false;
            if (placeholderEscudo) placeholderEscudo.hidden = true;
         });
      });

      function generarCodigoInvitacion() {
         const caracteres = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789"; // sin 0/O/1/I para no confundir
         let codigo = "";
         for (let i = 0; i < 6; i++) {
            codigo += caracteres.charAt(Math.floor(Math.random() * caracteres.length));
         }
         return `ASND-${codigo}`;
      }

      form.addEventListener("submit", (evento) => {
         evento.preventDefault();

         const nombreEquipo = document.getElementById("crear-nombre").value.trim();
         const juegoId = document.getElementById("crear-juego").value;
         const localidad = document.getElementById("crear-localidad").value.trim();
         const descripcion = document.getElementById("crear-descripcion").value.trim();

         if (!nombreEquipo || !juegoId) {
            alert("Completá el nombre del equipo y el juego antes de continuar.");
            return;
         }

         const equipoNuevo = {
            id: Date.now(),
            nombre_equipo: nombreEquipo,
            juego_id: juegoId,
            localidad: localidad,
            descripcion: descripcion,
            escudo_url: previewEscudoImg.hidden ? null : previewEscudoImg.src,
            banner: previewBannerImg.hidden ? null : previewBannerImg.src,
            codigo_invitacion: generarCodigoInvitacion(),
            creado_por: "vos",
            miembros: [
               { id: 1, nombre: "Vos", usuario: "@tu-usuario", avatar: "../../publico/img/avatars/a1.png", rol: "lider" }
            ],
            solicitudes: []
         };

         localStorage.setItem(CLAVE_EQUIPO, JSON.stringify(equipoNuevo));

         alert(
            `¡Equipo "${nombreEquipo}" creado con éxito! (Simulación)\n\n` +
            `Código de invitación: ${equipoNuevo.codigo_invitacion}\n` +
            `Ahora podés sumar miembros desde esta misma página.`
         );

         window.location.reload();
      });
   }

   /*
   ================================================
      ESTADO 2: GESTIONAR EQUIPO
   ================================================
   */

   function iniciarGestion(equipoInicial) {

      let equipo = equipoInicial;

      function guardarEquipo() {
         localStorage.setItem(CLAVE_EQUIPO, JSON.stringify(equipo));
      }

      // --- Referencias al DOM ---
      const etiquetaRol = document.getElementById("gestion-rol-etiqueta");
      const nombreEquipoTitulo = document.getElementById("gestion-nombre-equipo");

      const inputBanner = document.getElementById("input-banner-gestion");
      const previewBannerImg = document.getElementById("preview-banner-gestion-img");
      const placeholderBanner = document.querySelector("#preview-banner-gestion .placeholder-texto");

      const inputEscudo = document.getElementById("input-escudo-gestion");
      const previewEscudoImg = document.getElementById("preview-escudo-gestion-img");
      const placeholderEscudo = document.querySelector("#preview-escudo-gestion .placeholder-icono");

      const formEditar = document.getElementById("formulario-editar-equipo");
      const campoNombre = document.getElementById("gestion-nombre");
      const campoJuego = document.getElementById("gestion-juego");
      const campoLocalidad = document.getElementById("gestion-localidad");
      const campoDescripcion = document.getElementById("gestion-descripcion");

      const codigoTexto = document.getElementById("codigo-invitacion-texto");
      const btnCopiarCodigo = document.getElementById("btn-copiar-codigo");

      const listaMiembros = document.getElementById("lista-miembros");
      const inputBuscar = document.getElementById("input-buscar-jugador");
      const listaResultados = document.getElementById("lista-resultados-busqueda");
      const mensajeBusquedaVacia = document.getElementById("mensaje-busqueda-vacia");
      const listaSolicitudes = document.getElementById("lista-solicitudes");
      const mensajeSolicitudesVacia = document.getElementById("mensaje-solicitudes-vacia");

      const JUGADORES_BUSCABLES = [
         { id: 101, nombre: "Camila Duarte", usuario: "@camiduarte", avatar: "../../publico/img/avatars/a2.png" },
         { id: 102, nombre: "Ignacio Ferreira", usuario: "@nachoferreira", avatar: "../../publico/img/avatars/a3.png" },
         { id: 103, nombre: "Sofía Méndez", usuario: "@sofimendez", avatar: "../../publico/img/avatars/a1.png" },
         { id: 104, nombre: "Bruno Castro", usuario: "@brunocastro", avatar: "../../publico/img/avatars/a2.png" },
         { id: 105, nombre: "Valentina Acosta", usuario: "@valeacosta", avatar: "../../publico/img/avatars/a3.png" }
      ];

      // --- Pintar datos del equipo ---
      function pintarDatosEquipo() {
         nombreEquipoTitulo.textContent = equipo.nombre_equipo || "Gestionar Equipo";
         campoNombre.value = equipo.nombre_equipo || "";
         campoJuego.value = equipo.juego_id || "1";
         campoLocalidad.value = equipo.localidad || "";
         campoDescripcion.value = equipo.descripcion || "";
         codigoTexto.textContent = equipo.codigo_invitacion || "----";

         const soyLider = equipo.miembros.some((m) => m.nombre === "Vos" && m.rol === "lider");
         etiquetaRol.textContent = soyLider ? "Líder del equipo" : "Capitán del equipo";

         if (equipo.banner) {
            previewBannerImg.src = equipo.banner;
            previewBannerImg.hidden = false;
            if (placeholderBanner) placeholderBanner.hidden = true;
         }

         if (equipo.escudo_url) {
            previewEscudoImg.src = equipo.escudo_url;
            previewEscudoImg.hidden = false;
            if (placeholderEscudo) placeholderEscudo.hidden = true;
         }
      }

      inputBanner.addEventListener("change", () => {
         leerImagenComoURL(inputBanner, (url) => {
            previewBannerImg.src = url;
            previewBannerImg.hidden = false;
            if (placeholderBanner) placeholderBanner.hidden = true;
         });
      });

      inputEscudo.addEventListener("change", () => {
         leerImagenComoURL(inputEscudo, (url) => {
            previewEscudoImg.src = url;
            previewEscudoImg.hidden = false;
            if (placeholderEscudo) placeholderEscudo.hidden = true;
         });
      });

      formEditar.addEventListener("submit", (evento) => {
         evento.preventDefault();

         equipo.nombre_equipo = campoNombre.value.trim();
         equipo.juego_id = campoJuego.value;
         equipo.localidad = campoLocalidad.value.trim();
         equipo.descripcion = campoDescripcion.value.trim();
         equipo.banner = previewBannerImg.hidden ? equipo.banner : previewBannerImg.src;
         equipo.escudo_url = previewEscudoImg.hidden ? equipo.escudo_url : previewEscudoImg.src;

         guardarEquipo();
         pintarDatosEquipo();
         alert("Cambios guardados (simulación).");
      });

      btnCopiarCodigo.addEventListener("click", () => {
         navigator.clipboard.writeText(equipo.codigo_invitacion || "");
         btnCopiarCodigo.innerHTML = '<i class="fa-solid fa-check"></i> Copiado';
         setTimeout(() => {
            btnCopiarCodigo.innerHTML = '<i class="fa-solid fa-copy"></i> Copiar';
         }, 1500);
      });

      // --- Miembros ---
      function renderMiembros() {
         listaMiembros.innerHTML = equipo.miembros.map((m) => {
            let badge = "";
            if (m.rol === "lider") badge = '<span class="fila-jugador-badge badge-lider">Líder</span>';
            else if (m.rol === "capitan") badge = '<span class="fila-jugador-badge badge-capitan">Capitán</span>';

            const botonCapitan = (m.rol === "miembro")
               ? `<button type="button" class="boton-hacer-capitan" data-id="${m.id}">Hacer Capitán</button>`
               : "";

            const botonCeder = (m.rol === "capitan")
               ? `<button type="button" class="boton-ceder-liderazgo" data-id="${m.id}">Ceder Liderazgo</button>`
               : "";

            const botonQuitar = (m.rol !== "lider")
               ? `<button type="button" class="boton-quitar-miembro" data-id="${m.id}">Quitar</button>`
               : "";

            return `
               <div class="fila-jugador">
                  <img class="fila-jugador-avatar" src="${m.avatar}" alt="${m.nombre}">
                  <div class="fila-jugador-info">
                     <span class="fila-jugador-nombre">${m.nombre}</span>
                     <span class="fila-jugador-usuario">${m.usuario}</span>
                  </div>
                  ${badge}
                  <div class="fila-jugador-acciones">
                     ${botonCapitan}
                     ${botonCeder}
                     ${botonQuitar}
                  </div>
               </div>
            `;
         }).join("");
      }

      listaMiembros.addEventListener("click", (evento) => {
         const id = Number(evento.target.dataset.id);
         if (!id) return;

         if (evento.target.classList.contains("boton-hacer-capitan")) {
            equipo.miembros.forEach((m) => {
               if (m.id === id) m.rol = "capitan";
            });
         }

         if (evento.target.classList.contains("boton-ceder-liderazgo")) {
            const nuevoLider = equipo.miembros.find((m) => m.id === id);
            if (!nuevoLider) return;

            if (!confirm(`¿Seguro que querés ceder el liderazgo a ${nuevoLider.nombre}? Vos vas a pasar a ser capitán.`)) {
               return;
            }

            equipo.miembros.forEach((m) => {
               if (m.rol === "lider") m.rol = "capitan";
               if (m.id === id) m.rol = "lider";
            });
         }

         if (evento.target.classList.contains("boton-quitar-miembro")) {
            if (!confirm("¿Seguro que querés sacar a este jugador del equipo?")) return;
            equipo.miembros = equipo.miembros.filter((m) => m.id !== id);
         }

         guardarEquipo();
         renderMiembros();
         pintarDatosEquipo();
      });

      // --- Buscar y agregar jugadores ---
      function yaEsMiembro(id) {
         return equipo.miembros.some((m) => m.id === id);
      }

      function yaTieneSolicitud(id) {
         return equipo.solicitudes.some((s) => s.id === id);
      }

      function renderResultadosBusqueda(query) {
         const texto = query.trim().toLowerCase();

         if (!texto) {
            listaResultados.innerHTML = "";
            listaResultados.appendChild(mensajeBusquedaVacia);
            mensajeBusquedaVacia.hidden = false;
            return;
         }

         const resultados = JUGADORES_BUSCABLES.filter((j) =>
            j.nombre.toLowerCase().includes(texto) || j.usuario.toLowerCase().includes(texto)
         );

         if (!resultados.length) {
            listaResultados.innerHTML = `<p class="lista-vacia">No se encontraron jugadores con ese nombre.</p>`;
            return;
         }

         listaResultados.innerHTML = resultados.map((j) => {
            if (yaEsMiembro(j.id)) {
               return `
                  <li class="fila-jugador">
                     <img class="fila-jugador-avatar" src="${j.avatar}" alt="${j.nombre}">
                     <div class="fila-jugador-info">
                        <span class="fila-jugador-nombre">${j.nombre}</span>
                        <span class="fila-jugador-usuario">${j.usuario}</span>
                     </div>
                     <span class="fila-jugador-badge badge-capitan">Ya es miembro</span>
                  </li>
               `;
            }

            if (yaTieneSolicitud(j.id)) {
               return `
                  <li class="fila-jugador">
                     <img class="fila-jugador-avatar" src="${j.avatar}" alt="${j.nombre}">
                     <div class="fila-jugador-info">
                        <span class="fila-jugador-nombre">${j.nombre}</span>
                        <span class="fila-jugador-usuario">${j.usuario}</span>
                     </div>
                     <span class="fila-jugador-badge badge-pendiente">Ya invitado</span>
                  </li>
               `;
            }

            return `
               <li class="fila-jugador">
                  <img class="fila-jugador-avatar" src="${j.avatar}" alt="${j.nombre}">
                  <div class="fila-jugador-info">
                     <span class="fila-jugador-nombre">${j.nombre}</span>
                     <span class="fila-jugador-usuario">${j.usuario}</span>
                  </div>
                  <div class="fila-jugador-acciones">
                     <button type="button" class="boton-invitar" data-id="${j.id}">Invitar</button>
                  </div>
               </li>
            `;
         }).join("");
      }

      inputBuscar.addEventListener("input", () => {
         renderResultadosBusqueda(inputBuscar.value);
      });

      listaResultados.addEventListener("click", (evento) => {
         if (!evento.target.classList.contains("boton-invitar")) return;

         const id = Number(evento.target.dataset.id);
         const jugador = JUGADORES_BUSCABLES.find((j) => j.id === id);
         if (!jugador) return;

         equipo.solicitudes.push({ ...jugador, estado: "pendiente" });
         guardarEquipo();

         renderResultadosBusqueda(inputBuscar.value);
         renderSolicitudes();
      });

      // --- Solicitudes pendientes ---
      function renderSolicitudes() {
         if (!equipo.solicitudes.length) {
            listaSolicitudes.innerHTML = "";
            listaSolicitudes.appendChild(mensajeSolicitudesVacia);
            mensajeSolicitudesVacia.hidden = false;
            return;
         }

         listaSolicitudes.innerHTML = equipo.solicitudes.map((s) => `
            <li class="fila-jugador">
               <img class="fila-jugador-avatar" src="${s.avatar}" alt="${s.nombre}">
               <div class="fila-jugador-info">
                  <span class="fila-jugador-nombre">${s.nombre}</span>
                  <span class="fila-jugador-usuario">${s.usuario}</span>
               </div>
               <span class="fila-jugador-badge badge-pendiente">Pendiente</span>
               <div class="fila-jugador-acciones">
                  <button type="button" class="boton-simular-aceptar" data-id="${s.id}" title="Solo para probar, en la vida real lo acepta el jugador">
                     Simular aceptación
                  </button>
                  <button type="button" class="boton-cancelar-solicitud" data-id="${s.id}">Cancelar</button>
               </div>
            </li>
         `).join("");
      }

      listaSolicitudes.addEventListener("click", (evento) => {
         const id = Number(evento.target.dataset.id);
         if (!id) return;

         if (evento.target.classList.contains("boton-cancelar-solicitud")) {
            equipo.solicitudes = equipo.solicitudes.filter((s) => s.id !== id);
         }

         if (evento.target.classList.contains("boton-simular-aceptar")) {
            const solicitud = equipo.solicitudes.find((s) => s.id === id);
            if (solicitud) {
               equipo.miembros.push({ ...solicitud, rol: "miembro" });
               equipo.solicitudes = equipo.solicitudes.filter((s) => s.id !== id);
            }
         }

         guardarEquipo();
         renderSolicitudes();
         renderMiembros();
         renderResultadosBusqueda(inputBuscar.value);
      });

      // --- Inicializar ---
      pintarDatosEquipo();
      renderMiembros();
      renderSolicitudes();
   }

   /*
   ================================================
      UTILIDAD COMPARTIDA
   ================================================
   */

   function leerImagenComoURL(input, callback) {
      if (!input.files || !input.files[0]) return;
      const lector = new FileReader();
      lector.onload = () => callback(lector.result);
      lector.readAsDataURL(input.files[0]);
   }

});
