const TorneoCompetencia = (() => {

    /*
    Genera el fixture y vuelve a conectar los botones
    de las rondas cada vez que cambia la ronda activa.
*/
function renderizarFixture() {
    const rondas = Array.isArray(torneoActual.fixture)
        ? torneoActual.fixture
        : [];

    /*
        Si todavía no se eligió una ronda,
        se selecciona la primera.
    */
    if (
        rondaFixtureActiva === null &&
        rondas.length > 0
    ) {
        rondaFixtureActiva = rondas[0].round;
    }

    elementos.contenido.innerHTML =
        TorneoVista.fixture(
            torneoActual,
            rondaFixtureActiva
        );

    document
        .querySelectorAll(".fixture-round-button")
        .forEach((boton) => {
            boton.addEventListener("click", () => {
                rondaFixtureActiva =
                    Number(boton.dataset.round);

                renderizarFixture();
            });
        });

        const botonGenerar =
    document.getElementById("generarFixture");

botonGenerar?.addEventListener(
    "click",
    generarFixtureActual
);

}

function generarFixtureActual() {
    if (
        torneoActual.sistema !==
        "Eliminación directa"
    ) {
        alert(
            "Este generador corresponde únicamente a eliminación directa."
        );

        return;
    }

    const participantes =
        Array.isArray(
            torneoActual.participantesData
        )
            ? torneoActual.participantesData
            : [];

    if (participantes.length < 2) {
        alert(
            "Se necesitan al menos 2 participantes confirmados."
        );

        return;
    }

    if (!esPotenciaDeDos(participantes.length)) {
        alert(
            `Actualmente hay ${participantes.length} participantes. ` +
            "Para eliminación directa se necesitan 2, 4, 8, 16, 32 o 64."
        );

        return;
    }

    const tieneResultados =
        torneoActual.fixture?.some(
            (ronda) =>
                ronda.matches?.some(
                    (partido) =>
                        partido.status ===
                        "Finalizado"
                )
        );

    if (tieneResultados) {
        alert(
            "No se puede regenerar el fixture porque ya existen resultados registrados."
        );

        return;
    }

    const confirmado = confirm(
        torneoActual.fixture?.length > 0
            ? "Esto reemplazará el fixture actual. ¿Querés continuar?"
            : "¿Querés generar automáticamente el fixture?"
    );

    if (!confirmado) {
        return;
    }

    try {
        const nuevoFixture =
            generarFixtureEliminacionDirecta(
                participantes,
                {
                    mezclar: true,
                    fechaInicio:
                        torneoActual.fechaInicio,
                    ubicacion:
                        torneoActual.ubicacion
                }
            );

        const actualizado = actualizarTorneo(
            torneoActual.id,
            {
                fixture: nuevoFixture
            }
        );

        if (!actualizado) {
            alert(
                "No fue posible guardar el fixture."
            );

            return;
        }

        torneoActual = actualizado;
        rondaFixtureActiva =
            nuevoFixture[0]?.round ?? null;

        renderizarFixture();
    } catch (error) {
        console.error(error);

        alert(
            error.message ||
            "No fue posible generar el fixture."
        );
    }
}

/*
=====================================================
    RESULTADOS
=====================================================
*/

function renderizarResultados() {
    elementos.contenido.innerHTML =
        TorneoVista.resultados(torneoActual);

    document
        .querySelectorAll(".resultado-form")
        .forEach((formulario) => {
            formulario.addEventListener(
                "submit",
                guardarResultado
            );
        });
}

function guardarResultado(evento) {
    evento.preventDefault();

    const formulario = evento.currentTarget;

    const partidoId = Number(
        formulario.dataset.partidoId
    );

    const numeroRonda = Number(
        formulario.dataset.ronda
    );

    const scoreA = Number(
        formulario.elements.scoreA.value
    );

    const scoreB = Number(
        formulario.elements.scoreB.value
    );

    const mensaje = formulario.querySelector(
        ".resultado-mensaje"
    );

    if (
        !Number.isInteger(scoreA) ||
        !Number.isInteger(scoreB) ||
        scoreA < 0 ||
        scoreB < 0
    ) {
        mostrarMensajeResultado(
            mensaje,
            "Los marcadores deben ser números enteros mayores o iguales a cero.",
            "error"
        );

        return;
    }

    /*
        En eliminación directa no puede haber empate,
        porque necesariamente debe avanzar un ganador.
    */
    if (
        torneoActual.sistema ===
            "Eliminación directa" &&
        scoreA === scoreB
    ) {
        mostrarMensajeResultado(
            mensaje,
            "En eliminación directa el partido no puede finalizar empatado.",
            "error"
        );

        return;
    }

    const fixtureActualizado =
        torneoActual.fixture.map((ronda) => {
            if (
                Number(ronda.round) !== numeroRonda
            ) {
                return ronda;
            }

            return {
                ...ronda,

                matches: ronda.matches.map(
                    (partido) => {
                        if (
                            Number(partido.id) !==
                            partidoId
                        ) {
                            return partido;
                        }

                        let winner = null;

                        if (scoreA > scoreB) {
                            winner = partido.teamA;
                        } else if (scoreB > scoreA) {
                            winner = partido.teamB;
                        }

                        return {
                            ...partido,
                            scoreA,
                            scoreB,
                            winner,
                            status: "Finalizado"
                        };
                    }
                )
            };
        });

    const actualizado = actualizarTorneo(
        torneoActual.id,
        {
            fixture: fixtureActualizado
        }
    );

    if (!actualizado) {
        mostrarMensajeResultado(
            mensaje,
            "No fue posible guardar el resultado.",
            "error"
        );

        return;
    }

    torneoActual = actualizado;

    renderizarResultados();

    const formularioActualizado =
        document.querySelector(
            `.resultado-form` +
            `[data-partido-id="${partidoId}"]` +
            `[data-ronda="${numeroRonda}"]`
        );

    const mensajeActualizado =
        formularioActualizado?.querySelector(
            ".resultado-mensaje"
        );

    mostrarMensajeResultado(
        mensajeActualizado,
        "Resultado guardado correctamente.",
        "success"
    );
}

function mostrarMensajeResultado(
    contenedor,
    texto,
    tipo
) {
    if (!contenedor) {
        return;
    }

    contenedor.innerHTML = `
        <p class="${
            tipo === "success"
                ? "success-message"
                : "error-message"
        }">
            ${TorneoVista.escaparHTML(texto)}
        </p>
    `;
}

/*
=====================================================
    POSICIONES
=====================================================
*/

function renderizarPosiciones() {
    elementos.contenido.innerHTML =
        TorneoVista.posiciones(torneoActual);
}

/*
=====================================================
    CRONOGRAMA
=====================================================
*/
function renderizarCronograma() {
    elementos.contenido.innerHTML =
        TorneoVista.cronograma(torneoActual);
}


/*
=====================================================
    DISTRIBUCIÓN DE SECCIONES
=====================================================
*/

function renderizar(seccion) {
    switch (seccion) {
        case "fixture":
            renderizarFixture();
            return true;

        case "resultados":
            renderizarResultados();
            return true;

        case "posiciones":
            renderizarPosiciones();
            return true;

        case "cronograma":
            renderizarCronograma();
            return true;

        default:
            return false;
    }
}


/*
=====================================================
    API PÚBLICA DEL MÓDULO
=====================================================
*/

return {
    renderizar
};

})();