/**
 * Shared CRM helpers (form submit state, Bootstrap 5 modal shim).
 */
function initializeFormSubmission(formSelector) {
    const form = $(formSelector);
    if (!form.length) return;

    form.on('submit', function () {
        const submitBtn = $(this).find('[type="submit"]:visible').first();
        if (!submitBtn.length) return;

        const spinner = submitBtn.find('.spinner-border');
        const btnText = submitBtn.find('.btn-text');

        submitBtn.prop('disabled', true);
        if (spinner.length) spinner.removeClass('d-none');
        if (btnText.length) btnText.text('Submitting...');
    });

    $(window).on('pageshow', function () {
        const submitBtn = form.find('[type="submit"]');
        const spinner = submitBtn.find('.spinner-border');
        const btnText = submitBtn.find('.btn-text');

        submitBtn.prop('disabled', false);
        if (spinner.length) spinner.addClass('d-none');
        if (btnText.length) btnText.text('Submit');
    });
}

function showCrmModal(modalId) {
    const el = document.getElementById(modalId);
    if (el && typeof bootstrap !== 'undefined') {
        bootstrap.Modal.getOrCreateInstance(el).show();
    }
}

function hideCrmModal(modalId) {
    const el = document.getElementById(modalId);
    if (el && typeof bootstrap !== 'undefined') {
        bootstrap.Modal.getOrCreateInstance(el).hide();
    }
}

if (typeof jQuery !== 'undefined' && typeof bootstrap !== 'undefined' && !jQuery.fn.modal) {
    jQuery.fn.modal = function (action) {
        return this.each(function () {
            const instance = bootstrap.Modal.getOrCreateInstance(this);
            if (action === 'show') instance.show();
            else if (action === 'hide') instance.hide();
            else if (action === 'toggle') instance.toggle();
        });
    };
}
