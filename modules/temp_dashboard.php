<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/secure-token.php';
require_once __DIR__ . '/../includes/appointment-helpers.php';

$connect = new mysqli("localhost", "root", "", "hms");
if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

$role = $_SESSION['role'] ?? '';
$username = $_SESSION['username'] ?? 'User';
$sessionId = (int)($_SESSION['id'] ?? 0);
$sessionUid = (int)($_SESSION['uid'] ?? 0);

function hms_scalar(mysqli $connect, string $query, $default = 0)
{
    $result = $connect->query($query);
    if (!$result) {
        return $default;
    }

    $row = $result->fetch_row();
    return $row[0] ?? $default;
}

function hms_rows(mysqli $connect, string $query): array
{
    $result = $connect->query($query);
    if (!$result) {
        return [];
    }

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    return $rows;
}

function hms_money($value): string
{
    return number_format((float)$value) . ' EGP';
}

function hms_date_label($value): string
{
    if (!$value) {
        return '-';
    }

    $time = strtotime((string)$value);
    return $time ? date('M d, Y', $time) : (string)$value;
}

function hms_doctor_presence_badge($value): string
{
    $isOnline = (int)$value === 1;
    $classes = $isOnline
        ? 'bg-green-50 text-green-700 ring-green-600/20'
        : 'bg-slate-100 text-slate-600 ring-slate-400/20';
    $label = $isOnline ? 'ON' : 'OFF';

    return "<span class='inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold ring-1 ring-inset {$classes}'>{$label}</span>";
}

$heroTitle = 'Dashboard';
$heroText = 'A role-based summary of the most important medical workflow data for your day.';
$stats = [];
$quickLinks = [];
$primaryRows = [];
$secondaryRows = [];
$primaryTitle = 'Today at a glance';
$primaryEmpty = 'No data found.';
$secondaryTitle = 'Live overview';
$secondaryEmpty = 'No data found.';

