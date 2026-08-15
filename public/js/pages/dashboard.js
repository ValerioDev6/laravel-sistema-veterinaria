(function () {
    var COLORS = {
        primary: "#405189",
        success: "#0ab39c",
        warning: "#f7b84b",
        danger: "#f06548",
        info: "#299cdb",
        purple: "#6559cc",
    };

    var charts = {};

    var kpis = {
        facturado: "#kpi-facturado",
        cobrado: "#kpi-cobrado",
        por_cobrar: "#kpi-por-cobrar",
        citas_hoy: "#kpi-citas-hoy",
        pacientes: "#kpi-pacientes",
        vacunas: "#kpi-vacunas",
        cirugias: "#kpi-cirugias",
        recordatorios_pendientes: "#kpi-recordatorios",
    };

    var moneda = ["facturado", "cobrado", "por_cobrar"];

    function formatoMoneda(valor) {
        return (
            "S/ " +
            Number(valor || 0).toLocaleString("es-PE", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            })
        );
    }

    function formatoEntero(valor) {
        return Number(valor || 0).toLocaleString("es-PE");
    }

    function pintarKpis(data, year) {
        Object.keys(kpis).forEach(function (key) {
            var el = document.querySelector(kpis[key]);
            if (!el) return;

            var valor = data[key];
            el.textContent = moneda.indexOf(key) !== -1 ? formatoMoneda(valor) : formatoEntero(valor);
        });

        document.querySelectorAll(".kpi-anio").forEach(function (el) {
            el.textContent = year;
        });
    }

    function destruir(id) {
        if (charts[id]) {
            charts[id].destroy();
            delete charts[id];
        }
    }

    function crearChart(id, config) {
        var canvas = document.getElementById(id);
        if (!canvas) return;

        destruir(id);
        charts[id] = new Chart(canvas, config);
    }

    function chartIngresos(data) {
        crearChart("chartIngresos", {
            type: "bar",
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: "Emitido",
                        data: data.emitido,
                        backgroundColor: COLORS.primary,
                        borderRadius: 4,
                        maxBarThickness: 32,
                    },
                    {
                        label: "Cobrado",
                        data: data.cobrado,
                        backgroundColor: COLORS.success,
                        borderRadius: 4,
                        maxBarThickness: 32,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        ticks: {
                            callback: function (value) {
                                return "S/ " + value;
                            },
                        },
                    },
                },
                plugins: {
                    legend: {
                        position: "top",
                    },
                },
            },
        });
    }

    function chartCitasEstado(data) {
        crearChart("chartCitasEstado", {
            type: "doughnut",
            data: {
                labels: data.labels,
                datasets: [
                    {
                        data: data.data,
                        backgroundColor: [COLORS.warning, COLORS.info, COLORS.success, COLORS.danger],
                        borderWidth: 0,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: "65%",
                plugins: {
                    legend: {
                        position: "bottom",
                    },
                },
            },
        });
    }

    function chartEspecies(data) {
        crearChart("chartEspecies", {
            type: "bar",
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: "Pacientes",
                        data: data.data,
                        backgroundColor: COLORS.info,
                        borderRadius: 4,
                        maxBarThickness: 40,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                },
            },
        });
    }

    function chartTopVets(data) {
        crearChart("chartTopVets", {
            type: "bar",
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: "Citas",
                        data: data.data,
                        backgroundColor: COLORS.purple,
                        borderRadius: 4,
                        maxBarThickness: 24,
                    },
                ],
            },
            options: {
                indexAxis: "y",
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                },
            },
        });
    }

    function pintarCharts(data) {
        chartIngresos(data.ingresos_mensuales);
        chartCitasEstado(data.citas_por_estado);
        chartEspecies(data.pacientes_por_especie);
        chartTopVets(data.top_veterinarios);
    }

    function cargarDashboard(year) {
        ajax.get("/admin/dashboard?year=" + encodeURIComponent(year))
            .then(function (res) {
                pintarKpis(res.data.kpis, year);
                pintarCharts(res.data.charts);
            })
            .catch(function () {});
    }

    document.addEventListener("DOMContentLoaded", function () {
        var select = document.querySelector("#selectAnio");
        if (!select) return;

        cargarDashboard(select.value);

        select.addEventListener("change", function () {
            cargarDashboard(this.value);
        });
    });
})();
