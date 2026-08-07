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

    // ------------------------------------------------------------------
    // Selects dependientes (especie -> raza)
    // ------------------------------------------------------------------
    function vincularSelectsEspecieRaza(especieId, razaId, breedIdInicial) {
        const $especie = $("#" + especieId);
        const $raza = $("#" + razaId);
        if (!$especie.length || !$raza.length) return;

        function cargarRazas(speciesId, seleccionadaId) {
            if (!speciesId) {
                $raza
                    .html('<option value="">Primero elige la especie</option>')
                    .prop("disabled", true);
                return Promise.resolve();
            }

            $raza
                .prop("disabled", true)
                .html('<option value="">Cargando razas...</option>');

            return ajax
                .get("/admin/breeds?species_id=" + speciesId)
                .then((res) => {
                    let opciones = '<option value="">Seleccionar raza</option>';
                    res.data.forEach((razaItem) => {
                        const seleccionada =
                            seleccionadaId &&
                            String(razaItem.id) === String(seleccionadaId);
                        opciones +=
                            '<option value="' +
                            razaItem.id +
                            '"' +
                            (seleccionada ? " selected" : "") +
                            ">" +
                            razaItem.name +
                            "</option>";
                    });
                    $raza.html(opciones).prop("disabled", false);
                })
                .catch(() => {
                    $raza
                        .html('<option value="">No se pudieron cargar</option>')
                        .prop("disabled", false);
                });
        }

        $especie.on("change", function () {
            cargarRazas(this.value);
        });

        if (breedIdInicial) {
            cargarRazas($especie.val(), breedIdInicial);
        }

        return { cargarRazas: (sel) => cargarRazas($especie.val(), sel) };
    }

    let dataTable = null;
    let cargarRazasTab = null;

    // ------------------------------------------------------------------
    // Página: index (tabla de pacientes)
    // ------------------------------------------------------------------
    if ($("#table-pacientes").length) {
        let terminoBusqueda = "";

        function cargarDatos() {
            const url = "/admin/pacientes";

            if (!dataTable) {
                dataTable = $("#table-pacientes").DataTable({
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
                                data: res.data,
                            });
                        });
                    },
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json",
                    },
                    columns: [
                        { data: "id" },
                        {
                            data: "photo",
                            orderable: false,
                            render: function (foto, type, fila) {
                                return renderFoto(foto, fila);
                            },
                        },
                        { data: "name" },
                        { data: "species" },
                        { data: "breed" },
                        { data: "owner_name" },
                        { data: "gender" },
                        {
                            data: null,
                            orderable: false,
                            render: function (data, type, fila) {
                                return renderAcciones(fila);
                            },
                        },
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

        function renderFoto(foto, fila) {
            if (!foto) return '<i class="ri-paw-line text-muted"></i>';
            return (
                '<img src="' +
                foto +
                '" class="rounded-circle" style="width:32px;height:32px;object-fit:cover;" alt="' +
                fila.name +
                '">'
            );
        }

        function renderAcciones(paciente) {
            return (
                '<a href="' +
                paciente.show_url +
                '" class="btn btn-soft-info btn-sm me-1" title="Ver ficha">' +
                '<i class="ri-eye-line"></i></a>' +
                '<button type="button" class="btn btn-soft-primary btn-sm me-1 btn-editar-paciente" data-id="' +
                paciente.id +
                '" title="Editar"><i class="ri-pencil-line"></i></button>' +
                '<button type="button" class="btn btn-soft-danger btn-sm btn-eliminar-paciente" data-id="' +
                paciente.id +
                '" title="Eliminar"><i class="ri-delete-bin-line"></i></button>'
            );
        }

        function renderHandlers() {
            $(".btn-editar-paciente")
                .off("click")
                .on("click", function () {
                    const fila = dataTable.row($(this).closest("tr")).data();
                    abrirFormularioEdicion(fila);
                });

            $(".btn-eliminar-paciente")
                .off("click")
                .on("click", function () {
                    eliminarPaciente($(this).data("id"));
                });
        }

        function eliminarPaciente(id) {
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

        let terminoBusquedaTimer = null;

        $("#busquedaPaciente").on("input", function () {
            const termino = this.value;
            clearTimeout(terminoBusquedaTimer);
            terminoBusquedaTimer = setTimeout(function () {
                terminoBusqueda = termino.trim();
                dataTable.ajax.reload();
            }, 350);
        });

        cargarDatos();
    }

    // ------------------------------------------------------------------
    // Tab: Formulario de Paciente (crear / editar en el mismo form)
    // ------------------------------------------------------------------
    const $formTab = $("#formCrearPaciente");
    const $tituloForm = $("#tituloFormPaciente");

    function activarTabFormulario() {
        const tabLink = document.getElementById("tabFormPaciente-tab");
        if (tabLink && window.bootstrap) {
            new bootstrap.Tab(tabLink).show();
        }
    }

    function abrirFormularioEdicion(fila) {
        activarTabFormulario();
        if ($tituloForm.length) $tituloForm.text("Editar Mascota");

        $("#owner_id").val(fila.owner_id || "");
        $("#name").val(fila.name ?? "");
        $("#species_id").val(fila.species_id ?? "");
        $("#gender").val(fila.gender ?? "");
        $("#birth_date").val(fila.birth_date ?? "");
        $("#color").val(fila.color ?? "");
        $("#weight").val(fila.weight ?? "");
        $("#medical_notes").val(fila.medical_notes ?? "");
        if ($formTab.length) {
            $formTab.attr("data-id", fila.id);
            $formTab.attr("data-modo", "editar");
        }

        if (cargarRazasTab && fila.breed_id) {
            cargarRazasTab(fila.breed_id);
        }
    }

    function abrirFormularioNuevo() {
        activarTabFormulario();
        if ($tituloForm.length) $tituloForm.text("Nueva Mascota");
        if ($formTab.length) {
            $formTab[0].reset();
            $formTab.removeAttr("data-id data-modo");
            limpiarErroresValidacion("formCrearPaciente");
            $("#breed_id")
                .html('<option value="">Primero elige la especie</option>')
                .prop("disabled", true);
        }
    }

    $("#btnNuevaMascota").on("click", abrirFormularioNuevo);

    // ------------------------------------------------------------------
    // Formularios de páginas standalone (crear / editar)
    // ------------------------------------------------------------------
    function vincularForm(formId, botonId) {
        const $form = $("#" + formId);
        const $btn = $("#" + botonId);
        if (!$form.length || !$btn.length) return;

        $form.on("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion(formId);
            $btn.prop("disabled", true);

            const ruta =
                "/admin/pacientes" +
                ($form.attr("data-id") ? "/" + $form.attr("data-id") : "");
            const metodo = $form.attr("data-id") ? "put" : "post";

            ajax[metodo](ruta, recolectarDatos($form[0]))
                .then((res) => {
                    const inline = !!$form.closest(".tab-pane").length;
                    Swal.fire("Listo", res.message, "success");
                    if (inline) {
                        abrirFormularioNuevo();
                        if (dataTable) dataTable.ajax.reload();
                    } else {
                        window.location.href = "/admin/pacientes";
                    }
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(error.response.errors, formId);
                    }
                })
                .finally(() => {
                    $btn.prop("disabled", false);
                });
        });

        $form.on("reset", () => limpiarErroresValidacion(formId));
    }

    vincularForm("formCrearPaciente", "btnGuardarPaciente");
    vincularForm("formEditarPaciente", "btnActualizarPaciente");

    // ------------------------------------------------------------------
    // Selects dependientes en el formulario del tab y standalone
    // ------------------------------------------------------------------
    const razaInicial = $("#breed_id").attr("data-selected");
    const vinculoRazasTab = vincularSelectsEspecieRaza(
        "species_id",
        "breed_id",
        razaInicial,
    );
    if (vinculoRazasTab) {
        cargarRazasTab = vinculoRazasTab.cargarRazas;
    }

    // ------------------------------------------------------------------
    // Recolección manual de datos con FormData
    // ------------------------------------------------------------------
    function recolectarDatos(form) {
        const datos = new FormData();

        const campos = [
            "owner_id",
            "name",
            "species_id",
            "breed_id",
            "gender",
            "birth_date",
            "color",
            "weight",
            "medical_notes",
        ];
        campos.forEach((campo) => {
            const elemento = form.elements[campo];
            if (elemento) {
                datos.append(campo, elemento.value.trim());
            }
        });

        const foto = form.elements["photo"];
        if (foto && foto.files && foto.files.length) {
            datos.append("photo", foto.files[0]);
        }

        return datos;
    }
})();
