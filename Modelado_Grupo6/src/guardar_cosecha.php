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
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Exception\Exception;

// Conexión a MongoDB
try {
    $mongoClient = new Client("mongodb://grupo6:grupo6@localhost:27017");
    $db = $mongoClient->selectDatabase("grupo6_agrohub");
    $sembríosCollection = $db->sembríos; // Colección de sembríos en MongoDB con tilde
    $cosechasCollection = $db->cosechas; // Colección de cosechas en MongoDB
    
} catch (Exception $e) {
    die("Error de conexión a MongoDB: " . $e->getMessage());
}

// Si se ha enviado el formulario, procesar los datos de la cosecha
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $producto_nombre = $_POST['producto_nombre'];
    $fecha_cosecha = new UTCDateTime(strtotime($_POST['fecha_cosecha']) * 1000); // Convertir a UTCDateTime
    $cantidad = (int)$_POST['cantidad'];
    $unidad = $_POST['unidad'];
    $descripcion = $_POST['descripcion'];
    $calidad = $_POST['calidad'];

    // Crear el array de detalles de cosecha
    $detalles_cosecha = [
        'descripcion' => $descripcion,
        'calidad' => $calidad
    ];

    // Buscar el sembrío por nombre para obtener su ID
    $sembrío = $sembríosCollection->findOne(['producto_nombre' => $producto_nombre]);

    if ($sembrío) {
        $sembrío_id = $sembrío->_id;

        // Preparar datos para insertar en MongoDB
        $nueva_cosecha = [
            'usuario_id' => new ObjectID($_SESSION['usuario_id']),
            'sembrío_id' => new ObjectID($sembrío_id),
            'producto_nombre' => $producto_nombre, // Guardar también el nombre del producto para mostrar en la tabla
            'fecha_cosecha' => $fecha_cosecha,
            'cantidad' => $cantidad,
            'unidad' => $unidad,
            'detalles_cosecha' => $detalles_cosecha
        ];

        try {
            // Insertar la nueva cosecha en la colección de cosechas
            $insertOneResult = $cosechasCollection->insertOne($nueva_cosecha);
            
            // Mostrar mensaje de éxito
            if ($insertOneResult->getInsertedCount() === 1) {
                $mensaje = '<p style="color: green;">¡La cosecha se ha guardado correctamente!</p>';
            } else {
                $mensaje = '<p style="color: red;">Hubo un error al guardar la cosecha.</p>';
            }
        } catch (Exception $e) {
            $mensaje = '<p style="color: red;">Error: ' . $e->getMessage() . '</p>';
        }
    } else {
        $mensaje = '<p style="color: red;">No se encontró el sembrío con el nombre especificado.</p>';
    }
}

// Obtener todos los sembríos para mostrar en el formulario
$sembríosCursor = $sembríosCollection->find();
$sembríos = iterator_to_array($sembríosCursor);

// Obtener todas las cosechas para mostrar en la tabla
$cosechasCursor = $cosechasCollection->find();
$cosechas = iterator_to_array($cosechasCursor);

?>
