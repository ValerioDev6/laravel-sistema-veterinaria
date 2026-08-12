(function () {
    const contenedor = document.getElementById("movimientos");
    if (!contenedor || typeof window.ajax === "undefined") return;

    const usuarioId = contenedor.dataset.userId;

    const iniciados = {};

    function renderEstado(status) {
        const map = {
            pendiente: "bg-warning-subtle text-warning",
            confirmada: "bg-info-subtle text-info",
            en_proceso: "bg-info-subtle text-info",
            completada: "bg-success-subtle text-success",
            cancelada: "bg-danger-subtle text-danger",
        };
        return (
            '<span class="badge ' +
            (map[status] || "bg-secondary-subtle text-secondary") +
            '">' +
            (status || "-")
                .charAt(0)
                .toUpperCase() +
            (status || "-").slice(1) +
            "</span>"
        );
    }

    function renderPago(status) {
        const map = {
            pagado: "bg-success-subtle text-success",
            parcial: "bg-warning-subtle text-warning",
            pendiente: "bg-danger-subtle text-danger",
            anulada: "bg-secondary-subtle text-secondary",
        };
        return status
            ? '<span class="badge ' +
                  (map[status] || "bg-secondary-subtle text-secondary") +
                  '">' +
                  status.charAt(0).toUpperCase() +
                  status.slice(1) +
                  "</span>"
            : "-";
    }

    function renderEditar(row) {
        return row.edit_url
            ? '<a href="' +
                  row.edit_url +
                  '" class="btn btn-soft-primary btn-sm" title="Editar"><i class="ri-pencil-line"></i></a>'
            : "-";
    }

    function crearTabla(selector, url, extraParams, columns) {
        $(selector).DataTable({
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
                Object.assign(params, extraParams);
                window.ajax.get(url, params).then((res) => {
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
            columns,
            order: [],
        });
    }

    const definiciones = {
        "tab-citas-vet": {
            table: "table-citas-vet",
            url: "/admin/citas",
            params: { veterinarian_id: usuarioId },
            columns: [
                { data: "id" },
                { data: "pet_name", render: (d, t, r) => r.pet_name || "-" },
                { data: "appointment_date" },
                { data: "appointment_time" },
                { data: "service", render: (d, t, r) => r.service || "-" },
                {
                    data: "status",
                    render: (d, t, r) => renderEstado(r.status),
                    orderable: false,
                },
                {
                    data: null,
                    render: (d, t, r) => renderEditar(r),
                    orderable: false,
                },
            ],
        },
        "tab-citas-creadas": {
            table: "table-citas-creadas",
            url: "/admin/citas",
            params: { created_by: usuarioId },
            columns: [
                { data: "id" },
                { data: "pet_name", render: (d, t, r) => r.pet_name || "-" },
                { data: "appointment_date" },
                { data: "appointment_time" },
                {
                    data: "veterinarian",
                    render: (d, t, r) => r.veterinarian || "-",
                },
                { data: "service", render: (d, t, r) => r.service || "-" },
                {
                    data: "status",
                    render: (d, t, r) => renderEstado(r.status),
                    orderable: false,
                },
            ],
        },
        "tab-vacunas": {
            table: "table-vacunas",
            url: "/admin/vacunas",
            params: { veterinarian_id: usuarioId },
            columns: [
                { data: "id" },
                { data: "pet_name", render: (d, t, r) => r.pet_name || "-" },
                {
                    data: "vaccine_type",
                    render: (d, t, r) => r.vaccine_type || "-",
                },
                { data: "vaccination_date", render: (d) => d || "-" },
                { data: "next_due_date", render: (d) => d || "-" },
                {
                    data: "payment_status",
                    render: (d, t, r) => renderPago(r.payment_status),
                    orderable: false,
                },
            ],
        },
        "tab-cirugias": {
            table: "table-cirugias",
            url: "/admin/cirugias",
            params: { veterinarian_id: usuarioId },
            columns: [
                { data: "id" },
                { data: "pet_name", render: (d, t, r) => r.pet_name || "-" },
                {
                    data: "surgery_type",
                    render: (d, t, r) => r.surgery_type || "-",
                },
                { data: "surgery_date", render: (d, t, r) => r.surgery_date || "-" },
                {
                    data: "status",
                    render: (d, t, r) => renderEstado(r.status),
                    orderable: false,
                },
            ],
        },
    };

    Object.keys(definiciones).forEach(function (tabId) {
        const link = document.querySelector(
            'a[data-bs-toggle="tab"][href="#' + tabId + '"]',
        );
        const def = definiciones[tabId];

        function inicializar() {
            if (!iniciados[tabId]) {
                iniciados[tabId] = true;
                crearTabla(
                    "#" + def.table,
                    def.url,
                    def.params,
                    def.columns,
                );
            }
        }

        if (link.classList.contains("active")) {
            inicializar();
        } else if (link) {
            link.addEventListener("shown.bs.tab", inicializar);
        }
    });
})();