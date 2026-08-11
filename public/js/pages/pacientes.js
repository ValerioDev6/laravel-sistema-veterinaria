(function () {
    "use strict";

    if (window.Dropzone) {
        Dropzone.autoDiscover = false;
    }

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
            const contenedor = input.closest("[class*='col-'], .mb-3");
            const feedback = contenedor?.querySelector(".invalid-feedback");
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

    // ------------------------------------------------------------------
    // Validación previa: evitar duplicar propietarios antes de enviar
    // ------------------------------------------------------------------
    function validarPropietarioDuplicado(datos) {
        const modoNuevo =
            !datos.get("owner_id") &&
            (datos.get("first_name") ||
                datos.get("email") ||
                datos.get("phone"));

        if (!modoNuevo) return Promise.resolve();

        const nombre = [datos.get("first_name"), datos.get("last_name")]
            .filter(Boolean)
            .join(" ")
            .trim()
            .toLowerCase();
        const email = (datos.get("email") || "").trim().toLowerCase();
        const phone = (datos.get("phone") || "").trim();

        if (!nombre && !email && !phone) return Promise.resolve();

        return ajax
            .get("/admin/owners", { per_page: 200, search: nombre || "" })
            .then((res) => {
                const duplicado = (res.data || []).some((owner) => {
                    const nombreExiste =
                        nombre &&
                        (owner.first_name + " " + owner.last_name)
                            .trim()
                            .toLowerCase() === nombre;
                    const emailExiste =
                        email && owner.email?.toLowerCase() === email;
                    const phoneExiste =
                        phone && (owner.phone || "").trim() === phone;
                    return nombreExiste || emailExiste || phoneExiste;
                });

                if (duplicado) {
                    Swal.fire({
                        toast: true,
                        position: "top-end",
                        icon: "warning",
                        title: "El propietario ya está registrado. Revisa los datos.",
                        showConfirmButton: false,
                        timer: 3500,
                        timerProgressBar: true,
                    });
                    return `El propietario "${nombre}" ya está registrado.`;
                }
                return null;
            })
            .catch(() => null);
    }

    // ------------------------------------------------------------------
    // Dropzone de la foto (previsualización simple)
    // ------------------------------------------------------------------
    let dropzoneFoto = null;

    function initDropzoneFoto() {
        if (!window.Dropzone) return;
        const el = document.getElementById("dropzoneFoto");
        if (!el) return;

        dropzoneFoto = new Dropzone("#dropzoneFoto", {
            url: "#",
            autoProcessQueue: false,
            maxFiles: 1,
            acceptedFiles: "image/*",
            addRemoveLinks: true,
            dictDefaultMessage: "Arrastra la foto o haz clic para seleccionar",
            dictRemoveFile: "Quitar",
            maxFilesize: 5,
        });

        dropzoneFoto.on("addedfile", function (file) {
            while (dropzoneFoto.files.length > 1) {
                dropzoneFoto.removeFile(dropzoneFoto.files[0]);
            }
        });

        dropzoneFoto.on("maxfilesexceeded", function (file) {
            dropzoneFoto.removeFile(file);
        });
    }

    function mostrarFotoDropzone(url) {
        const preview = document.getElementById("previewFotoActual");
        if (preview) {
            if (url) {
                preview.classList.remove("d-none");
                preview.querySelector("img").src = url;
            } else {
                preview.classList.add("d-none");
                preview.querySelector("img").removeAttribute("src");
            }
        }
        if (dropzoneFoto) dropzoneFoto.removeAllFiles(true);
        if (!url) return;
        const mock = {
            name: "foto_actual",
            size: 1,
            type: "image/jpeg",
            accepted: true,
            _recolectar: false,
        };
        dropzoneFoto.files.push(mock);
        dropzoneFoto.emit("addedfile", mock);
        dropzoneFoto.emit("thumbnail", mock, url);
        dropzoneFoto.emit("complete", mock);
    }

    function setearPropietarioRequerido(esEditar) {
        ["first_name", "last_name", "phone"].forEach((campo) => {
            const el = document.getElementById(campo);
            if (el) {
                if (esEditar) {
                    el.removeAttribute("required");
                } else {
                    el.setAttribute("required", "");
                }
            }
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
                    .catch(() => {});
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
        setearPropietarioRequerido(true);
        $("#first_name").val(fila.owner_first_name ?? "");
        $("#last_name").val(fila.owner_last_name ?? "");
        $("#email").val(fila.owner_email ?? "");
        $("#phone").val(fila.owner_phone ?? "");
        $("#address").val(fila.owner_address ?? "");
        $("#city").val(fila.owner_city ?? "");
        $("#type_documento").val(fila.owner_type_documento ?? "");
        $("#n_documento").val(fila.owner_n_documento ?? "");
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

        mostrarFotoDropzone(fila.photo);
    }

    function abrirFormularioNuevo() {
        activarTabFormulario();
        if ($tituloForm.length) $tituloForm.text("Nueva Mascota");
        if ($formTab.length) {
            $formTab[0].reset();
            $formTab.removeAttr("data-id data-modo");
            limpiarErroresValidacion("formCrearPaciente");
            setearPropietarioRequerido(false);
            $("#breed_id")
                .html('<option value="">Primero elige la especie</option>')
                .prop("disabled", true);
            if (dropzoneFoto) dropzoneFoto.removeAllFiles(true);
            const previewFoto = document.getElementById("previewFotoActual");
            if (previewFoto) {
                previewFoto.classList.add("d-none");
                previewFoto.querySelector("img").removeAttribute("src");
            }
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

            const datos = recolectarDatos($form[0]);
            const prevalidar =
                metodo === "post"
                    ? validarPropietarioDuplicado(datos)
                    : Promise.resolve();

            prevalidar
                .then((duplicado) => {
                    if (duplicado) {
                        $btn.prop("disabled", false);
                        return;
                    }
                    return ajax[metodo](ruta, datos)
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
                                mostrarToastError(error.response.errors);
                                pintarErroresValidacion(
                                    error.response.errors,
                                    formId,
                                );
                            }
                        });
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
            "first_name",
            "last_name",
            "email",
            "phone",
            "address",
            "city",
            "type_documento",
            "n_documento",
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

        if (dropzoneFoto && dropzoneFoto.files && dropzoneFoto.files.length) {
            const archivo = dropzoneFoto.files[0];
            if (archivo._recolectar !== false) {
                datos.append("photo", archivo);
            }
        } else {
            const foto = form.elements["photo"];
            if (foto && foto.files && foto.files.length) {
                datos.append("photo", foto.files[0]);
            }
        }

        return datos;
    }

    initDropzoneFoto();
    setearPropietarioRequerido(false);
})();
