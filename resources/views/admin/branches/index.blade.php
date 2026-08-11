<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="javascript: void(0);">Configuración</a>
                        </li>
                        <li class="breadcrumb-item active">Sucursales</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tabListadoBranches" role="tab" aria-selected="true">
                                <i class="ri-list-unordered me-1"></i>Listado
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#tabNuevaBranch" id="tabNuevaBranch-tab" role="tab" aria-selected="false">
                                <i class="ri-store-line me-1"></i>Nueva Sucursal
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body tab-content">
                    {{-- Listado --}}
                    <div class="tab-pane active" id="tabListadoBranches" role="tabpanel">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h4 class="card-title mb-0">Listado de Sucursales</h4>
                                <button type="button" class="btn btn-primary btn-sm" id="btnNuevaBranch">
                                    <i class="ri-add-line me-1"></i>Nueva Sucursal
                                </button>
                            </div>
                            <div class="card-body">
                                <form id="formFiltrosBranches" class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label" for="busquedaBranches">Buscar</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ri-search-line"></i></span>
                                            <input type="search" class="form-control" id="busquedaBranches" name="search" placeholder="Nombre, dirección, ciudad o teléfono…">
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary me-2">
                                            <i class="ri-filter-line me-1"></i>Filtrar
                                        </button>
                                        <button type="button" class="btn btn-light" id="btnLimpiarFiltrosBranches">
                                            <i class="ri-eraser-line me-1"></i>Limpiar
                                        </button>
                                    </div>
                                </form>

                                <table id="table-branches" class="table table-borderless dt-responsive nowrap w-100">
                                    <thead>
                                        <tr style="border-bottom: 2px solid #212529;">
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Dirección</th>
                                            <th>Ciudad</th>
                                            <th>Teléfono</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Nueva Sucursal --}}
                    <div class="tab-pane fade" id="tabNuevaBranch" role="tabpanel">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h4 class="card-title mb-0">
                                    <i class="ri-store-line me-1 text-primary"></i>Nueva Sucursal
                                </h4>
                                <span class="badge bg-info-subtle text-info">Registro de sucursal</span>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-center">
                                    <div class="col-12 col-md-10 col-xl-8">
                                        <form id="formCrearBranch">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label" for="id">ID</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="ri-hashtag"></i></span>
                                                        <input type="text" class="form-control" id="id" value="Autogenerado" disabled>
                                                    </div>
                                                </div>

                                                <div class="col-md-8">
                                                    <label class="form-label" for="name">Nombre <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="ri-store-line"></i></span>
                                                        <input type="text" class="form-control" id="name" name="name" placeholder="Ej. Sede Miraflores" required>
                                                    </div>
                                                    <div class="invalid-feedback"></div>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label" for="address">Dirección <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="ri-map-pin-line"></i></span>
                                                        <input type="text" class="form-control" id="address" name="address" placeholder="Ej. Av. Larco 123" required>
                                                    </div>
                                                    <div class="invalid-feedback"></div>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label" for="city">Ciudad <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="ri-building-2-line"></i></span>
                                                        <input type="text" class="form-control" id="city" name="city" placeholder="Ej. Lima" required>
                                                    </div>
                                                    <div class="invalid-feedback"></div>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label" for="phone">Teléfono <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="ri-phone-line"></i></span>
                                                        <input type="text" class="form-control" id="phone" name="phone" placeholder="Ej. (01) 445-7890" required>
                                                    </div>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-end gap-2 pt-3 mt-2 border-top">
                                                <button type="button" class="btn btn-light waves-effect waves-light" id="btnVolverBranches">
                                                    <i class="ri-close-line me-1"></i>Cancelar
                                                </button>
                                                <button type="submit" class="btn btn-primary waves-effect waves-light" id="btnGuardarBranch">
                                                    <i class="ri-save-3-line me-1"></i>Guardar
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/branches.js') }}"></script>
    @endpush
</x-app-layout>