@extends('layouts.admin')

@section('title', 'Editar membro')

@section('page_title', 'Editar membro')

@section('breadcrumbs')
    <li><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
    <li><a href="{{ route('admin.membros.index') }}">Membros</a></li>
    <li><span>Editar</span></li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">{{ $membro->nome }}</h2>
                </header>
                <div class="card-body">
                    <form method="post" action="{{ route('admin.membros.update', $membro) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group mb-3">
                            <label for="nome">Nome <span class="text-danger">*</span></label>
                            <input type="text" name="nome" id="nome" class="form-control" value="{{ old('nome', $membro->nome) }}" required maxlength="255">
                            @error('nome')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $membro->email) }}" required maxlength="255">
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
                            @if ($membro->foto)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $membro->foto) }}" alt="Foto de {{ $membro->nome }}" style="max-width: 120px; border-radius: 6px;">
                                </div>
                            @endif
                            <small class="text-muted">Formatos: JPG, PNG, WEBP. Tamanho máximo: 2MB.</small>
                            @error('foto')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="cargo_id">Cargo <span class="text-danger">*</span></label>
                            <select name="cargo_id" id="cargo_id" class="form-control" required>
                                @foreach ($cargos as $cargo)
                                    <option value="{{ $cargo->id }}" @selected((string) old('cargo_id', $membro->cargo_id) === (string) $cargo->id)>
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
                            <input type="text" name="telefone" id="telefone" class="form-control" value="{{ old('telefone', $membro->telefone) }}" maxlength="40">
                            @error('telefone')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Atualizar</button>
                            <a href="{{ route('admin.membros.index') }}" class="btn btn-default">Voltar</a>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
@endsection
