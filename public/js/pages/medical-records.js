$(function () {
    const medicamentos = window.medicines || [];

    const MAPA_TIPOS = {
        cita: { label: "Cita", icono: "ri-calendar-check-line", clase: "bg-soft-info text-info" },
        consulta: { label: "Cita", icono: "ri-calendar-check-line", clase: "bg-soft-info text-info" },
        vacuna: { label: "Vacuna", icono: "ri-syringe-line", clase: "bg-soft-success text-success" },
        cirugia: { label: "Cirugía", icono: "ri-scissors-2-line", clase: "bg-soft-primary text-primary" },
        otro: { label: "Otro", icono: "ri-file-line", clase: "bg-soft-secondary text-secondary" },
    };
    const TIPOS_TABS = { todos: null, citas: ["consulta", "cita"], vacunas: "vacuna", cirugias: "cirugia" };
    let historialCargado = [];

    function renderTarjeta(evento) {
        const meta = MAPA_TIPOS[evento.tipo] || MAPA_TIPOS.otro;
        const notas = evento.notas ? evento.notas : "Sin notas registradas.";
        return `
            <a href="${evento.url}" class="text-decoration-none text-body">
                <div class="card mb-3 medical-card shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge ${meta.clase}">
                                <i class="${meta.icono} me-1"></i>${meta.label}
                            </span>
                            <small class="text-muted text-nowrap">
                                <i class="ri-calendar-line me-1"></i>${evento.fecha || "-"}
                            </small>
                        </div>
                        <h6 class="mb-2">${evento.pet_name || "Mascota"}</h6>
                        <p class="small text-muted mb-2 notas-clamp">
                            <i class="ri-sticky-note-line me-1"></i>${notas}
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="ri-user-star-line me-1"></i>${evento.vet || "—"}
                            </small>
                            <small class="text-primary">
                                Ver detalle <i class="ri-arrow-right-s-line"></i>
                            </small>
                        </div>
                    </div>
                </div>
            </a>
        `;
    }

    function aEventoHistorial(r) {
        if (r.event_type) {
            return {
                tipo: r.event_type,
                fecha: r.event_date,
                pet_name: r.pet_name,
                vet: r.veterinarian,
                notas: r.notes,
                url: r.show_url,
            };
        }
        return {
            tipo: "cita",
            fecha: r.appointment_date,
            pet_name: r.pet_name,
            vet: r.veterinarian,
            notas: r.reason,
            url: r.edit_url,
        };
    }

    function renderVacio(mensaje) {
        return `
            <div class="text-center py-5">
                <div class="avatar-lg mx-auto mb-3 rounded-circle bg-soft-light d-flex align-items-center justify-content-center">
                    <i class="ri-inbox-line fs-1 text-muted"></i>
                </div>
                <p class="text-muted mb-0">${mensaje}</p>
            </div>
        `;
    }

    function pintarTab(tipo, eventos) {
        const pane = document.getElementById(`tab-${tipo}`);
        if (!pane) return;
        const tiposTab = TIPOS_TABS[tipo];
        const filtrados = Array.isArray(tiposTab)
            ? eventos.filter((e) => tiposTab.includes(e.tipo))
            : tiposTab
              ? eventos.filter((e) => e.tipo === tiposTab)
              : eventos;

        const badgeCount = document.getElementById(`count-${tipo}`);
        if (badgeCount) badgeCount.textContent = filtrados.length;

        const grid = $("<div>").addClass("row g-3");
        if (!filtrados.length) {
            grid.append(
                `<div class="col-12">${renderVacio(
                    tipo === "todos"
                        ? "Esta mascota no tiene historial médico registrado."
                        : `Sin ${tipo} para esta mascota.`
                )}</div>`,
            );
        } else {
            filtrados.forEach((e) => {
                grid.append(`<div class="col-md-6 col-xl-4">${renderTarjeta(e)}</div>`);
            });
        }
        $(pane).empty().append(grid);
    }

    function cargarHistorial(petId) {
        Promise.all([
            ajax.get("/admin/medical-records", { pet_id: petId }),
            ajax.get("/admin/citas", { pet_id: petId, per_page: 100 }),
        ]).then(([recordsRes, citasRes]) => {
            historialCargado = [
                ...(recordsRes.data || []),
                ...(citasRes.data || []),
            ]
                .map(aEventoHistorial)
                .sort((a, b) => (a.fecha || "").localeCompare(b.fecha || ""))
                .reverse();
            $("#bloqueHistorial").removeClass("d-none");
            $("#estadoVacio").addClass("d-none");
            Object.keys(TIPOS_TABS).forEach((tipo) => pintarTab(tipo, historialCargado));
        });
    }

    $("#filtro_pet_id").on("change", function () {
        const petId = this.value;
        historialCargado = [];
        Object.keys(TIPOS_TABS).forEach((tipo) => {
            const badgeCount = document.getElementById(`count-${tipo}`);
            if (badgeCount) badgeCount.textContent = "0";
        });
        if (!petId) {
            $("#bloqueHistorial").addClass("d-none");
            $("#estadoVacio").removeClass("d-none");
            return;
        }
        cargarHistorial(petId);
    });

    $("#tabsHistorial").on("shown.bs.tab", "a[data-tipo]", function () {
        const tipo = $(this).data("tipo");
        if (tipo) pintarTab(tipo, historialCargado);
    });

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
        function renderPreviewMascota() {
            const data = (window.medicalRecordsPacientesData || {})[
                $formCrear.find('[name="pet_id"]').val()
            ];
            const $bloque = $("#bloquePreviewMascota");
            if (!data) {
                $bloque.addClass("d-none");
                return;
            }
            $("#previewFotoMascota").html(
                data.photo
                    ? `<img src="${data.photo}" alt="${data.name}" class="rounded-circle" style="width:44px;height:44px;object-fit:cover;">`
                    : '<i class="ri-paw-line fs-2 text-muted"></i>',
            );
            $("#previewDatosMascota").html(
                `<strong class="text-dark">${data.name}</strong><br>` +
                    [data.species, data.breed, data.gender]
                        .filter(Boolean)
                        .join(" · ") +
                    `<div class="text-muted"><i class="ri-user-line me-1"></i>${data.owner || "—"}` +
                    (data.weight ? ` · ${data.weight} kg` : "") +
                    "</div>",
            );
            $bloque.removeClass("d-none");
        }

        $formCrear.find('[name="pet_id"]').on("change", renderPreviewMascota);

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
});