document.addEventListener('DOMContentLoaded', () => {
    const maintenanceToggle = document.querySelector('[data-maintenance-toggle]');
    if (maintenanceToggle) {
        maintenanceToggle.addEventListener('change', () => {
            App.createToast('Bakım modu güncellendi', 'info');
        });
    }

    document.querySelectorAll('[data-role-action]').forEach((button) => {
        button.addEventListener('click', () => {
            const role = button.getAttribute('data-role-action');
            App.createToast(`${role} rolü için işlem tetiklendi`, 'success');
        });
    });
});
