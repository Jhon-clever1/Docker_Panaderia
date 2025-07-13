<!DOCTYPE html>
<html>
<head>
	<title>Panaderia</title>

    <link rel="stylesheet" href="./css/fontawesome-all.min.css">
	<link rel="stylesheet" href="./css/2.css">
	<link rel="stylesheet" href="./css/estilo.css">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

	<style>
		body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
			background-image: url('imagenes/Fondo1.jpg');
			background-size: cover; 
			background-position: center;
			background-repeat: no-repeat;
			background-attachment: fixed;
        }
		.panel-default {
            border-radius: 20px !important;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: none;
			background-color: rgba(225, 222, 219, 0.69);
        }
		.panel-heading {
            background-color: #141313ff;
            color: black !important;
            border-radius: 20px 20px 0 0 !important;
            padding: 20px;
            text-align: center;
            font-weight: 700;
            font-size: 1.5rem;
            border: none;
        }
		.panel-body {
            border-radius: 0 0 20px 20px !important;
            padding: 30px !important;
            background-color: white;
        }
        .login-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 30px;
        }
        .logo-side {
            flex: 1;
            text-align: center;
        }
        .logo-side img {
            width: 250px;
            height: auto;
            border-radius: 15px;
        }
        .login-form {
            flex: 1;
        }
		.form-control {
            border-radius: 10px !important;
            padding: 12px 15px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            font-family: 'Poppins', sans-serif;
        }
		.btn-primary {
            border-radius: 10px !important;
            padding: 12px;
            font-weight: 600;
            background-color: #371608;
            border: none;
            transition: all 0.3s ease;
        }
		.btn-primary:hover {
            background-color: #4d2a0f;
            transform: translateY(-2px);
        }
		label {
            font-weight: 600;
            color: #555;
        }
		h4 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
	<br><br><br>
	<div class= "container">
		<div class="row">
			<div class="col-sm-2"></div>
			<div class="col-md-8">
				<br><br>
				<div class="panel panel-default">
					<div  class="panel panel-heading">
						<h4>Panadería Santa Rosa</h4> 
					</div>
					<div class="panel panel-body login-container">
						<div class="logo-side">
							<img src="imagenes/Pan.jpg" alt="Logo Panaderia">
						</div>
						<div class="login-form">
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
			</div>
			<div class="col-sm-2"></div>	
		</div>

		<div class=" py-5">
			<br>
			<br>
			<br>
			<p style="text-align: center;color: white; font-family:'Poppins', sans-ser">&copy 2020 Panadería Santa Rosa.Todos los derechos reservados </p>
		</div>
	</div>
</body>	
</html>

