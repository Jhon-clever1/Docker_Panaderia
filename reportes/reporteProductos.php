<?php
require('../fpdf/fpdf.php');

$servidor="db";
$usuario="panaderia";
$password="pan12345";
$bd="ventas";

$conexion=mysqli_connect($servidor, 
                        $usuario, 
                        $password, 
                        $bd);

$sql="SELECT prod.codigo,
                prod.descripcion,
                prod.precioVenta,
                prod.existencia
        from productos as prod ";

$result=mysqli_query($conexion,$sql);

class PDF extends FPDF{

    // Cabecera de página
    function Header(){
        // Logo
        $this->Image('../imagenes/Pan.jpg',10,8,25);
        
        // Título principal
        $this->SetFont('Arial','B',18);
        $this->SetTextColor(44, 62, 80); // Azul oscuro
        $this->Cell(80);
        $this->Cell(30,10,'REPORTE DE PRODUCTOS',0,0,'C');
        $this->Ln(12);
        
        // Información de la empresa
        $this->SetFont('Arial','',10);
        $this->SetTextColor(106, 115, 125); // Gris
        $this->Cell(0,5,'Panaderia Artesanal "El Buen Pan"',0,1,'C');
        $this->Cell(0,5,'Camino antiguo a Coatepec, Bosque Briones #20',0,1,'C');
        $this->Cell(0,5,'Tel: 228 123 4567 | RFC: PBA123456XYZ',0,1,'C');
        
        // Fecha del reporte
        $this->Ln(5);
        $this->SetFont('Arial','B',10);
        $this->SetTextColor(44, 62, 80);
        $this->Cell(160);
        $this->Cell(30,5,'Fecha: '.date('d/m/Y'),0,1,'R');
        
        // Línea decorativa
        $this->SetDrawColor(52, 152, 219); // Azul
        $this->SetLineWidth(0.5);
        $this->Line(10,47,200,47);
        $this->Ln(20);
    }

    // Pie de página
    function Footer(){
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        $this->SetTextColor(106, 115, 125); // Gris
        // Número de página
        $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
    }

    // Tabla mejorada
    function ImprovedTable($header, $data) {
        // Colores, ancho de línea y fuente
        $this->SetFillColor(52, 152, 219); // Azul
        $this->SetTextColor(255); // Blanco
        $this->SetDrawColor(44, 62, 80); // Azul oscuro
        $this->SetLineWidth(0.3);
        $this->SetFont('Arial','B',12);
        
        // Anchuras de las columnas
        $w = array(25, 80, 30, 30);
        
        // Cabeceras
        $this->Cell(25); // Margen izquierdo
        for($i=0;$i<count($header);$i++) {
            $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
        }
        $this->Ln();
        
        // Restauración de colores y fuente
        $this->SetFillColor(245, 245, 245); // Gris claro
        $this->SetTextColor(44, 62, 80); // Azul oscuro
        $this->SetFont('Arial','',10);
        
        // Datos
        $fill = false;
        foreach($data as $row) {
            $this->Cell(25); // Margen izquierdo
            $this->Cell($w[0],6,$row['codigo'],'LR',0,'C',$fill);
            $this->Cell($w[1],6,utf8_decode($row['descripcion']),'LR',0,'L',$fill);
            $this->Cell($w[2],6,'S/'.number_format($row['precioVenta'],2),'LR',0,'R',$fill);
            $this->Cell($w[3],6,$row['existencia'],'LR',0,'C',$fill);
            $this->Ln();
            $fill = !$fill;
        }
        
        // Línea de cierre
        $this->Cell(25);
        $this->Cell(array_sum($w),0,'','T');
    }
}

// Creación del PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// Encabezados de columna
$header = array('CODIGO', 'DESCRIPCION', 'PRECIO', 'EXISTENCIA');

// Cargar los datos
$data = array();
while($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Mostrar la tabla mejorada
$pdf->ImprovedTable($header,$data);

// Pie adicional
$pdf->Ln(10);
$pdf->SetFont('Arial','I',9);
$pdf->SetTextColor(106, 115, 125);
$pdf->Cell(0,5,'* Reporte generado automaticamente el '.date('d/m/Y H:i:s'),0,0,'C');

$pdf->Output();
?>