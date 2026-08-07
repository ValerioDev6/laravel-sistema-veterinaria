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

    function recolectarFiltros(form) {
        const data = {};
        const veterinario = form.elements["veterinarian_id"];
        if (veterinario) data.veterinarian_id = veterinario.value.trim();
        const fecha = form.elements["appointment_date"];
        if (fecha) data.appointment_date = fecha.value.trim();
        const estado = form.elements["status"];
        if (estado) data.status = estado.value.trim();
        return data;
    }

    function recolectarDatos(form) {
        const datos = new FormData();
        const pet_id = form.elements["pet_id"];
        if (pet_id) datos.append("pet_id", pet_id.value.trim());
        const veterinarian_id = form.elements["veterinarian_id"];
        if (veterinarian_id) datos.append("veterinarian_id", veterinarian_id.value.trim());
        const service_id = form.elements["service_id"];
        if (service_id) datos.append("service_id", service_id.value.trim());
        const appointment_date = form.elements["appointment_date"];
        if (appointment_date) datos.append("appointment_date", appointment_date.value.trim());
        const appointment_time = form.elements["appointment_time"];
        if (appointment_time) datos.append("appointment_time", appointment_time.value.trim());
        const reason = form.elements["reason"];
        if (reason) datos.append("reason", reason.value.trim());
        const status = form.elements["status"];
        if (status) datos.append("status", status.value.trim());
        return datos;
    }

    const tableEl = document.getElementById("table-citas");
    if (tableEl) {
        let dataTable = null;

        function getFilters() {
            const form = document.getElementById("formFiltrosCitas");
            if (!form) return {};
            return recolectarFiltros(form) || {};
        }

        function cargarDatos() {
            if (!dataTable) {
                dataTable = $("#table-citas").DataTable({
                    serverSide: true,
                    processing: true,
                    pageLength: 15,
                    ajax: function (data, callback) {
                        const params = {
                            per_page: data.length,
                            page: Math.floor(data.start / data.length) + 1,
                        };
                        if (data.search && data.search.value) {
                            params.search = data.search.value;
                        }
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
                        { data: "pet_name", render: (data, type, row) => row.pet_name || "-" },
                        { data: "veterinarian", render: (data, type, row) => row.veterinarian || "-" },
                        { data: "service", render: (data, type, row) => row.service || "—" },
                        { data: "appointment_date" },
                        { data: "appointment_time" },
                        { data: "status", render: (data, type, row) => renderEstado(row.status), orderable: false },
                        { data: null, render: (data, type, row) => renderAcciones(row), orderable: false },
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
                        error.response?.message || "No se pudo actualizar el estado.",
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
                    .catch((error) => {
                        if (error.status === 422) {
                            Swal.fire(
                                "No se pudo eliminar",
                                error.response?.errors?.cita?.[0],
                                "warning",
                            );
                        }
                    });
            });
        }

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
                    cargarDatos();
                });
        }

        cargarDatos();
    }

    const formCrear = document.getElementById("formCrearCita");
    if (formCrear) {
        const btn = document.getElementById("btnGuardarCita");
        formCrear.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formCrearCita");
            btn.disabled = true;
            ajax.post("/admin/citas", recolectarDatos(formCrear))
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/citas";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(error.response.errors, "formCrearCita");
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }

    const formEditar = document.getElementById("formEditarCita");
    if (formEditar) {
        const btn = document.getElementById("btnActualizarCita");
        formEditar.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formEditarCita");
            btn.disabled = true;
            ajax.put(
                "/admin/citas/" + formEditar.dataset.id,
                recolectarDatos(formEditar),
            )
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/citas";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(error.response.errors, "formEditarCita");
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }
})();