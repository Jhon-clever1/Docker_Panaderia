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
		$this->Image('../imagenes/logo.png',10,5,30);

		// Arial bold 15
		$this->SetFont('Arial','B',18);

		// Movernos a la derecha
		$this->Cell(70);

		// Título
		$this->Cell(70,10,'Productos registrados',0,0,'C');

		// Salto de línea

		//Dirección
		$this->Ln();
		$this->SetFont('Times','',15);
		$this->Cell(55);
		$this->Cell(70, 10,'Camino antiguo a Coatepec, Bosque Briones #20', 0,1,'');

		//Fecha
		$this->Ln(10);
		$this->Cell(145);
		$this->SetFont('Arial','B',14);
		$this->Cell(20, 10,'Fecha:', 0,0,'');
		$this->SetFont('Arial','',14);
		$this->Cell(20, 10,date('d/m/Y'), 0,1,'');
		$this->Ln(10);


	}

	// Pie de página
	function Footer(){

		// Posición: a 1,5 cm del final
		$this->SetY(-15);
		// Arial italic 8
		$this->SetFont('Arial','I',8);
		// Número de página
		$this->Cell(0,10,utf8_decode('Página').$this->PageNo().'/{nb}',0,0,'C');

	}
}

	// Creación del objeto de la clase heredada

	$pdf = new PDF();
	$pdf->AliasNbPages();
    $pdf->AddPage();
    
    $pdf->Cell(30);

	$pdf->SetFillColor(232,232,232);
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(20,6,'Codigo',1,0,'C',1);
    $pdf->Cell(60,6,'Nombre ',1,0,'C',1);
    $pdf->Cell(30,6,'Precio ',1,0,'C',1);
	$pdf->Cell(30,6,'Existencia',1,1,'C',1);
	
    $pdf->SetFont('Arial','',10);
    

	while($row = $result->fetch_assoc())
	{
        $pdf->Cell(30);
		$pdf->Cell(20,6,utf8_decode($row['codigo']),1,0,'C');
        $pdf->Cell(60,6,utf8_decode($row['descripcion']),1,0,'C');
        $pdf->Cell(30,6,utf8_decode($row['precioVenta']),1,0,'C');
		$pdf->Cell(30,6,utf8_decode($row['existencia']),1,1,'C');
	}
	$pdf->Output();

?>