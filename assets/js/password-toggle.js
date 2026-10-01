document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    const inputId = button.getAttribute('aria-controls');
    const input = document.getElementById(inputId);

    if (!input) {
        return;
    }

    button.addEventListener('click', () => {
        const isVisible = input.type === 'text';
        input.type = isVisible ? 'password' : 'text';
        button.setAttribute('aria-label', isVisible
            ? 'Afficher le mot de passe'
            : 'Masquer le mot de passe');
        button.setAttribute('title', isVisible
            ? 'Afficher le mot de passe'
            : 'Masquer le mot de passe');
    });
});
