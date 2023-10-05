<?php
// Initialize the session


// Check if the user is logged in, if not, redirect to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Include config file
require_once "config.php";

// Fetch user information based on the session data
$user_id = $_SESSION["id"];
$sql = "SELECT full_name, phone_number, agent_code FROM users WHERE id = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $full_name, $phone_number, $agent_code);
        mysqli_stmt_fetch($stmt);
    }
    mysqli_stmt_close($stmt);
}
?>