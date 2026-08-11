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

    function mostrarToastError(errors) {
        if (!errors || typeof Swal === "undefined") return;
        const firstKey = Object.keys(errors)[0];
        const mensaje = errors[firstKey]?.[0] ?? "Ocurrió un error";
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "warning",
            title: mensaje,
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
        });
    }

    function recolectarDatos(form) {
        const datos = new FormData();
        const nombre = form.elements["name"];
        if (nombre) datos.append("name", nombre.value.trim());
        const specie = form.elements["species_id"];
        if (specie) datos.append("species_id", specie.value);
        return datos;
    }

    // ------------------------------------------------------------------
    // Página: index (tabla + modal)
    // ------------------------------------------------------------------
    const tableEl = document.getElementById("table-breeds");
    const modalEl = document.getElementById("modalBreed");
    const formModal = document.getElementById("formModalBreed");
    const modalInstance = modalEl ? new bootstrap.Modal(modalEl) : null;
    let dataTable = null;

    function abrirModalEdicion(id, speciesId, name) {
        const title = document.getElementById("modalBreedTitle");
        const campoId = document.getElementById("modal-breed-id");
        const campoSel = document.getElementById("modal-breed-species");
        const campoNombre = document.getElementById("modal-breed-nombre");
        if (title) title.textContent = "Editar Raza";
        if (campoId) campoId.value = id;
        if (campoSel) campoSel.value = speciesId;
        if (campoNombre) campoNombre.value = name;
        limpiarErroresValidacion("formModalBreed");
        if (modalInstance) modalInstance.show();
    }

    if (tableEl) {
        let terminoBusqueda = "";

        function cargarDatos() {
            const url = "/admin/breeds";

            if (!dataTable) {
                dataTable = $("#table-breeds").DataTable({
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
                                data: res.data.map((breed) => ({
                                    id: breed.id,
                                    especie: breed.species || "—",
                                    name: breed.name,
                                    acciones: renderAcciones(breed),
                                })),
                            });
                        });
                    },
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json",
                    },
                    columns: [
                        { data: "id" },
                        { data: "especie" },
                        { data: "name" },
                        { data: "acciones", orderable: false },
                    ],
                    order: [],
                    drawCallback: function () {
                        renderHandlers();
                    },
                });
            } else {
                dataTable.ajax.reload();
            }
            return dataTable;
        }

        function renderAcciones(breed) {
            const editar = modalInstance
                ? '<button type="button" class="btn btn-soft-primary btn-sm me-1 btn-editar-breed" data-id="' +
                  breed.id +
                  '" data-species="' +
                  breed.species_id +
                  '" data-name="' +
                  breed.name +
                  '" title="Editar"><i class="ri-pencil-line"></i></button>'
                : '<a href="' +
                  breed.edit_url +
                  '" class="btn btn-soft-primary btn-sm me-1" title="Editar">' +
                  '<i class="ri-pencil-line"></i></a>';
            return (
                editar +
                '<button type="button" class="btn btn-soft-danger btn-sm btn-eliminar-breed" data-id="' +
                breed.id +
                '" title="Eliminar"><i class="ri-delete-bin-line"></i></button>'
            );
        }

        function renderHandlers() {
            document.querySelectorAll(".btn-editar-breed").forEach((btn) => {
                btn.addEventListener("click", function () {
                    abrirModalEdicion(
                        this.dataset.id,
                        this.dataset.species,
                        this.dataset.name,
                    );
                });
            });
            document.querySelectorAll(".btn-eliminar-breed").forEach((btn) => {
                btn.addEventListener("click", function () {
                    eliminarRaza(parseInt(this.dataset.id, 10));
                });
            });
        }

        function eliminarRaza(id) {
            Swal.fire({
                icon: "warning",
                title: "¿Eliminar raza?",
                text: "Esta acción no se puede deshacer.",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
                confirmButtonColor: "#d33",
            }).then((result) => {
                if (!result.isConfirmed) return;

                ajax.delete("/admin/breeds/" + id)
                    .then((res) => {
                        Swal.fire("Eliminado", res.message, "success");
                        cargarDatos();
                    })
                    .catch(() => {});
            });
        }

        const inputBusqueda = document.getElementById("busquedaRaza");
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
                pestana.addEventListener("shown.bs.tab", function handler() {
                    cargarDatos();
                    pestana.removeEventListener("shown.bs.tab", handler);
                });
            } else {
                cargarDatos();
            }
        }
    }

    // ------------------------------------------------------------------
    // Modal de crear / editar
    // ------------------------------------------------------------------
    if (formModal && modalEl) {
        const btn = document.getElementById("btnModalBreedGuardar");

        formModal.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formModalBreed");

            const id = document.getElementById("modal-breed-id").value;
            btn.disabled = true;
            const datos = recolectarDatos(formModal);
            delete datos.id;
            const peticion = id
                ? ajax.put("/admin/breeds/" + id, datos)
                : ajax.post("/admin/breeds", datos);

            peticion
                .then((res) => {
                    Swal.fire("Listo", res.message, "success");
                    modalInstance.hide();
                    if (dataTable) dataTable.ajax.reload();
                })
                .catch((error) => {
                    if (error.status === 422) {
                        mostrarToastError(error.response.errors);
                        pintarErroresValidacion(
                            error.response.errors,
                            "formModalBreed",
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });

        modalEl.addEventListener("hidden.bs.modal", function () {
            const titulo = document.getElementById("modalBreedTitle");
            if (titulo) titulo.textContent = "Nueva Raza";
            formModal.reset();
        });
    }

    // ------------------------------------------------------------------
    // Páginas standalone: crear / editar
    // ------------------------------------------------------------------
    const formCrear = document.getElementById("formCrearBreed");
    if (formCrear) {
        const btn = document.getElementById("btnGuardarBreed");
        formCrear.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formCrearBreed");
            btn.disabled = true;
            ajax.post("/admin/breeds", recolectarDatos(formCrear))
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/breeds";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        mostrarToastError(error.response.errors);
                        pintarErroresValidacion(
                            error.response.errors,
                            "formCrearBreed",
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }

    const formEditar = document.getElementById("formEditarBreed");
    if (formEditar) {
        const btn = document.getElementById("btnActualizarBreed");
        formEditar.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formEditarBreed");
            btn.disabled = true;
            ajax.put(
                "/admin/breeds/" + formEditar.dataset.id,
                recolectarDatos(formEditar),
            )
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/breeds";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        mostrarToastError(error.response.errors);
                        pintarErroresValidacion(
                            error.response.errors,
                            "formEditarBreed",
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }
})();
