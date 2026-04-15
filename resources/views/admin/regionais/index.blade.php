@extends('layouts.admin')

@section('title', 'Regionais')

@section('page_title', 'Regionais')

@section('breadcrumbs')
    <li><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
    <li><span>Regionais</span></li>
@endsection

@php($porto = asset('porto-admin'))
@php($hasRows = $regionais->count() > 0)

@push('head')
    <link rel="stylesheet" href="{{ $porto }}/vendor/datatables/media/css/dataTables.bootstrap5.css" />
@endpush

@section('content')
    <div class="row mb-3">
        <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <p class="text-muted mb-0">Cadastre o <strong>nome da regional</strong> e o <strong>pastor responsável</strong> antes de vincular igrejas.</p>
            <a href="{{ route('admin.regionais.create') }}" class="btn btn-primary">Nova regional</a>
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
                    <h2 class="card-title">Lista de regionais</h2>
                </header>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0" id="datatable-regionais" width="100%">
                            <thead>
                                <tr>
                                    <th>Nome da regional</th>
                                    <th>Pastor responsável</th>
                                    <th class="text-end" style="width: 12rem;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($regionais as $r)
                                    <tr>
                                        <td>{{ $r->nome }}</td>
                                        <td>{{ $r->pastor_responsavel }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.regionais.edit', $r) }}" class="btn btn-sm btn-default">Editar</a>
                                            <form action="{{ route('admin.regionais.destroy', $r) }}" method="post" class="d-inline" onsubmit="return confirm('Excluir esta regional?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">Nenhuma regional cadastrada.</td>
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
                    var table = $('#datatable-regionais').DataTable({
                        paging: false,
                        lengthChange: false,
                        info: false,
                        ordering: true,
                        columnDefs: [{ orderable: false, targets: 2 }],
                        language: { search: 'Buscar:', zeroRecords: 'Nenhum registro encontrado.', emptyTable: '—' },
                        dom: '<"row"<"col-lg-12"f>><"table-responsive"t>',
                        buttons: [{ extend: 'print', text: 'Imprimir' }, { extend: 'excel', text: 'Excel' }, { extend: 'pdf', text: 'PDF' }]
                    });
                    $('<div />').addClass('dt-buttons mb-2 pb-1 text-end').prependTo('#datatable-regionais_wrapper');
                    table.buttons().container().prependTo('#datatable-regionais_wrapper .dt-buttons');
                    $('#datatable-regionais_wrapper').find('.btn-secondary').removeClass('btn-secondary').addClass('btn-default');
                });
            })(jQuery);
        </script>
    @endif
@endpush
