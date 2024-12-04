document.addEventListener('DOMContentLoaded', function () {
    const openModalButton = document.getElementById('openModal');
    const modal = new bootstrap.Modal(document.getElementById('modal'));

    if (openModalButton) {
        openModalButton.addEventListener('click', function () {
            modal.show(); 
        });
    }
});

