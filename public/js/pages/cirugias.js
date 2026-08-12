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

    // ------------------------------------------------------------------
    // Preview de mascota
    // ------------------------------------------------------------------
    function initPreviewMascota(form) {
        const petSelect = form.elements["pet_id"];
        const bloque = document.getElementById("bloquePreviewMascota");
        const previewFoto = document.getElementById("previewFotoMascota");
        const previewDatos = document.getElementById("previewDatosMascota");
        if (!petSelect || !bloque) return;

        function render() {
            const data =
                (window.cirugiasPacientesData || {})[petSelect.value] || null;
            if (!data) {
                bloque.classList.add("d-none");
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
            bloque.classList.remove("d-none");
        }

        petSelect.addEventListener("change", render);

        if (form.dataset.id) {
            render();
        }
    }

    function recolectarDatos(form) {
        const datos = new FormData();
        const campos = [
            "pet_id",
            "cita_id",
            "surgery_type",
            "status",
            "outcome",
            "medical_notes",
            "reminder_date",
            "total",
            "payment_method",
            "advance_amount",
        ];
        campos.forEach((campo) => {
            const el = form.elements[campo];
            if (el && el.value && el.value.trim() !== "") {
                datos.append(campo, el.value.trim());
            }
        });
        const fecha = form.elements["surgery_date"];
        const hora = form.elements["surgery_time"];
        if (fecha && fecha.value) datos.append("surgery_date", fecha.value.trim());
        if (hora && hora.value) datos.append("surgery_time", hora.value.trim());
        const vet = form.elements["veterinarian_id"];
        if (vet && vet.value) datos.append("veterinarian_id", vet.value.trim());
        return datos;
    }

    // ------------------------------------------------------------------
    // Listado (DataTable + filtros)
    // ------------------------------------------------------------------
    const tableEl = document.getElementById("table-cirugias");
    if (tableEl) {
        let dataTable = null;
        let terminoBusqueda = "";
        let terminoBusquedaTimer = null;

        function recolectarFiltros(form) {
            const data = {};
            const campos = [
                "veterinarian_id",
                "species_id",
                "payment_status",
                "surgery_date_from",
                "surgery_date_to",
            ];
            campos.forEach((campo) => {
                const el = form.elements[campo];
                if (el && el.value && el.value.trim() !== "") {
                    data[campo] = el.value.trim();
                }
            });
            return data;
        }

        function getFilters() {
            const form = document.getElementById("formFiltrosCirugias");
            const filtros = form ? recolectarFiltros(form) : {};
            if (terminoBusqueda) filtros.search = terminoBusqueda;
            return filtros;
        }

        function cargarDatos() {
            if (!dataTable) {
                dataTable = $("#table-cirugias").DataTable({
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
                        ajax.get("/admin/cirugias", params).then((res) => {
                            callback({
                                draw: data.draw,
                                recordsTotal: res.pagination.total,
                                recordsFiltered: res.pagination.total,
                                data: res.data.map(datosCargados),
                            });
                        });
                    },
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json",
                    },
                    columns: [
                        { data: "id" },
                        { data: "mascota" },
                        { data: "tipo" },
                        { data: "veterinario" },
                        { data: "fecha" },
                        {
                            data: "pago",
                            render: (data, type, row) => renderPago(row),
                            orderable: false,
                        },
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

        function datosCargados(c) {
            return {
                id: c.id,
                mascota: c.pet_name,
                tipo: c.surgery_type || "—",
                veterinario: c.veterinarian,
                fecha: c.surgery_date,
                payment_status: c.payment_status,
                status: c.status,
                estado: renderEstado(c.status),
                acciones: renderAcciones(c),
            };
        }

        function renderPago(v) {
            const map = {
                pendiente: "bg-warning-subtle text-warning",
                parcial: "bg-info-subtle text-info",
                pagado: "bg-success-subtle text-success",
                anulado: "bg-danger-subtle text-danger",
            };
            const label = v.payment_status
                ? v.payment_status.charAt(0).toUpperCase() +
                  v.payment_status.slice(1)
                : "Sin factura";
            return (
                '<span class="badge ' +
                (map[v.payment_status] || "bg-secondary-subtle text-secondary") +
                '">' +
                label +
                "</span>"
            );
        }

        function renderEstado(status) {
            const map = {
                pendiente: "bg-warning-subtle text-warning",
                en_proceso: "bg-info-subtle text-info",
                completada: "bg-success-subtle text-success",
                cancelada: "bg-danger-subtle text-danger",
            };
            return (
                '<span class="badge ' +
                (map[status] || "bg-secondary-subtle text-secondary") +
                '">' +
                status.replace("_", " ").replace(/^\w/, (c) => c.toUpperCase()) +
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
                '<button type="button" class="btn btn-soft-warning btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">' +
                '<i class="ri-bank-card-line"></i></button>' +
                '<ul class="dropdown-menu">' +
                '<li><button type="button" class="dropdown-item btn-cambiar-estado-pago" data-id="' +
                c.id +
                '" data-estado="pendiente">Pago: Pendiente</button></li>' +
                '<li><button type="button" class="dropdown-item btn-cambiar-estado-pago" data-id="' +
                c.id +
                '" data-estado="parcial">Pago: Parcial</button></li>' +
                '<li><button type="button" class="dropdown-item btn-cambiar-estado-pago" data-id="' +
                c.id +
                '" data-estado="pagado">Pago: Pagado</button></li>' +
                '<li><button type="button" class="dropdown-item btn-cambiar-estado-pago" data-id="' +
                c.id +
                '" data-estado="anulado">Pago: Anulado</button></li>' +
                "</ul></div>" +
                '<div class="btn-group dropstart d-inline-block me-1">' +
                '<button type="button" class="btn btn-soft-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">' +
                '<i class="ri-shuffle-line"></i></button>' +
                '<ul class="dropdown-menu">' +
                '<li><button type="button" class="dropdown-item btn-cambiar-estado" data-id="' +
                c.id +
                '" data-estado="pendiente">Pendiente</button></li>' +
                '<li><button type="button" class="dropdown-item btn-cambiar-estado" data-id="' +
                c.id +
                '" data-estado="en_proceso">En proceso</button></li>' +
                '<li><button type="button" class="dropdown-item btn-cambiar-estado" data-id="' +
                c.id +
                '" data-estado="completada">Completada</button></li>' +
                '<li><button type="button" class="dropdown-item btn-cambiar-estado" data-id="' +
                c.id +
                '" data-estado="cancelada">Cancelada</button></li>' +
                "</ul></div>" +
                '<button type="button" class="btn btn-soft-danger btn-sm btn-eliminar-cirugia" data-id="' +
                c.id +
                '" title="Eliminar"><i class="ri-delete-bin-line"></i></button>'
            );
        }

        function bindHandlers() {
            document
                .querySelectorAll(".btn-eliminar-cirugia")
                .forEach((btn) => {
                    btn.addEventListener("click", function () {
                        eliminar(parseInt(this.dataset.id, 10));
                    });
                });

            document
                .querySelectorAll(".btn-cambiar-estado")
                .forEach((btn) => {
                    btn.addEventListener("click", function () {
                        cambiarEstado(
                            parseInt(this.dataset.id, 10),
                            this.dataset.estado,
                        );
                    });
                });

            document
                .querySelectorAll(".btn-cambiar-estado-pago")
                .forEach((btn) => {
                    btn.addEventListener("click", function () {
                        cambiarEstadoPago(
                            parseInt(this.dataset.id, 10),
                            this.dataset.estado,
                        );
                    });
                });
        }

        function cambiarEstado(id, estado) {
            ajax.patch("/admin/cirugias/" + id + "/estado", {
                status: estado,
            })
                .then((res) => {
                    Swal.fire("Listo", res.message, "success");
                    cargarDatos();
                })
                .catch((error) => {
                    Swal.fire(
                        "Error",
                        error.response?.message || "No se pudo actualizar el estado.",
                        "error",
                    );
                });
        }

        function cambiarEstadoPago(id, estado) {
            ajax.patch("/admin/cirugias/" + id + "/estado-pago", {
                status: estado,
            })
                .then((res) => {
                    Swal.fire("Listo", res.message, "success");
                    cargarDatos();
                })
                .catch((error) => {
                    Swal.fire(
                        "Error",
                        error.response?.message || "No se pudo actualizar el pago.",
                        "error",
                    );
                });
        }

        function eliminar(id) {
            Swal.fire({
                icon: "warning",
                title: "¿Eliminar cirugía?",
                text: "Esta acción no se puede deshacer.",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
                confirmButtonColor: "#d33",
            }).then((result) => {
                if (!result.isConfirmed) return;

                ajax.delete("/admin/cirugias/" + id)
                    .then((res) => {
                        Swal.fire("Eliminado", res.message, "success");
                        cargarDatos();
                    })
                    .catch(() => {});
            });
        }

        $("#busquedaCirugias").on("input", function () {
            const termino = this.value;
            clearTimeout(terminoBusquedaTimer);
            terminoBusquedaTimer = setTimeout(function () {
                terminoBusqueda = termino.trim();
                dataTable.ajax.reload();
            }, 350);
        });

        $("#busquedaCirugias").on("keydown", function (e) {
            if (e.key === "Enter") e.preventDefault();
        });

        const formFiltros = document.getElementById("formFiltrosCirugias");
        if (formFiltros) {
            formFiltros.addEventListener("submit", (e) => {
                e.preventDefault();
                cargarDatos();
            });
            document
                .getElementById("btnLimpiarFiltros")
                ?.addEventListener("click", () => {
                    formFiltros.reset();
                    terminoBusqueda = "";
                    const input = document.getElementById("busquedaCirugias");
                    if (input) input.value = "";
                    cargarDatos();
                });
        }

        cargarDatos();
    }

    // ------------------------------------------------------------------
    // Crear / Editar (desde fecha → veterinarios → horas, igual que Vacunas)
    // ------------------------------------------------------------------
    function initCirugiaForm(form) {
        const esEdicion = form.id === "formEditarCirugia";
        const btn = esEdicion
            ? document.getElementById("btnActualizarCirugia")
            : document.getElementById("btnGuardarCirugia");
        const fechaInput = document.getElementById("surgery_date");
        const bloqueDispo = document.getElementById("bloqueDisponibilidad");
        const sinDispo = document.getElementById("sinDisponibilidad");
        const contenedorDispo = document.getElementById(
            "contenedorDisponibilidad",
        );
        const bloqueHoras = document.getElementById("bloqueHoras");
        const contenedorHoras = document.getElementById("contenedorHoras");
        const nombreVetSel = document.getElementById("nombreVetSeleccionado");
        const hiddenVet = document.getElementById("veterinarian_id");
        const hiddenHora = document.getElementById("surgery_time");

        let disponibilidadCache = {};
        let vetSeleccionado = null;
        let initSeleccionado = false;

        initPreviewMascota(form);

        fechaInput.addEventListener("change", cargarDisponibilidad);

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

            ajax.get("/admin/cirugias/disponibilidad", { fecha })
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
            const init = window.cirugiaEditarInit;
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

            const total = parseFloat(
                document.getElementById("total_pago").value,
            ) || 0;
            const adelantoInput = document.getElementById("advance_amount");
            const adelanto = parseFloat(adelantoInput.value) || 0;
            if (adelanto > total) {
                adelantoInput.classList.add("is-invalid");
                const fb = adelantoInput
                    .closest(".col-md-4")
                    .querySelector(".invalid-feedback");
                if (fb)
                    fb.textContent =
                        "El adelanto no puede exceder el total a pagar.";
                return;
            }

            if (!hiddenVet.value) {
                Swal.fire("Aviso", "Selecciona un veterinario disponible para la fecha.", "warning");
                return;
            }
            if (!hiddenHora.value) {
                Swal.fire("Aviso", "Selecciona una hora libre para el veterinario.", "warning");
                return;
            }

            btn.disabled = true;
            const peticion = esEdicion
                ? ajax.put(
                      "/admin/cirugias/" + form.dataset.id,
                      recolectarDatos(form),
                  )
                : ajax.post("/admin/cirugias", recolectarDatos(form));

            peticion
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/cirugias";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(error.response.errors, form.id);
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });

        if (esEdicion && fechaInput.value) {
            cargarDisponibilidad();
        }
    }

    const formCrear = document.getElementById("formCrearCirugia");
    if (formCrear) initCirugiaForm(formCrear);

    const formEditar = document.getElementById("formEditarCirugia");
    if (formEditar) initCirugiaForm(formEditar);
})();