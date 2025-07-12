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

$sql="SELECT ve.id,
				ve.fecha,
                ve.total
		from ventas as ve ";

$result=mysqli_query($conexion,$sql);

$variable = "SELECT SUM(total) as totalVentas from ventas";
$consulta = mysqli_query($conexion, $variable);


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
    
    $pdf->Cell(40);

	$pdf->SetFillColor(232,232,232);
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(20,6,'ID',1,0,'C',1);
    $pdf->Cell(60,6,'Fecha Venta ',1,0,'C',1);
	$pdf->Cell(30,6,'Total',1,1,'C',1);
	
    $pdf->SetFont('Arial','',10);
    

	while($row = $result->fetch_assoc())
	{
        $pdf->Cell(40);
		$pdf->Cell(20,6,utf8_decode($row['id']),1,0,'C');
        $pdf->Cell(60,6,utf8_decode($row['fecha']),1,0,'C');
		$pdf->Cell(30,6,utf8_decode($row['total']),1,1,'C');
    }
    
    $pdf->Ln(25);

    while($col = $consulta->fetch_assoc()){
        $pdf->SetFillColor(232,232,232,);
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(70);
        $pdf->Cell(45,6,'Total de las ventas',0,0,'C',1);
        $pdf->Cell(45,6,utf8_decode($col['totalVentas']),0,1,'C'); 
    }  
	$pdf->Output();

?>