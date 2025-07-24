<?php
include_once "encabezado.php";
include_once "control_acceso.php";

// 1. Obtener hora y día actual
$hora = date("G");
$dia_numero = date("N"); // 1 (Lunes) a 7 (Domingo)

// Convertir día a español
$dias_espanol = [
    1 => "Lunes",
    2 => "Martes",
    3 => "Miércoles",
    4 => "Jueves",
    5 => "Viernes",
    6 => "Sábado",
    7 => "Domingo"
];

$dia_semana = $dias_espanol[$dia_numero];

// URL base de la API
$api_base_url = "http://10.55.114.186:8000";

// Función para enviar datos de venta via POST
function registrarVenta($api_url, $datosVenta) {
    $url = $api_url . "/ventas/";
    $opts = [
        "http" => [
            "header" => "Content-Type: application/json",
            "method" => "POST",
            "content" => json_encode($datosVenta)
        ]
    ];
    $ctx = stream_context_create($opts);
    return @file_get_contents($url, false, $ctx);
}

// Función para consultar ventas via GET
function consultarVentas($api_url, $filtros = []) {
    $url = $api_url . "/ventas/";
    if (!empty($filtros)) {
        $url .= "?" . http_build_query($filtros);
    }
    return @file_get_contents($url);
}

// Función para obtener predicción
function obtenerPrediccion($api_url, $hora, $dia_numero) {
    $url = $api_url . "/predict/";
    $body = json_encode(["hora" => $hora, "dia_semana" => $dia_numero]);
    $opts = [
        "http" => [
            "header" => "Content-Type: application/json",
            "method" => "POST",
            "content" => $body
        ]
    ];
    $ctx = stream_context_create($opts);
    return @file_get_contents($url, false, $ctx);
}

// Procesamiento del formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_venta'])) {
    $datosVenta = [
        'hora' => $_POST['hora'] ?? $hora,
        'dia_semana' => $_POST['dia_semana'] ?? $dia_numero,
        'id_producto' => $_POST['id_producto'],
        'cantidad' => $_POST['cantidad'],
        'precio_total' => $_POST['precio_total']
    ];
    $resultadoPost = registrarVenta($api_base_url, $datosVenta);
}

// Consulta de ventas si se solicitó
$ventas = [];
if (isset($_GET['consultar_ventas'])) {
    $filtros = array_filter([
        'hora' => $_GET['hora'] ?? null,
        'dia_semana' => $_GET['dia_semana'] ?? null,
        'id_producto' => $_GET['id_producto'] ?? null
    ]);
    $response = consultarVentas($api_base_url, $filtros);
    if ($response !== false) {
        $ventas = json_decode($response, true) ?: [];
    }
}

// Lógica de predicción
$response = obtenerPrediccion($api_base_url, $hora, $dia_numero);
$cluster = null;
$producto = "No disponible";

if ($response !== false) {
    $data = json_decode($response, true);
    $cluster = $data["cluster"] ?? null;
    $productos_por_cluster = [
        0 => "Concha de chocolate",
        1 => "Dona rellena de fresa",
        2 => "Pan danés",
        3 => "Torta de Chocolate",
        4 => "Pay",
        5 => "Cuernito",
        6 => "Pan de muerto",
        7 => "Rosca de reyes",
        8 => "Baguette",
        9 => "Galleta de mantequilla"
    ];
    $producto = $productos_por_cluster[$cluster] ?? "Producto no registrado";
}
?>

