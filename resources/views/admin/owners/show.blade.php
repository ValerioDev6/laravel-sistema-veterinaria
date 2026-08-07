<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.owners.index') }}">Propietarios</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $owner->first_name }} {{ $owner->last_name }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Datos del Propietario</h4>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted">Nombres</dt>
                        <dd class="col-sm-8">{{ $owner->first_name }}</dd>

                        <dt class="col-sm-4 text-muted">Apellidos</dt>
                        <dd class="col-sm-8">{{ $owner->last_name }}</dd>

                        <dt class="col-sm-4 text-muted">Documento</dt>
                        <dd class="col-sm-8">
                            @if ($owner->type_documento)
                                {{ $owner->type_documento }} — {{ $owner->n_documento }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4 text-muted">Teléfono</dt>
                        <dd class="col-sm-8">{{ $owner->phone }}</dd>

                        <dt class="col-sm-4 text-muted">Email</dt>
                        <dd class="col-sm-8">{{ $owner->email ?? '—' }}</dd>

                        <dt class="col-sm-4 text-muted">Ciudad</dt>
                        <dd class="col-sm-8">{{ $owner->city ?? '—' }}</dd>

                        <dt class="col-sm-4 text-muted">Dirección</dt>
                        <dd class="col-sm-8">{{ $owner->address ?? '—' }}</dd>
                    </dl>

                    <div class="d-flex gap-2 mt-3">
                        <a href="{{ route('admin.owners.edit', $owner->id) }}" class="btn btn-soft-primary btn-sm">
                            <i class="ri-pencil-line me-1"></i>Editar
                        </a>
                        <a href="{{ route('admin.pacientes.create') }}" class="btn btn-primary btn-sm">
                            <i class="ri-add-line me-1"></i>Nueva Mascota
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Mascotas ({{ $owner->pacientes->count() }})</h4>
                </div>
                <div class="card-body">
                    @if ($owner->pacientes->isEmpty())
                        <div class="text-center text-muted py-4">
                            <i class="ri-paw-line fs-1"></i>
                            <p class="mb-0 mt-2">Este propietario aún no tiene mascotas registradas.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Especie</th>
                                        <th>Raza</th>
                                        <th>Nacimiento</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($owner->pacientes as $paciente)
                                        <tr>
                                            <td>{{ $paciente->name }}</td>
                                            <td>{{ $paciente->species->name }}</td>
                                            <td>{{ $paciente->breed?->name ?? '—' }}</td>
                                            <td>{{ $paciente->birth_date?->format('d/m/Y') ?? '—' }}</td>
                                            <td>
                                                <a href="{{ route('admin.pacientes.show', $paciente->id) }}" class="btn btn-soft-primary btn-sm" title="Ficha">
                                                    <i class="ri-file-user-line"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>