<?php
// Incluir el archivo autoload.php de MongoDB
require '../vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectID;
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

// Verificar si se recibió el ID válido para eliminar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        $deleteResult = $cosechasCollection->deleteOne(['_id' => new ObjectID($id)]);

        if ($deleteResult->getDeletedCount() === 1) {
            $response = ['success' => true];
        } else {
            $response = ['success' => false, 'message' => 'No se encontró la cosecha para eliminar.'];
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => 'Error al eliminar la cosecha: ' . $e->getMessage()];
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
