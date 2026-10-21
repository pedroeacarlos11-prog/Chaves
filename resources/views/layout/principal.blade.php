<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#171717">
    <title>@yield('title', 'Controle de Chaves | SENAI')</title>
    <link rel="icon" type="chave.png" href="{{ asset('chave.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('painel.css') . '?v=' . filemtime(public_path('painel.css')) }}">
</head>
<body>
    @php
        $page = $page ?? 'inicio';
        $titles = [
            'inicio' => 'Painel de Controle',
            'agenda' => 'Agenda de Retirada',
            'emprestimos' => 'Gerenciamento de Empréstimos',
            'funcionarios' => 'Cadastro de Funcionários',
            'chaves' => 'Controle de Chaves',
        ];
    @endphp

    <div class="app-shell">
        <aside class="sidebar offcanvas-lg offcanvas-start bg-dark text-white" tabindex="-1" id="menuLateral" aria-labelledby="menuLateralTitulo">
            <div class="offcanvas-header d-lg-none">
                <h2 class="visually-hidden" id="menuLateralTitulo">Menu principal</h2>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="offcanvas" data-bs-target="#menuLateral" aria-label="Fechar menu"></button>
            </div>
            <div class="sidebar-body">
                <a class="brand" href="{{ route('inicio') }}" aria-label="SENAI Controle de Chaves, início">
                    <span class="brand-mark">SENAI</span>
                    <span class="brand-name">Controle de<br>Chaves</span>
                </a>

                <div class="sidebar-caption">MENU PRINCIPAL</div>
                <nav class="nav flex-column sidebar-nav" aria-label="Navegação principal">
                    <a class="nav-link {{ $page === 'inicio' ? 'active' : '' }}" href="{{ route('inicio') }}"><i class="bi bi-house-door"></i><span>Início</span></a>
                    <a class="nav-link {{ $page === 'agenda' ? 'active' : '' }}" href="{{ route('agenda') }}"><i class="bi bi-calendar3"></i><span>Agenda</span></a>
                    <a class="nav-link {{ $page === 'emprestimos' ? 'active' : '' }}" href="{{ route('emprestimos') }}"><i class="bi bi-clipboard-check"></i><span>Empréstimos</span></a>
                    <a class="nav-link {{ $page === 'funcionarios' ? 'active' : '' }}" href="{{ route('funcionarios') }}"><i class="bi bi-people"></i><span>Funcionários</span></a>
                    <a class="nav-link {{ $page === 'chaves' ? 'active' : '' }}" href="{{ route('chaves') }}"><i class="bi bi-key"></i><span>Chaves</span></a>
                </nav>

                <div class="sidebar-footer">
                    <span class="unit-dot"></span>
                    <div><strong>Unidade CE-024</strong><span>Tatuí · SP</span></div>
                </div>
            </div>
        </aside>

        <div class="app-main">
            <header class="topbar">
                <button class="btn menu-toggle d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuLateral" aria-controls="menuLateral" aria-label="Abrir menu">
                    <i class="bi bi-list"></i>
                </button>
                <h1 class="topbar-title">{{ $titles[$page] ?? $titles['inicio'] }}</h1>
                <div class="topbar-account">
                    <div class="dropdown">
                        <button class="notification-button btn" id="notificacoesDropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Abrir notificações">
                                <i class="bi bi-bell"></i>@if ($stats['emprestimos_atrasados'] > 0)<span class="notification-dot"></span>@endif
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-3 shadow" aria-labelledby="notificacoesDropdown">
                            <div class="dropdown-header d-flex justify-content-between align-items-center px-0">
                                <strong class="text-dark">Empréstimos atrasados</strong>
                                <span class="badge rounded-pill text-bg-danger">{{ $stats['emprestimos_atrasados'] }}</span>
                            </div>
                            <div class="dropdown-divider"></div>
                            <p class="small text-body-secondary mb-2">{{ $stats['emprestimos_atrasados'] > 0 ? 'Consulte os registros marcados como atrasados.' : 'Não há empréstimos marcados como atrasados.' }}</p>
                            <a class="btn btn-primary btn-sm w-100 mt-2" href="{{ route('emprestimos') }}">Ver empréstimos</a>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn d-flex align-items-center gap-3 p-0 border-0 bg-transparent text-start" id="perfilDropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Abrir opções do perfil">
                            @if (auth()->user()->profile_photo_path)
                                <img class="avatar object-fit-cover" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url(auth()->user()->profile_photo_path) }}" alt="Foto de {{ auth()->user()->name }}">
                            @else
                                <span class="avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                            @endif
                            <span class="account-copy"><strong>{{ auth()->user()->name }}</strong><span>{{ auth()->user()->email }}</span></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="perfilDropdown">
                            <form id="profilePhotoForm" method="POST" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data">
                                @csrf
                                <input class="visually-hidden" type="file" id="profilePhotoInput" name="foto" accept="image/jpeg,image/png,image/webp">
                                <label class="dropdown-item d-flex align-items-center gap-2" for="profilePhotoInput"><i class="bi bi-camera"></i>Alterar foto</label>
                            </form>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item d-flex align-items-center gap-2 text-danger" type="submit"><i class="bi bi-box-arrow-right"></i>Sair</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="main-content">
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                    </div>
                @endif
                @error('foto')
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                    </div>
                @enderror
                @yield('content')
            </main>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="feedbackToast" class="toast align-items-center text-bg-dark border-0" role="status" aria-live="polite" aria-atomic="true">
            <div class="d-flex"><div class="toast-body" id="feedbackToastText">Tudo certo.</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fechar"></button></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="{{ asset('painel.js') . '?v=' . filemtime(public_path('painel.js')) }}"></script>
</body>
</html>
