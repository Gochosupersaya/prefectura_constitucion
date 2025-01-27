<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedula = $_POST['cedula'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $telefono = $_POST['telefono'];
    $residencia = $_POST['residencia'];

    $carpeta_cedula = 'imagenes/cedula/';
    $carpeta_rif = 'imagenes/rif/';
    $cfoto_path = NULL;
    $rfoto_path = NULL;

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

    // Validación para evitar insertar un usuario duplicado
    $sql = "SELECT * FROM preftmper WHERE Per_cedul = '$cedula'";
    $result = $conexion->query($sql);

    if ($result->num_rows > 0) {
        echo "El usuario con esta cédula ya existe.";
    } else {
        $sql = "INSERT INTO preftmper (Per_cedul, Per_nombr, Per_apell, Per_telef, Per_cfoto, Per_rifpe)
                VALUES ('$cedula', '$nombre', '$apellido', '$telefono', '$cfoto_path', '$rfoto_path')";
        if ($conexion->query($sql) === TRUE) {
            $sql = "INSERT INTO prefttusu (Usu_cedul, Usu_corre, Usu_contr, Usu_statu) VALUES ('$cedula', '$correo', '$contrasena', 'Activo')";
            if ($conexion->query($sql) === TRUE) {
                $sql = "INSERT INTO prefttcli (Cli_cedul) VALUES ('$cedula')";
                if ($conexion->query($sql) === TRUE) {
                    if ($residencia == 'constitucion') {
                        $aldea = $_POST['aldea'];
                        $calle = $_POST['calle1'];
                        $carre = $_POST['carre1'];
                        $ncasa = $_POST['ncasa1'];
                        $sql = "INSERT INTO prefttdii (Din_cedul, Din_aldea, Din_calle, Din_carre, Din_ncasa) VALUES ('$cedula', '$aldea', '$calle', '$carre', '$ncasa')";
                    } else {
                        $municipio = $_POST['municipio'];
                        $calle = $_POST['calle'];
                        $carre = $_POST['carre'];
                        $ncasa = $_POST['ncasa'];
                        $sql = "INSERT INTO prefttdie (Die_cedul, Die_munic, Die_calle, Die_carre, Die_ncasa) VALUES ('$cedula', '$municipio', '$calle', '$carre', '$ncasa')";
                    }
                    if ($conexion->query($sql) === TRUE) {
                        header("Location: sesion_admin.php#blog");
                    } else {
                        echo "Error al insertar la dirección: " . $conexion->error;
                    }
                } else {
                    echo "Error al insertar en la tabla de clientes: " . $conexion->error;
                }
            } else {
                echo "Error al insertar en la tabla de usuarios: " . $conexion->error;
            }
        } else {
            echo "Error al insertar en la tabla de personas: " . $conexion->error;
        }
    }
}
?>
