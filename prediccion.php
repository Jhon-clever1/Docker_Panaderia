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

// 2. Función para obtener conteo de clusters durante el día
function obtenerConteoClusters($dia_numero) {
    $conteo = array_fill(0, 10, 0); // Inicializar conteo para 10 clusters
    $url = "http://flask_api:5000/predict";
    
    for ($hora = 0; $hora < 24; $hora++) {
        $body = json_encode(["hora" => $hora, "dia_semana" => $dia_numero]);
        $opts = [
            "http" => [
                "header" => "Content-Type: application/json",
                "method" => "POST",
                "content" => $body
            ]
        ];
        
        $ctx = stream_context_create($opts);
        $response = @file_get_contents($url, false, $ctx);
        
        if ($response !== false) {
            $data = json_decode($response, true);
            $cluster = $data["cluster"];
            $conteo[$cluster]++;
        }
    }
    
    return $conteo;
}

$conteoClusters = obtenerConteoClusters($dia_numero);

// 3. Llamar a la API Flask para la hora actual
$url = "http://flask_api:5000/predict";
$body = json_encode(["hora" => $hora, "dia_semana" => $dia_numero]);

$opts = [
    "http" => [
        "header" => "Content-Type: application/json",
        "method" => "POST",
        "content" => $body
    ]
];

$ctx = stream_context_create($opts);
$response = @file_get_contents($url, false, $ctx);

$cluster = null;
$producto = "No disponible";

if ($response !== false) {
    $data = json_decode($response, true);
    $cluster = $data["cluster"];

    // 4. Relacionar cada cluster con un producto
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
    /* Estilos existentes */
    .container {
        max-width: 95%;
        margin-top: 20px;
    }
    
    h1 {
        color: #000000ff;
        font-weight: 600;
        margin-bottom: 20px;
    }
    
    .card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        padding: 30px;
        margin-top: 20px;
    }
    
    .result-item {
        padding: 15px 0;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .result-item:last-child {
        border-bottom: none;
    }
    
    .result-item strong {
        color: #b65d09d1;
        min-width: 200px;
        display: inline-block;
    }
    
    .highlight {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin: 20px 0;
        border-left: 4px solid #b65d09d1;
    }
    
    .highlight strong {
        font-size: 1.2em;
        color: #b65d09d1;
    }
    
    .btn-back {
        border-radius: 8px;
        font-weight: 500;
        padding: 10px 20px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        background-color: #b65d09d1;
        color: white;
        text-decoration: none;
        display: inline-block;
        margin-top: 20px;
    }
    
    .btn-back:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        background-color: #a55208;
        color: white;
    }
    
    .error-message {
        color: #e74a3b;
        background-color: #fdecea;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid #e74a3b;
    }
</style>

<div class="container py-5">
    <div class="col-xs-12">
        <h1>Predicción de Ventas</h1>
        
        <div class="dashboard-container">
            <!-- Tarjeta de recomendación (izquierda) -->
            <div class="card recommendation-card">
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
                            <span style="color: <?= $colores[$cluster] ?? '#333' ?>; font-weight: bold;">
                                <?= $producto ?>
                            </span>
                        </div>
                        
                        <p style="margin-top: 15px; font-style: italic;">
                            Basado en el análisis de patrones de compra, este es el producto con mayor probabilidad de venta en este momento.
                        </p>
                    </div>
                <?php else: ?>
                    <div class="error-message">
                        <p>Error al contactar el servicio de predicción. Por favor intente más tarde.</p>
                    </div>
                <?php endif; ?>
                
                <a href="dashboard.php" class="btn-back">
                    <i class="fa fa-arrow-left"></i> Volver al Menú
                </a>
            </div>
            
        </div>
    </div>
</div>

<?php include_once "pie.php" ?>