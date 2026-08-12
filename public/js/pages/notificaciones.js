(function () {
    const contenedor = document.getElementById("lista-notificaciones");
    const badge = document.getElementById("badge-notificaciones");
    const boton = document.getElementById("page-header-notifications-dropdown");

    if (!contenedor || typeof window.ajax === "undefined") {
        return;
    }

    function vacio(mensaje) {
        contenedor.innerHTML = "";
        const div = document.createElement("div");
        div.className = "text-center py-4 text-muted";
        div.textContent = mensaje;
        contenedor.appendChild(div);
    }

    function crearItem(item) {
        const wrapper = document.createElement("div");

        const header = document.createElement("a");
        header.href = "/admin/reminders";
        header.className =
            "text-reset notification-item d-block dropdown-item";
        header.style.cssText =
            "padding-top:.45rem;padding-bottom:.1rem;white-space:normal;";

        const fila = document.createElement("div");
        fila.className = "d-flex align-items-start";

        const avatar = document.createElement("div");
        avatar.className = "flex-shrink-0 me-2";
        const avatarSpan = document.createElement("span");
        avatarSpan.className =
            "avatar-title rounded-circle bg-soft-primary text-primary fs-16";
        avatarSpan.textContent = "🐾";
        avatar.appendChild(avatarSpan);

        const cuerpo = document.createElement("div");
        cuerpo.className = "flex-grow-1";
        const titulo = document.createElement("h6");
        titulo.className = "mt-0 mb-1 fs-13";
        titulo.textContent =
            (item.pet_name || "Mascota") +
            " · " +
            (item.type_label || "Recordatorio");
        const mensaje = document.createElement("div");
        mensaje.className = "fs-12 text-muted mb-1";
        mensaje.textContent = item.message || "";
        const fecha = document.createElement("div");
        fecha.className = "fs-11 text-muted mb-1";
        fecha.textContent = "🔔 " + (item.remind_at || "");
        cuerpo.append(titulo, mensaje, fecha);

        fila.append(avatar, cuerpo);
        header.appendChild(fila);
        wrapper.appendChild(header);

        const acciones = document.createElement("div");
        acciones.className =
            "d-flex gap-1 px-3 pb-2 border-bottom mb-1";
        const btnEnviado = document.createElement("button");
        btnEnviado.type = "button";
        btnEnviado.className = "btn btn-sm btn-success w-50";
        btnEnviado.textContent = "Enviado";
        btnEnviado.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            cambiarEstado(item.id, "enviado");
        });
        const btnCancelado = document.createElement("button");
        btnCancelado.type = "button";
        btnCancelado.className = "btn btn-sm btn-soft-danger w-50";
        btnCancelado.textContent = "Cancelar";
        btnCancelado.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            cambiarEstado(item.id, "cancelado");
        });
        acciones.append(btnEnviado, btnCancelado);
        wrapper.appendChild(acciones);

        return wrapper;
    }

    function pintar(data) {
        if (!data || !Array.isArray(data.data) || data.data.length === 0) {
            vacio("Sin recordatorios pendientes.");
            return;
        }
        contenedor.innerHTML = "";
        const visibles = data.data.slice(0, 5);
        visibles.forEach((item) =>
            contenedor.appendChild(crearItem(item)),
        );
        if (data.data.length > 5) {
            const verTodos = document.createElement("a");
            verTodos.href = "/admin/reminders";
            verTodos.className =
                "d-block text-center small fw-semibold text-primary py-2";
            verTodos.textContent = "Ver todos los recordatorios";
            contenedor.appendChild(verTodos);
        }
    }

    function cargar() {
        window.ajax
            .get("/admin/notificaciones")
            .then((res) => {
                if (badge) badge.textContent = res?.count ?? 0;
                pintar(res);
            })
            .catch(() => {
                if (badge) badge.textContent = 0;
                vacio("No se pudieron cargar las notificaciones.");
            });
    }

    function cambiarEstado(id, estado) {
        window.ajax
            .patch("/admin/reminders/" + id + "/estado", { status: estado })
            .then(cargar)
            .catch(() => {});
    }

    if (boton) {
        boton.addEventListener("shown.bs.dropdown", cargar);
    }
    cargar();
})();