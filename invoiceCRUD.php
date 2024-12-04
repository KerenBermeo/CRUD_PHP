<?php
// Crear una factura
function crearFactura($pdo, $numeroFactura, $idCliente, $idProducto, $cantidad, $valor) {
    try {
        $sql = "INSERT INTO factura (numero_factura, id_cliente, id_producto, cantidad, valor) 
                VALUES (:numeroFactura, :idCliente, :idProducto, :cantidad, :valor)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':numeroFactura', $numeroFactura);
        $stmt->bindParam(':idCliente', $idCliente);
        $stmt->bindParam(':idProducto', $idProducto);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->bindParam(':valor', $valor);
        $stmt->execute();
        echo "Factura creada exitosamente.";
    } catch (PDOException $e) {
        echo "Error al crear la factura: " . $e->getMessage();
    }
}

// Leer todas las facturas
function leerFacturas($pdo) {
    try {
        $sql = "SELECT f.id, f.numero_factura, c.nombre AS cliente, p.nombre_producto AS producto, f.cantidad, f.valor 
                FROM factura f 
                INNER JOIN cliente c ON f.id_cliente = c.id 
                INNER JOIN producto p ON f.id_producto = p.id";
        $stmt = $pdo->query($sql);
        $facturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($facturas as $factura) {
            echo "ID: " . $factura['id'] . "<br>";
            echo "Número de Factura: " . $factura['numero_factura'] . "<br>";
            echo "Cliente: " . $factura['cliente'] . "<br>";
            echo "Producto: " . $factura['producto'] . "<br>";
            echo "Cantidad: " . $factura['cantidad'] . "<br>";
            echo "Valor: $" . $factura['valor'] . "<br><br>";
        }
    } catch (PDOException $e) {
        echo "Error al leer las facturas: " . $e->getMessage();
    }
}

// Actualizar una factura
function actualizarFactura($pdo, $id, $cantidad, $valor) {
    try {
        $sql = "UPDATE factura SET cantidad = :cantidad, valor = :valor WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->bindParam(':valor', $valor);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        echo "Factura actualizada exitosamente.";
    } catch (PDOException $e) {
        echo "Error al actualizar la factura: " . $e->getMessage();
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


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Verificar si la acción solicitada es 'numeroFactura'
    $accion = $_GET['action'] ?? '';
    if ($accion === 'numeroFactura') {
        // Generar un número de factura aleatorio dentro del rango
        $numeroFactura = rand(1000, 9999);
        echo json_encode(["factura_id" => $numeroFactura]);
        exit;
    }
}


// Manejo de datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST['accion'] ?? '';

    switch ($accion) {
        case 'crear_factura':
            $numeroFactura = htmlspecialchars(trim($_POST['numeroFactura'] ?? ''));
            $nombreCliente = intval($_POST['nombreCliente'] ?? 0);
            $nombreProducto = intval($_POST['nombreProducto'] ?? 0);
            $cantidad = intval($_POST['cantidad'] ?? 0);
            $valor = floatval($_POST['valor'] ?? 0.0);

            if ($numeroFactura && $idCliente > 0 && $idProducto > 0 && $cantidad > 0 && $valor > 0) {
                crearFactura($pdo, $numeroFactura, $idCliente, $idProducto, $cantidad, $valor);
            } else {
                echo "Error: todos los campos son obligatorios.";
            }
            break;

        case 'leer_facturas':
            leerFacturas($pdo);
            break;

        case 'actualizar_factura':
            $id = intval($_POST['id'] ?? 0);
            $cantidad = intval($_POST['cantidad'] ?? 0);
            $valor = floatval($_POST['valor'] ?? 0.0);

            if ($id > 0 && $cantidad > 0 && $valor > 0) {
                actualizarFactura($pdo, $id, $cantidad, $valor);
            } else {
                echo "Error: todos los campos son obligatorios.";
            }
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
