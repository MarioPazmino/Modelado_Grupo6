<?php
session_start();

// Verificar si el usuario está autenticado y tiene el rol adecuado
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'usuario')) {
    echo json_encode(['success' => false, 'message' => 'No estás autorizado para realizar esta acción.']);
    exit();
}

// Incluir el archivo autoload.php de MongoDB
require '../vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;
use MongoDB\Exception\Exception;

// Configuración de la conexión a MongoDB
$mongoClient = new Client("mongodb://grupo6:grupo6@localhost:27017");
$db = $mongoClient->selectDatabase("grupo6_agrohub");
$sembríosCollection = $db->sembríos; // Colección de sembríos en MongoDB

try {
    // Obtener datos del formulario
    $sembrío_id = isset($_POST['sembrío_id']) ? $_POST['sembrío_id'] : null;
    $tarea_id = isset($_POST['tarea_id']) ? $_POST['tarea_id'] : null;

    // Validar que el sembrío_id y tarea_id no estén vacíos
    if (!$sembrío_id || !$tarea_id) {
        echo json_encode(['success' => false, 'message' => 'ID de sembrío o tarea no especificado.']);
        exit();
    }

    // Convertir tarea_id a ObjectId
    $tarea_id = new ObjectId($tarea_id);

    // Eliminar la tarea del sembrío en MongoDB
    $updateResult = $sembríosCollection->updateOne(
        ['_id' => new ObjectId($sembrío_id)],
        ['$pull' => ['tareas' => ['_id' => $tarea_id]]]
    );

    if ($updateResult->getModifiedCount() === 1) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se pudo eliminar la tarea del sembrío.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar la tarea: ' . $e->getMessage()]);
}
?>
