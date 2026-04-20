@extends('layouts.admin')

@section('title', 'Inscrições')

@section('page_title', 'Pré-inscrições')

@section('breadcrumbs')
    <li>
        <a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a>
    </li>
    <li><span>Admin</span></li>
    <li><span>Pré-inscrições</span></li>
@endsection

@php($porto = asset('porto-admin'))
@php($hasRows = $inscricoes->count() > 0)
@php($statusOptions = \App\Models\PreInscricao::statusOptions())
@push('head')
    <link rel="stylesheet" href="{{ $porto }}/vendor/datatables/media/css/dataTables.bootstrap5.css" />
    <style>
        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            white-space: nowrap;
            font-size: 0.75rem;
            line-height: 1;
            color: #fff;
            border-radius: 999px;
            padding: 0.3rem 0.55rem;
        }
        .status-badge--aguardando {
            background-color: #f0ad4e;
        }
        .status-badge--confirmada {
            background-color: #5cb85c;
        }
        .status-badge--cancelada {
            background-color: #d9534f;
        }
        .status-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            padding: 1rem;
        }
        .status-modal-backdrop.is-open {
            display: flex;
        }
        .status-modal {
            width: min(420px, 100%);
            background: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        .status-modal__header,
        .status-modal__footer {
            padding: 0.9rem 1rem;
            border-bottom: 1px solid #e9ecef;
        }
        .status-modal__footer {
            border-bottom: 0;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
        }
        .status-modal__body {
            padding: 1rem;
        }
        .meta-progress {
            height: 10px;
            border-radius: 999px;
            overflow: hidden;
            background: #e9ecef;
        }
        .meta-progress__bar {
            height: 100%;
            background: linear-gradient(90deg, #4aa3ff 0%, #1c7ed6 100%);
        }
        .meta-regionais-lista {
            margin-top: 1rem;
            border-top: 1px solid #e9ecef;
            padding-top: 0.75rem;
        }
        .meta-regionais-item {
            padding: 0.6rem 0 0.8rem;
            border-bottom: 1px dashed #e9ecef;
        }
        .meta-regionais-item:last-child {
            border-bottom: 0;
        }
        .meta-regionais-progress {
            height: 8px;
            border-radius: 999px;
            overflow: hidden;
            background: #e9ecef;
        }
        .meta-regionais-progress__bar {
            height: 100%;
            background: linear-gradient(90deg, #5ba3eb 0%, #2d7dd2 100%);
        }
        /* DataTables: o filtro vem em col-12 e o input herdava largura total */
        #datatable-tabletools_wrapper .dataTables_filter {
            float: none !important;
            text-align: right;
        }
        #datatable-tabletools_wrapper .dataTables_filter label {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0;
            width: auto;
            max-width: 100%;
        }
        #datatable-tabletools_wrapper .dataTables_filter input {
            display: inline-block;
            width: 220px !important;
            max-width: min(280px, calc(100vw - 3rem)) !important;
        }
        #datatable-tabletools_wrapper > .row:first-of-type {
            justify-content: flex-end;
        }
        #datatable-tabletools_wrapper > .row:first-of-type > [class*="col-"] {
            flex: 0 1 auto;
            width: auto;
            max-width: 100%;
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            'use strict';
            var tipo = document.getElementById('destinatario_tipo');
            var wrapperStatus = document.getElementById('wrapper_status_notificacao');
            var wrapperIgreja = document.getElementById('wrapper_igreja_notificacao');
            var wrapperRegional = document.getElementById('wrapper_regional_notificacao');
            var inputStatus = document.getElementById('status_notificacao_id');
            var inputIgreja = document.getElementById('igreja_notificacao_id');
            var inputRegional = document.getElementById('regional_notificacao_id');
            if (!tipo || !wrapperStatus || !wrapperIgreja || !wrapperRegional) return;

            var update = function() {
                var val = tipo.value;
                var showStatus = val === 'status';
                var showIgreja = val === 'igreja';
                var showRegional = val === 'regional';

                wrapperStatus.style.display = showStatus ? '' : 'none';
                wrapperIgreja.style.display = showIgreja ? '' : 'none';
                wrapperRegional.style.display = showRegional ? '' : 'none';

                if (inputStatus) inputStatus.required = showStatus;
                if (inputIgreja) inputIgreja.required = showIgreja;
                if (inputRegional) inputRegional.required = showRegional;
            };

            tipo.addEventListener('change', update);
            update();
        })();
    </script>
