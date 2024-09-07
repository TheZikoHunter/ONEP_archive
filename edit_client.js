document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.edit-button');
    const overlay = document.querySelector('.modal-overlay')
    buttons.forEach(button => {
        const parentDiv = button.closest('td');
        const editDialog = parentDiv.querySelector('.edit-dialog');
        const form = editDialog.querySelector('form')
        // Show dialog when the button is clicked
        button.addEventListener('click', (event) => {
            event.preventDefault();  // Prevent the form from submitting
            event.stopPropagation(); // Prevent the click from bubbling up
            editDialog.style.display = 'block';
            overlay.style.display = 'block'
        });

        // Close dialog when clicking the exit button inside the dialog
        const exitButton = editDialog.querySelector('.exit-button');

        if (exitButton) {
            exitButton.addEventListener('click', (event) => {
                event.preventDefault();
                editDialog.style.display = 'none';
                overlay.style.display = 'none'
            });
        }

        // Close dialog when clicking outside the dialog
        document.addEventListener('click', (event) => {
            if (!editDialog.contains(event.target) && !button.contains(event.target)) {
                editDialog.style.display = 'none';
                overlay.style.display = 'none'
            }
        });

        // Prevent the click inside the dialog from closing it
        editDialog.addEventListener('click', (event) => {
            event.stopPropagation();
        });

        form.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                // Make sure Enter key triggers form submission
                form.submit();
            }
        });
    });
});
