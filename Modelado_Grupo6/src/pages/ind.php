<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos de la base de datos mario1</title>
</head>
<body>
    <h1>Datos de la base de datos mario1</h1>
    <ul>
    <?php
    require '../../vendor/autoload.php'; // Ruta correcta al archivo autoload.php

    try {
        // Conectar a MongoDB en el contenedor Docker
        $mongoClient = new MongoDB\Client("mongodb://localhost:27017");
        $db = $mongoClient->mario1; // Seleccionar la base de datos mario1
        echo "<p>Conexión exitosa a la base de datos mario1</p>";

        // Seleccionar la colección 'usuarios'
        $collection = $db->usuarios;

        // Obtener todos los documentos de la colección 'usuarios'
        $documents = $collection->find([], ['projection' => ['_id' => 1, 'nombre' => 1, 'edad' => 1]]);

        foreach ($documents as $document) {
            echo "<li>";
            echo "<strong>ID:</strong> " . $document->_id . "<br>";
            echo "<strong>Nombre:</strong> " . $document->nombre . "<br>";
            echo "<strong>Edad:</strong> " . $document->edad . "<br>";
            echo "</li>";
        }
    } catch (Exception $e) {
        echo "No se pudo conectar a MongoDB: ", $e->getMessage(), "\n";
    }
    ?>
    </ul>
</body>

<script src="https://kit.fontawesome.com/87f3cd4132.js" crossorigin="anonymous"></script>
    <script src="../components/cuenta.js"></script>
    <script src="../components/section.js"></script>
    <script src="../components/custom-elements.js"></script>
    <script src="../components/htmltemaplte.js"></script>
</html>
