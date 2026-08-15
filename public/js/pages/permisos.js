(function () {
    "use strict";

    const tableEl = document.getElementById("table-permisos");
    if (!tableEl) return;

    let dataTable = null;
    let terminoBusqueda = "";
    let terminoBusquedaTimer = null;

    function cargarDatos() {
        const url = "/admin/permisos";

        if (!dataTable) {
            dataTable = $("#table-permisos").DataTable({
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
                    const grupo = document.getElementById("grupoPermisos");
                    if (grupo && grupo.value) {
                        params.grupo = grupo.value;
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
                            data: res.data.map((p) => ({
                                permiso: p.name,
                                descripcion: p.label,
                                modulo: p.grupo,
                                guard: p.guard_name,
                                roles: p.roles_count,
                            })),
                        });
                    });
                },
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json",
                },
                columns: [
                    { data: "permiso" },
                    { data: "descripcion", orderable: false },
                    { data: "modulo", orderable: false },
                    { data: "guard" },
                    { data: "roles", orderable: false },
                ],
                order: [[0, "asc"]],
            });
        } else {
            dataTable.ajax.reload();
        }
        return dataTable;
    }

    $("#busquedaPermisos").on("input", function () {
        const termino = this.value;
        clearTimeout(terminoBusquedaTimer);
        terminoBusquedaTimer = setTimeout(function () {
            terminoBusqueda = termino.trim();
            dataTable.ajax.reload();
        }, 350);
    });

    $("#busquedaPermisos").on("keydown", function (e) {
        if (e.key === "Enter") e.preventDefault();
    });

    const formFiltros = document.getElementById("formFiltrosPermisos");
    if (formFiltros) {
        formFiltros.addEventListener("submit", (e) => {
            e.preventDefault();
            dataTable.ajax.reload();
        });
        document
            .getElementById("btnLimpiarFiltrosPermisos")
            ?.addEventListener("click", () => {
                formFiltros.reset();
                const input = document.getElementById("busquedaPermisos");
                if (input) input.value = "";
                terminoBusqueda = "";
                dataTable.ajax.reload();
            });
    }

    cargarDatos();
})();
