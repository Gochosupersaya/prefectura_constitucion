<?php
session_start();
include('conexion.php');
header('Content-Type: application/json'); // Aseguramos que la respuesta sea JSON

$response = ["status" => "error", "message" => "Error desconocido"]; // Mensaje por defecto

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedula = $_POST['cedula'];
    $contrasena = $_POST['contrasena'];

    $sql = $conexion->prepare("SELECT Usu_cedul, Usu_contr FROM Prefttusu WHERE Usu_cedul = ?");
    $sql->bind_param("i", $cedula);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hash_password = $row['Usu_contr'];

        if ($contrasena === $hash_password) {
            $_SESSION['cedula'] = $cedula;

            // Verificar rol y redirigir en función del tipo de usuario
            $sqlEmpleado = $conexion->prepare("SELECT Emp_rolpe FROM Prefttemp WHERE Emp_cedul = ?");
            $sqlEmpleado->bind_param("i", $cedula);
            $sqlEmpleado->execute();
            $resultEmpleado = $sqlEmpleado->get_result();

            if ($resultEmpleado->num_rows > 0) {
                $rowEmpleado = $resultEmpleado->fetch_assoc();
                $rol = $rowEmpleado['Emp_rolpe'];

                if ($rol == 1) {
                    $response = ["status" => "success", "redirect" => "sesion_admin.php"];
                } else if ($rol == 2) {
                    $response = ["status" => "success", "redirect" => "sesion_empleado.php"];
                }
            } else {
                $sqlCliente = $conexion->prepare("SELECT Cli_codig FROM Prefttcli WHERE Cli_cedul = ?");
                $sqlCliente->bind_param("i", $cedula);
                $sqlCliente->execute();
                $resultCliente = $sqlCliente->get_result();

                if ($resultCliente->num_rows > 0) {
                    $response = ["status" => "success", "redirect" => "sesion_cliente.php"];
                } else {
                    $response = ["status" => "error", "message" => "Usuario no encontrado"];
                }
            }
        } else {
            $response = ["status" => "error", "message" => "Contraseña incorrecta"];
        }
    } else {
        $response = ["status" => "error", "message" => "Usuario no encontrado"];
    }

    $sql->close();
    $conexion->close();
}

echo json_encode($response);
?>
