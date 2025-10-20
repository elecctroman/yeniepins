document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-open-modal]').forEach((button) => {
        button.addEventListener('click', () => {
            const target = button.getAttribute('data-open-modal');
            App.toggleModal(target, true);
        });
    });

    document.querySelectorAll('[data-close-modal]').forEach((button) => {
        button.addEventListener('click', () => {
            const target = button.getAttribute('data-close-modal');
            App.toggleModal(target, false);
        });
    });
});
