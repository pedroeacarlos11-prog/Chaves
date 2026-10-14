@extends('layout.autenticacao')

@section('title', 'Entrar | Controle de Chaves')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4 p-md-5">
            <div class="mb-4">
                <h1 class="h3 mb-2">Entrar</h1>
                <p class="text-body-secondary mb-0">Acesse sua conta para continuar.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">Confira o e-mail e a senha informados.</div>
            @endif

            <form method="POST" action="{{ route('login.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="email">E-mail</label>
                    <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Senha</label>
                    <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" autocomplete="current-password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" id="remember" name="remember" type="checkbox" value="1">
                    <label class="form-check-label" for="remember">Manter conectado</label>
                </div>
                <button class="btn btn-danger w-100" type="submit">Entrar</button>
            </form>

            <p class="text-center text-body-secondary mt-4 mb-0">
                Ainda não tem conta? <a href="{{ route('register') }}">Cadastre-se</a>
            </p>
        </div>
    </div>
@endsection
