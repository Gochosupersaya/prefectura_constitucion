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
        FROM prefttcli cli 
        JOIN preftmper per ON cli.Cli_cedul = per.Per_cedul 
        WHERE per.Per_cedul = '$current_user_cedula'";
$result = $conn->query($sql);
$current_user_codigo = $result->fetch_assoc()['Cli_codig'];

// Obtener datos del formulario
$motivo = $_POST['pobreza'];

// Insertar datos en prefttspo
$sql = "INSERT INTO prefttspo (Spo_clien, Spo_motiv, Spo_statu) 
        VALUES ('$current_user_codigo', '$motivo', 'Enviada')";
if ($conn->query($sql) === TRUE) {
    echo "Constancia de Pobreza enviada correctamente.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Redirigir después del registro
header("Location: sesion_cliente.php#resume");
exit();

$conn->close();
?>
