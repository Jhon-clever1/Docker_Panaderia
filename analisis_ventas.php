<?php
include_once "encabezado.php";
include_once "base_de_datos.php";

// Obtener parámetros de fecha
$fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-d', strtotime('-7 days'));
$fechaFin = $_GET['fecha_fin'] ?? date('Y-m-d');

// Validar fechas
if (strtotime($fechaInicio) > strtotime($fechaFin)) {
    $temp = $fechaInicio;
    $fechaInicio = $fechaFin;
    $fechaFin = $temp;
}

// Consulta para obtener ventas por hora
$consultaHoras = "
    SELECT 
        hora_venta AS hora,
        COUNT(*) AS total_ventas,
        SUM(total) AS monto_total
    FROM ventas
    WHERE DATE(fecha) BETWEEN ? AND ?
    GROUP BY hora_venta
    ORDER BY total_ventas DESC
";

$ventasPorHora = $base_de_datos->prepare($consultaHoras);
$ventasPorHora->execute([$fechaInicio, $fechaFin]);
$ventasPorHora = $ventasPorHora->fetchAll(PDO::FETCH_OBJ);

// Consulta para ventas por día de la semana
$consultaDias = "
    SELECT 
        dia_semana AS dia,
        COUNT(*) AS total_ventas,
        SUM(total) AS monto_total
    FROM ventas
    WHERE DATE(fecha) BETWEEN ? AND ?
    GROUP BY dia_semana
    ORDER BY total_ventas DESC
";

$ventasPorDia = $base_de_datos->prepare($consultaDias);
$ventasPorDia->execute([$fechaInicio, $fechaFin]);
$ventasPorDia = $ventasPorDia->fetchAll(PDO::FETCH_OBJ);

// Consulta para ventas por fecha específica
$consultaFechas = "
    SELECT 
        DATE(fecha) AS fecha,
        COUNT(*) AS total_ventas,
        SUM(total) AS monto_total
    FROM ventas
    WHERE DATE(fecha) BETWEEN ? AND ?
    GROUP BY DATE(fecha)
    ORDER BY fecha ASC
";

$ventasPorFecha = $base_de_datos->prepare($consultaFechas);
$ventasPorFecha->execute([$fechaInicio, $fechaFin]);
$ventasPorFecha = $ventasPorFecha->fetchAll(PDO::FETCH_OBJ);

// Obtener datos pico
$horaPico = $ventasPorHora[0]->hora ?? null;
$diaPico = $ventasPorDia[0]->dia ?? null;
$fechaPico = null;
$maxVentas = 0;

foreach ($ventasPorFecha as $venta) {
    if ($venta->total_ventas > $maxVentas) {
        $maxVentas = $venta->total_ventas;
        $fechaPico = $venta->fecha;
    }
}

// Consultas para productos más vendidos
if ($horaPico !== null) {
    $consultaProductosHora = "
        SELECT 
            p.descripcion,
            SUM(pv.cantidad) AS total_vendido,
            p.precioVenta,
            SUM(pv.cantidad * p.precioVenta) AS monto_total
        FROM productos_vendidos pv
        JOIN productos p ON pv.id_producto = p.id
        JOIN ventas v ON pv.id_venta = v.id
        WHERE v.hora_venta = ? AND DATE(v.fecha) BETWEEN ? AND ?
        GROUP BY pv.id_producto
        ORDER BY total_vendido DESC
        LIMIT 5
    ";
    
    $productosHoraPico = $base_de_datos->prepare($consultaProductosHora);
    $productosHoraPico->execute([$horaPico, $fechaInicio, $fechaFin]);
    $productosHoraPico = $productosHoraPico->fetchAll(PDO::FETCH_OBJ);
}

