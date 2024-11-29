document.addEventListener('DOMContentLoaded', function () {
    // Asegúrate de que los elementos existen antes de agregar los eventos
    const openModalButton = document.getElementById('openModal');
    const modal = new bootstrap.Modal(document.getElementById('modal')); // Crear una instancia del modal

    // Verificar si el botón de abrir modal existe antes de agregar el evento
    if (openModalButton) {
        openModalButton.addEventListener('click', function () {
            modal.show(); // Mostrar el modal cuando se hace clic en el botón
        });
    }

    // También puedes añadir el evento para el botón de cerrar el modal (esto es opcional si usas el data-bs-dismiss)
    const closeModalButton = document.getElementById('closeModal');
    if (closeModalButton) {
        closeModalButton.addEventListener('click', function () {
            modal.hide(); // Cerrar el modal si haces clic en "Cancelar"
        });
    }
});

