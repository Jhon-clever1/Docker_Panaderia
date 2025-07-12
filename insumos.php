<?php include_once "encabezado.php" ?>

<?php
    include_once "base_de_datos.php";
    $sentencia = $base_de_datos->query("SELECT * FROM insumo;");
    $insumo = $sentencia->fetchAll(PDO::FETCH_OBJ);

    $usuario = $_SESSION['usuario'];

    if(!isset($usuario)){
        header("location: index.php");
    }else{
?>

	<div class="container py-5">
	
		<div class="col-xs-12">
			<h1>Insumos y/o Materia prima</h1>
			<div>
				<a class="btn btn-success" href="./formularioInsumo.php">Agregar <i class="fa fa-plus"></i></a>
			</div>
			<br>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Nombre</th>
						<th>Cantidad comprada</th>
						<th>Unidad de Medida</th>
						<th>Total pagado</th>
						<th>Existencia</th>
                        <th>Fecha compra</th>
						<th>Editar</th>
						<th>Eliminar</th>
					</tr>
				</thead>
				<tbody>

					<?php foreach($insumo as $insumos){ ?>
					<tr>
						<td><?php echo $insumos->nombre_Insumo ?></td>
						<td><?php echo $insumos->cantidadComprada ?></td>
						<td><?php echo $insumos->unidadMedida ?></td>
						<td><?php echo "$".$insumos->total_Compra ?></td>
						<td><?php echo $insumos->existencia ?></td>
						<td><?php echo $insumos->fecha_compra ?></td>
						<td><a class="btn btn-warning" href="<?php echo "editarInsumo.php?id=" . $insumos->id?>"><i class="fa fa-edit"></i></a></td>
						<td><a class="btn btn-danger" href="<?php echo "eliminarInsumo.php?id=" . $insumos->id?>"><i class="fa fa-trash"></i></a></td>
					</tr>
					<?php } ?>

				</tbody>
			</table>
		</div>
	</div>

<?php include_once "pie.php" ?>

<?php } ?>