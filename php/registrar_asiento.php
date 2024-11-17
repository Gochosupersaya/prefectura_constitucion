<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Sisbdpref";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

// Obtener el código del usuario actual
$user_cedula = $_SESSION['cedula'];
$sql = "SELECT Cli_codig FROM Prefttcli WHERE Cli_cedul = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_cedula);
$stmt->execute();
$stmt->bind_result($cli_codig);
$stmt->fetch();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $motivo = $_POST["asiento-motivo"];

    // Datos de la persona difunta
    $difunto_cedula = $_POST["difunto-cedula"];
    $difunto_nombre = $_POST["difunto-nombre"];
    $difunto_apellido = $_POST["difunto-apellido"];
    $difunto_fecha_fallecimiento = $_POST["difunto-fecha-fallecimiento"];
    $difunto_hora_fallecimiento = $_POST["difunto-hora-fallecimiento"];
    $difunto_foto_cedula = $_FILES["difunto-foto-cedula"]["name"];
    $difunto_foto_rif = $_FILES["difunto-foto-rif"]["name"];
    $difunto_acta_defuncion_numero = $_POST["difunto-acta-defuncion-numero"];
    $difunto_acta_defuncion_foto = $_FILES["difunto-acta-defuncion-foto"]["name"];
    $difunto_aldea = $_POST["difunto-aldea"];
    $difunto_calle = $_POST["difunto-calle"];
    $difunto_carrera = $_POST["difunto-carrera"];
    $difunto_casa = $_POST["difunto-casa"];

    // Datos del testigo 1
    $testigo1_cedula = $_POST["testigo1-cedula"];
    $testigo1_nombre = $_POST["testigo1-nombre"];
    $testigo1_apellido = $_POST["testigo1-apellido"];
    $testigo1_telefono = $_POST["testigo1-telefono"];
    $testigo1_foto_cedula = $_FILES["testigo1-foto-cedula"]["name"];
    $testigo1_foto_rif = $_FILES["testigo1-foto-rif"]["name"];
    $testigo1_aldea = $_POST["testigo1-aldea"];
    $testigo1_calle = $_POST["testigo1-calle"];
    $testigo1_carrera = $_POST["testigo1-carrera"];
    $testigo1_casa = $_POST["testigo1-casa"];

    // Datos del testigo 2
    $testigo2_cedula = $_POST["testigo2-cedula"];
    $testigo2_nombre = $_POST["testigo2-nombre"];
    $testigo2_apellido = $_POST["testigo2-apellido"];
    $testigo2_telefono = $_POST["testigo2-telefono"];
    $testigo2_foto_cedula = $_FILES["testigo2-foto-cedula"]["name"];
    $testigo2_foto_rif = $_FILES["testigo2-foto-rif"]["name"];
    $testigo2_aldea = $_POST["testigo2-aldea"];
    $testigo2_calle = $_POST["testigo2-calle"];
    $testigo2_carrera = $_POST["testigo2-carrera"];
    $testigo2_casa = $_POST["testigo2-casa"];

    // Insertar en la tabla prefttsas
    $sql = "INSERT INTO Prefttsas (Sas_motiv, Sas_statu) VALUES (?, 'Enviada')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $motivo);
    $stmt->execute();
    $sas_codig = $stmt->insert_id;
    $stmt->close();

    // Guardar datos de la persona difunta en preftmper
    $sql = "INSERT INTO Preftmper (Per_cedul, Per_nombr, Per_apell, Per_telef, Per_cfoto, Per_rifpe)
            VALUES (?, ?, ?, '', ?, ?)
            ON DUPLICATE KEY UPDATE
                Per_nombr = VALUES(Per_nombr),
                Per_apell = VALUES(Per_apell),
                Per_cfoto = VALUES(Per_cfoto),
                Per_rifpe = VALUES(Per_rifpe)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $difunto_cedula, $difunto_nombre, $difunto_apellido, $difunto_foto_cedula, $difunto_foto_rif);
    $stmt->execute();
    $stmt->close();

    // Asociar la cédula de la persona difunta a su dirección en prefttdii
    $sql = "INSERT INTO Prefttdii (Din_cedul, Din_aldea, Din_calle, Din_carre, Din_ncasa)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                Din_aldea = VALUES(Din_aldea),
                Din_calle = VALUES(Din_calle),
                Din_carre = VALUES(Din_carre),
                Din_ncasa = VALUES(Din_ncasa)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $difunto_cedula, $difunto_aldea, $difunto_calle, $difunto_carrera, $difunto_casa);
    $stmt->execute();
    $stmt->close();

    // Asociar la cédula de la persona difunta a la tabla prefttcli
    $sql = "INSERT INTO Prefttcli (Cli_cedul) VALUES (?) ON DUPLICATE KEY UPDATE Cli_cedul = VALUES(Cli_cedul)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $difunto_cedula);
    $stmt->execute();
    $difunto_cli_codig = $stmt->insert_id;
    $stmt->close();

    // Insertar en prefttpdi los datos adicionales de la persona difunta
    $sql = "INSERT INTO Prefttpdi (Pdi_clien, Pdi_asien, Pdi_ffall, Pdi_hfall, Pdi_nacta, Pdi_fotoa)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissss", $difunto_cli_codig, $sas_codig, $difunto_fecha_fallecimiento, $difunto_hora_fallecimiento, $difunto_acta_defuncion_numero, $difunto_acta_defuncion_foto);
    $stmt->execute();
    $stmt->close();

    // Función para insertar datos del testigo en preftmper, prefttdii, prefttcli y prefttpas
    function insertar_testigo($conn, $sas_codig, $cedula, $nombre, $apellido, $telefono, $foto_cedula, $foto_rif, $aldea, $calle, $carrera, $casa, $rolcl) {
        $sql = "INSERT INTO Preftmper (Per_cedul, Per_nombr, Per_apell, Per_telef, Per_cfoto, Per_rifpe)
                VALUES (?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    Per_nombr = VALUES(Per_nombr),
                    Per_apell = VALUES(Per_apell),
                    Per_telef = VALUES(Per_telef),
                    Per_cfoto = VALUES(Per_cfoto),
                    Per_rifpe = VALUES(Per_rifpe)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $cedula, $nombre, $apellido, $telefono, $foto_cedula, $foto_rif);
        $stmt->execute();
        $stmt->close();

        // Asociar la cédula del testigo a su dirección en prefttdii
        $sql = "INSERT INTO Prefttdii (Din_cedul, Din_aldea, Din_calle, Din_carre, Din_ncasa)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    Din_aldea = VALUES(Din_aldea),
                    Din_calle = VALUES(Din_calle),
                    Din_carre = VALUES(Din_carre),
                    Din_ncasa = VALUES(Din_ncasa)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $cedula, $aldea, $calle, $carrera, $casa);
        $stmt->execute();
        $stmt->close();

        // Asociar la cédula del testigo a la tabla prefttcli
        $sql = "INSERT INTO Prefttcli (Cli_cedul) VALUES (?) ON DUPLICATE KEY UPDATE Cli_cedul = VALUES(Cli_cedul)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $cedula);
        $stmt->execute();
        $cli_codig = $stmt->insert_id;
        $stmt->close();

        // Insertar en prefttpas los datos del testigo
        $sql = "INSERT INTO Prefttpas (Pas_clien, Pas_asien, Pas_rolcl) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $cli_codig, $sas_codig, $rolcl);
        $stmt->execute();
        $stmt->close();
    }

    // Insertar datos del testigo 1
    insertar_testigo($conn, $sas_codig, $testigo1_cedula, $testigo1_nombre, $testigo1_apellido, $testigo1_telefono, $testigo1_foto_cedula, $testigo1_foto_rif, $testigo1_aldea, $testigo1_calle, $testigo1_carrera, $testigo1_casa, 3);

    // Insertar datos del testigo 2
    insertar_testigo($conn, $sas_codig, $testigo2_cedula, $testigo2_nombre, $testigo2_apellido, $testigo2_telefono, $testigo2_foto_cedula, $testigo2_foto_rif, $testigo2_aldea, $testigo2_calle, $testigo2_carrera, $testigo2_casa, 3);

    // Insertar datos del usuario actual en prefttpas
    $sql = "INSERT INTO Prefttpas (Pas_clien, Pas_asien, Pas_rolcl) VALUES (?, ?, 8)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $cli_codig, $sas_codig);
    $stmt->execute();
    $stmt->close();

    // Redirigir después del registro
header("Location: sesion_cliente.php#resume");
exit();
}

$conn->close();
?>
