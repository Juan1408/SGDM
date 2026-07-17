"use strict";

/*
=====================================================
    VISTAS DE ADMINISTRACIÓN
=====================================================
*/

const TorneoVista = (() => {
    /*
    =================================================
        UTILIDADES
    =================================================
    */

    function escaparHTML(valor) {
        const elemento = document.createElement("div");

        elemento.textContent = String(valor ?? "");

        return elemento.innerHTML;
    }

    function formatearFecha(fecha) {
        if (!fecha) {
            return "Sin definir";
        }

        const valor = new Date(`${fecha}T00:00:00`);

        if (Number.isNaN(valor.getTime())) {
            return fecha;
        }

        return new Intl.DateTimeFormat("es-UY", {
            day: "2-digit",
            month: "short",
            year: "numeric"
        }).format(valor);
    }

    function opcionesSelect(opciones, seleccion) {
        return opciones.map((opcion) => `
            <option
                value="${escaparHTML(opcion)}"
                ${
                    String(opcion) === String(seleccion)
                        ? "selected"
                        : ""
                }
            >
                ${escaparHTML(opcion)}
            </option>
        `).join("");
    }


    /*
    =================================================
        COMPONENTES REUTILIZABLES
    =================================================
    */

    function ayuda(texto) {
        return texto
            ? `
                <small class="form-help">
                    ${escaparHTML(texto)}
                </small>
            `
            : "";
    }

    function tarjeta(etiqueta, valor) {
        return `
            <article class="information-card">
                <span>${escaparHTML(etiqueta)}</span>
                <strong>${escaparHTML(valor)}</strong>
            </article>
        `;
    }

    function bloqueTexto(titulo, texto) {
        return `
            <div class="tournament-description">
                <strong>${escaparHTML(titulo)}</strong>
                <p>${escaparHTML(texto)}</p>
            </div>
        `;
    }

    function input({
        id,
        etiqueta,
        valor,
        tipo = "text",
        atributos = "",
        textoAyuda = "",
        clase = ""
    }) {
        return `
            <div class="form-group ${clase}">
                <label for="${id}">
                    ${escaparHTML(etiqueta)}
                </label>

                <input
                    id="${id}"
                    name="${id}"
                    type="${tipo}"
                    value="${escaparHTML(valor)}"
                    ${atributos}
                >

                ${ayuda(textoAyuda)}
            </div>
        `;
    }

    function inputBloqueado(
        id,
        etiqueta,
        valor,
        textoAyuda
    ) {
        return input({
            id,
            etiqueta,
            valor,
            atributos: "readonly",
            textoAyuda
        });
    }

    function select({
        id,
        etiqueta,
        opciones,
        seleccionado,
        bloqueado = false,
        textoAyuda = ""
    }) {
        return `
            <div class="form-group">
                <label for="${id}">
                    ${escaparHTML(etiqueta)}
                </label>

                <select
                    id="${id}"
                    name="${id}"
                    ${bloqueado ? "disabled" : ""}
                >
                    ${opcionesSelect(opciones, seleccionado)}
                </select>

                ${ayuda(textoAyuda)}
            </div>
        `;
    }

    function textarea(
        id,
        etiqueta,
        valor,
        maximo
    ) {
        return `
            <div class="form-group form-group--full">
                <label for="${id}">
                    ${escaparHTML(etiqueta)}
                </label>

                <textarea
                    id="${id}"
                    name="${id}"
                    minlength="10"
                    maxlength="${maximo}"
                    required
                >${escaparHTML(valor)}</textarea>
            </div>
        `;
    }


    /*
    =================================================
        INFORMACIÓN GENERAL
    =================================================
    */

    function informacion(torneo) {
        const esEquipo = torneo.modalidad === "Equipos";

        const datos = [
            ["Disciplina", torneo.disciplina],
            ["Modalidad", torneo.modalidad],
            [
                esEquipo
                    ? "Integrantes por equipo"
                    : "Participación",
                esEquipo
                    ? torneo.integrantesPorEquipo
                    : "Individual"
            ],
            ["Sistema de competencia", torneo.sistema],
            ["Participantes", torneo.participantes],
            [
                "Fecha de inicio",
                formatearFecha(torneo.fechaInicio)
            ],
            [
                "Cierre de inscripciones",
                formatearFecha(torneo.fechaCierre)
            ],
            ["Ubicación", torneo.ubicacion]
        ];

        return `
            <h2 class="panel-title">
                Información general
            </h2>

            <p class="panel-description">
                Consulta los datos principales y la configuración
                de la competencia.
            </p>

            <div class="information-grid">
                ${datos
                    .map(([etiqueta, valor]) =>
                        tarjeta(etiqueta, valor)
                    )
                    .join("")}
            </div>

            ${bloqueTexto(
                "Descripción",
                torneo.descripcion
            )}

            ${bloqueTexto(
                "Reglamento",
                torneo.reglamento
            )}
        `;
    }

    /*
=================================================
    FIXTURE
=================================================
*/

function fixture(torneo, rondaActiva = null) {
    const rondas = Array.isArray(torneo.fixture)
        ? torneo.fixture
        : [];

    if (rondas.length === 0) {
        return pendiente(
            "Fixture",
            "Este torneo todavía no tiene rondas ni partidos configurados.",
            "fa-sitemap"
        );
    }

    const numeroRonda = rondaActiva ?? rondas[0].round;

    const rondaSeleccionada =
        rondas.find(
            (ronda) =>
                Number(ronda.round) === Number(numeroRonda)
        ) ?? rondas[0];

    const totalPartidos = rondas.reduce(
        (total, ronda) =>
            total + (ronda.matches?.length ?? 0),
        0
    );

    const botonesRondas = rondas.map((ronda) => `
        <button
            class="fixture-round-button ${
                Number(ronda.round) ===
                Number(rondaSeleccionada.round)
                    ? "active"
                    : ""
            }"
            type="button"
            data-round="${ronda.round}"
        >
            ${escaparHTML(
                ronda.label || `Ronda ${ronda.round}`
            )}
        </button>
    `).join("");

    const partidos = rondaSeleccionada.matches?.length
        ? rondaSeleccionada.matches.map((partido) => `
            <article class="fixture-card">

                <div class="fixture-card-main">

                    <div class="fixture-card-title">
                        <h3>
                            ${escaparHTML(partido.teamA)}
                            <span>vs</span>
                            ${escaparHTML(partido.teamB)}
                        </h3>

                        <span class="fixture-card-badge">
                            ${escaparHTML(partido.status)}
                        </span>
                    </div>

                    <div class="fixture-card-info">
                        <p>
                            <i class="fa-solid fa-calendar-day"></i>
                            <strong>Fecha:</strong>
                            ${formatearFecha(partido.date)}
                        </p>

                        <p>
                            <i class="fa-solid fa-clock"></i>
                            <strong>Hora:</strong>
                            ${escaparHTML(partido.time)}
                        </p>

                        <p>
                            <i class="fa-solid fa-location-dot"></i>
                            <strong>Sede:</strong>
                            ${escaparHTML(partido.venue)}
                        </p>
                    </div>

                </div>

            </article>
        `).join("")
        : `
            <div class="section-placeholder">
                <i class="fa-solid fa-calendar-xmark"></i>
                <h2>Sin partidos</h2>
                <p>Esta ronda todavía no tiene partidos.</p>
            </div>
        `;

    return `

      <div class="fixture-layout">

        <div class="fixture-acciones">

            <div>
                <span class="fixture-summary-label">
                    Gestión del fixture
                </span>

                <p>
                    ${
                        torneo.sistema ===
                        "Eliminación directa"
                            ? "Generá automáticamente las llaves con los participantes confirmados."
                            : "Consultá los enfrentamientos generados para el torneo."
                    }
                </p>
            </div>

            ${
                torneo.sistema ===
                "Eliminación directa"
                    ? `
                        <button
                            class="fixture-generar-boton"
                            id="generarFixture"
                            type="button"
                        >
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                            Generar fixture
                        </button>
                    `
                    : ""
            }

        </div>

            <div class="fixture-summary-panel">

                <div>
                    <span class="fixture-summary-label">
                        Fixture del torneo
                    </span>

                    <h2>${escaparHTML(torneo.nombre)}</h2>

                    <p>
                        ${formatearFecha(torneo.fechaInicio)}
                        —
                        ${formatearFecha(torneo.fechaCierre)}
                    </p>
                </div>

                <div class="fixture-summary-boxes">

                    <div class="fixture-summary-item">
                        <span>${rondas.length}</span>
                        <p>Rondas</p>
                    </div>

                    <div class="fixture-summary-item">
                        <span>${totalPartidos}</span>
                        <p>Partidos</p>
                    </div>

                </div>

            </div>

            <div class="fixture-rounds">
                ${botonesRondas}
            </div>

            <div class="fixture-match-list">
                ${partidos}
            </div>

        </div>
    `;
}

/*
=================================================
    RESULTADOS
=================================================
*/

function resultados(torneo) {
    const rondas = Array.isArray(torneo.fixture)
        ? torneo.fixture
        : [];

    const totalPartidos = rondas.reduce(
        (total, ronda) =>
            total + (ronda.matches?.length ?? 0),
        0
    );

    if (totalPartidos === 0) {
        return pendiente(
            "Resultados",
            "Este torneo todavía no tiene partidos configurados.",
            "fa-flag-checkered"
        );
    }

    const rondasHTML = rondas.map((ronda) => {
        const partidos = Array.isArray(ronda.matches)
            ? ronda.matches
            : [];

        const partidosHTML = partidos.map((partido) => {
            const finalizado =
                partido.status === "Finalizado" &&
                partido.scoreA !== null &&
                partido.scoreB !== null;

            const resultadoTexto = finalizado
                ? `${partido.scoreA} - ${partido.scoreB}`
                : "Resultado pendiente";

            return `
                <article class="resultado-card">

                    <div class="resultado-card__encabezado">

                        <div>
                            <span class="resultado-card__partido">
                                Partido ${escaparHTML(partido.id)}
                            </span>

                            <h3>
                                ${escaparHTML(partido.teamA)}
                                <span>vs</span>
                                ${escaparHTML(partido.teamB)}
                            </h3>
                        </div>

                        <span class="resultado-estado ${
                            finalizado
                                ? "resultado-estado--finalizado"
                                : "resultado-estado--pendiente"
                        }">
                            ${escaparHTML(partido.status)}
                        </span>

                    </div>

                    <div class="resultado-card__datos">

                        <p>
                            <i class="fa-solid fa-calendar-day"></i>
                            ${formatearFecha(partido.date)}
                        </p>

                        <p>
                            <i class="fa-solid fa-clock"></i>
                            ${escaparHTML(partido.time)}
                        </p>

                        <p>
                            <i class="fa-solid fa-location-dot"></i>
                            ${escaparHTML(partido.venue)}
                        </p>

                    </div>

                    <div class="resultado-actual">
                        <span>Resultado actual</span>
                        <strong>${resultadoTexto}</strong>
                    </div>

                    <form
                        class="resultado-form"
                        data-partido-id="${partido.id}"
                        data-ronda="${ronda.round}"
                    >

                        <div class="resultado-equipo">

                            <label for="scoreA-${ronda.round}-${partido.id}">
                                ${escaparHTML(partido.teamA)}
                            </label>

                            <input
                                id="scoreA-${ronda.round}-${partido.id}"
                                name="scoreA"
                                type="number"
                                min="0"
                                step="1"
                                value="${
                                    partido.scoreA ?? ""
                                }"
                                required
                            >

                        </div>

                        <span class="resultado-vs">
                            VS
                        </span>

                        <div class="resultado-equipo">

                            <label for="scoreB-${ronda.round}-${partido.id}">
                                ${escaparHTML(partido.teamB)}
                            </label>

                            <input
                                id="scoreB-${ronda.round}-${partido.id}"
                                name="scoreB"
                                type="number"
                                min="0"
                                step="1"
                                value="${
                                    partido.scoreB ?? ""
                                }"
                                required
                            >

                        </div>

                        <button
                            class="resultado-guardar"
                            type="submit"
                        >
                            <i class="fa-solid fa-floppy-disk"></i>

                            ${
                                finalizado
                                    ? "Actualizar resultado"
                                    : "Guardar resultado"
                            }
                        </button>

                        <div
                            class="resultado-mensaje"
                            aria-live="polite"
                        ></div>

                    </form>

                </article>
            `;
        }).join("");

        return `
            <section class="resultados-ronda">

                <div class="resultados-ronda__titulo">

                    <div>
                        <span>Ronda ${ronda.round}</span>

                        <h2>
                            ${escaparHTML(
                                ronda.label ||
                                `Ronda ${ronda.round}`
                            )}
                        </h2>
                    </div>

                    <strong>
                        ${partidos.length}
                        ${
                            partidos.length === 1
                                ? "partido"
                                : "partidos"
                        }
                    </strong>

                </div>

                <div class="resultados-lista">
                    ${partidosHTML}
                </div>

            </section>
        `;
    }).join("");

    return `
        <div class="resultados-layout">

            <div class="resultados-encabezado">

                <div>
                    <span class="fixture-summary-label">
                        Gestión de resultados
                    </span>

                    <h2 class="panel-title">
                        ${escaparHTML(torneo.nombre)}
                    </h2>

                    <p class="panel-description">
                        Registrá o modificá los marcadores de
                        los partidos del torneo.
                    </p>
                </div>

                <div class="participantes-resumen">
                    <strong>${totalPartidos}</strong>
                    <span>Partidos</span>
                </div>

            </div>

            ${rondasHTML}

        </div>
    `;
}

/*
=================================================
    POSICIONES
=================================================
*/

function posiciones(torneo) {
    const participantes = Array.isArray(
        torneo.participantesData
    )
        ? torneo.participantesData
        : [];

    const fixture = Array.isArray(torneo.fixture)
        ? torneo.fixture
        : [];

    /*
        Se crea una fila inicial para cada participante.
    */
    const tabla = new Map();

    participantes.forEach((participante) => {
        tabla.set(participante.nombre, {
            nombre: participante.nombre,
            pj: 0,
            pg: 0,
            pe: 0,
            pp: 0,
            gf: 0,
            gc: 0,
            dg: 0,
            puntos: 0
        });
    });

    /*
        También se incorporan equipos que aparezcan
        en el fixture aunque no estén en participantesData.
    */
    fixture.forEach((ronda) => {
        const partidos = Array.isArray(ronda.matches)
            ? ronda.matches
            : [];

        partidos.forEach((partido) => {
            [partido.teamA, partido.teamB].forEach(
                (nombre) => {
                    if (
                        nombre &&
                        !nombre.startsWith("Ganador") &&
                        !tabla.has(nombre)
                    ) {
                        tabla.set(nombre, {
                            nombre,
                            pj: 0,
                            pg: 0,
                            pe: 0,
                            pp: 0,
                            gf: 0,
                            gc: 0,
                            dg: 0,
                            puntos: 0
                        });
                    }
                }
            );
        });
    });

    /*
        Se procesan únicamente los partidos finalizados.
    */
    fixture.forEach((ronda) => {
        const partidos = Array.isArray(ronda.matches)
            ? ronda.matches
            : [];

        partidos.forEach((partido) => {
            const scoreA = partido.scoreA;
            const scoreB = partido.scoreB;

            const finalizado =
                partido.status === "Finalizado" &&
                Number.isInteger(scoreA) &&
                Number.isInteger(scoreB);

            if (!finalizado) {
                return;
            }

            const equipoA = tabla.get(partido.teamA);
            const equipoB = tabla.get(partido.teamB);

            if (!equipoA || !equipoB) {
                return;
            }

            equipoA.pj++;
            equipoB.pj++;

            equipoA.gf += scoreA;
            equipoA.gc += scoreB;

            equipoB.gf += scoreB;
            equipoB.gc += scoreA;

            if (scoreA > scoreB) {
                equipoA.pg++;
                equipoA.puntos += 3;
                equipoB.pp++;
            } else if (scoreB > scoreA) {
                equipoB.pg++;
                equipoB.puntos += 3;
                equipoA.pp++;
            } else {
                equipoA.pe++;
                equipoB.pe++;

                equipoA.puntos++;
                equipoB.puntos++;
            }
        });
    });

    const posicionesOrdenadas = [...tabla.values()]
        .map((equipo) => ({
            ...equipo,
            dg: equipo.gf - equipo.gc
        }))
        .sort((a, b) => {
            if (b.puntos !== a.puntos) {
                return b.puntos - a.puntos;
            }

            if (b.dg !== a.dg) {
                return b.dg - a.dg;
            }

            if (b.gf !== a.gf) {
                return b.gf - a.gf;
            }

            return a.nombre.localeCompare(
                b.nombre,
                "es"
            );
        });

    if (posicionesOrdenadas.length === 0) {
        return pendiente(
            "Posiciones",
            "Todavía no hay participantes para mostrar.",
            "fa-ranking-star"
        );
    }

    const filas = posicionesOrdenadas.map(
        (equipo, indice) => `
            <tr>
                <td>
                    <span class="posicion-numero">
                        ${indice + 1}
                    </span>
                </td>

                <td class="posicion-equipo">
                    ${escaparHTML(equipo.nombre)}
                </td>

                <td>${equipo.pj}</td>
                <td>${equipo.pg}</td>
                <td>${equipo.pe}</td>
                <td>${equipo.pp}</td>
                <td>${equipo.gf}</td>
                <td>${equipo.gc}</td>

                <td class="${
                    equipo.dg > 0
                        ? "diferencia-positiva"
                        : equipo.dg < 0
                            ? "diferencia-negativa"
                            : ""
                }">
                    ${
                        equipo.dg > 0
                            ? `+${equipo.dg}`
                            : equipo.dg
                    }
                </td>

                <td class="posicion-puntos">
                    ${equipo.puntos}
                </td>
            </tr>
        `
    ).join("");

    const partidosFinalizados = fixture.reduce(
        (total, ronda) => {
            const partidos = Array.isArray(
                ronda.matches
            )
                ? ronda.matches
                : [];

            return total + partidos.filter(
                (partido) =>
                    partido.status === "Finalizado" &&
                    Number.isInteger(partido.scoreA) &&
                    Number.isInteger(partido.scoreB)
            ).length;
        },
        0
    );

    return `
        <div class="posiciones-layout">

            <div class="posiciones-encabezado">

                <div>
                    <span class="fixture-summary-label">
                        Clasificación
                    </span>

                    <h2 class="panel-title">
                        Tabla de posiciones
                    </h2>

                    <p class="panel-description">
                        La tabla se actualiza automáticamente
                        con los resultados registrados.
                    </p>
                </div>

                <div class="posiciones-resumenes">

                    <div class="participantes-resumen">
                        <strong>
                            ${posicionesOrdenadas.length}
                        </strong>

                        <span>Participantes</span>
                    </div>

                    <div class="participantes-resumen">
                        <strong>
                            ${partidosFinalizados}
                        </strong>

                        <span>Finalizados</span>
                    </div>

                </div>

            </div>

            <div class="posiciones-tabla-contenedor">

                <table class="posiciones-tabla">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Equipo</th>
                            <th title="Partidos jugados">PJ</th>
                            <th title="Partidos ganados">PG</th>
                            <th title="Partidos empatados">PE</th>
                            <th title="Partidos perdidos">PP</th>
                            <th title="Goles a favor">GF</th>
                            <th title="Goles en contra">GC</th>
                            <th title="Diferencia de goles">DG</th>
                            <th title="Puntos">PTS</th>
                        </tr>
                    </thead>

                    <tbody>
                        ${filas}
                    </tbody>

                </table>

            </div>

            <div class="posiciones-referencia">
                <span><strong>PJ:</strong> jugados</span>
                <span><strong>PG:</strong> ganados</span>
                <span><strong>PE:</strong> empatados</span>
                <span><strong>PP:</strong> perdidos</span>
                <span><strong>DG:</strong> diferencia</span>
            </div>

        </div>
    `;
}

/*
=================================================
    CRONOGRAMA
=================================================
*/

function cronograma(torneo) {
    const eventos = Array.isArray(torneo.eventos)
        ? [...torneo.eventos]
        : [];

    if (eventos.length === 0) {
        return pendiente(
            "Cronograma",
            "Este torneo todavía no tiene actividades programadas.",
            "fa-calendar-days"
        );
    }

    eventos.sort((a, b) => {
        const fechaA = new Date(
            `${a.fecha}T${a.hora || "00:00"}`
        );

        const fechaB = new Date(
            `${b.fecha}T${b.hora || "00:00"}`
        );

        return fechaA - fechaB;
    });

    const grupos = eventos.reduce(
        (acumulador, evento) => {
            const clave = evento.fecha;

            if (!acumulador[clave]) {
                acumulador[clave] = [];
            }

            acumulador[clave].push(evento);

            return acumulador;
        },
        {}
    );

    const diasHTML = Object.entries(grupos)
        .map(([fecha, actividades]) => {
            const actividadesHTML = actividades
                .map((evento) => `
                    <article class="cronograma-evento">

                        <div class="cronograma-evento__hora">
                            <span>
                                ${escaparHTML(
                                    evento.hora || "Sin hora"
                                )}
                            </span>
                        </div>

                        <div class="cronograma-evento__contenido">

                            <div class="cronograma-evento__encabezado">

                                <div>
                                    <span class="cronograma-tipo cronograma-tipo--${escaparHTML(
                                        evento.tipo
                                    )}">
                                        ${escaparHTML(evento.tipo)}
                                    </span>

                                    <h3>
                                        ${escaparHTML(evento.titulo)}
                                    </h3>
                                </div>

                                <span class="cronograma-estado">
                                    ${escaparHTML(evento.estado)}
                                </span>

                            </div>

                            <p>
                                ${escaparHTML(
                                    evento.descripcion
                                )}
                            </p>

                            <div class="cronograma-evento__datos">

                                <span>
                                    <i class="fa-solid fa-location-dot"></i>
                                    ${escaparHTML(
                                        evento.ubicacion
                                    )}
                                </span>

                            </div>

                        </div>

                    </article>
                `)
                .join("");

            return `
                <section class="cronograma-dia">

                    <div class="cronograma-dia__fecha">
                        <span>
                            ${formatearFecha(fecha)}
                        </span>
                    </div>

                    <div class="cronograma-dia__lista">
                        ${actividadesHTML}
                    </div>

                </section>
            `;
        })
        .join("");

    return `
        <div class="cronograma-layout">

            <div class="cronograma-encabezado">

                <div>
                    <span class="fixture-summary-label">
                        Agenda del torneo
                    </span>

                    <h2 class="panel-title">
                        Cronograma
                    </h2>

                    <p class="panel-description">
                        Consulta las actividades importantes,
                        reuniones y fechas principales.
                    </p>
                </div>

                <div class="participantes-resumen">
                    <strong>${eventos.length}</strong>
                    <span>Actividades</span>
                </div>

            </div>

            <div class="cronograma-lista">
                ${diasHTML}
            </div>

        </div>
    `;
}

/*
=================================================
    PARTICIPANTES
=================================================
*/

function participantes(torneo) {
    const lista = Array.isArray(torneo.participantesData)
        ? torneo.participantesData
        : [];

    if (lista.length === 0) {
        return pendiente(
            "Participantes",
            "Este torneo todavía no tiene participantes inscritos.",
            "fa-users"
        );
    }

    const tarjetas = lista.map((participante) => `
        <article
            class="participante-card"
            data-participante-id="${participante.id}"
        >
            <div class="participante-card-header">
                <div>
                    <span class="participante-tipo">
                        ${escaparHTML(participante.tipo)}
                    </span>

                    <h3>
                        ${escaparHTML(participante.nombre)}
                    </h3>
                </div>

                <span class="participante-status">
                    ${escaparHTML(participante.estado)}
                </span>
            </div>

            <div class="participante-info">
                <p>
                    <i class="fa-solid fa-user-shield"></i>
                    <strong>Capitán:</strong>
                    ${escaparHTML(participante.capitan)}
                </p>

                <p>
                    <i class="fa-solid fa-users"></i>
                    <strong>Modalidad:</strong>
                    ${escaparHTML(torneo.modalidad)}
                </p>
            </div>

            <div class="participante-card-actions">
                <button
                    class="participante-btn participante-btn-view"
                    type="button"
                    data-accion="ver"
                    data-id="${participante.id}"
                >
                    <i class="fa-solid fa-eye"></i>
                    Ver información
                </button>

                <button
                    class="participante-btn participante-btn-remove"
                    type="button"
                    data-accion="eliminar"
                    data-id="${participante.id}"
                >
                    <i class="fa-solid fa-user-minus"></i>
                    Eliminar
                </button>
            </div>
        </article>
    `).join("");

    return `
        <div class="participantes-layout">

            <div class="participantes-encabezado">
                <div>
                    <span class="fixture-summary-label">
                        Participantes del torneo
                    </span>

                    <h2 class="panel-title">
                        ${escaparHTML(torneo.nombre)}
                    </h2>

                    <p class="panel-description">
                        Gestioná los equipos o jugadores inscritos.
                    </p>
                </div>

                <div class="participantes-resumen">
                    <strong>${lista.length}</strong>
                    <span>Registrados</span>
                </div>
            </div>

            <div class="participantes-list">
                ${tarjetas}
            </div>

        </div>
    `;
}

/*
=================================================
    SOLICITUDES
=================================================
*/

function solicitudes(torneo) {
    const lista = Array.isArray(torneo.solicitudesData)
        ? torneo.solicitudesData
        : [];

    const pendientes = lista.filter(
        (solicitud) =>
            solicitud.estado === "Pendiente"
    );

    if (pendientes.length === 0) {
        return pendiente(
            "Solicitudes",
            "No hay solicitudes pendientes para este torneo.",
            "fa-envelope-open-text"
        );
    }

    const tarjetas = pendientes.map((solicitud) => `
        <article class="solicitud-card">

            <div class="solicitud-card__encabezado">

                <div>
                    <span class="solicitud-tipo">
                        ${escaparHTML(solicitud.tipo)}
                    </span>

                    <h3>
                        ${escaparHTML(solicitud.nombre)}
                    </h3>
                </div>

                <span class="solicitud-estado">
                    ${escaparHTML(solicitud.estado)}
                </span>

            </div>

            <div class="solicitud-card__datos">

                <p>
                    <i class="fa-solid fa-user"></i>
                    <strong>Responsable:</strong>
                    ${escaparHTML(solicitud.responsable)}
                </p>

                <p>
                    <i class="fa-solid fa-calendar-day"></i>
                    <strong>Fecha:</strong>
                    ${formatearFecha(solicitud.fecha)}
                </p>

            </div>

            <div class="solicitud-card__acciones">

                <button
                    class="solicitud-boton solicitud-boton--aceptar"
                    type="button"
                    data-solicitud-accion="aceptar"
                    data-id="${solicitud.id}"
                >
                    <i class="fa-solid fa-check"></i>
                    Aceptar
                </button>

                <button
                    class="solicitud-boton solicitud-boton--rechazar"
                    type="button"
                    data-solicitud-accion="rechazar"
                    data-id="${solicitud.id}"
                >
                    <i class="fa-solid fa-xmark"></i>
                    Rechazar
                </button>

            </div>

        </article>
    `).join("");

    return `
        <div class="solicitudes-layout">

            <div class="solicitudes-encabezado">

                <div>
                    <span class="fixture-summary-label">
                        Inscripciones
                    </span>

                    <h2 class="panel-title">
                        Solicitudes pendientes
                    </h2>

                    <p class="panel-description">
                        Aceptá o rechazá las solicitudes
                        recibidas para el torneo.
                    </p>
                </div>

                <div class="participantes-resumen">
                    <strong>${pendientes.length}</strong>
                    <span>Pendientes</span>
                </div>

            </div>

            <div class="solicitudes-lista">
                ${tarjetas}
            </div>

        </div>
    `;
}

    /*
    =================================================
        SECCIONES PENDIENTES
    =================================================
    */

    function pendiente(titulo, descripcion, icono) {
        return `
            <div class="section-placeholder">
                <i
                    class="fa-solid ${icono}"
                    aria-hidden="true"
                ></i>

                <h2>${escaparHTML(titulo)}</h2>
                <p>${escaparHTML(descripcion)}</p>
            </div>
        `;
    }


    /*
    =================================================
        CAMPO DE CUPOS
    =================================================
    */

    function campoCupos(
        torneo,
        sistema = torneo.sistema
    ) {
        const bloqueado =
            !TorneoValidaciones.puedeEditarCupos(
                torneo
            );

        if (sistema === "Eliminación directa") {
            const opciones =
                TorneoValidaciones.obtenerCuposEliminacion(
                    torneo.participantesActuales
                );

            /*
                Conserva un valor antiguo cuando el campo ya
                está bloqueado, evitando que desaparezca del
                selector.
            */
            if (
                bloqueado &&
                !opciones.includes(torneo.cupos)
            ) {
                opciones.push(torneo.cupos);
                opciones.sort((a, b) => a - b);
            }

            return select({
                id: "cupos",
                etiqueta: "Cupos máximos",
                opciones,
                seleccionado: torneo.cupos,
                bloqueado,
                textoAyuda: bloqueado
                    ? "Los cupos no pueden modificarse después de comenzar el torneo."
                    : "Seleccioná una llave de hasta 64 participantes."
            });
        }

        const limites =
            TorneoValidaciones.obtenerLimitesCupos(
                sistema
            );

        const minimo = Math.max(
            limites.minimo,
            torneo.participantesActuales
        );

        const descripcion =
            sistema === "Liga"
                ? "Admite cantidades pares o impares, entre 2 y 40."
                : "Admite cantidades pares o impares, entre 4 y 64.";

        return input({
            id: "cupos",
            etiqueta: "Cupos máximos",
            tipo: "number",
            valor: torneo.cupos,
            atributos: `
                min="${minimo}"
                max="${limites.maximo}"
                step="1"
                ${bloqueado ? "disabled" : ""}
                required
            `,
            textoAyuda: bloqueado
                ? "Los cupos no pueden modificarse después de comenzar el torneo."
                : descripcion
        });
    }


    /*
    =================================================
        FORMULARIO DE EDICIÓN
    =================================================
    */

    function formularioEdicion(torneo) {
        const configuracion =
            obtenerConfiguracionDisciplina(
                torneo.disciplina
            );

        const sistemas =
            configuracion?.sistemas ?? [
                torneo.sistema
            ];

        const sistemaBloqueado =
            !TorneoValidaciones.puedeEditarSistema(
                torneo
            );

        const fechasBloqueadas =
            !TorneoValidaciones.puedeEditarFechas(
                torneo
            );

        const esEquipo =
            torneo.modalidad === "Equipos";

        const hoy =
            TorneoValidaciones.obtenerFechaActual();

        return `
            <h2 class="panel-title">
                Editar torneo
            </h2>

            <p class="panel-description">
                Modifica únicamente los datos permitidos.
                La disciplina, modalidad e integrantes son
                datos estructurales.
            </p>

            <form
                class="edit-form"
                id="formEditarTorneo"
                novalidate
            >
                <div
                    id="mensajeEdicion"
                    aria-live="polite"
                ></div>

                <div class="edit-form__grid">

                    ${input({
                        id: "nombre",
                        etiqueta: "Nombre del torneo",
                        valor: torneo.nombre,
                        atributos:
                            'minlength="3" maxlength="80" required',
                        clase: "form-group--full"
                    })}

                    ${inputBloqueado(
                        "disciplina",
                        "Disciplina",
                        torneo.disciplina,
                        "Se define al crear el torneo."
                    )}

                    ${inputBloqueado(
                        "modalidad",
                        "Modalidad",
                        torneo.modalidad,
                        "Depende de la disciplina seleccionada."
                    )}

                    ${inputBloqueado(
                        "integrantes",
                        esEquipo
                            ? "Integrantes por equipo"
                            : "Tipo de participación",
                        esEquipo
                            ? torneo.integrantesPorEquipo
                            : "Individual",
                        "Este valor no puede modificarse."
                    )}

                    ${select({
                        id: "sistema",
                        etiqueta: "Sistema de competencia",
                        opciones: sistemas,
                        seleccionado: torneo.sistema,
                        bloqueado: sistemaBloqueado,
                        textoAyuda: sistemaBloqueado
                            ? "No puede cambiarse porque el torneo ya tiene participantes o comenzó."
                            : "Puede cambiarse mientras no existan participantes registrados."
                    })}

                    ${select({
                        id: "estado",
                        etiqueta: "Estado",
                        opciones:
                            TorneoValidaciones
                                .obtenerEstadosPermitidos(
                                    torneo
                                ),
                        seleccionado: torneo.estado,
                        textoAyuda:
                            "El torneo no puede volver a un estado anterior."
                    })}

                    ${input({
                        id: "fechaInicio",
                        etiqueta: "Fecha de inicio",
                        tipo: "date",
                        valor: torneo.fechaInicio,
                        atributos: fechasBloqueadas
                            ? "disabled required"
                            : `min="${hoy}" required`,
                        textoAyuda: fechasBloqueadas
                            ? "No puede modificarse porque el torneo ya comenzó."
                            : "No puede ser anterior a la fecha actual."
                    })}

                    ${input({
                        id: "fechaCierre",
                        etiqueta: "Cierre de inscripciones",
                        tipo: "date",
                        valor: torneo.fechaCierre,
                        atributos: fechasBloqueadas
                            ? "disabled required"
                            : `min="${hoy}" required`,
                        textoAyuda: fechasBloqueadas
                            ? "No puede modificarse porque las inscripciones ya finalizaron."
                            : "Debe ser igual o anterior a la fecha de inicio."
                    })}

                    <div id="grupoCupos">
                        ${campoCupos(torneo)}
                    </div>

                    ${input({
                        id: "ubicacion",
                        etiqueta: "Ubicación",
                        valor: torneo.ubicacion,
                        atributos:
                            'minlength="2" maxlength="100" required'
                    })}

                    ${textarea(
                        "descripcion",
                        "Descripción",
                        torneo.descripcion,
                        500
                    )}

                    ${textarea(
                        "reglamento",
                        "Reglamento",
                        torneo.reglamento,
                        1500
                    )}

                </div>

                <div class="form-actions">
                    <button
                        class="button-secondary"
                        id="cancelarEdicion"
                        type="button"
                    >
                        Cancelar
                    </button>

                    <button
                        class="button-primary"
                        type="submit"
                    >
                        Guardar cambios
                    </button>
                </div>
            </form>
        `;
    }

    return {
        escaparHTML,
        formatearFecha,
        informacion,
        fixture,
        resultados,
        posiciones,
        cronograma,
        participantes,
        solicitudes,
        pendiente,
        campoCupos,
        formularioEdicion
    };
})();