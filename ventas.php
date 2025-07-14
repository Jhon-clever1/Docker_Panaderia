<?php 
$_SESSION['current_page'] = 'ventas.php';
include_once "encabezado.php" 
?>
<?php
include_once "base_de_datos.php";
$sentencia = $base_de_datos->query("SELECT ventas.total, ventas.fecha, ventas.id, GROUP_CONCAT(	productos.codigo, '..',  productos.descripcion, '..', productos_vendidos.cantidad SEPARATOR '__') AS productos FROM ventas INNER JOIN productos_vendidos ON productos_vendidos.id_venta = ventas.id INNER JOIN productos ON productos.id = productos_vendidos.id_producto GROUP BY ventas.id ORDER BY ventas.id;");
$ventas = $sentencia->fetchAll(PDO::FETCH_OBJ);

$usuario = $_SESSION['usuario'];

if(!isset($usuario)){
	header("location: index.php");
}else{
?>

<style>
    /* Estilos personalizados para ventas.php */
    .container {
        max-width: 95%;
        margin-top: 20px;
    }
    
    h1 {
        color: #000000d1;
        font-weight: 600;
        margin-bottom: 20px;
    }
    
    .table-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        padding: 20px;
        margin-top: 20px;
        overflow: hidden;
    }
    
    .main-table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        border-radius: 15px;
        overflow: hidden;
    }
    
    .main-table thead th {
        background-color: #4e73df;
        color: white;
        font-weight: 500;
        border: none;
        padding: 15px;
    }
    
    .main-table tbody td {
        padding: 15px;
        border-bottom: 1px solid #e0e0e0;
        vertical-align: top;
    }
    
    .main-table tbody tr:last-child td {
        border-bottom: none;
    }
    
    .main-table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .product-table {
        width: 100%;
        margin: 0;
        background-color: #f8f9fa;
        border-radius: 8px;
    }
    
    .product-table thead th {
        background-color: #5a5c69;
        color: white;
        font-size: 0.9em;
        padding: 8px 12px;
    }
    
    .product-table tbody td {
        padding: 8px 12px;
        font-size: 0.9em;
    }
    
    .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 10px 20px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border: none;
    }
    
    .btn-success {
        background-color: #1cc88a;
    }
    
    .btn-danger {
        background-color: #e74a3b;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    .btn i {
        margin-right: 8px;
    }
    
    .action-btn {
        padding: 8px 12px;
        min-width: 40px;
    }
    
    .currency-format {
        font-family: 'Courier New', monospace;
        font-weight: bold;
    }
    
    .date-format {
        white-space: nowrap;
    }
    
    .btn-group {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
    }
    .filtros-ventas {
        background: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .filtro-group {
        display: flex;
        gap: 15px;
        align-items: center;
        flex-wrap: wrap;
    }
    .filtro-group label {
        margin-bottom: 0;
        font-weight: 500;
    }
</style>

	<div class=" container py-5 col-xs-12">
		<h1>Ventas Realizadas</h1>
        <div class="filtros-ventas">
    <h3>Filtrar Ventas</h3>
    <form method="get" action="">
        <div class="filtro-group">
            <div>
                <label for="fecha_inicio">Desde:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" 
                       value="<?= isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-d') ?>" 
                       class="form-control">
            </div>
            <div>
                <label for="fecha_fin">Hasta:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" 
                       value="<?= isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d') ?>" 
                       class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Filtrar
            </button>
            <a href="ventas.php" class="btn btn-secondary">
                <i class="fas fa-sync-alt"></i> Limpiar
            </a>
            <a href="?periodo=hoy" class="btn btn-info">
                <i class="fas fa-calendar-day"></i> Hoy
            </a>
        </div>
    </form>
</div>

<?php
    // Modificar la consulta SQL para incluir filtros
    $where = "";
    $params = [];

    if(isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin'])) {
        $where = " WHERE DATE(ventas.fecha) BETWEEN ? AND ? ";
        $params = [$_GET['fecha_inicio'], $_GET['fecha_fin']];
    } elseif(isset($_GET['periodo']) && $_GET['periodo'] == 'hoy') {
        $where = " WHERE DATE(ventas.fecha) = ? ";
        $params = [date('Y-m-d')];
    }

    $sql = "SELECT ventas.total, ventas.fecha, ventas.id, 
            GROUP_CONCAT(productos.codigo, '..', productos.descripcion, '..', productos_vendidos.cantidad SEPARATOR '__') AS productos 
            FROM ventas 
            INNER JOIN productos_vendidos ON productos_vendidos.id_venta = ventas.id 
            INNER JOIN productos ON productos.id = productos_vendidos.id_producto 
            $where
            GROUP BY ventas.id 
            ORDER BY ventas.id DESC";

    $sentencia = $base_de_datos->prepare($sql);
    $sentencia->execute($params);
    $ventas = $sentencia->fetchAll(PDO::FETCH_OBJ);

    if(empty($ventas)) {
        echo '<div class="alert alert-info">No se encontraron ventas en el período seleccionado</div>';
    }
    
?>
		<div class="btn-group">
			<a class="btn btn-success" href="./vender.php"><i class="fa fa-plus"></i>Nueva Venta
        </a>
			<a class="btn btn-danger" href="./reportes/reporteVentas2.php"><i class="fa fa-list"></i>Reporte de venta
        </a>
		</div>
		
		<div class="table-container">
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
		</div>
	</div>
<?php include_once "pie.php" ?>
<?php } ?>