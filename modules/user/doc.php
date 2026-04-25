<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/secure-token.php';
// ini_set("display_errors", 0);
$connect = new mysqli("localhost", "root", "", "hms");
if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>" data-theme="<?= $theme ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Log Time</title>
    <link rel="stylesheet" href="../css/user-log.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" href="../../assets/images/echol.png">

    <link rel="stylesheet" href="/assets/css/responsive.css">
</head>

<body>
    <div class="min-h-full">
        <?php $activePage = 'doctors'; require_once __DIR__ . '/../../includes/nav.php'; ?>

        <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Doctors</h1>
            </div>
        </header>
        <main class="">
            <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
                <form class="space-y-6 flex flex-wrap p-3" action="./search-doc.php" method="POST">
                    <div class="px-3 py-1.5">
                        <label for="Search" class="flex text-sm font-medium text-gray-900">Search</label>
                        <div class="mt-2">
                            <input id="Search" name="input" type="Search" required class="block font-bold w-26 rounded-md border-2 px-4 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>

                    <div class="px-3 py-1.5">
                        <button name="search" type="submit" class="flex w-20 justify-center rounded-md bg-blue-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Search</button>
                    </div>
                </form>
                <table class=" table table-striped table-hover table-bordered border-gray-400 w-full">
                    <thead>
                        <tr>
                            <th scope="col">
                                <p class="text-lg leading- font-bold text-gray-900 text-center">Doctor ID</p>
                            </th>
                            <th scope="col">
                                <p class="text-lg leading- font-bold text-gray-900 text-center">Doctor Name</p>
                            </th>
                            <th scope="col">
                                <p class="text-lg leading- font-bold text-gray-900 text-center">Doctor statue</p>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        <?php
                        $sql = mysqli_query($connect, "SELECT DISTINCT  * FROM doctors ORDER BY statue desc ");
                        while ($row = mysqli_fetch_array($sql)) {
                        ?>
                            <tr>
                                <th scope="col">
                                    <p class="text-lg leading- font-bold text-gray-900 text-center"><?php echo $row['id']; ?></p>
                                </th>
                                <th scope="col">
                                    <p class="text-lg leading- font-bold text-gray-900 text-center"><?php echo $row['doctorName']; ?></p>
                                </th>

                                <th scope="col">
                                    <p class="text-lg leading- font-bold text-gray-900 text-center">
                                        <?php
                                        if (($row['statue'] == 1)) {
                                        ?>
                                            <button class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-lg font-medium text-blue-700 ring-1 ring-inset ring-red-600/20">`ON`</button>
                                        <?php } else {
                                        ?>
                                            <button class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-lg font-medium text-red-700 ring-1 ring-inset ring-red-600/20">`OFF`</button>
                                        <?php } ?>
                                    </p>
                                </th>
                            </tr>
                    </tbody>
                <?php } ?>
                </table>

            </div>
        </main>

    <script src="/assets/js/responsive-nav.js" defer></script>
</body>

</html>




<tbody class="table-group-divider">

    <?php
    $docid = $_SESSION['id'];
    $sql = mysqli_query($connect, "select * from appointment join users on users.uid=appointment.userId where doctorId='$docid' AND appointmentDate = CURRENT_DATE() AND userStatus = 1 ");
    while ($row = mysqli_fetch_array($sql)) {
    ?>
        <tr>
            <th scope="col">
                <p class="text-lg leading- font-bold text-gray-900 text-center"><?php echo $row['id']; ?></p>
            </th>
            <th scope="col">
                <p class="text-lg leading- font-bold text-gray-900 text-center"><?php echo $row['fullName']; ?></p>
            </th>
            <th scope="col">
                <p class="text-lg leading- font-bold text-gray-900 text-center"><?php echo $row['doctorSpecialization']; ?></p>
            </th>
            <th scope="col">
                <p class="text-lg leading- font-bold text-gray-900 text-center"><?php echo $row['PatientContno']; ?></p>
            </th>
            <th scope="col">
                <p class="text-lg leading- font-bold text-gray-900 text-center"><?php echo $row['gender']; ?></p>
            </th>
            <th scope="col">
                <p class="text-lg leading- font-bold text-gray-900 text-center"><?php echo $row['appointmentDate']; ?></p>
            </th>
            <th scope="col">
                <p class="text-lg leading- font-bold text-gray-900 text-center">
                <p class="text-lg leading- font-bold text-gray-900 text-center"><button class=" text-lg inline-flex items-center rounded-md bg-green-50 px-2 py-1 font-medium text-blue-700 ring-1 ring-inset ring-blue-600">
                        <?php if (($row['userStatus'] == 1) && ($row['doctorStatus'] == 1)) {
                            echo "Active";
                        }
                        if (($row['userStatus'] == 0) && ($row['doctorStatus'] == 1)) {

                            echo "Canceled By Patient";
                        }
                        if (($row['userStatus'] == 1) && ($row['doctorStatus'] == 0)) {
                            echo "Canceled By Doctor";
                        }
                        if (($row['userStatus'] == 1) && ($row['doctorStatus'] == 2)) {
                            echo "Done";
                        }
                        ?></button></p>
                </p>
            </th>
            <th scope="col">
                <form method="get">
                    <?php
                    if (($row['userStatus'] == 1) && ($row['doctorStatus'] == 2)) { ?>
                        <p class="text-lg leading- font-bold text-gray-900 text-center"><button class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-lg font-medium text-blue-700 ring-1 ring-inset ring-green-600/20">Done</button></p>
                    <?php
                    } else { ?>
                        <p class="text-lg leading- font-bold text-gray-900 text-center"><a href="./doc-write.php?ref=<?php echo urlencode(hms_encrypt_id((int)$row['id'])); ?>" class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-lg font-medium text-green-700 ring-1 ring-inset ring-green-600/20">View</a></p>

                    <?php } ?>
                </form>
            </th>
        </tr>
</tbody>
<?php }
?>
