<?php
include_once "encabezado.php";
include_once "base_de_datos.php";

$usuario = $_SESSION['usuario'];

if(!isset($usuario)) {
    header("location: index.php");
    exit();
}

// Manejo de fechas
$periodo = isset($_GET['periodo']) ? $_GET['periodo'] : 'dia';
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-d');
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d');

// Validar y ajustar fechas
if($periodo == 'personalizado') {
    // Validar que fecha_inicio <= fecha_fin
    if(strtotime($fecha_inicio) > strtotime($fecha_fin)) {
        $temp = $fecha_inicio;
        $fecha_inicio = $fecha_fin;
        $fecha_fin = $temp;
    }
    
    // Limitar a máximo 1 año de diferencia
    if((strtotime($fecha_fin) - strtotime($fecha_inicio)) > 31536000) {
        $_SESSION['error'] = "El rango máximo permitido es de 1 año";
        $fecha_fin = date('Y-m-d', strtotime($fecha_inicio.' + 1 year'));
    }
}

// Ajustar fechas según período seleccionado
switch($periodo) {
    case 'dia':
        $fecha_inicio = $fecha_fin = date('Y-m-d');
        $titulo = "Productos Más Vendidos - Hoy (".date('d/m/Y').")";
        break;
    case 'semana':
        $fecha_inicio = date('Y-m-d', strtotime('monday this week'));
        $titulo = "Productos Más Vendidos - Esta Semana";
        break;
    case 'mes':
        $fecha_inicio = date('Y-m-01');
        $titulo = "Productos Más Vendidos - Este Mes";
        break;
    case 'año':
        $fecha_inicio = date('Y-01-01');
        $titulo = "Productos Más Vendidos - Este Año";
        break;
    case 'personalizado':
        $titulo = "Productos Más Vendidos - Personalizado (".date('d/m/Y', strtotime($fecha_inicio))." a ".date('d/m/Y', strtotime($fecha_fin)).")";
        break;
    default:
        $fecha_inicio = $fecha_fin = date('Y-m-d');
        $titulo = "Productos Más Vendidos - Hoy";
}

// Consulta SQL
$sql = "SELECT p.id, p.descripcion, SUM(pv.cantidad) as total_vendido 
        FROM productos_vendidos pv 
        JOIN productos p ON pv.id_producto = p.id 
        JOIN ventas v ON pv.id_venta = v.id
        WHERE DATE(v.fecha) BETWEEN ? AND ?
        GROUP BY pv.id_producto 
        ORDER BY total_vendido DESC 
        LIMIT 10";

$productos = $base_de_datos->prepare($sql);
$productos->execute([$fecha_inicio, $fecha_fin]);
$productos = $productos->fetchAll(PDO::FETCH_OBJ);

