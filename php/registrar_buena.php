<?php
session_start();
include('conexion.php'); // Asegúrate de tener el archivo de conexión correcto.

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener la cédula del usuario actual de la sesión
    $cedula_usuario = $_SESSION['cedula'];

    // Obtener el código del usuario actual de la tabla Prefttcli
    $query = "SELECT Cli_codig FROM Prefttcli WHERE Cli_cedul = '$cedula_usuario'";
    $result = mysqli_query($conexion, $query);
    $row = mysqli_fetch_assoc($result);
    $codigo_usuario = $row['Cli_codig'];

    // Insertar en prefttsbc
    $motivo_buena_conducta = $_POST['buena-conducta-motivo'];
    $query = "INSERT INTO Prefttsbc (Sbc_motiv, Sbc_statu) VALUES ('$motivo_buena_conducta', 'Enviada')";
    mysqli_query($conexion, $query);
    $codigo_solicitud = mysqli_insert_id($conexion);

    function registrar_testigo($conexion, $codigo_usuario, $codigo_solicitud, $rol_cliente, $testigo_data) {
        // Verificar si el testigo ya existe en la base de datos
        $cedula_testigo = $testigo_data['cedula'];
        $query = "SELECT Per_cedul FROM Preftmper WHERE Per_cedul = '$cedula_testigo'";
        $result = mysqli_query($conexion, $query);

        if (mysqli_num_rows($result) > 0) {
            // Testigo ya existe, obtener su código de cliente
            $query = "SELECT Cli_codig FROM Prefttcli WHERE Cli_cedul = '$cedula_testigo'";
            $result = mysqli_query($conexion, $query);
            $row = mysqli_fetch_assoc($result);
            $codigo_cliente = $row['Cli_codig'];
        } else {
            // Testigo no existe, insertarlo en Preftmper y Prefttcli
            $nombre_testigo = $testigo_data['nombre'];
            $apellido_testigo = $testigo_data['apellido'];
            $telefono_testigo = $testigo_data['telefono'];
            $foto_cedula_testigo = $testigo_data['foto_cedula'];
            $foto_rif_testigo = $testigo_data['foto_rif'];
            $query = "INSERT INTO Preftmper (Per_cedul, Per_nombr, Per_apell, Per_telef, Per_cfoto, Per_rifpe) 
                      VALUES ('$cedula_testigo', '$nombre_testigo', '$apellido_testigo', '$telefono_testigo', '$foto_cedula_testigo', '$foto_rif_testigo')";
            mysqli_query($conexion, $query);

            $query = "INSERT INTO Prefttcli (Cli_cedul) VALUES ('$cedula_testigo')";
            mysqli_query($conexion, $query);
            $codigo_cliente = mysqli_insert_id($conexion);

            // Insertar la dirección del testigo en Prefttdii
            $aldea_testigo = $testigo_data['aldea'];
            $calle_testigo = $testigo_data['calle'];
            $carrera_testigo = $testigo_data['carrera'];
            $ncasa_testigo = $testigo_data['ncasa'];
            $query = "INSERT INTO Prefttdii (Din_cedul, Din_aldea, Din_calle, Din_carre, Din_ncasa) 
                      VALUES ('$cedula_testigo', '$aldea_testigo', '$calle_testigo', '$carrera_testigo', '$ncasa_testigo')";
            mysqli_query($conexion, $query);
        }

        // Insertar en Prefttpbc
        $query = "INSERT INTO Prefttpbc (Pbc_clien, Pbc_rolcl, Pbc_buena) 
                  VALUES ('$codigo_cliente', '$rol_cliente', '$codigo_solicitud')";
        mysqli_query($conexion, $query);
    }

    // Datos del Testigo 1
    $testigo1_data = [
        'cedula' => $_POST['testigo1-cedula'],
        'nombre' => $_POST['testigo1-nombre'],
        'apellido' => $_POST['testigo1-apellido'],
        'telefono' => $_POST['testigo1-telefono'],
        'foto_cedula' => $_POST['testigo1-foto-cedula'],
        'foto_rif' => $_POST['testigo1-foto-rif'],
        'aldea' => $_POST['testigo1-aldea'],
        'calle' => $_POST['testigo1-calle'],
        'carrera' => $_POST['testigo1-carrera'],
        'ncasa' => $_POST['testigo1-casa']
    ];
    registrar_testigo($conexion, $codigo_usuario, $codigo_solicitud, 3, $testigo1_data);

    // Datos del Testigo 2
    $testigo2_data = [
        'cedula' => $_POST['testigo2-cedula'],
        'nombre' => $_POST['testigo2-nombre'],
        'apellido' => $_POST['testigo2-apellido'],
        'telefono' => $_POST['testigo2-telefono'],
        'foto_cedula' => $_POST['testigo2-foto-cedula'],
        'foto_rif' => $_POST['testigo2-foto-rif'],
        'aldea' => $_POST['testigo2-aldea'],
        'calle' => $_POST['testigo2-calle'],
        'carrera' => $_POST['testigo2-carrera'],
        'ncasa' => $_POST['testigo2-casa']
    ];
    registrar_testigo($conexion, $codigo_usuario, $codigo_solicitud, 3, $testigo2_data);

    // Insertar en Prefttpbc para el usuario actual
    $query = "INSERT INTO Prefttpbc (Pbc_clien, Pbc_rolcl, Pbc_buena) 
              VALUES ('$codigo_usuario', 5, '$codigo_solicitud')";
    mysqli_query($conexion, $query);

    // Redirigir después del registro
    header("Location: sesion_cliente.php#resume");
    exit();
}
?>
