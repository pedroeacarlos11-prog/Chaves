(() => {
    const toastElement = document.getElementById('feedbackToast');
    const toastText = document.getElementById('feedbackToastText');

    function showToast(message) {
        if (!toastElement || !window.bootstrap) return;

        toastText.textContent = message;
        bootstrap.Toast.getOrCreateInstance(toastElement, { delay: 3200 }).show();
    }

    const keyStateStorageKey = 'chaves:estado:v1';
    const keyCards = [...document.querySelectorAll('#listaChaves [data-card-item][data-key-id]')];
    let keyStates = {};

    try {
        keyStates = JSON.parse(window.localStorage.getItem(keyStateStorageKey) || '{}') || {};
    } catch {
        keyStates = {};
    }

    function getKeyStatus(card) {
        const savedStatus = keyStates[card.dataset.keyId]?.status;
        const validStatuses = ['Disponível', 'Emprestada', 'Indisponível'];

        return validStatuses.includes(savedStatus) ? savedStatus : card.dataset.defaultStatus;
    }

    function renderKeyActions(card, status) {
        const actionContainer = card.querySelector('.key-actions');
        const currentButton = actionContainer?.querySelector('[data-key-action]');

        if (!actionContainer || !currentButton) return;

        const button = document.createElement('button');
        button.type = 'button';
        button.className = status === 'Disponível' ? 'btn btn-senai' : 'btn btn-light-outline';

        if (status === 'Disponível') {
            button.dataset.keyAction = 'withdrawal';
            button.textContent = 'Registrar retirada';
        } else if (status === 'Emprestada') {
            button.dataset.keyAction = 'return';
            button.textContent = 'Registrar devolução';
        } else {
            button.dataset.keyAction = 'details';
            const icon = document.createElement('i');
            icon.className = 'bi bi-eye me-2';
            button.append(icon, document.createTextNode('Ver detalhes'));
        }

        currentButton.replaceWith(button);
    }

    function renderKeyCard(card) {
        const keyId = card.dataset.keyId;
        const status = getKeyStatus(card);
        const savedState = keyStates[keyId];
        const statusPill = card.querySelector('.status-pill');
        const keyMeta = card.querySelector('.key-meta');

        card.dataset.filter = status;

        if (statusPill) {
            const statusClass = {
                'Disponível': 'status-active',
                'Emprestada': 'status-pending',
                'Indisponível': 'status-inactive',
            }[status] || 'status-inactive';
            const statusDot = document.createElement('span');

            statusPill.className = 'status-pill ' + statusClass;
            statusPill.replaceChildren(statusDot, document.createTextNode(status));
        }

        if (keyMeta && savedState?.status === 'Emprestada' && savedState.currentLoan) {
            keyMeta.querySelector('span').textContent = 'Com';
            keyMeta.querySelector('strong').textContent = savedState.currentLoan.borrower;
        } else if (keyMeta && savedState?.status === 'Disponível') {
            keyMeta.querySelector('span').textContent = 'Responsável padrão';
            keyMeta.querySelector('strong').textContent = '—';
        }

        renderKeyActions(card, status);
    }

    function updateKeySelect() {
        const keySelect = document.getElementById('emprestimoChave');

        if (!keySelect || keyCards.length === 0) return;

        const selectedKey = keySelect.value;
        keySelect.replaceChildren(new Option('Selecione uma chave disponível...', ''));

        keyCards.forEach((card) => {
            const option = new Option(
                card.querySelector('h3').textContent + ' · ' + card.querySelector('.key-location').textContent.trim(),
                card.dataset.keyId
            );

            option.disabled = getKeyStatus(card) !== 'Disponível';
            keySelect.add(option);
        });

        if ([...keySelect.options].some((option) => option.value === selectedKey && !option.disabled)) {
            keySelect.value = selectedKey;
        }
    }

    function persistKeyStates() {
        try {
            window.localStorage.setItem(keyStateStorageKey, JSON.stringify(keyStates));
            return true;
        } catch {
            return false;
        }
    }

    function formatDateTime(dateValue, timeValue = '') {
        if (!dateValue) return '—';

        const [datePart, storedTime] = dateValue.split('T');
        const dateParts = datePart.split('-');
        const formattedDate = dateParts.length === 3
            ? dateParts[2] + '/' + dateParts[1] + '/' + dateParts[0]
            : datePart;
        const time = timeValue || storedTime || '';

        return time ? formattedDate + ' ' + time.slice(0, 5) : formattedDate;
    }

    function fillKeyDetails(card) {
        const keyId = card.dataset.keyId;
        const savedState = keyStates[keyId] || {};
        const loan = savedState.currentLoan || savedState.lastLoan;
        const meta = card.querySelector('.key-meta');
        const currentHolder = meta.querySelector('strong').textContent;
        const lastReturn = savedState.lastLoan?.returnedAt
            ? new Intl.DateTimeFormat('pt-BR', { dateStyle: 'short', timeStyle: 'short' }).format(new Date(savedState.lastLoan.returnedAt))
            : '—';
        const details = {
            detalhesChaveNome: card.querySelector('h3').textContent,
            detalhesChaveLocal: card.querySelector('.key-location').textContent.trim(),
            detalhesChaveStatus: getKeyStatus(card),
            detalhesChaveResponsavel: loan?.borrower || currentHolder,
            detalhesChaveRetirada: loan ? formatDateTime(loan.date, loan.time) : '—',
            detalhesChaveDevolucao: loan ? formatDateTime(loan.expectedReturn) : '—',
            detalhesChaveUltimaDevolucao: lastReturn,
            detalhesChaveObservacoes: loan?.observations || '—',
        };

        Object.entries(details).forEach(([elementId, value]) => {
            const element = document.getElementById(elementId);

            if (element) element.textContent = value;
        });
    }

    keyCards.forEach(renderKeyCard);
    updateKeySelect();

    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-key-action]');

        if (!button) return;

        const card = button.closest('[data-key-id]');
        if (!card) return;

        const keyId = card.dataset.keyId;
        const keyName = card.querySelector('h3').textContent;

        if (button.dataset.keyAction === 'withdrawal') {
            if (getKeyStatus(card) !== 'Disponível') {
                showToast('Esta chave não está disponível para retirada.');
                return;
            }

            const keySelect = document.getElementById('emprestimoChave');
            if (keySelect) keySelect.value = keyId;

            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEmprestimo')).show();
            return;
        }

        if (button.dataset.keyAction === 'return') {
            const previousLoan = keyStates[keyId]?.currentLoan || {
                borrower: card.querySelector('.key-meta strong').textContent,
                date: '',
                time: '',
                expectedReturn: '',
                observations: '',
            };

            keyStates[keyId] = {
                status: 'Disponível',
                currentLoan: null,
                lastLoan: {
                    ...previousLoan,
                    returnedAt: new Date().toISOString(),
                },
            };

            const saved = persistKeyStates();
            renderKeyCard(card);
            updateKeySelect();
            const cardContainer = document.getElementById('listaChaves');
            if (cardContainer) updateCards(cardContainer);
            showToast(saved
                ? 'Devolução da ' + keyName + ' registrada.'
                : 'A devolução foi registrada nesta tela, mas não pôde ser salva neste navegador.');
            return;
        }

        if (button.dataset.keyAction === 'details') {
            fillKeyDetails(card);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDetalhesChave')).show();
        }
    });

    const loanForm = document.getElementById('formEmprestimo');

    loanForm?.addEventListener('submit', (event) => {
        event.preventDefault();

        if (!loanForm.reportValidity()) return;

        const formData = new FormData(loanForm);
        const keyId = formData.get('chave');
        const card = keyCards.find((keyCard) => keyCard.dataset.keyId === keyId);
        const currentStatus = card ? getKeyStatus(card) : keyStates[keyId]?.status;

        if (currentStatus === 'Emprestada' || currentStatus === 'Indisponível') {
            showToast('Esta chave não está disponível para retirada.');
            updateKeySelect();
            return;
        }

        keyStates[keyId] = {
            status: 'Emprestada',
            currentLoan: {
                borrower: formData.get('usuario'),
                date: formData.get('data'),
                time: formData.get('hora'),
                expectedReturn: formData.get('devolucao'),
                observations: formData.get('observacoes'),
            },
            lastLoan: keyStates[keyId]?.lastLoan || null,
        };

        const saved = persistKeyStates();

        if (card) {
            renderKeyCard(card);
            updateKeySelect();
            const cardContainer = document.getElementById('listaChaves');
            if (cardContainer) updateCards(cardContainer);
        }

        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEmprestimo')).hide();
        loanForm.reset();
        showToast(saved
            ? 'Retirada da chave registrada.'
            : 'A retirada foi registrada nesta tela, mas não pôde ser salva neste navegador.');
    });

    const userStorageKey = 'chaves:usuarios:v1';
    const userTable = document.getElementById('usuariosTabela');
    const userTableBody = userTable?.querySelector('tbody');

    function getInitialUsers() {
        if (!userTableBody) return [];

        return [...userTableBody.querySelectorAll('tr')].map((row, index) => ({
            id: 'usuario-' + (index + 1),
            name: row.querySelector('.person-cell strong').textContent.trim(),
            email: row.cells[1].textContent.trim(),
            type: row.querySelector('.type-label').textContent.trim(),
            status: row.querySelector('.status-pill').textContent.trim(),
        }));
    }

    let users = getInitialUsers();

    try {
        const savedUsers = JSON.parse(window.localStorage.getItem(userStorageKey) || 'null');
        if (Array.isArray(savedUsers)) users = savedUsers;
    } catch {
        users = getInitialUsers();
    }

    function persistUsers() {
        try {
            window.localStorage.setItem(userStorageKey, JSON.stringify(users));
            return true;
        } catch {
            return false;
        }
    }

    function createUserRow(user, index) {
        const row = document.createElement('tr');
        const nameCell = document.createElement('td');
        const personCell = document.createElement('div');
        const avatar = document.createElement('span');
        const name = document.createElement('strong');
        const emailCell = document.createElement('td');
        const typeCell = document.createElement('td');
        const type = document.createElement('span');
        const statusCell = document.createElement('td');
        const status = document.createElement('span');
        const statusDot = document.createElement('span');
        const actionCell = document.createElement('td');
        const avatarColors = ['avatar-red', 'avatar-blue', 'avatar-green', 'avatar-gold'];
        const initials = user.name.trim().split(/\s+/).slice(0, 2).map((part) => part.charAt(0)).join('').toUpperCase();

        row.dataset.userId = user.id;
        personCell.className = 'person-cell';
        avatar.className = 'mini-avatar ' + avatarColors[index % avatarColors.length];
        avatar.textContent = initials;
        avatar.setAttribute('aria-hidden', 'true');
        name.textContent = user.name;
        personCell.append(avatar, name);
        nameCell.append(personCell);
        emailCell.textContent = user.email;
        type.className = 'type-label';
        type.textContent = user.type;
        typeCell.append(type);
        status.className = 'status-pill ' + (user.status === 'Ativo' ? 'status-active' : 'status-inactive');
        statusDot.setAttribute('aria-hidden', 'true');
        status.append(statusDot, document.createTextNode(user.status));
        statusCell.append(status);
        actionCell.className = 'text-end';

        [
            ['view', 'bi-eye', 'Ver detalhes de '],
            ['edit', 'bi-pencil', 'Editar '],
            ['delete', 'bi-trash3', 'Remover '],
        ].forEach(([action, iconClass, label]) => {
            const button = document.createElement('button');
            const icon = document.createElement('i');

            button.type = 'button';
            button.className = 'icon-button' + (action === 'delete' ? ' text-danger' : '');
            button.dataset.userAction = action;
            button.dataset.userId = user.id;
            button.setAttribute('aria-label', label + user.name);
            icon.className = 'bi ' + iconClass;
            button.append(icon);
            actionCell.append(button);
        });

        row.append(nameCell, emailCell, typeCell, statusCell, actionCell);

        return row;
    }

    function renderPagination(table, totalPages, currentPage) {
        const pagination = document.querySelector('[data-table-pagination="' + table.id + '"]');
        if (!pagination) return;

        pagination.replaceChildren();

        function addPage(label, page, disabled = false, active = false, ariaLabel = '') {
            const item = document.createElement('li');
            const link = document.createElement(disabled || active ? 'span' : 'a');

            item.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '');
            link.className = 'page-link';
            link.textContent = label;

            if (disabled || active) {
                if (active) link.setAttribute('aria-current', 'page');
            } else {
                link.href = '#';
                link.dataset.tablePage = String(page);
                link.dataset.tableTarget = table.id;
                if (ariaLabel) link.setAttribute('aria-label', ariaLabel);
            }

            item.append(link);
            pagination.append(item);
        }

        addPage('‹', currentPage - 1, currentPage === 1, false, 'Página anterior');

        for (let page = 1; page <= totalPages; page += 1) {
            addPage(String(page), page, false, page === currentPage);
        }

        addPage('›', currentPage + 1, currentPage === totalPages, false, 'Próxima página');
    }

    function updateUsersTable(table, requestedPage = 1) {
        if (!table) return;

        const search = document.querySelector('[data-table-search="' + table.id + '"]');
        const filter = document.querySelector('[data-table-filter="' + table.id + '"]');
        const query = search?.value.trim().toLocaleLowerCase('pt-BR') ?? '';
        const selected = filter?.value.trim().toLocaleLowerCase('pt-BR') ?? '';
        const rows = [...table.querySelectorAll('tbody tr')];
        const matchingRows = rows.filter((row) => {
            const rowText = row.textContent.toLocaleLowerCase('pt-BR');
            return rowText.includes(query) && (!selected || rowText.includes(selected));
        });
        const pageSize = Number(table.dataset.pageSize || 2);
        const totalPages = Math.max(1, Math.ceil(matchingRows.length / pageSize));
        const currentPage = Math.min(Math.max(1, requestedPage), totalPages);
        const firstVisibleIndex = (currentPage - 1) * pageSize;
        const visibleRows = matchingRows.slice(firstVisibleIndex, firstVisibleIndex + pageSize);

        rows.forEach((row) => {
            row.hidden = !visibleRows.includes(row);
        });

        const count = table.closest('.content-panel')?.querySelector('[data-row-count]');
        const totalCount = table.closest('.content-panel')?.querySelector('[data-total-count]');
        if (count) count.textContent = String(visibleRows.length);
        if (totalCount) totalCount.textContent = String(matchingRows.length);
        renderPagination(table, totalPages, currentPage);
    }

    function renderUserTable() {
        if (!userTableBody) return;

        userTableBody.replaceChildren(...users.map(createUserRow));
        updateUsersTable(userTable, 1);
    }

    function prepareUserForm(user = null) {
        const form = document.getElementById('formUsuario');
        if (!form) return;

        form.reset();

        if (user) {
            form.dataset.userId = user.id;
            document.getElementById('usuarioNome').value = user.name;
            document.getElementById('usuarioEmail').value = user.email;
            document.getElementById('usuarioTipo').value = user.type;
            document.getElementById('usuarioStatus').value = user.status;
            document.getElementById('modalUsuarioEyebrow').textContent = 'ATUALIZAÇÃO DE CADASTRO';
            document.getElementById('modalUsuarioTitulo').textContent = 'Editar usuário';
            document.getElementById('salvarUsuarioBtn').textContent = 'Salvar alterações';
            return;
        }

        delete form.dataset.userId;
        document.getElementById('modalUsuarioEyebrow').textContent = 'NOVO CADASTRO';
        document.getElementById('modalUsuarioTitulo').textContent = 'Cadastrar usuário';
        document.getElementById('salvarUsuarioBtn').textContent = 'Salvar usuário';
    }

    function renderUserTableAndSave() {
        const saved = persistUsers();
        renderUserTable();

        return saved;
    }

    renderUserTable();

    function updateTable(table, requestedPage = Number(table.dataset.currentPage || 1)) {
        const search = document.querySelector(`[data-table-search="${table.id}"]`);
        const filter = document.querySelector(`[data-table-filter="${table.id}"]`);
        const query = search?.value.trim().toLocaleLowerCase('pt-BR') ?? '';
        const selected = filter?.value.trim().toLocaleLowerCase('pt-BR') ?? '';
        const rows = [...table.querySelectorAll('tbody tr')];
        const matchingRows = rows.filter((row) => {
            const rowText = row.textContent.toLocaleLowerCase('pt-BR');
            return rowText.includes(query) && (!selected || rowText.includes(selected));
        });
        const pageSize = Number(table.dataset.pageSize || 0);
        const totalPages = pageSize ? Math.max(1, Math.ceil(matchingRows.length / pageSize)) : 1;
        const currentPage = Math.min(Math.max(1, requestedPage), totalPages);
        const firstVisibleIndex = (currentPage - 1) * pageSize;
        const visibleRows = pageSize
            ? matchingRows.slice(firstVisibleIndex, firstVisibleIndex + pageSize)
            : matchingRows;

        table.dataset.currentPage = String(currentPage);
        rows.forEach((row) => {
            row.hidden = !visibleRows.includes(row);
        });

        const count = table.closest('.content-panel')?.querySelector('[data-row-count]');
        const totalCount = table.closest('.content-panel')?.querySelector('[data-total-count]');
        if (count) count.textContent = String(visibleRows.length);
        if (totalCount) totalCount.textContent = String(matchingRows.length);
        if (pageSize) renderPagination(table, totalPages, currentPage);
    }

    const loanStorageKey = 'chaves:emprestimos:v1';
    const loanTable = document.getElementById('emprestimosTabela');
    const loanRows = loanTable ? [...loanTable.querySelectorAll('tbody tr[data-loan-id]')] : [];
    let loanStates = {};
    let pendingLoanReturnId = null;

    try {
        loanStates = JSON.parse(window.localStorage.getItem(loanStorageKey) || '{}') || {};
    } catch {
        loanStates = {};
    }

    function getLoanStatus(row) {
        return loanStates[row.dataset.loanId]?.status || row.querySelector('.status-pill').textContent.trim();
    }

    function renderLoanRow(row) {
        if (getLoanStatus(row) !== 'Devolvido') return;

        const status = row.querySelector('.status-pill');
        const statusDot = document.createElement('span');

        status.className = 'status-pill status-returned';
        status.replaceChildren(statusDot, document.createTextNode('Devolvido'));
        row.querySelector('[data-loan-action="return"]')?.remove();
    }

    function persistLoanStates() {
        try {
            window.localStorage.setItem(loanStorageKey, JSON.stringify(loanStates));
            return true;
        } catch {
            return false;
        }
    }

    function parseLoanDate(value) {
        const [dateText, time = ''] = value.trim().split(/\s+/);
        const [day, month, year] = dateText.split('/');

        return {
            date: year && month && day ? [year, month, day].join('-') : '',
            time,
        };
    }

    function setLoanDetail(elementId, value) {
        const element = document.getElementById(elementId);

        if (element) element.textContent = value || '—';
    }

    function openLoanDetails(row) {
        const returnedAt = loanStates[row.dataset.loanId]?.returnedAt;

        setLoanDetail('detalhesEmprestimoChave', row.cells[0].querySelector('strong').textContent);
        setLoanDetail('detalhesEmprestimoUsuario', row.cells[1].textContent.trim());
        setLoanDetail('detalhesEmprestimoRetirada', row.cells[2].textContent.trim());
        setLoanDetail('detalhesEmprestimoPrevista', row.cells[3].textContent.trim());
        setLoanDetail('detalhesEmprestimoStatus', getLoanStatus(row));
        setLoanDetail(
            'detalhesEmprestimoDevolvida',
            returnedAt
                ? new Intl.DateTimeFormat('pt-BR', { dateStyle: 'short', timeStyle: 'short' }).format(new Date(returnedAt))
                : '—'
        );

        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDetalhesEmprestimo')).show();
    }

    loanRows.forEach(renderLoanRow);
    if (loanTable) updateTable(loanTable, 1);

    document.querySelectorAll('[data-table-search], [data-table-filter]').forEach((control) => {
        control.addEventListener('input', () => {
            const tableId = control.dataset.tableSearch || control.dataset.tableFilter;
            const table = document.getElementById(tableId);
            if (table) {
                if (table === userTable) {
                    updateUsersTable(table, 1);
                } else {
                    updateTable(table, 1);
                }
            }
        });
        control.addEventListener('change', () => {
            const tableId = control.dataset.tableSearch || control.dataset.tableFilter;
            const table = document.getElementById(tableId);
            if (table) {
                if (table === userTable) {
                    updateUsersTable(table, 1);
                } else {
                    updateTable(table, 1);
                }
            }
        });
    });

    document.addEventListener('click', (event) => {
        const target = event.target instanceof Element ? event.target : null;
        const pageLink = target?.closest('[data-table-page]');

        if (pageLink) {
            event.preventDefault();
            const table = document.getElementById(pageLink.dataset.tableTarget);

            if (table === userTable) {
                updateUsersTable(table, Number(pageLink.dataset.tablePage));
            } else if (table) {
                updateTable(table, Number(pageLink.dataset.tablePage));
            }

            return;
        }

        const button = target?.closest('[data-user-action]');
        if (!button) return;

        const action = button.dataset.userAction;

        if (action === 'create') {
            prepareUserForm();
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalUsuario')).show();
            return;
        }

        const user = users.find((record) => record.id === button.dataset.userId);
        if (!user) return;

        if (action === 'view') {
            document.getElementById('detalhesUsuarioNome').textContent = user.name;
            document.getElementById('detalhesUsuarioEmail').textContent = user.email;
            document.getElementById('detalhesUsuarioTipo').textContent = user.type;
            document.getElementById('detalhesUsuarioStatus').textContent = user.status;
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDetalhesUsuario')).show();
        } else if (action === 'edit') {
            prepareUserForm(user);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalUsuario')).show();
        } else if (action === 'delete') {
            const confirmationButton = document.getElementById('confirmarRemocaoUsuarioBtn');
            confirmationButton.dataset.userId = user.id;
            document.getElementById('confirmarRemocaoUsuarioTexto').textContent =
                'Tem certeza de que deseja remover ' + user.name + '?';
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalRemoverUsuario')).show();
        }
    });

    document.getElementById('confirmarRemocaoUsuarioBtn')?.addEventListener('click', (event) => {
        const userId = event.currentTarget.dataset.userId;
        users = users.filter((user) => user.id !== userId);
        const saved = renderUserTableAndSave();

        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalRemoverUsuario')).hide();
        showToast(saved
            ? 'Usuário removido.'
            : 'Usuário removido desta tela, mas não pôde ser salvo neste navegador.');
    });

    document.getElementById('formUsuario')?.addEventListener('submit', (event) => {
        event.preventDefault();

        const form = event.currentTarget;
        if (!form.reportValidity()) return;

        const formData = new FormData(form);
        const userId = form.dataset.userId;
        const email = String(formData.get('email')).trim();
        const duplicateEmail = users.some((user) =>
            user.email.toLocaleLowerCase('pt-BR') === email.toLocaleLowerCase('pt-BR') && user.id !== userId
        );

        if (duplicateEmail) {
            const emailInput = document.getElementById('usuarioEmail');
            emailInput.setCustomValidity('Este e-mail já está cadastrado.');
            emailInput.reportValidity();
            emailInput.setCustomValidity('');
            return;
        }

        const user = {
            id: userId || 'usuario-' + Date.now(),
            name: String(formData.get('nome')).trim(),
            email,
            type: String(formData.get('tipo')),
            status: String(formData.get('status')),
        };

        if (userId) {
            users = users.map((currentUser) => currentUser.id === userId ? user : currentUser);
        } else {
            users.unshift(user);
        }

        const saved = renderUserTableAndSave();
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalUsuario')).hide();
        prepareUserForm();
        showToast(saved
            ? (userId ? 'Cadastro do usuário atualizado.' : 'Usuário cadastrado.')
            : 'Alteração feita nesta tela, mas não pôde ser salva neste navegador.');
    });

    document.addEventListener('click', (event) => {
        const target = event.target instanceof Element ? event.target : null;
        const button = target?.closest('[data-loan-action]');

        if (!button) return;

        const row = button.closest('tr[data-loan-id]');
        if (!row) return;

        if (button.dataset.loanAction === 'details') {
            openLoanDetails(row);
            return;
        }

        if (button.dataset.loanAction === 'return') {
            pendingLoanReturnId = row.dataset.loanId;
            const keyName = row.cells[0].querySelector('strong').textContent;
            const borrower = row.cells[1].textContent.trim();

            document.getElementById('confirmarDevolucaoTexto').textContent =
                'Registrar a devolução da ' + keyName + ' que está com ' + borrower + '?';
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalConfirmarDevolucao')).show();
            return;
        }

        if (button.dataset.loanAction === 'notify') {
            const keyName = row.cells[0].querySelector('strong').textContent;
            const keyLocation = row.cells[0].querySelector('.cell-subtitle').textContent;
            const borrower = row.cells[1].textContent.trim();
            const expectedReturn = row.cells[3].textContent.trim();

            document.getElementById('textoNotificacaoEmprestimo').value =
                'Olá, ' + borrower + '! O prazo de devolução da ' + keyName + ' (' + keyLocation +
                ') venceu em ' + expectedReturn + '. Por favor, devolva a chave ou entre em contato com a unidade.';
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalNotificacaoEmprestimo')).show();
        }
    });

    document.getElementById('confirmarDevolucaoBtn')?.addEventListener('click', () => {
        const row = loanRows.find((loanRow) => loanRow.dataset.loanId === pendingLoanReturnId);
        if (!row) return;

        const returnedAt = new Date();
        const loanId = row.dataset.loanId;
        const keyId = row.dataset.keyId;
        const startedAt = parseLoanDate(row.cells[2].textContent);
        const expectedAt = parseLoanDate(row.cells[3].textContent);
        const previousLoan = keyStates[keyId]?.currentLoan || {
            borrower: row.cells[1].textContent.trim(),
            date: startedAt.date,
            time: startedAt.time,
            expectedReturn: expectedAt.date + (expectedAt.time ? 'T' + expectedAt.time : ''),
            observations: '',
        };

        loanStates[loanId] = {
            status: 'Devolvido',
            returnedAt: returnedAt.toISOString(),
        };
        keyStates[keyId] = {
            status: 'Disponível',
            currentLoan: null,
            lastLoan: {
                ...previousLoan,
                returnedAt: returnedAt.toISOString(),
            },
        };

        const loanSaved = persistLoanStates();
        const keySaved = persistKeyStates();

        renderLoanRow(row);
        if (loanTable) updateTable(loanTable);

        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalConfirmarDevolucao')).hide();
        pendingLoanReturnId = null;
        showToast(loanSaved && keySaved
            ? 'Devolução registrada.'
            : 'Devolução atualizada na tela, mas não pôde ser salva neste navegador.');
    });

    document.getElementById('copiarNotificacaoEmprestimo')?.addEventListener('click', async () => {
        const message = document.getElementById('textoNotificacaoEmprestimo');

        try {
            await navigator.clipboard.writeText(message.value);
            showToast('Mensagem copiada.');
        } catch {
            message.focus();
            message.select();
            const copied = document.execCommand('copy');
            showToast(copied ? 'Mensagem copiada.' : 'Selecione e copie a mensagem manualmente.');
        }
    });

    function updateCards(container) {
        const search = document.querySelector(`[data-card-search="${container.id}"]`);
        const filter = document.querySelector(`[data-card-filter="${container.id}"]`);
        const query = search?.value.trim().toLocaleLowerCase('pt-BR') ?? '';
        const selected = filter?.value.trim().toLocaleLowerCase('pt-BR') ?? '';

        container.querySelectorAll('[data-card-item]').forEach((card) => {
            const matchesQuery = card.textContent.toLocaleLowerCase('pt-BR').includes(query);
            const matchesFilter = !selected || card.dataset.filter.toLocaleLowerCase('pt-BR') === selected;
            card.hidden = !(matchesQuery && matchesFilter);
        });
    }

    document.querySelectorAll('[data-card-search], [data-card-filter]').forEach((control) => {
        const update = () => {
            const containerId = control.dataset.cardSearch || control.dataset.cardFilter;
            const container = document.getElementById(containerId);
            if (container) updateCards(container);
        };

        control.addEventListener('input', update);
        control.addEventListener('change', update);
    });

    document.querySelectorAll('[data-clear-search]').forEach((button) => {
        button.addEventListener('click', () => {
            const toolbar = button.closest('.toolbar');
            toolbar?.querySelectorAll('input[type="search"], select').forEach((control) => {
                control.value = '';
                control.dispatchEvent(new Event('input', { bubbles: true }));
                control.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });
    });

    const calendarDays = document.querySelector('[data-calendar-days]');
    const calendarWeekLabel = document.querySelector('[data-calendar-week-label]');
    const calendarSchedule = document.querySelector('[data-calendar-schedule]');

    if (calendarDays && calendarWeekLabel && calendarSchedule) {
        const scheduleRows = [...calendarSchedule.querySelectorAll('[data-calendar-schedule-date]')];
        const emptyScheduleMessage = calendarSchedule.querySelector('[data-calendar-empty]');
        const dateInCalendar = new Date(calendarDays.closest('[data-calendar-initial-date]').dataset.calendarInitialDate + 'T12:00:00');
        let selectedDate = Number.isNaN(dateInCalendar.getTime()) ? new Date() : dateInCalendar;

        function getMonday(date) {
            const monday = new Date(date.getFullYear(), date.getMonth(), date.getDate());
            const daysSinceMonday = (monday.getDay() + 6) % 7;

            monday.setDate(monday.getDate() - daysSinceMonday);

            return monday;
        }

        function getDateKey(date) {
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');

            return date.getFullYear() + '-' + month + '-' + day;
        }

        function formatCalendarDate(date) {
            return new Intl.DateTimeFormat('pt-BR', { day: 'numeric', month: 'short' }).format(date);
        }

        function renderCalendar() {
            const monday = getMonday(selectedDate);
            const sunday = new Date(monday);
            sunday.setDate(sunday.getDate() + 6);

            calendarWeekLabel.textContent = formatCalendarDate(monday) + ' – ' + formatCalendarDate(sunday);

            calendarDays.replaceChildren();

            for (let dayOffset = 0; dayOffset < 5; dayOffset++) {
                const date = new Date(monday);
                date.setDate(monday.getDate() + dayOffset);

                const dayButton = document.createElement('button');
                const dayName = new Intl.DateTimeFormat('pt-BR', { weekday: 'short' })
                    .format(date)
                    .replace('.', '')
                    .toLocaleUpperCase('pt-BR');
                const dayLabel = document.createElement('span');
                const dayNumber = document.createElement('strong');
                const isSelected = getDateKey(date) === getDateKey(selectedDate);

                dayButton.type = 'button';
                dayButton.className = 'week-day' + (isSelected ? ' selected' : '');
                dayButton.setAttribute('aria-label', new Intl.DateTimeFormat('pt-BR', {
                    dateStyle: 'full',
                }).format(date));
                dayButton.setAttribute('aria-pressed', String(isSelected));
                dayButton.dataset.calendarDate = getDateKey(date);
                dayLabel.textContent = dayName;
                dayNumber.textContent = String(date.getDate()).padStart(2, '0');
                dayButton.append(dayLabel, dayNumber);

                dayButton.addEventListener('click', () => {
                    selectedDate = date;
                    renderCalendar();
                });

                calendarDays.append(dayButton);
            }

            const selectedDateKey = getDateKey(selectedDate);
            const hasSchedule = scheduleRows.some((row) => row.dataset.calendarScheduleDate === selectedDateKey);

            scheduleRows.forEach((row) => {
                row.hidden = row.dataset.calendarScheduleDate !== selectedDateKey;
            });

            if (emptyScheduleMessage) {
                emptyScheduleMessage.hidden = hasSchedule;
            }
        }

        document.querySelectorAll('[data-calendar-step]').forEach((button) => {
            button.addEventListener('click', () => {
                const nextDate = new Date(selectedDate);
                nextDate.setDate(nextDate.getDate() + Number(button.dataset.calendarStep) * 7);
                selectedDate = nextDate;
                renderCalendar();
            });
        });

        renderCalendar();
    }

    document.querySelectorAll('[data-feedback]').forEach((button) => {
        button.addEventListener('click', () => showToast(button.dataset.feedback));
    });

    const profilePhotoInput = document.getElementById('profilePhotoInput');
    profilePhotoInput?.addEventListener('change', () => {
        if (profilePhotoInput.files?.length > 0) {
            document.getElementById('profilePhotoForm')?.requestSubmit();
        }
    });

    document.querySelectorAll('[data-demo-form]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            if (!form.reportValidity()) return;

            showToast('Formulário validado. A gravação precisa ser conectada ao banco de dados.');
        });
    });
})();
