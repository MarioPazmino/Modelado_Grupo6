<?php
// Incluir el archivo autoload.php de MongoDB
require '../vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectID;
use MongoDB\Exception\Exception;

// Verificar si se recibió una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si se recibió el parámetro 'id' y si es válido
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $ventaId = $_POST['id'];

        // Conexión a MongoDB
        try {
            $mongoClient = new Client("mongodb://grupo6:grupo6@localhost:27017");
            $db = $mongoClient->selectDatabase("grupo6_agrohub");
            $ventasCollection = $db->ventas; // Colección de ventas en MongoDB

            // Convertir el ID en ObjectID de MongoDB
            $objectId = new ObjectID($ventaId);

            // Eliminar la venta de la colección
            $deleteResult = $ventasCollection->deleteOne(['_id' => $objectId]);

            // Verificar si la eliminación fue exitosa
            if ($deleteResult->getDeletedCount() === 1) {
                // Enviar respuesta JSON de éxito
                echo json_encode(['success' => true]);
                exit;
            } else {
                // Enviar respuesta JSON de error si no se encontró la venta
                echo json_encode(['success' => false, 'message' => 'No se encontró la venta para eliminar']);
                exit;
            }
        } catch (Exception $e) {
            // Enviar respuesta JSON de error si ocurrió una excepción
            echo json_encode(['success' => false, 'message' => 'Error al eliminar la venta: ' . $e->getMessage()]);
            exit;
        }
    } else {
        // Enviar respuesta JSON de error si no se recibió el parámetro 'id'
        echo json_encode(['success' => false, 'message' => 'No se recibió el ID de la venta']);
        exit;
    }
} else {
    // Enviar respuesta JSON de error si no es una solicitud POST válida
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido']);
    exit;
}
?>
