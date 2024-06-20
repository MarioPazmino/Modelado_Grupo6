<?php
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
    $cosechasCollection = $db->selectCollection("cosechas"); // Colección de cosechas en MongoDB
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error de conexión a MongoDB: ' . $e->getMessage()];
    echo json_encode($response);
    exit();
}

// Verificar si se recibió el ID válido para obtener los detalles de la cosecha
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        // Buscar la cosecha por su ID en MongoDB
        $cosecha = $cosechasCollection->findOne(['_id' => new ObjectID($id)]);

        if ($cosecha) {
            // Preparar los datos de la cosecha para enviarlos como respuesta
            $response = [
                'success' => true,
                'cosecha' => [
                    '_id' => (string)$cosecha['_id'],
                    'producto_nombre' => $cosecha['producto_nombre'],
                    'fecha_cosecha' => $cosecha['fecha_cosecha']->toDateTime()->format('Y-m-d'),
                    'cantidad' => $cosecha['cantidad'],
                    'unidad' => $cosecha['unidad'],
                    'detalles_cosecha' => [
                        'descripcion' => $cosecha['detalles_cosecha'][0]['descripcion'],
                        'calidad' => $cosecha['detalles_cosecha'][0]['calidad']
                    ]
                ]
            ];
        } else {
            $response = ['success' => false, 'message' => 'No se encontró la cosecha.'];
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => 'Error al obtener la cosecha: ' . $e->getMessage()];
    }

    // Devolver respuesta en formato JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
} else {
    $response = ['success' => false, 'message' => 'ID de cosecha no válido.'];
    echo json_encode($response);
    exit();
}
?>
