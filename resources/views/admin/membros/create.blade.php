@extends('layouts.admin')

@section('title', 'Novo membro')

@section('page_title', 'Novo membro')

@section('breadcrumbs')
    <li><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
    <li><a href="{{ route('admin.membros.index') }}">Membros</a></li>
    <li><span>Novo</span></li>
@endsection

@section('content')
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
                            <label for="cargo_id">Cargo</label>
                            <select name="cargo_id" id="cargo_id" class="form-control">
                                <option value="">Sem cargo</option>
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
                            <label for="telefone">Telefone <span class="text-danger">*</span></label>
                            <input type="text" name="telefone" id="telefone" class="form-control" value="{{ old('telefone') }}" required maxlength="15" inputmode="numeric" placeholder="(99) 99999-9999">
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
@endsection

@push('scripts')
    <script>
        (function() {
            'use strict';
            var input = document.getElementById('telefone');
            if (!input) return;

            var formatPhone = function(value) {
                var digits = (value || '').replace(/\D/g, '').slice(0, 11);
                if (!digits) return '';
                if (digits.length <= 2) return '(' + digits;
                if (digits.length <= 7) return '(' + digits.slice(0, 2) + ') ' + digits.slice(2);
                return '(' + digits.slice(0, 2) + ') ' + digits.slice(2, 7) + '-' + digits.slice(7);
            };

            input.addEventListener('input', function(e) {
                e.target.value = formatPhone(e.target.value);
            });

            input.value = formatPhone(input.value);
        })();
    </script>
@endpush
