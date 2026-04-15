@extends('layouts.admin')

@section('title', 'Novo membro')

@section('page_title', 'Novo membro')

@section('breadcrumbs')
    <li><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
    <li><a href="{{ route('admin.membros.index') }}">Membros</a></li>
    <li><span>Novo</span></li>
@endsection

@section('content')
    @if ($cargos->isEmpty())
        <div class="alert alert-warning">
            Não há cargos cadastrados. <a href="{{ route('admin.cargos.create') }}">Cadastre um cargo</a> primeiro.
        </div>
        <a href="{{ route('admin.membros.index') }}" class="btn btn-default">Voltar</a>
    @else
        <div class="row">
            <div class="col-lg-8">
                <section class="card">
                    <header class="card-header">
                        <h2 class="card-title">Dados do membro</h2>
                    </header>
                    <div class="card-body">
                        <form method="post" action="{{ route('admin.membros.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="nome">Nome <span class="text-danger">*</span></label>
                                <input type="text" name="nome" id="nome" class="form-control" value="{{ old('nome') }}" required maxlength="255">
                                @error('nome')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required maxlength="255" placeholder="exemplo@email.com">
                                @error('email')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="senha">Senha <span class="text-danger">*</span></label>
                                <input type="password" name="senha" id="senha" class="form-control" required minlength="6" maxlength="255">
                                @error('senha')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="foto">Foto do membro</label>
                                <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
                                <small class="text-muted">Formatos: JPG, PNG, WEBP. Tamanho máximo: 2MB.</small>
                                @error('foto')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="cargo_id">Cargo <span class="text-danger">*</span></label>
                                <select name="cargo_id" id="cargo_id" class="form-control" required>
                                    <option value="">Selecione o cargo…</option>
                                    @foreach ($cargos as $cargo)
                                        <option value="{{ $cargo->id }}" @selected((string) old('cargo_id') === (string) $cargo->id)>
                                            {{ $cargo->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cargo_id')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="telefone">Telefone</label>
                                <input type="text" name="telefone" id="telefone" class="form-control" value="{{ old('telefone') }}" maxlength="40" placeholder="Ex.: 61999998888">
                                @error('telefone')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Salvar</button>
                                <a href="{{ route('admin.membros.index') }}" class="btn btn-default">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    @endif
@endsection
