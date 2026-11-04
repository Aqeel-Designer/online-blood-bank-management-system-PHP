<?php



/* This is a security code, it insures that no one can access these pages without logging in
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php"); 
    exit();
}*/

$email = $_SESSION['email'];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <!--logout dropdown button toggle code in java-->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggle = document.getElementById("dropdownToggle");
            const menu = document.getElementById("dropdownMenu");

            toggle.addEventListener("click", function(e) {
                e.stopPropagation();
                menu.classList.toggle("show");
            });

            document.addEventListener("click", function() {
                menu.classList.remove("show");
            });
        });
    </script>


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../Recipient/recipient_style.css">
</head>

<body>

    <!-- Sidebar Code  -->
    <div class="sidebar">
        <h2>Admin</h2>
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="manage_inventory.php">Manage Inventory</a></li>
            <li><a href="manage_donors.php">Manage Donors</a></li>
            <li><a href="manage_donation_requests.php">Donors Blood Requests</a></li>
            <li><a href="donation_history.php">Donation History</a></li>
            <li><a href="manage_receiving_requests.php">Receivers Blood Requests</a></li>
            <li><a href="admin_profile.php">Profile</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <h1>Welcome to OBMS🩸</h1>
            <div class="user-info" id="dropdownToggle">
                Admin ▼
                <div class="dropdown" id="dropdownMenu">
                    <a href="../login.php">Logout</a>
                </div>
            </div>
        </header>
</body>

</html>