<?php
require_once("../../config/conexion.php");

if (isset($_SESSION["usu_id"])) {
?>

<!DOCTYPE html>
<html>
<?php require_once("../MainHead/head.php") ?>

<link rel="stylesheet" href="./estilopersonal.css">
<title>Sistema Emergencia</title>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAQrYCFSz7Q-a-WONxo4yymu9SAPgmaA6c&libraries=places&callback=initMap"></script>
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

            <h5 class="m-t-lg with-border">Ingresar información</h5>

            <div class="row">
                 <div class="col-lg-6 col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Nombre</h6>
                            <p class="card-text"><?php echo $_SESSION['usu_nom']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Apellido</h6>
                            <p class="card-text"><?php echo $_SESSION['usu_ape']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Teléfono</h6>
                            <p class="card-text"><?php echo $_SESSION['usu_telefono']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Dirección de correo electrónico</h6>
                            <p class="card-text"><?php echo $_SESSION['usu_correo']; ?></p>
                        </div>
                    </div>
                </div>
                <form class="event-box" action="nuevoevento.js" method="post" id="event_form" enctype="multipart/form-data">
                    <div class="group-emergencia">
                        <div class="col-lg-5">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="exampleInput">Seleccione una Categoría</label>
                                <select id="cat_id" class="form-control">
                                    <!-- Datos recopilados desde función get_categoria -->
                                </select>
                            </fieldset>
                        </div>                                           
                        <div class="col-lg-5">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="descripcion">Ingrese una breve descripción</label>
                                <input type="text" class="form-control" id="descripcion" placeholder="Descripción">
                            </fieldset>
                        </div>     
                    <div class="col-lg-5">
                        <fieldset class="form-group">
                            <label class="form-label semibold" for="formFile">Desea adjuntar una imagen de la emergencia?</label>
                            <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                            <small id="archivoAdjuntado" class="form-text text-muted">No hay archivo adjunto (.JPG/.JPEG/.PNG)</small>
                        </fieldset>
                    </div>
                </div>

                    <div class="group-ident">
                        <div class="col-lg-5" id="select-ubicacion">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="exampleInput">Ingresa una dirección manualmente y adicionalmente<span class="label-red"> (marque la emeregencia en el mapa)</span> </label>
                                <select id="elegir-ubicacion" class="form-control">
                                    <option value="direccion-escrita">Seleccionar </option>
                                    <option value="direccion-escrita">Escribir la ubicación</option>
                                    <option value="ubicacion-content">Geolocalización</option>
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-lg-5" id="direccion-escrita" style="display:none;">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="Address">Ingrese Dirección</label>
                                <input type="text" class="form-control" id="address" placeholder="Dirección">
                            </fieldset>
                        </div>
                        <div class="ubicacion-content" id="direccion-geolocalizacion" style="display:none;">
                            <!-- Contenido relacionado con la geolocalización -->
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
                                <!-- Aquí se muestra el mapa utilizando la API de Google Maps -->
                            </div>
                <input type="hidden" id="ev_latitud"> 
                <input type="hidden" id="ev_longitud"> 
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
</html>
<?php
} else {
    header("Location:" . Conectar::ruta() . "index.php");
}
?>
