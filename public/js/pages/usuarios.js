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
        const username = form.elements["username"];
        if (username) datos.append("username", username.value.trim());
        const email = form.elements["email"];
        if (email) datos.append("email", email.value.trim());
        const password = form.elements["password"];
        if (password) datos.append("password", password.value);
        const password_confirmation = form.elements["password_confirmation"];
        if (password_confirmation) datos.append("password_confirmation", password_confirmation.value);
        const role = form.elements["role"];
        if (role) datos.append("role", role.value.trim());
        const branch_id = form.elements["branch_id"];
        if (branch_id) datos.append("branch_id", branch_id.value.trim());
        const phone = form.elements["phone"];
        if (phone) datos.append("phone", phone.value.trim());
        const type_documento = form.elements["type_documento"];
        if (type_documento) datos.append("type_documento", type_documento.value.trim());
        const n_documento = form.elements["n_documento"];
        if (n_documento) datos.append("n_documento", n_documento.value.trim());
        const birthday = form.elements["birthday"];
        if (birthday) datos.append("birthday", birthday.value.trim());
        const avatar = form.elements["avatar"];
        if (avatar && avatar.files && avatar.files.length) datos.append("avatar", avatar.files[0]);
        return datos;
    }

    const tableEl = document.getElementById("table-usuarios");
    if (tableEl) {
        let dataTable = null;

        function renderUsuario(u) {
            const src = u.avatar
                ? u.avatar
                : "/assets/images/users/avatar-1.jpg";
            return (
                '<div class="d-flex align-items-center">' +
                '<img src="' +
                src +
                '" class="rounded-circle me-2" width="34" height="34" alt="avatar" style="object-fit:cover">' +
                "<strong>" +
                u.username +
                "</strong></div>"
            );
        }

        function renderEstado(u) {
            const badge = u.is_active
                ? '<span class="badge bg-secondary-subtle text-success">Activo</span>'
                : '<span class="badge bg-secondary-subtle text-danger">Inactivo</span>';
            return badge;
        }

        function renderAcciones(u) {
            return (
                '<a href="' +
                u.edit_url +
                '" class="btn btn-soft-primary btn-sm me-1" title="Editar">' +
                '<i class="ri-pencil-line"></i></a>' +
                '<button type="button" class="btn btn-soft-danger btn-sm btn-eliminar-user" data-id="' +
                u.id +
                '" title="Eliminar"><i class="ri-delete-bin-line"></i></button>' +
                '<button type="button" class="btn btn-soft-secondary btn-sm btn-toggle-user" data-id="' +
                u.id +
                '" title="Activar/Desactivar"><i class="ri-arrow-left-right-line"></i></button>'
            );
        }

        function cargarDatos() {
            const url = "/admin/users";

            if (!dataTable) {
                dataTable = $("#table-usuarios").DataTable({
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
                                data: res.data.map((u) => ({
                                    usuario: renderUsuario(u),
                                    email: u.email,
                                    branch: u.branch || "—",
                                    roles: (u.roles || []).join(", "),
                                    estado: renderEstado(u),
                                    acciones: renderAcciones(u),
                                })),
                            });
                        });
                    },
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json",
                    },
                    columns: [
                        { data: "usuario", orderable: false },
                        { data: "email" },
                        { data: "branch" },
                        { data: "roles" },
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

        function bindHandlers() {
            document
                .querySelectorAll(".btn-eliminar-user")
                .forEach((btn) => {
                    btn.addEventListener("click", function () {
                        eliminarUsuario(parseInt(this.dataset.id, 10));
                    });
                });

            document
                .querySelectorAll(".btn-toggle-user")
                .forEach((btn) => {
                    btn.addEventListener("click", function () {
                        toggleStatus(parseInt(this.dataset.id, 10));
                    });
                });
        }

        function eliminarUsuario(id) {
            Swal.fire({
                icon: "warning",
                title: "¿Eliminar usuario?",
                text: "Esta acción no se puede deshacer.",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
                confirmButtonColor: "#d33",
            }).then((result) => {
                if (!result.isConfirmed) return;

                ajax.delete("/admin/users/" + id)
                    .then((res) => {
                        Swal.fire("Eliminado", res.message, "success");
                        cargarDatos();
                    })
                    .catch(() => {});
            });
        }

        function toggleStatus(id) {
            ajax.patch("/admin/users/" + id + "/toggle-status")
                .then((res) => {
                    Swal.fire("Listo", res.message, "success");
                    cargarDatos();
                })
                .catch(() => {});
        }

        cargarDatos();
    }

    const formCrear = document.getElementById("formCrearUsuario");
    if (formCrear) {
        const btn = document.getElementById("btnGuardarUsuario");

        formCrear.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formCrearUsuario");
            const datos = recolectarDatos(formCrear);
            btn.disabled = true;

            ajax.post(
                "/admin/users",
                datos,
            )
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/usuarios";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(
                            error.response.errors,
                            "formCrearUsuario",
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }

    const formEditar = document.getElementById("formEditarUsuario");
    if (formEditar) {
        const btn = document.getElementById("btnActualizarUsuario");

        formEditar.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formEditarUsuario");
            const datos = recolectarDatos(formEditar);
            btn.disabled = true;

            ajax.put(
                "/admin/users/" + formEditar.dataset.id,
                datos,
            )
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/usuarios";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(
                            error.response.errors,
                            "formEditarUsuario",
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }
})();