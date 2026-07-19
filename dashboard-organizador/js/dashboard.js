function crearTarjetaTorneo(torneo) {
    const accion = torneo.estado === "Finalizado"
        ? "Ver resultados"
        : "Administrar";

    return `
        <article class="torneo-card ${torneo.categoria}">
            <div class="torneo-info">
                <h3>${torneo.nombre}</h3>
                <p>${torneo.disciplina}</p>

                <span class="estado ${torneo.claseEstado}">
                    ${torneo.estado}
                </span>

                <div class="torneo-detalles">
                    <span>
                        <i class="fa-solid fa-users"></i>
                        ${torneo.participantes}
                    </span>

                    <span>
                        <i class="fa-solid fa-calendar-days"></i>
                        ${torneo.fecha}
                    </span>

                    <span>
                        <i class="fa-solid fa-layer-group"></i>
                        ${torneo.sistema}
                    </span>
                </div>
            </div>

            <a
                class="torneo-card__action"
                href="administrar-torneo.html?id=${torneo.id}"
            >
                ${accion}
            </a>
        </article>
    `;
}

function renderizarTorneos() {
    const contenedor = document.getElementById("torneosGrid");

    if (!contenedor) {
        return;
    }

    contenedor.innerHTML = TORNEOS
        .map(crearTarjetaTorneo)
        .join("");
}

renderizarTorneos();