<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedula = $_POST['cedula'];

    // Obtener los valores actuales de la base de datos
    $sql_actual = "SELECT Per_nombr, Per_apell, Per_telef, Usu_corre, Usu_statu FROM Preftmper p JOIN Prefttusu u ON p.Per_cedul = u.Usu_cedul WHERE p.Per_cedul = '$cedula'";
    $resultado = $conexion->query($sql_actual);
    $row = $resultado->fetch_assoc();

    // Si no se han enviado valores, mantener los valores actuales de la base de datos
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : $row['Per_nombr'];
    $apellido = isset($_POST['apellido']) ? $_POST['apellido'] : $row['Per_apell'];
    $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : $row['Per_telef'];
    $correo = isset($_POST['correo']) ? $_POST['correo'] : $row['Usu_corre'];
    $estado = isset($_POST['estado']) ? $_POST['estado'] : $row['Usu_statu'];

    // Manejo de las fotos, si se enviaron
    $cfoto = isset($_FILES['cfoto']['tmp_name']) && $_FILES['cfoto']['tmp_name'] != '' ? addslashes(file_get_contents($_FILES['cfoto']['tmp_name'])) : null;
    $rfoto = isset($_FILES['rfoto']['tmp_name']) && $_FILES['rfoto']['tmp_name'] != '' ? addslashes(file_get_contents($_FILES['rfoto']['tmp_name'])) : null;

    // Actualizar datos personales y correo
    $sql_persona = "UPDATE Preftmper SET Per_nombr='$nombre', Per_apell='$apellido', Per_telef='$telefono'";
    $sql_persona .= $cfoto ? ", Per_cfoto='$cfoto'" : "";
    $sql_persona .= " WHERE Per_cedul='$cedula'";

    $sql_usuario = "UPDATE Prefttusu SET Usu_corre='$correo'";
    $sql_usuario .= $estado ? ", Usu_statu='$estado'" : "";
    $sql_usuario .= " WHERE Usu_cedul='$cedula'";

    // Ejecución de las consultas principales
    $actualizacion_exitosa = $conexion->query($sql_persona) === TRUE && $conexion->query($sql_usuario) === TRUE;

    // Verificar el cambio de residencia
    if ($_POST['residencia'] == 'fuera') {
        // Mover el registro a la tabla externa
        $conexion->query("DELETE FROM Prefttdii WHERE Din_cedul='$cedula'");

        // Insertar nuevo registro en Prefttdie
        $municipio = $_POST['municipio'];
        $calle = $_POST['calle'];
        $carre = $_POST['carre'];
        $ncasa = $_POST['ncasa'];

        $sql_insert = "INSERT INTO Prefttdie (Die_cedul, Die_munic, Die_calle, Die_carre, Die_ncasa) VALUES ('$cedula', '$municipio', '$calle', '$carre', '$ncasa')";
        $conexion->query($sql_insert);
    } else {
        // Mover el registro a la tabla interna
        $conexion->query("DELETE FROM Prefttdie WHERE Die_cedul='$cedula'");

        // Insertar nuevo registro en Prefttdii
        $aldea = $_POST['aldea'];
        $calle1 = $_POST['calle1'];
        $carre1 = $_POST['carre1'];
        $ncasa1 = $_POST['ncasa1'];

        $sql_insert = "INSERT INTO Prefttdii (Din_cedul, Din_aldea, Din_calle, Din_carre, Din_ncasa) VALUES ('$cedula', '$aldea', '$calle1', '$carre1', '$ncasa1')";
        $conexion->query($sql_insert);
    }

    // Verificación y salida del resultado
    if ($actualizacion_exitosa) {
        echo "Datos actualizados correctamente";
    } else {
        echo "Error al actualizar los datos: " . $conexion->error;
    }

    $conexion->close();
}
?>