if ($diaPico !== null) {
    $consultaProductosDia = "
        SELECT 
            p.descripcion,
            SUM(pv.cantidad) AS total_vendido,
            p.precioVenta,
            SUM(pv.cantidad * p.precioVenta) AS monto_total
        FROM productos_vendidos pv
        JOIN productos p ON pv.id_producto = p.id
        JOIN ventas v ON pv.id_venta = v.id
        WHERE v.dia_semana = ? AND DATE(v.fecha) BETWEEN ? AND ?
        GROUP BY pv.id_producto
        ORDER BY total_vendido DESC
        LIMIT 5
    ";
    
    $productosDiaPico = $base_de_datos->prepare($consultaProductosDia);
    $productosDiaPico->execute([$diaPico, $fechaInicio, $fechaFin]);
    $productosDiaPico = $productosDiaPico->fetchAll(PDO::FETCH_OBJ);
}

if ($fechaPico !== null) {
    $consultaProductosFecha = "
        SELECT 
            p.descripcion,
            SUM(pv.cantidad) AS total_vendido,
            p.precioVenta,
            SUM(pv.cantidad * p.precioVenta) AS monto_total
        FROM productos_vendidos pv
        JOIN productos p ON pv.id_producto = p.id
        JOIN ventas v ON pv.id_venta = v.id
        WHERE DATE(v.fecha) = ?
        GROUP BY pv.id_producto
        ORDER BY total_vendido DESC
        LIMIT 5
    ";
    
    $productosFechaPico = $base_de_datos->prepare($consultaProductosFecha);
    $productosFechaPico->execute([$fechaPico]);
    $productosFechaPico = $productosFechaPico->fetchAll(PDO::FETCH_OBJ);
}
?>

