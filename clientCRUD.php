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
function actualizarCliente($pdo, $id, $campos) {
    try {
        // Construir la consulta dinámica
        $setPart = [];
        foreach ($campos as $campo => $valor) {
            if (!empty($valor)) { // Solo agregar campos que no estén vacíos
                $setPart[] = "$campo = :$campo";
            }
        }

        // Verificar si hay campos a actualizar
        if (empty($setPart)) {
            throw new Exception("No hay campos para actualizar.");
        }

        $sql = "UPDATE cliente SET " . implode(", ", $setPart) . " WHERE id = :id";
        $stmt = $pdo->prepare($sql);

        // Asignar valores a los parámetros
        foreach ($campos as $campo => $valor) {
            if (!empty($valor)) {
                $stmt->bindValue(":$campo", $valor);
            }
        }
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();
        header("Location: client.html");
        exit;
    } catch (PDOException $e) {
        echo "Error al actualizar el cliente: " . $e->getMessage();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
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
            // Recoger los datos enviados desde el formulario
            $nombre = htmlspecialchars(trim($_POST['nombre'] ?? ''));
            $apellido = htmlspecialchars(trim($_POST['apellido'] ?? ''));
            $tipo_documento = htmlspecialchars(trim($_POST['tipoDocumento'] ?? ''));
            $numero_documento = htmlspecialchars(trim($_POST['numeroDocumento'] ?? ''));
            $telefono = htmlspecialchars(trim($_POST['telefono'] ?? ''));
            $fecha_nacimiento = htmlspecialchars(trim($_POST['fechaNacimiento'] ?? ''));

            // Validar la fecha de nacimiento
            if (!empty($fecha_nacimiento)) {
                $fechaObj = DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);
                if ($fechaObj) {
                    $fecha_nacimiento = $fechaObj->format('Y-m-d');
                } else {
                    echo "Error: Fecha de nacimiento no válida.";
                    exit;
                }
            }

            // Verificar que los campos obligatorios no estén vacíos
            if (empty($nombre) || empty($apellido) || empty($tipo_documento) || empty($numero_documento) || empty($telefono) || empty($fecha_nacimiento)) {
                echo "Error: Todos los campos son obligatorios.";
                exit;
            }

            // Llamar a la función para crear el cliente
            crearCliente($pdo, $nombre, $apellido, $tipo_documento, $numero_documento, $telefono, $fecha_nacimiento);
            break;

        case 'read_client':
            leerClientes($pdo);
            break;

        case 'update_client':
            $id = $_POST['id'] ?? null;
            $nombre = $_POST['nombre'] ?? '';
            $apellido = $_POST['apellido'] ?? '';
            $tipo_documento = $_POST['tipo_documento'] ?? '';
            $numero_documento = $_POST['numero_documento'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
        
            // Validar y reformatear la fecha si no está vacía
            if (!empty($fecha_nacimiento)) {
                $fechaObj = DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);
                if ($fechaObj) {
                    $fecha_nacimiento = $fechaObj->format('Y-m-d');
                } else {
                    echo "Error: Fecha de nacimiento no válida.";
                    exit;
                }
            }
        
            if ($id) {
                $campos = [
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'tipo_documento' => $tipo_documento,
                    'numero_documento' => $numero_documento,
                    'telefono' => $telefono,
                    'fecha_nacimiento' => $fecha_nacimiento
                ];
        
                actualizarCliente($pdo, $id, $campos);
            } else {
                echo "Error: ID del cliente es obligatorio.";
            }
            break;
            
        
        case 'delete_client':
            $id = $_POST['id'] ?? null;

            eliminarCliente($pdo, $id);
            break;

        default:
            echo "Error: Acción no reconocida.";
            break;
    }
}
?>
