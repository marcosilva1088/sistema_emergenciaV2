<?php 
require_once __DIR__.'/../models/Usuario.php';
require_once __DIR__.'/../models/Correo.php';
require_once __DIR__.'/../models/Evento.php';
require_once __DIR__.'/../models/Formato.php';

class Noticia extends Conectar {

  public function add_noticia(string $asunto, string $mensaje, string $url=null){
    $sql = "INSERT INTO tm_noticia (asunto, mensaje, url) VALUES (:asunto,:mensaje,:url)";
    $params = [
      ':asunto'=> $asunto,
      ':mensaje'=> $mensaje,
      ':url'=> $url,
    ];
    $result = $this->ejecutarAccion($sql, $params);
    if ($result){
        return [
            'status' => 'success',
            'message' => "Se agregó la noticia"
        ];
    }
    return [
        'status' => 'warning',
        'message' => "Problemas al agregar dato"
    ];
  }

  /**
   * Crea una noticia y la envía a un grupo de usuarios basado en el asunto.
   *
   * @param array $argsNoticia Un array asociativo con los siguientes elementos:
   *  - 'asunto': (string) El asunto de la noticia.
   *  - 'mensaje': (string) El mensaje de la noticia.
   *  - 'url': (string|null) La URL asociada con la noticia (opcional).
   * 
   * @return array Un array con el estado y mensaje del proceso, junto con los resultados de las operaciones.
   */
  public function crear_noticia_y_enviar_grupo_usuario(array $agrsNoticia){
    $formato = $this->formato_noticia_correo_segun_asunto($agrsNoticia);
    $asunto = $formato->asunto;
    // WARNING: los emojis pueden causar errores al guardar en base de datos
    $asunto = str_replace("🚨", " ", $asunto);
    $asunto = str_replace("📎", " ", $asunto);
    $mensaje = $agrsNoticia["mensaje"];
    $url = $agrsNoticia["url"];
    $add_noticia = $this->add_noticia($asunto,$mensaje,$url);
    if ($add_noticia["status"] !== "success"){
      return ["status"=>"error", "message"=>"Error al agregar", "add_noticia"=>$add_noticia];
    }
    $lista_usuario = $this->usuarios_a_enviar_segun_regla($agrsNoticia["asunto"]);
    $ultima_noticia = $this->obtenerUltimoRegistro('tm_noticia',"noticia_id");
    $id_noticia_new = $ultima_noticia["noticia_id"];
    $envio_usuario = $this->enviar_noticia_grupal_por_lista_usuario($id_noticia_new,$lista_usuario);
    $correo = new Correo('', $formato->asunto, $formato->mensaje);
    $correo->agregarEncabezado('Content-Type', 'text/html; charset=utf-8');
    $correo->setGrupoDestinatario($lista_usuario);
    $resultado_correo = $correo->enviar();

    if ($envio_usuario["status"] !== "success"){
      return [
        "status"=>"error",
        "message"=>"Error al enviar",
        "add_noticia"=>$add_noticia,
        "enviar_grupo"=>$envio_usuario,
      ];
    }
    return [
      "status"=>"success",
      "message"=>"Crear y enviar Terminado",
      "add_noticia"=>$add_noticia,
      "enviar_grupo"=>$envio_usuario,
      "enviar_correo"=>$resultado_correo,
    ];
  }
  public function crear_y_enviar_noticia_para_derivados(array $argsNoticia){
    $id_evento = $argsNoticia["id_evento"];
    $formato = $this->formato_noticia_correo_segun_asunto($argsNoticia);
    $usuario = new Usuario();
    $lista_usuarios = $usuario->get_usuario_derivados_por_evento($id_evento);
    if (empty($lista_usuarios)){
      return "No hay usuarios asociados a este evento";
    }
    $correo = new Correo('', $formato->asunto, $formato->mensaje);
    $correo->setGrupoDestinatario($lista_usuarios);
    $correo->agregarEncabezado('Content-Type', 'text/html; charset=utf-8');
    $resultado_correo = $correo->enviar();
    return $resultado_correo;
  }

