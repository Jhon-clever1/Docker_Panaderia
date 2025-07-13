<?php include_once "encabezado.php" 

?>

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
    
    .btn-secondary {
        background-color: #6c757d;
        color: white;
        min-width: 150px;
    }
    
    .btn-secondary:hover {
        background-color: #5a6268;
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

		<div class="buttons-container">
		<button class="btn btn-secondary" type="button" onclick="window.history.back();"><i class="fas fa-times"></i> Cancelar
        </button>
		<button class="btn btn-info" type="submit"><i class="fas fa-save"></i>Guardar Insumo
		</button>
		</div>
	</form>
</div>
<?php include_once "pie.php" ?>

