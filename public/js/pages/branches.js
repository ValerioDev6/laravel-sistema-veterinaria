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
        const address = form.elements["address"];
        if (address) datos.append("address", address.value.trim());
        const city = form.elements["city"];
        if (city) datos.append("city", city.value.trim());
        const phone = form.elements["phone"];
        if (phone) datos.append("phone", phone.value.trim());
        return datos;
    }

    // ------------------------------------------------------------------
    // Página: index
    // ------------------------------------------------------------------
    const tableEl = document.getElementById("table-branches");
    if (tableEl) {
        let dataTable = null;

        function cargarDatos() {
            const url = "/admin/branches";

            if (!dataTable) {
                dataTable = $("#table-branches").DataTable({
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
                                data: res.data,
                            });
                        });
                    },
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json",
                    },
                    columns: [
                        { data: "id" },
                        { data: "name" },
                        { data: "address" },
                        { data: "city" },
                        { data: "phone" },
                        {
                            data: null,
                            orderable: false,
                            render: function (data) {
                                return renderAcciones(data);
                            },
                        },
                    ],
                    order: [[0, "asc"]],
                    drawCallback: function () {
                        renderDeleteHandlers();
                    },
                });
            } else {
                dataTable.ajax.reload();
            }
            return dataTable;
        }

        function renderAcciones(data) {
            return (
                '<a href="' +
                data.edit_url +
                '" class="btn btn-soft-primary btn-sm me-1" title="Editar">' +
                '<i class="ri-pencil-line"></i></a>' +
                '<button type="button" class="btn btn-soft-danger btn-sm btn-eliminar-branch" data-id="' +
                data.id +
                '" title="Eliminar"><i class="ri-delete-bin-line"></i></button>'
            );
        }

        function renderDeleteHandlers() {
            document.querySelectorAll(".btn-eliminar-branch").forEach((btn) => {
                btn.addEventListener("click", function () {
                    eliminarBranch(parseInt(this.dataset.id, 10));
                });
            });
        }

        function eliminarBranch(id) {
            Swal.fire({
                icon: "warning",
                title: "¿Eliminar sucursal?",
                text: "Esta acción no se puede deshacer.",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
                confirmButtonColor: "#d33",
            }).then((result) => {
                if (!result.isConfirmed) return;

                ajax.delete("/admin/branches/" + id)
                    .then((res) => {
                        Swal.fire("Eliminado", res.message, "success");
                        cargarDatos();
                    })
                    .catch(() => {});
            });
        }

        cargarDatos();
    }

    // ------------------------------------------------------------------
    // Página: crear
    // ------------------------------------------------------------------
    const formCrear = document.getElementById("formCrearBranch");
    if (formCrear) {
        const btn = document.getElementById("btnGuardarBranch");

        formCrear.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formCrearBranch");
            btn.disabled = true;

            const datos = recolectarDatos(formCrear);
            ajax.post("/admin/branches", datos)
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href =
                            formCrear.dataset.redirect || "/admin/branches";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(
                            error.response.errors,
                            "formCrearBranch",
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }

    // ------------------------------------------------------------------
    // Página: editar
    // ------------------------------------------------------------------
    const formEditar = document.getElementById("formEditarBranch");
    if (formEditar) {
        const btn = document.getElementById("btnActualizarBranch");

        formEditar.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formEditarBranch");
            btn.disabled = true;

            const datos = recolectarDatos(formEditar);
            ajax.put(
                "/admin/branches/" + formEditar.dataset.id,
                datos,
            )
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/branches";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(
                            error.response.errors,
                            "formEditarBranch",
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }
})();
