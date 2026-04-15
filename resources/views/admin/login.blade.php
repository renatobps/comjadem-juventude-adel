@extends('layouts.admin')

@section('title', 'Login administrativo')

@section('content')
    <a href="{{ url('/') }}" class="logo float-start">
        <img src="/logoComjadem.png" height="70" alt="{{ config('app.name') }}">
    </a>
    <div class="panel card-sign">
        <div class="card-title-sign mt-3 text-end">
            <h2 class="title text-uppercase font-weight-bold m-0">
                <i class="bx bx-user-circle me-1 text-6 position-relative top-5"></i> Área administrativa
            </h2>
        </div>
        <div class="card-body">
            <p class="text-muted">Entre com o e-mail e a senha do administrador.</p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $e)
                        <div>{{ $e }}</div>
                    @endforeach
                </div>
            @endif

            <form method="post" action="{{ route('admin.login.perform') }}">
                @csrf
                <div class="form-group mb-3">
                    <label for="email">E-mail</label>
                    <div class="input-group">
                        <input id="email" name="email" type="email" class="form-control form-control-lg" value="{{ old('email') }}" required autocomplete="username">
                        <span class="input-group-text">
                            <i class="bx bx-user text-4"></i>
                        </span>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label for="password">Senha</label>
                    <div class="input-group">
                        <input id="password" name="password" type="password" class="form-control form-control-lg" required autocomplete="current-password">
                        <span class="input-group-text">
                            <i class="bx bx-lock text-4"></i>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-8">
                        <div class="checkbox-custom checkbox-default">
                            <input id="remember" name="remember" type="checkbox" value="1">
                            <label for="remember">Lembrar-me</label>
                        </div>
                    </div>
                    <div class="col-sm-4 text-end">
                        <button type="submit" class="btn btn-primary mt-2">Entrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <p class="text-center text-muted mt-3 mb-3">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
@endsection
