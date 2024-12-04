document.addEventListener('DOMContentLoaded', function () {
    const openModalButton = document.getElementById('openModal');
    const modalElement = document.getElementById('modal');
    const modal = new bootstrap.Modal(modalElement);
    const numeroFactura = document.getElementById('numeroFactura');

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
                numeroFactura.innerText = numero;
            })
            .catch(error => {
                // Mostrar "error" en el HTML si ocurre un problema
                numeroFactura.innerText = 'error';
            });
        });
    }

    // Función para actualizar el total
    function actualizarTotal() {
        const cantidad = document.getElementById("cantidad").value;
        const valor = document.getElementById("valor").value;
        const total = document.getElementById("total");
    
        // Verifica que cantidad y valor no estén vacíos y sean números
        if (cantidad && valor) {
            total.value = (cantidad * valor).toFixed(2); // Calcula el total y lo muestra con 2 decimales
        } else {
            total.value = ""; // Si alguno de los campos está vacío, no muestra nada
        }
    }
    
    // Asegurarse de agregar los eventos solo cuando el modal esté visible
    modalElement.addEventListener('shown.bs.modal', function () {
        // Agrega eventos para actualizar el total cuando cambian los valores
        document.getElementById("cantidad").addEventListener("input", actualizarTotal);
        document.getElementById("valor").addEventListener("input", actualizarTotal);
    });

    // Si el modal ya está abierto, agrega los eventos para actualizar el total
    if (modalElement.classList.contains('show')) {
        document.getElementById("cantidad").addEventListener("input", actualizarTotal);
        document.getElementById("valor").addEventListener("input", actualizarTotal);
    }
});
