<?php
session_start();

/*if (!isset($_SESSION['email'])) {
    header("Location: ../login.php"); 
    exit();
}*/

include "sidebar_header.php";
include "../config.php";

$email = $_SESSION['email'];
$username = $_SESSION['username'];

// Donation Request Submission 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['donateBloodType'])) {
    $bloodtype = $_POST['donateBloodType'];
    $units = $_POST['unitsdonated'];
    $location = $_POST['location'];
    $donationDate = $_POST['donationdate'];

    $sql = "INSERT INTO donation_requests (username, donor_email, bloodtype, unitsdonated, location, status, donationdate) 
            VALUES ('$username','$email', '$bloodtype', '$units', '$location', 'pending', '$donationDate')";
    $conn->query($sql);

    header("Location: donor_dashboard.php#my-donations");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donate Blood</title>
    <link rel="stylesheet" href="../Recipient/recipient_style.css">
    <style>
        /* CSS  */
        input[type="date"] {
            width: 100%;
            padding: 10px;
            background: #eee;
            border-radius: 6px;
            border: none;
            outline: none;
            font-size: 15px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <!-- Donate Blood Section -->
    <section id="donate-blood">
        <h2>Donate Blood</h2>
        <form action="" method="POST">
            <div>
                <label for="donateBloodType">Select Blood Type to Donate</label>
                <select id="donateBloodType" name="donateBloodType" required>
                    <option value="">Select Blood Type</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                </select>
            </div>

            <div>
                <label for="unitsdonated">Number of Units to Donate</label>
                <input type="number" id="unitsdonated" name="unitsdonated" min="1" max="10" required>
            </div>

            <div>
                <label for="donationdate">Select Donation Date</label>
                <input type="date" id="donationdate" name="donationdate" required>
            </div>

            <div>
                <label for="location">Location</label>
                <input type="text" id="location" name="location" required>
            </div>

            <button type="submit">Submit Donation Request</button>
        </form>
    </section>
</body>

</html>