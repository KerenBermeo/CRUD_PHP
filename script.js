document.addEventListener('DOMContentLoaded', function () {
    // Asegúrate de que los elementos existen antes de agregar los eventos
    const openModalButton = document.getElementById('openModal');
    const modal = new bootstrap.Modal(document.getElementById('modal')); // Crear una instancia del modal

    // Verificar si el botón de abrir modal existe antes de agregar el evento
    if (openModalButton) {
        openModalButton.addEventListener('click', function () {
            modal.show(); // Mostrar el modal cuando se hace clic en el botón
        });
        // Realizar la petición al archivo PHP
    }

});

