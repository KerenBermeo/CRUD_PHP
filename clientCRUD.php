<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'connection.php';
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

        $html = <<<HTML
        <div style="max-width: 1200px; margin: 0 auto; font-family: Arial, sans-serif;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                
                <a href="client.html" style="text-decoration: none;">
                    <button style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Volver</button>
                </a>
                <h1 style="color: #333;">Lista de Clientes</h1>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
        HTML;

        foreach ($clientes as $cliente) {
            $id = htmlspecialchars($cliente['id']);
            $nombre = htmlspecialchars($cliente['nombre']);
            $apellido = htmlspecialchars($cliente['apellido']);
            $tipoDocumento = htmlspecialchars($cliente['tipo_documento']);
            $numeroDocumento = htmlspecialchars($cliente['numero_documento']);
            $telefono = htmlspecialchars($cliente['telefono']);
            $fechaNacimiento = htmlspecialchars($cliente['fecha_nacimiento']);

            $html .= <<<HTML
            <div style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; background-color: #f9f9f9;">
                <h2 style="color: #4CAF50; font-size: 20px;">$nombre $apellido</h2>
                <p><strong>Codigo:</strong> $id</p>
                <p><strong>Tipo de documento:</strong> $tipoDocumento</p>
                <p><strong>Número de documento:</strong> $numeroDocumento</p>
                <p><strong>Teléfono:</strong> $telefono</p>
                <p><strong>Fecha de nacimiento:</strong> $fechaNacimiento</p>
            </div>
            HTML;
        }

        $html .= <<<HTML
            </div>
        </div>
        HTML;

        echo $html;

    } catch (PDOException $e) {
        echo "Error al leer los clientes: " . $e->getMessage();
    }
}

// Actualizar un cliente
function actualizarCliente($pdo, $id, $nombre, $apellido, $telefono, $fechaNacimiento) {
    try {
        $sql = "UPDATE cliente SET nombre = :nombre, apellido = :apellido, telefono = :telefono, fecha_nacimiento = :fechaNacimiento WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':fechaNacimiento', $fechaNacimiento);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header("Location: client.html");
        exit;
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
        header("Location: client.html");
        exit;
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
            // Obtener los datos del formulario
            $id = intval($_POST['id'] ?? 0);
            $nombre = isset($_POST['update_nombre']) ? htmlspecialchars(trim($_POST['nombre'] ?? '')) : null;
            $apellido = isset($_POST['update_apellido']) ? htmlspecialchars(trim($_POST['apellido'] ?? '')) : null;
            $tipoDocumento = isset($_POST['update_tipoDocumento']) ? htmlspecialchars(trim($_POST['tipoDocumento'] ?? '')) : null;
            $numeroDocumento = isset($_POST['update_numeroDocumento']) ? htmlspecialchars(trim($_POST['numeroDocumento'] ?? '')) : null;
            $telefono = isset($_POST['update_telefono']) ? htmlspecialchars(trim($_POST['telefono'] ?? '')) : null;
            $fechaNacimiento = isset($_POST['update_fechaNacimiento']) ? htmlspecialchars(trim($_POST['fechaNacimiento'] ?? '')) : null;
            

            // Depuración: Mostrar los datos recibidos
            echo "Datos recibidos:<br>";
            echo "ID: $id<br>";
            echo "Nombre: $nombre<br>";
            echo "Apellido: $apellido<br>";
            echo "Teléfono: $telefono<br>";
            echo "Fecha de Nacimiento: $fechaNacimiento<br>";
            echo "Tipo de Documento: $tipoDocumento<br>";
            echo "Número de Documento: $numeroDocumento<br>";
            // Solo actualizar si los datos están presentes
            if ($id > 0) {
                actualizarCliente($pdo, $id, $nombre, $apellido, $telefono, $fechaNacimiento, $tipoDocumento, $numeroDocumento);
            } else {
                echo "Error: Debes completar el 'Codigo'.";
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
