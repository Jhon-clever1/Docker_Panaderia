<?php
include_once "encabezado.php";
include_once "base_de_datos.php";

// Consulta para obtener todas las ventas
$sentencia = $base_de_datos->query("SELECT ventas.total, ventas.fecha, ventas.id, 
       GROUP_CONCAT(productos.codigo, '..', productos.descripcion, '..', productos_vendidos.cantidad SEPARATOR '__') AS productos 
       FROM ventas 
       INNER JOIN productos_vendidos ON productos_vendidos.id_venta = ventas.id 
       INNER JOIN productos ON productos.id = productos_vendidos.id_producto 
       GROUP BY ventas.id 
       ORDER BY ventas.fecha DESC");

$ventas = $sentencia->fetchAll(PDO::FETCH_OBJ);
?>

<div class="container">
    <h1>Reporte Completo de Ventas</h1>
    
    <div class="filtro-container">
        <form method="get" action="">
            <div class="form-row">
                <div class="col-md-4">
                    <label for="fecha_inicio">Desde:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="fecha_fin">Hasta:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" class="form-control">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <table class="main table">
			<thead>
				<tr>
					<th>Número</th>
					<th>Fecha</th>
					<th>Productos vendidos</th>
					<th>Total</th>

                    <?php if (esAdministrador()): ?>
					<th>Acciones</th>
                    <?php endif; ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach($ventas as $venta){ ?>
				<tr>
					<td><?php echo $venta->id ?></td>
					<td class="date-format"><?php echo date('d/m/Y H:i', strtotime($venta->fecha)) ?></td>
					<td>
						<table class="product-table">
							<thead>
								<tr>
									<th>Código</th>
									<th>Descripción</th>
									<th>Cantidad</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach(explode("__", $venta->productos) as $productosConcatenados){ 
								$producto = explode("..", $productosConcatenados)
								?>
								<tr>
									<td><?php echo $producto[0] ?></td>
									<td><?php echo $producto[1] ?></td>
									<td><?php echo $producto[2] ?></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</td>
					<td class="currency-format"><?php echo "S/".number_format($venta->total, 2) ?></td>
                    <?php if (esAdministrador()): ?>
					<td><a class="btn btn-danger action-btn" href="<?php echo "eliminarVenta.php?id=" . $venta->id?>"><i class="fa fa-trash"></i>Eliminar</a></td>
                    <?php endif; ?>
				</tr>
				<?php } ?>
			</tbody>
	</table>
    
    <a href="ventas.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver a ventas del día
    </a>
</div>

<?php include_once "pie.php"; ?>