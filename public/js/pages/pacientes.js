(function () {
    "use strict";

    const tableEl = document.getElementById("table-pacientes");
    if (tableEl) {
        let dataTable = null;

        function cargarDatos() {
            const url = "/admin/pacientes";

            if (!dataTable) {
                dataTable = $("#table-pacientes").DataTable({
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
                        { data: "foto", orderable: false },
                        { data: "nombre" },
                        { data: "especie" },
                        { data: "raza" },
                        { data: "propietario" },
                        { data: "sexo" },
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

        function datosCargados(p) {
            return {
                id: p.id,
                foto: p.photo
                    ? '<img src="' +
                      p.photo +
                      '" class="rounded-circle" style="width:32px;height:32px;object-fit:cover;">'
                    : '<i class="ri-paw-line text-muted"></i>',
                nombre: p.name,
                especie: p.species || "—",
                raza: p.breed || "—",
                propietario: p.owner_name,
                sexo: p.gender,
                acciones: renderAcciones(p),
            };
        }

        function renderAcciones(p) {
            return (
                '<a href="' +
                p.show_url +
                '" class="btn btn-soft-info btn-sm me-1" title="Ver ficha">' +
                '<i class="ri-eye-line"></i></a>' +
                '<a href="' +
                p.edit_url +
                '" class="btn btn-soft-primary btn-sm me-1" title="Editar">' +
                '<i class="ri-pencil-line"></i></a>' +
                '<button type="button" class="btn btn-soft-danger btn-sm btn-eliminar-paciente" data-id="' +
                p.id +
                '" title="Eliminar"><i class="ri-delete-bin-line"></i></button>'
            );
        }

        function bindHandlers() {
            document
                .querySelectorAll(".btn-eliminar-paciente")
                .forEach((btn) => {
                    btn.addEventListener("click", function () {
                        eliminar(parseInt(this.dataset.id, 10));
                    });
                });
        }

        function eliminar(id) {
            Swal.fire({
                icon: "warning",
                title: "¿Eliminar mascota?",
                text: "Esta acción no se puede deshacer.",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
                confirmButtonColor: "#d33",
            }).then((result) => {
                if (!result.isConfirmed) return;

                ajax.delete("/admin/pacientes/" + id)
                    .then((res) => {
                        Swal.fire("Eliminado", res.message, "success");
                        cargarDatos();
                    })
                    .catch((error) => {
                        if (error.status === 422) {
                            Swal.fire(
                                "No se pudo eliminar",
                                error.response?.errors?.paciente?.[0],
                                "warning",
                            );
                        }
                    });
            });
        }

        cargarDatos();
    }

    const speciesSelect = document.getElementById("species_id");
    const breedSelect = document.getElementById("breed_id");

    function cargarRazas(speciesId, selectedBreedId) {
        if (!breedSelect) return Promise.resolve();

        if (!speciesId) {
            breedSelect.innerHTML =
                '<option value="">Primero elige la especie</option>';
            breedSelect.disabled = true;
            return Promise.resolve();
        }

        breedSelect.disabled = true;
        breedSelect.innerHTML =
            '<option value="">Cargando razas...</option>';

        return ajax
            .get("/admin/breeds?species_id=" + speciesId)
            .then((res) => {
                let html = '<option value="">Seleccionar raza</option>';
                res.data.forEach((b) => {
                    html +=
                        '<option value="' +
                        b.id +
                        '"' +
                        (selectedBreedId && String(b.id) === String(selectedBreedId)
                            ? " selected"
                            : "") +
                        ">" +
                        b.name +
                        "</option>";
                });
                breedSelect.innerHTML = html;
                breedSelect.disabled = false;
            })
            .catch(() => {
                breedSelect.innerHTML =
                    '<option value="">No se pudieron cargar</option>';
                breedSelect.disabled = false;
            });
    }

    if (speciesSelect && breedSelect) {
        speciesSelect.addEventListener("change", function () {
            cargarRazas(this.value, null);
        });

        if (breedSelect.dataset.selected) {
            cargarRazas(speciesSelect.value, breedSelect.dataset.selected);
        }
    }

    function initForm(formId, btnId, endpoint, method) {
        const form = document.getElementById(formId);
        if (!form) return;

        const btn = document.getElementById(btnId);
        form.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion(formId);
            btn.disabled = true;
            ajax[method](endpoint + (form.dataset.id ? "/" + form.dataset.id : ""), serializarFormData(formId))
                .then((res) => {
                    Swal.fire("Listo", res.message, "success").then(() => {
                        window.location.href = "/admin/pacientes";
                    });
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(error.response.errors, formId);
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }

    initForm("formCrearPaciente", "btnGuardarPaciente", "/admin/pacientes", "post");
    initForm("formEditarPaciente", "btnActualizarPaciente", "/admin/pacientes", "put");
})();