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
//  Handle Blood Request Submission 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reqBloodType'])) {
    $bloodtype = $_POST['reqBloodType'];
    $units = $_POST['units'];
    $location = $_POST['hospital'];

    $sql = "INSERT INTO blood_requests (username, recipient_email, hospital_email, hospital_name, bloodtype, units, location, status, requester_type) 
            VALUES ('$username','$email', NULL, NULL, '$bloodtype', '$units', '$location', 'pending', 'recipient')";
    $conn->query($sql);

    header("Location:recipient_dashboard.php#my-requests");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipient Dashboard</title>
    <link rel="stylesheet" href="recipient_style.css">

</head>
<body>
        <!-- Request Blood Section -->
        <section id="request-blood">
            <h2>Request Blood Units</h2>
            <form action="" method="POST">
                <div>
                    <label for="reqBloodType">Blood Type Needed</label>
                    <select id="reqBloodType" name="reqBloodType" required>
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
                    <label for="units">Number of Units</label>
                    <input type="number" id="units" name="units" min="1" max="10" required>
                </div>
                
                <div>
                    <label for="hospital">Location</label>
                    <input type="text" id="hospital" name="hospital" required>
                </div>
                
                <button type="submit">Submit Request</button>
            </form>
        </section>
</body>
</html>