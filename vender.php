<?php
$_SESSION['current_page'] = 'vender.php';
include_once "encabezado.php";
if(!isset($_SESSION["carrito"])) $_SESSION["carrito"] = [];
$granTotal = 0;

$usuario = $_SESSION['usuario'];

if(!isset($usuario)){
	header("location: index.php");
}else{
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
$(function() {
    // Autocompletado
    $("#busqueda").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "buscar_productos.php",
                dataType: "json",
                data: {
                    term: request.term,
                    tipo: $("#metodo_busqueda").val()
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        minLength: 2,
        select: function(event, ui) {
            // Al seleccionar un producto, enviar el formulario
            if(ui.item) {
                $("#busqueda").val(ui.item.value);
                $("form").submit();
            }
            return false;
        }
    }).data("ui-autocomplete")._renderItem = function(ul, item) {
        return $("<li>")
            .append("<div>" + item.label + " <small>(" + item.codigo + ")</small><br>" +
                   "<small>Precio: S/" + item.precio + " | Stock: " + item.stock + "</small></div>")
            .appendTo(ul);
    };

    // Cambiar tipo de búsqueda
    $("#metodo_busqueda").change(function() {
        $("#tipo_busqueda").val($(this).val());
    });
});
</script>

<style>
    /* Estilos personalizados para vender.php */
    .container {
        max-width: 95%;
        margin-top: 20px;
    }
    
    h1 {
        color: #000000ff;
        font-weight: 600;
        margin-bottom: 20px;
    }
    
    h3 {
        color: #000000d1;
        font-weight: 600;
        margin: 20px 0;
    }
    
    .table-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        padding: 20px;
        margin: 20px 0;
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
    
    .btn-warning {
        background-color: #f6c23e;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        opacity: 0.9;
    }
    
    .btn i {
        margin-right: 8px;
    }
    
    .action-btns .btn {
        padding: 8px 12px;
    }
    
    .currency-format {
        font-family: 'Courier New', monospace;
        font-weight: bold;
    }
    
    .form-control {
        border-radius: 8px;
        padding: 12px 15px;
        border: 1px solid #ddd;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    
    .alert {
        border-radius: 8px;
        padding: 15px 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .total-container {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin: 20px 0;
        text-align: right;
    }
    
    .btn-group {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 20px;
    }

    .search-form {
        background: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .form-row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -15px;
    }
    .col-md-6, .col-md-4, .col-md-2 {
        position: relative;
        width: 100%;
        padding-right: 15px;
        padding-left: 15px;
    }
    @media (min-width: 768px) {
        .col-md-6 { flex: 0 0 50%; max-width: 50%; }
        .col-md-4 { flex: 0 0 33.333333%; max-width: 33.333333%; }
        .col-md-2 { flex: 0 0 16.666667%; max-width: 16.666667%; }
    }
    .d-flex { display: flex; }
    .align-items-end { align-items: flex-end; }
    .btn-block { display: block; width: 100%; }

</style>

	<div class="container py-5 col-xs-12">
		<h1>Vender</h1>
		<?php
			if(isset($_GET["status"])){
				if($_GET["status"] === "1"){
					?>
						<div class="alert alert-success">
							<strong>¡Correcto!</strong> Venta realizada correctamente
						</div>
					<?php
				}else if($_GET["status"] === "2"){
					?>
					<div class="alert alert-info">
							<strong>Venta cancelada</strong>
						</div>
					<?php
				}else if($_GET["status"] === "3"){
					?>
					<div class="alert alert-info">
							<strong>Ok</strong> Producto quitado de la lista
						</div>
					<?php
				}else if($_GET["status"] === "4"){
					?>
					<div class="alert alert-warning">
							<strong>Error:</strong> El producto que buscas no existe
						</div>
					<?php
				}else if($_GET["status"] === "5"){
					?>
					<div class="alert alert-danger">
							<strong>Error: </strong>El producto está agotado
						</div>
                    <?php
                }else if($_GET["status"] === "6"){
                    ?>
                    <div class="alert alert-danger">
                            <strong>Error: </strong>Debes agregar al menos un producto para realizar la venta
                    </div>
					<?php
				}else{
					?>
					<div class="alert alert-danger">
							<strong>Error:</strong> Algo salió mal mientras se realizaba la venta
						</div>
					<?php
				}
			}
		?>
		<br>
		<form method="post" action="agregarAlCarrito.php" class="search-form">
            <div class="form-row">
                <div class="col-md-6">
                    <label for="busqueda">Buscar por código o nombre:</label>
                    <input autocomplete="off" autofocus class="form-control" name="busqueda" required type="text" id="busqueda" 
                        placeholder="Escribe código o nombre del producto">
                    <input type="hidden" name="tipo_busqueda" id="tipo_busqueda" value="auto">
                </div>
                <div class="col-md-4">
                    <label for="metodo_busqueda">Método de búsqueda:</label>
                    <select class="form-control" id="metodo_busqueda">
                        <option value="auto">Autodetección</option>
                        <option value="codigo">Código de barras</option>
                        <option value="nombre">Nombre del producto</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i> Agregar
                    </button>
                </div>
            </div>
        </form>
		
		<div class="table-container">
		<table class="table">
			<thead>
				<tr>
					<th>ID</th>
					<th>Código</th>
					<th>Descripción</th>
					<th>Precio de venta</th>
					<th>Cantidad</th>
					<th>Total</th>
					<th>Accion</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($_SESSION["carrito"] as $indice => $producto){ 
						$granTotal += $producto->total;
					?>
				<tr>
					<td><?php echo $producto->id ?></td>
					<td><?php echo $producto->codigo ?></td>
					<td><?php echo $producto->descripcion ?></td>
					<td class="currency-format"><?php echo "S/".number_format($producto->precioVenta, 2) ?></td>
					<td><?php echo $producto->cantidad ?></td>
					<td class="currency-format"><?php echo "S/".number_format($producto->total, 2) ?></td>
					<td class="action-btns">
						<a class="btn btn-danger" href="<?php echo "quitarDelCarrito.php?indice=" . $indice?>"><i class="fa fa-trash"></i>Quitar</a></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		</div>
		<div class="total-container">
		<h3>Total: <span class="currency-format"><?php echo "S/".number_format($granTotal, 2); ?></span></h3>
		<div class="btn-group">
		<form action="./terminarVenta.php" method="POST">
			<input name="total" type="hidden" value="<?php echo $granTotal;?>">
			<button type="submit" class="btn btn-success">
				<i class="fa fa-check-circle"></i>Realizar venta
			</button>
		</form>
		<div>
		<a href="./cancelarVenta.php" class="btn btn-danger">
			<i class="fa fa-times-circle"></i>Cancelar venta</a>
		</div>
		</div>
		</div>
	</div>
<?php include_once "pie.php" ?>

<?php } ?>