<style>
    .analisis-container {
        max-width: 95%;
        margin: 30px auto;
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        padding: 30px;
    }
    
    .chart-container {
        height: 400px;
        margin: 40px 0;
    }
    
    .pico-section {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin: 20px 0;
    }
    
    .badge-pico {
        background-color: #b65d09d1;
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 1.2em;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
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
    
    .tab-content {
        margin-top: 20px;
    }
    
    .nav-tabs .nav-link {
        color: #495057;
        font-weight: 500;
    }
    
    .nav-tabs .nav-link.active {
        color: #b65d09d1;
        font-weight: 600;
        border-bottom: 2px solid #b65d09d1;
    }
    
    .filtro-fechas {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    
    .btn-rapido {
        margin-top: 28px;
    }
</style>

<div class="analisis-container">
    <h2><i class="fas fa-chart-line"></i> Análisis Completo de Ventas</h2>
    
    <!-- Filtros de fecha -->
    <div class="filtro-fechas">
        <form method="get" class="row">
            <div class="col-md-3">
                <label>Fecha Inicio:</label>
                <input type="date" name="fecha_inicio" value="<?= $fechaInicio ?>" class="form-control" max="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-3">
                <label>Fecha Fin:</label>
                <input type="date" name="fecha_fin" value="<?= $fechaFin ?>" class="form-control" max="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 28px;">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
            </div>
            <div class="col-md-2">
                <a href="?fecha_inicio=<?= date('Y-m-d') ?>&fecha_fin=<?= date('Y-m-d') ?>" class="btn btn-info btn-block btn-rapido">
                    <i class="fas fa-calendar-day"></i> Hoy
                </a>
            </div>
            <div class="col-md-2">
                <a href="?fecha_inicio=<?= date('Y-m-d', strtotime('-7 days')) ?>&fecha_fin=<?= date('Y-m-d') ?>" class="btn btn-secondary btn-block btn-rapido">
                    <i class="fas fa-calendar-week"></i> Últimos 7 días
                </a>
            </div>
        </form>
    </div>
    
    <div class="alert alert-info mt-3">
        <i class="fas fa-info-circle"></i> Mostrando datos del <?= date('d/m/Y', strtotime($fechaInicio)) ?> al <?= date('d/m/Y', strtotime($fechaFin)) ?>
    </div>
    
    <ul class="nav nav-tabs" id="analisisTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="horas-tab" data-toggle="tab" href="#horas" role="tab">
                <i class="fas fa-clock"></i> Por Horas
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="dias-tab" data-toggle="tab" href="#dias" role="tab">
                <i class="fas fa-calendar-week"></i> Por Días
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="fechas-tab" data-toggle="tab" href="#fechas" role="tab">
                <i class="fas fa-calendar-alt"></i> Por Fechas
            </a>
        </li>
    </ul>
    
    <div class="tab-content" id="analisisTabsContent">
        <!-- Pestaña de Horas -->
        <div class="tab-pane fade show active" id="horas" role="tabpanel">
            <?php if (!empty($ventasPorHora)): ?>
                <div class="chart-container">
                    <canvas id="ventasHoraChart"></canvas>
                </div>
                
                <div class="pico-section">
                    <h3>Hora Pico de Ventas: <span class="badge-pico"><?= $horaPico ?>:00 - <?= ($horaPico + 1) ?>:00</span></h3>
                    <p>Con un total de <?= $ventasPorHora[0]->total_ventas ?> ventas y S/<?= number_format($ventasPorHora[0]->monto_total, 2) ?> en esta franja horaria.</p>
                </div>
                
                <?php if (!empty($productosHoraPico)): ?>
                    <h3><i class="fas fa-star"></i> Productos más vendidos en la hora pico</h3>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad Vendida</th>
                                <th>Monto Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productosHoraPico as $producto): ?>
                            <tr>
                                <td><?= htmlspecialchars($producto->descripcion) ?></td>
                                <td><?= $producto->total_vendido ?></td>
                                <td>S/<?= number_format($producto->monto_total, 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No hay datos de ventas para el período seleccionado.
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Pestaña de Días -->
        <div class="tab-pane fade" id="dias" role="tabpanel">
            <?php if (!empty($ventasPorDia)): ?>
                <div class="chart-container">
                    <canvas id="ventasDiaChart"></canvas>
                </div>
                
                <?php if ($diaPico): ?>
                <div class="pico-section">
                    <?php 
                    $diasEspanol = [
                        'Monday' => 'Lunes',
                        'Tuesday' => 'Martes',
                        'Wednesday' => 'Miércoles',
                        'Thursday' => 'Jueves',
                        'Friday' => 'Viernes',
                        'Saturday' => 'Sábado',
                        'Sunday' => 'Domingo'
                    ];
                    ?>
                    <h3>Día con más ventas: <span class="badge-pico"><?= $diasEspanol[$diaPico] ?? $diaPico ?></span></h3>
                    <p>Con un total de <?= $ventasPorDia[0]->total_ventas ?> ventas y S/<?= number_format($ventasPorDia[0]->monto_total, 2) ?> en este día.</p>
                </div>
                
                <?php if (!empty($productosDiaPico)): ?>
                    <h3><i class="fas fa-star"></i> Productos más vendidos en el día pico</h3>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad Vendida</th>
                                <th>Monto Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productosDiaPico as $producto): ?>
                            <tr>
                                <td><?= htmlspecialchars($producto->descripcion) ?></td>
                                <td><?= $producto->total_vendido ?></td>
                                <td>S/<?= number_format($producto->monto_total, 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No hay datos de ventas para el período seleccionado.
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Pestaña de Fechas -->
        <div class="tab-pane fade" id="fechas" role="tabpanel">
            <?php if (!empty($ventasPorFecha)): ?>
                <div class="chart-container">
                    <canvas id="ventasFechaChart"></canvas>
                </div>
                
                <?php if ($fechaPico): ?>
                <div class="pico-section">
                    <h3>Fecha con más ventas: <span class="badge-pico"><?= date('d/m/Y', strtotime($fechaPico)) ?></span></h3>
                    <p>Con un total de <?= $maxVentas ?> ventas y S/<?= number_format($ventasPorFecha[array_search($fechaPico, array_column($ventasPorFecha, 'fecha'))]->monto_total, 2) ?> en este día.</p>
                </div>
                
                <?php if (!empty($productosFechaPico)): ?>
                    <h3><i class="fas fa-star"></i> Productos más vendidos en la fecha pico</h3>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad Vendida</th>
                                <th>Monto Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productosFechaPico as $producto): ?>
                            <tr>
                                <td><?= htmlspecialchars($producto->descripcion) ?></td>
                                <td><?= $producto->total_vendido ?></td>
                                <td>S/<?= number_format($producto->monto_total, 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No hay datos de ventas para el período seleccionado.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($ventasPorHora)): ?>
    // Gráfico por horas
    const horas = Array.from({length: 24}, (_, i) => i);
    const ventasHoraData = Array(24).fill(0);
    const montoHoraData = Array(24).fill(0);
    
    <?php foreach ($ventasPorHora as $venta): ?>
    ventasHoraData[<?= $venta->hora ?>] = <?= $venta->total_ventas ?>;
    montoHoraData[<?= $venta->hora ?>] = <?= $venta->monto_total ?>;
    <?php endforeach; ?>
    
    const ctxHora = document.getElementById('ventasHoraChart').getContext('2d');
    new Chart(ctxHora, {
        type: 'bar',
        data: {
            labels: horas.map(h => h + ':00'),
            datasets: [
                {
                    label: 'Número de Ventas',
                    data: ventasHoraData,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    yAxisID: 'y'
                },
                {
                    label: 'Monto Total (S/)',
                    data: montoHoraData,
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    type: 'line',
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Número de Ventas'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Monto Total (S/)'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
    <?php endif; ?>
    
    <?php if (!empty($ventasPorDia)): ?>
    // Gráfico por días
    const diasOrden = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    const diasEspañol = {
        'Monday': 'Lunes',
        'Tuesday': 'Martes',
        'Wednesday': 'Miércoles',
        'Thursday': 'Jueves',
        'Friday': 'Viernes',
        'Saturday': 'Sábado',
        'Sunday': 'Domingo'
    };
    
    const ventasDiaData = {};
    const montoDiaData = {};
    
    <?php foreach ($ventasPorDia as $venta): ?>
    ventasDiaData['<?= $venta->dia ?>'] = <?= $venta->total_ventas ?>;
    montoDiaData['<?= $venta->dia ?>'] = <?= $venta->monto_total ?>;
    <?php endforeach; ?>
    
    // Asegurar que todos los días estén presentes
    diasOrden.forEach(dia => {
        if (!ventasDiaData.hasOwnProperty(dia)) {
            ventasDiaData[dia] = 0;
            montoDiaData[dia] = 0;
        }
    });
    
    const ctxDia = document.getElementById('ventasDiaChart').getContext('2d');
    new Chart(ctxDia, {
        type: 'bar',
        data: {
            labels: diasOrden.map(dia => diasEspañol[dia]),
            datasets: [
                {
                    label: 'Número de Ventas',
                    data: diasOrden.map(dia => ventasDiaData[dia]),
                    backgroundColor: 'rgba(255, 159, 64, 0.7)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1,
                    yAxisID: 'y'
                },
                {
                    label: 'Monto Total (S/)',
                    data: diasOrden.map(dia => montoDiaData[dia]),
                    backgroundColor: 'rgba(153, 102, 255, 0.7)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1,
                    type: 'line',
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Número de Ventas'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Monto Total (S/)'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
    <?php endif; ?>
    
    <?php if (!empty($ventasPorFecha)): ?>
    // Gráfico por fechas
    const fechas = <?= json_encode(array_map(function($v) { 
        return date('d/m', strtotime($v->fecha)); 
    }, $ventasPorFecha)) ?>;
    
    const ventasFechaData = <?= json_encode(array_column($ventasPorFecha, 'total_ventas')) ?>;
    const montoFechaData = <?= json_encode(array_column($ventasPorFecha, 'monto_total')) ?>;
    
    const ctxFecha = document.getElementById('ventasFechaChart').getContext('2d');
    new Chart(ctxFecha, {
        type: 'bar',
        data: {
            labels: fechas,
            datasets: [
                {
                    label: 'Número de Ventas',
                    data: ventasFechaData,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    yAxisID: 'y'
                },
                {
                    label: 'Monto Total (S/)',
                    data: montoFechaData,
                    backgroundColor: 'rgba(255, 99, 132, 0.7)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1,
                    type: 'line',
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Número de Ventas'
                    }
                },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Monto Total (S/)'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
    <?php endif; ?>
    
    // Deshabilitar fechas futuras en los inputs
    const today = new Date().toISOString().split('T')[0];
    document.querySelectorAll('input[type="date"]').forEach(input => {
        input.max = today;
    });
});
</script>

<?php include_once "pie.php"; ?>