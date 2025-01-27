<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Sisbdpref";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$marcaId = $_GET['marca'];
$sql = "SELECT Mca_codig, Mca_model FROM prefttmca WHERE Car_codig = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $marcaId);
$stmt->execute();
$result = $stmt->get_result();

$modelos = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $modelos[] = $row;
    }
}
$stmt->close();
$conn->close();

echo json_encode($modelos);
?>
