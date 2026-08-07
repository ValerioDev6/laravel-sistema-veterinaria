(function () {
    "use strict";

    const tabContent = document.getElementById("tabPaciente");
    if (!tabContent) return;

    const petId = window.location.pathname.split("/").filter(Boolean).pop();
    const tabs = {
        records: {
            url: "/admin/pacientes/" + petId + "/records",
            empty: "Sin registros médicos.",
            columns: ["Fecha", "Tipo", "Veterinario", "Notas"],
            row: (r) => [
                r.event_date,
                ucfirst(r.event_type),
                r.veterinarian || "—",
                r.notes || "—",
            ],
        },
        vacunas: {
            url: "/admin/pacientes/" + petId + "/vacunas",
            empty: "Sin vacunas registradas.",
            columns: ["Vacuna", "Fecha", "Próxima dosis", "Veterinario"],
            row: (r) => [
                r.vaccine_type,
                r.vaccination_date,
                r.next_due_date || "—",
                r.veterinarian || "—",
            ],
        },
        cirugias: {
            url: "/admin/pacientes/" + petId + "/cirugias",
            empty: "Sin cirugías registradas.",
            columns: ["Tipo", "Fecha", "Estado", "Veterinario"],
            row: (r) => [
                r.surgery_type || "—",
                r.surgery_date,
                ucfirst(r.status),
                r.veterinarian || "—",
            ],
        },
    };

    function ucfirst(str) {
        if (!str) return "";
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function renderTable(container, config, data) {
        if (!data.length) {
            container.innerHTML =
                '<div class="text-center text-muted py-4"><i class="ri-inbox-line fs-1"></i><p class="mb-0 mt-2">' +
                config.empty +
                "</p></div>";
            return;
        }

        let html =
            '<div class="table-responsive"><table class="table table-striped mb-0"><thead><tr>';
        config.columns.forEach((c) => {
            html += "<th>" + c + "</th>";
        });
        html += "</tr></thead><tbody>";

        data.forEach((row) => {
            html += "<tr>";
            config.row(row).forEach((cell) => {
                html += "<td>" + cell + "</td>";
            });
            html += "</tr>";
        });

        html += "</tbody></table></div>";
        container.innerHTML = html;
    }

    function cargarTab(name, containerId) {
        const config = tabs[name];
        const container = document.getElementById(containerId);
        if (!config || !container) return;

        container.innerHTML =
            '<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>';

        ajax.get(config.url)
            .then((res) => {
                renderTable(container, config, res.data);
            })
            .catch(() => {
                container.innerHTML =
                    '<div class="alert alert-danger mb-0">No se pudieron cargar los datos.</div>';
            });
    }

    const tabLinks = document.querySelectorAll('#tabPaciente, .nav-tabs a[data-bs-toggle="tab"]');
    tabLinks.forEach((link) => {
        link.addEventListener("shown.bs.tab", function (e) {
            const name = e.target.dataset.tab;
            if (!name) return;
            cargarTab(name, "tab-" + name);
        });
    });

    cargarTab("records", "tab-records");
})();