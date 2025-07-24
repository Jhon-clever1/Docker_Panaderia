<?php
// URL base de la API FastAPI
$base_url = "http://10.55.114.186:8000";

// Endpoint: GET /kmeans/clusterizados
$url = "$base_url/kmeans/clusterizados";

// Realizar la solicitud GET
$response = file_get_contents($url);

// Verificar si hubo respuesta
if ($response === FALSE) {
    echo "<h2>Error al conectar con la API</h2>";
} else {
    // Decodificar y mostrar los resultados
    $resultados = json_decode($response, true);

    echo "<h2>Resultados de Clustering (GET /kmeans/clusterizados)</h2>";
    echo "<pre>";
    print_r($resultados);
    echo "</pre>";
}
?>
