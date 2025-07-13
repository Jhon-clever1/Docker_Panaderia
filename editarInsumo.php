<?php

if(!isset($_GET["id"])) exit();
$id = $_GET["id"];
include_once "base_de_datos.php";
$sentencia = $base_de_datos->prepare("SELECT * FROM insumo WHERE id = ?;");
$sentencia->execute([$id]);
$insumo = $sentencia->fetch(PDO::FETCH_OBJ);
if($insumo === FALSE){
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
    
    .form-control[readonly] {
        background-color: #f5f5f5;
        color: #6c757d;
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

		<h1>Editar insumo con el ID <?php echo $insumo->id; ?></h1>
		<form method="post" action="guardarDatosEditados2.php">

			<input type="hidden" name="id" value="<?php echo $insumo->id; ?>">

			<label for="nombre">Nombre:</label>
			<input value="<?php echo $insumo->nombre_Insumo ?>"class="form-control" name="nombre" required type="text" id="nombre" placeholder="Nombre de la materia prima o insumo" readonly>

			<label for="cantidad">Cantidad comprada:</label>
			<input value="<?php echo $insumo->cantidadComprada ?>" required id="cantidad" name="cantidad" type="number" class="form-control" readonly></input>

			<label for="unidad">Unidad de medida:</label>
			<input value="<?php echo $insumo->unidadMedida ?>" class="form-control" name="unidad" required type="text" id="unidad" placeholder="Ej: pieza, kilos, gramos, litros, etc."readonly >

			<label for="totalCompra">Total de la compra:</label>
			<input value="<?php echo $insumo->total_Compra ?>" class="form-control" name="totalCompra" required type="number" id="totalCompra" placeholder="Precio de compra" readonly>

			<label for="existencia">Existencia disponible:</label>
			<input value="<?php echo $insumo->existencia ?>" class="form-control" name="existencia" required type="number" id="existencia" placeholder="existencia">

			<label for="fecha">Fecha de la Compra:</label>
			<input value="<?php echo $insumo->fecha_compra ?>" class="form-control" name="fecha" required type="date" id="fecha" placeholder="fecha de la compra" readonly>
			
			<div class="buttons-container ">
			<a class="btn btn-warning" href="./insumos.php"><i class="fas fa-times"></i>Cancelar</a>
			<button class="btn btn-info" type="submit"><i class="fas fa-save"></i>Guardar</button>
			</div>

		</form>
	</div>
<?php include_once "pie.php" ?>
