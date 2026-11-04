<?php
session_start();

/*if (!isset($_SESSION['email'])) {
    header("Location: ../login.php"); 
    exit();
}*/

include "sidebar_header.php";
include "../config.php";

$email = $_SESSION['email'];

$userQuery = "SELECT username FROM users WHERE email='$email'";
$userResult = $conn -> query($userQuery);

if ($userResult && $userResult-> num_rows>0) {

    $userRow = $userResult-> fetch_assoc();
    $username = $userRow['username'];

}
else{
    $username = "User";
}
// Fetch Requests for Logged-in Recipient 
$sql = "SELECT request_id, bloodtype, units, location, requested_at, status 
        FROM blood_requests 
        WHERE recipient_email = '$email' 
        ORDER BY request_id ASC"; 
$result = $conn->query($sql);
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

        <!-- Dashboard Section -->
        <section id="dashboard">
            <h2>Hello <?php echo htmlspecialchars($username)?>, Welcome to the Recipient Dashboard</h2>
    <p style="text-align: center; font-size:17px;">Use the sidebar to navigate.</p>
        </section>


        <!-- My Requests Section -->
        <section id="my-requests">
    <h2>My Blood Requests</h2>
    <?php if ($result && $result->num_rows > 0): ?>
        <table class="requests-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Blood Type</th>
                    <th>Units</th>
                    <th>Location</th>
                    <th>Requested At</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $counter = 1;
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo str_pad($counter++, 3, "0", STR_PAD_LEFT); ?></td>
                        <td><?php echo $row['bloodtype']; ?></td>
                        <td><?php echo $row['units']; ?></td>
                        <td><?php echo $row['location']; ?></td>
                        <td><?php echo $row['requested_at']; ?></td>
                        <td>
                            <span class="status <?php echo strtolower($row['status']); ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-results">You have not made any requests yet.</p>
    <?php endif; ?>
</section>

</body>
</html>
