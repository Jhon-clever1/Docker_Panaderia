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
            if(ui.item) {
                agregarProducto(ui.item.id, $("#cantidad").val());
            }
            return false;
        }
    }).data("ui-autocomplete")._renderItem = function(ul, item) {
        return $("<li>")
            .append("<div>" + item.label + " <small>(" + item.codigo + ")</small><br>" +
                   "<small>Precio: S/" + item.precio + " | Stock: " + item.stock + "</small></div>")
            .appendTo(ul);
    };

    // Manejar Enter en el campo de búsqueda
    $("#busqueda").keypress(function(e) {
        if(e.which == 13) {
            e.preventDefault();
            if($("#busqueda").val().length >= 2) {
                buscarYAgregar();
            }
        }
    });

    // Manejar clic en el botón buscar
    $("#btn-buscar").click(function() {
        if($("#busqueda").val().length >= 2) {
            buscarYAgregar();
        }
    });

    function buscarYAgregar() {
        $.ajax({
            url: "buscar_productos.php",
            dataType: "json",
            data: {
                term: $("#busqueda").val(),
                tipo: $("#metodo_busqueda").val()
            },
            success: function(data) {
                if(data.length > 0) {
                    agregarProducto(data[0].id, $("#cantidad").val());
                } else {
                    alert("No se encontraron productos");
                }
            }
        });
    }

    function agregarProducto(idProducto, cantidad) {
        if(cantidad < 1) {
            alert("La cantidad debe ser al menos 1");
            return;
        }
        
        $.ajax({
            url: "agregarAlCarrito.php",
            method: "POST",
            data: {
                busqueda: idProducto,
                tipo_busqueda: "id",
                cantidad: cantidad
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                alert("Error: " + xhr.responseText);
            }
        });
    }
});
</script>

<script>
function obtenerRecomendaciones() {
    // Obtener IDs de productos en el carrito
    let productosEnCarrito = [];
    $(".producto-en-carrito").each(function() {
        productosEnCarrito.push($(this).data('id'));
    });

    // Si no hay productos, mostrar mensaje
    if(productosEnCarrito.length === 0) {
        $("#recomendaciones-container").html(`
            <div class="carrito-vacio">
                <i class="fas fa-shopping-basket"></i>
                <p>Agrega productos al carrito para ver recomendaciones</p>
            </div>
        `);
        return;
    }

    // Mostrar loader mientras carga
    $("#recomendaciones-container").html(`
        <div class="text-center py-3">
            <i class="fas fa-spinner fa-spin"></i> Buscando recomendaciones...
        </div>
    `);

    // Llamar al servidor para obtener recomendaciones
    $.ajax({
        url: "obtener_recomendaciones.php",
        method: "POST",
        dataType: 'html',
        data: {
            productos: JSON.stringify(productosEnCarrito)
        },
        success: function(response) {
            $("#recomendaciones-container").html(response);
        },
        error: function(xhr) {
            $("#recomendaciones-container").html(`
                <div class="alert alert-danger">
                    Error al cargar recomendaciones: ${xhr.statusText}
                </div>
            `);
            console.error("Error:", xhr.responseText);
        }
    });
}

// Llamar a esta función cuando:
// 1. La página carga
$(document).ready(function() {
    obtenerRecomendaciones();
});

// 2. Cuando se agrega un producto (modifica tu función agregarProducto)
function agregarProducto(idProducto, cantidad) {
    if(cantidad < 1) {
        alert("La cantidad debe ser al menos 1");
        return;
    }
    
    $.ajax({
        url: "agregarAlCarrito.php",
        method: "POST",
        data: {
            busqueda: idProducto,
            tipo_busqueda: "id",
            cantidad: cantidad
        },
        success: function(response) {
            location.reload(); // Recargar para ver cambios
            // O mejor: 
            // obtenerRecomendaciones(); // Actualizar sin recargar
        },
        error: function(xhr) {
            alert("Error: " + xhr.responseText);
        }
    });
}
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

    #cantidad {
    text-align: center;
    padding: 10px;
}

/* Estilo para el campo de cantidad */
.input-group-quantity {
    display: flex;
    align-items: center;
}

.input-group-quantity button {
    width: 30px;
    height: 30px;
    border: 1px solid #ddd;
    background: #f8f9fa;
    cursor: pointer;
}

.input-group-quantity input {
    width: 50px;
    text-align: center;
    margin: 0 5px;
}

/* Nuevos estilos para la sección de recomendaciones */
.recomendaciones-section {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    padding: 20px;
    margin: 20px 0;
}

.recomendaciones-title {
    color: #b65d09d1;
    font-weight: 600;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
}

.recomendaciones-title i {
    margin-right: 10px;
}

.recomendaciones-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 15px;
}

.recomendacion-card {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
    transition: all 0.3s;
    border: 1px solid #eee;
}

.recomendacion-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    border-color: #b65d09d1;
}

.recomendacion-nombre {
    font-weight: 500;
    margin-bottom: 5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.recomendacion-precio {
    color: #b65d09d1;
    font-weight: bold;
    margin-bottom: 10px;
}

.recomendacion-stock {
    font-size: 0.8em;
    color: #6c757d;
    margin-bottom: 10px;
}

.recomendacion-btn {
    width: 100%;
    padding: 8px;
    font-size: 0.9em;
}

/* Mejoras para el carrito vacío */
.carrito-vacio {
    text-align: center;
    padding: 30px;
    color: #6c757d;
}

.carrito-vacio i {
    font-size: 3em;
    margin-bottom: 15px;
    color: #dee2e6;
}
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
		<form class="search-form">
            <div class="form-row">
                <div class="col-md-5">
                    <label for="busqueda">Buscar producto:</label>
                    <input autocomplete="off" autofocus class="form-control" type="text" id="busqueda" 
                        placeholder="Código o nombre del producto">
                </div>
                <div class="col-md-3">
                    <label for="cantidad">Cantidad:</label>
                    <input type="number" min="1" value="1" class="form-control" id="cantidad">
                </div>
                <div class="col-md-2">
                    <label for="metodo_busqueda">Método:</label>
                    <select class="form-control" id="metodo_busqueda">
                        <option value="auto">Autodetección</option>
                        <option value="codigo">Por código</option>
                        <option value="nombre">Por nombre</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" id="btn-buscar" class="btn btn-primary btn-block">
                        <i class="fas fa-cart-plus"></i> Agregar
                    </button>
                </div>
            </div>
        </form>
		
        <div class="recomendaciones-section">
            <h3 class="recomendaciones-title">
                <i class="fas fa-lightbulb"></i> Recomendaciones basadas en tus ventas
            </h3>
            <div id="recomendaciones-container">
                <?php if(empty($_SESSION["carrito"])): ?>
                    <div class="carrito-vacio">
                        <i class="fas fa-shopping-basket"></i>
                        <p>Agrega productos al carrito para ver recomendaciones</p>
                    </div>
                <?php else: ?>
                    <!-- Las recomendaciones se cargarán aquí via AJAX -->
                    <div class="text-center py-3">
                        <i class="fas fa-spinner fa-spin"></i> Cargando recomendaciones...
                    </div>
                <?php endif; ?>
            </div>
        </div>

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
                <tr class="producto-en-carrito" data-id="<?php echo $producto->id ?>">
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