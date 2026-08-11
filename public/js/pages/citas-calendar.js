(function () {
    "use strict";

    const calendarEl = document.getElementById("calendar-citas");
    if (!calendarEl || typeof FullCalendar === "undefined") return;

    const offcanvasEl = document.getElementById("offcanvasCita");
    if (!offcanvasEl) return;

    let offcanvasInstance = null;
    let citaActual = null;

    function formatearCosto(costo) {
        const n = parseFloat(costo) || 0;
        return "S/ " + n.toFixed(2);
    }

    function abrirDetalle(event) {
        const p = event.extendedProps || {};
        document.getElementById("oc-veterinario").textContent =
            p.veterinarian || "—";
        document.getElementById("oc-pet").textContent =
            (p.pet && (p.pet.name || "—")) || "—";
        document.getElementById("oc-hora").textContent =
            p.hora_atencion || "—";
        document.getElementById("oc-dia").textContent = p.day || "—";
        document.getElementById("oc-costo").textContent =
            formatearCosto(p.cost);
        document.getElementById("oc-servicio").textContent =
            p.service || "—";
        document.getElementById("oc-razon").textContent = p.reason || "—";
        document.getElementById("oc-notas").textContent = p.notes || "—";

        const selectEstado = document.getElementById("oc-estado");
        selectEstado.value = event.extendedProps.status || "pendiente";

        const linkEditar = document.getElementById("oc-editar");
        linkEditar.setAttribute("href", p.edit_url || "#");

        citaActual = {
            id: event.id,
            status: event.extendedProps.status || "pendiente",
        };

        if (!offcanvasInstance) {
            offcanvasInstance = new bootstrap.Offcanvas(offcanvasEl);
        }
        offcanvasInstance.show();
    }

    function pintarLeyenda(events) {
        const contenedor = document.getElementById("leyendaVets");
        if (!contenedor) return;
        contenedor
            .querySelectorAll(".chip-vet")
            .forEach((chip) => chip.remove());
        const vets = {};
        (events || []).forEach((c) => {
            const p = c.extendedProps || {};
            if (!p.veterinarian || vets[p.veterinarian]) return;
            vets[p.veterinarian] = p.vet_color || "#878a99";
        });
        Object.keys(vets).forEach((nombre) => {
            const chip = document.createElement("span");
            chip.className = "chip-vet d-inline-flex align-items-center gap-1 small";
            chip.innerHTML =
                '<span class="d-inline-block" style="width:12px;height:12px;border-radius:3px;background:' +
                vets[nombre] +
                '"></span> ' +
                nombre;
            contenedor.appendChild(chip);
        });
    }

    const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: "es",
        initialView: "dayGridMonth",
        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,timeGridWeek,listWeek",
        },
        buttonText: {
            today: "Hoy",
            month: "Mes",
            week: "Semana",
            list: "Lista",
        },
        height: "auto",
        events: function (info, success, failure) {
            ajax.get("/admin/citas/calendario")
                .then((res) => {
                    const events = (res.data || []).map((c) => ({
                        id: c.id,
                        title: c.title,
                        start: c.start,
                        end: c.end,
                        allDay: false,
                        color:
                            c.extendedProps?.vet_color ||
                            c.color ||
                            "#878a99",
                        extendedProps: c.extendedProps,
                    }));
                    pintarLeyenda(events);
                    success(events);
                })
                .catch(() => failure());
        },
        eventClick: function (info) {
            abrirDetalle(info.event);
        },
        eventMouseEnter: function (info) {
            const p = info.event.extendedProps || {};
            info.el.setAttribute(
                "title",
                (p.pet?.name || "") +
                    " · " +
                    (p.hora_atencion || "") +
                    " · " +
                    (p.veterinarian || ""),
            );
        },
    });

    calendar.render();

    document.getElementById("oc-guardar").addEventListener("click", () => {
        if (!citaActual) return;
        const nuevoEstado = document.getElementById("oc-estado").value;
        const btn = document.getElementById("oc-guardar");
        btn.disabled = true;

        ajax.patch("/admin/citas/" + citaActual.id + "/estado", {
            status: nuevoEstado,
        })
            .then((res) => {
                if (offcanvasInstance) offcanvasInstance.hide();
                calendar.refetchEvents();
                Swal.fire({
                    icon: "success",
                    title: "Estado actualizado",
                    text: res?.message || "El estado de la cita se actualizó correctamente.",
                    timer: 1800,
                    showConfirmButton: false,
                });
            })
            .catch(() => {})
            .finally(() => {
                btn.disabled = false;
            });
    });
})();
