## Fix #7 — Citas duplicadas en el mismo horario (diagnóstico + fix real)

**Tipo:** Fix de bug — requiere diagnóstico antes de corregir, no asumas la causa

**Prompt:**

Antes de tocar nada, lee en este orden:

1. `proyecto-veterinaria.md` — fuente de verdad funcional del sistema
2. `plan-implementacion.md` — verifica en qué fase/módulo estamos
3. `project-map.md` — estado real de Citas (Fase 5)

**Síntoma reportado:** al registrar una cita para un veterinario en una fecha/hora donde ya existe otra cita activa del mismo veterinario, el sistema "deja pasar" el registro — no bloquea como debería.

**No asumas la causa. Diagnostica primero, en este orden exacto:**

### Paso 1 — Confirmar si realmente se duplica en la base de datos

Reproduce el caso (registrar 2 citas del mismo veterinario, misma fecha, misma hora) y revisa directo en la tabla `citas` si quedaron **2 registros insertados**, o si solo hay 1 (lo cual significaría que la validación sí funcionó y el problema es que el frontend no mostró el error).

Agrega temporalmente un log en `App\Actions\Citas\ValidarDisponibilidadCita::execute()`, justo antes de la consulta de conflicto:

```php
\Log::info('Validando disponibilidad', [
    'date' => $date,
    'time' => $time,
    'vetId' => $vetId,
]);
```

Revisa `storage/logs/laravel.log` y confirma el formato exacto de `$time` en el momento del conflicto real (compáralo contra el formato `TIME` que espera el `whereRaw('TIME(appointment_time) = ?', [$time])`).

### Paso 2A — Si SÍ se duplica en la base de datos (bug de backend)

La causa más probable es un mismatch de formato en `$time` entre lo que manda el formulario y lo que espera la comparación SQL. Revisa la cadena completa:

- `resources/views/admin/citas/create.blade.php` / `edit.blade.php` — qué tipo de input es `appointment_time` (`type="time"` da `"HH:mm"`, sin segundos)
- `resources/js/pages/citas.js` — si el JS transforma el valor antes de mandarlo (por ejemplo a un objeto `Date`/ISO completo) antes del `axios.post`
- `StoreCitaRequest`/`UpdateCitaRequest` — si hay alguna regla o `prepareForValidation()` que reformatee `appointment_time`

Normaliza el formato en **un solo punto** (recomendado: al inicio de `ValidarDisponibilidadCita::execute()`, con `\Carbon\Carbon::parse($time)->format('H:i:s')`) para que la comparación sea consistente sin importar qué mande el frontend. Aplica el mismo `Carbon::parse(...)->format('H:i:s')` también en `CreateCitaAction`/`UpdateCitaAction` antes de guardar, para que lo que se compara y lo que se guarda usen siempre el mismo formato.

### Paso 2B — Si NO se duplica (bug de UI, la validación sí funcionó)

El backend está devolviendo `422` con los `errors` del `ValidationException` correctamente, pero el JS no lo está mostrando. Revisa `resources/js/pages/citas.js`:

- Confirma que el handler de error del `axios.post`/`.catch()` lee `error.response.data.errors` y arma un SweetAlert2 con esos mensajes (no un mensaje genérico silencioso)
- Confirma que la capa AJAX propia del proyecto (`public/js/config/ajax.js`) no está interceptando y tragándose el 422 antes de que llegue al `.catch()` del código específico de citas
- Si el interceptor global maneja ciertos códigos de status de forma genérica, confirma que 422 no está siendo tratado como "éxito silencioso" ni ignorado

Corrige para que **cualquier 422 con `errors`** se muestre siempre en SweetAlert2 con el mensaje real del backend, de forma consistente en todos los módulos (no solo Citas) — si encuentras que este bug de UI existe en Citas, es probable que exista en todos los formularios del admin por igual, ya que probablemente comparten la misma capa AJAX. Verifica al menos 2-3 módulos más antes de dar el fix por cerrado.

---

**Alcance:** diagnóstico primero, luego fix puntual solo donde esté la causa real confirmada. No toques ambas rutas (2A y 2B) si solo una es la causa real — corrige la que corresponda según lo que confirmes en el Paso 1, y solo aplica la otra si el diagnóstico muestra que ambos problemas coexisten.

**Al terminar:**

Crea `/fixes/2026-08-06-citas-duplicadas.md` documentando:

- Resultado del diagnóstico (¿se duplicó en BD o fue solo UI?)
- Causa raíz exacta confirmada (con evidencia del log, no suposición)
- Qué se corrigió
- Si el fix de manejo de 422 se extendió a otros módulos, cuáles
- Fecha del fix

Actualiza `project-map.md` con la decisión técnica sobre normalización de formato de hora (si aplicó) y/o el manejo estándar de errores 422 en la capa AJAX.

Registra este mismo prompt en `prompts.md` como el siguiente número de entrada correlativo, con la fecha de hoy.

Quita el log temporal de diagnóstico antes de cerrar el fix. Detente al terminar y espera confirmación antes de continuar con cualquier otra cosa.
