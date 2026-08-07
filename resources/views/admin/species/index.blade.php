<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="javascript: void(0);">Catálogo</a>
                        </li>
                        <li class="breadcrumb-item active">Especies</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @include('admin.species.partials.table')
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/pages/species.js') }}"></script>
    @endpush
</x-app-layout>