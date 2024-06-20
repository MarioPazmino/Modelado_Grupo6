<?php
session_start();

// Verificar si el usuario está autenticado y tiene el rol adecuado
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'usuario')) {
    echo json_encode(['success' => false, 'message' => 'No estás autorizado para realizar esta acción.']);
    exit();
}

require '../vendor/autoload.php'; // Incluir el archivo autoload.php de MongoDB

use MongoDB\Client;
use MongoDB\BSON\ObjectId;
use MongoDB\Exception\Exception;

try {
    // Conexión a MongoDB
    $mongoClient = new Client("mongodb://grupo6:grupo6@localhost:27017");
    $db = $mongoClient->selectDatabase("grupo6_agrohub");
    $sembríosCollection = $db->sembríos; // Colección de sembríos en MongoDB

    // Obtener el ID del sembrío a eliminar
    $id = $_POST['id'];

    // Eliminar el sembrío
    $deleteResult = $sembríosCollection->deleteOne(['_id' => new ObjectId($id)]);

    if ($deleteResult->getDeletedCount() === 1) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se pudo eliminar el sembrío.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
