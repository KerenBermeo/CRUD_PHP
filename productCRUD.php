<?php 
require_once 'connection.php';

// Crear Producto
function crearProducto($pdo, $nombreProducto) {
    try {
        $sql = "INSERT INTO producto (nombre_producto) VALUES (:nombre)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre', $nombreProducto);
        $stmt->execute();
        echo "Producto creado exitosamente.";
    } catch (PDOException $e) {
        echo "Error al crear el producto: " . $e->getMessage();
    }
}

// Leer los Productos
function leerProductos($pdo) {
    try {
        $sql = "SELECT * FROM producto";
        $stmt = $pdo->query($sql);
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($productos as $producto) {
            echo "ID: " . $producto['id'] . "<br>";
            echo "Nombre: " . $producto['nombre_producto'] . "<br><br>";
        }
    } catch (PDOException $e) {
        echo "Error al leer productos: " . $e->getMessage();
    }
}

// Actualizar los Productos 
function actualizarProducto($pdo, $id, $nuevoNombre) {
    try {
        $sql = "UPDATE producto SET nombre_producto = :nombre WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre', $nuevoNombre);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        echo "Producto actualizado exitosamente.";
    } catch (PDOException $e) {
        echo "Error al actualizar el producto: " . $e->getMessage();
    }
}

// Eliminar un producto
function eliminarProducto($pdo, $id) {
    try {
        $sql = "DELETE FROM producto WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        echo "Producto eliminado exitosamente.";
    } catch (PDOException $e) {
        echo "Error al eliminar el producto: " . $e->getMessage();
    }
}

// Manejo de datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST['accion'] ?? '';

    switch ($accion) {
        case 'create_product':
            $nombreProducto = htmlspecialchars(trim($_POST['nombreProducto'] ?? ''));
            if ($nombreProducto) {
                crearProducto($pdo, $nombreProducto);
            } else {
                echo "Error: el campo 'nombreProducto' es obligatorio.";
            }
            break;

        case 'read_product':
            leerProductos($pdo);
            break;

        case 'update_product':
            $id = intval($_POST['id'] ?? 0);
            $nuevoNombre = htmlspecialchars(trim($_POST['nuevoNombre'] ?? ''));
            if ($id > 0 && $nuevoNombre) {
                actualizarProducto($pdo, $id, $nuevoNombre);
            } else {
                echo "Error: ambos campos 'id' y 'nuevoNombre' son obligatorios.";
            }
            break;
        
        case 'delete_product':
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