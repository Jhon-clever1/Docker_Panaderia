<?php
include_once "encabezado.php";
include_once "control_acceso.php";

$usuario = $_SESSION['usuario'] ?? null;

if(!isset($usuario)) {
    header("location: index.php");
    exit();
}
?>

<?php
// Verificar stock bajo para el dashboard
include_once "base_de_datos.php";
$sentencia = $base_de_datos->query("SELECT descripcion, existencia FROM productos WHERE existencia <= 5");
$stockBajo = $sentencia->fetchAll(PDO::FETCH_OBJ);

if(!empty($stockBajo)) {
    echo '<div class="alert alert-warning" style="margin: 20px auto; max-width: 1200px;">';
    echo '<h4><i class="fas fa-exclamation-triangle"></i> Alerta de Stock Bajo</h4>';
    echo '<p>Algunos productos están por agotarse:</p>';
    echo '<ul>';
    foreach($stockBajo as $producto) {
        echo '<li><strong>'.$producto->descripcion.'</strong> - '.$producto->existencia.' unidades restantes</li>';
    }
    echo '</ul>';
    echo '<a href="listar.php" class="btn btn-sm btn-warning">Ver Productos</a>';
    echo '</div>';
}
?>

<style>
    .dashboard-container {
        max-width: 1200px;
        margin: 30px auto;
        padding: 20px;
    }
    
    .dashboard-header {
        text-align: center;
        margin-bottom: 40px;
    }
    
    .dashboard-title {
        color: #b65d09d1;
        font-weight: 600;
        margin-bottom: 10px;
    }
    
    .cards-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 25px;
        padding: 20px;
    }
    
    .card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        overflow: hidden;
        border: none;
        cursor: pointer;
    }
    
    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15);
    }
    
    .card-body {
        padding: 25px;
        text-align: center;
    }
    
    .card-icon {
        font-size: 50px;
        margin-bottom: 20px;
        color: #b65d09d1;
    }
    
    .card-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 10px;
        color: #2c3e50;
    }
    
    .card-text {
        color: #7f8c8d;
        font-size: 14px;
    }
    
    .card-footer {
        background: #f8f9fa;
        padding: 15px;
        text-align: center;
        border-top: 1px solid #eee;
    }
    
    .btn-card {
        border-radius: 8px;
        padding: 8px 20px;
        font-weight: 500;
        transition: all 0.3s;
    }
    
    @media (max-width: 768px) {
        .cards-container {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Panel de Control</h1>
        <p>Bienvenido, <?= htmlspecialchars($usuario) ?></p>
    </div>
    
    <div class="cards-container">
        <!-- Tarjeta de Productos -->
        <div class="card" onclick="window.location.href='listar.php'">
            <div class="card-body">
                <div class="card-icon">
                    <i class="fas fa-box-open"></i>
                </div>
                <h3 class="card-title">Gestión de Productos</h3>
                <p class="card-text">Administra el inventario de productos, precios y existencias.</p>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary btn-card">Acceder</button>
            </div>
        </div>
        
        <!-- Tarjeta de Ventas -->
        <div class="card" onclick="window.location.href='vender.php'">
            <div class="card-body">
                <div class="card-icon">
                    <i class="fas fa-cash-register"></i>
                </div>
                <h3 class="card-title">Punto de Venta</h3>
                <p class="card-text">Realiza nuevas ventas y gestiona transacciones.</p>
            </div>
            <div class="card-footer">
                <button class="btn btn-success btn-card">Acceder</button>
            </div>
        </div>
        
        <!-- Tarjeta de Historial -->
        <div class="card" onclick="window.location.href='ventas.php'">
            <div class="card-body">
                <div class="card-icon">
                    <i class="fas fa-history"></i>
                </div>
                <h3 class="card-title">Historial de Ventas</h3>
                <p class="card-text">Consulta el registro completo de todas las ventas realizadas.</p>
            </div>
            <div class="card-footer">
                <button class="btn btn-info btn-card">Acceder</button>
            </div>
        </div>
        
        <!-- Tarjeta de Insumos -->
        <div class="card" onclick="window.location.href='insumos.php'">
            <div class="card-body">
                <div class="card-icon">
                    <i class="fas fa-warehouse"></i>
                </div>
                <h3 class="card-title">Gestión de Insumos</h3>
                <p class="card-text">Administra materias primas y suministros.</p>
            </div>
            <div class="card-footer">
                <button class="btn btn-warning btn-card">Acceder</button>
            </div>
        </div>
        
        <!-- Tarjeta de Reportes -->
        <div class="card" onclick="window.location.href='reporte_productos_vendidos.php'">
            <div class="card-body">
                <div class="card-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3 class="card-title">Reporte</h3>
                <p class="card-text">Genera reporte del productos más vendidos</p>
            </div>
            <div class="card-footer">
                <button class="btn btn-danger btn-card">Acceder</button>
            </div>
        </div>
        
        <!-- Tarjeta de Usuarios -->
         <?php if (esAdministrador()): ?>
        <div class="card" onclick="window.location.href='gestion_usuarios.php'">
            <div class="card-body">
                <div class="card-icon">
                    <i class="fas fa-users-cog"></i>
                </div>
                <h3 class="card-title">Gestión de Usuarios</h3>
                <p class="card-text">Administra usuarios y permisos del sistema.</p>
            </div>
            <div class="card-footer">
                <button class="btn btn-secondary btn-card">Acceder</button>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tarjeta de Análisis de Ventas -->
        <div class="card" onclick="window.location.href='analisis_ventas.php'">
            <div class="card-body">
                <div class="card-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3 class="card-title">Análisis de Ventas</h3>
                <p class="card-text">Ver horas pico y productos más vendidos por hora.</p>
            </div>
            <div class="card-footer">
                <button class="btn btn-info btn-card">Ver Análisis</button>
            </div>
        </div>
    </div>
</div>

<?php include_once "pie.php"; ?>