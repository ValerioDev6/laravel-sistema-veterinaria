(function () {
    "use strict";

    let terminoBusqueda = "";

    function recolectarFiltros(form) {
        const data = {};
        const campos = ["pet_id", "tipo", "status"];
        campos.forEach((campo) => {
            const el = form.elements[campo];
            if (el && el.value && el.value.trim() !== "") {
                data[campo] = el.value.trim();
            }
        });
        return data;
    }

    function limpiarBuscador() {
        const input = document.getElementById("busquedaReminders");
        if (input) input.value = "";
        terminoBusqueda = "";
    }

    const tableEl = document.getElementById("table-reminders");
    if (tableEl) {
        let dataTable = null;
        let terminoBusquedaTimer = null;

        function getFilters() {
            const form = document.getElementById("formFiltrosReminders");
            if (!form) return {};
            const filtros = recolectarFiltros(form) || {};
            if (terminoBusqueda) filtros.search = terminoBusqueda;
            return filtros;
        }

        function cargarDatos() {
            if (!dataTable) {
                dataTable = $("#table-reminders").DataTable({
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
                        ajax.get("/admin/reminders", params).then((res) => {
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
                        {
                            data: "mascota",
                            render: (data, type, row) => row.mascota || "-",
                        },
                        {
                            data: "tipo",
                            render: (data, type, row) =>
                                renderTipo(row) || "-",
                        },
                        {
                            data: "mensaje",
                            render: (data, type, row) => row.mensaje || "-",
                        },
                        { data: "fecha" },
                        {
                            data: "estado",
                            render: (data, type, row) => renderEstado(row),
                            orderable: false,
                        },
                        {
                            data: null,
                            render: (data, type, row) => renderAcciones(row),
                            orderable: false,
                        },
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
                pet_photo: v.pet_photo,
                tipo: v.remindable_type,
                tipo_label: v.type_label,
                mensaje: v.message,
                fecha: v.remind_at,
                estado: v.status,
            };
        }

        function renderTipo(v) {
            const map = {
                cita: "bg-primary-subtle text-primary",
                vacuna: "bg-success-subtle text-success",
                cirugia: "bg-warning-subtle text-warning",
                surgiere: "bg-warning-subtle text-warning",
            };
            return (
                '<span class="badge ' +
                (map[v.tipo] || "bg-secondary-subtle text-secondary") +
                '">' +
                (v.tipo_label || v.tipo) +
                "</span>"
            );
        }

        function renderEstado(v) {
            const map = {
                pendiente: "bg-warning-subtle text-warning",
                enviado: "bg-success-subtle text-success",
                cancelado: "bg-danger-subtle text-danger",
            };
            const label = v.estado
                ? v.estado.charAt(0).toUpperCase() + v.estado.slice(1)
                : "—";
            return (
                '<span class="badge ' +
                (map[v.estado] || "bg-secondary-subtle text-secondary") +
                '">' +
                label +
                "</span>"
            );
        }

        function renderAcciones(v) {
            if (v.estado === "enviado" || v.estado === "cancelado") {
                return (
                    '<button type="button" class="btn btn-soft-primary btn-sm btn-cambiar-estado-reminder" data-id="' +
                    v.id +
                    '" title="Cambiar estado"><i class="ri-refresh-line"></i></button>'
                );
            }
            return (
                '<button type="button" class="btn btn-soft-success btn-sm me-1 btn-marcar-enviado" data-id="' +
                v.id +
                '" title="Marcar como enviado"><i class="ri-mail-send-line"></i></button>' +
                '<button type="button" class="btn btn-soft-danger btn-sm btn-marcar-cancelado" data-id="' +
                v.id +
                '" title="Marcar como cancelado"><i class="ri-close-circle-line"></i></button>'
            );
        }

        function bindHandlers() {
            document
                .querySelectorAll(".btn-marcar-enviado")
                .forEach((btn) => {
                    btn.addEventListener("click", function () {
                        cambiarEstado(
                            parseInt(this.dataset.id, 10),
                            "enviado",
                        );
                    });
                });
            document
                .querySelectorAll(".btn-marcar-cancelado")
                .forEach((btn) => {
                    btn.addEventListener("click", function () {
                        cambiarEstado(
                            parseInt(this.dataset.id, 10),
                            "cancelado",
                        );
                    });
                });
            document
                .querySelectorAll(".btn-cambiar-estado-reminder")
                .forEach((btn) => {
                    btn.addEventListener("click", function () {
                        cambiarEstado(
                            parseInt(this.dataset.id, 10),
                            "pendiente",
                        );
                    });
                });
        }

        function cambiarEstado(id, status) {
            const labels = {
                enviado: "enviado",
                cancelado: "cancelado",
                pendiente: "pendiente",
            };
            Swal.fire({
                icon: "question",
                title: "¿Cambiar estado?",
                text: "El recordatorio pasará a estado " + labels[status] + ".",
                showCancelButton: true,
                confirmButtonText: "Sí, cambiar",
                cancelButtonText: "Cancelar",
            }).then((result) => {
                if (!result.isConfirmed) return;

                ajax.patch("/admin/reminders/" + id + "/estado", { status })
                    .then((res) => {
                        Swal.fire("Listo", res.message, "success");
                        cargarDatos();
                    })
                    .catch(() => {});
            });
        }

        $("#busquedaReminders").on("input", function () {
            const termino = this.value;
            clearTimeout(terminoBusquedaTimer);
            terminoBusquedaTimer = setTimeout(function () {
                terminoBusqueda = termino.trim();
                dataTable.ajax.reload();
            }, 350);
        });

        $("#busquedaReminders").on("keydown", function (e) {
            if (e.key === "Enter") e.preventDefault();
        });

        const formFiltros = document.getElementById("formFiltrosReminders");
        if (formFiltros) {
            formFiltros.addEventListener("submit", (e) => {
                e.preventDefault();
                cargarDatos();
            });
            document
                .getElementById("btnLimpiarFiltros")
                ?.addEventListener("click", () => {
                    formFiltros.reset();
                    limpiarBuscador();
                    cargarDatos();
                });
        }

        cargarDatos();
    }
})();