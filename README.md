# Sistema Veterinario - Documentación de Setup

## 1. Generación de modelos con Reliese (scaffolding desde la base de datos)

Reliese lee las tablas ya existentes en MySQL y genera los modelos Eloquent automáticamente.

```bash
composer require reliese/laravel --dev
php artisan vendor:publish --tag=reliese-models
php artisan config:clear
```

Generar todos los modelos de la base de datos:

```bash
php artisan code:models
```

Generar solo un modelo puntual (ej. `users`):

```bash
php artisan code:models --table=users
```

Generar modelos de un schema específico:

```bash
php artisan code:models --schema=shop
```

> **Nota:** Reliese genera el modelo con `extends Model`. Para el modelo `User`
> específicamente hay que cambiarlo a `extends Authenticatable` (ver sección 3),
> porque Reliese no sabe que esa tabla se usa para login.

---

## 1.5 excel y pdf

composer require phpoffice/phpspreadsheet

composer require maatwebsite/excel:4.x-dev

php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config

pára isntalarlo modica la versiond ephp a una comptibl 8.4

composer require barryvdh/laravel-dompdf

## 2. Roles y permisos con Spatie

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan config:clear
php artisan migrate
```

Esto publica y corre las migraciones de: `permissions`, `roles`,
`model_has_permissions`, `model_has_roles`, `role_has_permissions`.
No se escribe ninguna de estas a mano.

---

## 3. Modelo `User` final

El modelo generado por Reliese necesita 3 ajustes para funcionar como usuario
autenticable con roles:

```php
<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;   // necesario para User::factory()
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;  // en vez de Model
use Laravel\Sanctum\HasApiTokens;                         // si se consume via API
use Spatie\Permission\Traits\HasRoles;                    // roles y permisos

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, HasRoles;


}
```

**Por qué cada cambio:**

| Cambio                    | Motivo                                                               |
| ------------------------- | -------------------------------------------------------------------- |
| `extends Authenticatable` | Sin esto no hay `Auth::attempt()`, login, ni sesión real.            |
| `HasFactory`              | Necesario para `User::factory()` en seeders/tests.                   |
| `HasApiTokens` (Sanctum)  | Necesario si la API se consume vía AJAX/fetch desde el propio Blade. |
| `HasRoles` (Spatie)       | Habilita `assignRole()`, `hasPermissionTo()`, etc.                   |

---

## 4. `UserFactory` corregida

La tabla usa `username`, no `name` (el default de Laravel). Hay que sobrescribir
`database/factories/UserFactory.php` completo:

## 5. Ejecutar el seeder de roles y permisos

`PermissionsDemoSeeder` crea todos los permisos, los 3 roles
(`Super-Admin`, `Veterinario`, `Recepcionista`) y un usuario admin de prueba.
Usa `firstOrCreate` en vez de `create`, por lo que se puede correr varias
veces sin romperse por duplicados.

```bash
php artisan db:seed --class=PermissionsDemoSeeder
```

Usuario de prueba generado:

| Campo    | Valor             |
| -------- | ----------------- |
| username | `admin`           |
| email    | `admin@gmail.com` |
| password | `12345678`        |
| rol      | `Super-Admin`     |

`guard_name` usado: **`api`** (porque el propio Blade consume la API vía
AJAX/fetch en vez de sesión tradicional — requiere Sanctum configurado).
w

---

## Errores comunes durante el setup (referencia rápida)

| Error                                                               | Causa                                                         | Solución                                                                                  |
| ------------------------------------------------------------------- | ------------------------------------------------------------- | ----------------------------------------------------------------------------------------- |
| `Table 'users' already exists`                                      | Se importó el `.sql` manual y luego se corrió `migrate`       | Usar `Schema::hasTable('users')` para saltar la creación si ya existe                     |
| `Cannot redeclare ... up()`                                         | Quedaron dos métodos `up()` en el mismo archivo tras editar   | Revisar que el archivo tenga un solo `up()` y un solo `down()`                            |
| `Call to undefined method User::factory()`                          | Falta el trait `HasFactory` en el modelo                      | Agregar `use HasFactory;`                                                                 |
| `Unknown column 'name' in INSERT`                                   | `UserFactory` no actualizada, sigue generando `name`          | Sobrescribir la factory completa (sección 4)                                              |
| `A permission already exists for guard`                             | El seeder se corrió parcialmente antes de fallar              | Usar `firstOrCreate` en vez de `create`, o truncar las tablas de permisos                 |
| `Foreign key constraint is incorrectly formed` (en `migrate:fresh`) | Faltan migraciones reales para tablas creadas solo con `.sql` | Crear la migración de la tabla referenciada (ej. `branches`) con fecha anterior a `users` |