switch ($role) {
    case 'System Admin':
        $heroTitle = 'System Command Dashboard';
        $heroText = 'Track today’s operations, live doctor availability, payments, and active hospital workflow from one place.';

        $stats = [
            ['label' => 'Today Reservations', 'value' => hms_scalar($connect, "SELECT COUNT(*) FROM appointment WHERE STR_TO_DATE(appointmentDate, '%Y-%m-%d') = CURDATE()"), 'tone' => 'cyan'],
            ['label' => 'Active Queue', 'value' => hms_scalar($connect, "SELECT COUNT(*) FROM appointment WHERE STR_TO_DATE(appointmentDate, '%Y-%m-%d') = CURDATE() AND userStatus = 1 AND doctorStatus = 1"), 'tone' => 'blue'],
            ['label' => 'Completed Today', 'value' => hms_scalar($connect, "SELECT COUNT(*) FROM appointment WHERE STR_TO_DATE(appointmentDate, '%Y-%m-%d') = CURDATE() AND userStatus = 2 AND doctorStatus = 2"), 'tone' => 'emerald'],
            ['label' => 'Doctors Online', 'value' => hms_scalar($connect, "SELECT COUNT(*) FROM doctors WHERE statue = 1"), 'tone' => 'teal'],
            ['label' => 'Front Desk Online', 'value' => hms_scalar($connect, "SELECT COUNT(*) FROM employ WHERE employ_statue = 1"), 'tone' => 'indigo'],
            ['label' => 'Payments Today', 'value' => hms_money(hms_scalar($connect, "SELECT COALESCE(SUM(COALESCE(NULLIF(paid, 0), consultancyFees)), 0) FROM appointment WHERE STR_TO_DATE(appointmentDate, '%Y-%m-%d') = CURDATE() AND userStatus != 0 AND doctorStatus != 0")), 'tone' => 'amber'],
        ];

        $quickLinks = [
            ['label' => 'Reservations', 'href' => '/modules/super-admin/super-Reservations.php'],
            ['label' => 'Payments', 'href' => '/modules/super-admin/super-Payments.php'],
            ['label' => 'Users', 'href' => '/modules/super-admin/super-user-log.php'],
            ['label' => 'Add Doctor', 'href' => '/modules/super-admin/Add-Doctor.php'],
            ['label' => 'Audit Log', 'href' => '/includes/audit-log.php'],
        ];

        $primaryTitle = 'Today Reservations';
        $primaryEmpty = 'No reservations scheduled for today.';
        $primaryRows = hms_rows($connect, "
            SELECT
                a.apid,
                COALESCE(NULLIF(a.patient_Name, ''), u.fullName, 'Unknown Patient') AS patient_name,
                COALESCE(d.doctorName, 'Unknown Doctor') AS doctor_name,
                a.appointmentDate,
                a.appointmentTime,
                a.userStatus,
                a.doctorStatus,
                COALESCE(NULLIF(a.paid, 0), a.consultancyFees, 0) AS amount_paid
            FROM appointment a
            LEFT JOIN users u ON u.uid = a.userId
            LEFT JOIN doctors d ON d.id = a.doctorId
            WHERE STR_TO_DATE(a.appointmentDate, '%Y-%m-%d') = CURDATE()
            ORDER BY a.postingDate DESC
            LIMIT 8
        ");

        $secondaryTitle = 'Doctor Coverage';
        $secondaryEmpty = 'No doctors found.';
        $secondaryRows = hms_rows($connect, "
            SELECT
                d.doctorName,
                d.specilization,
                COALESCE(d.statue, 0) AS statue,
                COUNT(a.apid) AS today_count
            FROM doctors d
            LEFT JOIN appointment a
                ON a.doctorId = d.id
               AND STR_TO_DATE(a.appointmentDate, '%Y-%m-%d') = CURDATE()
            GROUP BY d.id, d.doctorName, d.specilization, d.statue
            ORDER BY statue DESC, today_count DESC, d.doctorName ASC
            LIMIT 6
        ");
        break;

    case 'Admin':
        $heroTitle = 'Operations Dashboard';
        $heroText = 'Follow live clinic flow, front desk activity, daily reservations, and payments in a medical-friendly overview.';

        $stats = [
            ['label' => 'Today Reservations', 'value' => hms_scalar($connect, "SELECT COUNT(*) FROM appointment WHERE STR_TO_DATE(appointmentDate, '%Y-%m-%d') = CURDATE()"), 'tone' => 'cyan'],
            ['label' => 'Active Queue', 'value' => hms_scalar($connect, "SELECT COUNT(*) FROM appointment WHERE STR_TO_DATE(appointmentDate, '%Y-%m-%d') = CURDATE() AND userStatus = 1 AND doctorStatus = 1"), 'tone' => 'blue'],
            ['label' => 'Completed Today', 'value' => hms_scalar($connect, "SELECT COUNT(*) FROM appointment WHERE STR_TO_DATE(appointmentDate, '%Y-%m-%d') = CURDATE() AND userStatus = 2 AND doctorStatus = 2"), 'tone' => 'emerald'],
            ['label' => 'Doctors Online', 'value' => hms_scalar($connect, "SELECT COUNT(*) FROM doctors WHERE statue = 1"), 'tone' => 'teal'],
            ['label' => 'Patients With Reports', 'value' => hms_scalar($connect, "SELECT COUNT(DISTINCT UserID) FROM tblmedicalhistory"), 'tone' => 'indigo'],
            ['label' => 'Payments Today', 'value' => hms_money(hms_scalar($connect, "SELECT COALESCE(SUM(COALESCE(NULLIF(paid, 0), consultancyFees)), 0) FROM appointment WHERE STR_TO_DATE(appointmentDate, '%Y-%m-%d') = CURDATE() AND userStatus != 0 AND doctorStatus != 0")), 'tone' => 'amber'],
        ];

        $quickLinks = [
            ['label' => 'Reservations', 'href' => '/modules/admin/admin-Reservations.php'],
            ['label' => 'Payments', 'href' => '/modules/admin/admin-Payments.php'],
            ['label' => 'Users', 'href' => '/modules/admin/admin-user-log.php'],
            ['label' => 'Medical Records', 'href' => '/includes/med-record.php'],
            ['label' => 'Audit Log', 'href' => '/includes/audit-log.php'],
        ];

        $primaryTitle = 'Today Reservations';
        $primaryEmpty = 'No reservations scheduled for today.';
        $primaryRows = hms_rows($connect, "
            SELECT
                a.apid,
                COALESCE(NULLIF(a.patient_Name, ''), u.fullName, 'Unknown Patient') AS patient_name,
                COALESCE(d.doctorName, 'Unknown Doctor') AS doctor_name,
                a.appointmentDate,
                a.appointmentTime,
                a.userStatus,
                a.doctorStatus,
                COALESCE(NULLIF(a.paid, 0), a.consultancyFees, 0) AS amount_paid
            FROM appointment a
            LEFT JOIN users u ON u.uid = a.userId
            LEFT JOIN doctors d ON d.id = a.doctorId
            WHERE STR_TO_DATE(a.appointmentDate, '%Y-%m-%d') = CURDATE()
            ORDER BY a.postingDate DESC
            LIMIT 8
        ");

        $secondaryTitle = 'Doctors Online Now';
        $secondaryEmpty = 'No doctor availability data found.';
        $secondaryRows = hms_rows($connect, "
            SELECT
                d.doctorName,
                d.specilization,
                COALESCE(d.statue, 0) AS statue,
                COUNT(a.apid) AS today_count
            FROM doctors d
            LEFT JOIN appointment a
                ON a.doctorId = d.id
               AND STR_TO_DATE(a.appointmentDate, '%Y-%m-%d') = CURDATE()
            GROUP BY d.id, d.doctorName, d.specilization, d.statue
            ORDER BY statue DESC, today_count DESC, d.doctorName ASC
            LIMIT 6
        ");
        break;

    case 'Doctor':
        $heroTitle = 'Doctor Workboard';
        $heroText = 'See your patient queue, pending reports, visit progress, and the cases that need attention today.';

        $stats = [
            ['label' => 'Today Appointments', 'value' => hms_scalar($connect, "SELECT COUNT(*) FROM appointment WHERE doctorId = {$sessionId} AND STR_TO_DATE(appointmentDate, '%Y-%m-%d') = CURDATE()"), 'tone' => 'cyan'],
            ['label' => 'Active Queue', 'value' => hms_scalar($connect, "SELECT COUNT(*) FROM appointment WHERE doctorId = {$sessionId} AND STR_TO_DATE(appointmentDate, '%Y-%m-%d') = CURDATE() AND userStatus = 1 AND doctorStatus = 1"), 'tone' => 'blue'],
            ['label' => 'Completed Today', 'value' => hms_scalar($connect, "SELECT COUNT(*) FROM appointment WHERE doctorId = {$sessionId} AND STR_TO_DATE(appointmentDate, '%Y-%m-%d') = CURDATE() AND userStatus = 2 AND doctorStatus = 2"), 'tone' => 'emerald'],
            ['label' => 'Pending Reports', 'value' => hms_scalar($connect, "SELECT COUNT(*) FROM appointment WHERE doctorId = {$sessionId} AND userStatus IN (1,2) AND doctorStatus IN (1,2) AND apid NOT IN (SELECT apid FROM tblmedicalhistory)"), 'tone' => 'amber'],
            ['label' => 'Cancelled Cases', 'value' => hms_scalar($connect, "SELECT COUNT(*) FROM appointment WHERE doctorId = {$sessionId} AND (userStatus = 0 OR doctorStatus = 0)"), 'tone' => 'rose'],
        ];

        $quickLinks = [
            ['label' => 'Today Reservations', 'href' => '/modules/doctor/doc-Reservations.php'],
            ['label' => 'Write Report', 'href' => '/modules/doctor/doc-write.php'],
            ['label' => 'Medical Records', 'href' => '/includes/med-record.php'],
        ];

        $primaryTitle = 'Today Patient Queue';
        $primaryEmpty = 'No patient appointments assigned for today.';
        $primaryRows = hms_rows($connect, "
            SELECT
                a.apid,
                COALESCE(NULLIF(a.patient_Name, ''), u.fullName, 'Unknown Patient') AS patient_name,
                a.appointmentDate,
                a.appointmentTime,
                a.userStatus,
                a.doctorStatus
            FROM appointment a
            LEFT JOIN users u ON u.uid = a.userId
            WHERE a.doctorId = {$sessionId}
              AND STR_TO_DATE(a.appointmentDate, '%Y-%m-%d') = CURDATE()
            ORDER BY a.appointmentTime ASC, a.postingDate ASC
            LIMIT 8
        ");

        $secondaryTitle = 'Reports To Finish';
        $secondaryEmpty = 'No pending reports right now.';
        $secondaryRows = hms_rows($connect, "
            SELECT
                a.apid,
                COALESCE(NULLIF(a.patient_Name, ''), u.fullName, 'Unknown Patient') AS patient_name,
                a.appointmentDate,
                a.appointmentTime
            FROM appointment a
            LEFT JOIN users u ON u.uid = a.userId
            WHERE a.doctorId = {$sessionId}
              AND a.userStatus IN (1,2)
              AND a.doctorStatus IN (1,2)
              AND a.apid NOT IN (SELECT apid FROM tblmedicalhistory)
            ORDER BY STR_TO_DATE(a.appointmentDate, '%Y-%m-%d') DESC, a.appointmentTime DESC
            LIMIT 6
        ");
        break;

    case 'User':
        $heroTitle = 'Front Desk Dashboard';
        $heroText = 'Monitor reservations created by you, payment collection, and today’s reception workload in one medical operations view.';

        $stats = [
            ['label' => 'Created Today', 'value' => hms_scalar($connect, "SELECT COUNT(*) FROM appointment WHERE employId = {$sessionId} AND DATE(postingDate) = CURDATE()"), 'tone' => 'cyan'],
            ['label' => 'Total Created', 'value' => hms_scalar($connect, "SELECT COUNT(*) FROM appointment WHERE employId = {$sessionId}"), 'tone' => 'blue'],
            ['label' => 'Active Reservations', 'value' => hms_scalar($connect, "SELECT COUNT(*) FROM appointment WHERE employId = {$sessionId} AND userStatus = 1 AND doctorStatus = 1"), 'tone' => 'emerald'],
            ['label' => 'Collected Payments', 'value' => hms_money(hms_scalar($connect, "SELECT COALESCE(SUM(COALESCE(NULLIF(paid, 0), consultancyFees)), 0) FROM appointment WHERE employId = {$sessionId} AND userStatus != 0 AND doctorStatus != 0")), 'tone' => 'amber'],
            ['label' => 'Unpaid Cases', 'value' => hms_scalar($connect, "SELECT COUNT(*) FROM appointment WHERE employId = {$sessionId} AND COALESCE(paid, 0) = 0 AND userStatus != 0 AND doctorStatus != 0"), 'tone' => 'rose'],
        ];

        $quickLinks = [
            ['label' => 'New Appointment', 'href' => '/modules/user/new_appoint.php'],
            ['label' => 'Reservations', 'href' => '/modules/user/Reservations.php'],
            ['label' => 'Doctors', 'href' => '/modules/user/doc.php'],
            ['label' => 'Medical Records', 'href' => '/includes/med-record.php'],
        ];

        $primaryTitle = 'Today Front Desk Activity';
        $primaryEmpty = 'No reservations created today yet.';
        $primaryRows = hms_rows($connect, "
            SELECT
                a.apid,
                COALESCE(NULLIF(a.patient_Name, ''), u.fullName, 'Unknown Patient') AS patient_name,
                COALESCE(d.doctorName, 'Unknown Doctor') AS doctor_name,
                a.appointmentDate,
                a.appointmentTime,
                a.userStatus,
                a.doctorStatus,
                COALESCE(NULLIF(a.paid, 0), a.consultancyFees, 0) AS amount_paid
            FROM appointment a
            LEFT JOIN users u ON u.uid = a.userId
            LEFT JOIN doctors d ON d.id = a.doctorId
            WHERE STR_TO_DATE(a.appointmentDate, '%Y-%m-%d') = CURDATE()
            ORDER BY a.postingDate DESC
            LIMIT 8
        ");

        $secondaryTitle = 'Doctors Available Now';
        $secondaryEmpty = 'No doctors found.';
        $secondaryRows = hms_rows($connect, "
            SELECT
                doctorName,
                specilization,
                COALESCE(statue, 0) AS statue
            FROM doctors
            ORDER BY statue DESC, doctorName ASC
            LIMIT 6
        ");
        break;

    case 'Patient':
        $lastVisit = hms_scalar($connect, "SELECT MAX(STR_TO_DATE(appointmentDate, '%Y-%m-%d')) FROM appointment WHERE userId = {$sessionUid}", null);
        $heroTitle = 'Patient Care Dashboard';
        $heroText = 'Your medical journey in one place: upcoming visits, completed consultations, reports, and doctors you have visited before.';

        $stats = [
            ['label' => 'My Reservations', 'value' => hms_scalar($connect, "SELECT COUNT(*) FROM appointment WHERE userId = {$sessionUid}"), 'tone' => 'cyan'],
            ['label' => 'Today Visits', 'value' => hms_scalar($connect, "SELECT COUNT(*) FROM appointment WHERE userId = {$sessionUid} AND STR_TO_DATE(appointmentDate, '%Y-%m-%d') = CURDATE() AND userStatus = 1 AND doctorStatus = 1"), 'tone' => 'blue'],
            ['label' => 'Completed Visits', 'value' => hms_scalar($connect, "SELECT COUNT(*) FROM appointment WHERE userId = {$sessionUid} AND userStatus = 2 AND doctorStatus = 2"), 'tone' => 'emerald'],
            ['label' => 'Medical Reports', 'value' => hms_scalar($connect, "SELECT COUNT(*) FROM tblmedicalhistory WHERE UserID = {$sessionUid}"), 'tone' => 'teal'],
            ['label' => 'Doctors Seen', 'value' => hms_scalar($connect, "SELECT COUNT(DISTINCT doctorId) FROM appointment WHERE userId = {$sessionUid} AND doctorId IS NOT NULL"), 'tone' => 'indigo'],
            ['label' => 'Last Visit', 'value' => hms_date_label($lastVisit), 'tone' => 'amber'],
        ];

        $quickLinks = [
            ['label' => 'New Reservation', 'href' => '/modules/patient/New-reservation.php'],
            ['label' => 'My Reservations', 'href' => '/modules/patient/Reservations.php'],
            ['label' => 'My Doctors', 'href' => '/modules/patient/doc.php'],
            ['label' => 'Profile', 'href' => '/modules/patient/Profile.php'],
            ['label' => 'Medical Record', 'href' => '/includes/patient-profile.php?ref=' . urlencode(hms_encrypt_id($sessionUid))],
        ];

        $primaryTitle = 'Today Visits';
        $primaryEmpty = 'No reservations found for today.';
        $primaryRows = hms_rows($connect, "
            SELECT
                a.apid,
                COALESCE(d.doctorName, 'Unknown Doctor') AS doctor_name,
                a.doctorSpecialization,
                a.appointmentDate,
                a.appointmentTime,
                a.userStatus,
                a.doctorStatus
            FROM appointment a
            LEFT JOIN doctors d ON d.id = a.doctorId
            WHERE a.userId = {$sessionUid}
              AND STR_TO_DATE(a.appointmentDate, '%Y-%m-%d') = CURDATE()
            ORDER BY a.appointmentTime ASC
            LIMIT 8
        ");

        $secondaryTitle = 'Doctors You Have Seen';
        $secondaryEmpty = 'No previous doctors found.';
        $secondaryRows = hms_rows($connect, "
            SELECT
                d.id,
                d.doctorName,
                d.specilization,
                COALESCE(d.statue, 0) AS statue,
                COUNT(a.apid) AS reservations_count,
                MAX(STR_TO_DATE(a.appointmentDate, '%Y-%m-%d')) AS last_visit
            FROM appointment a
            INNER JOIN doctors d ON d.id = a.doctorId
            WHERE a.userId = {$sessionUid}
            GROUP BY d.id, d.doctorName, d.specilization, d.statue
            ORDER BY last_visit DESC, d.doctorName ASC
            LIMIT 6
        ");
        break;
}

$toneMap = [
    'cyan' => 'from-cyan-500/10 to-sky-500/5 border-cyan-200',
    'blue' => 'from-blue-500/10 to-indigo-500/5 border-blue-200',
    'emerald' => 'from-emerald-500/10 to-teal-500/5 border-emerald-200',
    'teal' => 'from-teal-500/10 to-cyan-500/5 border-teal-200',
    'indigo' => 'from-indigo-500/10 to-blue-500/5 border-indigo-200',
    'amber' => 'from-amber-500/10 to-orange-500/5 border-amber-200',
    'rose' => 'from-rose-500/10 to-pink-500/5 border-rose-200',
];
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>" data-theme="<?= $theme ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/assets/css/responsive.css">
    <link rel="icon" href="/assets/images/echol.png">
</head>
<body class="bg-slate-100 min-h-screen">
    <div class="min-h-full">
        <?php $activePage = 'dashboard'; require_once __DIR__ . '/../includes/nav.php'; ?>

        <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <p class="text-sm uppercase tracking-[0.28em] text-slate-500"><?= htmlspecialchars($role) ?></p>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900"><?= htmlspecialchars($heroTitle) ?></h1>
                <p class="mt-2 max-w-3xl text-slate-600"><?= htmlspecialchars($heroText) ?></p>
            </div>
        </header>

        <main>
            <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
                <section class="mb-8 overflow-hidden rounded-3xl border border-cyan-100 bg-white shadow-sm">
                    <div class="bg-gradient-to-r from-cyan-50 via-white to-sky-50 px-6 py-6">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <p class="text-sm uppercase tracking-[0.3em] text-slate-500"><?= htmlspecialchars($role) ?></p>
                                <h2 class="mt-2 text-2xl font-bold text-slate-900">Welcome back, <?= htmlspecialchars($username) ?></h2>
                                <p class="mt-2 text-sm text-slate-600">This board highlights the operational and medical data that matter most for your role today.</p>
                            </div>
                            <div class="rounded-2xl border border-cyan-100 bg-white/80 px-4 py-3 text-sm text-slate-600">
                                <p class="font-semibold text-slate-900">Today</p>
                                <p><?= date('l, F j, Y') ?></p>
                            </div>
                        </div>

                        <div class="mt-5 flex flex-wrap gap-3">
                            <?php foreach ($quickLinks as $link): ?>
                                <a href="<?= htmlspecialchars($link['href']) ?>" class="inline-flex items-center rounded-xl border border-cyan-100 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-cyan-200 hover:bg-cyan-50">
                                    <?= htmlspecialchars($link['label']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>

                <section class="mb-8 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    <?php foreach ($stats as $stat): ?>
                        <?php $tone = $toneMap[$stat['tone'] ?? 'cyan'] ?? $toneMap['cyan']; ?>
                        <div class="rounded-3xl border bg-gradient-to-br <?= $tone ?> bg-white p-5 shadow-sm">
                            <p class="text-sm font-medium text-slate-500"><?= htmlspecialchars($stat['label']) ?></p>
                            <p class="mt-3 text-3xl font-bold text-slate-900"><?= htmlspecialchars((string)$stat['value']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </section>

                <section class="grid gap-6 xl:grid-cols-[1.6fr_1fr]">
                    <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-6 py-5">
                            <h3 class="text-lg font-semibold text-slate-900"><?= htmlspecialchars($primaryTitle) ?></h3>
                        </div>

                        <div class="p-6">
                            <?php if ($primaryRows === []): ?>
                                <p class="text-sm text-slate-500"><?= htmlspecialchars($primaryEmpty) ?></p>
                            <?php else: ?>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-slate-200">
                                        <thead class="bg-slate-50">
                                            <?php if (in_array($role, ['System Admin', 'Admin', 'User'], true)): ?>
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Patient</th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Doctor</th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Visit Time</th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Amount</th>
                                                </tr>
                                            <?php elseif ($role === 'Doctor'): ?>
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Patient</th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Date</th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Time</th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                                                </tr>
                                            <?php else: ?>
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Doctor</th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Specialization</th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Date</th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Time</th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                                                </tr>
                                            <?php endif; ?>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 bg-white">
                                            <?php foreach ($primaryRows as $row): ?>
                                                <tr class="align-top">
                                                    <?php if (in_array($role, ['System Admin', 'Admin', 'User'], true)): ?>
                                                        <td class="px-4 py-4 text-sm font-semibold text-slate-900"><?= htmlspecialchars($row['patient_name']) ?></td>
                                                        <td class="px-4 py-4 text-sm text-slate-700"><?= htmlspecialchars($row['doctor_name']) ?></td>
                                                        <td class="px-4 py-4 text-sm text-slate-600"><?= htmlspecialchars(trim(($row['appointmentDate'] ?? '-') . ' ' . ($row['appointmentTime'] ?? ''))) ?></td>
                                                        <td class="px-4 py-4 text-sm"><?= appt_status_badge($row) ?></td>
                                                        <td class="px-4 py-4 text-sm font-semibold text-slate-900"><?= hms_money($row['amount_paid'] ?? 0) ?></td>
                                                    <?php elseif ($role === 'Doctor'): ?>
                                                        <td class="px-4 py-4 text-sm font-semibold text-slate-900"><?= htmlspecialchars($row['patient_name']) ?></td>
                                                        <td class="px-4 py-4 text-sm text-slate-600"><?= htmlspecialchars($row['appointmentDate'] ?: '-') ?></td>
                                                        <td class="px-4 py-4 text-sm text-slate-600"><?= htmlspecialchars($row['appointmentTime'] ?: '-') ?></td>
                                                        <td class="px-4 py-4 text-sm"><?= appt_status_badge($row) ?></td>
                                                    <?php else: ?>
                                                        <td class="px-4 py-4 text-sm font-semibold text-slate-900"><?= htmlspecialchars($row['doctor_name']) ?></td>
                                                        <td class="px-4 py-4 text-sm text-slate-700"><?= htmlspecialchars($row['doctorSpecialization'] ?: '-') ?></td>
                                                        <td class="px-4 py-4 text-sm text-slate-600"><?= htmlspecialchars($row['appointmentDate'] ?: '-') ?></td>
                                                        <td class="px-4 py-4 text-sm text-slate-600"><?= htmlspecialchars($row['appointmentTime'] ?: '-') ?></td>
                                                        <td class="px-4 py-4 text-sm"><?= appt_status_badge($row) ?></td>
                                                    <?php endif; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-6 py-5">
                            <h3 class="text-lg font-semibold text-slate-900"><?= htmlspecialchars($secondaryTitle) ?></h3>
                        </div>

                        <div class="p-6">
                            <?php if ($secondaryRows === []): ?>
                                <p class="text-sm text-slate-500"><?= htmlspecialchars($secondaryEmpty) ?></p>
                            <?php else: ?>
                                <div class="space-y-4">
                                    <?php foreach ($secondaryRows as $row): ?>
                                        <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-4">
                                            <?php if (in_array($role, ['System Admin', 'Admin'], true)): ?>
                                                <div class="flex items-start justify-between gap-3">
                                                    <div>
                                                        <p class="font-semibold text-slate-900"><?= htmlspecialchars($row['doctorName']) ?></p>
                                                        <p class="text-sm text-slate-500"><?= htmlspecialchars($row['specilization'] ?: '-') ?></p>
                                                        <p class="mt-2 text-xs text-slate-500">Today reservations: <?= htmlspecialchars((string)$row['today_count']) ?></p>
                                                    </div>
                                                    <?= hms_doctor_presence_badge($row['statue']) ?>
                                                </div>
                                            <?php elseif ($role === 'Doctor'): ?>
                                                <div class="flex items-start justify-between gap-3">
                                                    <div>
                                                        <p class="font-semibold text-slate-900"><?= htmlspecialchars($row['patient_name']) ?></p>
                                                        <p class="text-sm text-slate-500"><?= htmlspecialchars($row['appointmentDate'] ?: '-') ?> at <?= htmlspecialchars($row['appointmentTime'] ?: '-') ?></p>
                                                    </div>
                                                    <a href="/modules/doctor/doc-write.php?ref=<?= urlencode(hms_encrypt_id((int)$row['apid'])) ?>" class="inline-flex items-center rounded-lg border border-cyan-200 bg-cyan-50 px-3 py-1.5 text-xs font-semibold text-cyan-700 hover:bg-cyan-100">Write Report</a>
                                                </div>
                                            <?php elseif ($role === 'User'): ?>
                                                <div class="flex items-start justify-between gap-3">
                                                    <div>
                                                        <p class="font-semibold text-slate-900"><?= htmlspecialchars($row['doctorName']) ?></p>
                                                        <p class="text-sm text-slate-500"><?= htmlspecialchars($row['specilization'] ?: '-') ?></p>
                                                    </div>
                                                    <?= hms_doctor_presence_badge($row['statue']) ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="flex items-start justify-between gap-3">
                                                    <div>
                                                        <p class="font-semibold text-slate-900"><?= htmlspecialchars($row['doctorName']) ?></p>
                                                        <p class="text-sm text-slate-500"><?= htmlspecialchars($row['specilization'] ?: '-') ?></p>
                                                        <p class="mt-2 text-xs text-slate-500">Visits: <?= htmlspecialchars((string)$row['reservations_count']) ?> | Last visit: <?= hms_date_label($row['last_visit'] ?? null) ?></p>
                                                    </div>
                                                    <?= hms_doctor_presence_badge($row['statue']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script src="/assets/js/responsive-nav.js" defer></script>
</body>
</html>
