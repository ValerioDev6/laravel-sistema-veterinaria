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
                        <li class="breadcrumb-item active">Personal</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Listado de Personal</h4>
                    <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary btn-sm">
                        <i class="ri-add-line me-1"></i>Nuevo Usuario
                    </a>
                </div>
                <div class="card-body">
                    <table id="table-usuarios" class="table table-borderless dt-responsive nowrap w-100">
                        <thead>
                            <tr style="border-bottom: 2px solid #212529;">
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Sucursal</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/usuarios.js') }}"></script>
    @endpush
</x-app-layout>