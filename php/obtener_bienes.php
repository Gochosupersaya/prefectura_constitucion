<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Sisbdpref";

$conn = new mysqli($servername, $username, $password, $dbname);

// Consulta para obtener los nombres de bienes
$sql = "SELECT Bie_codig, Bie_nombr FROM Preftmbie";
$result = $conn->query($sql);

$bienes = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $bienes[] = $row;
    }
}

$conn->close();

// Devolver datos como JSON
header('Content-Type: application/json');
echo json_encode($bienes);
?>
