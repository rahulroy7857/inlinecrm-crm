import 'bootstrap';

function showCrmToast(type, message) {
    const stack = document.getElementById('toast-stack');
    if (!stack || !message) return;

    const icons = {
        success: 'bx-check-circle',
        error: 'bx-x-circle',
        warning: 'bx-error',
        info: 'bx-info-circle',
    };

    const titles = {
        success: 'Success',
        error: 'Error',
        warning: 'Warning',
        info: 'Info',
    };

    const el = document.createElement('div');
    el.className = `toast toast-${type} show`;
    el.setAttribute('role', 'alert');
    el.innerHTML = `
        <div class="toast-header">
            <i class="bx ${icons[type] || icons.info} me-2"></i>
            <strong class="me-auto">${titles[type] || 'Notice'}</strong>
            <small>Just now</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">${message}</div>
    `;
    stack.appendChild(el);

    const toast = new bootstrap.Toast(el, { delay: 4000, autohide: true });
    toast.show();
    el.addEventListener('hidden.bs.toast', () => el.remove());
}

window.showCrmToast = showCrmToast;
window.openCrmDeleteModal = openCrmDeleteModal;
window.openCrmPickModal = openCrmPickModal;

function setupBootstrapModalShim() {
    if (typeof jQuery === 'undefined' || typeof bootstrap === 'undefined' || jQuery.fn.modal) {
        return;
    }

    jQuery.fn.modal = function (action) {
        return this.each(function () {
            const instance = bootstrap.Modal.getOrCreateInstance(this);
            if (action === 'show') instance.show();
            else if (action === 'hide') instance.hide();
            else if (action === 'toggle') instance.toggle();
        });
    };
}

let crmDeleteCallback = null;
let crmPickCallback = null;

function openCrmDeleteModal(message, onConfirm) {
    const modalEl = document.getElementById('crmDeleteModal');
    if (!modalEl || typeof bootstrap === 'undefined') {
        if (window.confirm(message || 'Are you sure you want to delete this item?')) {
            onConfirm();
        }
        return;
    }

    const messageEl = document.getElementById('crmDeleteMessage');
    if (messageEl) {
        messageEl.textContent = message || 'Are you sure you want to delete this item?';
    }

    crmDeleteCallback = onConfirm;
    bootstrap.Modal.getOrCreateInstance(modalEl).show();
}

function openCrmPickModal(message, onConfirm) {
    const modalEl = document.getElementById('crmPickModal');
    if (!modalEl || typeof bootstrap === 'undefined') {
        if (window.confirm(message || 'Are you sure you want to pick this lead?')) {
            onConfirm();
        }
        return;
    }

    const messageEl = document.getElementById('crmPickMessage');
    if (messageEl) {
        messageEl.textContent = message || 'Are you sure you want to pick this lead?';
    }

    crmPickCallback = onConfirm;
    bootstrap.Modal.getOrCreateInstance(modalEl).show();
}

function setupCrmDeleteConfirmation() {
    const modalEl = document.getElementById('crmDeleteModal');
    const confirmBtn = document.getElementById('crmDeleteConfirmBtn');
    const pickModalEl = document.getElementById('crmPickModal');
    const pickConfirmBtn = document.getElementById('crmPickConfirmBtn');

    confirmBtn?.addEventListener('click', () => {
        if (crmDeleteCallback) {
            crmDeleteCallback();
            crmDeleteCallback = null;
        }
        if (modalEl) {
            bootstrap.Modal.getOrCreateInstance(modalEl).hide();
        }
    });

    modalEl?.addEventListener('hidden.bs.modal', () => {
        crmDeleteCallback = null;
    });

    pickConfirmBtn?.addEventListener('click', () => {
        if (crmPickCallback) {
            crmPickCallback();
            crmPickCallback = null;
        }
        if (pickModalEl) {
            bootstrap.Modal.getOrCreateInstance(pickModalEl).hide();
        }
    });

    pickModalEl?.addEventListener('hidden.bs.modal', () => {
        crmPickCallback = null;
    });

    document.addEventListener('click', (event) => {
        const pickLink = event.target.closest('a[data-confirm-pick]');
        if (pickLink) {
            event.preventDefault();
            openCrmPickModal(pickLink.getAttribute('data-confirm-pick'), () => {
                window.location.href = pickLink.href;
            });
            return;
        }

        const link = event.target.closest('a[data-confirm-delete]');
        if (link) {
            event.preventDefault();
            openCrmDeleteModal(link.getAttribute('data-confirm-delete'), () => {
                window.location.href = link.href;
            });
            return;
        }

        const legacy = event.target.closest('a[onclick*="confirm"], button[onclick*="confirm"]');
        if (!legacy) return;

        const onclick = legacy.getAttribute('onclick');
        if (!onclick || !onclick.includes('confirm')) return;

        event.preventDefault();
        event.stopPropagation();

        const match = onclick.match(/confirm\((['"])(.*?)\1\)/);
        const message = match?.[2] || 'Are you sure you want to delete this item?';
        const isPickAction = /pick/i.test(message) || /pick/i.test(legacy.href || '');

        const openModal = isPickAction ? openCrmPickModal : openCrmDeleteModal;
        openModal(message, () => {
            if (legacy.tagName === 'A' && legacy.href) {
                window.location.href = legacy.href;
                return;
            }
            if (legacy.type === 'submit') {
                const form = legacy.closest('form');
                if (form) {
                    form.dataset.deleteConfirmed = '1';
                    form.submit();
                }
            }
        });
    }, true);

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) return;

        const submitter = event.submitter;
        const message =
            submitter?.getAttribute('data-confirm-delete') ||
            form.getAttribute('data-confirm-delete');

        if (!message) return;

        if (form.dataset.deleteConfirmed === '1') {
            delete form.dataset.deleteConfirmed;
            return;
        }

        event.preventDefault();
        openCrmDeleteModal(message, () => {
            form.dataset.deleteConfirmed = '1';
            if (submitter) {
                form.requestSubmit(submitter);
            } else {
                form.submit();
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    setupBootstrapModalShim();

    const sidebar = document.getElementById('layout-menu');
    const overlay = document.getElementById('sidebar-overlay');

    const closeSidebar = () => {
        sidebar?.classList.remove('sidebar-open');
        overlay?.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };

    const openSidebar = () => {
        sidebar?.classList.add('sidebar-open');
        overlay?.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    };

    document.querySelectorAll('[data-sidebar-toggle]').forEach((toggle) => {
        toggle.addEventListener('click', (event) => {
            event.preventDefault();
            if (sidebar?.classList.contains('sidebar-open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    });

    overlay?.addEventListener('click', closeSidebar);

    document.querySelectorAll('.menu-toggle').forEach((toggle) => {
        toggle.addEventListener('click', (event) => {
            event.preventDefault();
            const item = toggle.closest('.menu-item');
            item?.classList.toggle('open');
        });
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            closeSidebar();
        }
    });

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => {
        new bootstrap.Tooltip(el);
    });

    setupCrmDeleteConfirmation();

    document.querySelectorAll('.toast-stack-item').forEach((item) => {
        showCrmToast(item.dataset.toastType, item.dataset.toastMessage);
        item.remove();
    });

    document.querySelectorAll('.auto-hide-toast, .bs-toast.auto-hide-toast').forEach((el) => {
        const toast = bootstrap.Toast.getOrCreateInstance(el, { delay: 4000, autohide: true });
        toast.show();
    });

    setTimeout(() => {
        document.querySelectorAll('.auto-hide-alert').forEach((alert) => {
            bootstrap.Alert.getOrCreateInstance(alert).close();
        });
    }, 3000);
});
