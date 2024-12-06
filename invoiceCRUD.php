<?php
require_once 'connection.php';
// Crear una factura
function crearFactura($pdo, $nombreCliente, $nombreProducto, $cantidad, $valor) {
    try {
        // Obtener el id del cliente
        $sqlCliente = "SELECT id FROM cliente WHERE nombre = :nombreCliente";
        $stmtCliente = $pdo->prepare($sqlCliente);
        $stmtCliente->bindParam(':nombreCliente', $nombreCliente);
        $stmtCliente->execute();
        $cliente = $stmtCliente->fetch(PDO::FETCH_ASSOC);

        if (!$cliente) {
            throw new Exception("Cliente no encontrado.");
        }
        $idCliente = $cliente['id'];

        // Obtener el id del producto
        $sqlProducto = "SELECT id FROM producto WHERE nombre_producto = :nombreProducto";
        $stmtProducto = $pdo->prepare($sqlProducto);
        $stmtProducto->bindParam(':nombreProducto', $nombreProducto);
        $stmtProducto->execute();
        $producto = $stmtProducto->fetch(PDO::FETCH_ASSOC);

        if (!$producto) {
            throw new Exception("Producto no encontrado.");
        }
        $idProducto = $producto['id'];

        // Insertar la factura
        $sqlFactura = "INSERT INTO factura (id_cliente, id_producto, cantidad, valor) 
                       VALUES (:idCliente, :idProducto, :cantidad, :valor)";
        $stmtFactura = $pdo->prepare($sqlFactura);
        $stmtFactura->bindParam(':idCliente', $idCliente);
        $stmtFactura->bindParam(':idProducto', $idProducto);
        $stmtFactura->bindParam(':cantidad', $cantidad);
        $stmtFactura->bindParam(':valor', $valor);
        $stmtFactura->execute();

        header("Location: client.html");
        exit;
    } catch (PDOException $e) {
        echo "Error de base de datos: " . $e->getMessage();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}


// Leer todas las facturas
function leerFacturas($pdo) {
    try {
        // Consulta para obtener las facturas con los datos del cliente y producto
        $sql = "SELECT 
                    f.numero_factura, 
                    c.nombre AS cliente_nombre, 
                    c.apellido AS cliente_apellido, 
                    p.nombre_producto, 
                    f.cantidad, 
                    f.valor 
                FROM factura f
                JOIN cliente c ON f.id_cliente = c.id
                JOIN producto p ON f.id_producto = p.id";
        
        $stmt = $pdo->query($sql);
        $facturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $html = <<<HTML
        <div style="max-width: 1200px; margin: 0 auto; font-family: Arial, sans-serif;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <a href="invoice.html" style="text-decoration: none;">
                    <button style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Volver</button>
                </a>
                <h1 style="color: #333;">Lista de Facturas</h1>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
        HTML;

        // Mostrar cada factura con los datos correspondientes
        foreach ($facturas as $factura) {
            $idFactura = htmlspecialchars($factura['numero_factura']);
            $clienteNombre = htmlspecialchars($factura['cliente_nombre']);
            $clienteApellido = htmlspecialchars($factura['cliente_apellido']);
            $productoNombre = htmlspecialchars($factura['nombre_producto']);
            $cantidad = htmlspecialchars($factura['cantidad']);
            $valor = htmlspecialchars($factura['valor']);

            // Aquí se construye cada factura con la información obtenida
            $html .= <<<HTML
            <div style="border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9;">
                <h3>Factura #$idFactura</h3>
                <p><strong>Cliente:</strong> $clienteNombre $clienteApellido</p>
                <p><strong>Producto:</strong> $productoNombre</p>
                <p><strong>Cantidad:</strong> $cantidad</p>
                <p><strong>Valor:</strong> $$valor</p>
            </div>
            HTML;
        }

        $html .= <<<HTML
            </div>
        </div>
        HTML;

        echo $html;
    } catch (PDOException $e) {
        echo "Error al leer las facturas: " . $e->getMessage();
    }
}



// Eliminar una factura
function eliminarFactura($pdo, $id) {
    try {
        $sql = "DELETE FROM factura WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        echo "Factura eliminada exitosamente.";
    } catch (PDOException $e) {
        echo "Error al eliminar la factura: " . $e->getMessage();
    }
}

function numeroFactura($pdo) {
    try {
        $query = "SELECT MAX(numero_factura) AS max_numero_factura FROM Factura";
        $result = $pdo->query($query)->fetch(PDO::FETCH_ASSOC);
        
        $maxFactura = $result['max_numero_factura'] ?? 0;
        return $maxFactura + 1;
    } catch (PDOException $e) {
        echo "Error en numeroFactura: " . $e->getMessage();
        return 0;
    }
}

function nombreClientes($pdo, $term) {
    try {
        $stmt = $pdo->prepare("SELECT id, nombre FROM cliente WHERE nombre LIKE :term LIMIT 10");
        $stmt->bindValue(':term', '%' . $term . '%', PDO::PARAM_STR);
        $stmt->execute();

        // Devuelve los resultados como un arreglo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error en nombreClientes: " . $e->getMessage();
        return [];
    }
}



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $accion = $_GET['action'] ?? '';

    switch ($accion) {
        case 'numeroFactura':
            $numeroFactura = numeroFactura($pdo);
            echo json_encode(["factura_id" => $numeroFactura]);
            break;

        case 'nombreCliente':
            $term = $_GET['term'] ?? '';
            $nombreClientes = nombreClientes($pdo, $term);
            echo json_encode(["list_nombres" => $nombreClientes]);
            break;
        
        case 'nombrePrducto':

            break;
        
        default:
            echo "Error: acción no reconocida.";
            break;
    }
}


// Manejo de datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST['accion'] ?? '';

    switch ($accion) {
        case 'crear_factura':
            $nombreCliente = htmlspecialchars(trim($_POST['nombreCliente'] ?? ''), ENT_QUOTES, 'UTF-8');
            $nombreProducto = htmlspecialchars(trim($_POST['nombreProducto'] ?? ''), ENT_QUOTES, 'UTF-8');
            $cantidad = intval($_POST['cantidad'] ?? 0);
            $valor = floatval($_POST['valor'] ?? 0.0);

            if ($nombreCliente > 0 && $nombreProducto > 0 && $cantidad > 0 && $valor > 0) {
                crearFactura($pdo, $nombreCliente, $nombreProducto, $cantidad, $valor);
            } else {
                echo "Error: todos los campos son obligatorios.";
            }
            break;

        case 'leer_facturas':
            leerFacturas($pdo);
            break;

        case 'eliminar_factura':
            $id = intval($_POST['id'] ?? 0);

            if ($id > 0) {
                eliminarFactura($pdo, $id);
            } else {
                echo "Error: el campo 'id' es obligatorio.";
            }
            break;

        default:
            echo "Error: acción no reconocida.";
            break;
    }
}
?>
