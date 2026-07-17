
const lista = document.getElementById("torneosLista");
const detalle = document.getElementById("torneoSeleccionado");

function mostrarLista() {
    lista.innerHTML = TORNEOS.map((torneo) => `
        <button
            class="torneo-lista-card ${torneo.categoria}"
            type="button"
            data-id="${torneo.id}"
        >
            <div>
                <h3>${torneo.nombre}</h3>
                <p>${torneo.disciplina}</p>
            </div>

            <span class="estado ${torneo.claseEstado}">
                ${torneo.estado}
            </span>
        </button>
    `).join("");

    document
        .querySelectorAll(".torneo-lista-card")
        .forEach((boton) => {
            boton.addEventListener("click", () => {
                seleccionarTorneo(Number(boton.dataset.id));
            });
        });
}

function seleccionarTorneo(id) {
    const torneo = TORNEOS.find((item) => item.id === id);

    if (!torneo) {
        return;
    }

    document
        .querySelectorAll(".torneo-lista-card")
        .forEach((boton) => {
            boton.classList.toggle(
                "seleccionado",
                Number(boton.dataset.id) === id
            );
        });

    detalle.innerHTML = `
        <div class="torneo-seleccionado__header">
            <span class="categoria ${torneo.categoria}">
                ${torneo.disciplina}
            </span>

            <span class="estado ${torneo.claseEstado}">
                ${torneo.estado}
            </span>
        </div>

        <h2>${torneo.nombre}</h2>

        <div class="torneo-seleccionado__datos">

            <div>
                <span>Participantes</span>
                <strong>${torneo.participantes}</strong>
            </div>

            <div>
                <span>Fecha</span>
                <strong>${torneo.fecha}</strong>
            </div>

            <div>
                <span>Sistema</span>
                <strong>${torneo.sistema}</strong>
            </div>

        </div>

        <div class="torneo-seleccionado__acciones">

            <a href="administrar-torneo.html?id=${torneo.id}">
                <i class="fa-solid fa-gear"></i>
                Administrar torneo
            </a>

        </div>
    `;
}

mostrarLista();

if (TORNEOS.length > 0) {
    seleccionarTorneo(TORNEOS[0].id);
}