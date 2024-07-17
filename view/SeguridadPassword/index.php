<?php
require_once("../../config/conexion.php");
require_once("../../models/SeguridadPassword.php");

if (isset($_SESSION["usu_id"])) {
?>

<!DOCTYPE html>
<html>
<?php require_once("../MainHead/head.php") ?>
<title>Sistema Emergencia</title>
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.css" />
<link rel="stylesheet" href="./StyleSeguridadPassword.css">
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.js" defer></script>
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.js" defer></script>
<script defer type="text/javascript" src="./searchpass.js"></script>
</head>

<body class="with-side-menu">

    <?php require_once("../MainHeader/header.php"); ?>

    <div class="mobile-menu-left-overlay"></div>

    <?php require_once("../MainNav/nav.php"); ?>

    <div class="page-content">
        <div class="container-fluid">
            <div class="box-typical box-typical-padding table-responsive-sm" id="tabla-password">
                    <div class="input-group rounded">
                    </div> <!-- .input-group -->
                    
                    
<table id="table-data" class="table table-bordered responsive  table-vcenter js-dataTable-js"> 
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Correo</th>
            <th>estado</th>
            <th>Unidad</th>
            <th>Dias antiguedad</th>
            <th>Dias limite</th>
            <th>Detalle</th>
        </tr>
    </thead>
    <tbody>
        <!-- Aquí se agregarán las filas de la tabla con JavaScript -->
    </tbody>
</table>
            </div> <!-- .box-typical -->


        </div><!--.container-fluid-->
    </div><!--.page-content-->
        
        <?php require_once("../MainFooter/footer.php"); ?>

</body>
        
    <?php
       require_once("../MainJs/js.php"); 
      
    }else{
        header("Location:". Conectar::ruta () ."index.php");
    }
    
    ?>
 

</html>
