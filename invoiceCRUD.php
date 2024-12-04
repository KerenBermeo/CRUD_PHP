<?php
require_once 'connection.php';
// Crear una factura
function crearFactura($pdo, $nombreCliente, $nombreProducto, $cantidad, $valor) {
    try {
        $sql = "INSERT INTO factura (nombre_cliente, nombre_producto, cantidad, valor) 
                VALUES (:idCliente, :nombreProducto, :cantidad, :valor)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombreCliente', $nombreCliente);
        $stmt->bindParam(':nombreProducto', $nombreProducto);
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
            echo "NumeroFactura: " . $factura['id'] . "<br>";
            echo "Cliente: " . $factura['cliente'] . "<br>";
            echo "Producto: " . $factura['producto'] . "<br>";
            echo "Cantidad: " . $factura['cantidad'] . "<br>";
            echo "Valor: $" . $factura['valor'] . "<br><br>";
        }
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
        // Usando query porque la consulta no tiene parámetros dinámicos
        $query = "SELECT MAX(numero_factura) AS max_numero_factura FROM Factura";
        $result = $pdo->query($query)->fetch(PDO::FETCH_ASSOC);

        // Retornar el valor máximo encontrado, o 1 si es nulo
        return $result['max_numero_factura'] ?? 1;
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
            $nombreCliente = intval($_POST['nombreCliente'] ?? 0);
            $nombreProducto = intval($_POST['nombreProducto'] ?? 0);
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
