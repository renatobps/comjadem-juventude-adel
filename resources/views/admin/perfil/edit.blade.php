@extends('layouts.admin')

@section('title', 'Meu perfil')

@section('page_title', 'Meu perfil')

@section('breadcrumbs')
    <li><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
    <li><span>Meu perfil</span></li>
@endsection

@section('content')
    @php($porto = asset('porto-admin'))
    @php($fotoPadrao = $porto . '/img/!logged-user.jpg')
    @php($caminhoFoto = $membro->foto)
    @php($fotoPerfil = $caminhoFoto && \Illuminate\Support\Facades\Storage::disk('public')->exists($caminhoFoto) ? asset('storage/' . $caminhoFoto) : $fotoPadrao)

    <div class="row">
        <div class="col-lg-8">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">Atualizar perfil</h2>
                </header>
                <div class="card-body">
                    <form method="post" action="{{ route('admin.perfil.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label class="form-label d-block">Foto atual</label>
                            <img src="{{ $fotoPerfil }}" alt="Foto de perfil" class="rounded-circle border" width="84" height="84" style="object-fit: cover;" onerror="this.onerror=null;this.src='{{ $fotoPadrao }}';">
                        </div>

                        <div class="form-group mb-3">
                            <label for="foto">Nova foto</label>
                            <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
                            <small class="text-muted">Formatos: JPG, PNG, WEBP. Tamanho máximo: 2MB.</small>
                            @error('foto')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

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
                            <label for="telefone">Telefone <span class="text-danger">*</span></label>
                            <input type="text" name="telefone" id="telefone" class="form-control" value="{{ old('telefone', $membro->telefone) }}" required maxlength="15" inputmode="numeric" placeholder="(99) 99999-9999">
                            @error('telefone')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="senha">Nova senha</label>
                                    <input type="password" name="senha" id="senha" class="form-control" minlength="6" maxlength="255">
                                    <small class="text-muted">Preencha apenas se quiser alterar a senha.</small>
                                    @error('senha')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="senha_confirmation">Confirmar nova senha</label>
                                    <input type="password" name="senha_confirmation" id="senha_confirmation" class="form-control" minlength="6" maxlength="255">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Salvar alterações</button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-default">Voltar</a>
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
