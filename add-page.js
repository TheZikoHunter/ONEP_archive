document.addEventListener('DOMContentLoaded', () => {
    const plus = document.querySelector('.plus')

    const dialog = document.querySelector('.add-dialog')

    const overlay = document.querySelector('.modal-overlay')

    plus.addEventListener('click', (event) => {
        event.preventDefault();  // Prevent the form from submitting
        event.stopPropagation(); // Prevent the click from bubbling up
        dialog.style.display = 'block';
        overlay.style.display = 'block'
    })

    document.addEventListener('click', (event) => {
        if (!dialog.contains(event.target) && !plus.contains(event.target)) {
            dialog.style.display = 'none';
            overlay.style.display = 'none'
        }
    });
        

    });
