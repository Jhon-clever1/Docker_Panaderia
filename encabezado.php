<?php
	ob_start();
	session_start();
	include_once "control_acceso.php";
	$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Santa Rosa</title>
	
	<link rel="stylesheet" href="./css/fontawesome-all.min.css">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">

	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
	
	<style>
		/* Estilos personalizados */
		body {
			font-family: 'Poppins', sans-serif;
			background-color: #fafaf8ff;
			background-image: url('imagenes/Fondo1.jpg');
			background-size: cover; 
			background-position: center;
			background-repeat: no-repeat;
			background-attachment: fixed;
		}
		
		.navbar {
			border-radius: 15px;
			box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
			margin: 15px auto;
			max-width: 95%;
		}
		
		.nav-link {
			font-weight: 500;
			color: #333 !important;
			padding: 8px 15px !important;
			margin: 0 5px;
			border-radius: 8px;
			transition: all 0.3s ease;
		}
		
		.nav-link:hover {
			background-color: #b65d09d1;
			color: white !important;
			transform: translateY(-2px);
		} 
		.nav-item.active{
			position: relative;
		}
		.nav-item.active .nav-link {
			background-color: #b65d09d1;
			color: white !important;
			transform: translateY(-2px);
		}
		.nav-item.active .nav-link:after {
			content: '';
			position: absolute;
			bottom: -5px;
			left: 50%;
			transform: translateX(-50%);
			width: 70%;
			height: 3px;
			background: white;
			border-radius: 3px;
		}
		.dropdown-menu {
			border-radius: 10px;
			border: none;
			box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
		}
		
		.dropdown-item {
			padding: 8px 15px;
			font-weight: 500;
		}
		
		.dropdown-item:hover {
			background-color: #b65d09d1;
			color: white;
			border-radius: 6px;
		}
		
		.navbar-toggler {
			border: none;
			outline: none;
		}
	</style>

</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-light bg-light ">
	
		<div class="container">
			<img src="./imagenes/Pan.jpg" width="70" height="70" class="d-inline-block align-top" alt="" style="border-radius: 50%;">
			
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item <?php echo $current_page == 'dashboard.php' ? 'active' : '' ?>">
    					<a class="nav-link" href="./dashboard.php"><i class="fas fa-tachometer-alt"></i> Inicio</a>
					</li>
					<li class="nav-item <?php echo $current_page == 'listar.php' ? 'active' : '' ?>">
						<a class="nav-link" href="./listar.php">Productos <span class="sr-only">(current)</span></a>
					</li>

					<li class="nav-item <?php echo $current_page == 'insumos.php' ? 'active' : '' ?>">
						<a class="nav-link" href="./insumos.php">Insumos <span class="sr-only">(current)</span></a>
					</li>

					<li class="nav-item <?php echo $current_page == 'vender.php' ? 'active' : '' ?>">
						<a class="nav-link" href="./vender.php">Vender</a>
					</li>
					
					<li class="nav-item <?php echo $current_page == 'ventas.php' ? 'active' : '' ?>">
						<a class="nav-link " href="./ventas.php">Ventas realizadas</a>
					</li>
					<li class="nav-item <?php echo $current_page == 'reporte_productos_vendidos.php' ? 'active' : '' ?>">
    					<a class="nav-link" href="./reporte_productos_vendidos.php"><i class="fas fa-star"></i> Top Ventas</a>
					</li>
				</ul>

				<ul class="nav navbar-nav navbar-right">
    				<li class="nav-item dropdown">
        				<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            				<?= htmlspecialchars($_SESSION['nombre'] ?? 'Usuario') ?>
            				<?php if(isset($_SESSION['rol']) && $_SESSION['rol'] == 'administrador'): ?>
                				<span class="badge badge-danger" style="background-color: #d35400; color: white; border-radius: 4px; padding: 3px 6px; font-size: 0.75em;">Admin</span>
            				<?php endif; ?>
        				</a>
        				<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
            				<?php if(esAdministrador()): ?>
                				<a class="dropdown-item" href="gestion_usuarios.php">
                    				<i class="fas fa-users-cog"></i> Gestión de Usuarios
                				</a>
                				<div class="dropdown-divider"></div>
            				<?php endif; ?>
            				<a class="dropdown-item" href="./salir.php">
                				<i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            				</a>
        				</div>
    				</li>
				</ul>
			</div>
		</div>	
	</nav>
	