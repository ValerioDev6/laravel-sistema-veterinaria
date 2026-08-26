<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.veterinarios.index') }}">Veterinarios</a>
                        </li>
                        <li class="breadcrumb-item active">Editar Veterinario</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Editar Veterinario</h4>
                </div>
                <div class="card-body">
                    <form id="formEditarVeterinario" data-id="{{ $user->id }}" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="username">Nombre de usuario</label>
                                <input type="text" class="form-control" id="username" name="username" value="{{ $user->username }}">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="password">Nueva contraseña <span class="text-muted">(déjala vacía para no cambiarla)</span></label>
                                <input type="password" class="form-control" id="password" name="password">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="password_confirmation">Confirmar contraseña</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="branch_id">Sucursal</label>
                                <select class="form-select" id="branch_id" name="branch_id">
                                    <option value="">Sin sede asignada</option>
                                    @foreach ($branches as $value => $label)
                                        <option value="{{ $value }}" @selected($user->branch_id == $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="phone">Teléfono</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ $user->phone }}">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="type_documento">Tipo documento</label>
                                <select class="form-select" id="type_documento" name="type_documento">
                                    <option value="">Seleccionar</option>
                                    <option value="DNI" @selected($user->type_documento === 'DNI')>DNI</option>
                                    <option value="CE" @selected($user->type_documento === 'CE')>CE</option>
                                    <option value="Pasaporte" @selected($user->type_documento === 'Pasaporte')>Pasaporte</option>
                                    <option value="RUC" @selected($user->type_documento === 'RUC')>RUC</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="n_documento">N° documento</label>
                                <input type="text" class="form-control" id="n_documento" name="n_documento" value="{{ $user->n_documento }}">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="birthday">Fecha de nacimiento</label>
                                <input type="date" class="form-control" id="birthday" name="birthday" value="{{ $user->birthday?->format('Y-m-d') }}">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label" for="avatar">Foto (dejar vacío para mantener la actual)</label>
                                <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="btnActualizarVeterinario">
                                <i class="ri-save-line me-1"></i>Actualizar
                            </button>
                            <a href="{{ route('admin.veterinarios.index') }}" class="btn btn-light">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card" id="cardHorarioVeterinario">
                <div class="card-header">
                    <h4 class="card-title mb-0">Horario de atención</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted fs-13 mb-3">
                        Marca las casillas de las horas en que atiende cada día (de lunes a sábado, de 07:00 a 18:30).
                        Las horas consecutivas se agrupan automáticamente en una sola franja.
                    </p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-centered align-middle mb-2 w-100" id="tablaHorarioVeterinario">
                            <thead>
                                <tr>
                                    <th class="text-muted fs-12">Hora</th>
                                    @foreach ([[1, 'Lunes'], [2, 'Martes'], [3, 'Miércoles'], [4, 'Jueves'], [5, 'Viernes'], [6, 'Sábado']] as [$dia, $etiqueta])
                                        <th class="text-center">
                                            <label class="form-check m-0">
                                                <input type="checkbox" class="form-check-input chk-dia-completo" data-day="{{ $dia }}">
                                                <span>{{ $etiqueta }}</span>
                                            </label>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody id="tbodyHorarioVeterinario"></tbody>
                        </table>
                    </div>
                    <small class="text-muted" id="resumenHorarioVeterinario">Sin horas seleccionadas.</small>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            window.veterinariosHorariosInit = @json($horarios);
        </script>
        <script src="{{ asset('js/pages/veterinarios.js') }}"></script>
    @endpush
</x-app-layout>
