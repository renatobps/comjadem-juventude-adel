@extends('layouts.admin')

@section('title', 'Nova igreja')

@section('page_title', 'Nova igreja')

@section('breadcrumbs')
    <li><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
    <li><a href="{{ route('admin.igrejas.index') }}">Igrejas</a></li>
    <li><span>Nova</span></li>
@endsection

@section('content')
    @if ($regionais->isEmpty())
        <div class="alert alert-warning">
            Não há regionais cadastradas. <a href="{{ route('admin.regionais.create') }}">Cadastre uma regional</a> primeiro.
        </div>
        <a href="{{ route('admin.igrejas.index') }}" class="btn btn-default">Voltar</a>
    @elseif ($membros->isEmpty())
        <div class="alert alert-warning">
            Não há membros cadastrados para selecionar como dirigente.
            <a href="{{ route('admin.membros.create') }}">Cadastre um membro</a> primeiro.
        </div>
        <a href="{{ route('admin.igrejas.index') }}" class="btn btn-default">Voltar</a>
    @else
        <div class="row">
            <div class="col-lg-8">
                <section class="card">
                    <header class="card-header">
                        <h2 class="card-title">Dados da igreja</h2>
                    </header>
                    <div class="card-body">
                        <form method="post" action="{{ route('admin.igrejas.store') }}">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="bairro">Bairro da igreja <span class="text-danger">*</span></label>
                                <input type="text" name="bairro" id="bairro" class="form-control" value="{{ old('bairro') }}" required maxlength="255" placeholder="Ex.: Setor Sul">
                                @error('bairro')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="dirigente_membro_id">Dirigente <span class="text-danger">*</span></label>
                                <select name="dirigente_membro_id" id="dirigente_membro_id" class="form-control" required>
                                    <option value="">Selecione o dirigente…</option>
                                    @foreach ($membros as $membro)
                                        <option value="{{ $membro->id }}" @selected((string) old('dirigente_membro_id') === (string) $membro->id)>
                                            {{ $membro->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('dirigente_membro_id')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="regional_id">Regional <span class="text-danger">*</span></label>
                                <select name="regional_id" id="regional_id" class="form-control" required>
                                    <option value="">Selecione a regional…</option>
                                    @foreach ($regionais as $regional)
                                        <option value="{{ $regional->id }}" @selected((string) old('regional_id') === (string) $regional->id)>
                                            {{ $regional->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('regional_id')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Salvar</button>
                                <a href="{{ route('admin.igrejas.index') }}" class="btn btn-default">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    @endif
@endsection
