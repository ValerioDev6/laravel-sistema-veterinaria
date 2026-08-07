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
        const nombre = form.elements["name"];
        if (nombre) datos.append("name", nombre.value.trim());
        return datos;
    }

    // ------------------------------------------------------------------
    // Página: index (tabla + modal)
    // ------------------------------------------------------------------
    const tableEl = document.getElementById("table-species");
    const modalEl = document.getElementById("modalSpecies");
    const formModal = document.getElementById("formModalSpecies");
    const modalInstance = modalEl ? new bootstrap.Modal(modalEl) : null;
    let dataTable = null;

    function abrirModalEdicion(id, name) {
        const title = document.getElementById("modalSpeciesTitle");
        const campoId = document.getElementById("modal-species-id");
        const campoNombre = document.getElementById("modal-species-nombre");
        if (title) title.textContent = "Editar Especie";
        if (campoId) campoId.value = id;
        if (campoNombre) campoNombre.value = name;
        limpiarErroresValidacion("formModalSpecies");
        if (modalInstance) modalInstance.show();
    }

    if (tableEl) {
        let terminoBusqueda = "";

        function cargarDatos() {
            const url = "/admin/species";

            if (!dataTable) {
                dataTable = $("#table-species").DataTable({
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
                                data: res.data.map((specie) => ({
                                    id: specie.id,
                                    name: specie.name,
                                    acciones: renderAcciones(specie),
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
                        { data: "acciones", orderable: false },
                    ],
                    order: [[0, "asc"]],
                    drawCallback: function () {
                        renderHandlers();
                    },
                });
            } else {
                dataTable.ajax.reload();
            }
            return dataTable;
        }

        function renderAcciones(specie) {
            const editar = modalInstance
                ? '<button type="button" class="btn btn-soft-primary btn-sm me-1 btn-editar-species" data-id="' +
                  specie.id +
                  '" data-name="' +
                  specie.name +
                  '" title="Editar"><i class="ri-pencil-line"></i></button>'
                : '<a href="' +
                  specie.edit_url +
                  '" class="btn btn-soft-primary btn-sm me-1" title="Editar">' +
                  '<i class="ri-pencil-line"></i></a>';
            return (
                editar +
                '<button type="button" class="btn btn-soft-danger btn-sm btn-eliminar-species" data-id="' +
                specie.id +
                '" title="Eliminar"><i class="ri-delete-bin-line"></i></button>'
            );
        }

        function renderHandlers() {
            document
                .querySelectorAll(".btn-editar-species")
                .forEach((btn) => {
                    btn.addEventListener("click", function () {
                        abrirModalEdicion(
                            this.dataset.id,
                            this.dataset.name,
                        );
                    });
                });
            document
                .querySelectorAll(".btn-eliminar-species")
                .forEach((btn) => {
                    btn.addEventListener("click", function () {
                        eliminarEspecie(parseInt(this.dataset.id, 10));
                    });
                });
        }

        function eliminarEspecie(id) {
            Swal.fire({
                icon: "warning",
                title: "¿Eliminar especie?",
                text: "Esta acción no se puede deshacer.",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
                confirmButtonColor: "#d33",
            }).then((result) => {
                if (!result.isConfirmed) return;

                ajax.delete("/admin/species/" + id)
                    .then((res) => {
                        Swal.fire("Eliminado", res.message, "success");
                        cargarDatos();
                    })
                    .catch((error) => {
                        if (error.status === 422) {
                            Swal.fire(
                                "No se pudo eliminar",
                                error.response?.errors?.species?.[0],
                                "warning",
                            );
                        }
                    });
            });
        }

        const inputBusqueda = document.getElementById("busquedaEspecie");
        if (inputBusqueda) {
            let temporizador = null;
            inputBusqueda.addEventListener("input", function () {
                const termino = inputBusqueda.value;
                clearTimeout(temporizador);
                temporizador = setTimeout(function () {
                    terminoBusqueda = termino.trim();
                    dataTable.ajax.reload();
                }, 350);
            });
        }

        // Solo inicializar la tabla cuando la pestaña esté visible
        const contenedor = tableEl.closest(".tab-pane");
        if (!contenedor || contenedor.classList.contains("active")) {
            cargarDatos();
        } else {
            const pestana = document.querySelector(
                '[data-bs-toggle="tab"][data-bs-target="#' +
                    contenedor.id +
                    '"]',
            );
            if (pestana) {
                pestana.addEventListener(
                    "shown.bs.tab",
                    function handler() {
                        cargarDatos();
                        pestana.removeEventListener("shown.bs.tab", handler);
                    },
                );
            } else {
                cargarDatos();
            }
        }
    }

    // ------------------------------------------------------------------
    // Modal de crear / editar
    // ------------------------------------------------------------------
    if (formModal && modalEl) {
        const btn = document.getElementById("btnModalSpeciesGuardar");

        formModal.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formModalSpecies");

            const id = document.getElementById("modal-species-id").value;
            btn.disabled = true;
            const datos = recolectarDatos(formModal);
            delete datos.id;
            const peticion = id
                ? ajax.put("/admin/species/" + id, datos)
                : ajax.post("/admin/species", datos);

            peticion
                .then((res) => {
                    Swal.fire("Listo", res.message, "success");
                    modalInstance.hide();
                    if (dataTable) dataTable.ajax.reload();
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(
                            error.response.errors,
                            "formModalSpecies",
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });

        modalEl.addEventListener("hidden.bs.modal", function () {
            const titulo = document.getElementById("modalSpeciesTitle");
            if (titulo) titulo.textContent = "Nueva Especie";
            formModal.reset();
        });
    }

    // ------------------------------------------------------------------
    // Páginas standalone: crear / editar
    // ------------------------------------------------------------------
    const formCrear = document.getElementById("formCrearSpecies");
    if (formCrear) {
        const btn = document.getElementById("btnGuardarSpecies");
        formCrear.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formCrearSpecies");
            btn.disabled = true;
            ajax.post(
                "/admin/species",
                recolectarDatos(formCrear),
            )
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/species";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(
                            error.response.errors,
                            "formCrearSpecies",
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }

    const formEditar = document.getElementById("formEditarSpecies");
    if (formEditar) {
        const btn = document.getElementById("btnActualizarSpecies");
        formEditar.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formEditarSpecies");
            btn.disabled = true;
            ajax.put(
                "/admin/species/" + formEditar.dataset.id,
                recolectarDatos(formEditar),
            )
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/species";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(
                            error.response.errors,
                            "formEditarSpecies",
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }
})();