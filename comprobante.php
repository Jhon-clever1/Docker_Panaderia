<?php
include_once "encabezado.php";
include_once "base_de_datos.php";

if(!isset($_GET["id"])) {
    header("Location: ./vender.php");
    exit;
}

$idVenta = $_GET["id"];

// Obtener datos de la venta
$sentencia = $base_de_datos->prepare("SELECT * FROM ventas WHERE id = ?");
$sentencia->execute([$idVenta]);
$venta = $sentencia->fetch(PDO::FETCH_OBJ);

if(!$venta) {
    header("Location: ./vender.php");
    exit;
}

// Obtener productos vendidos
$sentencia = $base_de_datos->prepare("
    SELECT p.descripcion, pv.cantidad, p.precioVenta, (pv.cantidad * p.precioVenta) as subtotal
    FROM productos_vendidos pv
    JOIN productos p ON pv.id_producto = p.id
    WHERE pv.id_venta = ?
");
$sentencia->execute([$idVenta]);
$productos = $sentencia->fetchAll(PDO::FETCH_OBJ);

$total = array_sum(array_column($productos, 'subtotal'));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Comprobante de Venta #<?= $venta->id ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Estilos para pantalla */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }
        
        .page-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 30px;
        }
        
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .venta-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .productos-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .productos-table th {
            background-color: #f8f9fa;
            padding: 12px;
            text-align: left;
        }
        
        .productos-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .total {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
        }
        
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 25px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-print {
            background-color: #3498db;
            color: white;
        }
        
        .btn-confirm {
            background-color: #2ecc71;
            color: white;
        }
        
        /* Estilos solo para impresión (comprobante real) */
        @media print {
            body, .page-container {
                background: none;
                padding: 0;
                margin: 0;
                box-shadow: none;
                width: 80mm; /* Ancho estándar ticket */
                font-size: 12px;
            }
            
            .page-container {
                padding: 5mm;
                border-radius: 0;
            }
            
            h1, .action-buttons {
                display: none;
            }
            
            .venta-info {
                flex-direction: column;
                margin-bottom: 10px;
            }
            
            .productos-table {
                font-size: 11px;
            }
            
            .productos-table th, 
            .productos-table td {
                padding: 3px 0;
            }
            
            .header {
                text-align: center;
                margin-bottom: 10px;
                border-bottom: 1px dashed #ccc;
                padding-bottom: 10px;
            }
            
            .logo {
                max-width: 50px;
                margin-bottom: 5px;
            }
            
            .empresa-info {
                font-size: 10px;
                margin-bottom: 5px;
            }
            
            .footer {
                text-align: center;
                font-size: 9px;
                margin-top: 10px;
                padding-top: 10px;
                border-top: 1px dashed #ccc;
            }
            
            @page {
                margin: 0;
                size: auto;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <!-- Vista normal en navegador -->
        <div class="screen-view">
            <h1><i class="fas fa-receipt"></i> Comprobante de Venta</h1>
            
            <div class="venta-info">
                <div>
                    <p><strong>N° Venta:</strong> <?= str_pad($venta->id, 6, '0', STR_PAD_LEFT) ?></p>
                    <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($venta->fecha)) ?></p>
                </div>
                <div>
                    <p><strong>Atendido por:</strong> <?= $_SESSION['usuario'] ?? 'Sistema' ?></p>
                </div>
            </div>
            
            <table class="productos-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>P. Unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($productos as $producto): ?>
                    <tr>
                        <td><?= htmlspecialchars($producto->descripcion) ?></td>
                        <td><?= $producto->cantidad ?></td>
                        <td>S/ <?= number_format($producto->precioVenta, 2) ?></td>
                        <td>S/ <?= number_format($producto->subtotal, 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="total">
                <p>TOTAL: S/ <?= number_format($total, 2) ?></p>
            </div>
            
            <div class="action-buttons">
                <button onclick="window.print();" class="btn btn-print">
                    <i class="fas fa-print"></i> Imprimir Comprobante
                </button>
                <a href="vender.php" class="btn btn-confirm">
                    <i class="fas fa-check-circle"></i> Confirmar sin Imprimir
                </a>
            </div>
        </div>
        
        <!-- Vista para impresión (comprobante real) -->
        <div class="print-view">
            <div class="header">
                <img src="imagenes/Pan.jpg" alt="Logo" class="logo">
                <div class="empresa-info">
                    <strong>Panadería "El Buen Pan"</strong><br>
                    Camino antiguo a Coatepec, Bosque Briones #20<br>
                    Tel: 9502343562 | RFC: PBA123456XYZ
                </div>
            </div>

            <div style="text-align: center; margin: 10px 0;">
                <strong>TICKET DE VENTA</strong><br>
                #<?= str_pad($venta->id, 6, '0', STR_PAD_LEFT) ?>
            </div>
            
            <div class="venta-info">
                <div><strong>Ticket:</strong> #<?= str_pad($venta->id, 6, '0', STR_PAD_LEFT) ?></div>
                <div><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($venta->fecha)) ?></div>
                <div><strong>Atendió:</strong> <?= substr($_SESSION['usuario'] ?? 'Sistema', 0, 15) ?></div>
            </div>
            
            <table class="productos-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cant</th>
                        <th>P.U.</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($productos as $producto): ?>
                    <tr>
                        <td><?= substr(htmlspecialchars($producto->descripcion), 0, 20) ?></td>
                        <td><?= $producto->cantidad ?></td>
                        <td>S/<?= number_format($producto->precioVenta, 2) ?></td>
                        <td>S/<?= number_format($producto->subtotal, 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="total" style="text-align: right; font-weight: bold; margin-top: 10px;">
                TOTAL: S/<?= number_format($total, 2) ?>
            </div>
            
            <div class="footer">
                <div style="text-align: center; margin-top: 10px;">
                    <strong>¡GRACIAS POR SU COMPRA!</strong><br>
                    <?= date('d/m/Y H:i:s') ?>
                </div>
                <?php if($total > 500): ?>
                <div style="text-align: center; margin-top: 5px; font-size: 0.8em;">
                    (Este ticket es válido como factura simplificada)
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Asegurar que solo se muestre la vista adecuada
        document.addEventListener('DOMContentLoaded', function() {
            const printView = document.querySelector('.print-view');
            printView.style.display = 'none';
            
            // Al imprimir, cambiar las vistas
            window.matchMedia('print').addListener((mql) => {
                if (mql.matches) {
                    document.querySelector('.screen-view').style.display = 'none';
                    printView.style.display = 'block';
                } else {
                    document.querySelector('.screen-view').style.display = 'block';
                    printView.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>

<?php include_once "pie.php"; ?>