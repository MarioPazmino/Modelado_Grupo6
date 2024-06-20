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
use MongoDB\BSON\UTCDateTime;
use MongoDB\Exception\Exception;

// Configuración de la conexión a MongoDB
$mongoClient = new Client("mongodb://grupo6:grupo6@localhost:27017");
$db = $mongoClient->selectDatabase("grupo6_agrohub");
$sembríosCollection = $db->sembríos; // Colección de sembríos en MongoDB

try {
    // Obtener el ID del sembrío desde la solicitud GET
    if (isset($_GET['id'])) {
        $sembrío_id = $_GET['id'];

        // Consultar MongoDB para obtener el sembrío por su ID
        $sembrío = $sembríosCollection->findOne(['_id' => new ObjectId($sembrío_id)]);

        if ($sembrío) {
            // Preparar los datos del sembrío para enviar como respuesta JSON
            $response = [
                'success' => true,
                'sembrío' => [
                    '_id' => (string) $sembrío['_id'],
                    'producto_nombre' => $sembrío['producto_nombre'],
                    'fecha_siembra' => $sembrío['fecha_siembra']->toDateTime()->format('Y-m-d'),
                    'ubicacion' => $sembrío['ubicacion'],
                    'area' => $sembrío['area'],
                    'tareas' => $sembrío['tareas'], // Incluir las tareas del sembrío
                ]
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Sembrío no encontrado'
            ];
        }

        // Devolver respuesta como JSON al frontend
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } else {
        $response = [
            'success' => false,
            'message' => 'ID de sembrío no especificado'
        ];
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Error al obtener el sembrío: ' . $e->getMessage()
    ];
}

// Devolver respuesta de error en caso de problemas
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
