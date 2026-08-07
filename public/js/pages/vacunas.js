(function () {
    "use strict";

    const tableEl = document.getElementById("table-vacunas");
    if (tableEl) {
        let dataTable = null;

        function cargarDatos() {
            const url = "/admin/vacunas";

            if (!dataTable) {
                dataTable = $("#table-vacunas").DataTable({
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
                                data: res.data.map(datosCargados),
                            });
                        });
                    },
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json",
                    },
                    columns: [
                        { data: "id" },
                        { data: "mascota" },
                        { data: "vacuna" },
                        { data: "veterinario" },
                        { data: "fecha" },
                        { data: "proxima" },
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

        function datosCargados(v) {
            return {
                id: v.id,
                mascota: v.pet_name,
                vacuna: v.vaccine_type,
                veterinario: v.veterinarian,
                fecha: v.vaccination_date,
                proxima: v.next_due_date || "—",
                acciones: renderAcciones(v),
            };
        }

        function renderAcciones(v) {
            return (
                '<a href="' +
                v.edit_url +
                '" class="btn btn-soft-primary btn-sm me-1" title="Editar">' +
                '<i class="ri-pencil-line"></i></a>' +
                '<button type="button" class="btn btn-soft-danger btn-sm btn-eliminar-vacuna" data-id="' +
                v.id +
                '" title="Eliminar"><i class="ri-delete-bin-line"></i></button>'
            );
        }

        function bindHandlers() {
            document
                .querySelectorAll(".btn-eliminar-vacuna")
                .forEach((btn) => {
                    btn.addEventListener("click", function () {
                        eliminar(parseInt(this.dataset.id, 10));
                    });
                });
        }

        function eliminar(id) {
            Swal.fire({
                icon: "warning",
                title: "¿Eliminar vacuna?",
                text: "Esta acción no se puede deshacer.",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
                confirmButtonColor: "#d33",
            }).then((result) => {
                if (!result.isConfirmed) return;

                ajax.delete("/admin/vacunas/" + id)
                    .then((res) => {
                        Swal.fire("Eliminado", res.message, "success");
                        cargarDatos();
                    })
                    .catch((error) => {
                        if (error.status === 422) {
                            Swal.fire(
                                "No se pudo eliminar",
                                error.response?.errors?.vacuna?.[0],
                                "warning",
                            );
                        }
                    });
            });
        }

        cargarDatos();
    }

    const formCrear = document.getElementById("formCrearVacuna");
    if (formCrear) {
        const btn = document.getElementById("btnGuardarVacuna");
        formCrear.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formCrearVacuna");
            btn.disabled = true;
            ajax.post("/admin/vacunas", serializarFormulario("formCrearVacuna"))
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/vacunas";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(error.response.errors, "formCrearVacuna");
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }

    const formEditar = document.getElementById("formEditarVacuna");
    if (formEditar) {
        const btn = document.getElementById("btnActualizarVacuna");
        formEditar.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formEditarVacuna");
            btn.disabled = true;
            ajax.put(
                "/admin/vacunas/" + formEditar.dataset.id,
                serializarFormulario("formEditarVacuna"),
            )
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/vacunas";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(error.response.errors, "formEditarVacuna");
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }
})();