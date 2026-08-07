<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h4 class="card-title mb-0">Listado de Especies</h4>
        @if (!empty($modal))
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalSpecies">
                <i class="ri-add-line me-1"></i>Nueva Especie
            </button>
        @else
            <a href="{{ route('admin.species.create') }}" class="btn btn-primary btn-sm">
                <i class="ri-add-line me-1"></i>Nueva Especie
            </a>
        @endif
    </div>
    <div class="card-body">
        <div class="input-group mb-3" style="max-width: 420px;">
            <span class="input-group-text"><i class="ri-search-line"></i></span>
            <input type="search" id="busquedaEspecie" class="form-control" placeholder="Buscar por nombre…">
        </div>
        <table id="table-species" class="table table-borderless dt-responsive nowrap w-100">
            <thead>
                <tr style="border-bottom: 2px solid #212529;">
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
