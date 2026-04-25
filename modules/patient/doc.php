<?php
require_once __DIR__ . '/../../includes/auth.php';

ini_set("display_errors", 0);
$connect = new mysqli("localhost", "root", "", "hms");
if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

$uid = (int)($_SESSION['uid'] ?? 0);
$doctors = [];

$stmt = $connect->prepare("
    SELECT
        d.id,
        d.doctorName,
        d.specilization,
        COALESCE(d.statue, 0) AS statue,
        COUNT(a.apid) AS reservations_count,
        MAX(a.postingDate) AS last_reservation_at
    FROM appointment a
    INNER JOIN doctors d ON d.id = a.doctorId
    WHERE a.userId = ?
      AND a.doctorId IS NOT NULL
      AND (
        COALESCE(a.userStatus, 0) > 0
        OR COALESCE(a.doctorStatus, 0) > 0
      )
    GROUP BY d.id, d.doctorName, d.specilization, d.statue
    ORDER BY last_reservation_at DESC, d.doctorName ASC
");

if ($stmt) {
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>" data-theme="<?= $theme ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Doctors</title>
    <link rel="stylesheet" href="./about.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" href="/assets/images/echol.png">
    <link rel="stylesheet" href="/assets/css/responsive.css">
    <link rel="stylesheet" href="./mobile-header-fix.css">
</head>
<body>
    <div class="min-h-full">
        <?php $activePage = 'doctors'; require_once __DIR__ . '/../../includes/nav.php'; ?>

        <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">My Doctors</h1>
                <p class="mt-2 text-sm text-slate-600">Doctors you have booked with before, with their current availability status.</p>
            </div>
        </header>

        <main>
            <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
                <?php if ($doctors === []): ?>
                    <div class="rounded-2xl border border-slate-200 bg-white px-6 py-10 text-center shadow-sm">
                        <p class="text-lg font-semibold text-slate-800">No doctors found yet.</p>
                        <p class="mt-2 text-sm text-slate-500">When you book a reservation, the doctor will appear here.</p>
                    </div>
                <?php else: ?>
                    <table class="table table-striped table-hover table-bordered border-gray-400 w-full">
                        <thead>
                            <tr>
                                <th scope="col">
                                    <p class="text-lg font-bold text-gray-900 text-center">Doctor ID</p>
                                </th>
                                <th scope="col">
                                    <p class="text-lg font-bold text-gray-900 text-center">Doctor Name</p>
                                </th>
                                <th scope="col">
                                    <p class="text-lg font-bold text-gray-900 text-center">Specialization</p>
                                </th>
                                <th scope="col">
                                    <p class="text-lg font-bold text-gray-900 text-center">Reservations</p>
                                </th>
                                <th scope="col">
                                    <p class="text-lg font-bold text-gray-900 text-center">Last Reservation</p>
                                </th>
                                <th scope="col">
                                    <p class="text-lg font-bold text-gray-900 text-center">Status</p>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            <?php foreach ($doctors as $row): ?>
                                <tr>
                                    <td>
                                        <p class="text-lg font-bold text-gray-900 text-center"><?= htmlspecialchars((string)$row['id']) ?></p>
                                    </td>
                                    <td>
                                        <p class="text-lg font-bold text-gray-900 text-center"><?= htmlspecialchars($row['doctorName']) ?></p>
                                    </td>
                                    <td>
                                        <p class="text-lg font-bold text-gray-900 text-center"><?= htmlspecialchars($row['specilization'] ?: '-') ?></p>
                                    </td>
                                    <td>
                                        <p class="text-lg font-bold text-gray-900 text-center"><?= htmlspecialchars((string)$row['reservations_count']) ?></p>
                                    </td>
                                    <td>
                                        <p class="text-lg font-bold text-gray-900 text-center"><?= htmlspecialchars($row['last_reservation_at'] ?: '-') ?></p>
                                    </td>
                                    <td>
                                        <p class="text-lg font-bold text-gray-900 text-center">
                                            <?php if ((int)$row['statue'] === 1): ?>
                                                <span class="inline-flex items-center rounded-md bg-green-50 px-3 py-1 text-sm font-medium text-green-700 ring-1 ring-inset ring-green-600/20">ON</span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center rounded-md bg-red-50 px-3 py-1 text-sm font-medium text-red-700 ring-1 ring-inset ring-red-600/20">OFF</span>
                                            <?php endif; ?>
                                        </p>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="/assets/js/responsive-nav.js" defer></script>
</body>
</html>
