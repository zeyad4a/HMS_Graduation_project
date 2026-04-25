<?php
require_once __DIR__ . '/bootstrap.php';

$connect = hms_management_connect();
require_once __DIR__ . '/../../../libs/fpdf/fpdf.php';

if (!class_exists('HmsReceiptPdf')) {
    class HmsReceiptPdf extends FPDF
    {
        public function Header()
        {
            $logoPath = str_replace('\\', '/', __DIR__ . '/../../../assets/images/black echo.png');
            $this->Image($logoPath, 0, 0, 25);
            $this->SetFont('Arial', 'B', 15);
            $this->Cell(80);
            $this->Cell(-70, 30, 'Echo Medical System', 0, 1, 'C');
            $this->Ln(20);
        }

        public function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
        }

        public function FancyTable(array $header, array $data): void
        {
            $this->SetY(40);
            $this->SetFillColor(255, 0, 0);
            $this->SetTextColor(255);
            $this->SetDrawColor(128, 0, 0);
            $this->SetLineWidth(.3);
            $this->SetFont('', 'B');

            $widths = [42, 60];
            $this->SetFillColor(224, 235, 255);
            $this->SetTextColor(0);
            $this->SetFont('');

            $fill = false;
            foreach ($data as $row) {
                for ($i = 0; $i < count($header); $i++) {
                    $this->SetX(1.8);
                    $this->Cell($widths[0], 9, $header[$i], 1, 0, 'L', true);
                    $this->Cell($widths[1], 9, $row[$i], 1, 1, 'L', $fill);
                }
                $this->Ln();
                $fill = !$fill;
            }

            $this->Ln(5);
            $this->SetX(10);
            $this->Cell(60, 0, 'Signature:', 0, 0, 'L');
            $this->Cell(50, 0, 'Stamp:', 0, 1, 'L');
        }
    }
}

$header = ['Patient Name', 'Reservation Date', 'DR Name', 'Reservation Type', 'Patient ID', 'Payed'];
$appointmentId = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

if ($appointmentId === 0) {
    die("Invalid parameters.");
}

$sql = mysqli_query($connect, "SELECT doctors.doctorName AS docname, appointment.* FROM appointment JOIN doctors ON doctors.id = appointment.doctorId WHERE apid = {$appointmentId}");
if (!$sql || mysqli_num_rows($sql) === 0) {
    die("Receipt not found.");
}

$row = mysqli_fetch_assoc($sql);
$data = [[
    $row['patient_Name'],
    $row['appointmentDate'],
    $row['docname'],
    $row['doctorSpecialization'],
    $row['userId'],
    $row['consultancyFees'] . ' EGP',
]];

$pdf = new HmsReceiptPdf('P', 'mm', [105, 148]);
$pdf->SetFont('Arial', '', 14);
$pdf->AddPage();
$pdf->FancyTable($header, $data);
$pdf->Output();
