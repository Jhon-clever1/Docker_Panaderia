<?php include_once "encabezado.php" ?>

<?php
include_once "control_acceso.php";
include_once "base_de_datos.php";
$sentencia = $base_de_datos->query("SELECT * FROM productos;");
$productos = $sentencia->fetchAll(PDO::FETCH_OBJ);

$usuario = $_SESSION['usuario'];

if(!isset($usuario)){
	header("location: index.php");
}else{

?>
	<style>
    /* Estilos adicionales para la página de listado */
    .container {
        max-width: 95%;
        margin-top: 20px;
    }
    
    h1 {
        color: #000000ff;
        font-weight: 600;
        margin-bottom: 20px;
    }
    
    .table-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        padding: 20px;
        margin-top: 20px;
    }
    
    .table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        border-radius: 15px;
        overflow: hidden;
    }
    
    .table thead th {
        background-color: #b65d09d1;
        color: white;
        font-weight: 500;
        border: none;
        padding: 15px;
    }
    
    .table tbody td {
        padding: 12px 15px;
        border-bottom: 1px solid #e0e0e0;
        vertical-align: middle;
    }
    
    .table tbody tr:last-child td {
        border-bottom: none;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 8px 15px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .btn-success {
        background-color: #1cc88a;
        border-color: #1cc88a;
    }
    
    .btn-danger {
        background-color: #e74a3b;
        border-color: #e74a3b;
    }
    
    .btn-warning {
        background-color: #f6c23e;
        border-color: #f6c23e;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    .btn i {
        margin-right: 5px;
    }
    
    .action-btns .btn {
        padding: 6px 10px;
        min-width: 40px;
    }
	</style>

	<div class="container py-5">
	
		<div class="col-xs-12">
			<h1>Productos</h1>
			<div class="mb-4">
				<a class="btn btn-success" href="./formulario.php">
					<i class="fa fa-plus"></i>Nuevo Producto</a>
			
				<a class="btn btn-danger" href="./reportes/reporteProductos.php">
					<i class="fa fa-list"></i>Reporte productos</a>
			</div>
			
            <?php
                // Verificar stock bajo y mostrar alertas
                $stockBajo = [];
                foreach($productos as $producto) {
                    if($producto->existencia <= 5) { // Umbral de 5 unidades
                        $stockBajo[] = $producto;
                    }
                }

                if(!empty($stockBajo)) {
                    echo '<div class="alert alert-warning" style="margin-bottom: 20px;">';
                    echo '<h4><i class="fas fa-exclamation-triangle"></i> Alerta de Stock Bajo</h4>';
                    echo '<p>Los siguientes productos tienen bajo stock:</p>';
                    echo '<ul>';
                    foreach($stockBajo as $producto) {
                        echo '<li><strong>'.$producto->descripcion.'</strong> - '.$producto->existencia.' unidades restantes</li>';
                    }
                    echo '</ul>';
                    echo '</div>';
                }
            ?>
			<div class="table-container">
				<table class="table">
				<thead>
					<tr>
						<th>ID</th>
						<th>Código</th>
						<th>Descripción</th>
						<th>Precio de venta</th>
						<th>Existencia</th>
                        <?php if (esAdministrador()): ?>
						<th class="action-btns" colspan="2">Acciones</th>
                        <?php endif; ?>
                    </tr>
				</thead>
				<tbody>
					<?php foreach($productos as $producto){ ?>
					<tr>
						<td><?php echo $producto->id ?></td>
						<td><?php echo $producto->codigo ?></td>
						<td><?php echo $producto->descripcion ?></td>
						<td><?php echo "S/". number_format($producto->precioVenta, 2)?></td>
						<td><?php echo $producto->existencia ?></td>
                        <?php if (esAdministrador()): ?>
						<td>
                                <a href="editar.php?id=<?php echo $producto->id ?>" class="btn btn-warning btn-sm">
                                    <i class="fa fa-edit"></i>Editar
                                </a>
                                <a href="eliminar.php?id=<?php echo $producto->id ?>" class="btn btn-danger btn-sm">
                                    <i class="fa fa-trash"></i>Eliminar
                                </a>
                        </td>
                        <?php endif; ?>
					</tr>
					<?php } ?>
				</tbody>
				</table>
			</div>
		</div>
	</div>

<?php include_once "pie.php" ?>

<?php } ?>