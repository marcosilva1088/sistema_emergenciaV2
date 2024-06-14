<?php
require_once("../config/conexion.php");
require_once("../models/Usuario.php");
require_once("../models/SeguridadPassword.php");
require_once("../models/RegistroLog.php");
$usuario = new Usuario();
$RegistroLog= new RegistroLog();

if (isset($_GET["op"])) {
    switch ($_GET["op"]) {
        case "add_usuario":
            $datos = $usuario->add_usuario($_POST['usu_nom'],
            $_POST['usu_ape'],
            $_POST['usu_correo'],
            $_POST['usu_name'],
            $_POST['usu_pass'],
            $_POST['fecha_crea'],
            $_POST['estado'],
            $_POST['usu_tipo']);
            if ($datos == true) {
                $seguridadPassword = new  SeguridadPassword();
                $seguridadPassword->add_password_info($_POST['usu_correo'],$_POST['usu_name'], $_POST['usu_pass']);
                echo 1;
                $RegistroLog->add_log_registro($_SESSION["usu_id"],$_GET['op'],"el usuario {$_SESSION['usu_nom']} crea al usuario {$_POST['usu_name']}");
            } else {
                echo 0;
                $RegistroLog->add_log_registro($_SESSION["usu_id"],$_GET['op'],"el usuario {$_SESSION['usu_nom']} intenta crear usuario: {$_POST['usu_name']} operaccion fallida");
            }
            break;

        case "get_tipo":

            $usu_tipo = $_POST['#usu_tipo'];

            $datos = $usuario->get_tipo($usu_tipo);

            if (count($datos) > 0){
                $resultado = $datos["usu_tipo"];
                echo $resultado;
            }else{
                echo false;
            }
        break;
        
        case "info-personal":
            // Verificar si se recibió el ID del usuario en la solicitud
           
                // Obtener el ID del usuario desde la session
                $usu_id = $_SESSION['usu_id'];

                // Obtener los datos de contacto del usuario
                $datos = $usuario->get_datos_contacto($usu_id);
        
                // Verificar si se encontraron datos de contacto
                if (count($datos) > 0) {
                    // Imprimir los datos antes de devolverlos
                    //var_dump($datos);
                    // Devolver los datos como JSON
                    echo json_encode($datos);
                } else {
                    echo false;
                }
           
        break;
            
            
        
        case "get_todos_usuarios":
            $datos = $usuario->get_todos_usuarios();

            if (is_array($datos) == true and count($datos) > 0) {
                $html = "";
                foreach ($datos as $row) {
                    $html .= "<option value=" . $row['usu_tipo_id'] . ">" . $row['usu_tipo_nom'] . "</option>";
                }
                echo $html;
            }
        break;

        case "update_password":
            // Recibir los datos POST
            $old_pass = $_POST['old_pass'];
            $new_pass = $_POST['new_pass'];
            $usu_id = $_SESSION['usu_id'];
            // Llamar a la función update_password
            $datos = $usuario->update_password($old_pass, $new_pass, $usu_id);
            // Verificar el resultado y enviar la respuesta
            if ($datos['status'] == 'success') {
                $seguridadPassword = new SeguridadPassword();
                $seguridadPassword->update_password_info($usu_id, $new_pass);
                $RegistroLog->add_log_registro($_SESSION["usu_id"],$_GET['op'],"el usuario {$_SESSION['usu_nom']} cambio su contraseña");
                echo json_encode($datos);
            } else {
                $RegistroLog->add_log_registro($_SESSION["usu_id"],$_GET['op'],"el usuario {$_SESSION['usu_nom']} intento cambiar contraseña");
                echo json_encode($datos);
            }
        break;
        case "update_phone":
            $new_phone = $_POST['new_phone'];
            $usu_id = $_SESSION['usu_id'];
            $datos = $usuario->update_phone($new_phone, $usu_id);
            if ($datos['status'] == 'success') {
                $RegistroLog->add_log_registro($_SESSION["usu_id"],$_GET['op'],"el usuario {$_SESSION['usu_nom']} cambió su número de teléfono");
                echo json_encode($datos);
            } else {
                $RegistroLog->add_log_registro($_SESSION["usu_id"],$_GET['op'],"el usuario {$_SESSION['usu_nom']} intentó cambiar su número de teléfono");
                echo json_encode($datos);
            }
        break;
        case "get_info_usuario":
           $data = $usuario->get_info_usuario($_SESSION['usu_id']);
           echo json_encode($data);
        break;
        case "get_full_info_usuario":
           $data = $usuario->get_full_usuarios($_SESSION['usu_id']);
           echo json_encode($data);
        break;

    }
}
