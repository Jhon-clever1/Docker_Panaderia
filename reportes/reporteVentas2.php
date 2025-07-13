<?php
require('../fpdf/fpdf.php');

$servidor = "db";
$usuario = "panaderia";
$password = "pan12345";
$bd = "ventas";

$conexion = mysqli_connect($servidor, $usuario, $password, $bd);

$sql = "SELECT pv.cantidad,
            prod.codigo,
            prod.descripcion,
            prod.precioVenta,
            ve.id,
            ve.fecha,
            (pv.cantidad * prod.precioVenta) as multiplicacion
        FROM productos_vendidos as pv 
        INNER JOIN productos as prod ON pv.id_producto=prod.id
        INNER JOIN ventas as ve ON pv.id_venta=ve.id";

$result = mysqli_query($conexion, $sql);

$variable = "SELECT SUM(total) as totalVentas FROM ventas";
$consulta = mysqli_query($conexion, $variable);

class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        // Logo
        $this->Image('../imagenes/Pan.jpg', 10, 8, 25);
        
        // Título principal
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(44, 62, 80); // Azul oscuro
        $this->Cell(0, 10, 'REPORTE DE VENTAS', 0, 1, 'C');
        
        // Información de la empresa
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(106, 115, 125); // Gris
        $this->Cell(0, 5, 'Panaderia Artesanal "El Buen Pan"', 0, 1, 'C');
        $this->Cell(0, 5, 'Camino antiguo a Coatepec, Bosque Briones #20', 0, 1, 'C');
        
        // Fecha del reporte
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(44, 62, 80);
        $this->Cell(0, 5, 'Fecha del reporte: ' . date('d/m/Y H:i:s'), 0, 1, 'R');
        
        // Línea decorativa
        $this->SetDrawColor(52, 152, 219); // Azul
        $this->SetLineWidth(0.5);
        $this->Line(10, 40, 200, 40);
        $this->Ln(10);
    }

    // Pie de página
    function Footer()
    {
        // Posición: a 1.5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(106, 115, 125); // Gris
        // Número de página
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // Tabla mejorada
    function ImprovedTable($header, $data)
    {
        // Colores, ancho de línea y fuente
        $this->SetFillColor(52, 152, 219); // Azul
        $this->SetTextColor(255); // Blanco
        $this->SetDrawColor(44, 62, 80); // Azul oscuro
        $this->SetLineWidth(0.3);
        $this->SetFont('Arial', 'B', 10);
        
        // Anchuras de las columnas
        $w = array(15, 20, 50, 20, 20, 25, 40);
        
        // Cabeceras
        $this->Cell(10); // Margen izquierdo
        for($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
        }
        $this->Ln();
        
        // Restauración de colores y fuente
        $this->SetFillColor(245, 245, 245); // Gris claro
        $this->SetTextColor(44, 62, 80); // Azul oscuro
        $this->SetFont('Arial', '', 9);
        
        // Datos
        $fill = false;
        foreach($data as $row) {
            $this->Cell(10); // Margen izquierdo
            $this->Cell($w[0], 6, $row['id'], 'LR', 0, 'C', $fill);
            $this->Cell($w[1], 6, $row['codigo'], 'LR', 0, 'C', $fill);
            $this->Cell($w[2], 6, utf8_decode($row['descripcion']), 'LR', 0, 'L', $fill);
            $this->Cell($w[3], 6, 'S/' . number_format($row['precioVenta'], 2), 'LR', 0, 'R', $fill);
            $this->Cell($w[4], 6, $row['cantidad'], 'LR', 0, 'C', $fill);
            $this->Cell($w[5], 6, 'S/' . number_format($row['multiplicacion'], 2), 'LR', 0, 'R', $fill);
            $this->Cell($w[6], 6, $row['fecha'], 'LR', 0, 'C', $fill);
            $this->Ln();
            $fill = !$fill;
        }
        
        // Línea de cierre
        $this->Cell(10);
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}

// Creación del PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// Encabezados de columna
$header = array('ID', utf8_decode('Código'), utf8_decode('Descripción'), 'Precio U.', 'Cantidad', 'Total', 'Fecha');

// Cargar los datos
$data = array();
while($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Mostrar la tabla mejorada
$pdf->ImprovedTable($header, $data);

// Total de ventas
$pdf->Ln(10);
while($col = $consulta->fetch_assoc()) {
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(52, 152, 219); // Azul
    $pdf->SetTextColor(255); // Blanco
    $pdf->Cell(140, 8, 'TOTAL GENERAL DE VENTAS:', 1, 0, 'R', true);
    $pdf->Cell(40, 8, 'S/' . number_format($col['totalVentas'], 2), 1, 1, 'R', true);
}

// Pie adicional
$pdf->Ln(5);
$pdf->SetFont('Arial', 'I', 8);
$pdf->SetTextColor(106, 115, 125);
$pdf->Cell(0, 5, utf8_decode('* Reporte generado automáticamente por el sistema'), 0, 0, 'C');

$pdf->Output('ventas.pdf', 'I');
?>