<?php
require_once("../../config/conexion.php");
require_once("../../models/Permisos.php");
Permisos::redirigirSiNoAutorizado("derivar");
?>

<!DOCTYPE html>
<html>
    <?php require_once("../MainHead/head.php") ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css">
    <link rel="stylesheet" href="./estilopersonaleventos.css">
    <script src="../../public/js/sweetaler2v11-11-0.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
     <!-- Agrega el script de DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
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
                            <h3>Eventos</h3>
                            <ol class="breadcrumb breadcrumb-simple">
                                <li><a href="../Home/">Inicio</a></li>
                                <li class="active">Control de eventos</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </header>
            
            <h5 class="m-t-lg with-border">Informaci&oacute;n Actual</h5>
            <div class="box-typical box-typical-padding table table-responsive-sm">

                <!-- tabla de asuntos inmediatos (Alto) -->
                <table id="tabla-control" class="table tabla-media tabla-basica table-bordered table-striped table-vcenter js-dataTable-js">
                    <thead>
                        <tr>
                            <th style="width:5%">ID</th>
                            <th style="width:15%">Direcci&oacute;n</th>
                            <th style="width:9.5%">Categor&iacute;a</th>
                            <th style="width:10%">Asignaci&oacute;n</th>
                            <th style="width:5%">Nivel peligro</th>
                            <th style="width:5%">Estado</th>
                            <th style="width:5%">Fecha y hora</th>
                            <th style="width:5%">Crtiticidad</th>
                            <th style="width:5%">Derivar</th>
                            <th style="width:5%">Chat</th>
                            
                        </tr>
                    </thead>
                    <tbody id="datos-criticos">
                        <!-- Datos de consulta -->
                    </tbody>
                    <tbody id="datos-medios">
                        <!-- Datos de consulta -->
                    </tbody>
                    <tbody id="datos-bajos">
                        <!-- Datos de consulta -->
                    </tbody>
                    <tbody id="datos-generales">
                        <!-- Datos de consulta -->
                    </tbody>
                </table>
            </div>

            
<div id="modal-mapa" class="modal-overlay">
    <div class="vista-mapa" id="map">
    </div>
    <button id='btn' type='button' class='btn btn-inline btn-primary btn-sm ladda-button btnCrearRuta' > Ir </button>
    <span class="glyphicon glyphicon-remove CerrarModalMap"></span>
</div>



        </div>
    </div><!--.page-content-->

    <?php require_once("../MainFooter/footer.php"); ?>

<?php

require_once('modalNivelPeligro.php');
require_once('modalDerivar.php');
require_once("../MainJs/js.php");
?>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAdCMoRAl_-ARUflpa4Jn_qUoOpdXlxQEg&libraries=places&v=3.55"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript" src="./nivelpeligro.js"></script>
<script type="text/javascript" src="./evento.js"></script>
<script type="text/javascript" src="./derivar.js"></script>
<?php
if (isset($_GET['id_evento'])) {
    $id_eventos = explode(',', $_GET['id_evento']);
    $id_eventos = array_map('intval', $id_eventos);

    $id_eventos_js = json_encode($id_eventos);

    echo "<script defer type='text/javascript'>
            window.onload = function() {
                const id_eventos = $id_eventos_js;

                // Ejecutar la función highlightRow cada 1 segundo (1000 milisegundos)
                setInterval(function() {
                    highlightRows(id_eventos);
                }, 1000);
          };
          </script>";
}
?>
<script defer type="text/javascript">
    function highlightRows(id_eventos) {
        const rows = document.querySelectorAll("td#id_evento_celda");

        rows.forEach(cell => {
            // Obtener el <tr> padre de la celda
            const row = cell.closest("tr");

            // Verificar si el id_evento de la celda está en la lista de id_eventos
            if (id_eventos.includes(parseInt(cell.textContent))) {
                row.classList.add("table-success");
            } else {
                row.classList.remove("table-success");
            }
        });
    }
</script>
</body>
</html>
