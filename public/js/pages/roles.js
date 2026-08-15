(function () {
    "use strict";

    // ------------------------------------------------------------------
    // Helpers de validación de formulario
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

    function recolectarRol(form) {
        const datos = new FormData();
        const name = form.elements["name"];
        if (name) datos.append("name", name.value.trim());
        form.querySelectorAll(".permiso-check:checked").forEach((cb) => {
            datos.append("permissions[]", cb.value);
        });
        return datos;
    }

    // ------------------------------------------------------------------
    // Listado de roles (DataTable server-side + search manual)
    // ------------------------------------------------------------------
    const tableEl = document.getElementById("table-roles");
    if (tableEl) {
        let dataTable = null;
        let terminoBusqueda = "";
        let terminoBusquedaTimer = null;

        function renderRol(r) {
            return r.is_super_admin
                ? '<strong>' + r.name + '</strong> <span class="badge bg-primary-subtle text-primary">Super Admin</span>'
                : "<strong>" + r.name + "</strong>";
        }

        function renderAcciones(r) {
            let acciones =
                '<a href="' +
                r.edit_url +
                '" class="btn btn-soft-primary btn-sm me-1" title="Editar">' +
                '<i class="ri-pencil-line"></i></a>';

            if (!r.is_super_admin) {
                acciones +=
                    '<button type="button" class="btn btn-soft-danger btn-sm btn-eliminar-rol" data-id="' +
                    r.id +
                    '" data-name="' +
                    r.name +
                    '" title="Eliminar">' +
                    '<i class="ri-delete-bin-line"></i></button>';
            }

            return acciones;
        }

        function cargarDatos() {
            const url = "/admin/roles";

            if (!dataTable) {
                dataTable = $("#table-roles").DataTable({
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
                        if (terminoBusqueda) {
                            params.search = terminoBusqueda;
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
                                data: res.data.map((r) => ({
                                    rol: renderRol(r),
                                    guard: r.guard_name,
                                    usuarios: r.users_count,
                                    permisos: r.permissions_count,
                                    acciones: renderAcciones(r),
                                })),
                            });
                        });
                    },
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json",
                    },
                    columns: [
                        { data: "rol" },
                        { data: "guard" },
                        { data: "usuarios", orderable: false },
                        { data: "permisos", orderable: false },
                        { data: "acciones", orderable: false },
                    ],
                    order: [[0, "asc"]],
                    drawCallback: function () {
                        bindHandlers();
                    },
                });
            } else {
                dataTable.ajax.reload();
            }
            return dataTable;
        }

        function bindHandlers() {
            document
                .querySelectorAll(".btn-eliminar-rol")
                .forEach((btn) => {
                    btn.addEventListener("click", function () {
                        eliminarRol(
                            parseInt(this.dataset.id, 10),
                            this.dataset.name,
                        );
                    });
                });
        }

        function eliminarRol(id, name) {
            Swal.fire({
                icon: "warning",
                title: "¿Eliminar rol?",
                text: 'El rol "' + name + '" se eliminará. Esta acción no se puede deshacer.',
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
                confirmButtonColor: "#d33",
            }).then((result) => {
                if (!result.isConfirmed) return;

                ajax.delete("/admin/roles/" + id)
                    .then((res) => {
                        Swal.fire("Eliminado", res.message, "success");
                        cargarDatos();
                    })
                    .catch(() => {});
            });
        }

        $("#busquedaRoles").on("input", function () {
            const termino = this.value;
            clearTimeout(terminoBusquedaTimer);
            terminoBusquedaTimer = setTimeout(function () {
                terminoBusqueda = termino.trim();
                dataTable.ajax.reload();
            }, 350);
        });

        $("#busquedaRoles").on("keydown", function (e) {
            if (e.key === "Enter") e.preventDefault();
        });

        const formFiltros = document.getElementById("formFiltrosRoles");
        if (formFiltros) {
            formFiltros.addEventListener("submit", (e) => {
                e.preventDefault();
                dataTable.ajax.reload();
            });
            document
                .getElementById("btnLimpiarFiltrosRoles")
                ?.addEventListener("click", () => {
                    formFiltros.reset();
                    const input = document.getElementById("busquedaRoles");
                    if (input) input.value = "";
                    terminoBusqueda = "";
                    dataTable.ajax.reload();
                });
        }

        cargarDatos();
    }

    // ------------------------------------------------------------------
    // Formularios crear/editar (checkboxes de permisos agrupados)
    // ------------------------------------------------------------------
    const botonSeleccionarTodos = document.getElementById("btnSeleccionarTodos");
    if (botonSeleccionarTodos) {
        botonSeleccionarTodos.addEventListener("click", () => {
            document
                .querySelectorAll(".permiso-check")
                .forEach((cb) => (cb.checked = true));
        });
    }

    const botonLimpiarPermisos = document.getElementById("btnLimpiarPermisos");
    if (botonLimpiarPermisos) {
        botonLimpiarPermisos.addEventListener("click", () => {
            document
                .querySelectorAll(".permiso-check")
                .forEach((cb) => (cb.checked = false));
        });
    }

    const formCrear = document.getElementById("formCrearRol");
    if (formCrear) {
        const btn = document.getElementById("btnGuardarRol");

        formCrear.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formCrearRol");
            const datos = recolectarRol(formCrear);
            btn.disabled = true;

            ajax.post("/admin/roles", datos)
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/roles";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(
                            error.response.errors,
                            "formCrearRol",
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }

    const formEditar = document.getElementById("formEditarRol");
    if (formEditar) {
        const btn = document.getElementById("btnActualizarRol");

        formEditar.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formEditarRol");
            const datos = recolectarRol(formEditar);
            btn.disabled = true;

            ajax.put("/admin/roles/" + formEditar.dataset.id, datos)
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/roles";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(
                            error.response.errors,
                            "formEditarRol",
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }
})();
