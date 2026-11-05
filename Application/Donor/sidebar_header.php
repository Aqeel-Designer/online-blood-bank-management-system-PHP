<?php

if (!isset($_SESSION['email'])) {
    header("Location: ../login.php"); 
    exit();
}

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
    <title>Donor Dashboard</title>
    <link rel="stylesheet" href="../Recipient/recipient_style.css">
</head>

<body>

    <!-- Sidebar Code  -->
    <div class="sidebar">
        <h2>Donor</h2>
        <ul>
            <li><a href="donor_dashboard.php">Dashboard</a></li>
            <li><a href="donate_blood.php">Donate Blood</a></li>
            <li><a href="donor_dashboard.php#donation-requests">My Donations</a></li>
            <li><a href="add_donation_data.php">Add Donation Data</a></li>
            <li><a href="donor_profile.php">Profile</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <h1>Welcome to OBMS🩸</h1>
            <div class="user-info" id="dropdownToggle">
                Donor ▼
                <div class="dropdown" id="dropdownMenu">
                    <a href="../login.php">Logout</a>
                </div>
            </div>
        </header>
</body>

</html>