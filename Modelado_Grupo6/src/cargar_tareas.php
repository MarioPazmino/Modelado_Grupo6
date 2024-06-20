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
    // Obtener el ID del sembrío desde el formulario POST
    $sembrío_id = isset($_POST['sembrío_id']) ? $_POST['sembrío_id'] : null;

    // Validar que el ID del sembrío no esté vacío
    if (!$sembrío_id) {
        echo json_encode(['success' => false, 'message' => 'ID de sembrío no especificado.']);
        exit();
    }

    // Obtener las tareas del sembrío específico
    $sembrío = $sembríosCollection->findOne(['_id' => new ObjectId($sembrío_id)]);

    if (!$sembrío) {
        echo json_encode(['success' => false, 'message' => 'No se encontró el sembrío especificado.']);
        exit();
    }

    // Extraer las tareas del sembrío
    $tareas = isset($sembrío['tareas']) ? $sembrío['tareas'] : [];

    // Preparar el arreglo de tareas para enviar como respuesta JSON
    $response = [
        'success' => true,
        'tareas' => []
    ];

    // Formatear las tareas para excluir la fecha de realización, ya que no existe en el esquema
    foreach ($tareas as $tarea) {
        $formattedTarea = [
            '_id' => (string)$tarea['_id'],
            'descripcion' => $tarea['descripcion'],
            'estado' => $tarea['estado']
        ];
        $response['tareas'][] = $formattedTarea;
    }

    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error al cargar las tareas: ' . $e->getMessage()]);
}
?>
