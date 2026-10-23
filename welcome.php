<?php
session_start();
$admin_user = "admin";
$admin_pass = "admin123";

$user_user = "user";
$user_pass = "user123";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST["role"];
    $username = $_POST["username"];
    $password = $_POST["password"];

    if ($role == "admin" && $username == $admin_user && $password == $admin_pass) {
        $_SESSION['role'] = "admin";
        header("Location: adash.php");
    } elseif ($role == "user" && $username == $user_user && $password == $user_pass) {
        $_SESSION['role'] = "user";
        header("Location: udash.php");
    } else {
        echo "<script>alert('Invalid Credentials,Please check your Login!'); window.location='login.html';</script>";
    }
}
?>