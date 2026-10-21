(() => {
    const resourceTitles = {
        funcionario: { create: 'Novo funcionário', edit: 'Editar funcionário' },
        chave: { create: 'Nova chave', edit: 'Editar chave' },
        emprestimo: { create: 'Novo empréstimo', edit: 'Editar empréstimo' },
    };
    const resourceDetailFields = {
        funcionario: [['Nome', 'nome'], ['E-mail', 'email'], ['Cargo', 'cargo']],
        chave: [['Identificação', 'nomeChave'], ['Localização', 'localizacao'], ['Situação', 'status']],
        emprestimo: [['Funcionário', 'funcionario'], ['Chave', 'chave'], ['Retirada', 'retirada'], ['Devolução', 'devolucao'], ['Situação', 'status']],
    };

    function setResourceForm(resource, button, isEditing) {
        const form = document.querySelector(`[data-resource-form="${resource}"]`);

        if (!form) return;

        form.reset();
        form.action = isEditing ? button.dataset.updateUrl : form.dataset.storeUrl;

        const methodOverride = form.querySelector('[data-method-override]');
        if (methodOverride) methodOverride.value = isEditing ? 'PUT' : '';

        form.querySelectorAll('[data-edit-only] input, [data-edit-only] select').forEach((field) => {
            field.disabled = !isEditing;
        });

        form.querySelectorAll('[data-edit-field]').forEach((field) => {
            if (!isEditing) return;

            const datasetKey = field.name.replace(/_([a-z])/g, (_, letter) => letter.toUpperCase());
            field.value = button.dataset[datasetKey] ?? '';
        });

        const title = document.querySelector(`[data-resource-title="${resource}"]`);
        if (title) title.textContent = resourceTitles[resource][isEditing ? 'edit' : 'create'];
    }

    document.addEventListener('click', (event) => {
        if (!(event.target instanceof Element)) return;

        const createButton = event.target.closest('[data-create-resource]');
        if (createButton) {
            setResourceForm(createButton.dataset.createResource, createButton, false);
        }

        const viewButton = event.target.closest('[data-view-resource]');
        if (viewButton) {
            const resource = viewButton.dataset.viewResource;
            const title = document.getElementById('modalDetalhesTitulo');
            const details = document.getElementById('detalhesRecurso');

            if (title && details) {
                title.textContent = viewButton.dataset.detailTitle || 'Detalhes do registro';
                details.replaceChildren();

                (resourceDetailFields[resource] || []).forEach(([label, key]) => {
                    const term = document.createElement('dt');
                    const description = document.createElement('dd');

                    term.className = 'col-sm-4 text-body-secondary';
                    term.textContent = label;
                    description.className = 'col-sm-8';
                    description.textContent = viewButton.dataset[`detail${key.charAt(0).toUpperCase()}${key.slice(1)}`] || '—';
                    details.append(term, description);
                });
            }
        }

        const editButton = event.target.closest('[data-edit-resource]');
        if (editButton) {
            setResourceForm(editButton.dataset.editResource, editButton, true);
        }

        const deleteButton = event.target.closest('[data-delete-form]');
        if (deleteButton) {
            const message = document.getElementById('mensagemConfirmacao');
            const confirmButton = document.getElementById('confirmarExclusao');

            if (message) message.textContent = deleteButton.dataset.confirmMessage || 'Deseja excluir este registro?';
            if (confirmButton) confirmButton.dataset.formId = deleteButton.dataset.deleteForm;
        }
    });

    document.getElementById('confirmarExclusao')?.addEventListener('click', (event) => {
        const formId = event.currentTarget.dataset.formId;
        const form = formId ? document.querySelector(formId) : null;

        if (form instanceof HTMLFormElement) form.requestSubmit();
    });

    const profilePhotoInput = document.getElementById('profilePhotoInput');
    profilePhotoInput?.addEventListener('change', () => {
        if (profilePhotoInput.files?.length) profilePhotoInput.form?.requestSubmit();
    });

    const modalId = document.querySelector('[data-open-modal]')?.dataset.openModal;
    const modalElement = modalId ? document.getElementById(modalId) : null;

    if (modalElement && window.bootstrap) {
        window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
    }
})();
