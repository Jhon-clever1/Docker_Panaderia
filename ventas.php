<?php 
$_SESSION['current_page'] = 'ventas.php';
include_once "encabezado.php";

include_once "base_de_datos.php";

// Verificar usuario
$usuario = $_SESSION['usuario'];
if(!isset($usuario)){
    header("location: index.php");
    exit;
}

// Manejo de filtros
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'hoy';
$fecha_actual = date('Y-m-d');
$params = [];

$sql = "SELECT ventas.total, ventas.fecha, ventas.id, 
        GROUP_CONCAT(productos.codigo, '..', productos.descripcion, '..', productos_vendidos.cantidad SEPARATOR '__') AS productos 
        FROM ventas 
        INNER JOIN productos_vendidos ON productos_vendidos.id_venta = ventas.id 
        INNER JOIN productos ON productos.id = productos_vendidos.id_producto";

// Aplicar filtro
if(isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin'])){
    // Filtro por rango de fechas
    $sql .= " WHERE DATE(ventas.fecha) BETWEEN ? AND ?";
    $params[] = $_GET['fecha_inicio'];
    $params[] = $_GET['fecha_fin'];
    $filtro = 'personalizado';
} elseif ($filtro === 'hoy') {
    // Filtro por día actual (por defecto)
    $sql .= " WHERE DATE(ventas.fecha) = ?";
    $params[] = $fecha_actual;
} elseif ($filtro === 'ayer') {
    // Filtro por día anterior
    $sql .= " WHERE DATE(ventas.fecha) = ?";
    $params[] = date('Y-m-d', strtotime('-1 day'));
} elseif ($filtro === 'semana') {
    // Filtro por semana actual
    $sql .= " WHERE YEARWEEK(ventas.fecha, 1) = YEARWEEK(CURDATE(), 1)";
} elseif ($filtro === 'mes') {
    // Filtro por mes actual
    $sql .= " WHERE MONTH(ventas.fecha) = MONTH(CURRENT_DATE()) 
              AND YEAR(ventas.fecha) = YEAR(CURRENT_DATE())";
}

$sql .= " GROUP BY ventas.id ORDER BY ventas.id DESC";

$sentencia = $base_de_datos->prepare($sql);
$sentencia->execute($params);
$ventas = $sentencia->fetchAll(PDO::FETCH_OBJ);

$total_ventas = 0;
foreach($ventas as $venta){
    $total_ventas += $venta->total;
}
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
        display: inline-flex;
        margin-top: 10px;
    }
    .btn-group a {
        margin-right: 5px;
    }
    .btn-group a.active {
        background-color: #b65d09d1;
        border-color: #b65d09d1;
    }
    .filtro-container {
        background: white;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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

	<div class="container py-5 col-xs-12">
    <h1>Ventas Realizadas</h1>
    
    <div class="filtros-ventas">
        <h3>Filtrar Ventas</h3>
        <form method="get" action="">
            <div class="filtro-group">
                <div>
                    <label for="fecha_inicio">Desde:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" 
                           value="<?php echo isset($_GET['fecha_inicio']) ? htmlspecialchars($_GET['fecha_inicio']) : ''; ?>" 
                           class="form-control">
                </div>
                <div>
                    <label for="fecha_fin">Hasta:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" 
                           value="<?php echo isset($_GET['fecha_fin']) ? htmlspecialchars($_GET['fecha_fin']) : ''; ?>" 
                           class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrar por fecha
                </button>
            </div>
        </form>

        <div class="btn-group-filtros mt-3">
            <a href="?filtro=hoy" class="btn <?php echo ($filtro === 'hoy') ? 'btn-primary' : 'btn-outline-primary'; ?>">
                <i class="fas fa-calendar-day"></i> Hoy
            </a>
            <a href="?filtro=ayer" class="btn <?php echo ($filtro === 'ayer') ? 'btn-primary' : 'btn-outline-primary'; ?>">
                <i class="fas fa-calendar-minus"></i> Ayer
            </a>
            <a href="?filtro=semana" class="btn <?php echo ($filtro === 'semana') ? 'btn-primary' : 'btn-outline-primary'; ?>">
                <i class="fas fa-calendar-week"></i> Esta semana
            </a>
            <a href="?filtro=mes" class="btn <?php echo ($filtro === 'mes') ? 'btn-primary' : 'btn-outline-primary'; ?>">
                <i class="fas fa-calendar-alt"></i> Este mes
            </a>
            <a href="?filtro=todas" class="btn <?php echo ($filtro === 'todas') ? 'btn-primary' : 'btn-outline-primary'; ?>">
                <i class="fas fa-calendar"></i> Todas
            </a>
        </div>
    </div>

    <div class="btn-group mt-3">
        <a class="btn btn-success" href="./vender.php"><i class="fa fa-plus"></i> Nueva Venta</a>
        <a class="btn btn-danger" href="./reportes/reporteVentas2.php"><i class="fa fa-list"></i> Reporte de venta</a>
    </div>

    <div class="filtro-container mt-3">
        <h4>
            <?php 
            if($filtro === 'hoy'){
                echo "Ventas del día: ".date('d/m/Y');
            } elseif($filtro === 'ayer'){
                echo "Ventas de ayer: ".date('d/m/Y', strtotime('-1 day'));
            } elseif($filtro === 'semana'){
                echo "Ventas de esta semana";
            } elseif($filtro === 'mes'){
                echo "Ventas de este mes: ".date('m/Y');
            } elseif($filtro === 'personalizado'){
                echo "Ventas desde ".htmlspecialchars($_GET['fecha_inicio'])." hasta ".htmlspecialchars($_GET['fecha_fin']);
            } else {
                echo "Todas las ventas";
            }
            ?>
            <span class="badge bg-secondary ms-2">Total: S/ <?php echo number_format($total_ventas, 2); ?></span>
        </h4>
    </div>

    <?php if(empty($ventas)): ?>
        <div class="alert alert-info mt-3">
            No hay ventas registradas para el período seleccionado
        </div>
    <?php else: ?>
        <div class="table-container">
            <table class="table table-bordered table-hover">
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
                    <?php foreach($ventas as $venta): ?>
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
                                    <?php foreach(explode("__", $venta->productos) as $productosConcatenados): 
                                    $producto = explode("..", $productosConcatenados); ?>
                                    <tr>
                                        <td><?php echo $producto[0] ?></td>
                                        <td><?php echo $producto[1] ?></td>
                                        <td><?php echo $producto[2] ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </td>
                        <td class="currency-format"><?php echo "S/".number_format($venta->total, 2) ?></td>
                        <?php if (esAdministrador()): ?>
                        <td><a class="btn btn-danger action-btn" href="<?php echo "eliminarVenta.php?id=" . $venta->id?>"><i class="fa fa-trash"></i> Eliminar</a></td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include_once "pie.php" ?>