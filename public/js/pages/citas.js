(function () {
    "use strict";

    // ------------------------------------------------------------------
    // Helpers de validación de formulario (inline, sin depender de js/helpers/)
    // ------------------------------------------------------------------
    window.pintarErroresValidacion = function (errors, formId) {
        const form = document.getElementById(formId);
        if (!form) return;

        limpiarErroresValidacion(formId);

        Object.entries(errors || {}).forEach(([field, mensajes]) => {
            const nombreCampo = field.replace(
                /\.([a-zA-Z0-9_]+)/g,
                (_, k) => "[" + k + "]",
            );
            const input =
                form.querySelector(`[name="${field}"]`) ||
                form.querySelector(`[name="${nombreCampo}"]`);
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

    let terminoBusqueda = "";

    function recolectarFiltros(form) {
        const data = {};
        const campos = [
            "veterinarian_id",
            "species_id",
            "appointment_date_from",
            "appointment_date_to",
            "status",
        ];
        campos.forEach((campo) => {
            const el = form.elements[campo];
            if (el && el.value && el.value.trim() !== "") {
                data[campo] = el.value.trim();
            }
        });
        return data;
    }

    function limpiarBuscador() {
        const input = document.getElementById("busquedaCitas");
        if (input) input.value = "";
        terminoBusqueda = "";
    }

    function recolectarDatos(form) {
        const datos = new FormData();
        const pet_id = form.elements["pet_id"];
        if (pet_id) datos.append("pet_id", pet_id.value.trim());
        const veterinarian_id = form.elements["veterinarian_id"];
        if (veterinarian_id)
            datos.append("veterinarian_id", veterinarian_id.value.trim());
        const service_id = form.elements["service_id"];
        if (service_id && service_id.value && service_id.value !== "__nuevo__")
            datos.append("service_id", service_id.value.trim());
        const appointment_date = form.elements["appointment_date"];
        if (appointment_date)
            datos.append("appointment_date", appointment_date.value.trim());
        const appointment_time = form.elements["appointment_time"];
        if (appointment_time)
            datos.append("appointment_time", appointment_time.value.trim());
        const reason = form.elements["reason"];
        if (reason) datos.append("reason", reason.value.trim());
        const status = form.elements["status"];
        if (status) datos.append("status", status.value.trim());
        const payment_method = form.elements["payment_method"];
        if (payment_method)
            datos.append("payment_method", payment_method.value.trim());
        const advance_amount = form.elements["advance_amount"];
        if (advance_amount)
            datos.append("advance_amount", advance_amount.value.trim());
        const nuevoNombre = form.elements["new_service[name]"];
        if (nuevoNombre && nuevoNombre.value.trim()) {
            datos.append("new_service[name]", nuevoNombre.value.trim());
            datos.append(
                "new_service[category]",
                form.elements["new_service[category]"].value.trim(),
            );
            datos.append(
                "new_service[base_price]",
                form.elements["new_service[base_price]"].value.trim(),
            );
            datos.append(
                "new_service[duration_minutes]",
                form.elements["new_service[duration_minutes]"].value.trim(),
            );
        }
        return datos;
    }

    const tableEl = document.getElementById("table-citas");
    if (tableEl) {
        let dataTable = null;
        let terminoBusquedaTimer = null;

        function getFilters() {
            const form = document.getElementById("formFiltrosCitas");
            if (!form) return {};
            const filtros = recolectarFiltros(form) || {};
            if (terminoBusqueda) filtros.search = terminoBusqueda;
            return filtros;
        }

        function cargarDatos() {
            if (!dataTable) {
                dataTable = $("#table-citas").DataTable({
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
                        if (data.order && data.order.length) {
                            params.sort_by = data.order[0].column;
                            params.sort_dir = data.order[0].dir;
                        }
                        Object.assign(params, getFilters());
                        ajax.get("/admin/citas", params).then((res) => {
                            callback({
                                draw: data.draw,
                                recordsTotal: res.pagination.total,
                                recordsFiltered: res.pagination.total,
                                data: res.data,
                            });
                        });
                    },
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json",
                    },
                    columns: [
                        { data: "id" },
                        {
                            data: "pet_name",
                            render: (data, type, row) => row.pet_name || "-",
                        },
                        {
                            data: "veterinarian",
                            render: (data, type, row) =>
                                row.veterinarian || "-",
                        },
                        {
                            data: "service",
                            render: (data, type, row) => row.service || "—",
                        },
                        { data: "appointment_date" },
                        { data: "appointment_time" },
                        {
                            data: "status",
                            render: (data, type, row) =>
                                renderEstado(row.status),
                            orderable: false,
                        },
                        {
                            data: null,
                            render: (data, type, row) => renderAcciones(row),
                            orderable: false,
                        },
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

        function renderEstado(status) {
            const map = {
                pendiente: "bg-warning-subtle text-warning",
                confirmada: "bg-info-subtle text-info",
                completada: "bg-success-subtle text-success",
                cancelada: "bg-danger-subtle text-danger",
            };
            return (
                '<span class="badge ' +
                (map[status] || "bg-secondary-subtle text-secondary") +
                '">' +
                status.charAt(0).toUpperCase() +
                status.slice(1) +
                "</span>"
            );
        }

        function renderAcciones(c) {
            return (
                '<a href="' +
                c.edit_url +
                '" class="btn btn-soft-primary btn-sm me-1" title="Editar">' +
                '<i class="ri-pencil-line"></i></a>' +
                '<div class="btn-group dropstart d-inline-block me-1">' +
                '<button type="button" class="btn btn-soft-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">' +
                '<i class="ri-shuffle-line"></i></button>' +
                '<ul class="dropdown-menu">' +
                '<li><button type="button" class="dropdown-item btn-cambiar-estado" data-id="' +
                c.id +
                '" data-estado="pendiente">Pendiente</button></li>' +
                '<li><button type="button" class="dropdown-item btn-cambiar-estado" data-id="' +
                c.id +
                '" data-estado="confirmada">Confirmada</button></li>' +
                '<li><button type="button" class="dropdown-item btn-cambiar-estado" data-id="' +
                c.id +
                '" data-estado="completada">Completada</button></li>' +
                '<li><button type="button" class="dropdown-item btn-cambiar-estado" data-id="' +
                c.id +
                '" data-estado="cancelada">Cancelada</button></li>' +
                "</ul></div>" +
                '<button type="button" class="btn btn-soft-danger btn-sm btn-eliminar-cita" data-id="' +
                c.id +
                '" title="Eliminar"><i class="ri-delete-bin-line"></i></button>'
            );
        }

        function bindHandlers() {
            document.querySelectorAll(".btn-eliminar-cita").forEach((btn) => {
                btn.addEventListener("click", function () {
                    eliminar(parseInt(this.dataset.id, 10));
                });
            });

            document.querySelectorAll(".btn-cambiar-estado").forEach((btn) => {
                btn.addEventListener("click", function () {
                    cambiarEstado(
                        parseInt(this.dataset.id, 10),
                        this.dataset.estado,
                    );
                });
            });
        }

        function cambiarEstado(id, estado) {
            ajax.patch("/admin/citas/" + id + "/estado", {
                status: estado,
            })
                .then((res) => {
                    Swal.fire("Listo", res.message, "success");
                    cargarDatos();
                })
                .catch((error) => {
                    Swal.fire(
                        "Error",
                        error.response?.message ||
                            "No se pudo actualizar el estado.",
                        "error",
                    );
                });
        }

        function eliminar(id) {
            Swal.fire({
                icon: "warning",
                title: "¿Eliminar cita?",
                text: "Esta acción no se puede deshacer.",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
                confirmButtonColor: "#d33",
            }).then((result) => {
                if (!result.isConfirmed) return;

                ajax.delete("/admin/citas/" + id)
                    .then((res) => {
                        Swal.fire("Eliminado", res.message, "success");
                        cargarDatos();
                    })
                    .catch(() => {});
            });
        }

        $("#busquedaCitas").on("input", function () {
            const termino = this.value;
            clearTimeout(terminoBusquedaTimer);
            terminoBusquedaTimer = setTimeout(function () {
                terminoBusqueda = termino.trim();
                dataTable.ajax.reload();
            }, 350);
        });

        $("#busquedaCitas").on("keydown", function (e) {
            if (e.key === "Enter") e.preventDefault();
        });

        const formFiltros = document.getElementById("formFiltrosCitas");
        if (formFiltros) {
            formFiltros.addEventListener("submit", (e) => {
                e.preventDefault();
                cargarDatos();
            });
            document
                .getElementById("btnLimpiarFiltros")
                ?.addEventListener("click", () => {
                    formFiltros.reset();
                    limpiarBuscador();
                    cargarDatos();
                });
        }

        cargarDatos();
    }

    function initCitaForm(form) {
        const esEdicion = form.id === "formEditarCita";
        const btn = esEdicion
            ? document.getElementById("btnActualizarCita")
            : document.getElementById("btnGuardarCita");
        const selectServicio = document.getElementById("service_id");
        const bloqueNuevo = document.getElementById("bloqueNuevoServicio");
        const fechaInput = document.getElementById("appointment_date");
        const bloqueDispo = document.getElementById("bloqueDisponibilidad");
        const sinDispo = document.getElementById("sinDisponibilidad");
        const contenedorDispo = document.getElementById(
            "contenedorDisponibilidad",
        );
        const bloqueHoras = document.getElementById("bloqueHoras");
        const contenedorHoras = document.getElementById("contenedorHoras");
        const nombreVetSel = document.getElementById(
            "nombreVetSeleccionado",
        );
        const hiddenVet = document.getElementById("veterinarian_id");
        const hiddenHora = document.getElementById("appointment_time");
        const selectMascota = document.getElementById("pet_id");
        const bloquePreview = document.getElementById(
            "bloquePreviewMascota",
        );
        const previewFoto = document.getElementById("previewFotoMascota");
        const previewDatos = document.getElementById("previewDatosMascota");
        const totalPago = document.getElementById("total_pago");

        const nuevaPrecio = document.getElementById("nuevo_service_price");

        let disponibilidadCache = {};
        let vetSeleccionado = null;
        let initSeleccionado = false;

        function actualizarTotal() {
            let total = 0;
            if (selectServicio.value && selectServicio.value !== "__nuevo__") {
                total =
                    parseFloat(
                        selectServicio.selectedOptions[0].dataset.price,
                    ) || 0;
            } else if (selectServicio.value === "__nuevo__") {
                total = parseFloat(nuevaPrecio.value) || 0;
            }
            totalPago.value = total.toFixed(2);
        }

        function limpiarErroresCamposNuevos() {
            form
                .querySelectorAll("#bloqueNuevoServicio .is-invalid")
                .forEach((el) => el.classList.remove("is-invalid"));
            form
                .querySelectorAll("#bloqueNuevoServicio .invalid-feedback")
                .forEach((el) => (el.textContent = ""));
        }

        selectServicio.addEventListener("change", function () {
            const esNuevo = this.value === "__nuevo__";
            bloqueNuevo.classList.toggle("d-none", !esNuevo);
            if (esNuevo) {
                selectServicio.classList.add("is-valid");
                selectServicio.classList.remove("is-invalid");
            } else {
                limpiarErroresCamposNuevos();
            }
            actualizarTotal();
        });

        nuevaPrecio.addEventListener("input", actualizarTotal);

        fechaInput.addEventListener("change", cargarDisponibilidad);

        selectMascota.addEventListener("change", renderPreviewMascota);

        function renderPreviewMascota() {
            const data = (window.citasPacientesData || {})[
                selectMascota.value
            ];
            if (!data) {
                bloquePreview.classList.add("d-none");
                return;
            }
            previewFoto.innerHTML = data.photo
                ? '<img src="' +
                  data.photo +
                  '" class="rounded-circle" style="width:44px;height:44px;object-fit:cover;" alt="' +
                  data.name +
                  '">'
                : '<i class="ri-paw-line fs-2 text-muted"></i>';
            previewDatos.innerHTML =
                '<strong class="text-dark">' +
                data.name +
                "</strong><br>" +
                [data.species, data.breed, data.gender]
                    .filter(Boolean)
                    .join(" · ") +
                (data.weight ? " · " + data.weight + " kg" : "") +
                "<br><i class='ri-user-line me-1'></i>" +
                (data.owner || "-");
            bloquePreview.classList.remove("d-none");
        }

        function cargarDisponibilidad() {
            const fecha = fechaInput.value;
            hiddenVet.value = "";
            hiddenHora.value = "";
            vetSeleccionado = null;
            initSeleccionado = false;
            contenedorHoras.innerHTML = "";
            contenedorDispo.innerHTML = "";
            bloqueHoras.classList.add("d-none");
            bloqueDispo.classList.add("d-none");
            sinDispo.classList.add("d-none");

            if (!fecha) return;

            contenedorDispo.innerHTML =
                '<div class="col-12 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Cargando disponibilidad…</div>';
            bloqueDispo.classList.remove("d-none");

            ajax.get("/admin/citas/disponibilidad", { fecha })
                .then((res) => {
                    const vets = res.data || [];
                    if (!vets.length) {
                        bloqueDispo.classList.add("d-none");
                        sinDispo.classList.remove("d-none");
                        return;
                    }

                    disponibilidadCache = {};
                    const cards = [];
                    vets.forEach((vet) => {
                        disponibilidadCache[vet.veterinarian_id] = vet.slots;
                        cards.push(renderVetCard(vet));
                    });
                    contenedorDispo.innerHTML = cards.join("");
                    bindVetCards();
                    preseleccionarEnEdicion();
                })
                .catch(() => {
                    contenedorDispo.innerHTML =
                        '<div class="col-12"><div class="alert alert-danger mb-0">No se pudo cargar la disponibilidad.</div></div>';
                });
        }

        function preseleccionarEnEdicion() {
            if (!esEdicion || initSeleccionado) return;
            const init = window.citaEditarInit;
            if (!init || !init.veterinario) return;

            initSeleccionado = true;
            if (
                document.querySelector(
                    '#contenedorDisponibilidad .vet-card[data-vet="' +
                        init.veterinario +
                        '"] .btn-aplicar-vet',
                )
            ) {
                aplicarVet(init.veterinario, init.hora);
            }
        }

        function renderVetCard(vet) {
            const estado = vet.estado || (vet.disponible ? "disponible" : "ocupado");
            const sinHorario = estado === "sin_horario";
            const ocupado = estado === "ocupado";
            const disponible = estado === "disponible";
            const tachado = sinHorario || ocupado;
            const badge =
                sinHorario
                    ? '<span class="badge bg-secondary-subtle text-secondary"><i class="ri-calendar-close-line me-1"></i>Sin horario</span>'
                    : ocupado
                      ? '<span class="badge bg-danger-subtle text-danger"><i class="ri-close-circle-line me-1"></i>Ocupado</span>'
                      : '<span class="badge bg-success-subtle text-success"><i class="ri-checkbox-circle-line me-1"></i>Disponible</span>';
            const boton = disponible
                ? '<button type="button" class="btn btn-sm btn-soft-success w-100 btn-aplicar-vet">' +
                  '<i class="ri-check-line me-1"></i>Aplicar</button>'
                : '<button type="button" class="btn btn-sm btn-soft-secondary w-100" disabled>' +
                  (sinHorario
                      ? '<i class="ri-calendar-close-line me-1"></i>No disponible'
                      : '<i class="ri-time-line me-1"></i>Sin horas') +
                  "</button>";
            return (
                '<div class="col-md-4 col-lg-3">' +
                '<div class="card h-100 vet-card' +
                (tachado ? " vet-card-ocupado opacity-50" : "") +
                '" data-vet="' +
                vet.veterinarian_id +
                '">' +
                '<div class="card-body p-3">' +
                '<div class="d-flex align-items-center justify-content-between mb-1">' +
                '<h6 class="mb-0 text-truncate' +
                (tachado ? " text-decoration-line-through" : "") +
                '" title="' +
                vet.veterinarian +
                '">' +
                '<i class="ri-stethoscope-line me-1"></i>' +
                vet.veterinarian +
                "</h6>" +
                badge +
                "</div>" +
                '<div class="text-muted small mb-2">' +
                (vet.horario || "–") +
                "</div>" +
                boton +
                "</div>" +
                "</div>" +
                "</div>"
            );
        }

        function bindVetCards() {
            document
                .querySelectorAll("#contenedorDisponibilidad .btn-aplicar-vet")
                .forEach((boton) => {
                    boton.addEventListener("click", function () {
                        const card = this.closest(".vet-card");
                        aplicarVet(card.dataset.vet);
                    });
                });
        }

        function aplicarVet(vetId, horaPreseleccionada) {
            document
                .querySelectorAll("#contenedorDisponibilidad .vet-card")
                .forEach((c) => {
                    const seleccionada = c.dataset.vet === String(vetId);
                    c.classList.toggle("border-primary", seleccionada);
                    c.classList.toggle("shadow-sm", seleccionada);
                });

            hiddenVet.value = vetId;
            vetSeleccionado = vetId;

            const slots = disponibilidadCache[vetId] || [];
            const nombre = document.querySelector(
                '#contenedorDisponibilidad .vet-card[data-vet="' +
                    vetId +
                    '"] h6',
            )?.textContent.trim();
            nombreVetSel.textContent = nombre || "";
            renderHoras(slots, horaPreseleccionada);
        }

        function renderHoras(slots, horaPreseleccionada) {
            contenedorHoras.innerHTML = "";
            if (!slots.length) {
                contenedorHoras.innerHTML =
                    '<span class="text-muted">Sin horas libres.</span>';
                bloqueHoras.classList.remove("d-none");
                return;
            }
            slots.forEach((hora) => {
                const boton = document.createElement("button");
                boton.type = "button";
                boton.className = "btn btn-sm btn-outline-primary";
                boton.textContent = hora;
                boton.dataset.hora = hora;
                if (horaPreseleccionada && hora === horaPreseleccionada) {
                    boton.classList.replace("btn-outline-primary", "btn-primary");
                }
                boton.addEventListener("click", function () {
                    hiddenHora.value = hora;
                    contenedorHoras
                        .querySelectorAll(".btn-primary")
                        .forEach((b) =>
                            b.classList.replace("btn-primary", "btn-outline-primary"),
                        );
                    this.classList.replace("btn-outline-primary", "btn-primary");
                });
                contenedorHoras.appendChild(boton);
            });
            if (horaPreseleccionada) {
                hiddenHora.value = horaPreseleccionada;
            }
            bloqueHoras.classList.remove("d-none");
        }

        form.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion(form.id);
            limpiarErroresCamposNuevos();

            const total = parseFloat(totalPago.value) || 0;
            const adelanto = parseFloat(
                document.getElementById("advance_amount").value,
            ) || 0;
            if (adelanto > total) {
                document
                    .getElementById("advance_amount")
                    .classList.add("is-invalid");
                const fb = document
                    .getElementById("advance_amount")
                    .closest(".col-md-4")
                    .querySelector(".invalid-feedback");
                if (fb)
                    fb.textContent =
                        "El adelanto no puede exceder el total a pagar.";
                return;
            }

            btn.disabled = true;
            const peticion = esEdicion
                ? ajax.put(
                      "/admin/citas/" + form.dataset.id,
                      recolectarDatos(form),
                  )
                : ajax.post("/admin/citas", recolectarDatos(form));

            peticion
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/citas";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(
                            error.response.errors,
                            form.id,
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });

        renderPreviewMascota();
        actualizarTotal();

        if (esEdicion && fechaInput.value) {
            cargarDisponibilidad();
        }
    }

    const formCita = document.getElementById("formCrearCita");
    if (formCita) initCitaForm(formCita);

    const formEditar = document.getElementById("formEditarCita");
    if (formEditar) initCitaForm(formEditar);
})();
