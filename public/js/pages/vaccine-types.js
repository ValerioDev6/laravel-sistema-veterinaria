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
        const base_price = form.elements["base_price"];
        if (base_price) datos.append("base_price", base_price.value.trim());
        const species_id = form.elements["species_id"];
        if (species_id) datos.append("species_id", species_id.value.trim());
        return datos;
    }

    const tableEl = document.getElementById("table-vaccine-types");
    if (tableEl) {
        let dataTable = null;
        let terminoBusqueda = "";
        let terminoBusquedaTimer = null;

        function getFilters() {
            const filtros = {};
            if (terminoBusqueda) filtros.search = terminoBusqueda;
            return filtros;
        }

        function cargarDatos() {
            const url = "/admin/vaccine-types";

            if (!dataTable) {
                dataTable = $("#table-vaccine-types").DataTable({
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
                        if (data.order && data.order.length) {
                            params.sort_by = data.order[0].column;
                            params.sort_dir = data.order[0].dir;
                        }
                        Object.assign(params, getFilters());
                        ajax.get(url, params).then((res) => {
                            callback({
                                draw: data.draw,
                                recordsTotal: res.pagination.total,
                                recordsFiltered: res.pagination.total,
                                data: res.data.map((v) => ({
                                    id: v.id,
                                    name: v.name,
                                    precio:
                                        "<span class='fw-semibold'>S/ " +
                                        (v.base_price
                                            ? Number(v.base_price).toFixed(2)
                                            : "0.00") +
                                        "</span>",
                                    species: v.species || "Todas",
                                    acciones: renderAcciones(v),
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
                        { data: "precio" },
                        { data: "species" },
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

        function renderAcciones(v) {
            return (
                '<a href="' +
                v.edit_url +
                '" class="btn btn-soft-primary btn-sm me-1" title="Editar">' +
                '<i class="ri-pencil-line"></i></a>' +
                '<button type="button" class="btn btn-soft-danger btn-sm btn-eliminar-vaccine-type" data-id="' +
                v.id +
                '" title="Eliminar"><i class="ri-delete-bin-line"></i></button>'
            );
        }

        function bindHandlers() {
            document
                .querySelectorAll(".btn-eliminar-vaccine-type")
                .forEach((btn) => {
                    btn.addEventListener("click", function () {
                        eliminar(parseInt(this.dataset.id, 10));
                    });
                });
        }

        function eliminar(id) {
            Swal.fire({
                icon: "warning",
                title: "¿Eliminar tipo de vacuna?",
                text: "Esta acción no se puede deshacer.",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
                confirmButtonColor: "#d33",
            }).then((result) => {
                if (!result.isConfirmed) return;

                ajax.delete("/admin/vaccine-types/" + id)
                    .then((res) => {
                        Swal.fire("Eliminado", res.message, "success");
                        cargarDatos();
                    })
                    .catch(() => {});
            });
        }

        $("#busquedaVaccineTypes").on("input", function () {
            const termino = this.value;
            clearTimeout(terminoBusquedaTimer);
            terminoBusquedaTimer = setTimeout(function () {
                terminoBusqueda = termino.trim();
                dataTable.ajax.reload();
            }, 350);
        });

        $("#busquedaVaccineTypes").on("keydown", function (e) {
            if (e.key === "Enter") e.preventDefault();
        });

        const formFiltros = document.getElementById("formFiltrosVaccineTypes");
        if (formFiltros) {
            formFiltros.addEventListener("submit", (e) => {
                e.preventDefault();
                terminoBusqueda = document
                    .getElementById("busquedaVaccineTypes")
                    .value.trim();
                cargarDatos();
            });
            document
                .getElementById("btnLimpiarFiltros")
                ?.addEventListener("click", () => {
                    formFiltros.reset();
                    terminoBusqueda = "";
                    cargarDatos();
                });
        }

        cargarDatos();
    }

    const tabFormular = document.getElementById("tabFormularVacunas-tab");

    function activarTabFormular() {
        if (tabFormular && window.bootstrap) {
            new bootstrap.Tab(tabFormular).show();
        }
    }

    function activarTabListado() {
        const tabListado = document.querySelector(
            '[data-bs-toggle="tab"][href="#tabListadoVaccineTypes"]',
        );
        if (tabListado && window.bootstrap) {
            new bootstrap.Tab(tabListado).show();
        }
    }

    document
        .getElementById("btnNuevoVaccineType")
        ?.addEventListener("click", activarTabFormular);

    const formCrear = document.getElementById("formCrearVaccineType");
    if (formCrear) {
        const btn = document.getElementById("btnGuardarVaccineType");
        formCrear.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formCrearVaccineType");
            const datos = recolectarDatos(formCrear);
            btn.disabled = true;
            ajax.post("/admin/vaccine-types", datos)
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        activarTabListado();
                        formCrear.reset();
                        const dataTable = $("#table-vaccine-types").DataTable();
                        if (dataTable) dataTable.ajax.reload();
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(
                            error.response.errors,
                            "formCrearVaccineType",
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }

    const formEditar = document.getElementById("formEditarVaccineType");
    if (formEditar) {
        const btn = document.getElementById("btnActualizarVaccineType");
        formEditar.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formEditarVaccineType");
            const datos = recolectarDatos(formEditar);
            btn.disabled = true;
            ajax.put(
                "/admin/vaccine-types/" + formEditar.dataset.id,
                datos,
            )
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/vaccine-types";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(
                            error.response.errors,
                            "formEditarVaccineType",
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }
})();