@extends('layout.principal')

@section('title', 'Controle de Chaves | SENAI')

@section('content')
    @if ($page === 'inicio')
        <section class="welcome-panel">
            <div class="welcome-copy">
                <span class="eyebrow"><span class="eyebrow-line"></span> SENAI · UNIDADE CE-024</span>
                <h2>Bem-vindo ao Sistema de Controle de Chaves</h2>
                <p>Gerencie usuários, chaves e empréstimos de forma simples, rápida e segura.</p>
                <a class="btn btn-senai mt-2" href="{{ route('emprestimos') }}"><i class="bi bi-arrow-right me-2"></i>Acessar empréstimos</a>
            </div>
            <div class="welcome-art" aria-hidden="true">
                <div class="art-orbit orbit-one"></div><div class="art-orbit orbit-two"></div>
                <div class="art-key"><i class="bi bi-key-fill"></i></div>
                <span class="art-tag tag-top">ACESSO ORGANIZADO</span>
                <span class="art-tag tag-bottom"><i class="bi bi-shield-check me-2"></i>Controle seguro</span>
            </div>
        </section>

        <section class="section-block">
            <div class="section-heading">
                <div><span class="eyebrow">ATALHOS</span><h2>Ações rápidas</h2></div>
                <span class="section-note">O que você precisa fazer hoje</span>
            </div>
            <div class="row g-3 g-xl-4">
                <div class="col-md-6 col-xl-4">
                    <article class="action-card h-100">
                        <div class="action-icon"><i class="bi bi-people"></i></div>
                        <h3>Usuários</h3><p>Cadastre, edite e consulte os usuários do sistema.</p>
                        <a class="btn btn-senai" href="{{ route('usuarios') }}">Acessar <i class="bi bi-arrow-up-right ms-2"></i></a>
                    </article>
                </div>
                <div class="col-md-6 col-xl-4">
                    <article class="action-card h-100">
                        <div class="action-icon"><i class="bi bi-key"></i></div>
                        <h3>Chaves</h3><p>Consulte a localização e a disponibilidade das chaves.</p>
                        <a class="btn btn-senai" href="{{ route('chaves') }}">Acessar <i class="bi bi-arrow-up-right ms-2"></i></a>
                    </article>
                </div>
                <div class="col-md-6 col-xl-4">
                    <article class="action-card h-100">
                        <div class="action-icon"><i class="bi bi-clipboard-check"></i></div>
                        <h3>Empréstimos</h3><p>Registre uma retirada e acompanhe as devoluções.</p>
                        <a class="btn btn-senai" href="{{ route('emprestimos') }}">Acessar <i class="bi bi-arrow-up-right ms-2"></i></a>
                    </article>
                </div>
            </div>
        </section>

        <div class="alert alert-info-panel d-flex align-items-center gap-3" role="note">
            <i class="bi bi-info-circle"></i><span>Mais segurança para o seu dia a dia. O sistema ajuda a manter o controle das chaves e a rotina da unidade mais organizada.</span>
        </div>

    @elseif ($page === 'usuarios')
        <section class="page-intro">
            <div><span class="eyebrow">CADASTROS</span><h2>Usuários do sistema</h2><p>Consulte quem tem acesso ao controle de chaves da unidade.</p></div>
            <button class="btn btn-senai" type="button" data-user-action="create"><i class="bi bi-plus-lg me-2"></i>Cadastrar usuário</button>
        </section>
        <section class="content-panel">
            <div class="toolbar">
                <div class="search-field"><i class="bi bi-search"></i><input class="form-control" type="search" placeholder="Buscar por nome ou e-mail..." aria-label="Buscar usuário" data-table-search="usuariosTabela"></div>
                <select class="form-select filter-select" aria-label="Filtrar por tipo" data-table-filter="usuariosTabela"><option value="">Todos os tipos</option><option>Aluno</option><option>Funcionário</option><option>Professor</option></select>
                <button class="btn btn-light-outline" type="button" data-clear-search><i class="bi bi-arrow-counterclockwise me-2"></i>Limpar</button>
            </div>
            <div class="table-responsive">
                <table class="table align-middle data-table" id="usuariosTabela" data-page-size="2">
                    <thead><tr><th>Nome</th><th>E-mail institucional</th><th>Tipo</th><th>Status</th><th class="text-end">Ações</th></tr></thead>
                    <tbody>
                        <tr><td><div class="person-cell"><span class="mini-avatar avatar-red">PH</span><strong>Pedro Henrique</strong></div></td><td>pedro.henrique@aluno.senai.br</td><td><span class="type-label">Aluno</span></td><td><span class="status-pill status-active"><span></span>Ativo</span></td><td class="text-end"><button class="icon-button" aria-label="Ver Pedro Henrique"><i class="bi bi-eye"></i></button><button class="icon-button" aria-label="Editar Pedro Henrique"><i class="bi bi-pencil"></i></button><button class="icon-button text-danger" aria-label="Remover Pedro Henrique"><i class="bi bi-trash3"></i></button></td></tr>
                        <tr><td><div class="person-cell"><span class="mini-avatar avatar-blue">AC</span><strong>Ana Clara</strong></div></td><td>ana.clara@senai.br</td><td><span class="type-label">Funcionário</span></td><td><span class="status-pill status-active"><span></span>Ativo</span></td><td class="text-end"><button class="icon-button" aria-label="Ver Ana Clara"><i class="bi bi-eye"></i></button><button class="icon-button" aria-label="Editar Ana Clara"><i class="bi bi-pencil"></i></button><button class="icon-button text-danger" aria-label="Remover Ana Clara"><i class="bi bi-trash3"></i></button></td></tr>
                        <tr><td><div class="person-cell"><span class="mini-avatar avatar-green">JV</span><strong>João Victor</strong></div></td><td>joao.victor@senai.br</td><td><span class="type-label">Professor</span></td><td><span class="status-pill status-inactive"><span></span>Inativo</span></td><td class="text-end"><button class="icon-button" aria-label="Ver João Victor"><i class="bi bi-eye"></i></button><button class="icon-button" aria-label="Editar João Victor"><i class="bi bi-pencil"></i></button><button class="icon-button text-danger" aria-label="Remover João Victor"><i class="bi bi-trash3"></i></button></td></tr>
                        <tr><td><div class="person-cell"><span class="mini-avatar avatar-gold">MS</span><strong>Maria Silva</strong></div></td><td>maria.silva@aluno.senai.br</td><td><span class="type-label">Aluno</span></td><td><span class="status-pill status-active"><span></span>Ativo</span></td><td class="text-end"><button class="icon-button" aria-label="Ver Maria Silva"><i class="bi bi-eye"></i></button><button class="icon-button" aria-label="Editar Maria Silva"><i class="bi bi-pencil"></i></button><button class="icon-button text-danger" aria-label="Remover Maria Silva"><i class="bi bi-trash3"></i></button></td></tr>
                    </tbody>
                </table>
            </div>
            <div class="table-foot"><span>Exibindo <strong data-row-count>4</strong> de <strong data-total-count>4</strong> usuários</span><nav aria-label="Paginação"><ul class="pagination pagination-sm mb-0" data-table-pagination="usuariosTabela"></ul></nav></div>
        </section>

    @elseif ($page === 'chaves')
        <section class="page-intro">
            <div><span class="eyebrow">PATRIMÔNIO</span><h2>Cadastro de chaves físicas</h2><p>Acompanhe a localização e a disponibilidade de cada chave.</p></div>
            <button class="btn btn-senai" data-bs-toggle="modal" data-bs-target="#modalChave"><i class="bi bi-plus-lg me-2"></i>Cadastrar chave</button>
        </section>
        <div class="toolbar content-panel toolbar-spaced">
            <div class="search-field"><i class="bi bi-search"></i><input class="form-control" type="search" placeholder="Buscar chave ou setor..." aria-label="Buscar chave" data-card-search="listaChaves"></div>
            <select class="form-select filter-select" aria-label="Filtrar disponibilidade" data-card-filter="listaChaves"><option value="">Todas as situações</option><option>Disponível</option><option>Emprestada</option><option>Indisponível</option></select>
            <button class="btn btn-light-outline" type="button" data-clear-search><i class="bi bi-arrow-counterclockwise me-2"></i>Limpar</button>
        </div>
        <section class="row g-3 g-xl-4" id="listaChaves">
            <div class="col-xl-6" data-card-item data-key-id="chave-01" data-default-status="Disponível" data-filter="Disponível"><article class="key-card"><div class="key-card-top"><div class="key-icon"><i class="bi bi-key-fill"></i></div><span class="status-pill status-active"><span></span>Disponível</span></div><h3>Chave 01</h3><p class="key-location"><i class="bi bi-geo-alt me-1"></i>Laboratório de Informática</p><div class="key-meta"><span>Responsável padrão</span><strong>Carlos Eduardo</strong></div><div class="key-actions"><button class="btn btn-senai" type="button" data-key-action="withdrawal">Registrar retirada</button><button class="btn btn-light-outline" data-bs-toggle="modal" data-bs-target="#modalChave">Editar</button></div></article></div>
            <div class="col-xl-6" data-card-item data-key-id="chave-02" data-default-status="Emprestada" data-filter="Emprestada"><article class="key-card"><div class="key-card-top"><div class="key-icon"><i class="bi bi-key-fill"></i></div><span class="status-pill status-pending"><span></span>Emprestada</span></div><h3>Chave 02</h3><p class="key-location"><i class="bi bi-geo-alt me-1"></i>Sala 05 · Bloco B</p><div class="key-meta"><span>Com</span><strong>Pedro Henrique</strong></div><div class="key-actions"><button class="btn btn-senai" type="button" data-key-action="return">Registrar devolução</button><button class="btn btn-light-outline" data-bs-toggle="modal" data-bs-target="#modalChave">Editar</button></div></article></div>
            <div class="col-xl-6" data-card-item data-key-id="chave-03" data-default-status="Indisponível" data-filter="Indisponível"><article class="key-card"><div class="key-card-top"><div class="key-icon"><i class="bi bi-key-fill"></i></div><span class="status-pill status-inactive"><span></span>Indisponível</span></div><h3>Chave 03</h3><p class="key-location"><i class="bi bi-geo-alt me-1"></i>Biblioteca</p><div class="key-meta"><span>Responsável padrão</span><strong>Maria Silva</strong></div><div class="key-actions"><button class="btn btn-light-outline" type="button" data-key-action="details"><i class="bi bi-eye me-2"></i>Ver detalhes</button><button class="btn btn-light-outline" data-bs-toggle="modal" data-bs-target="#modalChave">Editar</button></div></article></div>
            <div class="col-xl-6" data-card-item data-key-id="chave-04" data-default-status="Disponível" data-filter="Disponível"><article class="key-card"><div class="key-card-top"><div class="key-icon"><i class="bi bi-key-fill"></i></div><span class="status-pill status-active"><span></span>Disponível</span></div><h3>Chave 04</h3><p class="key-location"><i class="bi bi-geo-alt me-1"></i>Laboratório de Eletrônica</p><div class="key-meta"><span>Responsável padrão</span><strong>Ana Clara</strong></div><div class="key-actions"><button class="btn btn-senai" type="button" data-key-action="withdrawal">Registrar retirada</button><button class="btn btn-light-outline" data-bs-toggle="modal" data-bs-target="#modalChave">Editar</button></div></article></div>
        </section>

    @elseif ($page === 'emprestimos')
        <section class="page-intro">
            <div><span class="eyebrow">MOVIMENTAÇÕES</span><h2>Empréstimos de chaves</h2><p>Registre retiradas e acompanhe os prazos de devolução.</p></div>
            <button class="btn btn-senai" data-bs-toggle="modal" data-bs-target="#modalEmprestimo"><i class="bi bi-plus-lg me-2"></i>Novo empréstimo</button>
        </section>
        <section class="stat-grid mb-4">
            <article class="stat-card"><span class="stat-icon stat-icon-red"><i class="bi bi-arrow-left-right"></i></span><div><span>Em andamento</span><strong>08</strong></div><span class="stat-detail">neste momento</span></article>
            <article class="stat-card"><span class="stat-icon stat-icon-green"><i class="bi bi-check2-circle"></i></span><div><span>Devolvidos hoje</span><strong>12</strong></div><span class="stat-detail">no prazo</span></article>
            <article class="stat-card"><span class="stat-icon stat-icon-amber"><i class="bi bi-clock-history"></i></span><div><span>Atrasados</span><strong>02</strong></div><span class="stat-detail">precisam de atenção</span></article>
        </section>
        <section class="content-panel">
            <div class="toolbar">
                <div class="search-field"><i class="bi bi-search"></i><input class="form-control" type="search" placeholder="Buscar usuário ou chave..." aria-label="Buscar empréstimo" data-table-search="emprestimosTabela"></div>
                <select class="form-select filter-select" aria-label="Filtrar por status" data-table-filter="emprestimosTabela"><option value="">Todos os status</option><option>Em andamento</option><option>Devolvido</option><option>Atrasado</option></select>
                <button class="btn btn-light-outline" type="button" data-clear-search><i class="bi bi-arrow-counterclockwise me-2"></i>Limpar</button>
            </div>
            <div class="table-responsive"><table class="table align-middle data-table" id="emprestimosTabela" data-page-size="2">
                <thead><tr><th>Chave</th><th>Usuário</th><th>Retirada</th><th>Devolução prevista</th><th>Status</th><th class="text-end">Ações</th></tr></thead>
                <tbody>
                    <tr data-loan-id="emprestimo-01" data-key-id="chave-01"><td><strong>Chave 01</strong><span class="cell-subtitle">Lab. Informática</span></td><td>Pedro Henrique</td><td>30/09/2026 <span class="cell-subtitle">08:00</span></td><td>30/09/2026 <span class="cell-subtitle">12:00</span></td><td><span class="status-pill status-active"><span></span>Em andamento</span></td><td class="text-end"><button type="button" class="btn btn-link btn-sm text-danger p-1" aria-label="Registrar devolução" title="Registrar devolução" data-loan-action="return"><i class="bi bi-box-arrow-in-left"></i></button><button type="button" class="btn btn-link btn-sm text-primary p-1" aria-label="Ver empréstimo" title="Ver empréstimo" data-loan-action="details"><i class="bi bi-eye"></i></button></td></tr>
                    <tr data-loan-id="emprestimo-02" data-key-id="chave-03"><td><strong>Chave 03</strong><span class="cell-subtitle">Biblioteca</span></td><td>Ana Clara</td><td>29/09/2026 <span class="cell-subtitle">14:20</span></td><td>29/09/2026 <span class="cell-subtitle">17:00</span></td><td><span class="status-pill status-returned"><span></span>Devolvido</span></td><td class="text-end"><button type="button" class="btn btn-link btn-sm text-primary p-1" aria-label="Ver empréstimo" title="Ver empréstimo" data-loan-action="details"><i class="bi bi-eye"></i></button></td></tr>
                    <tr data-loan-id="emprestimo-03" data-key-id="chave-02"><td><strong>Chave 02</strong><span class="cell-subtitle">Sala 05 · Bloco B</span></td><td>João Victor</td><td>29/09/2026 <span class="cell-subtitle">09:10</span></td><td>29/09/2026 <span class="cell-subtitle">11:00</span></td><td><span class="status-pill status-inactive"><span></span>Atrasado</span></td><td class="text-end"><button type="button" class="btn btn-link btn-sm text-warning p-1" aria-label="Preparar notificação" title="Preparar notificação" data-loan-action="notify"><i class="bi bi-bell"></i></button><button type="button" class="btn btn-link btn-sm text-primary p-1" aria-label="Ver empréstimo" title="Ver empréstimo" data-loan-action="details"><i class="bi bi-eye"></i></button></td></tr>
                    <tr data-loan-id="emprestimo-04" data-key-id="chave-04"><td><strong>Chave 04</strong><span class="cell-subtitle">Lab. Eletrônica</span></td><td>Maria Silva</td><td>28/09/2026 <span class="cell-subtitle">13:00</span></td><td>28/09/2026 <span class="cell-subtitle">16:30</span></td><td><span class="status-pill status-returned"><span></span>Devolvido</span></td><td class="text-end"><button type="button" class="btn btn-link btn-sm text-primary p-1" aria-label="Ver empréstimo" title="Ver empréstimo" data-loan-action="details"><i class="bi bi-eye"></i></button></td></tr>
                </tbody>
            </table></div>
            <div class="table-foot"><span>Exibindo <strong data-row-count>4</strong> de <strong data-total-count>4</strong> empréstimos</span><nav aria-label="Paginação"><ul class="pagination pagination-sm mb-0" data-table-pagination="emprestimosTabela"></ul></nav></div>
        </section>

    @elseif ($page === 'agenda')
        <section class="page-intro">
            <div><span class="eyebrow">PLANEJAMENTO</span><h2>Agendamentos ativos</h2><p>Organize as retiradas e evite conflitos de horário.</p></div>
            <button class="btn btn-senai" data-bs-toggle="modal" data-bs-target="#modalAgenda"><i class="bi bi-plus-lg me-2"></i>Novo agendamento</button>
        </section>
        <section class="agenda-layout">
            <div class="content-panel calendar-panel" data-calendar-initial-date="2026-09-30">
                <div class="calendar-heading">
                    <div>
                        <span class="eyebrow" data-calendar-week-caption>SEMANA ATUAL</span>
                        <h3 data-calendar-week-label>28 de set. – 4 de out.</h3>
                    </div>
                    <div class="calendar-controls">
                        <button class="btn btn-light-outline" type="button" data-calendar-step="-1" aria-label="Semana anterior"><i class="bi bi-chevron-left"></i></button>
                        <button class="btn btn-light-outline" type="button" data-calendar-step="1" aria-label="Próxima semana"><i class="bi bi-chevron-right"></i></button>
                    </div>
                </div>
                <div class="week-strip" data-calendar-days role="group" aria-label="Selecione um dia da semana">
                    <button class="week-day" type="button" data-calendar-date="2026-09-28" aria-pressed="false"><span>SEG</span><strong>28</strong></button>
                    <button class="week-day" type="button" data-calendar-date="2026-09-29" aria-pressed="false"><span>TER</span><strong>29</strong></button>
                    <button class="week-day selected" type="button" data-calendar-date="2026-09-30" aria-pressed="true"><span>QUA</span><strong>30</strong></button>
                    <button class="week-day" type="button" data-calendar-date="2026-10-01" aria-pressed="false"><span>QUI</span><strong>01</strong></button>
                    <button class="week-day" type="button" data-calendar-date="2026-10-02" aria-pressed="false"><span>SEX</span><strong>02</strong></button>
                </div>
                <div class="schedule-list" data-calendar-schedule aria-live="polite">
                    <div class="schedule-row" data-calendar-schedule-date="2026-09-30"><time>08:00</time><div class="schedule-slot booked"><strong>Pedro H.</strong><span>Chave 01 · Lab. Informática</span></div></div>
                    <div class="schedule-row" data-calendar-schedule-date="2026-09-30"><time>10:00</time><div class="schedule-slot open"><span>Horário disponível</span></div></div>
                    <div class="schedule-row" data-calendar-schedule-date="2026-09-30"><time>14:00</time><div class="schedule-slot booked booked-gold"><strong>Ana Clara</strong><span>Chave 03 · Biblioteca</span></div></div>
                    <div class="schedule-row" data-calendar-schedule-date="2026-09-30"><time>16:00</time><div class="schedule-slot open"><span>Horário disponível</span></div></div>
                    <div class="schedule-row" data-calendar-schedule-date="2026-10-01" hidden><time>09:00</time><div class="schedule-slot booked"><strong>João Victor</strong><span>Chave 02 · Sala 05</span></div></div>
                    <div class="schedule-row" data-calendar-empty hidden><time>—</time><div class="schedule-slot open"><span>Nenhum agendamento neste dia.</span></div></div>
                </div>
            </div>
            <aside class="upcoming-panel"><div class="upcoming-heading"><div><span class="eyebrow">PRÓXIMOS</span><h3>Agendamentos</h3></div><span class="upcoming-count">3</span></div>
                <article class="appointment-card"><div class="appointment-top"><strong>Pedro Henrique</strong><span class="status-pill status-active"><span></span>Confirmado</span></div><p>Chave 01 · Lab. Informática</p><span class="appointment-time"><i class="bi bi-calendar3 me-2"></i>Hoje, 08:00 – 10:00</span><div class="appointment-actions"><button class="btn btn-light-outline btn-sm" data-bs-toggle="modal" data-bs-target="#modalAgenda">Editar</button><button class="btn btn-link btn-sm text-danger" data-feedback="Agendamento cancelado.">Cancelar</button></div></article>
                <article class="appointment-card"><div class="appointment-top"><strong>Ana Clara</strong><span class="status-pill status-pending"><span></span>Pendente</span></div><p>Chave 03 · Biblioteca</p><span class="appointment-time"><i class="bi bi-calendar3 me-2"></i>Hoje, 14:00 – 16:00</span><div class="appointment-actions"><button class="btn btn-senai btn-sm" data-feedback="Agendamento confirmado.">Confirmar</button><button class="btn btn-light-outline btn-sm" data-bs-toggle="modal" data-bs-target="#modalAgenda">Editar</button></div></article>
                <article class="appointment-card"><div class="appointment-top"><strong>João Victor</strong><span class="status-pill status-pending"><span></span>Pendente</span></div><p>Chave 02 · Sala 05</p><span class="appointment-time"><i class="bi bi-calendar3 me-2"></i>Qui, 01/10 · 09:00 – 11:00</span><div class="appointment-actions"><button class="btn btn-senai btn-sm" data-feedback="Agendamento confirmado.">Confirmar</button><button class="btn btn-light-outline btn-sm" data-bs-toggle="modal" data-bs-target="#modalAgenda">Editar</button></div></article>
            </aside>
        </section>
    @endif

    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioTitulo" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><div><span class="eyebrow" id="modalUsuarioEyebrow">NOVO CADASTRO</span><h2 class="modal-title" id="modalUsuarioTitulo">Cadastrar usuário</h2></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button></div><div class="modal-body"><form id="formUsuario"><div class="mb-3"><label class="form-label" for="usuarioNome">Nome completo</label><input class="form-control" id="usuarioNome" name="nome" placeholder="Ex.: Pedro Henrique" required></div><div class="mb-3"><label class="form-label" for="usuarioEmail">E-mail institucional</label><input class="form-control" type="email" id="usuarioEmail" name="email" placeholder="nome@senai.br" required></div><div class="row g-3"><div class="col-sm-6"><label class="form-label" for="usuarioTipo">Tipo de usuário</label><select class="form-select" id="usuarioTipo" name="tipo"><option>Aluno</option><option>Funcionário</option><option>Professor</option></select></div><div class="col-sm-6"><label class="form-label" for="usuarioStatus">Status</label><select class="form-select" id="usuarioStatus" name="status"><option>Ativo</option><option>Inativo</option></select></div></div><div class="modal-actions"><button type="button" class="btn btn-light-outline" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-senai" id="salvarUsuarioBtn">Salvar usuário</button></div></form></div></div></div></div>

    <div class="modal fade" id="modalDetalhesUsuario" tabindex="-1" aria-labelledby="modalDetalhesUsuarioTitulo" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><div><span class="eyebrow">CADASTRO</span><h2 class="modal-title" id="modalDetalhesUsuarioTitulo">Detalhes do usuário</h2></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button></div><div class="modal-body"><dl class="row mb-0"><dt class="col-sm-4">Nome</dt><dd class="col-sm-8" id="detalhesUsuarioNome"></dd><dt class="col-sm-4">E-mail</dt><dd class="col-sm-8" id="detalhesUsuarioEmail"></dd><dt class="col-sm-4">Tipo</dt><dd class="col-sm-8" id="detalhesUsuarioTipo"></dd><dt class="col-sm-4">Status</dt><dd class="col-sm-8" id="detalhesUsuarioStatus"></dd></dl></div><div class="modal-footer"><button type="button" class="btn btn-light-outline" data-bs-dismiss="modal">Fechar</button></div></div></div></div>

    <div class="modal fade" id="modalRemoverUsuario" tabindex="-1" aria-labelledby="modalRemoverUsuarioTitulo" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><div><span class="eyebrow">CONFIRMAÇÃO</span><h2 class="modal-title" id="modalRemoverUsuarioTitulo">Remover usuário</h2></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button></div><div class="modal-body"><p id="confirmarRemocaoUsuarioTexto" class="mb-0"></p></div><div class="modal-footer"><button type="button" class="btn btn-light-outline" data-bs-dismiss="modal">Cancelar</button><button type="button" class="btn btn-senai" id="confirmarRemocaoUsuarioBtn">Remover</button></div></div></div></div>

    <div class="modal fade" id="modalChave" tabindex="-1" aria-labelledby="modalChaveTitulo" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><div><span class="eyebrow">PATRIMÔNIO</span><h2 class="modal-title" id="modalChaveTitulo">Cadastrar nova chave</h2></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button></div><div class="modal-body"><form data-demo-form data-success="Chave cadastrada."><div class="mb-3"><label class="form-label" for="chaveNome">Identificação da chave</label><input class="form-control" id="chaveNome" name="nome" placeholder="Ex.: Chave 05" required></div><div class="mb-3"><label class="form-label" for="chaveLocal">Localização / setor</label><input class="form-control" id="chaveLocal" name="localizacao" placeholder="Ex.: Sala de reunião · Bloco C" required></div><div class="row g-3"><div class="col-sm-6"><label class="form-label" for="chaveResponsavel">Responsável padrão</label><select class="form-select" id="chaveResponsavel" name="responsavel"><option>Ana Clara</option><option>Carlos Eduardo</option><option>Maria Silva</option></select></div><div class="col-sm-6"><label class="form-label" for="chaveStatus">Status inicial</label><select class="form-select" id="chaveStatus" name="status"><option>Disponível</option><option>Indisponível</option></select></div></div><div class="mt-3"><label class="form-label" for="chaveObs">Observações</label><textarea class="form-control" id="chaveObs" name="observacoes" rows="3" placeholder="Detalhes adicionais ou restrições de acesso"></textarea></div><div class="modal-actions"><button type="button" class="btn btn-light-outline" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-senai">Salvar chave</button></div></form></div></div></div></div>

    <div class="modal fade" id="modalEmprestimo" tabindex="-1" aria-labelledby="modalEmprestimoTitulo" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><div><span class="eyebrow">MOVIMENTAÇÃO</span><h2 class="modal-title" id="modalEmprestimoTitulo">Novo empréstimo</h2></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button></div><div class="modal-body"><form id="formEmprestimo"><div class="mb-3"><label class="form-label" for="emprestimoUsuario">Usuário</label><select class="form-select" id="emprestimoUsuario" name="usuario" required><option value="">Selecione o usuário...</option><option>Pedro Henrique</option><option>Ana Clara</option><option>João Victor</option><option>Maria Silva</option></select></div><div class="mb-3"><label class="form-label" for="emprestimoChave">Chave</label><select class="form-select" id="emprestimoChave" name="chave" required><option value="">Selecione uma chave disponível...</option><option value="chave-01">Chave 01 · Lab. Informática</option><option value="chave-04">Chave 04 · Lab. Eletrônica</option></select></div><div class="row g-3"><div class="col-sm-6"><label class="form-label" for="emprestimoData">Data da retirada</label><input class="form-control" type="date" id="emprestimoData" name="data" value="2026-09-30"></div><div class="col-sm-6"><label class="form-label" for="emprestimoHora">Horário</label><input class="form-control" type="time" id="emprestimoHora" name="hora" value="08:00"></div></div><div class="mt-3"><label class="form-label" for="emprestimoPrevisto">Devolução prevista</label><input class="form-control" type="datetime-local" id="emprestimoPrevisto" name="devolucao"></div><div class="mt-3"><label class="form-label" for="emprestimoObs">Observações</label><textarea class="form-control" id="emprestimoObs" name="observacoes" rows="3" placeholder="Observações relevantes"></textarea></div><div class="modal-actions"><button type="button" class="btn btn-light-outline" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-senai">Salvar empréstimo</button></div></form></div></div></div></div>

    <div class="modal fade" id="modalDetalhesChave" tabindex="-1" aria-labelledby="modalDetalhesChaveTitulo" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><div><span class="eyebrow">PATRIMÔNIO</span><h2 class="modal-title" id="modalDetalhesChaveTitulo">Detalhes da chave</h2></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button></div><div class="modal-body"><dl class="row mb-0"><dt class="col-sm-4">Chave</dt><dd class="col-sm-8" id="detalhesChaveNome"></dd><dt class="col-sm-4">Localização</dt><dd class="col-sm-8" id="detalhesChaveLocal"></dd><dt class="col-sm-4">Situação</dt><dd class="col-sm-8" id="detalhesChaveStatus"></dd><dt class="col-sm-4">Responsável</dt><dd class="col-sm-8" id="detalhesChaveResponsavel"></dd><dt class="col-sm-4">Retirada</dt><dd class="col-sm-8" id="detalhesChaveRetirada"></dd><dt class="col-sm-4">Devolução prevista</dt><dd class="col-sm-8" id="detalhesChaveDevolucao"></dd><dt class="col-sm-4">Última devolução</dt><dd class="col-sm-8" id="detalhesChaveUltimaDevolucao"></dd><dt class="col-sm-4">Observações</dt><dd class="col-sm-8" id="detalhesChaveObservacoes"></dd></dl></div><div class="modal-footer"><button type="button" class="btn btn-light-outline" data-bs-dismiss="modal">Fechar</button></div></div></div></div>

    <div class="modal fade" id="modalDetalhesEmprestimo" tabindex="-1" aria-labelledby="modalDetalhesEmprestimoTitulo" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h2 class="modal-title" id="modalDetalhesEmprestimoTitulo">Detalhes do empréstimo</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button></div><div class="modal-body"><dl class="row mb-0"><dt class="col-sm-4">Chave</dt><dd class="col-sm-8" id="detalhesEmprestimoChave"></dd><dt class="col-sm-4">Usuário</dt><dd class="col-sm-8" id="detalhesEmprestimoUsuario"></dd><dt class="col-sm-4">Retirada</dt><dd class="col-sm-8" id="detalhesEmprestimoRetirada"></dd><dt class="col-sm-4">Devolução prevista</dt><dd class="col-sm-8" id="detalhesEmprestimoPrevista"></dd><dt class="col-sm-4">Situação</dt><dd class="col-sm-8" id="detalhesEmprestimoStatus"></dd><dt class="col-sm-4">Devolução registrada</dt><dd class="col-sm-8" id="detalhesEmprestimoDevolvida"></dd></dl></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button></div></div></div></div>

    <div class="modal fade" id="modalConfirmarDevolucao" tabindex="-1" aria-labelledby="modalConfirmarDevolucaoTitulo" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h2 class="modal-title" id="modalConfirmarDevolucaoTitulo">Confirmar devolução</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button></div><div class="modal-body"><p id="confirmarDevolucaoTexto" class="mb-0"></p></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="button" class="btn btn-danger" id="confirmarDevolucaoBtn">Registrar devolução</button></div></div></div></div>

    <div class="modal fade" id="modalNotificacaoEmprestimo" tabindex="-1" aria-labelledby="modalNotificacaoEmprestimoTitulo" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h2 class="modal-title" id="modalNotificacaoEmprestimoTitulo">Lembrete de devolução</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button></div><div class="modal-body"><p class="mb-2">Copie esta mensagem para enviar ao usuário:</p><textarea class="form-control" id="textoNotificacaoEmprestimo" rows="4" readonly></textarea></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button><button type="button" class="btn btn-primary" id="copiarNotificacaoEmprestimo"><i class="bi bi-clipboard me-2"></i>Copiar mensagem</button></div></div></div></div>

    <div class="modal fade" id="modalAgenda" tabindex="-1" aria-labelledby="modalAgendaTitulo" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><div><span class="eyebrow">PLANEJAMENTO</span><h2 class="modal-title" id="modalAgendaTitulo">Novo agendamento</h2></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button></div><div class="modal-body"><form data-demo-form data-success="Agendamento criado."><div class="mb-3"><label class="form-label" for="agendaUsuario">Usuário</label><select class="form-select" id="agendaUsuario" name="usuario"><option value="">Selecione o usuário...</option><option>Pedro Henrique</option><option>Ana Clara</option><option>João Victor</option><option>Maria Silva</option></select></div><div class="mb-3"><label class="form-label" for="agendaChave">Chave</label><select class="form-select" id="agendaChave" name="chave"><option value="">Selecione uma chave...</option><option>Chave 01 · Lab. Informática</option><option>Chave 02 · Sala 05</option><option>Chave 03 · Biblioteca</option></select></div><div class="mb-3"><label class="form-label" for="agendaData">Data</label><input class="form-control" type="date" id="agendaData" name="data" value="2026-09-30"></div><div class="row g-3"><div class="col-sm-6"><label class="form-label" for="agendaInicio">Horário de início</label><input class="form-control" type="time" id="agendaInicio" name="inicio" value="08:00"></div><div class="col-sm-6"><label class="form-label" for="agendaFim">Horário de término</label><input class="form-control" type="time" id="agendaFim" name="fim" value="10:00"></div></div><div class="mt-3"><label class="form-label" for="agendaObs">Observações</label><textarea class="form-control" id="agendaObs" name="observacoes" rows="3" placeholder="Informações para este agendamento"></textarea></div><div class="modal-actions"><button type="button" class="btn btn-light-outline" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-senai">Agendar</button></div></form></div></div></div></div>
@endsection
