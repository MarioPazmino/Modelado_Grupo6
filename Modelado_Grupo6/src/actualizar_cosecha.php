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
    $cosechasCollection = $db->cosechas; // Colección de cosechas en MongoDB
} catch (Exception $e) {
    die("Error de conexión a MongoDB: " . $e->getMessage());
}

// Si se ha enviado el formulario de edición, procesar los datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $id = $_POST['editId'];
    $fechaCosecha = new UTCDateTime(strtotime($_POST['editFechaCosecha']) * 1000); // Convertir a UTCDateTime
    $cantidad = (int)$_POST['editCantidad'];
    $unidad = $_POST['editUnidad'];
    $descripcion = $_POST['editDescripcion'];
    $calidad = $_POST['editCalidad'];

    // Crear el array de detalles de cosecha
    $detallesCosecha = [
        'descripcion' => $descripcion,
        'calidad' => $calidad
    ];

    // Preparar datos actualizados para MongoDB
    $actualizacionCosecha = [
        'fecha_cosecha' => $fechaCosecha,
        'cantidad' => $cantidad,
        'unidad' => $unidad,
        'detalles_cosecha' => [$detallesCosecha] // Guardar como un array de objetos
    ];

    try {
        // Actualizar la cosecha en la colección de cosechas
        $updateResult = $cosechasCollection->updateOne(
            ['_id' => new ObjectID($id)],
            ['$set' => $actualizacionCosecha]
        );

        // Verificar si se realizó la actualización correctamente
        if ($updateResult->getModifiedCount() === 1) {
            $response = ['success' => true];
        } else {
            $response = ['success' => false, 'message' => 'No se pudo actualizar la cosecha.'];
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => 'Error al actualizar la cosecha: ' . $e->getMessage()];
    }

    // Devolver respuesta en formato JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>
