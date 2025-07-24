<?php
include_once "base_de_datos.php";
include_once "control_acceso.php";

if(!esAdministrador()) {
    header("Location: dashboard.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productosBase = $_POST['productosBase'];
    $productosAsociados = $_POST['productosAsociados'];
    $descuento = $_POST['descuento'];
    $fechaInicio = $_POST['fechaInicio'];
    $fechaFin = $_POST['fechaFin'];
    
    // Aquí implementarías la lógica para guardar la promoción en tu base de datos
    $sentencia = $base_de_datos->prepare("INSERT INTO promociones (productos_base, productos_asociados, descuento, fecha_inicio, fecha_fin) 
                                         VALUES (?, ?, ?, ?, ?)");
    $resultado = $sentencia->execute([$productosBase, $productosAsociados, $descuento, $fechaInicio, $fechaFin]);
    
    if($resultado) {
        header("Location: analisis_asociacion.php?status=1");
    } else {
        header("Location: analisis_asociacion.php?status=2");
    }
    exit;
}

// Implementar formulario para crear promoción
include_once "encabezado.php";
?>

<div class="container">
    <h2>Crear Nueva Promoción</h2>
    
    <form method="post">
        <div class="form-group">
            <label>Productos Base:</label>
            <input type="text" name="productosBase" class="form-control" readonly>
        </div>
        
        <div class="form-group">
            <label>Productos Asociados:</label>
            <input type="text" name="productosAsociados" class="form-control" readonly>
        </div>
        
        <div class="form-group">
            <label>Descuento (%):</label>
            <input type="number" name="descuento" class="form-control" min="1" max="50" required>
        </div>
        
        <div class="form-group">
            <label>Fecha Inicio:</label>
            <input type="date" name="fechaInicio" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label>Fecha Fin:</label>
            <input type="date" name="fechaFin" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Guardar Promoción</button>
    </form>
</div>

<?php include_once "pie.php"; ?>