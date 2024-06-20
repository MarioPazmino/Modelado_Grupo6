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
    $tarea_id = isset($_POST['tarea_id']) ? $_POST['tarea_id'] : null; // ID de la tarea, opcional para actualizar
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : null;
    $estado = isset($_POST['estado']) ? $_POST['estado'] : null;

    // Validar que el sembrío_id no esté vacío
    if (!$sembrío_id) {
        echo json_encode(['success' => false, 'message' => 'ID de sembrío no especificado.']);
        exit();
    }

    // Validar que la descripción no esté vacía
    if (empty($descripcion)) {
        echo json_encode(['success' => false, 'message' => 'La descripción es obligatoria.']);
        exit();
    }

    // Crear el documento de la tarea
    $nueva_tarea = [
        '_id' => $tarea_id ? new ObjectId($tarea_id) : new ObjectId(),
        'descripcion' => $descripcion,
        'estado' => $estado,
    ];

    // Si hay un tarea_id, significa que se está actualizando una tarea existente
    if ($tarea_id) {
        // Convertir tarea_id a ObjectId
        $tarea_id = new ObjectId($tarea_id);

        // Actualizar la tarea dentro del sembrío en MongoDB
        $updateResult = $sembríosCollection->updateOne(
            ['_id' => new ObjectId($sembrío_id), 'tareas._id' => $tarea_id],
            ['$set' => ['tareas.$.descripcion' => $descripcion, 'tareas.$.estado' => $estado]]
        );

        if ($updateResult->getModifiedCount() === 1) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo actualizar la tarea.']);
        }
    } else {
        // Agregar la nueva tarea al sembrío en MongoDB
        $updateResult = $sembríosCollection->updateOne(
            ['_id' => new ObjectId($sembrío_id)],
            ['$push' => ['tareas' => $nueva_tarea]]
        );

        if ($updateResult->getModifiedCount() === 1) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo agregar la tarea al sembrío.']);
        }
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error al agregar o actualizar la tarea: ' . $e->getMessage()]);
}
?>
