(function () {
    "use strict";

    const calendarEl = document.getElementById("calendar-general");
    if (!calendarEl || typeof FullCalendar === "undefined") return;

    const offcanvasEl = document.getElementById("offcanvasEvento");
    if (!offcanvasEl) return;

    let offcanvasInstance = null;
    let eventoActual = null;

    const COLORES_TIPO = {
        cita: "#405189",
        vacuna: "#10b981",
        cirugia: "#f59e0b",
    };

    const OPCIONES_ESTADO = {
        cita: [
            ["pendiente", "Pendiente"],
            ["confirmada", "Confirmada"],
            ["completada", "Completada"],
            ["cancelada", "Cancelada"],
        ],
        vacuna: [
            ["pendiente", "Pendiente"],
            ["parcial", "Parcial"],
            ["pagado", "Pagado"],
            ["anulado", "Anulado"],
        ],
        cirugia: [
            ["pendiente", "Pendiente"],
            ["en_proceso", "En proceso"],
            ["completada", "Completada"],
            ["cancelada", "Cancelada"],
        ],
    };

    const RUTAS_ESTADO = {
        cita: (id) => "/admin/citas/" + id + "/estado",
        vacuna: (id) => "/admin/vacunas/" + id + "/estado-pago",
        cirugia: (id) => "/admin/cirugias/" + id + "/estado",
    };

    function poblarSelectEstado(tipo, actual) {
        const select = document.getElementById("oe-estado-select");
        select.innerHTML = "";
        const opciones = OPCIONES_ESTADO[tipo] || [];
        opciones.forEach(([valor, label]) => {
            const opt = document.createElement("option");
            opt.value = valor;
            opt.textContent = label;
            select.appendChild(opt);
        });
        select.value = actual || "";
    }

    function abrirDetalle(event) {
        const p = event.extendedProps || {};

        const tipo = p.tipo || "cita";
        const badge = document.getElementById("oe-tipo-badge");
        badge.textContent = p.tipo_label || tipo;
        badge.style.background = COLORES_TIPO[tipo] || "#878a99";

        const mascota = p.mascota || {};
        document.getElementById("oe-mascota").textContent = mascota.name || "—";
        document.getElementById("oe-especie").textContent =
            mascota.species || "—";
        document.getElementById("oe-raza").textContent = mascota.breed || "—";
        document.getElementById("oe-dueno").textContent = mascota.owner || "—";
        document.getElementById("oe-telefono").textContent =
            mascota.phone || "—";
        document.getElementById("oe-veterinario").textContent =
            p.veterinarian || "—";
        document.getElementById("oe-fecha").textContent =
            event.start
                ? new Intl.DateTimeFormat("es-PE", {
                      dateStyle: "full",
                      timeStyle: "short",
                  }).format(event.start)
                : "—";
        document.getElementById("oe-detalle-label").textContent =
            tipo === "cita"
                ? "Servicio"
                : tipo === "vacuna"
                  ? "Tipo de vacuna"
                  : "Tipo de cirugía";
        document.getElementById("oe-detalle").textContent =
            p.detalle || "—";

        const proximaLabel = document.getElementById("oe-proxima-label");
        const proxima = document.getElementById("oe-proxima");
        if (tipo === "vacuna" && p.proxima_dosis && p.proxima_dosis !== "—") {
            proximaLabel.classList.remove("d-none");
            proxima.classList.remove("d-none");
            proxima.textContent = p.proxima_dosis;
        } else {
            proximaLabel.classList.add("d-none");
            proxima.classList.add("d-none");
        }

        document.getElementById("oe-notas").textContent = p.notas || "—";

        poblarSelectEstado(tipo, p.status);

        const idNumero = String(event.id).split("-").pop();
        eventoActual = { tipo: tipo, id: idNumero };

        if (!offcanvasInstance) {
            offcanvasInstance = new bootstrap.Offcanvas(offcanvasEl);
        }
        offcanvasInstance.show();
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
            ajax.get("/admin/calendario")
                .then((res) => {
                    const events = (res.data || []).map((e) => {
                        const tipo = e.extendedProps?.tipo || "cita";
                        return {
                            id: e.id,
                            title: e.title,
                            start: e.start,
                            end: e.end,
                            allDay: e.allDay,
                            className: "ev-" + tipo,
                            color:
                                e.extendedProps?.color ||
                                COLORES_TIPO[tipo] ||
                                "#878a99",
                            extendedProps: e.extendedProps,
                        };
                    });
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
                (p.tipo_label || "") +
                    " · " +
                    (p.mascota?.name || "") +
                    " · " +
                    (p.veterinarian || ""),
            );
        },
    });

    calendar.render();

    document.getElementById("oe-guardar").addEventListener("click", () => {
        if (!eventoActual) return;

        const status = document.getElementById("oe-estado-select").value;
        if (!status) return;

        const btn = document.getElementById("oe-guardar");
        btn.disabled = true;

        ajax.patch(RUTAS_ESTADO[eventoActual.tipo](eventoActual.id), {
            status: status,
        })
            .then((res) => {
                if (offcanvasInstance) offcanvasInstance.hide();
                calendar.refetchEvents();
                Swal.fire({
                    icon: "success",
                    title: "Estado actualizado",
                    text: res?.message || "El estado se actualizó correctamente.",
                    timer: 1800,
                    showConfirmButton: false,
                });
            })
            .catch(() => {})
            .finally(() => {
                btn.disabled = false;
            });
    });

    document.getElementById("oe-cancelar").addEventListener("click", () => {
        if (offcanvasInstance) offcanvasInstance.hide();
    });
})();
