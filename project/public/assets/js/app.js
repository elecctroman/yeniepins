(() => {
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

    const ensureToastContainer = () => {
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
        return container;
    };

    const toast = (message, variant = 'info', timeout = 4000) => {
        const container = ensureToastContainer();
        const el = document.createElement('div');
        el.className = `toast toast--${variant}`;
        el.textContent = message;
        container.appendChild(el);
        const remove = () => {
            el.classList.add('is-leaving');
            el.addEventListener('animationend', () => el.remove(), { once: true });
        };
        setTimeout(remove, timeout);
    };

    const modalElement = () => document.getElementById('global-modal');

    const modalOpen = (options = {}) => {
        const modal = modalElement();
        if (!modal) return;
        const { title = 'Bilgi', body = '', footer } = options;
        const titleEl = modal.querySelector('[data-modal-title]');
        if (titleEl) {
            titleEl.textContent = title;
        }
        const bodyEl = modal.querySelector('[data-modal-body]');
        if (bodyEl) {
            bodyEl.innerHTML = body;
        }
        if (footer !== undefined) {
            const footerEl = modal.querySelector('[data-modal-footer]');
            if (footerEl) {
                footerEl.innerHTML = footer;
            }
        }
        modal.classList.add('is-active');
        modal.setAttribute('aria-hidden', 'false');
    };

    const modalClose = () => {
        const modal = modalElement();
        if (!modal) return;
        modal.classList.remove('is-active');
        modal.setAttribute('aria-hidden', 'true');
    };

    const copyToClipboard = (text) => navigator.clipboard?.writeText(text) ?? Promise.reject('Desteklenmiyor');

    const kopyalaButonlari = () => {
        document.querySelectorAll('[data-copy]').forEach((btn) => {
            btn.addEventListener('click', async () => {
                const value = btn.getAttribute('data-copy');
                if (!value) return;
                let text = value;
                if (value.startsWith('#')) {
                    const target = document.querySelector(value);
                    text = target ? target.innerText.trim() : '';
                }
                if (!text) return;
                try {
                    await copyToClipboard(text);
                    toast('Panoya kopyalandı', 'success');
                } catch (error) {
                    toast('Kopyalama başarısız', 'danger');
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
                    toast('Lütfen zorunlu alanları doldurun', 'warning');
                }
            });
        });
    };

    const setupSidebarToggle = () => {
        const toggle = document.querySelector('[data-sidebar-toggle]');
        const sidebar = document.querySelector('[data-sidebar]');
        if (!toggle || !sidebar) return;
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('is-open');
        });
        sidebar.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => sidebar.classList.remove('is-open'));
        });
    };

    const setupModalCloseButtons = () => {
        document.querySelectorAll('[data-modal-close]').forEach((btn) => {
            btn.addEventListener('click', modalClose);
        });
        const modal = modalElement();
        if (modal) {
            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    modalClose();
                }
            });
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        kopyalaButonlari();
        setupFormValidation();
        setupSidebarToggle();
        setupModalCloseButtons();
    });

    window.toast = toast;
    window.modalOpen = modalOpen;
    window.modalClose = modalClose;
    window.kopyalaButonlari = kopyalaButonlari;
    window.kopyalaButonları = kopyalaButonlari;
    window.App = { fetchWithCsrf, toast, modalOpen, modalClose, kopyalaButonlari };
})();
