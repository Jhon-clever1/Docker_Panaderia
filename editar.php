<?php

if(!isset($_GET["id"])) exit();
$id = $_GET["id"];
include_once "base_de_datos.php";
$sentencia = $base_de_datos->prepare("SELECT * FROM productos WHERE id = ?;");
$sentencia->execute([$id]);
$producto = $sentencia->fetch(PDO::FETCH_OBJ);
if($producto === FALSE){
	echo "¡No existe algún producto con ese ID!";
	exit();
}

?>
<?php include_once "encabezado.php" ?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');
    
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f8f9fa;
    }
    
    .container {
        max-width: 800px;
        margin: 30px auto;
        padding: 30px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    h1 {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 25px;
        text-align: center;
        font-size: 1.8rem;
    }
    
    .form-control {
        border-radius: 8px;
        padding: 12px 15px;
        border: 1px solid #ddd;
        transition: all 0.3s;
        margin-bottom: 10px;
    }
    
    .form-control:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52,152,219,.25);
    }
    
    label {
        font-weight: 500;
        color: #34495e;
        margin-top: 10px;
        display: block;
    }
    
    .btn {
        border-radius: 8px;
        padding: 12px 25px;
        font-weight: 500;
        letter-spacing: 0.5px;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }
    
    .btn-primary {
        background-color: #3498db;
        color: white;
        min-width: 150px;
    }
    
    .btn-primary:hover {
        background-color: #2980b9;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .btn-warning {
        background-color: #f39c12;
        color: white;
        min-width: 150px;
    }
    
    .btn-warning:hover {
        background-color: #d35400;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .buttons-container {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        margin-top: 25px;
    }
    
    .btn i {
        margin-right: 8px;
        font-size: 0.9rem;
    }
</style>

	<div class="container py-5 col-xs-12">

		<h1>Editar producto con el ID <?php echo $producto->id; ?></h1>

		<form method="post" action="guardarDatosEditados.php">
			<input type="hidden" name="id" value="<?php echo $producto->id; ?>">
	
			<label for="codigo">Código de barras:</label>
			<input value="<?php echo $producto->codigo ?>" class="form-control" name="codigo" required type="text" id="codigo" placeholder="Escribe el código">

			<label for="descripcion">Descripción:</label>
			<textarea required id="descripcion" name="descripcion" cols="30" rows="5" class="form-control"><?php echo $producto->descripcion ?></textarea>

			<label for="precioVenta">Precio de venta:</label>
			<input value="<?php echo $producto->precioVenta ?>" class="form-control" name="precioVenta" required type="number" id="precioVenta" placeholder="Precio de venta">

			<label for="existencia">Existencia:</label>
			<input value="<?php echo $producto->existencia ?>" class="form-control" name="existencia" required type="number" id="existencia" placeholder="Cantidad o existencia">

			<div class="buttons-container">
			<a class="btn btn-warning" href="./listar.php"><i class="fas fa-save"></i>Cancelar</a>
			<button class="btn btn-primary" type="submit"><i class="fas fa-save"></i>Guardar</button>
			</div>
		</form>
		
	</div>
<?php include_once "pie.php" ?>
