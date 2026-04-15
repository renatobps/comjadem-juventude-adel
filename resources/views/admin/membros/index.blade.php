@extends('layouts.admin')

@section('title', 'Membros')

@section('page_title', 'Cadastro de membros')

@section('breadcrumbs')
    <li><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
    <li><span>Membros</span></li>
    <li><span>Cadastro</span></li>
@endsection

@php($porto = asset('porto-admin'))
@php($hasRows = $membros->count() > 0)

@push('head')
    <link rel="stylesheet" href="{{ $porto }}/vendor/datatables/media/css/dataTables.bootstrap5.css" />
@endpush

@section('content')
    <div class="row mb-3">
        <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <p class="text-muted mb-0">Cadastre os membros com nome, email, senha, cargo e telefone.</p>
            <a href="{{ route('admin.membros.create') }}" class="btn btn-primary {{ $cargos->isEmpty() ? 'disabled' : '' }}" @if($cargos->isEmpty()) onclick="return false;" aria-disabled="true" @endif>Novo membro</a>
        </div>
    </div>
    @if ($cargos->isEmpty())
        <div class="alert alert-warning">
            Cadastre pelo menos um <a href="{{ route('admin.cargos.create') }}">cargo</a> antes de incluir membros.
        </div>
    @endif
    <div class="row">
        <div class="col-12">
            <section class="card">
                <header class="card-header">
                    <div class="card-actions">
                        <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
                        <a href="#" class="card-action card-action-dismiss" data-card-dismiss></a>
                    </div>
                    <h2 class="card-title">Lista de membros</h2>
                </header>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0" id="datatable-membros" width="100%">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Cargo</th>
                                    <th>Telefone</th>
                                    <th class="text-end" style="width: 12rem;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($membros as $membro)
                                    <tr>
                                        <td>{{ $membro->nome }}</td>
                                        <td>{{ $membro->email }}</td>
                                        <td>{{ $membro->cargo?->nome }}</td>
                                        <td>{{ $membro->telefone }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.membros.edit', $membro) }}" class="btn btn-sm btn-default">Editar</a>
                                            <form action="{{ route('admin.membros.destroy', $membro) }}" method="post" class="d-inline" onsubmit="return confirm('Excluir este membro?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Nenhum membro cadastrado.</td>
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
                    var table = $('#datatable-membros').DataTable({
                        paging: false,
                        lengthChange: false,
                        info: false,
                        ordering: true,
                        columnDefs: [{ orderable: false, targets: 4 }],
                        language: { search: 'Buscar:', zeroRecords: 'Nenhum registro encontrado.', emptyTable: '—' },
                        dom: '<"row"<"col-lg-12"f>><"table-responsive"t>',
                        buttons: [{ extend: 'print', text: 'Imprimir' }, { extend: 'excel', text: 'Excel' }, { extend: 'pdf', text: 'PDF' }]
                    });
                    $('<div />').addClass('dt-buttons mb-2 pb-1 text-end').prependTo('#datatable-membros_wrapper');
                    table.buttons().container().prependTo('#datatable-membros_wrapper .dt-buttons');
                    $('#datatable-membros_wrapper').find('.btn-secondary').removeClass('btn-secondary').addClass('btn-default');
                });
            })(jQuery);
        </script>
    @endif
@endpush
