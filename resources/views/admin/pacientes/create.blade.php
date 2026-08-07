<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.pacientes.index') }}">Pacientes</a>
                        </li>
                        <li class="breadcrumb-item active">Nueva Mascota</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Nueva Mascota</h4>
                </div>
                <div class="card-body">
                    @include('admin.pacientes.partials.form-create', [
                        'regresarUrl' => route('admin.pacientes.index'),
                    ])
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/pacientes.js') }}"></script>
    @endpush
</x-app-layout>