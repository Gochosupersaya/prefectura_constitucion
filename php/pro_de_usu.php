<?php
session_start();
include('conexion.php'); // Asegúrate de tener el archivo de conexión correcto

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $cedula_denunciado = $_POST['cedulad'];
    $nombre_denunciado = $_POST['nombres'];
    $apellido_denunciado = $_POST['apellidos'];
    $telefono_denunciado = $_POST['telefono'];
    $aldea = $_POST['aldea'];
    $calle = $_POST['calle'];
    $carrera = $_POST['carrera'];
    $num_casa = $_POST['num_casa'];
    $tipo_denuncia = $_POST['tipo_denuncia'];
    $descripcion = $_POST['descripcion'];

    // Verificar si el denunciante se está denunciando a sí mismo
    if ($cedula_denunciado == $_SESSION['cedula']) {
        $_SESSION['error'] = "Error, usted no puede denunciarse a sí mismo";
        header("Location: sesion_cliente.php#about");
        exit();
    }

    // Iniciar transacción
    $conexion->begin_transaction();

    try {
        // Verificar si el denunciado ya existe en Preftmper
        $sql_check_denunciado = $conexion->prepare("SELECT Cli_codig FROM prefttcli WHERE Cli_cedul = ?");
        $sql_check_denunciado->bind_param("s", $cedula_denunciado);
        $sql_check_denunciado->execute();
        $result_check_denunciado = $sql_check_denunciado->get_result();

        if ($result_check_denunciado->num_rows > 0) {
            $row_denunciado = $result_check_denunciado->fetch_assoc();
            $codigo_cliente_denunciado = $row_denunciado['Cli_codig'];
        } else {
            // Insertar datos del denunciado en Preftmper
            $sql_insert_denunciado = $conexion->prepare("INSERT INTO preftmper (Per_cedul, Per_nombr, Per_apell, Per_telef) VALUES (?, ?, ?, ?)");
            $sql_insert_denunciado->bind_param("ssss", $cedula_denunciado, $nombre_denunciado, $apellido_denunciado, $telefono_denunciado);
            $sql_insert_denunciado->execute();

            // Insertar datos del denunciado en Prefttcli
            $sql_insert_cliente = $conexion->prepare("INSERT INTO prefttcli (Cli_cedul) VALUES (?)");
            $sql_insert_cliente->bind_param("s", $cedula_denunciado);
            $sql_insert_cliente->execute();

            // Obtener el código del cliente insertado (denunciado)
            $codigo_cliente_denunciado = $conexion->insert_id;

            // Insertar dirección del denunciado en Prefttdii
            $sql_insert_direccion = $conexion->prepare("INSERT INTO prefttdii (Din_cedul, Din_aldea, Din_calle, Din_carre, Din_ncasa) VALUES (?, ?, ?, ?, ?)");
            $sql_insert_direccion->bind_param("sisss", $cedula_denunciado, $aldea, $calle, $carrera, $num_casa);
            $sql_insert_direccion->execute();
        }

        // Verificar si el denunciante ya existe en Prefttcli
        $sql_check_cliente_actual = $conexion->prepare("SELECT Cli_codig FROM prefttcli WHERE Cli_cedul = ?");
        $sql_check_cliente_actual->bind_param("s", $_SESSION['cedula']);
        $sql_check_cliente_actual->execute();
        $result_check_cliente_actual = $sql_check_cliente_actual->get_result();

        if ($result_check_cliente_actual->num_rows > 0) {
            $row_cliente_actual = $result_check_cliente_actual->fetch_assoc();
            $codigo_cliente_actual = $row_cliente_actual['Cli_codig'];
        } else {
            // Si el cliente no existe, inserta sus datos en Preftmper y Prefttcli
            $sql_insert_cliente_actual = $conexion->prepare("INSERT INTO preftmper (Per_cedul, Per_nombr, Per_apell) VALUES (?, ?, ?)");
            $sql_insert_cliente_actual->bind_param("sss", $_SESSION['cedula'], $_SESSION['nombre'], $_SESSION['apellido']);
            $sql_insert_cliente_actual->execute();

            $sql_insert_cliente_actual = $conexion->prepare("INSERT INTO prefttcli (Cli_cedul) VALUES (?)");
            $sql_insert_cliente_actual->bind_param("s", $_SESSION['cedula']);
            $sql_insert_cliente_actual->execute();

            $codigo_cliente_actual = $conexion->insert_id;
        }

        // Insertar denuncia en Prefttden
        $den_statu = "Enviada";
        $sql_insert_denuncia = $conexion->prepare("INSERT INTO prefttden (Den_tipod, Den_motiv, Den_statu, Den_fecha) VALUES (?, ?, ?, NOW())");
        $sql_insert_denuncia->bind_param("iss", $tipo_denuncia, $descripcion, $den_statu);
        $sql_insert_denuncia->execute();

        // Obtener el código de la denuncia insertada
        $codigo_denuncia = $conexion->insert_id;

        // Insertar detalles de la denuncia para el denunciante en Prefttdtd
        $rol_denunciante = 1; // Rol 1 para denunciante
        $sql_insert_detalle_denunciante = $conexion->prepare("INSERT INTO prefttdtd (Dtd_denun, Dtd_clien, Dtd_rolde) VALUES (?, ?, ?)");
        $sql_insert_detalle_denunciante->bind_param("iii", $codigo_denuncia, $codigo_cliente_actual, $rol_denunciante);
        $sql_insert_detalle_denunciante->execute();

        // Insertar detalles de la denuncia para el denunciado en Prefttdtd
        $rol_denunciado = 2; // Rol 2 para denunciado
        $sql_insert_detalle_denunciado = $conexion->prepare("INSERT INTO prefttdtd (Dtd_denun, Dtd_clien, Dtd_rolde) VALUES (?, ?, ?)");
        $sql_insert_detalle_denunciado->bind_param("iii", $codigo_denuncia, $codigo_cliente_denunciado, $rol_denunciado);
        $sql_insert_detalle_denunciado->execute();

        // Confirmar la transacción
        $conexion->commit();

        $_SESSION['success'] = "Denuncia realizada con éxito.";
        header("Location: sesion_cliente.php#about");
        exit();
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conexion->rollback();
        $_SESSION['error'] = "Error al realizar la denuncia: " . $e->getMessage();
        header("Location: sesion_cliente.php#about");
        exit();
    }

    // Cerrar conexión
    $conexion->close();
}
?>
