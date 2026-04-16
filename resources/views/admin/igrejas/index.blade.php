@extends('layouts.admin')

@section('title', 'Igrejas')

@section('page_title', 'Igrejas')

@section('breadcrumbs')
    <li><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
    <li><span>Igrejas</span></li>
@endsection

@php($porto = asset('porto-admin'))
@php($hasRows = $igrejas->count() > 0)

@push('head')
    <link rel="stylesheet" href="{{ $porto }}/vendor/datatables/media/css/dataTables.bootstrap5.css" />
@endpush

@section('content')
    @php($cardVariants = ['primary', 'secondary', 'tertiary', 'quaternary'])
    @if ($regionaisCards->isNotEmpty())
        <div class="row mb-3">
            @foreach ($regionaisCards as $regional)
                @php($variant = $cardVariants[$loop->index % count($cardVariants)])
                <div class="col-xl-6">
                    <section class="card card-featured-left card-featured-{{ $variant }} mb-3">
                        <div class="card-body">
                            <div class="widget-summary">
                                <div class="widget-summary-col widget-summary-col-icon">
                                    <div class="summary-icon bg-{{ $variant }}">
                                        <i class="fa-solid fa-church"></i>
                                    </div>
                                </div>
                                <div class="widget-summary-col">
                                    <div class="summary">
                                        <h4 class="title">{{ $regional->nome }}</h4>
                                        <div class="info">
                                            <strong class="amount">{{ $regional->igrejas_count }} igrejas</strong>
                                        </div>
                                    </div>
                                    <div class="summary-footer">
                                        <span class="text-muted text-uppercase">Pastor: {{ $regional->pastor_responsavel }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            @endforeach
        </div>
    @endif
    <div class="row mb-3">
        <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <p class="text-muted mb-0">Cadastre o <strong>bairro</strong>, o <strong>dirigente</strong> e a <strong>regional</strong> da igreja.</p>
            <a href="{{ route('admin.igrejas.create') }}" class="btn btn-primary {{ $regionais->isEmpty() ? 'disabled' : '' }}" @if($regionais->isEmpty()) onclick="return false;" aria-disabled="true" @endif>Nova igreja</a>
        </div>
    </div>
    @if ($regionais->isEmpty())
        <div class="alert alert-warning">
            Cadastre pelo menos uma <a href="{{ route('admin.regionais.create') }}">regional</a> antes de incluir igrejas.
        </div>
    @endif
    <div class="row mb-3">
        <div class="col-12">
            <section class="card">
                <div class="card-body">
                    <form method="get" action="{{ route('admin.igrejas.index') }}" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label for="regional_id" class="form-label">Filtrar por Regional</label>
                            <select name="regional_id" id="regional_id" class="form-control">
                                <option value="">Todas</option>
                                @foreach ($regionaisFiltro as $regional)
                                    <option value="{{ $regional->id }}" @selected((string) $selectedRegionalId === (string) $regional->id)>
                                        {{ $regional->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="per_page" class="form-label">Por página</label>
                            <select name="per_page" id="per_page" class="form-control">
                                <option value="10" @selected((string) $selectedPerPage === '10')>10</option>
                                <option value="25" @selected((string) $selectedPerPage === '25')>25</option>
                                <option value="80" @selected((string) $selectedPerPage === '80')>80</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Aplicar filtro</button>
                            <a href="{{ route('admin.igrejas.index') }}" class="btn btn-default">Limpar</a>
                        </div>
                    </form>
                </div>
            </section>
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
                    <h2 class="card-title">Lista de igrejas</h2>
                </header>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0" id="datatable-igrejas" width="100%">
                            <thead>
                                <tr>
                                    <th>Bairro da igreja</th>
                                    <th>Dirigente</th>
                                    <th>Regional</th>
                                    <th class="text-end" style="width: 12rem;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($igrejas as $igreja)
                                    <tr>
                                        <td>{{ $igreja->bairro }}</td>
                                        <td>{{ $igreja->dirigenteMembro?->nome ?? $igreja->dirigente }}</td>
                                        <td>{{ $igreja->regional?->nome }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.igrejas.edit', $igreja) }}" class="btn btn-sm btn-default">Editar</a>
                                            <form action="{{ route('admin.igrejas.destroy', $igreja) }}" method="post" class="d-inline" onsubmit="return confirm('Excluir esta igreja?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Nenhuma igreja cadastrada.</td>
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
                    var exportOpts = {
                        columns: [0, 1, 2],
                        format: {
                            body: function(data) {
                                if (typeof data === 'string') {
                                    return $('<div>').html(data).text().replace(/\s+/g, ' ').trim();
                                }
                                return data != null ? String(data) : '';
                            }
                        }
                    };
                    var table = $('#datatable-igrejas').DataTable({
                        paging: true,
                        lengthChange: true,
                        pageLength: {{ (int) $selectedPerPage }},
                        lengthMenu: [[10, 25, 80], [10, 25, 80]],
                        info: true,
                        ordering: true,
                        columnDefs: [{ orderable: false, targets: 3 }],
                        language: {
                            search: 'Buscar:',
                            lengthMenu: 'Exibir _MENU_',
                            info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                            infoEmpty: 'Mostrando 0 a 0 de 0 registros',
                            zeroRecords: 'Nenhum registro encontrado.',
                            emptyTable: '—'
                        },
                        dom: '<"row"<"col-md-6"f><"col-md-6 text-end"B>><"table-responsive"t><"row mt-2"<"col-md-5"i><"col-md-7"p>>',
                        buttons: [
                            { extend: 'print', text: 'Imprimir', exportOptions: exportOpts },
                            { extend: 'excel', text: 'Excel', exportOptions: exportOpts },
                            {
                                extend: 'pdf',
                                text: 'PDF',
                                exportOptions: exportOpts,
                                customize: function(doc) {
                                    if (doc.content[1] && doc.content[1].table) {
                                        doc.content[1].table.widths = ['*', '*', '*'];
                                    }
                                }
                            }
                        ]
                    });
                    $('<div />').addClass('dt-buttons mb-2 pb-1 text-end').prependTo('#datatable-igrejas_wrapper');
                    table.buttons().container().prependTo('#datatable-igrejas_wrapper .dt-buttons');
                    $('#datatable-igrejas_wrapper').find('.btn-secondary').removeClass('btn-secondary').addClass('btn-default');
                });
            })(jQuery);
        </script>
    @endif
@endpush
