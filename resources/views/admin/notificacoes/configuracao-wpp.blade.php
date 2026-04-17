@extends('layouts.admin')

@section('title', 'Configuração WPP')

@section('page_title', 'Configuração WPP')

@section('breadcrumbs')
    <li><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
    <li><a href="{{ route('admin.notificacoes.index') }}">Notificações</a></li>
    <li><span>Configuração WPP</span></li>
@endsection

@section('content')
    <style>
        .wpp-card-header {
            background: #0b8a5a;
            color: #fff;
            font-weight: 600;
        }
        .wpp-alert {
            background: #fff8d8;
            border: 1px solid #f3e6a2;
            color: #7a6423;
            border-radius: 4px;
            padding: 14px 16px;
            margin-bottom: 1rem;
        }
        .wpp-alert strong {
            color: #4f3f14;
        }
        .wpp-btn {
            width: 100%;
            border: 0;
            border-radius: 4px;
            padding: 10px 14px;
            color: #fff;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
        }
        .wpp-btn--status {
            background: #43b649;
        }
        .wpp-btn--status:hover {
            background: #3aa13f;
            color: #fff;
        }
        .wpp-btn--qr {
            background: #00a0e9;
        }
        .wpp-btn--qr:hover {
            background: #0090d1;
            color: #fff;
        }
        .wpp-qr-box {
            min-height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px dashed #dee2e6;
            border-radius: 4px;
            background: #fff;
            padding: 1rem;
        }
        .wpp-card-header--teste {
            background: #f1b41b;
            color: #212529;
            font-weight: 600;
        }
        .wpp-btn--teste {
            background: #f1b41b;
            color: #fff;
        }
        .wpp-btn--teste:hover {
            background: #de9f0f;
            color: #fff;
        }
    </style>

    <div class="row">
        <div class="col-lg-6 mb-3">
            <section class="card h-100">
                <header class="card-header wpp-card-header">
                    <h2 class="card-title mb-0">Status da Conexão</h2>
                </header>
                <div class="card-body">
                    @if (!empty($erros))
                        <div class="alert alert-danger mb-3">
                            @foreach ($erros as $erro)
                                <div>{{ $erro }}</div>
                            @endforeach
                        </div>
                    @endif

                    @php($connected = (bool) data_get($status, 'Connected', false))
                    @if ($connected)
                        <div class="alert alert-success mb-3">
                            <strong>Conectado</strong><br>
                            Sua instância está ativa.
                        </div>
                    @else
                        <div class="wpp-alert">
                            <strong>Desconectado</strong><br>
                            Clique em "Obter QR Code" para conectar.
                        </div>
                    @endif

                    <div class="d-grid gap-2">
                        <form method="get" action="{{ route('admin.notificacoes.configuracao-wpp') }}">
                            <input type="hidden" name="action" value="status">
                            <button type="submit" class="wpp-btn wpp-btn--status">
                                <i class="bx bx-refresh"></i>
                                Verificar Status
                            </button>
                        </form>
                        <form method="get" action="{{ route('admin.notificacoes.configuracao-wpp') }}">
                            <input type="hidden" name="action" value="qr">
                            <button type="submit" class="wpp-btn wpp-btn--qr">
                                <i class="bx bx-qr"></i>
                                Obter QR Code
                            </button>
                        </form>
                    </div>
                </div>
            </section>
        </div>
        <div class="col-lg-6 mb-3">
            <section class="card h-100">
                <header class="card-header">
                    <h2 class="card-title mb-0">QR Code de Conexão</h2>
                </header>
                <div class="card-body text-center">
                    @if ($qrCode)
                        <div class="wpp-qr-box">
                            <img src="{{ $qrCode }}" alt="QR Code da instância" style="max-width: 260px; width: 100%;">
                        </div>
                    @else
                        <div class="wpp-qr-box">
                            <p class="text-muted mb-0">Clique em "Obter QR Code" para conectar.</p>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-3">
            <section class="card">
                <header class="card-header wpp-card-header--teste">
                    <h2 class="card-title mb-0">Enviar Mensagem de Teste</h2>
                </header>
                <div class="card-body">
                    <form method="post" action="{{ route('admin.notificacoes.configuracao-wpp.teste-numero') }}" class="row g-2 align-items-end mb-3">
                        @csrf
                        <div class="col-md-2">
                            <label for="numero_teste" class="form-label">Número (com DDD)</label>
                            <input type="text" class="form-control" id="numero_teste" name="numero_teste" value="{{ old('numero_teste', '61993640457') }}" placeholder="61993640457">
                           
                        </div>
                        <div class="col-md-6">
                            <label for="mensagem_teste" class="form-label">Mensagem</label>
                            <input type="text" class="form-control" id="mensagem_teste" name="mensagem_teste" value="{{ old('mensagem_teste', 'Teste do sistema ADELSS!') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="wpp-btn wpp-btn--teste">
                                <i class="bx bx-send"></i> Enviar
                            </button>
                        </div>
                    </form>

                </div>
            </section>
        </div>
    </div>

@endsection
