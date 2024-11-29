<?php 
require_once 'connection.php';

// Crear Producto
function crearProducto($pdo, $nombreProducto) {
    try {
        $sql = "INSERT INTO producto (nombre_producto) VALUES (:nombre)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre', $nombreProducto);
        $stmt->execute();

        // Guardar mensaje en sesión
        session_start();
        $_SESSION['mensaje'] = "Producto creado exitosamente.";

        // Redirigir a la página products.html
        header("Location: products.html");
        exit; // Detener la ejecución para evitar procesar más código
    } catch (PDOException $e) {
        echo "Error al crear el producto: " . $e->getMessage();
    }
}

function leerProductos($pdo) {
    try {
        $sql = "SELECT * FROM producto";
        $stmt = $pdo->query($sql);
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // HTML principal
        $html = <<<HTML
        <div style="max-width: 1200px; margin: 0 auto; font-family: Arial, sans-serif;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                
                <a href="products.html" style="text-decoration: none;">
                    <button style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Volver</button>
                </a>
                <h1 style="color: #333;">Lista de Productos</h1>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
        HTML;

        // Añadir productos al HTML
        foreach ($productos as $producto) {
            $id = htmlspecialchars($producto['id']);
            $nombre = htmlspecialchars($producto['nombre_producto']);
            $html .= <<<HTML
                <div style="border: 1px solid #ccc; border-radius: 8px; padding: 16px; background-color: #f9f9f9; box-shadow: 0 4px 8px rgba(0,0,0,0.1); text-align: center;">
                    <h2 style="font-size: 18px; color: #4CAF50; margin-bottom: 10px;">Producto</h2>
                    <p><strong>Código:</strong> $id</p>
                    <p><strong>Nombre:</strong> $nombre</p>
                </div>
            HTML;
        }

        // Finalizar el HTML
        $html .= <<<HTML
            </div>
        </div>
        HTML;

        echo $html;
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Error al leer productos: " . htmlspecialchars($e->getMessage()) . "</p>";
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
        // Guardar mensaje en sesión
        session_start();
        $_SESSION['mensaje'] = "Producto actualizado exitosamente.";
        // Redirigir a la página products.html
        header("Location: products.html");
        exit; 
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
        // Guardar mensaje en sesión
        session_start();
        $_SESSION['mensaje'] = "Producto eliminado exitosamente.";
        // Redirigir a la página products.html
        header("Location: products.html");
        exit; 
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

        case 'read_products':
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