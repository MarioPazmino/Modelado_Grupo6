<?php
session_start();

// Verificar si el usuario está autenticado y tiene el rol adecuado
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'usuario')) {
    header("HTTP/1.1 403 Forbidden");
    exit();
}

require '../vendor/autoload.php'; // Incluir el archivo autoload.php de MongoDB

use MongoDB\Client;
use MongoDB\BSON\ObjectId;
use MongoDB\Exception\Exception;

// Conexión a MongoDB
$mongoClient = new Client("mongodb://grupo6:grupo6@localhost:27017");
$db = $mongoClient->selectDatabase("grupo6_agrohub");
$sembríosCollection = $db->sembríos; // Colección de sembríos en MongoDB

// Verificar si se recibieron todos los datos necesarios
if (isset($_POST['sembrío_id'], $_POST['tarea_id'], $_POST['descripcion'], $_POST['fecha_realizacion'], $_POST['estado'])) {
    $sembrío_id = new ObjectId($_POST['sembrío_id']);
    $tarea_id = new ObjectId($_POST['tarea_id']);
    $descripcion = $_POST['descripcion'];
    $fecha_realizacion = $_POST['fecha_realizacion'];
    $estado = $_POST['estado'];

    try {
        // Actualizar la tarea específica dentro del sembrío
        $updateResult = $sembríosCollection->updateOne(
            ['_id' => $sembrío_id, 'tareas._id' => $tarea_id],
            ['$set' => [
                'tareas.$.descripcion' => $descripcion,
                'tareas.$.fecha_realizacion' => $fecha_realizacion,
                'tareas.$.estado' => $estado,
            ]]
        );

        // Verificar si se realizó la actualización correctamente
        if ($updateResult->getModifiedCount() === 1) {
            // Envío de respuesta exitosa
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit();
        } else {
            // Envío de respuesta de error si no se encontró la tarea para actualizar
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No se encontró la tarea para actualizar']);
            exit();
        }
    } catch (Exception $e) {
        // Envío de respuesta de error en caso de excepción
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error al actualizar la tarea: ' . $e->getMessage()]);
        exit();
    }
} else {
    // Envío de respuesta de error si no se proporcionaron todos los datos necesarios
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Datos incompletos para actualizar la tarea']);
    exit();
}
?>
