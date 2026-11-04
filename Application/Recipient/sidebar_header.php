<?php

/* This is a security code, it insures that no one can access these pages without logging in
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php"); 
    exit();
}*/

?>

<!DOCTYPE html>
<html lang="en">
<head>

<!--logout dropdown button toggle code in java--> 
<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.getElementById("dropdownToggle");
    const menu = document.getElementById("dropdownMenu");

    toggle.addEventListener("click", function (e) {
        e.stopPropagation(); 
        menu.classList.toggle("show");
    });

    document.addEventListener("click", function () {
        menu.classList.remove("show");
    });
});
</script>


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipient Dashboard</title>
    <link rel="stylesheet" href="recipient_style.css">
</head>
<body>

<!-- Sidebar Code  -->
    <div class="sidebar">
        <h2>Recipient</h2>
        <ul>
            <li><a href="recipient_dashboard.php">Dashboard</a></li>
            <li><a href="recipient_search_blood.php">Search for Available Blood</a></li>
            <li><a href="recipient_request_blood.php">Request Blood Units</a></li>
            <li><a href="recipient_dashboard.php#my-requests">Request Status</a></li>
            <li><a href="recipient_profile.php">Profile</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <h1>Welcome to OBMS🩸</h1>
            <div class="user-info" id="dropdownToggle">
            Recipient ▼
            <div class="dropdown" id="dropdownMenu">
            <a href="../login.php">Logout</a>
            </div>
    </div>
        </header>
</body>
</html>