@endpush

@section('content')
    <div class="row mb-3">
        <div class="col-12">
            <p class="text-muted mb-0">Total registrado: <strong class="text-dark">{{ $total }}</strong></p>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-lg-6">
            <section class="card card-featured-left card-featured-primary h-100">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h4 class="mb-1">Inscrições totais x meta</h4>
                            <p class="mb-0 text-muted">
                                <strong class="text-dark">{{ $total }}</strong> de {{ $metaInscricoes }} inscrições ({{ $percentualMeta }}%)
                            </p>
                        </div>
                        <div class="text-end">
                            <strong class="h5 mb-0">{{ $total }}/{{ $metaInscricoes }}</strong>
                        </div>
                    </div>
                    <div class="meta-progress mt-3">
                        <div class="meta-progress__bar" style="width: {{ $percentualMeta }}%;"></div>
                    </div>
                    @if ($metasRegionais->isNotEmpty())
                        <div class="meta-regionais-lista">
                            @foreach ($metasRegionais as $item)
                                <div class="meta-regionais-item">
                                    <div>
                                        <strong>Inscrições totais x meta {{ $item['regional']->nome }}</strong>
                                        <div class="text-muted">
                                            {{ $item['inscricoes_atual'] }} de {{ $item['meta'] }} inscrições ({{ $item['percentual'] }}%)
                                        </div>
                                    </div>
                                    <div class="meta-regionais-progress mt-2">
                                        <div class="meta-regionais-progress__bar" style="width: {{ $item['percentual'] }}%;"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
        </div>
        @php($pieSeries = $regionaisCards->filter(fn ($card) => $card['total'] > 0)->map(function ($card) {
            return [
                'label' => $card['regional']->nome,
                'data' => [[1, $card['total']]],
            ];
        })->values())
        <div class="col-lg-6">
            <section class="card h-100">
                <header class="card-header">
                    <h2 class="card-title">Inscrições por regional</h2>
                    <p class="card-subtitle">Distribuição das pré-inscrições por regional.</p>
                </header>
                <div class="card-body">
                    @if ($pieSeries->isEmpty())
                        <p class="text-muted mb-0">Sem inscrições para exibir no gráfico.</p>
                    @else
                        <div class="chart chart-md" id="flotPieRegionais"></div>
                    @endif
                </div>
            </section>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">Enviar notificação para inscritos</h2>
                </header>
                <div class="card-body">
                    <form method="post" action="{{ route('admin.inscricoes.notificacoes.enviar') }}" class="row g-2 align-items-end" id="formEnvioNotificacaoInscricoes">
                        @csrf
                        <div class="col-md-6">
                            <label for="mensagem_notificacao" class="form-label">Mensagem</label>
                            <textarea
                                id="mensagem_notificacao"
                                name="mensagem"
                                class="form-control"
                                rows="3"
                                maxlength="4096"
                                required
                            >{{ old('mensagem') }}</textarea>
                        </div>
                        <div class="col-md-3">
                            <label for="destinatario_tipo" class="form-label">Destinatário</label>
                            <select name="destinatario_tipo" id="destinatario_tipo" class="form-control" required>
                                <option value="status" @selected(old('destinatario_tipo', 'status') === 'status')>Por status da inscrição</option>
                                <option value="igreja" @selected(old('destinatario_tipo') === 'igreja')>Por igreja</option>
                                <option value="regional" @selected(old('destinatario_tipo') === 'regional')>Por regional</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="wrapper_status_notificacao">
                            <label for="status_notificacao_id" class="form-label">Status da inscrição</label>
                            <select name="status_destinatario" id="status_notificacao_id" class="form-control">
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}" @selected((string) old('status_destinatario', \App\Models\PreInscricao::STATUS_AGUARDANDO) === (string) $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3" id="wrapper_igreja_notificacao">
                            <label for="igreja_notificacao_id" class="form-label">Igreja</label>
                            <select name="igreja_id" id="igreja_notificacao_id" class="form-control">
                                <option value="">Selecione</option>
                                @foreach ($igrejasFiltro as $igreja)
                                    <option value="{{ $igreja->id }}" @selected((string) old('igreja_id') === (string) $igreja->id)>
                                        {{ $igreja->nomeNoFormulario() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3" id="wrapper_regional_notificacao">
                            <label for="regional_notificacao_id" class="form-label">Regional</label>
                            <select name="regional_id" id="regional_notificacao_id" class="form-control">
                                <option value="">Selecione</option>
                                @foreach ($regionaisFiltro as $regional)
                                    <option value="{{ $regional->id }}" @selected((string) old('regional_id') === (string) $regional->id)>
                                        {{ $regional->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Enviar</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12">
            <section class="card">
                <div class="card-body">
                    <form method="get" action="{{ route('admin.dashboard') }}" class="row g-2 align-items-end">
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
                        <div class="col-md-3">
                            <label for="igreja_id" class="form-label">Filtrar por Igreja</label>
                            <select name="igreja_id" id="igreja_id" class="form-control">
                                <option value="">Todas</option>
                                @foreach ($igrejasFiltro as $igreja)
                                    <option value="{{ $igreja->id }}" @selected((string) $selectedIgrejaId === (string) $igreja->id)>
                                        {{ $igreja->nomeNoFormulario() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="per_page" class="form-label">Por página</label>
                            <select name="per_page" id="per_page" class="form-control">
                                <option value="10" @selected((string) $selectedPerPage === '10')>10</option>
                                <option value="50" @selected((string) $selectedPerPage === '50')>50</option>
                                <option value="100" @selected((string) $selectedPerPage === '100')>100</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Aplicar filtro</button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-default">Limpar</a>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <section class="card">
                <header class="card-header">
                    <div class="card-actions">
                        <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
                        <a href="#" class="card-action card-action-dismiss" data-card-dismiss></a>
                    </div>
                    <h2 class="card-title">Pré-inscrições</h2>
                </header>
                @unless ($hasRows)
                    <div class="card-body">
                        <p class="text-muted text-center py-4 mb-0">Nenhuma pré-inscrição ainda.</p>
                    </div>
                @else
                    <div class="card-body">
                        <table class="table table-bordered table-striped mb-0" id="datatable-tabletools" width="100%">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Idade</th>
                                    <th>WhatsApp</th>
                                    <th>Tamanho da camiseta</th>
                                    <th>Nome da igreja</th>
                                    <th>Regional</th>
                                    <th>Líder</th>
                                    <th>Status</th>
                                    <th class="text-end" style="width: 12rem;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inscricoes as $row)
                                    @php($st = $row->status ?: \App\Models\PreInscricao::STATUS_AGUARDANDO)
                                    <tr>
                                        <td>{{ $row->nome }}</td>
                                        <td>{{ $row->idade }}</td>
                                        <td>{{ $row->whatsapp }}</td>
                                        <td>{{ $row->tamanho_camiseta ?: '—' }}</td>
                                        <td>{{ $row->igrejaRel?->nomeNoFormulario() ?? $row->igreja }}</td>
                                        <td>{{ $row->igrejaRel?->regional?->nome ?? '—' }}</td>
                                        <td>{{ $row->lider_jovens ? 'Sim' : 'Não' }}</td>
                                        <td class="js-status-cell">
                                            <span class="status-badge status-badge--{{ $st }}">
                                                <span class="js-status-text">{{ $statusOptions[$st] }}</span>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-primary me-1 js-open-status-modal"
                                                data-patch-url="{{ route('admin.inscricoes.status', $row) }}"
                                                data-current-status="{{ $st }}"
                                                data-nome="{{ $row->nome }}"
                                                aria-label="Alterar status de {{ $row->nome }}"
                                            >
                                                <i class="fas fa-rotate"></i>
                                            </button>
                                            <a href="{{ route('admin.inscricoes.edit', $row) }}" class="btn btn-sm btn-default" title="Editar" aria-label="Editar {{ $row->nome }}">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <form action="{{ route('admin.inscricoes.destroy', $row) }}" method="post" class="d-inline" onsubmit="return confirm('Excluir esta pré-inscrição?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Excluir" aria-label="Excluir {{ $row->nome }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($inscricoes->hasPages())
                        <footer class="card-footer">
                            <div class="d-flex flex-wrap align-items-center gap-3">
                                <span class="text-muted">Página {{ $inscricoes->currentPage() }} de {{ $inscricoes->lastPage() }}</span>
                                @if (! $inscricoes->onFirstPage())
                                    <a class="btn btn-sm btn-default" href="{{ $inscricoes->previousPageUrl() }}">Anterior</a>
                                @endif
                                @if ($inscricoes->hasMorePages())
                                    <a class="btn btn-sm btn-default" href="{{ $inscricoes->nextPageUrl() }}">Próxima</a>
                                @endif
                            </div>
                        </footer>
                    @endif
                @endunless
            </section>
        </div>
    </div>
    <div class="status-modal-backdrop" id="statusModalBackdrop" aria-hidden="true">
        <div class="status-modal" role="dialog" aria-modal="true" aria-labelledby="statusModalTitle">
            <div class="status-modal__header">
                <h3 class="m-0 h5" id="statusModalTitle">Alterar status</h3>
            </div>
            <div class="status-modal__body">
                <p class="mb-2 text-muted" id="statusModalDescription">Selecione o novo status:</p>
                <select id="statusModalSelect" class="form-control">
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="status-modal__footer">
                <button type="button" class="btn btn-default" id="statusModalCancel">Cancelar</button>
                <button type="button" class="btn btn-primary" id="statusModalSave">Salvar</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @if ($hasRows)
        <script src="{{ $porto }}/vendor/flot/jquery.flot.js"></script>
        <script src="{{ $porto }}/vendor/flot/jquery.flot.pie.js"></script>
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
                    var pieData = @json($pieSeries);
                    if (pieData.length && $('#flotPieRegionais').length) {
                        var pieColors = ['#0088cc', '#2baab1', '#734ba9', '#E36159', '#f0ad4e', '#5cb85c', '#6c757d'];
                        pieData = pieData.map(function(item, index) {
                            item.color = pieColors[index % pieColors.length];
                            return item;
                        });

                        $.plot('#flotPieRegionais', pieData, {
                            series: {
                                pie: {
                                    show: true,
                                    label: {
                                        show: true,
                                        radius: 1,
                                        formatter: function(label, series) {
                                            var valor = 0;
                                            if (series && series.data && series.data.length && series.data[0].length > 1) {
                                                valor = series.data[0][1];
                                            }
                                            return '<div style="font-size:x-small;text-align:center;padding:2px;color:' +
                                                series.color + ';">' + label + '<br>' + valor + '</div>';
                                        },
                                        threshold: 0.03
                                    }
                                }
                            },
                            legend: {
                                show: false
                            }
                        });
                    }

                    var $table = $('#datatable-tabletools');
                    /* Normaliza células com HTML para exportação (print/excel/pdf). */
                    var exportOpts = {
                        columns: [0, 1, 2, 3, 4, 5, 7],
                        format: {
                            body: function(data, row, col, node) {
                                if (typeof data === 'string') {
                                    return $('<div>').html(data).text().replace(/\s+/g, ' ').trim();
                                }
                                return data != null ? String(data) : '';
                            }
                        }
                    };
                    var dt = $table.DataTable({
                        paging: false,
                        lengthChange: false,
                        info: false,
                        ordering: true,
                        columnDefs: [{
                            orderable: false,
                            targets: [8]
                        }],
                        language: {
                            search: 'Buscar:',
                            zeroRecords: 'Nenhum registro encontrado.',
                            emptyTable: '—'
                        },
                        dom: '<"row"<"col-lg-12"f>><"table-responsive"t>',
                        buttons: [{
                                extend: 'print',
                                text: 'Imprimir',
                                exportOptions: exportOpts
                            },
                            {
                                extend: 'excel',
                                text: 'Excel',
                                exportOptions: exportOpts
                            },
                            {
                                extend: 'pdf',
                                text: 'PDF',
                                exportOptions: exportOpts,
                                customize: function(doc) {
                                    var colCount = [];
                                    $('#datatable-tabletools').find('tbody tr:first-child td').each(function() {
                                        var cs = $(this).attr('colspan');
                                        if (cs) {
                                            var n = parseInt(cs, 10);
                                            for (var i = 1; i <= n; i++) {
                                                colCount.push('*');
                                            }
                                        } else {
                                            colCount.push('*');
                                        }
                                    });
                                    if (doc.content[1] && doc.content[1].table) {
                                        doc.content[1].table.widths = colCount;
                                    }
                                }
                            }
                        ]
                    });
                    $('<div />').addClass('dt-buttons mb-2 pb-1 text-end').prependTo('#datatable-tabletools_wrapper');
                    dt.buttons().container().prependTo('#datatable-tabletools_wrapper .dt-buttons');
                    $('#datatable-tabletools_wrapper').find('.btn-secondary').removeClass('btn-secondary').addClass('btn-default');

                    var csrf = document.querySelector('meta[name="csrf-token"]');
                    var csrfToken = csrf ? csrf.getAttribute('content') : '';
                    var modalBackdrop = document.getElementById('statusModalBackdrop');
                    var modalSelect = document.getElementById('statusModalSelect');
                    var modalDescription = document.getElementById('statusModalDescription');
                    var modalSaveBtn = document.getElementById('statusModalSave');
                    var modalCancelBtn = document.getElementById('statusModalCancel');
                    var activeStatusButton = null;

                    function closeStatusModal() {
                        if (!modalBackdrop) return;
                        modalBackdrop.classList.remove('is-open');
                        modalBackdrop.setAttribute('aria-hidden', 'true');
                        activeStatusButton = null;
                    }

                    $(document).on('click', '.js-open-status-modal', function() {
                        if (!modalBackdrop || !modalSelect || !modalDescription) return;
                        activeStatusButton = this;
                        var currentStatus = this.getAttribute('data-current-status') || 'aguardando';
                        var nome = this.getAttribute('data-nome') || 'inscrição';
                        modalSelect.value = currentStatus;
                        modalDescription.textContent = 'Selecione o novo status para ' + nome + ':';
                        modalBackdrop.classList.add('is-open');
                        modalBackdrop.setAttribute('aria-hidden', 'false');
                    });

                    if (modalCancelBtn) {
                        modalCancelBtn.addEventListener('click', closeStatusModal);
                    }

                    if (modalBackdrop) {
                        modalBackdrop.addEventListener('click', function(e) {
                            if (e.target === modalBackdrop) closeStatusModal();
                        });
                    }

                    if (modalSaveBtn) {
                        modalSaveBtn.addEventListener('click', function() {
                            if (!activeStatusButton || !modalSelect) return;
                            var $btn = $(activeStatusButton);
                            var url = $btn.data('patch-url');
                            var val = modalSelect.value;
                            modalSaveBtn.disabled = true;
                            if (modalCancelBtn) modalCancelBtn.disabled = true;
                            $btn.prop('disabled', true);
                            fetch(url, {
                                    method: 'PATCH',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken,
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    credentials: 'same-origin',
                                    body: JSON.stringify({
                                        status: val
                                    })
                                })
                                .then(function(r) {
                                    if (!r.ok) {
                                        return r.json().then(function(body) {
                                            var msg = (body.errors && body.errors.status && body.errors.status[0]) || body.message || 'Erro ao atualizar.';
                                            throw new Error(msg);
                                        });
                                    }
                                    return r.json();
                                })
                                .then(function() {
                                    $btn.attr('data-current-status', val);
                                    var $statusCell = $btn.closest('tr').find('.js-status-cell');
                                    var $statusBadge = $statusCell.find('.status-badge');
                                    $statusBadge.removeClass('status-badge--aguardando status-badge--confirmada status-badge--cancelada');
                                    $statusBadge.addClass('status-badge--' + val);
                                    var label = $('#statusModalSelect option:selected').text();
                                    $statusCell.find('.js-status-text').text(label);
                                    closeStatusModal();
                                    alert('Status alterado com sucesso.');
                                })
                                .catch(function(err) {
                                    alert(err.message || 'Não foi possível atualizar o status.');
                                })
                                .finally(function() {
                                    modalSaveBtn.disabled = false;
                                    if (modalCancelBtn) modalCancelBtn.disabled = false;
                                    $btn.prop('disabled', false);
                                });
                        });
                    }
                });
            })(jQuery);
        </script>
    @endif
@endpush
