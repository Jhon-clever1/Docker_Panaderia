<?php
require('../fpdf/fpdf.php');
require_once __DIR__ . '/../base_de_datos.php';

// Consulta para obtener los productos más vendidos
$sql = "SELECT p.id, p.descripcion, SUM(pv.cantidad) as total_vendido 
        FROM productos_vendidos pv 
        JOIN productos p ON pv.id_producto = p.id 
        GROUP BY pv.id_producto 
        ORDER BY total_vendido DESC";

$productos_vendidos = $base_de_datos->query($sql)->fetchAll(PDO::FETCH_OBJ);

class PDF extends FPDF {
    // Cabecera de página
    function Header() {
        // Logo
        $this->Image('../imagenes/Pan.jpg', 10, 8, 25);
        
        // Título principal
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(44, 62, 80);
        $this->Cell(0, 10, 'PRODUCTOS MAS VENDIDOS', 0, 1, 'C');
        
        // Información de la empresa
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(106, 115, 125);
        $this->Cell(0, 5, 'Panaderia Artesanal "El Buen Pan"', 0, 1, 'C');
        $this->Cell(0, 5, 'Fecha del reporte: ' . date('d/m/Y H:i:s'), 0, 1, 'C');
        
        // Línea decorativa
        $this->SetDrawColor(52, 152, 219);
        $this->SetLineWidth(0.5);
        $this->Line(10, 35, 200, 35);
        $this->Ln(15);
    }

    // Pie de página
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(106, 115, 125);
        $this->Cell(0, 10, 'Página ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // Gráfica de barras simple
    function BarChart($data, $max_value) {
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(44, 62, 80);
        
        $bar_width = 8; // Ancho de cada barra
        $x = 50; // Posición X inicial
        $y = $this->GetY() + 10; // Posición Y inicial
        $height_scale = 50 / $max_value; // Escala para la altura
        
        foreach($data as $row) {
            // Nombre del producto (abreviado si es muy largo)
            $nombre = strlen($row->descripcion) > 15 ? substr($row->descripcion, 0, 12).'...' : $row->descripcion;
            $this->Text($x-5, $y + 60, $nombre);
            
            // Barra
            $bar_height = $row->total_vendido * $height_scale;
            $this->SetFillColor(52, 152, 219);
            $this->Rect($x, $y + (50 - $bar_height), $bar_width, $bar_height, 'F');
            
            // Valor
            $this->Text($x-5, $y + (50 - $bar_height) - 5, $row->total_vendido);
            
            $x += 20; // Espacio entre barras
        }
        
        // Eje Y
        $this->Line(45, $y, 45, $y + 50);
        // Eje X
        $this->Line(45, $y + 50, $x, $y + 50);
        
        $this->SetY($y + 70);
    }
}

// Creación del PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// Encabezados de la tabla
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(52, 152, 219);
$pdf->SetTextColor(255);
$pdf->Cell(30, 10, 'ID', 1, 0, 'C', true);
$pdf->Cell(100, 10, 'Producto', 1, 0, 'C', true);
$pdf->Cell(60, 10, 'Total Vendido', 1, 1, 'C', true);

// Datos de la tabla
$pdf->SetTextColor(44, 62, 80);
$pdf->SetFont('Arial', '', 10);

$max_value = 0;
$chart_data = [];

foreach($productos_vendidos as $producto) {
    $pdf->Cell(30, 8, $producto->id, 1, 0, 'C');
    $pdf->Cell(100, 8, utf8_decode($producto->descripcion), 1, 0, 'L');
    $pdf->Cell(60, 8, $producto->total_vendido, 1, 1, 'C');
    
    // Preparamos datos para la gráfica (top 5)
    if(count($chart_data) < 5) {
        $chart_data[] = $producto;
        if($producto->total_vendido > $max_value) {
            $max_value = $producto->total_vendido;
        }
    }
}

// Agregamos espacio antes de la gráfica
$pdf->Ln(15);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Top 5 Productos Mas Vendidos', 0, 1, 'C');

// Dibujamos la gráfica
$pdf->BarChart($chart_data, $max_value);

// Pie adicional
$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 8);
$pdf->SetTextColor(106, 115, 125);
$pdf->Cell(0, 5, utf8_decode('* Reporte generado automáticamente por el sistema'), 0, 0, 'C');

$pdf->Output('productos_mas_vendidos.pdf', 'I');
?>