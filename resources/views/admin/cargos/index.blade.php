@extends('layouts.admin')

@section('title', 'Cargos')

@section('page_title', 'Cargos')

@section('breadcrumbs')
    <li><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
    <li><span>Membros</span></li>
    <li><span>Cargos</span></li>
@endsection

@php($porto = asset('porto-admin'))
@php($hasRows = $cargos->count() > 0)

@push('head')
    <link rel="stylesheet" href="{{ $porto }}/vendor/datatables/media/css/dataTables.bootstrap5.css" />
@endpush

@section('content')
    <div class="row mb-3">
        <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <p class="text-muted mb-0">Cadastre os cargos que serão usados no formulário de membros.</p>
            <a href="{{ route('admin.cargos.create') }}" class="btn btn-primary">Novo cargo</a>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <section class="card">
                <header class="card-header">
                    <div class="card-actions">
                        <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
                        <a href="#" class="card-action card-action-dismiss" data-card-dismiss></a>
                    </div>
                    <h2 class="card-title">Lista de cargos</h2>
                </header>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0" id="datatable-cargos" width="100%">
                            <thead>
                                <tr>
                                    <th>Nome do cargo</th>
                                    <th class="text-end" style="width: 12rem;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($cargos as $cargo)
                                    <tr>
                                        <td>{{ $cargo->nome }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.cargos.edit', $cargo) }}" class="btn btn-sm btn-default">Editar</a>
                                            <form action="{{ route('admin.cargos.destroy', $cargo) }}" method="post" class="d-inline" onsubmit="return confirm('Excluir este cargo?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-4">Nenhum cargo cadastrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('scripts')
    @if ($hasRows)
        <script src="{{ $porto }}/vendor/datatables/media/js/jquery.dataTables.min.js"></script>
        <script src="{{ $porto }}/vendor/datatables/media/js/dataTables.bootstrap5.min.js"></script>
        <script src="{{ $porto }}/vendor/datatables/extras/TableTools/Buttons-1.4.2/js/dataTables.buttons.min.js"></script>
        <script src="{{ $porto }}/vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.bootstrap4.min.js"></script>
        <script src="{{ $porto }}/vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.html5.min.js"></script>
        <script src="{{ $porto }}/vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.print.min.js"></script>
        <script src="{{ $porto }}/vendor/datatables/extras/TableTools/JSZip-2.5.0/jszip.min.js"></script>
        <script src="{{ $porto }}/vendor/datatables/extras/TableTools/pdfmake-0.1.32/pdfmake.min.js"></script>
        <script src="{{ $porto }}/vendor/datatables/extras/TableTools/pdfmake-0.1.32/vfs_fonts.js"></script>
        <script>
            (function($) {
                'use strict';
                $(function() {
                    var table = $('#datatable-cargos').DataTable({
                        paging: false,
                        lengthChange: false,
                        info: false,
                        ordering: true,
                        columnDefs: [{ orderable: false, targets: 1 }],
                        language: { search: 'Buscar:', zeroRecords: 'Nenhum registro encontrado.', emptyTable: '—' },
                        dom: '<"row"<"col-lg-12"f>><"table-responsive"t>',
                        buttons: [{ extend: 'print', text: 'Imprimir' }, { extend: 'excel', text: 'Excel' }, { extend: 'pdf', text: 'PDF' }]
                    });
                    $('<div />').addClass('dt-buttons mb-2 pb-1 text-end').prependTo('#datatable-cargos_wrapper');
                    table.buttons().container().prependTo('#datatable-cargos_wrapper .dt-buttons');
                    $('#datatable-cargos_wrapper').find('.btn-secondary').removeClass('btn-secondary').addClass('btn-default');
                });
            })(jQuery);
        </script>
    @endif
@endpush
