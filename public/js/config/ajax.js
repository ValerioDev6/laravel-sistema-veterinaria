(function () {
    const baseURL = "/api";
    const csrfToken = document.querySelector(
        "meta[name='csrf-token']",
    )?.content;

    function request(method, url, data = null) {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.open(method, baseURL + url, true);
            xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
            xhr.setRequestHeader("Accept", "application/json");
            xhr.setRequestHeader("X-CSRF-TOKEN", csrfToken ?? "");

            const isFormData = data instanceof FormData;
            if (!isFormData) {
                xhr.setRequestHeader("Content-Type", "application/json");
            }

            xhr.onload = function () {
                let response;
                try {
                    response = JSON.parse(xhr.responseText);
                } catch (e) {
                    response = null;
                }

                if (xhr.status >= 200 && xhr.status < 300) {
                    resolve(response);
                    return;
                }

                const error = { status: xhr.status, response };

                // Errores de validación (422): la respuesta siempre trae el
                // mensaje real del backend en `message` y los detalles en
                // `errors`. Se muestra SIEMPRE con SweetAlert2 (por si el
                // campo afectado es invisible/oculto, ej. appointment_time),
                // y ademas se rechaza la promesa para que la pagina pueda
                // pintar el detalle inline (is-invalid) si lo desea.
                if (xhr.status === 422) {
                    const esEliminacion = method.toUpperCase() === "DELETE";
                    const primerMensaje =
                        response?.message ||
                        Object.values(response?.errors || {}).flat()[0] ||
                        (esEliminacion
                            ? "No se pudo eliminar el registro."
                            : "Revisa los campos del formulario.");
                    if (typeof Swal !== "undefined") {
                        Swal.fire({
                            icon: "warning",
                            title: esEliminacion
                                ? "No se pudo eliminar"
                                : "No se pudo guardar",
                            text: primerMensaje,
                        });
                    }
                    reject(error);
                    return;
                }

                if (typeof Swal !== "undefined") {
                    Swal.fire({
                        icon: "error",
                        title: "Ocurrió un error",
                        text:
                            response?.message ??
                            "Intenta nuevamente en unos segundos.",
                    });
                }

                reject(error);
            };

            xhr.onerror = function () {
                const error = { status: 0, response: null };
                if (typeof Swal !== "undefined") {
                    Swal.fire({
                        icon: "error",
                        title: "Sin conexión",
                        text: "No se pudo contactar al servidor. Verifica tu conexión.",
                    });
                }
                reject(error);
            };

            xhr.send(isFormData ? data : data ? JSON.stringify(data) : null);
        });
    }

    window.ajax = {
        get: (url, params) =>
            request(
                "GET",
                params
                    ? url + "?" + new URLSearchParams(params).toString()
                    : url,
            ),
        post: (url, data) => request("POST", url, data),
        put: (url, data) => request("PUT", url, data),
        patch: (url, data) => request("PATCH", url, data),
        delete: (url) => request("DELETE", url),
    };
})();
