@extends('layouts.admin')

@section('title', 'Nova regional')

@section('page_title', 'Nova regional')

@section('breadcrumbs')
    <li><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
    <li><a href="{{ route('admin.regionais.index') }}">Regionais</a></li>
    <li><span>Nova</span></li>
@endsection

@section('content')
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
                            <label for="pastor_responsavel">Pastor responsável <span class="text-danger">*</span></label>
                            <input type="text" name="pastor_responsavel" id="pastor_responsavel" class="form-control" value="{{ old('pastor_responsavel') }}" required maxlength="255" placeholder="Nome completo do pastor">
                            @error('pastor_responsavel')
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
@endsection