  private function formato_noticia_correo_segun_asunto(array $argsNoticia) {
    $formato = new Formato();
    $evento = new Evento();
    $asunto = $argsNoticia["asunto"];
    if ($asunto === "Nuevo Evento"){
      $id_evento = $evento->get_id_ultimo_evento();
      $datos_evento = $evento->informacion_evento_completa($id_evento);
      $formato->setCuerpoNuevoEvento($datos_evento);
      return $formato;
    }elseif( $asunto === "Evento Cerrado"){
      $id_evento = $argsNoticia["id_evento"];
      $datos_evento = $evento->get_evento_motivo_cierre($id_evento);
      $formato->SetCuerpoCierreEvento($datos_evento);
      return $formato;
    }elseif ($asunto === "Derivado"){
      $id_evento = $argsNoticia["id_evento"];
      $datos_evento = $argsNoticia;
      $formato->setCuerpoDerivadoAgregado($datos_evento);
      return $formato;
    }elseif ($asunto === "Detalle Evento"){
      $id_evento = $argsNoticia["id_evento"];
      $datos_evento = $argsNoticia;
      $formato->setCuerpoActualizarEvento($datos_evento);
      return $formato;
    }elseif ($asunto === "Eliminar Derivado"){
      $id_evento = $argsNoticia["id_evento"];
      $datos_evento = $argsNoticia;
      $formato->setCuerpoDerivadorEliminado($datos_evento);
      return $formato;
    }
    $formato->setAsunto($asunto);
    $formato->setMensaje($argsNoticia["mensaje"]);
    return $formato;
  }

 public function enviar_noticia_grupal_por_lista_usuario(int $noticia, array $lista_usuario) {
    $sql = "INSERT INTO tm_noticia_usuario(usu_id,noticia_id) VALUES ";
    $valores_usuario = $this->preparar_consulta_por_lista_usuario($lista_usuario,$noticia);
    $sql .= $valores_usuario;
    // !FIX: puede ver error por sin dato en valores_usuario
    try {
        $result = $this->ejecutarAccion($sql);
        if ($result) {
            return array('status' => 'success', 'message' => "Se envió la noticia");
        } else {
            return array('status' => 'warning', 'message' => "Problemas al agregar dato");
        }
    } catch (PDOException $e) {
        return array('status' => 'error', 'message' => "Error en la base de datos: " . $e->getMessage());
    } catch (Exception $e) {
        return array('status' => 'error', 'message' => "Error: " . $e->getMessage());
    }
}
  public function enviar_noticia_simple(int $noticia_id, int $usuario_id){

    $sql = "INSERT INTO tm_noticia_usuario(usu_id,noticia_id) VALUES (:usuario_id,:noticia_id)";
    $params = [":usuario_id"=> $usuario_id, ":noticia_id"=>$noticia_id];
    $result = $this->ejecutarAccion($sql,$params);
    if($result){
      return ["status"=>"success","message"=>"se envio correo al usuario"];
    }
    return ["status"=>"warning", "message"=>"No se pudo enviar mensaje"];

  }
  public function crear_y_enviar_noticia_simple($data) {
    $asunto = $data['asunto'];
    $mensaje = $data['mensaje'];
    $url = $data['url'];
    $usuario_id = $data['usuario_id'];
    $news = $this->add_noticia($asunto,$mensaje,$url);
    if($news["status"] !== "success"){
      return ["status"=>"error"];
    }
    $news_last = $this->obtenerUltimoRegistro("tm_noticia","noticia_id");

    $noticia_simple = $this->enviar_noticia_simple($news_last["noticia_id"],$usuario_id);

    return $noticia_simple;
  }
  public  function preparar_consulta_por_lista_usuario($list_usuarios,$id_noticia){
    $consulta = "";
    foreach($list_usuarios as $usuario){
        $id_usuario = $usuario["usu_id"];
        $consulta .= "($id_usuario,$id_noticia),";
    }
    if (!empty($consulta)){
      $consulta = substr($consulta, 0, -1).';';
    }
    return $consulta;
  }
  public function check_mensaje_leido($noticia_id, $usuario_id){
    $fecha_lectura = date('Y-m-d H:i:s');
    $sql = "UPDATE tm_noticia_usuario SET leido=1, fecha_lectura=:fecha_lectura WHERE usu_id=:usuario_id and noticia_id=:noticia_id";
    $params = [
      ":usuario_id" => $usuario_id,
      ":noticia_id" => $noticia_id,
      ":fecha_lectura"=> $fecha_lectura,
    ];
    $result = $this->ejecutarAccion($sql,$params);
    if ($result){
      return array( 'status'=>'succes', 'message'=>"Se marca mensaje como leido");
    }
    return array('status'=>'error', 'message'=>"problemas al actualizar estado del mensaje noticia" );
  }
  public function get_noticias_usuario($usuario_id){
    $sql = "SELECT  nti.asunto AS 'asunto',
                    nti.mensaje AS 'mensaje',
                    ns.leido AS 'leido',
                    nti.noticia_id AS 'id',
                    ns.fecha_lectura AS 'fecha_leido',
                    coalesce(nti.url,'#') AS 'url'
            FROM tm_noticia_usuario as ns
            JOIN tm_noticia as nti
            ON (nti.noticia_id=ns.noticia_id)
    WHERE usu_id=:usuario_id
    ORDER BY nti.noticia_id DESC";
    $params = [":usuario_id"=>$usuario_id];
    $result = $this->ejecutarConsulta($sql,$params);
    if ($result){
      return $result;
    }
    if ($result == null){
      return [];
    }
  }

