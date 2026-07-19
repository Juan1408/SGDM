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
        pendiente,
        campoCupos,
        formularioEdicion
    };
})();