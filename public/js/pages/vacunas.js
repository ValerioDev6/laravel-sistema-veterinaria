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
        const vaccine_type_id = form.elements["vaccine_type_id"];
        if (vaccine_type_id) datos.append("vaccine_type_id", vaccine_type_id.value.trim());
        const veterinarian_id = form.elements["veterinarian_id"];
        if (veterinarian_id) datos.append("veterinarian_id", veterinarian_id.value.trim());
        const vaccination_date = form.elements["vaccination_date"];
        if (vaccination_date) datos.append("vaccination_date", vaccination_date.value.trim());
        const next_due_date = form.elements["next_due_date"];
        if (next_due_date) datos.append("next_due_date", next_due_date.value.trim());
        const cita_id = form.elements["cita_id"];
        if (cita_id) datos.append("cita_id", cita_id.value.trim());
        return datos;
    }

    const tableEl = document.getElementById("table-vacunas");
    if (tableEl) {
        let dataTable = null;

        function cargarDatos() {
            const url = "/admin/vacunas";

            if (!dataTable) {
                dataTable = $("#table-vacunas").DataTable({
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
                        { data: "vacuna" },
                        { data: "veterinario" },
                        { data: "fecha" },
                        { data: "proxima" },
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

        function datosCargados(v) {
            return {
                id: v.id,
                mascota: v.pet_name,
                vacuna: v.vaccine_type,
                veterinario: v.veterinarian,
                fecha: v.vaccination_date,
                proxima: v.next_due_date || "—",
                acciones: renderAcciones(v),
            };
        }

        function renderAcciones(v) {
            return (
                '<a href="' +
                v.edit_url +
                '" class="btn btn-soft-primary btn-sm me-1" title="Editar">' +
                '<i class="ri-pencil-line"></i></a>' +
                '<button type="button" class="btn btn-soft-danger btn-sm btn-eliminar-vacuna" data-id="' +
                v.id +
                '" title="Eliminar"><i class="ri-delete-bin-line"></i></button>'
            );
        }

        function bindHandlers() {
            document
                .querySelectorAll(".btn-eliminar-vacuna")
                .forEach((btn) => {
                    btn.addEventListener("click", function () {
                        eliminar(parseInt(this.dataset.id, 10));
                    });
                });
        }

        function eliminar(id) {
            Swal.fire({
                icon: "warning",
                title: "¿Eliminar vacuna?",
                text: "Esta acción no se puede deshacer.",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
                confirmButtonColor: "#d33",
            }).then((result) => {
                if (!result.isConfirmed) return;

                ajax.delete("/admin/vacunas/" + id)
                    .then((res) => {
                        Swal.fire("Eliminado", res.message, "success");
                        cargarDatos();
                    })
                    .catch((error) => {
                        if (error.status === 422) {
                            Swal.fire(
                                "No se pudo eliminar",
                                error.response?.errors?.vacuna?.[0],
                                "warning",
                            );
                        }
                    });
            });
        }

        cargarDatos();
    }

    const formCrear = document.getElementById("formCrearVacuna");
    if (formCrear) {
        const btn = document.getElementById("btnGuardarVacuna");
        formCrear.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formCrearVacuna");
            btn.disabled = true;
            ajax.post("/admin/vacunas", recolectarDatos(formCrear))
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/vacunas";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(error.response.errors, "formCrearVacuna");
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }

    const formEditar = document.getElementById("formEditarVacuna");
    if (formEditar) {
        const btn = document.getElementById("btnActualizarVacuna");
        formEditar.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formEditarVacuna");
            btn.disabled = true;
            ajax.put(
                "/admin/vacunas/" + formEditar.dataset.id,
                recolectarDatos(formEditar),
            )
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/vacunas";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(error.response.errors, "formEditarVacuna");
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }
})();