// Calcular total vendido para porcentajes
$total_vendido = array_sum(array_column($productos, 'total_vendido'));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Productos Vendidos</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        
        .reporte-container {
            max-width: 95%;
            margin: 30px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 30px;
        }
        
        .periodo-filtros {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 30px;
            align-items: center;
        }
        
        .periodo-btn {
            padding: 10px 15px;
            border-radius: 8px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: #333;
            font-size: 14px;
        }
        
        .periodo-btn:hover, .periodo-btn.active {
            background: #3498db;
            color: white;
            border-color: #3498db;
        }
        
        .filtro-personalizado {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: flex-end;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-left: auto;
        }
        
        .filtro-personalizado .form-group {
            margin-bottom: 0;
        }
        
        .filtro-personalizado label {
            display: block;
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .chart-container {
            margin: 40px 0;
            height: 400px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .no-data-message {
            text-align: center;
            padding: 30px;
            background: #f8f9fa;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .no-data-message i {
            font-size: 48px;
            color: #3498db;
            margin-bottom: 15px;
        }
        
        .progress-bar {
            height: 20px;
            background: #e0e0e0;
            border-radius: 10px;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .progress {
            height: 100%;
            background: #3498db;
            border-radius: 10px;
        }
        
        @media (max-width: 768px) {
            .periodo-filtros {
                flex-direction: column;
            }
            
            .filtro-personalizado {
                margin-left: 0;
                margin-top: 15px;
                width: 100%;
            }
            
            .periodo-btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="reporte-container">
        <h2 style="text-align: center; margin-bottom: 30px;">
            <i class="fas fa-chart-line"></i> <?= $titulo ?>
        </h2>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger" style="margin-bottom: 20px;">
                <?= $_SESSION['error'] ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <div class="periodo-filtros">
            <!-- Botones de período rápido -->
            <a href="?periodo=dia" class="periodo-btn <?= $periodo == 'dia' ? 'active' : '' ?>">
                <i class="fas fa-calendar-day"></i> Hoy
            </a>
            <a href="?periodo=semana" class="periodo-btn <?= $periodo == 'semana' ? 'active' : '' ?>">
                <i class="fas fa-calendar-week"></i> Semana
            </a>
            <a href="?periodo=mes" class="periodo-btn <?= $periodo == 'mes' ? 'active' : '' ?>">
                <i class="fas fa-calendar-alt"></i> Mes
            </a>
            <a href="?periodo=año" class="periodo-btn <?= $periodo == 'año' ? 'active' : '' ?>">
                <i class="fas fa-calendar"></i> Año
            </a>
            
            <!-- Formulario de rango personalizado -->
            <form method="get" action="" class="filtro-personalizado">
                <input type="hidden" name="periodo" value="personalizado">
                
                <div class="form-group">
                    <label for="fecha_inicio"><i class="fas fa-calendar"></i> Desde:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" 
                           value="<?= $periodo == 'personalizado' ? $fecha_inicio : '' ?>" 
                           class="form-control" style="padding: 8px 12px;">
                </div>
                
                <div class="form-group">
                    <label for="fecha_fin"><i class="fas fa-calendar"></i> Hasta:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" 
                           value="<?= $periodo == 'personalizado' ? $fecha_fin : '' ?>" 
                           class="form-control" style="padding: 8px 12px;">
                </div>
                
                <button type="submit" class="btn btn-primary" style="padding: 8px 15px; height: 38px;">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </form>
        </div>
        
        <?php if(empty($productos)): ?>
            <div class="no-data-message">
                <i class="fas fa-info-circle"></i>
                <h3>No hay datos de ventas</h3>
                <p>
                    <?php if($periodo == 'personalizado'): ?>
                        No se registraron ventas entre <?= date('d/m/Y', strtotime($fecha_inicio)) ?> y <?= date('d/m/Y', strtotime($fecha_fin)) ?>
                    <?php else: ?>
                        No se registraron ventas en el período seleccionado
                    <?php endif; ?>
                </p>
            </div>
        <?php else: ?>
            <!-- Gráfico -->
            <div class="chart-container">
                <canvas id="ventasChart"></canvas>
            </div>
            
            <!-- Tabla -->
            <h3 style="margin-top: 40px;">
                <i class="fas fa-list-ol"></i> Ranking de Productos
            </h3>
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Producto</th>
                        <th>Cantidad Vendida</th>
                        <th>Porcentaje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($productos as $i => $producto): 
                        $porcentaje = $total_vendido > 0 ? ($producto->total_vendido / $total_vendido) * 100 : 0;
                    ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($producto->descripcion) ?></td>
                        <td><?= $producto->total_vendido ?></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 100px;">
                                    <div class="progress-bar">
                                        <div class="progress" style="width: <?= round($porcentaje) ?>%"></div>
                                    </div>
                                </div>
                                <span><?= round($porcentaje, 1) ?>%</span>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- Script del gráfico -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Deshabilitar fechas futuras
                    const today = new Date().toISOString().split('T')[0];
                    document.getElementById('fecha_inicio').max = today;
                    document.getElementById('fecha_fin').max = today;
                    
                    // Configurar gráfico
                    const productos = <?= json_encode(array_map(function($p) { 
                        return strlen($p->descripcion) > 20 ? substr($p->descripcion, 0, 17).'...' : $p->descripcion; 
                    }, $productos)) ?>;
                    
                    const ventas = <?= json_encode(array_column($productos, 'total_vendido')) ?>;
                    const colors = [
                        '#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6',
                        '#1abc9c', '#d35400', '#34495e', '#7f8c8d', '#27ae60'
                    ];
                    
                    const ctx = document.getElementById('ventasChart').getContext('2d');
                    const chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: productos,
                            datasets: [{
                                label: 'Unidades Vendidas',
                                data: ventas,
                                backgroundColor: colors,
                                borderColor: colors.map(c => c.replace('0.8', '1')),
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return `${context.parsed.y} unidades (${Math.round(context.parsed.y/<?= $total_vendido ?>*100)}%)`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Unidades Vendidas'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Productos'
                                    }
                                }
                            }
                        }
                    });
                });
            </script>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
include_once "pie.php";
?>