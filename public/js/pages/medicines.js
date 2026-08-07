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
        const quantity = form.elements["quantity"];
        if (quantity) datos.append("quantity", quantity.value.trim());
        const unit_cost = form.elements["unit_cost"];
        if (unit_cost) datos.append("unit_cost", unit_cost.value.trim());
        return datos;
    }

    const tableEl = document.getElementById("table-medicines");
    if (tableEl) {
        let dataTable = null;

        function cargarDatos() {
            const url = "/admin/medicines";

            if (!dataTable) {
                dataTable = $("#table-medicines").DataTable({
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
                                data: res.data.map((m) => ({
                                    id: m.id,
                                    name: m.name,
                                    quantity: m.quantity,
                                    unit_cost:
                                        "S/ " + parseFloat(m.unit_cost).toFixed(2),
                                    acciones: renderAcciones(m),
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
                        { data: "quantity" },
                        { data: "unit_cost" },
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

        function renderAcciones(m) {
            return (
                '<a href="' +
                m.edit_url +
                '" class="btn btn-soft-primary btn-sm me-1" title="Editar">' +
                '<i class="ri-pencil-line"></i></a>' +
                '<button type="button" class="btn btn-soft-danger btn-sm btn-eliminar-medicine" data-id="' +
                m.id +
                '" title="Eliminar"><i class="ri-delete-bin-line"></i></button>'
            );
        }

        function bindHandlers() {
            document
                .querySelectorAll(".btn-eliminar-medicine")
                .forEach((btn) => {
                    btn.addEventListener("click", function () {
                        eliminar(parseInt(this.dataset.id, 10));
                    });
                });
        }

        function eliminar(id) {
            Swal.fire({
                icon: "warning",
                title: "¿Eliminar medicamento?",
                text: "Esta acción no se puede deshacer.",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
                confirmButtonColor: "#d33",
            }).then((result) => {
                if (!result.isConfirmed) return;

                ajax.delete("/admin/medicines/" + id)
                    .then((res) => {
                        Swal.fire("Eliminado", res.message, "success");
                        cargarDatos();
                    })
                    .catch((error) => {
                        if (error.status === 422) {
                            Swal.fire(
                                "No se pudo eliminar",
                                error.response?.errors?.medicine?.[0],
                                "warning",
                            );
                        }
                    });
            });
        }

        cargarDatos();
    }

    const formCrear = document.getElementById("formCrearMedicine");
    if (formCrear) {
        const btn = document.getElementById("btnGuardarMedicine");
        formCrear.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formCrearMedicine");
            const datos = recolectarDatos(formCrear);
            btn.disabled = true;
            ajax.post("/admin/medicines", datos)
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/medicines";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(
                            error.response.errors,
                            "formCrearMedicine",
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }

    const formEditar = document.getElementById("formEditarMedicine");
    if (formEditar) {
        const btn = document.getElementById("btnActualizarMedicine");
        formEditar.addEventListener("submit", (e) => {
e.preventDefault();
        limpiarErroresValidacion("formEditarMedicine");
        const datos = recolectarDatos(formEditar);
        btn.disabled = true;
        ajax.put(
            "/admin/medicines/" + formEditar.dataset.id,
            datos,
        )
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/medicines";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(
                            error.response.errors,
                            "formEditarMedicine",
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }
})();