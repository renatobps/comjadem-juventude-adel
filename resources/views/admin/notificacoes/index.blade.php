@extends('layouts.admin')

@section('title', 'Notificações')

@section('page_title', 'Notificações WhatsApp')

@section('breadcrumbs')
    <li><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
    <li><span>Notificações</span></li>
@endsection

@section('content')
    <style>
        .wpp-log-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #fff;
        }
        .wpp-log-badge--ok {
            background: #22a652;
        }
        .wpp-log-badge--erro {
            background: #dc3545;
        }
    </style>

    <div class="row">
        <div class="col-lg-8">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">Enviar mensagem de texto</h2>
                    <p class="card-subtitle mb-0">Exemplo simples de envio via Evolution Go API.</p>
                </header>
                <div class="card-body">
                    <form method="post" action="{{ route('admin.notificacoes.enviar-texto') }}" id="formNotificacaoWhatsapp" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="numero">Número WhatsApp (DDD + número, sem 55) <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                name="numero"
                                id="numero"
                                class="form-control"
                                required
                                maxlength="11"
                                inputmode="numeric"
                                pattern="[0-9]{11}"
                                value="{{ old('numero', '61999999999') }}"
                                placeholder="Ex.: 61993640457"
                            >
                            <small class="text-muted">O sistema adiciona automaticamente o prefixo 55 no envio.</small>
                            @error('numero')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="mensagem">Mensagem / Legenda <span class="text-danger">*</span></label>
                            <textarea
                                name="mensagem"
                                id="mensagem"
                                class="form-control"
                                rows="4"
                                required
                                maxlength="4096"
                                placeholder="Digite a mensagem de texto."
                            >{{ old('mensagem', 'Paz do Senhor! Este é um teste de notificação do sistema CONJADEM.') }}</textarea>
                            @error('mensagem')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="arquivo">Anexo (opcional)</label>
                            <input
                                type="file"
                                name="arquivo"
                                id="arquivo"
                                class="form-control"
                                accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar,.txt"
                            >
                            <small class="text-muted">
                                Se anexar arquivo, o sistema envia via <code>/send/media</code> e detecta automaticamente o tipo:
                                image, video, audio ou document.
                            </small>
                            @error('arquivo')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Enviar mensagem</button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-default">Voltar</a>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title mb-0">Mensagem automática após inscrição</h2>
                </header>
                <div class="card-body">
                    <form method="post" action="{{ route('admin.notificacoes.mensagem-pos-inscricao') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="mensagem_pos_inscricao">Mensagem</label>
                            <textarea
                                class="form-control"
                                id="mensagem_pos_inscricao"
                                name="mensagem_pos_inscricao"
                                rows="10"
                                required
                                maxlength="8000"
                            >{{ old('mensagem_pos_inscricao', $mensagemPosInscricao ?? '') }}</textarea>
                            <small class="text-muted">
                                Placeholders disponíveis: <code>{nome_do_inscrito}</code> e <code>{tamanho_camiseta}</code>.
                            </small>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Salvar mensagem</button>
                            <button type="button" class="btn btn-default" id="btnRestaurarMensagemPadrao">Restaurar padrão</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title mb-0">Mensagem quando status for Confirmada</h2>
                </header>
                <div class="card-body">
                    <form method="post" action="{{ route('admin.notificacoes.mensagem-confirmada') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="mensagem_confirmada">Mensagem</label>
                            <textarea
                                class="form-control"
                                id="mensagem_confirmada"
                                name="mensagem_confirmada"
                                rows="10"
                                required
                                maxlength="8000"
                            >{{ old('mensagem_confirmada', $mensagemConfirmada ?? '') }}</textarea>
                            <small class="text-muted">
                                Placeholders disponíveis: <code>{nome_do_inscrito}</code> e <code>{tamanho_camiseta}</code>.
                            </small>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Salvar mensagem de confirmação</button>
                            <button type="button" class="btn btn-default" id="btnRestaurarMensagemConfirmadaPadrao">Restaurar padrão</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title mb-0">Histórico</h2>
                </header>
                <div class="card-body pb-0">
                    <form method="get" action="{{ route('admin.notificacoes.index') }}" class="row g-2 justify-content-end">
                        <div class="col-md-2">
                            <select class="form-control" name="status_historico">
                                <option value="todos" @selected(($statusHistorico ?? 'todos') === 'todos')>Todos os status</option>
                                <option value="enviada" @selected(($statusHistorico ?? '') === 'enviada')>Enviada</option>
                                <option value="erro" @selected(($statusHistorico ?? '') === 'erro')>Erro</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" name="data_inicio" value="{{ $dataInicioHistorico ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" name="data_fim" value="{{ $dataFimHistorico ?? '' }}">
                        </div>
                        <div class="col-md-1 d-grid">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                        </div>
                        <div class="col-md-2 d-grid">
                            <a href="{{ route('admin.notificacoes.index') }}" class="btn btn-default">Limpar filtro</a>
                        </div>
                    </form>
                </div>
                <div class="card-body p-0 pt-2">
                    @if (empty($historicoNotificacoes))
                        <p class="text-muted m-3 mb-0">Sem histórico de notificações.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Destinatário</th>
                                        <th>Mensagem</th>
                                        <th style="width: 120px;">Status</th>
                                        <th style="width: 170px;">Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($historicoNotificacoes as $item)
                                        <tr>
                                            <td>{{ $item['destinatario'] ?? '—' }}</td>
                                            <td>
                                                @if (!empty($item['mensagem']))
                                                    <button
                                                        type="button"
                                                        class="btn btn-xs btn-default js-ver-mensagem"
                                                        data-mensagem="{{ $item['mensagem'] }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalMensagemHistorico"
                                                    >
                                                        Ver mensagem
                                                    </button>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>
                                                <span class="wpp-log-badge {{ ($item['status'] ?? 'enviada') === 'erro' ? 'wpp-log-badge--erro' : 'wpp-log-badge--ok' }}">
                                                    {{ ($item['status'] ?? 'enviada') === 'erro' ? 'Erro' : 'Enviada' }}
                                                </span>
                                            </td>
                                            <td>
                                                @php($dt = isset($item['data']) ? \Carbon\Carbon::parse($item['data']) : null)
                                                {{ $dt ? $dt->format('d/m/Y H:i') : '—' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>

    <div class="modal fade" id="modalMensagemHistorico" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mensagem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div id="conteudoMensagemHistorico" style="white-space: pre-wrap;">—</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            'use strict';
            var input = document.getElementById('numero');
            var form = document.getElementById('formNotificacaoWhatsapp');
            var textareaMensagemPosInscricao = document.getElementById('mensagem_pos_inscricao');
            var btnRestaurarMensagemPadrao = document.getElementById('btnRestaurarMensagemPadrao');
            var textareaMensagemConfirmada = document.getElementById('mensagem_confirmada');
            var btnRestaurarMensagemConfirmadaPadrao = document.getElementById('btnRestaurarMensagemConfirmadaPadrao');
            if (!input) return;

            var normalize = function(value) {
                return (value || '').replace(/\D/g, '').slice(0, 11);
            };

            input.addEventListener('input', function(e) {
                e.target.value = normalize(e.target.value);
            });

            input.value = normalize(input.value);

            if (form) {
                form.addEventListener('submit', function() {
                    var digits = normalize(input.value);
                    if (digits.length === 11) {
                        input.value = '55' + digits;
                    }
                });
            }

            var modalBody = document.getElementById('conteudoMensagemHistorico');
            document.querySelectorAll('.js-ver-mensagem').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    if (!modalBody) return;
                    modalBody.textContent = btn.getAttribute('data-mensagem') || '—';
                });
            });

            if (btnRestaurarMensagemPadrao && textareaMensagemPosInscricao) {
                var mensagemPadrao = @json($mensagemPosInscricaoPadrao ?? '');
                btnRestaurarMensagemPadrao.addEventListener('click', function() {
                    textareaMensagemPosInscricao.value = mensagemPadrao;
                });
            }

            if (btnRestaurarMensagemConfirmadaPadrao && textareaMensagemConfirmada) {
                var mensagemConfirmadaPadrao = @json($mensagemConfirmadaPadrao ?? '');
                btnRestaurarMensagemConfirmadaPadrao.addEventListener('click', function() {
                    textareaMensagemConfirmada.value = mensagemConfirmadaPadrao;
                });
            }
        })();
    </script>
@endpush
