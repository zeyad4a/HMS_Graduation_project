<?php
require_once __DIR__ . '/../includes/auth.php';
$connect = new mysqli("localhost", "root", "", "hms");


if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}
if (!empty($_POST["email"])) {
    $email = $_POST["email"];

    $result = mysqli_query($connect, "SELECT email FROM users WHERE email='$email'");
    $count = mysqli_num_rows($result);
    if ($count > 0) {
        echo "<span style='color:red'> email is used .</span>";
        echo "<script>$('#submit').prop('disabled',true);</script>";
    } else {

        echo "<span style='color:green'> email is not used.</span>";
        echo "<script>$('#submit').prop('disabled',false);</script>";
    }
}


        
