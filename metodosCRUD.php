<?php
// Configuración de conexión a la base de datos
$host = 'localhost';
$dbname = 'crud_php';
$usuario = 'root';
$contraseña = '';

try {
    // Crear una nueva conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $usuario, $contraseña);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("¡Error en la conexión a la base de datos!: " . $e->getMessage());
}


// Funciones para la tabla Producto
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

// Funciones para la tabla Cliente
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
        echo "Cliente creado exitosamente.";
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
        case 'create_product':
            $nombreProducto = htmlspecialchars(trim($_POST['nombreProducto'] ?? ''));
            if ($nombreProducto) {
                crearProducto($pdo, $nombreProducto);
            } else {
                echo "Error: el campo 'nombreProducto' es obligatorio.";
            }
            break;

        case 'create_client':
            // Recoger datos del formulario y llamar a la función crearCliente
            break;

        case 'create_invoice':
            // Recoger datos del formulario y llamar a la función crearFactura
            break;

        default:
            echo "Error: acción no reconocida.";
            break;
    }
}
?>


