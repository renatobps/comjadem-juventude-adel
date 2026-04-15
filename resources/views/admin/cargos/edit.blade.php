@extends('layouts.admin')

@section('title', 'Editar cargo')

@section('page_title', 'Editar cargo')

@section('breadcrumbs')
    <li><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
    <li><a href="{{ route('admin.cargos.index') }}">Membros / Cargos</a></li>
    <li><span>Editar</span></li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">{{ $cargo->nome }}</h2>
                </header>
                <div class="card-body">
                    <form method="post" action="{{ route('admin.cargos.update', $cargo) }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group mb-3">
                            <label for="nome">Nome do cargo <span class="text-danger">*</span></label>
                            <input type="text" name="nome" id="nome" class="form-control" value="{{ old('nome', $cargo->nome) }}" required maxlength="255">
                            @error('nome')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Atualizar</button>
                            <a href="{{ route('admin.cargos.index') }}" class="btn btn-default">Voltar</a>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
@endsection
