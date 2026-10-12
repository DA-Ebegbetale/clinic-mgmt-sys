document.querySelectorAll('form.search-bar').forEach(form => {
    const select = form.querySelector('select[name="field"]');
    const input = form.querySelector('input[name="value"]');

    if (!select || !input) return;

    const setState = () => {
        if (select.value === 'all' || select.value === 'all_archived' || select.value === 'all_archives') {
            input.disabled = true;
            input.value = '';
        } else {
            input.disabled = false;
        }
    };

    setState();
    select.addEventListener('change', setState);
});

const confirmForms = document.querySelectorAll('form.require-confirm');
confirmForms.forEach(form => {
    form.addEventListener('submit', function (e) {
        const message = form.getAttribute('data-confirm-message') || 'Are you sure you want to proceed?';
        if (!confirm(message)) {
            e.preventDefault();
            return;
        }

        // If this is an admin deactivate/restore that requires a reason, prompt the user and append a hidden input
        const action = (form.getAttribute('action') || '').toLowerCase();
        const needsReason = action.endsWith('/admin/users/deactivate') || action.endsWith('/admin/users/restore');

        if (needsReason) {
            const reason = prompt('Please enter a reason for this action (required):');
            if (!reason || reason.trim() === '') {
                e.preventDefault();
                alert('Reason is required.');
                return;
            }
            // Append hidden input named 'reason'
            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'reason';
            reasonInput.value = reason.trim();
            form.appendChild(reasonInput);
        }

        const needsNewPass = (form.getAttribute('action') || '').toLowerCase().endsWith('/admin/users/reset-password');

        if (needsNewPass) {
            const tempPass = prompt('Enter new temporary password (leave blank to auto-generate). Press Cancel to abort.');
            if (tempPass === null) { // user pressed Cancel
                e.preventDefault();
                return;
            }
            if (tempPass.trim() !== '') {
                const passInput = document.createElement('input');
                passInput.type = 'hidden';
                passInput.name = 'new_password';
                passInput.value = tempPass.trim();
                form.appendChild(passInput);
            }
            // if empty string OK pressed, allow submit and server will generate fallback password
        }

    });
});

// Toggle manual/generate password input on user provisioning form
(function () {
    const createForm = document.querySelector('form[action="/admin/users/store"]');
    if (!createForm) return;
    const radios = createForm.querySelectorAll('input[name="password_mode"]');
    const passInput = createForm.querySelector('#tempPasswordInput');

    const update = () => {
        const manual = createForm.querySelector('input[name="password_mode"]:checked')?.value === 'manual';
        if (passInput) {
            passInput.disabled = !manual;
            if (!manual) passInput.value = '';
        }
    };

    radios.forEach(r => r.addEventListener('change', update));
    update();
})();

// Sidebar toggle behavior
(function () {
    const toggle = document.getElementById('sidebarToggle');
    if (!toggle) return;

    const root = document.querySelector('.app-body');

    const updateButton = () => {
        toggle.textContent = root.classList.contains('sidebar-collapsed') ? '»' : '⟨';
    };

    toggle.addEventListener('click', () => {
        root.classList.toggle('sidebar-collapsed');
        updateButton();
    });

    updateButton();
})();


