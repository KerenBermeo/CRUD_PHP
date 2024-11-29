<?php
// Funciones para la tabla Factura
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
                INNER JOIN cliente c ON f.numero_cliente = c.id 
                INNER JOIN producto p ON f.numero_producto = p.id";
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

// Manejo de datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST['accion'] ?? '';

    switch ($accion) {
        case 'create_invoice':
            $nombreProducto = htmlspecialchars(trim($_POST['nombreProducto'] ?? ''));
            if ($nombreProducto) {
                crearProducto($pdo, $nombreProducto);
            } else {
                echo "Error: el campo 'nombreProducto' es obligatorio.";
            }
            break;

        case 'read_invoice':
            leerProductos($pdo);
            break;

        case 'update_invoice':
            $id = intval($_POST['id'] ?? 0);
            $nuevoNombre = htmlspecialchars(trim($_POST['nuevoNombre'] ?? ''));
            if ($id > 0 && $nuevoNombre) {
                actualizarProducto($pdo, $id, $nuevoNombre);
            } else {
                echo "Error: ambos campos 'id' y 'nuevoNombre' son obligatorios.";
            }
            break;
        
        case 'delete_invoice':
            $id = intval($_POST['id'] ?? 0);
            if ($id > 0) {
                eliminarProducto($pdo, $id);
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