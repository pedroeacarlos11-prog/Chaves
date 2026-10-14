@extends('layout.autenticacao')

@section('title', 'Criar conta | Controle de Chaves')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4 p-md-5">
            <div class="mb-4">
                <h1 class="h3 mb-2">Criar conta</h1>
                <p class="text-body-secondary mb-0">Preencha seus dados para entrar no sistema.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">Revise os campos destacados e tente novamente.</div>
            @endif

            <form method="POST" action="{{ route('register.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="name">Nome</label>
                    <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" maxlength="255" required autofocus>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="email">E-mail</label>
                    <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" maxlength="255" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Senha</label>
                    <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" autocomplete="new-password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Use pelo menos 8 caracteres.</div>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="password_confirmation">Confirmar senha</label>
                    <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
                </div>
                <button class="btn btn-danger w-100" type="submit">Criar conta</button>
            </form>

            <p class="text-center text-body-secondary mt-4 mb-0">
                Já tem uma conta? <a href="{{ route('login') }}">Entrar</a>
            </p>
        </div>
    </div>
@endsection
