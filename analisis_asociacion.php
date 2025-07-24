<?php
include_once "encabezado.php";
include_once "base_de_datos.php";
include_once "control_acceso.php";

// Verificar permisos
if(!esAdministrador()) {
    header("Location: dashboard.php");
    exit;
}

// Obtener parámetros de fecha
$fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-d', strtotime('-1 month'));
$fechaFin = $_GET['fecha_fin'] ?? date('Y-m-d');
$min_support = $_GET['min_support'] ?? 0.1;
$min_confidence = $_GET['min_confidence'] ?? 0.5;

// Validar fechas
if(strtotime($fechaInicio) > strtotime($fechaFin)) {
    $temp = $fechaInicio;
    $fechaInicio = $fechaFin;
    $fechaFin = $temp;
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

.filtro-fechas {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.reglas-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.reglas-table th, .reglas-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.reglas-table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.reglas-table tr:hover {
    background-color: #f5f5f5;
}

.badge-pico {
    background-color: #b65d09d1;
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 1.2em;
}
</style>

<div class="analisis-container">
    <h2><i class="fas fa-project-diagram"></i> Análisis de Asociación entre Productos</h2>
    
    <!-- Filtros -->
    <div class="filtro-fechas">
        <form method="get" class="row">
            <div class="col-md-3">
                <label>Fecha Inicio:</label>
                <input type="date" name="fecha_inicio" value="<?= $fechaInicio ?>" class="form-control">
            </div>
            <div class="col-md-3">
                <label>Fecha Fin:</label>
                <input type="date" name="fecha_fin" value="<?= $fechaFin ?>" class="form-control">
            </div>
            <div class="col-md-2">
                <label>Soporte Mínimo:</label>
                <input type="number" name="min_support" value="<?= $min_support ?>" step="0.01" min="0.01" max="1" class="form-control">
            </div>
            <div class="col-md-2">
                <label>Confianza Mínima:</label>
                <input type="number" name="min_confidence" value="<?= $min_confidence ?>" step="0.01" min="0.01" max="1" class="form-control">
            </div>
            <div class="col-md-2" style="margin-top: 28px;">
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-filter"></i> Analizar
                </button>
            </div>
        </form>
    </div>
    
    <?php
    // Obtener transacciones de la base de datos
    $sql = "SELECT v.id, GROUP_CONCAT(p.descripcion SEPARATOR ', ') AS productos 
            FROM ventas v
            JOIN productos_vendidos pv ON v.id = pv.id_venta
            JOIN productos p ON pv.id_producto = p.id
            WHERE DATE(v.fecha) BETWEEN ? AND ?
            GROUP BY v.id
            HAVING COUNT(pv.id_producto) > 1";
    
    $sentencia = $base_de_datos->prepare($sql);
    $sentencia->execute([$fechaInicio, $fechaFin]);
    $transacciones = $sentencia->fetchAll(PDO::FETCH_OBJ);
    
    if(count($transacciones) > 0) {
        // Preparar datos para Apriori
        $transacciones_array = array();
        foreach($transacciones as $trans) {
            $transacciones_array[] = explode(', ', $trans->productos);
        }
        
        // Ejecutar Apriori (implementación PHP)
        require_once 'AprioriPHP/Apriori.php';
        
        $apriori = new Apriori();
        $apriori->setMaxScan(20);       // Número máximo de escaneos
        $apriori->setMinSup($min_support);  // Soporte mínimo
        $apriori->setMinConf($min_confidence); // Confianza mínima
        $apriori->setDelimiter(',');    // Delimitador
        
        $apriori->process($transacciones_array);
        
        // Obtener resultados
        $reglas = $apriori->getRules();
        
        if(count($reglas) > 0) {
            echo '<div class="alert alert-success">Se encontraron '.count($reglas).' reglas de asociación</div>';
            
            echo '<table class="reglas-table">';
            echo '<thead><tr>
                    <th>Productos Base</th>
                    <th>Productos Asociados</th>
                    <th>Soporte</th>
                    <th>Confianza</th>
                    <th>Lift</th>
                    <th>Acciones</th>
                  </tr></thead>';
            echo '<tbody>';
            
            foreach($reglas as $regla) {
                echo '<tr>';
                echo '<td>'.implode(', ', $regla['antecedent']).'</td>';
                echo '<td>'.implode(', ', $regla['consequent']).'</td>';
                echo '<td>'.round($regla['support']*100, 2).'%</td>';
                echo '<td>'.round($regla['confidence']*100, 2).'%</td>';
                echo '<td>'.round($regla['lift'], 2).'</td>';
                echo '<td>
                        <button class="btn btn-sm btn-primary" onclick="crearPromocion(\''.implode(',', $regla['antecedent']).'\', \''.implode(',', $regla['consequent']).'\')">
                            <i class="fas fa-tags"></i> Crear promoción
                        </button>
                      </td>';
                echo '</tr>';
            }
            
            echo '</tbody></table>';
        } else {
            echo '<div class="alert alert-warning">No se encontraron reglas de asociación con los parámetros actuales</div>';
        }
    } else {
        echo '<div class="alert alert-info">No hay suficientes transacciones para analizar en el período seleccionado</div>';
    }
    ?>
</div>

<script>
function crearPromocion(productosBase, productosAsociados) {
    // Implementar lógica para crear promoción basada en la regla
    alert('Creando promoción para: ' + productosBase + ' → ' + productosAsociados);
    // Aquí podrías hacer una llamada AJAX o redireccionar a un formulario de creación de promoción
}
</script>

<?php include_once "pie.php"; ?>