<style>
    /* Estilos originales... */
    .form-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        padding: 20px;
        margin: 20px 0;
    }
    .form-group {
        margin-bottom: 15px;
    }
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }
    .form-control {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .btn {
        padding: 10px 15px;
        background-color: #b65d09d1;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    .btn:hover {
        background-color: #a55208;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }
    .table th, .table td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
    }
    .table th {
        background-color: #f5f5f5;
    }
</style>

<div class="container py-5">
    <div class="col-xs-12">
        <h1>Predicción de Ventas</h1>
        
        <!-- Sección de Predicción Original -->
        <div class="card">
            <div class="result-item">
                <strong>Hora actual:</strong> <?= $hora ?>:00 hrs
            </div>
            <div class="result-item">
                <strong>Día actual:</strong> <?= $dia_semana ?>
            </div>
            
            <?php if ($cluster !== null): ?>
                <div class="highlight">
                    <div class="result-item">
                        <strong>Cluster predicho:</strong> <?= $cluster ?>
                    </div>
                    <div class="result-item">
                        <strong>Producto recomendado:</strong> 
                        <span style="color: #b65d09d1; font-weight: bold;">
                            <?= $producto ?>
                        </span>
                    </div>
                </div>
            <?php else: ?>
                <div class="error-message">
                    <p>Error al contactar el servicio de predicción.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sección para Registrar Ventas (POST) -->
        <div class="form-container">
            <h2>Registrar Nueva Venta</h2>
            <form method="post">
                <input type="hidden" name="registrar_venta" value="1">
                <div class="form-group">
                    <label>Hora:</label>
                    <input type="number" name="hora" class="form-control" value="<?= $hora ?>" min="0" max="23">
                </div>
                <div class="form-group">
                    <label>Día de la semana (1-7):</label>
                    <input type="number" name="dia_semana" class="form-control" value="<?= $dia_numero ?>" min="1" max="7">
                </div>
                <div class="form-group">
                    <label>ID Producto:</label>
                    <input type="number" name="id_producto" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Cantidad:</label>
                    <input type="number" name="cantidad" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Precio Total:</label>
                    <input type="number" step="0.01" name="precio_total" class="form-control" required>
                </div>
                <button type="submit" class="btn">Registrar Venta</button>
            </form>
            <?php if (isset($resultadoPost)): ?>
                <div style="margin-top: 15px; padding: 10px; background-color: #d4edda; color: #155724; border-radius: 4px;">
                    <?= $resultadoPost !== false ? "Venta registrada exitosamente!" : "Error al registrar la venta" ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sección para Consultar Ventas (GET) -->
        <div class="form-container">
            <h2>Consultar Ventas</h2>
            <form method="get">
                <input type="hidden" name="consultar_ventas" value="1">
                <div class="form-group">
                    <label>Filtrar por Hora:</label>
                    <input type="number" name="hora" class="form-control" min="0" max="23">
                </div>
                <div class="form-group">
                    <label>Filtrar por Día (1-7):</label>
                    <input type="number" name="dia_semana" class="form-control" min="1" max="7">
                </div>
                <div class="form-group">
                    <label>Filtrar por ID Producto:</label>
                    <input type="number" name="id_producto" class="form-control">
                </div>
                <button type="submit" class="btn">Consultar</button>
            </form>

            <?php if (!empty($ventas)): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID Venta</th>
                            <th>Hora</th>
                            <th>Día</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ventas as $venta): ?>
                            <tr>
                                <td><?= $venta['venta_id'] ?? '' ?></td>
                                <td><?= $venta['hora'] ?? '' ?></td>
                                <td><?= $dias_espanol[$venta['dia_semana'] ?? 1] ?? '' ?></td>
                                <td><?= $venta['id_producto'] ?? '' ?></td>
                                <td><?= $venta['cantidad'] ?? '' ?></td>
                                <td>$<?= number_format($venta['precio_total'] ?? 0, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php elseif (isset($_GET['consultar_ventas'])): ?>
                <div style="margin-top: 15px; padding: 10px; background-color: #f8d7da; color: #721c24; border-radius: 4px;">
                    No se encontraron ventas con los filtros especificados.
                </div>
            <?php endif; ?>
        </div>

        <a href="dashboard.php" class="btn-back">
            <i class="fa fa-arrow-left"></i> Volver al Menú
        </a>
    </div>
</div>

<?php include_once "pie.php" ?>