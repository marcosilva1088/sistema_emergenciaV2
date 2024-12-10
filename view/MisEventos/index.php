<?php
require_once("../../config/conexion.php");
require_once("../../models/Permisos.php");
require_once("../MainJs/js.php");
Permisos::redirigirSiNoAutorizado();
	?>
<!DOCTYPE html>
<html>
	<?php require_once("../MainHead/head.php") ?>
<link rel="stylesheet" href="estilopersonal.css">
<title>Sistema Emergencia</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css">
<script defer src="https://cdn.datatables.net/1.13.6/js/dataTables.js"></script>
<script defer src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.js"></script>
<script defer src="https://cdn.datatables.net/responsive/2.0.3/js/dataTables.responsive.min.js"></script>
<script defer src="https://cdn.datatables.net/responsive/2.0.3/js/responsive.dataTables.min.js"></script>
<script defer src="../../public/js/sweetaler2v11-11-0.js"></script>
<script defer type="text/javascript" src="miseventos.js"></script>
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
							<h3>Unidades Municipales</h3>
							<ol class="breadcrumb breadcrumb-simple">
								<li><a href="#">Registro</a></li>
								<li class="active">Unidades</li>
							</ol>
						</div>
					</div>
				</div>
			</header>

			<h5 class="m-t-lg with-border">Informaci&oacute;n de Unidades</h5>
      <div id="informacion_evento" class="row justify-content-center">
          <div class="spinner-border text-primary mt-5" role="status">
            <span class="sr-only">Cargando...</span>
          </div>
      </div>
			
        </div><!--.container-fluid-->
    </div><!--.page-content-->

	<?php require_once("../MainFooter/footer.php"); ?>
	
</body>

</html>
