$(function () {
    const servicios = window.servicios || { cita: {}, vacuna: {}, surgiere: {} };

    function badgeEstado(status) {
        const map = { pagado: "success", parcial: "warning", pendiente: "info", anulado: "secondary" };
        const texto = status.charAt(0).toUpperCase() + status.slice(1);
        return `<span class="badge bg-${map[status] || "secondary"}">${texto}</span>`;
    }

    function getFiltros(formulario) {
        const $form = $(formulario);
        return $form.length ? $form.serializeArray().reduce((acc, campo) => {
            if (campo.value) acc[campo.name] = campo.value;
            return acc;
        }, {}) : {};
    }

    function cargarDatos() {
        const tabla = $("#table-invoices");
        if (!tabla.length) return;
        const url = "/admin/invoices";

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
                    Object.assign(params, getFiltros("#formFiltros"));
                    ajax.get(url, params).then((res) => {
                        callback({
                            draw: data.draw,
                            recordsTotal: res.pagination.total,
                            recordsFiltered: res.pagination.total,
                            data: res.data.map((registro) => [
                                `#${registro.id}`,
                                (registro.invoiceable_label || "-"),
                                registro.owner_name || "-",
                                `$${Number(registro.total).toLocaleString("es", { minimumFractionDigits: 2 })}`,
                                `$${Number(registro.remaining_balance).toLocaleString("es", { minimumFractionDigits: 2 })}`,
                                badgeEstado(registro.status),
                                registro.issued_at || "-",
                                `
                                    <a href="${registro.show_url}" class="btn btn-sm btn-outline-primary"><i class="ri-eye-line"></i></a>
                                `,
                            ]),
                        });
                    });
                },
                language: { url: "//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json" },
                order: [[0, "desc"]],
                columnDefs: [{ targets: [7], orderable: false }],
            });
        } else {
            tabla.DataTable().ajax.reload();
        }
    }

    function cargarPagos() {
        const tabla = $("#table-payments");
        if (!tabla.length) return;
        const url = "/admin/payments";

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
                    Object.assign(params, getFiltros("#formFiltrosPagos"));
                    ajax.get(url, params).then((res) => {
                        callback({
                            draw: data.draw,
                            recordsTotal: res.pagination.total,
                            recordsFiltered: res.pagination.total,
                            data: res.data.map((registro) => [
                                `#${registro.id}`,
                                `<a href="${registro.invoice_url}">${registro.invoice_label}</a>`,
                                registro.owner_name || "-",
                                `$${Number(registro.amount).toLocaleString("es", { minimumFractionDigits: 2 })}`,
                                registro.payment_method || "-",
                                badgeEstado(registro.status),
                                registro.paid_at || "-",
                                `
                                    <a href="${registro.invoice_url}" class="btn btn-sm btn-outline-primary"><i class="ri-eye-line"></i></a>
                                `,
                            ]),
                        });
                    });
                },
                language: { url: "//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json" },
                order: [[0, "desc"]],
                columnDefs: [{ targets: [7], orderable: false }],
            });
        } else {
            tabla.DataTable().ajax.reload();
        }
    }

    function construirDataTable() {
        const tabla = $("#table-invoices");
        if (!tabla.length || $.fn.DataTable.isDataTable(tabla)) return;

        tabla.DataTable({
            language: { url: "//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json" },
            order: [[0, "desc"]],
            columnDefs: [{ targets: [7], orderable: false }],
        });

        const tablaPagos = $("#table-payments");
        if (tablaPagos.length && !$.fn.DataTable.isDataTable(tablaPagos)) {
            tablaPagos.DataTable({
                language: { url: "//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json" },
                order: [[0, "desc"]],
                columnDefs: [{ targets: [7], orderable: false }],
            });
        }
    }

    $("#formFiltros").on("submit", function (e) {
        e.preventDefault();
        cargarDatos();
    });

    $("#formFiltrosPagos").on("submit", function (e) {
        e.preventDefault();
        cargarPagos();
    });

    function activarTabDesdeHash() {
        if (window.location.hash === "#pagos") {
            $('[data-bs-toggle="tab"][data-tab="payments"]').tab("show");
        }
    }

    activarTabDesdeHash();

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

    const $formCrear = $("#formCrearFactura");

    function cargarServicios() {
        const tipo = $formCrear.find('[name="invoiceable_type"]').val();
        const $select = $formCrear.find('[name="invoiceable_id"]');
        $select.empty().append('<option value="">Seleccionar</option>');
        const lista = servicios[tipo] || {};
        Object.entries(lista).forEach(([id, label]) => {
            $select.append(`<option value="${id}">${label}</option>`);
        });
    }

    $formCrear.find('[name="invoiceable_type"]').on("change", cargarServicios);

    if ($formCrear.length) {
        $formCrear.find("#first_payment").on("change", function () {
            const activo = $(this).val() === "on";
            $formCrear.find('[name="payment_method"]').prop("required", activo);
            $formCrear.find('[name="advance_amount"]').prop("required", activo);
        });

        $formCrear.on("submit", function (e) {
            e.preventDefault();
            const payload = {
                invoiceable_type: $formCrear.find('[name="invoiceable_type"]').val(),
                invoiceable_id: $formCrear.find('[name="invoiceable_id"]').val(),
                owner_id: $formCrear.find('[name="owner_id"]').val(),
                total: $formCrear.find('[name="total"]').val(),
                issued_at: $formCrear.find('[name="issued_at"]').val(),
                first_payment: $formCrear.find('[name="first_payment"]').val(),
                payment_method: $formCrear.find('[name="payment_method"]').val() || null,
                advance_amount: $formCrear.find('[name="advance_amount"]').val() || null,
            };

            const $btn = $("#btnGuardarFactura").prop("disabled", true);
            $.ajax({
                url: "/api/admin/invoices",
                method: "POST",
                data: JSON.stringify(payload),
                contentType: "application/json",
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                success: function (respuesta) {
                    Swal.fire({ icon: "success", title: "Éxito", text: respuesta.message, timer: 1500, showConfirmButton: false })
                        .then(() => window.location.href = `/admin/invoices/${respuesta.data.id}`);
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
    }

    if (window.factura) {
        const $formPago = $("#formRegistrarPago");
        if ($formPago.length) {
            $formPago.on("submit", function (e) {
                e.preventDefault();
                const payload = {
                    amount: $formPago.find('[name="amount"]').val(),
                    advance_amount: $formPago.find('[name="advance_amount"]').val() || null,
                    payment_method: $formPago.find('[name="payment_method"]').val(),
                    paid_at: $formPago.find('[name="paid_at"]').val() || null,
                };

                const $btn = $("#btnRegistrarPago").prop("disabled", true);
                $.ajax({
                    url: `/api/admin/invoices/${window.factura}/payments`,
                    method: "POST",
                    data: JSON.stringify(payload),
                    contentType: "application/json",
                    headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                    success: function (respuesta) {
                        Swal.fire({ icon: "success", title: "Éxito", text: respuesta.message, timer: 1200, showConfirmButton: false })
                            .then(() => window.location.reload());
                    },
                    error: function (xhr) {
                        pintarErroresValidacion(xhr, $formPago);
                        if (xhr.responseJSON?.message) {
                            Swal.fire({ icon: "error", title: "Error", text: xhr.responseJSON.message });
                        }
                    },
                    complete: function () { $btn.prop("disabled", false); },
                });
            });
        }

        $(document).on("click", ".btn-anular-pago", function () {
            const $btn = $(this);
            const id = $btn.data("id");
            Swal.fire({
                title: "¿Anular pago?",
                text: "El saldo de la factura se recalculará.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, anular",
                cancelButtonText: "Cancelar",
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: `/api/admin/payments/${id}/anular`,
                    method: "PATCH",
                    headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                    success: function () { window.location.reload(); },
                });
            });
        });

        $("#btnAnularFactura").on("click", function () {
            Swal.fire({
                title: "¿Anular factura?",
                text: "Esta acción no se puede deshacer.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, anular",
                cancelButtonText: "Cancelar",
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: `/api/admin/invoices/${window.factura}/anular`,
                    method: "PATCH",
                    headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                    success: function () { window.location.reload(); },
                });
            });
        });
    }

    cargarDatos();
    cargarPagos();
});