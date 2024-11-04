<?php
require_once("../config/conexion.php");
require_once("../models/Evento.php");
require_once("../models/Categoria.php");
require_once("../models/Unidad.php");
require_once("../models/Estado.php");
require_once("../models/EventoUnidad.php");
require_once("../models/RegistroLog.php");
require_once("../models/Noticia.php");
require_once("../models/Seccion.php");
require_once("../models/Permisos.php");

Permisos::redirigirSiNoAutorizado();


$evento = new Evento();
$categoria = new Categoria();
$unidad = new Unidad();
$estado = new Estado();
$derivado = new EventoUnidad();
$registroLog = new RegistroLog();
$noticia  = new Noticia();
$seccion = new Seccion();
header('Content-Type: application/json; charset=utf-8');
if (isset($_GET["op"])) {
    switch ($_GET["op"]) {

        case "agregar_derivado":
            $id_seccion = $_POST['unid_id'];
           $est =  $seccion->seccion_estado($id_seccion);
           if ($est == true){
                $datos = $derivado->add_eventoUnidad(
                $_POST['ev_id'],
                $_POST['unid_id']);
           }else {
                $datos = false;
               }
            if ($datos == true) {
             $seccion->seccion_ocupado($id_seccion);
             $usu_id = $_SESSION["usu_id"];
            $unidad_data = $unidad->get_seccion_unidad($id_seccion);
            $unidad_nom = $unidad_data[0]['unid_nom'];
            $ev_desc = "Se deriva a unidad: " . $unidad_nom;
            $evento->insert_emergencia_detalle($_POST["ev_id"], $usu_id, $ev_desc);
            $ags_noticia = [
              "asunto" => "Derivado",
              "mensaje" => $ev_desc,
              "id_evento"=>$_POST['ev_id'],
              "usuario"=>$_SESSION['usu_nom'],
              "unidad"=>$unidad_nom,
            ];
            $noticia->crear_y_enviar_noticia_para_derivados($ags_noticia);
             $resutado = ["status"=>"success","message"=>"se agrego la seccion"];
            } else {
             $resutado = ["status"=>"warning","message"=>"no se pudo hacer el cambio"];
            }
            echo json_encode($resultado);
            $registroLog->add_log_registro($_SESSION['usu_id'],$_GET['op'],"evento id:{$_POST['ev_id']} unid:{unid_id}");
            break;
        case "get_seccion_asignados_evento":
            if (!isset($_GET['ev_id']) || !is_numeric($_GET['ev_id'])) {
                echo json_encode(['status'=>'warning','message'=>'Falta el parametro ev_id']);
                break;
            }
            $evento_id = intval($_POST['ev_id']);
            $datos = $derivado->get_datos_eventoUnidad($evento_id);
            echo json_encode($datos);
            break;

        case "reporte_actualizacion" :
            $datos = $derivado->add_reporte_cambio_unidad(
                $_POST['ev_id'],
                $_POST['str_antiguo'],
                $_POST['str_nuevo'],
                $_POST['fec_cambio']
            );
            if ($datos == true) {
             $resutado = ["status"=>"success","message"=>"no se pudo hacer el cambio"];
            } else {
             $resutado = ["status"=>"warning","message"=>"no se pudo hacer el cambio"];
            }
            echo json_encode($resultado);
            $registroLog->add_log_registro($_SESSION['usu_id'],$_GET['op'],"Actualizar evento id:{$_POST['ev_id']} Nombre:{$_POST['str_antiguo']} a {$_POST['str_nuevo']}");
            break;
        case "delete_derivado":
            $datos = $derivado->delete_unidad($_POST['ev_id'],$_POST['sec_id']);
            if ($datos == true){
            $usu_id = $_SESSION["usu_id"];
            $id_seccion = $_POST['sec_id'];
            $unidad_data = $unidad->get_seccion_unidad($id_seccion);
            $unidad_nom = $unidad_data[0]['unid_nom'];
            $ev_desc = "Se ha eliminado la unidad: " . $unidad_nom;
            $seccion->seccion_disponible($id_seccion);
            $evento->insert_emergencia_detalle($_POST['ev_id'], $usu_id, $ev_desc);
            $resutado = ["status"=>"success","message"=>"se eliminado"];
            } else {
             $resutado = ["status"=>"warning","message"=>"no se pudo hacer el cambio"];
            }
            echo json_encode($resultado);
            $registroLog->add_log_registro($_SESSION['usu_id'],$_GET['op'],"Eliminar unidad:{$_POST['unid_id']} de evento id {$_POST['ev_id']}");
            break;


    }
}
