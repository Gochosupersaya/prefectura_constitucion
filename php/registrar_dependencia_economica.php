<?php
session_start();
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Sisbdpref";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener la cédula del usuario actual de la sesión
$cedula_usuario = $_SESSION['cedula'];

// Obtener el código del usuario actual de la tabla Prefttcli
$query = "SELECT Cli_codig FROM Prefttcli WHERE Cli_cedul = '$cedula_usuario'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$cedula_actual = $row['Cli_codig'];

// Obtener datos del formulario
$motivo = $_POST['dependencia-motivo'];

// Datos de la Persona Independiente
$independiente_cedula = $_POST['Independiente-cedula'];
$independiente_nombre = $_POST['Independiente-nombre'];
$independiente_apellido = $_POST['Independiente-apellido'];
$independiente_telefono = $_POST['Independiente-telefono'];
$independiente_foto_cedula = $_FILES['Independiente-foto-cedula']['name'];
$independiente_foto_rif = $_FILES['Independiente-foto-rif']['name'];
$independiente_aldea = $_POST['Independiente-aldea'];
$independiente_calle = $_POST['Independiente-calle'];
$independiente_carrera = $_POST['Independiente-carrera'];
$independiente_casa = $_POST['Independiente-casa'];

// Datos del Testigo 1
$testigo1_cedula = $_POST['testigo1-cedula'];
$testigo1_nombre = $_POST['testigo1-nombre'];
$testigo1_apellido = $_POST['testigo1-apellido'];
$testigo1_telefono = $_POST['testigo1-telefono'];
$testigo1_foto_cedula = $_FILES['testigo1-foto-cedula']['name'];
$testigo1_foto_rif = $_FILES['testigo1-foto-rif']['name'];
$testigo1_aldea = $_POST['testigo1-aldea'];
$testigo1_calle = $_POST['testigo1-calle'];
$testigo1_carrera = $_POST['testigo1-carrera'];
$testigo1_casa = $_POST['testigo1-casa'];

// Datos del Testigo 2
$testigo2_cedula = $_POST['testigo2-cedula'];
$testigo2_nombre = $_POST['testigo2-nombre'];
$testigo2_apellido = $_POST['testigo2-apellido'];
$testigo2_telefono = $_POST['testigo2-telefono'];
$testigo2_foto_cedula = $_FILES['testigo2-foto-cedula']['name'];
$testigo2_foto_rif = $_FILES['testigo2-foto-rif']['name'];
$testigo2_aldea = $_POST['testigo2-aldea'];
$testigo2_calle = $_POST['testigo2-calle'];
$testigo2_carrera = $_POST['testigo2-carrera'];
$testigo2_casa = $_POST['testigo2-casa'];

// Insertar en la tabla prefttsde
$sql = "INSERT INTO prefttsde (Sde_motiv, Sde_statu) VALUES ('$motivo', 'Enviada')";
if ($conn->query($sql) === TRUE) {
    $sde_codig = $conn->insert_id;
} else {
    die("Error al insertar en prefttsde: " . $conn->error);
}

// Insertar la constancia del usuario actual en prefttped
$sql = "INSERT INTO prefttped (Ped_clien, Ped_rolcl, Ped_depen) VALUES ('$cedula_actual', '6', '$sde_codig')";
if ($conn->query($sql) === FALSE) {
    die("Error al insertar en prefttped: " . $conn->error);
}

// Función para manejar la inserción o actualización de personas y su dirección
function manejar_persona($conn, $cedula, $nombre, $apellido, $telefono, $foto_cedula, $foto_rif, $aldea, $calle, $carrera, $casa, $rol, $sde_codig) {
    // Verificar si la persona ya existe
    $sql = "SELECT Per_cedul FROM preftmper WHERE Per_cedul = '$cedula'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // La persona ya existe, actualizar datos
        $sql = "UPDATE preftmper SET Per_nombr='$nombre', Per_apell='$apellido', Per_telef='$telefono', Per_cfoto='$foto_cedula', Per_rifpe='$foto_rif' WHERE Per_cedul='$cedula'";
        if ($conn->query($sql) === FALSE) {
            die("Error al actualizar preftmper: " . $conn->error);
        }
    } else {
        // La persona no existe, insertar datos
        $sql = "INSERT INTO preftmper (Per_cedul, Per_nombr, Per_apell, Per_telef, Per_cfoto, Per_rifpe) VALUES ('$cedula', '$nombre', '$apellido', '$telefono', '$foto_cedula', '$foto_rif')";
        if ($conn->query($sql) === FALSE) {
            die("Error al insertar en preftmper: " . $conn->error);
        }
    }

    // Manejar dirección
    $sql = "SELECT Din_cedul FROM prefttdii WHERE Din_cedul = '$cedula'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // La dirección ya existe, actualizar datos
        $sql = "UPDATE prefttdii SET Din_aldea='$aldea', Din_calle='$calle', Din_carre='$carrera', Din_ncasa='$casa' WHERE Din_cedul='$cedula'";
        if ($conn->query($sql) === FALSE) {
            die("Error al actualizar prefttdii: " . $conn->error);
        }
    } else {
        // La dirección no existe, insertar datos
        $sql = "INSERT INTO prefttdii (Din_cedul, Din_aldea, Din_calle, Din_carre, Din_ncasa) VALUES ('$cedula', '$aldea', '$calle', '$carrera', '$casa')";
        if ($conn->query($sql) === FALSE) {
            die("Error al insertar en prefttdii: " . $conn->error);
        }
    }

    // Asociar a prefttcli
    $sql = "SELECT Cli_codig FROM prefttcli WHERE Cli_cedul = '$cedula'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $cli_codig = $row['Cli_codig'];
    } else {
        // Insertar en prefttcli si no existe
        $sql = "INSERT INTO prefttcli (Cli_cedul) VALUES ('$cedula')";
        if ($conn->query($sql) === TRUE) {
            $cli_codig = $conn->insert_id;
        } else {
            die("Error al insertar en prefttcli: " . $conn->error);
        }
    }

    // Insertar en prefttped
    $sql = "INSERT INTO prefttped (Ped_clien, Ped_rolcl, Ped_depen) VALUES ('$cli_codig', '$rol', '$sde_codig')";
    if ($conn->query($sql) === FALSE) {
        die("Error al insertar en prefttped: " . $conn->error);
    }
}

// Manejar datos de la Persona Independiente
manejar_persona($conn, $independiente_cedula, $independiente_nombre, $independiente_apellido, $independiente_telefono, $independiente_foto_cedula, $independiente_foto_rif, $independiente_aldea, $independiente_calle, $independiente_carrera, $independiente_casa, '7', $sde_codig);

// Manejar datos del Testigo 1
manejar_persona($conn, $testigo1_cedula, $testigo1_nombre, $testigo1_apellido, $testigo1_telefono, $testigo1_foto_cedula, $testigo1_foto_rif, $testigo1_aldea, $testigo1_calle, $testigo1_carrera, $testigo1_casa, '3', $sde_codig);

// Manejar datos del Testigo 2
manejar_persona($conn, $testigo2_cedula, $testigo2_nombre, $testigo2_apellido, $testigo2_telefono, $testigo2_foto_cedula, $testigo2_foto_rif, $testigo2_aldea, $testigo2_calle, $testigo2_carrera, $testigo2_casa, '3', $sde_codig);

// Redirigir después del registro
header("Location: sesion_cliente.php#resume");
exit();
$conn->close();
?>