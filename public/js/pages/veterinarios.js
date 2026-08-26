(function () {
    "use strict";

    // ------------------------------------------------------------------
    // Helpers de tiempo
    // ------------------------------------------------------------------
    function minutos(texto) {
        const partes = String(texto).split(":");
        return parseInt(partes[0], 10) * 60 + parseInt(partes[1], 10);
    }

    function formato(mins) {
        const h = String(Math.floor(mins / 60)).padStart(2, "0");
        const m = String(mins % 60).padStart(2, "0");
        return h + ":" + m;
    }

    // Filas base de la cuadrícula: bloques de 1 hora desde 07:00,
    // con un último bloque de media hora que cierra a las 18:30.
    function construirFilasBase() {
        const filas = [];
        let m = 7 * 60;
        const cierre = 18 * 60 + 30;
        while (m < cierre) {
            const paso = m + 60 <= cierre ? 60 : cierre - m;
            filas.push({ inicio: m, fin: m + paso });
            m += paso;
        }
        return filas;
    }

    // Si los horarios precargados superan las 18:30 se agregan filas
    // extra de 30 min para no perder información al guardar.
    function extenderFilas(filas, horariosInit) {
        const maxFin = Math.max(
            0,
            ...(horariosInit || []).map((h) => minutos(h.end_time)),
        );
        let ultima = filas[filas.length - 1];
        while (ultima && ultima.fin < maxFin) {
            filas.push({ inicio: ultima.fin, fin: ultima.fin + 30 });
            ultima = filas[filas.length - 1];
        }
        return filas;
    }

    // ------------------------------------------------------------------
    // Validación inline (misma mecánica que usuarios.js)
    // ------------------------------------------------------------------
    window.pintarErroresValidacionVeterinario = function (errors, formId) {
        const form = document.getElementById(formId);
        if (!form) return;

        limpiarErroresValidacion(formId);

        Object.entries(errors || {}).forEach(([field, mensajes]) => {
            if (/^schedules(\.|$)/.test(field)) {
                const card = document.getElementById("cardHorarioVeterinario");
                if (card) {
                    card.querySelector(".invalid-feedback").textContent =
                        Array.isArray(mensajes)
                            ? mensajes.join(", ")
                            : String(mensajes);
                }
                return;
            }

            const input = form.querySelector(`[name="${field}"]`);
            if (!input) return;

            input.classList.add("is-invalid");
            const feedback = input
                .closest(".mb-3")
                ?.querySelector(".invalid-feedback");
            if (feedback) {
                feedback.textContent = Array.isArray(mensajes)
                    ? mensajes.join(", ")
                    : String(mensajes);
            }
        });
    };

    function limpiarErroresValidacion(formId) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.querySelectorAll(".is-invalid").forEach((el) =>
            el.classList.remove("is-invalid"),
        );
        form.querySelectorAll(".invalid-feedback").forEach((el) => {
            el.textContent = "";
        });
    }

    function recolectarDatosFormulario(form) {
        const datos = new FormData();
        datos.append("username", form.elements["username"].value.trim());
        datos.append("email", form.elements["email"].value.trim());
        datos.append("password", form.elements["password"].value);
        datos.append(
            "password_confirmation",
            form.elements["password_confirmation"].value,
        );
        datos.append("branch_id", form.elements["branch_id"].value.trim());
        datos.append("phone", form.elements["phone"].value.trim());
        datos.append(
            "type_documento",
            form.elements["type_documento"].value.trim(),
        );
        datos.append("n_documento", form.elements["n_documento"].value.trim());
        datos.append("birthday", form.elements["birthday"].value.trim());
        const avatar = form.elements["avatar"];
        if (avatar && avatar.files && avatar.files.length) {
            datos.append("avatar", avatar.files[0]);
        }
        datos.append("schedules", JSON.stringify(recolectarFranjas()));
        return datos;
    }

    // ------------------------------------------------------------------
    // Cuadrícula de horario (create/edit)
    // ------------------------------------------------------------------
    const tbodyHorario = document.getElementById("tbodyHorarioVeterinario");

    if (tbodyHorario) {
        const horariosInit = window.veterinariosHorariosInit || [];
        const filas = extenderFilas(construirFilasBase(), horariosInit);
        const diasSemana = [1, 2, 3, 4, 5, 6];

        function etiquetaFila(fila) {
            return formato(fila.inicio) + " – " + formato(fila.fin);
        }

        function pintarFilas() {
            filas.forEach((fila) => {
                const tr = document.createElement("tr");
                let html =
                    '<td class="text-muted fs-12 fw-medium">' +
                    etiquetaFila(fila) +
                    "</td>";
                diasSemana.forEach((dia) => {
                    html +=
                        '<td class="text-center">' +
                        '<input type="checkbox" class="form-check-input chk-horario" ' +
                        'data-day="' + dia + '" ' +
                        'data-start="' + formato(fila.inicio) + '" ' +
                        'data-end="' + formato(fila.fin) + '">' +
                        "</td>";
                });
                tr.innerHTML = html;
                tbodyHorario.appendChild(tr);
            });
        }

        function aplicarPrecarga() {
            horariosInit.forEach((horario) => {
                const ini = minutos(horario.start_time);
                const fin = minutos(horario.end_time);
                tbodyHorario
                    .querySelectorAll('.chk-horario[data-day="' + horario.day_of_week + '"]')
                    .forEach((chk) => {
                        const cIni = minutos(chk.dataset.start);
                        const cFin = minutos(chk.dataset.end);
                        if (cIni >= ini && cFin <= fin) chk.checked = true;
                    });
            });
        }

        function actualizarChkDia(dia) {
            const cabecera = document.querySelector(
                '.chk-dia-completo[data-day="' + dia + '"]',
            );
            if (!cabecera) return;
            const celdas = [
                ...tbodyHorario.querySelectorAll(
                    '.chk-horario[data-day="' + dia + '"]',
                ),
            ];
            const marcadas = celdas.filter((c) => c.checked).length;
            cabecera.checked = marcadas === celdas.length && celdas.length > 0;
            cabecera.indeterminate = marcadas > 0 && marcadas < celdas.length;
        }

        function actualizarResumen() {
            const resumen = document.getElementById("resumenHorarioVeterinario");
            if (!resumen) return;

            let totalMin = 0;
            const porDia = {};
            tbodyHorario
                .querySelectorAll(".chk-horario:checked")
                .forEach((chk) => {
                    const dia = chk.dataset.day;
                    porDia[dia] = (porDia[dia] || 0) + 1;
                    totalMin += minutos(chk.dataset.end) - minutos(chk.dataset.start);
                });

            if (totalMin === 0) {
                resumen.textContent = "Sin horas seleccionadas.";
                return;
            }

            const etiquetas = { 1: "Lun", 2: "Mar", 3: "Mié", 4: "Jue", 5: "Vie", 6: "Sáb" };
            const detalle = Object.keys(porDia)
                .sort()
                .map((d) => etiquetas[d] + ": " + porDia[d] + "h")
                .join(" · ");
            resumen.textContent =
                "Total: " + totalMin / 60 + " h/semana (" + detalle + ")";
        }

        pintarFilas();
        aplicarPrecarga();
        diasSemana.forEach(actualizarChkDia);
        actualizarResumen();

        tbodyHorario.addEventListener("change", (e) => {
            if (!e.target.classList.contains("chk-horario")) return;
            actualizarChkDia(e.target.dataset.day);
            actualizarResumen();
        });

        document.querySelectorAll(".chk-dia-completo").forEach((chk) => {
            chk.addEventListener("change", function () {
                tbodyHorario
                    .querySelectorAll('.chk-horario[data-day="' + this.dataset.day + '"]')
                    .forEach((celda) => {
                        celda.checked = this.checked;
                    });
                actualizarResumen();
            });
        });
    }

    function recolectarFranjas() {
        const porDia = {};
        document
            .querySelectorAll("#tbodyHorarioVeterinario .chk-horario:checked")
            .forEach((chk) => {
                const dia = parseInt(chk.dataset.day, 10);
                (porDia[dia] = porDia[dia] || []).push({
                    inicio: minutos(chk.dataset.start),
                    fin: minutos(chk.dataset.end),
                });
            });

        const franjas = [];
        Object.keys(porDia)
            .sort((a, b) => a - b)
            .forEach((dia) => {
                const bloques = porDia[dia].sort((a, b) => a.inicio - b.inicio);
                let actual = null;
                bloques.forEach((b) => {
                    if (actual && b.inicio === actual.fin) {
                        actual.fin = b.fin;
                    } else {
                        if (actual) {
                            franjas.push({
                                day_of_week: Number(dia),
                                start_time: formato(actual.inicio),
                                end_time: formato(actual.fin),
                            });
                        }
                        actual = { inicio: b.inicio, fin: b.fin };
                    }
                });
                if (actual) {
                    franjas.push({
                        day_of_week: Number(dia),
                        start_time: formato(actual.inicio),
                        end_time: formato(actual.fin),
                    });
                }
            });

        return franjas;
    }

    // ------------------------------------------------------------------
    // Listado (DataTable server-side)
    // ------------------------------------------------------------------
    const tableEl = document.getElementById("table-veterinarios");
    if (tableEl) {
        let dataTable = null;
        let terminoBusqueda = "";
        let terminoBusquedaTimer = null;

        const ETIQUETAS_DIA = {
            0: "Dom",
            1: "Lun",
            2: "Mar",
            3: "Mié",
            4: "Jue",
            5: "Vie",
            6: "Sáb",
        };

        function renderUsuario(u) {
            const src = u.avatar
                ? u.avatar
                : "/assets/images/users/avatar-1.jpg";
            return (
                '<div class="d-flex align-items-center">' +
                '<img src="' +
                src +
                '" class="rounded-circle me-2" width="34" height="34" alt="avatar" style="object-fit:cover">' +
                "<strong>" +
                u.username +
                "</strong></div>"
            );
        }

        function renderHorario(u) {
            const horarios = u.horarios || [];
            if (!horarios.length) {
                return '<span class="text-muted fs-12">Sin horario definido</span>';
            }
            const porDia = {};
            horarios.forEach((h) => {
                (porDia[h.day_of_week] = porDia[h.day_of_week] || []).push(
                    h.start_time + "–" + h.end_time,
                );
            });
            return Object.keys(porDia)
                .sort((a, b) => a - b)
                .map(
                    (dia) =>
                        '<div class="fs-12"><strong>' +
                        ETIQUETAS_DIA[dia] +
                        ":</strong> " +
                        porDia[dia].join(", ") +
                        "</div>",
                )
                .join("");
        }

        function renderEstado(u) {
            return u.is_active
                ? '<span class="badge bg-secondary-subtle text-success">Activo</span>'
                : '<span class="badge bg-secondary-subtle text-danger">Inactivo</span>';
        }

        function renderAcciones(u) {
            return (
                '<a href="' +
                u.edit_url +
                '" class="btn btn-soft-primary btn-sm me-1" title="Editar">' +
                '<i class="ri-pencil-line"></i></a>' +
                '<button type="button" class="btn btn-soft-danger btn-sm btn-eliminar-vet" data-id="' +
                u.id +
                '" title="Eliminar"><i class="ri-delete-bin-line"></i></button>' +
                '<button type="button" class="btn btn-soft-secondary btn-sm btn-toggle-vet" data-id="' +
                u.id +
                '" title="Activar/Desactivar"><i class="ri-arrow-left-right-line"></i></button>'
            );
        }

        function cargarDatos() {
            const url = "/admin/veterinarios";

            if (!dataTable) {
                dataTable = $("#table-veterinarios").DataTable({
                    serverSide: true,
                    processing: true,
                    pageLength: 15,
                    searching: false,
                    lengthChange: false,
                    info: false,
                    ajax: function (data, callback) {
                        const params = {
                            per_page: data.length,
                            page: Math.floor(data.start / data.length) + 1,
                        };
                        if (terminoBusqueda) {
                            params.search = terminoBusqueda;
                        }
                        if (data.order && data.order.length) {
                            params.sort_by = data.order[0].column;
                            params.sort_dir = data.order[0].dir;
                        }
                        ajax.get(url, params).then((res) => {
                            callback({
                                draw: data.draw,
                                recordsTotal: res.pagination.total,
                                recordsFiltered: res.pagination.total,
                                data: res.data.map((u) => ({
                                    usuario: renderUsuario(u),
                                    email: u.email,
                                    branch: u.branch || "—",
                                    horario: renderHorario(u),
                                    estado: renderEstado(u),
                                    acciones: renderAcciones(u),
                                })),
                            });
                        });
                    },
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json",
                    },
                    columns: [
                        { data: "usuario", orderable: false },
                        { data: "email" },
                        { data: "branch", orderable: false },
                        { data: "horario", orderable: false },
                        { data: "estado", orderable: false },
                        { data: "acciones", orderable: false },
                    ],
                    order: [],
                    drawCallback: function () {
                        bindHandlers();
                    },
                });
            } else {
                dataTable.ajax.reload();
            }
            return dataTable;
        }

        function bindHandlers() {
            document
                .querySelectorAll(".btn-eliminar-vet")
                .forEach((btn) => {
                    btn.addEventListener("click", function () {
                        eliminar(parseInt(this.dataset.id, 10));
                    });
                });

            document.querySelectorAll(".btn-toggle-vet").forEach((btn) => {
                btn.addEventListener("click", function () {
                    toggleStatus(parseInt(this.dataset.id, 10));
                });
            });
        }

        function eliminar(id) {
            Swal.fire({
                icon: "warning",
                title: "¿Eliminar veterinario?",
                text: "Esta acción no se puede deshacer.",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
                confirmButtonColor: "#d33",
            }).then((result) => {
                if (!result.isConfirmed) return;

                ajax.delete("/admin/veterinarios/" + id)
                    .then((res) => {
                        Swal.fire("Eliminado", res.message, "success");
                        cargarDatos();
                    })
                    .catch(() => {});
            });
        }

        function toggleStatus(id) {
            ajax.patch("/admin/veterinarios/" + id + "/toggle-status")
                .then((res) => {
                    Swal.fire("Listo", res.message, "success");
                    cargarDatos();
                })
                .catch(() => {});
        }

        $("#busquedaVeterinarios").on("input", function () {
            const termino = this.value;
            clearTimeout(terminoBusquedaTimer);
            terminoBusquedaTimer = setTimeout(function () {
                terminoBusqueda = termino.trim();
                dataTable.ajax.reload();
            }, 350);
        });

        $("#busquedaVeterinarios").on("keydown", function (e) {
            if (e.key === "Enter") e.preventDefault();
        });

        const formFiltros = document.getElementById("formFiltrosVeterinarios");
        if (formFiltros) {
            formFiltros.addEventListener("submit", (e) => {
                e.preventDefault();
                dataTable.ajax.reload();
            });
            document
                .getElementById("btnLimpiarFiltrosVeterinarios")
                ?.addEventListener("click", () => {
                    formFiltros.reset();
                    document.getElementById("busquedaVeterinarios").value = "";
                    terminoBusqueda = "";
                    dataTable.ajax.reload();
                });
        }

        cargarDatos();
    }

    // ------------------------------------------------------------------
    // Crear / editar veterinario
    // ------------------------------------------------------------------
    const formCrear = document.getElementById("formCrearVeterinario");
    if (formCrear) {
        const btn = document.getElementById("btnGuardarVeterinario");

        formCrear.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formCrearVeterinario");
            const datos = recolectarDatosFormulario(formCrear);
            btn.disabled = true;

            ajax.post("/admin/veterinarios", datos)
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/veterinarios";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacionVeterinario(
                            error.response.errors,
                            "formCrearVeterinario",
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }

    const formEditar = document.getElementById("formEditarVeterinario");
    if (formEditar) {
        const btn = document.getElementById("btnActualizarVeterinario");

        formEditar.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formEditarVeterinario");
            const datos = recolectarDatosFormulario(formEditar);
            btn.disabled = true;

            ajax.put("/admin/veterinarios/" + formEditar.dataset.id, datos)
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/veterinarios";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacionVeterinario(
                            error.response.errors,
                            "formEditarVeterinario",
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }
})();
