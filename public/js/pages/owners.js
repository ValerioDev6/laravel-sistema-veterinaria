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
        const first_name = form.elements["first_name"];
        if (first_name) datos.append("first_name", first_name.value.trim());
        const last_name = form.elements["last_name"];
        if (last_name) datos.append("last_name", last_name.value.trim());
        const type_documento = form.elements["type_documento"];
        if (type_documento) datos.append("type_documento", type_documento.value.trim());
        const n_documento = form.elements["n_documento"];
        if (n_documento) datos.append("n_documento", n_documento.value.trim());
        const email = form.elements["email"];
        if (email) datos.append("email", email.value.trim());
        const phone = form.elements["phone"];
        if (phone) datos.append("phone", phone.value.trim());
        const address = form.elements["address"];
        if (address) datos.append("address", address.value.trim());
        const city = form.elements["city"];
        if (city) datos.append("city", city.value.trim());
        return datos;
    }

    const tableEl = document.getElementById("table-owners");
    if (tableEl) {
        let dataTable = null;

        function cargarDatos() {
            const url = "/admin/owners";
            let opt = {};

            if (!dataTable) {
                dataTable = $("#table-owners").DataTable({
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
                                data: res.data.map((o) => ({
                                    id: o.id,
                                    nombre: o.full_name,
                                    documento: o.n_documento
                                        ? o.type_documento + " — " + o.n_documento
                                        : "—",
                                    phone: o.phone,
                                    email: o.email || "—",
                                    mascotas: o.pacientes_count,
                                    acciones: renderAcciones(o),
                                })),
                            });
                        });
                    },
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json",
                    },
                    columns: [
                        { data: "id" },
                        { data: "nombre" },
                        { data: "documento" },
                        { data: "phone" },
                        { data: "email" },
                        { data: "mascotas" },
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

        function renderAcciones(o) {
            return (
                '<a href="' +
                o.show_url +
                '" class="btn btn-soft-info btn-sm me-1" title="Ver ficha">' +
                '<i class="ri-eye-line"></i></a>' +
                '<a href="' +
                o.edit_url +
                '" class="btn btn-soft-primary btn-sm me-1" title="Editar">' +
                '<i class="ri-pencil-line"></i></a>' +
                '<button type="button" class="btn btn-soft-danger btn-sm btn-eliminar-owner" data-id="' +
                o.id +
                '" title="Eliminar"><i class="ri-delete-bin-line"></i></button>'
            );
        }

        function bindHandlers() {
            document.querySelectorAll(".btn-eliminar-owner").forEach((btn) => {
                btn.addEventListener("click", function () {
                    eliminar(parseInt(this.dataset.id, 10));
                });
            });
        }

        function eliminar(id) {
            Swal.fire({
                icon: "warning",
                title: "¿Eliminar propietario?",
                text: "Esta acción no se puede deshacer.",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
                confirmButtonColor: "#d33",
            }).then((result) => {
                if (!result.isConfirmed) return;

                ajax.delete("/admin/owners/" + id)
                    .then((res) => {
                        Swal.fire("Eliminado", res.message, "success");
                        cargarDatos();
                    })
                    .catch((error) => {
                        if (error.status === 422) {
                            Swal.fire(
                                "No se pudo eliminar",
                                error.response?.errors?.owner?.[0],
                                "warning",
                            );
                        }
                    });
            });
        }

        cargarDatos();
    }

    const formCrear = document.getElementById("formCrearOwner");
    if (formCrear) {
        const btn = document.getElementById("btnGuardarOwner");
        formCrear.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formCrearOwner");
            btn.disabled = true;
            const datos = recolectarDatos(formCrear);
            ajax.post("/admin/owners", datos)
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/owners";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(
                            error.response.errors,
                            "formCrearOwner",
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }

    const formEditar = document.getElementById("formEditarOwner");
    if (formEditar) {
        const btn = document.getElementById("btnActualizarOwner");
        formEditar.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formEditarOwner");
            btn.disabled = true;
            const datos = recolectarDatos(formEditar);
            ajax.put(
                "/admin/owners/" + formEditar.dataset.id,
                datos,
            )
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/owners";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(
                            error.response.errors,
                            "formEditarOwner",
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }
})();