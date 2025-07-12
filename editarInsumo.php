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
			
			<br><br><input class="btn btn-info" type="submit" value="Guardar">
			<a class="btn btn-warning" href="./insumos.php">Cancelar</a>

		</form>
	</div>
<?php include_once "pie.php" ?>
