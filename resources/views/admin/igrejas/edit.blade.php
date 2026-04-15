@extends('layouts.admin')

@section('title', 'Editar igreja')

@section('page_title', 'Editar igreja')

@section('breadcrumbs')
    <li><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
    <li><a href="{{ route('admin.igrejas.index') }}">Igrejas</a></li>
    <li><span>Editar</span></li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">{{ $igreja->bairro }}</h2>
                </header>
                <div class="card-body">
                    <form method="post" action="{{ route('admin.igrejas.update', $igreja) }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group mb-3">
                            <label for="bairro">Bairro da igreja <span class="text-danger">*</span></label>
                            <input type="text" name="bairro" id="bairro" class="form-control" value="{{ old('bairro', $igreja->bairro) }}" required maxlength="255">
                            @error('bairro')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="dirigente_membro_id">Dirigente <span class="text-danger">*</span></label>
                            <select name="dirigente_membro_id" id="dirigente_membro_id" class="form-control" required>
                                <option value="">Selecione o dirigente…</option>
                                @foreach ($membros as $membro)
                                    <option value="{{ $membro->id }}" @selected((string) old('dirigente_membro_id', $igreja->dirigente_membro_id) === (string) $membro->id)>
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
                                @foreach ($regionais as $regional)
                                    <option value="{{ $regional->id }}" @selected((string) old('regional_id', $igreja->regional_id) === (string) $regional->id)>
                                        {{ $regional->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('regional_id')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Atualizar</button>
                            <a href="{{ route('admin.igrejas.index') }}" class="btn btn-default">Voltar</a>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
@endsection
