<?php include_once "encabezado.php" 

?>

<div class="container py-5 col-xs-12">
	<h1>Agregar nuevo insumo y/o materia prima</h1>
	<form method="post" action="nuevoInsumo.php">
		<label for="nombre">Nombre:</label>
		<input class="form-control" name="nombre" required type="text" id="nombre" placeholder="Nombre de la materia prima o insumo">

		<label for="cantidad">Cantidad comprada:</label>
		<input required id="cantidad" name="cantidad" type="number" class="form-control"></input>

		<label for="unidad">Unidad de medida:</label>
		<input class="form-control" name="unidad" required type="text" id="unidad" placeholder="Ej: pieza, kilos, gramos, litros, etc.">

		<label for="totalCompra">Total de la compra:</label>
		<input class="form-control" name="totalCompra" required type="number" id="totalCompra" placeholder="Precio de compra">

		<label for="existencia">Existencia disponible:</label>
		<input class="form-control" name="existencia" required type="number" id="existencia" placeholder="existencia">

		<label for="fecha">Fecha de la Compra:</label>
		<input class="form-control" name="fecha" required type="date" id="fecha" placeholder="fecha de la compra">

		<br><br><input class="btn btn-info" type="submit" value="Guardar">
	</form>
</div>
<?php include_once "pie.php" ?>

