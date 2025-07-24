<?php
include_once "base_de_datos.php";
include_once "AprioriPHP/Apriori.php";

header('Content-Type: text/html; charset=utf-8');

// 1. Obtener productos del carrito
$productosCarrito = isset($_POST['productos']) ? json_decode($_POST['productos'], true) : [];

if(empty($productosCarrito)) {
    echo '<div class="carrito-vacio">
            <i class="fas fa-shopping-basket"></i>
            <p>Agrega productos al carrito para ver recomendaciones</p>
          </div>';
    exit;
}

// 2. Obtener todas las transacciones históricas
try {
    $sql = "SELECT v.id, GROUP_CONCAT(p.id SEPARATOR ',') AS productos_ids 
            FROM ventas v
            JOIN productos_vendidos pv ON v.id = pv.id_venta
            JOIN productos p ON pv.id_producto = p.id
            GROUP BY v.id
            HAVING COUNT(pv.id_producto) > 1";

    $sentencia = $base_de_datos->prepare($sql);
    $sentencia->execute();
    $transacciones = $sentencia->fetchAll(PDO::FETCH_OBJ);

    // 3. Preparar datos para Apriori
    $transacciones_array = array();
    foreach($transacciones as $trans) {
        $transacciones_array[] = explode(',', $trans->productos_ids);
    }

    // 4. Ejecutar Apriori
    $apriori = new Apriori();
    $apriori->setMinSup(0.1);  // Soporte mínimo (10%)
    $apriori->setMinConf(0.3); // Confianza mínima (30%)
    $apriori->process($transacciones_array);

    // 5. Obtener reglas relevantes
    // 5. Obtener reglas relevantes con validación
    $reglasRecomendaciones = array();
    $rules = $apriori->getRules();

    if(is_array($rules)) {
        foreach($rules as $regla) {
            // Verificar que la regla tenga la estructura esperada
            if(!isset($regla['antecedent'], $regla['consequent'], $regla['confidence'], $regla['lift'])) {
                continue; // Saltar reglas mal formadas
            }

            // Verificar si los productos base están en el carrito
            $interseccion = array_intersect($regla['antecedent'], $productosCarrito);
            
            if(count($interseccion) > 0) {
                // Excluir productos que ya están en el carrito
                $recomendaciones = array_diff($regla['consequent'], $productosCarrito);
                
                if(count($recomendaciones) > 0) {
                    $reglasRecomendaciones[] = [
                        'productos' => $recomendaciones,
                        'confianza' => $regla['confidence'],
                        'lift' => $regla['lift']
                    ];
                }
            }
        }
    } else {
        echo '<div class="alert alert-warning">No se pudieron generar reglas de asociación</div>';
        exit;
    }

    // 6. Ordenar por lift (las mejores recomendaciones primero)
    usort($reglasRecomendaciones, function($a, $b) {
        return $b['lift'] <=> $a['lift'];
    });

    // 7. Obtener detalles de los productos recomendados
    $productosRecomendados = array();
    foreach($reglasRecomendaciones as $regla) {
        foreach($regla['productos'] as $productoId) {
            if(!isset($productosRecomendados[$productoId])) {
                $productosRecomendados[$productoId] = $regla;
            }
        }
    }

    // Limitar a 6 recomendaciones
    $productosRecomendados = array_slice($productosRecomendados, 0, 6, true);

    // 8. Si no hay recomendaciones
    if(empty($productosRecomendados)) {
        echo '<div class="alert alert-info">
                No encontramos recomendaciones para los productos en tu carrito.
                Prueba agregando diferentes productos.
              </div>';
        exit;
    }

    // 9. Obtener información detallada de los productos
    $placeholders = str_repeat('?,', count($productosRecomendados) - 1) . '?';
    $sql = "SELECT id, codigo, descripcion, precioVenta, existencia 
            FROM productos 
            WHERE id IN ($placeholders)";
    
    $sentencia = $base_de_datos->prepare($sql);
    $sentencia->execute(array_keys($productosRecomendados));
    $productos = $sentencia->fetchAll(PDO::FETCH_OBJ);

    // 10. Mostrar las recomendaciones
    echo '<div class="recomendaciones-grid">';
    
    foreach($productos as $producto) {
        $regla = $productosRecomendados[$producto->id];
        $confianzaPorcentaje = isset($regla['confidence']) ? round($regla['confidence'] * 100, 1) : 0;
        
        echo '<div class="recomendacion-card">
                <div class="recomendacion-nombre" title="'.htmlspecialchars($producto->descripcion).'">
                    '.htmlspecialchars($producto->descripcion).'
                </div>
                <div class="recomendacion-precio">
                    S/'.number_format($producto->precioVenta, 2).'
                </div>
                <div class="recomendacion-stock">
                    <small>Stock: '.$producto->existencia.'</small>
                </div>
                <div class="recomendacion-stock">
                    <small>Confianza: '.$confianzaPorcentaje.'%</small>
                </div>
                <button onclick="agregarProducto('.$producto->id.', 1)" 
                        class="btn btn-sm btn-primary recomendacion-btn">
                    <i class="fas fa-cart-plus"></i> Agregar
                </button>
              </div>';
    }
    
    echo '</div>';

} catch(Exception $e) {
    echo '<div class="alert alert-danger">
            Error al generar recomendaciones: '.htmlspecialchars($e->getMessage()).'
          </div>';
    error_log("Error en obtener_recomendaciones.php: " . $e->getMessage());
}
?>