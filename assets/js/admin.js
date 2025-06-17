document.addEventListener('DOMContentLoaded', () => {
    // Find all forms that have a 'data-confirm' attribute
    const confirmationForms = document.querySelectorAll('form[data-confirm]');

    confirmationForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            // Get the confirmation message from the attribute
            const message = form.dataset.confirm;
            // Show the confirmation dialog. If the user clicks "Cancel", prevent the form from submitting.
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
});