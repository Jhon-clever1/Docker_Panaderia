<!DOCTYPE html>
<html>
<head>
	<title>Panaderia</title>

    <link rel="stylesheet" href="./css/fontawesome-all.min.css">
	<link rel="stylesheet" href="./css/2.css">
	<link rel="stylesheet" href="./css/estilo.css">

</head>
<body>
	<br><br><br>
	<div class= "container">
		<div class="row">
			<div class="col-sm-4"></div>
			<div class="col-md-4">
				<br><br>
				<div class="panel panel-default">
					<div  style="" class="panel panel-heading">
						<h4 style="color: #371608">Panadería Santa Rosa</h4> 
					</div>
					<div class="panel panel-body">
						<p class="text text-center">
							<img src="imagenes/logo.png" width="120px" height="130px">
						</p>
						<form action="procesos/login.php" method="POST">

							<label>Usuario</label>
							<input type="text" class="form-control input sm" name="usuario" id="usuario" placeholder="usuario">

							<label>Contraseña</label>
							<input type="password" name="password" id="password" class="form-control input sm" placeholder="contraseña">

							<p></p>

							<button class="btn btn-primary btn-block" type="submit">Entrar</button>

						</form>
					</div>
				</div>
			</div>
			<div class="col-sm-4"></div>	
		</div>

		<div class=" py-5">
			<br>
			<br>
			<br>
			<p style="text-align: center;">&copy 2020 Panadería Santa Rosa.Todos los derechos reservados </p>
		</div>
	</div>
</body>	
</html>

