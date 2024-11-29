<?php 
// Crear  cliente
function crearCliente($pdo, $nombre, $apellido, $tipoDocumento, $numeroDocumento, $telefono, $fechaNacimiento) {
    try {
        $sql = "INSERT INTO cliente (nombre, apellido, tipo_documento, numero_documento, telefono, fecha_nacimiento) 
                VALUES (:nombre, :apellido, :tipoDocumento, :numeroDocumento, :telefono, :fechaNacimiento)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':tipoDocumento', $tipoDocumento);
        $stmt->bindParam(':numeroDocumento', $numeroDocumento);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':fechaNacimiento', $fechaNacimiento);
        $stmt->execute();
        header("Location: client.html");
        exit;
    } catch (PDOException $e) {
        echo "Error al crear el cliente: " . $e->getMessage();
    }
}

// Leer todos los clientes
function leerClientes($pdo) {
    try {
        $sql = "SELECT * FROM cliente";
        $stmt = $pdo->query($sql);
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($clientes as $cliente) {
            echo "ID: " . $cliente['id'] . "<br>";
            echo "Nombre: " . $cliente['nombre'] . " " . $cliente['apellido'] . "<br>";
            echo "Tipo de documento: " . $cliente['tipo_documento'] . "<br>";
            echo "Número de documento: " . $cliente['numero_documento'] . "<br>";
            echo "Teléfono: " . $cliente['telefono'] . "<br>";
            echo "Fecha de nacimiento: " . $cliente['fecha_nacimiento'] . "<br><br>";
        }
    } catch (PDOException $e) {
        echo "Error al leer los clientes: " . $e->getMessage();
    }
}

// Actualizar un cliente
function actualizarCliente($pdo, $id, $nombre, $apellido, $telefono) {
    try {
        $sql = "UPDATE cliente SET nombre = :nombre, apellido = :apellido, telefono = :telefono WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        echo "Cliente actualizado exitosamente.";
    } catch (PDOException $e) {
        echo "Error al actualizar el cliente: " . $e->getMessage();
    }
}

// Eliminar un cliente
function eliminarCliente($pdo, $id) {
    try {
        $sql = "DELETE FROM cliente WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        echo "Cliente eliminado exitosamente.";
    } catch (PDOException $e) {
        echo "Error al eliminar el cliente: " . $e->getMessage();
    }
}

// Manejo de datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST['accion'] ?? '';

    switch ($accion) {
        case 'create_client':
            // Obtener los datos del formulario para crear un cliente
            $nombre = htmlspecialchars(trim($_POST['nombre'] ?? ''));
            $apellido = htmlspecialchars(trim($_POST['apellido'] ?? ''));
            $tipoDocumento = htmlspecialchars(trim($_POST['tipoDocumento'] ?? ''));
            $numeroDocumento = htmlspecialchars(trim($_POST['numeroDocumento'] ?? ''));
            $telefono = htmlspecialchars(trim($_POST['telefono'] ?? ''));
            $fechaNacimiento = htmlspecialchars(trim($_POST['fechaNacimiento'] ?? ''));

            // Verificar que todos los campos estén completos
            if ($nombre && $apellido && $tipoDocumento && $numeroDocumento && $telefono && $fechaNacimiento) {
                crearCliente($pdo, $nombre, $apellido, $tipoDocumento, $numeroDocumento, $telefono, $fechaNacimiento);
            } else {
                echo "Error: Todos los campos son obligatorios.";
            }
            break;

        case 'read_client':
            leerClientes($pdo);
            break;

        case 'update_client':
            // Obtener los datos del formulario para actualizar un cliente
            $id = intval($_POST['id'] ?? 0);
            $nombre = htmlspecialchars(trim($_POST['nombre'] ?? ''));
            $apellido = htmlspecialchars(trim($_POST['apellido'] ?? ''));
            $telefono = htmlspecialchars(trim($_POST['telefono'] ?? ''));

            if ($id > 0 && $nombre && $apellido && $telefono) {
                actualizarCliente($pdo, $id, $nombre, $apellido, $telefono);
            } else {
                echo "Error: Debes completar el 'id', 'nombre', 'apellido' y 'telefono'.";
            }
            break;
        
        case 'delete_client':
            $id = intval($_POST['id'] ?? 0);
            if ($id > 0) {
                eliminarCliente($pdo, $id);
            } else {
                echo "Error: El campo 'id' es obligatorio.";
            }
            break;

        default:
            echo "Error: Acción no reconocida.";
            break;
    }
}
?>
