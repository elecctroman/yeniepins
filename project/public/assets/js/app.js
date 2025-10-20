const App = (() => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ||
        document.querySelector('input[name="_csrf"]')?.value || '';

    const fetchWithCsrf = (url, options = {}) => {
        const headers = new Headers(options.headers || {});
        if (csrfToken) {
            headers.set('X-CSRF-TOKEN', csrfToken);
        }
        return fetch(url, {
            ...options,
            headers,
        });
    };

    const createToast = (message, variant = 'info') => {
        const container = document.querySelector('.toast-container') || (() => {
            const el = document.createElement('div');
            el.className = 'toast-container';
            document.body.appendChild(el);
            return el;
        })();
        const toast = document.createElement('div');
        toast.className = `toast toast--${variant}`;
        toast.textContent = message;
        container.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('is-leaving');
            toast.addEventListener('transitionend', () => toast.remove());
        }, 3500);
    };

    const toggleModal = (id, show = true) => {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.toggle('is-active', show);
    };

    const copyToClipboard = (text) => {
        return navigator.clipboard?.writeText(text) || Promise.reject('Desteklenmiyor');
    };

    const handleCopyButtons = () => {
        document.querySelectorAll('[data-copy]').forEach((btn) => {
            btn.addEventListener('click', async () => {
                const value = btn.getAttribute('data-copy');
                try {
                    await copyToClipboard(value);
                    createToast('Panoya kopyalandı', 'success');
                } catch (e) {
                    createToast('Kopyalama başarısız', 'danger');
                }
            });
        });
    };

    const setupFormValidation = () => {
        document.querySelectorAll('form[data-validate]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                const invalid = Array.from(form.elements).some((el) => el.required && !el.value.trim());
                if (invalid) {
                    event.preventDefault();
                    createToast('Lütfen zorunlu alanları doldurun', 'warning');
                }
            });
        });
    };

    document.addEventListener('DOMContentLoaded', () => {
        handleCopyButtons();
        setupFormValidation();
    });

    return { fetchWithCsrf, createToast, toggleModal, copyToClipboard };
})();
