$(function () {
    const medicamentos = window.medicines || [];

function getFiltros() {
        const $form = $("#formFiltros");
        return $form.length ? $form.serializeArray().reduce((acc, campo) => {
            if (campo.value) acc[campo.name] = campo.value;
            return acc;
        }, {}) : {};
    }

    function cargarDatos() {
        const tabla = $("#table-medical-records");
        if (!tabla.length) return;
        const url = "/admin/medical-records";

        if (!$.fn.DataTable.isDataTable(tabla)) {
            tabla.DataTable({
                serverSide: true,
                processing: true,
                pageLength: 15,
                ajax: function (data, callback) {
                    const params = {
                        per_page: data.length,
                        page: Math.floor(data.start / data.length) + 1,
                        search: data.search.value,
                    };
                    if (data.order && data.order.length) {
                        params.sort_by = data.order[0].column;
                        params.sort_dir = data.order[0].dir;
                    }
                    Object.assign(params, getFiltros());
                    ajax.get(url, params).then((res) => {
                        callback({
                            draw: data.draw,
                            recordsTotal: res.pagination.total,
                            recordsFiltered: res.pagination.total,
                            data: res.data.map((registro) => [
                                registro.id,
                                registro.pet_name || "-",
                                registro.veterinarian || "-",
                                registro.event_type,
                                registro.event_date || "-",
                                `
                                    <a href="${registro.show_url}" class="btn btn-sm btn-outline-primary"><i class="ri-eye-line"></i></a>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar" data-id="${registro.id}">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                `,
                            ]),
                        });
                    });
                },
                language: { url: "//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json" },
                order: [[0, "desc"]],
                columnDefs: [{ targets: [5], orderable: false }],
            });
        } else {
            tabla.DataTable().ajax.reload();
        }
    }

    function construirDataTable() {
        const tabla = $("#table-medical-records");
        if (!tabla.length || $.fn.DataTable.isDataTable(tabla)) return;

        tabla.DataTable({
            language: { url: "//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json" },
            order: [[0, "desc"]],
            columnDefs: [{ targets: [5], orderable: false }],
        });
    }

    function construirDataTable() {
        const tabla = $("#table-medical-records");
        if (!tabla.length || $.fn.DataTable.isDataTable(tabla)) return;

        tabla.DataTable({
            language: { url: "//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json" },
            order: [[0, "desc"]],
            columnDefs: [{ targets: [5], orderable: false }],
        });
    }

    function pintarErroresValidacion(xhr, formulario) {
        const $form = $(formulario);
        $form.find(".invalid-feedback").text("").parent().find(".is-invalid").removeClass("is-invalid");
        const errores = xhr.responseJSON?.errors;
        if (!errores) return;
        Object.entries(errores).forEach(([campo, mensajes]) => {
            const $input = $form.find(`[name="${campo}"]`);
            if ($input.length) {
                $input.addClass("is-invalid");
                $input.siblings(".invalid-feedback").text(mensajes[0]);
            }
        });
    }

    $("#formFiltros").on("submit", function (e) {
        e.preventDefault();
        cargarDatos();
    });

    function pintarRecetas(data = null) {
        const contenedor = $("#listaRecetas");
        if (!contenedor.length) return;
        contenedor.empty();

        const recetas = data?.prescriptions || [];
        recetas.forEach((receta) => pintarFilaReceta(receta));

        if (!recetas.length) {
            contenedor.append(`
                <p class="text-muted mb-0" id="sinRecetas">Sin medicamentos agregados.</p>
            `);
        }
    }

    function pintarFilaReceta(receta = {}) {
        const contenedor = $("#listaRecetas");
        if (!contenedor.length) return;
        contenedor.find("#sinRecetas").remove();

        const opciones = medicamentos.map((m) =>
            `<option value="${m.id}" ${String(m.id) === String(receta.medicine_id) ? "selected" : ""}>${m.name}</option>`
        ).join("");

        contenedor.append(`
            <div class="row g-2 mb-2 fila-receta">
                <div class="col-md-4">
                    <select class="form-select" name="prescriptions[][medicine_id]">
                        <option value="">Medicamento</option>
                        ${opciones}
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="prescriptions[][dosage]" value="${receta.dosage || ""}" placeholder="Dosis (ej: 1 cda c/8h)">
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control" name="prescriptions[][duration_days]" value="${receta.duration_days || ""}" placeholder="Duración (días)">
                </div>
                <div class="col-md-1 d-flex align-items-center">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-quitar-receta"><i class="ri-close-line"></i></button>
                </div>
            </div>
        `);
    }

    $("#btnAgregarReceta").on("click", function () {
        pintarFilaReceta();
    });

    $(document).on("click", ".btn-quitar-receta", function () {
        const fila = $(this).closest(".fila-receta");
        fila.remove();
        if (!$("#listaRecetas .fila-receta").length) {
            $("#listaRecetas").append(
                '<p class="text-muted mb-0" id="sinRecetas">Sin medicamentos agregados.</p>'
            );
        }
    });

    function serializarRecetas() {
        const recetas = [];
        $("#listaRecetas .fila-receta").each(function () {
            const fila = $(this);
            const medicineId = fila.find('[name="prescriptions[][medicine_id]"]').val();
            const dosage = fila.find('[name="prescriptions[][dosage]"]').val();
            const duration = fila.find('[name="prescriptions[][duration_days]"]').val();
            if (medicineId) {
                recetas.push({
                    medicine_id: medicineId,
                    dosage: dosage,
                    duration_days: duration || null,
                });
            }
        });
        return recetas;
    }

    const $formCrear = $("#formCrearRegistro");
    if ($formCrear.length) {
        $formCrear.on("submit", function (e) {
            e.preventDefault();
            const datos = $formCrear.serializeArray();
            const vital = {};
            datos.forEach((d) => {
                if (d.name.startsWith("vital_signs[")) {
                    const clave = d.name.replace("vital_signs[", "").replace("]", "");
                    vital[clave] = d.value;
                }
            });

            const payload = {
                pet_id: $formCrear.find('[name="pet_id"]').val(),
                veterinarian_id: $formCrear.find('[name="veterinarian_id"]').val(),
                event_type: $formCrear.find('[name="event_type"]').val(),
                event_date: $formCrear.find('[name="event_date"]').val(),
                cita_id: $formCrear.find('[name="cita_id"]').val() || null,
                notes: $formCrear.find('[name="notes"]').val(),
                vital_signs: Object.values(vital).some((v) => v !== "") ? vital : null,
                prescriptions: serializarRecetas(),
            };

            const $btn = $("#btnGuardarRegistro").prop("disabled", true);
            $.ajax({
                url: "/api/admin/medical-records",
                method: "POST",
                data: JSON.stringify(payload),
                contentType: "application/json",
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                success: function (respuesta) {
                    Swal.fire({ icon: "success", title: "Éxito", text: respuesta.message, timer: 1500, showConfirmButton: false })
                        .then(() => window.location.href = `/admin/medical-records/${respuesta.data.id}`);
                },
                error: function (xhr) {
                    pintarErroresValidacion(xhr, $formCrear);
                    if (xhr.responseJSON?.message) {
                        Swal.fire({ icon: "error", title: "Error", text: xhr.responseJSON.message });
                    }
                },
                complete: function () { $btn.prop("disabled", false); },
            });
        });

        pintarRecetas();
    }

    const $formAdjunto = $("#formSubirAdjunto");
    if ($formAdjunto.length) {
        $formAdjunto.on("submit", function (e) {
            e.preventDefault();
            const formData = new FormData($formAdjunto[0]);
            $.ajax({
                url: `/api/admin/medical-records/${window.registro}/adjuntos`,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                success: function (respuesta) {
                    Swal.fire({ icon: "success", title: "Éxito", text: respuesta.message, timer: 1200, showConfirmButton: false })
                        .then(() => window.location.reload());
                },
                error: function (xhr) {
                    const $input = $formAdjunto.find('[name="file"]');
                    $input.removeClass("is-invalid").siblings(".invalid-feedback").text("");
                    if (xhr.responseJSON?.errors?.file) {
                        $input.addClass("is-invalid").siblings(".invalid-feedback").text(xhr.responseJSON.errors.file[0]);
                    } else if (xhr.responseJSON?.message) {
                        Swal.fire({ icon: "error", title: "Error", text: xhr.responseJSON.message });
                    }
                },
            });
        });

        $(document).on("click", ".btn-eliminar-adjunto", function () {
            const $btn = $(this);
            const id = $btn.data("id");
            Swal.fire({
                title: "¿Eliminar adjunto?",
                text: "Esta acción no se puede deshacer.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: `/api/admin/adjuntos/${id}`,
                    method: "DELETE",
                    headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                    success: function () {
                        $btn.closest(".d-flex").fadeOut(200, function () { $(this).remove(); });
                    },
                });
            });
        });
    }

    $("#btnEliminarRegistro").on("click", function () {
        Swal.fire({
            title: "¿Eliminar entrada?",
            text: "Esta acción no se puede deshacer.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (!result.isConfirmed) return;
            $.ajax({
                url: `/api/admin/medical-records/${window.registro}`,
                method: "DELETE",
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                success: function (respuesta) {
                    Swal.fire({ icon: "success", title: "Éxito", text: respuesta.message, timer: 1200, showConfirmButton: false })
                        .then(() => window.location.href = "/admin/medical-records");
                },
            });
        });
    });

    $(document).on("click", ".btn-eliminar", function () {
        const $btn = $(this);
        const id = $btn.data("id");
        Swal.fire({
            title: "¿Eliminar entrada?",
            text: "Esta acción no se puede deshacer.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (!result.isConfirmed) return;
            $.ajax({
                url: `/api/admin/medical-records/${id}`,
                method: "DELETE",
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                success: function () { cargarDatos(); },
            });
        });
    });

    cargarDatos();
});