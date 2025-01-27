<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedula = $_POST['cedula'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    // Nuevos datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $telefono = $_POST['telefono'];
    $residencia = $_POST['residencia'];

    $carpeta_cedula = 'imagenes/cedula/';
    $carpeta_rif = 'imagenes/rif/';
    $cfoto_path = NULL;
    $rfoto_path = NULL;

    

    // Verificar si el usuario ya existe
    $sql = "SELECT * FROM preftmper WHERE Per_cedul = '$cedula'";
    $result = $conexion->query($sql);

    if ($result->num_rows > 0) {
        echo "El usuario con esta cédula ya existe.";
    } else {

        // Guardar la foto de la cédula
    if (isset($_FILES['cfoto']) && $_FILES['cfoto']['error'] == 0) {
        $cfoto_nombre = $carpeta_cedula . 'cedula_' . $cedula . '_' . time() . '.jpg';
        if (move_uploaded_file($_FILES['cfoto']['tmp_name'], $cfoto_nombre)) {
            $cfoto_path = $cfoto_nombre;
        }
    }

    // Guardar la foto del RIF
    if (isset($_FILES['rfoto']) && $_FILES['rfoto']['error'] == 0) {
        $rfoto_nombre = $carpeta_rif . 'rif_' . $cedula . '_' . time() . '.jpg';
        if (move_uploaded_file($_FILES['rfoto']['tmp_name'], $rfoto_nombre)) {
            $rfoto_path = $rfoto_nombre;
        }
    }
        // Insertar el nuevo usuario en la tabla de personas
        $sql = "INSERT INTO preftmper (Per_cedul, Per_nombr, Per_apell, Per_telef, Per_cfoto, Per_rifpe) VALUES ('$cedula', '$nombre', '$apellido', '$telefono', '$cfoto_path', '$rfoto_path')";
        if ($conexion->query($sql) === TRUE) {
            // Insertar el nuevo usuario en la tabla de usuarios
            $sql = "INSERT INTO prefttusu (Usu_cedul, Usu_corre, Usu_contr) VALUES ('$cedula', '$correo', '$contrasena')";
            if ($conexion->query($sql) === TRUE) {
                // Insertar el nuevo usuario en la tabla de clientes
                $sql = "INSERT INTO prefttcli (Cli_cedul) VALUES ('$cedula')";
                if ($conexion->query($sql) === TRUE) {
                    // Insertar la dirección según la residencia
                    if ($residencia == 'constitucion') {
                        $aldea = $_POST['aldea'];
                        $calle = $_POST['calle1'];
                        $carre = $_POST['carre1'];
                        $ncasa = $_POST['ncasa1'];
                        $sql = "INSERT INTO prefttdii (Din_cedul, Din_aldea, Din_calle, Din_carre, Din_ncasa) VALUES ('$cedula', '$aldea', '$calle', '$carre', '$ncasa')";
                    } else {
                        // Municipio y otros datos necesarios para insertar en la tabla de direcciones para residentes fuera de la parroquia
                        if (isset($_POST['municipio']) && isset($_POST['calle']) && isset($_POST['carre']) && isset($_POST['ncasa'])) {
                            $municipio = $_POST['municipio'];
                            $calle = $_POST['calle'];
                            $carre = $_POST['carre'];
                            $ncasa = $_POST['ncasa'];
                            $sql = "INSERT INTO prefttdie (Die_cedul, Die_munic, Die_calle, Die_carre, Die_ncasa) VALUES ('$cedula', '$municipio', '$calle', '$carre', '$ncasa')";
                        } else {
                            echo "Error: Faltan datos necesarios para la dirección.";
                            exit;
                        }
                    }
                    if ($conexion->query($sql) === TRUE) {
                        header("location: login.php");
                    } else {
                        echo "Error al insertar la dirección del usuario: " . $sql . "<br>" . $conexion->error;
                    }
                } else {
                    echo "Error al insertar el usuario en la tabla de clientes: " . $sql . "<br>" . $conexion->error;
                }
            } else {
                echo "Error al insertar el usuario en la tabla de usuarios: " . $sql . "<br>" . $conexion->error;
            }
        } else {
            echo "Error al insertar el usuario en la tabla de personas: " . $sql . "<br>" . $conexion->error;
        }
    }
}
?>
