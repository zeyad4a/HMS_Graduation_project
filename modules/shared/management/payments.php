<?php
require_once __DIR__ . '/bootstrap.php';
ini_set("display_errors", 0);

$connect = hms_management_connect();
$employId = intval($_GET['employId'] ?? 0);
$today = date('Y-m-d');
$filterSql = " AND appointmentDate = '{$today}'";
if ($employId > 0) {
    $filterSql .= " AND employId = {$employId}";
}

$titleSuffix = $employId > 0 ? " for selected user today" : " for today";
$total = 0;
$employees = $connect->query("SELECT id, username FROM employ ORDER BY username ASC");
$totalQuery = $connect->query("SELECT SUM(COALESCE(NULLIF(paid, 0), consultancyFees)) AS total FROM appointment WHERE userStatus IN (1,2) {$filterSql}");
if ($totalQuery && ($row = $totalQuery->fetch_assoc())) {
    $total = (int)($row['total'] ?? 0);
}
$payments = $connect->query("SELECT * FROM appointment WHERE userStatus IN (1,2) {$filterSql} ORDER BY postingDate DESC");
$searchPath = hms_management_sibling_path('search-pay.php');
$receiptPath = hms_management_sibling_path('receipt.php');
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>" data-theme="<?= $theme ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/assets/css/responsive.css">
    <link rel="icon" href="/assets/images/echol.png">
</head>
<body>
    <div class="min-h-full">
        <?php $activePage = 'payments'; require __DIR__ . '/../../../includes/nav.php'; ?>

        <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Payments<?= htmlspecialchars($titleSuffix) ?></h1>
            </div>
        </header>

        <main>
            <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
                <form class="space-y-6 flex flex-wrap items-end p-3 mb-4 bg-white rounded-lg shadow-sm" action="<?= htmlspecialchars($searchPath) ?>" method="POST">
                    <div class="px-3 py-1.5">
                        <label for="input" class="flex text-sm font-medium text-gray-900">Patient / Invoice</label>
                        <div class="mt-2">
                            <input id="input" name="input" type="search" class="block font-bold w-56 rounded-md border-2 px-4 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300" placeholder="Patient name, ID, phone, invoice">
                        </div>
                    </div>
                    <div class="px-3 py-1.5">
                        <label for="employId" class="flex text-sm font-medium text-gray-900">User</label>
                        <div class="mt-2">
                            <select id="employId" name="employId" class="block w-48 rounded-md border-2 px-4 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300">
                                <option value="">All users</option>
                                <?php if ($employees): ?>
                                    <?php while ($employee = $employees->fetch_assoc()): ?>
                                        <option value="<?= (int)$employee['id'] ?>" <?= $employId === (int)$employee['id'] ? 'selected' : '' ?>><?= htmlspecialchars($employee['username']) ?></option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="px-3 py-1.5">
                        <label for="fromDate" class="flex text-sm font-medium text-gray-900">From</label>
                        <div class="mt-2">
                            <input id="fromDate" name="fromDate" type="date" value="<?= htmlspecialchars($today) ?>" class="block rounded-md border-2 px-4 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300">
                        </div>
                    </div>
                    <div class="px-3 py-1.5">
                        <label for="toDate" class="flex text-sm font-medium text-gray-900">To</label>
                        <div class="mt-2">
                            <input id="toDate" name="toDate" type="date" value="<?= htmlspecialchars($today) ?>" class="block rounded-md border-2 px-4 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300">
                        </div>
                    </div>
                    <div class="px-3 py-1.5">
                        <button type="submit" class="flex justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500">Search</button>
                    </div>
                </form>

                <table class="table table-striped table-hover table-bordered border-gray-400">
                    <caption class="caption-bottom border-2 border-gray-400 bg-gray-300">
                        <p class="text-lg font-bold text-center text-black p-1">Total Payments [ <?= htmlspecialchars((string)$total) ?> ]</p>
                    </caption>
                    <thead>
                        <tr>
                            <th><p class="text-lg font-bold text-gray-900 text-center">Patient ID</p></th>
                            <th><p class="text-lg font-bold text-gray-900 text-center">Patient Name</p></th>
                            <th><p class="text-lg font-bold text-gray-900 text-center">Reservation</p></th>
                            <th><p class="text-lg font-bold text-gray-900 text-center">Paid</p></th>
                            <th><p class="text-lg font-bold text-gray-900 text-center">By</p></th>
                            <th><p class="text-lg font-bold text-gray-900 text-center">Print</p></th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        <?php if ($payments && $payments->num_rows > 0): ?>
                            <?php while ($row = $payments->fetch_assoc()): ?>
                                <tr>
                                    <th><p class="text-lg font-bold text-gray-900 text-center"><?= htmlspecialchars((string)$row['userId']) ?></p></th>
                                    <th><p class="text-lg font-bold text-gray-900 text-center"><?= htmlspecialchars($row['patient_Name']) ?></p></th>
                                    <th><p class="text-lg font-bold text-gray-900 text-center"><?= htmlspecialchars($row['doctorSpecialization']) ?></p></th>
                                    <th><p class="text-lg font-bold text-gray-900 text-center"><?= htmlspecialchars((string)($row['paid'] ?: $row['consultancyFees'])) ?></p></th>
                                    <th><p class="text-lg font-bold text-gray-900 text-center"><?= htmlspecialchars($row['employname']) ?></p></th>
                                    <th><p class="text-lg font-bold text-gray-900 text-center"><a href="<?= htmlspecialchars($receiptPath) ?>?id=<?= (int)$row['apid'] ?>" class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-lg font-medium text-green-700 ring-1 ring-inset ring-green-600/20">View</a></p></th>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6"><p class="text-center text-gray-500 py-4 mb-0">No payments found for today.</p></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <script src="/assets/js/responsive-nav.js" defer></script>
</body>
</html>
