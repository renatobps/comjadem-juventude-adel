@extends('layouts.admin')

@section('title', 'Nova regional')

@section('page_title', 'Nova regional')

@section('breadcrumbs')
    <li><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
    <li><a href="{{ route('admin.regionais.index') }}">Regionais</a></li>
    <li><span>Nova</span></li>
@endsection

@section('content')
    @if ($membros->isEmpty())
        <div class="alert alert-warning">
            Não há membros cadastrados para selecionar como pastor responsável.
            <a href="{{ route('admin.membros.create') }}">Cadastre um membro</a> primeiro.
        </div>
        <a href="{{ route('admin.regionais.index') }}" class="btn btn-default">Voltar</a>
    @else
        <div class="row">
            <div class="col-lg-8">
                <section class="card">
                    <header class="card-header">
                        <h2 class="card-title">Dados da regional</h2>
                    </header>
                    <div class="card-body">
                        <form method="post" action="{{ route('admin.regionais.store') }}">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="nome">Nome da regional <span class="text-danger">*</span></label>
                                <input type="text" name="nome" id="nome" class="form-control" value="{{ old('nome') }}" required maxlength="255" placeholder="Ex.: Regional Centro">
                                @error('nome')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="pastor_membro_id">Pastor responsável <span class="text-danger">*</span></label>
                                <select name="pastor_membro_id" id="pastor_membro_id" class="form-control" required>
                                    <option value="">Selecione o pastor…</option>
                                    @foreach ($membros as $membro)
                                        <option value="{{ $membro->id }}" @selected((string) old('pastor_membro_id') === (string) $membro->id)>
                                            {{ $membro->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('pastor_membro_id')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Salvar</button>
                                <a href="{{ route('admin.regionais.index') }}" class="btn btn-default">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    @endif
@endsection
