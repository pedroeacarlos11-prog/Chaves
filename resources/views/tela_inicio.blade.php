@extends('layout.principal')

@section('title', 'Controle de Chaves | SENAI')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>Revise os campos do formulário.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($page === 'inicio')
        <section class="welcome-panel">
            <div class="welcome-copy">
                <span class="eyebrow"><span class="eyebrow-line"></span> SENAI · UNIDADE CE-024</span>
                <h2>Controle de chaves e empréstimos</h2>
                <p>Consulte os registros e acompanhe a disponibilidade das chaves.</p>
                <a class="btn btn-senai mt-2" href="{{ route('emprestimos') }}"><i class="bi bi-arrow-right me-2"></i>Ver empréstimos</a>
            </div>
            <div class="welcome-art" aria-hidden="true">
                <div class="art-orbit orbit-one"></div><div class="art-orbit orbit-two"></div>
                <div class="art-key"><i class="bi bi-key-fill"></i></div>
                <span class="art-tag tag-top">REGISTROS DO SISTEMA</span>
                <span class="art-tag tag-bottom"><i class="bi bi-database-check me-2"></i>Dados atualizados</span>
            </div>
        </section>

        <section class="stat-grid mt-4" aria-label="Resumo do sistema">
            <article class="stat-card"><span class="stat-icon stat-icon-red"><i class="bi bi-key"></i></span><div><span>Chaves cadastradas</span><strong>{{ $stats['chaves'] }}</strong></div></article>
            <article class="stat-card"><span class="stat-icon stat-icon-green"><i class="bi bi-check2-circle"></i></span><div><span>Chaves disponíveis</span><strong>{{ $stats['chaves_disponiveis'] }}</strong></div></article>
            <article class="stat-card"><span class="stat-icon stat-icon-amber"><i class="bi bi-arrow-left-right"></i></span><div><span>Empréstimos em andamento</span><strong>{{ $stats['emprestimos_andamento'] }}</strong></div></article>
        </section>

        <section class="content-panel mt-4">
            <div class="toolbar justify-content-between">
                <div><span class="eyebrow">MOVIMENTAÇÕES</span><h2 class="h5 mb-0 mt-1">Empréstimos recentes</h2></div>
                <a class="btn btn-light-outline" href="{{ route('emprestimos') }}">Ver todos</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle data-table mb-0">
                    <thead><tr><th>Chave</th><th>Funcionário</th><th>Retirada</th><th>Situação</th></tr></thead>
                    <tbody>
                        @forelse ($recentEmprestimos as $emprestimo)
                            <tr>
                                <td><strong>{{ $emprestimo->chave?->nome_chave ?? 'Chave removida' }}</strong><span class="cell-subtitle">{{ $emprestimo->chave?->localizacao ?? '—' }}</span></td>
                                <td>{{ $emprestimo->funcionario?->nome ?? 'Funcionário removido' }}</td>
                                <td>{{ $emprestimo->data_retirada ? \Illuminate\Support\Carbon::parse($emprestimo->data_retirada)->format('d/m/Y') : '—' }} · {{ $emprestimo->hora_retirada ? substr((string) $emprestimo->hora_retirada, 0, 5) : '—' }}</td>
                                <td><span class="status-pill {{ $emprestimo->status === 'Devolvido' ? 'status-returned' : ($emprestimo->status === 'Atrasado' ? 'status-pending' : 'status-active') }}"><span></span>{{ $emprestimo->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-body-secondary py-4">Ainda não há empréstimos registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @elseif ($page === 'funcionarios')
        <section class="page-intro">
            <div><span class="eyebrow">CADASTROS</span><h2>Funcionários</h2><p>Cadastre as pessoas que podem retirar chaves.</p></div>
            <button class="btn btn-senai" type="button" data-create-resource="funcionario" data-bs-toggle="modal" data-bs-target="#modalFuncionario"><i class="bi bi-plus-lg me-2"></i>Novo funcionário</button>
        </section>

        <section class="content-panel">
            <form class="toolbar" method="GET" action="{{ route('funcionarios') }}">
                <div class="search-field"><i class="bi bi-search"></i><input class="form-control" type="search" name="busca" value="{{ request('busca') }}" placeholder="Buscar nome, e-mail ou cargo" aria-label="Buscar funcionários"></div>
                <button class="btn btn-senai" type="submit">Buscar</button>
                @if (request('busca'))<a class="btn btn-light-outline" href="{{ route('funcionarios') }}">Limpar</a>@endif
            </form>
            <div class="table-responsive">
                <table class="table align-middle data-table mb-0">
                    <thead><tr><th>Nome</th><th>E-mail</th><th>Cargo</th><th class="text-end">Ações</th></tr></thead>
                    <tbody>
                        @forelse ($funcionarios as $funcionario)
                            <tr>
                                <td><strong>{{ $funcionario->nome }}</strong></td>
                                <td>{{ $funcionario->email }}</td>
                                <td>{{ $funcionario->cargo ?: '—' }}</td>
                                <td class="text-end text-nowrap">
                                    <button class="icon-button" type="button" aria-label="Ver {{ $funcionario->nome }}" data-view-resource="funcionario" data-detail-title="{{ $funcionario->nome }}" data-detail-nome="{{ $funcionario->nome }}" data-detail-email="{{ $funcionario->email }}" data-detail-cargo="{{ $funcionario->cargo ?: '—' }}" data-bs-toggle="modal" data-bs-target="#modalDetalhes"><i class="bi bi-eye"></i></button>
                                    <button class="icon-button" type="button" aria-label="Editar {{ $funcionario->nome }}" data-edit-resource="funcionario" data-id="{{ $funcionario->id }}" data-update-url="{{ route('painel.funcionarios.update', $funcionario) }}" data-nome="{{ $funcionario->nome }}" data-email="{{ $funcionario->email }}" data-cargo="{{ $funcionario->cargo }}" data-bs-toggle="modal" data-bs-target="#modalFuncionario"><i class="bi bi-pencil"></i></button>
                                    <button class="icon-button" type="button" aria-label="Excluir {{ $funcionario->nome }}" data-delete-form="#excluirFuncionario{{ $funcionario->id }}" data-confirm-message="Excluir o cadastro de {{ $funcionario->nome }}?" data-bs-toggle="modal" data-bs-target="#modalConfirmacao"><i class="bi bi-trash"></i></button>
                                    <form id="excluirFuncionario{{ $funcionario->id }}" class="d-none" method="POST" action="{{ route('painel.funcionarios.destroy', $funcionario) }}">@csrf @method('DELETE')</form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-body-secondary py-4">Nenhum funcionário encontrado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($funcionarios->hasPages())<div class="table-foot"><span>{{ $funcionarios->firstItem() }}–{{ $funcionarios->lastItem() }} de {{ $funcionarios->total() }} funcionários</span>{{ $funcionarios->onEachSide(1)->links('pagination::bootstrap-5') }}</div>@endif
        </section>
    @elseif ($page === 'chaves')
        <section class="page-intro">
            <div><span class="eyebrow">PATRIMÔNIO</span><h2>Chaves</h2><p>Consulte a localização e a disponibilidade das chaves cadastradas.</p></div>
            <button class="btn btn-senai" type="button" data-create-resource="chave" data-bs-toggle="modal" data-bs-target="#modalChave"><i class="bi bi-plus-lg me-2"></i>Nova chave</button>
        </section>

        <section class="content-panel">
            <form class="toolbar" method="GET" action="{{ route('chaves') }}">
                <div class="search-field"><i class="bi bi-search"></i><input class="form-control" type="search" name="busca" value="{{ request('busca') }}" placeholder="Buscar chave ou localização" aria-label="Buscar chaves"></div>
                <select class="form-select filter-select" name="status" aria-label="Filtrar por situação">
                    <option value="">Todas as situações</option>
                    @foreach (['Disponível', 'Emprestada', 'Indisponível'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                <button class="btn btn-senai" type="submit">Filtrar</button>
                @if (request('busca') || request('status'))<a class="btn btn-light-outline" href="{{ route('chaves') }}">Limpar</a>@endif
            </form>
            <div class="table-responsive">
                <table class="table align-middle data-table mb-0">
                    <thead><tr><th>Chave</th><th>Localização</th><th>Situação</th><th class="text-end">Ações</th></tr></thead>
                    <tbody>
                        @forelse ($chaves as $chave)
                            <tr>
                                <td><strong>{{ $chave->nome_chave }}</strong></td>
                                <td>{{ $chave->localizacao ?: '—' }}</td>
                                <td><span class="status-pill {{ $chave->status === 'Disponível' ? 'status-active' : ($chave->status === 'Emprestada' ? 'status-pending' : 'status-inactive') }}"><span></span>{{ $chave->status }}</span></td>
                                <td class="text-end text-nowrap">
                                    <button class="icon-button" type="button" aria-label="Ver {{ $chave->nome_chave }}" data-view-resource="chave" data-detail-title="{{ $chave->nome_chave }}" data-detail-nome-chave="{{ $chave->nome_chave }}" data-detail-localizacao="{{ $chave->localizacao ?: '—' }}" data-detail-status="{{ $chave->status }}" data-bs-toggle="modal" data-bs-target="#modalDetalhes"><i class="bi bi-eye"></i></button>
                                    <button class="icon-button" type="button" aria-label="Editar {{ $chave->nome_chave }}" data-edit-resource="chave" data-id="{{ $chave->id }}" data-update-url="{{ route('painel.chaves.update', $chave) }}" data-nome-chave="{{ $chave->nome_chave }}" data-localizacao="{{ $chave->localizacao }}" data-status="{{ $chave->status }}" data-bs-toggle="modal" data-bs-target="#modalChave"><i class="bi bi-pencil"></i></button>
                                    <button class="icon-button" type="button" aria-label="Excluir {{ $chave->nome_chave }}" data-delete-form="#excluirChave{{ $chave->id }}" data-confirm-message="Excluir o cadastro de {{ $chave->nome_chave }}?" data-bs-toggle="modal" data-bs-target="#modalConfirmacao"><i class="bi bi-trash"></i></button>
                                    <form id="excluirChave{{ $chave->id }}" class="d-none" method="POST" action="{{ route('painel.chaves.destroy', $chave) }}">@csrf @method('DELETE')</form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-body-secondary py-4">Nenhuma chave encontrada.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($chaves->hasPages())<div class="table-foot"><span>{{ $chaves->firstItem() }}–{{ $chaves->lastItem() }} de {{ $chaves->total() }} chaves</span>{{ $chaves->onEachSide(1)->links('pagination::bootstrap-5') }}</div>@endif
        </section>
    @else
        <section class="page-intro">
            <div><span class="eyebrow">{{ $page === 'agenda' ? 'RETIRADAS REGISTRADAS' : 'MOVIMENTAÇÕES' }}</span><h2>{{ $page === 'agenda' ? 'Agenda de retiradas' : 'Empréstimos de chaves' }}</h2><p>{{ $page === 'agenda' ? 'Acompanhe as retiradas pela data registrada no sistema.' : 'Registre empréstimos e atualize as devoluções.' }}</p></div>
            <button class="btn btn-senai" type="button" data-create-resource="emprestimo" data-bs-toggle="modal" data-bs-target="#modalEmprestimo"><i class="bi bi-plus-lg me-2"></i>Novo empréstimo</button>
        </section>

        <section class="stat-grid mb-4">
            <article class="stat-card"><span class="stat-icon stat-icon-red"><i class="bi bi-arrow-left-right"></i></span><div><span>Em andamento</span><strong>{{ $stats['emprestimos_andamento'] }}</strong></div></article>
            <article class="stat-card"><span class="stat-icon stat-icon-green"><i class="bi bi-check2-circle"></i></span><div><span>Chaves disponíveis</span><strong>{{ $stats['chaves_disponiveis'] }}</strong></div></article>
            <article class="stat-card"><span class="stat-icon stat-icon-amber"><i class="bi bi-clock-history"></i></span><div><span>Atrasados registrados</span><strong>{{ $stats['emprestimos_atrasados'] }}</strong></div></article>
        </section>

        <section class="content-panel">
            <form class="toolbar" method="GET" action="{{ $page === 'agenda' ? route('agenda') : route('emprestimos') }}">
                <div class="search-field"><i class="bi bi-search"></i><input class="form-control" type="search" name="busca" value="{{ request('busca') }}" placeholder="Buscar funcionário ou chave" aria-label="Buscar empréstimos"></div>
                <select class="form-select filter-select" name="status" aria-label="Filtrar por situação">
                    <option value="">Todas as situações</option>
                    @foreach (['Em andamento', 'Devolvido', 'Atrasado'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                <button class="btn btn-senai" type="submit">Filtrar</button>
                @if (request('busca') || request('status'))<a class="btn btn-light-outline" href="{{ $page === 'agenda' ? route('agenda') : route('emprestimos') }}">Limpar</a>@endif
            </form>
            <div class="table-responsive">
                <table class="table align-middle data-table mb-0">
                    <thead><tr><th>Chave</th><th>Funcionário</th><th>Retirada</th><th>Devolução</th><th>Situação</th><th class="text-end">Ações</th></tr></thead>
                    <tbody>
                        @forelse ($emprestimos as $emprestimo)
                            <tr>
                                <td><strong>{{ $emprestimo->chave?->nome_chave ?? 'Chave removida' }}</strong><span class="cell-subtitle">{{ $emprestimo->chave?->localizacao ?? '—' }}</span></td>
                                <td>{{ $emprestimo->funcionario?->nome ?? 'Funcionário removido' }}</td>
                                <td>{{ $emprestimo->data_retirada ? \Illuminate\Support\Carbon::parse($emprestimo->data_retirada)->format('d/m/Y') : '—' }}<span class="cell-subtitle">{{ $emprestimo->hora_retirada ? substr((string) $emprestimo->hora_retirada, 0, 5) : '—' }}</span></td>
                                <td>{{ $emprestimo->data_devolucao ? \Illuminate\Support\Carbon::parse($emprestimo->data_devolucao)->format('d/m/Y') : '—' }}<span class="cell-subtitle">{{ $emprestimo->hora_devolucao ? substr((string) $emprestimo->hora_devolucao, 0, 5) : '—' }}</span></td>
                                <td><span class="status-pill {{ $emprestimo->status === 'Devolvido' ? 'status-returned' : ($emprestimo->status === 'Atrasado' ? 'status-pending' : 'status-active') }}"><span></span>{{ $emprestimo->status }}</span></td>
                                <td class="text-end text-nowrap">
                                    <button class="icon-button" type="button" aria-label="Ver empréstimo {{ $emprestimo->id }}" data-view-resource="emprestimo" data-detail-title="Empréstimo #{{ $emprestimo->id }}" data-detail-funcionario="{{ $emprestimo->funcionario?->nome ?? 'Funcionário removido' }}" data-detail-chave="{{ $emprestimo->chave?->nome_chave ?? 'Chave removida' }}" data-detail-retirada="{{ $emprestimo->data_retirada ? \Illuminate\Support\Carbon::parse($emprestimo->data_retirada)->format('d/m/Y') : '—' }} · {{ $emprestimo->hora_retirada ? substr((string) $emprestimo->hora_retirada, 0, 5) : '—' }}" data-detail-devolucao="{{ $emprestimo->data_devolucao ? \Illuminate\Support\Carbon::parse($emprestimo->data_devolucao)->format('d/m/Y') : '—' }} · {{ $emprestimo->hora_devolucao ? substr((string) $emprestimo->hora_devolucao, 0, 5) : '—' }}" data-detail-status="{{ $emprestimo->status }}" data-bs-toggle="modal" data-bs-target="#modalDetalhes"><i class="bi bi-eye"></i></button>
                                    <button class="icon-button" type="button" aria-label="Editar empréstimo {{ $emprestimo->id }}" data-edit-resource="emprestimo" data-id="{{ $emprestimo->id }}" data-update-url="{{ route('painel.emprestimos.update', $emprestimo) }}" data-funcionario-id="{{ $emprestimo->funcionario_id }}" data-chave-id="{{ $emprestimo->chave_id }}" data-data-retirada="{{ $emprestimo->data_retirada }}" data-hora-retirada="{{ $emprestimo->hora_retirada ? substr((string) $emprestimo->hora_retirada, 0, 5) : '' }}" data-data-devolucao="{{ $emprestimo->data_devolucao }}" data-hora-devolucao="{{ $emprestimo->hora_devolucao ? substr((string) $emprestimo->hora_devolucao, 0, 5) : '' }}" data-status="{{ $emprestimo->status }}" data-bs-toggle="modal" data-bs-target="#modalEmprestimo"><i class="bi bi-pencil"></i></button>
                                    <button class="icon-button" type="button" aria-label="Excluir empréstimo {{ $emprestimo->id }}" data-delete-form="#excluirEmprestimo{{ $emprestimo->id }}" data-confirm-message="Excluir o empréstimo da chave {{ $emprestimo->chave?->nome_chave ?? '' }}?" data-bs-toggle="modal" data-bs-target="#modalConfirmacao"><i class="bi bi-trash"></i></button>
                                    <form id="excluirEmprestimo{{ $emprestimo->id }}" class="d-none" method="POST" action="{{ route('painel.emprestimos.destroy', $emprestimo) }}">@csrf @method('DELETE')</form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-body-secondary py-4">Nenhum empréstimo encontrado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($emprestimos->hasPages())<div class="table-foot"><span>{{ $emprestimos->firstItem() }}–{{ $emprestimos->lastItem() }} de {{ $emprestimos->total() }} empréstimos</span>{{ $emprestimos->onEachSide(1)->links('pagination::bootstrap-5') }}</div>@endif
        </section>
    @endif

    @if ($page === 'funcionarios')
        <div class="modal fade" id="modalFuncionario" tabindex="-1" aria-labelledby="modalFuncionarioTitulo" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
                <div class="modal-header"><h2 class="modal-title" id="modalFuncionarioTitulo" data-resource-title="funcionario">Novo funcionário</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button></div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('painel.funcionarios.store') }}" data-resource-form="funcionario" data-store-url="{{ route('painel.funcionarios.store') }}">
                        @csrf<input type="hidden" name="_method" value="" data-method-override>
                        <div class="mb-3"><label class="form-label" for="funcionarioNome">Nome</label><input class="form-control" id="funcionarioNome" name="nome" data-edit-field value="{{ old('nome') }}" maxlength="255" required></div>
                        <div class="mb-3"><label class="form-label" for="funcionarioEmail">E-mail</label><input class="form-control" type="email" id="funcionarioEmail" name="email" data-edit-field value="{{ old('email') }}" maxlength="255" required></div>
                        <div class="mb-3"><label class="form-label" for="funcionarioCargo">Cargo <span class="text-body-secondary">(opcional)</span></label><input class="form-control" id="funcionarioCargo" name="cargo" data-edit-field value="{{ old('cargo') }}" maxlength="255"></div>
                        <div class="modal-actions"><button class="btn btn-light-outline" type="button" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-senai" type="submit">Salvar funcionário</button></div>
                    </form>
                </div>
            </div></div>
        </div>
    @elseif ($page === 'chaves')
        <div class="modal fade" id="modalChave" tabindex="-1" aria-labelledby="modalChaveTitulo" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
                <div class="modal-header"><h2 class="modal-title" id="modalChaveTitulo" data-resource-title="chave">Nova chave</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button></div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('painel.chaves.store') }}" data-resource-form="chave" data-store-url="{{ route('painel.chaves.store') }}">
                        @csrf<input type="hidden" name="_method" value="" data-method-override>
                        <div class="mb-3"><label class="form-label" for="chaveNome">Identificação</label><input class="form-control" id="chaveNome" name="nome_chave" data-edit-field value="{{ old('nome_chave') }}" maxlength="100" required></div>
                        <div class="mb-3"><label class="form-label" for="chaveLocalizacao">Localização <span class="text-body-secondary">(opcional)</span></label><input class="form-control" id="chaveLocalizacao" name="localizacao" data-edit-field value="{{ old('localizacao') }}" maxlength="100"></div>
                        <div class="mb-3"><label class="form-label" for="chaveStatus">Situação</label><select class="form-select" id="chaveStatus" name="status" data-edit-field required>@foreach (['Disponível', 'Emprestada', 'Indisponível'] as $status)<option value="{{ $status }}" @selected(old('status', 'Disponível') === $status)>{{ $status }}</option>@endforeach</select></div>
                        <div class="modal-actions"><button class="btn btn-light-outline" type="button" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-senai" type="submit">Salvar chave</button></div>
                    </form>
                </div>
            </div></div>
        </div>
    @elseif (in_array($page, ['agenda', 'emprestimos'], true))
        <div class="modal fade" id="modalEmprestimo" tabindex="-1" aria-labelledby="modalEmprestimoTitulo" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
                <div class="modal-header"><h2 class="modal-title" id="modalEmprestimoTitulo" data-resource-title="emprestimo">Novo empréstimo</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button></div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('painel.emprestimos.store') }}" data-resource-form="emprestimo" data-store-url="{{ route('painel.emprestimos.store') }}">
                        @csrf<input type="hidden" name="_method" value="" data-method-override>
                        <div class="mb-3"><label class="form-label" for="emprestimoFuncionario">Funcionário</label><select class="form-select" id="emprestimoFuncionario" name="funcionario_id" data-edit-field required><option value="">Selecione um funcionário</option>@foreach ($funcionarioOptions as $funcionarioOption)<option value="{{ $funcionarioOption->id }}" @selected(old('funcionario_id') == $funcionarioOption->id)>{{ $funcionarioOption->nome }}</option>@endforeach</select></div>
                        <div class="mb-3"><label class="form-label" for="emprestimoChave">Chave</label><select class="form-select" id="emprestimoChave" name="chave_id" data-edit-field required><option value="">Selecione uma chave</option>@foreach ($chaveOptions as $chaveOption)<option value="{{ $chaveOption->id }}" @selected(old('chave_id') == $chaveOption->id)>{{ $chaveOption->nome_chave }} · {{ $chaveOption->localizacao ?: 'Sem localização' }} ({{ $chaveOption->status }})</option>@endforeach</select></div>
                        <div class="row g-3"><div class="col-sm-6"><label class="form-label" for="emprestimoDataRetirada">Data da retirada</label><input class="form-control" type="date" id="emprestimoDataRetirada" name="data_retirada" data-edit-field value="{{ old('data_retirada', now()->format('Y-m-d')) }}" required></div><div class="col-sm-6"><label class="form-label" for="emprestimoHoraRetirada">Horário da retirada</label><input class="form-control" type="time" id="emprestimoHoraRetirada" name="hora_retirada" data-edit-field value="{{ old('hora_retirada', now()->format('H:i')) }}" required></div></div>
                        <div class="row g-3 mt-1" data-edit-only><div class="col-sm-6"><label class="form-label" for="emprestimoDataDevolucao">Data da devolução</label><input class="form-control" type="date" id="emprestimoDataDevolucao" name="data_devolucao" data-edit-field value="{{ old('data_devolucao') }}"></div><div class="col-sm-6"><label class="form-label" for="emprestimoHoraDevolucao">Horário da devolução</label><input class="form-control" type="time" id="emprestimoHoraDevolucao" name="hora_devolucao" data-edit-field value="{{ old('hora_devolucao') }}"></div></div>
                        <div class="mt-3" data-edit-only><label class="form-label" for="emprestimoStatus">Situação</label><select class="form-select" id="emprestimoStatus" name="status" data-edit-field required>@foreach (['Em andamento', 'Devolvido', 'Atrasado'] as $status)<option value="{{ $status }}" @selected(old('status', 'Em andamento') === $status)>{{ $status }}</option>@endforeach</select></div>
                        <div class="modal-actions"><button class="btn btn-light-outline" type="button" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-senai" type="submit">Salvar empréstimo</button></div>
                    </form>
                </div>
            </div></div>
        </div>
    @endif

    <div class="modal fade" id="modalDetalhes" tabindex="-1" aria-labelledby="modalDetalhesTitulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
            <div class="modal-header"><h2 class="modal-title fs-5" id="modalDetalhesTitulo">Detalhes do registro</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button></div>
            <div class="modal-body"><dl class="row mb-0" id="detalhesRecurso"></dl></div>
            <div class="modal-footer"><button class="btn btn-light-outline" type="button" data-bs-dismiss="modal">Fechar</button></div>
        </div></div>
    </div>

    <div class="modal fade" id="modalConfirmacao" tabindex="-1" aria-labelledby="modalConfirmacaoTitulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content">
            <div class="modal-header"><h2 class="modal-title fs-5" id="modalConfirmacaoTitulo">Confirmar exclusão</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button></div>
            <div class="modal-body"><p class="mb-0" id="mensagemConfirmacao">Deseja excluir este registro?</p></div>
            <div class="modal-footer"><button class="btn btn-light-outline" type="button" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-danger" type="button" id="confirmarExclusao">Excluir</button></div>
        </div></div>
    </div>

    @if ($errors->any())
        <div hidden data-open-modal="{{ $page === 'funcionarios' ? 'modalFuncionario' : ($page === 'chaves' ? 'modalChave' : (in_array($page, ['agenda', 'emprestimos'], true) ? 'modalEmprestimo' : '')) }}"></div>
    @endif
@endsection
