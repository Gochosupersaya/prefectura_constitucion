<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedula = $_POST['cedula'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $telefono = $_POST['telefono'];
    $cfoto = isset($_FILES['cfoto']['tmp_name']) && $_FILES['cfoto']['tmp_name'] != '' ? addslashes(file_get_contents($_FILES['cfoto']['tmp_name'])) : null;
    $rfoto = isset($_FILES['rfoto']['tmp_name']) && $_FILES['rfoto']['tmp_name'] != '' ? addslashes(file_get_contents($_FILES['rfoto']['tmp_name'])) : null;

    // Actualizar datos personales
    $sql_persona = "UPDATE Preftmper SET Per_nombr='$nombre', Per_apell='$apellido', Per_telef='$telefono'";
    $sql_persona .= $cfoto ? ", Per_cfoto='$cfoto'" : "";
    $sql_persona .= $rfoto ? ", Per_rifpe='$rfoto'" : "";
    $sql_persona .= " WHERE Per_cedul='$cedula'";

    // Manejo de contraseña
    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
        $confirm_password = $_POST['confirmPassword'];

        if ($password === $confirm_password) {
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $sql_usuario = "INSERT INTO Prefttusu (Usu_cedul, Usu_contr) VALUES ('$cedula', '$password_hash')";

            if ($conexion->query($sql_usuario) !== TRUE) {
                echo "Error al insertar la contraseña: " . $conexion->error;
                exit();
            }
        } else {
            echo "Las contraseñas no coinciden.";
            exit();
        }
    }

    // Manejo de dirección
    $aldea = $_POST['aldea'];
    $calle1 = $_POST['calle1'];
    $carre1 = $_POST['carre1'];
    $ncasa1 = $_POST['ncasa1'];

    $sql_delete = "DELETE FROM Prefttdii WHERE Din_cedul='$cedula'";
    $conexion->query($sql_delete);

    $sql_insert = "INSERT INTO Prefttdii (Din_cedul, Din_aldea, Din_calle, Din_carre, Din_ncasa) VALUES ('$cedula', '$aldea', '$calle1', '$carre1', '$ncasa1')";
    $conexion->query($sql_insert);

    if ($conexion->query($sql_persona) === TRUE) {
        echo "Datos actualizados correctamente";
    } else {
        echo "Error al actualizar los datos: " . $conexion->error;
    }

    $conexion->close();
}
?>
