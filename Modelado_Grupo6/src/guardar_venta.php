<?php
session_start();

// Verificar si el usuario está autenticado y tiene el rol adecuado
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'usuario')) {
    header("Location: index.php");
    exit();
}

// Incluir el archivo autoload.php de MongoDB
require '../vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Exception\Exception;

// Conexión a MongoDB
try {
    $mongoClient = new Client("mongodb://grupo6:grupo6@localhost:27017");
    $db = $mongoClient->selectDatabase("grupo6_agrohub");
    $ventasCollection = $db->ventas; // Colección de ventas en MongoDB
    
} catch (Exception $e) {
    die("Error de conexión a MongoDB: " . $e->getMessage());
}

// Si se ha enviado el formulario, procesar los datos de la venta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Obtener datos del formulario
        $fecha_venta = new UTCDateTime(strtotime($_POST['fecha_venta']) * 1000); // Convertir a UTCDateTime
        $cantidad_vendida = (int)$_POST['cantidad_vendida'];
        $precio_total = (float)$_POST['precio_total'];
        $comprador_nombre = $_POST['comprador']['nombre'];
        $comprador_contacto_tipo = $_POST['comprador']['contacto']['tipo'];
        $comprador_contacto_valor = $_POST['comprador']['contacto']['valor'];
        $venta_tipo = $_POST['venta_tipo'];
        
        // Verificar si es venta de cosecha
        if ($venta_tipo === 'cosecha') {
            $producto_nombre = $_POST['item_id']; // Nombre de la cosecha seleccionada
            // Preparar datos para insertar en MongoDB
            $nueva_venta = [
                'usuario_id' => new ObjectId($_SESSION['usuario_id']),
                'fecha_venta' => $fecha_venta,
                'cantidad_vendida' => $cantidad_vendida,
                'precio_total' => $precio_total,
                'comprador' => [
                    'nombre' => $comprador_nombre,
                    'contacto' => [
                        [
                            'tipo' => $comprador_contacto_tipo,
                            'valor' => $comprador_contacto_valor
                        ]
                    ]
                ],
                'venta_tipo' => $venta_tipo,
                'producto_detalle' => [
                    'producto_nombre' => $producto_nombre
                ]
            ];
        } elseif ($venta_tipo === 'producto') {
            // Si es venta de producto, obtener detalles del producto
            $producto_nombre = $_POST['producto_nombre'];
            $producto_descripcion = $_POST['producto_descripcion'];
            $producto_tipo = $_POST['producto_tipo'];
            
            // Preparar datos para insertar en MongoDB
            $nueva_venta = [
                'usuario_id' => new ObjectId($_SESSION['usuario_id']),
                'fecha_venta' => $fecha_venta,
                'cantidad_vendida' => $cantidad_vendida,
                'precio_total' => $precio_total,
                'comprador' => [
                    'nombre' => $comprador_nombre,
                    'contacto' => [
                        [
                            'tipo' => $comprador_contacto_tipo,
                            'valor' => $comprador_contacto_valor
                        ]
                    ]
                ],
                'venta_tipo' => $venta_tipo,
                'producto_detalle' => [
                    'producto_nombre' => $producto_nombre,
                    'producto_descripcion' => $producto_descripcion,
                    'producto_tipo' => $producto_tipo
                ]
            ];
        } else {
            // Tipo de venta no reconocido
            throw new Exception('Tipo de venta no válido.');
        }

        // Insertar la nueva venta en la colección de ventas
        $insertOneResult = $ventasCollection->insertOne($nueva_venta);

        // Mostrar mensaje de éxito o error
        if ($insertOneResult->getInsertedCount() === 1) {
            $mensaje = '<p style="color: green;">¡La venta se ha guardado correctamente!</p>';
        } else {
            $mensaje = '<p style="color: red;">Hubo un error al guardar la venta.</p>';
        }
    } catch (Exception $e) {
        $mensaje = '<p style="color: red;">Error: ' . $e->getMessage() . '</p>';
    }
}

// Redirigir a la página principal o mostrar el mensaje
header("Location: ventas.php?mensaje=" . urlencode($mensaje));
exit();
?>
