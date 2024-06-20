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
use MongoDB\BSON\UTCDateTime;
use MongoDB\Exception\Exception;

try {
    // Conexión a MongoDB
    $mongoClient = new Client("mongodb://grupo6:grupo6@localhost:27017");
    $db = $mongoClient->selectDatabase("grupo6_agrohub");
    $sembríosCollection = $db->sembríos; // Colección de sembríos en MongoDB

    // Obtener datos del formulario
    $sembrío_id = isset($_POST['sembrío_id']) ? $_POST['sembrío_id'] : null;
    $producto_nombre = $_POST['producto_nombre'];
    $fecha_siembra = new UTCDateTime(strtotime($_POST['fecha_siembra']) * 1000); // Convertir a UTCDateTime
    $ubicacion = $_POST['ubicacion'];
    $area = (int)$_POST['area'];
    $tareas = isset($_POST['tareas']) ? json_decode($_POST['tareas'], true) : []; // Decodificar las tareas desde JSON

    // Validar que el área sea mayor a 0
    if ($area <= 0) {
        echo json_encode(['success' => false, 'message' => 'El área debe ser mayor a 0.']);
        exit();
    }

    if ($sembrío_id) {
        // Actualizar un sembrío existente
        $updateResult = $sembríosCollection->updateOne(
            ['_id' => new ObjectId($sembrío_id)],
            ['$set' => [
                'producto_nombre' => $producto_nombre,
                'fecha_siembra' => $fecha_siembra,
                'ubicacion' => $ubicacion,
                'area' => $area,
                'tareas' => [], // Limpiar las tareas existentes antes de agregar las nuevas
            ]]
        );

        if ($updateResult->getModifiedCount() === 1) {
            // Insertar las tareas
            foreach ($tareas as $index => $tarea) {
                $tarea_descripcion = $tarea['descripcion'];
                $tarea_fecha_realizacion = isset($tarea['fecha_realizacion']) ? new UTCDateTime(strtotime($tarea['fecha_realizacion']) * 1000) : null;
                $tarea_estado = isset($tarea['estado']) ? $tarea['estado'] : 'pendiente';

                if ($tarea_descripcion && $tarea_fecha_realizacion) {
                    $tarea = [
                        'descripcion' => $tarea_descripcion,
                        'fecha_realizacion' => $tarea_fecha_realizacion,
                        'estado' => $tarea_estado,
                    ];
                    $sembríosCollection->updateOne(
                        ['_id' => new ObjectId($sembrío_id)],
                        ['$push' => ['tareas' => $tarea]]
                    );
                }
            }

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo actualizar el sembrío.']);
        }
    } else {
        // Insertar un nuevo sembrío
        $nuevo_sembrío = [
            'usuario_id' => new ObjectId($_SESSION['usuario_id']),
            'producto_nombre' => $producto_nombre,
            'fecha_siembra' => $fecha_siembra,
            'ubicacion' => $ubicacion,
            'area' => $area,
            'tareas' => [], // Inicializar con un array vacío para las tareas
        ];

        $insertOneResult = $sembríosCollection->insertOne($nuevo_sembrío);

        if ($insertOneResult->getInsertedCount() === 1) {
            // Insertar las tareas
            $sembrío_id = $insertOneResult->getInsertedId();
            foreach ($tareas as $index => $tarea) {
                $tarea_descripcion = $tarea['descripcion'];
                $tarea_fecha_realizacion = isset($tarea['fecha_realizacion']) ? new UTCDateTime(strtotime($tarea['fecha_realizacion']) * 1000) : null;
                $tarea_estado = isset($tarea['estado']) ? $tarea['estado'] : 'pendiente';

                if ($tarea_descripcion && $tarea_fecha_realizacion) {
                    $tarea = [
                        'descripcion' => $tarea_descripcion,
                        'fecha_realizacion' => $tarea_fecha_realizacion,
                        'estado' => $tarea_estado,
                    ];
                    $sembríosCollection->updateOne(
                        ['_id' => new ObjectId($sembrío_id)],
                        ['$push' => ['tareas' => $tarea]]
                    );
                }
            }

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Hubo un error al guardar el sembrío.']);
        }
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
