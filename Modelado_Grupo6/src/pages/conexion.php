<?php
require '../../vendor/autoload.php'; // Ruta correcta al archivo autoload.php

try {
    // Conectar a MongoDB en el contenedor Docker
    $mongoClient = new MongoDB\Client("mongodb://localhost:27017");
    $db = $mongoClient->mario1; // Seleccionar la base de datos mario1
    echo "<p>Conexión exitosa a la base de datos mario1</p>";

    // Seleccionar la colección 'usuarios'
    $collection = $db->usuarios;
} catch (Exception $e) {
    echo "No se pudo conectar a MongoDB: ", $e->getMessage(), "\n";
}
?>
