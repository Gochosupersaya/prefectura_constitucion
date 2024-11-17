<?php
session_start();
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Sisbdpref";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener la cédula del usuario actual (aquí debes reemplazarlo por la forma en que obtienes la cédula del usuario actual)
$current_user_cedula = $_SESSION['cedula'];

// Obtener el código del usuario actual
$sql = "SELECT cli.Cli_codig 
        FROM Prefttcli cli 
        JOIN Preftmper per ON cli.Cli_cedul = per.Per_cedul 
        WHERE per.Per_cedul = '$current_user_cedula'";
$result = $conn->query($sql);
$current_user_codigo = $result->fetch_assoc()['Cli_codig'];

// Insertar datos en prefttsmd
$smd_munll = $_POST['mudanza-municipio'];
$smd_lugar = $_POST['mudanza-fuera-calle'];

$sql = "INSERT INTO Prefttsmd (Smd_munll, Smd_lugar, Smd_statu) 
        VALUES ('$smd_munll', '$smd_lugar', 'Enviada')";
if ($conn->query($sql) === TRUE) {
    $smd_codig = $conn->insert_id;  // Obtener el código generado para la solicitud de mudanza
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Insertar datos del vehículo en prefttveh
$veh_model = $_POST['vehiculo-modelo'];
$veh_anio = $_POST['vehiculo-anio'];
$veh_color = $_POST['vehiculo-color'];
$veh_clase = $_POST['vehiculo-clase'];
$veh_placa = $_POST['vehiculo-placa'];
$veh_serial_motor = $_POST['vehiculo-serial-motor'];
$veh_serial_carroceria = $_POST['vehiculo-serial-carroceria'];

$sql = "INSERT INTO Prefttveh (Veh_model, Veh_añove, Veh_color, Veh_clase, Veh_placa, Veh_smoto, Veh_scarr) 
        VALUES ('$veh_model', '$veh_anio', '$veh_color', '$veh_clase', '$veh_placa', '$veh_serial_motor', '$veh_serial_carroceria')";
if ($conn->query($sql) === TRUE) {
    $veh_codig = $conn->insert_id;  // Obtener el código generado para el vehículo
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Insertar datos del conductor en preftmper
$conductor_cedula = $_POST['conductor-cedula'];
$conductor_nombre = $_POST['conductor-nombre'];
$conductor_apellido = $_POST['conductor-apellido'];
$conductor_telefono = $_POST['conductor-telefono'];

$sql = "INSERT INTO Preftmper (Per_cedul, Per_nombr, Per_apell, Per_telef) 
        VALUES ('$conductor_cedula', '$conductor_nombre', '$conductor_apellido', '$conductor_telefono')
        ON DUPLICATE KEY UPDATE Per_nombr='$conductor_nombre', Per_apell='$conductor_apellido', Per_telef='$conductor_telefono'";
if ($conn->query($sql) === TRUE) {
    if ($conn->affected_rows == 1) {
        $per_codig = $conn->insert_id;  // Nuevo conductor
    } else {
        $sql = "SELECT Per_cedul FROM Preftmper WHERE Per_cedul = '$conductor_cedula'";
        $result = $conn->query($sql);
        $per_codig = $result->fetch_assoc()['Per_cedul'];  // Conductor existente
    }
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Insertar datos del conductor en prefttcli si no existe
$sql = "INSERT INTO Prefttcli (Cli_cedul) VALUES ('$conductor_cedula') 
        ON DUPLICATE KEY UPDATE Cli_cedul = '$conductor_cedula'";
if ($conn->query($sql) === TRUE) {
    $sql = "SELECT Cli_codig FROM Prefttcli WHERE Cli_cedul = '$conductor_cedula'";
    $result = $conn->query($sql);
    $conductor_codigo = $result->fetch_assoc()['Cli_codig'];
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Insertar datos del conductor en prefttpmd
$sql = "INSERT INTO Prefttpmd (Pmd_clien, Pmd_rolcl, Pmd_mudan) 
        VALUES ('$conductor_codigo', 10, '$smd_codig')";
if ($conn->query($sql) === TRUE) {
    $pmd_codig_conductor = $conn->insert_id;  // Obtener el código generado para la relación conductor-mudanza
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Insertar datos del vehículo y conductor en prefttvyc
$sql = "INSERT INTO Prefttvyc (Vyc_vehic, Vyc_chofe) 
        VALUES ('$veh_codig', '$pmd_codig_conductor')";
if ($conn->query($sql) === TRUE) {
    // Vehículo y conductor insertados correctamente
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Insertar datos del cliente en prefttpmd
$sql = "INSERT INTO Prefttpmd (Pmd_clien, Pmd_rolcl, Pmd_mudan) 
        VALUES ('$current_user_codigo', 9, '$smd_codig')";
if ($conn->query($sql) === TRUE) {
    $pmd_codig_cliente = $conn->insert_id;  // Obtener el código generado para la relación cliente-mudanza
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Insertar bienes en prefttlis
$bienes_nombres = $_POST['bienes-nombre'];
$bienes_cantidades = $_POST['bienes-cantidad'];

for ($i = 0; $i < count($bienes_nombres); $i++) {
    $bien_codigo = $bienes_nombres[$i];
    $bien_cantidad = $bienes_cantidades[$i];

    $sql = "INSERT INTO Prefttlis (Lis_nbien, Lis_canti, Lis_mudan) 
            VALUES ('$bien_codigo', '$bien_cantidad', '$smd_codig')";
    if ($conn->query($sql) === TRUE) {
        // Bien insertado correctamente
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Redirigir después del registro
header("Location: sesion_cliente.php#resume");
exit();

$conn->close();
?>
