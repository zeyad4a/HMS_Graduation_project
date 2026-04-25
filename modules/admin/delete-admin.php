<?php
require_once __DIR__ . '/../../includes/auth.php';

$connect = new mysqli("localhost", "root", "", "hms");
if ($connect->connect_error) die("Connection failed");

$apid   = intval($_GET['id']  ?? 0);
$return = $_GET['from'] ?? 'admin-Reservations.php';

$res = $connect->query("SELECT apid FROM appointment WHERE apid=$apid");
if ($res->num_rows === 0) {
    echo "<script>alert('Appointment not found!'); window.location.href='./$return';</script>"; exit();
}

$connect->query("DELETE FROM appointment WHERE apid=$apid");
echo "<script>alert('Appointment deleted successfully.'); window.location.href='./$return';</script>";
exit();
?>
