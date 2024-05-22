<?php
require_once("../../config/conexion.php");

// if (isset($_SESSION["usu_id"])) {
if (isset($_SESSION["usu_id"])) {
?>

<!DOCTYPE html>
<html>
<?php require_once("../MainHead/head.php") ?>
<link rel="stylesheet" href="./estilopersonal.css">
<title>Sistema Emergencia</title>
</head>

<body class="with-side-menu">

    <?php require_once("../MainHeader/header.php"); ?>

    <div class="mobile-menu-left-overlay"></div>

    <?php require_once("../MainNav/nav.php"); ?>

    <div class="page-content">
        <div class="container-fluid">
            <header class="section-header">
                <div class="tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <h3>Nuevo Evento</h3>
                            <ol class="breadcrumb breadcrumb-simple">
                                <li><a href="#">Home</a></li>
                                <li class="active">Nuevo Evento</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </header>

            <h5 class="m-t-lg with-border">Ingresar informaci&oacute;n</h5>

            <div class="row">
                <form class="event-box" action="nuevoevento.js" method="post" id="event_form" enctype="multipart/form-data">

                    <div class="group-info-personal">   
                        <div class="col-lg-5">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="nombre">Nombre</label>
                                <input type="text" class="form-control" id="nombre" placeholder="Nombre" value="<?php echo $_SESSION['usu_nom']; ?>" readonly>
                            </fieldset>
                        </div>
                        <div class="col-lg-5">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="apellido">Apellido</label>
                                <input type="text" class="form-control" id="apellido" placeholder="Apellido" value="<?php echo $_SESSION['usu_ape']; ?>" readonly>
                            </fieldset>
                        </div>
                        <div class="col-lg-5">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="telefono">Teléfono</label>
                                <input type="text" class="form-control" id="telefono" placeholder="Ingrese su N° telefónico" value="<?php echo $_SESSION['usu_telefono']; ?>" readonly>
                            </fieldset>
                        </div>
                        <div class="col-lg-5">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="mail">Dirección de correo electrónico</label>
                                <input type="email" class="form-control" id="correo" placeholder="Ingrese su email" value="<?php echo $_SESSION['usu_correo']; ?>" readonly>
                            </fieldset>
                        </div>
                    </div>

                    <div class="group-emergencia">
                        <div class="col-lg-5">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="exampleInput">Seleccione una Categor&iacute;a</label>
                                <select id="cat_id" class="form-control" >
                                <!-- Datos recopilados desde funcion get_categoria -->
                                </select>
                            </fieldset>
                        </div>                                           
                        <div class="col-lg-5">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="descripcion">Ingrese una breve descripci&oacute;n </label>
                                <input type="text" class="form-control" id="descripcion" placeholder="Descripci&oacute;n">
                            </fieldset>
                        </div>     
                        <div class="col-lg-5">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="exampleInput">Desea Adjuntar una imagen de la emergencia? </label>
                                <input type="file" id="imagen" name="imagen" accept="image/*">
                                <button id="btnCargarArchivo" class="btn btn-round btn-inline btn-primary">Cargar Archivo</button>
                                <label id="archivoAdjuntado">No hay archivo adjunto (.JPG/.JPEG/.PNG)</label>
                            </fieldset>
                    </div>
                                            
                    </div>                  

                    <div class="group-ident">
                        <div class="col-lg-5" id="select-ubicacion">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="exampleInput">Ubicación de la emergencia</label>
                                <select id="elegir-ubicacion" class="form-control">
                                    <option value="direccion-escrita">Elija una opción</option>
                                    <option value="direccion-escrita">Escribir la ubicación</option>
                                    <option value="ubicacion-content">Geolocalización</option>
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-lg-5" id="direccion-escrita" style="display:none;">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="Address">Ingrese Dirección </label>
                                <input type="text" class="form-control" id="address" placeholder="Dirección">
                            </fieldset>
                        </div>
                        <div class="ubicacion-content" id="direccion-geolocalizacion" style="display:none;">
                            <!-- Agregamos el checklist para permitir o no permitir la ubicación -->
                            <div class="col-lg-5">
                                <fieldset class="form-group">
                                    <label class="form-label semibold">Utilizar Ubicación</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ubicacion" id="permitirActual" value="permitirActual">
                                        <label class="form-check-label" for="permitirActual">Ubicación Actual</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ubicacion" id="permitirUbicacion" value="permitir">
                                        <label class="form-check-label" for="permitirUbicacion">Marcador</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ubicacion" id="noPermitirUbicacion" value="noPermitir" checked>
                                        <label class="form-check-label" for="noPermitirUbicacion">No utilizar</label>
                                    </div>
                                </fieldset>
                            </div>
                            <div id="map">
                                <!-- API DE GOOGLE MAPS -->
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-10">
                        <button type="button" class="btn btn-round btn-inline btn-primary" id="btnGuardar">AGREGAR NUEVA EMERGENCIA</button>
                    </div>   

                </form>               
            </div>
        </div><!--.container-fluid-->
    </div><!--.page-content-->
     
    <?php require_once("../MainFooter/footer.php"); ?>
    <?php require_once("../MainJs/js.php"); ?>
    <script type="text/javascript" src="./nuevoevento.js"></script>
    
</body>
        
    <?php
       require_once("../MainJs/js.php"); 
       
    }else{
        header("Location:". Conectar::ruta () ."index.php");
    }
    
    ?>
   
</html>
