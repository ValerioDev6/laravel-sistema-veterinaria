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
        const name = form.elements["name"];
        if (name) datos.append("name", name.value.trim());
        const category = form.elements["category"];
        if (category) datos.append("category", category.value.trim());
        const base_price = form.elements["base_price"];
        if (base_price) datos.append("base_price", base_price.value.trim());
        const duration_minutes = form.elements["duration_minutes"];
        if (duration_minutes) datos.append("duration_minutes", duration_minutes.value.trim());
        const description = form.elements["description"];
        if (description) datos.append("description", description.value.trim());
        return datos;
    }

    const tableEl = document.getElementById("table-services");
    if (tableEl) {
        let dataTable = null;

        const categoryLabels = {
            consulta: "Consulta",
            vacunacion: "Vacunación",
            cirugia: "Cirugía",
            estetica: "Estética",
            otro: "Otro",
        };

        function cargarDatos() {
            const url = "/admin/services";

            if (!dataTable) {
                dataTable = $("#table-services").DataTable({
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
                                    name: s.name,
                                    category: categoryLabels[s.category] || s.category,
                                    base_price: "S/ " + Number(s.base_price).toFixed(2),
                                    duration_minutes: s.duration_minutes,
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
                        { data: "name" },
                        { data: "category" },
                        { data: "base_price" },
                        { data: "duration_minutes" },
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
                '<button type="button" class="btn btn-soft-danger btn-sm btn-eliminar-service" data-id="' +
                s.id +
                '" title="Eliminar"><i class="ri-delete-bin-line"></i></button>'
            );
        }

        function bindHandlers() {
            document.querySelectorAll(".btn-eliminar-service").forEach((btn) => {
                btn.addEventListener("click", function () {
                    eliminar(parseInt(this.dataset.id, 10));
                });
            });
        }

        function eliminar(id) {
            Swal.fire({
                icon: "warning",
                title: "¿Eliminar servicio?",
                text: "Esta acción no se puede deshacer.",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
                confirmButtonColor: "#d33",
            }).then((result) => {
                if (!result.isConfirmed) return;

                ajax.delete("/admin/services/" + id)
                    .then((res) => {
                        Swal.fire("Eliminado", res.message, "success");
                        cargarDatos();
                    })
                    .catch(() => {});
            });
        }

        cargarDatos();
    }

    const formCrear = document.getElementById("formCrearService");
    if (formCrear) {
        const btn = document.getElementById("btnGuardarService");
        formCrear.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formCrearService");
            btn.disabled = true;
            const datos = recolectarDatos(formCrear);
            ajax.post("/admin/services", datos)
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/services";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(
                            error.response.errors,
                            "formCrearService",
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }

    const formEditar = document.getElementById("formEditarService");
    if (formEditar) {
        const btn = document.getElementById("btnActualizarService");
        formEditar.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formEditarService");
            btn.disabled = true;
            const datos = recolectarDatos(formEditar);
            ajax.put(
                "/admin/services/" + formEditar.dataset.id,
                datos,
            )
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/services";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(
                            error.response.errors,
                            "formEditarService",
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }
})();