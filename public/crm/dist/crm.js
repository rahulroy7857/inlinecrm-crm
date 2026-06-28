(function () {
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
        el.className = 'toast toast-' + type + ' show';
        el.setAttribute('role', 'alert');
        el.innerHTML =
            '<div class="toast-header">' +
            '<i class="bx ' + (icons[type] || icons.info) + ' me-2"></i>' +
            '<strong class="me-auto">' + (titles[type] || 'Notice') + '</strong>' +
            '<small>Just now</small>' +
            '<button type="button" class="btn-close" data-bs-dismiss="toast"></button>' +
            '</div>' +
            '<div class="toast-body">' + message + '</div>';
        stack.appendChild(el);

        const toast = new bootstrap.Toast(el, { delay: 4000, autohide: true });
        toast.show();
        el.addEventListener('hidden.bs.toast', function () {
            el.remove();
        });
    }

    window.showCrmToast = showCrmToast;
    window.openCrmDeleteModal = openCrmDeleteModal;

    function setupBootstrapModalShim() {
        if (typeof jQuery === 'undefined' || typeof bootstrap === 'undefined' || jQuery.fn.modal) {
            return;
        }

        jQuery.fn.modal = function (action) {
            return this.each(function () {
                var instance = bootstrap.Modal.getOrCreateInstance(this);
                if (action === 'show') instance.show();
                else if (action === 'hide') instance.hide();
                else if (action === 'toggle') instance.toggle();
            });
        };
    }

    var crmDeleteCallback = null;

    function openCrmDeleteModal(message, onConfirm) {
        var modalEl = document.getElementById('crmDeleteModal');
        if (!modalEl || typeof bootstrap === 'undefined') {
            if (window.confirm(message || 'Are you sure you want to delete this item?')) {
                onConfirm();
            }
            return;
        }
        var messageEl = document.getElementById('crmDeleteMessage');
        if (messageEl) {
            messageEl.textContent = message || 'Are you sure you want to delete this item?';
        }
        crmDeleteCallback = onConfirm;
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }

    function setupCrmDeleteConfirmation() {
        var modalEl = document.getElementById('crmDeleteModal');
        var confirmBtn = document.getElementById('crmDeleteConfirmBtn');

        if (confirmBtn) {
            confirmBtn.addEventListener('click', function () {
                if (crmDeleteCallback) {
                    crmDeleteCallback();
                    crmDeleteCallback = null;
                }
                if (modalEl) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).hide();
                }
            });
        }

        if (modalEl) {
            modalEl.addEventListener('hidden.bs.modal', function () {
                crmDeleteCallback = null;
            });
        }

        document.addEventListener('click', function (event) {
            var link = event.target.closest('a[data-confirm-delete]');
            if (link) {
                event.preventDefault();
                openCrmDeleteModal(link.getAttribute('data-confirm-delete'), function () {
                    window.location.href = link.href;
                });
                return;
            }

            var legacy = event.target.closest('a[onclick*="confirm"], button[onclick*="confirm"]');
            if (!legacy) return;

            var onclick = legacy.getAttribute('onclick');
            if (!onclick || onclick.indexOf('confirm') === -1) return;

            event.preventDefault();
            event.stopPropagation();

            var match = onclick.match(/confirm\((['"])(.*?)\1\)/);
            var message = (match && match[2]) ? match[2] : 'Are you sure you want to delete this item?';

            openCrmDeleteModal(message, function () {
                if (legacy.tagName === 'A' && legacy.href) {
                    window.location.href = legacy.href;
                    return;
                }
                if (legacy.type === 'submit') {
                    var form = legacy.closest('form');
                    if (form) {
                        form.dataset.deleteConfirmed = '1';
                        form.submit();
                    }
                }
            });
        }, true);

        document.addEventListener('submit', function (event) {
            var form = event.target;
            if (!form || form.tagName !== 'FORM') return;

            var submitter = event.submitter;
            var message = (submitter && submitter.getAttribute('data-confirm-delete')) ||
                form.getAttribute('data-confirm-delete');

            if (!message) return;

            if (form.dataset.deleteConfirmed === '1') {
                delete form.dataset.deleteConfirmed;
                return;
            }

            event.preventDefault();
            openCrmDeleteModal(message, function () {
                form.dataset.deleteConfirmed = '1';
                if (submitter && form.requestSubmit) {
                    form.requestSubmit(submitter);
                } else {
                    form.submit();
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        setupBootstrapModalShim();

        const sidebar = document.getElementById('layout-menu');
        const overlay = document.getElementById('sidebar-overlay');

        const closeSidebar = function () {
            sidebar?.classList.remove('sidebar-open');
            overlay?.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };

        const openSidebar = function () {
            sidebar?.classList.add('sidebar-open');
            overlay?.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        };

        document.querySelectorAll('[data-sidebar-toggle]').forEach(function (toggle) {
            toggle.addEventListener('click', function (event) {
                event.preventDefault();
                if (sidebar?.classList.contains('sidebar-open')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });
        });

        overlay?.addEventListener('click', closeSidebar);

        document.querySelectorAll('.menu-toggle').forEach(function (toggle) {
            toggle.addEventListener('click', function (event) {
                event.preventDefault();
                const item = toggle.closest('.menu-item');
                item?.classList.toggle('open');
            });
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth >= 1024) {
                closeSidebar();
            }
        });

        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
            new bootstrap.Tooltip(el);
        });

        setupCrmDeleteConfirmation();

        document.querySelectorAll('.toast-stack-item').forEach(function (item) {
            showCrmToast(item.dataset.toastType, item.dataset.toastMessage);
            item.remove();
        });

        document.querySelectorAll('.auto-hide-toast, .bs-toast.auto-hide-toast').forEach(function (el) {
            const toast = bootstrap.Toast.getOrCreateInstance(el, { delay: 4000, autohide: true });
            toast.show();
        });

        setTimeout(function () {
            document.querySelectorAll('.auto-hide-alert').forEach(function (alert) {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            });
        }, 3000);
    });
})();
