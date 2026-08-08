(function () {
    "use strict";

    window.pintarErroresValidacion = function (errors, formId) {
        const form = document.getElementById(formId);
        if (!form) return;

        limpiarErroresValidacion(formId);

        Object.entries(errors || {}).forEach(([field, mensajes]) => {
            const input = form.querySelector(`[name="${field}"]`);
            if (!input) return;

            input.classList.add("is-invalid");
            const feedback = input
                .closest(".col-md-6")
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

    function recolectarOwnerDatos(form) {
        const datos = new FormData();
        [
            "first_name",
            "last_name",
            "type_documento",
            "n_documento",
            "email",
            "phone",
            "address",
            "city",
        ].forEach((campo) => {
            const el = form.elements[campo];
            if (el) datos.append(campo, el.value.trim());
        });
        return datos;
    }

    function abrirTab(tabHref) {
        const tabLink = document.querySelector(
            `a[data-bs-toggle="tab"][href="${tabHref}"]`,
        );
        if (tabLink && window.bootstrap) {
            bootstrap.Tab.getOrCreateInstance(tabLink).show();
        }
    }

    function nuevoPropietario() {
        const form = document.getElementById("formOwner");
        if (!form) return;

        limpiarErroresValidacion("formOwner");
        form.reset();
        delete form.dataset.id;
        document.getElementById("tituloFormPropietario").textContent =
            "Nuevo Propietario";
        abrirTab("#tabFormPropietario");
    }

    function editarPropietario(o) {
        const form = document.getElementById("formOwner");
        if (!form) return;

        limpiarErroresValidacion("formOwner");
        form.dataset.id = o.id;
        form.elements["first_name"].value = o.first_name || "";
        form.elements["last_name"].value = o.last_name || "";
        form.elements["type_documento"].value = o.type_documento || "";
        form.elements["n_documento"].value = o.n_documento || "";
        form.elements["email"].value = o.email || "";
        form.elements["phone"].value = o.phone || "";
        form.elements["address"].value = o.address || "";
        form.elements["city"].value = o.city || "";
        document.getElementById("tituloFormPropietario").textContent =
            "Editar Propietario — " + (o.full_name || "Propietario");
        abrirTab("#tabFormPropietario");
    }

    document.getElementById("btnNuevoPropietario")?.addEventListener(
        "click",
        nuevoPropietario,
    );
    document.getElementById("btnRegresarListado")?.addEventListener(
        "click",
        () => abrirTab("#tabBuscarPropietario"),
    );
    document.getElementById("btnRegresarFicha")?.addEventListener(
        "click",
        () => abrirTab("#tabBuscarPropietario"),
    );

    function verFichaPropietario(id) {
        const contenedor = document.getElementById("contenidoFichaPropietario");
        const titulo = document.getElementById("tituloFichaPropietario");
        if (!contenedor || !titulo) return;

        contenedor.innerHTML =
            '<div class="text-center text-muted py-4">' +
            '<div class="spinner-border text-primary" role="status"></div>' +
            '<p class="mb-0 mt-2">Cargando ficha…</p></div>';
        abrirTab("#tabFichaPropietario");

        ajax.get("/admin/owners/" + id)
            .then((res) => {
                const o = res.data || res;
                titulo.textContent =
                    "Ficha del Propietario — " + (o.full_name || "Propietario");
                contenedor.innerHTML = renderFicha(o);
            })
            .catch(() => {
                contenedor.innerHTML =
                    '<div class="text-center text-muted py-4">' +
                    '<i class="ri-error-warning-line fs-1"></i>' +
                    '<p class="mb-0 mt-2">No se pudo cargar la ficha.</p></div>';
            });
    }

    function renderFicha(o) {
        const filas = [
            ["Nombres", o.first_name],
            ["Apellidos", o.last_name],
            ["Documento", o.type_documento ? o.type_documento + " — " + o.n_documento : "—"],
            ["Teléfono", o.phone],
            ["Email", o.email || "—"],
            ["Ciudad", o.city || "—"],
            ["Dirección", o.address || "—"],
        ];
        const dl = filas
            .map(
                ([label, valor]) =>
                    '<dt class="col-sm-4 text-muted">' +
                    label +
                    "</dt><dd class='col-sm-8'>" +
                    (valor ?? "—") +
                    "</dd>",
            )
            .join("");

        const mascotas = o.pacientes || [];
        let filasMascotas;
        if (!mascotas.length) {
            filasMascotas =
                '<div class="text-center text-muted py-4">' +
                '<i class="ri-paw-line fs-1"></i>' +
                '<p class="mb-0 mt-2">Este propietario aún no tiene mascotas registradas.</p></div>';
        } else {
            filasMascotas =
                '<div class="table-responsive"><table class="table table-borderless dt-responsive nowrap w-100 mb-0">' +
                '<thead><tr style="border-bottom: 2px solid #212529;">' +
                "<th>Nombre</th><th>Especie</th><th>Raza</th><th>Nacimiento</th><th>Acciones</th>" +
                "</tr></thead><tbody>" +
                mascotas
                    .map(
                        (p) =>
                            "<tr><td>" +
                            (p.name || "—") +
                            "</td><td>" +
                            (p.species || "—") +
                            "</td><td>" +
                            (p.breed || "—") +
                            "</td><td>" +
                            (p.birth_date
                                ? new Date(p.birth_date).toLocaleDateString("es-PE")
                                : "—") +
                            '</td><td><a href="' +
                            p.show_url +
                            '" class="btn btn-soft-primary btn-sm" title="Ficha">' +
                            '<i class="ri-file-user-line"></i></a></td></tr>',
                    )
                    .join("") +
                "</tbody></table></div>";
        }

        return (
            '<div class="row g-4">' +
            '<div class="col-lg-5">' +
            '<div class="card mb-0">' +
            '<div class="card-header"><h4 class="card-title mb-0">Datos del Propietario</h4></div>' +
            '<div class="card-body"><dl class="row mb-0">' +
            dl +
            '</dl>' +
            '<div class="d-flex gap-2 mt-3">' +
            '<button type="button" class="btn btn-soft-primary btn-sm btn-ficha-editar" data-id="' +
            o.id +
            '"><i class="ri-pencil-line me-1"></i>Editar</button>' +
            "</div></div></div></div>" +
            '<div class="col-lg-7">' +
            '<div class="card mb-0">' +
            '<div class="card-header"><h4 class="card-title mb-0">Mascotas (' +
            (o.pacientes_count || mascotas.length) +
            ")</h4></div>" +
            '<div class="card-body">' +
            filasMascotas +
            "</div></div></div></div>"
        );
    }

    function aplicarEdicionPorQuery() {
        const params = new URLSearchParams(window.location.search);
        const id = params.get("edit");
        if (!id) return;
        ajax.get("/admin/owners/" + id).then((res) => {
            editarPropietario(res.data || res);
        });
    }

    let dataTable = null;

    const tableEl = document.getElementById("table-owners");
    if (tableEl) {
        let terminoBusqueda = "";
        let terminoBusquedaTimer = null;

        function cargarDatos() {
            const url = "/admin/owners";

            if (!dataTable) {
                dataTable = $("#table-owners").DataTable({
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
                '<button type="button" class="btn btn-soft-info btn-sm me-1 btn-ver-owner" data-id="' +
                o.id +
                '" title="Ver ficha"><i class="ri-eye-line"></i></button>' +
                '<button type="button" class="btn btn-soft-primary btn-sm me-1 btn-editar-owner" data-id="' +
                o.id +
                '" title="Editar"><i class="ri-pencil-line"></i></button>' +
                '<button type="button" class="btn btn-soft-danger btn-sm btn-eliminar-owner" data-id="' +
                o.id +
                '" title="Eliminar"><i class="ri-delete-bin-line"></i></button>'
            );
        }

        function bindHandlers() {
            document.querySelectorAll(".btn-ver-owner").forEach((btn) => {
                if (btn.dataset.bound) return;
                btn.dataset.bound = "1";
                btn.addEventListener("click", function () {
                    verFichaPropietario(parseInt(this.dataset.id, 10));
                });
            });

            document.querySelectorAll(".btn-editar-owner").forEach((btn) => {
                if (btn.dataset.bound) return;
                btn.dataset.bound = "1";
                btn.addEventListener("click", function () {
                    const id = parseInt(this.dataset.id, 10);
                    ajax.get("/admin/owners/" + id).then((res) => {
                        editarPropietario(res.data);
                    });
                });
            });

            document.querySelectorAll(".btn-eliminar-owner").forEach((btn) => {
                if (btn.dataset.bound) return;
                btn.dataset.bound = "1";
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

        $("#busquedaPropietario").on("input", function () {
            const termino = this.value;
            clearTimeout(terminoBusquedaTimer);
            terminoBusquedaTimer = setTimeout(function () {
                terminoBusqueda = termino.trim();
                dataTable.ajax.reload();
            }, 350);
        });

        $("#busquedaPropietario").on("keydown", function (e) {
            if (e.key === "Enter") e.preventDefault();
        });

        cargarDatos();
    }

    aplicarEdicionPorQuery();

    const formOwner = document.getElementById("formOwner");
    if (formOwner) {
        const btn = document.getElementById("btnGuardarOwner");
        formOwner.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formOwner");
            btn.disabled = true;
            const id = formOwner.dataset.id;
            const ruta = id ? "/admin/owners/" + id : "/admin/owners";
            const datos = recolectarOwnerDatos(formOwner);
            const peticion = id
                ? ajax.put(ruta, datos)
                : ajax.post(ruta, datos);

            peticion
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        nuevoPropietario();
                        abrirTab("#tabBuscarPropietario");
                        if (dataTable) dataTable.ajax.reload();
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(
                            error.response.errors,
                            "formOwner",
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }

    document.addEventListener("click", function (e) {
        const btnEditar = e.target.closest(".btn-ficha-editar");
        if (btnEditar) {
            const id = parseInt(btnEditar.dataset.id, 10);
            ajax.get("/admin/owners/" + id).then((res) => {
                editarPropietario(res.data || res);
            });
        }
    });
})();