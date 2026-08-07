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

    function recolectarDatos(form) {
        const datos = new FormData();
        const veterinarian_id = form.elements["veterinarian_id"];
        if (veterinarian_id) datos.append("veterinarian_id", veterinarian_id.value.trim());
        const day_of_week = form.elements["day_of_week"];
        if (day_of_week) datos.append("day_of_week", day_of_week.value.trim());
        const start_time = form.elements["start_time"];
        if (start_time) datos.append("start_time", start_time.value.trim());
        const end_time = form.elements["end_time"];
        if (end_time) datos.append("end_time", end_time.value.trim());
        const is_active = form.elements["is_active"];
        if (is_active && is_active.checked) datos.append("is_active", is_active.value);
        return datos;
    }

    const tableEl = document.getElementById("table-schedules");
    if (tableEl) {
        let dataTable = null;

        function cargarDatos() {
            const url = "/admin/veterinarian-schedules";

            if (!dataTable) {
                dataTable = $("#table-schedules").DataTable({
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
                        ajax.get(url, params).then((res) => {
                            callback({
                                draw: data.draw,
                                recordsTotal: res.pagination.total,
                                recordsFiltered: res.pagination.total,
                                data: res.data.map((s) => ({
                                    id: s.id,
                                    veterinario: s.veterinarian,
                                    dia: s.day_label,
                                    inicio: s.start_time,
                                    fin: s.end_time,
                                    estado: s.is_active
                                        ? '<span class="badge bg-success-subtle text-success">Activo</span>'
                                        : '<span class="badge bg-danger-subtle text-danger">Inactivo</span>',
                                    acciones: renderAcciones(s),
                                })),
                            });
                        });
                    },
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json",
                    },
                    columns: [
                        { data: "id" },
                        { data: "veterinario" },
                        { data: "dia" },
                        { data: "inicio" },
                        { data: "fin" },
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

        function renderAcciones(s) {
            return (
                '<a href="' +
                s.edit_url +
                '" class="btn btn-soft-primary btn-sm me-1" title="Editar">' +
                '<i class="ri-pencil-line"></i></a>' +
                '<button type="button" class="btn btn-soft-danger btn-sm btn-eliminar-schedule" data-id="' +
                s.id +
                '" title="Eliminar"><i class="ri-delete-bin-line"></i></button>'
            );
        }

        function bindHandlers() {
            document
                .querySelectorAll(".btn-eliminar-schedule")
                .forEach((btn) => {
                    btn.addEventListener("click", function () {
                        eliminar(parseInt(this.dataset.id, 10));
                    });
                });
        }

        function eliminar(id) {
            Swal.fire({
                icon: "warning",
                title: "¿Eliminar horario?",
                text: "Esta acción no se puede deshacer.",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
                confirmButtonColor: "#d33",
            }).then((result) => {
                if (!result.isConfirmed) return;

                ajax.delete("/admin/veterinarian-schedules/" + id)
                    .then((res) => {
                        Swal.fire("Eliminado", res.message, "success");
                        cargarDatos();
                    })
                    .catch(() => {});
            });
        }

        cargarDatos();
    }

    const formCrear = document.getElementById("formCrearSchedule");
    if (formCrear) {
        const btn = document.getElementById("btnGuardarSchedule");
        formCrear.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formCrearSchedule");
            const datos = recolectarDatos(formCrear);
            btn.disabled = true;
            ajax.post("/admin/veterinarian-schedules", datos)
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/veterinarian-schedules";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(
                            error.response.errors,
                            "formCrearSchedule",
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }

    const formEditar = document.getElementById("formEditarSchedule");
    if (formEditar) {
        const btn = document.getElementById("btnActualizarSchedule");
        formEditar.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formEditarSchedule");
            const datos = recolectarDatos(formEditar);
            btn.disabled = true;
            ajax.put(
                "/admin/veterinarian-schedules/" + formEditar.dataset.id,
                datos,
            )
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/veterinarian-schedules";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(
                            error.response.errors,
                            "formEditarSchedule",
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }
})();