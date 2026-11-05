<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../login.php"); 
    exit();
}

include "sidebar_header.php";
include "../config.php";

$email = $_SESSION['email'];
$username = $_SESSION['username'];

// Handle Donation History Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bloodtype = $_POST['bloodtype'];
    $unitsdonated = $_POST['unitsdonated'];
    $location = $_POST['location'];
    $donationdate = $_POST['donationdate'];

    // Insert donation record into donation_history
    $sql = "INSERT INTO donation_history (username, donor_email, bloodtype, unitsdonated, location, status, donationdate)
            VALUES ('$username','$email', '$bloodtype', '$unitsdonated', '$location', 'donated', '$donationdate')";

    $conn->query($sql);

    // Update Blood Inventory
    $blood = $bloodtype;
    $units = (int)$unitsdonated;
    $loc = $location;

    $checkInv = "SELECT id, units_available FROM blood_inventory 
                 WHERE bloodtype = '$blood' AND location = '$loc' LIMIT 1";
    $invResult = $conn->query($checkInv);

    if ($invResult && $invResult->num_rows > 0) {
        $invRow = $invResult->fetch_assoc();
        $newUnits = $invRow['units_available'] + $units;

        $updateInv = "UPDATE blood_inventory 
                      SET units_available = $newUnits 
                      WHERE id = {$invRow['id']}";
        $conn->query($updateInv);
    } else {
        $addInv = "INSERT INTO blood_inventory (bloodtype, units_available, location)
                   VALUES ('$blood', $units, '$loc')";
        $conn->query($addInv);
    }


    header("Location:donor_dashboard.php#donation-requests");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Donation Data</title>
    <link rel="stylesheet" href="recipient_style.css">
</head>

<body>
    <section id="add-donation-data">
        <h2>Add Donation Data</h2>
        <form action="" method="POST">
            <div>
                <label for="bloodtype">Blood Type</label>
                <select id="bloodtype" name="bloodtype" required>
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
                <label for="unitsdonated">Units Donated</label>
                <input type="number" id="unitsdonated" name="unitsdonated" min="1" max="10" required>
            </div>

            <div>
                <label for="location">Donation Location</label>
                <input type="text" id="location" name="location" required>
            </div>

            <div>
                <label for="donationdate">Donation Date</label>
                <input type="date" id="donationdate" name="donationdate" required>
            </div>

            <button type="submit">Add Donation Record</button>
        </form>
    </section>
</body>

</html>