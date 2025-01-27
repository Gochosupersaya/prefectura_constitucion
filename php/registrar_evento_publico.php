<?php
session_start();
include('conexion.php'); // Asegúrate de tener el archivo de conexión correcto

// Obtener los datos del formulario
$tipo_evento = $_POST['evento-tipo'];
$motivo = $_POST['evento-motivo'];
$aldea = $_POST['evento-aldea'];
$calle = $_POST['evento-calle'];
$carrera = $_POST['evento-carrera'];
$lugar = $_POST['evento-lugar'];
$fecha_inicio = $_POST['evento-fecha-inicio'];
$hora_inicio = $_POST['evento-hora-inicio'];
$fecha_fin = $_POST['evento-fecha-fin'];
$hora_fin = $_POST['evento-hora-fin'];
$duracion = $_POST['evento-duracion'];
$asistencia = $_POST['evento-asistencia'];

// Obtener la cédula del usuario actual desde la sesión
$cedula_actual = $_SESSION['cedula'];

// Obtener el código del cliente a partir de la cédula
$query_cliente = "SELECT Cli_codig FROM prefttcli WHERE Cli_cedul = '$cedula_actual'";
$result_cliente = mysqli_query($conexion, $query_cliente);
$row_cliente = mysqli_fetch_assoc($result_cliente);
$codigo_cliente = $row_cliente['Cli_codig'];

if (!$codigo_cliente) {
    $_SESSION['error'] = "Error, el cliente no está registrado.";
    header("Location: sesion_cliente.php#resume");
    exit();
}

// Insertar los datos en la tabla Prefttsep
$query_insert = "INSERT INTO prefttsep (Sep_clien, Sep_tipoe, Sep_motiv, Sep_aldea, Sep_calle, Sep_carre, Sep_delug, Sep_finic, Sep_hinic, Sep_ffinl, Sep_hfinl, Sep_durac, Sep_asist, Sep_statu)
                 VALUES ('$codigo_cliente', '$tipo_evento', '$motivo', '$aldea', '$calle', '$carrera', '$lugar', '$fecha_inicio', '$hora_inicio', '$fecha_fin', '$hora_fin', '$duracion', '$asistencia', 'Enviada')";

if (mysqli_query($conexion, $query_insert)) {
    $_SESSION['success'] = "Evento público registrado con éxito.";
    header("Location: sesion_cliente.php#resume");
} else {
    $_SESSION['error'] = "Error al registrar el evento: " . mysqli_error($conexion);
    header("Location: sesion_cliente.php#resume");
}

mysqli_close($conexion);
?>
