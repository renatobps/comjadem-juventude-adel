@extends('layouts.admin')

@section('title', 'Editar pré-inscrição')

@section('page_title', 'Editar pré-inscrição')

@section('breadcrumbs')
    <li><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
    <li><a href="{{ route('admin.dashboard') }}">Pré-inscrições</a></li>
    <li><span>Editar</span></li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">{{ $inscricao->nome }}</h2>
                </header>
                <div class="card-body">
                    <form method="post" action="{{ route('admin.inscricoes.update', $inscricao) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="nome">Nome <span class="text-danger">*</span></label>
                            <input type="text" name="nome" id="nome" class="form-control" value="{{ old('nome', $inscricao->nome) }}" required maxlength="255">
                            @error('nome')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="idade">Idade <span class="text-danger">*</span></label>
                            <input type="number" name="idade" id="idade" class="form-control" value="{{ old('idade', $inscricao->idade) }}" min="10" max="120" required>
                            @error('idade')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="whatsapp">WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" name="whatsapp" id="whatsapp" class="form-control" value="{{ old('whatsapp', $inscricao->whatsapp) }}" required maxlength="40">
                            @error('whatsapp')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="tamanho_camiseta">Tamanho da camiseta <span class="text-danger">*</span></label>
                            <select name="tamanho_camiseta" id="tamanho_camiseta" class="form-control" required>
                                <option value="">Selecione...</option>
                                @foreach (\App\Models\PreInscricao::tamanhoCamisetaOptions() as $value => $label)
                                    <option value="{{ $value }}" @selected(old('tamanho_camiseta', $inscricao->tamanho_camiseta) === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tamanho_camiseta')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="igreja_id">Igreja</label>
                            <select name="igreja_id" id="igreja_id" class="form-control">
                                <option value="">Selecione...</option>
                                @foreach ($igrejas as $igreja)
                                    <option value="{{ $igreja->id }}" @selected((string) old('igreja_id', $inscricao->igreja_id) === (string) $igreja->id)>
                                        {{ $igreja->nomeNoFormulario() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('igreja_id')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="lider_jovens">É líder de jovens? <span class="text-danger">*</span></label>
                            <select name="lider_jovens" id="lider_jovens" class="form-control" required>
                                <option value="1" @selected((string) old('lider_jovens', (int) $inscricao->lider_jovens) === '1')>Sim</option>
                                <option value="0" @selected((string) old('lider_jovens', (int) $inscricao->lider_jovens) === '0')>Não</option>
                            </select>
                            @error('lider_jovens')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control" required>
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', $inscricao->status ?: \App\Models\PreInscricao::STATUS_AGUARDANDO) === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Salvar alterações</button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-default">Cancelar</a>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
@endsection
