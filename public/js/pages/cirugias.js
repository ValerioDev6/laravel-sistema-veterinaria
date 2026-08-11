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
        const pet_id = form.elements["pet_id"];
        if (pet_id) datos.append("pet_id", pet_id.value.trim());
        const cita_id = form.elements["cita_id"];
        if (cita_id) datos.append("cita_id", cita_id.value.trim());
        const veterinarian_id = form.elements["veterinarian_id"];
        if (veterinarian_id) datos.append("veterinarian_id", veterinarian_id.value.trim());
        const surgery_date = form.elements["surgery_date"];
        if (surgery_date) datos.append("surgery_date", surgery_date.value.trim());
        const surgery_type = form.elements["surgery_type"];
        if (surgery_type) datos.append("surgery_type", surgery_type.value.trim());
        const outcome = form.elements["outcome"];
        if (outcome) datos.append("outcome", outcome.value.trim());
        const medical_notes = form.elements["medical_notes"];
        if (medical_notes) datos.append("medical_notes", medical_notes.value.trim());
        const status = form.elements["status"];
        if (status) datos.append("status", status.value.trim());
        return datos;
    }

    const tableEl = document.getElementById("table-cirugias");
    if (tableEl) {
        let dataTable = null;

        function cargarDatos() {
            const url = "/admin/cirugias";

            if (!dataTable) {
                dataTable = $("#table-cirugias").DataTable({
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
                estado: renderEstado(c.status),
                acciones: renderAcciones(c),
            };
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

        cargarDatos();
    }

    const formCrear = document.getElementById("formCrearCirugia");
    if (formCrear) {
        const btn = document.getElementById("btnGuardarCirugia");
        formCrear.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formCrearCirugia");
            btn.disabled = true;
            ajax.post("/admin/cirugias", recolectarDatos(formCrear))
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/cirugias";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(error.response.errors, "formCrearCirugia");
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }

    const formEditar = document.getElementById("formEditarCirugia");
    if (formEditar) {
        const btn = document.getElementById("btnActualizarCirugia");
        formEditar.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formEditarCirugia");
            btn.disabled = true;
            ajax.put(
                "/admin/cirugias/" + formEditar.dataset.id,
                recolectarDatos(formEditar),
            )
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/cirugias";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(error.response.errors, "formEditarCirugia");
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }
})();