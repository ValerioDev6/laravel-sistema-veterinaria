(function () {
    "use strict";

    // ------------------------------------------------------------------
    // Helpers de validación de formulario (inline, sin depender de js/helpers/)
    // ------------------------------------------------------------------
    window.pintarErroresValidacion = function (errors, formId) {
        const form = document.getElementById(formId);
        if (!form) return;

        limpiarErroresValidacion(formId);

        Object.entries(errors || {}).forEach(([field, mensajes]) => {
            const input = form.querySelector(`[name="${field}"]`);
            if (!input) return;

            input.classList.add("is-invalid");
            const feedback = input
                .closest(".mb-3")
                ?.querySelector(".invalid-feedback");
            if (feedback) {
                feedback.textContent = Array.isArray(mensajes)
                    ? mensajes.join(", ")
                    : String(mensajes);
            }
        });
    };

    function limpiarErroresValidacion(formId) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.querySelectorAll(".is-invalid").forEach((el) =>
            el.classList.remove("is-invalid"),
        );
        form.querySelectorAll(".invalid-feedback").forEach((el) => {
            el.textContent = "";
        });
    }

    function recolectarDatosEspecie(form) {
        const datos = new FormData();
        const name = form.elements["name"];
        if (name) datos.append("name", name.value.trim());
        return datos;
    }

    function recolectarDatosRaza(form) {
        const datos = new FormData();
        const name = form.elements["name"];
        if (name) datos.append("name", name.value.trim());
        const species_id = form.elements["species_id"];
        if (species_id) datos.append("species_id", species_id.value.trim());
        return datos;
    }

    function refrescarSelectsEspecie() {
        const selects = Array.from(
            document.querySelectorAll('select[name="species_id"]'),
        );
        if (!selects.length) return;

        const valoresPrevios = {};
        selects.forEach((sel) => {
            valoresPrevios[sel.id] = sel.value;
        });

        ajax.get("/admin/species", { per_page: 100, page: 1 })
            .then((res) => {
                const animales = res.data && res.data.length ? res.data : [];
                const opciones =
                    '<option value="">Seleccionar especie</option>' +
                    animales
                        .map(
                            (e) =>
                                '<option value="' +
                                e.id +
                                '">' +
                                e.name +
                                "</option>",
                        )
                        .join("");

                selects.forEach((sel) => {
                    sel.innerHTML = opciones;
                    sel.value = valoresPrevios[sel.id] || "";
                });
            })
            .catch(() => {});
    }

    const formEspecie = document.getElementById("formNuevaEspecie");
    if (formEspecie) {
        formEspecie.addEventListener("reset", function () {
            limpiarErroresValidacion("formNuevaEspecie");
        });
        const btn = document.getElementById("btnNuevaEspecie");
        formEspecie.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formNuevaEspecie");
            const datos = recolectarDatosEspecie(formEspecie);
            btn.disabled = true;
            ajax.post("/admin/species", datos)
                .then((res) => {
                    Swal.fire("Guardado", res.message, "success");
                    formEspecie.reset();
                    limpiarErroresValidacion("formNuevaEspecie");
                    refrescarSelectsEspecie();
                    if (typeof window.recargarEspecies === "function") {
                        window.recargarEspecies();
                    }
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(
                            error.response.errors,
                            "formNuevaEspecie",
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }

    const formRaza = document.getElementById("formNuevaRaza");
    if (formRaza) {
        formRaza.addEventListener("reset", function () {
            limpiarErroresValidacion("formNuevaRaza");
        });
        const btn = document.getElementById("btnNuevaRaza");
        formRaza.addEventListener("submit", (e) => {
            e.preventDefault();
            limpiarErroresValidacion("formNuevaRaza");
            const datos = recolectarDatosRaza(formRaza);
            btn.disabled = true;
            ajax.post("/admin/breeds", datos)
                .then((res) => {
                    Swal.fire("Guardado", res.message, "success");
                    formRaza.reset();
                    limpiarErroresValidacion("formNuevaRaza");
                    if (typeof window.recargarRazas === "function") {
                        window.recargarRazas();
                    }
                })
                .catch((error) => {
                    if (error.status === 422) {
                        pintarErroresValidacion(
                            error.response.errors,
                            "formNuevaRaza",
                        );
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }
})();
