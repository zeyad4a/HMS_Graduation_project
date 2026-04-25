<?php
require_once __DIR__ . '/bootstrap.php';

$pageTitle = $pageTitle ?? 'Doctors Status';
$showDeleteAction = $showDeleteAction ?? false;

$connect = hms_management_connect();

if ($showDeleteAction && isset($_GET['cancel'])) {
    $doctorId = intval($_GET['id'] ?? 0);
    if ($doctorId > 0) {
        $doctorResult = mysqli_query($connect, "SELECT id, doctorName, specilization, docEmail FROM doctors WHERE id = {$doctorId}");
        $doctor = $doctorResult ? mysqli_fetch_assoc($doctorResult) : null;
        mysqli_query($connect, "DELETE FROM doctors WHERE id = {$doctorId}");

        if ($doctor) {
            hms_audit_log($connect, 'doctor.deleted', [
                'description' => 'A doctor account was deleted.',
                'entity_type' => 'doctor',
                'entity_id' => (string)$doctorId,
                'details' => [
                    'doctor_name' => $doctor['doctorName'] ?? null,
                    'specialization' => $doctor['specilization'] ?? null,
                    'email' => $doctor['docEmail'] ?? null,
                ],
            ]);
        }
    }

    header("Location: " . hms_management_current_path());
    exit();
}

$doctors = mysqli_query($connect, "SELECT DISTINCT * FROM doctors ORDER BY statue DESC");
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>" data-theme="<?= $theme ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" href="/assets/images/echol.png">
    <link rel="stylesheet" href="/assets/css/responsive.css">
</head>
<body>
    <div class="min-h-full">
        <?php $activePage = 'doctors'; require __DIR__ . '/../../../includes/nav.php'; ?>

        <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900"><?= htmlspecialchars($pageTitle) ?></h1>
            </div>
        </header>

        <main>
            <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
                <form class="space-y-6 flex flex-wrap p-3" action="<?= htmlspecialchars(hms_management_sibling_path('search-doc.php')) ?>" method="POST">
                    <div class="px-3 py-1.5">
                        <label for="Search" class="flex text-sm font-medium text-gray-900">Search</label>
                        <div class="mt-2">
                            <input id="Search" name="input" type="Search" required class="font-bold block w-26 rounded-md border-2 px-4 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>
                    <div class="px-3 py-1.5">
                        <button name="search" type="submit" class="flex w-20 justify-center rounded-md bg-blue-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500">Search</button>
                    </div>
                </form>

                <table class="table table-striped table-hover table-bordered border-gray-400 w-full">
                    <thead>
                        <tr>
                            <th><p class="text-lg font-bold text-gray-900 text-center">Doctor ID</p></th>
                            <th><p class="text-lg font-bold text-gray-900 text-center">Doctor Name</p></th>
                            <th><p class="text-lg font-bold text-gray-900 text-center">Doctor Status</p></th>
                            <?php if ($showDeleteAction): ?>
                                <th><p class="text-lg font-bold text-gray-900 text-center">Delete Doctor</p></th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        <?php if ($doctors): ?>
                            <?php while ($row = mysqli_fetch_assoc($doctors)): ?>
                                <tr>
                                    <th><p class="text-lg font-bold text-gray-900 text-center"><?= htmlspecialchars((string)$row['id']) ?></p></th>
                                    <th><p class="text-lg font-bold text-gray-900 text-center"><?= htmlspecialchars($row['doctorName']) ?></p></th>
                                    <th>
                                        <p class="text-lg font-bold text-gray-900 text-center">
                                            <?php if ((int)$row['statue'] === 1): ?>
                                                <button class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-lg font-medium text-blue-700 ring-1 ring-inset ring-red-600/20">ON</button>
                                            <?php else: ?>
                                                <button class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-lg font-medium text-red-700 ring-1 ring-inset ring-red-600/20">OFF</button>
                                            <?php endif; ?>
                                        </p>
                                    </th>
                                    <?php if ($showDeleteAction): ?>
                                        <th><p class="text-lg font-bold text-gray-900 text-center"><a href="<?= htmlspecialchars(hms_management_current_path()) ?>?id=<?= (int)$row['id'] ?>&cancel=1" onClick="return confirm('Are You Sure You Want To Delete ?');" class="text-lg inline-flex items-center rounded-md bg-green-50 px-2 py-1 font-medium text-red-700 ring-1 ring-inset ring-red-600/20">Delete</a></p></th>
                                    <?php endif; ?>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <script src="/assets/js/responsive-nav.js" defer></script>
</body>
</html>
