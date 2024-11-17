<?php
include('conexion.php'); // Asegúrate de incluir tu archivo de conexión a la base de datos

if (is_uploaded_file($_FILES['imagen']['tmp_name'])) {
    // Obtener el contenido de la imagen temporal
    $imagen_temp = file_get_contents($_FILES['imagen']['tmp_name']);

    // Crear una imagen desde el contenido temporal
    $imagen_original = imagecreatefromstring($imagen_temp);

    // Convertir y guardar la imagen en un formato JPG o PNG
    ob_start(); // Inicia el almacenamiento en buffer
    imagejpeg($imagen_original); // Guarda como JPEG en el buffer de salida
    $imagen_convertida = ob_get_contents(); // Obtén los datos de la imagen en el buffer
    ob_end_clean(); // Limpia el buffer

    // Ahora $imagen_convertida contiene los datos JPEG de la imagen, que puedes almacenar en la base de datos
    $stmt = $conexion->prepare("INSERT INTO Preftmper (Per_cfoto) VALUES (?)");
    $stmt->bind_param("b", $imagen_convertida);
    $stmt->send_long_data(0, $imagen_convertida);
    $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica si se subió un archivo
    if (isset($_FILES['imagen']) && is_uploaded_file($_FILES['imagen']['tmp_name'])) {
        
        // Obtener el contenido de la imagen temporal
        $imagen_temp = file_get_contents($_FILES['imagen']['tmp_name']);

        // Crear una imagen desde el contenido temporal
        $imagen_original = imagecreatefromstring($imagen_temp);

        // Convertir y guardar la imagen en un formato JPG o PNG
        ob_start(); // Inicia el almacenamiento en buffer
        imagejpeg($imagen_original); // Guarda como JPEG en el buffer de salida
        $imagen_convertida = ob_get_contents(); // Obtén los datos de la imagen en el buffer
        ob_end_clean(); // Limpia el buffer

        // Guardar la imagen en la base de datos
        $stmt = $conexion->prepare("INSERT INTO Preftmper (Per_cfoto) VALUES (?)");
        $stmt->bind_param("b", $imagen_convertida);
        $stmt->send_long_data(0, $imagen_convertida);
        
        if ($stmt->execute()) {
            echo "Imagen guardada exitosamente.";
        } else {
            echo "Error al guardar la imagen.";
        }
        $stmt->close();
    } else {
        echo "No se subió ninguna imagen.";
    }
}
?>
