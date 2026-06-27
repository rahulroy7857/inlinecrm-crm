function initializeFormSubmission(formSelector) {
    const form = $(formSelector);
    
    if (!form.length) return;

    // Add CSRF token to form if not present
    if (!form.find('input[name="_token"]').length) {
        const token = $('meta[name="csrf-token"]').attr('content');
        form.prepend(`<input type="hidden" name="_token" value="${token}">`);
    }

    form.on('submit', function(e) {
        // Get the submit button
        const submitBtn = $(this).find('[type="submit"]');
        const spinner = submitBtn.find('.spinner-border');
        const btnText = submitBtn.find('.btn-text');
        const originalText = btnText.text();

        // Update CSRF token before submission
        const token = $('meta[name="csrf-token"]').attr('content');
        $(this).find('input[name="_token"]').val(token);

        // Show loading state
        spinner.removeClass('d-none');
        btnText.text('Submitting...');
        submitBtn.prop('disabled', true);

        // Store form data
        const formData = new FormData(this);

        // If there are validation errors, enable the submit button again
        if (form.find('.is-invalid').length > 0) {
            submitBtn.prop('disabled', false);
            spinner.addClass('d-none');
            btnText.text(originalText);
            return;
        }

        // Disable only the submit button during submission
        submitBtn.prop('disabled', true);
    });

    // Reset form only on modal/offcanvas close
    const modal = form.closest('.modal');
    const offcanvas = form.closest('.offcanvas');

    if (modal.length) {
        modal.on('hidden.bs.modal', () => resetForm(form));
    }

    if (offcanvas.length) {
        offcanvas.on('hidden.bs.offcanvas', () => resetForm(form));
    }
}

function resetForm(form) {
    form[0].reset();
    const submitBtn = form.find('[type="submit"]');
    const spinner = submitBtn.find('.spinner-border');
    const btnText = submitBtn.find('.btn-text');
    
    submitBtn.prop('disabled', false);
    spinner.addClass('d-none');
    btnText.text('Submit');
    
    // Reset validation states
    form.find('.is-invalid').removeClass('is-invalid');
    form.find('.select2').val(null).trigger('change');
}