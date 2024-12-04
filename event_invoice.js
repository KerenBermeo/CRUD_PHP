document.addEventListener('DOMContentLoaded', function () {
    const openModalButton = document.getElementById('openModal');
    const modal = new bootstrap.Modal(document.getElementById('modal'));
    const numeroFactutra = document.getElementById('numeroFactura');

    // Verificar si el botón de abrir modal existe antes de agregar el evento
    if (openModalButton) {
        openModalButton.addEventListener('click', function () {
            modal.show(); 
        });

        openModalButton.addEventListener('click', function(){
            // Realizar la petición al archivo PHP
            fetch('http://localhost/CRUD_PHP/invoiceCRUD.php?action=numeroFactura')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la solicitud');
                }
                return response.json(); // Parsear la respuesta como JSON
            })
            .then(data => {
                // Obtener el número de factura del JSON
                let numero = data.factura_id;

                // Insertar el número resultante en el span
                numeroFactutra.innerText = numero;
            })
            .catch(error => {
                // Mostrar "error" en el HTML si ocurre un problema
                numeroFactutra.innerText = 'error';
            });
        });
    }

});