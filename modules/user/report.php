<?php
ob_start(); // ✅ منع أي output قبل الـ PDF
require_once __DIR__ . '/../../includes/auth.php';
$connect = hms_db_connect();
if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

require_once __DIR__ . '/../../libs/fpdf/fpdf.php';


class PDF extends FPDF
{
    function Header()
    {
        $this->Image('../../assets/images/echol.png', 10, 6, 20);
        $this->SetFont('Arial', 'B', 12);

        $pageWidth = $this->GetPageWidth();
        $echoText = 'ECHO Medical Report';
        $echoTextWidth = $this->GetStringWidth($echoText);
        $echoTextX = ($pageWidth - $echoTextWidth) / 2;

        $patientText = 'Patient Report';
        $patientTextWidth = $this->GetStringWidth($patientText);
        $patientTextX = ($pageWidth - $patientTextWidth) / 2;

        $this->SetXY($echoTextX, 10);
        $this->Cell($echoTextWidth, 10, $echoText, 0, 1, 'C');

        $this->SetXY($patientTextX, 20);
        $this->Cell($patientTextWidth, 10, $patientText, 0, 1, 'C');

        $this->Ln(20);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'page ' . $this->PageNo(), 0, 0, 'C');
    }

    function CreateTable($header, $data)
    {
        $widths = array(26, 40, 39, 48, 38);

        $this->SetFont('Arial', 'B', 12);
        foreach ($header as $i => $col) {
            $this->Cell($widths[$i], 10, $col, 1, 0, 'C');
        }
        $this->Ln();

        $this->SetFont('Arial', '', 12);
        foreach ($data as $row) {
            foreach ($row as $i => $col) {
                $this->Cell($widths[$i], 10, $col, 1, 0, 'C');
            }
            $this->Ln();
        }
    }

    function AddDetails($treatment, $report, $Scan)
    {
        $this->Ln(10);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Treatment:', 0, 1);
        $this->SetFont('Arial', '', 12);
        $this->MultiCell(0, 10, $treatment);
        $this->Ln(10);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Scan:', 0, 1);
        $this->SetFont('Arial', '', 12);
        $this->MultiCell(0, 10, $Scan);
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Report:', 0, 1);
        $this->SetFont('Arial', '', 12);
        $this->MultiCell(0, 10, $report);
    }
}

$header = array('ID', 'Patient Name', 'Reservation Date', 'Doctor', 'Reservation');

// ✅ Secure token: decrypt the ref parameter
require_once __DIR__ . '/../../includes/secure-token.php';
$id = 0;
if (!empty($_GET['ref'])) {
    $id = hms_decrypt_id($_GET['ref']);
    if ($id === null) { die("Invalid or tampered link."); }
} elseif (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $ref = hms_encrypt_id($id);
    header("Location: report.php?ref=" . urlencode($ref));
    exit();
}

if ($id === 0) {
    die("Invalid parameters.");
}

$sql = mysqli_query($connect, "SELECT doctors.doctorName as docname, appointment.* FROM appointment JOIN doctors ON doctors.id = appointment.doctorId WHERE apid = $id");
if ($sql && mysqli_num_rows($sql) > 0) {
    $row = mysqli_fetch_assoc($sql);
    $data = array(
        array($row['apid'], $row['patient_Name'], $row['appointmentDate'], $row['docname'], $row['doctorSpecialization']),
    );
} else {
    die("Appointment not found.");
}

// ✅ الأسماء الصح من الـ database: prescription, description
$sql2 = mysqli_query($connect, "SELECT * FROM tblmedicalhistory WHERE apid = '$id'");
if ($sql2 && mysqli_num_rows($sql2) > 0) {
    $row2     = mysqli_fetch_assoc($sql2);
    $treatment = $row2['prescription'] ?? 'The Treatment Has Not Been Written Yet'; // ✅ كان 'treatment'
    $report    = $row2['description']  ?? 'The Report Has Not Been Written Yet';    // ✅ كان 'Report'
    $Scan      = $row2['Scan']        ??  'The Scan Has Not Been Written Yet';                                                              // ✅ مش موجود في الـ DB
} else {
    $treatment = 'The Treatment Has Not Been Written Yet';
    $report    = 'The Report Has Not Been Written Yet';
    $Scan      = 'The Scan Has Not Been Written Yet';
}

// ✅ امسح أي warnings اتطبعت قبل الـ PDF
ob_end_clean();

$pdf = new PDF();
$pdf->AddPage();
$pdf->CreateTable($header, $data);
$pdf->AddDetails($treatment, $report, $Scan);
$pdf->Output();
