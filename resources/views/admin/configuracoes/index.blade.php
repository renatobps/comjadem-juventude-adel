@extends('layouts.admin')

@section('title', 'Configurações')

@section('page_title', 'Configurações de acesso')

@section('breadcrumbs')
    <li><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
    <li><span>Configurações</span></li>
@endsection

@section('content')
    <div class="row mb-3">
        <div class="col-12">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">Metas de inscrições</h2>
                </header>
                <div class="card-body">
                    <form method="post" action="{{ route('admin.configuracoes.metas.store') }}" class="row g-2">
                        @csrf
                        <div class="col-md-4">
                            <label for="meta_total" class="form-label">Meta total (todas as regionais)</label>
                            <input
                                type="number"
                                min="1"
                                step="1"
                                name="meta_total"
                                id="meta_total"
                                class="form-control"
                                value="{{ old('meta_total', $metaTotal) }}"
                                required
                            >
                            @error('meta_total')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="valor_inscricao" class="form-label">Valor da inscrição (R$)</label>
                            <input
                                type="number"
                                min="0"
                                step="0.01"
                                name="valor_inscricao"
                                id="valor_inscricao"
                                class="form-control"
                                value="{{ old('valor_inscricao', number_format($valorInscricao, 2, '.', '')) }}"
                                required
                            >
                            @error('valor_inscricao')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <div class="row">
                                @foreach ($regionais as $regional)
                                    <div class="col-md-4 mb-2">
                                        <label for="meta_regional_{{ $regional->id }}" class="form-label">{{ $regional->nome }}</label>
                                        <input
                                            type="number"
                                            min="0"
                                            step="1"
                                            name="metas_regionais[{{ $regional->id }}]"
                                            id="meta_regional_{{ $regional->id }}"
                                            class="form-control"
                                            value="{{ old("metas_regionais.{$regional->id}", (int) ($metasRegionais[$regional->id] ?? 0)) }}"
                                        >
                                    </div>
                                @endforeach
                            </div>
                            @error('metas_regionais')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Salvar metas</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-lg-6">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">Administradores (acesso total)</h2>
                </header>
                <div class="card-body">
                    <p class="mb-3">Selecione um membro para promover a administrador com acesso total ao sistema.</p>
                    <form method="post" action="{{ route('admin.configuracoes.admins.store') }}" class="row g-2 align-items-end">
                        @csrf
                        <div class="col-md-6">
                            <label for="membro_id_admin" class="form-label">Membro</label>
                            <select name="membro_id_admin" id="membro_id_admin" class="form-control" required>
                                <option value="">Selecione um membro...</option>
                                @foreach ($membros as $membro)
                                    <option value="{{ $membro->id }}" @selected((string) old('membro_id_admin') === (string) $membro->id)>
                                        {{ $membro->nome }} ({{ $membro->email ?: 'sem e-mail' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('membro_id_admin')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 d-grid">
                            <button type="submit" class="btn btn-primary">Tornar administrador</button>
                        </div>
                    </form>
                    <hr>
                    <h6 class="mb-2">Administradores atuais</h6>
                    @if ($admins->isEmpty())
                        <p class="text-muted mb-0">Nenhum administrador cadastrado.</p>
                    @else
                        <ul class="mb-0">
                            @foreach ($admins as $admin)
                                <li>{{ $admin->name }} ({{ $admin->email }})</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </section>
        </div>
        <div class="col-lg-6">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">Atribuição de acesso por perfil</h2>
                </header>
                <div class="card-body">
                    <p class="mb-3">
                        Selecione um membro e uma regional para atribuir o acesso.
                        Quando o usuário (mesmo e-mail do membro) fizer login, verá apenas igrejas e inscrições das regionais atribuídas.
                    </p>
                    <form method="post" action="{{ route('admin.configuracoes.acessos.store') }}" class="row g-2 align-items-end">
                        @csrf
                        <div class="col-12">
                            <label for="membro_id" class="form-label">Membro</label>
                            <select name="membro_id" id="membro_id" class="form-control" required>
                                <option value="">Selecione um membro...</option>
                                @foreach ($membros as $membro)
                                    <option value="{{ $membro->id }}" @selected((string) old('membro_id') === (string) $membro->id)>
                                        {{ $membro->nome }} ({{ $membro->email ?: 'sem e-mail' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('membro_id')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="regional_ids" class="form-label">Regionais (pode selecionar mais de uma)</label>
                            <select name="regional_ids[]" id="regional_ids" class="form-control" multiple required size="6">
                                @foreach ($regionais as $regional)
                                    <option value="{{ $regional->id }}" @selected(collect(old('regional_ids', []))->contains((string) $regional->id) || collect(old('regional_ids', []))->contains($regional->id))>
                                        {{ $regional->nome }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Use Ctrl (ou Cmd) para selecionar múltiplas regionais.</small>
                            @error('regional_ids')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                            @error('regional_ids.*')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12 d-grid d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">Atribuir acesso</button>
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
                    <h2 class="card-title">Acessos atribuídos</h2>
                </header>
                <div class="card-body">
                    @php($membrosComAcesso = $membros->filter(fn ($membro) => $membro->acessosRegionais->isNotEmpty()))
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Membro</th>
                                    <th>E-mail</th>
                                    <th>Cargo</th>
                                    <th>Regional de acesso</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($membrosComAcesso as $membro)
                                    <tr>
                                        <td>{{ $membro->nome }}</td>
                                        <td>{{ $membro->email }}</td>
                                        <td>{{ $membro->cargo?->nome }}</td>
                                        <td>{{ $membro->acessosRegionais->pluck('regional.nome')->filter()->implode(', ') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Nenhum acesso atribuído.</td>
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
