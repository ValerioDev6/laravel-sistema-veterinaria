(function () {
    "use strict";

    const calendarEl = document.getElementById("calendar-citas");
    if (!calendarEl || typeof FullCalendar === "undefined") return;

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
            ajax.get("/admin/citas")
                .then((res) => {
                    const events = res.data.map((c) => {
                        const colorMap = {
                            pendiente: "#f7b84b",
                            confirmada: "#50a5f1",
                            completada: "#1cbb8c",
                            cancelada: "#f06548",
                        };
                        return {
                            id: String(c.id),
                            title:
                                (c.pet_name || "") +
                                " — " +
                                (c.veterinarian || ""),
                            start: c.appointment_date + "T" + c.appointment_time,
                            color: colorMap[c.status] || "#878a99",
                            extendedProps: {
                                status: c.status,
                                service: c.service || "—",
                                reason: c.reason || "—",
                            },
                        };
                    });
                    success(events);
                })
                .catch(() => failure());
        },
        eventClick: function (info) {
            Swal.fire({
                icon: "info",
                title: info.event.title,
                html:
                    "<strong>Servicio:</strong> " +
                    info.event.extendedProps.service +
                    "<br><strong>Estado:</strong> " +
                    info.event.extendedProps.status +
                    "<br><strong>Motivo:</strong> " +
                    info.event.extendedProps.reason,
                confirmButtonText: "Cerrar",
            });
        },
    });

    calendar.render();
})();