  public function lista_posibles_envios_por_ids(string $ids, string $id_name){
    // WARNING: no se puede usar el ejecutarConsulta porque agrega comillas o 
    // termina utilizando los numeros como un string
    $conectar = parent::conexion();
    parent::set_names();
    $idsArray = explode(',', $ids);
    $placeholders = implode(',', array_fill(0, count($idsArray), '?'));
    $sql = "SELECT * FROM tm_usuario WHERE $id_name IN ($placeholders);";
    $consulta = $conectar->prepare($sql);
    $consulta->execute($idsArray);
    return $consulta->fetchAll(PDO::FETCH_ASSOC);
}

public function eliminarDuplicadosPorUsuId(...$listas) {
    $todosLosElementos = [];

    foreach ($listas as $lista) {
        $todosLosElementos = array_merge($todosLosElementos, $lista);
    }
    
    // Utilizar un array asociativo para eliminar duplicados basados en usu_id
    $usuariosUnicos = [];
    
    foreach ($todosLosElementos as $elemento) {
        $usuariosUnicos[$elemento['usu_id']] = $elemento;
    }
    return array_values($usuariosUnicos);
}
 public function get_regla_envio_por_asunto(string $asunto = null) {
    $sql = "SELECT * FROM tm_regla_envio WHERE asunto = :asunto;";
    $params = [":asunto"=>$asunto];
    return $this->ejecutarConsulta($sql,$params,false);
 }

 public function get_reglas() {
    $sql = "SELECT * FROM tm_regla_envio;";
    return $this->ejecutarConsulta($sql);
 }
public function usuarios_a_enviar_segun_regla(string $asunto) {
    $list_regla = $this->get_regla_envio_por_asunto($asunto);

    $filtro = [
      "tipo_usuario" => [
        "id_name" =>"usu_tipo",
        "value"=> isset($list_regla["tipo_usuario"]) ? $list_regla["tipo_usuario"] : null,
      ],
      "usuario" =>[
        "id_name" =>"usu_id",
        "value"=> isset($list_regla["usuario"]) ? $list_regla["usuario"] : null,
      ],
      "seccion" =>[
        "id_name" =>"usu_seccion",
        "value"=> isset($list_regla["seccion"]) ? $list_regla["seccion"] : null,
      ],
      "unidad" =>[
        "id_name" =>"usu_unidad",
        "value"=> isset($list_regla["unidad"]) ? $list_regla["unidad"] : null,
      ]
    ];

    // Si el valor es null, asigna un array vacío
    $tipo_usuario = $filtro["tipo_usuario"]["value"] !== null
        ? $this->lista_posibles_envios_por_ids($filtro["tipo_usuario"]["value"], $filtro["tipo_usuario"]["id_name"])
        : [];

    $usuario = $filtro["usuario"]["value"] !== null
        ? $this->lista_posibles_envios_por_ids($filtro["usuario"]["value"], $filtro["usuario"]["id_name"])
        : [];

    $unidad = $filtro["unidad"]["value"] !== null
        ? $this->lista_posibles_envios_por_ids($filtro["unidad"]["value"], $filtro["unidad"]["id_name"])
        : [];

    $seccion = $filtro["seccion"]["value"] !== null
        ? $this->lista_posibles_envios_por_ids($filtro["seccion"]["value"], $filtro["seccion"]["id_name"])
        : [];

    return $this->eliminarDuplicadosPorUsuId($tipo_usuario,$usuario,$unidad,$seccion);
  }
  public function update_regla_envio(array $args) {
    $sql = "UPDATE tm_regla_envio
            SET unidad= :unidad , seccion= :seccion,usuario= :usuario ,tipo_usuario= :tipo_usuario WHERE id_regla= :id_regla";
    $params = [
      ":seccion"=> $args["seccion"],
      ":usuario"=> $args["usuario"],
      ":tipo_usuario"=> $args["tipo_usuario"],
      ":unidad"=> $args["unidad"],
      ":id_regla"=> $args["id_regla"],
    ];
    $result = $this->ejecutarAccion($sql,$params);
    if ($result){
        return ["status"=>"success", "message"=>"actualizacion exitosa"];
    } 
    return ["status"=>"warning", "message"=>"no se hizo ningun cambio"];
  }
}
