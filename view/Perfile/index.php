<?php
require_once("../../config/conexion.php");
require_once("../MainJs/js.php");
if (isset($_SESSION["usu_id"]) && ($_SESSION["usu_tipo"] == 1 || $_SESSION["usu_tipo"] == 2)) {
	
	?>
<!DOCTYPE html>
<html>
	<?php require_once("../MainHead/head.php") ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css">
<link rel="stylesheet" href="./nivelCateogiraStyle.css">
<title>Sistema Emergencia</title>
<script defer type="text/javascript" src="./perfile.js"></script>
<script src="../../public/js/sweetaler2v11-11-0.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
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
							<h3>Instituciones de Emergencias</h3>
							<ol class="breadcrumb breadcrumb-simple">
								<li><a href="#">Registro</a></li>
								<li class="active">Instituciones</li>
							</ol>
						</div>
					</div>
				</div>
			</header>

			<h5 class="m-t-lg with-border">Informaci&oacute;n de Instituciones de Emergencias</h5>
    <div class="container">
<h1>Cambiar Contrase&ntilde;a</h1>
        <form id="updatePasswordForm">
            <div class="form-group">
                <label for="old_pass">Contraseña antigua:</label>
                <input type="password" class="form-control" id="old_pass" name="old_pass" required>
            </div>

            <div class="form-group">
                <label for="new_pass">Nueva contraseña:</label>
                <input type="password" class="form-control" id="new_pass" name="new_pass" required>
                <small id="passwordHelp" class="form-text text-muted">Debe tener al menos 8 caracteres.</small>
            </div>

            <div class="form-group">
                <label for="confirm_new_pass">Confirmar nueva contraseña:</label>
                <input type="password" class="form-control" id="confirm_new_pass" name="confirm_new_pass" required>
                <small id="confirmHelp" class="form-text"></small>
            </div>

            <input type="hidden" id="usu_id" name="usu_id" value="1"> <!-- Reemplaza el valor con el ID del usuario -->

            <button type="submit" class="btn btn-primary">Cambiar contraseña</button>
        </form>
    </div><!--.container -->
        </div><!--.container-fluid-->
    </div><!--.page-content-->

	<?php require_once("../MainFooter/footer.php"); ?>
	
</body>
	<script>
		document.getElementById('show-hide-sidebar-toggle').addEventListener('click', function(e) {
			e.preventDefault();
			
			var body = document.body;
			
			if (!body.classList.contains('sidebar-hidden')) {
				body.classList.add('sidebar-hidden');
			} else {
				body.classList.remove('sidebar-hidden');
			}
		});
	</script>

<?php

}else{
	header("location:".Conectar::ruta()."index.php");
}
?>
</html>
